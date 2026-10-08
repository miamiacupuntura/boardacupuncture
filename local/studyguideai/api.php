<?php
if (!defined('AJAX_SCRIPT')) { define('AJAX_SCRIPT', true); }
require_once(__DIR__ . '/../../config.php');

use local_studyguideai\service;

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, private');
try {
    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
        throw new invalid_parameter_exception('POST required');
    }
    require_sesskey();
    $cmid = required_param('cmid', PARAM_INT);
    $action = required_param('action', PARAM_ALPHA);
    $topic = optional_param('topic', service::TOPIC, PARAM_ALPHANUMEXT);
    $objective = optional_param('objective', service::OBJECTIVE, PARAM_ALPHANUMEXT);
    service::scope($topic, $objective);
    service::access($cmid);
    switch ($action) {
        case 'generate':
            if (!required_param('confirmed', PARAM_BOOL)) {
                service::fail('forbidden');
            }
            service::generate($cmid, $topic, $objective);
            break;
        case 'approve':
        case 'replace':
        case 'reject':
        case 'withdraw':
            if (in_array($action, ['approve', 'replace'], true) && !required_param('reviewed', PARAM_BOOL)) {
                service::fail('forbidden');
            }
            $replaceid = $action === 'replace' ? required_param('replaceid', PARAM_INT) : 0;
            if ($action === 'replace' && $replaceid <= 0) {
                service::fail('invalidscope');
            }
            service::decide($cmid, required_param('imageid', PARAM_INT), $action === 'replace' ? 'approve' : $action,
                required_param('revision', PARAM_INT), optional_param('title', '', PARAM_TEXT),
                optional_param('alttext', '', PARAM_TEXT), $replaceid);
            break;
        case 'list':
            break;
        case 'audit':
            echo json_encode(['success' => true, 'audit' => service::history($cmid)], JSON_THROW_ON_ERROR);
            exit;
        default:
            service::fail('invalidscope');
    }
    echo json_encode(['success' => true, 'images' => array_values(array_map([service::class, 'serialize'],
        service::visible($cmid, $topic, $objective)))], JSON_THROW_ON_ERROR);
} catch (Throwable $e) {
    // Do not expose provider request headers, file paths, prompts or stack traces.
    http_response_code($e instanceof required_capability_exception ? 403 : 400);
    $message = $e instanceof moodle_exception && $e->module === 'local_studyguideai' ?
        $e->getMessage() : get_string('error', 'local_studyguideai');
    echo json_encode(['success' => false, 'error' => $message]);
}
