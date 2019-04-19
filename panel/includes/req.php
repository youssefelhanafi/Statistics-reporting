<?php

//include '../config/dbConfig.php';



function sql0v1($db,$selected_val){
    $sql0v1 = "SELECT distinct 
    cat.name AS 'CATEGORIE',c.fullname AS 'FORMATION', c.visible as 'VISIBILITE',
    CASE 
    WHEN gi.itemtype = 'course' 
    THEN CONCAT('Total des activités')
    ELSE gi.itemname
    END  AS 'ACTIVITE',u.idnumber as 'MATRICULE', u.firstname AS 'PRENOM',u.lastname AS 'NOM', 

    CASE WHEN u.suspended=1  then 'suspendu' WHEN u.deleted=1  
    then 'supprimé' else 'Actif' END As Etat ,
    FROM_UNIXTIME(enr.timecreated,'%d/%m/%Y') AS 'DATE INSCRIPTION',



    case
    when (select distinct ROUND(gg.finalgrade,2) from mdl_grade_grades AS gg where  gg.itemid=gi.id and  gg.userid=u.id)>=ROUND(gi.gradepass,2)
    then 'Terminé'
    when (select distinct ROUND(gg.finalgrade,2) from mdl_grade_grades AS gg where  gg.itemid=gi.id and  gg.userid=u.id)<ROUND(gi.gradepass,2)
    then 'Non Terminé'
    else 'Non Terminé'
    END
    as 'ETAT_FORMATION',

    CASE
    WHEN gi.itemmodule = 'quiz' 
    THEN(
    case 
    when (select DATE_FORMAT(FROM_UNIXTIME(cmc.timemodified), '%d/%m/%Y')  
    FROM mdl_course_modules_completion cmc  
    JOIN mdl_course_modules cm ON cmc.coursemoduleid = cm.id
    JOIN mdl_modules m ON cm.module = m.id
    where cmc.userid = u.id and cm.course = c.id 
    and  gi.iteminstance=cm.instance and m.id=16) <>'01/01/1970' 
    then  (select DATE_FORMAT(FROM_UNIXTIME(cmc.timemodified), '%d/%m/%Y')  
    FROM mdl_course_modules_completion cmc  
    JOIN mdl_course_modules cm ON cmc.coursemoduleid = cm.id
    JOIN mdl_modules m ON cm.module = m.id
    where cmc.userid = u.id and cm.course = c.id 
    and  gi.iteminstance=cm.instance and m.id=16)
    else ' '
    end)
    WHEN gi.itemmodule = 'scorm' 
    then (
    case 
    when(
    select DATE_FORMAT(FROM_UNIXTIME(cmc.timemodified), '%d/%m/%Y')  from   mdl_course_modules_completion cmc  
    JOIN mdl_course_modules cm ON cmc.coursemoduleid = cm.id
    JOIN mdl_modules m ON cm.module = m.id
    where cmc.userid = u.id and cm.course = c.id 
    and  gi.iteminstance=cm.instance and m.id=18)<>'01/01/1970' 
    then  
    (select DATE_FORMAT(FROM_UNIXTIME(cmc.timemodified), '%d/%m/%Y')  
    FROM mdl_course_modules_completion cmc  
    JOIN mdl_course_modules cm ON cmc.coursemoduleid = cm.id
    JOIN mdl_modules m ON cm.module = m.id
    where cmc.userid = u.id and cm.course = c.id 
    and  gi.iteminstance=cm.instance and m.id=18 ) 
    else ' '
    end) 
    end as 'DATE_REALISATION',


    (select distinct ROUND(gg.finalgrade,2) from mdl_grade_grades AS gg where  gg.itemid=gi.id and  gg.userid=u.id 
    ) as NOTE,
    CASE
    WHEN gi.itemmodule = 'quiz' THEN(select MAX(at.attempt) from mdl_quiz_attempts at,mdl_quiz q where at.quiz=q.id and at.userid=u.id and q.course=c.id and q.id=gi.iteminstance)
    WHEN gi.itemmodule = 'scorm' THEN (SELECT MAX(st.attempt) FROM  mdl_scorm_scoes_track AS st, mdl_scorm sc where sc.id=st.scormid and st.userid=u.id and sc.course=c.id and sc.id=gi.iteminstance)  
    END AS TENTATIVE,



    FROM_UNIXTIME(f5.data,'%d/%m/%Y') as date_recrutement,FROM_UNIXTIME(f10.data,'%d/%m/%Y') as DATE_PRISE_POSTE,
    f6.data as CODE_EMPLOI,
    f7.data as LIBELLE_EMPLOI,
    f8.data as LIBELLE_POSTE,
    f22.data as MATRICULE_MANAGER,
    f11.data as CODE_UNITE,
    f24.data as DIRECTION,
    f23.data as DGA,
    f15.data as MANAGER,
    f12.data as LIBELLE_UNITE,


    case 
    when FROM_UNIXTIME(c.startdate,'%d/%m/%Y')<=FROM_UNIXTIME(f17.data,'%d/%m/%Y')<=FROM_UNIXTIME(c.enddate,'%d/%m/%Y') 
    or
    FROM_UNIXTIME(c.startdate,'%d/%m/%Y')<=FROM_UNIXTIME(f18.data,'%d/%m/%Y')<=FROM_UNIXTIME(c.enddate,'%d/%m/%Y') 
    THEN 
    case when f16.data='MTN' then 'Maternité'
    when f16.data='MLD' then 'Maladie Longue Durée'
    else  '---' 
    END
    else '---'

    END AS ABSENCE,

    case 
    when (f16.data='MTN' or f16.data='MLD')
    and FROM_UNIXTIME(c.startdate,'%d/%m/%Y')<=FROM_UNIXTIME(f17.data,'%d/%m/%Y')<=FROM_UNIXTIME(c.enddate,'%d/%m/%Y')
    then FROM_UNIXTIME(f17.data,'%d/%m/%Y')
    else '---'
    END as DEBUT_ABSENCE,

    case 
    when (f16.data='MTN' or f16.data='MLD')
    and   FROM_UNIXTIME(c.startdate,'%d/%m/%Y')<=FROM_UNIXTIME(f18.data,'%d/%m/%Y')<=FROM_UNIXTIME(c.enddate,'%d/%m/%Y')
    then FROM_UNIXTIME(f18.data,'%d/%m/%Y')
    else '---'
    END as FIN_ABSENCE


    from mdl_user u
    join mdl_user_enrolments enr on u.id=enr.userid
    join mdl_enrol er on enr.enrolid=er.id
    join mdl_course c on er.courseid=c.id
    JOIN mdl_course_categories cat on cat.id=c.category
    
    left join mdl_grade_items gi on c.id=gi.courseid and gi.itemtype<>'course'


    LEFT JOIN mdl_user_info_data AS f5 ON u.id = f5.userid and f5.fieldid=5
    LEFT JOIN mdl_user_info_data AS f6  ON u.id = f6.userid and f6.fieldid=6
    LEFT JOIN mdl_user_info_data AS f7  ON u.id = f7.userid and f7.fieldid=7
    LEFT JOIN mdl_user_info_data AS f8  ON u.id = f8.userid and f8.fieldid=8 
    LEFT JOIN mdl_user_info_data AS f22  ON u.id = f22.userid and f22.fieldid=22 
    LEFT JOIN mdl_user_info_data AS f11  ON u.id = f11.userid and f11.fieldid=11 
    LEFT JOIN mdl_user_info_data AS f24  ON u.id = f24.userid and f24.fieldid=24 
    LEFT JOIN mdl_user_info_data AS f23  ON u.id = f23.userid and f23.fieldid=23 
    LEFT JOIN mdl_user_info_data AS f15  ON u.id = f15.userid and f15.fieldid=15 
    LEFT JOIN mdl_user_info_data AS f12  ON u.id = f12.userid and f12.fieldid=12 
    LEFT JOIN mdl_user_info_data AS f16  ON u.id = f16.userid and f16.fieldid=16 

    LEFT JOIN mdl_user_info_data AS f10  ON u.id = f10.userid and f10.fieldid=10 
    LEFT JOIN mdl_user_info_data AS f17  ON u.id = f17.userid and f17.fieldid=17 
    LEFT JOIN mdl_user_info_data AS f18  ON u.id = f18.userid and f18.fieldid=18
    where 1=1  and gi.id = $selected_val

    order by c.id,u.id limit 5000";
    $query0 = $db->query($sql0v1);

    //print_r ($query0);
    return array($query0,$sql0v1);
}







