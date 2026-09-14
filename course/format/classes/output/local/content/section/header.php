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
<td class="all-columns" style="font-size:12px;text-align:center;">
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
<td class="all-columns2" style="font-size:12px;text-align:center;">
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
<td class="all-columns2" style="font-size:12px;text-align:center;">
HTML;
$rowHTML3 = <<<HTML
<tr><td colspan="4" class="special-column" style="text-align:center;background-color:#F47E2C;font-size:13px;font-weight:bold;color:#ffffff;padding:3px;">
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

///EXAM LIST INSIDE TOPICS BEGIN
if ($inside == true) {  
$Passed = array();
$maxScore = array();
$maxAttempt = array();
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
$scormurlValue = "/mod/scorm/view.php?id=" . $scormurl->id;

if (($currentSection->name) == $sectionName) {
$attempts = $DB->get_records('scorm_attempt', ['scormid' => $scorm->id, 'userid' => $USER->id]);
foreach ($attempts as $attemptX) {
$tracks = $DB->get_record('scorm_scoes_value',['attemptid' => $attemptX->id, 'elementid'=> 8]);
$attemptScores[] = $tracks->value;
$tempAttempt = $tempAttempt + 1;
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
$btnValue = "Try Now";
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
<table class="course-stats course-stats2 show-table">
  <tr>
    <th colspan = "6" class="first-column">Exams List</th>
  </tr>
  <tr>
    <td style="text-align:center;font-size:14px;" class="all-columns"><strong>#</strong></td>
	<td style="text-align:left;font-size:14px;" class="all-columns"><strong>Exam Name</strong></td>
	<td style="text-align:center;font-size:14px;" class="all-columns"><strong>Attempts</strong></td>
	<td style="text-align:center;font-size:14px;" class="all-columns"><strong>Maximum Score</strong></td>
	<td style="text-align:center;font-size:14px;" class="all-columns"><strong>Exam Status</strong></td>
	<td style="text-align:center;font-size:14px;" class="all-columns"><strong>Exam Link</strong></td>
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
$sectionNames = array();
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
$scormurlValue = "/mod/scorm/view.php?id=" . $scormurl->id;

$attempts = $DB->get_records('scorm_attempt', ['scormid' => $scorm->id, 'userid' => $USER->id]);
foreach ($attempts as $attemptX) {
$tracks = $DB->get_record('scorm_scoes_value',['attemptid' => $attemptX->id, 'elementid'=> 8]);
$attemptScores[] = $tracks->value;
$tempAttempt = $tempAttempt + 1;
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
$btnValue = "Try Now";
$tempAttempt = 0;
}

$examLink = <<<HTML
<a href="$scormurlValue" class="link-retry">$btnValue
HTML;

$examLinks[] = $rowHTMLLink . $examLink . $linkEND . $rowHTMLend;
$Passed[] = $rowHTML . $tempPassed . $rowHTMLend;
$scormName[] = $rowHTMLB . $tempName . $rowHTMLend;
$maxAttempt[] = $rowHTML . $tempAttempt . $rowHTMLend;
$maxScore[] = $rowHTML . $tempScore . $rowHTMLend;;
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
<table class="course-stats">
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

$tempSectionName = "";
array_multisort($scormName, $sectionNames, $maxScore, $maxAttempt, $examLinks);
for ($z = 0; $z <= $maxsection; $z++) {	
for ($x = 0; $x <= count($sectionNames)-1; $x++) {
if ($format->get_section_name($z) == $sectionNames[$x]) {
if ($tempSectionName !== $sectionNames[$x]) {
$resultExams = $resultExams . $rowHTML3 . $sectionNames[$x] . $rowHTMLend3 . $rowHTML2 . $scormName[$x] . $maxAttempt[$x] . $maxScore[$x] . $examLinks[$x] . $rowHTMLend2;	
$tempSectionName = $sectionNames[$x];
} else {
$resultExams = $resultExams . $rowHTML2 . $scormName[$x] . $maxAttempt[$x] . $maxScore[$x] . $examLinks[$x] . $rowHTMLend2;
}
}
}
}

$htmlMainSectionAdd = <<<HTML
<table style="display:none;" class="course-stats course-stats2">
  <tr>
    <th colspan = "4" class="first-column">Exams List</th>
  </tr>
  <tr>
    <td style="text-align:left;font-size:14px;" class="all-columns"><strong>Exam Name</strong></td>
	<td style="text-align:center;font-size:14px;" class="all-columns"><strong>Total Attempts</strong></td>
	<td style="text-align:center;font-size:14px;" class="all-columns"><strong>Maximum Score</strong></td>
	<td style="text-align:center;font-size:14px;" class="all-columns"><strong>Exam Link</strong></td>
  </tr>
$resultExams
</table>
HTML;
$coursestats = $coursestats . $htmlNavMainPage . $htmlLastPage . $htmlMainSectionAddToggle . $htmlNavMainPageEND . $htmlMainSectionAdd;
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
