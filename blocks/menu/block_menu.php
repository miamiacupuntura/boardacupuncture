<?php

defined('MOODLE_INTERNAL') || die();

class block_menu extends block_base {

    public function init() {
        $this->title = get_string('pluginname', 'block_menu');
    }

    public function get_content() {
        global $USER, $DB;

        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass();

        $loginurl = (new moodle_url('/login/index.php'))->out(false);
        $signupurl = (new moodle_url('/login/signup.php'))->out(false);
        $cecatalogurl = (new moodle_url('/study-guide/continuing-education.html'))->out(false);
        $isguestview = !isloggedin() || isguestuser();

        // Each Board is purchased separately. The primary course controls access to its blue study panel.
        $boardproducts = [
            [
                'name' => 'Foundations of Oriental Medicine',
                'courseid' => 30,
                'openurl' => (new moodle_url('/course/view.php', ['id' => 30]))->out(false),
            ],
            [
                'name' => 'Acupuncture & Point Location',
                'courseid' => 34,
                'openurl' => (new moodle_url('/course/view.php', ['id' => 34]))->out(false),
            ],
            [
                'name' => 'Chinese Herbology',
                'courseid' => 45,
                'openurl' => (new moodle_url('/blocks/myprogress/herbology.php'))->out(false),
            ],
            [
                'name' => 'Biomedicine',
                'courseid' => 36,
                'openurl' => (new moodle_url('/course/view.php', ['id' => 36]))->out(false),
            ],
            [
                'name' => 'Clean Needle Techniques',
                'courseid' => 113,
                'openurl' => (new moodle_url('/course/view.php', ['id' => 113]))->out(false),
            ],
        ];

        $boardrows = '';
        foreach ($boardproducts as $boardproduct) {
            $hasaccess = false;
            $paymentready = $DB->record_exists('enrol', [
                'courseid' => $boardproduct['courseid'],
                'enrol' => 'gwpayments',
                'status' => 0,
            ]);
            if (!$isguestview && $DB->record_exists('course', ['id' => $boardproduct['courseid']])) {
                $boardcontext = context_course::instance($boardproduct['courseid']);
                $hasaccess = is_enrolled($boardcontext, $USER->id, '', true)
                    || has_capability('moodle/course:view', $boardcontext, $USER->id);
            }

            $enrolurl = (new moodle_url('/enrol/index.php', ['id' => $boardproduct['courseid']]))->out(false);
            $statuslabel = $hasaccess ? 'ACCESS ACTIVE' : '$30 / 30 DAYS';
            $statusclass = $hasaccess ? ' board-product-row__status--active' : '';
            if ($hasaccess) {
                $actionhtml = '<a href="' . $boardproduct['openurl'] . '">Open Board</a>';
            } else if ($paymentready) {
                $actionhtml = '<a href="' . $enrolurl . '">Buy 30-day access</a>';
            } else {
                $actionhtml = '<span class="board-product-row__pending">Payment setup pending</span>';
            }

            $boardrows .= '<li class="board-product-row">'
                . '<div><span class="board-product-row__status' . $statusclass . '">' . $statuslabel . '</span>'
                . '<h4>' . s($boardproduct['name']) . '</h4>'
                . '<p>One-time payment. Access lasts 30 days from this Board\'s purchase date.</p></div>'
                . '<strong class="board-product-row__price">$30 <small>for 30 days</small></strong>'
                . $actionhtml . '</li>';
        }

        // Public CE titles are separate from the internal Study Guide names and routes.
        // Add approved credits and an enrolment URL only after the official approval record exists.
        $ceproducts = [
            [
                'title' => 'The Channels and Collaterals',
                'credits' => null,
                'enrolmenturl' => '',
            ],
            [
                'title' => 'Point Categories',
                'credits' => null,
                'enrolmenturl' => '',
            ],
        ];

        $cerows = '';
        foreach ($ceproducts as $ceproduct) {
            $credits = (float) ($ceproduct['credits'] ?? 0);
            $isavailable = $credits > 0 && !empty($ceproduct['enrolmenturl']);
            $creditlabel = $isavailable ? format_float($credits, 2, true) : 'Pending approval';
            $pricelabel = $isavailable ? '$' . number_format($credits * 10, 2) : '—';
            $access = $isavailable
                ? '<a href="' . s($ceproduct['enrolmenturl']) . '">Buy CE course</a>'
                : '<span>Not available yet</span>';

            $cerows .= '<tr><th scope="row">' . s($ceproduct['title']) . '</th>'
                . '<td>' . $creditlabel . '</td><td>' . $pricelabel . '</td><td>' . $access . '</td></tr>';
        }

        $accountactions = $isguestview
            ? '<a class="learning-catalog-action learning-catalog-action--secondary" href="' . $loginurl . '">Log In</a>'
                . '<a class="learning-catalog-action" href="' . $signupurl . '">Create Account</a>'
            : '';

        $this->content->text = <<<HTML
<section class="learning-product-catalog" aria-labelledby="learning-product-catalog-title">
  <header class="learning-product-catalog__hero">
    <p class="public-learning-routes__eyebrow">CHOOSE YOUR LEARNING PATH</p>
    <h2 id="learning-product-catalog-title">Board Preparation or Continuing Education</h2>
    <p>Each Board is purchased separately for $30 and provides 30 days of access. Payments do not renew automatically. Continuing Education courses are purchased according to their approved credits.</p>
    <div class="learning-product-catalog__account-actions">{$accountactions}</div>
  </header>

  <section class="learning-product-section" id="board-access-options" aria-labelledby="board-access-options-title">
    <div class="learning-product-section__heading">
      <div>
        <span>BOARD PREPARATION</span>
        <h3 id="board-access-options-title">Select a Board</h3>
        <p>Purchase one or more Boards. Each Board receives its own 30-day access period beginning on its purchase date.</p>
      </div>
      <strong>$30 <small>per Board / 30 days</small></strong>
    </div>
    <ul class="board-product-list">{$boardrows}</ul>
  </section>

  <section class="learning-product-section learning-product-section--ce" id="continuing-education-courses" aria-labelledby="continuing-education-title">
    <div class="learning-product-section__heading">
      <div>
        <span>CONTINUING EDUCATION</span>
        <h3 id="continuing-education-title">Continuing Education Courses</h3>
        <p>Only approved and active courses can be purchased. The associated Study Guide remains inside the individual course.</p>
      </div>
      <strong>$10 <small>per approved credit</small></strong>
    </div>
    <div class="ce-product-table-wrap">
      <table class="ce-product-table">
        <thead>
          <tr><th scope="col">Course</th><th scope="col">Approved credits</th><th scope="col">Price</th><th scope="col">Access</th></tr>
        </thead>
        <tbody>{$cerows}</tbody>
      </table>
    </div>
    <a class="learning-product-section__catalog-link" href="{$cecatalogurl}">View Continuing Education catalog</a>
  </section>
</section>
HTML;

        return $this->content;
    }
}
