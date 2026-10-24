<?php

class block_menu_course extends block_base {

function init() {
$this->title = get_string('pluginname', 'block_menu_course');
}

/**
 * Return the short, meaningful action shown beside a Board resource.
 *
 * The Basic View contains several different resource types. A generic
 * "Study" label makes it difficult to know what each button opens, so the
 * label is resolved from the known course IDs and retained course names.
 *
 * @param stdClass $course Moodle course record.
 * @return array Button label and modifier class.
 */
private function get_course_action($course) {
    $courseid = (int) $course->id;
    $name = core_text::strtolower($course->fullname);

    $knownresources = [
        'notes' => [91, 92, 93, 94, 120],
        'simulation' => [40, 41, 42, 43, 118],
        'games' => [48, 82, 83, 84, 119],
        'formulas' => [44, 117],
        'questions' => [30, 34, 36, 45, 113],
    ];

    if (in_array($courseid, $knownresources['notes'], true) || strpos($name, 'notes') !== false) {
        return ['Notes', 'notes'];
    }
    if (in_array($courseid, $knownresources['simulation'], true) || strpos($name, 'simulation') !== false) {
        return ['Simulation', 'simulation'];
    }
    if (in_array($courseid, $knownresources['games'], true)
            || strpos($name, 'playing') !== false
            || strpos($name, 'games') !== false) {
        return ['Games', 'games'];
    }
    if (in_array($courseid, $knownresources['formulas'], true) || strpos($name, 'formula') !== false) {
        return ['Formulas', 'formulas'];
    }
    if ($courseid === 45 || strpos($name, 'single herbs') !== false) {
        return ['Single Herbs', 'questions'];
    }
    if (in_array($courseid, $knownresources['questions'], true) || strpos($name, 'exam') !== false) {
        return ['Questions', 'questions'];
    }

    return ['Open', 'course'];
}

/**
 * Match a Board category with its Study Guide area.
 *
 * @param string $categoryname Moodle course-category name.
 * @return string|null Study Guide area key.
 */
private function get_study_guide_area($categoryname) {
    $name = core_text::strtolower($categoryname);

    if (strpos($name, 'foundation') !== false) {
        return 'foundations';
    }
    if (strpos($name, 'point location') !== false || strpos($name, 'acupuncture') !== false) {
        return 'pointlocation';
    }
    if (strpos($name, 'herb') !== false) {
        return 'herbology';
    }
    if (strpos($name, 'biomedicine') !== false) {
        return 'biomedicine';
    }
    if (strpos($name, 'clean needle') !== false) {
        return 'cleanneedle';
    }

    return null;
}

function get_content() {
global $USER, $SESSION, $OUTPUT, $CFG, $PAGE, $DB;

if ($this->content !== NULL) {
    return $this->content;
}

$this->content = new stdClass;

if (!isloggedin() || isguestuser()) {
    $loginurl = (new moodle_url('/login/index.php'))->out(false);
    $signupurl = (new moodle_url('/login/signup.php'))->out(false);
    $ceurl = (new moodle_url('/study-guide/continuing-education.html'))->out(false);
    $this->content->text = <<<HTML
<section class="public-learning-routes" aria-labelledby="public-learning-routes-title">
  <p class="public-learning-routes__eyebrow">CHOOSE YOUR LEARNING PATH</p>
  <h2 id="public-learning-routes-title">Board Preparation or Continuing Education</h2>
  <p class="public-learning-routes__intro">Select the access that matches your professional goal. Board preparation and Continuing Education are purchased separately.</p>
  <div class="public-learning-routes__grid">
    <article class="public-learning-route public-learning-route--board">
      <span class="public-learning-route__badge">MONTHLY MEMBERSHIP</span>
      <h3>Board Exam Preparation</h3>
      <p>Access the available Study Guides, learning resources and Board-level practice with one fixed membership.</p>
      <p class="public-learning-route__price"><strong>$30</strong> per month</p>
      <div class="public-learning-route__actions">
        <a class="public-learning-route__button" href="{$signupurl}">Sign Up</a>
        <a class="public-learning-route__button public-learning-route__button--secondary" href="{$loginurl}">Log In</a>
      </div>
    </article>
    <article class="public-learning-route public-learning-route--ce">
      <span class="public-learning-route__badge">PAY PER APPROVED CREDIT</span>
      <h3>Continuing Education</h3>
      <p>Browse courses that have been formally approved and activated for Continuing Education.</p>
      <p class="public-learning-route__price"><strong>$10</strong> per approved credit</p>
      <div class="public-learning-route__actions">
        <a class="public-learning-route__button" href="{$ceurl}">View CE Courses</a>
      </div>
    </article>
  </div>
</section>
HTML;
    return $this->content;
}

$membershipurl = (new moodle_url('/enrol/index.php', ['id' => 116]))->out(false);

require_once($CFG->dirroot.'/mod/scorm/locallib.php');
require_once($CFG->libdir.'/gradelib.php');
require_once($CFG->dirroot.'/grade/querylib.php');

$categoryNames = array();
$courseNames = array();
$courseLinks = array();
$courseSummary = array();
$noshow = false;

$rowHTML2 = <<<HTML
<tr>
HTML;
$rowHTMLend2 = <<<HTML
</tr>
HTML;
$rowHTML = <<<HTML
<td class="all-columns3" style="font-size:12px;text-align:center;">
HTML;
$rowHTMLend = <<<HTML
</td>
HTML;
$rowHTMLend3 = <<<HTML
</td></tr>
HTML;
$rowHTML4 = <<<HTML
<td class="all-columns4" style="font-size:12px;text-align:center;">
HTML;
$rowHTMLend4 = <<<HTML
</td>
HTML;
$rowHTML5 = <<<HTML
<td class="all-columns3" colspan="3" style="font-size:12px;text-align:center;">
HTML;
$rowHTML6 = <<<HTML
<td class="all-columns" style="text-align:left;">
HTML;
$rowHTML3 = <<<HTML
<tr><td colspan="2" class="special-column special-column2" style="text-align:center;background-color:#F47E2C;font-size:14px;font-weight:bold;color:#ffffff;padding:3px;">
HTML;

$categoryNames = array();
$courseNames = array();
$courseLinks = array();
$courseSummary = array();
$categories = $DB->get_records('course_categories');
$counting2 = 0;
$resultList2 = "";
$tempCategoryName = "";

foreach ($categories as $category) {
if ($category->name == "ALL-IN-ONE") {
	continue;
}
$courses = $DB->get_records('course', ['category' => $category->id]);
if (count($courses) == 0) {
	continue;
}
$tempCategoryName = "";
$categoryhasvisiblecourse = false;
$categoryhasaccess = false;
foreach ($courses as $course) {
if ($course->visible == false) {
	continue;
}
$categoryhasvisiblecourse = true;
$context = context_course::instance($course->id);
$hascourseaccess = is_enrolled($context, $USER->id, '', true)
	|| has_capability('moodle/course:view', $context, $USER->id);
$categoryhasaccess = $categoryhasaccess || $hascourseaccess;
if (!$hascourseaccess) {
	$noshow = true;
}
$categoryNames[$counting2] = $category->name;
$coursename = format_string($course->fullname, true, ['context' => $context]);
$courseNames[$counting2] = $rowHTML6 . $coursename . $rowHTMLend;
//$courseSummary[$counting2] = $rowHTML6. $course->summary . $rowHTMLend;

$courseURL = $hascourseaccess
	? (new moodle_url('/course/view.php', ['id' => $course->id]))->out(false)
	: $membershipurl;
$resourceaction = $this->get_course_action($course);
$courseAction = $hascourseaccess ? $resourceaction[0] : 'Subscribe';
$courseActionClass = $hascourseaccess
	? ' link-retry--' . $resourceaction[1]
	: ' link-retry--locked';
$courseActionLabel = $hascourseaccess
	? 'Open ' . $coursename
	: 'Subscribe to access ' . $coursename;
$courseActionLabel = s($courseActionLabel);
$examLink = <<<HTML
<a href="$courseURL" class="link-retry link-retry2{$courseActionClass}" aria-label="{$courseActionLabel}" title="{$courseActionLabel}">{$courseAction}</a>
HTML;

$courseLinks[$counting2] = $rowHTML4 . $examLink . $rowHTMLend4;

if ($tempCategoryName !== $categoryNames[$counting2]) {
$resultList2 = $resultList2 . $rowHTML3 . $categoryNames[$counting2] . $rowHTML2 . $rowHTMLend3 . $courseNames[$counting2] . $courseLinks[$counting2] . $rowHTMLend2;
$tempCategoryName = $categoryNames[$counting2];
} else {
$resultList2 = $resultList2 . $rowHTML2 . $courseNames[$counting2] . $courseLinks[$counting2] . $rowHTMLend2;
}
$counting2 = $counting2 + 1;
}
$studyguidearea = $this->get_study_guide_area($category->name);
if ($categoryhasvisiblecourse && $studyguidearea !== null) {
	$studyguideurl = $categoryhasaccess
		? (new moodle_url('/blocks/myprogress/study-guide.php', ['area' => $studyguidearea]))->out(false)
		: $membershipurl;
	$studyguideaction = $categoryhasaccess ? 'Study Guide' : 'Subscribe';
	$studyguideclass = $categoryhasaccess ? ' link-retry--guide' : ' link-retry--locked';
	$studyguidearia = $categoryhasaccess
		? 'Open the ' . format_string($category->name) . ' Study Guide'
		: 'Subscribe to access the ' . format_string($category->name) . ' Study Guide';
	$studyguidearia = s($studyguidearia);
	$resultList2 .= $rowHTML2
		. $rowHTML6 . 'Study Guide' . $rowHTMLend
		. $rowHTML4
		. '<a href="' . $studyguideurl . '" class="link-retry link-retry2' . $studyguideclass
		. '" aria-label="' . $studyguidearia . '" title="' . $studyguidearia . '">'
		. $studyguideaction . '</a>'
		. $rowHTMLend4 . $rowHTMLend2;
}
}
//array_multisort($categoryNames, $courseNames, $courseLinks, $courseAverage, $courseCompleted, $courseTotalScorms, $Lecture, $isLecture);

$htmlMainSection = <<<HTML
<table class="course-stats course-stats3">
  <tr>
    <th colspan = "2" class="first-column">Study Guide | Basic View</th>
  </tr>
    <td style="text-align:left;font-size:14px;" class="all-columns"><strong>Resource</strong></td>
	<td style="text-align:center;font-size:14px;" class="all-columns"><strong>Open</strong></td>
$resultList2
</table>
HTML;


$allcontent = $htmlMainSection;

if ($noshow == true) {
$purchase = <<<HTML
<section class="membership-access-notice" aria-labelledby="membership-access-title-course">
  <div>
    <strong id="membership-access-title-course">Your account is ready.</strong>
    <span>Activate the $30 monthly Board Preparation membership to open the courses below.</span>
  </div>
  <a href="{$membershipurl}" class="link-retry link-retry2 link-retry3">Start membership</a>
</section>
HTML;
	$allcontent = $purchase . $allcontent;
}

$this->content->text = $allcontent;
if ($this->content !== NULL) {
     return $this->content;
}
}
}
