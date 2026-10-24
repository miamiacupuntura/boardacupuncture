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
    space_before: int = 0,
    space_after: int = 100,
    keep_next: bool = False,
    keep_lines: bool = False,
    page_break_before: bool = False,
    alignment: str | None = None,
    fill: str | None = None,
    border_color: str | None = None,
    left_indent: int | None = None,
    right_indent: int | None = None,
) -> str:
    value = normalise_text(text)
    properties = []
    if style:
        properties.append(f'<w:pStyle w:val="{xml_escape(style)}"/>')
    if keep_next:
        properties.append('<w:keepNext/>')
    if keep_lines:
        properties.append('<w:keepLines/>')
    if page_break_before:
        properties.append('<w:pageBreakBefore/>')
    properties.append(f'<w:spacing w:before="{space_before}" w:after="{space_after}"/>')
    if alignment:
        properties.append(f'<w:jc w:val="{xml_escape(alignment)}"/>')
    if left_indent is not None or right_indent is not None:
        properties.append(
            f'<w:ind w:left="{left_indent or 0}" w:right="{right_indent or 0}"/>'
        )
    if fill:
        properties.append(f'<w:shd w:val="clear" w:color="auto" w:fill="{xml_escape(fill)}"/>')
    if border_color:
        properties.append(
            '<w:pBdr>'
            f'<w:left w:val="single" w:sz="20" w:space="8" w:color="{xml_escape(border_color)}"/>'
            '</w:pBdr>'
        )
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

    def add_page_break(self) -> None:
        self.body.append('<w:p><w:r><w:br w:type="page"/></w:r></w:p>')

    def add_image(self, data: bytes, extension: str, description: str) -> None:
        extension = extension.lower().lstrip(".")
        if extension == "jpeg":
            extension = "jpg"
        if extension not in {"png", "jpg", "gif"}:
            return

        number = len(self.media) + 1
        filename = f"image{number}.{extension}"
        relationship = f"rId{number + 4}"
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

    def add_key_value_card(
        self,
        title: str,
        rows: list[tuple[str, str]],
        *,
        header_fill: str = "F47E2C",
    ) -> None:
        """Add a compact full-width card with aligned labels and values."""
        header = (
            '<w:tr><w:trPr><w:cantSplit/></w:trPr>'
            '<w:tc><w:tcPr><w:gridSpan w:val="2"/><w:shd w:val="clear" '
            f'w:color="auto" w:fill="{header_fill}"/></w:tcPr>'
            + paragraph_xml(
                title,
                bold=True,
                color="FFFFFF",
                size=22,
                space_after=0,
                keep_next=True,
                keep_lines=True,
            )
            + '</w:tc></w:tr>'
        )
        bodyrows = []
        for rownumber, (label, value) in enumerate(rows):
            keepwithnext = rownumber < len(rows) - 1
            bodyrows.append(
                '<w:tr><w:trPr><w:cantSplit/></w:trPr>'
                '<w:tc><w:tcPr><w:tcW w:w="1900" w:type="dxa"/>'
                '<w:shd w:val="clear" w:color="auto" w:fill="F2F6F8"/>'
                '<w:vAlign w:val="top"/></w:tcPr>'
                + paragraph_xml(
                    label,
                    bold=True,
                    color="17324D",
                    space_after=0,
                    keep_next=keepwithnext,
                    keep_lines=True,
                )
                + '</w:tc>'
                '<w:tc><w:tcPr><w:tcW w:w="7100" w:type="dxa"/>'
                '<w:shd w:val="clear" w:color="auto" w:fill="FFFFFF"/>'
                '<w:vAlign w:val="top"/></w:tcPr>'
                + paragraph_xml(
                    value,
                    color="203040",
                    space_after=0,
                    keep_next=keepwithnext,
                    keep_lines=True,
                )
                + '</w:tc></w:tr>'
            )
        table = (
            '<w:tbl><w:tblPr><w:tblW w:w="9000" w:type="dxa"/>'
            '<w:tblLayout w:type="fixed"/>'
            '<w:tblBorders>'
            '<w:top w:val="single" w:sz="8" w:color="D6E1E7"/>'
            '<w:left w:val="single" w:sz="8" w:color="D6E1E7"/>'
            '<w:bottom w:val="single" w:sz="8" w:color="D6E1E7"/>'
            '<w:right w:val="single" w:sz="8" w:color="D6E1E7"/>'
            '<w:insideH w:val="single" w:sz="6" w:color="D6E1E7"/>'
            '<w:insideV w:val="single" w:sz="6" w:color="D6E1E7"/>'
            '</w:tblBorders>'
            '<w:tblCellMar>'
            '<w:top w:w="100" w:type="dxa"/><w:left w:w="120" w:type="dxa"/>'
            '<w:bottom w:w="100" w:type="dxa"/><w:right w:w="120" w:type="dxa"/>'
            '</w:tblCellMar></w:tblPr>'
            '<w:tblGrid><w:gridCol w:w="1900"/><w:gridCol w:w="7100"/></w:tblGrid>'
            + header
            + ''.join(bodyrows)
            + '</w:tbl>'
        )
        self.body.append(table)
        self.add_paragraph("", space_after=120)

    def add_comparison_table(
        self,
        title: str,
        columns: list[str],
        rows: list[tuple[str, list[str]]],
        *,
        header_fill: str = "F47E2C",
        corner_label: str = "Compare",
    ) -> None:
        """Add a side-by-side comparison table for two keyed Matching items."""
        if len(columns) != 2:
            raise ValueError("Comparison tables require exactly two item columns")

        widths = [1500, 3750, 3750]

        def cell(
            value: str,
            width: int,
            *,
            fill: str,
            bold: bool = False,
            color: str = "203040",
            span: int = 1,
            keep_next: bool = False,
        ) -> str:
            spanxml = f'<w:gridSpan w:val="{span}"/>' if span > 1 else ""
            return (
                '<w:tc><w:tcPr>'
                + spanxml
                + f'<w:tcW w:w="{width}" w:type="dxa"/>'
                + f'<w:shd w:val="clear" w:color="auto" w:fill="{fill}"/>'
                + '<w:vAlign w:val="top"/></w:tcPr>'
                + paragraph_xml(
                    value,
                    bold=bold,
                    color=color,
                    space_after=0,
                    keep_next=keep_next,
                    keep_lines=True,
                )
                + '</w:tc>'
            )

        title_row = (
            '<w:tr><w:trPr><w:cantSplit/></w:trPr>'
            + cell(
                title,
                sum(widths),
                fill=header_fill,
                bold=True,
                color="FFFFFF",
                span=3,
                keep_next=True,
            )
            + '</w:tr>'
        )
        heading_row = (
            '<w:tr><w:trPr><w:cantSplit/></w:trPr>'
            + cell(corner_label, widths[0], fill="DDEAF0", bold=True, keep_next=True)
            + cell(columns[0], widths[1], fill="DDEAF0", bold=True, keep_next=True)
            + cell(columns[1], widths[2], fill="DDEAF0", bold=True, keep_next=True)
            + '</w:tr>'
        )
        datarows = []
        for rownumber, (label, values) in enumerate(rows):
            keepwithnext = rownumber < len(rows) - 1
            highlightshared = label.casefold() == "shared clues"
            labelfill = "FFE4B8" if highlightshared else "F2F6F8"
            valuefill = "FFF6E8" if highlightshared else "FFFFFF"
            valuecolor = "8A3F0A" if highlightshared else "203040"
            rowxml = (
                '<w:tr><w:trPr><w:cantSplit/></w:trPr>'
                + cell(
                    label,
                    widths[0],
                    fill=labelfill,
                    bold=True,
                    color="8A3F0A" if highlightshared else "17324D",
                    keep_next=keepwithnext,
                )
            )
            if len(values) == 1:
                rowxml += cell(
                    values[0],
                    widths[1] + widths[2],
                    fill=valuefill,
                    bold=highlightshared,
                    color=valuecolor,
                    span=2,
                    keep_next=keepwithnext,
                )
            else:
                rowxml += cell(
                    values[0],
                    widths[1],
                    fill=valuefill,
                    bold=highlightshared,
                    color=valuecolor,
                    keep_next=keepwithnext,
                )
                rowxml += cell(
                    values[1],
                    widths[2],
                    fill=valuefill,
                    bold=highlightshared,
                    color=valuecolor,
                    keep_next=keepwithnext,
                )
            datarows.append(rowxml + '</w:tr>')

        table = (
            '<w:tbl><w:tblPr><w:tblW w:w="9000" w:type="dxa"/>'
            '<w:tblLayout w:type="fixed"/>'
            '<w:tblBorders>'
            '<w:top w:val="single" w:sz="8" w:color="D6E1E7"/>'
            '<w:left w:val="single" w:sz="8" w:color="D6E1E7"/>'
            '<w:bottom w:val="single" w:sz="8" w:color="D6E1E7"/>'
            '<w:right w:val="single" w:sz="8" w:color="D6E1E7"/>'
            '<w:insideH w:val="single" w:sz="6" w:color="D6E1E7"/>'
            '<w:insideV w:val="single" w:sz="6" w:color="D6E1E7"/>'
            '</w:tblBorders>'
            '<w:tblCellMar>'
            '<w:top w:w="100" w:type="dxa"/><w:left w:w="110" w:type="dxa"/>'
            '<w:bottom w:w="100" w:type="dxa"/><w:right w:w="110" w:type="dxa"/>'
            '</w:tblCellMar></w:tblPr>'
            '<w:tblGrid><w:gridCol w:w="1500"/><w:gridCol w:w="3750"/>'
            '<w:gridCol w:w="3750"/></w:tblGrid>'
            + title_row
            + heading_row
            + ''.join(datarows)
            + '</w:tbl>'
        )
        self.body.append(table)
        self.add_paragraph("", space_after=120)

    def write(self, destination: Path) -> None:
        destination.parent.mkdir(parents=True, exist_ok=True)
        timestamp = dt.datetime.now(dt.timezone.utc).replace(microsecond=0).isoformat()
        document = f'''<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main"
 xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
 <w:body>{''.join(self.body)}
  <w:sectPr><w:pgSz w:w="12240" w:h="15840"/>
   <w:pgMar w:top="1080" w:right="900" w:bottom="1080" w:left="900" w:header="540" w:footer="540" w:gutter="0"/>
   <w:headerReference w:type="default" r:id="rId2"/>
   <w:footerReference w:type="default" r:id="rId3"/>
  </w:sectPr>
 </w:body>
</w:document>'''
        styles = '''<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<w:styles xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">
 <w:docDefaults><w:rPrDefault><w:rPr><w:rFonts w:ascii="Aptos" w:hAnsi="Aptos" w:eastAsia="Aptos"/><w:color w:val="203040"/><w:sz w:val="21"/></w:rPr></w:rPrDefault></w:docDefaults>
 <w:style w:type="paragraph" w:default="1" w:styleId="Normal"><w:name w:val="Normal"/><w:pPr><w:spacing w:after="100" w:line="276" w:lineRule="auto"/></w:pPr></w:style>
 <w:style w:type="paragraph" w:styleId="BookBrand"><w:name w:val="Book Brand"/><w:basedOn w:val="Normal"/><w:pPr><w:spacing w:after="260"/><w:jc w:val="center"/></w:pPr><w:rPr><w:b/><w:color w:val="F47E2C"/><w:sz w:val="22"/><w:caps/></w:rPr></w:style>
 <w:style w:type="paragraph" w:styleId="Title"><w:name w:val="Title"/><w:basedOn w:val="Normal"/><w:next w:val="BookSubtitle"/><w:pPr><w:spacing w:before="240" w:after="240"/><w:jc w:val="center"/><w:keepNext/></w:pPr><w:rPr><w:b/><w:color w:val="17324D"/><w:sz w:val="48"/></w:rPr></w:style>
 <w:style w:type="paragraph" w:styleId="BookSubtitle"><w:name w:val="Book Subtitle"/><w:basedOn w:val="Normal"/><w:pPr><w:spacing w:after="360"/><w:jc w:val="center"/></w:pPr><w:rPr><w:color w:val="527286"/><w:sz w:val="26"/></w:rPr></w:style>
 <w:style w:type="paragraph" w:styleId="Heading1"><w:name w:val="heading 1"/><w:basedOn w:val="Normal"/><w:next w:val="Normal"/><w:pPr><w:keepNext/><w:keepLines/><w:spacing w:before="180" w:after="180"/><w:pBdr><w:bottom w:val="single" w:sz="16" w:space="8" w:color="F47E2C"/></w:pBdr></w:pPr><w:rPr><w:b/><w:color w:val="17324D"/><w:sz w:val="32"/></w:rPr></w:style>
 <w:style w:type="paragraph" w:styleId="Heading2"><w:name w:val="heading 2"/><w:basedOn w:val="Normal"/><w:next w:val="Normal"/><w:pPr><w:keepNext/><w:keepLines/><w:spacing w:before="220" w:after="100"/><w:shd w:val="clear" w:color="auto" w:fill="E8F3F5"/><w:pBdr><w:left w:val="single" w:sz="28" w:space="8" w:color="F47E2C"/></w:pBdr></w:pPr><w:rPr><w:b/><w:color w:val="17324D"/><w:sz w:val="26"/></w:rPr></w:style>
</w:styles>'''
        relationships = [
            '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>',
            '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/header" Target="header1.xml"/>',
            '<Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/footer" Target="footer1.xml"/>',
            '<Relationship Id="rId4" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/settings" Target="settings.xml"/>',
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
 <Override PartName="/word/header1.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.header+xml"/>
 <Override PartName="/word/footer1.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.footer+xml"/>
 <Override PartName="/word/settings.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.settings+xml"/>
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
        shorttitle = xml_escape(self.title[:90])
        header = f'''<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<w:hdr xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">
 <w:p><w:pPr><w:jc w:val="right"/><w:spacing w:after="80"/><w:pBdr><w:bottom w:val="single" w:sz="12" w:space="6" w:color="F47E2C"/></w:pBdr></w:pPr>
  <w:r><w:rPr><w:b/><w:color w:val="527286"/><w:sz w:val="17"/></w:rPr><w:t>{shorttitle}</w:t></w:r>
 </w:p>
</w:hdr>'''
        footer = '''<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<w:ftr xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">
 <w:p><w:pPr><w:jc w:val="center"/><w:spacing w:before="80"/><w:pBdr><w:top w:val="single" w:sz="8" w:space="6" w:color="D8E2E8"/></w:pBdr></w:pPr>
  <w:r><w:rPr><w:color w:val="527286"/><w:sz w:val="17"/></w:rPr><w:t xml:space="preserve">AcupunctureTests  •  Page </w:t></w:r>
  <w:fldSimple w:instr=" PAGE "><w:r><w:rPr><w:color w:val="527286"/><w:sz w:val="17"/></w:rPr><w:t>1</w:t></w:r></w:fldSimple>
 </w:p>
</w:ftr>'''
        settings = '''<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<w:settings xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">
 <w:compat/><w:doNotTrackMoves/><w:doNotTrackFormatting/>
</w:settings>'''
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
            docx.writestr("word/header1.xml", header)
            docx.writestr("word/footer1.xml", footer)
            docx.writestr("word/settings.xml", settings)
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


def question_type_label(value: str) -> str:
    """Convert SCORM class names into reader-friendly labels."""
    label = re.sub(r"Question$", "", value)
    label = re.sub(r"(?<!^)(?=[A-Z])", " ", label)
    return label.strip() or "Question"


def add_option(builder: DocxBuilder, label: str, value: Any) -> None:
    builder.add_paragraph(
        f"{label}) {answer_text(value)}",
        fill="F7F9FB",
        left_indent=180,
        right_indent=120,
        space_after=55,
        keep_lines=True,
    )


def add_correct_answer(builder: DocxBuilder, text: str) -> None:
    builder.add_paragraph(
        text,
        bold=True,
        color="167A3A",
        fill="EAF6EE",
        border_color="2E8B57",
        left_indent=180,
        right_indent=120,
        space_before=80,
        space_after=120,
        keep_lines=True,
    )


def split_combined_feedback(
    question: dict[str, Any],
    text: str,
) -> tuple[str, list[tuple[str, str, str]]] | None:
    """Split legacy combined feedback into a rationale and per-option explanations."""
    marker = "Why are the other options incorrect?"
    if marker not in text:
        return None

    correct_feedback, incorrect_feedback = text.split(marker, 1)
    correct_feedback = correct_feedback.strip()
    incorrect_feedback = incorrect_feedback.strip()
    answers = question.get("Answers") or []
    rightanswer = answer_text(question.get("RightAnswer"))
    positions = []
    for index, answer in enumerate(answers):
        value = answer_text(answer)
        if not value or value == rightanswer:
            continue
        needle = value + " —"
        position = incorrect_feedback.find(needle)
        if position >= 0:
            label = chr(65 + index) if index < 26 else str(index + 1)
            positions.append((position, label, value, len(needle)))

    positions.sort(key=lambda item: item[0])
    explanations = []
    for index, (position, label, value, needlelength) in enumerate(positions):
        start = position + needlelength
        end = positions[index + 1][0] if index + 1 < len(positions) else len(incorrect_feedback)
        explanation = incorrect_feedback[start:end].strip()
        if explanation:
            explanations.append((label, value, explanation))
    return correct_feedback, explanations


def question_learning_target(question: dict[str, Any]) -> str:
    """Extract a concise learning focus from a question stem."""
    text = normalise_text(question.get("QuestionText"))
    questiontype = str(question.get("__type") or "")
    if questiontype == "MatchingQuestion" and text:
        title = text.splitlines()[0].strip().rstrip(".")
        if title:
            return f'the matching exercise “{title}”'
    matches = re.findall(
        r"What (?:is|are) the modifications? for\s+([^?]+)",
        text,
        flags=re.IGNORECASE,
    )
    if matches:
        return matches[-1].strip().rstrip(".")

    ingredients = re.search(
        r"What (?:is|are) the ingredients of\s+([^?]+)",
        text,
        flags=re.IGNORECASE,
    )
    if ingredients:
        return f"the ingredients of {ingredients.group(1).strip().rstrip('.')}"

    presentation = re.search(
        r"Which of the following formula (?:can be used|is suitable) for\s+(.+?)"
        r"(?:\s+(?:manifested|marked) by|\?|$)",
        text,
        flags=re.IGNORECASE,
    )
    if presentation:
        return presentation.group(1).strip().rstrip(".")

    if re.search(r"\bNOT correct\b", text, flags=re.IGNORECASE):
        return "the NOT-correct statement requested in the question"
    if re.search(r"suitable (?:far|for) the case", text, flags=re.IGNORECASE):
        return "the clinical case described in the question"
    return "the question prompt"


def question_answer_key(question: dict[str, Any]) -> str:
    """Return a readable answer key for every supported SCORM question type."""
    questiontype = str(question.get("__type") or "")
    if questiontype == "MatchingQuestion":
        left = {item.get("Id"): answer_text(item) for item in question.get("LeftColumn", [])}
        right = {item.get("Id"): answer_text(item) for item in question.get("RightColumn", [])}
        return "; ".join(
            f"{leftvalue} → {right.get(itemid, '[missing match]')}"
            for itemid, leftvalue in left.items()
        )
    if questiontype == "MultipleResponseQuestion":
        correct = []
        for item in question.get("Answers", []):
            iscorrect = False
            value: Any = item
            if isinstance(item, (list, tuple)) and len(item) >= 2:
                iscorrect = bool(item[0])
                value = item[1]
            elif isinstance(item, dict):
                iscorrect = bool(item.get("Correct"))
                value = item.get("Content", item)
            if iscorrect:
                correct.append(answer_text(value))
        return "; ".join(correct)
    if questiontype == "SequenceQuestion":
        return "; ".join(
            f"{index}. {answer_text(item)}"
            for index, item in enumerate(question.get("Items", []), start=1)
        )
    if questiontype == "ClickMapQuestion":
        return "; ".join(
            f"{item.get('Name', 'Region')} at {item.get('Data') or []}"
            for item in question.get("Answers", [])
            if item.get("Correct")
        )

    rightanswer = answer_text(question.get("RightAnswer"))
    if rightanswer:
        return rightanswer
    if questiontype == "FillInTheBlankQuestion":
        return "; ".join(
            answer_text(item) for item in question.get("Answers", []) if answer_text(item)
        )
    return ""


def feedback_headings(question: dict[str, Any]) -> tuple[str, str]:
    """Use labels that make sense for the current interaction type."""
    questiontype = str(question.get("__type") or "")
    if questiontype == "FillInTheBlankQuestion":
        return "Why the accepted answer is correct", "How other responses are evaluated"
    if questiontype == "MatchingQuestion":
        return "Why these matches are correct", "How incorrect matches are evaluated"
    if questiontype == "MultipleResponseQuestion":
        return "Why these selections are correct", "Why the other options are incorrect"
    if questiontype == "SequenceQuestion":
        return "Why this sequence is correct", "How an incorrect sequence is evaluated"
    if questiontype == "ClickMapQuestion":
        return "Why this location is correct", "How other locations are evaluated"
    return "Why the correct answer is correct", "Why the other options are incorrect"


def matching_pair_details(question: dict[str, Any]) -> list[tuple[str, str, str, int]]:
    """Extract a short name, category, association label, and count for each pair."""
    right = {item.get("Id"): answer_text(item) for item in question.get("RightColumn", [])}
    details = []
    for item in question.get("LeftColumn", []):
        lefttext = answer_text(item)
        leftlines = [line.strip() for line in lefttext.splitlines() if line.strip()]
        name = leftlines[0] if leftlines else "Item"
        category = next(
            (
                line.split(":", 1)[1].strip().rstrip(".")
                for line in leftlines
                if line.casefold().startswith("category:") and ":" in line
            ),
            "the category shown in the keyed match",
        )
        righttext = right.get(item.get("Id"), "")
        rightlines = [line.strip() for line in righttext.splitlines() if line.strip()]
        header = rightlines[0].rstrip(":").casefold() if rightlines else ""
        if rightlines and rightlines[0].endswith(":"):
            associations = rightlines[1:]
        else:
            associations = rightlines
        associationlabel = "formula association" if header in {"formula", "formulas"} else "keyed association"
        details.append((name, category, associationlabel, len(associations)))
    return details


def matching_card_rows(lefttext: str, righttext: str) -> list[tuple[str, str]]:
    """Turn a potentially multiline matching pair into compact labelled rows."""
    leftlines = [line.strip() for line in lefttext.splitlines() if line.strip()]
    rows = [("Item", leftlines[0] if leftlines else "[missing item]")]
    details = []
    for line in leftlines[1:]:
        if line.casefold().startswith("category:") and ":" in line:
            rows.append(("Category", line.split(":", 1)[1].strip()))
        else:
            details.append(line)
    if details:
        rows.append(("Details", "\n".join(details)))

    rightlines = [line.strip() for line in righttext.splitlines() if line.strip()]
    if rightlines and rightlines[0].endswith(":"):
        heading = rightlines[0].rstrip(":").strip()
        values = rightlines[1:]
    else:
        heading = "Correct match"
        values = rightlines
    if not values:
        values = ["[missing match]"]
    label = heading
    if len(values) > 1:
        label += f" ({len(values)})"
    rows.append((label, "\n".join(f"• {value}" for value in values)))
    return rows


def matching_comparison_data(
    question: dict[str, Any],
) -> tuple[list[str], list[tuple[str, list[str]]]] | None:
    """Return aligned columns and rows when a Matching question compares two items."""
    leftitems = question.get("LeftColumn", [])
    rightitems = {
        item.get("Id"): answer_text(item) for item in question.get("RightColumn", [])
    }
    if len(leftitems) != 2:
        return None

    cards = []
    for item in leftitems:
        rows = matching_card_rows(
            answer_text(item),
            rightitems.get(item.get("Id"), "[missing match]"),
        )
        cards.append(rows)

    columns = [next(value for label, value in card if label == "Item") for card in cards]
    rowlabels = []
    valuesbycard = []
    for card in cards:
        cardvalues = {}
        for label, value in card:
            if label == "Item":
                continue
            normalizedlabel = re.sub(r"\s+\(\d+\)$", "", label)
            if normalizedlabel not in rowlabels:
                rowlabels.append(normalizedlabel)
            cardvalues[normalizedlabel] = value
        valuesbycard.append(cardvalues)

    rows = [
        (label, [valuesbycard[0].get(label, "—"), valuesbycard[1].get(label, "—")])
        for label in rowlabels
    ]
    return columns, rows


def matching_prompt_and_tips(questiontext: Any) -> tuple[str, list[tuple[str, str]]]:
    """Separate the Matching prompt from embedded clues intended as study tips."""
    lines = [line.strip() for line in normalise_text(questiontext).splitlines() if line.strip()]
    if not lines:
        return "", []
    prompt = lines[0]
    tips = []
    for line in lines[1:]:
        primary = re.match(
            r"Primary conditions or symptoms for which (.+?) may be prescribed:\s*(.*)",
            line,
            flags=re.IGNORECASE,
        )
        if primary:
            itemname = primary.group(1).strip()
            clues = [
                clue.strip()
                for clue in re.split(r"\.\s*", primary.group(2).strip())
                if clue.strip()
            ]
            tips.append((itemname, "\n".join(f"• {clue}" for clue in clues)))
        elif line.casefold().startswith("both "):
            tips.append(("Shared clues", line))
        else:
            tips.append(("Helpful clue", line))
    return prompt, tips


def matching_tip_comparison_data(
    tips: list[tuple[str, str]],
) -> tuple[list[str], list[tuple[str, list[str]]]] | None:
    """Align two item-specific tip lists and any shared clues side by side."""
    shared = [value for label, value in tips if label in {"Shared clues", "Helpful clue"}]
    itemtips = [(label, value) for label, value in tips if label not in {"Shared clues", "Helpful clue"}]
    if len(itemtips) != 2:
        return None

    columns = [label for label, _value in itemtips]
    rows = []
    if shared:
        rows.append(("Shared clues", ["\n".join(shared)]))
    rows.append(("Symptoms / conditions", [itemtips[0][1], itemtips[1][1]]))
    return columns, rows


def matching_feedback_summary(question: dict[str, Any], target: str) -> str:
    """Explain the evidence used by a Matching key without restating its long lists."""
    details = matching_pair_details(question)
    lines = [
        f"For {target}, the source key distinguishes the items by their classification "
        "and keyed associations, not by shared symptoms or actions alone."
    ]
    for index, (name, category, associationlabel, associationcount) in enumerate(
        details, start=1
    ):
        counttext = (
            f"{associationcount} {associationlabel}"
            if associationcount == 1
            else f"{associationcount} {associationlabel}s"
        )
        location = "comparison column" if len(details) == 2 else "Correct match card"
        lines.append(
            f"Match {index} — {name}: classified as {category}; paired with the "
            f"{counttext} shown in its {location}."
        )
    return "\n".join(lines)


def correct_feedback_summary(
    question: dict[str, Any],
    target: str,
    answerkey: str,
    hassourcereview: bool,
) -> str:
    """Explain the keyed response without repeating long keys already printed above."""
    questiontype = str(question.get("__type") or "")
    if questiontype == "FillInTheBlankQuestion":
        summary = (
            "The response or responses shown under Accepted answer(s) are the values keyed "
            "by the source activity."
        )
    elif questiontype == "MatchingQuestion":
        return matching_feedback_summary(question, target)
    elif questiontype == "MultipleResponseQuestion":
        summary = (
            "The selections shown under Correct answer(s) are the complete set keyed by "
            "the source activity."
        )
    elif questiontype == "SequenceQuestion":
        summary = (
            "The numbered items shown under Correct sequence are the order keyed by the "
            "source activity."
        )
    elif questiontype == "ClickMapQuestion":
        summary = (
            "The hotspot shown under Correct hotspot is the location keyed by the source "
            "activity."
        )
    else:
        summary = f"For {target}, the source bank answer key is {answerkey}."

    if hassourcereview:
        return summary + " The detailed source review above supplies the retained rationale."
    return summary + " The original SCORM question does not contain a more detailed rationale."


def question_incorrect_options(question: dict[str, Any]) -> list[tuple[str, str]]:
    """Return labelled distractors only for question types that expose options."""
    questiontype = str(question.get("__type") or "")
    if questiontype == "MultipleResponseQuestion":
        incorrect = []
        for index, item in enumerate(question.get("Answers", [])):
            iscorrect = False
            value: Any = item
            if isinstance(item, (list, tuple)) and len(item) >= 2:
                iscorrect = bool(item[0])
                value = item[1]
            elif isinstance(item, dict):
                iscorrect = bool(item.get("Correct"))
                value = item.get("Content", item)
            if not iscorrect:
                label = chr(65 + index) if index < 26 else str(index + 1)
                incorrect.append((label, answer_text(value)))
        return incorrect
    if questiontype in {
        "FillInTheBlankQuestion",
        "MatchingQuestion",
        "SequenceQuestion",
        "ClickMapQuestion",
    }:
        return []

    rightanswer = answer_text(question.get("RightAnswer"))
    incorrect = []
    for index, answer in enumerate(question.get("Answers", [])):
        value = answer_text(answer)
        if value and value != rightanswer:
            label = chr(65 + index) if index < 26 else str(index + 1)
            incorrect.append((label, value))
    return incorrect


def nonchoice_evaluation_guidance(question: dict[str, Any]) -> str:
    """Explain evaluation when a question has no selectable distractor list."""
    questiontype = str(question.get("__type") or "")
    if questiontype == "FillInTheBlankQuestion":
        return (
            "A response is accepted only when it matches one of the accepted answers "
            "shown above, subject to the matching rules configured in the source activity."
        )
    if questiontype == "MatchingQuestion":
        details = matching_pair_details(question)
        names = [name for name, _category, _label, _count in details]
        distinction = " and ".join(names[:2]) if len(names) == 2 else "the matched items"
        location = "comparison table" if len(details) == 2 else "numbered card"
        return (
            "Keep each item with the classification and association group shown in its "
            f"{location}. "
            f"Interchanging the groups for {distinction} would attach the source associations "
            "to the wrong item and is evaluated as an incorrect match."
        )
    if questiontype == "SequenceQuestion":
        return (
            "The items must follow the keyed order shown above. A different order is "
            "evaluated as incorrect by the source activity."
        )
    if questiontype == "ClickMapQuestion":
        return (
            "The selected point must fall within the keyed hotspot region. Locations outside "
            "that source region are evaluated as incorrect."
        )
    return ""


def annotate_related_answer_targets(questions: list[Any]) -> None:
    """Link repeated answer choices to the symptom they answer elsewhere.

    Several legacy banks repeat the same option set across a family of Mock
    questions.  When an option is correct in a related question, that gives us
    a useful, source-backed explanation for why it is a distractor here.
    """
    targets_by_answer: dict[str, set[str]] = defaultdict(set)
    for question in questions:
        if not isinstance(question, dict):
            continue
        target = question_learning_target(question)
        rightanswer = question_answer_key(question)
        if rightanswer and target != "the question prompt":
            targets_by_answer[normalise_text(rightanswer).casefold()].add(target)

    unique_targets = {
        answer: next(iter(targets))
        for answer, targets in targets_by_answer.items()
        if len(targets) == 1
    }
    for question in questions:
        if isinstance(question, dict):
            question["__related_answer_targets"] = unique_targets


def add_duplicate_source_feedback(
    builder: DocxBuilder,
    question: dict[str, Any],
    source_feedback: str,
) -> None:
    """Replace duplicated correct/incorrect source feedback with Mock-style guidance."""
    target = question_learning_target(question)
    rightanswer = question_answer_key(question)
    correctheading, incorrectheading = feedback_headings(question)
    questiontype = str(question.get("__type") or "")

    showsourcereview = questiontype != "MatchingQuestion"
    if showsourcereview:
        sourcereviewheading = (
            "Detailed source review" if len(source_feedback) > 1200 else "Source review"
        )
        builder.add_paragraph(
            sourcereviewheading,
            bold=True,
            color="17324D",
            space_before=100,
            space_after=50,
            keep_next=True,
        )
        builder.add_paragraph(
            source_feedback,
            color="31586B",
            fill="EEF7FA",
            border_color="58A6B8",
            left_indent=180,
            right_indent=120,
            space_after=100,
            keep_lines=True,
        )

    builder.add_paragraph(
        correctheading,
        bold=True,
        color="17324D",
        space_before=100,
        space_after=50,
        keep_next=True,
    )
    builder.add_paragraph(
        correct_feedback_summary(question, target, rightanswer, showsourcereview),
        color="245B38",
        fill="EAF6EE",
        border_color="2E8B57",
        left_indent=180,
        right_indent=120,
        space_after=100,
        keep_lines=True,
    )

    related_targets = question.get("__related_answer_targets") or {}
    incorrect = [
        (label, value, related_targets.get(normalise_text(value).casefold()))
        for label, value in question_incorrect_options(question)
    ]
    if incorrect:
        builder.add_paragraph(
            incorrectheading,
            bold=True,
            color="17324D",
            space_before=100,
            space_after=50,
            keep_next=True,
        )
        for label, value, related_target in incorrect:
            if related_target and related_target.casefold() != target.casefold():
                explanation = (
                    f"This option is keyed elsewhere in this review for {related_target}, "
                    f"not for {target}."
                )
            else:
                explanation = (
                    f"The source bank does not key this option as correct for {target}. "
                    "It represents a different answer distinction; compare the specific "
                    "pattern, symptom, ingredients, or analysis requested in the stem."
                )
            builder.add_paragraph(
                f"{label}) {value}\n{explanation}",
                color="6B3030",
                fill="FDF1F1",
                border_color="C96D6D",
                left_indent=180,
                right_indent=120,
                space_after=80,
                keep_lines=True,
            )
    else:
        evaluation = nonchoice_evaluation_guidance(question)
        if evaluation:
            builder.add_paragraph(
                incorrectheading,
                bold=True,
                color="17324D",
                space_before=100,
                space_after=50,
                keep_next=True,
            )
            builder.add_paragraph(
                evaluation,
                color="6B3030",
                fill="FDF1F1",
                border_color="C96D6D",
                left_indent=180,
                right_indent=120,
                space_after=80,
                keep_lines=True,
            )


def add_missing_source_feedback(builder: DocxBuilder, question: dict[str, Any]) -> None:
    """Supply transparent Mock-style guidance when legacy feedback is absent."""
    target = question_learning_target(question)
    rightanswer = question_answer_key(question)
    if not rightanswer:
        return
    correctheading, incorrectheading = feedback_headings(question)

    builder.add_paragraph(
        correctheading,
        bold=True,
        color="17324D",
        space_before=100,
        space_after=50,
        keep_next=True,
    )
    builder.add_paragraph(
        correct_feedback_summary(question, target, rightanswer, False),
        color="245B38",
        fill="EAF6EE",
        border_color="2E8B57",
        left_indent=180,
        right_indent=120,
        space_after=100,
        keep_lines=True,
    )

    related_targets = question.get("__related_answer_targets") or {}
    incorrect = [
        (label, value, related_targets.get(normalise_text(value).casefold()))
        for label, value in question_incorrect_options(question)
    ]

    if incorrect:
        builder.add_paragraph(
            incorrectheading,
            bold=True,
            color="17324D",
            space_before=100,
            space_after=50,
            keep_next=True,
        )
        for label, value, related_target in incorrect:
            if related_target and related_target.casefold() != target.casefold():
                explanation = (
                    f"This answer is keyed elsewhere in this review for {related_target}, "
                    f"not for {target}."
                )
            else:
                explanation = (
                    f"The source bank does not identify this option as correct for {target}. "
                    "No option-specific rationale was stored in the original SCORM question."
                )
            builder.add_paragraph(
                f"{label}) {value}\n{explanation}",
                color="6B3030",
                fill="FDF1F1",
                border_color="C96D6D",
                left_indent=180,
                right_indent=120,
                space_after=80,
                keep_lines=True,
            )
    else:
        evaluation = nonchoice_evaluation_guidance(question)
        if not evaluation:
            return
        builder.add_paragraph(
            incorrectheading,
            bold=True,
            color="17324D",
            space_before=100,
            space_after=50,
            keep_next=True,
        )
        builder.add_paragraph(
            evaluation
            + " No option-specific rationale was stored in the original SCORM question.",
            color="6B3030",
            fill="FDF1F1",
            border_color="C96D6D",
            left_indent=180,
            right_indent=120,
            space_after=80,
            keep_lines=True,
        )


def add_feedback(builder: DocxBuilder, question: dict[str, Any]) -> None:
    """Render source feedback as clearly labelled study-book callouts."""
    correctheading, incorrectheading = feedback_headings(question)
    feedback = question.get("Feedback") or []
    if not isinstance(feedback, list) or not any(normalise_text(value) for value in feedback):
        add_missing_source_feedback(builder, question)
        return

    feedbacktexts = [normalise_text(value) for value in feedback if normalise_text(value)]
    if len(feedbacktexts) >= 2 and len(set(feedbacktexts)) == 1:
        add_duplicate_source_feedback(builder, question, feedbacktexts[0])
        return

    for index, value in enumerate(feedback):
        text = normalise_text(value)
        if not text:
            continue
        combined = split_combined_feedback(question, text)
        if combined:
            correct_feedback, explanations = combined
            if correct_feedback:
                builder.add_paragraph(
                    correctheading,
                    bold=True,
                    color="17324D",
                    space_before=100,
                    space_after=50,
                    keep_next=True,
                )
                builder.add_paragraph(
                    correct_feedback,
                    color="245B38",
                    fill="EAF6EE",
                    border_color="2E8B57",
                    left_indent=180,
                    right_indent=120,
                    space_after=100,
                    keep_lines=True,
                )
            if explanations:
                builder.add_paragraph(
                    incorrectheading,
                    bold=True,
                    color="17324D",
                    space_before=100,
                    space_after=50,
                    keep_next=True,
                )
                for label, answer, explanation in explanations:
                    builder.add_paragraph(
                        f"{label}) {answer}\n{explanation}",
                        color="6B3030",
                        fill="FDF1F1",
                        border_color="C96D6D",
                        left_indent=180,
                        right_indent=120,
                        space_after=80,
                        keep_lines=True,
                    )
            continue

        label = "Correct-answer feedback" if index == 0 else "Incorrect-answer feedback"
        builder.add_paragraph(
            f"{label}: {text}",
            color="31586B",
            fill="EEF7FA",
            border_color="58A6B8",
            left_indent=180,
            right_indent=120,
            space_after=100,
            keep_lines=True,
        )


def add_question(
    builder: DocxBuilder,
    question: dict[str, Any],
    number: int,
    images: PackageImages,
) -> None:
    qtype = str(question.get("__type") or "Unknown")
    builder.add_paragraph(f"Question {number}", style="Heading2", space_after=70)
    builder.add_paragraph(
        question_type_label(qtype),
        italic=True,
        color="527286",
        size=18,
        left_indent=180,
        space_after=70,
        keep_next=True,
    )
    if qtype == "MatchingQuestion":
        questionprompt, studytips = matching_prompt_and_tips(question.get("QuestionText", ""))
        builder.add_paragraph(
            questionprompt,
            bold=True,
            size=22,
            left_indent=180,
            right_indent=120,
            space_after=120,
            keep_lines=True,
        )
        if studytips:
            tipcomparison = matching_tip_comparison_data(studytips)
            if tipcomparison:
                builder.add_comparison_table(
                    "Study tip · Clues to help answer",
                    tipcomparison[0],
                    tipcomparison[1],
                    header_fill="527286",
                    corner_label="Clue",
                )
            else:
                builder.add_key_value_card(
                    "Study tip · Clues to help answer",
                    studytips,
                    header_fill="527286",
                )
    else:
        builder.add_paragraph(
            question.get("QuestionText", ""),
            bold=True,
            size=22,
            left_indent=180,
            right_indent=120,
            space_after=150,
            keep_lines=True,
        )

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
        builder.add_paragraph("Correct matches", bold=True, color="17324D", keep_next=True)
        comparison = matching_comparison_data(question)
        if comparison:
            builder.add_comparison_table("Comparative answer key", comparison[0], comparison[1])
        else:
            for matchnumber, (itemid, leftvalue) in enumerate(left.items(), start=1):
                builder.add_key_value_card(
                    f"Match {matchnumber}",
                    matching_card_rows(
                        leftvalue,
                        right.get(itemid, "[missing match]"),
                    ),
                )
    elif qtype == "MultipleResponseQuestion":
        answers = question.get("Answers") or []
        builder.add_paragraph("Options", bold=True, color="17324D", keep_next=True)
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
            add_option(builder, label, value)
            if iscorrect:
                correct.append(answer_text(value))
        add_correct_answer(builder, "Correct answer(s): " + "; ".join(correct))
    elif qtype == "SequenceQuestion":
        items = question.get("Items") or []
        builder.add_paragraph("Correct sequence", bold=True, color="17324D", keep_next=True)
        for index, item in enumerate(items, start=1):
            add_correct_answer(builder, f"{index}. {answer_text(item)}")
    elif qtype == "ClickMapQuestion":
        correct = [item for item in question.get("Answers", []) if item.get("Correct")]
        if correct:
            values = []
            for item in correct:
                data = item.get("Data") or []
                values.append(f"{item.get('Name', 'Region')} at {data}")
            add_correct_answer(builder, "Correct hotspot: " + "; ".join(values))
    else:
        answers = question.get("Answers") or []
        if isinstance(answers, list) and answers:
            builder.add_paragraph("Options", bold=True, color="17324D", keep_next=True)
            answer_images = question.get("AnswerImageURLs") or []
            for index, item in enumerate(answers):
                label = chr(65 + index) if index < 26 else str(index + 1)
                add_option(builder, label, item)
                if isinstance(answer_images, list) and index < len(answer_images):
                    add_referenced_images(
                        builder,
                        images,
                        [answer_images[index]],
                        f"Question {number}, option {label}",
                    )
        rightanswer = question.get("RightAnswer")
        if rightanswer not in (None, ""):
            add_correct_answer(builder, "Correct answer: " + answer_text(rightanswer))
        elif qtype == "FillInTheBlankQuestion" and answers:
            add_correct_answer(
                builder,
                "Accepted answer(s): " + "; ".join(answer_text(item) for item in answers),
            )

    notes = normalise_text(question.get("QuestionNotes"))
    if notes:
        builder.add_paragraph(
            "Notes: " + notes,
            italic=True,
            color="8A3F0A",
            fill="FFF6E8",
            border_color="F4A261",
            left_indent=180,
            right_indent=120,
            space_before=80,
            space_after=100,
            keep_lines=True,
        )

    add_feedback(builder, question)

    builder.add_paragraph("", space_after=160)


def find_quizdata_member(package: zipfile.ZipFile) -> str | None:
    candidates = [
        member
        for member in package.namelist()
        if PurePosixPath(member.replace("\\", "/")).name.lower() == "quizdata.json"
    ]
    if not candidates:
        return None
    return sorted(candidates, key=lambda item: (item.count("/"), len(item)))[0]


def book_chapters(question_count: int, chapter_size: int = 10) -> list[tuple[int, int, int]]:
    """Return chapter number and inclusive question ranges."""
    chapters = []
    for start in range(1, question_count + 1, chapter_size):
        end = min(start + chapter_size - 1, question_count)
        chapters.append((len(chapters) + 1, start, end))
    return chapters


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
        annotate_related_answer_targets(questions)

        title = str(placement.get("scormname") or "SCORM Exam")
        builder = DocxBuilder(title)
        builder.add_paragraph("", space_after=420)
        builder.add_paragraph(
            "ACUPUNCTURETESTS",
            style="BookBrand",
            alignment="center",
            space_after=260,
        )
        builder.add_paragraph(
            title,
            style="Title",
            alignment="center",
            space_before=180,
            space_after=220,
            keep_lines=True,
        )
        builder.add_paragraph(
            "Exam Review Book · Instructor Edition",
            style="BookSubtitle",
            alignment="center",
            color="527286",
            size=26,
            space_after=420,
        )
        builder.add_paragraph(
            f"Course: {placement.get('coursename', '')}\n"
            f"Section: {placement.get('sectionname') or 'Unlabelled section'}\n"
            f"Questions: {len(questions)}\n"
            f"Source CMID: {placement.get('cmid', '')}",
            color="31586B",
            fill="E8F3F5",
            border_color="F47E2C",
            left_indent=720,
            right_indent=720,
            space_before=120,
            space_after=220,
            keep_lines=True,
        )
        builder.add_paragraph(
            "Answer key and source feedback are included for instructor review.",
            italic=True,
            color="8A3F0A",
            alignment="center",
            space_after=240,
        )

        chapters = book_chapters(len(questions))
        builder.add_page_break()
        builder.add_paragraph("Contents", style="Heading1", space_after=180)
        builder.add_paragraph(
            "This study book is arranged in groups of up to ten questions for easier reading and review.",
            color="527286",
            space_after=180,
        )
        for chapter, start, end in chapters:
            builder.add_paragraph(
                f"Chapter {chapter}  ·  Questions {start}–{end}",
                bold=True,
                color="17324D",
                fill="F7F9FB",
                border_color="F47E2C",
                left_indent=180,
                right_indent=120,
                space_after=80,
                keep_lines=True,
            )

        images = PackageImages(package)
        for number, question in enumerate(questions, start=1):
            if (number - 1) % 10 == 0:
                builder.add_page_break()
                chapter = ((number - 1) // 10) + 1
                end = min(number + 9, len(questions))
                builder.add_paragraph(
                    f"Chapter {chapter} · Questions {number}–{end}",
                    style="Heading1",
                    space_after=220,
                )
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
