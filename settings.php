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
 * Plugin administration pages are defined here.
 *
 * @package     
 * @category    
 * @copyright   
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {

    // SECURITY SECTION

    $settings->add(new admin_setting_heading('qtype_qlowcode/security', 
        get_string('security', 'qtype_qlowcode'), 
        get_string('securityhelp', 'qtype_qlowcode')));

    $settings->add(new admin_setting_configtext('qtype_qlowcode/privatekey',
        get_string('privatekey', 'qtype_qlowcode'), get_string('privatekeyhelp', 'qtype_qlowcode'), 
        null));  
    
    $settings->add(new admin_setting_configtext('qtype_qlowcode/publickey',
        get_string('publickey', 'qtype_qlowcode'), get_string('publickeyhelp', 'qtype_qlowcode'), 
        null));     

    // QUESTIONNAIRE SECTION
        
    $settings->add(new admin_setting_heading('qtype_qlowcode/questionnaire', 
        get_string('questionnaire', 'qtype_qlowcode'), 
        get_string('questionnairehelp', 'qtype_qlowcode')));

    // QUESTIONNAIRES
    
    foreach (range(1, 5) as $number) {

        $settings->add(new admin_setting_description("qtype_qlowcode/questionnaire$number",
            get_string('questionnaire', 'qtype_qlowcode') . " $number", ''
        ));
        $settings->add(new admin_setting_configtext("qtype_qlowcode/description$number",
            get_string('description', 'qtype_qlowcode'), get_string('descriptionhelp', 'qtype_qlowcode'),
            null, PARAM_RAW_TRIMMED
        ));
        $settings->add(new qtype_qlowcode_admin_setting_configtext_url("qtype_qlowcode/url$number",
            get_string('url', 'qtype_qlowcode'), get_string('urlhelp', 'qtype_qlowcode'),
            null, 's+u-a+p-f?q-r-'
        ));

    }

}
