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
 * Unit tests for mod_ardora events.
 *
 * @package    mod_ardora
 * @category   test
 * @copyright  2024 José Manuel Bouzán Matanza
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_ardora;
use core\event\base;
use mod_ardora\event\course_module_instance_list_viewed;
use mod_ardora\event\course_module_viewed;
use advanced_testcase;

/**
 * Unit test class for mod_ardora events.
 *
 * @package    mod_ardora
 * @category   test
 * @copyright  2024 José Manuel Bouzán Matanza
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class events_test extends advanced_testcase {

    /**
     * Test the course_module_instance_list_viewed event.
     *
     * @covers ::course_module_instance_list_viewed
     */
    public function test_course_module_instance_list_viewed(): void {
        $this->resetAfterTest(true);

        // Create a course.
        $course = $this->getDataGenerator()->create_course();

        // Trigger the event.
        $event = course_module_instance_list_viewed::create([
            'context' => context_course::instance($course->id),
        ]);
        $event->trigger();

        // Validate the event.
        $this->assertInstanceOf(course_module_instance_list_viewed::class, $event);
        $this->assertEventContextNotUsed($event);
        $this->assertEventIsValid($event);
    }

    /**
     * Test the course_module_viewed event.
     *
     * @covers ::course_module_viewed
     */
    public function test_course_module_viewed(): void {
        $this->resetAfterTest(true);

        // Create a course and an ardora module.
        $course = $this->getDataGenerator()->create_course();
        $ardora = $this->getDataGenerator()->create_module('ardora', ['course' => $course->id]);

        // Trigger the event.
        $context = context_module::instance($ardora->cmid);
        $event = course_module_viewed::create([
            'context' => $context,
            'objectid' => $ardora->id,
            'relateduserid' => null,
            'courseid' => $course->id,
            'userid' => $this->getDataGenerator()->create_user()->id,
        ]);
        $event->add_record_snapshot('course_modules', $ardora->cm);
        $event->trigger();

        // Validate the event.
        $this->assertInstanceOf(course_module_viewed::class, $event);
        $this->assertEventContextNotUsed($event);
        $this->assertEventIsValid($event);
    }

    /**
     * Test the event base class for valid event handling.
     *
     * @covers ::event_base
     */
    public function test_event_base(): void {
        $this->resetAfterTest(true);

        // Create a mock event.
        $event = $this->getMockForAbstractClass(base::class, [], '', false);
        $event->expects($this->any())
            ->method('get_context')
            ->willReturn(context_system::instance());

        // Validate the event.
        $this->assertEventContextNotUsed($event);
        $this->assertEventIsValid($event);
    }
}
