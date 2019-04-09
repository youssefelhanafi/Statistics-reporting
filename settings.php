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
 * filtered_reporting block
 *
 * @package    block_filtered_reporting
 * @author     Youssef Elhanafi <ysf.elhanafi@gmail.com>
 * @copyright  2019 Youssef Elhanafi 
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$settings->add(new admin_setting_heading(
    'headerconfig',
    get_string('headerconfig', 'block_filtered_reporting'),
    get_string('descconfig', 'block_filtered_reporting')
));

$settings->add(new admin_setting_configcheckbox(
    'filtered_reporting/Set_Background',
    get_string('labelsetbackground', 'block_filtered_reporting'),
    get_string('descsetbackground', 'block_filtered_reporting'),
    '0'
));