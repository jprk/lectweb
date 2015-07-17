<?php
/*
 * Created on 25.3.2010
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

/* Global configuration */
require ( 'config.php' );

//require ( REQUIRE_DIR . 'tools.php');
require ( REQUIRE_DIR . 'LectwebSmarty.class.php');
require ( REQUIRE_DIR . 'BaseBean.class.php');
require ( REQUIRE_DIR . 'DatabaseBean.class.php');

//require ( REQUIRE_DIR . 'ArticleBean.class.php');
require ( REQUIRE_DIR . 'AssignmentsBean.class.php');
//require ( REQUIRE_DIR . 'DeadlineExtensionBean.class.php');
//require ( REQUIRE_DIR . 'EvaluationBean.class.php');
//require ( REQUIRE_DIR . 'EvaluationTasksBean.class.php');
//require ( REQUIRE_DIR . 'ExerciseBean.class.php');
//require ( REQUIRE_DIR . 'exerciseListBean.class.php');
//require ( REQUIRE_DIR . 'FileBean.class.php');
require ( REQUIRE_DIR . 'FormAssignmentBean.class.php');
require ( REQUIRE_DIR . 'FormSolutionsBean.class.php');
//require ( REQUIRE_DIR . 'LoginBean.class.php');
//require ( REQUIRE_DIR . 'MenuBean.class.php');
//require ( REQUIRE_DIR . 'NewsBean.class.php');
//require ( REQUIRE_DIR . 'NoteBean.class.php');
require ( REQUIRE_DIR . 'PointsBean.class.php');
require ( REQUIRE_DIR . 'LectureBean.class.php');
//require ( REQUIRE_DIR . 'LecturerBean.class.php');
//require ( REQUIRE_DIR . 'SectionBean.class.php');
require ( REQUIRE_DIR . 'SessionDataBean.class.php');
require ( REQUIRE_DIR . 'SolutionBean.class.php');
//require ( REQUIRE_DIR . 'StudentBean.class.php');
//require ( REQUIRE_DIR . 'StudentExerciseBean.class.php');
//require ( REQUIRE_DIR . 'StudentLectureBean.class.php');
//require ( REQUIRE_DIR . 'StudentPassGenBean.class.php');
require ( REQUIRE_DIR . 'SubtaskBean.class.php');
//require ( REQUIRE_DIR . 'SubtaskDatesBean.class.php');
//require ( REQUIRE_DIR . 'TaskBean.class.php');
//require ( REQUIRE_DIR . 'TaskSubtasksBean.class.php');
//require ( REQUIRE_DIR . 'URLsBean.class.php');
require ( REQUIRE_DIR . 'UserBean.class.php');

	header ("Content-Type: text/html; charset=utf-8");

	/* Fetch / initialize session */
    session_start ();
    
    /* Initialise session defaults in case that the session data
       storage does not contain the variables we would need later. */
    SessionDataBean::conditionalInit ( schoolYearStart() );
    
    /* Binary flags for user roles */
    $isAllowed  = isRole ( USR_ADMIN ) || isRole ( USR_LECTURER );

    /* Construct a Smarty instance. Configuration has been specified
       in config.php. */
    $smarty = new LectwebSmarty ( $config, true );

    /* Initialise database connection */
    $smlink = $smarty->dbOpen ();

    /* Subtask id. Some subtask have maximum match point 6, some have 7. */
    $subtaskId = 79;
    $maxSubtaskPoints = 6;
    $schoolYear = 2011;
    
    /* Get data of this subtask. */
    $subtaskBean = new SubtaskBean ( $subtaskId, $smarty, NULL, NULL );
    $subtaskBean->assignSingle ();
    $subtaskBean->dumpThis();
        
    /* Query the list of all assignment solutions. */
    $assignmentsBean = new AssignmentsBean ( NULL, $smarty, NULL, NULL );
    $assignmentList = $assignmentsBean->getAssignmentList ( $subtaskId );
	  $assignmentsBean->dumpVar ( 'assignment list', $assignmentList );

    /* Initialise some instances of objects that will be needed later. */
    $fsBean = new FormSolutionsBean  ( $subtaskId, $smarty, NULL, NULL );
    $faBean = new FormAssignmentBean ( $subtaskId, $smarty, NULL, NULL );
    $pointsBean = new PointsBean ( NULL, $smarty, NULL, NULL );
    
    
    /* Re-evaluate every solution in the list. */
    foreach ( $assignmentList as $ar )
    {
        /* Extract assignment and student id. */
        $assignmentId = $ar['assignmnt_id'];
        $studentId    = $ar['student_id'];
        echo "student id $studentId<br/>\n";

        /* Get solutions submitted for all parts of the given
           assignment by this student in this school year. */
        $solutionData = $fsBean->getSolutionData ( $studentId, $assignmentId );
        $subtaskBean->dumpVar (
            'solution data for assignment ' . $assignmentId,
            $solutionData );
        
        if ( empty ( $solutionData )) continue;
        
        /* Evaluate the answer.
           The value of `$match` will be from 0 to 6. */
        $match = 0;
        foreach ( $solutionData as $key => $val )
        {
            $a = returnDefault ( $val['a'], 0 );
            $b = returnDefault ( $val['b'], 0 );
            $c = returnDefault ( $val['c'], 0 );
            $d = returnDefault ( $val['d'], 0 );
            $e = returnDefault ( $val['e'], 0 );
            $f = returnDefault ( $val['f'], 0 );
            $g = returnDefault ( $val['g'], NULL );
            
        	$match += $faBean->matchSolution ( 
                $assignmentId, $val['part'], $subtaskBean->type,
                $a, $b, $c, $d, $e, $f, $g
                );
            $subtaskBean->dumpVar ( 'key', $key );
            $subtaskBean->dumpVar ( 'val', $val );
            $subtaskBean->dumpVar ( 'valDefault', array ( $a, $b, $c, $d, $e, $f, $g ));
            $subtaskBean->dumpVar ( 'match', $match );
        }
        
        /* Match percentage from 0.0 to 1.0. */
        $fmatch = floatval ( $match ) / ( count ( $solutionData ) * $maxSubtaskPoints );
        $subtaskBean->dumpVar ( 'fmatch', $fmatch );
            
        /* Compute the number of points corresponding to this match
           percentage. Do not round, the number of points has one decimal
           place, it will be rounded by SQL when written to database. */
        $points = $fmatch * $subtaskBean->maxpts ;
        echo "points $points <br>\n";
          
        /* And store the points. The `dbReplace()` funciton expects to find an
           array of subtask results indexed by student ids in `points` variable.
           We will have to create it for this single student. */
        /* TODO: Implement this as a method of PointsBean. */
        if ( TRUE )
        {
            $pointsBean->points = array ( 
                $studentId => array ( $subtaskId => $points )
                );
            $pointsBean->setSchoolYear ( $schoolYear );
            $pointsBean->dbReplace();
        }
        else
        {
            $pts = $pointsBean->getPoints (
                array ( $studentId ),
                array ( $subtaskId ),
                schoolYearStart()
                );
            echo "existing points " . $pts[$studentId][$subtaskId]['points'] . "<br>\n";
        }
    }
    
    $smarty->dbClose( $smlink );
?>
