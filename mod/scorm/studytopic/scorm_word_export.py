#!/usr/bin/env python3
"""Export Moodle SCORM question banks as real DOCX files.

The exporter is intentionally read-only with respect to Moodle. It reads package
files through their content hashes, writes into a new export directory, and
creates separate ZIP archives for formula and herb exams.
"""

from __future__ import annotations

import argparse
import csv
import datetime as dt
import html
import json
import re
import shutil
import struct
import subprocess
import sys
import tempfile
import zipfile
from collections import defaultdict
from pathlib import Path, PurePosixPath
from typing import Any, Iterable
from urllib.parse import unquote, urlparse


SCRIPT_DIR = Path(__file__).resolve().parent
INVENTORY_HELPER = SCRIPT_DIR / "scorm_word_inventory.php"
DEFAULT_FORMULA_COURSES = [117, 44]
DEFAULT_HERB_COURSES = [45, 76, 41, 82]
MAX_QUIZDATA_BYTES = 64 * 1024 * 1024
EMU_PER_PIXEL = 9525
MAX_IMAGE_WIDTH_EMU = int(6.2 * 914400)
MAX_IMAGE_HEIGHT_EMU = int(7.0 * 914400)
EXPORT_MARKER = ".scorm-word-export"


def parse_course_ids(value: str) -> list[int]:
    ids: list[int] = []
    for raw in value.split(","):
        raw = raw.strip()
        if not raw.isdigit() or int(raw) <= 0:
            raise argparse.ArgumentTypeError(f"Invalid course id: {raw!r}")
        courseid = int(raw)
        if courseid not in ids:
            ids.append(courseid)
    if not ids:
        raise argparse.ArgumentTypeError("At least one course id is required")
    return ids


def clean_xml_text(value: Any) -> str:
    text = "" if value is None else str(value)
    text = text.replace("\r\n", "\n").replace("\r", "\n")
    return "".join(
        character
        for character in text
        if character in "\n\t"
        or 0x20 <= ord(character) <= 0xD7FF
        or 0xE000 <= ord(character) <= 0xFFFD
        or 0x10000 <= ord(character) <= 0x10FFFF
    )


def xml_escape(value: Any) -> str:
    return html.escape(clean_xml_text(value), quote=False)


def safe_segment(value: Any, fallback: str, max_length: int = 110) -> str:
    segment = clean_xml_text(value).strip()
    segment = re.sub(r"[\\/:*?\"<>|]+", " - ", segment)
    segment = re.sub(r"\s+", " ", segment).strip(" .-")
    if not segment:
        segment = fallback
    return segment[:max_length].rstrip(" .-") or fallback


def normalise_text(value: Any) -> str:
    return re.sub(r"\n{3,}", "\n\n", clean_xml_text(value).strip())


def paragraph_xml(
    text: Any,
    *,
    style: str | None = None,
    bold: bool = False,
    italic: bool = False,
    color: str | None = None,
    size: int | None = None,
    space_after: int = 100,
) -> str:
    value = normalise_text(text)
    properties = []
    if style:
        properties.append(f'<w:pStyle w:val="{xml_escape(style)}"/>')
    properties.append(f'<w:spacing w:after="{space_after}"/>')
    ppr = f"<w:pPr>{''.join(properties)}</w:pPr>"

    run_properties = []
    if bold:
        run_properties.append("<w:b/>")
    if italic:
        run_properties.append("<w:i/>")
    if color:
        run_properties.append(f'<w:color w:val="{xml_escape(color)}"/>')
    if size:
        run_properties.append(f'<w:sz w:val="{size}"/><w:szCs w:val="{size}"/>')
    rpr = f"<w:rPr>{''.join(run_properties)}</w:rPr>" if run_properties else ""

    lines = value.split("\n") if value else [""]
    runs = []
    for index, line in enumerate(lines):
        if index:
            runs.append("<w:r><w:br/></w:r>")
        runs.append(
            f'<w:r>{rpr}<w:t xml:space="preserve">{xml_escape(line)}</w:t></w:r>'
        )
    return f"<w:p>{ppr}{''.join(runs)}</w:p>"