function sql0v2($db,$selected_val,$time1,$time2){ // Date formation
    $sql0v2 = "SELECT distinct 
            cat.name AS 'CATEGORIE',c.fullname AS 'FORMATION', c.visible as 'VISIBILITE',
            CASE 
              WHEN gi.itemtype = 'course' 
               THEN CONCAT('Total des activités')
              ELSE gi.itemname
            END  AS 'ACTIVITE',u.idnumber as 'MATRICULE', u.firstname AS 'PRENOM',u.lastname AS 'NOM', 
            
             CASE WHEN u.suspended=1  then 'suspendu' WHEN u.deleted=1  
             then 'supprimé' else 'Actif' END As Etat ,
            FROM_UNIXTIME(enr.timecreated,'%d/%m/%Y') AS 'DATE INSCRIPTION',
            
            
              
                    case
                    when (select distinct ROUND(gg.finalgrade,2) from mdl_grade_grades AS gg where  gg.itemid=gi.id and  gg.userid=u.id)>=ROUND(gi.gradepass,2)
                    then 'Terminé'
                    when (select distinct ROUND(gg.finalgrade,2) from mdl_grade_grades AS gg where  gg.itemid=gi.id and  gg.userid=u.id)<ROUND(gi.gradepass,2)
                    then 'Non Terminé'
                    else 'Non Terminé'
                        END
            as 'ETAT_FORMATION',
            
            CASE
            WHEN gi.itemmodule = 'quiz' 
            THEN(
                case 
                    when (select DATE_FORMAT(FROM_UNIXTIME(cmc.timemodified), '%d/%m/%Y')  
                        FROM mdl_course_modules_completion cmc  
                        JOIN mdl_course_modules cm ON cmc.coursemoduleid = cm.id
                        JOIN mdl_modules m ON cm.module = m.id
                        where cmc.userid = u.id and cm.course = c.id 
                        and  gi.iteminstance=cm.instance and m.id=16) <>'01/01/1970' 
                    then  (select DATE_FORMAT(FROM_UNIXTIME(cmc.timemodified), '%d/%m/%Y')  
                        FROM mdl_course_modules_completion cmc  
                        JOIN mdl_course_modules cm ON cmc.coursemoduleid = cm.id
                        JOIN mdl_modules m ON cm.module = m.id
                        where cmc.userid = u.id and cm.course = c.id 
                        and  gi.iteminstance=cm.instance and m.id=16)
                    else ' '
                    end)
            WHEN gi.itemmodule = 'scorm' 
            then (
                case 
                    when(
                    select DATE_FORMAT(FROM_UNIXTIME(cmc.timemodified), '%d/%m/%Y')  from   mdl_course_modules_completion cmc  
                    JOIN mdl_course_modules cm ON cmc.coursemoduleid = cm.id
                    JOIN mdl_modules m ON cm.module = m.id
                    where cmc.userid = u.id and cm.course = c.id 
                    and  gi.iteminstance=cm.instance and m.id=18)<>'01/01/1970' 
                    then  
                    (select DATE_FORMAT(FROM_UNIXTIME(cmc.timemodified), '%d/%m/%Y')  
                    FROM mdl_course_modules_completion cmc  
                    JOIN mdl_course_modules cm ON cmc.coursemoduleid = cm.id
                    JOIN mdl_modules m ON cm.module = m.id
                    where cmc.userid = u.id and cm.course = c.id 
                    and  gi.iteminstance=cm.instance and m.id=18 ) 
                    else ' '
                    end) 
            end as 'DATE_REALISATION',
            
             
            (select distinct ROUND(gg.finalgrade,2) from mdl_grade_grades AS gg where  gg.itemid=gi.id and  gg.userid=u.id 
            ) as NOTE,
            CASE
            WHEN gi.itemmodule = 'quiz' THEN(select MAX(at.attempt) from mdl_quiz_attempts at,mdl_quiz q where at.quiz=q.id and at.userid=u.id and q.course=c.id and q.id=gi.iteminstance)
            WHEN gi.itemmodule = 'scorm' THEN (SELECT MAX(st.attempt) FROM  mdl_scorm_scoes_track AS st, mdl_scorm sc where sc.id=st.scormid and st.userid=u.id and sc.course=c.id and sc.id=gi.iteminstance)  
            END AS TENTATIVE,
            
            
            
             FROM_UNIXTIME(f5.data,'%d/%m/%Y') as date_recrutement,FROM_UNIXTIME(f10.data,'%d/%m/%Y') as DATE_PRISE_POSTE,
            f6.data as CODE_EMPLOI,
             f7.data as LIBELLE_EMPLOI,
             f8.data as LIBELLE_POSTE,
             f22.data as MATRICULE_MANAGER,
             f11.data as CODE_UNITE,
             f24.data as DIRECTION,
             f23.data as DGA,
             f15.data as MANAGER,
             f12.data as LIBELLE_UNITE,
            
            
            case 
            when FROM_UNIXTIME(c.startdate,'%d/%m/%Y')<=FROM_UNIXTIME(f17.data,'%d/%m/%Y')<=FROM_UNIXTIME(c.enddate,'%d/%m/%Y') 
            or
            FROM_UNIXTIME(c.startdate,'%d/%m/%Y')<=FROM_UNIXTIME(f18.data,'%d/%m/%Y')<=FROM_UNIXTIME(c.enddate,'%d/%m/%Y') 
            THEN 
                case when f16.data='MTN' then 'Maternité'
                     when f16.data='MLD' then 'Maladie Longue Durée'
                    else  '---' 
                END
            else '---'
            
            END AS ABSENCE,
            
            case 
            when (f16.data='MTN' or f16.data='MLD')
            and FROM_UNIXTIME(c.startdate,'%d/%m/%Y')<=FROM_UNIXTIME(f17.data,'%d/%m/%Y')<=FROM_UNIXTIME(c.enddate,'%d/%m/%Y')
            then FROM_UNIXTIME(f17.data,'%d/%m/%Y')
            else '---'
            END as DEBUT_ABSENCE,
            
            case 
            when (f16.data='MTN' or f16.data='MLD')
            and   FROM_UNIXTIME(c.startdate,'%d/%m/%Y')<=FROM_UNIXTIME(f18.data,'%d/%m/%Y')<=FROM_UNIXTIME(c.enddate,'%d/%m/%Y')
            then FROM_UNIXTIME(f18.data,'%d/%m/%Y')
            else '---'
            END as FIN_ABSENCE
            
            
            from mdl_user u
            join mdl_user_enrolments enr on u.id=enr.userid
            join mdl_enrol er on enr.enrolid=er.id
            join mdl_course c on er.courseid=c.id
            JOIN mdl_course_categories cat on cat.id=c.category
            left join mdl_grade_items gi on c.id=gi.courseid and gi.itemtype<>'course'
            
            
            LEFT JOIN mdl_user_info_data AS f5 ON u.id = f5.userid and f5.fieldid=5
            LEFT JOIN mdl_user_info_data AS f6  ON u.id = f6.userid and f6.fieldid=6
            LEFT JOIN mdl_user_info_data AS f7  ON u.id = f7.userid and f7.fieldid=7
            LEFT JOIN mdl_user_info_data AS f8  ON u.id = f8.userid and f8.fieldid=8 
            LEFT JOIN mdl_user_info_data AS f22  ON u.id = f22.userid and f22.fieldid=22 
            LEFT JOIN mdl_user_info_data AS f11  ON u.id = f11.userid and f11.fieldid=11 
            LEFT JOIN mdl_user_info_data AS f24  ON u.id = f24.userid and f24.fieldid=24 
            LEFT JOIN mdl_user_info_data AS f23  ON u.id = f23.userid and f23.fieldid=23 
            LEFT JOIN mdl_user_info_data AS f15  ON u.id = f15.userid and f15.fieldid=15 
            LEFT JOIN mdl_user_info_data AS f12  ON u.id = f12.userid and f12.fieldid=12 
            LEFT JOIN mdl_user_info_data AS f16  ON u.id = f16.userid and f16.fieldid=16 
            
            LEFT JOIN mdl_user_info_data AS f10  ON u.id = f10.userid and f10.fieldid=10 
            LEFT JOIN mdl_user_info_data AS f17  ON u.id = f17.userid and f17.fieldid=17 
            LEFT JOIN mdl_user_info_data AS f18  ON u.id = f18.userid and f18.fieldid=18
            where 1=1  
            and gi.id = $selected_val 
            and enr.timecreated BETWEEN $time1 and $time2 
            
            order by c.id,u.id limit 5000";
            $query0 = $db->query($sql0v2);

            return array($query0,$sql0v2);
}
//$sql0v2 = "";


function sql0dr($db,$selected_val,$time3,$time4){
    $sqlv0dr = "SELECT distinct 
    cat.name AS 'CATEGORIE',c.fullname AS 'FORMATION', c.visible as 'VISIBILITE',
    CASE 
      WHEN gi.itemtype = 'course' 
       THEN CONCAT('Total des activités')
      ELSE gi.itemname
    END  AS 'ACTIVITE',u.idnumber as 'MATRICULE', u.firstname AS 'PRENOM',u.lastname AS 'NOM', 
    
     CASE WHEN u.suspended=1  then 'suspendu' WHEN u.deleted=1  
     then 'supprimé' else 'Actif' END As Etat ,
    FROM_UNIXTIME(enr.timecreated,'%d/%m/%Y') AS 'DATE INSCRIPTION',
    
    
      
            case
            when (select distinct ROUND(gg.finalgrade,2) from mdl_grade_grades AS gg where  gg.itemid=gi.id and  gg.userid=u.id)>=ROUND(gi.gradepass,2)
            then 'Terminé'
            when (select distinct ROUND(gg.finalgrade,2) from mdl_grade_grades AS gg where  gg.itemid=gi.id and  gg.userid=u.id)<ROUND(gi.gradepass,2)
            then 'Non Terminé'
            else 'Non Terminé'
                END
    as 'ETAT_FORMATION',
    
    CASE
    WHEN gi.itemmodule = 'quiz' 
    THEN(
        case 
            when (select DATE_FORMAT(FROM_UNIXTIME(cmc.timemodified), '%d/%m/%Y')  
                FROM mdl_course_modules_completion cmc  
                JOIN mdl_course_modules cm ON cmc.coursemoduleid = cm.id
                JOIN mdl_modules m ON cm.module = m.id
                where cmc.userid = u.id and cm.course = c.id 
                and  gi.iteminstance=cm.instance and m.id=16) <>'01/01/1970' 
            then  (select DATE_FORMAT(FROM_UNIXTIME(cmc.timemodified), '%d/%m/%Y')  
                FROM mdl_course_modules_completion cmc  
                JOIN mdl_course_modules cm ON cmc.coursemoduleid = cm.id
                JOIN mdl_modules m ON cm.module = m.id
                where cmc.userid = u.id and cm.course = c.id 
                and  gi.iteminstance=cm.instance and m.id=16)
            else ' '
            end)
    WHEN gi.itemmodule = 'scorm' 
    then (
        case 
            when(
            select DATE_FORMAT(FROM_UNIXTIME(cmc.timemodified), '%d/%m/%Y')  from   mdl_course_modules_completion cmc  
            JOIN mdl_course_modules cm ON cmc.coursemoduleid = cm.id
            JOIN mdl_modules m ON cm.module = m.id
            where cmc.userid = u.id and cm.course = c.id 
            and  gi.iteminstance=cm.instance and m.id=18)<>'01/01/1970' 
            then  
            (select DATE_FORMAT(FROM_UNIXTIME(cmc.timemodified), '%d/%m/%Y')  
            FROM mdl_course_modules_completion cmc  
            JOIN mdl_course_modules cm ON cmc.coursemoduleid = cm.id
            JOIN mdl_modules m ON cm.module = m.id
            where cmc.userid = u.id and cm.course = c.id 
            and  gi.iteminstance=cm.instance and m.id=18 ) 
            else ' '
            end) 
    end as 'DATE_REALISATION',
    
     
    (select distinct ROUND(gg.finalgrade,2) from mdl_grade_grades AS gg where  gg.itemid=gi.id and  gg.userid=u.id 
    ) as NOTE,
    CASE
    WHEN gi.itemmodule = 'quiz' THEN(select MAX(at.attempt) from mdl_quiz_attempts at,mdl_quiz q where at.quiz=q.id and at.userid=u.id and q.course=c.id and q.id=gi.iteminstance)
    WHEN gi.itemmodule = 'scorm' THEN (SELECT MAX(st.attempt) FROM  mdl_scorm_scoes_track AS st, mdl_scorm sc where sc.id=st.scormid and st.userid=u.id and sc.course=c.id and sc.id=gi.iteminstance)  
    END AS TENTATIVE,
    
    
    
     FROM_UNIXTIME(f5.data,'%d/%m/%Y') as date_recrutement,FROM_UNIXTIME(f10.data,'%d/%m/%Y') as DATE_PRISE_POSTE,
    f6.data as CODE_EMPLOI,
     f7.data as LIBELLE_EMPLOI,
     f8.data as LIBELLE_POSTE,
     f22.data as MATRICULE_MANAGER,
     f11.data as CODE_UNITE,
     f24.data as DIRECTION,
     f23.data as DGA,
     f15.data as MANAGER,
     f12.data as LIBELLE_UNITE,
    
    
    case 
    when FROM_UNIXTIME(c.startdate,'%d/%m/%Y')<=FROM_UNIXTIME(f17.data,'%d/%m/%Y')<=FROM_UNIXTIME(c.enddate,'%d/%m/%Y') 
    or
    FROM_UNIXTIME(c.startdate,'%d/%m/%Y')<=FROM_UNIXTIME(f18.data,'%d/%m/%Y')<=FROM_UNIXTIME(c.enddate,'%d/%m/%Y') 
    THEN 
        case when f16.data='MTN' then 'Maternité'
             when f16.data='MLD' then 'Maladie Longue Durée'
            else  '---' 
        END
    else '---'
    
    END AS ABSENCE,
    
    case 
    when (f16.data='MTN' or f16.data='MLD')
    and FROM_UNIXTIME(c.startdate,'%d/%m/%Y')<=FROM_UNIXTIME(f17.data,'%d/%m/%Y')<=FROM_UNIXTIME(c.enddate,'%d/%m/%Y')
    then FROM_UNIXTIME(f17.data,'%d/%m/%Y')
    else '---'
    END as DEBUT_ABSENCE,
    
    case 
    when (f16.data='MTN' or f16.data='MLD')
    and   FROM_UNIXTIME(c.startdate,'%d/%m/%Y')<=FROM_UNIXTIME(f18.data,'%d/%m/%Y')<=FROM_UNIXTIME(c.enddate,'%d/%m/%Y')
    then FROM_UNIXTIME(f18.data,'%d/%m/%Y')
    else '---'
    END as FIN_ABSENCE
    
    
    from mdl_user u
    join mdl_user_enrolments enr on u.id=enr.userid
    join mdl_enrol er on enr.enrolid=er.id
    join mdl_course c on er.courseid=c.id
    JOIN mdl_course_categories cat on cat.id=c.category
    left join mdl_grade_items gi on c.id=gi.courseid and gi.itemtype<>'course'
    
    
    LEFT JOIN mdl_user_info_data AS f5 ON u.id = f5.userid and f5.fieldid=5
    LEFT JOIN mdl_user_info_data AS f6  ON u.id = f6.userid and f6.fieldid=6
    LEFT JOIN mdl_user_info_data AS f7  ON u.id = f7.userid and f7.fieldid=7
    LEFT JOIN mdl_user_info_data AS f8  ON u.id = f8.userid and f8.fieldid=8 
    LEFT JOIN mdl_user_info_data AS f22  ON u.id = f22.userid and f22.fieldid=22 
    LEFT JOIN mdl_user_info_data AS f11  ON u.id = f11.userid and f11.fieldid=11 
    LEFT JOIN mdl_user_info_data AS f24  ON u.id = f24.userid and f24.fieldid=24 
    LEFT JOIN mdl_user_info_data AS f23  ON u.id = f23.userid and f23.fieldid=23 
    LEFT JOIN mdl_user_info_data AS f15  ON u.id = f15.userid and f15.fieldid=15 
    LEFT JOIN mdl_user_info_data AS f12  ON u.id = f12.userid and f12.fieldid=12 
    LEFT JOIN mdl_user_info_data AS f16  ON u.id = f16.userid and f16.fieldid=16 
    
    LEFT JOIN mdl_user_info_data AS f10  ON u.id = f10.userid and f10.fieldid=10 
    LEFT JOIN mdl_user_info_data AS f17  ON u.id = f17.userid and f17.fieldid=17 
    LEFT JOIN mdl_user_info_data AS f18  ON u.id = f18.userid and f18.fieldid=18
    where 1=1  
    and gi.id = $selected_val 
    and f5.data BETWEEN $time3 and $time4
    
    order by c.id,u.id limit 5000";

    $query0 = $db->query($sqlv0dr);

    return array($query0,$sqlv0dr);
}



