<?php

class block_menu extends block_base {

function init() {
$this->title = get_string('pluginname', 'block_menu');
}

function get_content() {
$details = false;

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
$boardcatalogurl = (new moodle_url('/study-guide/board-courses.html'))->out(false);
$cecatalogurl = (new moodle_url('/study-guide/continuing-education.html'))->out(false);
$studyguideurl = (new moodle_url('/study-guide/index.html'))->out(false);
$channelsurl = $studyguideurl . '#channels-chapter-introduction';
$pointcategoriesurl = $studyguideurl . '#point-categories-draft';

$hasboardmembership = false;
$membershipcourse = $DB->get_record('course', ['id' => 116], 'id', IGNORE_MISSING);
if ($membershipcourse) {
    $membershipcontext = context_course::instance($membershipcourse->id);
    $hasboardmembership = is_enrolled($membershipcontext, $USER->id, '', true)
        || has_capability('moodle/course:view', $membershipcontext, $USER->id);
}

if(!isset($_COOKIE["btnToggleDashboard"])) {
$checkBox = <<<HTML
<input class="course-input course-input2" onclick="Toggle2()" type="checkbox">
HTML;
$details = false;
} else {
if ($_COOKIE["btnToggleDashboard"] == 1) {
$checkBox = <<<HTML
<input class="course-input course-input2" onclick="Toggle2()" type="checkbox" checked>
HTML;
$details = true;
} else {
$checkBox = <<<HTML
<input class="course-input course-input2" onclick="Toggle2()" type="checkbox">
HTML;
$details = false;
}
}

require_once($CFG->dirroot.'/mod/scorm/locallib.php');
require_once($CFG->libdir.'/gradelib.php');
require_once($CFG->dirroot.'/grade/querylib.php');

$categoryNames = array();
$courseNames = array();
$courseLinks = array();
$courseSummary = array();

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

if ($details == true) {
$rowHTML3 = <<<HTML
<tr><td colspan="5" class="special-column special-column2" style="text-align:center;background-color:#F47E2C;font-size:14px;font-weight:bold;color:#ffffff;padding:3px;">
HTML;
} else {
$rowHTML3 = <<<HTML
<tr><td colspan="2" class="special-column special-column2" style="text-align:center;background-color:#F47E2C;font-size:14px;font-weight:bold;color:#ffffff;padding:3px;">
HTML;
}

$noshow = false;
if ($details == true) { /////////////////////////////////////////////////Cookie ON
$attemptids = array();
$courseAverage = array();
$courseCompleted = array();
$courseTotalScorms = array();
$Lecture = array();
$isLecture = array();
$counting = 0;
$resultList = "";
$tempCategoryName = "";

$categories = $DB->get_records('course_categories');
foreach ($categories as $category) {
if ($category->name == "ALL-IN-ONE") {
	continue;
}
$courses = $DB->get_records('course', ['category' => $category->id]);
if (count($courses) == 0) {
	continue;
}
$tempCategoryName = "";
foreach ($courses as $course) {
if ($course->visible == false) {
	continue;
}
$context = context_course::instance($course->id);
$hascourseaccess = is_enrolled($context, $USER->id, '', true)
	|| has_capability('moodle/course:view', $context, $USER->id);
if (!$hascourseaccess) {
	$noshow = true;
}
$average = 0;
$count = 0;
$countCompleted = 0;
$overallcount = 0;

$scorms = $DB->get_records('scorm', ['course' => $course->id]);
foreach ($scorms as $scorm) {
$attempts = $DB->get_records('scorm_attempt', ['scormid' => $scorm->id, 'userid' => $USER->id]);
$tempAttempt = 0;
$attemptScores = array();

foreach ($attempts as $attemptX) {
$tracks = $DB->get_record('scorm_scoes_value',['attemptid' => $attemptX->id, 'elementid'=> 8]);
$attemptScores[] = $tracks->value;
$tempAttempt = $tempAttempt + 1;
}

if ($tempAttempt >= 1) {
if (max($attemptScores) > 0) {
$average = $average + max($attemptScores);
$count = $count + 1;
}
if (max($attemptScores) >= 70) {
$countCompleted = $countCompleted + 1;
}
}
$overallcount = $overallcount + 1;
}

$categoryNames[$counting] = $category->name;
$courseNames[$counting] = $rowHTML6 . $course->fullname . $rowHTMLend;

$courseURL = $hascourseaccess
	? (new moodle_url('/course/view.php', ['id' => $course->id]))->out(false)
	: $membershipurl;
$courseAction = $hascourseaccess ? 'Study' : 'Subscribe to access';
$courseActionClass = $hascourseaccess ? '' : ' link-retry--locked';
$examLink = <<<HTML
<a href="$courseURL" class="link-retry link-retry2{$courseActionClass}">{$courseAction}</a>
HTML;

if ($count > 0) {
	$currentGrade = Round($average / $count, 2) . "%";
} else {
	$currentGrade = "Not Started";
}

if ($overallcount > 0) {
$courseAverage[$counting] = $rowHTML . $currentGrade . $rowHTMLend;
$courseCompleted[$counting] = $rowHTML . $countCompleted . $rowHTMLend;
$courseTotalScorms[$counting] = $rowHTML . $overallcount . $rowHTMLend;
$Lecture[$counting] = "";
$isLecture[$counting] = false;
} else {
$Lecture[$counting] = $rowHTML5 . "Lecture" . $rowHTMLend;
$courseAverage[$counting] = "";
$courseCompleted[$counting] = "";
$courseTotalScorms[$counting] = "";
$isLecture[$counting] = true;
}

$courseLinks[$counting] = $rowHTML4 . $examLink . $rowHTMLend4;

if ($isLecture[$counting] == false) {
if ($tempCategoryName !== $categoryNames[$counting]) {
$resultList = $resultList . $rowHTML3 . $categoryNames[$counting] . $rowHTML2 . $rowHTMLend3 . $courseNames[$counting] . $courseAverage[$counting] . $courseTotalScorms[$counting] . $courseCompleted[$counting] . $courseLinks[$counting] . $rowHTMLend2;
$tempCategoryName = $categoryNames[$counting];
} else {
$resultList = $resultList . $rowHTML2 . $courseNames[$counting] . $courseAverage[$counting] . $courseTotalScorms[$counting] . $courseCompleted[$counting] . $courseLinks[$counting] . $rowHTMLend2;
}
} else {
if ($tempCategoryName !== $categoryNames[$counting]) {
$resultList = $resultList . $rowHTML3 . $categoryNames[$counting] . $rowHTML2 . $rowHTMLend3 . $courseNames[$counting] . $Lecture[$counting] . $courseLinks[$counting] . $rowHTMLend2;
$tempCategoryName = $categoryNames[$counting];
} else {
$resultList = $resultList . $rowHTML2 . $courseNames[$counting] . $Lecture[$counting] . $courseLinks[$counting] . $rowHTMLend2;
}
}
$counting = $counting + 1;
}
}
} else { /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////|||||||||||||||||||||Cookie OFF
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
foreach ($courses as $course) {
if ($course->visible == false) {
	continue;
}
$context = context_course::instance($course->id);
$hascourseaccess = is_enrolled($context, $USER->id, '', true)
	|| has_capability('moodle/course:view', $context, $USER->id);
if (!$hascourseaccess) {
	$noshow = true;
}
$categoryNames[$counting2] = $category->name;
$courseNames[$counting2] = $rowHTML6 . $course->fullname . $rowHTMLend;
//$courseSummary[$counting2] = $rowHTML6. $course->summary . $rowHTMLend;

$courseURL = $hascourseaccess
	? (new moodle_url('/course/view.php', ['id' => $course->id]))->out(false)
	: $membershipurl;
$courseAction = $hascourseaccess ? 'Study' : 'Subscribe to access';
$courseActionClass = $hascourseaccess ? '' : ' link-retry--locked';
$examLink = <<<HTML
<a href="$courseURL" class="link-retry link-retry2{$courseActionClass}">{$courseAction}</a>
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
}
}

//array_multisort($categoryNames, $courseNames, $courseLinks, $courseAverage, $courseCompleted, $courseTotalScorms, $Lecture, $isLecture);

$htmlMainSectionAddToggle = <<<HTML
<div class="course-toggle">
<span class="switch-text">Toggle to show/hide the detailed view:</span>
<label class="btn-switch">
$checkBox
  <span class="slider2 round2"></span>
</label>
</div>
HTML;

$htmlMainSection = <<<HTML
<table class="course-stats course-stats3">
  <tr>
    <th colspan = "5" class="first-column">Study Guide | Detailed View</th>
  </tr>
    <td style="text-align:center;font-size:14px;" class="all-columns"><strong>Course Name</strong></td>
	<td style="text-align:center;font-size:14px;" class="all-columns"><strong>Current Grade</strong></td>
	<td style="text-align:center;font-size:14px;" class="all-columns"><strong>Total Activities</strong></td>
	<td style="text-align:center;font-size:14px;" class="all-columns"><strong>Passed Activities (&ge;70%)</strong></td>
	<td style="text-align:center;font-size:14px;" class="all-columns"><strong>Course Link</strong></td>
$resultList
</table>
HTML;

$htmlMainSection2 = <<<HTML
<table class="course-stats course-stats3">
  <tr>
    <th colspan = "2" class="first-column">Study Guide | Basic View</th>
  </tr>
    <td style="text-align:left;font-size:14px;" class="all-columns"><strong>Course Name</strong></td>
	<td style="text-align:center;font-size:14px;" class="all-columns"><strong>Course Link</strong></td>
$resultList2
</table>
HTML;

if ($details == true) {
$examLibraryContent = $htmlMainSectionAddToggle . $htmlMainSection;
} else {
$examLibraryContent = $htmlMainSectionAddToggle . $htmlMainSection2;
}

$boardactionurl = $hasboardmembership ? $boardcatalogurl : $membershipurl;
$boardactionlabel = $hasboardmembership ? 'Continue Board Preparation' : 'Start $30 membership';
$boardstatus = $hasboardmembership ? 'MEMBERSHIP ACTIVE' : 'MONTHLY MEMBERSHIP';

$studentHub = <<<HTML
<section class="student-learning-hub" aria-labelledby="student-learning-hub-title">
  <div class="student-learning-hub__intro">
    <p class="student-learning-hub__eyebrow">YOUR LEARNING DASHBOARD</p>
    <h2 id="student-learning-hub-title">Choose where you want to continue</h2>
    <p>Board Preparation and Continuing Education are two separate learning paths. Select the one that matches your goal today.</p>
  </div>

  <div class="student-learning-paths">
    <article class="student-learning-path student-learning-path--board">
      <span class="student-learning-path__badge">{$boardstatus}</span>
      <h3>Board Preparation</h3>
      <p>Study the available guides and practice with Board-level activities included in the monthly membership.</p>
      <p class="student-learning-path__price"><strong>$30</strong> per month</p>
      <a class="student-learning-path__button" href="{$boardactionurl}">{$boardactionlabel}</a>
    </article>

    <article class="student-learning-path student-learning-path--ce">
      <span class="student-learning-path__badge">SEPARATE ENROLLMENT</span>
      <h3>Continuing Education</h3>
      <p>Choose an approved CE course. The final price depends on the number of formally approved credits.</p>
      <p class="student-learning-path__price"><strong>$10</strong> per approved credit</p>
      <a class="student-learning-path__button" href="{$cecatalogurl}">View Continuing Education</a>
    </article>
  </div>

  <div class="student-ready-guides">
    <div class="student-ready-guides__heading">
      <div>
        <p class="student-learning-hub__eyebrow">READY TO STUDY</p>
        <h3>Available Study Guides</h3>
      </div>
      <a href="{$boardcatalogurl}">View all Board guides</a>
    </div>
    <ul class="student-guide-list">
      <li class="student-guide-list__item">
        <div class="student-guide-list__content">
          <span class="student-guide-list__status">STUDY GUIDE READY</span>
          <h4>The Channels and Collaterals</h4>
          <p>Channel functions, pathways, relationships and organization.</p>
        </div>
        <div class="student-guide-list__meta">9 learning objectives</div>
        <a href="{$channelsurl}">Open Study Guide</a>
      </li>
      <li class="student-guide-list__item">
        <div class="student-guide-list__content">
          <span class="student-guide-list__status">STUDY GUIDE READY</span>
          <h4>Point Categories</h4>
          <p>Point-category definitions, clinical relationships and key distinctions.</p>
        </div>
        <div class="student-guide-list__meta">26 learning objectives</div>
        <a href="{$pointcategoriesurl}">Open Study Guide</a>
      </li>
    </ul>
  </div>
</section>
HTML;

$examLibrary = <<<HTML
<details class="student-exam-library">
  <summary>
    <span>
      <strong>Exam Library</strong>
      <small>Open the complete exam, simulation and notes list</small>
    </span>
    <span class="student-exam-library__action">Open library</span>
  </summary>
  <div class="student-exam-library__content">
    {$examLibraryContent}
  </div>
</details>
HTML;

$allcontent = $studentHub;

if (!$hasboardmembership) {
$purchase = <<<HTML
<section class="membership-access-notice" aria-labelledby="membership-access-title-menu">
  <div>
    <strong id="membership-access-title-menu">Your account is ready.</strong>
    <span>Activate the $30 monthly Board Preparation membership to open the courses below.</span>
  </div>
  <a href="{$membershipurl}" class="link-retry link-retry2 link-retry3">Start membership</a>
</section>
HTML;
		$allcontent .= $purchase;
}

$allcontent .= $examLibrary;
ass:
$this->content->text = $allcontent;
if ($this->content !== NULL) {
     return $this->content;
}
}
}