def image_dimensions(data: bytes, extension: str) -> tuple[int, int]:
    extension = extension.lower()
    if extension == "png" and len(data) >= 24 and data[:8] == b"\x89PNG\r\n\x1a\n":
        return struct.unpack(">II", data[16:24])
    if extension == "gif" and len(data) >= 10 and data[:3] == b"GIF":
        return struct.unpack("<HH", data[6:10])
    if extension in {"jpg", "jpeg"} and data[:2] == b"\xff\xd8":
        offset = 2
        while offset + 9 < len(data):
            if data[offset] != 0xFF:
                offset += 1
                continue
            marker = data[offset + 1]
            offset += 2
            if marker in {0xD8, 0xD9}:
                continue
            if offset + 2 > len(data):
                break
            length = struct.unpack(">H", data[offset : offset + 2])[0]
            if marker in {
                0xC0,
                0xC1,
                0xC2,
                0xC3,
                0xC5,
                0xC6,
                0xC7,
                0xC9,
                0xCA,
                0xCB,
                0xCD,
                0xCE,
                0xCF,
            } and offset + 7 <= len(data):
                height, width = struct.unpack(">HH", data[offset + 3 : offset + 7])
                return width, height
            offset += max(length, 2)
    return 640, 480


class DocxBuilder:
    """Small OOXML writer with no third-party dependencies."""

    def __init__(self, title: str):
        self.title = title
        self.body: list[str] = []
        self.media: list[tuple[str, bytes, str]] = []
        self.image_relationships: list[tuple[str, str]] = []

    def add_paragraph(self, text: Any, **kwargs: Any) -> None:
        self.body.append(paragraph_xml(text, **kwargs))

    def add_image(self, data: bytes, extension: str, description: str) -> None:
        extension = extension.lower().lstrip(".")
        if extension == "jpeg":
            extension = "jpg"
        if extension not in {"png", "jpg", "gif"}:
            return

        number = len(self.media) + 1
        filename = f"image{number}.{extension}"
        relationship = f"rId{number + 1}"
        width, height = image_dimensions(data, extension)
        cx = max(width, 1) * EMU_PER_PIXEL
        cy = max(height, 1) * EMU_PER_PIXEL
        scale = min(1.0, MAX_IMAGE_WIDTH_EMU / cx, MAX_IMAGE_HEIGHT_EMU / cy)
        cx = int(cx * scale)
        cy = int(cy * scale)

        self.media.append((filename, data, extension))
        self.image_relationships.append((relationship, filename))
        name = xml_escape(description or filename)
        drawing = f"""
<w:p><w:pPr><w:spacing w:after="120"/></w:pPr><w:r><w:drawing>
<wp:inline distT="0" distB="0" distL="0" distR="0"
 xmlns:wp="http://schemas.openxmlformats.org/drawingml/2006/wordprocessingDrawing">
 <wp:extent cx="{cx}" cy="{cy}"/>
 <wp:docPr id="{number}" name="{name}" descr="{name}"/>
 <a:graphic xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main">
  <a:graphicData uri="http://schemas.openxmlformats.org/drawingml/2006/picture">
   <pic:pic xmlns:pic="http://schemas.openxmlformats.org/drawingml/2006/picture">
    <pic:nvPicPr><pic:cNvPr id="0" name="{name}"/><pic:cNvPicPr/></pic:nvPicPr>
    <pic:blipFill><a:blip r:embed="{relationship}"/><a:stretch><a:fillRect/></a:stretch></pic:blipFill>
    <pic:spPr><a:xfrm><a:off x="0" y="0"/><a:ext cx="{cx}" cy="{cy}"/></a:xfrm>
     <a:prstGeom prst="rect"><a:avLst/></a:prstGeom>
    </pic:spPr>
   </pic:pic>
  </a:graphicData>
 </a:graphic>
</wp:inline></w:drawing></w:r></w:p>"""
        self.body.append(drawing)

    def write(self, destination: Path) -> None:
        destination.parent.mkdir(parents=True, exist_ok=True)
        timestamp = dt.datetime.now(dt.timezone.utc).replace(microsecond=0).isoformat()
        document = f'''<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main"
 xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
 <w:body>{''.join(self.body)}
  <w:sectPr><w:pgSz w:w="12240" w:h="15840"/>
   <w:pgMar w:top="1080" w:right="1080" w:bottom="1080" w:left="1080" w:header="720" w:footer="720" w:gutter="0"/>
  </w:sectPr>
 </w:body>
</w:document>'''
        styles = '''<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<w:styles xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">
 <w:docDefaults><w:rPrDefault><w:rPr><w:rFonts w:ascii="Aptos" w:hAnsi="Aptos"/><w:sz w:val="22"/></w:rPr></w:rPrDefault></w:docDefaults>
 <w:style w:type="paragraph" w:default="1" w:styleId="Normal"><w:name w:val="Normal"/></w:style>
 <w:style w:type="paragraph" w:styleId="Title"><w:name w:val="Title"/><w:basedOn w:val="Normal"/><w:next w:val="Normal"/><w:rPr><w:b/><w:color w:val="17324D"/><w:sz w:val="36"/></w:rPr></w:style>
 <w:style w:type="paragraph" w:styleId="Heading1"><w:name w:val="heading 1"/><w:basedOn w:val="Normal"/><w:next w:val="Normal"/><w:rPr><w:b/><w:color w:val="17324D"/><w:sz w:val="28"/></w:rPr></w:style>
 <w:style w:type="paragraph" w:styleId="Heading2"><w:name w:val="heading 2"/><w:basedOn w:val="Normal"/><w:next w:val="Normal"/><w:rPr><w:b/><w:color w:val="F47E2C"/><w:sz w:val="24"/></w:rPr></w:style>
</w:styles>'''
        relationships = [
            '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>'
        ]
        for relationship, filename in self.image_relationships:
            relationships.append(
                f'<Relationship Id="{relationship}" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/image" Target="media/{filename}"/>'
            )
        document_relationships = f'''<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
 {''.join(relationships)}
</Relationships>'''
        extensions = {extension for _, _, extension in self.media}
        image_types = []
        if "png" in extensions:
            image_types.append('<Default Extension="png" ContentType="image/png"/>')
        if "jpg" in extensions:
            image_types.append('<Default Extension="jpg" ContentType="image/jpeg"/>')
        if "gif" in extensions:
            image_types.append('<Default Extension="gif" ContentType="image/gif"/>')
        content_types = f'''<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
 <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
 <Default Extension="xml" ContentType="application/xml"/>
 {''.join(image_types)}
 <Override PartName="/word/document.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml"/>
 <Override PartName="/word/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.styles+xml"/>
 <Override PartName="/docProps/core.xml" ContentType="application/vnd.openxmlformats-package.core-properties+xml"/>
 <Override PartName="/docProps/app.xml" ContentType="application/vnd.openxmlformats-officedocument.extended-properties+xml"/>
</Types>'''
        package_relationships = '''<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
 <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="word/document.xml"/>
 <Relationship Id="rId2" Type="http://schemas.openxmlformats.org/package/2006/relationships/metadata/core-properties" Target="docProps/core.xml"/>
 <Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/extended-properties" Target="docProps/app.xml"/>
</Relationships>'''
        core = f'''<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<cp:coreProperties xmlns:cp="http://schemas.openxmlformats.org/package/2006/metadata/core-properties"
 xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:dcterms="http://purl.org/dc/terms/"
 xmlns:dcmitype="http://purl.org/dc/dcmitype/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
 <dc:title>{xml_escape(self.title)}</dc:title><dc:creator>AcupunctureTests</dc:creator>
 <cp:lastModifiedBy>AcupunctureTests SCORM Word Exporter</cp:lastModifiedBy>
 <dcterms:created xsi:type="dcterms:W3CDTF">{timestamp}</dcterms:created>
 <dcterms:modified xsi:type="dcterms:W3CDTF">{timestamp}</dcterms:modified>
</cp:coreProperties>'''
        app = '''<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Properties xmlns="http://schemas.openxmlformats.org/officeDocument/2006/extended-properties"
 xmlns:vt="http://schemas.openxmlformats.org/officeDocument/2006/docPropsVTypes">
 <Application>AcupunctureTests SCORM Word Exporter</Application>
</Properties>'''

        with zipfile.ZipFile(destination, "w", zipfile.ZIP_DEFLATED) as docx:
            docx.writestr("[Content_Types].xml", content_types)
            docx.writestr("_rels/.rels", package_relationships)
            docx.writestr("word/document.xml", document)
            docx.writestr("word/styles.xml", styles)
            docx.writestr("word/_rels/document.xml.rels", document_relationships)
            docx.writestr("docProps/core.xml", core)
            docx.writestr("docProps/app.xml", app)
            for filename, data, _ in self.media:
                docx.writestr(f"word/media/{filename}", data)


