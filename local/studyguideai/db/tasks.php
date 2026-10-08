<?php
defined('MOODLE_INTERNAL') || die();
$tasks = [[
    'classname' => '\local_studyguideai\task\expire_drafts', 'blocking' => 0,
    'minute' => 'R', 'hour' => '2', 'day' => '*', 'month' => '*', 'dayofweek' => '*',
]];
