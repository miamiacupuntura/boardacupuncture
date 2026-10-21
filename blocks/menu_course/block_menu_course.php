<?php 

class block_menu_course extends block_base { 
 
function init() {
$this->title = get_string('pluginname', 'block_menu_course');
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
foreach ($courses as $course) {
if ($course->visible == false) {
	continue;
}
if ($noshow == false) {
$context = context_course::instance($course->id);
if (is_enrolled($context, $USER->id, '', true) == false) {
	$noshow = true;
}
}
$categoryNames[$counting2] = $category->name;
$courseNames[$counting2] = $rowHTML6 . $course->fullname . $rowHTMLend;
//$courseSummary[$counting2] = $rowHTML6. $course->summary . $rowHTMLend;

$courseURL = "https://" . $_SERVER['HTTP_HOST'] . "/course/view.php?id=" . $course->id;
$examLink = <<<HTML
<a href="$courseURL" class="link-retry link-retry2">Study</a>
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
//array_multisort($categoryNames, $courseNames, $courseLinks, $courseAverage, $courseCompleted, $courseTotalScorms, $Lecture, $isLecture);

$htmlMainSection = <<<HTML
<table class="course-stats course-stats3">
  <tr>
    <th colspan = "2" class="first-column">Study Guide | Basic View</th>
  </tr>
    <td style="text-align:left;font-size:14px;" class="all-columns"><strong>Course Name</strong></td>
	<td style="text-align:center;font-size:14px;" class="all-columns"><strong>Course Link</strong></td>
$resultList2
</table>
HTML;


$allcontent = $htmlMainSection;

$this->content->text = $allcontent;
if ($this->content !== NULL) {
     return $this->content;
}
}
}
