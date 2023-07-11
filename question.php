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
 * qlow question definition class.
 *
 * @package    qtype
 * @subpackage qlow
 * @copyright  2023 ISYC

 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


//--------------------------------------
include($CFG->dirroot . '/question/type/qlow/phpseclib/Crypt/RSA.php');
include($CFG->dirroot . '/question/type/qlow/phpseclib/Math/BigInteger.php');

// require_once("Crypt\RSA.php");
//--------------------------------------

/** 
 *This holds the definition of a particular question of this type. 
 *If you load three questions from the question bank, then you will get three instances of 
 *that class. This class is not just the question definition, it can also track the current
 *state of a question as a student attempts it through a question_attempt instance. 
 */


/**
 * Represents a qlow question.
 *
 * @copyright  2023 ISYC

 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_qlow_question extends question_graded_automatically_with_countback
{ /* it may make more sense to think of this as get expected data types */
    // public $rightanswer;

    public $rightanswer;
    public function get_expected_data()
    {
        return array('answer' => PARAM_RAW_TRIMMED);
    }

    public function start_attempt(question_attempt_step $step, $variant)
    {
        //TODO
        /* there are 9 occurrances of this method defined in files called question.php a new install of Moodle
        so you are probably going to have to define it */
    }

    /**
     * @return summary 
     * string that summarises how the user responded. 
     * It is written to responsesummary field of
     * the question_attempts table, and used in the
     * the quiz responses report
     * */
    public function summarise_response(array $response)
    {
        // TODO
        if (array_key_exists('answer', $response)) {
            $textPlain = $this->decrypt_answer($response["answer"]);

            if (!is_null($textPlain)) {
                $json_response = json_decode($textPlain);

                if (!is_null($json_response)) {
                    return $json_response->response;
                }
            }
        }

        return null;
    }

    public function is_complete_response(array $response)
    {
        // TODO.
        /* You might want to check that the user has done something
            before returning true, e.g. clicked a radio button or entered some 
            text 
            */

        if (array_key_exists('answer', $response)) {
            $textPlain = $this->decrypt_answer($response["answer"]);
            $json_response = json_decode($textPlain);
            if (!is_null($json_response)) {
                return boolval($json_response->completeResponse);
            }
        }

        return false;

        // return true;
    }

    public function get_validation_error(array $response)
    {
        // TODO.
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

    public function is_same_response(array $prevresponse, array $newresponse)
    {
        // TODO.
        return $prevresponse === $newresponse;
    }

    /**
     * @return question_answer an answer that
     * contains the a response that would get full marks.
     * used in preview mode. If this doesn't return a 
     * correct value the button labeled "Fill in correct response"
     * in the preview form will not work. This value gets written
     * into the rightanswer field of the question_attempts table
     * when a quiz containing this question starts.
     */
    public function get_correct_response()
    {
        $rightanswer = array("answer" => $this->rightanswer);
        return $rightanswer;
    }
    /**
     * Given a response, reset the parts that are wrong. Relevent in
     * interactive with multiple tries
     * @param array $response a response
     * @return array a cleaned up response with the wrong bits reset.
     */
    public function clear_wrong_from_response(array $response)
    {
        foreach ($response as $key => $value) {
            /*clear the wrong response/s*/
        }
        return $response;
    }

    public function check_file_access(
        $qa,
        $options,
        $component,
        $filearea,
        $args,
        $forcedownload
    ) {
        // TODO.
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
     * @param array $response responses, as returned by
     *      {@link question_attempt_step::get_qt_data()}.
     * @return array (number, integer) the fraction, and the state.
     */

    public static function decrypt_answer($answer)
    {
        if (!is_null($answer)) {
            // the key here is temporary------------------------------------
            $privateKey = "-----BEGIN RSA PRIVATE KEY-----MIICWgIBAAKBgHLM3bC4Bhxa1yljiHByu26S9gTdh23Z742FQbLEErlCzJiysEGx5TOE1TezQnxTMRLm0+Mwn0mJuxVUzP38/leLxElWvkHQYKuJ/dFuLti+cnFe6MQI8zaVNPTI1XIxuFFFwSY93F3Wfgoz3TbU9M1hlRsCmDB4yYEjXPDJbKqhAgMBAAECgYBRkas7c6Yz43/aErTRYVQ4Pwe7cURXE3EY10RVJug+5m3FWcHPC/3VW168kwx8lgfabFTFqrijYc+iWnzFQ0vcKPFi0JrjgR4PwA3XKWDdSPM7j4E6awcA5dGQFCrGfWNDMxsaBStOcZR4yYKDb/Y5sxRFfshpxjKlX+ZTBE5R+QJBAK8HehmblLLuilioga779S8zdBIAMnfUaDgRAGLu+eTRIVWlsJirCVBTWritjLYuD0fVeOIKeEXGlW6aqhIAeusCQQCn6Ine17uS1+gupxYz224LYN+qGVfwncoarBje1Mk+6yoNsYxQ13U9o0GYhmwJ/IK92e44AbnsEYN9uAYSk3WjAkBlSehpBVYKLm01XV6fCwQaqqYS/LY4Dl25hG06050dw8CMtfP6hZBAQdyQXy69Bu6k3W61MOXlS0SS20JsZIa9AkBrBYC7FO5tzkgjVESGkRo3DmwBU14F88zZ609+2EndXK7VQ5GYBXyo6OHqgeNjChubPsjj0dXbbd5Nx3m3ZV3ZAkBIK7GLX8F1wgWNkDR4AZ7yb14JB8zhRJuXz5mS6baJZPO5W4uRQLgcN/Y9WU6mAV3xohfnOixzBfP3mwfghINO-----END RSA PRIVATE KEY-----";
            //--------------------------------------------------------------

            // decrypt
            $rsa = new Crypt_RSA();
            $rsa->loadKey($privateKey); // $key->loadKey(file_get_contents('private_key_file'));
            $rsa->setEncryptionMode(CRYPT_RSA_ENCRYPTION_PKCS1);
            $cipherText = base64_decode($answer);

            return $rsa->decrypt($cipherText);
        }

        return null;
    }

    public function grade_response(array $response)
    {
        $fraction = 0.0;
        if (array_key_exists('answer', $response)) {
            $textPlain = $this->decrypt_answer($response["answer"]);
            $json_response = json_decode($textPlain);

            if (!is_null($json_response)) {
                $fraction = $json_response->fraction;
            }
        }

        return array($fraction, question_state::graded_state_for_fraction($fraction));
    }

    /**
     * Work out a final grade for this attempt, taking into account all the
     * tries the student made. Used in interactive behaviour once all
     * hints have been used.     * 
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
    public function compute_final_grade($responses, $totaltries)
    {
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
        //TODO

        $fraction_ref = 0.999999;
        $fraction_max = 0;
        $tries = 0;

        // Count attempts before exceeding the threshold
        foreach ($responses as $response) {
            $textPlain = $this->decrypt_answer($response["answer"]);
            $json_response = json_decode($textPlain);
            if (!is_null($json_response)) {
                $fraction = $json_response->fraction;

                // bookkeeping within threshold
                if ($fraction_max < $fraction_ref) {
                    if ($fraction < $fraction_ref) {
                        $tries++;
                    }
                }

                $fraction_max = max($fraction_max, $fraction);
            }
        }

        // threshold reached, apply penalty
        if ($fraction_max > $fraction_ref) {
            // ensure [0,1] interval
            $filtered_penalty = min(1, max(0, $this->penalty));
            if ($filtered_penalty > 0 && $tries > 1) {
                $discount = $fraction_max * $filtered_penalty * $tries;
                $fraction_max -= $discount;

            }
        }

        return $fraction_max;
    }
}