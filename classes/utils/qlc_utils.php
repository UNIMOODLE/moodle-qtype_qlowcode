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

namespace qtype_qlowcode\utils;
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
use invalid_parameter_exception;
use qtype_qlowcode\constants;
use Random\RandomException;

/**
 *  qlc_utlis class
 */
class qlc_utils extends external_api {
    /**
     * Validate css size
     *
     * @param string $subject
     * @return bool
     */
    public static function validate_css_size($subject): bool {
        $pattern = '/^((auto|initial|inherit)|(([0-9]+)|([0-9]+[\.][0-9]+))(cm|mm|in|px|pt|pc|em|%)?)$/';
        return preg_match($pattern, $subject);
    }

    /**
     * Generate key
     *
     * @param int $length
     * @return string
     */
    public static function generate_key($length = 10) {
        $bytes = random_bytes($length);
        $data = bin2hex($bytes);
        $key = hash('sha256', $data);

        return $key;
    }

    /**
     * Create user api
     *
     * @param string $pass
     * @return bool|string
     */
    public static function api_create_user($pass) {
        global $USER;

        $response = false;

        $url = get_config('qtype_qlowcode', "apiurl" . constants::QLOW_DEFAULT_REPOSITORY);
        $completeurl = rtrim($url, '/');
        if (empty($completeurl)) {
            throw new moodle_exception(get_string('invalidsettings', 'qtype_qlowcode'));
        }
        $apitoken = get_config('qtype_qlowcode', "apitoken" . constants::QLOW_DEFAULT_REPOSITORY);
        if (empty($apitoken)) {
            throw new moodle_exception(get_string('invalidsettings', 'qtype_qlowcode'));
        }

        try {
            $curl = new curl();
            $curl->setHeader(['qlctoken: ' . $apitoken]);
            $params = http_build_query(['email' => $USER->email, 'password' => $pass], '', '&');
            $response = $curl->post($completeurl . constants::QLOW_API_USERNEW, $params);
            return $response;
        } catch (Exception $e) {
            throw new moodle_exception('One or more of the dates provided were invalid');
        }

        return $response;
    }

    /**
     * Generate url
     *
     * @param string $pass
     * @return string
     */
    public static function generate_url($pass) {
        global $USER;

        $url = get_config('qtype_qlowcode', "qlowurl" . constants::QLOW_DEFAULT_REPOSITORY);
        $completeurl = rtrim($url, '/');
        $params = '?username=' . $USER->email . '&token=' . $pass;

        return $completeurl . constants::QLOW_API_URL . $params;
    }

    /**
     * Get workspaces
     *
     * @param integer $id
     * @return  array Provided array of workspaces
     * @throws moodle_exception If CURL fails
     */
    public static function get_workspaces($id = null) {

        global $USER;

        $return[] = [
                '_id' => '',
                'name' => get_string('selectworkspaceid', 'qtype_qlowcode'),
                'email' => '',
                'deleted' => '',
        ];

        $data = [];
        if ($id) {
            $data["id"] = $id;
        }
        $params = self::validate_parameters(self::get_workspaces_parameters(), $data);

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

            try {
                $curl = new curl();
                $curl->setHeader(['qlctoken: ' . $apitoken]);
                $response = $curl->get($completeurl . constants::QLOW_API_WORKSPACES, ['userEmail' => $USER->email]);
                $arrayworkspaces = json_decode($response, true);
                if (!empty($arrayworkspaces)) {
                    foreach ($arrayworkspaces as $workspace) {
                        $return[] = $workspace;
                    }
                }
            } catch (Exception $e) {
                throw new moodle_exception(get_string('curlerror', 'qtype_qlowcode'));
            }
        }

        return $return;
    }

    /**
     * Describes the parameters for get_workspaces.
     *
     * @return external_function_parameters
     */
    public static function get_workspaces_parameters() {
        return new \external_function_parameters([
                'id' => new \external_value(PARAM_INT, 'configuration id', VALUE_DEFAULT),
        ]);
    }

    /**
     * Describes the workspaces return format.
     *
     * @return external_multiple_structure
     */
    public static function get_workspaces_returns() {
        return new external_multiple_structure(
            new external_single_structure([
                        '_id' => new external_value(PARAM_TEXT, '_id'),
                        'name' => new external_value(PARAM_TEXT, 'name'),
                ])
        );
    }

    /**
     * Get applications
     *
     * @param integer $id
     * @param integer $workspaceid
     * @return  array Provided array of applications
     * @throws moodle_exception If CURL fails
     */
    public static function get_applications($id = null, $workspaceid = null) {

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
    public static function get_applications_parameters() {
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
    public static function get_applications_returns() {
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

    /**
     * Get pages
     *
     * @param integer $id
     * @param string $applicationid
     * @return  array Provided array of pages
     * @throws moodle_exception If CURL fails
     */
    public static function get_pages($id = null, $applicationid = null) {

        $return[] = [
                'name' => get_string('selectpageurl', 'qtype_qlowcode'),
                'id' => '',
                'applicationId' => '',
        ];

        $data = [];
        if ($id) {
            $data["id"] = $id;
        }
        if ($applicationid) {
            $data["applicationId"] = $applicationid;
        }
        $params = self::validate_parameters(self::get_pages_parameters(), $data);

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

            $applicationid = $params['applicationId'] ?? '';

            try {
                $curl = new curl();
                $curl->setHeader(['qlctoken: ' . $apitoken]);
                $response = $curl->get($completeurl . constants::QLOW_API_PAGES);
                $arraypages = json_decode($response, true);
                if (!empty($arraypages) && !empty($applicationid)) {
                    foreach ($arraypages as $filter) {
                        if ($filter["applicationId"] == $applicationid) {
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
     * Describes the parameters for get_pages.
     *
     * @return external_function_parameters
     */
    public static function get_pages_parameters() {
        return new \external_function_parameters([
                'id' => new \external_value(PARAM_INT, 'configuration id', VALUE_DEFAULT),
                'applicationId' => new \external_value(PARAM_TEXT, 'applicationId', VALUE_DEFAULT),
        ]);
    }

    /**
     * Describes the pages return format.
     *
     * @return external_multiple_structure
     */
    public static function get_pages_returns() {
        return new external_multiple_structure(
            new external_single_structure([
                        'name' => new external_value(PARAM_TEXT, 'name'),
                        'id' => new external_value(PARAM_TEXT, 'id'),
                        'applicationId' => new external_value(PARAM_TEXT, 'applicationId'),
                ])
        );
    }
}
