<?php

//require_once('/var/www/html/moodle/lib/excellib.class.php');
$excellib = '/var/www/html/moodle/lib/excellib.class.php';
if (file_exists($excellib)) {

    $config = '/var/www/html/moodle/config.php';
    if (file_exists($config)){

        // Get data
        /* $db = '/var/www/html/moodle/blocks/filtered_reporting/panel/config/db.php';
        require $db;
        $sql = strval($_POST['date']);
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
        $data = DatabaseData($conn,$sql); */

        //echo '11';
        //require_once
        require_once($config);
        require_once($excellib);


        $filename = 'report_'.(time());

        $downloadfilename = clean_filename($filename);
        /// Creating a workbook
        $workbook = new MoodleExcelWorkbook("-");
        /// Sending HTTP headers
        $workbook->send($downloadfilename);
        /// Adding the worksheet
        $myxls = $workbook->add_worksheet($filename);

        $myxls->write_string(0,0,"Hello");
        $myxls->write_string(1,0,"test"); 

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
