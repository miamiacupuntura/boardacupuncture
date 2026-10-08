<?php
namespace local_studyguideai;
defined('MOODLE_INTERNAL') || die();
class observer {
    public static function module_deleted(\core\event\course_module_deleted $event): void {
        global $DB;
        $cmid = $event->objectid;
        $records = $DB->get_records('local_studyguideai_image', ['cmid' => $cmid]);
        foreach ($records as $record) {
            if ($record->status === 'pending') {
                $context = \context_user::instance($record->generatedby, IGNORE_MISSING);
                if ($context) {
                    $file = get_file_storage()->get_file($context->id, 'user', 'draft',
                        $record->draftitemid, '/', $record->draftfilename);
                    if ($file) { $file->delete(); }
                }
            }
        }
        // Moodle deletes module-context files itself when removing the activity.
        $DB->delete_records('local_studyguideai_audit', ['cmid' => $cmid]);
        $DB->delete_records('local_studyguideai_image', ['cmid' => $cmid]);
    }
}
