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
 * Admin settings for the multichoice question type.
 *
 * @package   qtype_multichoice
 * @copyright  2015 onwards Nadav Kavalerchik
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/validateurlsyntax.php');

/**
 * Admin settings class for the multichoice question type method.
 *
 * Just so we can lazy-load the numbering style choices.
 *
 * @copyright  2015 onwards Nadav Kavalerchik
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_qlowcode_admin_setting_configtext_url extends admin_setting_configtext {

    public function validate($data)
    {
        if (isset($data) && $data !== '') {
            if (validateUrlSyntax($data, $this->paramtype)) {
                return true;
            } else {
                return get_string('validateerror', 'admin');
            }
        }
        return true;
    }

}