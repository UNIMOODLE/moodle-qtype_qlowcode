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
 *  Plugin restore class
 */
class restore_qtype_qlowcode_plugin extends restore_qtype_plugin {
    /**
     * Returns the paths to be handled by the plugin at question level
     */
    protected function define_question_plugin_structure() {
        $paths = [];

        // Add own qtype stuff.
        $elename = 'qlowcode';
        $elepath = $this->get_pathfor('/qlowcode');
        $paths[] = new restore_path_element($elename, $elepath);

        // Add own qtype stuff.
        $elename = 'qlowcode_temp';
        $elepath = $this->get_pathfor('/qlowcode_temp');
        $paths[] = new restore_path_element($elename, $elepath);

        return $paths; // And we return the interesting paths.
    }

    /**
     * Process the qtype/qlowcode element
     *
     * @param object $data
     */
    public function process_qlowcode($data) {
        global $DB;

        $data = (object) $data;
        // Adjust some columns.
        $data->questionid = $this->get_mappingid('question', $data->questionid);
        // Insert record.
        $newitemid = $DB->insert_record('question_qlowcode', $data);
        // Create mapping (need it for after_execute recode of sequence).
        $this->set_mapping('question_qlowcode', $data->id, $newitemid);
    }

    /**
     * Process the qtype/qlowcode_temp element
     *
     * @param object $data
     */
    public function process_qlowcode_temp($data) {
        global $DB;

        $data = (object) $data;
        // Adjust some columns.
        $data->qaid = $this->get_mappingid('question_attempt', $data->qaid);
        // Insert record.
        $newitemid = $DB->insert_record('question_qlowcode_temp', $data);
        // Create mapping (need it for after_execute recode of sequence).
        $this->set_mapping('question_qlowcode_temp', $data->id, $newitemid);
    }

    /**
     * After restoring
     */
    public function after_restore_question() {
        global $DB;

        $records = $DB->get_records_sql("
               SELECT bi.itemid, bi.newitemid, qt.qaid
                FROM {backup_ids_temp} bi
                JOIN {question_qlowcode_temp} qt ON bi.itemid = qt.id
                 WHERE bi.backupid = :backupid
                   AND bi.itemname = 'question_qlowcode_temp'
                ", ['backupid' => $this->get_restoreid()]);

        foreach ($records as $record) {
            $newid = $this->get_mappingid('question_attempt', $record->qaid);
            $DB->update_record('question_qlowcode_temp', (object) ['id' => $record->newitemid, 'qaid' => $newid]);
        }
    }
}
