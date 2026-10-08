<?php
namespace local_studyguideai\task;
defined('MOODLE_INTERNAL') || die();
class expire_drafts extends \core\task\scheduled_task {
    public function get_name(): string {
        return get_string('cleanup', 'local_studyguideai');
    }
    public function execute(): void {
        \local_studyguideai\service::expire();
    }
}
