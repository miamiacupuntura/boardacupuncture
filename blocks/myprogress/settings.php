<?php
// Permanent Site administration entry for the student evaluation report.

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $ADMIN->add('reports', new admin_externalpage(
        'block_myprogress_student_evaluations',
        'Student Evaluations',
        new moodle_url('/blocks/myprogress/admin-student-evaluations.php'),
        'moodle/site:config'
    ));
    $ADMIN->add('reports', new admin_externalpage(
        'block_myprogress_ncbahm_evaluations',
        'Evaluaciones NCBAHM',
        new moodle_url('/blocks/myprogress/admin-ncbahm-evaluations.php'),
        'moodle/site:config'
    ));
}

