<?php
include './config/dbConfig.php';
include './includes/req.php';

//Fetch all the categorie data
$sql = "SELECT id,name from mdl_course_categories order by name";
$query = $db->query($sql);

//Count total number of rows
$rowCount = $query->num_rows;

//html table creator
function table($result,$query) {
    $result->fetch_array( MYSQLI_ASSOC );
    echo '<form action="/moodle352/blocks/filtered_reporting/panel/export/xls.php"  method="POST">';
    echo '<table style="max-width:100%;
    margin: auto;
    border: 2px solid black;">';
    tableHead( $result );
    tableBody( $result );
    echo '</table>';
    echo '<br>';
    echo '<div class="container"><div class="col-sm-12" align="center">';
    echo '<input type="submit" name="SubmitButton" class="btn btn-success" value="Télécharger Excel" />';
    echo '<input type="hidden" name="date" id="hiddenField" value="'.$query.'"/>';
    echo '<br>';
    echo '<a href="http://localhost/moodle/">Accueil<a/>';
    echo "</div></div>";
}

function tableHead( $result ) {
    echo '<thead style="max-width:100%;
    margin: auto;
    border: 2px solid black;">';
    foreach ( $result as $x ) {
    echo '<tr>';
    foreach ( $x as $k => $y ) {
        echo '<th>' . ucfirst( $k ) . '</th>';
    }
    echo '</tr>';
    break;
    }
    echo '</thead>';
}

function tableBody( $result ) {
    echo '<tbody>';
    foreach ( $result as $x ) {
    echo '<tr>';
    foreach ( $x as $y ) {
        echo '<td>' . $y . '</td>';
    }
    echo '</tr>';
    }
    echo '</tbody>';
}

include'./includes/header2.php';
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
                    <input type="date" name="debut"><br><br>
                    </div>
                    <div class="col-sm-4" align="center">
                    Date fin formation:
                    <input type="date" name="fin"><br><br>
                    </div>
                    <div class="col-sm-4" align="center">
                    <label><input type="checkbox" class="agree"> Activer date formation</label>
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
    if(!isset($_POST['SubmitButton'])){
        $selected_val = 'NULL';
    }
    else{
        $selected_val = $_POST['activite'];
        if(!isset($_POST['debut']) && !isset($_POST['fin']) ){
            list($query0,$sql0) = sql0v1($db,$selected_val);
            }
        else{
            
            $time1 = strtotime($_POST['debut'].' 02:00');
            $time2 = strtotime($_POST['fin'].' 02:00');
            //$date_inscription = strval(date('d/m/Y',$time));
            list($query0,$sql0) = sql0v2($db,$selected_val,$time1,$time2);
            }


        list($query1,$sql1) = sql1($db);
        
        switch ($_GET['rapport']) {
            case '1':
                $vartest = strval(table($query0,$sql0));
                print $vartest;
                //table($query0,$sql0);
                break;
            
            case '2':
                table($query1,$sql1); 
                break;
        }
    } 
    include './includes/footer2.php';
?>
        
      

  
