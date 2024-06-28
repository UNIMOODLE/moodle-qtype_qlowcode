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

namespace qtype_qlowcode;
/**
 * Constants class
 */
class constants {
    /**
     * Qlow number repository
     */
    public const QLOW_NUMBER_REPOSITORY = 1;
    /**
     * Qlow default repository
     */
    public const QLOW_DEFAULT_REPOSITORY = 1;
    /**
     * Qlow role name
     */
    public const QLOW_ROLE_NAME = 'Qlowcode';
    /**
     * Qlow role shortname
     */
    public const QLOW_ROLE_SHORTNAME = 'qlowcode';
    /**
     * Qlow sso capability
     */
    public const QLOW_ROLE_CAPABILITY_SSO = 'qtype/qlowcode:access';
    /**
     * App url
     */
    public const QLOW_URL_APP = '/app/';
    /**
     * Workspaces url
     */
    public const QLOW_API_WORKSPACES = '/db/workspacesUser';
    /**
     * Applications url
     */
    public const QLOW_API_APPLICATIONS = '/db/applications';
    /**
     * Pages url
     */
    public const QLOW_API_PAGES = '/db/pages';
    /**
     * API usernew url
     */
    public const QLOW_API_USERNEW = '/qlc/userNew';
    /**
     * API login url
     */
    public const QLOW_API_URL = '/qlc/login.html';
    /**
     * API OK status
     */
    public const QLOW_API_STATUS_OK = 'OK';
    /**
     * API KO status.
     */
    public const QLOW_API_STATUS_KO = 'KO';
    /**
     * API token qlc
     */
    public const QLOW_API_QLC_TOKEN = 'q45253c53v54yy6ub6u34vb';
}
