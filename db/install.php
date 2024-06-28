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
 * Custom code to be run on installing the plugin.
 */
function xmldb_qtype_qlowcode_install() {
    global $CFG, $DB;
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
}
