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

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {
    foreach (range(1, constants::QLOW_NUMBER_REPOSITORY) as $number) {
        $settings->add(new admin_setting_heading(
            "qtype_qlowcode/applicationurl$number",
            get_string('applicationurl', 'qtype_qlowcode', ['number' => '']),
            ''
        ));

        $settings->add(new admin_setting_configtext(
            "qtype_qlowcode/description$number",
            get_string('description', 'qtype_qlowcode'),
            get_string('descriptionhelp', 'qtype_qlowcode'),
            null,
            PARAM_RAW_TRIMMED
        ));
        $settings->add(new qtype_qlowcode_admin_setting_configtext_url(
            "qtype_qlowcode/qlowurl$number",
            get_string('qlowurl', 'qtype_qlowcode'),
            get_string('qlowurlhelp', 'qtype_qlowcode'),
            null,
            's+u?a+p?f?q-r-'
        ));
        $settings->add(new qtype_qlowcode_admin_setting_configtext_url(
            "qtype_qlowcode/apiurl$number",
            get_string('apiurl', 'qtype_qlowcode'),
            get_string('apiurlhelp', 'qtype_qlowcode'),
            null,
            's+u?a+p?f?q-r-'
        ));
        $settings->add(new admin_setting_configpasswordunmask(
            "qtype_qlowcode/apitoken$number",
            get_string('apitoken', 'qtype_qlowcode'),
            get_string('apitokenhelp', 'qtype_qlowcode'),
            ''
        ));
    }
}
