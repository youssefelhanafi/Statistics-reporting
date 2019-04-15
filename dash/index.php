<?php
session_start();

include_once('includes/header.php');

include './config/dbConfig.php';
//Fetch all the categorie data
$query = $db->query("SELECT id,name from mdl_course_categories order by name");

//Count total number of rows
$rowCount = $query->num_rows;
?>
<div class="container">
    <form action="" method="post">
        <div class="p-2 bg-primary" align="center">
            <h1>Admin Dashboard</h1>
        </div>
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

                            
        </div>
        <div class="row">
            <div class="col-sm-4" align="center">
                    Direction : 
                    <input type="text" name="debut"><br><br>
                    </div>
                    <div class="col-sm-4" align="center">
                    DGA :
                    <input type="text" name="fin"><br><br>
                    </div>
                    <div class="col-sm-4" align="center">
                        Unité : 
                    <input type="text" class="agree">
                    </div>
                    <div class="col-sm-12" align="center">
                    <input type="submit" name="SubmitButton" class="btn btn-primary" value="Soumettre" /><br><br>
                </div>
        </div>
    </form>

    <?php

    if(!isset($_POST['SubmitButton'])){
        $selected_val = 'NULL';
        //header("Location: index.php"); /* Redirect browser */

    }
    else{
        $selected_val = $_POST['activite'];
    }
?>
<?php
// Custom queries 
// Query 1
$query1 = "SELECT 
count(distinct u.id) as nbr
from mdl_course c
JOIN mdl_context AS ctx ON c.id = ctx.instanceid
JOIN mdl_role_assignments AS ra ON ra.contextid = ctx.id
LEFT JOIN mdl_enrol AS er ON er.courseid = c.id
JOIN mdl_user AS u ON u.id = ra.userid
LEFT JOIN mdl_user_enrolments AS enr ON enr.enrolid = er.id
JOIN mdl_grade_grades AS gg ON gg.userid = u.id
JOIN mdl_grade_items AS gi ON gi.id = gg.itemid and gi.itemmodule='quiz'
JOIN mdl_course_categories AS cc ON cc.id = c.category
where (gi.courseid = c.id  and enr.userid = u.id and enr.status = 0  ) and gi.id = ".$selected_val." 
and ROUND(gg.finalgrade,2) > 0 AND   FROM_UNIXTIME(gg.timemodified,'%d/%m/%Y') is not NULL
";
$result1 = mysqli_query($db, $query1);

if (mysqli_num_rows($result1) > 0) {
    // output data of each row
    while($row1 = mysqli_fetch_assoc($result1)) {
        $nbrtermine =  $row1['nbr'];
    }
} else {
    $nbrtermine = 0;
}


// Query 2
$query2 = "SELECT 
count(distinct u.id) as nbr
from mdl_course c
JOIN mdl_context AS ctx ON c.id = ctx.instanceid
JOIN mdl_role_assignments AS ra ON ra.contextid = ctx.id
LEFT JOIN mdl_enrol AS er ON er.courseid = c.id
JOIN mdl_user AS u ON u.id = ra.userid
LEFT JOIN mdl_user_enrolments AS enr ON enr.enrolid = er.id
JOIN mdl_grade_grades AS gg ON gg.userid = u.id
JOIN mdl_grade_items AS gi ON gi.id = gg.itemid and gi.itemmodule='quiz'
JOIN mdl_course_categories AS cc ON cc.id = c.category
where (gi.courseid = c.id  and enr.userid = u.id and enr.status = 0  ) and gi.id =".$selected_val."";
$result2 = mysqli_query($db, $query2);

if (mysqli_num_rows($result2) > 0) {
    // output data of each row
    while($row2 = mysqli_fetch_assoc($result2)) {
        $nbrinvite =  $row2['nbr'];
    }
} else {
    echo "0 results";
}

// Query 3
$query3 = "SELECT 
count(*) as nbrencours 
from mdl_course c
JOIN mdl_context AS ctx ON c.id = ctx.instanceid
JOIN mdl_role_assignments AS ra ON ra.contextid = ctx.id
LEFT JOIN mdl_enrol AS er ON er.courseid = c.id
JOIN mdl_user AS u ON u.id = ra.userid
LEFT JOIN mdl_user_enrolments AS enr ON enr.enrolid = er.id
JOIN mdl_grade_grades AS gg ON gg.userid = u.id
JOIN mdl_grade_items AS gi ON gi.id = gg.itemid and gi.itemmodule='quiz'
JOIN mdl_course_categories AS cc ON cc.id = c.category
where (gi.courseid = c.id  and enr.userid = u.id and enr.status = 0  ) and gi.id = ".$selected_val." and ROUND(gg.finalgrade,2) is null";
$result3 = mysqli_query($db, $query3);

