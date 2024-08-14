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

use qtype_qlowcode\constants;

/**
 * Edit form class
 */
class qtype_qlowcode_edit_form extends question_edit_form {
    /**
     * Form inner definition
     *
     * @param QuickformForm $mform
     * @return void
     */
    protected function definition_inner($mform) {
        global $PAGE;

        // We don't need this default element.
        $mform->removeElement('questiontext');

        $PAGE->requires->js_call_amd('qtype_qlowcode/qlc', 'init');

        $configurl = null;
        $workspaceid = null;
        $applicationid = null;
        $pageurl = null;
        $framewidth = 'auto';
        $frameheight = 'auto';

        if (isset($this->question->options->configurl)) {
            $configurl = $this->question->options->configurl;
        }

        if (isset($this->question->options->workspaceid)) {
            $workspaceid = $this->question->options->workspaceid;
        }

        if (isset($this->question->options->applicationid)) {
            $applicationid = $this->question->options->applicationid;
        }

        if (isset($this->question->options->pageurl)) {
            $pageurl = $this->question->options->pageurl;
        }

        if (isset($this->question->options->framewidth)) {
            $framewidth = $this->question->options->framewidth;
        }

        if (isset($this->question->options->frameheight)) {
            $frameheight = $this->question->options->frameheight;
        }

        $configurlselected = $configurl ?? constants::QLOW_DEFAULT_REPOSITORY;

        // Current url number from config.
        foreach (range(1, constants::QLOW_NUMBER_REPOSITORY) as $number) {
            $ithdescription = get_config('qtype_qlowcode', "description$number");
            $qlowurl = get_config('qtype_qlowcode', "qlowurl$number");
            $apiurl = get_config('qtype_qlowcode', "apiurl$number");
            if ($ithdescription && $qlowurl && $apiurl) {
                $items[$number] = $ithdescription;
            }
        }

        $select = $mform->addElement(
            'select',
            'configurl',
            get_string('configurl', 'qtype_qlowcode'),
            $items
        );
        $select->setSelected($configurlselected);
        $mform->addRule(
            'configurl',
            get_string('required', 'qtype_qlowcode'),
            'required'
        );

        $workspaces = qtype_qlowcode\ws\qlc_get_workspaces::get_workspaces($configurlselected);
        if (!empty($workspaces)) {
            foreach ($workspaces as $workspace) {
                $selectworkspaces[$workspace["_id"]] = $workspace["name"];
            }
        }

        $select = $mform->addElement(
            'select',
            'workspaceid',
            get_string('workspaceid', 'qtype_qlowcode'),
            $selectworkspaces ?? []
        );
        $select->setSelected($workspaceid);
        $mform->addRule(
            'workspaceid',
            get_string('required', 'qtype_qlowcode'),
            'required'
        );

        $applications = qtype_qlowcode\ws\qlc_get_applications::get_applications($configurlselected, $workspaceid);
        if (!empty($applications)) {
            foreach ($applications as $application) {
                $selectapplications[$application["id"]] = $application["name"];
            }
        }

        $select = $mform->addElement(
            'select',
            'applicationid',
            get_string('applicationid', 'qtype_qlowcode'),
            $selectapplications ?? []
        );
        $select->setSelected($applicationid);
        $mform->addRule(
            'applicationid',
            get_string('required', 'qtype_qlowcode'),
            'required'
        );

        $pages = qtype_qlowcode\ws\qlc_get_pages::get_pages($configurlselected, $applicationid);
        if (!empty($pages)) {
            foreach ($pages as $page) {
                $selectpages[$page["id"]] = $page["name"];
            }
        }
        $select = $mform->addElement(
            'select',
            'pageurl',
            get_string('pageurl', 'qtype_qlowcode'),
            $selectpages ?? []
        );
        $select->setSelected($pageurl);
        $mform->addRule(
            'pageurl',
            get_string('required', 'qtype_qlowcode'),
            'required'
        );

        $mform->addElement(
            'text',
            'frameheight',
            get_string('frameheight', 'qtype_qlowcode'),
            ['size' => '20', 'value' => $frameheight]
        );
        $mform->setType('frameheight', PARAM_RAW_TRIMMED);
        $mform->addHelpButton('frameheight', 'frameheight', 'qtype_qlowcode');
        $mform->addRule(
            'frameheight',
            get_string('validateerror', 'admin'),
            'callback',
            'qlc_utils::validate_css_size',
            'server',
            false,
            true
        );

        $this->add_combined_feedback_fields(true);
        $this->add_interactive_settings(true, true);
    }

    /**
     * Set form data
     *
     * @param string $question
     * @return void
     */
    public function set_data($question) {
        question_bank::get_qtype($question->qtype)->set_default_options($question);

        // Prepare general feedback.
        $draftid = file_get_submitted_draft_itemid('generalfeedback');

        if (empty($question->generalfeedback)) {
            $generalfeedback = $this->_form->getElement('generalfeedback')->getValue();
            $question->generalfeedback = $generalfeedback['text'];
        }

        $feedback = file_prepare_draft_area(
            $draftid,
            $this->context->id,
            'question',
            'generalfeedback',
            empty($question->id) ? null : (int) $question->id,
            $this->fileoptions,
            $question->generalfeedback
        );
        $question->generalfeedback = [];
        $question->generalfeedback['text'] = $feedback;
        $question->generalfeedback['format'] = empty($question->generalfeedbackformat) ?
                editors_get_preferred_format() : $question->generalfeedbackformat;
        $question->generalfeedback['itemid'] = $draftid;

        // Remove unnecessary trailing 0s form grade fields.
        if (isset($question->defaultgrade)) {
            $question->defaultgrade = 0 + $question->defaultgrade;
        }
        if (isset($question->penalty)) {
            $question->penalty = 0 + $question->penalty;
        }

        // Set any options.
        $extraquestionfields = question_bank::get_qtype($question->qtype)->extra_question_fields();
        if (is_array($extraquestionfields) && !empty($question->options)) {
            array_shift($extraquestionfields);
            foreach ($extraquestionfields as $field) {
                if (property_exists($question->options, $field)) {
                    $question->$field = $question->options->$field;
                }
            }
        }

        // Subclass adds data_preprocessing code here.
        $question = $this->data_preprocessing($question);

        moodleform::set_data($question);
    }

    /**
     * Get form data
     *
     * @return object|null
     */
    public function get_data() {
        $data = parent::get_data();

        if (!empty($data)) {
            $mform =& $this->_form;

            // Add the configurl properly to the $data object.
            if (!empty($mform->_submitValues['configurl'])) {
                $data->configurl = $mform->_submitValues['configurl'];
            }

            // Add the applicationid properly to the $data object.
            if (!empty($mform->_submitValues['applicationid'])) {
                $data->applicationid = $mform->_submitValues['applicationid'];
            }

            // Add the pageurl properly to the $data object.
            if (!empty($mform->_submitValues['pageurl'])) {
                $data->pageurl = $mform->_submitValues['pageurl'];
            }
        }

        return $data;
    }

    /**
     * Qlowcode type
     *
     * @return string
     */
    public function qtype() {
        return 'qlowcode';
    }
}
