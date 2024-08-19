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
 * Unit tests for mod_ardora backup and restore.
 *
 * @package    mod_ardora
 * @category   backup
 * @copyright  2024 José Manuel Bouzán Matanza
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_ardora;
use advanced_testcase;

/**
 * Unit test class for mod_ardora backup and restore functionality.
 *
 * @package    mod_ardora
 * @category   backup
 * @copyright  2024 José Manuel Bouzán Matanza
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backup_test extends advanced_testcase {

    /**
     * Test backup of a single ardora activity.
     *
     * @covers ::backup_single_activity
     */
    public function test_backup_single_activity(): void {
        global $DB;

        $this->resetAfterTest(true);

        // Create a course and an ardora module.
        $course = $this->getDataGenerator()->create_course();
        $ardora = $this->getDataGenerator()->create_module('ardora', ['course' => $course->id]);

        // Create backup for the ardora module.
        $bc = new backup_controller(backup::TYPE_1ACTIVITY, $ardora->cmid, backup::FORMAT_MOODLE,
            backup::INTERACTIVE_NO, backup::MODE_GENERAL, 2);
        $bc->execute_plan();
        $results = $bc->get_results();
        $this->assertNotEmpty($results['backup_destination']);

        // Verify that the backup contains the necessary data.
        $backupid = $bc->get_backupid();
        $this->assertNotEmpty($backupid);

        // Clean up.
        $bc->destroy();
    }

    /**
     * Test restore of a single ardora activity.
     *
     * @covers ::restore_single_activity
     */
    public function test_restore_single_activity(): void {
        global $DB;

        $this->resetAfterTest(true);

        // Create a course and an ardora module.
        $course = $this->getDataGenerator()->create_course();
        $ardora = $this->getDataGenerator()->create_module('ardora', ['course' => $course->id]);

        // Create backup for the ardora module.
        $bc = new backup_controller(backup::TYPE_1ACTIVITY, $ardora->cmid, backup::FORMAT_MOODLE,
            backup::INTERACTIVE_NO, backup::MODE_GENERAL, 2);
        $bc->execute_plan();
        $results = $bc->get_results();
        $backupfile = $results['backup_destination'];
        $backupid = $bc->get_backupid();

        // Restore the backup into a new course.
        $newcourse = $this->getDataGenerator()->create_course();
        $rc = new restore_controller($backupid, $newcourse->id, backup::INTERACTIVE_NO,
            backup::MODE_GENERAL, 2, context_course::instance($newcourse->id));
        $rc->execute_precheck();
        $rc->execute_plan();

        // Verify that the ardora module was restored correctly.
        $cm = get_coursemodule_from_instance('ardora', $ardora->id, $newcourse->id);
        $this->assertNotEmpty($cm);
        $restoredrecord = $DB->get_record('ardora', ['id' => $cm->instance]);
        $this->assertEquals($ardora->name, $restoredrecord->name);

        // Clean up.
        $bc->destroy();
        $rc->destroy();
    }

    /**
     * Test backup and restore of an entire course with ardora activities.
     *
     * @covers ::backup_course
     * @covers ::restore_course
     */
    public function test_backup_and_restore_course(): void {
        global $DB;

        $this->resetAfterTest(true);

        // Create a course and add multiple ardora modules.
        $course = $this->getDataGenerator()->create_course();
        $ardora1 = $this->getDataGenerator()->create_module('ardora', ['course' => $course->id]);
        $ardora2 = $this->getDataGenerator()->create_module('ardora', ['course' => $course->id]);

        // Create backup for the entire course.
        $bc = new backup_controller(backup::TYPE_COURSE, $course->id, backup::FORMAT_MOODLE,
            backup::INTERACTIVE_NO, backup::MODE_GENERAL, 2);
        $bc->execute_plan();
        $results = $bc->get_results();
        $backupid = $bc->get_backupid();

        // Restore the course backup into a new course.
        $newcourse = $this->getDataGenerator()->create_course();
        $rc = new restore_controller($backupid, $newcourse->id, backup::INTERACTIVE_NO,
            backup::MODE_GENERAL, 2, context_course::instance($newcourse->id));
        $rc->execute_precheck();
        $rc->execute_plan();

        // Verify that the ardora modules were restored correctly.
        $cm1 = get_coursemodule_from_instance('ardora', $ardora1->id, $newcourse->id);
        $cm2 = get_coursemodule_from_instance('ardora', $ardora2->id, $newcourse->id);
        $this->assertNotEmpty($cm1);
        $this->assertNotEmpty($cm2);

        // Clean up.
        $bc->destroy();
        $rc->destroy();
    }
}
