# Study Guide illustration review (Moodle 4.5)

Development plugin. No automatic AI generation or deployment is performed by installation, upgrade, cron or tests.

The existing V2 Hand Yin illustration slot is bound to the SCORM course module, topic `channels-and-collaterals` and objective `primary-channel-circulation`. Other subjects must be added to the server-side allowlist with their own reviewed source; client prompts cannot change the subject. The existing manual uploads and other Study Guide tools are unchanged.

Editing teachers and managers receive `generate`, `approve` and `manage` capabilities at activity context. A pending image may be previewed/approved/rejected only by its generating instructor with the appropriate capability. Students cannot approve or fetch private drafts. Authors review the pixels, title and alternative text, then explicitly confirm publication. All API calls use POST and sesskey. Generation additionally requires charge confirmation. Tests inject a mocked HTTP client; test fixture pixels are never real paid generations.

Only approval copies the draft to `local_studyguideai/illustration` in the activity context. The `itemid` is the illustration record ID. File access checks enrolment, activity visibility/restrictions, SCORM dates and publication state. A withdrawal/replacement makes old URLs inaccessible to students. Formerly approved files remain for instructor audit; rejected drafts are deleted. User drafts expire after three days and cannot be approved after expiry.

A scope lock and database transaction protect decisions. The unique nullable `activekey` additionally guarantees a single current approved illustration per activity/topic/objective. Revisions reject stale requests; replacement must name the currently approved ID. File records, state changes and immutable audit snapshots commit together. File API content blobs left by a rollback are handled by Moodle's normal orphan cleanup. No prompts, headers, API keys, or private generated image bytes are added to Git.

Permanent files do not rely on draft cleanup or a login session. Students are served via authenticated `pluginfile.php` with private, zero-lifetime caching, so access is rechecked. A recipient who already downloaded an image can keep that copy; withdrawal controls future server access.

Backups include only formerly approved files and their decisions, never pending/rejected drafts. Restore remaps module, illustration, user and replacement IDs. Deleting an activity removes this plugin's metadata/drafts; Moodle removes module-context files. Privacy erasure removes authored files and personal attribution while retaining anonymised decision history.

Tests: `php vendor/bin/phpunit --testsuite local_studyguideai_testsuite` (initialise Moodle PHPUnit first). Endpoint tests run isolated CLI requests with fresh sessions against the PHPUnit database; generation always uses mocked HTTP. Frontend tests: `node --test --test-isolation=none local/studyguideai/tests/review_test.cjs`. No live OpenAI key is needed.