class PackageImages:
    def __init__(self, package: zipfile.ZipFile):
        self.package = package
        self.by_path: dict[str, str] = {}
        self.by_name: dict[str, str] = {}
        for member in package.namelist():
            normalised = member.replace("\\", "/").lstrip("/")
            self.by_path[normalised.lower()] = member
            self.by_name[PurePosixPath(normalised).name.lower()] = member

    def read(self, reference: Any) -> tuple[bytes, str] | None:
        if not reference or not isinstance(reference, str):
            return None
        parsed = urlparse(unquote(reference))
        if parsed.scheme in {"http", "https", "data"}:
            return None
        candidate = parsed.path.replace("\\", "/").lstrip("/")
        member = self.by_path.get(candidate.lower())
        if not member:
            member = self.by_name.get(PurePosixPath(candidate).name.lower())
        if not member:
            return None
        extension = PurePosixPath(member).suffix.lower().lstrip(".")
        if extension not in {"png", "jpg", "jpeg", "gif"}:
            return None
        return self.package.read(member), extension


def add_referenced_images(
    builder: DocxBuilder,
    images: PackageImages,
    references: Iterable[Any],
    description: str,
) -> None:
    seen: set[str] = set()
    for reference in references:
        key = str(reference)
        if key in seen:
            continue
        seen.add(key)
        image = images.read(reference)
        if image:
            builder.add_image(image[0], image[1], description)


