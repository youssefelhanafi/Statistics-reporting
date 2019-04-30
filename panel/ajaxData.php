<?php
//Include the database configuration file
include './config/dbConfig.php';

if(!empty($_POST["categorie_id"])){
    //Fetch all cours data
    $query = $db->query("SELECT id,fullname from mdl_course where category = ".$_POST['categorie_id']."  ORDER BY fullname ASC");//6
    
    //Count total number of rows
    $rowCount = $query->num_rows;
    
    //cours option list
    if($rowCount > 0){
        echo '<option value="">Sélectionnez un cours</option>';
        while($row = $query->fetch_assoc()){ 
            echo '<option value="'.$row['id'].'">'.$row['fullname'].'</option>';
        }
    }else{
        echo '<option value="">cours non disponible</option>';
    }
}
elseif(!empty($_POST["cours_id"])){
    //Fetch all activite data
    $query = $db->query("SELECT id,itemname from mdl_grade_items where courseid = ".$_POST['cours_id']." AND itemtype <> 'course' order by itemname ASC");
    //Count total number of rows
    $rowCount = $query->num_rows;
    
    //activite option list
    if($rowCount > 0){
        echo '<option value="">Sélectionnez une activité</option>';
        while($row = $query->fetch_assoc()){ 
            echo '<option value="'.$row['id'].'">'.$row['itemname'].'</option>';
        }
    }else{
        echo '<option value="">activité non disponible</option>';
    } 
}
?>