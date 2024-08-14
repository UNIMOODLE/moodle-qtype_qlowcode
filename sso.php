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

require_once(__DIR__ . '/../../../config.php');

use qtype_qlowcode\constants;
use qtype_qlowcode\utils\qlc_utils;

require_login();

if (has_capability(constants::QLOW_ROLE_CAPABILITY_SSO, context_system::instance())) {
    $pass = rand(1000,9999).'.'.time();
    if (qlc_utils::api_create_user($pass) == constants::QLOW_API_STATUS_OK) {
        $url = qlc_utils::generate_url($pass);
        redirect($url);
    }
}

$courseid = optional_param('id', 1, PARAM_INT);
redirect(
    new moodle_url('/course/view.php', ['id' => $courseid]),
    get_string('sso_error', 'qtype_qlowcode'),
    null,
    \core\output\notification::NOTIFY_ERROR
);
