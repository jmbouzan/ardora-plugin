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
 * Ardora function library
 * Auxiliary functions for Ardora
 * @package    mod_ardora
 * @copyright  2024 José Manuel Bouzán Matanza (https://www.webardora.net)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
require_login();
require_once("$CFG->dirroot/mod/ardora/lib.php");

if ($_POST["action"] == "add_job") {
    $datajob = $_POST['datajob'];
    $type = $_POST['type'];
    $father = $_POST['father'];
    $paqname = $_POST['paq_name'];
    $ardoraid = $_POST['ardora_id'];
    $activity = $_POST['activity'];
    $hstart = $_POST['hstart'];
    $hend = $_POST['hend'];
    $attemps = $_POST['attemps'];
    $points = $_POST['points'];
    $state = $_POST['state'];
    $typegrade = $_POST['typegrade'];
    $us = mod_ardora_save_job(
        $datajob,
        $father,
        $type,
        $paqname,
        $activity,
        $hstart,
        $hend,
        $attemps,
        $points,
        $state,
        $ardoraid,
        $typegrade
    );
}

if ($_POST["action"] == "get_job") {
    $type = $_POST['type'];
    $father = $_POST['father'];
    $paqname = $_POST['paq_name'];
    $ardoraid = $_POST['ardora_id'];
    $jobs = get_user_ardora_jobs($type, $father, $paqname, $ardoraid);
    // Array to JSON.
    $jsonresponse = json_encode($jobs);
    // JSON to client.
    header('Content-Type: application/json');
    echo $jsonresponse;
}

if ($_POST["action"] == "get_eval") {
    $type = $_POST['type'];
    $father = $_POST['father'];
    $paqname = $_POST['paq_name'];
    $ardoraid = $_POST['ardora_id'];
    $jobs = get_user_ardora_eval($type, $father, $paqname, $ardoraid);
    $jsonresponse = json_encode($jobs);
    header('Content-Type: application/json');
    echo $jsonresponse;
}

if ($_POST["action"] == "get_info") {
    $type = $_POST['type'];
    $ardoraid = $_POST['ardora_id'];
    $jobs = get_user_ardora_info($type, $ardoraid);
    $jsonresponse = json_encode($jobs);
    header('Content-Type: application/json');
    echo $jsonresponse;
}

if ($_POST["action"] == "del_job") {
    // Teacher deletes activity records.
    $userid = $_POST['user_id'];
    $datajob = $_POST['datajob'];
    $ardoraid = $_POST['ardora_id'];
    $jobs = del_user_ardora_job($userid, $datajob, $ardoraid);
}
