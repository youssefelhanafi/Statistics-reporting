<?php
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
if (isset($_POST['SubmitButton']) && isset($_POST['date'])) {
require './config/db.php';
//include the file that loads the PhpSpreadsheet classes
require '/var/www/html/moodle/vendor/autoload.php';

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

//include the classes needed to create and write .xlsx file


//object of the Spreadsheet class to create the excel data
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();


$cell_st =[
 'font' =>['bold' => true],
 'alignment' =>['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
 'borders'=>['bottom' =>['style'=> \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM]]
];


$headers = GetHeaders($conn,$sql);
$data = DatabaseData($conn,$sql);
// Headers
for ($i=0; $i < sizeof($headers); $i++) { 
    $sheet->setCellValueByColumnAndRow($i+1, 1, $headers[$i]);
    $spreadsheet->getActiveSheet()->getStyle('1:1')->applyFromArray($cell_st);
}
// END Headers
//print_r($data);
for ($j=2; $j < sizeof($data)+2; $j++) { 
    for ($h=0; $h < sizeof($headers); $h++) { 
        $sheet->setCellValueByColumnAndRow($h+1, $j, $data[$j-2][$headers[$h]]);
    }
    
}
//print_r($headers);
//print_r($data['1']);
$spreadsheet->getActiveSheet()->setTitle('Suivi des formations'); //set a title for Worksheet

//make object of the Xlsx class to save the excel file
$writer = new Xlsx($spreadsheet);
$fxls ='suivi_des_formations.xlsx';
//$writer->save('php://output');
$writer->save($fxls);
header('Location: index.php'); 
}
else{
    echo '0';
}