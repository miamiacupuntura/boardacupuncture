<?php
defined('MOODLE_INTERNAL') || die();

/** Back up only illustrations which have been approved, including their audit history. */
class backup_local_studyguideai_plugin extends backup_local_plugin {
    protected function define_module_plugin_structure() {
        $plugin = $this->get_plugin_element(null, '../../modulename', 'scorm');
        $wrapper = new backup_nested_element($this->get_recommended_name());
        $plugin->add_child($wrapper);
        $images = new backup_nested_element('images');
        $image = new backup_nested_element('image', ['id'], [
            'topic', 'objective', 'status', 'title', 'alttext', 'contenthash', 'generatedby', 'reviewedby',
            'revision', 'replacedby', 'timecreated', 'timemodified',
        ]);
        $audit = new backup_nested_element('decisions');
        $decision = new backup_nested_element('decision', ['id'], ['actorid', 'action', 'relatedid', 'snapshot', 'timecreated']);
        $wrapper->add_child($images);
        $images->add_child($image);
        $image->add_child($audit);
        $audit->add_child($decision);
        $userinfo = $this->get_setting_value('userinfo');
        $authors = $userinfo ? 'generatedby, reviewedby' : '0 AS generatedby, 0 AS reviewedby';
        $image->set_source_sql("SELECT id, topic, objective, status, title, alttext, contenthash, $authors,
            revision, replacedby, timecreated, timemodified FROM {local_studyguideai_image}
            WHERE cmid = ? AND status IN ('approved', 'withdrawn', 'replaced')", [backup::VAR_MODID]);
        $actor = $userinfo ? 'actorid' : '0 AS actorid';
        $decision->set_source_sql("SELECT id, $actor, action, relatedid, snapshot, timecreated
            FROM {local_studyguideai_audit} WHERE imageid = ? ORDER BY id", [backup::VAR_PARENTID]);
        if ($userinfo) {
            $image->annotate_ids('user', 'generatedby');
            $image->annotate_ids('user', 'reviewedby');
            $decision->annotate_ids('user', 'actorid');
        }
        $image->annotate_files('local_studyguideai', 'illustration', 'id');
        return $plugin;
    }
}
