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

        // One 30-day purchase unlocks all five Board areas.
        $membershipcourseid = 116;
        $membershipurl = (new moodle_url('/enrol/index.php', ['id' => $membershipcourseid]))->out(false);
        $membershipexists = $DB->record_exists('course', ['id' => $membershipcourseid]);
        $hasboardaccess = false;
        $paymentready = false;

        if ($membershipexists) {
            $paymentready = $DB->record_exists('enrol', [
                'courseid' => $membershipcourseid,
                'enrol' => 'gwpayments',
                'status' => 0,
            ]);
            if (!$isguestview) {
                $membershipcontext = context_course::instance($membershipcourseid);
                $hasboardaccess = is_enrolled($membershipcontext, $USER->id, '', true)
                    || has_capability('moodle/course:view', $membershipcontext, $USER->id);
            }
        }

        $boardproducts = [
            [
                'name' => 'Foundations of Oriental Medicine',
                'courseid' => 30,
                'openurl' => (new moodle_url('/blocks/menu/board.php', ['board' => 'foundations']))->out(false),
            ],
            [
                'name' => 'Acupuncture & Point Location',
                'courseid' => 34,
                'openurl' => (new moodle_url('/blocks/menu/board.php', ['board' => 'pointlocation']))->out(false),
            ],
            [
                'name' => 'Chinese Herbology',
                'courseid' => 45,
                'openurl' => (new moodle_url('/blocks/menu/board.php', ['board' => 'herbology']))->out(false),
            ],
            [
                'name' => 'Biomedicine',
                'courseid' => 36,
                'openurl' => (new moodle_url('/blocks/menu/board.php', ['board' => 'biomedicine']))->out(false),
            ],
            [
                'name' => 'Clean Needle Techniques',
                'courseid' => 113,
                'openurl' => (new moodle_url('/blocks/menu/board.php', ['board' => 'cleanneedle']))->out(false),
            ],
        ];

        $boardrows = '';
        foreach ($boardproducts as $boardproduct) {
            $statuslabel = $hasboardaccess ? 'ACCESS ACTIVE' : 'INCLUDED WITH BOARD ACCESS';
            $statusclass = $hasboardaccess ? ' board-product-row__status--active' : '';
            if ($hasboardaccess) {
                $actionhtml = '<a href="' . $boardproduct['openurl'] . '">Open Board</a>';
            } else {
                $actionhtml = '<span class="board-product-row__pending">Available after purchase</span>';
            }

            $boardrows .= '<li class="board-product-row">'
                . '<div><span class="board-product-row__status' . $statusclass . '">' . $statuslabel . '</span>'
                . '<h4>' . s($boardproduct['name']) . '</h4>'
                . '<p>Questions, simulations, games, notes and available Study Guides.</p></div>'
                . '<strong class="board-product-row__price">Included <small>all five Boards</small></strong>'
                . $actionhtml . '</li>';
        }

        if ($hasboardaccess) {
            $membershipaction = '<span class="board-membership-action board-membership-action--active">ACCESS ACTIVE</span>';
        } else if ($isguestview) {
            $membershipaction = '<a class="board-membership-action" href="' . $loginurl . '">Log in to purchase</a>';
        } else if ($paymentready) {
            $membershipaction = '<a class="board-membership-action" href="' . $membershipurl . '">Buy all Board access</a>';
        } else {
            $membershipaction = '<span class="board-membership-action board-membership-action--pending">Payment setup pending</span>';
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
    <p>One $30 payment provides 30 days of access to all five Board preparation areas. The payment does not renew automatically. Continuing Education courses are purchased separately according to their approved credits.</p>
    <div class="learning-product-catalog__account-actions">{$accountactions}</div>
  </header>

  <section class="learning-product-section" id="board-access-options" aria-labelledby="board-access-options-title">
    <div class="learning-product-section__heading">
      <div>
        <span>BOARD PREPARATION</span>
        <h3 id="board-access-options-title">All five Boards included</h3>
        <p>Pay once and study any Board during the same 30-day access period.</p>
      </div>
      <strong>$30 <small>all Boards / 30 days</small></strong>
    </div>
    <div class="board-membership-purchase">{$membershipaction}</div>
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
