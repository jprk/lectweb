<?php
/**
 * Controller of the Lectweb application.
 * 
 * (c) 2009,2010,2011,2012 Jan Prikryl, CVUT FD, Prague
 * 
 * Version: $Id: ctrl.php 59 2012-03-13 19:25:50Z prikryl $ 
 */

/* Global configuration */
require ( 'config.php' );

/* Read implementation of all classes that will be needed by our code. */
require ( REQUIRE_DIR . 'LectwebSmarty.class.php');
require ( REQUIRE_DIR . 'BaseBean.class.php');
require ( REQUIRE_DIR . 'DatabaseBean.class.php');

require ( REQUIRE_DIR . 'ArticleBean.class.php');
require ( REQUIRE_DIR . 'AssignmentsBean.class.php');
require ( REQUIRE_DIR . 'DeadlineExtensionBean.class.php');
require ( REQUIRE_DIR . 'EvaluationBean.class.php');
require ( REQUIRE_DIR . 'EvaluationTasksBean.class.php');
require ( REQUIRE_DIR . 'ExerciseBean.class.php');
require ( REQUIRE_DIR . 'ExerciseListBean.class.php');
require ( REQUIRE_DIR . 'FileBean.class.php');
require ( REQUIRE_DIR . 'FormAssignmentBean.class.php');
require ( REQUIRE_DIR . 'FormSolutionsBean.class.php');
require ( REQUIRE_DIR . 'ImportBean.class.php');
require ( REQUIRE_DIR . 'LDAPConnection.class.php');
require ( REQUIRE_DIR . 'LoginBean.class.php');
require ( REQUIRE_DIR . 'MenuBean.class.php');
require ( REQUIRE_DIR . 'NewsBean.class.php');
require ( REQUIRE_DIR . 'NoteBean.class.php');
require ( REQUIRE_DIR . 'PointsBean.class.php');
require ( REQUIRE_DIR . 'LectureBean.class.php');
require ( REQUIRE_DIR . 'LecturerBean.class.php');
require ( REQUIRE_DIR . 'SchoolYearBean.class.php');
require ( REQUIRE_DIR . 'SectionBean.class.php');
require ( REQUIRE_DIR . 'SessionDataBean.class.php');
require ( REQUIRE_DIR . 'SolutionBean.class.php');
require ( REQUIRE_DIR . 'StudentBean.class.php');
require ( REQUIRE_DIR . 'StudentExerciseBean.class.php');
require ( REQUIRE_DIR . 'StudentLectureBean.class.php');
require ( REQUIRE_DIR . 'StudentPassGenBean.class.php');
require ( REQUIRE_DIR . 'SubtaskBean.class.php');
require ( REQUIRE_DIR . 'SubtaskDatesBean.class.php');
require ( REQUIRE_DIR . 'TaskBean.class.php');
require ( REQUIRE_DIR . 'TaskDatesBean.class.php');
require ( REQUIRE_DIR . 'TaskSubtasksBean.class.php');
require ( REQUIRE_DIR . 'URLsBean.class.php');
require ( REQUIRE_DIR . 'UserBean.class.php');

/* Smarty plugins. */
//require ( REQUIRE_DIR . 'function.throt.php');

/* Administrator privileges needed for $action? */
$adminNeeded    = false;
$lecturerNeeded = false;
$studentNeeded  = false;
$loginNeeded    = false;

/* Performance counter needs function getmicrotime which is defined in
   the process of requiring 'tools.php' in 'LectwebSmarty.class.php'.*/
$timeStart = getmicrotime ();

/*
   Parse the command which is in the form of URI. The URI is passed to this script in $_SERVER['REQUEST_URI'].
   The expected format of the URI is

      <application_prefix>/<lecture_id_string>/<payload>

   where <payload> has more-or-less arbitrary format with reserved format

      node/<action>/<object>/<id>
      node/<id>

   which mimicks the original act=<action>,<object>,<id> format of the original application.
*/
$app_prefix = dirname ( $_SERVER['SCRIPT_NAME'] );
$param_path = substr ( $_SERVER['REQUEST_URI'], strlen($app_prefix)+1 );
list ( $lecture_id_str, $payload ) = explode ( '/', $param_path, 2 );

