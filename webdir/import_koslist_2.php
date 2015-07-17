<?php

/* Global configuration */
require ( 'config.php' );

//require ( REQUIRE_DIR . 'tools.php');
require ( REQUIRE_DIR . 'BaseBean.class.php');
require ( REQUIRE_DIR . 'DatabaseBean.class.php');
//require ( REQUIRE_DIR . 'FileBean.class.php');
require ( REQUIRE_DIR . 'UserBean.class.php');
//require ( REQUIRE_DIR . 'LoginBean.class.php');
//require ( REQUIRE_DIR . 'NewsBean.class.php');
//require ( REQUIRE_DIR . 'NoteBean.class.php');
require ( REQUIRE_DIR . 'PointsBean.class.php');
require ( REQUIRE_DIR . 'TaskBean.class.php');
require ( REQUIRE_DIR . 'SubtaskBean.class.php');
require ( REQUIRE_DIR . 'TaskSubtasksBean.class.php');
require ( REQUIRE_DIR . 'EvaluationBean.class.php');
require ( REQUIRE_DIR . 'EvaluationTasksBean.class.php');
require ( REQUIRE_DIR . 'LectwebSmarty.class.php');
require ( REQUIRE_DIR . 'StudentBean.class.php');
//require ( REQUIRE_DIR . 'LectureBean.class.php');
//require ( REQUIRE_DIR . 'LecturerBean.class.php');
//require ( REQUIRE_DIR . 'ExerciseBean.class.php');
//require ( REQUIRE_DIR . 'exerciseListBean.class.php');
require ( REQUIRE_DIR . 'StudentExerciseBean.class.php');
require ( REQUIRE_DIR . 'StudentLectureBean.class.php');
require ( REQUIRE_DIR . 'SessionDataBean.class.php');

/* Fetch / initialize session */
session_start ();

/* Initialise session defaults in case that the session data storage does not
   contain the variables we would need later. */
SessionDataBean::conditionalInit ( schoolYearStart() );

/* Binary flags for user roles */
$isAllowed  = isRole ( USR_ADMIN ) || isRole ( USR_LECTURER );

$firstname  = $_POST['firstname'];
$surname    = $_POST['surname'];
$hash       = $_POST['hash'];
$yearno     = $_POST['yearno'];
$groupno    = $_POST['groupno'];
$login      = $_POST['login'];
$email      = $_POST['email'];
$lecture_id = $_POST['lecture_id'];
$schoolyear = $_POST['schoolyear'];

$schoolyearText = $schoolyear . '/' . ($schoolyear+1);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
    <title>Import dat z KOSu - krok 3</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head>
<body>
<?php
$errstr = '';
if ( empty ( $firstname ))  $errstr .= "<li>Chybí křestní jména</li>\n";
if ( empty ( $surname ))    $errstr .= "<li>Chybí příjmení</li>\n";
if ( empty ( $hash ))       $errstr .= "<li>Chybí heše</li>\n";
if ( empty ( $yearno ))     $errstr .= "<li>Chybí ročníky</li>\n";
if ( empty ( $groupno ))    $errstr .= "<li>Chybí skupiny</li>\n";
if ( empty ( $login ))      $errstr .= "<li>Chybí loginy</li>\n";
if ( empty ( $email ))      $errstr .= "<li>Chybí emaily</li>\n";
if ( empty ( $lecture_id )) $errstr .= "<li>Chybí předmět</li>\n";
if ( empty ( $schoolyear )) $errstr .= "<li>Chybí školní rok</li>\n";
if ( ! $isAllowed )         $errstr .= "<li>Nemáte dostatečná oprávnění pro zápis seznamu studentů</li>\n";

