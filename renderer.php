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
 * qlowcode question renderer class.
 *
 * @package    qtype
 * @subpackage qlowcode
 * @copyright  2023 ISYC

 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/type/qlowcode/qlowcodelib.php');

/**
 * Generates the output for qlowcode questions.
 *
 * @copyright  2023 ISYC

 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_qlowcode_renderer extends qtype_renderer
{
    public function formulation_and_controls(
        question_attempt $qa,
        question_display_options $options
    ) {
        global $CFG, $PAGE, $USER, $DB;
        $PAGE->requires->js(new moodle_url($CFG->wwwroot . '/question/type/qlowcode/javascript/jquery-3.7.0.min.js'));
        $PAGE->requires->js(new moodle_url($CFG->wwwroot . '/question/type/qlowcode/javascript/qlowcode.js'));

        $lang = current_language();
        $qaId = $qa->get_database_id();
        $userId = $USER->id;

        // Aquire temp data

        $responses = array();
        $resultscorrect = array();
        $equations = array();
        $rightanswer = null;
        $records = $DB->get_records('question_qlowcode_temp', array(
            'qaid' => $qaId, 'userid' => $userId)
        );
        if ($records) {            
            foreach ($records as $record) {
                array_push($responses, $record->response);
                array_push($resultscorrect, $record->resultcorrect);
                $equations[$record->eid] = $record->equation;
            }

            // Force rightanswer update

            $qa_record = $DB->get_record('question_attempts', array('id' => $qaId));
            if ($qa_record) {
                $rightanswer = implode(',', $resultscorrect);
                $qa_record->rightanswer = $rightanswer;
                $DB->update_record('question_attempts', $qa_record);
            }            
        }        

        // passing data back to question class (overrated)

        $qa->get_question()->qaId = $qaId;
        $qa->get_question()->userId = $userId;
        $qa->get_question()->rightanswer = $rightanswer;
        
        // expected data

        $qaId_qt_field = $qa->get_qt_field_name('qaId');
        $qaIdAttributes = array(
            'type' => 'hidden',
            'name' =>  $qaId_qt_field,
            'id' => $qaId_qt_field,
            'value' => $qaId,
        );

        $userId_qt_field = $qa->get_qt_field_name('userId');
        $userIdAttributes = array(
            'type' => 'hidden',
            'name' =>  $userId_qt_field,
            'id' => $userId_qt_field,
            'value' => $userId,
        );

        // iframe payload

        $info = json_encode(
            array(
                'lang' => $lang,
                'response' => implode(',', $responses),
                'qaId' => $qaId,
                'userId' => $userId,
                'equations' => $equations,
            )
        );

        $infoAttributes = array(
            'type' => 'text',
            'name' => 'info',
            'value' => $info,
            'id' => "info",
            'size' => 20,
            'readonly' => 'readonly',
            'class' => 'form-control d-inline',
        );

        // additional http query parameters
        $http_query_data = array('userId' => $userId, 'qaId' => $qaId, 'embedded' => true);
        // iframe source
        $src = $qa->get_question()->questionurl . '?' . http_build_query($http_query_data);

        $iframeAttributes = array(
            'id' => 'inlineFrameExample',
            'title' => 'Inline Frame Example',
            'width' => '620',
            'height' => '230',
            'frameBorder' => '0',
            'src' => $src,
        );

        $iframe = html_writer::start_tag('iframe', $iframeAttributes);
        $iframe .= html_writer::empty_tag('input', $infoAttributes);
        $iframe .= html_writer::end_tag('iframe');

        $result = html_writer::empty_tag('input', $qaIdAttributes);
        $result .= html_writer::empty_tag('input', $userIdAttributes);
        $result .= $iframe;

        return $result;
    }

    public function specific_feedback(question_attempt $qa)
    {
        global $USER, $DB;

        $fraction = 0;
        $qaId = $qa->get_database_id();
        $userId = $USER->id;

        $records = $DB->get_records('question_qlowcode_temp', array(
            'qaid' => $qaId, 'userid' => $userId)
        );
        if ($records) { 

            $count = 0;           
            foreach ($records as $record) {
                $fraction += $record->score;
                $count += 1;
            }

            if ($count > 0) {
                $fraction /= $count; 
            }
        }

        if ($fraction > 0.99) {
            return $qa->get_question()->correctfeedback;
        } else if ($fraction < 0.01) {
            return $qa->get_question()->incorrectfeedback;
        } else {
            return $qa->get_question()->partiallycorrectfeedback;
        }

    }

    public function correct_response(question_attempt $qa)
    {

        global $USER, $DB;

        $qaId = $qa->get_database_id();
        $userId = $USER->id;
        
        $records = $DB->get_records('question_qlowcode_temp', array(
            'qaid' => $qaId, 'userid' => $userId)
        );

        $correct_responses = null;
        if ($records) {
            $correct_responses = array();
                       
            foreach ($records as $record) {
                array_push($correct_responses, $record->resultcorrect);
            }
            $correct_responses = implode(',', $correct_responses);
        }  
        
        return get_string("rightanswer", 'qtype_qlowcode') . ' : ' . $correct_responses;

    }

}