/* Empty <payload> means that the request is for the lecture home page. */
if ( empty ( $payload ))
{
    $payload = 'node/show/home/1';
}

/*
 * echo "Lecture ID = $lecture_id_str<br/>";
 * echo "Payload = $payload<br/>";
 */

list ( $prefix, $action, $object, $stringId ) = explode ( '/', $payload );

/* In case that the value of $prefix is not `node`, the value of $page
   contains the name of the section or article to display. */
if ( $prefix == 'node' )
{
    /* Test the parsed values for correctness. */
    $errorMsg = "";
    switch ( $object )
    {
	    case "article":
        case "board":
        case "evaluation":
        case "evltsk":
        case "exercise":
        case "exclist":
        case "extension":
        case "file":
        case "formassign":
        case "formsolution":
        case "lecture":
        case "import":
        case "lecturer":
        case "login":
        case "news":
        case "note":
        case "points":
        case "schoolyear":
        case "section":
        case "solution":
        case "student":
        case "stupass":
        case "stuexe":
        case "stulec":
        case "subtask":
        case "subtaskdates":
        case "task":
        case "taskdates":
        case "tsksub":
        case "urls":
        case "user":
            break;
        case "home":
            $action = "show";
            break;
        default:
            $object = 'error';
    }

    switch ( $action )
    {
        case "realdelete":
        case "edit":
        case "save":
            if ( $action == "save" && $object == "solution" )
            {
                $studentNeeded = true;
                break;
            }
            if ( $action == "edit" && $object == "formsolution" )
            {
                $studentNeeded = true;
                break;
            }
            if ( $action == "save" && $object == "formsolution" )
            {
                $studentNeeded = true;
                break;
            }
        case "admin":
            if ( $object != "comments" && $object != "order" ) $lecturerNeeded = true;
            break;
        case "delete":
            if ( $object == "login" ) break;
            if ( $object != "comments" && $object != "order" ) $lecturerNeeded = true;
        case "show":
            if ( $object == "stulec" ) $lecturerNeeded = true;
            break;
        case "verify":
            if ( $object == 'login' || $object == 'article' ) break;
        default:
            $action = "error";
    }

    $id = intval ( $stringId );
}
else
{
    /* The value of $page contains name of the section to display. */
    echo '<pre>';
    var_dump ( $_SERVER );
    echo '</pre>';

    $app_prefix = dirname ( $_SERVER['SCRIPT_NAME'] );
    $cmd_path = substr ( $_SERVER['REQUEST_URI'], strlen($app_prefix)+1);
    echo '<p>app_prefix = '.$app_prefix.'<br/>cmd_path = '.$cmd_path.'</p>' . PHP_EOL;

    die ( 'requests for section names are not implemented yet');
}

/* Headers. Do not send header for object 'file' and method 'show' as it is
   likely that the file is not an HTML document. Remember the output flag as we
   will use it later when constructing the Smarty instance to temporarily
   switch off the debugging output of the controller when serving a file
   object.
   @todo
   The same header is provided by the file/show method in case of an error
   message in HTML format. It would be nice to find a way how to circumvent
   this and have it in onle single place. */
//$isPageOutput = ( $object != "file" || ( $action != 'show' && $object == "file" )) ? true : false ;
$isPageOutput=true;
if ( $isPageOutput )
{
	header ("Content-Type: text/html; charset=utf-8");
}

/* Fetch / initialize session */
session_start ();

/* Check for the switch to another schoolyear. */
if ( ! empty($_GET['schoolyear']))
{
    SessionDataBean::setSchoolYear ( $_GET['schoolyear'] );
}
/* Initialise session defaults in case that the session data storage does not
   contain the variables we would need later. */
SessionDataBean::conditionalInit ( schoolYearStart() );

