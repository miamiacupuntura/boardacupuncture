<?php
/**
 * Output hook registration.
 *
 * @package    local_jquery
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

$callbacks = [
    [
        'hook' => \core\hook\output\before_standard_head_html_generation::class,
        'callback' => [\local_jquery\hook_callbacks::class, 'before_standard_head_html_generation'],
    ],
];
