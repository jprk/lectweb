<?php
/* Fetch / initialize session */
session_start ();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<title>Import seznamu studentů z KOSu - krok 2</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head>
<body>
<?php

/* Global configuration */
require ( 'config.php' );

//require ( REQUIRE_DIR . 'tools.php');
require ( REQUIRE_DIR . 'LectwebSmarty.class.php');
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
require ( REQUIRE_DIR . 'StudentBean.class.php');
require ( REQUIRE_DIR . 'LectureBean.class.php');
//require ( REQUIRE_DIR . 'LecturerBean.class.php');
//require ( REQUIRE_DIR . 'ExerciseBean.class.php');
//require ( REQUIRE_DIR . 'exerciseListBean.class.php');
//require ( REQUIRE_DIR . 'StudentExerciseBean.class.php');
//require ( REQUIRE_DIR . 'StudentLectureBean.class.php');
require ( REQUIRE_DIR . 'SessionDataBean.class.php');

$addyear = $_POST['addyear'];
$schoolyear = SessionDataBean::getSchoolYear();
$lecture_id = SessionDataBean::getLectureId();

if ( is_uploaded_file ( $_FILES['kosfile']['tmp_name'] ))
{
	echo "<h1>Soubor ". $_FILES['kosfile']['name'] ." byl úspěšně nahrán na server.</h1>\n";
	echo "<p>Nyní se pokusíme zobrazit jeho obsah:<p>\n";
	
	$handle = @fopen ( $_FILES['kosfile']['tmp_name'], "r" );
	if ( $handle )
	{
        /* Construct a Smarty instance. Configuration has been specified in config.php. */
        $smarty = new LectwebSmarty ( $config, TRUE );
        /* Initialise database connection */
        $smlink = $smarty->dbOpen ();
        $lct = new LectureBean ( 0, $smarty, "x", "x" );
        $map = $lct->getSelectMap();
    
	echo "\n<!-- "; print_r($map); echo " -->\n";
    
    $row = 0;
    $date = getdate ();
	$isValid = FALSE;
    
    $surname = array ();
    $firstname = array ();
    $hash = array ();
    $yearno = array ();
    $groupno = array ();
    $parno = array ();
    
	echo "<form action=\"import_koslist_2.php\" method=\"post\" name=\"kosimport1\" id=\"kosimport1\">\n";
    echo "<table border=\"1\" cellspacing=\"0\" cellpadding=\"4\">";
    echo "<thead>";
    echo "<tr><th>rodné číslo</th><th>příjmení</th><th>jméno</th><th>ročník</th><th>skupina</th><th>login</th><th>e-mail</th></tr>";
    echo "</thead>";
		
    while ( !feof ( $handle ))
	{
        $buffer = fgets ( $handle, 4096 );
        /* The file contains sometimes also form feed character (^L, 0x0c)
           which shall be removed as well. */
        $trimmed = trim ( $buffer, " \t\n\r\0\x0b\x0c" );
			
        if ( empty ( $trimmed ))
        {
  		    echo "\n<!-- line is empty, continuing -->\n";
            continue;
        }
      
        $la = explode ( ";",  $trimmed );
			
        echo "\n<!-- la="; print_r($la); echo " -->\n";
        //echo "<!-- buffer='$buffer' -->";
			
        echo "<tr>";
        $surname   = iconv ( "windows-1250", "utf-8", trim ( $la[1], " \t\n\r\"" ));
        $firstname = iconv ( "windows-1250", "utf-8", trim ( $la[2], " \t\n\r\"" ));
		$syear     = trim ( $la[3], " \t\n\r\"" );
		$group     = trim ( $la[4], " \t\n\r\"" );
		$email     = trim ( $la[5], " \t\n\r\"" );
        $emailex   = explode ( "@", $email );
        $login     = trim ( $emailex[0], " \t\n\r\"" );
		$hash      = trim ( $la[6], " \t\n\r\"" );
      
		/* Students from Decin do not have a group number. We will assign them
		   with group 99 which is not used in Prague. */
		if ( empty ( $group )) { $group = "99"; }
		if ( $addyear ) { $syear++; }
        	
        echo '<tr>'."\n";
        echo '<td><input type="text" name="hash['.$row.']"      readonly="readonly" value="'.$hash.'" size="10"></td>'."\n";
        echo '<td><input type="text" name="surname['.$row.']"   readonly="readonly" value="'.$surname.'"></td>'."\n";
        echo '<td><input type="text" name="firstname['.$row.']" readonly="readonly" value="'.$firstname.'"></td>'."\n";
        echo '<td align="center"><input type="text" name="yearno['.$row.']"  readonly="readonly" value="'.$syear.'" size="1" style="text-align: center;"></td>'."\n";
        echo '<td align="center"><input type="text" name="groupno['.$row.']" readonly="readonly" value="'.$group.'" size="2" style="text-align: center;"></td>'."\n";
        echo '<td align="center"><input type="text" name="login['.$row.']"   readonly="readonly" value="'.$login.'" size="20" style="text-align: center;"></td>'."\n";
        echo '<td><input type="text" name="email['.$row.']"   readonly="readonly" value="'.$email.'" size="40" style="text-align: center;"></td>'."\n";
        echo "</tr>\n"."\n";
      
        $row++;
 	}
 	
    fclose ( $handle );
	echo "</table>\n";
		
    if ( $addyear )
    {
        echo "<p>\n";
        echo "Ročník studentů zvýšen o jedna.\n";
        echo "</p>\n";
    }
    
    echo "<p>\n";
    echo "Celkem $row studentů.\n";
    echo "</p>\n";

    echo "<p>\n";
    echo "Předmět: \n";
    echo '<select name="lecture_id">'."\n";
    foreach ( $map as $key=>$val )
    {
      echo '<option value="'.$key.'">'.$val."</option>\n";
    }
    echo '</select>';
    echo "</p>\n";

    echo "<p>\n";
    echo "Školní rok: \n";
    echo '<select name="schoolyear">'."\n";
    foreach ( $smarty->_yearMap as $key=>$val )
    {
        if ( $key == $schoolyear ) $sel = ' selected="selected"';
        else $sel = '';
        echo '<option value="'.$key.'"'.$sel.'>'.$val."</option>\n";
    }
    echo '</select>';
    echo "</p>\n";

    echo "<input type=\"submit\" name=\"Submit\" value=\"Nahrát\">\n";
    echo "</form>\n";
	}
	else
	{
		echo "<p>Nahraný soubor nelze otevřít pro čtení.</p>\n";
	}
}
else
{
		echo "<h1>Možný útok na nahrávání souborů</h1>";
		echo "<p>Soubor '". $_FILES['userfile']['tmp_name'] . "'.</p>";
}
?>
</body>
</html>
