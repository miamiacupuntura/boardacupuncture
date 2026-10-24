<?php
namespace local_studyguideai\privacy;
defined('MOODLE_INTERNAL') || die();

use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\userlist;
use core_privacy\local\request\writer;

/** Privacy requests remove user attribution and authored images without exposing drafts. */
class provider implements \core_privacy\local\metadata\provider,
        \core_privacy\local\request\plugin\provider, \core_privacy\local\request\core_userlist_provider {
    public static function get_metadata(collection $collection): collection {
        $collection->add_database_table('local_studyguideai_image', [
            'generatedby' => 'privacy:metadata:userid', 'reviewedby' => 'privacy:metadata:userid',
            'title' => 'privacy:metadata:metadata', 'alttext' => 'privacy:metadata:metadata',
        ], 'privacy:metadata:images');
        $collection->add_database_table('local_studyguideai_audit', [
            'actorid' => 'privacy:metadata:userid', 'snapshot' => 'privacy:metadata:metadata',
        ], 'privacy:metadata:audit');
        $collection->add_subsystem_link('core_files', [], 'privacy:metadata:files');
        return $collection;
    }

    public static function get_contexts_for_userid(int $userid): contextlist {
        $contexts = new contextlist();
        $contexts->add_from_sql('SELECT DISTINCT c.id FROM {context} c
            JOIN {local_studyguideai_image} i ON i.cmid = c.instanceid
            WHERE c.contextlevel = :level AND (i.generatedby = :author OR i.reviewedby = :reviewer)',
            ['level' => CONTEXT_MODULE, 'author' => $userid, 'reviewer' => $userid]);
        $contexts->add_from_sql('SELECT DISTINCT c.id FROM {context} c
            JOIN {local_studyguideai_audit} a ON a.cmid = c.instanceid
            WHERE c.contextlevel = :level AND a.actorid = :actor', ['level' => CONTEXT_MODULE, 'actor' => $userid]);
        return $contexts;
    }

    public static function get_users_in_context(userlist $userlist): void {
        $context = $userlist->get_context();
        if ($context->contextlevel !== CONTEXT_MODULE) { return; }
        foreach (['generatedby', 'reviewedby'] as $field) {
            $userlist->add_from_sql($field, "SELECT $field FROM {local_studyguideai_image} WHERE cmid = :cmid AND $field > 0",
                ['cmid' => $context->instanceid]);
        }
        $userlist->add_from_sql('actorid', 'SELECT actorid FROM {local_studyguideai_audit} WHERE cmid = :cmid AND actorid > 0',
            ['cmid' => $context->instanceid]);
    }

    public static function export_user_data(approved_contextlist $contextlist): void {
        global $DB;
        $userid = $contextlist->get_user()->id;
        foreach ($contextlist->get_contexts() as $context) {
            if ($context->contextlevel !== CONTEXT_MODULE) { continue; }
            $images = $DB->get_records_select('local_studyguideai_image', 'cmid = ? AND (generatedby = ? OR reviewedby = ?)',
                [$context->instanceid, $userid, $userid]);
            foreach ($images as $image) {
                $path = [get_string('pluginname', 'local_studyguideai'), (string)$image->id];
                $data = (object)array_intersect_key((array)$image, array_flip([
                    'status', 'title', 'alttext', 'generatedby', 'reviewedby', 'timecreated', 'timemodified',
                ]));
                writer::with_context($context)->export_data($path, $data);
                writer::with_context($context)->export_area_files($path, 'local_studyguideai', 'illustration', $image->id);
            }
            $audit = array_values($DB->get_records('local_studyguideai_audit', ['cmid' => $context->instanceid, 'actorid' => $userid]));
            writer::with_context($context)->export_data([get_string('pluginname', 'local_studyguideai'), 'Audit'], (object)['decisions' => $audit]);
        }
    }

    public static function delete_data_for_all_users_in_context(\context $context): void {
        global $DB;
        if ($context->contextlevel !== CONTEXT_MODULE) { return; }
        $images = $DB->get_records('local_studyguideai_image', ['cmid' => $context->instanceid]);
        foreach ($images as $image) { self::erase($context, $image, null); }
        $DB->set_field('local_studyguideai_audit', 'actorid', 0, ['cmid' => $context->instanceid]);
    }

    public static function delete_data_for_user(approved_contextlist $contextlist): void {
        foreach ($contextlist->get_contexts() as $context) {
            self::delete_users($context, [$contextlist->get_user()->id]);
        }
    }

    public static function delete_data_for_users(approved_userlist $userlist): void {
        self::delete_users($userlist->get_context(), $userlist->get_userids());
    }

    private static function delete_users(\context $context, array $userids): void {
        global $DB;
        if ($context->contextlevel !== CONTEXT_MODULE) { return; }
        foreach ($DB->get_records('local_studyguideai_image', ['cmid' => $context->instanceid]) as $image) {
            if (in_array((int)$image->generatedby, $userids) || in_array((int)$image->reviewedby, $userids)) {
                self::erase($context, $image, $userids);
            }
        }
        foreach ($userids as $userid) {
            $DB->set_field('local_studyguideai_audit', 'actorid', 0, ['cmid' => $context->instanceid, 'actorid' => $userid]);
        }
    }

    private static function erase(\context $context, \stdClass $initial, ?array $userids): void {
        global $DB;
        $lock = \core\lock\lock_config::get_lock_factory('local_studyguideai')->get_lock(
            \local_studyguideai\service::key($initial->cmid, $initial->topic, $initial->objective), 10);
        if (!$lock) { \local_studyguideai\service::fail('conflict'); }
        try {
            $transaction = $DB->start_delegated_transaction();
            $image = $DB->get_record('local_studyguideai_image', ['id' => $initial->id], '*', MUST_EXIST);
            if ($userids === null || in_array((int)$image->generatedby, $userids)) {
                if ($image->status === 'pending' && $image->generatedby) {
                    $draft = get_file_storage()->get_file(\context_user::instance($image->generatedby)->id,
                        'user', 'draft', $image->draftitemid, '/', $image->draftfilename);
                    if ($draft) { $draft->delete(); }
                }
                get_file_storage()->delete_area_files($context->id, 'local_studyguideai', 'illustration', $image->id);
                $image->generatedby = 0;
                $image->status = 'withdrawn';
                $image->activekey = null;
                $image->title = '';
                $image->alttext = '';
                $image->draftitemid = 0;
                $image->draftfilename = '';
                $image->contenthash = '';
                $DB->set_field('local_studyguideai_audit', 'snapshot', '{"status":"privacy-erased"}', ['imageid' => $image->id]);
            }
            if ($userids === null || in_array((int)$image->reviewedby, $userids)) { $image->reviewedby = 0; }
            $image->revision++;
            $image->timemodified = time();
            $DB->update_record('local_studyguideai_image', $image);
            $transaction->allow_commit();
        } catch (\Throwable $e) {
            if (isset($transaction)) { $transaction->rollback($e); }
            throw $e;
        } finally { $lock->release(); }
    }
}
