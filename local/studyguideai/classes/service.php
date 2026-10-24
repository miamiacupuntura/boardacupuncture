<?php
namespace local_studyguideai;

defined('MOODLE_INTERNAL') || die();

/** Illustration lifecycle. All publication decisions are locked and transactional. */
class service {
    public const TOPIC = 'channels-and-collaterals';
    public const OBJECTIVE = 'primary-channel-circulation';
    public const MAXSIZE = 10485760;

    /** Resolve SCORM and enforce enrolment, visibility and availability on every access. */
    public static function access(int $cmid): array {
        global $DB, $USER, $CFG;
        $cm = get_coursemodule_from_id('scorm', $cmid, 0, false, MUST_EXIST);
        $course = get_course($cm->course);
        $context = \context_module::instance($cmid);
        require_login($course, false, $cm, false, true);
        if (isguestuser() || !isloggedin()) {
            self::fail('forbidden');
        }
        $staff = self::can_review($context);
        if (!$staff && !is_enrolled($context, $USER, '', true)) {
            self::fail('forbidden');
        }
        $cminfo = get_fast_modinfo($course)->get_cm($cmid);
        if (!$cminfo->uservisible) {
            self::fail('forbidden');
        }
        $scorm = $DB->get_record('scorm', ['id' => $cm->instance, 'course' => $course->id], '*', MUST_EXIST);
        require_once($CFG->dirroot . '/mod/scorm/locallib.php');
        if (!$staff && !scorm_get_availability_status($scorm)[0]) {
            self::fail('forbidden');
        }
        return [$cm, $course, $context, $scorm];
    }

    public static function can_review(\context $context): bool {
        foreach (['generate', 'approve', 'manage'] as $capability) {
            if (has_capability('local/studyguideai:' . $capability, $context)) {
                return true;
            }
        }
        return false;
    }

    public static function scope(string $topic, string $objective): void {
        // Only the subject matter already supported by V2 may be generated.
        if ($topic !== self::TOPIC || $objective !== self::OBJECTIVE) {
            self::fail('invalidscope');
        }
    }

    public static function key(int $cmid, string $topic, string $objective): string {
        return hash('sha256', $cmid . ':' . $topic . ':' . $objective);
    }

    private static function lock(int $cmid, string $topic, string $objective): \core\lock\lock {
        $lock = \core\lock\lock_config::get_lock_factory('local_studyguideai')->get_lock(
            self::key($cmid, $topic, $objective), 0);
        if (!$lock) {
            self::fail('conflict');
        }
        return $lock;
    }

    public static function fail(string $message): never {
        throw new \moodle_exception($message, 'local_studyguideai');
    }

    private static function metadata(string $title, string $alttext): array {
        $title = trim(clean_param($title, PARAM_TEXT));
        $alttext = trim(clean_param($alttext, PARAM_TEXT));
        if ($title === '' || $alttext === '' || \core_text::strlen($title) > 255 || \core_text::strlen($alttext) > 1000) {
            self::fail('invalidmetadata');
        }
        return [$title, $alttext];
    }

    private static function validate_image(\stored_file $file): void {
        if ($file->is_directory() || $file->get_filesize() <= 0 || $file->get_filesize() > self::MAXSIZE) {
            self::fail('invalidimage');
        }
        $info = @getimagesizefromstring($file->get_content());
        if (!$info || !in_array($info['mime'], ['image/png', 'image/jpeg'], true) ||
                $info[0] * $info[1] > 25000000 || $info['mime'] !== $file->get_mimetype()) {
            self::fail('invalidimage');
        }
    }