/* Binary flags for user roles */
$isAdmin     = isRole ( USR_ADMIN );
$isLecturer  = isRole ( USR_LECTURER );
$isStudent   = isRole ( USR_STUDENT );

/* Set the anonymous user role. */
$isAnonymous = false;
if (( $isAdmin == false ) && ( $isLecturer == false ) && ( $isStudent == false ))
{
	$isAnonymous = true;
	SessionDataBean::setUserRole ( USR_ANONYMOUS );
}

/* Allow editing and saving only to logged in users. */
if ( $adminNeeded )
{
	if ( $isAdmin == false )
	{
		$errorMsg .= "<p>\n";
		$errorMsg .= "Pro přístup na tuto stránku potřebujete administrátorská práva.\n";
		$errorMsg .= "Přihlašte se jako uživatel s administrátorským opravněním a zkuste to prosím znovu.<br>\n";
		$errorMsg .= "</p>\n";
		$errorMsg .= "<p><em>\n";
		$errorMsg .= "(session is '" . dumpToString ( $_SESSION ) . "')<br/>\n";
		$errorMsg .= "(isAdmin is '" . dumpToString ( $isAdmin ) . "')<br/>\n";
		$errorMsg .= "(isLecturer is '" . dumpToString ( $isLecturer ) . "')\n";
		$errorMsg .= "</em></p>\n";
		$object    = "error";
		$action    = "e_noadmn";
	}
}
else if ( $lecturerNeeded )
{
	if (( $isLecturer == false ) && ( $isAdmin == false ))
	{
		$errorMsg .= "<p>\n";
		$errorMsg .= "Pro přístup na tuto stránku potřebujete alespoň práva učitele.\n";
		$errorMsg .= "Přihlašte se jako uživatel s tímto opravněním a akci opakujte.<br>\n";
		$errorMsg .= "</p>\n";
		$errorMsg .= "<p><em>\n";
		$errorMsg .= "(session is '" . dumpToString ( $_SESSION ) . "')<br/>\n";
		$errorMsg .= "(isAdmin is '" . dumpToString ( $isAdmin ) . "')<br/>\n";
		$errorMsg .= "(isLecturer is '" . dumpToString ( $isLecturer ) . "')\n";
		$errorMsg .= "</em></p>\n";
		$object    = "error";
		$action    = "e_nolctr";
	}
}
else if ( $studentNeeded )
{
	if (( $isStudent == false ) && ( $isLecturer == false ) && ( $isAdmin == false ))
	{
		$object    = "error";
		$action    = "e_nostdt";
	}
}
else if ( $loginNeeded )
{
	if ( isLoggedIn() )
	{
		$errorMsg .= "<p>\n";
		$errorMsg .= "Pro přístup na tuto stránku musíte být přihlášeni do systému.\n";
		$errorMsg .= "Přihlašte se jako uživatel s administrátoským opravněním a zkuste to prosím znovu.<br>\n";
		$errorMsg .= "</p>\n";
		$errorMsg .= "<p><em>\n";
		$errorMsg .= "(session is '" . dumpToString ( $_SESSION ) . "')<br/>\n";
		$errorMsg .= "(isAdmin is '" . dumpToString ( $isAdmin ) . "')<br/>\n";
		$errorMsg .= "(isLecturer is '" . dumpToString ( $isLecturer ) . "')\n";
		$errorMsg .= "</em></p>\n";
		$object    = "error";
		$action    = "error";
	}
}

/* Construct a Smarty instance. Configuration has been specified in config.php. */
$smarty = new LectwebSmarty ( $config, $isPageOutput, $isAdmin );

/* Initialise database connection */
try 
{
	$smlink = $smarty->dbOpen ();
}
catch ( Exception $e )
{
	/* Make sure smlink has some value. */
	$smlink = NULL;
	/* And modify the displayed object and action. */
	$action = "exception";
	$object = "error";
	$smarty->assign ( 'exceptionMsg', $e->getMessage() );
}

