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
                  <select id="categorie" class="form-control" name="categorie">
                        
                        <?php
                        if (isset($_POST['categorie'])) {
                            $sqli = "SELECT id,name from mdl_course_categories where id = ".$_POST['categorie'];
                            $queryi = $db->query($sqli);
                            $rowi = $queryi->fetch_assoc();
                            echo '<option value="'.$_POST['categorie'].'">'.utf8_encode($rowi['name']).'</option>';
                        }
                        else{
                            echo '<option value="">Choisir catégorie</option>';
                        }
                        ?>
                        <?php
                            if($rowCount > 0){
                                while($row = $query->fetch_assoc()){ 
                                    echo '<option value="'.$row['id'].'">'.utf8_encode($row['name']).'</option>';
                                }
                            }else{
                                echo '<option value="">Catégorie non disponible</option>';
                        }
                        ?>
                  </select>
              </div>
              <div class="col-sm-4">
                    <select id="cours" class="form-control" name="cours">
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
                   // echo ' 
                    ?>
                    <div class="col-sm-4" align="center">
                    Date début formation:
                    <input id="formation" type="date" name="debutf" ><br><br>
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
                    <div class="col-sm-6" align="center">
                    Unité:
                    <input id="filtreunite" type="text" name="unite" value="<?php  if (isset($_POST['unite'])){echo htmlspecialchars($_POST['unite']); } ?> "><br><br>
                    </div>
                    <div class="col-sm-6" align="center">
                    <label><input type="checkbox" class="agreeunite"> Activer filtre unité</label>
                    </div>

                    <div class="col-sm-6" align="center">
                    Manager:
                    <input id="filtremanager" type="text" name="manager" value="<?php  if (isset($_POST['manager'])){echo htmlspecialchars($_POST['manager']); } ?> "><br><br>
                    </div>
                    <div class="col-sm-6" align="center">
                    <label><input type="checkbox" class="agreemanager"> Activer filtre manager</label>
                    </div>

                    <div class="col-sm-6" align="center">
                    dga:
                    <input id="filtredga" type="text" name="dga" value="<?php  if (isset($_POST['dga'])){echo htmlspecialchars($_POST['dga']); } ?> "><br><br>
                    </div>
                    <div class="col-sm-6" align="center">
                    <label><input type="checkbox" class="agreedga"> Activer filtre dga</label>
                    </div>

                    <div class="col-sm-6" align="center">
                    direction:
                    <input id="filtredirection" type="text" name="direction" value="<?php  if (isset($_POST['direction'])){echo htmlspecialchars($_POST['direction']); } ?> "><br><br>
                    </div>
                    <div class="col-sm-6" align="center">
                    <label><input type="checkbox" class="agreedirection"> Activer filtre direction</label>
                    </div>
                         ';
                         <?php
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
        if(!isset($_POST['debutf']) && !isset($_POST['finf']) && !isset($_POST['debutr']) && !isset($_POST['finr'])
        && !isset($_POST['unite']) && !isset($_POST['manager']) && !isset($_POST['dga']) && !isset($_POST['direction']))
        { // Nothing selected
            list($query0,$sql0) = sql0v1($db,$selected_val);
        }
        if(isset($_POST['debutf']) && isset($_POST['finf']) )
        { // Date formation
            $time1 = strtotime($_POST['debutf'].' 02:00');
            $time2 = strtotime($_POST['finf'].' 02:00');
            list($query0,$sql0) = sql0v2($db,$selected_val,$time1,$time2);
        }
        if(isset($_POST['debutr']) && isset($_POST['finr']) )
        { // Date recrutement
            $time3 = strtotime($_POST['debutr'].' 02:00');
            $time4 = strtotime($_POST['finr'].' 02:00');
            list($query0,$sql0) = sql0dr($db,$selected_val,$time3,$time4);
        }
        if(isset($_POST['debutf']) && isset($_POST['finf']) && isset($_POST['debutr']) && isset($_POST['finr']))
        { // Date formation && Date recrutement
            $time1 = strtotime($_POST['debutf'].' 02:00');
            $time2 = strtotime($_POST['finf'].' 02:00');
            $time3 = strtotime($_POST['debutr'].' 02:00');
            $time4 = strtotime($_POST['finr'].' 02:00');
            list($query0,$sql0) = sql0v3($db,$selected_val,$time1,$time2,$time3,$time4);   
        }
        if(isset($_POST['unite']))
        {// Unité
            $unite = htmlspecialchars($_POST['unite']);
            list($query0,$sql0) = sql0unite($db,$selected_val,$unite); 
        }
        if(isset($_POST['manager']))
        {// Manager
            $manager = htmlspecialchars($_POST['manager']);
            list($query0,$sql0) = sql0manager($db,$selected_val,$manager); 
        }
        if (isset($_POST['dga']))
        { // Dga
            $dga = htmlspecialchars($_POST['dga']);
            list($query0,$sql0) = sql0dga($db,$selected_val,$dga); 
        }
        if (isset($_POST['direction'])) 
        { // Direction
            $direction = htmlspecialchars($_POST['direction']);
            list($query0,$sql0) = sql0direction($db,$selected_val,$direction);
        }
        if (isset($_POST['unite']) && isset($_POST['manager']) && isset($_POST['dga']) && isset($_POST['direction'])) 
        { // All filters
            $unite = htmlspecialchars($_POST['unite']);
            $manager = htmlspecialchars($_POST['manager']);
            $dga = htmlspecialchars($_POST['dga']);
            $direction = htmlspecialchars($_POST['direction']);
            list($query0,$sql0) = sql0filters($db,$selected_val,$unite,$manager,$dga,$direction);
        }
        if(isset($_POST['debutf']) && isset($_POST['finf']) && isset($_POST['debutr']) && isset($_POST['finr'])
        && isset($_POST['unite']) && isset($_POST['manager']) && isset($_POST['dga']) && isset($_POST['direction']))
        { // ALL
            $time1 = strtotime($_POST['debutf'].' 02:00');
            $time2 = strtotime($_POST['finf'].' 02:00');
            $time3 = strtotime($_POST['debutr'].' 02:00');
            $time4 = strtotime($_POST['finr'].' 02:00');
            $unite = htmlspecialchars($_POST['unite']);
            $manager = htmlspecialchars($_POST['manager']);
            $dga = htmlspecialchars($_POST['dga']);
            $direction = htmlspecialchars($_POST['direction']);
            list($query0,$sql0) = sql0v4($db,$selected_val,$time1,$time2,$time3,$time4,$unite,$manager,$dga,$direction);
        }
        if (isset($_POST['debutf']) && isset($_POST['finf']) && isset($_POST['unite']) && isset($_POST['manager']) 
        && isset($_POST['dga']) && isset($_POST['direction'])) 
        { // All filters with date formation
            $time1 = strtotime($_POST['debutf'].' 02:00');
            $time2 = strtotime($_POST['finf'].' 02:00');
            $unite = htmlspecialchars($_POST['unite']);
            $manager = htmlspecialchars($_POST['manager']);
            $dga = htmlspecialchars($_POST['dga']);
            $direction = htmlspecialchars($_POST['direction']);
            list($query0,$sql0) = sql0v5($db,$selected_val,$time1,$time2,$unite,$manager,$dga,$direction);
        }
        if(isset($_POST['debutr']) && isset($_POST['finr'])
        && isset($_POST['unite']) && isset($_POST['manager']) && isset($_POST['dga']) && isset($_POST['direction']))
        { // ALL filters with date recrutement
            $time3 = strtotime($_POST['debutr'].' 02:00');
            $time4 = strtotime($_POST['finr'].' 02:00');
            $unite = htmlspecialchars($_POST['unite']);
            $manager = htmlspecialchars($_POST['manager']);
            $dga = htmlspecialchars($_POST['dga']);
            $direction = htmlspecialchars($_POST['direction']);
            list($query0,$sql0) = sql0v6($db,$selected_val,$time3,$time4,$unite,$manager,$dga,$direction);
        }
        list($query1,$sql1) = sql1($db);
        
        switch ($_GET['rapport']) {
            case '1':
                if (mysqli_num_rows($query0) == 0) {
                    echo '<div class="alert alert-danger" role="alert">
                    Aucun résultat trouvé pour cette requête !
                  </div>';
                }
                else {
                    $sql = "create table if not exists testtemp".$selected_val."(
                        id INT PRIMARY KEY AUTO_INCREMENT,
                        req LONGTEXT NOT NULL,
                        start_at TIME)
                        ";
                    $db->query($sql);
                    table($query0,$sql0);
                    $sql1 = "insert into testtemp".$selected_val." (req,start_at) values ( ";
                    $sql1 .= ' " ';
                    $sql1 .= $sql0.' " ,';
                    $sql1 .= ' " ';
                    $sql1 .= date("h:i:s") . ' " )'; 
                    $db->query($sql1);
                }
                break;    
            case '2':
                if (mysqli_num_rows($query1) == 0) {
                    echo '<div class="alert alert-danger" role="alert">
                    Aucun résultat trouvé pour cette requête !
                  </div>';
                }
                else {
                    table($query1,$sql1);
                }
                break;
        }
        }    
    include './includes/footer2.php';
?>
        
      

  
