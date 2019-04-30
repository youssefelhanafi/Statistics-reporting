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

require_once("{$CFG->libdir}/formslib.php");
require_once($CFG->dirroot.'/blocks/filtered_reporting/lib.php');
// Define the class used to display our form
class filtered_reporting_form extends moodleform {
 
    function definition() {
        global $DB;
        
        $mform =& $this->_form;
        $mform->addElement('header','displayinfo', get_string('textfields', 'block_filtered_reporting'));

        // add page title element.
        $mform->addElement('text', 'pagetitle', get_string('pagetitle', 'block_filtered_reporting'));
        $mform->setType('pagetitle', PARAM_RAW);
        $mform->addRule('pagetitle', null, 'required', null, 'client');
        
        // add display text field
        $mform->addElement('htmleditor', 'displaytext', get_string('displayedhtml', 'block_filtered_reporting'));
        $mform->setType('displaytext', PARAM_RAW);
        $mform->addRule('displaytext', null, 'required', null, 'client');
        
        // add filename selection.
        $mform->addElement('filepicker', 'filename', get_string('file'), null, array('accepted_types' => '*')); 
        
        // add picture fields grouping
        $mform->addElement('header', 'picfield', get_string('picturefields', 'block_filtered_reporting'), null, false); 
    
        // add display picture yes / no option
        $mform->addElement('selectyesno', 'displaypicture', get_string('displaypicture', 'block_filtered_reporting'));
        $mform->setDefault('displaypicture', 1);

        // add image selector radio buttons
        $images = block_filtered_reporting_images();
        $radioarray = array();
        for ($i = 0; $i < count($images); $i++) {
            $radioarray[] =& $mform->createElement('radio', 'picture', '', $images[$i], $i);
        }
        $mform->addGroup($radioarray, 'radioar', get_string('pictureselect', 'block_filtered_reporting'), array(' '), FALSE); 
    
        // add description field
        $attributes = array('size' => '50', 'maxlength' => '100');
        $mform->addElement('text', 'description', get_string('picturedesc', 'block_filtered_reporting'), $attributes);
        $mform->setType('description', PARAM_TEXT);
        
        // add optional grouping
        $mform->addElement('header', 'optional', get_string('optional', 'form'), null, false); 
    
        // add date_time selector in optional area
        $mform->addElement('date_selector', 'displaydate', get_string('displaydate', 'block_filtered_reporting'), array('optional' => true));
        $mform->setAdvanced('optional');

        // hidden elements
        $mform->addElement('hidden', 'blockid');
        $mform->addElement('hidden', 'courseid');
        $this->add_action_buttons();

        
    }
}