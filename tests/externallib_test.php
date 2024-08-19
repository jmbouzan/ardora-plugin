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
 * Unit tests for mod_ardora external functions.
 *
 * @package    mod_ardora
 * @category   external
 * @copyright  2024 José Manuel Bouzán Matanza
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_ardora;
use externallib_advanced_testcase;

/**
 * Unit test class for mod_ardora external functions.
 *
 * @package    mod_ardora
 * @category   external
 * @copyright  2024 José Manuel Bouzán Matanza
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class externallib_test extends externallib_advanced_testcase {

    /**
     * Test the view_ardora external function.
     *
     * @covers ::view_ardora
     */
    public function test_view_ardora(): void {
        global $DB;

        $this->resetAfterTest(true);

        // Create a course and an ardora module.
        $course = $this->getDataGenerator()->create_course();
        $ardora = $this->getDataGenerator()->create_module('ardora', ['course' => $course->id]);

        // Simulate the view_ardora external function.
        $result = mod_ardora_external::view_ardora($ardora->id);
        $result = external_api::clean_returnvalue(mod_ardora_external::view_ardora_returns(), $result);

        // Check that the function returns a successful status.
        $this->assertTrue($result['status']);

        // Check that no warnings are returned.
        $this->assertEmpty($result['warnings']);

        // Verify the record in the logs.
        $this->assertTrue($DB->record_exists('logstore_standard_log', [
            'component' => 'mod_ardora',
            'action' => 'viewed',
            'target' => 'course_module',
            'objectid' => $ardora->id,
        ]));
    }

    /**
     * Test the get_ardoras_by_courses external function.
     *
     * @covers ::get_ardoras_by_courses
     */
    public function test_get_ardoras_by_courses(): void {
        global $DB;

        $this->resetAfterTest(true);

        // Create a course and an ardora module.
        $course = $this->getDataGenerator()->create_course();
        $ardora = $this->getDataGenerator()->create_module('ardora', ['course' => $course->id]);

        // Simulate the get_ardoras_by_courses external function.
        $result = mod_ardora_external::get_ardoras_by_courses([$course->id]);
        $result = external_api::clean_returnvalue(mod_ardora_external::get_ardoras_by_courses_returns(), $result);

        // Check that one ardora is returned.
        $this->assertCount(1, $result['ardoras']);
        $this->assertEquals($ardora->id, $result['ardoras'][0]['id']);

        // Verify the course ID.
        $this->assertEquals($course->id, $result['ardoras'][0]['course']);
    }

    /**
     * Test the save_job external function.
     *
     * @covers ::save_job
     */
    public function test_save_job(): void {
        global $DB;

        $this->resetAfterTest(true);

        // Create a course and an ardora module.
        $course = $this->getDataGenerator()->create_course();
        $ardora = $this->getDataGenerator()->create_module('ardora', ['course' => $course->id]);

        // Define the job data.
        $params = [
            'ardoraid' => $ardora->id,
            'datajob' => '2024-08-15 12:00:00',
            'father' => 'parent1',
            'type' => 'quiz',
            'paq_name' => 'package1',
            'activity' => 'activity1',
            'hstart' => '2024-08-15 12:05:00',
            'hend' => '2024-08-15 12:10:00',
            'state' => 1,
            'attemps' => 3,
            'points' => 80,
        ];

        // Simulate the save_job external function.
        $result = mod_ardora_external::save_job($params);
        $result = external_api::clean_returnvalue(mod_ardora_external::save_job_returns(), $result);

        // Check that the function returns a successful status.
        $this->assertTrue($result['status']);

        // Verify that the job was saved in the database.
        $record = $DB->get_record('ardora_jobs', ['ardora_id' => $ardora->id, 'datajob' => $params['datajob']]);
        $this->assertNotEmpty($record);
        $this->assertEquals($params['father'], $record->father);
        $this->assertEquals($params['attemps'], $record->attemps);
    }
}
