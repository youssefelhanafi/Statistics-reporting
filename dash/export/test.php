<?php

if (isset($_POST['prenomvalide']) && isset($_POST['nomvalide']) && isset($_POST['prenomnonvalide'])
&& isset($_POST['nomnonvalide']) && isset($_POST['prenomentame']) && isset($_POST['nomentame'])) {
    //$tab = array($_POST['realisation'],$_POST['participation'],$_POST['echec'],$_POST['valide'],$_POST['nonvalide'],$_POST['nonentame']);
    //print_r($tab);
    //print_r(unserialize($_POST['prenomvalide']));
    //print_r($_POST['prenomvalide']);

    //$string = "Array ( [0] => Youssef [1] => meriyem [2] => siham ) ";
    
    
    $string1 = $_POST['prenomvalide'];
    $string2 = $_POST['nomvalide'];
    $string3 = $_POST['prenomnonvalide'];
    $string4 = $_POST['nomnonvalide'];
    $string5 = $_POST['prenomentame'];
    $string6 = $_POST['nomentame'];



    
    /* echo '<pre>';
    echo $stringtest;
    echo '</pre>'; */


    //echo '<pre>';
    //echo $string1;
    //echo '</pre>';


/*     $arr1 = array();
    $s1 = explode(" ",$stringtest);
    for ($i=6; $i < sizeof($s1) ; $i+=6) { 
        //echo $s[$i];
        //echo '<br>';
        array_push($arr1,$s1[$i]);
    }
    sort($arr1);
    foreach ($arr1 as $key => $value) {
        if ($value == " " || $value == "=>") {
            //unset($arr1[$key]);
        }
    } */


    echo '<pre>';
    //print_r($arr1);
    echo '</pre>';
    //print_r(array_filter($arr));
    /* for ($i=0; $i < sizeof($arr1); $i++) {
        echo '<pre>';
        echo $arr1[$i];
        echo '</pre>';
    }   */  

    $arr2 = array();
    $s2 = explode(" ",$string2);
    for ($i=6; $i < sizeof($s2) ; $i+=3) { 
        //echo $s[$i];
        //echo '<br>';
        array_push($arr2,$s2[$i]);
    }
    //print_r($arr2);
    /* for ($i=0; $i < sizeof($arr2); $i++) {
        echo '<pre>';
        echo $arr2[$i];
        echo '</pre>';
    }  */ 
    

}
else{
    echo '0';

} 



/* $string="REGISTER 11223344 here";
$s = explode(" ",$string);
unset($s[1]);
$s = implode(" ",$s);
print "$s\n"; */


//print_r($s); 


include "include.php";
/* $arr1 = array();
    $s1 = explode(" ",$stringtest);
    for ($i=6; $i < sizeof($s1) ; $i+=6) { 
        //echo $s[$i];
        //echo '<br>';
        array_push($arr1,$s1[$i]);
    }


$arrayName = array('A' => 1, 'B' => 2, 'C'=> '', 'D' => '=>','E' => 6, 'F' => 'M', 'G'=> '', 'H' => '=>', 'I' => '=>', 'J' => '=>',  'K' => '',  'L' => 58, 'M' => 'Abd' );
//sort($arr1);
//print_r($arrayName);



foreach ($arr1 as $key => $value) {
    if (!preg_match('/^[a-zA-Z]+/', $value)) {
        unset($arr1[$key]);
    }
}
/*
echo '<pre>';
print_r($arr1);
echo '</pre>';  */
$arrayName = array('A' => 1, 'B' => 2, 'C'=> '', 'D' => '=>','E' => 6, 'F' => 'M', 'G'=> '', 'H' => '=>', 'I' => '=>', 'J' => '=>',  'K' => '',  'L' => 58, 'M' => 'Abd' );


$arr1 = arrProcess($stringtest);

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

echo '<pre>';
print_r($arr1);
echo '</pre>';