function sql0unite($db,$selected_val,$unite){ //  unite
    $sql0unite = "SELECT distinct 
    cat.name AS 'CATEGORIE',c.fullname AS 'FORMATION', c.visible as 'VISIBILITE',
    CASE 
      WHEN gi.itemtype = 'course' 
       THEN CONCAT('Total des activités')
      ELSE gi.itemname
    END  AS 'ACTIVITE',u.idnumber as 'MATRICULE', u.firstname AS 'PRENOM',u.lastname AS 'NOM', 
    
     CASE WHEN u.suspended=1  then 'suspendu' WHEN u.deleted=1  
     then 'supprimé' else 'Actif' END As Etat ,
    FROM_UNIXTIME(enr.timecreated,'%d/%m/%Y') AS 'DATE INSCRIPTION',
    
    
      
            case
            when (select distinct ROUND(gg.finalgrade,2) from mdl_grade_grades AS gg where  gg.itemid=gi.id and  gg.userid=u.id)>=ROUND(gi.gradepass,2)
            then 'Terminé'
            when (select distinct ROUND(gg.finalgrade,2) from mdl_grade_grades AS gg where  gg.itemid=gi.id and  gg.userid=u.id)<ROUND(gi.gradepass,2)
            then 'Non Terminé'
            else 'Non Terminé'
                END
    as 'ETAT_FORMATION',
    
    CASE
    WHEN gi.itemmodule = 'quiz' 
    THEN(
        case 
            when (select DATE_FORMAT(FROM_UNIXTIME(cmc.timemodified), '%d/%m/%Y')  
                FROM mdl_course_modules_completion cmc  
                JOIN mdl_course_modules cm ON cmc.coursemoduleid = cm.id
                JOIN mdl_modules m ON cm.module = m.id
                where cmc.userid = u.id and cm.course = c.id 
                and  gi.iteminstance=cm.instance and m.id=16) <>'01/01/1970' 
            then  (select DATE_FORMAT(FROM_UNIXTIME(cmc.timemodified), '%d/%m/%Y')  
                FROM mdl_course_modules_completion cmc  
                JOIN mdl_course_modules cm ON cmc.coursemoduleid = cm.id
                JOIN mdl_modules m ON cm.module = m.id
                where cmc.userid = u.id and cm.course = c.id 
                and  gi.iteminstance=cm.instance and m.id=16)
            else ' '
            end)
    WHEN gi.itemmodule = 'scorm' 
    then (
        case 
            when(
            select DATE_FORMAT(FROM_UNIXTIME(cmc.timemodified), '%d/%m/%Y')  from   mdl_course_modules_completion cmc  
            JOIN mdl_course_modules cm ON cmc.coursemoduleid = cm.id
            JOIN mdl_modules m ON cm.module = m.id
            where cmc.userid = u.id and cm.course = c.id 
            and  gi.iteminstance=cm.instance and m.id=18)<>'01/01/1970' 
            then  
            (select DATE_FORMAT(FROM_UNIXTIME(cmc.timemodified), '%d/%m/%Y')  
            FROM mdl_course_modules_completion cmc  
            JOIN mdl_course_modules cm ON cmc.coursemoduleid = cm.id
            JOIN mdl_modules m ON cm.module = m.id
            where cmc.userid = u.id and cm.course = c.id 
            and  gi.iteminstance=cm.instance and m.id=18 ) 
            else ' '
            end) 
    end as 'DATE_REALISATION',
    
     
    (select distinct ROUND(gg.finalgrade,2) from mdl_grade_grades AS gg where  gg.itemid=gi.id and  gg.userid=u.id 
    ) as NOTE,
    CASE
    WHEN gi.itemmodule = 'quiz' THEN(select MAX(at.attempt) from mdl_quiz_attempts at,mdl_quiz q where at.quiz=q.id and at.userid=u.id and q.course=c.id and q.id=gi.iteminstance)
    WHEN gi.itemmodule = 'scorm' THEN (SELECT MAX(st.attempt) FROM  mdl_scorm_scoes_track AS st, mdl_scorm sc where sc.id=st.scormid and st.userid=u.id and sc.course=c.id and sc.id=gi.iteminstance)  
    END AS TENTATIVE,
    
    
    
     FROM_UNIXTIME(f5.data,'%d/%m/%Y') as date_recrutement,FROM_UNIXTIME(f10.data,'%d/%m/%Y') as DATE_PRISE_POSTE,
    f6.data as CODE_EMPLOI,
     f7.data as LIBELLE_EMPLOI,
     f8.data as LIBELLE_POSTE,
     f22.data as MATRICULE_MANAGER,
     f11.data as CODE_UNITE,
     f24.data as DIRECTION,
     f23.data as DGA,
     f15.data as MANAGER,
     f12.data as LIBELLE_UNITE,
    
    
    case 
    when FROM_UNIXTIME(c.startdate,'%d/%m/%Y')<=FROM_UNIXTIME(f17.data,'%d/%m/%Y')<=FROM_UNIXTIME(c.enddate,'%d/%m/%Y') 
    or
    FROM_UNIXTIME(c.startdate,'%d/%m/%Y')<=FROM_UNIXTIME(f18.data,'%d/%m/%Y')<=FROM_UNIXTIME(c.enddate,'%d/%m/%Y') 
    THEN 
        case when f16.data='MTN' then 'Maternité'
             when f16.data='MLD' then 'Maladie Longue Durée'
            else  '---' 
        END
    else '---'
    
    END AS ABSENCE,
    
    case 
    when (f16.data='MTN' or f16.data='MLD')
    and FROM_UNIXTIME(c.startdate,'%d/%m/%Y')<=FROM_UNIXTIME(f17.data,'%d/%m/%Y')<=FROM_UNIXTIME(c.enddate,'%d/%m/%Y')
    then FROM_UNIXTIME(f17.data,'%d/%m/%Y')
    else '---'
    END as DEBUT_ABSENCE,
    
    case 
    when (f16.data='MTN' or f16.data='MLD')
    and   FROM_UNIXTIME(c.startdate,'%d/%m/%Y')<=FROM_UNIXTIME(f18.data,'%d/%m/%Y')<=FROM_UNIXTIME(c.enddate,'%d/%m/%Y')
    then FROM_UNIXTIME(f18.data,'%d/%m/%Y')
    else '---'
    END as FIN_ABSENCE
    
    
    from mdl_user u
    join mdl_user_enrolments enr on u.id=enr.userid
    join mdl_enrol er on enr.enrolid=er.id
    join mdl_course c on er.courseid=c.id
    JOIN mdl_course_categories cat on cat.id=c.category
    left join mdl_grade_items gi on c.id=gi.courseid and gi.itemtype<>'course'
    
    
    LEFT JOIN mdl_user_info_data AS f5 ON u.id = f5.userid and f5.fieldid=5
    LEFT JOIN mdl_user_info_data AS f6  ON u.id = f6.userid and f6.fieldid=6
    LEFT JOIN mdl_user_info_data AS f7  ON u.id = f7.userid and f7.fieldid=7
    LEFT JOIN mdl_user_info_data AS f8  ON u.id = f8.userid and f8.fieldid=8 
    LEFT JOIN mdl_user_info_data AS f22  ON u.id = f22.userid and f22.fieldid=22 
    LEFT JOIN mdl_user_info_data AS f11  ON u.id = f11.userid and f11.fieldid=11 
    LEFT JOIN mdl_user_info_data AS f24  ON u.id = f24.userid and f24.fieldid=24 
    LEFT JOIN mdl_user_info_data AS f23  ON u.id = f23.userid and f23.fieldid=23 
    LEFT JOIN mdl_user_info_data AS f15  ON u.id = f15.userid and f15.fieldid=15 
    LEFT JOIN mdl_user_info_data AS f12  ON u.id = f12.userid and f12.fieldid=12 
    LEFT JOIN mdl_user_info_data AS f16  ON u.id = f16.userid and f16.fieldid=16 
    
    LEFT JOIN mdl_user_info_data AS f10  ON u.id = f10.userid and f10.fieldid=10 
    LEFT JOIN mdl_user_info_data AS f17  ON u.id = f17.userid and f17.fieldid=17 
    LEFT JOIN mdl_user_info_data AS f18  ON u.id = f18.userid and f18.fieldid=18
    where 1=1  
    and gi.id = $selected_val 
    and f12.data like '%$unite%'
    
    order by c.id,u.id limit 5000";

    $query0 = $db->query($sql0unite);

    return array($query0,$sql0unite);
}

