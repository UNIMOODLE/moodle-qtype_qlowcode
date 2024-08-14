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

/*https://docs.moodle.org/dev/Question_types#Question_type_and_question_definition_classes*/

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/questionlib.php');
require_once($CFG->libdir . '/validateurlsyntax.php');

require_once($CFG->dirroot . '/question/engine/lib.php');
require_once($CFG->dirroot . '/question/type/qlowcode/question.php');

/**
 * The qlowcode question type.
 */
class qtype_qlowcode extends question_type {
    /* ties additional table fields to the database */
    /**
     * Extra question fields
     *
     * @return string[]
     */
    public function extra_question_fields() {
        return [
                'question_qlowcode',
                'configurl',
                'applicationid',
                'applicationurl',
                'pageurl',
                'framewidth',
                'frameheight',
        ];
    }

    /**
     * Move files
     *
     * @param int $questionid
     * @param int $oldcontextid
     * @param int $newcontextid
     * @return void
     */
    public function move_files($questionid, $oldcontextid, $newcontextid) {
        parent::move_files($questionid, $oldcontextid, $newcontextid);
        $this->move_files_in_hints($questionid, $oldcontextid, $newcontextid);
    }

    /**
     *  Delete files
     *
     * @param int $questionid
     * @param int $contextid
     * @return void
     */
    protected function delete_files($questionid, $contextid) {
        parent::delete_files($questionid, $contextid);
        $this->delete_files_in_hints($questionid, $contextid);
    }

    /**
     * Save question options
     *
     * @param object $question
     * @return void
     */
    public function save_question_options($question) {
        global $DB;

        $applicationurl = null;
        if (isset($question->configurl) && isset($question->workspaceid) && isset($question->applicationid)) {
            $applications = qtype_qlowcode\ws\qlc_get_applications::get_applications($question->configurl, $question->workspaceid);
            if (!empty($applications)) {
                foreach ($applications as $application) {
                    if ($application["id"] == $question->applicationid) {
                        $applicationurl = $application["slug"];
                        break;
                    }
                }
            }
        }

        $options = $DB->get_record('question_qlowcode', ['questionid' => $question->id]);
        if (!$options) {
            $options = new stdClass();
            $options->questionid = $question->id;

            if (isset($question->configurl)) {
                $options->configurl = $question->configurl;
            }

            if (isset($question->workspaceid)) {
                $options->workspaceid = $question->workspaceid;
            }

            if (isset($question->applicationid)) {
                $options->applicationid = $question->applicationid;
            }

            if (isset($question->pageurl)) {
                $options->pageurl = $question->pageurl;
            }

            if (isset($question->framewidth)) {
                $options->framewidth = $question->framewidth;
            }

            if (isset($question->frameheight)) {
                $options->frameheight = $question->frameheight;
            }

            /* add any more non combined feedback fields here */

            $options->id = $DB->insert_record('question_qlowcode', $options);
        }
        $options->applicationurl = $applicationurl;
        $options = $this->save_combined_feedback_helper($options, $question, $question->context, true);
        $DB->update_record('question_qlowcode', $options);

        $this->save_hints($question);
    }

    /*
     * populates fields such as combined feedback
     * also make $DB calls to get data from other tables
     */
    /**
     * Get question options
     *
     * @param object $question
     * @return false|void
     */
    public function get_question_options($question) {
        parent::get_question_options($question);
        // Load combined feedback.
        global $DB, $OUTPUT;
        if (!$question->options = $DB->get_record('question_qlowcode', ['questionid' => $question->id])) {
            echo $OUTPUT->notification('Error: Missing question options!');
            return false;
        }
    }

    /**
     * executed at runtime (e.g. in a quiz or preview
     *
     * @param question_definition $question
     * @param object $questiondata
     * @return void
     */
    protected function initialise_question_instance(question_definition $question, $questiondata) {
        parent::initialise_question_instance($question, $questiondata);
        $this->initialise_question_answers($question, $questiondata);
        parent::initialise_combined_feedback($question, $questiondata);
    }

    /**
     * Initialize question
     *
     * @param question_definition $question
     * @param string $questiondata
     * @param bool $forceplaintextanswers
     * @return void
     */
    public function initialise_question_answers(question_definition $question, $questiondata, $forceplaintextanswers = true) {
    }

    /**
     * Import from xml
     *
     * @param array $data
     * @param object $question
     * @param qformat_xml $format
     * @param string $extra
     * @return false|object
     */
    public function import_from_xml($data, $question, qformat_xml $format, $extra = null) {
        if (!isset($data['@']['type']) || $data['@']['type'] != 'question_qlowcode') {
            return false;
        }
        $question = parent::import_from_xml($data, $question, $format, null);
        $format->import_combined_feedback($question, $data, true);
        $format->import_hints($question, $data, true, false, $format->get_format($question->questiontextformat));
        return $question;
    }

    /**
     * Export to xml
     *
     * @param object $question
     * @param qformat_xml $format
     * @param string $extra
     * @return string
     */
    public function export_to_xml($question, qformat_xml $format, $extra = null) {
        global $CFG;
        $pluginmanager = core_plugin_manager::instance();
        $gapfillinfo = $pluginmanager->get_plugin_info('question_qlowcode');
        $output = parent::export_to_xml($question, $format);
        $output .= $format->write_combined_feedback($question->options, $question->id, $question->contextid);
        return $output;
    }

    /**
     * Random guess score
     *
     * @param string $questiondata
     * @return int
     */
    public function get_random_guess_score($questiondata) {
        return 0;
    }

    /**
     * Possible responses
     *
     * @param string $questiondata
     * @return array
     */
    public function get_possible_responses($questiondata) {
        return [];
    }
}
