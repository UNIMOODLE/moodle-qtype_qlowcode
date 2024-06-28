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
 * @category   string
 * @copyright  2023 Proyecto UNIMOODLE
 * @author     UNIMOODLE Group (Coordinator) <direccion.area.estrategia.digital@uva.es>
 * @author     ISYC <soporte@isyc.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 *  Plugin backup class
 */
class backup_qtype_qlowcode_plugin extends backup_qtype_plugin {
    /**
     * Returns the qtype information to attach to question element
     */
    protected function define_question_plugin_structure() {

        // Define the virtual plugin element with the condition to fulfill.
        $plugin = $this->get_plugin_element(null, '../../qtype', 'qlowcode');

        // Create one standard named plugin element (the visible container).
        $pluginwrapper = new backup_nested_element($this->get_recommended_name());

        // Connect the visible container ASAP.
        $plugin->add_child($pluginwrapper);

        // Define the elements.
        $qlowcode = new backup_nested_element('qlowcode', ['id'], [
                'questionid', 'configurl', 'workspaceid', 'applicationid',
                'applicationurl', 'pageurl', 'framewidth', 'frameheight',
                'correctfeedback', 'correctfeedbackformat', 'partiallycorrectfeedback',
                'partiallycorrectfeedbackformat', 'incorrectfeedback', 'incorrectfeedbackformat']);

        // Build the tree.
        $pluginwrapper->add_child($qlowcode);

        // Now create the qtype own structures.
        $qlowcodetemp = new backup_nested_element('qlowcode_temp', ['id'], [
                'qaid', 'userid', 'eid', 'score', 'response', 'equation', 'resultcorrect', 'seckey', 'mask']);

        // Now the own qtype tree.
        $pluginwrapper->add_child($qlowcodetemp);

        // Set the sources.
        $qlowcode->set_source_table('question_qlowcode', ['questionid' => backup::VAR_PARENTID], 'id ASC');

        // Rule source.
        $qlowcodetemp->set_source_sql(
            '
                SELECT qt.*
                   FROM {question_qlowcode_temp} qt
                   JOIN {question_attempts} qa on qt.qaid = qa.id
                WHERE qa.questionid = ?
            ',
            [
                        backup::VAR_PARENTID,
                ]
        );
        // Don't need to annotate ids nor files.

        return $plugin;
    }
}
