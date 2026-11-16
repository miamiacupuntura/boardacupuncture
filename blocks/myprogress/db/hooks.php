<?php
// Hook registrations for the My Progress block.

defined('MOODLE_INTERNAL') || die();

$callbacks = [
    [
        'hook' => \core\hook\navigation\primary_extend::class,
        'callback' => \block_myprogress\hook\navigation::class . '::extend_primary_navigation',
    ],
];

