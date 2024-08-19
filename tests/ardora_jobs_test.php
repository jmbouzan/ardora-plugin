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

namespace mod_ardora;

use advanced_testcase;

/**
 * Unit test class for mod_ardora_jobs.
 *
 * @package    mod_ardora
 * @category   test
 * @copyright  2024 José Manuel Bouzán Matanza
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class ardora_jobs_test extends advanced_testcase {

    /**
     * Test the insertion of a record in the mdl_ardora_jobs table.
     *
     * @covers ::insert_ardora_jobs_record
     */
    public function test_insert_ardora_jobs_record(): void {
        global $DB;

        $this->resetAfterTest(true);

        // Define the data to be inserted.
        $data = new stdClass();
        $data->courseid = 1;
        $data->userid = 2;
        $data->datajob = '2024-08-15 12:00:00';
        $data->father = 'parent1';
        $data->type = 'quiz';
        $data->paq_name = 'package1';
        $data->ardora_id = '123456';
        $data->activity = 'activity1';
        $data->hstart = '2024-08-15 12:05:00';
        $data->hend = '2024-08-15 12:10:00';
        $data->state = 1;
        $data->attemps = 3;
        $data->points = 80;

        // Insert the data into the mdl_ardora_jobs table.
        $insertedid = $DB->insert_record('ardora_jobs', $data);

        // Retrieve the record to verify the insertion.
        $record = $DB->get_record('ardora_jobs', ['id' => $insertedid]);

        // Assert that the data was inserted correctly.
        $this->assertEquals($data->courseid, $record->courseid);
        $this->assertEquals($data->userid, $record->userid);
        $this->assertEquals($data->datajob, $record->datajob);
        $this->assertEquals($data->father, $record->father);
        $this->assertEquals($data->type, $record->type);
        $this->assertEquals($data->paq_name, $record->paq_name);
        $this->assertEquals($data->ardora_id, $record->ardora_id);
        $this->assertEquals($data->activity, $record->activity);
        $this->assertEquals($data->hstart, $record->hstart);
        $this->assertEquals($data->hend, $record->hend);
        $this->assertEquals($data->state, $record->state);
        $this->assertEquals($data->attemps, $record->attemps);
        $this->assertEquals($data->points, $record->points);
    }

    /**
     * Test the update of a record in the mdl_ardora_jobs table.
     *
     * @covers ::update_ardora_jobs_record
     */
    public function test_update_ardora_jobs_record(): void {
        global $DB;

        $this->resetAfterTest(true);

        // Insert an initial record.
        $data = new stdClass();
        $data->courseid = 1;
        $data->userid = 2;
        $data->datajob = '2024-08-15 12:00:00';
        $data->ardora_id = '123456';
        $data->state = 1;
        $data->attemps = 3;
        $data->points = 80;

        $insertedid = $DB->insert_record('ardora_jobs', $data);

        // Update the record.
        $data->id = $insertedid;
        $data->state = 2;
        $data->points = 90;
        $DB->update_record('ardora_jobs', $data);

        // Retrieve the updated record.
        $record = $DB->get_record('ardora_jobs', ['id' => $insertedid]);

        // Assert that the data was updated correctly.
        $this->assertEquals(2, $record->state);
        $this->assertEquals(90, $record->points);
    }

    /**
     * Test the deletion of a record from the mdl_ardora_jobs table.
     *
     * @covers ::delete_ardora_jobs_record
     */
    public function test_delete_ardora_jobs_record(): void {
        global $DB;

        $this->resetAfterTest(true);

        // Insert an initial record.
        $data = new stdClass();
        $data->courseid = 1;
        $data->userid = 2;
        $data->datajob = '2024-08-15 12:00:00';
        $data->ardora_id = '123456';
        $data->state = 1;

        $insertedid = $DB->insert_record('ardora_jobs', $data);

        // Delete the record.
        $DB->delete_records('ardora_jobs', ['id' => $insertedid]);

        // Verify that the record was deleted.
        $record = $DB->get_record('ardora_jobs', ['id' => $insertedid]);
        $this->assertFalse($record);
    }

    /**
     * Test the retrieval of a record from the ardora_jobs table.
     *
     * @covers ::get_ardora_jobs_record
     */
    public function test_get_ardora_jobs_record(): void {
        global $DB;

        $this->resetAfterTest(true);

        // Insert an initial record.
        $data = new stdClass();
        $data->courseid = 1;
        $data->userid = 2;
        $data->datajob = '2024-08-15 12:00:00';
        $data->ardora_id = '123456';
        $data->state = 1;

        $insertedid = $DB->insert_record('ardora_jobs', $data);

        // Retrieve the record.
        $record = $DB->get_record('ardora_jobs', ['courseid' => 1, 'userid' => 2, 'ardora_id' => '123456']);

        // Assert that the correct record was retrieved.
        $this->assertNotEmpty($record);
        $this->assertEquals($data->courseid, $record->courseid);
        $this->assertEquals($data->userid, $record->userid);
        $this->assertEquals($data->datajob, $record->datajob);
    }
}
