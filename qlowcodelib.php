<?php
// This file is part of Moodle - http://moodle.org/
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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Multi-answer question type upgrade code.
 *
 * @package    qtype
 * @subpackage qlowcode
 * @copyright  2912 Marcus Green 
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->libdir . '/mathslib.php');

require_once($CFG->dirroot . '/question/type/qlowcode/phpseclib/Crypt/RSA.php');
require_once($CFG->dirroot . '/question/type/qlowcode/phpseclib/Math/BigInteger.php');

class qlc_utils {

    private static $_privatekey;

    public static function get_privatekey() {

        if (!isset(self::$_privatekey)) {
            self::$_privatekey = get_config('qtype_qlowcode', 'privatekey');
        }

        return self::$_privatekey;
    }

    public static function decrypt($cipher64, $privatekey = null) {

        if (isset($cipher64)) {
            $cipher64decoded = base64_decode($cipher64);
            if ($cipher64decoded) {
    
                if (!isset($privatekey)) {
                    // Aquired from adminstration settings if unset, lazy load
                    $privatekey = self::get_privatekey();
                }
    
                $rsa = new Crypt_RSA();
                $rsa->loadKey($privatekey);
                $rsa->setEncryptionMode(CRYPT_RSA_ENCRYPTION_PKCS1);
                
                return $rsa->decrypt($cipher64decoded);
    
            }        
        }
    
        return null;    
    }

    public static function get_from_response(array $response, $key, $privatekey = null) {
        
        if (array_key_exists('answer', $response)) {
            $cipher64 = $response['answer'];
            $plain = self::decrypt($cipher64, $privatekey);

            if (isset($plain)) {
                // Force associative array for later key exists checking
                $json_assoc = json_decode($plain, true);
                if (isset($json_assoc) && array_key_exists($key, $json_assoc)) {
                    return $json_assoc[$key];
                }
            }
        }

        return null;
    }

    public static function get_records_from(array $response, $table = 'question_qlowcode_temp') {

        global $DB;

        if (array_key_exists('qaId', $response) && array_key_exists('userId', $response)) {

        }

        return null;
    }
 
}
