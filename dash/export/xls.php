<?php

if (isset($_POST['validequery']) && isset($_POST['nonvalidequery']) && isset($_POST['nonentamequery']) )
{
//require_once('/var/www/html/moodle/lib/excellib.class.php');
//$excellib = '/var/www/html/moodle/lib/excellib.class.php';
$excellib = '../../../../lib/excellib.class.php';
if (file_exists($excellib)) {
    //$config = '/var/www/html/moodle/config.php';
    $config = '../../../../config.php';
    if (file_exists($config)){   
        require_once($config);
        require_once($excellib);
        $sql1 = strval($_POST['validequery']);
        $sql2 = strval($_POST['nonvalidequery']);
        $sql3 = strval($_POST['nonentamequery']);
        $tauxrealisation = $_POST['realisation'];
        $tauxparticipation = $_POST['participation']; 
        $tauxechec = $_POST['echec'];
        $nbrtermine = $_POST['valide'];
        $nbrstatusencours = $_POST['nonvalide']; 
        $nbrstatusjamais = $_POST['nonentame'];
        //db connection
        $servername = "localhost";
        $username = "youssef";
        $password = "password";
        $database = "moodle352";
        // Create connection
        $conn = mysqli_connect($servername, $username, $password, $database);        
        //end db connection
        //get data
        function GetHeaders($conn,$sql)
        {
            $data = Array();
            $result = mysqli_query($conn,$sql); 
            $i = 0; 
            while($i<mysqli_num_fields($result)) 
            { 
            $meta=mysqli_fetch_field($result); 
            array_push($data,$meta->name);
            $i++; 
            }
            return $data;
            //print_r($data);
        }
        function DatabaseData($conn,$sql)
        {
            $result = mysqli_query($conn,$sql);
            $columnValues = Array();
            while ($row = mysqli_fetch_assoc($result)){
                $columnValues[] = $row;
            }
            return $columnValues;
            //print_r($columnValues);
        }


        $headers1 = GetHeaders($conn,$sql1);
        $data1 = DatabaseData($conn,$sql1);

        $headers2 = GetHeaders($conn,$sql2);
        $data2 = DatabaseData($conn,$sql2);

        $headers3 = GetHeaders($conn,$sql3);
        $data3 = DatabaseData($conn,$sql3);

        //end get data
        $filename = 'Dash_'.(time());
        $downloadfilename = clean_filename($filename);
        /// Creating a workbook
        $workbook = new MoodleExcelWorkbook("-");
        /// Sending HTTP headers
        $workbook->send($downloadfilename);
        /// Adding the worksheet
        $myxls1 = $workbook->add_worksheet("formation validé");
        for ($i=0; $i < sizeof($headers1); $i++) { 
            $myxls1->write_string(0, $i, $headers1[$i]);           
        }
        // END Headers
        for ($j=1; $j < sizeof($data1)+2; $j++) { 
            for ($h=0; $h < sizeof($headers1); $h++) { 
                $myxls1->write_string($j, $h, $data1[$j-2][$headers1[$h]]);
            }          
        }
        $workbook->close();

        $myxls2 = $workbook->add_worksheet("formation non validé");
        for ($i=0; $i < sizeof($headers2); $i++) { 
            $myxls2->write_string(0, $i, $headers2[$i]);           
        }
        // END Headers
        for ($j=1; $j < sizeof($data2)+2; $j++) { 
            for ($h=0; $h < sizeof($headers2); $h++) { 
                $myxls2->write_string($j, $h, $data2[$j-2][$headers2[$h]]);
            }          
        }
        $workbook->close();

        $myxls3 = $workbook->add_worksheet("formation non entamé");
        for ($i=0; $i < sizeof($headers3); $i++) { 
            $myxls3->write_string(0, $i, $headers3[$i]);           
        }
        // END Headers
        for ($j=1; $j < sizeof($data3)+2; $j++) { 
            for ($h=0; $h < sizeof($headers3); $h++) { 
                $myxls3->write_string($j, $h, $data3[$j-2][$headers3[$h]]);
            }          
        }
        $workbook->close();

        $myxls4 = $workbook->add_worksheet("stats");
        $myxls4->write_string(2, 9, 'Taux de réalisation');
        $myxls4->write_string(3, 9, $tauxrealisation.'%');
        $myxls4->write_string(2, 10, 'Taux de participation');
        $myxls4->write_string(3, 10, $tauxparticipation.'%');
        $myxls4->write_string(2, 11, 'Taux d\'échec');
        $myxls4->write_string(3, 11, $tauxechec.'%');
        $myxls4->write_string(4, 9, 'Collaborateur ayant validé la formation');
        $myxls4->write_string(5, 9, $nbrtermine);
        $myxls4->write_string(4, 10, 'Collaborateurs n\'ayant pas validé la formation');
        $myxls4->write_string(5, 10, $nbrstatusencours);
        $myxls4->write_string(4, 11, 'Collaborateurs n\'ayant pas encore entamé a formation');
        $myxls4->write_string(5, 11, $nbrstatusjamais);

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


}

?>