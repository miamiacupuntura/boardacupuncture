<?php
defined('MOODLE_INTERNAL') || die();

/** Restore permanent images only after the SCORM instance and context exist. */
class restore_local_studyguideai_plugin extends restore_local_plugin {
    private array $images = [];
    private array $decisions = [];
    private int $parentimage = 0;

    protected function define_module_plugin_structure() {
        return [
            new restore_path_element('studyguideai_image', $this->get_pathfor('/images/image')),
            new restore_path_element('studyguideai_decision', $this->get_pathfor('/images/image/decisions/decision')),
        ];
    }
    public function process_studyguideai_image($data): void {
        $this->parentimage = (int)$data['id'];
        $this->images[$this->parentimage] = (object)$data;
    }
    public function process_studyguideai_decision($data): void {
        $this->decisions[] = [(object)$data, $this->parentimage];
    }
    public function after_restore_module(): void {
        global $DB;
        $cmid = $this->get_task()->get_moduleid();
        $cm = get_coursemodule_from_id('scorm', $cmid, 0, false, MUST_EXIST);
        $transaction = $DB->start_delegated_transaction();
        $newids = [];
        foreach ($this->images as $oldid => $data) {
            if (!in_array($data->status, ['approved', 'withdrawn', 'replaced'], true)) { continue; }
            \local_studyguideai\service::scope($data->topic, $data->objective);
            $record = (object)[
                'cmid' => $cmid, 'scormid' => $cm->instance, 'topic' => $data->topic, 'objective' => $data->objective,
                'status' => $data->status, 'title' => $data->title, 'alttext' => $data->alttext,
                'contenthash' => $data->contenthash, 'generatedby' => $this->get_mappingid('user', $data->generatedby, 0),
                'reviewedby' => $this->get_mappingid('user', $data->reviewedby, 0), 'revision' => $data->revision,
                'timecreated' => $data->timecreated, 'timemodified' => $data->timemodified, 'replacedby' => 0,
                'activekey' => $data->status === 'approved' ? \local_studyguideai\service::key($cmid, $data->topic, $data->objective) : null,
                'draftitemid' => 0, 'draftfilename' => '', 'expires' => 0,
            ];
            $newids[$oldid] = $DB->insert_record('local_studyguideai_image', $record);
            $this->set_mapping('local_studyguideai_image', $oldid, $newids[$oldid], true);
        }
        foreach ($this->images as $oldid => $data) {
            if (isset($newids[$oldid])) {
                $DB->set_field('local_studyguideai_image', 'replacedby', $newids[$data->replacedby] ?? 0, ['id' => $newids[$oldid]]);
            }
        }
        foreach ($this->decisions as [$data, $parentid]) {
            if (!isset($newids[$parentid])) { continue; }
            $snapshot = json_decode($data->snapshot, true, flags: JSON_THROW_ON_ERROR);
            if (isset($snapshot['replacedby'])) { $snapshot['replacedby'] = $newids[$snapshot['replacedby']] ?? 0; }
            $DB->insert_record('local_studyguideai_audit', (object)[
                'imageid' => $newids[$parentid], 'cmid' => $cmid,
                'actorid' => $this->get_mappingid('user', $data->actorid, 0), 'action' => $data->action,
                'relatedid' => $newids[$data->relatedid] ?? 0, 'snapshot' => json_encode($snapshot, JSON_THROW_ON_ERROR),
                'timecreated' => $data->timecreated,
            ]);
        }
        $this->add_related_files('local_studyguideai', 'illustration', 'local_studyguideai_image');
        $transaction->allow_commit();
    }
}
