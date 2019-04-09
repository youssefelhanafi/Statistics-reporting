<?php
include './config/dbConfig.php';

//Fetch all the categorie data
$sql = "SELECT id,name from mdl_course_categories order by name";
$query = $db->query($sql);

//Count total number of rows
$rowCount = $query->num_rows;

//html table creator
function table($result,$query) {
    $result->fetch_array( MYSQLI_ASSOC );
    echo '<form action="test.php"  method="POST">';
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
                    <div class="col-sm-12" align="center">
                    Date début formation:
                    <input type="date" name="debut"><br><br>
                    </div>
                    <div class="col-sm-12" align="center">
                    Date fin formation:
                    <input type="date" name="fin"><br><br>
                    </div>
                    <div class="col-sm-12" align="center">
                    <label><input type="checkbox" class="agree"> Enable date</label>
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
        if(!isset($_POST['debut']) && !isset($_POST['fin'])){
            $sql0 = "SELECT distinct 
            cat.name AS 'CATEGORIE',c.fullname AS 'FORMATION', c.visible as 'VISIBILITE',
            CASE 
            WHEN gi.itemtype = 'course' 
            THEN CONCAT('Total des activités')
            ELSE gi.itemname
            END  AS 'ACTIVITE',u.idnumber as 'MATRICULE', u.firstname AS 'PRENOM',u.lastname AS 'NOM', 
            CASE WHEN u.suspended=1  then 'suspendu' WHEN u.deleted=1  
            then 'supprimé' else 'Actif' END As Etat ,
            FROM_UNIXTIME(enr.timecreated,'%d/%m/%Y') AS 'DATE INSCRIPTION',
            CASE
            WHEN 
            gi.itemmodule = 'quiz' 
            THEN(select DATE_FORMAT(FROM_UNIXTIME(cmc.timemodified), '%d/%m/%Y')  FROM mdl_course_modules_completion cmc  
            JOIN mdl_course_modules cm ON cmc.coursemoduleid = cm.id
            JOIN mdl_modules m ON cm.module = m.id
            where cmc.userid = u.id and cm.course = c.id 
            and  gi.iteminstance=cm.instance and m.id=16 )
            WHEN gi.itemmodule = 'scorm' 
            THEN (
            select DATE_FORMAT(FROM_UNIXTIME(cmc.timemodified), '%d/%m/%Y')  from   mdl_course_modules_completion cmc  
            JOIN mdl_course_modules cm ON cmc.coursemoduleid = cm.id
            JOIN mdl_modules m ON cm.module = m.id
            where cmc.userid = u.id and cm.course = c.id 
            and  gi.iteminstance=cm.instance and m.id=18
            )  
            END AS 'DATE_REALISATION',  
            (select distinct ROUND(gg.finalgrade,2) from mdl_grade_grades AS gg where  gg.itemid=gi.id and  gg.userid=u.id 
            ) as NOTE,
            CASE
            WHEN gi.itemmodule = 'quiz' THEN(select MAX(at.attempt) from mdl_quiz_attempts at,mdl_quiz q where at.quiz=q.id and at.userid=u.id and q.course=c.id and q.id=gi.iteminstance)
            WHEN gi.itemmodule = 'scorm' THEN (SELECT MAX(st.attempt) FROM  mdl_scorm_scoes_track AS st, mdl_scorm sc where sc.id=st.scormid and st.userid=u.id and sc.course=c.id and sc.id=gi.iteminstance)  
            END AS TENTATIVE,
            case
            when (select count(*)
                FROM mdl_course_modules_completion cmc  
                JOIN mdl_course_modules cm ON cmc.coursemoduleid = cm.id
                JOIN mdl_modules m ON cm.module = m.id
                where cmc.userid = u.id and cm.course = c.id 
                and  gi.iteminstance=cm.instance) = 0
            then 'Non Terminé'
            WHEN gi.itemmodule='quiz'
            then (select
                    case 
                    when cmc.completionstate=0 then 'En cours'
                    when cmc.completionstate=1 then 'Terminé'
                    when cmc.completionstate=2 then 'Terminé'
                    when cmc.completionstate=3 then 'Non terminé'
                    ELSE 'Non entamé'
                    END 
                FROM mdl_course_modules_completion cmc  
                JOIN mdl_course_modules cm ON cmc.coursemoduleid = cm.id
                JOIN mdl_modules m ON cm.module = m.id
                where cmc.userid = u.id and cm.course = c.id 
                and  gi.iteminstance=cm.instance and m.id=16)
            When gi.itemmodule='scorm'
            then (select
                    case 
                    when cmc.completionstate=0 then 'En cours'
                    when cmc.completionstate=1 then 'Terminé'
                    when cmc.completionstate=2 then 'Terminé'
                    when cmc.completionstate=3 then 'Non terminé'
                    ELSE 'Non entamé'
                    END 
                FROM mdl_course_modules_completion cmc  
                JOIN mdl_course_modules cm ON cmc.coursemoduleid = cm.id
                JOIN mdl_modules m ON cm.module = m.id
                where cmc.userid = u.id and cm.course = c.id 
                and  gi.iteminstance=cm.instance and m.id=18)
            ELSE 'Non définit'
            END
            as ETAT_FORMATION
            from mdl_user u
            join mdl_user_enrolments enr on u.id=enr.userid
            join mdl_enrol er on enr.enrolid=er.id
            join mdl_course c on er.courseid=c.id
            JOIN mdl_course_categories cat on cat.id=c.category
            left join mdl_grade_items gi on c.id=gi.courseid and gi.itemtype<>'course'
                where gi.id = $selected_val 
            order by c.id,u.id";
            $query0 = $db->query($sql0);
            }
        else{
            
            $time1 = strtotime($_POST['debut'].' 02:00');
            $time2 = strtotime($_POST['fin'].' 02:00');
            //$date_inscription = strval(date('d/m/Y',$time));
            $sql0 = "SELECT distinct 
            cat.name AS 'CATEGORIE',c.fullname AS 'FORMATION', c.visible as 'VISIBILITE',
            CASE 
            WHEN gi.itemtype = 'course' 
            THEN CONCAT('Total des activités')
            ELSE gi.itemname
            END  AS 'ACTIVITE',u.idnumber as 'MATRICULE', u.firstname AS 'PRENOM',u.lastname AS 'NOM', 
            CASE WHEN u.suspended=1  then 'suspendu' WHEN u.deleted=1  
            then 'supprimé' else 'Actif' END As Etat ,
            FROM_UNIXTIME(enr.timecreated,'%d/%m/%Y') AS 'DATE INSCRIPTION',
            CASE
            WHEN 
            gi.itemmodule = 'quiz' 
            THEN(select DATE_FORMAT(FROM_UNIXTIME(cmc.timemodified), '%d/%m/%Y')  FROM mdl_course_modules_completion cmc  
            JOIN mdl_course_modules cm ON cmc.coursemoduleid = cm.id
            JOIN mdl_modules m ON cm.module = m.id
            where cmc.userid = u.id and cm.course = c.id 
            and  gi.iteminstance=cm.instance and m.id=16 )
            WHEN gi.itemmodule = 'scorm' 
            THEN (
            select DATE_FORMAT(FROM_UNIXTIME(cmc.timemodified), '%d/%m/%Y')  from   mdl_course_modules_completion cmc  
            JOIN mdl_course_modules cm ON cmc.coursemoduleid = cm.id
            JOIN mdl_modules m ON cm.module = m.id
            where cmc.userid = u.id and cm.course = c.id 
            and  gi.iteminstance=cm.instance and m.id=18
            )  
            END AS 'DATE_REALISATION',  
            (select distinct ROUND(gg.finalgrade,2) from mdl_grade_grades AS gg where  gg.itemid=gi.id and  gg.userid=u.id 
            ) as NOTE,
            CASE
            WHEN gi.itemmodule = 'quiz' THEN(select MAX(at.attempt) from mdl_quiz_attempts at,mdl_quiz q where at.quiz=q.id and at.userid=u.id and q.course=c.id and q.id=gi.iteminstance)
            WHEN gi.itemmodule = 'scorm' THEN (SELECT MAX(st.attempt) FROM  mdl_scorm_scoes_track AS st, mdl_scorm sc where sc.id=st.scormid and st.userid=u.id and sc.course=c.id and sc.id=gi.iteminstance)  
            END AS TENTATIVE,
            case
            when (select count(*)
                FROM mdl_course_modules_completion cmc  
                JOIN mdl_course_modules cm ON cmc.coursemoduleid = cm.id
                JOIN mdl_modules m ON cm.module = m.id
                where cmc.userid = u.id and cm.course = c.id 
                and  gi.iteminstance=cm.instance) = 0
            then 'Non Terminé'
            WHEN gi.itemmodule='quiz'
            then (select
                    case 
                    when cmc.completionstate=0 then 'En cours'
                    when cmc.completionstate=1 then 'Terminé'
                    when cmc.completionstate=2 then 'Terminé'
                    when cmc.completionstate=3 then 'Non terminé'
                    ELSE 'Non entamé'
                    END 
                FROM mdl_course_modules_completion cmc  
                JOIN mdl_course_modules cm ON cmc.coursemoduleid = cm.id
                JOIN mdl_modules m ON cm.module = m.id
                where cmc.userid = u.id and cm.course = c.id 
                and  gi.iteminstance=cm.instance and m.id=16)
            When gi.itemmodule='scorm'
            then (select
                    case 
                    when cmc.completionstate=0 then 'En cours'
                    when cmc.completionstate=1 then 'Terminé'
                    when cmc.completionstate=2 then 'Terminé'
                    when cmc.completionstate=3 then 'Non terminé'
                    ELSE 'Non entamé'
                    END 
                FROM mdl_course_modules_completion cmc  
                JOIN mdl_course_modules cm ON cmc.coursemoduleid = cm.id
                JOIN mdl_modules m ON cm.module = m.id
                where cmc.userid = u.id and cm.course = c.id 
                and  gi.iteminstance=cm.instance and m.id=18)
            ELSE 'Non définit'
            END
            as ETAT_FORMATION
            from mdl_user u
            join mdl_user_enrolments enr on u.id=enr.userid
            join mdl_enrol er on enr.enrolid=er.id
            join mdl_course c on er.courseid=c.id
            JOIN mdl_course_categories cat on cat.id=c.category
            left join mdl_grade_items gi on c.id=gi.courseid and gi.itemtype<>'course'
                where gi.id = $selected_val and enr.timecreated BETWEEN $time1 and $time2 
            order by c.id,u.id";
            $query0 = $db->query($sql0);
            }

        $sql1 = "SELECT idnumber as 'MATRICULE', 
        firstname as 'PRENOM',
        lastname as 'NOM',
        email as 'EMAIL',
        (select FROM_UNIXTIME(data,'%d/%m/%Y') from  mdl_user_info_data as d where d.fieldid=5 and d.userid=u.id) as DATE_RECRUTEMENT,
        (select data from  mdl_user_info_data as d where d.fieldid=6 and d.userid=u.id) as CODE_EMPLOI,
        (select data from  mdl_user_info_data as d where d.fieldid=7 and d.userid=u.id) as LIBELLE_EMPLOI,
        (select data from  mdl_user_info_data as d where d.fieldid=9 and d.userid=u.id) as CODE_POSTE,
        (select FROM_UNIXTIME(data,'%d/%m/%Y') from  mdl_user_info_data as d where d.fieldid=10 and d.userid=u.id) as DATE_PRISE_POSTE,
        (select data from  mdl_user_info_data as d where d.fieldid=11 and d.userid=u.id) as CODE_UNITE,
        
        (select data from  mdl_user_info_data as d where d.fieldid=16 and d.userid=u.id) as ABSENCE,
        (select FROM_UNIXTIME(data,'%d/%m/%Y') from  mdl_user_info_data as d where d.fieldid=17 and d.userid=u.id) as DATE_DEBUT_ABSENCE,
        (select FROM_UNIXTIME(data,'%d/%m/%Y') from  mdl_user_info_data as d where d.fieldid=18 and d.userid=u.id) as DATE_FIN_ABSENCE,
        (select data from  mdl_user_info_data as d where d.fieldid=22 and d.userid=u.id) as MATRICULE_MANAGER,
        (select data from  mdl_user_info_data as d where d.fieldid=23 and d.userid=u.id) as LIBELLE_DGA,
        (select data from  mdl_user_info_data as d where d.fieldid=24 and d.userid=u.id) as LIBELLE_DIRECTION,
        f12.data as LIBELLE_UNITE,
         f24.data as DIRECTION,
         f23.data as DGA,
         f15.data as MANAGER,
         f8.data as LIBELLE_POSTE       
        FROM mdl_user u    
        LEFT JOIN mdl_user_info_data AS f12  ON u.id = f12.userid and f12.fieldid=12 
        LEFT JOIN mdl_user_info_data AS f24  ON u.id = f24.userid and f24.fieldid=24 
        LEFT JOIN mdl_user_info_data AS f23  ON u.id = f23.userid and f23.fieldid=23 
        LEFT JOIN mdl_user_info_data AS f15  ON u.id = f15.userid and f15.fieldid=15 
        LEFT JOIN mdl_user_info_data AS f8  ON u.id = f8.userid and f8.fieldid=8 
        where   u.deleted=0 and u.suspended = 0";   
        $query1= $db->query($sql1);
        
        switch ($_GET['rapport']) {
            case '1':
                table($query0,$sql0);
                break;
            
            case '2':
                table($query1,$sql1); 
                break;
        }
    } 
    include './includes/footer2.php';
?>
        
      

  
