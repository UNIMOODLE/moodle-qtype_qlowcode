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

/**
 * This holds the definition of a particular question of this type.
 * If you load three questions from the question bank, then you will get three instances of
 * that class. This class is not just the question definition, it can also track the current
 * state of a question as a student attempts it through a question_attempt instance.
 */
class qtype_qlowcode_question extends question_graded_automatically_with_countback {
    /* it may make more sense to think of this as get expected data types */
    /**
     * @var $qaid
     */
    public $qaid;
    /**
     * @var $userid
     */
    public $userid;
    /**
     * @var $rightanswer
     */
    public $rightanswer;

    /**
     * Get expected data
     *
     * @return array
     */
    public function get_expected_data() {
        return ['qaId' => PARAM_RAW_TRIMMED, 'userId' => PARAM_RAW_TRIMMED];
    }

    /**
     * Start attempt
     *
     * @param question_attempt_step $step
     * @param int $variant
     * @return void
     */
    public function start_attempt(question_attempt_step $step, $variant) {
        /* there are 9 occurrances of this method defined in files called question.php a new install of Moodle
        so you are probably going to have to define it */
    }

    /**
     *  It is written to responsesummary field of
     *  the question_attempts table, and used in the
     *  the quiz responses report
     *
     * @param array $response
     * @return string|null
     */
    public function summarise_response(array $response) {
        global $DB;

        if (array_key_exists('qaId', $response) && array_key_exists('userId', $response)) {
            // Overrides fields.
            $this->qaId = $response['qaId'];
            $this->userId = $response['userId'];

            $sql = 'SELECT * FROM {question_qlowcode_temp} WHERE qaid = :qaid AND userid = :userid AND mask != :mask';
            $records = $DB->get_records_sql($sql, ['qaid' => $this->qaId, 'userid' => $this->userId, 'mask' => 0]);

            if ($records) {
                $responses = [];
                foreach ($records as $record) {
                    array_push($responses, $record->response);
                }
                return implode(',', $responses);
            }
        }
        return null;
    }

    /**
     * Is complete response
     *
     * @param array $response
     * @return bool|int
     */
    public function is_complete_response(array $response) {
        global $DB;

        if (array_key_exists('qaId', $response) && array_key_exists('userId', $response)) {
            // Overrides fields.
            $this->qaId = $response['qaId'];
            $this->userId = $response['userId'];

            $sql = 'SELECT COUNT(*) FROM {question_qlowcode_temp} WHERE qaid = :qaid AND userid = :userid AND mask != :mask';
            $count = $DB->count_records_sql($sql, ['qaid' => $this->qaId, 'userid' => $this->userId, 'mask' => 0]);

            return $count;
        }
        return false;
    }

    /**
     * Get validation error
     *
     * @param array $response
     * @return string
     */
    public function get_validation_error(array $response) {
        global $DB;

        if (array_key_exists('qaId', $response) && array_key_exists('userId', $response)) {
            // Overrides fields.
            $this->qaId = $response['qaId'];
            $this->userId = $response['userId'];
        }
        return '';
    }

    /**
     * if you are moving from viewing one question to another this will
     * discard the processing if the answer has not changed. If you don't
     * use this method it will constantantly generate new question steps and
     * the question will be repeatedly set to incomplete. This is a comparison of
     * the equality of two arrays.
     * Comment from base class:
     *
     * Use by many of the behaviours to determine whether the student's
     * response has changed. This is normally used to determine that a new set
     * of responses can safely be discarded.
     *
     * @param array $prevresponse the responses previously recorded for this question,
     *      as returned by {@link question_attempt_step::get_qt_data()}
     * @param array $newresponse the new responses, in the same format.
     * @return bool whether the two sets of responses are the same - that is
     *      whether the new set of responses can safely be discarded.
     */
    public function is_same_response(array $prevresponse, array $newresponse) {
        return false;
    }

    /**
     *  Return an answer that
     *  contains the a response that would get full marks.
     *  used in preview mode. If this doesn't return a
     *  correct value the button labeled "Fill in correct response"
     *  in the preview form will not work. This value gets written
     *  into the rightanswer field of the question_attempts table
     *  when a quiz containing this question starts.
     *
     * @return string|null
     */
    public function get_correct_response() {
        global $DB;

        // Use fields.
        if (isset($this->qaId) && isset($this->userId)) {
            $sql = 'SELECT * FROM {question_qlowcode_temp} WHERE qaid = :qaid AND userid = :userid AND mask != :mask';
            $records = $DB->get_records_sql($sql, ['qaid' => $this->qaId, 'userid' => $this->userId, 'mask' => 0]);

            if ($records) {
                $correctresponses = [];
                foreach ($records as $record) {
                    array_push($correctresponses, $record->resultcorrect);
                }
                return implode(',', $correctresponses);
            }
        }
        return null;
    }

