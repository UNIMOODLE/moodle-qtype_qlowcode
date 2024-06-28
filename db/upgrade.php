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
 * Upgrade code for the qlowcode question type.
 * A selection of things you might want to do when upgrading
 * to a new version. This file is generally not needed for
 * the first release of a question type.
 *
 * @param int $oldversion the version we are upgrading from.
 */
function xmldb_qtype_qlowcode_upgrade($oldversion = 0) {
    global $CFG, $DB;

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
    }

    if ($oldversion < 2013012913) {
        $dbman = $DB->get_manager();

        // Rename field questionurl on table question_qlowcode to questionnaire.
        $table = new xmldb_table('question_qlowcode');
        $field = new xmldb_field('questionurl', XMLDB_TYPE_TEXT, null, null, null, null, null, 'questionid');

        // Launch rename field questionnaire.
        $dbman->rename_field($table, $field, 'questionnaire');

        // Qlowcode savepoint reached.
        upgrade_plugin_savepoint(true, 2013012913, 'qtype', 'qlowcode');
    }

    if ($oldversion < 2013012914) {
        $dbman = $DB->get_manager();

        // Define field questionnairequestion to be added to question_qlowcode.
        $table = new xmldb_table('question_qlowcode');
        $field = new xmldb_field('questionnairequestion', XMLDB_TYPE_TEXT, null, null, null, null, null, 'questionnaire');

        // Conditionally launch add field questionnairequestion.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Qlowcode savepoint reached.
        upgrade_plugin_savepoint(true, 2013012914, 'qtype', 'qlowcode');
    }

    if ($oldversion < 2013012915) {
        $dbman = $DB->get_manager();

        // Define field seckey to be added to question_qlowcode_temp.
        $table = new xmldb_table('question_qlowcode_temp');
        $field = new xmldb_field('seckey', XMLDB_TYPE_TEXT, null, null, null, null, null, 'resultcorrect');

        // Conditionally launch add field seckey.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Qlowcode savepoint reached.
        upgrade_plugin_savepoint(true, 2013012915, 'qtype', 'qlowcode');
    }

    if ($oldversion < 2013012916) {
        $dbman = $DB->get_manager();

        // Define field frameheight to be added to question_qlowcode.
        $table = new xmldb_table('question_qlowcode');
        $field = new xmldb_field('frameheight', XMLDB_TYPE_TEXT, null, null, null, null, null, 'framewidth');

        // Conditionally launch add field frameheight.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Qlowcode savepoint reached.
        upgrade_plugin_savepoint(true, 2013012916, 'qtype', 'qlowcode');
    }

    if ($oldversion < 2013012917) {
        $dbman = $DB->get_manager();

        // Define field mask to be added to question_qlowcode_temp.
        $table = new xmldb_table('question_qlowcode_temp');
        $field = new xmldb_field('mask', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, '0', 'seckey');

        // Conditionally launch add field mask.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Qlowcode savepoint reached.
        upgrade_plugin_savepoint(true, 2013012917, 'qtype', 'qlowcode');
    }

    if ($oldversion < 2024041204) {
        $dbman = $DB->get_manager();

        // Define field pageurl to be added to question_qlowcode.
        $table = new xmldb_table('question_qlowcode');
        $field = new xmldb_field('configurl', XMLDB_TYPE_TEXT, null, null, null, null, null, 'questionid');

        // Conditionally launch add field pageurl.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Qlowcode savepoint reached.
        upgrade_plugin_savepoint(true, 2024041204, 'qtype', 'qlowcode');
    }

    if ($oldversion < 2024041205) {
        $dbman = $DB->get_manager();

        $table = new xmldb_table('question_qlowcode');
        // Rename field questionnaire on table question_qlowcode to applicationurl.
        $field = new xmldb_field('questionnaire', XMLDB_TYPE_TEXT, null, null, null, null, null, 'questionid');
        // Rename field questionnairequestion on table question_qlowcode to pageurl.
        $field2 = new xmldb_field('questionnairequestion', XMLDB_TYPE_TEXT, null, null, null, null, null, 'questionnaire');

        // Launch rename field applicationurl.
        $dbman->rename_field($table, $field, 'applicationurl');
        // Launch rename field pageurl.
        $dbman->rename_field($table, $field2, 'pageurl');

        // Qlowcode savepoint reached.
        upgrade_plugin_savepoint(true, 2024041205, 'qtype', 'qlowcode');
    }

    if ($oldversion < 2024041207) {
        $dbman = $DB->get_manager();

        // Define field applicationid to be added to question_qlowcode.
        $table = new xmldb_table('question_qlowcode');
        $field = new xmldb_field('applicationid', XMLDB_TYPE_TEXT, null, null, null, null, null, 'configurl');

        // Conditionally launch add field pageurl.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Qlowcode savepoint reached.
        upgrade_plugin_savepoint(true, 2024041207, 'qtype', 'qlowcode');
    }

    if ($oldversion < 2024041218) {
        require_once($CFG->libdir . '/accesslib.php');
        $id = create_role(constants::QLOW_ROLE_NAME, constants::QLOW_ROLE_SHORTNAME, 'SSO/Create apps');
        set_role_contextlevels($id, [CONTEXT_SYSTEM]);
        $context = context_system::instance();
        $capability = new stdClass();
        $capability->name = constants::QLOW_ROLE_CAPABILITY_SSO;
        $capability->captype = 'read';
        $capability->contextlevel = $context->id;
        $capability->component = 'qtype_qlowcode';
        $capability->riskbitmask = 0;
        $DB->insert_record('capabilities', $capability, false);
        assign_capability(constants::QLOW_ROLE_CAPABILITY_SSO, CAP_ALLOW, $id, $context->id, true);

        // Qlowcode savepoint reached.
        upgrade_plugin_savepoint(true, 2024041218, 'qtype', 'qlowcode');
    }

    if ($oldversion < 2024061001) {
        $dbman = $DB->get_manager();

        // Define field workspaceid to be added to question_qlowcode.
        $table = new xmldb_table('question_qlowcode');
        $field = new xmldb_field('workspaceid', XMLDB_TYPE_TEXT, null, null, null, null, null, 'configurl');

        // Conditionally launch add field pageurl.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Qlowcode savepoint reached.
        upgrade_plugin_savepoint(true, 2024061001, 'qtype', 'qlowcode');
    }

    return true;
}
