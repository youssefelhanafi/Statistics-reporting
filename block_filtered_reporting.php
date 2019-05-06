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


/**
 * filtered_reporting block
 *
 * @package    block_filtered_reporting
 * @copyright  2019 Youssef Elhanafi 
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


class block_filtered_reporting extends block_list { // block class definition
    
    public function init() { // This is essential for all blocks, and its purpose is to give values to any class member variables that need instantiating. 
        $this->title = get_string('filtered_reporting', 'block_filtered_reporting');
    }

    /* public function get_content() {
        if ($this->content !== null) {
          return $this->content;
        }
     
        $this->content         =  new stdClass;
        $this->content->text   = 'The content of our Filtered block!';
        $this->content->footer = 'Footer here...';
     
        return $this->content;
    } */
    public function get_content() {

        global $COURSE;
        global $USER;

        if ($this->content !== null) {
          return $this->content;
        }
       
        $this->content         = new stdClass;
        $this->content->items  = array();
        //$this->content->icons  = array();
        //$this->content->footer = 'Footer here...';
        $this->content->items[] = html_writer::tag('p',$USER->firstname.' '.$USER->lastname);
        //$this->content->items[] = html_writer::tag('a', 'Menu Option 1', array('href' => 'some_file.php'));
        //$this->content->icons[] = html_writer::empty_tag('img', array('src' => 'images/icons/report.png', 'class' => 'icon'));
        //$this->content->items[] = html_writer::tag('a', 'Menu Option 2', array('href' => 'some_file.php'));
        //$this->content->icons[] = html_writer::empty_tag('img', array('src' => 'images/icons/report.png', 'class' => 'icon'));
       
        // Add more list items here
        //$url = new moodle_url('/blocks/filtered_reporting/view.php', array('blockid' => $this->instance->id, 'courseid' => $COURSE->id));
        $url = new moodle_url('/blocks/filtered_reporting/panel/index.php', array('rapport' => '1'));
        $this->content->items[] = html_writer::link($url, get_string('addpage0', 'block_filtered_reporting'));

        $url = new moodle_url('/blocks/filtered_reporting/panel/', array('rapport' => '2'));
        $this->content->items[] = html_writer::link($url, get_string('addpage1', 'block_filtered_reporting'));

        $url = new moodle_url('/blocks/filtered_reporting/dash/');
        $this->content->items[] = html_writer::link($url, get_string('addpage2', 'block_filtered_reporting'));

        $url = new moodle_url('/blocks/filtered_reporting/panel/cohort.php');
        $this->content->items[] = html_writer::link($url, get_string('addpage3', 'block_filtered_reporting'));

        return $this->content;
    }
    
    public function specialization() {
        if (isset($this->config)) {
            if (empty($this->config->title)) {
                $this->title = get_string('defaulttitle', 'block_filtered_reporting');            
            } else {
                $this->title = $this->config->title;
            }
     
            if (empty($this->config->text)) {
                $this->config->text = get_string('defaulttext', 'block_filtered_reporting');
            }    
        }
    }

    public function instance_allow_multiple() {
        return false;
    }

    function has_config() {
        return true;
    }

    public function html_attributes() {
        $attributes = parent::html_attributes(); // Get default values
        $attributes['class'] .= ' block_'. $this->name(); // Append our class to class attribute
        return $attributes;
    }
    // The PHP tag and the curly bracket for the class definition 
    // will only be closed after there is another function added in the next section.
}