def answer_text(answer: Any) -> str:
    if isinstance(answer, dict):
        for key in ("Content", "Text", "Answer", "Name"):
            if key in answer:
                return normalise_text(answer[key])
        return json.dumps(answer, ensure_ascii=False, sort_keys=True)
    if isinstance(answer, (list, tuple)):
        return " | ".join(answer_text(part) for part in answer)
    return normalise_text(answer)


def add_question(
    builder: DocxBuilder,
    question: dict[str, Any],
    number: int,
    images: PackageImages,
) -> None:
    qtype = str(question.get("__type") or "Unknown")
    builder.add_paragraph(f"Question {number}", style="Heading2", space_after=60)
    builder.add_paragraph(f"Type: {qtype}", italic=True, color="666666", size=18)
    builder.add_paragraph(question.get("QuestionText", ""), space_after=140)

    question_images: list[Any] = []
    imageurl = question.get("ImageURL")
    if imageurl:
        question_images.append(imageurl)
    imageurls = question.get("ImageURLs") or []
    if isinstance(imageurls, list):
        question_images.extend(imageurls)
    add_referenced_images(builder, images, question_images, f"Question {number} image")

    if qtype == "MatchingQuestion":
        left = {item.get("Id"): answer_text(item) for item in question.get("LeftColumn", [])}
        right = {item.get("Id"): answer_text(item) for item in question.get("RightColumn", [])}
        builder.add_paragraph("Correct matches:", bold=True)
        for itemid, leftvalue in left.items():
            builder.add_paragraph(f"{leftvalue} → {right.get(itemid, '[missing match]')}")
    elif qtype == "MultipleResponseQuestion":
        answers = question.get("Answers") or []
        builder.add_paragraph("Options:", bold=True)
        correct = []
        for index, item in enumerate(answers):
            iscorrect = False
            value: Any = item
            if isinstance(item, (list, tuple)) and len(item) >= 2:
                iscorrect = bool(item[0])
                value = item[1]
            elif isinstance(item, dict):
                iscorrect = bool(item.get("Correct"))
                value = item.get("Content", item)
            label = chr(65 + index) if index < 26 else str(index + 1)
            builder.add_paragraph(f"{label}) {answer_text(value)}")
            if iscorrect:
                correct.append(answer_text(value))
        builder.add_paragraph("Correct answer(s): " + "; ".join(correct), bold=True, color="167A3A")
    elif qtype == "SequenceQuestion":
        items = question.get("Items") or []
        builder.add_paragraph("Correct sequence:", bold=True)
        for index, item in enumerate(items, start=1):
            builder.add_paragraph(f"{index}. {answer_text(item)}")
    elif qtype == "ClickMapQuestion":
        correct = [item for item in question.get("Answers", []) if item.get("Correct")]
        if correct:
            values = []
            for item in correct:
                data = item.get("Data") or []
                values.append(f"{item.get('Name', 'Region')} at {data}")
            builder.add_paragraph("Correct hotspot: " + "; ".join(values), bold=True, color="167A3A")
    else:
        answers = question.get("Answers") or []
        if isinstance(answers, list) and answers:
            builder.add_paragraph("Options:", bold=True)
            answer_images = question.get("AnswerImageURLs") or []
            for index, item in enumerate(answers):
                label = chr(65 + index) if index < 26 else str(index + 1)
                builder.add_paragraph(f"{label}) {answer_text(item)}")
                if isinstance(answer_images, list) and index < len(answer_images):
                    add_referenced_images(
                        builder,
                        images,
                        [answer_images[index]],
                        f"Question {number}, option {label}",
                    )
        rightanswer = question.get("RightAnswer")
        if rightanswer not in (None, ""):
            builder.add_paragraph(
                "Correct answer: " + answer_text(rightanswer),
                bold=True,
                color="167A3A",
            )
        elif qtype == "FillInTheBlankQuestion" and answers:
            builder.add_paragraph(
                "Accepted answer(s): " + "; ".join(answer_text(item) for item in answers),
                bold=True,
                color="167A3A",
            )

    notes = normalise_text(question.get("QuestionNotes"))
    if notes:
        builder.add_paragraph("Notes: " + notes, italic=True)

    feedback = question.get("Feedback") or []
    if isinstance(feedback, list):
        labels = ["Correct-answer feedback", "Incorrect-answer feedback"]
        for index, value in enumerate(feedback):
            text = normalise_text(value)
            if text:
                label = labels[index] if index < len(labels) else f"Feedback {index + 1}"
                builder.add_paragraph(f"{label}: {text}", italic=True, color="555555")

    builder.add_paragraph("", space_after=180)


