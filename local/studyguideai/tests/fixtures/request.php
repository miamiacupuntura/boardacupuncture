<?php
// CLI-only test harness: uses the isolated PHPUnit database, never the development/production prefix.
if (PHP_SAPI !== 'cli' || isset($_SERVER['REMOTE_ADDR'])) { exit(1); }
require __DIR__ . '/../../../../vendor/autoload.php';
define('PHPUNIT_UTIL', true);
require __DIR__ . '/../../../../lib/phpunit/bootstrap.php';
$data = json_decode(stream_get_contents(STDIN), true, flags: JSON_THROW_ON_ERROR);
$USER = $DB->get_record('user', ['id' => $data['userid']], '*', MUST_EXIST);
\core\session\manager::set_user($USER);
$PAGE = new moodle_page();
if ($data['mode'] === 'api') {
    $_POST = $data['params'];
    $_POST['sesskey'] = empty($data['badsesskey']) ? sesskey() : 'invalid-csrf-token';
    $_SERVER['REQUEST_METHOD'] = $data['method'] ?? 'POST';
    if (($_POST['action'] ?? '') === 'generate') {
        // No endpoint test is ever allowed to make a paid request.
        set_config('enabled', 1, 'aiprovider_openai');
        set_config('apikey', 'fake-key-no-network', 'aiprovider_openai');
        set_config('action_generate_image_model', 'gpt-image-1-mini', 'aiprovider_openai');
        core_plugin_manager::reset_caches();
        $mock = new \GuzzleHttp\Handler\MockHandler([
            new \GuzzleHttp\Psr7\Response(200, ['Content-Type' => 'application/json'], json_encode([
                'data' => [['b64_json' => base64_encode(file_get_contents($CFG->dirroot . '/ai/tests/fixtures/white.png'))]],
            ])),
        ]);
        \core\di::set(\core\http_client::class, new \core\http_client([
            'handler' => \GuzzleHttp\HandlerStack::create($mock),
        ]));
    }
    require __DIR__ . '/../../api.php';
} else {
    require_once __DIR__ . '/../../lib.php';
    $context = context_module::instance($data['cmid']);
    $result = local_studyguideai_pluginfile(null, null, $context, $data['filearea'],
        [(string)$data['imageid'], $data['filename']], false);
    echo json_encode(['denied' => $result === false]);
}
