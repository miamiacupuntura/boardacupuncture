<?php
defined('MOODLE_INTERNAL') || die();
$observers = [[
    'eventname' => '\core\event\course_module_deleted',
    'callback' => '\local_studyguideai\observer::module_deleted',
]];
