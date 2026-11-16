<?php
// Administrator-only primary navigation entry.

namespace block_myprogress\hook;

defined('MOODLE_INTERNAL') || die();

use core\hook\navigation\primary_extend;
use moodle_url;
use navigation_node;
use pix_icon;

final class navigation {
    /**
     * Adds the student evaluation report after Moodle's standard primary items.
     *
     * @param primary_extend $hook
     */
    public static function extend_primary_navigation(primary_extend $hook): void {
        if (!isloggedin() || isguestuser()) {
            return;
        }
        $context = \context_system::instance();
        if (!has_capability('moodle/site:config', $context)) {
            return;
        }
        $hook->get_primaryview()->add(
            'Evaluación de estudiantes',
            new moodle_url('/blocks/myprogress/admin-student-evaluations.php'),
            navigation_node::TYPE_CUSTOM,
            null,
            'block_myprogress_student_evaluations',
            new pix_icon('i/report', '')
        );
    }
}

