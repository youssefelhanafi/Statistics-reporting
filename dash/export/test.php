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

    $arr1 = array();
    $s1 = explode(" ",$string1);
    for ($i=6; $i < sizeof($s1) ; $i+=6) { 
        //echo $s[$i];
        //echo '<br>';
        array_push($arr1,$s1[$i]);
    }
    echo '<pre>';
    print_r($arr1);
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
