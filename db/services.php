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
defined('MOODLE_INTERNAL') || die();

use qtype_qlowcode\utils\qlc_utils;

$functions = [
    // The name of your web service function, as discussed above.
        'qtype_qlowcode_endpoint' => [
            // The name of the namespaced class that the function is located in.
                'classname' => 'endpoint',

            // A brief, human-readable, description of the web service function.
                'description' => 'Endpoint.',

            // Options include read, and write.
                'type' => 'read',

            // Whether the service is available for use in AJAX calls from the web.
                'ajax' => true,

            // An optional list of services where the function will be included.
                'services' => [
                    // A standard Moodle install includes one default service:
                    // - MOODLE_OFFICIAL_MOBILE_SERVICE.
                    // Specifying this service means that your function will be available for
                    // use in the Moodle Mobile App.
                        MOODLE_OFFICIAL_MOBILE_SERVICE,
                ],
        ],
        'qtype_qlowcode_get_workspaces' => [
                'classname' => qlc_utils::class,
                'methodname' => 'get_workspaces',
                'description' => 'Get workspaces from Qlowcode.',
                'type' => 'read',
                'ajax' => true,
                'capabilities' => '',
                'loginrequired' => true,
        ],
        'qtype_qlowcode_get_applications' => [
                'classname' => qlc_utils::class,
                'methodname' => 'get_applications',
                'description' => 'Get applications from Qlowcode.',
                'type' => 'read',
                'ajax' => true,
                'capabilities' => '',
                'loginrequired' => true,
        ],
        'qtype_qlowcode_get_pages' => [
                'classname' => qlc_utils::class,
                'methodname' => 'get_pages',
                'description' => 'Get pages from Qlowcode.',
                'type' => 'read',
                'ajax' => true,
                'capabilities' => '',
                'loginrequired' => true,
        ],
];
