<?php
include './config/dbConfig.php';
include './includes/req.php';
include './includes/tabfunction.php';
//liste des cohort
$sql = "SELECT id,name from mdl_cohort order by name";
$query = $db->query($sql);
///rq filter
$sql2 = "SELECT distinct data from mdl_user_info_data where fieldid=12 order by data";
$query2 = $db->query($sql2);
$sql3 = "SELECT distinct data from mdl_user_info_data where fieldid=8 order by data";
$query3 = $db->query($sql3);
$sql4 = "SELECT distinct data from mdl_user_info_data where fieldid=23 order by data";
$query4 = $db->query($sql4);
$sql5 = "SELECT distinct data from mdl_user_info_data where fieldid=24 order by data";
$query5 = $db->query($sql5);
//Count total number of rows
$rowCount = $query->num_rows;
$rowCount2 = $query2->num_rows;
$rowCount3 = $query3->num_rows;
$rowCount4 = $query4->num_rows;
$rowCount5 = $query5->num_rows;


include './includes/header2.php';
?>
<body>
  <div class="container">
    <div class="row">
      <div class="col-lg-12 text-center">
        <h1 class="mt-5">Inscription NR</h1></br></br>
        <div>
          <form action="" method="post">
            <div class="row">
              <div class="col-sm-4">
              <select name="cohortid" multiple="multiple" size="20" class="form-control no-overflow">
                <optgroup label="Liste cohort">
                <?php
                            if($rowCount > 0){
                                while($row = $query->fetch_assoc()){ 
                                    echo '<option  value="'.$row['id'].'">'.utf8_encode($row['name']).'</option>';
                                }
                            }else{
                                echo '<option value="">cohorte non disponible</option>';
                        }
                        ?>
                </optgroup>
                </select>
               
              </div>

              <div class="col-sm-4">
              <input name="unite" list="unite" placeholder="filtrer par unité " class="form-control no-overflow" >
                <datalist id="unite" name="unite">
                <?php
                    if($rowCount2 > 0){
                        while($row = $query2->fetch_assoc()){ 
                            echo '<option  value="'.utf8_encode($row['data']).'">';
                        }
                    }else{
                        echo '<option value="aucune resultat">';
                                 }
                            ?>           
                </datalist>
                </br>
                <input name="poste" list="poste" placeholder="filtrer par poste " class="form-control no-overflow" >
                <datalist id="poste">
                <?php
                    if($rowCount3 > 0){
                        while($row = $query3->fetch_assoc()){ 
                            echo '<option value="'.$row['data'].'">'.utf8_encode($row['data']).'</option>';
                        }
                    }else{
                        echo '<option value="aucune resultat">';
                                 }
                            ?>           
                </datalist>
                </br>
                <input name="dga" list="dga" placeholder="filtrer par dga " class="form-control no-overflow" >
                <datalist id="dga">
                <?php
                    if($rowCount4 > 0){
                        while($row = $query4->fetch_assoc()){ 
                            echo '<option value="'.$row['data'].'">';
                        }
                    }else{
                        echo '<option value="aucune resultat">';
                                 }
                            ?>           
                </datalist>
                </br>
                <input name="direction" list="direction" placeholder="filtrer par direction " class="form-control no-overflow" >
                <datalist id="direction">
                <?php
                    if($rowCount5 > 0){
                        while($row = $query5->fetch_assoc()){ 
                            echo '<option value="'.$row['data'].'">';
                        }
                    }else{
                        echo '<option value="aucune resultat">';
                                 }
                            ?>           
                </datalist>
              </div>

             
       <div class="col-sm-12" align="center">
                    <input type="submit" name="SubmitButton" class="btn btn-primary" value="Inscrire" /><br><br>
                </div>  
                
            </div>
          </form>
        </div>
       </div>
    </div>
  </div>
    
        <br><br>
        <?php
    if(!isset($_POST['SubmitButton'])){  //No submit
        $selected_val = 'NULL';
    }
    else{ // Submitted
            if(isset($_POST['cohortid']))  {
			//	var_dump($_POST['cohortid']);
				if(var_dump($_POST['unite'])=='' and  var_dump($_POST['poste'])==''  and var_dump($_POST['dga'])=='' and var_dump($_POST['direction'])=='')  {	
		          echo "<script>alert(\"Merci de rensiegner les filtres\")</script>";
				  
               }
			   
				                   elseif(var_dump($_POST['unite'])<>'' or  var_dump($_POST['poste'])<>''  or var_dump($_POST['dga'])<>'' or var_dump($_POST['direction'])<>''){
					 		//var_dump($_POST['unite']);var_dump($_POST['poste']);
            $sql = "SELECT distinct u.id as uid,u.email as uemail from mdl_user u where u.suspended=0 and u.deleted=0 and u.id not in(select userid from mdl_cohort_members where cohortid=$_POST[cohortid])";              
            $query = $db->query($sql);
            $rowCount = $query->num_rows;
        
                if ($rowCount>=0)	
                {	
			
			while($row = $query->fetch_assoc())
                    {
					
                    $sql = "SELECT f1.userid, f1.data, f2.data FROM mdl_user_info_data f1, mdl_user_info_data f2 WHERE f1.data like '%".$_POST['unite']."%' and f2.data like '%".$_POST['poste']."%' and f2.data like '%".$_POST['dga']."%' and f2.data like '%".$_POST['direction']."%' and f1.userid=f2.userid ";
                    $query = $db->query($sql);
                    $rowCount = $query->num_rows;   
					
				
					
                    if($rowCount > 0)   {
                    while($row = $query->fetch_assoc()){ 
                        echo '<li> Collaborateur : '.$row['userid'].'</li>';
                       // echo '<li> cohorte s�l�ctionn�e est : '.$_POST['cohortid'].'</li>';
                    $secondQuery = "INSERT INTO mdl_cohort_members (cohortid, userid, timeadded) VALUES ( $_POST[cohortid],$row[userid],CURDATE() )";
                     //   print_r($secondQuery );
                        $query1 = $db->query($secondQuery);
						
                                                        }
														
                                        }   
					}
                  
                    }
               				
        
								   }  
                                    } 
            elseif(!isset($_POST['cohortid'])){echo "<script>alert(\"Merci de selectionner une cohorte\")</script>"; }
  

        }
        
    include './includes/footer2.php';
   

 ?>


  