    /**
     * Given a response, reset the parts that are wrong. Relevent in
     * interactive with multiple tries
     *
     * @param array $response a response
     * @return array a cleaned up response with the wrong bits reset.
     */
    public function clear_wrong_from_response(array $response) {
        return $response;
    }

    /**
     * Check file access
     *
     * @param question_attempt $qa
     * @param question_display_options $options
     * @param string $component
     * @param string $filearea
     * @param array $args
     * @param bool $forcedownload
     * @return bool
     */
    public function check_file_access(
        $qa,
        $options,
        $component,
        $filearea,
        $args,
        $forcedownload
    ) {
        if ($component == 'question' && $filearea == 'hint') {
            return $this->check_hint_file_access($qa, $options, $args);
        } else {
            return parent::check_file_access(
                $qa,
                $options,
                $component,
                $filearea,
                $args,
                $forcedownload
            );
        }
    }

    /**
     * Grade response
     *
     * @param array $response responses, as returned by
     *      {@link question_attempt_step::get_qt_data()}.
     * @return array (number, integer) the fraction, and the state.
     */
    public function grade_response(array $response) {
        global $DB;

        $fraction = 0;
        if (array_key_exists('qaId', $response) && array_key_exists('userId', $response)) {
            // Overrides fields.
            $this->qaId = $response['qaId'];
            $this->userId = $response['userId'];

            $sql = 'SELECT * FROM {question_qlowcode_temp} WHERE qaid = :qaid AND userid = :userid AND mask != :mask';
            $records = $DB->get_records_sql($sql, ['qaid' => $this->qaId, 'userid' => $this->userId, 'mask' => 0]);

            if ($records) {
                $count = 0;
                foreach ($records as $record) {
                    $fraction += $record->score;
                    $count++;
                }

                if ($count > 0) {
                    $fraction /= $count;
                }
            }
        }

        return [$fraction, question_state::graded_state_for_fraction($fraction)];
    }

    /**
     * Work out a final grade for this attempt, taking into account all the
     * tries the student made. Used in interactive behaviour once all
     * hints have been used.     *
     *
     * @param array $responses an array of arrays of the response for each try.
     * Each element of this array is a response array, as would be
     * passed to {@link grade_response()}. There may be between 1 and
     * $totaltries responses.
     * @param int $totaltries is the maximum number of tries allowed. Generally
     * not used in the implementation.
     * @return numeric the fraction that should be awarded for this
     * sequence of response.
     *
     */
    public function compute_final_grade($responses, $totaltries) {
        /*This method is typically where penalty is used.
        When questions are run using the 'Interactive with multiple
        tries or 'Adaptive mode' behaviour, so that the student will
        have several tries to get the question right, then this option
        controls how much they are penalised for each incorrect try.

        The penalty is a proportion of the total question grade, so if
        the question is worth three marks, and the penalty is 0.3333333,
        then the student will score 3 if they get the question right first
        time, 2 if they get it right second try, and 1 of they get it right
        on the third try.*/
        global $DB;

        $fractionref = 0.999999;
        $fractionmax = 0;
        $tries = 0;

        foreach ($responses as $response) {
            $fraction = 0;
            if (array_key_exists('qaId', $response) && array_key_exists('userId', $response)) {
                $sql = 'SELECT * FROM {question_qlowcode_temp} WHERE qaid = :qaid AND userid = :userid AND mask != :mask';
                $records = $DB->get_records_sql($sql, ['qaid' => $response['qaId'], 'userid' => $response['userId'], 'mask' => 0]);

                if ($records) {
                    $count = 0;
                    foreach ($records as $record) {
                        $fraction += $record->score;
                        $count++;
                    }

                    if ($count > 0) {
                        $fraction /= $count;
                    }
                }
            }

            $fractionmax = max($fractionmax, $fraction);
            if ($fractionmax < $fractionref) {
                $tries++;
            }
        }

        // Threshold reached, apply penalty.
        if ($fractionmax < $fractionref) {
            return $fractionmax;
        } else {
            // Ensure [0,1] interval.
            $filteredpenalty = min(1, max(0, $this->penalty));
            $discount = $fractionmax * $filteredpenalty * $tries;
            $fractionmax -= $discount;
        }

        return $fractionmax;
    }
}
