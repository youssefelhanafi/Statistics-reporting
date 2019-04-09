<?php
 
require_once('../../config.php');
require_once('filtered_reporting_form.php');
 
global $DB, $OUTPUT, $PAGE;
 
// Check for all required variables.
$courseid = required_param('courseid', PARAM_INT);
$blockid = required_param('blockid', PARAM_INT);
 
// Next look for optional variables.
$id = optional_param('id', 0, PARAM_INT);

$settingsnode = $PAGE->settingsnav->add(get_string('filtered_reportingsettings', 'block_filtered_reporting'));
$editurl = new moodle_url('/blocks/filtered_reporting/view.php', array('id' => $id, 'courseid' => $courseid, 'blockid' => $blockid));
$editnode = $settingsnode->add(get_string('editpage', 'block_filtered_reporting'), $editurl);
$editnode->make_active();
 
 
if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    print_error('invalidcourse', 'block_filtered_reporting', $courseid);
}
 
require_login($course);

$PAGE->set_url('/blocks/filtered_reporting/view.php', array('id' => $courseid));
$PAGE->set_pagelayout('standard');
$PAGE->set_heading(get_string('edithtml', 'block_filtered_reporting'));
 
$filtered_reporting = new filtered_reporting_form();

$toform['blockid'] = $blockid;
$toform['courseid'] = $courseid;
$filtered_reporting->set_data($toform);

if($filtered_reporting->is_cancelled()) {
    // Cancelled forms redirect to the course main page.
    $courseurl = new moodle_url('/my');
    redirect($courseurl);
} else if ($fromform = $filtered_reporting->get_data()) {
    // We need to add code to appropriately act on and store the submitted data
    // but for now we will just redirect back to the course main page.
    $courseurl = new moodle_url('/course/view.php', array('id' => $courseid));
    //redirect($courseurl);
    print_object($fromform);
} else {
    // form didn't validate or this is the first display
    $site = get_site();
    echo $OUTPUT->header();
    $filtered_reporting->display();
    echo $OUTPUT->footer();
}

// We need to add code to appropriately act on and store the submitted data
if (!$DB->insert_record('block_filtered_reporting', $fromform)) {
    print_error('inserterror', 'block_filtered_reporting');
}
/* echo $OUTPUT->header();
$filtered_reporting->display();
echo $OUTPUT->footer(); */
 
//$filtered_reporting->display();
?>