if (mysqli_num_rows($result3) > 0) {
    // output data of each row
    while($row3 = mysqli_fetch_assoc($result3)) {
        $nbrstatusencours =  $row3['nbrencours'];
    }
} else {
    echo "0 results";
}

// Query 4
$query4 = "SELECT 
count(*) as nbrjamais
from mdl_course c
JOIN mdl_context AS ctx ON c.id = ctx.instanceid
JOIN mdl_role_assignments AS ra ON ra.contextid = ctx.id
LEFT JOIN mdl_enrol AS er ON er.courseid = c.id
JOIN mdl_user AS u ON u.id = ra.userid
LEFT JOIN mdl_user_enrolments AS enr ON enr.enrolid = er.id
JOIN mdl_grade_grades AS gg ON gg.userid = u.id
JOIN mdl_grade_items AS gi ON gi.id = gg.itemid and gi.itemmodule='quiz'
JOIN mdl_course_categories AS cc ON cc.id = c.category
where (gi.courseid = c.id  and enr.userid = u.id and enr.status = 0  ) 
and gi.id = ".$selected_val." and (select count(*) from mdl_quiz_attempts A,mdl_quiz B where A.userid = u.id and B.course = c.id and A.quiz = B.id)=0
and ROUND(gg.finalgrade,2) is null";
$result4 = mysqli_query($db, $query4);

if (mysqli_num_rows($result4) > 0) {
    // output data of each row
    while($row4 = mysqli_fetch_assoc($result4)) {
        $nbrstatusjamais =  $row4['nbrjamais'];
    }
} else {
    echo "0 results";
}

//END Custom queries

// Custom Calcu
if ($nbrinvite == 0) {
    $tauxrealisation = 0;
    $tauxparticipation = 0;
    $tauxechec = 0;
}
else{
    $tauxrealisation = round($nbrtermine / ($nbrinvite /100), 2) ;
    $tauxparticipation = round(($nbrtermine +$nbrstatusencours)  / $nbrinvite , 2) * 100 ;
    $tauxechec = round($nbrstatusencours/$nbrinvite,2) * 100  ;
}

if(is_nan($tauxrealisation)) $tauxrealisation = 0 ;
if(is_nan($tauxparticipation)) $tauxparticipation = 0 ;
if(is_nan($tauxechec )) $tauxechec = 0 ;