if ( empty ( $errstr ))
{
	/* Construct a Smarty instance. Configuration has been specified in config.php. */
	$smarty = new LectwebSmarty ( $config, true );
	/* Initialise database connection */
	$smlink = $smarty->dbOpen ();

    /* Get the information about evaluation. We have to initialise points to
       PTS_NOT_CLASSIFIED. Abort if there is no evaluation. */
	$evaluationBean = new EvaluationBean (0, $smarty, "x", "x");
	/* This will initialise EvaluationBean with evaluation scheme for lecture
	   given by $this->lecture_id. The function returns 'true' if the bean
	   has been initialised. */
	$ret = $evaluationBean->initialiseFor ( $lecture_id );
	
	/* Check the initialisation status. */
	if ( ! $ret )
	{
        exit ( 
            "<h1>Chyba!</h1>\n" .
            "<p>Nebylo nalezeno žádné schéma vyhodnocení platné pro rok " .
            "$schoolyear pro tento předmět.</p>\n" .
            "</body></html>"	);
    }
    
    $num = count ( $firstname );
  
    $urole = SessionDataBean::getUserRole();
    $ulogin = SessionDataBean::getUserLogin();
    
    echo "<h1>Data nahrána úspěšně</h1>\n";
	echo "<p>Vaše role je $urole a login je $ulogin.</p>\n";
    echo "<p>Přidáme celkem $num záznamů (některé možná jenom opravíme):<p>\n";
		
	/* Get the list of tasks for evaluation of this exercise. The list will contain
	   only task IDs and we will have to fetch task and subtask information
	   by ourselves later. */
	$taskList = $evaluationBean->getTaskList ();

	/* This will both create a full list of subtasks corresponding to the
	   tasks of the chosen evaluation scheme and assign this list to the
	   Smarty variable 'subtaskList'. */
	$tsbean = new TaskSubtasksBean ( 0, $smarty, "x", "x" );
	$subtaskMap  = $tsbean->getSubtaskMapForTaskList ( $taskList );
    $subtasks = array_keys ( $subtaskMap );
  
    echo "\n<!-- ::subtaskMap::\n"; print_r($subtaskMap); echo " -->\n";
    echo "\n<!-- ::subtasks::\n"; print_r($subtasks); echo " -->\n";
    //print_r ( $login );
  
    $row = 0;
	$date = getdate ();
	$stlist = array();
	
	echo '<table border="1" cellspacing="0" cellpadding="4">'."\n";
  
    $ptbean = new PointsBean ( 0, $smarty, "", "" );
    while ( $row < $num )
	{
        $sb = new StudentBean ( 0, $smarty, "x", "x" );
        $sb->hash         = $hash[$row];
        $sb->surname      = $surname[$row];
        $sb->firstname    = $firstname[$row];
        $sb->groupno      = intval ( $groupno[$row] );
        $sb->yearno       = intval ( $yearno[$row] );
        $sb->calendaryear = $schoolyear;
        $sb->login        = $login[$row];
        $sb->email        = $email[$row];
        $sb->active       = 1;
        $sb->password     = "";
        $sb->dbReplace();
        $id = $sb->getObjectId();
			
		echo "<tr><td>id=$id:</td><td>$login[$row]</td><td>$surname[$row]</td><td>$firstname[$row]</td><td>$yearno[$row]</td><td>$groupno[$row]</td></tr>\n";
	
    	/* Now that the id of the student is valid we can add it to the list of
           students that visit exercises for the given lecture. */
        $stlist[] = $id;
    	
        foreach ( $subtasks as $val )
        {
          $ptbean->updatePoints ( $id, $val, $schoolyear, PTS_NOT_CLASSIFIED );
        } 
  
		$row++;
	}
	echo "</table>";

    echo "\n<!-- "; print_r($stlist); echo " -->";
  
    /* Write the list of students visiting the lecture into the database. */
    $seb = new StudentLectureBean ( $lecture_id, $smarty, "x", "x" );
    $seb->year = $schoolyear;
    echo "\n<!-- setStudentList() -->\n";
    $seb->setStudentList( $stlist, false );
  
    echo "<p>\n";
    echo "Seznam studentů vložen do databáze pro předmět id $lecture_id a\n";
    echo "školní rok $schoolyear/".($schoolyear+1).".\n";
    echo "</p>\n";
}
else
{
    echo "<h1>Chyba při načítání záznamů</h1>\n";
    echo "<p>Při načítání záznamů byly nalezeny následující chyby:</p>\n<ul>\n";
    echo $errstr;
    echo "</ul>\n<p>Do databáze nebyla zapsána žádná data.</p>\n";
}
?>
</body>
</html>