    /** Register a server-generated draft. No arbitrary client file ID is accepted. */
    public static function register_draft(int $cmid, string $topic, string $objective, \stored_file $file): \stdClass {
        global $DB, $USER;
        [$cm, , $context] = self::access($cmid);
        require_capability('local/studyguideai:generate', $context);
        self::scope($topic, $objective);
        if ((int)$file->get_contextid() !== (int)\context_user::instance($USER->id)->id ||
                $file->get_component() !== 'user' || $file->get_filearea() !== 'draft' || $file->get_filepath() !== '/') {
            self::fail('invalidimage');
        }
        self::validate_image($file);
        $lock = self::lock($cmid, $topic, $objective);
        try {
            $transaction = $DB->start_delegated_transaction();
            $record = (object)[
                'cmid' => $cmid, 'scormid' => $cm->instance, 'topic' => $topic, 'objective' => $objective,
                'status' => 'pending', 'activekey' => null, 'generatedby' => $USER->id, 'reviewedby' => 0,
                'draftitemid' => $file->get_itemid(), 'draftfilename' => $file->get_filename(),
                'title' => 'Hand Yin Channels', 'alttext' => 'Chest to Hands: Lung, Pericardium and Heart.',
                'contenthash' => $file->get_contenthash(), 'revision' => 1, 'replacedby' => 0,
                'timecreated' => time(), 'timemodified' => time(), 'expires' => time() + 3 * DAYSECS,
            ];
            $record->id = $DB->insert_record('local_studyguideai_image', $record);
            self::audit($record, 'generated');
            $transaction->allow_commit();
            return $record;
        } catch (\Throwable $e) {
            if (isset($transaction)) {
                $transaction->rollback($e);
            }
            throw $e;
        } finally {
            $lock->release();
        }
    }

    /** Call Moodle's AI manager with a fixed approved educational subject, never from a task. */
    public static function generate(int $cmid, string $topic, string $objective): \stdClass {
        global $USER;
        [, , $context] = self::access($cmid);
        require_capability('local/studyguideai:generate', $context);
        self::scope($topic, $objective);
        $lock = \core\lock\lock_config::get_lock_factory('local_studyguideai')->get_lock('generate:' . $cmid . ':' . $USER->id, 0);
        if (!$lock) {
            self::fail('conflict');
        }
        try {
            $prompt = "Create ONE colorful educational schematic illustration for acupuncture students.\n" .
                "TITLE: Hand Yin Channels\n" .
                "The 12 Primary Channels are connected to form a continuous circuit around the body. " .
                "Hand Yin: Lung, Pericardium, Heart — Chest → Hands.\n" .
                "Show Chest and Hands connected by an arrow from Chest to Hands. Include Lung, Pericardium, Heart. " .
                "Do not draw anatomical pathways, acupuncture points, or unverified details. Do not add other channel names or facts.";
            $action = new \core_ai\aiactions\generate_image($context->id, $USER->id, $prompt, 'standard', 'square', 1, 'vivid');
            $response = \core\di::get(\core_ai\manager::class)->process_action($action);
            $file = $response->get_response_data()['draftfile'] ?? null;
            if (!$response->get_success() || !$file instanceof \stored_file) {
                // Provider errors can contain URLs or authentication details: do not return them to the browser.
                self::fail('error');
            }
            return self::register_draft($cmid, $topic, $objective, $file);
        } finally {
            $lock->release();
        }
    }

    private static function record(int $cmid, int $id): \stdClass {
        global $DB;
        $record = $DB->get_record('local_studyguideai_image', ['id' => $id, 'cmid' => $cmid]);
        if (!$record || (int)$record->scormid !== (int)$DB->get_field('course_modules', 'instance', ['id' => $cmid])) {
            self::fail('forbidden');
        }
        return $record;
    }

    private static function draft(\stdClass $record): \stored_file {
        $file = get_file_storage()->get_file(\context_user::instance($record->generatedby)->id,
            'user', 'draft', $record->draftitemid, '/', $record->draftfilename);
        if (!$file || $record->expires <= time()) {
            self::fail('expired');
        }
        if ($file->get_contenthash() !== $record->contenthash) {
            self::fail('invalidimage');
        }
        self::validate_image($file);
        return $file;
    }

