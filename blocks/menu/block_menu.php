<?php

defined('MOODLE_INTERNAL') || die();

class block_menu extends block_base {

    public function init() {
        $this->title = get_string('pluginname', 'block_menu');
    }

    /**
     * Return compact SCORM progress for a Board's question courses.
     *
     * @param array $courseids Course IDs included in the progress calculation.
     * @return array
     */
    private function get_board_progress(array $courseids): array {
        global $DB, $USER;

        $scoresum = 0;
        $attempted = 0;
        $passed = 0;
        $total = 0;
        $lastexam = '';
        $lasttime = 0;

        foreach ($courseids as $courseid) {
            $scorms = $DB->get_records('scorm', ['course' => (int) $courseid]);
            $total += count($scorms);

            foreach ($scorms as $scorm) {
                $bestscores = [];
                $attempts = $DB->get_records('scorm_attempt', [
                    'scormid' => $scorm->id,
                    'userid' => $USER->id,
                ]);
                foreach ($attempts as $attempt) {
                    $track = $DB->get_record('scorm_scoes_value', [
                        'attemptid' => $attempt->id,
                        'elementid' => 8,
                    ]);
                    if (!$track || $track->value === null || $track->value === '') {
                        continue;
                    }
                    $bestscores[] = (float) $track->value;
                    if ((int) $track->timemodified > $lasttime) {
                        $lasttime = (int) $track->timemodified;
                        $lastexam = $scorm->name;
                    }
                }

                if ($bestscores) {
                    $best = max($bestscores);
                    $scoresum += $best;
                    $attempted++;
                    if ($best >= 70) {
                        $passed++;
                    }
                }
            }
        }

        return [
            'grade' => $attempted ? round($scoresum / $attempted, 2) : 0,
            'passed' => $passed,
            'total' => $total,
            'lastexam' => $lastexam,
            'lasttime' => $lasttime,
        ];
    }

    /**
     * Render a direct Board resource button when the user can access its course.
     *
     * @param int $courseid Moodle course ID.
     * @param string $label Button label.
     * @param string $type Visual button type.
     * @return string
     */
    private function board_resource_button(int $courseid, string $label, string $type): string {
        global $DB, $USER;

        $course = $DB->get_record('course', ['id' => $courseid, 'visible' => 1], 'id');
        if (!$course) {
            return '<span class="board-hub__action board-hub__action--pending">' . s($label) . '</span>';
        }

        $context = context_course::instance($courseid);
        $hasaccess = is_enrolled($context, $USER->id, '', true)
            || has_capability('moodle/course:view', $context, $USER->id);
        if (!$hasaccess) {
            return '<span class="board-hub__action board-hub__action--pending">' . s($label) . '</span>';
        }

        $url = new moodle_url('/course/view.php', ['id' => $courseid]);
        return '<a class="board-hub__action board-hub__action--' . s($type) . '" href="'
            . $url->out(false) . '">' . s($label) . '</a>';
    }

