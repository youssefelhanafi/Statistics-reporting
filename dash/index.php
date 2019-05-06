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
                    <select id="categorie" class="form-control" name="categorie">
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
        <script>
            $(document).ready(function(){
            $('form input[type="text"]').prop("disabled", true);
            $(".agreetxt").click(function(){
                    if($(this).prop("checked") == true){
                        $('form input[type="text"]').prop("disabled", false);
                    }
                    else if($(this).prop("checked") == false){
                        $('form input[type="text"]').prop("disabled", true);
                    }
                });
            });
        </script>
            
                    <div class="col-sm-12" align="center">
                    <div class="col-sm-12" align="center">
                    </div>
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
/* echo $_POST['categorie'];
echo '<br>';
echo $_POST['activite'];
echo '<br>'; */
if (isset($_POST['categorie']) && empty($_POST['activite'])) {

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
    where (gi.courseid = c.id  and enr.userid = u.id and enr.status = 0  ) and cc.id = ".$_POST['categorie']." 
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
    $query11 = "SELECT 
    u.idnumber as matricule,
    u.firstname,
    u.lastname,
    f24.data as direction,
    f23.data as dga,
    f12.data as unite
    from mdl_course c
    JOIN mdl_context AS ctx ON c.id = ctx.instanceid
    JOIN mdl_role_assignments AS ra ON ra.contextid = ctx.id
    LEFT JOIN mdl_enrol AS er ON er.courseid = c.id
    JOIN mdl_user AS u ON u.id = ra.userid
    LEFT JOIN mdl_user_enrolments AS enr ON enr.enrolid = er.id
    JOIN mdl_grade_grades AS gg ON gg.userid = u.id
    JOIN mdl_grade_items AS gi ON gi.id = gg.itemid and gi.itemmodule='quiz'
    JOIN mdl_course_categories AS cc ON cc.id = c.category
    LEFT JOIN mdl_user_info_data AS f24  ON u.id = f24.userid and f24.fieldid=24 
    LEFT JOIN mdl_user_info_data AS f23  ON u.id = f23.userid and f23.fieldid=23 
    LEFT JOIN mdl_user_info_data AS f12  ON u.id = f12.userid and f12.fieldid=12 
    where (gi.courseid = c.id  and enr.userid = u.id and enr.status = 0  ) and cc.id = ".$_POST['categorie']."
    and ROUND(gg.finalgrade,2) > 0 AND   FROM_UNIXTIME(gg.timemodified,'%d/%m/%Y') is not NULL
    ";
    $result11 = mysqli_query($db,$query11);
    $prenomvalide = array();
    $nomvalide = array();
    $matricule1 = array();
    $direction1 = array();
    $dga1 = array();
    $unite1 = array();
    while ($row11 = mysqli_fetch_assoc($result11)) {
        array_push($prenomvalide,$row11['firstname']);
        array_push($nomvalide,$row11['lastname']);
        array_push($matricule1,$row11['matricule']);
        array_push($direction1,$row11['direction']);
        array_push($dga1,$row11['dga']);
        array_push($unite1,$row11['unite']);

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

    // Query 3 Collaborateurs n'ayant pas validé la formation
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
    where (gi.courseid = c.id  and enr.userid = u.id and enr.status = 0  ) and cc.id = ".$_POST['categorie']." and ROUND(gg.finalgrade,2) is null";
    $result3 = mysqli_query($db, $query3);

    if (mysqli_num_rows($result3) > 0) {
        // output data of each row
        while($row3 = mysqli_fetch_assoc($result3)) {
            $nbrstatusencours =  $row3['nbrencours'];
        }
    } else {
        echo "0 results";
    }

    $query33 = "SELECT 
    u.idnumber as matricule,
    u.firstname,
    u.lastname,
    f24.data as direction,
    f23.data as dga,
    f12.data as unite
    from mdl_course c
    JOIN mdl_context AS ctx ON c.id = ctx.instanceid
    JOIN mdl_role_assignments AS ra ON ra.contextid = ctx.id
    LEFT JOIN mdl_enrol AS er ON er.courseid = c.id
    JOIN mdl_user AS u ON u.id = ra.userid
    LEFT JOIN mdl_user_enrolments AS enr ON enr.enrolid = er.id
    LEFT JOIN mdl_user_info_data AS f24  ON u.id = f24.userid and f24.fieldid=24 
    LEFT JOIN mdl_user_info_data AS f23  ON u.id = f23.userid and f23.fieldid=23 
    LEFT JOIN mdl_user_info_data AS f12  ON u.id = f12.userid and f12.fieldid=12 
    JOIN mdl_grade_grades AS gg ON gg.userid = u.id
    JOIN mdl_grade_items AS gi ON gi.id = gg.itemid and gi.itemmodule='quiz'
    JOIN mdl_course_categories AS cc ON cc.id = c.category
    where (gi.courseid = c.id  and enr.userid = u.id and enr.status = 0  ) and cc.id = ".$_POST['categorie']." and ROUND(gg.finalgrade,2) is null
    ";
    $result33 = mysqli_query($db,$query33);
    $prenomnonvalide = array();
    $nomnonvalide = array();
    $matricule2 = array();
    $direction2 = array();
    $dga2 = array();
    $unite2 = array();
    while ($row33 = mysqli_fetch_assoc($result33)) {
        array_push($prenomnonvalide,$row33['firstname']);
        array_push($nomnonvalide,$row33['lastname']);
        array_push($matricule2,$row33['matricule']);
        array_push($direction2,$row33['direction']);
        array_push($dga2,$row33['dga']);
        array_push($unite2,$row33['unite']);
    }

    // Query 4 Collaborateurs n'ayant pas encore entamé a formation
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
    and cc.id = ".$_POST['categorie']." and (select count(*) from mdl_quiz_attempts A,mdl_quiz B where A.userid = u.id and B.course = c.id and A.quiz = B.id)=0
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

    $query44 = "SELECT 
    u.idnumber as matricule,
    u.firstname,
    u.lastname,
    f24.data as direction,
    f23.data as dga,
    f12.data as unite
    from mdl_course c
    JOIN mdl_context AS ctx ON c.id = ctx.instanceid
    JOIN mdl_role_assignments AS ra ON ra.contextid = ctx.id
    LEFT JOIN mdl_enrol AS er ON er.courseid = c.id
    JOIN mdl_user AS u ON u.id = ra.userid
    LEFT JOIN mdl_user_enrolments AS enr ON enr.enrolid = er.id
    LEFT JOIN mdl_user_info_data AS f24  ON u.id = f24.userid and f24.fieldid=24 
    LEFT JOIN mdl_user_info_data AS f23  ON u.id = f23.userid and f23.fieldid=23 
    LEFT JOIN mdl_user_info_data AS f12  ON u.id = f12.userid and f12.fieldid=12 
    JOIN mdl_grade_grades AS gg ON gg.userid = u.id
    JOIN mdl_grade_items AS gi ON gi.id = gg.itemid and gi.itemmodule='quiz'
    JOIN mdl_course_categories AS cc ON cc.id = c.category
    where (gi.courseid = c.id  and enr.userid = u.id and enr.status = 0  ) 
    and cc.id = ".$_POST['categorie']." and (select count(*) from mdl_quiz_attempts A,mdl_quiz B where A.userid = u.id and B.course = c.id and A.quiz = B.id)=0
    and ROUND(gg.finalgrade,2) is null
    ";
    $result44 = mysqli_query($db,$query44);
    $prenomentame = array();
    $nomentame = array();
    $matricule3 = array();
    $direction3 = array();
    $dga3 = array();
    $unite3 = array();
    while ($row44 = mysqli_fetch_assoc($result44)) {
        array_push($prenomentame,$row44['firstname']);
        array_push($nomentame,$row44['lastname']);
        array_push($matricule3,$row44['matricule']);
        array_push($direction3,$row44['direction']);
        array_push($dga3,$row44['dga']);
        array_push($unite3,$row44['unite']);
    }
}
elseif(isset($_POST['categorie']) && isset($_POST['activite']) && !empty($_POST['activite'])){
    /* echo '12';
    echo '<br>';
    echo 'categorie && activité'.$selected_val.' -- '.$_POST['categorie'];
    echo '<br>'; */

    // Custom queries 
    // Query 1 Collaborateur ayant validé la formation
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
    $query11 = "SELECT 
    u.idnumber as matricule,
    u.firstname,
    u.lastname,
    f24.data as direction,
    f23.data as dga,
    f12.data as unite
    from mdl_course c
    JOIN mdl_context AS ctx ON c.id = ctx.instanceid
    JOIN mdl_role_assignments AS ra ON ra.contextid = ctx.id
    LEFT JOIN mdl_enrol AS er ON er.courseid = c.id
    JOIN mdl_user AS u ON u.id = ra.userid
    LEFT JOIN mdl_user_enrolments AS enr ON enr.enrolid = er.id
    JOIN mdl_grade_grades AS gg ON gg.userid = u.id
    JOIN mdl_grade_items AS gi ON gi.id = gg.itemid and gi.itemmodule='quiz'
    JOIN mdl_course_categories AS cc ON cc.id = c.category
    LEFT JOIN mdl_user_info_data AS f24  ON u.id = f24.userid and f24.fieldid=24 
    LEFT JOIN mdl_user_info_data AS f23  ON u.id = f23.userid and f23.fieldid=23 
    LEFT JOIN mdl_user_info_data AS f12  ON u.id = f12.userid and f12.fieldid=12 
    where (gi.courseid = c.id  and enr.userid = u.id and enr.status = 0  ) and gi.id = ".$selected_val."
    and ROUND(gg.finalgrade,2) > 0 AND   FROM_UNIXTIME(gg.timemodified,'%d/%m/%Y') is not NULL
    ";
    $result11 = mysqli_query($db,$query11);
    $prenomvalide = array();
    $nomvalide = array();
    $matricule1 = array();
    $direction1 = array();
    $dga1 = array();
    $unite1 = array();
    while ($row11 = mysqli_fetch_assoc($result11)) {
        array_push($prenomvalide,$row11['firstname']);
        array_push($nomvalide,$row11['lastname']);
        array_push($matricule1,$row11['matricule']);
        array_push($direction1,$row11['direction']);
        array_push($dga1,$row11['dga']);
        array_push($unite1,$row11['unite']);

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

    // Query 3 Collaborateurs n'ayant pas validé la formation
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

    $query33 = "SELECT 
    u.idnumber as matricule,
    u.firstname,
    u.lastname,
    f24.data as direction,
    f23.data as dga,
    f12.data as unite
    from mdl_course c
    JOIN mdl_context AS ctx ON c.id = ctx.instanceid
    JOIN mdl_role_assignments AS ra ON ra.contextid = ctx.id
    LEFT JOIN mdl_enrol AS er ON er.courseid = c.id
    JOIN mdl_user AS u ON u.id = ra.userid
    LEFT JOIN mdl_user_enrolments AS enr ON enr.enrolid = er.id
    LEFT JOIN mdl_user_info_data AS f24  ON u.id = f24.userid and f24.fieldid=24 
    LEFT JOIN mdl_user_info_data AS f23  ON u.id = f23.userid and f23.fieldid=23 
    LEFT JOIN mdl_user_info_data AS f12  ON u.id = f12.userid and f12.fieldid=12 
    JOIN mdl_grade_grades AS gg ON gg.userid = u.id
    JOIN mdl_grade_items AS gi ON gi.id = gg.itemid and gi.itemmodule='quiz'
    JOIN mdl_course_categories AS cc ON cc.id = c.category
    where (gi.courseid = c.id  and enr.userid = u.id and enr.status = 0  ) and gi.id = ".$selected_val." and ROUND(gg.finalgrade,2) is null
    ";
    $result33 = mysqli_query($db,$query33);
    $prenomnonvalide = array();
    $nomnonvalide = array();
    $matricule2 = array();
    $direction2 = array();
    $dga2 = array();
    $unite2 = array();
    while ($row33 = mysqli_fetch_assoc($result33)) {
        array_push($prenomnonvalide,$row33['firstname']);
        array_push($nomnonvalide,$row33['lastname']);
        array_push($matricule2,$row33['matricule']);
        array_push($direction2,$row33['direction']);
        array_push($dga2,$row33['dga']);
        array_push($unite2,$row33['unite']);
    }

    // Query 4 Collaborateurs n'ayant pas encore entamé a formation
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

    $query44 = "SELECT 
    u.idnumber as matricule,
    u.firstname,
    u.lastname,
    f24.data as direction,
    f23.data as dga,
    f12.data as unite
    from mdl_course c
    JOIN mdl_context AS ctx ON c.id = ctx.instanceid
    JOIN mdl_role_assignments AS ra ON ra.contextid = ctx.id
    LEFT JOIN mdl_enrol AS er ON er.courseid = c.id
    JOIN mdl_user AS u ON u.id = ra.userid
    LEFT JOIN mdl_user_enrolments AS enr ON enr.enrolid = er.id
    LEFT JOIN mdl_user_info_data AS f24  ON u.id = f24.userid and f24.fieldid=24 
    LEFT JOIN mdl_user_info_data AS f23  ON u.id = f23.userid and f23.fieldid=23 
    LEFT JOIN mdl_user_info_data AS f12  ON u.id = f12.userid and f12.fieldid=12 
    JOIN mdl_grade_grades AS gg ON gg.userid = u.id
    JOIN mdl_grade_items AS gi ON gi.id = gg.itemid and gi.itemmodule='quiz'
    JOIN mdl_course_categories AS cc ON cc.id = c.category
    where (gi.courseid = c.id  and enr.userid = u.id and enr.status = 0  ) 
    and gi.id = ".$selected_val." and (select count(*) from mdl_quiz_attempts A,mdl_quiz B where A.userid = u.id and B.course = c.id and A.quiz = B.id)=0
    and ROUND(gg.finalgrade,2) is null
    ";
    $result44 = mysqli_query($db,$query44);
    $prenomentame = array();
    $nomentame = array();
    $matricule3 = array();
    $direction3 = array();
    $dga3 = array();
    $unite3 = array();
    while ($row44 = mysqli_fetch_assoc($result44)) {
        array_push($prenomentame,$row44['firstname']);
        array_push($nomentame,$row44['lastname']);
        array_push($matricule3,$row44['matricule']);
        array_push($direction3,$row44['direction']);
        array_push($dga3,$row44['dga']);
        array_push($unite3,$row44['unite']);
    }

//END Custom queries




}
/* if (isset($_POST['categorie']) && isset($_POST['activite'])) {
    echo '1';
}
elseif(isset($_POST['categorie']) && !isset($_POST['activite'])){
    echo '0';
} */


// Custom Calcu
if ($nbrinvite == 0) {
    $tauxrealisation = 0;
    $tauxparticipation = 0;
    $tauxechec = 0;
}
else{
    $tauxrealisation = round($nbrtermine / ($nbrinvite /100), 2) ;
    $tauxparticipation = round(($nbrtermine +$nbrstatusencours)  / $nbrinvite , 2) * 100 ;
    //$tauxechec = round($nbrstatusencours/$nbrinvite,2) * 100  ;
    $tauxechec = $tauxparticipation - $tauxrealisation;
}

if(is_nan($tauxrealisation)) $tauxrealisation = 0 ;
if(is_nan($tauxparticipation)) $tauxparticipation = 0 ;
if(is_nan($tauxechec )) $tauxechec = 0 ;

?>

    <!-- /.row -->
    <div class="row">
        <div class="col-sm-6">
            <div class="col">
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

                <div class="col">
                    <div class="panel panel-green">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3">
                                    <i class="fa fa-tasks fa-5x"></i>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <div>Taux de participation</div>
                                    <div class="huge"><?php echo $tauxparticipation;?> %</div>
                                    
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </div>

                <div class="col">
                    <div class="panel panel-green">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3">
                                    <i class="fa fa-tasks fa-5x"></i>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <div>Taux d'échec</div>
                                    <div class="huge"><?php echo $tauxechec;?> %</div>
                                    
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </div>
        </div>
        <div class="col-sm-6">
        <div id="chartContainer" style="height: 370px; width: 100%;"></div>
        </div>

        <div class="col-sm-6">
            <div class="col">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col-xs-3">
                                <i class="fa fa-user fa-5x"></i>
                            </div>
                            <div class="col-xs-9 text-right">
                                <div>Collaborateur ayant validé la formation</div>
                                <div class="huge"><?php echo $nbrtermine;?></div>
                                
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>

            <div class="col">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col-xs-3">
                                <i class="fa fa-user fa-5x"></i>
                            </div>
                            <div class="col-xs-9 text-right">
                                <div>Collaborateurs n'ayant pas validé la formation</div>
                                <div class="huge"><?php echo $nbrstatusencours;?></div>
                                
                            </div>
                        </div>
                    </div>
        
                </div>
            </div>

            <div class="col">
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
        <div class="col-sm-6">
        <div id="chartContainer1" style="height: 370px; width: 100%;"></div>
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
                    yValueFormatString: "#,##0\"\"",
                    indexLabel: "{label} ({y})",
                    dataPoints: <?php echo json_encode($dataPoints1, JSON_NUMERIC_CHECK); ?>
                }]
            });
            chart1.render();
            
            }
            </script>
        
            
            
            
        </div>

        <div class="col-sm-6" >
            
                
        </div>
        <div class="col-sm-12" align="center">
            <form action="./export/xls.php" method="post">

                <input type="hidden" name="realisation" id="hiddenField" value="<?php echo  $tauxrealisation ?>"/>
                <input type="hidden" name="participation" id="hiddenField" value="<?php echo $tauxparticipation  ?>"/>
                <input type="hidden" name="echec" id="hiddenField" value="<?php echo $tauxechec ?>"/>
                <input type="hidden" name="valide" id="hiddenField" value="<?php echo $nbrtermine ?>"/>
                <input type="hidden" name="nonvalide" id="hiddenField" value="<?php echo $nbrstatusencours ?>"/>
                <input type="hidden" name="nonentame" id="hiddenField" value="<?php echo $nbrstatusjamais ?>"/>
                

                <input type="hidden" name="prenomvalide" id="hiddenField" value="<?php print_r($prenomvalide)  ?>"/>
                <input type="hidden" name="nomvalide" id="hiddenField" value="<?php print_r($nomvalide) ?>"/>
                <input type="hidden" name="matricule1" id="hiddenField" value="<?php print_r($matricule1)  ?>"/>
                <input type="hidden" name="direction1" id="hiddenField" value="<?php print_r($direction1) ?>"/>
                <input type="hidden" name="dga1" id="hiddenField" value="<?php print_r($dga1)  ?>"/>
                <input type="hidden" name="unite1" id="hiddenField" value="<?php print_r($unite1) ?>"/>


                <input type="hidden" name="prenomnonvalide" id="hiddenField" value="<?php print_r($prenomnonvalide)  ?>"/>
                <input type="hidden" name="nomnonvalide" id="hiddenField" value="<?php print_r($nomnonvalide) ?>"/>
                <input type="hidden" name="matricule2" id="hiddenField" value="<?php print_r($matricule2)  ?>"/>
                <input type="hidden" name="direction2" id="hiddenField" value="<?php print_r($direction2) ?>"/>
                <input type="hidden" name="dga2" id="hiddenField" value="<?php print_r($dga2)  ?>"/>
                <input type="hidden" name="unite2" id="hiddenField" value="<?php print_r($unite2) ?>"/>


                <input type="hidden" name="prenomentame" id="hiddenField" value="<?php print_r($prenomentame)  ?>"/>
                <input type="hidden" name="nomentame" id="hiddenField" value="<?php print_r($nomentame) ?>"/>
                <input type="hidden" name="matricule3" id="hiddenField" value="<?php print_r($matricule3)  ?>"/>
                <input type="hidden" name="direction3" id="hiddenField" value="<?php print_r($direction3) ?>"/>
                <input type="hidden" name="dga3" id="hiddenField" value="<?php print_r($dga3)  ?>"/>
                <input type="hidden" name="unite3" id="hiddenField" value="<?php print_r($unite3) ?>"/>


                <input type="submit" name="SubmitButton" class="btn btn-success" value="Télécharger Excel" /><br>
                <a href="http://10.9.121.157/moodle352/">Accueil</a>
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