/* Publish $errorMsg for the case of displaying an error header. */
$smarty->assign ( 'errormsg', $errorMsg );

/* HTML Area header and calendar header and footer shall be loaded only when
   adding or editing data. */
if ( $action == 'add' || $action == 'edit' )
{
	$smarty->assign ('htmlareaheader', "htmlarea.header.tpl");
	$smarty->assign ('calendarheader', "calendar.header.tpl");
	$smarty->assign ('calendarfooter', "calendar.footer.tpl");
}
else
{
	$smarty->assign ('htmlareaheader', "empty.tpl");
	$smarty->assign ('calendarheader', "empty.tpl");
	$smarty->assign ('calendarfooter', "empty.tpl");
}

/* -----------------------------------------------------------------------
   Main dispatcher.
   Creates an instance of an object that will handle the current action.
   Then it calls the appropriate action handler.
   ----------------------------------------------------------------------- */

$bean = NULL; 
$haveValidAction = true; // This is modified only in case of SectionBean object. */

switch ( $object )
{
	case "article":
		$bean = new ArticleBean ( $id, $smarty, $action, $object );
		break;
	case "board":
		$bean = new BoardBean ( $id, $smarty, $action, $object );
		break;
	case "error";
		$smarty->assign ( 'leftcolumn', "leftempty.tpl" );
		break;
	case "evaluation":
		$bean = new EvaluationBean ( $id, $smarty, $action, $object );
		break;
	case "evltsk":
		$bean = new EvaluationTasksBean ( $id, $smarty, $action, $object );
		break;
	case "exercise":
		$bean = new ExerciseBean ( $id, $smarty, $action, $object );
		break;
	case "exclist":
		$bean = new ExerciseListBean ( $id, $smarty, $action, $object );
		break;
    case "extension":
        $bean = new DeadlineExtensionBean ( $id, $smarty, $action, $object );
        break;
	case "file":
		$bean = new FileBean ( $id, $smarty, $action, $object );
		break;
	case "formassign":
		$bean = new FormAssignmentBean ( $id, $smarty, $action, $object );
		break;
    case "formsolution":
        $bean = new FormSolutionsBean ( $id, $smarty, $action, $object );
        break;
	case "home":
		$bean = new SectionBean ( 0, $smarty, "show", "section" );
		$haveValidAction = ( $bean->prepareLectureHomePage ( $id ) == RET_OK );
		break;
	case "lecture":
		$bean = new LectureBean ( $id, $smarty, $action, $object );
		break;
	case "import":
		$bean = new ImportBean ( $id, $smarty, $action, $object );
		break;
	case "lecturer":
		$bean = new LecturerBean ( $id, $smarty, $action, $object );
		break;
	case "login":
		$bean = new LoginBean ( $smarty, $action, $object );
		break;
	case "news":
		$bean = new NewsBean ( $id, $smarty, $action, $object );
		break;
	case "note":
		$bean = new NoteBean ( $id, $smarty, $action, $object );
		break;
	case "points":
		$bean = new PointsBean ( $id, $smarty, $action, $object );
		break;
	case "schoolyear":
		$bean = new SchoolYearBean ( $smarty, $action, $object );
		break;
	case "section":
		$bean = new SectionBean ( $id, $smarty, $action, $object );
		break;
	case "solution":
		$bean = new SolutionBean ( $id, $smarty, $action, $object );
		break;
	case "student":
		$bean = new StudentBean ( $id, $smarty, $action, $object );
		break;
	case "stuexe":
		$bean = new StudentExerciseBean ( $id, $smarty, $action, $object );
		break;
	case "stulec":
		$bean = new StudentLectureBean ( $id, $smarty, $action, $object );
		break;
	case "stupass":
		$bean = new StudentPassGenBean ( $id, $smarty, $action, $object );
		break;
    case "subtask":
        $bean = new SubtaskBean ( $id, $smarty, $action, $object );
        break;
    case "subtaskdates":
        $bean = new SubtaskDatesBean ( $id, $smarty, $action, $object );
        break;
	case "task":
		$bean = new TaskBean ( $id, $smarty, $action, $object );
		break;
    case "taskdates":
        $bean = new TaskDatesBean ( $id, $smarty, $action, $object );
        break;
	case "tsksub":
		$bean = new TaskSubtasksBean ( $id, $smarty, $action, $object );
		break;
	case "urls":
		$bean = new URLsBean ( $id, $smarty, $action, $object );
		break;
	case "user":
		$bean = new UserBean ( $id, $smarty, $action, $object );
		break;
	default:
		/* Set $errormsg ... */
		$smarty->assign ( 'errormsg', "No handler for '" . $object . "' has been set up.<br>" );
		$object = "error";
		$action = "error";
}

