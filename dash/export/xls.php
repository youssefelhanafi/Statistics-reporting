<?php

//require_once('/var/www/html/moodle/lib/excellib.class.php');
$excellib = '/var/www/html/moodle/lib/excellib.class.php';
if (file_exists($excellib)) {

    $config = '/var/www/html/moodle/config.php';
    if (file_exists($config)){

        
        require_once($config);
        require_once($excellib);

        //$sql = strval($_POST['query']);
        if (isset($_POST['realisation']) && isset($_POST['participation']) && isset($_POST['echec']) 
        && isset($_POST['valide']) && isset($_POST['nonvalide']) && isset($_POST['nonentame'])) {
            $tab = array($_POST['realisation'],$_POST['participation'],$_POST['echec'],$_POST['valide'],$_POST['nonvalide'],$_POST['nonentame']);
            //print_r($tab);
            
        }
        else{
            echo '0';
        }

        //get Data
        //end get data

        $filename = 'Dash_'.(time());

        $downloadfilename = clean_filename($filename);
        /// Creating a workbook
        $workbook = new MoodleExcelWorkbook("-");
        /// Sending HTTP headers
        $workbook->send($downloadfilename);
        /// Adding the worksheet
        $myxls = $workbook->add_worksheet($filename);


        $myxls->write_string(0, 0, 'Taux de réalisation %');
        $myxls->write_string(0, 1, 'Taux de participation %');
        $myxls->write_string(0, 2, 'Taux d\'échec %');
        $myxls->write_string(0, 3, 'Collaborateur ayant validé la formation');
        $myxls->write_string(0, 4, 'Collaborateur n\'ayant pas validé la formation');
        $myxls->write_string(0, 5, 'Collaborateur n\'ayant pas encore entamé la formation');
        for ($i=0; $i < sizeof($tab); $i++) { 
            $myxls->write_string(1, $i, $tab[$i]);
        }

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
