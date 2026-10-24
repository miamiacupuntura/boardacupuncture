<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

/**
 * Render a generated SCORM Word document inside Moodle.
 *
 * @package    mod_scorm
 * @copyright  2026 Miami Acupuncture
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/../../../config.php');

const SCORM_WORD_MAX_DOCX_BYTES = 32 * 1024 * 1024;
const SCORM_WORD_MAX_XML_BYTES = 16 * 1024 * 1024;
const SCORM_WORD_MAX_IMAGE_BYTES = 12 * 1024 * 1024;

/**
 * Build a Moodle exception with a local diagnostic message.
 *
 * @param string $message Message displayed by Moodle.
 * @return moodle_exception
 */
function scorm_word_exception(string $message): moodle_exception {
    return new moodle_exception('generalexceptionmessage', 'error', '', $message);
}

/**
 * Read text and line breaks from a Word run.
 *
 * @param DOMXPath $xpath XML query helper.
 * @param DOMElement $run Word run.
 * @return string Escaped HTML.
 */
function scorm_word_render_run(DOMXPath $xpath, DOMElement $run): string {
    $parts = [];
    foreach ($xpath->query('.//w:t | .//w:br', $run) as $node) {
        if ($node->localName === 'br') {
            $parts[] = '<br>';
        } else {
            $parts[] = s($node->textContent);
        }
    }
    $content = implode('', $parts);
    if ($content === '') {
        return '';
    }

    if ($xpath->query('./w:rPr/w:b', $run)->length) {
        $content = html_writer::tag('strong', $content);
    }
    if ($xpath->query('./w:rPr/w:i', $run)->length) {
        $content = html_writer::tag('em', $content);
    }
    return $content;
}

/**
 * Return relationships between Word document image IDs and ZIP members.
 *
 * @param ZipArchive $zip Open DOCX archive.
 * @return array<string, string>
 */
function scorm_word_image_relationships(ZipArchive $zip): array {
    $xml = $zip->getFromName('word/_rels/document.xml.rels');
    if ($xml === false || strlen($xml) > SCORM_WORD_MAX_XML_BYTES) {
        return [];
    }

    $dom = new DOMDocument();
    if (!@$dom->loadXML($xml, LIBXML_NONET | LIBXML_NOBLANKS)) {
        return [];
    }

    $relationships = [];
    foreach ($dom->getElementsByTagName('Relationship') as $relationship) {
        $id = $relationship->getAttribute('Id');
        $target = $relationship->getAttribute('Target');
        if ($id === '' || $target === '' || str_contains($target, '..')) {
            continue;
        }
        $relationships[$id] = 'word/' . ltrim($target, '/');
    }
    return $relationships;
}

/**
 * Render an image referenced by a Word paragraph.
 *
 * @param DOMXPath $xpath XML query helper.
 * @param DOMElement $paragraph Word paragraph.
 * @param ZipArchive $zip Open DOCX archive.
 * @param array<string, string> $relationships Image relationship map.
 * @return string Image HTML.
 */
function scorm_word_render_images(
    DOMXPath $xpath,
    DOMElement $paragraph,
    ZipArchive $zip,
    array $relationships
): string {
    $images = [];
    foreach ($xpath->query('.//a:blip', $paragraph) as $blip) {
        $relationshipid = $blip->getAttributeNS(
            'http://schemas.openxmlformats.org/officeDocument/2006/relationships',
            'embed'
        );
        $member = $relationships[$relationshipid] ?? '';
        if ($member === '') {
            continue;
        }

        $stat = $zip->statName($member);
        if (!$stat || $stat['size'] > SCORM_WORD_MAX_IMAGE_BYTES) {
            continue;
        }
        $data = $zip->getFromName($member);
        if ($data === false) {
            continue;
        }

        $extension = strtolower(pathinfo($member, PATHINFO_EXTENSION));
        $mimetypes = [
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
        ];
        if (!isset($mimetypes[$extension])) {
            continue;
        }
        $images[] = html_writer::empty_tag('img', [
            'src' => 'data:' . $mimetypes[$extension] . ';base64,' . base64_encode($data),
            'alt' => 'Question image',
            'class' => 'img-fluid d-block my-3',
            'loading' => 'lazy',
        ]);
    }
    return implode('', $images);
}

/**
 * Convert the simple OOXML produced by scorm_word_export.py to safe HTML.
 *
 * @param string $pathname Temporary DOCX pathname.
 * @return string
 */