function sql0manager($db,$selected_val,$manager){ // manager
    $sql0manager = "SELECT distinct 
    cat.name AS 'CATEGORIE',c.fullname AS 'FORMATION', c.visible as 'VISIBILITE',
    CASE 
      WHEN gi.itemtype = 'course' 
       THEN CONCAT('Total des activités')
      ELSE gi.itemname
    END  AS 'ACTIVITE',u.idnumber as 'MATRICULE', u.firstname AS 'PRENOM',u.lastname AS 'NOM', 
    
     CASE WHEN u.suspended=1  then 'suspendu' WHEN u.deleted=1  
     then 'supprimé' else 'Actif' END As Etat ,
    FROM_UNIXTIME(enr.timecreated,'%d/%m/%Y') AS 'DATE INSCRIPTION',
    
    
      
            case
            when (select distinct ROUND(gg.finalgrade,2) from mdl_grade_grades AS gg where  gg.itemid=gi.id and  gg.userid=u.id)>=ROUND(gi.gradepass,2)
            then 'Terminé'
            when (select distinct ROUND(gg.finalgrade,2) from mdl_grade_grades AS gg where  gg.itemid=gi.id and  gg.userid=u.id)<ROUND(gi.gradepass,2)
            then 'Non Terminé'
            else 'Non Terminé'
                END
    as 'ETAT_FORMATION',
    
    CASE
    WHEN gi.itemmodule = 'quiz' 
    THEN(
        case 
            when (select DATE_FORMAT(FROM_UNIXTIME(cmc.timemodified), '%d/%m/%Y')  
                FROM mdl_course_modules_completion cmc  
                JOIN mdl_course_modules cm ON cmc.coursemoduleid = cm.id
                JOIN mdl_modules m ON cm.module = m.id
                where cmc.userid = u.id and cm.course = c.id 
                and  gi.iteminstance=cm.instance and m.id=16) <>'01/01/1970' 
            then  (select DATE_FORMAT(FROM_UNIXTIME(cmc.timemodified), '%d/%m/%Y')  
                FROM mdl_course_modules_completion cmc  
                JOIN mdl_course_modules cm ON cmc.coursemoduleid = cm.id
                JOIN mdl_modules m ON cm.module = m.id
                where cmc.userid = u.id and cm.course = c.id 
                and  gi.iteminstance=cm.instance and m.id=16)
            else ' '
            end)
    WHEN gi.itemmodule = 'scorm' 
    then (
        case 
            when(
            select DATE_FORMAT(FROM_UNIXTIME(cmc.timemodified), '%d/%m/%Y')  from   mdl_course_modules_completion cmc  
            JOIN mdl_course_modules cm ON cmc.coursemoduleid = cm.id
            JOIN mdl_modules m ON cm.module = m.id
            where cmc.userid = u.id and cm.course = c.id 
            and  gi.iteminstance=cm.instance and m.id=18)<>'01/01/1970' 
            then  
            (select DATE_FORMAT(FROM_UNIXTIME(cmc.timemodified), '%d/%m/%Y')  
            FROM mdl_course_modules_completion cmc  
            JOIN mdl_course_modules cm ON cmc.coursemoduleid = cm.id
            JOIN mdl_modules m ON cm.module = m.id
            where cmc.userid = u.id and cm.course = c.id 
            and  gi.iteminstance=cm.instance and m.id=18 ) 
            else ' '
            end) 
    end as 'DATE_REALISATION',
    
     
    (select distinct ROUND(gg.finalgrade,2) from mdl_grade_grades AS gg where  gg.itemid=gi.id and  gg.userid=u.id 
    ) as NOTE,
    CASE
    WHEN gi.itemmodule = 'quiz' THEN(select MAX(at.attempt) from mdl_quiz_attempts at,mdl_quiz q where at.quiz=q.id and at.userid=u.id and q.course=c.id and q.id=gi.iteminstance)
    WHEN gi.itemmodule = 'scorm' THEN (SELECT MAX(st.attempt) FROM  mdl_scorm_scoes_track AS st, mdl_scorm sc where sc.id=st.scormid and st.userid=u.id and sc.course=c.id and sc.id=gi.iteminstance)  
    END AS TENTATIVE,
    
    
    
     FROM_UNIXTIME(f5.data,'%d/%m/%Y') as date_recrutement,FROM_UNIXTIME(f10.data,'%d/%m/%Y') as DATE_PRISE_POSTE,
    f6.data as CODE_EMPLOI,
     f7.data as LIBELLE_EMPLOI,
     f8.data as LIBELLE_POSTE,
     f22.data as MATRICULE_MANAGER,
     f11.data as CODE_UNITE,
     f24.data as DIRECTION,
     f23.data as DGA,
     f15.data as MANAGER,
     f12.data as LIBELLE_UNITE,
    
    
    case 
    when FROM_UNIXTIME(c.startdate,'%d/%m/%Y')<=FROM_UNIXTIME(f17.data,'%d/%m/%Y')<=FROM_UNIXTIME(c.enddate,'%d/%m/%Y') 
    or
    FROM_UNIXTIME(c.startdate,'%d/%m/%Y')<=FROM_UNIXTIME(f18.data,'%d/%m/%Y')<=FROM_UNIXTIME(c.enddate,'%d/%m/%Y') 
    THEN 
        case when f16.data='MTN' then 'Maternité'
             when f16.data='MLD' then 'Maladie Longue Durée'
            else  '---' 
        END
    else '---'
    
    END AS ABSENCE,
    
    case 
    when (f16.data='MTN' or f16.data='MLD')
    and FROM_UNIXTIME(c.startdate,'%d/%m/%Y')<=FROM_UNIXTIME(f17.data,'%d/%m/%Y')<=FROM_UNIXTIME(c.enddate,'%d/%m/%Y')
    then FROM_UNIXTIME(f17.data,'%d/%m/%Y')
    else '---'
    END as DEBUT_ABSENCE,
    
    case 
    when (f16.data='MTN' or f16.data='MLD')
    and   FROM_UNIXTIME(c.startdate,'%d/%m/%Y')<=FROM_UNIXTIME(f18.data,'%d/%m/%Y')<=FROM_UNIXTIME(c.enddate,'%d/%m/%Y')
    then FROM_UNIXTIME(f18.data,'%d/%m/%Y')
    else '---'
    END as FIN_ABSENCE
    
    
    from mdl_user u
    join mdl_user_enrolments enr on u.id=enr.userid
    join mdl_enrol er on enr.enrolid=er.id
    join mdl_course c on er.courseid=c.id
    JOIN mdl_course_categories cat on cat.id=c.category
    left join mdl_grade_items gi on c.id=gi.courseid and gi.itemtype<>'course'
    
    
    LEFT JOIN mdl_user_info_data AS f5 ON u.id = f5.userid and f5.fieldid=5
    LEFT JOIN mdl_user_info_data AS f6  ON u.id = f6.userid and f6.fieldid=6
    LEFT JOIN mdl_user_info_data AS f7  ON u.id = f7.userid and f7.fieldid=7
    LEFT JOIN mdl_user_info_data AS f8  ON u.id = f8.userid and f8.fieldid=8 
    LEFT JOIN mdl_user_info_data AS f22  ON u.id = f22.userid and f22.fieldid=22 
    LEFT JOIN mdl_user_info_data AS f11  ON u.id = f11.userid and f11.fieldid=11 
    LEFT JOIN mdl_user_info_data AS f24  ON u.id = f24.userid and f24.fieldid=24 
    LEFT JOIN mdl_user_info_data AS f23  ON u.id = f23.userid and f23.fieldid=23 
    LEFT JOIN mdl_user_info_data AS f15  ON u.id = f15.userid and f15.fieldid=15 
    LEFT JOIN mdl_user_info_data AS f12  ON u.id = f12.userid and f12.fieldid=12 
    LEFT JOIN mdl_user_info_data AS f16  ON u.id = f16.userid and f16.fieldid=16 
    
    LEFT JOIN mdl_user_info_data AS f10  ON u.id = f10.userid and f10.fieldid=10 
    LEFT JOIN mdl_user_info_data AS f17  ON u.id = f17.userid and f17.fieldid=17 
    LEFT JOIN mdl_user_info_data AS f18  ON u.id = f18.userid and f18.fieldid=18
    where 1=1  
    and gi.id = $selected_val 
    and f15.data like '%$manager%'
    
    order by c.id,u.id limit 5000";

    $query0 = $db->query($sql0manager);

    return array($query0,$sql0manager);
}