def find_quizdata_member(package: zipfile.ZipFile) -> str | None:
    candidates = [
        member
        for member in package.namelist()
        if PurePosixPath(member.replace("\\", "/")).name.lower() == "quizdata.json"
    ]
    if not candidates:
        return None
    return sorted(candidates, key=lambda item: (item.count("/"), len(item)))[0]


def build_document(
    package_path: Path,
    destination: Path,
    placement: dict[str, Any],
) -> int:
    with zipfile.ZipFile(package_path, "r") as package:
        member = find_quizdata_member(package)
        if not member:
            raise ValueError("QUIZDATA.json not found")
        info = package.getinfo(member)
        if info.file_size > MAX_QUIZDATA_BYTES:
            raise ValueError("QUIZDATA.json exceeds the 64 MiB safety limit")
        quizdata = json.loads(package.read(member).decode("utf-8-sig"))
        questions = quizdata.get("Questions")
        if not isinstance(questions, list):
            raise ValueError("QUIZDATA.json has no Questions array")

        title = str(placement.get("scormname") or "SCORM Exam")
        builder = DocxBuilder(title)
        builder.add_paragraph(title, style="Title", space_after=160)
        builder.add_paragraph(
            f"Course: {placement.get('coursename', '')}\n"
            f"Section: {placement.get('sectionname') or 'Unlabelled section'}\n"
            f"Questions: {len(questions)}\n"
            f"Source CMID: {placement.get('cmid', '')}",
            color="555555",
        )
        builder.add_paragraph(
            "Answer key and source feedback are included for instructor review.",
            italic=True,
            color="8A3F0A",
            space_after=200,
        )
        images = PackageImages(package)
        for number, question in enumerate(questions, start=1):
            if isinstance(question, dict):
                add_question(builder, question, number, images)
            else:
                builder.add_paragraph(f"Question {number}", style="Heading2")
                builder.add_paragraph(answer_text(question))
        builder.write(destination)
        return len(questions)


def load_inventory(php_binary: str, courseids: list[int]) -> dict[str, Any]:
    command = [
        php_binary,
        str(INVENTORY_HELPER),
        "--courses=" + ",".join(str(courseid) for courseid in courseids),
    ]
    completed = subprocess.run(command, check=True, capture_output=True, text=True)
    return json.loads(completed.stdout)


