<?php
namespace local_studyguideai;
defined('MOODLE_INTERNAL') || die();

/** Small integration surface: manual images and other V2 tools remain independent. */
class ui {
    public static function render(int $cmid): string {
        [, , $context] = service::access($cmid);
        $strings = [];
        foreach (['illustrations', 'generate', 'approve', 'reject', 'withdraw', 'replace', 'title', 'alttext',
                'review', 'audit', 'error', 'previewerror', 'confirmcost', 'confirmwithdraw', 'confirmapprove', 'confirmreject', 'nocurrent', 'audit_generated', 'audit_approve', 'audit_reject', 'audit_withdraw',
                'audit_replaced', 'audit_replacement', 'audit_expired'] as $key) {
            $strings[$key] = get_string($key, 'local_studyguideai');
        }
        $config = [
            'cmid' => $cmid, 'topic' => service::TOPIC, 'objective' => service::OBJECTIVE,
            'api' => (new \moodle_url('/local/studyguideai/api.php'))->out(false), 'sesskey' => sesskey(),
            'generate' => has_capability('local/studyguideai:generate', $context),
            'approve' => has_capability('local/studyguideai:approve', $context),
            'manage' => has_capability('local/studyguideai:manage', $context), 'strings' => $strings,
        ];
        $images = array_values(array_map([service::class, 'serialize'], service::visible($cmid, service::TOPIC, service::OBJECTIVE)));
        // Server-render approved content so students can view it without JavaScript.
        $html = '';
        foreach ($images as $image) {
            if ($image['status'] === 'approved' && !empty($image['url'])) {
                $html .= \html_writer::tag('figure', \html_writer::empty_tag('img', [
                    'src' => $image['url'], 'alt' => $image['alttext'], 'style' => 'max-width:100%;height:auto',
                ]) . \html_writer::tag('figcaption', s($image['title'])));
            }
        }
        return \html_writer::tag('section', \html_writer::tag('h4', s($strings['illustrations'])) .
            \html_writer::tag('div', $html, ['data-sg-gallery' => '']) .
            \html_writer::tag('div', '', ['data-sg-controls' => '']) .
            \html_writer::tag('p', '', ['data-sg-status' => '', 'role' => 'status']) .
            \html_writer::tag('div', '', ['data-sg-review' => '']) .
            \html_writer::tag('div', '', ['data-sg-audit' => '', 'hidden' => 'hidden']), [
                'class' => 'studyguideai', 'data-config' => json_encode($config, JSON_THROW_ON_ERROR),
                'data-images' => json_encode($images, JSON_THROW_ON_ERROR),
            ]);
    }
}