    public function get_content() {
        global $USER, $DB;

        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass();

        $loginurl = (new moodle_url('/login/index.php'))->out(false);
        $signupurl = (new moodle_url('/login/signup.php'))->out(false);
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

        if ($hasboardaccess) {
            $boarddashboard = [
                [
                    'name' => 'Foundations',
                    'description' => 'Build a strong foundation in Oriental Medicine.',
                    'progressids' => [30],
                    'area' => 'foundations',
                    'resources' => [
                        [30, 'Questions', 'questions'],
                        [43, 'Simulation', 'simulation'],
                        [93, 'Exam Key', 'notes'],
                        [48, 'Games', 'games'],
                    ],
                ],
                [
                    'name' => 'Acupuncture & Point Location',
                    'description' => 'Master point location, indications and acupuncture knowledge.',
                    'progressids' => [34],
                    'area' => 'pointlocation',
                    'resources' => [
                        [34, 'Questions', 'questions'],
                        [40, 'Simulation', 'simulation'],
                        [94, 'Exam Key', 'notes'],
                        [83, 'Games', 'games'],
                    ],
                ],
                [
                    'name' => 'Chinese Herbology',
                    'description' => 'Master Chinese herbs and classical formulas.',
                    'progressids' => [45, 44],
                    'area' => 'herbology',
                    'resources' => [
                        [45, 'Single Herbs', 'questions'],
                        [44, 'Formulas', 'questions'],
                        [41, 'Simulation', 'simulation'],
                        [92, 'Exam Key', 'notes'],
                        [82, 'Games', 'games'],
                    ],
                ],
                [
                    'name' => 'Biomedicine',
                    'description' => 'Prepare for biomedical concepts and clinical knowledge.',
                    'progressids' => [36],
                    'area' => 'biomedicine',
                    'resources' => [
                        [36, 'Questions', 'questions'],
                        [42, 'Simulation', 'simulation'],
                        [91, 'Exam Key', 'notes'],
                        [84, 'Games', 'games'],
                    ],
                ],
                [
                    'name' => 'Clean Needle Techniques',
                    'description' => 'Review clean needle technique and safety principles.',
                    'progressids' => [113],
                    'area' => 'cleanneedle',
                    'resources' => [
                        [113, 'Questions', 'questions'],
                        [118, 'Simulation', 'simulation'],
                        [120, 'Exam Key', 'notes'],
                        [119, 'Games', 'games'],
                    ],
                ],
            ];

            $boardcards = '';
            foreach ($boarddashboard as $subject) {
                $progress = $this->get_board_progress($subject['progressids']);
                $grade = min(100, max(0, (float) $progress['grade']));
                $actions = '';
                foreach ($subject['resources'] as $resource) {
                    $actions .= $this->board_resource_button($resource[0], $resource[1], $resource[2]);
                }
                $studyguideurl = new moodle_url('/blocks/myprogress/study-guide.php', [
                    'area' => $subject['area'],
                ]);
                $actions .= '<a class="board-hub__action board-hub__action--guide study-guide-dashboard-link" href="'
                    . $studyguideurl->out(false) . '">Study Guide</a>';
                if ($subject['area'] === 'herbology') {
                    $comparisonsurl = new moodle_url('/study-guide/comparisons/index.html');
                    $actions .= '<a class="board-hub__action comparisons-dashboard-link" href="'
                        . $comparisonsurl->out(false) . '">Comparisons</a>';
                }

                $lastexam = 'No exam taken yet';
                if ($progress['lastexam'] !== '') {
                    $lastexam = s($progress['lastexam']) . '<br><small>'
                        . userdate($progress['lasttime'], '%b %d, %Y · %I:%M %p') . '</small>';
                }

                $boardcards .= '<article class="board-hub__card">'
                    . '<h3>' . s($subject['name']) . '</h3>'
                    . '<p class="board-hub__description">' . s($subject['description']) . '</p>'
                    . '<div class="board-hub__progress"><strong>' . format_float($progress['grade'], 2, true)
                    . '%</strong><span>' . $progress['passed'] . ' of ' . number_format($progress['total'])
                    . ' exams passed</span></div>'
                    . '<div class="board-hub__bar"><span style="width:' . $grade . '%"></span></div>'
                    . '<p class="board-hub__last"><strong>Last exam:</strong><br>' . $lastexam . '</p>'
                    . '<div class="board-hub__actions">' . $actions . '</div>'
                    . '</article>';
            }

            $this->content->text = <<<HTML
<section class="board-hub" aria-labelledby="board-hub-title">
  <header class="board-hub__heading">
    <p>BOARD PREPARATION</p>
    <h2 id="board-hub-title">Your NCBAHM Board Preparation</h2>
    <span>Track your progress and open Questions, Simulation, Exam Keys, Games, Study Guides or Comparisons directly from one screen.</span>
  </header>
  <div class="board-hub__grid">{$boardcards}</div>
</section>
HTML;
            return $this->content;
        }

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
                'courseid' => null,
                'credits' => null,
                'enrolmenturl' => '',
            ],
            [
                'title' => 'Point Categories',
                'courseid' => null,
                'credits' => null,
                'enrolmenturl' => '',
            ],
        ];

        $hasceaccess = false;
        foreach ($ceproducts as $index => $ceproduct) {
            $courseid = (int) ($ceproduct['courseid'] ?? 0);
            $courseaccess = false;
            if (!$isguestview && $courseid && $DB->record_exists('course', ['id' => $courseid])) {
                $coursecontext = context_course::instance($courseid);
                $courseaccess = is_enrolled($coursecontext, $USER->id, '', true)
                    || has_capability('moodle/course:view', $coursecontext, $USER->id);
            }
            $ceproducts[$index]['hasaccess'] = $courseaccess;
            $hasceaccess = $hasceaccess || $courseaccess;
        }

        $cerows = '';
        foreach ($ceproducts as $ceproduct) {
            if ($hasceaccess && !$ceproduct['hasaccess']) {
                continue;
            }

            $credits = (float) ($ceproduct['credits'] ?? 0);
            $isavailable = $credits > 0 && !empty($ceproduct['enrolmenturl']);
            $creditlabel = $isavailable ? format_float($credits, 2, true) : 'Pending approval';
            $pricelabel = $isavailable ? '$' . number_format($credits * 10, 2) : '—';
            if ($ceproduct['hasaccess']) {
                $courseurl = new moodle_url('/course/view.php', ['id' => $ceproduct['courseid']]);
                $access = '<a href="' . $courseurl->out(false) . '">Open CE course</a>';
            } else {
                $access = $isavailable
                    ? '<a href="' . s($ceproduct['enrolmenturl']) . '">Buy CE course</a>'
                    : '<span>Not available yet</span>';
            }

            $cerows .= '<tr><th scope="row">' . s($ceproduct['title']) . '</th>'
                . '<td>' . $creditlabel . '</td><td>' . $pricelabel . '</td><td>' . $access . '</td></tr>';
        }

        $accountactions = $isguestview
            ? '<a class="learning-catalog-action learning-catalog-action--secondary" href="' . $loginurl . '">Log In</a>'
                . '<a class="learning-catalog-action" href="' . $signupurl . '">Create Account</a>'
            : '';

        $showboardsection = !$hasceaccess || $hasboardaccess;
        $showcesection = !$hasboardaccess || $hasceaccess;

        $boardsection = '';
        if ($showboardsection) {
            $boardsection = <<<HTML
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
HTML;
        }

        $cesection = '';
        if ($showcesection) {
            $cesection = <<<HTML
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
  </section>
HTML;
        }

        if ($hasboardaccess && !$hasceaccess) {
            $catalogeyebrow = 'BOARD PREPARATION';
            $catalogtitle = 'Your Board Preparation';
            $catalogintro = 'Your $30 payment provides 30 days of access to all five Board preparation areas. The payment does not renew automatically.';
        } else if ($hasceaccess && !$hasboardaccess) {
            $catalogeyebrow = 'CONTINUING EDUCATION';
            $catalogtitle = 'Your Continuing Education';
            $catalogintro = 'Continue the approved Continuing Education courses you purchased.';
        } else {
            $catalogeyebrow = 'CHOOSE YOUR LEARNING PATH';
            $catalogtitle = 'Board Preparation or Continuing Education';
            $catalogintro = 'One $30 payment provides 30 days of access to all five Board preparation areas. The payment does not renew automatically. Continuing Education courses are purchased separately according to their approved credits.';
        }

        $this->content->text = <<<HTML
<section class="learning-product-catalog" aria-labelledby="learning-product-catalog-title">
  <header class="learning-product-catalog__hero">
    <p class="public-learning-routes__eyebrow">{$catalogeyebrow}</p>
    <h2 id="learning-product-catalog-title">{$catalogtitle}</h2>
    <p>{$catalogintro}</p>
    <div class="learning-product-catalog__account-actions">{$accountactions}</div>
  </header>
{$boardsection}
{$cesection}
</section>
HTML;

        return $this->content;
    }
}