def package_path(dataroot: Path, contenthash: str) -> Path:
    if not re.fullmatch(r"[0-9a-f]{40}", contenthash):
        raise ValueError(f"Invalid Moodle content hash: {contenthash!r}")
    return dataroot / "filedir" / contenthash[:2] / contenthash[2:4] / contenthash


def choose_placements(
    records: list[dict[str, Any]],
    courseids: list[int],
) -> dict[str, list[dict[str, Any]]]:
    priority = {courseid: index for index, courseid in enumerate(courseids)}
    newest_by_activity: dict[tuple[int, int], dict[str, Any]] = {}
    for record in records:
        courseid = int(record["courseid"])
        if courseid not in priority:
            continue
        key = (courseid, int(record["cmid"]))
        existing = newest_by_activity.get(key)
        if not existing or int(record.get("timemodified") or 0) > int(existing.get("timemodified") or 0):
            newest_by_activity[key] = record

    grouped: dict[str, list[dict[str, Any]]] = defaultdict(list)
    for record in newest_by_activity.values():
        grouped[str(record["contenthash"])].append(record)

    for placements in grouped.values():
        placements.sort(
            key=lambda item: (
                priority[int(item["courseid"])],
                int(item.get("sectionnumber") or 0),
                str(item.get("scormname") or ""),
                int(item.get("cmid") or 0),
            )
        )
    return dict(grouped)


def unique_destination(
    root: Path,
    placement: dict[str, Any],
    contenthash: str,
    used: set[Path],
) -> Path:
    course = safe_segment(placement.get("coursename"), f"Course {placement.get('courseid')}")
    section = safe_segment(placement.get("sectionname"), "Unlabelled Section")
    exam = safe_segment(placement.get("scormname"), f"Exam {placement.get('scormid')}")
    destination = root / course / section / f"{exam}.docx"
    if destination in used:
        destination = destination.with_name(f"{exam} [{contenthash[:8]}].docx")
    used.add(destination)
    return destination


def write_zip(source: Path, destination: Path) -> None:
    with zipfile.ZipFile(destination, "w", zipfile.ZIP_DEFLATED, allowZip64=True) as archive:
        for path in sorted(source.rglob("*")):
            if path.is_file():
                archive.write(path, path.relative_to(source.parent))


def export_category(
    category: str,
    records: list[dict[str, Any]],
    courseids: list[int],
    dataroot: Path,
    root: Path,
) -> dict[str, Any]:
    grouped = choose_placements(records, courseids)
    category_root = root / category
    category_root.mkdir(parents=True, exist_ok=True)
    manifest_rows: list[dict[str, Any]] = []
    errors: list[dict[str, Any]] = []
    used: set[Path] = set()

    for index, (contenthash, placements) in enumerate(sorted(grouped.items()), start=1):
        primary = placements[0]
        destination = unique_destination(category_root, primary, contenthash, used)
        source = package_path(dataroot, contenthash)
        row = {
            "category": category,
            "contenthash": contenthash,
            "courseid": primary.get("courseid"),
            "course": primary.get("coursename"),
            "section": primary.get("sectionname"),
            "exam": primary.get("scormname"),
            "cmid": primary.get("cmid"),
            "package": primary.get("packagefilename"),
            "docx": str(destination.relative_to(root)),
            "placements": placements,
        }
        try:
            if not source.is_file():
                raise FileNotFoundError(f"Moodle file-pool object not found: {source}")
            row["questions"] = build_document(source, destination, primary)
            manifest_rows.append(row)
        except Exception as exc:  # Continue so one damaged legacy package does not lose the rest.
            row["error"] = f"{type(exc).__name__}: {exc}"
            errors.append(row)

        if index % 25 == 0 or index == len(grouped):
            print(f"{category}: processed {index}/{len(grouped)} unique packages", flush=True)

    csv_path = category_root / "manifest.csv"
    with csv_path.open("w", encoding="utf-8-sig", newline="") as handle:
        fields = [
            "courseid",
            "course",
            "section",
            "exam",
            "cmid",
            "package",
            "contenthash",
            "questions",
            "docx",
        ]
        writer = csv.DictWriter(handle, fieldnames=fields, extrasaction="ignore")
        writer.writeheader()
        writer.writerows(manifest_rows)

    (category_root / "manifest.json").write_text(
        json.dumps(manifest_rows, ensure_ascii=False, indent=2) + "\n",
        encoding="utf-8",
    )
    (category_root / "errors.json").write_text(
        json.dumps(errors, ensure_ascii=False, indent=2) + "\n",
        encoding="utf-8",
    )
    (category_root / "README.txt").write_text(
        "AcupunctureTests SCORM Word Export\n"
        f"Category: {category}\n"
        f"Unique packages discovered: {len(grouped)}\n"
        f"DOCX files generated: {len(manifest_rows)}\n"
        f"Packages with errors: {len(errors)}\n\n"
        "Each document includes the source questions, answer key, and available feedback.\n"
        "Duplicate Moodle activities that use the same package are represented once.\n",
        encoding="utf-8",
    )
    zip_path = root / f"{category}_Exams_Word.zip"
    write_zip(category_root, zip_path)
    return {
        "category": category,
        "courseids": courseids,
        "unique_packages": len(grouped),
        "documents": len(manifest_rows),
        "errors": len(errors),
        "zip": zip_path.name,
    }


