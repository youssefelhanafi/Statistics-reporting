<?php
function arrProcess($string){
    $arr = array();
    $s = explode(" ",$string);
    for ($i=6; $i < sizeof($s) ; $i+=6) { 
        //echo $s[$i];
        //echo '<br>';
        array_push($arr,$s[$i]);
    }
    foreach ($arr as $key => $value) {
        if (!preg_match('/^[a-zA-Z]+/', $value)) {
            unset($arr[$key]);
        }
    }

    return $arr;
}
//require_once('/var/www/html/moodle/lib/excellib.class.php');
$excellib = '/var/www/html/moodle352/lib/excellib.class.php';
if (file_exists($excellib)) {

    $config = '/var/www/html/moodle352/config.php';
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
            
            $string7= $_POST['matricule1'];
            $string8= $_POST['direction1'];
            $string9= $_POST['dga1'];
            $string10= $_POST['unite1'];
            $string11= $_POST['matricule2'];
            $string12= $_POST['direction2'];
            $string13= $_POST['dga2'];
            $string14= $_POST['unite2'];
            $string15= $_POST['matricule3'];
            $string16= $_POST['direction3'];
            $string17= $_POST['dga3'];
            $string18= $_POST['unite3'];
            //$string19= $_POST[''];



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
        //validé
        $arr1 = arrProcess($string1);
        $arr2 = arrProcess($string2);
        $arr7 = arrProcess($string7);//Matricule
        $arr8 = arrProcess($string8);//direction
        $arr9 = arrProcess($string9);//dga
        $arr10 = arrProcess($string10);//unite
        //non validé
        $arr3 = arrProcess($string3);
        $arr4 = arrProcess($string4);
        $arr11 = arrProcess($string11);
        $arr12 = arrProcess($string12);
        $arr13 = arrProcess($string13);
        $arr14 = arrProcess($string14);
        //non entamé
        $arr5 = arrProcess($string5);
        $arr6 = arrProcess($string6);
        $arr15 = arrProcess($string15);
        $arr16 = arrProcess($string16);
        $arr17 = arrProcess($string17);
        $arr18 = arrProcess($string18);

        
        
        //end get data

        $filename = 'Dash_'.(time());

        $downloadfilename = clean_filename($filename);
        /// Creating a workbook
        $workbook = new MoodleExcelWorkbook("-");
        /// Sending HTTP headers
        $workbook->send($downloadfilename);
        /// Adding the worksheet
        $myxls0 = $workbook->add_worksheet("formation validé");
        $myxls1 = $workbook->add_worksheet("formation non validé");
        $myxls2 = $workbook->add_worksheet("formation non entamé");
        $myxls3 = $workbook->add_worksheet("stats");


        //formation validé
        $myxls0->write_string(0, 0, 'Matricule');
        $myxls0->write_string(0, 1, 'Prénom');
        $myxls0->write_string(0, 2, 'Nom');
        $myxls0->write_string(0, 3, 'Direction');
        $myxls0->write_string(0, 4, 'dga');
        $myxls0->write_string(0, 5, 'Unité');
        //formation non validé
        $myxls1->write_string(0, 0, 'Matricule');
        $myxls1->write_string(0, 1, 'Prénom');
        $myxls1->write_string(0, 2, 'Nom');
        $myxls1->write_string(0, 3, 'Direction');
        $myxls1->write_string(0, 4, 'dga');
        $myxls1->write_string(0, 5, 'Unité');
        //formation non entamé
        $myxls2->write_string(0, 0, 'Matricule');
        $myxls2->write_string(0, 1, 'Prénom');
        $myxls2->write_string(0, 2, 'Nom');
        $myxls2->write_string(0, 3, 'Direction');
        $myxls2->write_string(0, 4, 'dga');
        $myxls2->write_string(0, 5, 'Unité');
        //stats
        $myxls3->write_string(2, 9, 'Taux de réalisation');
        $myxls3->write_string(3, 9, $tauxrealisation.'%');
        $myxls3->write_string(2, 10, 'Taux de participation');
        $myxls3->write_string(3, 10, $tauxparticipation.'%');
        $myxls3->write_string(2, 11, 'Taux d\'échec');
        $myxls3->write_string(3, 11, $tauxechec.'%');
        $myxls3->write_string(4, 9, 'Collaborateur ayant validé la formation');
        $myxls3->write_string(5, 9, $nbrtermine);
        $myxls3->write_string(4, 10, 'Collaborateurs n\'ayant pas validé la formation');
        $myxls3->write_string(5, 10, $nbrstatusencours);
        $myxls3->write_string(4, 11, 'Collaborateurs n\'ayant pas encore entamé a formation');
        $myxls3->write_string(5, 11, $nbrstatusjamais);


        for ($i=1; $i <= sizeof($arr1); $i++) { 
            $myxls0->write_string($i, 0, $arr7[$i-1]);
            $myxls0->write_string($i, 1, $arr1[$i-1]);
            $myxls0->write_string($i, 2, $arr2[$i-1]);
            $myxls0->write_string($i, 3, $arr8[$i-1]);
            $myxls0->write_string($i, 4, $arr9[$i-1]);
            $myxls0->write_string($i, 5, $arr10[$i-1]);
        }

        for ($i=1; $i <= sizeof($arr3); $i++) { 
            $myxls1->write_string($i, 0, $arr11[$i-1]);
            $myxls1->write_string($i, 1, $arr3[$i-1]);
            $myxls1->write_string($i, 2, $arr4[$i-1]);
            $myxls1->write_string($i, 3, $arr12[$i-1]);
            $myxls1->write_string($i, 4, $arr13[$i-1]);
            $myxls1->write_string($i, 5, $arr14[$i-1]);
        }

        for ($i=1; $i <= sizeof($arr5); $i++) { 
            $myxls2->write_string($i, 0, $arr15[$i-1]);
            $myxls2->write_string($i, 1, $arr5[$i-1]);
            $myxls2->write_string($i, 2, $arr6[$i-1]);
            $myxls2->write_string($i, 3, $arr16[$i-1]);
            $myxls2->write_string($i, 4, $arr17[$i-1]);
            $myxls2->write_string($i, 5, $arr18[$i-1]);
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
