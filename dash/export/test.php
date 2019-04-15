<?php

if (isset($_POST['realisation']) && isset($_POST['participation']) && isset($_POST['echec']) 
&& isset($_POST['valide']) && isset($_POST['nonvalide']) && isset($_POST['nonentame'])) {
    $tab = array($_POST['realisation'],$_POST['participation'],$_POST['echec'],$_POST['valide'],$_POST['nonvalide'],$_POST['nonentame']);
    print_r($tab);
    
}
else{
    echo '0';
}