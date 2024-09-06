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

namespace qtype_qlowcode\external;

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/externallib.php");
require_once("$CFG->libdir/filelib.php");

use external_api;
use curl;
use external_function_parameters;
use external_value;
use external_single_structure;
use external_multiple_structure;
use Exception;
use moodle_exception;
use qtype_qlowcode\constants;

/**
 *  qlc_get_applications class
 */
class qlc_get_applications extends external_api
{

    /**
     * Get applications
     *
     * @param integer $id
     * @param integer $workspaceid
     * @return  array Provided array of applications
     * @throws moodle_exception If CURL fails
     */
    public static function get_applications($id = null, $workspaceid = null)
    {

        $return[] = [
            'name' => get_string('selectapplicationid', 'qtype_qlowcode'),
            'slug' => '',
            'id' => '',
            'workspaceId' => '',
            'deleted' => '',
        ];

        $data = [];
        if ($id) {
            $data["id"] = $id;
        }
        if ($workspaceid) {
            $data["workspaceId"] = $workspaceid;
        }
        $params = self::validate_parameters(self::get_applications_parameters(), $data);

        $id = $params['id'] ?? '';
        if (!empty($id)) {
            $url = get_config('qtype_qlowcode', "apiurl$id");
            $completeurl = rtrim($url, '/');
            if (empty($completeurl)) {
                throw new moodle_exception(get_string('invalidsettings', 'qtype_qlowcode'));
            }
            $apitoken = get_config('qtype_qlowcode', "apitoken$id");
            if (empty($apitoken)) {
                throw new moodle_exception(get_string('invalidsettings', 'qtype_qlowcode'));
            }

            $workspaceid = $params['workspaceId'] ?? '';

            try {
                $curl = new curl();
                $curl->setHeader(['qlctoken: ' . $apitoken]);
                $response = $curl->get($completeurl . constants::QLOW_API_APPLICATIONS);
                $arrayapplications = json_decode($response, true);
                if (!empty($arrayapplications)) {
                    foreach ($arrayapplications as $filter) {
                        // Only not deleted.
                        if ($filter["workspaceId"] == $workspaceid && $filter["deleted"] == false) {
                            $return[] = $filter;
                        }
                    }
                }
            } catch (Exception $e) {
                throw new moodle_exception(get_string('curlerror', 'qtype_qlowcode'));
            }
        }

        return $return;
    }

    /**
     * Describes the parameters for get_applications.
     *
     * @return external_function_parameters
     */
    public static function get_applications_parameters()
    {
        return new \external_function_parameters([
            'id' => new \external_value(PARAM_INT, 'configuration id', VALUE_DEFAULT),
            'workspaceId' => new \external_value(PARAM_TEXT, 'workspaceId', VALUE_DEFAULT),
        ]);
    }

    /**
     * Describes the applications return format.
     *
     * @return external_multiple_structure
     */
    public static function get_applications_returns()
    {
        return new external_multiple_structure(
            new external_single_structure([
                'name' => new external_value(PARAM_TEXT, 'name'),
                'slug' => new external_value(PARAM_TEXT, 'slug'),
                'id' => new external_value(PARAM_TEXT, 'id'),
                'workspaceId' => new external_value(PARAM_TEXT, 'workspaceId'),
                'deleted' => new external_value(PARAM_TEXT, 'deleted'),
            ])
        );
    }

}
