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
 * qlow question renderer class.
 *
 * @package    qtype
 * @subpackage qlow
 * @copyright  2023 ISYC

 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * Generates the output for qlow questions.
 *
 * @copyright  2023 ISYC

 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_qlow_renderer extends qtype_renderer
{
    public function formulation_and_controls(
        question_attempt $qa,
        question_display_options $options
    ) {
        global $CFG, $PAGE;
        $PAGE->requires->js(new moodle_url($CFG->wwwroot . '/question/type/qlow/javascript/jquery-3.7.0.min.js'));
        $PAGE->requires->js(new moodle_url($CFG->wwwroot . '/question/type/qlow/javascript/qlow.js'));

        // $qa->get_question()->rightanswer="Eur";
        global $currentanswerwithhint;
        require_once($CFG->dirroot . '/question/type/regexp/locallib.php');
        // $question = $qa->get_question();
        $inputname = $qa->get_qt_field_name('answer');
        // $ispreview = !isset($options->attempt);
        $currentanswer = remove_blanks($qa->get_last_qt_var('answer'));
        // $question = $qa->get_question();
        // $questiontext = $question->format_questiontext($qa);
        // $result = html_writer::tag('div', $questiontext, array('class' => 'qtext'));

        // $inputname = $qa->get_qt_field_name('answer');
        $inputattributes = array(
            'type' => 'text',
            'name' => $inputname,
            //$inputname,
            'value' => $currentanswer,
            //$currentanswer,
            'id' => $inputname,
            //$inputname,
            'size' => 20,
            'class' => 'form-control d-inline',
        );

        $iframe = '<iframe id="inlineFrameExample"
            // title="Inline Frame Example"
            // width="620"
            // frameBorder="0"
            // height="230"
            // src="' . $qa->get_question()->questionurl . '">
            // </iframe>';

        // src="https://app.appsmith.com/app/questionario-v4/pregunta2-6493031af79393336aa41086">
        // src="https://app.appsmith.com/app/questionario4/pregunta2-64372fe026013158789b124f?pk=MIGeMA0GCSqGSIb3DQEBAQUAA4GMADCBiAKBgHLM3bC4Bhxa1yljiHByu26S9gTdh23Z742FQbLEErlCzJiysEGx5TOE1TezQnxTMRLm0%2BMwn0mJuxVUzP38%2FleLxElWvkHQYKuJ%2FdFuLti%2BcnFe6MQI8zaVNPTI1XIxuFFFwSY93F3Wfgoz3TbU9M1hlRsCmDB4yYEjXPDJbKqhAgMBAAE%3D&embed=true">

        // http://localhost/questions/acirculo.html
        /* Some code to restore the state of the question as you move back and forth
        from one question to another in a quiz and some code to disable the input fields
        once a quesiton is submitted/marked */

        /* if ($qa->get_state() == question_state::$invalid) {
            $result .= html_writer::nonempty_tag('div',
                    $question->get_validation_error(array('answer' => $currentanswer)),
                    array('class' => 'validationerror'));
        }*/

        $iframe .= html_writer::empty_tag('input', $inputattributes);
        return $iframe;
    }

    public function specific_feedback(question_attempt $qa)
    {
        // TODO.
        return '';
    }

    public function correct_response(question_attempt $qa)
    {
        return 'La respuesta correcta es: (...)' . $qa->get_question()->get_correct_response()["answer"];
        // return $qa->get_question()->get_correct_response();
    }

    // public function combined_feedback(question_attempt $qa)
    // {
    //     exit();
    //     $question = $qa->get_question();

    //     $state = $qa->get_state();

    //     if (!$state->is_finished()) {
    //         $response = $qa->get_last_qt_data();
    //         if (!$qa->get_question()->is_gradable_response($response)) {
    //             return '';
    //         }
    //         list($notused, $state) = $qa->get_question()->grade_response($response);
    //     }

    //     $feedback = '';
    //     $field = $state->get_feedback_class() . 'feedback';
    //     $format = $state->get_feedback_class() . 'feedbackformat';
    //     if ($question->$field) {
    //         $feedback .= $question->format_text(
    //             $question->$field, $question->$format,
    //             $qa,
    //             'question',
    //             $field, $question->id
    //         );
    //     }

    //     return $feedback;
    // }
}