function scorm_word_document_html(string $pathname): string {
    if (!class_exists('ZipArchive')) {
        throw scorm_word_exception('The PHP Zip extension is required to preview Word documents.');
    }

    $zip = new ZipArchive();
    if ($zip->open($pathname) !== true) {
        throw scorm_word_exception('The Word document could not be opened.');
    }

    try {
        $stat = $zip->statName('word/document.xml');
        if (!$stat || $stat['size'] > SCORM_WORD_MAX_XML_BYTES) {
            throw scorm_word_exception('The Word document content is missing or too large.');
        }
        $xml = $zip->getFromName('word/document.xml');
        if ($xml === false) {
            throw scorm_word_exception('The Word document content is missing.');
        }

        $dom = new DOMDocument();
        if (!@$dom->loadXML($xml, LIBXML_NONET | LIBXML_NOBLANKS)) {
            throw scorm_word_exception('The Word document contains invalid XML.');
        }
        $xpath = new DOMXPath($dom);
        $xpath->registerNamespace(
            'w',
            'http://schemas.openxmlformats.org/wordprocessingml/2006/main'
        );
        $xpath->registerNamespace(
            'a',
            'http://schemas.openxmlformats.org/drawingml/2006/main'
        );
        $relationships = scorm_word_image_relationships($zip);
        $output = [];

        foreach ($xpath->query('/w:document/w:body/w:p') as $paragraph) {
            $text = '';
            foreach ($xpath->query('./w:r', $paragraph) as $run) {
                $text .= scorm_word_render_run($xpath, $run);
            }
            $images = scorm_word_render_images($xpath, $paragraph, $zip, $relationships);
            if ($text === '' && $images === '') {
                continue;
            }

            $style = '';
            $stylenodes = $xpath->query('./w:pPr/w:pStyle/@w:val', $paragraph);
            if ($stylenodes->length) {
                $style = $stylenodes->item(0)->nodeValue;
            }
            if ($style === 'Title') {
                $output[] = html_writer::tag('h2', $text, ['class' => 'mt-2 mb-3']) . $images;
            } else if ($style === 'Heading1') {
                $output[] = html_writer::tag('h3', $text, ['class' => 'mt-4']) . $images;
            } else if ($style === 'Heading2') {
                $output[] = html_writer::tag('h4', $text, ['class' => 'mt-4 text-primary']) . $images;
            } else {
                $output[] = html_writer::tag('p', $text, ['class' => 'mb-2']) . $images;
            }
        }
        return implode("\n", $output);
    } finally {
        $zip->close();
    }
}

$cmid = required_param('cmid', PARAM_INT);
$fileid = required_param('fileid', PARAM_INT);
$bookcmid = optional_param('bookcmid', 0, PARAM_INT);
$chapterid = optional_param('chapterid', 0, PARAM_INT);

$cm = get_coursemodule_from_id('folder', $cmid, 0, false, MUST_EXIST);
$course = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);
$folder = $DB->get_record('folder', ['id' => $cm->instance], '*', MUST_EXIST);
require_course_login($course, true, $cm);
$context = context_module::instance($cm->id);
require_capability('mod/folder:view', $context);

if (!str_contains((string)$folder->intro, 'data-scorm-word-export')) {
    throw scorm_word_exception('This folder is not a generated SCORM Word library.');
}

$file = get_file_storage()->get_file_by_id($fileid);
if (!$file || $file->is_directory() || (int)$file->get_contextid() !== (int)$context->id ||
        $file->get_component() !== 'mod_folder' || $file->get_filearea() !== 'content' ||
        strtolower(pathinfo($file->get_filename(), PATHINFO_EXTENSION)) !== 'docx') {
    throw scorm_word_exception('The requested Word document is not available in this library.');
}
if ($file->get_filesize() > SCORM_WORD_MAX_DOCX_BYTES) {
    throw scorm_word_exception('The requested Word document is too large to preview.');
}

$returnurl = new moodle_url('/mod/folder/view.php', ['id' => $cm->id]);
if ($bookcmid && $chapterid) {
    $bookcm = get_coursemodule_from_id('book', $bookcmid, $course->id, false, IGNORE_MISSING);
    if ($bookcm) {
        $returnurl = new moodle_url('/mod/book/view.php', [
            'id' => $bookcmid,
            'chapterid' => $chapterid,
        ]);
    }
}

$downloadurl = moodle_url::make_pluginfile_url(
    $context->id,
    'mod_folder',
    'content',
    0,
    $file->get_filepath(),
    $file->get_filename(),
    true
);
$PAGE->set_url('/mod/scorm/studytopic/scorm_word_view.php', [
    'cmid' => $cmid,
    'fileid' => $fileid,
    'bookcmid' => $bookcmid,
    'chapterid' => $chapterid,
]);
$PAGE->set_context($context);
$PAGE->set_cm($cm, $course);
$PAGE->set_title($file->get_filename());
$PAGE->set_heading($course->fullname);
$PAGE->add_body_class('limitedwidth');
$PAGE->add_body_class('scorm-word-library');
$PAGE->requires->css('/mod/scorm/studytopic/scorm_word_library.css');

$requestdirectory = make_request_directory();
$temppath = $requestdirectory . DIRECTORY_SEPARATOR . 'preview.docx';
if (!$file->copy_content_to($temppath)) {
    throw scorm_word_exception('The Word document could not be prepared for preview.');
}
$content = scorm_word_document_html($temppath);

echo $OUTPUT->header();
echo $OUTPUT->heading(s(pathinfo($file->get_filename(), PATHINFO_FILENAME)), 2);
echo html_writer::start_div('d-flex flex-wrap gap-2 mb-4');
echo html_writer::link($returnurl, get_string('back'), ['class' => 'btn btn-secondary']);
echo html_writer::link($downloadurl, 'Download Word', ['class' => 'btn btn-primary']);
echo html_writer::end_div();
echo html_writer::div($content, 'card card-body scorm-word-preview');
echo $OUTPUT->footer();
