<?php
defined('MOODLE_INTERNAL') || die();

/** Serve approved illustrations or an instructor's own pending preview. */
function local_studyguideai_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = []) {
    if ($context->contextlevel !== CONTEXT_MODULE || !in_array($filearea, ['pending', 'illustration'], true) ||
            count($args) !== 2 || !ctype_digit((string)$args[0])) {
        return false;
    }
    try {
        $file = \local_studyguideai\service::file((int)$context->instanceid, (int)$args[0], $filearea, $args[1]);
    } catch (\moodle_exception $e) {
        return false;
    }
    // Recheck access on each request. Old URLs must not bypass withdrawal or enrolment changes.
    $options['cacheability'] = 'private';
    $options['immutable'] = false;
    \core\session\manager::write_close();
    send_stored_file($file, 0, 0, false, $options);
}