    /** Decisions use a revision and explicit replacement target to reject stale UI requests. */
    public static function decide(int $cmid, int $id, string $decision, int $revision,
            string $title = '', string $alttext = '', int $replaceid = 0): \stdClass {
        global $DB, $USER;
        [, , $context] = self::access($cmid);
        if (!in_array($decision, ['approve', 'reject', 'withdraw'], true)) {
            self::fail('invalidscope');
        }
        require_capability('local/studyguideai:' . ($decision === 'withdraw' ? 'manage' : 'approve'), $context);
        $initial = self::record($cmid, $id);
        $lock = self::lock($cmid, $initial->topic, $initial->objective);
        try {
            $transaction = $DB->start_delegated_transaction();
            $record = self::record($cmid, $id);
            if ((int)$record->revision !== $revision ||
                    ($decision === 'withdraw' ? $record->status !== 'approved' : $record->status !== 'pending')) {
                self::fail('conflict');
            }
            if ($decision !== 'withdraw' && (int)$record->generatedby !== (int)$USER->id) {
                self::fail('forbidden');
            }
            if ($decision === 'approve') {
                [$title, $alttext] = self::metadata($title, $alttext);
                $draft = self::draft($record);
                $key = self::key($cmid, $record->topic, $record->objective);
                $current = $DB->get_record('local_studyguideai_image', ['activekey' => $key]);
                if (($current ? (int)$current->id : 0) !== $replaceid) {
                    self::fail('conflict');
                }
                if ($current) {
                    require_capability('local/studyguideai:manage', $context);
                    $current->status = 'replaced';
                    $current->activekey = null;
                    $current->replacedby = $id;
                    $current->reviewedby = $USER->id;
                    $current->revision++;
                    $current->timemodified = time();
                    $DB->update_record('local_studyguideai_image', $current);
                    self::audit($current, 'replaced', $id);
                }
                $extension = $draft->get_mimetype() === 'image/png' ? 'png' : 'jpg';
                $stored = get_file_storage()->create_file_from_storedfile([
                    'contextid' => $context->id, 'component' => 'local_studyguideai', 'filearea' => 'illustration',
                    'itemid' => $id, 'filepath' => '/', 'filename' => 'illustration.' . $extension,
                    'userid' => $USER->id,
                ], $draft);
                $record->contenthash = $stored->get_contenthash();
                $record->title = $title;
                $record->alttext = $alttext;
                $record->status = 'approved';
                $record->activekey = $key;
                $draft->delete();
            } else if ($decision === 'reject') {
                $file = get_file_storage()->get_file(\context_user::instance($record->generatedby)->id,
                    'user', 'draft', $record->draftitemid, '/', $record->draftfilename);
                if ($file) {
                    $file->delete();
                }
                $record->status = 'rejected';
            } else {
                $record->status = 'withdrawn';
                $record->activekey = null;
            }
            $record->draftitemid = 0;
            $record->draftfilename = '';
            $record->reviewedby = $USER->id;
            $record->revision++;
            $record->timemodified = time();
            $DB->update_record('local_studyguideai_image', $record);
            self::audit($record, $decision === 'approve' && $replaceid ? 'replacement' : $decision, $replaceid);
            $transaction->allow_commit();
            return $record;
        } catch (\Throwable $e) {
            if (isset($transaction)) {
                $transaction->rollback($e);
            }
            throw $e;
        } finally {
            $lock->release();
        }
    }

