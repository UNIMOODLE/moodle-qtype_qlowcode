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

use qbank_previewquestion\question_preview_options;
use qtype_qlowcode\constants;
use qtype_qlowcode\utils\qlc_utils;

/**
 * Renderer for qlowcode
 */
class qtype_qlowcode_renderer extends qtype_renderer {
    /**
     * Controls and formulation
     *
     * @param question_attempt $qa
     * @param question_display_options $options
     * @return string
     */
    public function formulation_and_controls(
        question_attempt $qa,
        question_display_options $options
    ) {
        global $CFG, $USER, $DB;

        $this->page->requires->js(new moodle_url('/question/type/qlowcode/javascript/jquery-3.7.0.min.js'));
        $this->page->requires->js(new moodle_url('/question/type/qlowcode/javascript/qlowcode.js'));

        $lang = current_language();
        $qaid = $qa->get_database_id();
        $userid = $USER->id;
        $display = null;
        $key = null;

        // ... https://wimski.org/api/4.0/d8/d05/classquestion__display__options.html#details
        // Classes extending question_display_options

        if ($options instanceof question_preview_options) {
            $display = 'preview';
        }
        if ($options instanceof mod_quiz_display_options) {
            // Switch to 'review' if 'readonly' field on. Check comments from parent class 'question_display_options'.
            $display = $options->readonly ? 'review' : 'attempt';
        }

        // Aquire temp data.

        $responses = [];
        $resultscorrect = [];
        $equations = [];
        $rightanswer = null;

        $records = $DB->get_records('question_qlowcode_temp', [
                'qaid' => $qaid, 'userid' => $userid,
        ]);

        if ($records) {
            foreach ($records as $record) {
                // Override, all should be the same.
                $key = $record->seckey;

                array_push($responses, $record->response);
                array_push($resultscorrect, $record->resultcorrect);
                $equations[$record->eid] = $record->equation;
            }

            // Force rightanswer update.

            $qarecord = $DB->get_record('question_attempts', ['id' => $qaid]);
            if ($qarecord) {
                $rightanswer = implode(',', $resultscorrect);
                $qarecord->rightanswer = $rightanswer;
                $DB->update_record('question_attempts', $qarecord);
            }
        } else {
            $key = qlc_utils::generate_key();

            $record = new stdClass();
            $record->id = null;
            $record->qaid = $qaid;
            $record->userid = $userid;
            $record->eid = -1;
            $record->response = null;
            $record->resultcorrect = null;
            $record->equation = null;
            $record->score = -1;
            $record->seckey = $key;
            $record->mask = 0;

            $result = $DB->insert_record('question_qlowcode_temp', $record);
        }

        // Passing data back to question class (overrated).

        $qa->get_question()->qaId = $qaid;
        $qa->get_question()->userId = $userid;
        $qa->get_question()->rightanswer = $rightanswer;

        // Expected data.

        $qaidqtfield = $qa->get_qt_field_name('qaId');
        $qaidattributes = [
                'type' => 'hidden',
                'name' => $qaidqtfield,
                'id' => $qaidqtfield,
                'value' => $qaid,
        ];

        $useridqtfield = $qa->get_qt_field_name('userId');
        $useridattributes = [
                'type' => 'hidden',
                'name' => $useridqtfield,
                'id' => $useridqtfield,
                'value' => $userid,
        ];

        // Iframe payload.
        $info = json_encode(
            [
                        'lang' => $lang,
                        'display' => $display,
                        'response' => implode(',', $responses),
                        'qaId' => $qaid,
                        'userId' => $userid,
                        'equations' => $equations,
                        'key' => $key,
                ]
        );

        $infoattributes = [
                'type' => 'text',
                'name' => 'info',
                'value' => $info,
                'id' => "info",
                'size' => 20,
                'readonly' => 'readonly',
                'class' => 'form-control d-inline',
        ];

        // Additional http query parameters.
        $httpquerydata = [
                'userId' => $userid,
                'qaId' => $qaid,
                'embedded' => true,
                'display' => $display,
                'key' => $key,
        ];
        // Iframe source.
        $src = "";

        if (isset($qa->get_question()->configurl)) {
            $configurl = $qa->get_question()->configurl;
            $url = get_config('qtype_qlowcode', "qlowurl$configurl");
            $src = rtrim($url, '/') . constants::QLOW_URL_APP;
        }

        if (isset($qa->get_question()->applicationurl)) {
            $src .= $qa->get_question()->applicationurl . '/';
        }

        if (isset($qa->get_question()->pageurl)) {
            $src .= $qa->get_question()->pageurl;
        }

        $src .= '?';
        $src .= http_build_query($httpquerydata, '', '&');

        // Must be acquire from 'qtype_qlowcode'.
        $iframewidth = 'auto';
        if (isset($qa->get_question()->framewidth)) {
            $iframewidth = $qa->get_question()->framewidth;
        }

        $iframeheight = 'auto';
        if (isset($qa->get_question()->frameheight)) {
            $iframeheight = $qa->get_question()->frameheight;
        }

        $iframeattributes = [
                'id' => 'inlineFrameExample',
                'title' => 'Inline Frame Example',
                'width' => '100%',
                'height' => $iframeheight,
                'frameBorder' => '0',
                'src' => $src,
        ];

        $iframe = html_writer::start_tag('iframe', $iframeattributes);
        $iframe .= html_writer::empty_tag('input', $infoattributes);
        $iframe .= html_writer::end_tag('iframe');

        $result = html_writer::empty_tag('input', $qaidattributes);
        $result .= html_writer::empty_tag('input', $useridattributes);
        $result .= $iframe;

        return $result;
    }

    /**
     * Specific feedback
     *
     * @param question_attempt $qa
     * @return string
     */
    public function specific_feedback(question_attempt $qa) {
        global $USER, $DB;

        $fraction = 0;
        $qaid = $qa->get_database_id();
        $userid = $USER->id;

        $sql = 'SELECT * FROM {question_qlowcode_temp} WHERE qaid = ? AND userid = ? AND mask != ?;';
        $records = $DB->get_records_sql($sql, [$qaid, $userid, 0]);

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

    /**
     * Correct response
     *
     * @param question_attempt $qa
     * @return string
     */
    public function correct_response(question_attempt $qa) {

        global $USER, $DB;

        $qaid = $qa->get_database_id();
        $userid = $USER->id;

        $sql = 'SELECT * FROM {question_qlowcode_temp} WHERE qaid = ? AND userid = ? AND mask != ?;';
        $records = $DB->get_records_sql($sql, [$qaid, $userid, 0]);

        $correctresponses = null;
        if ($records) {
            $correctresponses = [];

            foreach ($records as $record) {
                array_push($correctresponses, $record->resultcorrect);
            }
            $correctresponses = implode(',', $correctresponses);
        }

        return get_string("rightanswer", 'qtype_qlowcode') . ' : ' . $correctresponses;
    }
}
