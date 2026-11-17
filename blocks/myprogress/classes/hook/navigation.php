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
     * Adds administrator reports after Moodle's standard primary items.
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
            'Student Evaluations',
            new moodle_url('/blocks/myprogress/admin-student-evaluations.php'),
            navigation_node::TYPE_CUSTOM,
            null,
            'block_myprogress_student_evaluations',
            new pix_icon('i/report', '')
        );
        $hook->get_primaryview()->add(
            'NCBAHM Question Bank',
            new moodle_url('/blocks/myprogress/admin-herbology-question-bank.php'),
            navigation_node::TYPE_CUSTOM,
            null,
            'block_myprogress_ncbahm_question_bank',
            new pix_icon('i/questions', '')
        );
    }
}