function sql0dga($db,$selected_val,$dga){ //DGA
    $sql0dga = "SELECT distinct 
    cat.name AS 'CATEGORIE',c.fullname AS 'FORMATION', c.visible as 'VISIBILITE',
    CASE 
      WHEN gi.itemtype = 'course' 
       THEN CONCAT('Total des activités')
      ELSE gi.itemname
    END  AS 'ACTIVITE',u.idnumber as 'MATRICULE', u.firstname AS 'PRENOM',u.lastname AS 'NOM', 
    
     CASE WHEN u.suspended=1  then 'suspendu' WHEN u.deleted=1  
     then 'supprimé' else 'Actif' END As Etat ,
    FROM_UNIXTIME(enr.timecreated,'%d/%m/%Y') AS 'DATE INSCRIPTION',
    
    
      
            case
            when (select distinct ROUND(gg.finalgrade,2) from mdl_grade_grades AS gg where  gg.itemid=gi.id and  gg.userid=u.id)>=ROUND(gi.gradepass,2)
            then 'Terminé'
            when (select distinct ROUND(gg.finalgrade,2) from mdl_grade_grades AS gg where  gg.itemid=gi.id and  gg.userid=u.id)<ROUND(gi.gradepass,2)
            then 'Non Terminé'
            else 'Non Terminé'
                END
    as 'ETAT_FORMATION',
    
    CASE
    WHEN gi.itemmodule = 'quiz' 
    THEN(
        case 
            when (select DATE_FORMAT(FROM_UNIXTIME(cmc.timemodified), '%d/%m/%Y')  
                FROM mdl_course_modules_completion cmc  
                JOIN mdl_course_modules cm ON cmc.coursemoduleid = cm.id
                JOIN mdl_modules m ON cm.module = m.id
                where cmc.userid = u.id and cm.course = c.id 
                and  gi.iteminstance=cm.instance and m.id=16) <>'01/01/1970' 
            then  (select DATE_FORMAT(FROM_UNIXTIME(cmc.timemodified), '%d/%m/%Y')  
                FROM mdl_course_modules_completion cmc  
                JOIN mdl_course_modules cm ON cmc.coursemoduleid = cm.id
                JOIN mdl_modules m ON cm.module = m.id
                where cmc.userid = u.id and cm.course = c.id 
                and  gi.iteminstance=cm.instance and m.id=16)
            else ' '
            end)
    WHEN gi.itemmodule = 'scorm' 
    then (
        case 
            when(
            select DATE_FORMAT(FROM_UNIXTIME(cmc.timemodified), '%d/%m/%Y')  from   mdl_course_modules_completion cmc  
            JOIN mdl_course_modules cm ON cmc.coursemoduleid = cm.id
            JOIN mdl_modules m ON cm.module = m.id
            where cmc.userid = u.id and cm.course = c.id 
            and  gi.iteminstance=cm.instance and m.id=18)<>'01/01/1970' 
            then  
            (select DATE_FORMAT(FROM_UNIXTIME(cmc.timemodified), '%d/%m/%Y')  
            FROM mdl_course_modules_completion cmc  
            JOIN mdl_course_modules cm ON cmc.coursemoduleid = cm.id
            JOIN mdl_modules m ON cm.module = m.id
            where cmc.userid = u.id and cm.course = c.id 
            and  gi.iteminstance=cm.instance and m.id=18 ) 
            else ' '
            end) 
    end as 'DATE_REALISATION',
    
     
    (select distinct ROUND(gg.finalgrade,2) from mdl_grade_grades AS gg where  gg.itemid=gi.id and  gg.userid=u.id 
    ) as NOTE,
    CASE
    WHEN gi.itemmodule = 'quiz' THEN(select MAX(at.attempt) from mdl_quiz_attempts at,mdl_quiz q where at.quiz=q.id and at.userid=u.id and q.course=c.id and q.id=gi.iteminstance)
    WHEN gi.itemmodule = 'scorm' THEN (SELECT MAX(st.attempt) FROM  mdl_scorm_scoes_track AS st, mdl_scorm sc where sc.id=st.scormid and st.userid=u.id and sc.course=c.id and sc.id=gi.iteminstance)  
    END AS TENTATIVE,
    
    
    
     FROM_UNIXTIME(f5.data,'%d/%m/%Y') as date_recrutement,FROM_UNIXTIME(f10.data,'%d/%m/%Y') as DATE_PRISE_POSTE,
    f6.data as CODE_EMPLOI,
     f7.data as LIBELLE_EMPLOI,
     f8.data as LIBELLE_POSTE,
     f22.data as MATRICULE_MANAGER,
     f11.data as CODE_UNITE,
     f24.data as DIRECTION,
     f23.data as DGA,
     f15.data as MANAGER,
     f12.data as LIBELLE_UNITE,
    
    
    case 
    when FROM_UNIXTIME(c.startdate,'%d/%m/%Y')<=FROM_UNIXTIME(f17.data,'%d/%m/%Y')<=FROM_UNIXTIME(c.enddate,'%d/%m/%Y') 
    or
    FROM_UNIXTIME(c.startdate,'%d/%m/%Y')<=FROM_UNIXTIME(f18.data,'%d/%m/%Y')<=FROM_UNIXTIME(c.enddate,'%d/%m/%Y') 
    THEN 
        case when f16.data='MTN' then 'Maternité'
             when f16.data='MLD' then 'Maladie Longue Durée'
            else  '---' 
        END
    else '---'
    
    END AS ABSENCE,
    
    case 
    when (f16.data='MTN' or f16.data='MLD')
    and FROM_UNIXTIME(c.startdate,'%d/%m/%Y')<=FROM_UNIXTIME(f17.data,'%d/%m/%Y')<=FROM_UNIXTIME(c.enddate,'%d/%m/%Y')
    then FROM_UNIXTIME(f17.data,'%d/%m/%Y')
    else '---'
    END as DEBUT_ABSENCE,
    
    case 
    when (f16.data='MTN' or f16.data='MLD')
    and   FROM_UNIXTIME(c.startdate,'%d/%m/%Y')<=FROM_UNIXTIME(f18.data,'%d/%m/%Y')<=FROM_UNIXTIME(c.enddate,'%d/%m/%Y')
    then FROM_UNIXTIME(f18.data,'%d/%m/%Y')
    else '---'
    END as FIN_ABSENCE
    
    
    from mdl_user u
    join mdl_user_enrolments enr on u.id=enr.userid
    join mdl_enrol er on enr.enrolid=er.id
    join mdl_course c on er.courseid=c.id
    JOIN mdl_course_categories cat on cat.id=c.category
    left join mdl_grade_items gi on c.id=gi.courseid and gi.itemtype<>'course'
    
    
    LEFT JOIN mdl_user_info_data AS f5 ON u.id = f5.userid and f5.fieldid=5
    LEFT JOIN mdl_user_info_data AS f6  ON u.id = f6.userid and f6.fieldid=6
    LEFT JOIN mdl_user_info_data AS f7  ON u.id = f7.userid and f7.fieldid=7
    LEFT JOIN mdl_user_info_data AS f8  ON u.id = f8.userid and f8.fieldid=8 
    LEFT JOIN mdl_user_info_data AS f22  ON u.id = f22.userid and f22.fieldid=22 
    LEFT JOIN mdl_user_info_data AS f11  ON u.id = f11.userid and f11.fieldid=11 
    LEFT JOIN mdl_user_info_data AS f24  ON u.id = f24.userid and f24.fieldid=24 
    LEFT JOIN mdl_user_info_data AS f23  ON u.id = f23.userid and f23.fieldid=23 
    LEFT JOIN mdl_user_info_data AS f15  ON u.id = f15.userid and f15.fieldid=15 
    LEFT JOIN mdl_user_info_data AS f12  ON u.id = f12.userid and f12.fieldid=12 
    LEFT JOIN mdl_user_info_data AS f16  ON u.id = f16.userid and f16.fieldid=16 
    
    LEFT JOIN mdl_user_info_data AS f10  ON u.id = f10.userid and f10.fieldid=10 
    LEFT JOIN mdl_user_info_data AS f17  ON u.id = f17.userid and f17.fieldid=17 
    LEFT JOIN mdl_user_info_data AS f18  ON u.id = f18.userid and f18.fieldid=18
    where 1=1  
    and gi.id = $selected_val 
    and f23.data like '%$dga%'
    
    order by c.id,u.id limit 5000";

    $query0 = $db->query($sql0dga);

    return array($query0,$sql0dga);
}


