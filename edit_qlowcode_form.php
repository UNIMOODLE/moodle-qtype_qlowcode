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
 * Defines the editing form for the qlowcode question type.
 *
 * @package    qtype
 * @subpackage qlowcode
 * @copyright  2023 ISYC

 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/type/qlowcode/classes/output/edit_qlowcode_form_base.php');
require_once($CFG->libdir . '/validateurlsyntax.php');


/**
 * qlowcode question editing form definition.
 *
 * @copyright  2023 ISYC

 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_qlowcode_edit_form extends qlowcode_edit_form
{

    protected function definition_inner($mform)
    {
        $questionnaire = null;
        $question = null;
        $framewidth = '100%';

        if (isset($this->question->options->questionurl)) {
            if (validateUrlSyntax($this->question->options->questionurl, 's+u-a+p-f+q-r-')) {
                // safe to do so
                $pieces = explode('/', $this->question->options->questionurl);
                $question = array_pop($pieces);
                $questionnaire = implode('/', $pieces);
            }
        }

        if (isset($this->question->options->framewidth)) {
            $framewidth = $this->question->options->framewidth;
        }        

        $items = array();
        // Loop over questionnaires. ALERT the exact number is hardcoded.
        foreach (range(1, 5) as $number) {
            $ithurl = get_config('qtype_qlowcode', "url$number");
            if ($ithurl && validateUrlSyntax($ithurl, 's+u-a+p-f?q-r-')) {
                $ithdescription = get_config('qtype_qlowcode', "description$number");
                if ($ithdescription) {
                    // some sanitation, experimental...
                    $ithdescription = trim(strval($ithdescription));
                    if ($ithdescription === '') {
                        $ithdescription = $ithurl;
                    }
                }

                $items[$ithurl] = $ithdescription;
            }
        }

        // Append current 'questionnaire' if not exist
        if (isset($questionnaire) && !array_key_exists($questionnaire, $items)) {
            $items = array_merge($items, [$questionnaire => $questionnaire]);
        }

        $select = $mform->addElement(
            'select',
            'questionnaire',
            get_string('questionnaire', 'qtype_qlowcode'),
            $items
        );
        $select->setSelected($questionnaire);

        $text = $mform->addElement(
            'text',
            'questionnairequestion',
            get_string('question', 'qtype_qlowcode'),
            array('size' => 50, 'maxlength' => 255, 'value' => $question)
        );
        $mform->setType('questionnairequestion', PARAM_RAW_TRIMMED);
        $mform->addHelpButton('questionnairequestion', 'question', 'qtype_qlowcode');
        $mform->addRule(
            'questionnairequestion',
            get_string('validateerror', 'admin'),
            'regex',
            '/^[a-zA-Z0-9_\-]+$/',
            'server',
            false,
            true
        );

        $iframewidth = $mform->addElement(
            'text', 
            'framewidth', 
            get_string('framewidth', 'qtype_qlowcode'), 
            array('size' => '20', 'value' => $framewidth)
        );
        $mform->setType('framewidth', PARAM_RAW_TRIMMED);
        $mform->addHelpButton('framewidth', 'framewidth', 'qtype_qlowcode');
        $mform->addRule(
            'framewidth', 
            get_string('validateerror', 'admin'), 
            'regex', 
            '/^(([0-9]+)|([0-9]+[\.][0-9]+))%?$/', 
            'server', 
            false, 
            true);
        $mform->setDefault('framewidth', '100%');

        //

        $this->add_combined_feedback_fields(true);
        $this->add_interactive_settings(true, true);
    }

    protected function data_preprocessing($question)
    {
        $question = parent::data_preprocessing($question);
        $question = $this->data_preprocessing_hints($question);

        $question = $this->data_preprocessing_combined_feedback($question);

        return $question;
    }

    public function qtype()
    {
        return 'qlowcode';
    }
}