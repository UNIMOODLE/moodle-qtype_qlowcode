<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.
// Project implemented by the \"Recovery, Transformation and Resilience Plan.
// Funded by the European Union - Next GenerationEU\".
//
// Produced by the UNIMOODLE University Group: Universities of
// Valladolid, Complutense de Madrid, UPV/EHU, León, Salamanca,
// Illes Balears, Valencia, Rey Juan Carlos, La Laguna, Zaragoza, Málaga,
// Córdoba, Extremadura, Vigo, Las Palmas de Gran Canaria y Burgos.

/**
 * Version details
 *
 * @package    qtype_qlowcode
 * @copyright  2023 Proyecto UNIMOODLE
 * @author     UNIMOODLE Group (Coordinator) <direccion.area.estrategia.digital@uva.es>
 * @author     ISYC <soporte@isyc.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use qtype_qlowcode\constants;

/**
 * Checks file access for qlowcode questions.
 *
 * @param stdClass $course course object
 * @param stdClass $cm course module object
 * @param stdClass $context context object
 * @param string $filearea file area
 * @param array $args extra arguments
 * @param bool $forcedownload whether or not force download
 * @param array $options additional options affecting the file serving
 * @return bool
 * @package  qtype_qlowcode
 * @category files
 */
function qtype_qlowcode_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = []) {
    global $DB, $CFG;
    require_once($CFG->libdir . '/questionlib.php');
    question_pluginfile($course, $context, 'qtype_qlowcode', $filearea, $args, $forcedownload, $options);
}

/**
 * Navigation node
 *
 * @param navigation_node $parentnode
 * @param stdClass $course
 * @param context_course $context
 * @return void
 * @throws moodle_exception
 */
function qtype_qlowcode_extend_navigation_course(navigation_node $parentnode, stdClass $course, context_course $context) {
    global $CFG;

    if (has_capability(constants::QLOW_ROLE_CAPABILITY_SSO, $context)) {
        $parentnode->add(
            get_string('SSO', 'qtype_qlowcode'),
            new moodle_url($CFG->wwwroot . '/question/type/qlowcode/sso.php', ['id' => $course->id]),
            navigation_node::TYPE_SETTING,
            null,
            'qlowcode'
        );
    }
}