?>



    
    <!-- /.row -->
    <div class="row">
        <div class="col-sm-4">
                <div class="panel panel-green">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col-xs-3">
                                <i class="fa fa-tasks fa-5x"></i>
                            </div>
                            <div class="col-xs-9 text-right">
                                <div>Taux de réalisation</div>
                                <div class="huge"><?php echo $tauxrealisation; ?> %</div>
                                
                            </div>
                        </div>
                    </div>
                    
                </div>
        </div>

            <div class="col-sm-4">
                <div class="panel panel-green">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col-xs-3">
                                <i class="fa fa-tasks fa-5x"></i>
                            </div>
                            <div class="col-xs-9 text-right">
                                <div>Taux de participation</div>
                                <div class="huge"><?php echo $tauxparticipation; ?> %</div>
                                
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>

            <div class="col-sm-4">
                <div class="panel panel-green">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col-xs-3">
                                <i class="fa fa-tasks fa-5x"></i>
                            </div>
                            <div class="col-xs-9 text-right">
                                <div>Taux d'échec</div>
                                <div class="huge"><?php echo $tauxechec; ?> %</div>
                                
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>


            <div class="col-sm-4">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col-xs-3">
                                <i class="fa fa-user fa-5x"></i>
                            </div>
                            <div class="col-xs-9 text-right">
                                <div>Collaborateur ayant validé la formation</div>
                                <div class="huge"><?php echo $nbrtermine; ?></div>
                                
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>

            <div class="col-sm-4">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col-xs-3">
                                <i class="fa fa-user fa-5x"></i>
                            </div>
                            <div class="col-xs-9 text-right">
                                <div>Collaborateurs n'ayant pas validé la formation</div>
                                <div class="huge"><?php echo $nbrstatusencours; ?></div>
                                
                            </div>
                        </div>
                    </div>
        
                </div>
            </div>

            <div class="col-sm-4">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col-xs-3">
                                <i class="fa fa-user fa-5x"></i>
                            </div>
                            <div class="col-xs-9 text-right">
                                <div >Collaborateurs n'ayant pas encore entamé a formation</div>
                                <div class="huge"><?php echo $nbrstatusjamais; ?></div>
                                
                            </div>
                        </div>
                    </div>
        
                </div>
            </div>
            
    </div>
    <div class="row">

        <div class="col-sm-6">
            <?php
            $dataPoints = array( 
            array("label"=>"Taux de réalisation", "y"=>$tauxrealisation),
            array("label"=>"Taux de participation", "y"=>$tauxparticipation),
            array("label"=>"Taux d'échec", "y"=>$tauxechec)
            );
            $dataPoints1 = array( 
            array("label"=>"Collaborateur ayant validé la formation", "y"=>$nbrtermine),
            array("label"=>"Collaborateurs n'ayant pas validé la formation", "y"=>$nbrstatusencours),
            array("label"=>"Collaborateurs n'ayant pas encore entamé a formation", "y"=>$nbrstatusjamais)
            )
            ?>
            <script>
            window.onload = function() {
            
            
            var chart = new CanvasJS.Chart("chartContainer", {
                animationEnabled: true,
                title: {
                    text: "Indicateurs de formation"
                },
                subtitles: [{
                text: "Taux"
                }],
                data: [{
                    type: "pie",
                    yValueFormatString: "#,##0.00\"%\"",
                    indexLabel: "{label} ({y})",
                    dataPoints: <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK); ?>
                }]
            });
            chart.render();

            var chart1 = new CanvasJS.Chart("chartContainer1", {
                animationEnabled: true,
                title: {
                    text: "Indicateurs de formation"
                },
                subtitles: [{
                text: "Collaborateurs"
                }],
                data: [{
                    type: "pie",
                    yValueFormatString: "#,##0.00\"%\"",
                    indexLabel: "{label} ({y})",
                    dataPoints: <?php echo json_encode($dataPoints1, JSON_NUMERIC_CHECK); ?>
                }]
            });
            chart1.render();
            
            }
            </script>
        
            <div id="chartContainer" style="height: 370px; width: 100%;"></div>
            
            
        </div>

        <div class="col-sm-6" >
            
                <div id="chartContainer1" style="height: 370px; width: 100%;"></div>
                <?php 
                        $a = 'a';
                        $b = 'b';
                        $c = 'c';
                        $d = 'd';
                        $e = 'e';
                        $f = 'f';
                ?>
        </div>
        <div class="col-sm-12" align="center">
            <form action="./export/xls.php" method="post">
                <input type="hidden" name="realisation" id="hiddenField" value="<?php echo  $tauxrealisation ?>"/>
                <input type="hidden" name="participation" id="hiddenField" value="<?php echo $tauxparticipation  ?>"/>
                <input type="hidden" name="echec" id="hiddenField" value="<?php echo $tauxechec ?>"/>
                <input type="hidden" name="valide" id="hiddenField" value="<?php echo $nbrtermine ?>"/>
                <input type="hidden" name="nonvalide" id="hiddenField" value="<?php echo $nbrstatusencours ?>"/>
                <input type="hidden" name="nonentame" id="hiddenField" value="<?php echo $nbrstatusjamais ?>"/>
                <input type="submit" name="SubmitButton" class="btn btn-success" value="Télécharger Excel" /><br>
                <a href="http://localhost/moodle/">Accueil<a/>
            </form>
        </div>
        
    </div>
</div>
    <!-- /.row -->

<!-- /#page-wrapper -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type="text/javascript">
$(document).ready(function(){
    $('#categorie').on('change',function(){
        var categorieID = $(this).val();
        if(categorieID){
            $.ajax({
            type:'POST',
            url:'ajaxData.php',
            data:'categorie_id='+categorieID,
            success:function(html){
            $('#cours').html(html);
            $('#activite').html('<option value="">Sélectionnez le cours en premier</option>'); 
            }
            }); 
        }else{
            $('#cours').html('<option value="">Sélectionnez la catégorie en premier</option>');
            $('#activite').html('<option value="">Sélectionnez le cours en premier</option>'); 
        }
    });

    $('#cours').on('change',function(){
        var coursID = $(this).val();
        if(coursID){
            $.ajax({
            type:'POST',
            url:'ajaxData.php',
            data:'cours_id='+coursID,
            success:function(html){
            $('#activite').html(html);
            }
        }); 
        }else{
             $('#activite').html('<option value="">Sélectionnez le cours en premier</option>'); 
        }
    });
});
</script>


<?php include_once('includes/footer.php'); ?>
