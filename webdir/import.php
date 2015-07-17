<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<title>Import CSV dat - krok 2</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head>
<body>
<?php

/* Global configuration */
require ( 'config.php' );

require ( REQUIRE_DIR . 'tools.php');
require ( REQUIRE_DIR . 'BaseBean.class.php');
require ( REQUIRE_DIR . 'DatabaseBean.class.php');
//require ( REQUIRE_DIR . 'FileBean.class.php');
//require ( REQUIRE_DIR . 'UserBean.class.php');
//require ( REQUIRE_DIR . 'LoginBean.class.php');
//require ( REQUIRE_DIR . 'NewsBean.class.php');
//require ( REQUIRE_DIR . 'NoteBean.class.php');
//require ( REQUIRE_DIR . 'PointsBean.class.php');
//require ( REQUIRE_DIR . 'TaskBean.class.php');
//require ( REQUIRE_DIR . 'SubtaskBean.class.php');
//require ( REQUIRE_DIR . 'TaskSubtasksBean.class.php');
//require ( REQUIRE_DIR . 'EvaluationBean.class.php');
//require ( REQUIRE_DIR . 'EvaluationTasksBean.class.php');
require ( REQUIRE_DIR . 'LectwebSmarty.class.php');
require ( REQUIRE_DIR . 'StudentBean.class.php');
//require ( REQUIRE_DIR . 'LectureBean.class.php');
//require ( REQUIRE_DIR . 'LecturerBean.class.php');
//require ( REQUIRE_DIR . 'ExerciseBean.class.php');
//require ( REQUIRE_DIR . 'exerciseListBean.class.php');
//require ( REQUIRE_DIR . 'StudentExerciseBean.class.php');
//require ( REQUIRE_DIR . 'StudentLectureBean.class.php');

if ( is_uploaded_file ( $_FILES['csvfile']['tmp_name'] ))
{
	echo "<h1>File ". $_FILES['csvfile']['name'] ." uploaded successfully.</h1>\n";
	echo "<p>Displaying contents:<p>\n";
	
	/* Construct a Smarty instance. Configuration has been specified in config.php. */
	$smarty = new LectwebSmarty ( $config );
	/* Initialise database connection */
	$smlink = $smarty->dbOpen ();
	
	$handle = @fopen ( $_FILES['csvfile']['tmp_name'], "r" );
	if ( $handle )
	{
		$date = getdate ();
		echo "<table>";
		while ( !feof ( $handle ))
		{
			$buffer = fgets ( $handle, 4096 );
			$trimmed = trim ( $buffer );
			$la = explode ( ";",  $trimmed );
			
			echo "<tr>";
			if ( count ( $la ) == 4 )
			{
				$surname = trim ( $la[0] );
				$name    = trim ( $la[1] );
				$syear   = trim ( $la[2] );
				$group   = trim ( $la[3] );
				
				$sb = new StudentBean ( 0, $smarty, "x", "x" );
				$sb->surname   = $surname;
				$sb->firstname = $name;
				$sb->groupno   = intval ( $group );
				$sb->yearno    = intval ( $syear );
				$sb->yearcal   = $date["year"];
				$sb->dbReplace();
				$id = $sb->id;
				
				echo "<td>imported, id=$id:</td><td>$surname</td><td>$name</td><td>$syear</td><td>$group</td>";
			}
			else
			{
				echo "<td colspan=\"5\">skipped '$trimmed'</td>";
			}
			echo "</tr>\n";
   		}
   		fclose ( $handle );
		echo "</table>";
	}
	else
	{
		echo "<p>Error opening uploaded file.</p>\n";
	}
}
else
{
		echo "<h1>Possible file upload attack</h1>";
		echo "filename '". $_FILES['userfile']['tmp_name'] . "'.";
}
?>
</body>
</html>