function sql0direction($db,$selected_val,$direction){//direction
    $sql0direction = "SELECT distinct 
    cat.name AS 'CATEGORIE',c.fullname AS 'FORMATION', c.visible as 'VISIBILITE',
    CASE 
      WHEN gi.itemtype = 'course' 
       THEN CONCAT('Total des activités')
      ELSE gi.itemname
    END  AS 'ACTIVITE',u.idnumber as 'MATRICULE', u.firstname AS 'PRENOM',u.lastname AS 'NOM', 
    
     CASE WHEN u.suspended=1  then 'suspendu' WHEN u.deleted=1  
     then 'supprimé' else 'Actif' END As Etat ,
    FROM_UNIXTIME(enr.timecreated,'%d/%m/%Y') AS 'DATE INSCRIPTION',
    
    
      
            case
            when (select distinct ROUND(gg.finalgrade,2) from mdl_grade_grades AS gg where  gg.itemid=gi.id and  gg.userid=u.id)>=ROUND(gi.gradepass,2)
            then 'Terminé'
            when (select distinct ROUND(gg.finalgrade,2) from mdl_grade_grades AS gg where  gg.itemid=gi.id and  gg.userid=u.id)<ROUND(gi.gradepass,2)
            then 'Non Terminé'
            else 'Non Terminé'
                END
    as 'ETAT_FORMATION',
    
    CASE
    WHEN gi.itemmodule = 'quiz' 
    THEN(
        case 
            when (select DATE_FORMAT(FROM_UNIXTIME(cmc.timemodified), '%d/%m/%Y')  
                FROM mdl_course_modules_completion cmc  
                JOIN mdl_course_modules cm ON cmc.coursemoduleid = cm.id
                JOIN mdl_modules m ON cm.module = m.id
                where cmc.userid = u.id and cm.course = c.id 
                and  gi.iteminstance=cm.instance and m.id=16) <>'01/01/1970' 
            then  (select DATE_FORMAT(FROM_UNIXTIME(cmc.timemodified), '%d/%m/%Y')  
                FROM mdl_course_modules_completion cmc  
                JOIN mdl_course_modules cm ON cmc.coursemoduleid = cm.id
                JOIN mdl_modules m ON cm.module = m.id
                where cmc.userid = u.id and cm.course = c.id 
                and  gi.iteminstance=cm.instance and m.id=16)
            else ' '
            end)
    WHEN gi.itemmodule = 'scorm' 
    then (
        case 
            when(
            select DATE_FORMAT(FROM_UNIXTIME(cmc.timemodified), '%d/%m/%Y')  from   mdl_course_modules_completion cmc  
            JOIN mdl_course_modules cm ON cmc.coursemoduleid = cm.id
            JOIN mdl_modules m ON cm.module = m.id
            where cmc.userid = u.id and cm.course = c.id 
            and  gi.iteminstance=cm.instance and m.id=18)<>'01/01/1970' 
            then  
            (select DATE_FORMAT(FROM_UNIXTIME(cmc.timemodified), '%d/%m/%Y')  
            FROM mdl_course_modules_completion cmc  
            JOIN mdl_course_modules cm ON cmc.coursemoduleid = cm.id
            JOIN mdl_modules m ON cm.module = m.id
            where cmc.userid = u.id and cm.course = c.id 
            and  gi.iteminstance=cm.instance and m.id=18 ) 
            else ' '
            end) 
    end as 'DATE_REALISATION',
    
     
    (select distinct ROUND(gg.finalgrade,2) from mdl_grade_grades AS gg where  gg.itemid=gi.id and  gg.userid=u.id 
    ) as NOTE,
    CASE
    WHEN gi.itemmodule = 'quiz' THEN(select MAX(at.attempt) from mdl_quiz_attempts at,mdl_quiz q where at.quiz=q.id and at.userid=u.id and q.course=c.id and q.id=gi.iteminstance)
    WHEN gi.itemmodule = 'scorm' THEN (SELECT MAX(st.attempt) FROM  mdl_scorm_scoes_track AS st, mdl_scorm sc where sc.id=st.scormid and st.userid=u.id and sc.course=c.id and sc.id=gi.iteminstance)  
    END AS TENTATIVE,
    
    
    
     FROM_UNIXTIME(f5.data,'%d/%m/%Y') as date_recrutement,FROM_UNIXTIME(f10.data,'%d/%m/%Y') as DATE_PRISE_POSTE,
    f6.data as CODE_EMPLOI,
     f7.data as LIBELLE_EMPLOI,
     f8.data as LIBELLE_POSTE,
     f22.data as MATRICULE_MANAGER,
     f11.data as CODE_UNITE,
     f24.data as DIRECTION,
     f23.data as DGA,
     f15.data as MANAGER,
     f12.data as LIBELLE_UNITE,
    
    
    case 
    when FROM_UNIXTIME(c.startdate,'%d/%m/%Y')<=FROM_UNIXTIME(f17.data,'%d/%m/%Y')<=FROM_UNIXTIME(c.enddate,'%d/%m/%Y') 
    or
    FROM_UNIXTIME(c.startdate,'%d/%m/%Y')<=FROM_UNIXTIME(f18.data,'%d/%m/%Y')<=FROM_UNIXTIME(c.enddate,'%d/%m/%Y') 
    THEN 
        case when f16.data='MTN' then 'Maternité'
             when f16.data='MLD' then 'Maladie Longue Durée'
            else  '---' 
        END
    else '---'
    
    END AS ABSENCE,
    
    case 
    when (f16.data='MTN' or f16.data='MLD')
    and FROM_UNIXTIME(c.startdate,'%d/%m/%Y')<=FROM_UNIXTIME(f17.data,'%d/%m/%Y')<=FROM_UNIXTIME(c.enddate,'%d/%m/%Y')
    then FROM_UNIXTIME(f17.data,'%d/%m/%Y')
    else '---'
    END as DEBUT_ABSENCE,
    
    case 
    when (f16.data='MTN' or f16.data='MLD')
    and   FROM_UNIXTIME(c.startdate,'%d/%m/%Y')<=FROM_UNIXTIME(f18.data,'%d/%m/%Y')<=FROM_UNIXTIME(c.enddate,'%d/%m/%Y')
    then FROM_UNIXTIME(f18.data,'%d/%m/%Y')
    else '---'
    END as FIN_ABSENCE
    
    
    from mdl_user u
    join mdl_user_enrolments enr on u.id=enr.userid
    join mdl_enrol er on enr.enrolid=er.id
    join mdl_course c on er.courseid=c.id
    JOIN mdl_course_categories cat on cat.id=c.category
    left join mdl_grade_items gi on c.id=gi.courseid and gi.itemtype<>'course'
    
    
    LEFT JOIN mdl_user_info_data AS f5 ON u.id = f5.userid and f5.fieldid=5
    LEFT JOIN mdl_user_info_data AS f6  ON u.id = f6.userid and f6.fieldid=6
    LEFT JOIN mdl_user_info_data AS f7  ON u.id = f7.userid and f7.fieldid=7
    LEFT JOIN mdl_user_info_data AS f8  ON u.id = f8.userid and f8.fieldid=8 
    LEFT JOIN mdl_user_info_data AS f22  ON u.id = f22.userid and f22.fieldid=22 
    LEFT JOIN mdl_user_info_data AS f11  ON u.id = f11.userid and f11.fieldid=11 
    LEFT JOIN mdl_user_info_data AS f24  ON u.id = f24.userid and f24.fieldid=24 
    LEFT JOIN mdl_user_info_data AS f23  ON u.id = f23.userid and f23.fieldid=23 
    LEFT JOIN mdl_user_info_data AS f15  ON u.id = f15.userid and f15.fieldid=15 
    LEFT JOIN mdl_user_info_data AS f12  ON u.id = f12.userid and f12.fieldid=12 
    LEFT JOIN mdl_user_info_data AS f16  ON u.id = f16.userid and f16.fieldid=16 
    
    LEFT JOIN mdl_user_info_data AS f10  ON u.id = f10.userid and f10.fieldid=10 
    LEFT JOIN mdl_user_info_data AS f17  ON u.id = f17.userid and f17.fieldid=17 
    LEFT JOIN mdl_user_info_data AS f18  ON u.id = f18.userid and f18.fieldid=18
    where 1=1  
    and gi.id = $selected_val 
    and f24.data like '%$direction%'
    
    order by c.id,u.id limit 5000";

    $query0 = $db->query($sql0direction);

    return array($query0,$sql0direction);

}

function sql0filters($db,$selected_val,$unite,$manager,$dga,$direction){ // All filters
    $sql0filters = "SELECT distinct 
    cat.name AS 'CATEGORIE',c.fullname AS 'FORMATION', c.visible as 'VISIBILITE',
    CASE 
      WHEN gi.itemtype = 'course' 
       THEN CONCAT('Total des activités')
      ELSE gi.itemname
    END  AS 'ACTIVITE',u.idnumber as 'MATRICULE', u.firstname AS 'PRENOM',u.lastname AS 'NOM', 
    
     CASE WHEN u.suspended=1  then 'suspendu' WHEN u.deleted=1  
     then 'supprimé' else 'Actif' END As Etat ,
    FROM_UNIXTIME(enr.timecreated,'%d/%m/%Y') AS 'DATE INSCRIPTION',
    
    
      
            case
            when (select distinct ROUND(gg.finalgrade,2) from mdl_grade_grades AS gg where  gg.itemid=gi.id and  gg.userid=u.id)>=ROUND(gi.gradepass,2)
            then 'Terminé'
            when (select distinct ROUND(gg.finalgrade,2) from mdl_grade_grades AS gg where  gg.itemid=gi.id and  gg.userid=u.id)<ROUND(gi.gradepass,2)
            then 'Non Terminé'
            else 'Non Terminé'
                END
    as 'ETAT_FORMATION',
    
    CASE
    WHEN gi.itemmodule = 'quiz' 
    THEN(
        case 
            when (select DATE_FORMAT(FROM_UNIXTIME(cmc.timemodified), '%d/%m/%Y')  
                FROM mdl_course_modules_completion cmc  
                JOIN mdl_course_modules cm ON cmc.coursemoduleid = cm.id
                JOIN mdl_modules m ON cm.module = m.id
                where cmc.userid = u.id and cm.course = c.id 
                and  gi.iteminstance=cm.instance and m.id=16) <>'01/01/1970' 
            then  (select DATE_FORMAT(FROM_UNIXTIME(cmc.timemodified), '%d/%m/%Y')  
                FROM mdl_course_modules_completion cmc  
                JOIN mdl_course_modules cm ON cmc.coursemoduleid = cm.id
                JOIN mdl_modules m ON cm.module = m.id
                where cmc.userid = u.id and cm.course = c.id 
                and  gi.iteminstance=cm.instance and m.id=16)
            else ' '
            end)
    WHEN gi.itemmodule = 'scorm' 
    then (
        case 
            when(
            select DATE_FORMAT(FROM_UNIXTIME(cmc.timemodified), '%d/%m/%Y')  from   mdl_course_modules_completion cmc  
            JOIN mdl_course_modules cm ON cmc.coursemoduleid = cm.id
            JOIN mdl_modules m ON cm.module = m.id
            where cmc.userid = u.id and cm.course = c.id 
            and  gi.iteminstance=cm.instance and m.id=18)<>'01/01/1970' 
            then  
            (select DATE_FORMAT(FROM_UNIXTIME(cmc.timemodified), '%d/%m/%Y')  
            FROM mdl_course_modules_completion cmc  
            JOIN mdl_course_modules cm ON cmc.coursemoduleid = cm.id
            JOIN mdl_modules m ON cm.module = m.id
            where cmc.userid = u.id and cm.course = c.id 
            and  gi.iteminstance=cm.instance and m.id=18 ) 
            else ' '
            end) 
    end as 'DATE_REALISATION',
    
     
    (select distinct ROUND(gg.finalgrade,2) from mdl_grade_grades AS gg where  gg.itemid=gi.id and  gg.userid=u.id 
    ) as NOTE,
    CASE
    WHEN gi.itemmodule = 'quiz' THEN(select MAX(at.attempt) from mdl_quiz_attempts at,mdl_quiz q where at.quiz=q.id and at.userid=u.id and q.course=c.id and q.id=gi.iteminstance)
    WHEN gi.itemmodule = 'scorm' THEN (SELECT MAX(st.attempt) FROM  mdl_scorm_scoes_track AS st, mdl_scorm sc where sc.id=st.scormid and st.userid=u.id and sc.course=c.id and sc.id=gi.iteminstance)  
    END AS TENTATIVE,
    
    
    
     FROM_UNIXTIME(f5.data,'%d/%m/%Y') as date_recrutement,FROM_UNIXTIME(f10.data,'%d/%m/%Y') as DATE_PRISE_POSTE,
    f6.data as CODE_EMPLOI,
     f7.data as LIBELLE_EMPLOI,
     f8.data as LIBELLE_POSTE,
     f22.data as MATRICULE_MANAGER,
     f11.data as CODE_UNITE,
     f24.data as DIRECTION,
     f23.data as DGA,
     f15.data as MANAGER,
     f12.data as LIBELLE_UNITE,
    
    
    case 
    when FROM_UNIXTIME(c.startdate,'%d/%m/%Y')<=FROM_UNIXTIME(f17.data,'%d/%m/%Y')<=FROM_UNIXTIME(c.enddate,'%d/%m/%Y') 
    or
    FROM_UNIXTIME(c.startdate,'%d/%m/%Y')<=FROM_UNIXTIME(f18.data,'%d/%m/%Y')<=FROM_UNIXTIME(c.enddate,'%d/%m/%Y') 
    THEN 
        case when f16.data='MTN' then 'Maternité'
             when f16.data='MLD' then 'Maladie Longue Durée'
            else  '---' 
        END
    else '---'
    
    END AS ABSENCE,
    
    case 
    when (f16.data='MTN' or f16.data='MLD')
    and FROM_UNIXTIME(c.startdate,'%d/%m/%Y')<=FROM_UNIXTIME(f17.data,'%d/%m/%Y')<=FROM_UNIXTIME(c.enddate,'%d/%m/%Y')
    then FROM_UNIXTIME(f17.data,'%d/%m/%Y')
    else '---'
    END as DEBUT_ABSENCE,
    
    case 
    when (f16.data='MTN' or f16.data='MLD')
    and   FROM_UNIXTIME(c.startdate,'%d/%m/%Y')<=FROM_UNIXTIME(f18.data,'%d/%m/%Y')<=FROM_UNIXTIME(c.enddate,'%d/%m/%Y')
    then FROM_UNIXTIME(f18.data,'%d/%m/%Y')
    else '---'
    END as FIN_ABSENCE
    
    
    from mdl_user u
    join mdl_user_enrolments enr on u.id=enr.userid
    join mdl_enrol er on enr.enrolid=er.id
    join mdl_course c on er.courseid=c.id
    JOIN mdl_course_categories cat on cat.id=c.category
    left join mdl_grade_items gi on c.id=gi.courseid and gi.itemtype<>'course'
    
    
    LEFT JOIN mdl_user_info_data AS f5 ON u.id = f5.userid and f5.fieldid=5
    LEFT JOIN mdl_user_info_data AS f6  ON u.id = f6.userid and f6.fieldid=6
    LEFT JOIN mdl_user_info_data AS f7  ON u.id = f7.userid and f7.fieldid=7
    LEFT JOIN mdl_user_info_data AS f8  ON u.id = f8.userid and f8.fieldid=8 
    LEFT JOIN mdl_user_info_data AS f22  ON u.id = f22.userid and f22.fieldid=22 
    LEFT JOIN mdl_user_info_data AS f11  ON u.id = f11.userid and f11.fieldid=11 
    LEFT JOIN mdl_user_info_data AS f24  ON u.id = f24.userid and f24.fieldid=24 
    LEFT JOIN mdl_user_info_data AS f23  ON u.id = f23.userid and f23.fieldid=23 
    LEFT JOIN mdl_user_info_data AS f15  ON u.id = f15.userid and f15.fieldid=15 
    LEFT JOIN mdl_user_info_data AS f12  ON u.id = f12.userid and f12.fieldid=12 
    LEFT JOIN mdl_user_info_data AS f16  ON u.id = f16.userid and f16.fieldid=16 
    
    LEFT JOIN mdl_user_info_data AS f10  ON u.id = f10.userid and f10.fieldid=10 
    LEFT JOIN mdl_user_info_data AS f17  ON u.id = f17.userid and f17.fieldid=17 
    LEFT JOIN mdl_user_info_data AS f18  ON u.id = f18.userid and f18.fieldid=18
    where 1=1  
    and gi.id = $selected_val 
    and f15.data like '%$manager%'            
    and f23.data like  '%$dga%'           
    and f12.data like '%$unite%'  
    and f24.data like '%$direction%'          
    
    order by c.id,u.id limit 5000";
    $query0 = $db->query($sql0filters);

    return array($query0,$sql0filters);
}








