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
 * List of all resources in course
 * created from the "Resource module" version created by 1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @package    mod_ardora
 * @copyright  2024 José Manuel Bouzán Matanza (https://www.webardora.net)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php'); require("lib.php");

$id = required_param('id', PARAM_INT); // Course id.

$course = $DB->get_record('course', ['id' => $id], '*', MUST_EXIST);

require_course_login($course, true);
$PAGE->set_pagelayout('incourse');

$params = [
    'context' => context_course::instance($course->id),
];
$event = \mod_ardora\event\course_module_instance_list_viewed::create($params);
$event->add_record_snapshot('course', $course);
$event->trigger();

$strardora     = get_string('modulename', 'ardora');
$strardoras    = get_string('modulenameplural', 'ardora');
$strsectionname  = get_string('sectionname', 'format_'.$course->format);
$strname         = get_string('name');
$strintro        = get_string('moduleintro');
$strlastmodified = get_string('lastmodified');

$PAGE->set_url('/mod/ardora/index.php', ['id' => $course->id]);
$PAGE->set_title($course->shortname.': '.$strardoras);
$PAGE->set_heading($course->fullname);
$PAGE->navbar->add($strardoras);
echo $OUTPUT->header();
echo $OUTPUT->heading($strardoras);

if (!$ardoras = get_all_instances_in_course('ardora', $course)) {
    notice(get_string('thereareno', 'moodle', $strardoras), "$CFG->wwwroot/course/view.php?id=$course->id");
    exit;
}

$usesections = course_format_uses_sections($course->format);

$table = new html_table();
$table->attributes['class'] = 'generaltable mod_index';

if ($usesections) {
    $table->head  = [$strsectionname, $strname, $strintro];
    $table->align = ['center', 'left', 'left'];
} else {
    $table->head  = [$strlastmodified, $strname, $strintro];
    $table->align = ['left', 'left', 'left'];
}

$modinfo = get_fast_modinfo($course);
$currentsection = '';
foreach ($ardoras as $ardora) {
    $cm = $modinfo->cms[$ardora->coursemodule];
    if ($usesections) {
        $printsection = '';
        if ($ardora->section !== $currentsection) {
            if ($ardora->section) {
                $printsection = get_section_name($course, $ardora->section);
            }
            if ($currentsection !== '') {
                $table->data[] = 'hr';
            }
            $currentsection = $ardora->section;
        }
    } else {
        $printsection = '<span class="smallinfo">'.userdate($ardora->timemodified)."</span>";
    }

    $extra = empty($cm->extra) ? '' : $cm->extra;
    $icon = '';
    if (!empty($cm->icon)) {
        // Each ardora file has an icon in 2.0.
        $icon = $OUTPUT->pix_icon($cm->icon, get_string('modulename', $cm->modname));
    }

    $class = $ardora->visible ? '' : 'class="dimmed"'; // Hidden modules are dimmed.
    $table->data[] = [
        $printsection,
        "<a $class $extra href=\"view.php?id=$cm->id\">".$icon.format_string($ardora->name)."</a>",
        format_module_intro('ardora', $ardora, $cm->id),
    ];
}

echo html_writer::table($table);

echo $OUTPUT->footer();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ID actual user.
    $userid = $USER->id;
    $courseid = 12;
    // Obtain request data.
    $father = $_POST['father'];
    $paqname = $_POST['paq_name'];
    $activity = $_POST['activity'];
    $executions = $_POST['executions'];
    $state = $_POST['state'];
    $attemps = $_POST['attemps'];
    $points = $_POST['points'];
    // Insert data into Moodle database.
    mod_ardora_insert_jobs($courseid, $userid, $fahter, $paqname, $activity, $executions, $state, $attemps, $points);
}
