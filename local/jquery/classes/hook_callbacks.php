<?php
namespace local_jquery;

/**
 * Load the bundled library when a page header is generated.
 *
 * @package    local_jquery
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class hook_callbacks {
    /**
     * Register the existing library in the page head.
     *
     * @param \core\hook\output\before_standard_head_html_generation $hook Output hook.
     */
    public static function before_standard_head_html_generation(
        \core\hook\output\before_standard_head_html_generation $hook
    ): void {
        $hook->renderer->get_page()->requires->js('/local/jquery/jquery-1.8.3.min.js', true);
    }
}