function sql0v3($db,$selected_val,$time1,$time2,$time3,$time4){
    $sql0v3 = "SELECT distinct 
            cat.name AS 'CATEGORIE',c.fullname AS 'FORMATION', c.visible as 'VISIBILITE',
            CASE 
              WHEN gi.itemtype = 'course' 
               THEN CONCAT('Total des activités')
              ELSE gi.itemname
            END  AS 'ACTIVITE',u.idnumber as 'MATRICULE', u.firstname AS 'PRENOM',u.lastname AS 'NOM', 
            
             CASE WHEN u.suspended=1  then 'suspendu' WHEN u.deleted=1  
             then 'supprimé' else 'Actif' END As Etat ,
            FROM_UNIXTIME(enr.timecreated,'%d/%m/%Y') AS 'DATE INSCRIPTION',
            
            
              
                    case
                    when (select distinct ROUND(gg.finalgrade,2) from mdl_grade_grades AS gg where  gg.itemid=gi.id and  gg.userid=u.id)>=ROUND(gi.gradepass,2)
                    then 'Terminé'
                    when (select distinct ROUND(gg.finalgrade,2) from mdl_grade_grades AS gg where  gg.itemid=gi.id and  gg.userid=u.id)<ROUND(gi.gradepass,2)
                    then 'Non Terminé'
                    else 'Non Terminé'
                        END
            as 'ETAT_FORMATION',
            
            CASE
            WHEN gi.itemmodule = 'quiz' 
            THEN(
                case 
                    when (select DATE_FORMAT(FROM_UNIXTIME(cmc.timemodified), '%d/%m/%Y')  
                        FROM mdl_course_modules_completion cmc  
                        JOIN mdl_course_modules cm ON cmc.coursemoduleid = cm.id
                        JOIN mdl_modules m ON cm.module = m.id
                        where cmc.userid = u.id and cm.course = c.id 
                        and  gi.iteminstance=cm.instance and m.id=16) <>'01/01/1970' 
                    then  (select DATE_FORMAT(FROM_UNIXTIME(cmc.timemodified), '%d/%m/%Y')  
                        FROM mdl_course_modules_completion cmc  
                        JOIN mdl_course_modules cm ON cmc.coursemoduleid = cm.id
                        JOIN mdl_modules m ON cm.module = m.id
                        where cmc.userid = u.id and cm.course = c.id 
                        and  gi.iteminstance=cm.instance and m.id=16)
                    else ' '
                    end)
            WHEN gi.itemmodule = 'scorm' 
            then (
                case 
                    when(
                    select DATE_FORMAT(FROM_UNIXTIME(cmc.timemodified), '%d/%m/%Y')  from   mdl_course_modules_completion cmc  
                    JOIN mdl_course_modules cm ON cmc.coursemoduleid = cm.id
                    JOIN mdl_modules m ON cm.module = m.id
                    where cmc.userid = u.id and cm.course = c.id 
                    and  gi.iteminstance=cm.instance and m.id=18)<>'01/01/1970' 
                    then  
                    (select DATE_FORMAT(FROM_UNIXTIME(cmc.timemodified), '%d/%m/%Y')  
                    FROM mdl_course_modules_completion cmc  
                    JOIN mdl_course_modules cm ON cmc.coursemoduleid = cm.id
                    JOIN mdl_modules m ON cm.module = m.id
                    where cmc.userid = u.id and cm.course = c.id 
                    and  gi.iteminstance=cm.instance and m.id=18 ) 
                    else ' '
                    end) 
            end as 'DATE_REALISATION',
            
             
            (select distinct ROUND(gg.finalgrade,2) from mdl_grade_grades AS gg where  gg.itemid=gi.id and  gg.userid=u.id 
            ) as NOTE,
            CASE
            WHEN gi.itemmodule = 'quiz' THEN(select MAX(at.attempt) from mdl_quiz_attempts at,mdl_quiz q where at.quiz=q.id and at.userid=u.id and q.course=c.id and q.id=gi.iteminstance)
            WHEN gi.itemmodule = 'scorm' THEN (SELECT MAX(st.attempt) FROM  mdl_scorm_scoes_track AS st, mdl_scorm sc where sc.id=st.scormid and st.userid=u.id and sc.course=c.id and sc.id=gi.iteminstance)  
            END AS TENTATIVE,
            
            
            
             FROM_UNIXTIME(f5.data,'%d/%m/%Y') as date_recrutement,FROM_UNIXTIME(f10.data,'%d/%m/%Y') as DATE_PRISE_POSTE,
            f6.data as CODE_EMPLOI,
             f7.data as LIBELLE_EMPLOI,
             f8.data as LIBELLE_POSTE,
             f22.data as MATRICULE_MANAGER,
             f11.data as CODE_UNITE,
             f24.data as DIRECTION,
             f23.data as DGA,
             f15.data as MANAGER,
             f12.data as LIBELLE_UNITE,
            
            
            case 
            when FROM_UNIXTIME(c.startdate,'%d/%m/%Y')<=FROM_UNIXTIME(f17.data,'%d/%m/%Y')<=FROM_UNIXTIME(c.enddate,'%d/%m/%Y') 
            or
            FROM_UNIXTIME(c.startdate,'%d/%m/%Y')<=FROM_UNIXTIME(f18.data,'%d/%m/%Y')<=FROM_UNIXTIME(c.enddate,'%d/%m/%Y') 
            THEN 
                case when f16.data='MTN' then 'Maternité'
                     when f16.data='MLD' then 'Maladie Longue Durée'
                    else  '---' 
                END
            else '---'
            
            END AS ABSENCE,
            
            case 
            when (f16.data='MTN' or f16.data='MLD')
            and FROM_UNIXTIME(c.startdate,'%d/%m/%Y')<=FROM_UNIXTIME(f17.data,'%d/%m/%Y')<=FROM_UNIXTIME(c.enddate,'%d/%m/%Y')
            then FROM_UNIXTIME(f17.data,'%d/%m/%Y')
            else '---'
            END as DEBUT_ABSENCE,
            
            case 
            when (f16.data='MTN' or f16.data='MLD')
            and   FROM_UNIXTIME(c.startdate,'%d/%m/%Y')<=FROM_UNIXTIME(f18.data,'%d/%m/%Y')<=FROM_UNIXTIME(c.enddate,'%d/%m/%Y')
            then FROM_UNIXTIME(f18.data,'%d/%m/%Y')
            else '---'
            END as FIN_ABSENCE
            
            
            from mdl_user u
            join mdl_user_enrolments enr on u.id=enr.userid
            join mdl_enrol er on enr.enrolid=er.id
            join mdl_course c on er.courseid=c.id
            JOIN mdl_course_categories cat on cat.id=c.category
            left join mdl_grade_items gi on c.id=gi.courseid and gi.itemtype<>'course'
            
            
            LEFT JOIN mdl_user_info_data AS f5 ON u.id = f5.userid and f5.fieldid=5
            LEFT JOIN mdl_user_info_data AS f6  ON u.id = f6.userid and f6.fieldid=6
            LEFT JOIN mdl_user_info_data AS f7  ON u.id = f7.userid and f7.fieldid=7
            LEFT JOIN mdl_user_info_data AS f8  ON u.id = f8.userid and f8.fieldid=8 
            LEFT JOIN mdl_user_info_data AS f22  ON u.id = f22.userid and f22.fieldid=22 
            LEFT JOIN mdl_user_info_data AS f11  ON u.id = f11.userid and f11.fieldid=11 
            LEFT JOIN mdl_user_info_data AS f24  ON u.id = f24.userid and f24.fieldid=24 
            LEFT JOIN mdl_user_info_data AS f23  ON u.id = f23.userid and f23.fieldid=23 
            LEFT JOIN mdl_user_info_data AS f15  ON u.id = f15.userid and f15.fieldid=15 
            LEFT JOIN mdl_user_info_data AS f12  ON u.id = f12.userid and f12.fieldid=12 
            LEFT JOIN mdl_user_info_data AS f16  ON u.id = f16.userid and f16.fieldid=16 
            
            LEFT JOIN mdl_user_info_data AS f10  ON u.id = f10.userid and f10.fieldid=10 
            LEFT JOIN mdl_user_info_data AS f17  ON u.id = f17.userid and f17.fieldid=17 
            LEFT JOIN mdl_user_info_data AS f18  ON u.id = f18.userid and f18.fieldid=18
            where 1=1  
            and gi.id = $selected_val 
            and enr.timecreated BETWEEN $time1 and $time2 
            and f5.data BETWEEN $time3 and $time4
            
            order by c.id,u.id limit 5000";
            $query0 = $db->query($sql0v3);

            return array($query0,$sql0v3);
}

