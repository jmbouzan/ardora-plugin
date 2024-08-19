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
 * Unit tests for mod_ardora.
 *
 * @package    mod_ardora
 * @copyright  2024 José Manuel Bouzán Matanza
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_ardora;
use advanced_testcase;

/**
 * Unit test class for mod_ardora.
 *
 * @package    mod_ardora
 * @copyright  2024 José Manuel Bouzán Matanza
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class ardora_test extends advanced_testcase {

    /**
     * Test the insertion of a record in the mdl_ardora table.
     *
     * @covers ::insert_ardora_record
     */
    public function test_insert_ardora_record(): void {
        global $DB;

        $this->resetAfterTest(true);

        // Define the data to be inserted.
        $data = new stdClass();
        $data->course = 1;
        $data->name = 'Test Ardora Resource';
        $data->ardora_id = '123456';
        $data->intro = 'Intro text for the ardora resource.';
        $data->introformat = FORMAT_HTML;
        $data->tobemigrated = 0;
        $data->legacyfiles = 0;
        $data->legacyfileslast = null;
        $data->display = 1;
        $data->displayoptions = json_encode(['option1' => 'value1']);
        $data->filterfiles = 0;
        $data->revision = 1;
        $data->timemodified = time();

        // Insert the data into the mdl_ardora table.
        $insertedid = $DB->insert_record('ardora', $data);

        // Retrieve the record to verify the insertion.
        $record = $DB->get_record('ardora', ['id' => $insertedid]);

        // Assert that the data was inserted correctly.
        $this->assertEquals($data->course, $record->course);
        $this->assertEquals($data->name, $record->name);
        $this->assertEquals($data->ardora_id, $record->ardora_id);
        $this->assertEquals($data->intro, $record->intro);
        $this->assertEquals($data->introformat, $record->introformat);
        $this->assertEquals($data->tobemigrated, $record->tobemigrated);
        $this->assertEquals($data->legacyfiles, $record->legacyfiles);
        $this->assertEquals($data->display, $record->display);
        $this->assertEquals($data->displayoptions, $record->displayoptions);
        $this->assertEquals($data->filterfiles, $record->filterfiles);
        $this->assertEquals($data->revision, $record->revision);
        $this->assertEquals($data->timemodified, $record->timemodified);
    }

    /**
     * Test the update of a record in the mdl_ardora table.
     *
     * @covers ::update_ardora_record
     */
    public function test_update_ardora_record(): void {
        global $DB;

        $this->resetAfterTest(true);

        // Insert an initial record.
        $data = new stdClass();
        $data->course = 1;
        $data->name = 'Initial Ardora Resource';
        $data->ardora_id = '123456';
        $data->intro = 'Initial intro text';
        $data->introformat = FORMAT_HTML;
        $data->display = 1;
        $data->timemodified = time();

        $insertedid = $DB->insert_record('ardora', $data);

        // Update the record.
        $data->id = $insertedid;
        $data->name = 'Updated Ardora Resource';
        $data->intro = 'Updated intro text';
        $DB->update_record('ardora', $data);

        // Retrieve the updated record.
        $record = $DB->get_record('ardora', ['id' => $insertedid]);

        // Assert that the data was updated correctly.
        $this->assertEquals('Updated Ardora Resource', $record->name);
        $this->assertEquals('Updated intro text', $record->intro);
    }

    /**
     * Test the deletion of a record from the mdl_ardora table.
     *
     * @covers ::delete_ardora_record
     */
    public function test_delete_ardora_record(): void {
        global $DB;

        $this->resetAfterTest(true);

        // Insert an initial record.
        $data = new stdClass();
        $data->course = 1;
        $data->name = 'Ardora Resource to be Deleted';
        $data->ardora_id = '123456';
        $data->intro = 'Intro text';
        $data->timemodified = time();

        $insertedid = $DB->insert_record('ardora', $data);

        // Delete the record.
        $DB->delete_records('ardora', ['id' => $insertedid]);

        // Verify that the record was deleted.
        $record = $DB->get_record('ardora', ['id' => $insertedid]);
        $this->assertFalse($record);
    }

    /**
     * Test the retrieval of a record from the mdl_ardora table.
     *
     * @covers ::get_ardora_record
     */
    public function test_get_ardora_record(): void {
        global $DB;

        $this->resetAfterTest(true);

        // Insert an initial record.
        $data = new stdClass();
        $data->course = 1;
        $data->name = 'Ardora Resource to be Retrieved';
        $data->ardora_id = '123456';
        $data->intro = 'Intro text';
        $data->timemodified = time();

        $insertedid = $DB->insert_record('ardora', $data);

        // Retrieve the record.
        $record = $DB->get_record('ardora', ['course' => 1, 'ardora_id' => '123456']);

        // Assert that the correct record was retrieved.
        $this->assertNotEmpty($record);
        $this->assertEquals($data->name, $record->name);
        $this->assertEquals($data->intro, $record->intro);
    }
}