/* This fills in all the data needed by appropriate templates into the
   Smarty instance passed in by the constructor. */
if ( $bean )
{
	if ( $haveValidAction )
	{
		/* Call an action handler. The handler may cause an exception, in that
	   	   case we will change the action and object to generic type that allows
	       displaying an error message. */
		try {
			$ret = $bean->actionHandler ();
			$action = $bean->getAction ();
			$object = $bean->getObject ();
		}
		catch ( Exception $e )
		{
			/* Override the object and action. */
			$action = "exception";
			$object = "error";
			$smarty->assign ( 'exceptionMsg', $e->getMessage() );
			$html   = "<p>Exception occured: <tt>" . $e->getMessage() . "</tt></p>";
			logSystemError ( $html, $e->getTrace() );
		}
	}
	else
	{
		/* Non-standard action (error page, most probably) would cause the
		   action handler to complain.
		   @TODO this is probably not necessary at all, see the code for
		   section->prepareHomePage() which is the only piece that modifies
		   the action before calling actionHandler(). */  
		$action = $bean->getAction ();
		$object = $bean->getObject ();
	}
}

/* Handle admin/section/0 which occurs in case when user tries to log in after
   session timeout. */
/*if ( $id == 0 and $action == 'admin' and $object == 'section' )
{
	$action = 'show';
	$object = 'home';	
}*/

/* Publish user login - this has to be done _after_ the call to LoginBean's
   action handler as this call fills in the proper data into _SESSION in
   case of login verifiaction. */
if ( isLoggedIn() )
{
	$smarty->assign ( 'login', SessionDataBean::getUserLogin() );
	$smarty->assign ( 'uid',   SessionDataBean::getUserId() );
	
	/* And once again go through user roles. */
	$isAdmin    = isRole ( USR_ADMIN );
	$isLecturer = isRole ( USR_LECTURER );
	$isStudent  = isRole ( USR_STUDENT );
	if (( $isAdmin == false ) && ( $isLecturer == false ) && ( $isStudent == false ))
	{
		$isAnonymous = true;
		SessionDataBean::setUserRole ( USR_ANONYMOUS );
	}
	else
	{
		$isAnonymous = false;
	}
}
else
{
	$smarty->assign ( 'login', "anonymní" );
	
	/* Reset indicators for just logged-out users */
	$isAdmin = false;
	$isLecturer = false;
	$isStudent = false;
	$isAnonymous = true;
}

/* Publish user role */
$smarty->assign ( 'isAdmin',     $isAdmin     );
$smarty->assign ( 'isLecturer',  $isLecturer  );
$smarty->assign ( 'isStudent',   $isStudent   );
$smarty->assign ( 'isAnonymous', $isAnonymous );

