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

// Write regular expression.
define('QLC_W_RGX', '/(?i)^write$/');
// Read regular expression.
define('QLC_R_RGX', '/(?i)^read$/');

define('QLC_TEST_USERID_THRESHOLD', 9000000000);
define('QLC_TEST_SECKEY', 'c644c6a4564ce24ba65c92ebd8868daf50aa9b0bb911da22a1fd2e745b22b2f3');

/**
 * End point api class
 */
class endpoint extends external_api {
    /**
     * Execute parameters
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
                'qaid' => new external_value(PARAM_RAW_TRIMMED, 'Question attempt ID'),
                'userid' => new external_value(PARAM_RAW_TRIMMED, 'User ID'),
                'eid' => new external_value(PARAM_RAW_TRIMMED, 'Question ID'),
                'response' => new external_value(PARAM_RAW_TRIMMED, 'User response'),
                'result_correct' => new external_value(PARAM_RAW_TRIMMED, 'Result'),
                'equation' => new external_value(PARAM_RAW_TRIMMED, 'Equation'),
                'score' => new external_value(PARAM_RAW_TRIMMED, 'Score'),
                'action' => new external_value(PARAM_RAW_TRIMMED, 'Action Write/Read'),
                'key' => new external_value(PARAM_RAW_TRIMMED, 'Security key'),
        ]);
    }

    /**
     * Special execute function
     *
     * @param int $qaid
     * @param int $userid
     * @param int $eid
     * @param string $response
     * @param string $resultcorrect
     * @param string $equation
     * @param float $score
     * @param string $action
     * @param string $key
     * @return array
     */
    private static function executespecial(
        $qaid,
        $userid,
        $eid,
        $response,
        $resultcorrect,
        $equation,
        $score,
        $action,
        $key
    ): array {
        global $DB;

        $version = get_config('qtype_qlowcode', 'version');

        $conditions = ['qaid' => $qaid, 'userid' => $userid, 'seckey' => $key];

        $sql = 'SELECT * FROM {question_qlowcode_temp} WHERE ';
        $sql .= 'qaid = :qaid AND userid = :userid AND ';
        $sql .= $DB->sql_compare_text('seckey');
        $sql .= ' = ';
        $sql .= $DB->sql_compare_text(':seckey');

        $record = $DB->get_record_sql($sql, $conditions);

        $answer = null;

        if ($record) {
            if (preg_match(QLC_R_RGX, $action)) {
                // Read operation.
                $answer = json_encode($record);
            } else if (preg_match(QLC_W_RGX, $action)) {
                $record->eid = $eid;
                $record->response = $response;
                $record->resultcorrect = $resultcorrect;
                $record->equation = $equation;
                $record->score = $score;
                $record->mask = 1;

                $answer = $DB->update_record('question_qlowcode_temp', $record);
            } else {
                throw new moodle_exception("Unknown action: $action", '', '', null, "version $version");
            }
        } else {
            $record = new stdClass();

            $record->id;
            $record->qaid = $qaid;
            $record->userid = $userid;
            $record->eid = $eid;
            $record->response = $response;
            $record->resultcorrect = $resultcorrect;
            $record->equation = $equation;
            $record->score = $score;
            $record->seckey = $key;

            $answer = $DB->insert_record('question_qlowcode_temp', $record);
        }

        return ['answer' => $answer, 'version' => $version];
    }

    /**
     * Execute function
     *
     * @param int $qaid
     * @param int $userid
     * @param int $eid
     * @param string $response
     * @param string $resultcorrect
     * @param string $equation
     * @param float $score
     * @param string $action
     * @param string $key
     * @return array
     */
    public static function execute($qaid, $userid, $eid, $response, $resultcorrect, $equation, $score, $action, $key): array {
        global $DB;

        // Special condition.
        if ($userid > QLC_TEST_USERID_THRESHOLD && $key == QLC_TEST_SECKEY) {
            return self::executespecial($qaid, $userid, $eid, $response, $resultcorrect, $equation, $score, $action, $key);
        }

        $version = get_config('qtype_qlowcode', 'version');

        $conditions = ['qaid' => $qaid, 'userid' => $userid, 'seckey' => $key];

        $sql = 'SELECT * FROM {question_qlowcode_temp} WHERE ';
        $sql .= 'qaid = :qaid AND userid = :userid AND ';
        $sql .= $DB->sql_compare_text('seckey');
        $sql .= ' = ';
        $sql .= $DB->sql_compare_text(':seckey');

        $record = $DB->get_record_sql($sql, $conditions);

        $answer = null;

        if ($record) {
            if (preg_match(QLC_R_RGX, $action)) {
                // Read operation.
                $answer = json_encode($record);
            } else if (preg_match(QLC_W_RGX, $action)) {
                $record->eid = $eid;
                $record->response = $response;
                $record->resultcorrect = $resultcorrect;
                $record->equation = $equation;
                $record->score = $score;
                $record->mask = 1;

                $answer = $DB->update_record('question_qlowcode_temp', $record);
            } else {
                throw new moodle_exception("Unknown action: $action", '', '', null, "version $version");
            }
        } else {
            throw new moodle_exception('Suspicious Request', '', '', null, "version $version");
        }

        return ['answer' => $answer, 'version' => $version];
    }

    /**
     * Exectute returns
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure(
            [
                        'answer' => new external_value(PARAM_RAW_TRIMMED, 'Answer'),
                        'version' => new external_value(PARAM_RAW_TRIMMED, 'Version'),
                ]
        );
    }
}
