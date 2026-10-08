<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Contains the default section header format output class.
 *
 * @package   core_courseformat
 * @copyright 2020 Ferran Recio <ferran@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_courseformat\output\local\content\section;

use core\output\named_templatable;
use core_courseformat\base as course_format;
use core_courseformat\output\local\courseformat_named_templatable;
use renderable;
use section_info;
use stdClass;
$_SESSION["mainsection"] = 0;
$_SESSION["last_page"] = $_SERVER['HTTP_REFERER'];
/**
 * Base class to render a section header.
 *
 * @package   core_courseformat
 * @copyright 2020 Ferran Recio <ferran@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class header implements named_templatable, renderable {

    use courseformat_named_templatable;

    /** @var course_format the course format class */
    protected $format;

    /** @var section_info the course section class */
    protected $section;
	
    /**
     * Constructor.
     *
     * @param course_format $format the course format
     * @param section_info $section the section info
     */
    public function __construct(course_format $format, section_info $section) {
        $this->format = $format;
        $this->section = $section;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output typically, the renderer that's calling this function
     * @return array data context for a mustache template
     */
    public function export_for_template(\renderer_base $output): stdClass {
global $USER, $SESSION, $COURSE, $OUTPUT, $CFG, $PAGE, $DB;	
require_once($CFG->dirroot.'/mod/scorm/locallib.php');
require_once($CFG->libdir.'/gradelib.php');
require_once($CFG->dirroot.'/grade/querylib.php');

        $format = $this->format;
        $section = $this->section;
        $course = $format->get_course();
		$maxsection = $format->get_last_section_number();
		$currentsection = $format->get_section_number();

error_log("ACUP_DEBUG: section=" . $section->section .
    " currentsection=" . $currentsection .
    " mainsection=" . ($_SESSION["mainsection"] ?? "NULL") .
    " outside_before=" . ($outside ? "true" : "false"));
		$courseurl = $format->get_view_url(null);
		$modinfo = $format->get_modinfo();

		$finalaverage = "";
		$countCompleted = 0;
		$overallgrade = 0;
		$overallcount = 0;
		$failedExams = false;
		$currentGrade = -1;
		$inside = false;
		$outside = false;

if (!empty($_SESSION["last_page"])) {
$lastpage = $_SESSION["last_page"];
} else {
$lastpage = "hola";	
}

if(!isset($_COOKIE["btnToggle"])) {
$checkBox = <<<HTML
<input class="course-input" onclick="Toggle()" type="checkbox">
HTML;
} else {
if ($_COOKIE["btnToggle"] == 1) {
$checkBox = <<<HTML
<input class="course-input" onclick="Toggle()" type="checkbox" checked>
HTML;
} else {
$checkBox = <<<HTML
<input class="course-input" onclick="Toggle()" type="checkbox">
HTML;
}
}

///HTML BEGIN
$htmlContent1AB = <<<HTML
<em><span style="color: #ff0000">
HTML;
$htmlContent2AB = <<<HTML
<em><span style="color: #008000">
HTML;
$htmlContent3AB = <<<HTML
<em>
HTML;
$htmlContentemAB = <<<HTML
<em>
HTML;
$htmlContentENDAB = <<<HTML
</span></em>
HTML;
$htmlContentEND3AB = <<<HTML
</em>
HTML;
$htmlContentENDemAB = <<<HTML
</em>
HTML;

$rowHTMLAB = <<<HTML
<td class="all-columns" style="font-size:12px;text-align:center;background-color:#ffffff !important;">
HTML;
$rowHTMLendAB = <<<HTML
</td>
HTML;
$rowHTML2AB = <<<HTML
<tr>
HTML;
$rowHTMLend2AB = <<<HTML
</tr>
HTML;
$rowHTML3AB = <<<HTML
<td class="all-columns" style="font-size:12px;text-align:left;">
HTML;
$rowHTMLLinkAB = <<<HTML
<td class="all-columns2" style="font-size:10px;text-align:center;word-break:break-word;overflow-wrap:anywhere;">
HTML;

$rowHTML = <<<HTML
<td class="all-columns" style="font-size:12px;text-align:center;">
HTML;
$rowHTMLend = <<<HTML
</td>
HTML;
$rowHTML2 = <<<HTML
<tr>
HTML;
$rowHTMLB = <<<HTML
<td class="all-columns" style="font-size:12px;">
HTML;
$rowHTMLend2 = <<<HTML
</tr>
HTML;
$linkEND = <<<HTML
</a>
HTML;
$rowHTMLLink = <<<HTML
<td class="all-columns2" style="font-size:10px;text-align:center;word-break:break-word;overflow-wrap:anywhere;">
HTML;
$rowHTML3 = <<<HTML
<tr><td colspan="7" class="special-column" style="text-align:left;background-color:#F47E2C;font-size:13px;font-weight:bold;color:#ffffff;padding:3px;">
HTML;
$rowHTMLend3 = <<<HTML
</td></tr>
HTML;

$htmlContent3 = <<<HTML
<a class="link-retry mainbutton mainbutton2" href="$courseurl">
HTML;
$htmlContent6 = <<<HTML
<a class="link-retry mainbutton mainbutton2" href="$lastpage">Last Page</a>
HTML;
$htmlContentEND = <<<HTML
</a>
HTML;
$htmlContent4 = <<<HTML
<span class="section-average"><span>| Topic Score:</span>
HTML;
$htmlContent5 = <<<HTML
<span class="course-grade">
HTML;
$htmlContentEND2 = <<<HTML
</span>
HTML;
$htmlTableEnd = <<<HTML
<hr />
HTML;

$htmlNavMainPage = <<<HTML
<div class="main-page-border">
HTML;
$htmlNavMainPageEND = <<<HTML
</div>
HTML;
$htmlLastPage = <<<HTML
<div class="last-page-btn">
<a class="link-retry mainbutton2 mainbutton3" href="$lastpage">Last Page</a>
<span class="main-divider">|</span>
</div>
HTML;
$htmlMainSectionAddToggle = <<<HTML
<div class="course-toggle">
<span class="switch-text">Toggle to show/hide the exams list:</span>
<label class="btn-switch">
$checkBox
  <span class="slider2 round2"></span>
</label>
</div>
HTML;
///HTML END

if ($section->section > 0) {
if ($currentsection == $section->section) {
$inside = true;
} else {
$inside = false;	
}
} else {
$inside = false;
}

if ($currentsection == 0) {
if ($_SESSION["mainsection"] == 0) {
$outside = true;
} else {
$outside = false;
}
} else {
$outside = false;
}

error_log("ACUP_DEBUG_AFTER: section=" . $section->section .
    " currentsection=" . $currentsection .
    " mainsection=" . ($_SESSION["mainsection"] ?? "NULL") .
    " outside_after=" . ($outside ? "true" : "false"));

///EXAM LIST INSIDE TOPICS BEGIN
if ($inside == true) {  
$Passed = array();
$maxScore = array();
$maxAttempt = array();
$attemptsStarted = array();
$attemptsCompleted = array();
$lastAttemptDate = array();
$lastAttemptScore = array();
$lastAttemptStatus = array();
$scormName = array();
$examLink = array();

$scorms = $DB->get_records('scorm', ['course' => $COURSE->id]);
$currentSection = $DB->get_record('course_sections', ['course' => $COURSE->id, 'section' => $format->get_section_number()]);
$allSections = $DB->get_records('course_sections', ['course' => $COURSE->id]);

foreach ($allSections as $navSection) {
if ($navSection->section == ($format->get_section_number()+ 1)) {
$nextSection = $navSection->id;
}	
if ($navSection->section == ($format->get_section_number()- 1)) {
$prevSection = $navSection->id;
}
}

foreach ($scorms as $scorm) { //begin bucle
$tempName = "";
$tempPassed = "";
$tempAttempt = 0;
$tempScore = "";
$attemptScores = array();

$scormurl = $DB->get_record('course_modules', ['instance' => $scorm->id]);
$courseSections = $DB->get_records('course_sections', ['course' => $COURSE->id, 'id' => $scormurl->section]);
$sectionName = $courseSections[$scormurl->section]->name;
$scormurlValue = "/mod/scorm/studyguide/?id=" . $scormurl->instance . "&cmid=" . $scormurl->id;

if (($currentSection->name) == $sectionName) {
$attempts = $DB->get_records('scorm_attempt', ['scormid' => $scorm->id, 'userid' => $USER->id]);

$tempAttempt = count($attempts);
$tempCompleted = 0;
$latestAttemptTime = 0;
$latestAttemptScore = null;
$latestAttemptStatus = 'incomplete';

foreach ($attempts as $attemptX) {
    $tracks = $DB->get_record('scorm_scoes_value', [
        'attemptid' => $attemptX->id,
        'elementid' => 3
    ]);

    $statusTrack = $DB->get_record('scorm_scoes_value', [
        'attemptid' => $attemptX->id,
        'elementid' => 2
    ]);

    $lastActivityTrack = $DB->get_record_sql(
        "SELECT * FROM {scorm_scoes_value}
          WHERE attemptid = ?
          ORDER BY timemodified DESC, id DESC",
        [$attemptX->id]
    );

    if ($tracks && $tracks->value !== '') {
        $attemptScores[] = $tracks->value;
    }

    $status = $statusTrack ? strtolower(trim($statusTrack->value)) : 'incomplete';

    if ($status === 'passed' || $status === 'failed') {
        $tempCompleted++;
    }

    $attemptTime = 0;

    if ($lastActivityTrack && !empty($lastActivityTrack->timemodified)) {
        $attemptTime = (int)$lastActivityTrack->timemodified;
    }

    if ($attemptTime >= $latestAttemptTime) {
        $latestAttemptTime = $attemptTime;
        $latestAttemptScore = ($tracks && $tracks->value !== '') ? $tracks->value : null;
        $latestAttemptStatus = $status;
    }
}

$attemptsStarted[] = $mainRowHTML . $tempAttempt . $rowHTMLend;
$attemptsCompleted[] = $mainRowHTML . $tempCompleted . $rowHTMLend;
        $attemptsCombined[] = $mainRowHTML . $tempAttempt . " / " . $tempCompleted . $rowHTMLend;
$lastAttemptDate[] = $mainRowHTML . ($latestAttemptTime ? userdate($latestAttemptTime, get_string('strftimedatetimeshort', 'langconfig')) : '-') . $rowHTMLend;
$lastAttemptScore[] = $mainRowHTML . (($latestAttemptScore !== null && $latestAttemptScore !== '') ? $latestAttemptScore . '%' : '-') . $rowHTMLend;

if ($latestAttemptStatus === 'passed') {
    $lastAttemptStatus[] = '<td class="all-columns" style="font-size:12px;text-align:center;white-space:nowrap !important;word-break:normal !important;overflow-wrap:normal !important;width:110px !important;min-width:110px !important;background-color:#ffffff !important;">PASS</td>';
} else if ($latestAttemptStatus === 'failed') {
    $lastAttemptStatus[] = '<td class="all-columns" style="font-size:12px;text-align:center;white-space:nowrap !important;word-break:normal !important;overflow-wrap:normal !important;width:110px !important;min-width:110px !important;background-color:#ffffff !important;">FAIL</td>';
} else {
    $lastAttemptStatus[] = '<td class="all-columns" style="font-size:12px;text-align:center;white-space:nowrap !important;word-break:normal !important;overflow-wrap:normal !important;width:110px !important;min-width:110px !important;background-color:#ffffff !important;">Pending</td>';
}

$tempName = trim($scorm->name, " ");
$btnValue = "Retry";

if ($tempAttempt >= 1) {
if (!empty($attemptScores) && max($attemptScores) >= 70) {
$tempPassed = $htmlContent2AB . "Passed!" . $htmlContentENDAB;
$countCompleted = $countCompleted + 1;	
} else {
$tempPassed = $htmlContent1AB . "Failed!" . $htmlContentENDAB;
}
if (!empty($attemptScores) && max($attemptScores) !== null) {
$tempScore = max($attemptScores) . "%";
$overallgrade = $overallgrade + max($attemptScores);
$overallcount = $overallcount + 1;
} else {
$tempScore = "0%";	
}
}

if ($tempAttempt == 0) {
$tempScore = "-";
$tempPassed = "-";
$btnValue = "Study Guide";
$tempAttempt = 0;
}

$tempLink = <<<HTML
<a href="$scormurlValue" class="link-retry">$btnValue</a>
HTML;

$examLink[] = $tempLink;
$Passed[] = $tempPassed;
$scormName[] = $tempName;
$maxAttempt[] = $tempAttempt;
$maxScore[] = $tempScore;
}
}

array_multisort($scormName, $maxAttempt, $maxScore, $Passed, $examLink);
for ($x = 0; $x <= count($scormName)-1; $x++) {
$resultExamsAB =  $resultExamsAB . $rowHTML2AB . $rowHTMLAB . ($x+1) . $rowHTMLendAB . $rowHTML3AB . $scormName[$x] . $rowHTMLendAB . $rowHTMLAB . $maxAttempt[$x] . $rowHTMLendAB . $rowHTMLAB . $maxScore[$x] . $rowHTMLendAB . $rowHTMLAB . $Passed[$x] . $rowHTMLendAB . $rowHTMLLinkAB . $examLink[$x] . $rowHTMLendAB . $rowHTMLend2AB;
}
///COURSE AVERAGE BEGIN
if ($overallcount > 0) {
if (Round(($overallgrade / $overallcount), 2) < 70) {
$htmlContentGrade = <<<HTML
<span class="grade-failed">
HTML;
$htmlContentGradeEND = <<<HTML
</span>
HTML;
$finalaverage = $htmlContentGrade . (Round(($overallgrade / $overallcount), 2)) . "%" . $htmlContentGradeEND;	
} else {
$htmlContentGrade = <<<HTML
<span class="grade-pass">
HTML;
$htmlContentGradeEND = <<<HTML
</span>
HTML;
$finalaverage = $htmlContentGrade . (Round(($overallgrade / $overallcount), 2)) . "%" . $htmlContentGradeEND;	
}
}
///COURSE AVERAGE END

///NAVIGATION INSIDE TOPIC BEGIN
if ($overallcount > 0) {
$currentGrade = Round(($overallgrade / $overallcount),2);
} else {
$currentGrade = 0;	
}
if (($currentsection+1) < $maxsection) {
$nexturl = "/course/section.php?id=" . $nextSection;
$htmlContent2 = <<<HTML
<a class="link-retry mainbutton mainbutton2 nexturl" href="$nexturl">
HTML;
} else {
$htmlContent2 = <<<HTML
<a class="link-retry mainbutton mainbutton2 nexturl" href="$nexturl" style="opacity: 0.8;pointer-events: none;cursor: default;background-color:#c0c0c0;">
HTML;	
}
if (($currentsection-1) >= 1) {
$prevurl = "/course/section.php?id=" . $prevSection;
$htmlContent1 = <<<HTML
<span>|</span><a class="link-retry mainbutton mainbutton2 prevurl" href="$prevurl">
HTML;
} else {
$htmlContent1 = <<<HTML
<span>|</span><a class="link-retry mainbutton mainbutton2 prevurl" href="$prevurl" style="opacity: 0.8;pointer-events: none;cursor: default;background-color:#c0c0c0;">
HTML;	
}
if ($currentGrade >= 70) {
$htmlContentCourseGrade = <<<HTML
<span>Course Grade: </span><span class="course-grade-pass">
HTML;
$htmlContentCourseGradeEND = <<<HTML
</span>
HTML;
$coursegrade = $htmlContentCourseGrade . $currentGrade . "%" . $htmlContentCourseGradeEND;
} else {
$htmlContentCourseGrade = <<<HTML
<span>Course Grade: </span><span class="course-grade-failed">
HTML;
$htmlContentCourseGradeEND = <<<HTML
</span>
HTML;
$coursegrade = $htmlContentCourseGrade . $currentGrade . "%" . $htmlContentCourseGradeEND;	
}
$navigationTopic = $htmlContent3 . "Main Page" . $htmlContentEND . $htmlContent6 . $htmlContent1 . "<< Prev" . $htmlContentEND . $htmlContent2 . "Next >>" . $htmlContentEND . $htmlContent5 . $coursegrade . $htmlContentEND2 . $htmlContent4 . $finalaverage . $htmlContentEND2;
///NAVIGATION INSIDE TOPIC END

$htmlMainSectionExams = <<<HTML
<table class="course-stats course-stats2 exams-list-main show-table" style="width:100%;max-width:100%;table-layout:fixed;box-sizing:border-box;">
  <tr>
    <th colspan = "6" class="first-column">Exams List</th>
  </tr>
  <tr>
    <td style="text-align:center;font-size:14px;" class="all-columns"><strong>#</strong></td>
	<td style="text-align:left;font-size:14px;" class="all-columns"><strong>Exam Name</strong></td>
	<td style="text-align:center;font-size:14px;" class="all-columns"><strong>Attempts</strong></td>
	<td style="text-align:center;font-size:14px;" class="all-columns"><strong>Maximum Score</strong></td>
	<td style="text-align:center;font-size:14px;" class="all-columns"><strong>Exam Status</strong></td>
	<td style="text-align:center;font-size:14px;" class="all-columns"><strong>Exam History</strong></td>
  </tr>
$resultExamsAB
</table>
HTML;
}///EXAM LIST INSIDE TOPICS END
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if ($outside == true) { ///COURSE MAIN PAGE INFORMATION BEGIN
$Passed = array();
$maxScore = array();
$maxAttempt = array();
$scormName = array();
$examLinks = array();
$resultExamsAB = array();
$attemptsStarted = array();
$attemptsCompleted = array();
$lastAttemptDate = array();
$lastAttemptScore = array();
$lastAttemptStatus = array();
$sectionNames = array();
$sectionIds = array();
$mainRowHTML = <<<HTML
<td class="all-columns" style="font-size:12px;text-align:center;white-space:nowrap !important;word-break:normal !important;overflow-wrap:normal !important;background-color:#ffffff !important;border-bottom:1px solid #D9D9D9 !important;vertical-align:top !important;">
HTML;
$mainRowHTMLB = <<<HTML
<td class="all-columns" style="font-size:12px;text-align:left !important;padding-left:0 !important;background-color:#ffffff !important;border-bottom:1px solid #D9D9D9 !important;white-space:normal !important;word-break:normal !important;overflow-wrap:break-word !important;vertical-align:top !important;">
HTML;
$mainRowHTMLLink = <<<HTML
<td class="all-columns2" style="font-size:10px;text-align:center;word-break:break-word;overflow-wrap:anywhere;background-color:#ffffff !important;border-bottom:1px solid #D9D9D9 !important;">
HTML;
$mainRowHTML2 = <<<HTML
<tr style="background-color:#ffffff !important;border-bottom:1px solid #d9d9d9 !important;">
HTML;

$sectionName = "";
$scormcount = 0;

$scorms = $DB->get_records('scorm', ['course' => $COURSE->id]);
$allSections = $DB->get_records('course_sections', ['course' => $COURSE->id]);

foreach ($scorms as $scorm) { //begin bucle
$tempName = "";
$tempPassed = "";
$tempAttempt = 0;
$tempScore = "";
$attemptScores = array();
$scormcount = $scormcount + 1;	

$scormurl = $DB->get_record('course_modules', ['instance' => $scorm->id]);
$sectionName = $allSections[$scormurl->section]->name;
$sectionId = $scormurl->section;
$sectionIds[] = $sectionId;
$scormurlValue = "/mod/scorm/studyguide/?id=" . $scormurl->instance . "&cmid=" . $scormurl->id;

$attempts = $DB->get_records(
    'scorm_attempt',
    ['scormid' => $scorm->id, 'userid' => $USER->id],
    'attempt ASC'
);

$tempAttempt = count($attempts);
$tempCompleted = 0;
$latestAttemptTime = 0;
$latestAttemptScore = null;
$latestAttemptStatus = null;

foreach ($attempts as $attemptX) {
    $tracks = $DB->get_records(
        'scorm_scoes_value',
        ['attemptid' => $attemptX->id],
        'timemodified DESC'
    );

    $attemptStatus = null;
    $attemptScore = null;
    $attemptLastTime = 0;

    foreach ($tracks as $track) {
        if ($track->timemodified > $attemptLastTime) {
            $attemptLastTime = $track->timemodified;
        }

        if ((int)$track->elementid === 2) {
            $attemptStatus = strtolower(trim($track->value));
        }

        if ((int)$track->elementid === 8) {
            $attemptScore = trim($track->value);
        }
    }

    if ($attemptStatus === 'passed' || $attemptStatus === 'failed') {
        $tempCompleted++;
    }

    if ($attemptLastTime >= $latestAttemptTime) {
        $latestAttemptTime = $attemptLastTime;
        $latestAttemptScore = $attemptScore;
        $latestAttemptStatus = $attemptStatus;
    }
}

$attemptsStarted[] = $mainRowHTML . $tempAttempt . $rowHTMLend;
$attemptsCompleted[] = $mainRowHTML . $tempCompleted . $rowHTMLend;
        $attemptsCombined[] = $mainRowHTML . $tempAttempt . " / " . $tempCompleted . $rowHTMLend;
$lastAttemptDate[] = $mainRowHTML .
    ($latestAttemptTime
        ? userdate($latestAttemptTime, '%m/%d/%y') . '<br>' . userdate($latestAttemptTime, '%l:%M %p')
        : '-') .
    $rowHTMLend;
$lastAttemptScore[] = $mainRowHTML .
    (($latestAttemptScore !== null && $latestAttemptScore !== '') ? $latestAttemptScore . '%' : '-') .
    $rowHTMLend;

if ($latestAttemptStatus === 'passed') {
    $lastAttemptStatus[] = $mainRowHTML . '<span class="last-status-pill last-status-passed">PASS</span>' . $rowHTMLend;
} else if ($latestAttemptStatus === 'failed') {
    $lastAttemptStatus[] = $mainRowHTML . '<span class="last-status-pill last-status-failed">FAIL</span>' . $rowHTMLend;
} else {
    $lastAttemptStatus[] = $mainRowHTML . '<span class="last-status-pill last-status-incomplete">Pending</span>' . $rowHTMLend;
}

$tempName = trim($scorm->name, " ");
$btnValue = "Retry";

if ($tempAttempt >= 1) {
if (!empty($attemptScores) && max($attemptScores) >= 70) {
$tempPassed = $htmlContent2AB . "Passed!" . $htmlContentENDAB;
$countCompleted = $countCompleted + 1;	
} else {
$tempPassed = $htmlContent1AB . "Failed!" . $htmlContentENDAB;
}
if (!empty($attemptScores) && max($attemptScores) !== null) {
$tempScore = max($attemptScores) . "%";
$overallgrade = $overallgrade + max($attemptScores);
$overallcount = $overallcount + 1;
} else {
$tempScore = "0%";	
}
}

if ($tempAttempt == 0) {
$tempScore = "-";
$tempPassed = "-";
$btnValue = "Study Guide";
$tempAttempt = 0;
}

$examLink = <<<HTML
<a href="$scormurlValue" class="link-retry">$btnValue
HTML;

error_log("ACUP_NAME_DEBUG: " . $tempName);

$examLinks[] = $mainRowHTMLLink . $examLink . $linkEND . $rowHTMLend;
$Passed[] = $mainRowHTML . $tempPassed . $rowHTMLend;
$scormName[] = $mainRowHTMLB . $tempName . $rowHTMLend;
$maxAttempt[] = $mainRowHTML . $tempAttempt . $rowHTMLend;
$maxScore[] = $mainRowHTML . $tempScore . $rowHTMLend;;
$sectionNames[] = $sectionName;
}

///COURSE AVERAGE BEGIN
if ($overallcount > 0) {
if (Round(($overallgrade / $overallcount), 2) < 70) {
$htmlContentGrade = <<<HTML
<span class="grade-failed">
HTML;
$htmlContentGradeEND = <<<HTML
</span>
HTML;
$courseaverage = $htmlContentGrade . (Round(($overallgrade / $overallcount), 2)) . "%" . $htmlContentGradeEND;	
} else {
$htmlContentGrade = <<<HTML
<span class="grade-pass">
HTML;
$htmlContentGradeEND = <<<HTML
</span>
HTML;
$courseaverage = $htmlContentGrade . (Round(($overallgrade / $overallcount), 2)) . "%" . $htmlContentGradeEND;	
}
} ///COURSE AVERAGE END

$fullname = $COURSE->fullname;
$remaining = $scormcount - $countCompleted;
if ($countCompleted == 1) {
$summarynote = "$fullname has a total of $scormcount exams that you must pass with an overall grade of at least 70%. Currently, you have passed $countCompleted exams and your course grade is $courseaverage so far. There is no limit to the number of attempts you can make per exam. We wish you luck with the remaining $remaining exams.";
} else {
$summarynote = "$fullname has a total of $scormcount exams that you must pass with an overall grade of at least 70%. Currently, you have passed $countCompleted exams and your course grade is $courseaverage so far. There is no limit to the number of attempts you can make per exam. We wish you luck with the remaining $remaining exams.";	
}

$htmlMainSection = <<<HTML
<table class="course-stats course-stats-main" style="width:100% !important;max-width:100% !important;min-width:0 !important;box-sizing:border-box !important;">
  <tr>
    <th colspan = "5" class="first-column">Course Statistics</th>
  </tr>
  <tr>
    <td class="all-columns"><strong>Current Grade:</strong> <em>$courseaverage</em></td>
    <td class="all-columns"><strong>Passing Grade:</strong> <em>70%</em></td>
	<td class="all-columns"><strong>Passed Exams (&ge;70%):</strong> <em>$countCompleted</em></td>
    <td class="all-columns"><strong>Total Exams:</strong> <em>$scormcount</em></td>
    <td class="all-columns"><strong>Total Topics:</strong> <em>$maxsection</em></td>
  </tr>
  <tr>
    <td colspan = "5" class="last-column">$summarynote</td>
  </tr>
</table>
HTML;


$coursestats = $htmlMainSection;

error_log("ACUP_BEFORE_SORT: sectionNames=" . count($sectionNames) .
    " scormName=" . count($scormName) .
    " attemptsStarted=" . count($attemptsStarted) .
    " attemptsCompleted=" . count($attemptsCompleted) .
    " lastAttemptDate=" . count($lastAttemptDate) .
    " lastAttemptScore=" . count($lastAttemptScore) .
    " lastAttemptStatus=" . count($lastAttemptStatus));

$tempSectionName = "";
array_multisort($scormName, $sectionNames, $sectionIds, $maxScore, $maxAttempt, $examLinks, $attemptsStarted, $attemptsCompleted, $attemptsCombined, $lastAttemptDate, $lastAttemptScore, $lastAttemptStatus);
for ($z = 0; $z <= $maxsection; $z++) {	
for ($x = 0; $x <= count($sectionNames)-1; $x++) {
if ($format->get_section_name($z) == $sectionNames[$x]) {
if ($tempSectionName !== $sectionNames[$x]) {
/* Todos los Topics usan el mismo color naranja */
$topicHeaderBg = '#F4A261';
$topicHeaderText = '#ffffff';

$topicExamHeaders = <<<HTML
<tr class="exam-topic-header" data-topic="{$sectionNames[$x]}" style="background-color:{$topicHeaderBg} !important;">
<td style="text-align:left;font-size:10px;background-color:{$topicHeaderBg} !important;color:{$topicHeaderText} !important;padding:8px 8px !important;border-bottom:1px solid #D9D9D9 !important;border-right:1px solid #ffffff !important;" class="all-columns" style="border-right:1px solid #ffffff !important;"><strong>{$sectionNames[$x]}</strong></td>
<td style="text-align:center;font-size:10px;background-color:{$topicHeaderBg} !important;color:{$topicHeaderText} !important;padding:8px 6px !important;border-bottom:1px solid #D9D9D9 !important;border-right:1px solid #ffffff !important;" class="all-columns" style="border-right:1px solid #ffffff !important;"><strong>Attempts<br><span style="white-space:nowrap;font-size:9px;">Started /</span><br><span style="white-space:nowrap;font-size:9px;">Completed</span></strong></td>
<!-- REMOVED SECOND ATTEMPTS HEADER -->
<td style="text-align:center;font-size:10px;background-color:{$topicHeaderBg} !important;color:{$topicHeaderText} !important;padding:8px 6px !important;border-bottom:1px solid #D9D9D9 !important;border-right:1px solid #ffffff !important;" class="all-columns" style="border-right:1px solid #ffffff !important;"><strong>Last Attempt</strong></td>
<td style="text-align:center;font-size:10px;background-color:{$topicHeaderBg} !important;color:{$topicHeaderText} !important;padding:8px 6px !important;border-bottom:1px solid #D9D9D9 !important;border-right:1px solid #ffffff !important;" class="all-columns" style="border-right:1px solid #ffffff !important;"><strong><span style="white-space:normal;">Last Score</span></strong></td>
<td style="text-align:center;font-size:10px;white-space:normal;background-color:{$topicHeaderBg} !important;color:{$topicHeaderText} !important;padding:8px 6px !important;border-bottom:1px solid #D9D9D9 !important;border-right:1px solid #ffffff !important;" class="all-columns" style="border-right:1px solid #ffffff !important;"><strong>Last Status</strong></td>
<td style="text-align:center;font-size:10px;background-color:{$topicHeaderBg} !important;color:{$topicHeaderText} !important;padding:6px 4px !important;border-bottom:1px solid #D9D9D9 !important;border-right:1px solid #ffffff !important;" class="all-columns">
<a href="/mod/scorm/examhistory/?courseid={$COURSE->id}&sectionid={$sectionIds[$x]}" style="display:inline-block;background-color:#ffffff !important;color:#333333 !important;border:1px solid #d0d0d0 !important;border-radius:4px !important;padding:4px 7px !important;font-size:9px !important;font-weight:bold !important;text-decoration:none !important;white-space:nowrap !important;">Exam History</a>
</td>
</tr>
HTML;

$resultExams = $resultExams . $topicExamHeaders . str_replace('<tr style="background-color:#ffffff !important;border-bottom:1px solid #d9d9d9 !important;">', '<tr class="exam-topic-row" data-topic="' . s($sectionNames[$x]) . '" style="background-color:#ffffff !important;border-bottom:1px solid #d9d9d9 !important;">', $mainRowHTML2) . $scormName[$x] . $attemptsCombined[$x] . $lastAttemptDate[$x] . $lastAttemptScore[$x] . $lastAttemptStatus[$x] . $examLinks[$x] . $rowHTMLend2;	
$tempSectionName = $sectionNames[$x];
} else {
$resultExams = $resultExams . str_replace('<tr style="background-color:#ffffff !important;border-bottom:1px solid #d9d9d9 !important;">', '<tr class="exam-topic-row" data-topic="' . s($sectionNames[$x]) . '" style="background-color:#ffffff !important;border-bottom:1px solid #d9d9d9 !important;">', $mainRowHTML2) . $scormName[$x] . $attemptsCombined[$x] . $lastAttemptDate[$x] . $lastAttemptScore[$x] . $lastAttemptStatus[$x] . $examLinks[$x] . $rowHTMLend2;
}
}
}
}

/* Exams List: Topics desplegables */
$examTopicDropdownJS = <<<HTML
<style>
.main-exams-list .exam-topic-header {
    border-top: 2px solid #000000 !important;
    border-bottom: 2px solid #000000 !important;
}

.main-exams-list .exam-topic-header td {
    border-top: 2px solid #000000 !important;
    border-bottom: 2px solid #000000 !important;
}

.main-exams-list .exam-topic-header td:first-child {
    text-align: left !important;
}

.main-exams-list .exam-topic-header {
    cursor: pointer;
}

.main-exams-list .exam-topic-header td:first-child strong::before {
    content: "▶ ";
    display: inline-block;
    font-size: 9px;
    transition: transform 0.2s ease;
}

.main-exams-list .exam-topic-header.exam-topic-open td:first-child strong::before {
    transform: rotate(90deg);
}

.main-exams-list .exam-topic-row {
    display: none;
}

.main-exams-list .exam-topic-row.exam-topic-visible {
    display: table-row;
}

@media (max-width: 767px) {
    .main-exams-list {
        table-layout: fixed !important;
        width: 100% !important;
        max-width: 100% !important;
        min-width: 0 !important;
    }

    .main-exams-list .exam-topic-header td,
    .main-exams-list .exam-topic-row td {
        font-size: 9px !important;
        padding: 6px 3px !important;
    }

/* Reduce only Pending in Last Status */
.main-exams-list .exam-topic-row td:nth-child(5) {
    font-size: 8px !important;
}

    .main-exams-list.course-stats .exam-topic-row td {
        font-size: 9px !important;
    }

    /* Mobile: column widths are controlled only by the table colgroup */
    .main-exams-list .exam-topic-header td,
    .main-exams-list .exam-topic-row td {
        width: auto !important;
        min-width: 0 !important;
    }
}

/* Last Status pills */
.main-exams-list .last-status-pill {
    display: inline-block !important;
    padding: 4px 8px !important;
    border-radius: 999px !important;
    font-size: 8px !important;
    font-weight: 700 !important;
    line-height: 1.2 !important;
    white-space: nowrap !important;
    text-align: center !important;
    min-width: 52px !important;
    box-sizing: border-box !important;
}

.main-exams-list .last-status-passed {
    background: #d9f2df !important;
    color: #237a3b !important;
}

.main-exams-list .last-status-failed {
    background: #f8d7da !important;
    color: #a1262f !important;
}

.main-exams-list .last-status-incomplete {
    background: #dbeafe !important;
    color: #2563a6 !important;
}

@media (max-width: 767px) {
    .main-exams-list .last-status-pill {
        padding: 3px 5px !important;
        min-width: 48px !important;
        font-size: 8px !important;
    }
}

/* Mobile fix: final content alignment for main Exams List */
@media (max-width: 767px) {
    .main-exams-list .exam-topic-row td:nth-child(4) {
        white-space:normal !important;
        word-break:normal !important;
        overflow-wrap:normal !important;
        line-height:1.2 !important;
    }

    .main-exams-list .exam-topic-row td:nth-child(5) {
        white-space:nowrap !important;
        word-break:normal !important;
    }

    .main-exams-list .exam-topic-row td:nth-child(6) {
        white-space:normal !important;
        word-break:normal !important;
        overflow-wrap:break-word !important;
        line-height:1.2 !important;
    }

    .main-exams-list .exam-topic-row td:nth-child(6) {
        overflow:hidden !important;
        text-align:center !important;
        padding-left:2px !important;
        padding-right:2px !important;
    }

    .main-exams-list .exam-topic-row td:nth-child(6) .link-retry {
        display:inline-block !important;
        width:auto !important;
        max-width:100% !important;
        box-sizing:border-box !important;
        white-space:nowrap !important;
        font-size:10px !important;
        padding:6px 9px !important;
        overflow:hidden !important;
    }

    /* Mobile: allow exam-row cells to wrap inside their assigned columns */
    .main-exams-list .exam-topic-row td {
        white-space:normal !important;
        word-break:normal !important;
        overflow-wrap:break-word !important;
        min-width:0 !important;
        max-width:100% !important;
        box-sizing:border-box !important;
        vertical-align:top !important;
    }

    .main-exams-list .exam-topic-row td:first-child {
        white-space:normal !important;
        overflow-wrap:break-word !important;
    }

    .main-exams-list .exam-topic-row td:nth-child(2),
    .main-exams-list .exam-topic-row td:nth-child(3),
    .main-exams-list .exam-topic-row td:nth-child(5) {
        white-space:nowrap !important;
        overflow:hidden !important;
    }

    .main-exams-list .exam-topic-row td:nth-child(4),
    .main-exams-list .exam-topic-row td:nth-child(6) {
        white-space:normal !important;
        word-break:break-word !important;
        overflow-wrap:anywhere !important;
    }
}

/* Mobile fix: main Exams List only */
@media (max-width: 767px) {
    table.exams-list-main {
        table-layout: fixed !important;
        width: 100% !important;
    }

    table.exams-list-main tr td {
        font-size: 10px !important;
        padding: 5px 2px !important;
        vertical-align: middle !important;
        line-height: 1.2 !important;
        word-break: normal !important;
        overflow-wrap: normal !important;
    }

    table.exams-list-main tr td:nth-child(1) {
        width: 7% !important;
        white-space: nowrap !important;
        text-align: center !important;
    }

    table.exams-list-main tr td:nth-child(2) {
        width: 38% !important;
        white-space: normal !important;
        word-break: normal !important;
        overflow-wrap: break-word !important;
        text-align: left !important;
    }

    table.exams-list-main tr td:nth-child(3) {
        width: 10% !important;
        white-space: nowrap !important;
        text-align: center !important;
    }

    table.exams-list-main tr td:nth-child(4) {
        width: 15% !important;
        white-space: nowrap !important;
        text-align: center !important;
    }

    table.exams-list-main tr td:nth-child(5) {
        width: 15% !important;
        white-space: normal !important;
        text-align: center !important;
    }

    table.exams-list-main tr td:nth-child(6) {
        width: 15% !important;
        white-space: normal !important;
        text-align: center !important;
    }
}

</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var topics = document.querySelectorAll('.main-exams-list .exam-topic-header');

    topics.forEach(function(topic) {
        topic.addEventListener('click', function() {
            var topicName = topic.getAttribute('data-topic');
            var rows = document.querySelectorAll(
                '.main-exams-list .exam-topic-row[data-topic="' +
                CSS.escape(topicName) + '"]'
            );

            var isOpen = topic.classList.contains('exam-topic-open');

            topic.classList.toggle('exam-topic-open', !isOpen);

            rows.forEach(function(row) {
                row.classList.toggle('exam-topic-visible', !isOpen);
            });
        });
    });
});
</script>
HTML;

$htmlMainSectionAdd = <<<HTML
<table style="display:none;" class="course-stats course-stats2 main-exams-list">
  <colgroup>
    <col style="width:28%;">
    <col style="width:15%;">
    <col style="width:17%;">
    <col style="width:10%;">
    <col style="width:12%;">
    <col style="width:18%;">
  </colgroup>
  <tr>
    <th colspan = "6" class="first-column">Exams List</th>
  </tr>
$resultExams
  <tr>
    <td colspan="6" class="exams-list-explanation" style="background:#eef6ff !important; color:#34445a !important; text-align:left !important; padding:16px 18px !important; font-size:12px !important; line-height:1.5 !important; white-space:normal !important; overflow-wrap:break-word !important;">
      <strong style="font-size:14px;">How Attempts and Scores Are Shown</strong><br><br>
      <strong>Started / Completed:</strong> Shows the number of times you have started and completed the exam.<br>
      <strong>Last Score:</strong> Shows your score from your <strong>most recent completed attempt</strong>.<br>
      <strong>Last Status:</strong> Shows the status of your <strong>most recent completed attempt</strong>.<br>
      <strong>Study Guide:</strong> The Study Guide is available when you have <strong>not yet taken the exam</strong>. It is optional and allows you to review the study material before starting the exam. Once you have taken the exam, the Study Guide option is no longer displayed.<br>
      <span style="display:block; margin-top:10px; padding:10px 12px; background:#fff4d6; border-radius:6px;">
        <strong>Important:</strong> Previous attempts are <strong>not included</strong> in Last Score or Last Status.
        If you currently have an attempt in progress, it will not be shown as your Last Score until that attempt is completed.
      </span>
    </td>
  </tr>
</table>
HTML;
$coursestats = $coursestats . $htmlNavMainPage . $htmlLastPage . $htmlMainSectionAddToggle . $htmlNavMainPageEND . $examTopicDropdownJS . $htmlMainSectionAdd;

error_log("ACUP_COURSESTATS: has_attempts_started=" .
    (strpos($coursestats, 'Attempts Started') !== false ? 'YES' : 'NO') .
    " length=" . strlen($coursestats));
}///COURSE MAIN PAGE INFORMATION END
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$_SESSION["mainsection"] = $_SESSION["mainsection"] + 1;

        $data = (object)[
            'num' => $section->section,
            'id' => $section->id,
			'name' => $section->name,
			'inside' => $inside,
			'outside' => $outside,
			'navsection' => $navigationTopic,
			'exam_table' => $htmlMainSectionExams,
			'coursestats' => $coursestats, //$coursestats
		];

        $data->title = $output->section_title_without_link($section, $course);

        $coursedisplay = $format->get_course_display();
        $data->headerdisplaymultipage = false;
        if ($coursedisplay == COURSE_DISPLAY_MULTIPAGE) {
            $data->headerdisplaymultipage = true;
			
			
			
            $data->title = str_replace("#section-0", "",$output->section_title($section, $course));
        }

        if ($section->section > $format->get_last_section_number()) {
            // Stealth sections (orphaned) has special title.
            $data->title = get_string('orphanedactivitiesinsectionno', '', $section->section);
        }

        if (!$section->visible) {
            $data->ishidden = true;
        }

        if ($course->id == SITEID) {
            $data->sitehome = true;
        }

        $data->editing = $format->show_editor();

        if (!$format->show_editor() && $coursedisplay == COURSE_DISPLAY_MULTIPAGE && empty($data->issinglesection)) {
            if ($section->uservisible) {
                $data->url = $format->get_view_url(null);
				//course_get_url($course, $section->section);
            }
        }
        $data->name = get_section_name($course, $section);

        return $data;
    }
}