def parse_arguments() -> argparse.Namespace:
    timestamp = dt.datetime.now().strftime("%Y%m%d-%H%M%S")
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument(
        "--formula-courses",
        type=parse_course_ids,
        default=DEFAULT_FORMULA_COURSES,
        help="Comma-separated Formula course ids; first id has duplicate preference",
    )
    parser.add_argument(
        "--herb-courses",
        type=parse_course_ids,
        default=DEFAULT_HERB_COURSES,
        help="Comma-separated Herb course ids; first id has duplicate preference",
    )
    parser.add_argument(
        "--output",
        type=Path,
        default=Path("/var/tmp") / f"formula-herb-word-export-{timestamp}",
        help="New directory for generated documents and ZIP archives",
    )
    parser.add_argument("--php-bin", default="php", help="PHP CLI executable")
    parser.add_argument("--overwrite", action="store_true", help="Replace an existing output directory")
    return parser.parse_args()


def main() -> int:
    args = parse_arguments()
    all_courseids = list(dict.fromkeys(args.formula_courses + args.herb_courses))
    inventory = load_inventory(args.php_bin, all_courseids)
    dataroot = Path(inventory["dataroot"]).resolve()
    records = inventory.get("records") or []
    output = args.output.resolve()

    forbidden = {Path("/").resolve(), Path.home().resolve(), dataroot}
    if output in forbidden:
        raise SystemExit(f"Refusing unsafe output directory: {output}")
    if output.exists():
        if not args.overwrite:
            raise SystemExit(f"Output already exists; choose a new path or use --overwrite: {output}")
        marker = output / EXPORT_MARKER
        if not marker.is_file():
            raise SystemExit(
                "Refusing to overwrite a directory not created by this exporter: "
                f"{output}"
            )
        shutil.rmtree(output)

    output.parent.mkdir(parents=True, exist_ok=True)
    staging = Path(tempfile.mkdtemp(prefix=f".{output.name}-", dir=output.parent))
    try:
        (staging / EXPORT_MARKER).write_text(
            "Created by scorm_word_export.py; permits explicit --overwrite.\n",
            encoding="utf-8",
        )
        summaries = [
            export_category("Formulas", records, args.formula_courses, dataroot, staging),
            export_category("Herbs", records, args.herb_courses, dataroot, staging),
        ]
        (staging / "export-summary.json").write_text(
            json.dumps(summaries, ensure_ascii=False, indent=2) + "\n",
            encoding="utf-8",
        )
        staging.rename(output)
    except Exception:
        shutil.rmtree(staging, ignore_errors=True)
        raise

    print("Export complete")
    print(f"Output: {output}")
    for summary in summaries:
        final_zip = output / summary["zip"]
        print(
            f"{summary['category']}: {summary['documents']} DOCX, "
            f"{summary['errors']} errors, ZIP {final_zip}"
        )
    return 0


if __name__ == "__main__":
    try:
        raise SystemExit(main())
    except subprocess.CalledProcessError as exc:
        print(exc.stderr or str(exc), file=sys.stderr)
        raise SystemExit(exc.returncode or 1)
