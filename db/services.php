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

use qtype_qlowcode\ws\endpoint;
use qtype_qlowcode\ws\qlc_get_applications;
use qtype_qlowcode\ws\qlc_get_pages;
use qtype_qlowcode\ws\qlc_get_workspaces;

$functions = [
    // The name of your web service function, as discussed above.
    'qtype_qlowcode_endpoint' => [
        'classname' => endpoint::class,
        'methodname' => 'execute',
        'description' => 'Endpoint.',
        'type' => 'read',
        'ajax' => true,
        'services' => [
            MOODLE_OFFICIAL_MOBILE_SERVICE,
        ],
    ],
    'qtype_qlowcode_get_workspaces' => [
        'classname' => qlc_get_workspaces::class,
        'methodname' => 'get_workspaces',
        'description' => 'Get workspaces from Qlowcode.',
        'type' => 'read',
        'ajax' => true,
        'capabilities' => '',
        'loginrequired' => true,
    ],
    'qtype_qlowcode_get_applications' => [
        'classname' => qlc_get_applications::class,
        'methodname' => 'get_applications',
        'description' => 'Get applications from Qlowcode.',
        'type' => 'read',
        'ajax' => true,
        'capabilities' => '',
        'loginrequired' => true,
    ],
    'qtype_qlowcode_get_pages' => [
        'classname' => qlc_get_pages::class,
        'methodname' => 'get_pages',
        'description' => 'Get pages from Qlowcode.',
        'type' => 'read',
        'ajax' => true,
        'capabilities' => '',
        'loginrequired' => true,
    ],
];
