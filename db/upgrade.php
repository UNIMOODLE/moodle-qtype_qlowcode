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
 * Multi-answer question type upgrade code.
 *
 * @package    qtype
 * @subpackage qlowcode
 * @copyright  2912 Marcus Green 
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

/**
 * Upgrade code for the qlowcode question type.
 * A selection of things you might want to do when upgrading
 * to a new version. This file is generally not needed for 
 * the first release of a question type.
 * @param int $oldversion the version we are upgrading from.
 */
function xmldb_qtype_qlowcode_upgrade($oldversion = 0) {
    global $DB;

    if ($oldversion < 2013012912) {

        // Define field framewidth to be added to question_qlowcode.
        $table = new xmldb_table('question_qlowcode');
        $field = new xmldb_field('framewidth', XMLDB_TYPE_TEXT, null, null, null, null, null, 'questionurl');

        $dbman = $DB->get_manager();

        // Conditionally launch add field framewidth.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Qlowcode savepoint reached.
        upgrade_plugin_savepoint(true, 2013012912, 'qtype', 'qlowcode');

        return true;
    }    
}
