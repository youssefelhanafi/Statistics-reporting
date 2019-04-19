<?php

//require_once('/var/www/html/moodle/lib/excellib.class.php');
$excellib = '/var/www/html/moodle/lib/excellib.class.php';
if (file_exists($excellib)) {

    $config = '/var/www/html/moodle/config.php';
    if (file_exists($config)){

        
        require_once($config);
        require_once($excellib);

        //$sql = strval($_POST['query']);
        if (isset($_POST['prenomvalide']) && isset($_POST['nomvalide']) && isset($_POST['prenomnonvalide'])
        && isset($_POST['nomnonvalide']) && isset($_POST['prenomentame']) && isset($_POST['nomentame'])) {
            $string1 = $_POST['prenomvalide'];
            $string2 = $_POST['nomvalide'];
            $string3 = $_POST['prenomnonvalide'];
            $string4 = $_POST['nomnonvalide'];
            $string5 = $_POST['prenomentame'];
            $string6 = $_POST['nomentame'];


            $tauxrealisation = $_POST['realisation'];
            $tauxparticipation = $_POST['participation']; 
            $tauxechec = $_POST['echec'];
            $nbrtermine = $_POST['valide'];
            $nbrstatusencours = $_POST['nonvalide']; 
            $nbrstatusjamais = $_POST['nonentame'];
        }
        else {
            echo '0';
        }

        //get Data

        $arr1 = array();
        $s1 = explode(" ",$string1);
        for ($i=6; $i < sizeof($s1) ; $i+=6) { 
            //echo $s[$i];
            //echo '<br>';
            array_push($arr1,$s1[$i]);
        }
        foreach ($arr1 as $key => $value) {
            if (!preg_match('/^[a-zA-Z]+/', $value)) {
                unset($arr1[$key]);
            }
        }
        
        $arr2 = array();
        $s2 = explode(" ",$string2);
        for ($i=6; $i < sizeof($s2) ; $i+=6) { 
            //echo $s[$i];
            //echo '<br>';
            array_push($arr2,$s2[$i]);
        }
        foreach ($arr2 as $key => $value) {
            if (!preg_match('/^[a-zA-Z]+/', $value)) {
                unset($arr2[$key]);
            }
        }

        $arr3 = array();
        $s3 = explode(" ",$string3);
        for ($i=6; $i < sizeof($s3) ; $i+=6) { 
            //echo $s[$i];
            //echo '<br>';
            array_push($arr3,$s3[$i]);
        }
        foreach ($arr3 as $key => $value) {
            if (!preg_match('/^[a-zA-Z]+/', $value)) {
                unset($arr3[$key]);
            }
        }
        
        $arr4 = array();
        $s4 = explode(" ",$string4);
        for ($i=6; $i < sizeof($s4) ; $i+=6) { 
            //echo $s[$i];
            //echo '<br>';
            array_push($arr4,$s4[$i]);
        }
        foreach ($arr4 as $key => $value) {
            if (!preg_match('/^[a-zA-Z]+/', $value)) {
                unset($arr4[$key]);
            }
        }
        
        $arr5 = array();
        $s5 = explode(" ",$string5);
        for ($i=6; $i < sizeof($s5) ; $i+=6) { 
            //echo $s[$i];
            //echo '<br>';
            array_push($arr5,$s5[$i]);
        }
        foreach ($arr5 as $key => $value) {
            if (!preg_match('/^[a-zA-Z]+/', $value)) {
                unset($arr5[$key]);
            }
        }

        $arr6 = array();
        $s6 = explode(" ",$string6);
        for ($i=6; $i < sizeof($s6) ; $i+=6) { 
            //echo $s[$i];
            //echo '<br>';
            array_push($arr6,$s6[$i]);
        }
        foreach ($arr6 as $key => $value) {
            if (!preg_match('/^[a-zA-Z]+/', $value)) {
                unset($arr6[$key]);
            }
        }

        //end get data

        $filename = 'Dash_'.(time());

        $downloadfilename = clean_filename($filename);
        /// Creating a workbook
        $workbook = new MoodleExcelWorkbook("-");
        /// Sending HTTP headers
        $workbook->send($downloadfilename);
        /// Adding the worksheet
        $myxls = $workbook->add_worksheet($filename);


        /* $myxls->write_string(0, 0, 'Taux de réalisation %');
        $myxls->write_string(0, 1, 'Taux de participation %');
        $myxls->write_string(0, 2, 'Taux d\'échec %');
        $myxls->write_string(0, 3, 'Collaborateur ayant validé la formation');
        $myxls->write_string(0, 4, 'Collaborateur n\'ayant pas validé la formation');
        $myxls->write_string(0, 5, 'Collaborateur n\'ayant pas encore entamé la formation');
        for ($i=0; $i < sizeof($tab); $i++) { 
            $myxls->write_string(1, $i, $tab[$i]);
        } */
        //$format = array('font'=>array('size'=>10, 'name'=>'Arial'));
        $myxls->write_string(0, 0, 'Prénom(formation validé)',$format = array('font'=>array('size'=>10, 'name'=>'Arial')));
        $myxls->write_string(0, 1, 'Nom(formation validé)');
        $myxls->write_string(0, 2, 'Prénom(formation non validé)');
        $myxls->write_string(0, 3, 'Nom(formation non validé)');
        $myxls->write_string(0, 4, 'Prénom(formation non entamé)');
        $myxls->write_string(0, 5, 'Nom(formation non entamé)');

        $myxls->write_string(2, 9, 'Taux de réalisation');
        $myxls->write_string(3, 9, $tauxrealisation.'%');
        $myxls->write_string(2, 10, 'Taux de participation');
        $myxls->write_string(3, 10, $tauxparticipation.'%');
        $myxls->write_string(2, 11, 'Taux d\'échec');
        $myxls->write_string(3, 11, $tauxechec.'%');
        $myxls->write_string(4, 9, 'Collaborateur ayant validé la formation');
        $myxls->write_string(5, 9, $nbrtermine);
        $myxls->write_string(4, 10, 'Collaborateurs n\'ayant pas validé la formation');
        $myxls->write_string(5, 10, $nbrstatusencours);
        $myxls->write_string(4, 11, 'Collaborateurs n\'ayant pas encore entamé a formation');
        $myxls->write_string(5, 11, $nbrstatusjamais);


        for ($i=1; $i <= sizeof($arr1); $i++) { 
            $myxls->write_string($i, 0, $arr1[$i-1]);
        }
        for ($i=1; $i <= sizeof($arr2); $i++) { 
            $myxls->write_string($i, 1, $arr2[$i-1]);
        }
        for ($i=1; $i <= sizeof($arr3); $i++) { 
            $myxls->write_string($i, 2, $arr3[$i-1]);
        }
        for ($i=1; $i <= sizeof($arr4); $i++) { 
            $myxls->write_string($i, 3, $arr4[$i-1]);
        }
        for ($i=1; $i <= sizeof($arr5); $i++) { 
            $myxls->write_string($i, 4, $arr5[$i-1]);
        }
        for ($i=1; $i <= sizeof($arr6); $i++) { 
            $myxls->write_string($i, 5, $arr6[$i-1]);
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
