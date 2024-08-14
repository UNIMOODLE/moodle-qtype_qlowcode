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

namespace qtype_qlowcode;

use Generator;
use qtype_qlowcode\utils\qlc_utils;
use qtype_qlowcode\ws\qlc_get_workspaces;
use qtype_qlowcode\ws\qlc_get_applications;
use qtype_qlowcode\ws\qlc_get_pages;

/**
 * Check util functions works successfully
 *
 */
class utils_test extends \advanced_testcase {
    /**
     * Api url
     */
    public const APIURL = 'apiurl';
    /**
     * Api token
     */
    public const APITOKEN = 'apitoken';

    /**
     * Plugin space
     */
    public const PLUGIN = "qtype_qlowcode";

    /**
     * Default appsmith values
     */
    public const DEFAULT_WORKSPACE = 1;
    /**
     * Default application value
     */
    public const DEFAULT_APPLICATION = 1;
    /**
     * Default page
     */
    public const DEFAULT_PAGE = 1;

    public function setUp(): void {
        parent::setUp();
        self::setAdminUser();

        $apiurl = self::APIURL;
        $apitoken = self::APITOKEN;

        $arguments = $this->getProvidedData();
        foreach ($arguments as $key => $value) {
            $$key = $value;
        }

        $this->resetAfterTest(true);

        set_config('apiurl' . constants::QLOW_DEFAULT_REPOSITORY, $apiurl, self::PLUGIN);
        set_config('apitoken' . constants::QLOW_DEFAULT_REPOSITORY, $apitoken, self::PLUGIN);
    }

    /**
     * Data provider for execute
     */
    public static function dataprovider(): Generator {
        yield ['apiurl' => self::APIURL, 'apitoken' => self::APITOKEN, 'expected' => 'Could not resolve host: ' . self::APIURL];
    }

    /**
     * Check it generate a key
     *
     * @covers \qtype_qlowcode\utils\qlc_utils::generate_key
     *
     */
    public function test_generatekey() {
        $this->assertIsString(qlc_utils::generate_key());
    }

    /**
     * Check util functions works successfully
     *
     * @param string $value
     * @param bool $expected
     *
     *
     * @covers       \qtype_qlowcode\utils\qlc_utils::validate_css_size
     *
     * @dataProvider dataprovidercsssize
     */
    public function test_validatecsssize($value, $expected) {
        $this->assertSame(qlc_utils::validate_css_size($value), $expected);
    }

    /**
     * Data provider for css size validator
     *
     * @return array[]
     */
    public static function dataprovidercsssize(): array {
        return [
                'valid ' => ['auto', true],
                'invalid' => ['a', false],
        ];
    }

    /**
     * Check util functions for create user successfully
     *
     * @param array ...$parameters
     * @covers       \qtype_qlowcode\utils\qlc_utils::api_create_user
     *
     * @dataProvider dataprovider
     */
    public function test_createuser(...$parameters) {
        $expected = $parameters[2];
        $this->assertEquals($expected, qlc_utils::api_create_user("1234"));
    }

    /**
     * Check generate url
     *
     * @covers \qtype_qlowcode\utils\qlc_utils::generate_url
     *
     */
    public function test_generateurl() {
        $this->assertIsString(qlc_utils::generate_url("1234"));
    }

    /**
     * Check get workspaces
     *
     * @covers \qtype_qlowcode\ws\qlc_get_workspaces::get_workspaces
     *
     */
    public function test_getworkspaces() {
        $this->assertIsArray(qlc_get_workspaces::get_workspaces(constants::QLOW_DEFAULT_REPOSITORY));
    }

    /**
     * Check get applications
     *
     * @covers \qtype_qlowcode\ws\qlc_get_applications::get_applications
     *
     */
    public function test_getapplications() {
        $this->assertIsArray(qlc_get_applications::get_applications(constants::QLOW_DEFAULT_REPOSITORY, self::DEFAULT_WORKSPACE));
    }

    /**
     * Check get pages
     *
     * @covers \qtype_qlowcode\ws\qlc_get_pages::get_pages
     *
     */
    public function test_getpages() {
        $this->assertIsArray(qlc_get_pages::get_pages(constants::QLOW_DEFAULT_REPOSITORY, self::DEFAULT_APPLICATION));
    }
}