    private static function audit(\stdClass $record, string $action, int $relatedid = 0): void {
        global $DB, $USER;
        $DB->insert_record('local_studyguideai_audit', (object)[
            'imageid' => $record->id, 'cmid' => $record->cmid, 'actorid' => $USER->id ?? 0,
            'action' => $action, 'relatedid' => $relatedid, 'timecreated' => time(),
            'snapshot' => json_encode(array_intersect_key((array)$record, array_flip([
                'status', 'title', 'alttext', 'contenthash', 'topic', 'objective', 'revision', 'replacedby',
            ])), JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
        ]);
    }

    public static function visible(int $cmid, string $topic, string $objective): array {
        global $DB, $USER;
        [$cm, , $context] = self::access($cmid);
        self::scope($topic, $objective);
        $key = self::key($cmid, $topic, $objective);
        $records = $DB->get_records('local_studyguideai_image', ['cmid' => $cmid, 'scormid' => $cm->instance, 'topic' => $topic, 'objective' => $objective], 'id DESC');
        return array_filter($records, static function($record) use ($context, $USER, $key) {
            if ($record->status === 'approved' && $record->activekey === $key) {
                return true;
            }
            return self::can_review($context) && ($record->status !== 'pending' || (int)$record->generatedby === (int)$USER->id);
        });
    }

    /** Used by pluginfile, not just the page: unapproved/old URLs are denied to students. */
    public static function file(int $cmid, int $id, string $filearea, string $filename): \stored_file {
        global $USER;
        [, , $context] = self::access($cmid);
        $record = self::record($cmid, $id);
        if ($filearea === 'pending') {
            require_capability('local/studyguideai:approve', $context);
            if ($record->status !== 'pending' || (int)$record->generatedby !== (int)$USER->id || $filename !== 'preview') {
                self::fail('forbidden');
            }
            return self::draft($record);
        }
        if ($filearea !== 'illustration') {
            self::fail('forbidden');
        }
        $published = $record->status === 'approved' &&
            $record->activekey === self::key($cmid, $record->topic, $record->objective);
        if (!$published && !(has_capability('local/studyguideai:manage', $context) &&
                in_array($record->status, ['withdrawn', 'replaced'], true))) {
            self::fail('forbidden');
        }
        $file = get_file_storage()->get_file($context->id, 'local_studyguideai', 'illustration', $id, '/', $filename);
        if (!$file || $file->is_directory() || $file->get_contenthash() !== $record->contenthash) {
            self::fail('forbidden');
        }
        return $file;
    }

    /** Safe API representation; never expose somebody else's draft identifiers or paths. */
    public static function serialize(\stdClass $record): array {
        $context = \context_module::instance($record->cmid);
        $data = array_intersect_key((array)$record, array_flip(['id', 'status', 'title', 'alttext', 'revision', 'topic', 'objective']));
        if ($record->status === 'pending') {
            $data['url'] = \moodle_url::make_pluginfile_url($context->id, 'local_studyguideai', 'pending', $record->id, '/', 'preview')->out(false);
        } else if (in_array($record->status, ['approved', 'withdrawn', 'replaced'], true)) {
            $files = get_file_storage()->get_area_files($context->id, 'local_studyguideai', 'illustration', $record->id, 'id', false);
            $file = reset($files);
            if ($file) {
                $data['url'] = \moodle_url::make_pluginfile_url($context->id, 'local_studyguideai', 'illustration', $record->id, '/', $file->get_filename())->out(false);
            }
        }
        return $data;
    }

    public static function history(int $cmid): array {
        global $DB;
        [, , $context] = self::access($cmid);
        require_capability('local/studyguideai:manage', $context);
        $records = $DB->get_records('local_studyguideai_audit', ['cmid' => $cmid], 'id DESC');
        foreach ($records as $record) {
            $user = $record->actorid ? $DB->get_record('user', ['id' => $record->actorid],
                'id,' . implode(',', \core_user\fields::get_name_fields())) : false;
            $record->actor = $user ? fullname($user) : get_string('unknownactor', 'local_studyguideai');
        }
        return array_values($records);
    }

    /** Expire only unpublished drafts; permanent illustrations are never removed by cron. */
    public static function expire(): void {
        global $DB;
        $records = $DB->get_records_select('local_studyguideai_image', 'status = ? AND expires <= ?', ['pending', time()]);
        foreach ($records as $initial) {
            $lock = \core\lock\lock_config::get_lock_factory('local_studyguideai')->get_lock(
                self::key($initial->cmid, $initial->topic, $initial->objective), 0);
            if (!$lock) { continue; } // A review in progress must not fail the entire cleanup task.
            try {
                $transaction = $DB->start_delegated_transaction();
                $record = self::record($initial->cmid, $initial->id);
                if ($record->status === 'pending' && $record->expires <= time()) {
                    $file = get_file_storage()->get_file(\context_user::instance($record->generatedby)->id,
                        'user', 'draft', $record->draftitemid, '/', $record->draftfilename);
                    if ($file) {
                        $file->delete();
                    }
                    $record->status = 'expired';
                    $record->draftitemid = 0;
                    $record->draftfilename = '';
                    $record->revision++;
                    $record->timemodified = time();
                    $DB->update_record('local_studyguideai_image', $record);
                    self::audit($record, 'expired');
                }
                $transaction->allow_commit();
            } catch (\Throwable $e) {
                if (isset($transaction)) {
                    $transaction->rollback($e);
                }
                throw $e;
            } finally {
                $lock->release();
            }
        }
    }
}
