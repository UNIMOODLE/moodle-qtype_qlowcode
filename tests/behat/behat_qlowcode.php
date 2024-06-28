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

require_once(__DIR__ . '/../../../../../lib/behat/behat_base.php');

/**
 * Contains the helper class for the select missing words question type tests.
 */
class behat_qlowcode extends behat_base {
    /**
     * Click on autocomplete section
     *
     * @When I click the autocomplete selection
     */
    public function i_click_the_autocomplete_selection() {
        $page = $this->getSession()->getPage();

        // Encuentra el div por su clase.
        $autocompletediv = $page->find('css', '.form-autocomplete-selection');

        if (null === $autocompletediv) {
            throw new \Exception('Div with class "form-autocomplete-selection" not found');
        }

        // Hacer clic en el div.
        $autocompletediv->click();
    }

    /**
     * Click on element with class name
     * @When /^I click on the element with classname "([^"]*)"$/
     *
     * @param string $class
     */
    public function i_click_on_the_element_with_class_name($class) {
        $this->getSession()->getPage()->find('css', '.' . $class)->click();
    }
}