/* Left-hand menu depends on 'action' */
switch ( $action )
{
	case 'admin' :
	case 'verify' :
	  /* In case of login / session timeout, we will have lecture.id == 0 after
	     a succesful login. Any display of administrative menu will lead to
	     complains from the ctrl.php about invalid identifier. We will therefore
	     display a drop-down selection of possible lectures. */     
    case 'edit' :
    case 'save' :
    	$leftcolumn = ( $isStudent ) ? 'leftmenu.tpl' : 'leftadmin.tpl' ;
		break;
	case 'delete' :
		/* Action "delete.login" performs user logout from the system. It is
		   pointless to show administrative menu afterwards. */
		if ( $object != 'login' )
		{
			$leftcolumn = 'leftadmin.tpl';
			break;
		}
	case 'show' :
		/* Get header items. Has to be done _after_ all modifications to
		   sections have been accomplished in order to reflect possible
		   'mtitle' changes. */
		
		/* Check that information about the last section id exists, assign
		   a reasonable default value otherwise. */
		$lastSectionId = SessionDataBean::getLastSectionId();
		if ( $lastSectionId == NULL )
		{
			/* Session timeout or whatever else. We do not have a valid
			   lecture id as well, so let us set the lecture id to default
			   as well. */ 
			$lastSectionId = 0;
			SessionDataBean::setLastSectionId ( $lastSectionId );
			SessionDataBean::setDefaultLecture();
		}

		/* Menu shows in fact a part of section hierachy. Let's construct
		   an appropriate section object first. */
		$menu = new SectionBean ( $lastSectionId, $smarty, $object, $action );

		/* Now fetch the menu items that will form the menu. They will be
		   stored as a smarty variable 'menuHierList'. The session variable
       	   holding lecture data has been initialised in the call to section->show
       	   handler. In case the session data is empty, no menu will be displayed. */
		$rootSection = SessionDataBean::getRootSection();
	    if ( $rootSection )
    	{
      		$menu->assignMenuHierarchy ( $rootSection );
		  	/* This is the template that will display menu data */
		  	$leftcolumn = 'leftmenu.tpl';
		}
		else
		{
		  $leftcolumn = 'leftmenu.tpl';
    	}
		break;
	default:
		$leftcolumn = 'leftempty.tpl';
		break;
}
$smarty->assign ( 'leftcolumn', $leftcolumn );

/* Show footer only for "normal" pages. */
$footer = ( $action != "show" ) ? "empty.tpl" : "footer.tpl" ;
$smarty->assign ('footer', $footer);

/* This will go into page main column.  */
$maincolumn = $object.".".$action.".tpl";
$smarty->assign ('maincolumn', $maincolumn);
$maincolumntitle = $object.".".$action.".tit";
$smarty->assign ('maincolumntitle', $maincolumntitle);

/* This is an approximate time of execution of this script. */
$time = sprintf ( "%7.4f", getmicrotime() - $timeStart );
$smarty->assign ( 'exectime', $time );

/* This is an approximate time of execution of this script. */
$currentTime = date ( 'Y-m-d H:i:s' );
$smarty->assign ( 'currentTime', $currentTime );

/* Publish the public host name. */
$smarty->assign ( 'HOST_NAME', HOST_NAME );

/* Detect secure HTTP. */
$smarty->assign ( 'isHTTPS', ! empty ( $_SERVER['HTTPS'] ));

/* Tell Smarty about the active lecture. */
$smarty->assign ( 'lecture', SessionDataBean::getLecture() );

/* Tell Smarty about the active school year. */
$smarty->assign ( 
	'schoolyear',
	SessionDataBean::getSchoolYear() . '/' . (SessionDataBean::getSchoolYear()+1) );

/* Display the page */
$smarty->display ('main.tpl');

/* Close the dadtabase connection */
$smarty->dbClose ($smlink);

if ( $smarty->debug )
{
  /* This is an approximate time of execution of the whole page. */
  $time = getmicrotime() - $timeStart;
  echo "<!-- Total ".$time." sec -->\n";
  echo "<!-- SMARTY \n";
  print_r ($smarty);
  echo "-->\n";
  echo "<!-- _GET\n";
  print_r ( $_GET );
  echo "-->\n";
  echo "<!-- _POST\n";
  print_r ( $_POST );
  echo "-->\n";
  echo "<!-- session (SID=".session_id().")\n";
  print_r ($_SESSION);
  echo "-->\n";
  echo "<!-- _SERVER\n";
  print_r ( $_SERVER );
  echo "-->\n";
}
?>
