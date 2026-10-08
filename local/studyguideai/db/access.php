<?php
/** @package local_studyguideai @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later */
defined('MOODLE_INTERNAL') || die();
$capabilities = [];
foreach (['generate', 'approve', 'manage'] as $action) {
    $capabilities['local/studyguideai:' . $action] = [
        'riskbitmask' => RISK_XSS,
        'captype' => 'write',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes' => ['editingteacher' => CAP_ALLOW, 'manager' => CAP_ALLOW],
    ];
}
