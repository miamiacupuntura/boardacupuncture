<?php
namespace local_studyguideai;
defined('MOODLE_INTERNAL') || die();

/** @covers \local_studyguideai\service @covers \local_studyguideai\ui */
final class service_test extends \advanced_testcase {
    private \stdClass $course;
    private \stdClass $scorm;
    private \stdClass $teacher;
    private \stdClass $student;

    protected function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
        $this->preventResetByRollback();
        $this->setAdminUser();
        $generator = $this->getDataGenerator();
        $this->course = $generator->create_course();
        $this->scorm = $generator->create_module('scorm', ['course' => $this->course->id]);
        $this->teacher = $generator->create_user();
        $this->student = $generator->create_user();
        $generator->enrol_user($this->teacher->id, $this->course->id, 'editingteacher');
        $generator->enrol_user($this->student->id, $this->course->id, 'student');
        $this->setUser($this->teacher);
        $GLOBALS['PAGE'] = new \moodle_page();
    }

    private function pending(): \stdClass {
        global $USER, $CFG, $PAGE;
        $PAGE = new \moodle_page();
        require_once($CFG->libdir . '/filelib.php');
        $file = get_file_storage()->create_file_from_pathname([
            'contextid' => \context_user::instance($USER->id)->id, 'component' => 'user', 'filearea' => 'draft',
            'itemid' => file_get_unused_draft_itemid(), 'filepath' => '/', 'filename' => 'preview.png',
        ], $CFG->dirroot . '/ai/tests/fixtures/white.png');
        return service::register_draft($this->scorm->cmid, service::TOPIC, service::OBJECTIVE, $file);
    }

    private function approve(\stdClass $image, int $replaceid = 0): \stdClass {
        global $PAGE;
        $PAGE = new \moodle_page();
        return service::decide($this->scorm->cmid, $image->id, 'approve', $image->revision,
            'Reviewed Hand Yin', 'Chest to Hands, showing Lung, Pericardium and Heart.', $replaceid);
    }

    private function denied(callable $callback, string $error = ''): void {
        global $PAGE;
        $PAGE = new \moodle_page();
        try {
            $callback();
            $this->fail('Unauthorized or stale operation unexpectedly succeeded');
        } catch (\moodle_exception $e) {
            if ($error !== '') { $this->assertEquals($error, $e->errorcode); }
            else { $this->assertInstanceOf(\moodle_exception::class, $e); }
        }
    }

    public function test_pending_invisible_and_permanent_after_new_session(): void {
        global $DB;
        $pending = $this->pending();
        $this->assertCount(0, get_file_storage()->get_area_files(\context_module::instance($this->scorm->cmid)->id,
            'local_studyguideai', 'illustration', $pending->id, 'id', false));
        $this->setUser($this->student);
        $this->assertEmpty(service::visible($this->scorm->cmid, service::TOPIC, service::OBJECTIVE));
        $this->denied(fn() => service::file($this->scorm->cmid, $pending->id, 'pending', 'preview'));
        $this->denied(fn() => $this->approve($pending));
        $this->denied(fn() => service::history($this->scorm->cmid));
        $this->setUser($this->teacher);
        $preview = service::file($this->scorm->cmid, $pending->id, 'pending', 'preview');
        $hash = $preview->get_contenthash();
        $approved = $this->approve($pending);
        $this->assertEquals('approved', $approved->status);
        $this->assertEquals(0, $approved->draftitemid);
        $this->assertFalse(get_file_storage()->get_file(\context_user::instance($this->teacher->id)->id,
            'user', 'draft', $pending->draftitemid, '/', $pending->draftfilename));
        // End the generating identity and start a fresh student identity/session object.
        $this->setUser(null);
        $this->setUser($DB->get_record('user', ['id' => $this->student->id]));
        $file = service::file($this->scorm->cmid, $approved->id, 'illustration', 'illustration.png');
        $this->assertEquals($hash, $file->get_contenthash());
        $this->assertEquals('image/png', getimagesizefromstring($file->get_content())['mime']);
        $this->assertCount(1, service::visible($this->scorm->cmid, service::TOPIC, service::OBJECTIVE));
        $html = ui::render($this->scorm->cmid);
        $this->assertStringContainsString('Reviewed Hand Yin', $html);
        $this->assertStringContainsString('Chest to Hands', $html);
        $this->assertStringNotContainsString('/user/draft/', $html);
        $this->assertEquals(2, $DB->count_records('local_studyguideai_audit', ['imageid' => $approved->id]));
    }

    public function test_reject_withdraw_replace_and_audit(): void {
        global $DB;
        $rejected = $this->pending();
        service::decide($this->scorm->cmid, $rejected->id, 'reject', $rejected->revision);
        $this->denied(fn() => $this->approve($rejected), 'conflict');
        $first = $this->approve($this->pending());
        $second = $this->pending();
        $this->denied(fn() => $this->approve($second), 'conflict');
        $second = $this->approve($second, $first->id);
        $this->assertEquals('replaced', $DB->get_field('local_studyguideai_image', 'status', ['id' => $first->id]));
        $this->setUser($this->student);
        $this->denied(fn() => service::file($this->scorm->cmid, $first->id, 'illustration', 'illustration.png'), 'forbidden');
        $visible = service::visible($this->scorm->cmid, service::TOPIC, service::OBJECTIVE);
        $this->assertEquals($second->id, reset($visible)->id);
        $this->setUser($this->teacher);
        $withdrawn = service::decide($this->scorm->cmid, $second->id, 'withdraw', $second->revision);
        $this->assertEquals('withdrawn', $withdrawn->status);
        $this->assertNotEmpty(service::file($this->scorm->cmid, $second->id, 'illustration', 'illustration.png'));
        $actions = array_column(service::history($this->scorm->cmid), 'action');
        foreach (['generated', 'reject', 'approve', 'replaced', 'replacement', 'withdraw'] as $action) {
            $this->assertContains($action, $actions);
        }
        $this->setUser($this->student);
        $this->assertEmpty(service::visible($this->scorm->cmid, service::TOPIC, service::OBJECTIVE));
        $this->denied(fn() => service::file($this->scorm->cmid, $second->id, 'illustration', 'illustration.png'), 'forbidden');
        $this->denied(fn() => service::file($this->scorm->cmid, $rejected->id, 'illustration', 'illustration.png'), 'forbidden');
    }

    public function test_other_instructor_cannot_approve_private_draft(): void {
        $pending = $this->pending();
        $other = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($other->id, $this->course->id, 'editingteacher');
        $this->setUser($other);
        $this->assertEmpty(service::visible($this->scorm->cmid, service::TOPIC, service::OBJECTIVE));
        $this->denied(fn() => $this->approve($pending), 'forbidden');
        $this->denied(fn() => service::file($this->scorm->cmid, $pending->id, 'pending', 'preview'), 'forbidden');
    }

    public function test_scope_file_owner_and_guest_checks(): void {
        global $CFG;
        $this->denied(fn() => service::scope('another-topic', service::OBJECTIVE), 'invalidscope');
        $pending = $this->pending();
        $this->setAdminUser();
        $other = $this->getDataGenerator()->create_module('scorm', ['course' => $this->course->id]);
        $this->setUser($this->teacher);
        $this->denied(fn() => service::file($other->cmid, $pending->id, 'pending', 'preview'), 'forbidden');
        $this->denied(fn() => service::file($this->scorm->cmid, $pending->id, 'content', 'preview'), 'forbidden');
        $file = get_file_storage()->create_file_from_pathname([
            'contextid' => \context_user::instance($this->student->id)->id, 'component' => 'user', 'filearea' => 'draft',
            'itemid' => 98374, 'filepath' => '/', 'filename' => 'preview.png',
        ], $CFG->dirroot . '/ai/tests/fixtures/white.png');
        $this->denied(fn() => service::register_draft($this->scorm->cmid, service::TOPIC, service::OBJECTIVE, $file), 'invalidimage');
        $this->setGuestUser();
        $this->denied(fn() => service::visible($this->scorm->cmid, service::TOPIC, service::OBJECTIVE));
        $this->setUser($this->getDataGenerator()->create_user());
        $this->denied(fn() => service::visible($this->scorm->cmid, service::TOPIC, service::OBJECTIVE));
    }

    public function test_stale_revision_concurrency_and_transaction_rollback(): void {
        global $DB;
        $pending = $this->pending();
        $key = service::key($this->scorm->cmid, service::TOPIC, service::OBJECTIVE);
        // Use a genuinely independent DB connection: PostgreSQL advisory locks are session scoped.
        $originaldb = $DB;
        $cfg = $DB->export_dbconfig();
        $second = \moodle_database::get_driver_instance($cfg->dbtype, $cfg->dblibrary);
        $second->connect($cfg->dbhost, $cfg->dbuser, $cfg->dbpass, $cfg->dbname, $cfg->prefix, $cfg->dboptions);
        $DB = $second;
        $lock = \core\lock\lock_config::get_lock_factory('local_studyguideai')->get_lock($key, 0);
        $DB = $originaldb;
        $this->assertNotFalse($lock);
        try {
            $this->denied(fn() => $this->approve($pending), 'conflict');
        } finally {
            $lock->release();
            $second->dispose();
        }
        $this->denied(fn() => service::decide($this->scorm->cmid, $pending->id, 'approve', 99, 'Title', 'Alt'), 'conflict');
        $this->denied(fn() => service::decide($this->scorm->cmid, $pending->id, 'approve', 1, '', ''), 'invalidmetadata');
        $this->assertEquals('pending', $DB->get_field('local_studyguideai_image', 'status', ['id' => $pending->id]));
        $this->assertEquals(1, $DB->count_records('local_studyguideai_audit', ['imageid' => $pending->id]));
        $first = $this->approve($pending);
        $this->denied(fn() => $this->approve($pending), 'conflict');
        $second = $this->pending();
        $this->denied(fn() => $this->approve($second, 123456), 'conflict');
        $this->assertEquals('approved', $DB->get_field('local_studyguideai_image', 'status', ['id' => $first->id]));
        $this->assertEquals(1, $DB->count_records('local_studyguideai_image', ['activekey' => $key]));
    }

    public function test_expiry_never_removes_approved_files(): void {
        global $DB;
        $approved = $this->approve($this->pending());
        $pending = $this->pending();
        $DB->set_field('local_studyguideai_image', 'expires', time() - 1, ['id' => $pending->id]);
        $this->denied(fn() => $this->approve($pending), 'expired');
        service::expire();
        $this->assertEquals('expired', $DB->get_field('local_studyguideai_image', 'status', ['id' => $pending->id]));
        $this->setUser($this->student);
        $this->assertInstanceOf(\stored_file::class, service::file($this->scorm->cmid, $approved->id, 'illustration', 'illustration.png'));
    }

    public function test_hidden_and_closed_activity_access(): void {
        global $DB;
        $approved = $this->approve($this->pending());
        $this->setAdminUser();
        set_coursemodule_visible($this->scorm->cmid, 0);
        $this->setUser($this->student);
        $this->denied(fn() => service::file($this->scorm->cmid, $approved->id, 'illustration', 'illustration.png'));
        $this->setAdminUser();
        set_coursemodule_visible($this->scorm->cmid, 1);
        $DB->set_field('scorm', 'timeclose', time() - 1, ['id' => $this->scorm->id]);
        $this->setUser($this->student);
        $this->denied(fn() => service::file($this->scorm->cmid, $approved->id, 'illustration', 'illustration.png'), 'forbidden');
    }

    public function test_generate_uses_mocked_provider_and_creates_only_pending(): void {
        global $CFG, $DB;
        set_config('enabled', 1, 'aiprovider_openai');
        set_config('apikey', 'fake-key-no-network', 'aiprovider_openai');
        set_config('action_generate_image_model', 'gpt-image-1-mini', 'aiprovider_openai');
        \core_plugin_manager::reset_caches();
        ['mock' => $mock] = $this->get_mocked_http_client();
        $mock->append(new \GuzzleHttp\Psr7\Response(200, ['Content-Type' => 'application/json'], json_encode([
            'data' => [['b64_json' => base64_encode(file_get_contents($CFG->dirroot . '/ai/tests/fixtures/white.png'))]],
        ])));
        $image = service::generate($this->scorm->cmid, service::TOPIC, service::OBJECTIVE);
        $this->assertEquals('pending', $image->status);
        $this->assertCount(0, get_file_storage()->get_area_files(\context_module::instance($this->scorm->cmid)->id,
            'local_studyguideai', 'illustration', $image->id, 'id', false));
        $this->assertEquals(0, $mock->count());
        $this->assertTrue($DB->record_exists('ai_action_register', ['userid' => $this->teacher->id, 'success' => 1]));
    }
    public function test_database_rollback_keeps_previous_publication(): void {
        global $DB;
        $first = $this->approve($this->pending());
        $pending = $this->pending();
        $before = $DB->count_records('local_studyguideai_audit');
        $transaction = $DB->start_delegated_transaction();
        $this->approve($pending, $first->id);
        try { $transaction->rollback(new \moodle_exception('error', 'local_studyguideai')); }
        catch (\moodle_exception $e) { /* Deliberately injected failure after all publication writes. */ }
        $this->assertEquals('approved', $DB->get_field('local_studyguideai_image', 'status', ['id' => $first->id]));
        $this->assertEquals('pending', $DB->get_field('local_studyguideai_image', 'status', ['id' => $pending->id]));
        $this->assertEquals($before, $DB->count_records('local_studyguideai_audit'));
        $this->assertEmpty(get_file_storage()->get_area_files(\context_module::instance($this->scorm->cmid)->id,
            'local_studyguideai', 'illustration', $pending->id, 'id', false));
        $this->assertInstanceOf(\stored_file::class, service::file($this->scorm->cmid, $pending->id, 'pending', 'preview'));
    }

    public function test_backup_restore_keeps_approved_images_and_audit_only(): void {
        global $DB, $CFG, $PAGE;
        $approved = $this->approve($this->pending());
        $pending = $this->pending();
        $this->setAdminUser();
        $PAGE = new \moodle_page();
        require_once($CFG->dirroot . '/course/lib.php');
        $cm = get_fast_modinfo($this->course)->get_cm($this->scorm->cmid);
        $copy = duplicate_module($this->course, $cm);
        $records = $DB->get_records('local_studyguideai_image', ['cmid' => $copy->id]);
        $this->assertCount(1, $records);
        $image = reset($records);
        $this->assertEquals('approved', $image->status);
        $this->assertEquals($copy->instance, $image->scormid);
        $this->assertEquals(service::key($copy->id, service::TOPIC, service::OBJECTIVE), $image->activekey);
        $PAGE = new \moodle_page();
        $file = service::file($copy->id, $image->id, 'illustration', 'illustration.png');
        $this->assertEquals($approved->contenthash, $file->get_contenthash());
        $this->assertEquals(2, $DB->count_records('local_studyguideai_audit', ['cmid' => $copy->id]));
        $this->assertTrue($DB->record_exists('local_studyguideai_image', ['id' => $pending->id, 'status' => 'pending']));
    }

    public function test_activity_deletion_cleans_only_plugin_data(): void {
        global $DB, $CFG, $PAGE;
        $approved = $this->approve($this->pending());
        $pending = $this->pending();
        $this->setAdminUser();
        $PAGE = new \moodle_page();
        require_once($CFG->dirroot . '/course/lib.php');
        course_delete_module($this->scorm->cmid);
        $this->assertEquals(0, $DB->count_records('local_studyguideai_image', ['cmid' => $this->scorm->cmid]));
        $this->assertEquals(0, $DB->count_records('local_studyguideai_audit', ['cmid' => $this->scorm->cmid]));
        $this->assertFalse(get_file_storage()->get_file(\context_user::instance($this->teacher->id)->id,
            'user', 'draft', $pending->draftitemid, '/', $pending->draftfilename));
    }

    public function test_privacy_erasure_removes_authored_files_and_anonymises_history(): void {
        global $DB;
        $approved = $this->approve($this->pending());
        $context = \context_module::instance($this->scorm->cmid);
        $list = new \core_privacy\local\request\approved_contextlist($this->teacher, 'local_studyguideai', [$context->id]);
        \local_studyguideai\privacy\provider::delete_data_for_user($list);
        $image = $DB->get_record('local_studyguideai_image', ['id' => $approved->id]);
        $this->assertEquals(0, $image->generatedby);
        $this->assertEquals(0, $image->reviewedby);
        $this->assertEquals('withdrawn', $image->status);
        $this->assertEmpty($image->activekey);
        $this->assertEquals(0, $DB->count_records('local_studyguideai_audit', ['actorid' => $this->teacher->id]));
        $this->assertEquals(2, $DB->count_records('local_studyguideai_audit', ['imageid' => $approved->id]));
        $this->assertEmpty(get_file_storage()->get_area_files($context->id, 'local_studyguideai', 'illustration', $approved->id));
        $this->setUser($this->student);
        $this->denied(fn() => service::file($this->scorm->cmid, $approved->id, 'illustration', 'illustration.png'), 'forbidden');
    }

    private function child_request(array $request): string {
        $process = proc_open([PHP_BINARY, __DIR__ . '/fixtures/request.php'],
            [['pipe', 'r'], ['pipe', 'w'], ['pipe', 'w']], $pipes);
        $this->assertIsResource($process);
        fwrite($pipes[0], json_encode($request, JSON_THROW_ON_ERROR));
        fclose($pipes[0]);
        $stdout = stream_get_contents($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[1]); fclose($pipes[2]);
        $this->assertEquals(0, proc_close($process), $stderr . $stdout);
        if ($request['mode'] === 'api') { $this->assertJson($stdout, $stdout . $stderr); }
        return $stdout;
    }

    public function test_real_endpoints_csrf_review_confirmation_and_file_delivery(): void {
        $pending = $this->pending();
        $params = ['action' => 'approve', 'cmid' => $this->scorm->cmid, 'imageid' => $pending->id,
            'revision' => 1, 'title' => 'Reviewed title', 'alttext' => 'Reviewed alternative text', 'reviewed' => 1];
        $request = ['mode' => 'api', 'userid' => $this->teacher->id, 'params' => $params];
        $this->assertFalse(json_decode($this->child_request($request + ['badsesskey' => true]), true)['success']);
        $this->assertFalse(json_decode($this->child_request($request + ['method' => 'GET']), true)['success']);
        $unreviewed = $request;
        $unreviewed['params']['reviewed'] = 0;
        $this->assertFalse(json_decode($this->child_request($unreviewed), true)['success']);
        $student = $request;
        $student['userid'] = $this->student->id;
        $this->assertFalse(json_decode($this->child_request($student), true)['success']);
        $file = ['mode' => 'file', 'userid' => $this->student->id, 'cmid' => $this->scorm->cmid,
            'filearea' => 'pending', 'imageid' => $pending->id, 'filename' => 'preview'];
        $this->assertTrue(json_decode($this->child_request($file), true)['denied']);
        $response = json_decode($this->child_request($request), true, flags: JSON_THROW_ON_ERROR);
        $this->assertTrue($response['success']);
        // A new process/session serves the permanent file to a different enrolled user.
        $file['filearea'] = 'illustration'; $file['filename'] = 'illustration.png';
        $pixels = $this->child_request($file);
        $this->assertEquals('image/png', getimagesizefromstring($pixels)['mime']);
        $this->assertEquals(200, getimagesizefromstring($pixels)[0]);
        $withdraw = $request;
        $withdraw['params'] = ['action' => 'withdraw', 'cmid' => $this->scorm->cmid, 'imageid' => $pending->id, 'revision' => 2];
        $this->assertTrue(json_decode($this->child_request($withdraw), true)['success']);
        $this->assertTrue(json_decode($this->child_request($file), true)['denied']);
        $student['params'] = ['action' => 'list', 'cmid' => $this->scorm->cmid];
        $this->assertEmpty(json_decode($this->child_request($student), true)['images']);
    }

    public function test_endpoint_generation_requires_explicit_confirmation(): void {
        $request = ['mode' => 'api', 'userid' => $this->teacher->id,
            'params' => ['action' => 'generate', 'cmid' => $this->scorm->cmid, 'confirmed' => 0]];
        $this->assertFalse(json_decode($this->child_request($request), true)['success']);
        $request['params']['confirmed'] = 1;
        $response = json_decode($this->child_request($request), true, flags: JSON_THROW_ON_ERROR);
        $this->assertTrue($response['success']);
        $this->assertEquals('pending', $response['images'][0]['status']);
        $request['userid'] = $this->student->id;
        $request['params'] = ['action' => 'list', 'cmid' => $this->scorm->cmid];
        $this->assertEmpty(json_decode($this->child_request($request), true)['images']);
    }

    public function test_suspended_enrolment_and_availability_restriction_deny_file_access(): void {
        global $DB, $PAGE;
        $approved = $this->approve($this->pending());
        $enrol = $DB->get_record('enrol', ['courseid' => $this->course->id, 'enrol' => 'manual'], '*', MUST_EXIST);
        enrol_get_plugin('manual')->update_user_enrol($enrol, $this->student->id, ENROL_USER_SUSPENDED);
        $this->setUser($this->student);
        $this->denied(fn() => service::file($this->scorm->cmid, $approved->id, 'illustration', 'illustration.png'));
        $this->setAdminUser();
        enrol_get_plugin('manual')->update_user_enrol($enrol, $this->student->id, ENROL_USER_ACTIVE);
        set_config('enableavailability', 1);
        $DB->set_field('course_modules', 'availability', json_encode([
            'op' => '&', 'c' => [['type' => 'date', 'd' => '>=', 't' => time() + DAYSECS]], 'showc' => [true],
        ]), ['id' => $this->scorm->cmid]);
        rebuild_course_cache($this->course->id, true);
        $this->setUser($this->student);
        $this->denied(fn() => service::file($this->scorm->cmid, $approved->id, 'illustration', 'illustration.png'));
    }

    public function test_untrusted_file_bytes_are_not_published(): void {
        global $USER;
        $file = get_file_storage()->create_file_from_string([
            'contextid' => \context_user::instance($USER->id)->id, 'component' => 'user', 'filearea' => 'draft',
            'itemid' => 78647, 'filepath' => '/', 'filename' => 'not-an-image.png', 'mimetype' => 'image/png',
        ], '<svg><script>alert(1)</script></svg>');
        $this->denied(fn() => service::register_draft($this->scorm->cmid, service::TOPIC, service::OBJECTIVE, $file), 'invalidimage');
    }

    public function test_parallel_approval_requests_publish_exactly_once(): void {
        global $DB;
        $pending = $this->pending();
        $request = ['mode' => 'api', 'userid' => $this->teacher->id, 'params' => [
            'action' => 'approve', 'cmid' => $this->scorm->cmid, 'imageid' => $pending->id,
            'revision' => 1, 'title' => 'Concurrent review', 'alttext' => 'Chest to Hands', 'reviewed' => 1,
        ]];
        $children = [];
        for ($n = 0; $n < 2; $n++) {
            $process = proc_open([PHP_BINARY, __DIR__ . '/fixtures/request.php'],
                [['pipe', 'r'], ['pipe', 'w'], ['pipe', 'w']], $pipes);
            $this->assertIsResource($process);
            fwrite($pipes[0], json_encode($request, JSON_THROW_ON_ERROR));
            fclose($pipes[0]);
            $children[] = [$process, $pipes];
        }
        $successes = 0;
        foreach ($children as [$process, $pipes]) {
            $stdout = stream_get_contents($pipes[1]);
            $stderr = stream_get_contents($pipes[2]);
            fclose($pipes[1]); fclose($pipes[2]);
            $this->assertEquals(0, proc_close($process), $stderr . $stdout);
            $data = json_decode($stdout, true, flags: JSON_THROW_ON_ERROR);
            $successes += (int)$data['success'];
        }
        $this->assertEquals(1, $successes);
        $this->assertEquals(1, $DB->count_records('local_studyguideai_image', [
            'activekey' => service::key($this->scorm->cmid, service::TOPIC, service::OBJECTIVE),
        ]));
        $this->assertEquals(1, $DB->count_records('local_studyguideai_audit', ['imageid' => $pending->id, 'action' => 'approve']));
        $this->assertCount(1, get_file_storage()->get_area_files(\context_module::instance($this->scorm->cmid)->id,
            'local_studyguideai', 'illustration', $pending->id, 'id', false));
    }

}
