<?php

//require_once('/var/www/html/moodle/lib/excellib.class.php');
//$excellib = '/var/www/html/moodle/lib/excellib.class.php';
$excellib = '../../../../lib/excellib.class.php';
if (file_exists($excellib)) {

    //$config = '/var/www/html/moodle/config.php';
    $config = '../../../../config.php';
    if (file_exists($config)){

        
        require_once($config);
        require_once($excellib);

        $sql = strval($_POST['query']);

        //db connection
        $servername = "localhost";
        $username = "youssef";
        $password = "password";
        $database = "moodle";

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

        $headers = GetHeaders($conn,$sql);
        $data = DatabaseData($conn,$sql);
        //end get data

        $filename = 'report_'.(time());

        $downloadfilename = clean_filename($filename);
        /// Creating a workbook
        $workbook = new MoodleExcelWorkbook("-");
        /// Sending HTTP headers
        $workbook->send($downloadfilename);
        /// Adding the worksheet
        $myxls = $workbook->add_worksheet($filename);



        for ($i=0; $i < sizeof($headers); $i++) { 
            $myxls->write_string(0, $i, utf8_encode($headers[$i]));
            
        }
        // END Headers
        for ($j=1; $j < sizeof($data)+2; $j++) { 
            for ($h=0; $h < sizeof($headers); $h++) { 
                $myxls->write_string($j, $h, utf8_encode($data[$j-2][$headers[$h]]));
            }
            
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