function sql0v4($db,$selected_val,$time1,$time2,$time3,$time4,$unite,$manager,$dga,$direction){
    $sql0v4 = "SELECT distinct 
            cat.name AS 'CATEGORIE',c.fullname AS 'FORMATION', c.visible as 'VISIBILITE',
            CASE 
              WHEN gi.itemtype = 'course' 
               THEN CONCAT('Total des activités')
              ELSE gi.itemname
            END  AS 'ACTIVITE',u.idnumber as 'MATRICULE', u.firstname AS 'PRENOM',u.lastname AS 'NOM', 
            
             CASE WHEN u.suspended=1  then 'suspendu' WHEN u.deleted=1  
             then 'supprimé' else 'Actif' END As Etat ,
            FROM_UNIXTIME(enr.timecreated,'%d/%m/%Y') AS 'DATE INSCRIPTION',
            
            
              
                    case
                    when (select distinct ROUND(gg.finalgrade,2) from mdl_grade_grades AS gg where  gg.itemid=gi.id and  gg.userid=u.id)>=ROUND(gi.gradepass,2)
                    then 'Terminé'
                    when (select distinct ROUND(gg.finalgrade,2) from mdl_grade_grades AS gg where  gg.itemid=gi.id and  gg.userid=u.id)<ROUND(gi.gradepass,2)
                    then 'Non Terminé'
                    else 'Non Terminé'
                        END
            as 'ETAT_FORMATION',
            
            CASE
            WHEN gi.itemmodule = 'quiz' 
            THEN(
                case 
                    when (select DATE_FORMAT(FROM_UNIXTIME(cmc.timemodified), '%d/%m/%Y')  
                        FROM mdl_course_modules_completion cmc  
                        JOIN mdl_course_modules cm ON cmc.coursemoduleid = cm.id
                        JOIN mdl_modules m ON cm.module = m.id
                        where cmc.userid = u.id and cm.course = c.id 
                        and  gi.iteminstance=cm.instance and m.id=16) <>'01/01/1970' 
                    then  (select DATE_FORMAT(FROM_UNIXTIME(cmc.timemodified), '%d/%m/%Y')  
                        FROM mdl_course_modules_completion cmc  
                        JOIN mdl_course_modules cm ON cmc.coursemoduleid = cm.id
                        JOIN mdl_modules m ON cm.module = m.id
                        where cmc.userid = u.id and cm.course = c.id 
                        and  gi.iteminstance=cm.instance and m.id=16)
                    else ' '
                    end)
            WHEN gi.itemmodule = 'scorm' 
            then (
                case 
                    when(
                    select DATE_FORMAT(FROM_UNIXTIME(cmc.timemodified), '%d/%m/%Y')  from   mdl_course_modules_completion cmc  
                    JOIN mdl_course_modules cm ON cmc.coursemoduleid = cm.id
                    JOIN mdl_modules m ON cm.module = m.id
                    where cmc.userid = u.id and cm.course = c.id 
                    and  gi.iteminstance=cm.instance and m.id=18)<>'01/01/1970' 
                    then  
                    (select DATE_FORMAT(FROM_UNIXTIME(cmc.timemodified), '%d/%m/%Y')  
                    FROM mdl_course_modules_completion cmc  
                    JOIN mdl_course_modules cm ON cmc.coursemoduleid = cm.id
                    JOIN mdl_modules m ON cm.module = m.id
                    where cmc.userid = u.id and cm.course = c.id 
                    and  gi.iteminstance=cm.instance and m.id=18 ) 
                    else ' '
                    end) 
            end as 'DATE_REALISATION',
            
             
            (select distinct ROUND(gg.finalgrade,2) from mdl_grade_grades AS gg where  gg.itemid=gi.id and  gg.userid=u.id 
            ) as NOTE,
            CASE
            WHEN gi.itemmodule = 'quiz' THEN(select MAX(at.attempt) from mdl_quiz_attempts at,mdl_quiz q where at.quiz=q.id and at.userid=u.id and q.course=c.id and q.id=gi.iteminstance)
            WHEN gi.itemmodule = 'scorm' THEN (SELECT MAX(st.attempt) FROM  mdl_scorm_scoes_track AS st, mdl_scorm sc where sc.id=st.scormid and st.userid=u.id and sc.course=c.id and sc.id=gi.iteminstance)  
            END AS TENTATIVE,
            
            
            
             FROM_UNIXTIME(f5.data,'%d/%m/%Y') as date_recrutement,FROM_UNIXTIME(f10.data,'%d/%m/%Y') as DATE_PRISE_POSTE,
            f6.data as CODE_EMPLOI,
             f7.data as LIBELLE_EMPLOI,
             f8.data as LIBELLE_POSTE,
             f22.data as MATRICULE_MANAGER,
             f11.data as CODE_UNITE,
             f24.data as DIRECTION,
             f23.data as DGA,
             f15.data as MANAGER,
             f12.data as LIBELLE_UNITE,
            
            
            case 
            when FROM_UNIXTIME(c.startdate,'%d/%m/%Y')<=FROM_UNIXTIME(f17.data,'%d/%m/%Y')<=FROM_UNIXTIME(c.enddate,'%d/%m/%Y') 
            or
            FROM_UNIXTIME(c.startdate,'%d/%m/%Y')<=FROM_UNIXTIME(f18.data,'%d/%m/%Y')<=FROM_UNIXTIME(c.enddate,'%d/%m/%Y') 
            THEN 
                case when f16.data='MTN' then 'Maternité'
                     when f16.data='MLD' then 'Maladie Longue Durée'
                    else  '---' 
                END
            else '---'
            
            END AS ABSENCE,
            
            case 
            when (f16.data='MTN' or f16.data='MLD')
            and FROM_UNIXTIME(c.startdate,'%d/%m/%Y')<=FROM_UNIXTIME(f17.data,'%d/%m/%Y')<=FROM_UNIXTIME(c.enddate,'%d/%m/%Y')
            then FROM_UNIXTIME(f17.data,'%d/%m/%Y')
            else '---'
            END as DEBUT_ABSENCE,
            
            case 
            when (f16.data='MTN' or f16.data='MLD')
            and   FROM_UNIXTIME(c.startdate,'%d/%m/%Y')<=FROM_UNIXTIME(f18.data,'%d/%m/%Y')<=FROM_UNIXTIME(c.enddate,'%d/%m/%Y')
            then FROM_UNIXTIME(f18.data,'%d/%m/%Y')
            else '---'
            END as FIN_ABSENCE
            
            
            from mdl_user u
            join mdl_user_enrolments enr on u.id=enr.userid
            join mdl_enrol er on enr.enrolid=er.id
            join mdl_course c on er.courseid=c.id
            JOIN mdl_course_categories cat on cat.id=c.category
            left join mdl_grade_items gi on c.id=gi.courseid and gi.itemtype<>'course'
            
            
            LEFT JOIN mdl_user_info_data AS f5 ON u.id = f5.userid and f5.fieldid=5
            LEFT JOIN mdl_user_info_data AS f6  ON u.id = f6.userid and f6.fieldid=6
            LEFT JOIN mdl_user_info_data AS f7  ON u.id = f7.userid and f7.fieldid=7
            LEFT JOIN mdl_user_info_data AS f8  ON u.id = f8.userid and f8.fieldid=8 
            LEFT JOIN mdl_user_info_data AS f22  ON u.id = f22.userid and f22.fieldid=22 
            LEFT JOIN mdl_user_info_data AS f11  ON u.id = f11.userid and f11.fieldid=11 
            LEFT JOIN mdl_user_info_data AS f24  ON u.id = f24.userid and f24.fieldid=24 
            LEFT JOIN mdl_user_info_data AS f23  ON u.id = f23.userid and f23.fieldid=23 
            LEFT JOIN mdl_user_info_data AS f15  ON u.id = f15.userid and f15.fieldid=15 
            LEFT JOIN mdl_user_info_data AS f12  ON u.id = f12.userid and f12.fieldid=12 
            LEFT JOIN mdl_user_info_data AS f16  ON u.id = f16.userid and f16.fieldid=16 
            
            LEFT JOIN mdl_user_info_data AS f10  ON u.id = f10.userid and f10.fieldid=10 
            LEFT JOIN mdl_user_info_data AS f17  ON u.id = f17.userid and f17.fieldid=17 
            LEFT JOIN mdl_user_info_data AS f18  ON u.id = f18.userid and f18.fieldid=18
            where 1=1  
            and gi.id = $selected_val 
            and enr.timecreated BETWEEN $time1 and $time2 
            and f5.data BETWEEN $time3 and $time4
            and f15.data like '%$manager%'            
            and f23.data like  '%$dga%'           
            and f12.data like '%$unite%'  
            and f24.data like '%$direction%'          
            
            order by c.id,u.id limit 5000";
            $query0 = $db->query($sql0v4);

            return array($query0,$sql0v4);
}




function sql1($db){
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

    //return $query1;
    return array($query1,$sql1);
}


/* $selected_val = 12;
sql0v1($db,$selected_val); */