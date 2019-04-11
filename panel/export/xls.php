<?php

//require_once('/var/www/html/moodle/lib/excellib.class.php');
$excellib = '/var/www/html/moodle/lib/excellib.class.php';
if (file_exists($excellib)) {
    //echo '1';
    //require_once($excellib);
    /* $filename = 'report_'.(time()).'.xls';
    $downloadfilename = clean_filename($filename);
    $workbook = new MoodleExcelWorkbook("-");
    $workbook->send($downloadfilename);
    $myxls = $workbook->add_worksheet($filename);
    $myxls->write_string(0,0,"Hello");
    $workbook->close();
    exit; */

    //$dir = dirname(dirname(__FILE__));
    //echo $dir;
    //echo dirname(dirname(__FILE__));
    $config = '/var/www/html/moodle/config.php';
    if (file_exists($config)){
        //echo '11';
        //require_once
        require_once($config);
        require_once($excellib);

        $PAGE->set_context(context_system::instance());
        $PAGE->set_pagelayout('admin');
        $PAGE->set_title("Experiment Page");
        $PAGE->set_heading("Blank page");
        $PAGE->set_url($CFG->wwwroot.'/blank_page.php');


        echo $OUTPUT->header();

        $filename = 'report_'.(time());

        $downloadfilename = clean_filename($filename);
        /// Creating a workbook
        $workbook = new MoodleExcelWorkbook("-");
        /// Sending HTTP headers
        $workbook->send($downloadfilename);
        /// Adding the worksheet
        $myxls = $workbook->add_worksheet($filename);

        $myxls->write_string(0,0,"Hello");

        $workbook->close();
        exit;
    }
    else{
        echo '10';
    }
    
}
else{
    echo '0';
}
/*  /// Calculate file name

    $downloadfilename = clean_filename("thenameofthefile.xls");

/// Creating a workbook

    $workbook = new MoodleExcelWorkbook("-");

/// Sending HTTP headers

    $workbook->send($downloadfilename);

/// Adding the worksheet
    $myxls = $workbook->add_worksheet($downloadfilename);
    //$myxls =& $workbook->add_worksheet($strgrades);

/// Print cellls

    $myxls->write_string(0,0,"Hello");

    $myxls->write_string(0,1,"Bye");

/// Close the workbook

    $workbook->close(); */