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

// Write regular expression
define('QLC_W_RGX', '/(?i)^write$/');
// Read regular expression
define('QLC_R_RGX', '/(?i)^read$/');

class endpoint extends external_api
{
    public static function execute_parameters(): external_function_parameters
    {
        return new external_function_parameters([
            'qaid' => new external_value(PARAM_RAW_TRIMMED, 'Question attempt ID'),
            'userid' => new external_value(PARAM_RAW_TRIMMED, 'User ID'),
            'eid' => new external_value(PARAM_RAW_TRIMMED, 'Question ID'),
            'response' => new external_value(PARAM_RAW_TRIMMED, 'User response'),
            'result_correct' => new external_value(PARAM_RAW_TRIMMED, 'Result'),
            'equation' => new external_value(PARAM_RAW_TRIMMED, 'Equation'), 
            'score' => new external_value(PARAM_RAW_TRIMMED, 'Score'),
            'action' => new external_value(PARAM_RAW_TRIMMED, 'Action Write/Read'), 
        ]);
    }

    public static function execute($qaid, $userid, $eid, $response, $result_correct, $equation, $score, $action): array
    {
        global $DB;

        $conditions = array('qaid' => $qaid, 'userid' => $userid, 'eid' => $eid);
        $record = $DB->get_record('question_qlowcode_temp', $conditions);
        $result = null;

        if ($record) {

            if (preg_match(QLC_R_RGX, $action)) {
                // Read operation
                $result = json_encode($record);
            } else if (preg_match(QLC_W_RGX, $action)) {
                // Write (update) operation
                $record->response = $response;
                $record->resultcorrect = $result_correct;
                $record->equation = $equation;
                $record->score = $score;
    
                $result = $DB->update_record('question_qlowcode_temp', $record);
            } else {
                $result = "Unknown action: $action";
            }

        } else {

            if (preg_match(QLC_W_RGX, $action)) {
                // Write (insert) operation
                $record = new stdClass();

                $record->id;
                $record->qaid = $qaid;
                $record->userid = $userid;
                $record->eid = $eid;
                $record->response = $response;
                $record->resultcorrect = $result_correct;
                $record->equation = $equation;
                $record->score = $score;
    
                $result = $DB->insert_record('question_qlowcode_temp', $record);
            }
        }

        return ['answer' => $result];
    }

    public static function execute_returns(): external_single_structure
    {
        return new external_single_structure(
            [
                'answer' => new external_value(PARAM_RAW_TRIMMED, 'Answer'),
            ]
        );
    }
}
