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

use curl;
use Exception;
use moodle_exception;
use qtype_qlowcode\constants;

/**
 *  qlc_utils class
 */
class qlc_utils
{
    /**
     * Validate css size
     *
     * @param string $subject
     * @return bool
     */
    public static function validate_css_size($subject): bool
    {
        $pattern = '/^((auto|initial|inherit)|(([0-9]+)|([0-9]+[\.][0-9]+))(cm|mm|in|px|pt|pc|em|%)?)$/';
        return preg_match($pattern, $subject);
    }

    /**
     * Generate key
     *
     * @param int $length
     * @return string
     */
    public static function generate_key($length = 10)
    {
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
    public static function api_create_user($pass)
    {
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
    public static function generate_url($pass)
    {
        global $USER;

        $url = get_config('qtype_qlowcode', "qlowurl" . constants::QLOW_DEFAULT_REPOSITORY);
        $completeurl = rtrim($url, '/');
        $params = '?username=' . $USER->email . '&token=' . $pass;

        return $completeurl . constants::QLOW_API_URL . $params;

        // ----------------------------

        // $response = false;

        // $url = get_config('qtype_qlowcode', "qlowurl" . constants::QLOW_DEFAULT_REPOSITORY);
        // $completeurl = rtrim($url, ':809');
        // if (empty($completeurl)) {
        //     throw new moodle_exception(get_string('invalidsettings', 'qtype_qlowcode'));
        // }
        // $apitoken = get_config('qtype_qlowcode', "apitoken" . constants::QLOW_DEFAULT_REPOSITORY);
        // if (empty($apitoken)) {
        //     throw new moodle_exception(get_string('invalidsettings', 'qtype_qlowcode'));
        // }

        // try {
        //     $curl = new curl();
        //     $curl->setHeader(['qlctoken: ' . $apitoken]);
        //     $params = http_build_query(['username' => $USER->email, 'password' => $pass], '', '&');
        //     var_dump($completeurl);
        //     $response = $curl->post($completeurl . '/api/v1/login', $params);
        //     var_dump($response);exit();
        //     return $response;
        // } catch (Exception $e) {
        //     throw new moodle_exception('One or more of the dates provided were invalid');
        // }
    }

    public static function encrypt($data)
    {
        $iv = '0123456789012345';
        $cipher_key = '01234567890123456789012345678901';
        return base64_encode(openssl_encrypt($data, 'AES-256-CBC', $cipher_key, OPENSSL_RAW_DATA, $iv));
    }
    public static function decrypt($data)
    {
        $iv = '0123456789012345';
        $cipher_key = '01234567890123456789012345678901';
        return openssl_decrypt(base64_decode($data), 'AES-256-CBC', $cipher_key, OPENSSL_RAW_DATA, $iv);
    }
}
