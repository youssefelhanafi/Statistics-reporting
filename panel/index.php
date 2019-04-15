<?php
include './config/dbConfig.php';
include './includes/req.php';
include './includes/tabfunction.php';

//Fetch all the categorie data
$sql = "SELECT id,name from mdl_course_categories order by name";
$query = $db->query($sql);

//Count total number of rows
$rowCount = $query->num_rows;

include './includes/header2.php';
?>
<body>
  <div class="container">
    <div class="row">
      <div class="col-lg-12 text-center">
        <h1 class="mt-5">Filtered Reports - Moodle</h1>
        <div>
          <form action="" method="post">
            <div class="row">
              <div class="col-sm-4">
                  <select id="categorie" class="form-control">
                        <option value="">Choisir catégorie</option>
                        <?php
                            if($rowCount > 0){
                                while($row = $query->fetch_assoc()){ 
                                    echo '<option value="'.$row['id'].'">'.$row['name'].'</option>';
                                }
                            }else{
                                echo '<option value="">Catégorie non disponible</option>';
                        }
                        ?>
                  </select>
              </div>
              <div class="col-sm-4">
                    <select id="cours" class="form-control">
                        <option value="">Sélectionnez d'abord la catégorie</option>
                    </select>
                    <br>
                </div>

                <div class="col-sm-4">
                    <select id="activite" class="form-control" name="activite">
                        <option value="">Sélectionnez d'abord le cours</option>
                    </select>
                </div>


                <?php
                if ($_GET['rapport'] == 1) {
                    echo '
                    <div class="col-sm-4" align="center">
                    Date début formation:
                    <input id="formation" type="date" name="debutf"><br><br>
                    </div>
                    <div class="col-sm-4" align="center">
                    Date fin formation:
                    <input id="formation" type="date" name="finf"><br><br>
                    </div>
                    <div class="col-sm-4" align="center">
                    <label><input type="checkbox" class="agree"> Activer date formation</label>
                    </div>
                    <div class="col-sm-4" align="center">
                    Date début recrutement:
                    <input id="recrutement" type="date" name="debutr"><br><br>
                    </div>
                    <div class="col-sm-4" align="center">
                    Date fin recrutement:
                    <input id="recrutement" type="date" name="finr"><br><br>
                    </div>
                    <div class="col-sm-4" align="center">
                    <label><input type="checkbox" class="agree1"> Activer date recrutement</label>
                    </div>
                    
                    <div class="col-sm-4" align="center">
                    Unité:
                    <input id="filtre" type="text" name="unite"><br><br>
                    </div>
                    <div class="col-sm-4" align="center">
                    Manager:
                    <input id="filtre" type="text" name="manager"><br><br>
                    </div>
                    <div class="col-sm-4" align="center">
                    dga:
                    <input id="filtre" type="text" name="dga"><br><br>
                    </div>
                    <div class="col-sm-12" align="center">
                    <label><input type="checkbox" class="agreetxt"> Activer Filtres</label>
                    </div>
                         ';
                }
                ?>
                <div class="col-sm-12" align="center">
                    <input type="submit" name="SubmitButton" class="btn btn-primary" value="Soumettre" /><br><br>
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
        $selected_val = $_POST['activite'];
        if(!isset($_POST['debutf']) && !isset($_POST['finf']) ){ // No date selected
            list($query0,$sql0) = sql0v1($db,$selected_val);
            }
        else{ // Date formation selected
            if (!isset($_POST['debutr']) && !isset($_POST['finr'])) {
                $time1 = strtotime($_POST['debutf'].' 02:00');
                $time2 = strtotime($_POST['finf'].' 02:00');
                //$date_inscription = strval(date('d/m/Y',$time));
                list($query0,$sql0) = sql0v2($db,$selected_val,$time1,$time2);
            }
            else { // Date recrutement selected
                $time1 = strtotime($_POST['debutf'].' 02:00');
                $time2 = strtotime($_POST['finf'].' 02:00');
                $time3 = strtotime($_POST['debutr'].' 02:00');
                $time4 = strtotime($_POST['finr'].' 02:00');
                list($query0,$sql0) = sql0v3($db,$selected_val,$time1,$time2,$time3,$time4);
            }
            if (isset($_POST['unite']) || isset($_POST['manager']) || isset($_POST['dga'])) {
                $unite = htmlspecialchars($_POST['unite']);
                $manager = htmlspecialchars($_POST['manager']);
                $dga = htmlspecialchars($_POST['dga']);
                list($query0,$sql0) = sql0v4($db,$selected_val,$time1,$time2,$time3,$time4,$unite,$manager,$dga) ;
            }
            
            }
        list($query1,$sql1) = sql1($db);
        
        switch ($_GET['rapport']) {
            case '1':
                /* $vartest = strval(table($query0,$sql0));
                print $vartest; */
                table($query0,$sql0);
                break;
            
            case '2':
                table($query1,$sql1); 
                break;
        }
        }    
    include './includes/footer2.php';
?>
        
      

  
