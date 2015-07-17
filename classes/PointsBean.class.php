<?php

class PointsBean extends DatabaseBean
{
    const PTS_NOT_CLASSIFIED = -99;
    const PTS_IS_COPY = -98;
    const PTS_IS_EXCUSED = -97;

    var $points;
    var $comments;
    var $type;

    /* Static project resource identifier for ftok().
       Has to be a single character. */
    static private $editId = 'e';

    /* Constructor */
    function __construct($id, &$smarty, $action, $object)
    {
        /* Call parent's constructor first */
        parent::__construct($id, $smarty, "points", $action, $object);
    }

    /**
     * Replace points record for the given student and subtask.
     */
    function replacePoints(
        $studentId, $subtaskId, $schoolYear, $numPts, $comment,
        $origPts, $origComment, $convert = true
    )
    {
        /* If requested, convert the contents of $numPts into numeric form. */
        if ($convert) {
            /* Transform decimal comma to decimal dot */
            $numPts = str_replace(',', '.', $numPts);

            /* Transform '-' to PTS_NOT_CLASSIFIED */
            if ($numPts == '-') {
                /* The student was not classified and has no points assigned. */
                $numPts = self::PTS_NOT_CLASSIFIED;
            } /* Transform 'c' to PTS_IS_COPY */
            else if ($numPts == 'c' || $numPts == 'opis') {
                /* The student copied her or his work from someone else. */
                $numPts = self::PTS_IS_COPY;
            } /* Transform 'c' to PTS_IS_COPY */
            else if ($numPts == 'x' || $numPts == 'omluva') {
                /* The student copied her or his work from someone else. */
                $numPts = self::PTS_IS_EXCUSED;
            }

            /* Replace an empty comment with an empty string. */
            if (empty ($comment)) {
                $comment = '';
            }
        }

        /* Now check whether there has been some change with respect to
           the original data. If not, do not replace anything. */
        if ($numPts != $origPts || $comment != $origComment) {
            self::dumpVar('numPts', $numPts);
            self::dumpVar('origPts', $origPts);
            self::dumpVar('comment', $comment);
            self::dumpVar('origComment', $origComment);

            /* There has been some change in points or comment, or we want to
               create a new point record for a new student or a new subtask.
               Prepare an SQL string that will become an argument to the
             VALUES() clause. */
            $sqlData =
                $studentId . ","
                . $subtaskId . ","
                . $this->schoolyear . ",'"
                . $numPts . "','"
                . SessionDataBean::getUserId() . "','"
                . mysql_escape_string($comment) . "',NULL";

            /* Delete all point records for this student and subtask and
               school year. */
            DatabaseBean::dbQuery(
                "DELETE FROM points WHERE student_id=" .
                $studentId . " AND subtask_id=" . $subtaskId .
                " AND year=" . $this->schoolyear);

            /* Store the record. */
            DatabaseBean::dbQuery(
                "REPLACE points VALUES (" . $sqlData . ")"
            );

            /* In case of record update store the backup record so that we have
               a history. If $origPTS is NULL and $origComment is NULL as well,
               we are initialising the record (see `updatePoints()`). */
            if ($origPts != NULL || $origComment != NULL) {
                DatabaseBean::dbQuery(
                    "INSERT INTO points_bak VALUES ( 0," . $sqlData . ")");
            }
        }
    }

    function dbReplace()
    {
        /* Check that the object data were initialised properly with a valid
           schoolyear. */
        if ($this->schoolyear < 0) {
            throw new Exception ("Initialise schoolyear before calling this.");
        }

        /* Get student ids and subtask ids. The `points` array is indexed by
           studentIds, and every element contains a vector of points indexed
           by subtask ids. */
        $studentIds = array_keys($this->points);
        $subtaskIds = array_keys(current($this->points));
        $studentIdList = arrayToDBString($studentIds);
        $subtaskIdList = arrayToDBString($subtaskIds);

        /* Query the original points. */
        $rs = DatabaseBean::dbQuery(
            "SELECT * FROM points " .
            "WHERE student_id IN (" . $studentIdList . ") " .
            "AND subtask_id IN (" . $subtaskIdList . ") " .
            "AND year=" . $this->schoolyear);
        //$this->dumpVar ( 'student points', $rs );

        /* Convert the points and comments into arrays indexed by student id
           and subtask id. Missing values will be passed as empty to
           `replacePoints()`. */
        foreach ($rs as $val) {
            $studentId = $val['student_id'];
            $subtaskId = $val['subtask_id'];
            $origPts[$studentId][$subtaskId] = $val['points'];
            $origComment[$studentId][$subtaskId] = $val['comment'];
        }
        $this->dumpVar('original points', $origPts);

        foreach ($this->points as $studentId => $subtasks) {
            /* Update all point records with the submitted data. */
            foreach ($subtasks as $subtaskId => $numPts) {
                /* Transform decimal comma to decimal dot */
                $numPts = str_replace(',', '.', $numPts);

                /* Transform '-' to PTS_NOT_CLASSIFIED */
                if ($numPts == '-') {
                    /* The student was not classified and has no points assigned. */
                    $numPts = self::PTS_NOT_CLASSIFIED;
                } /* Transform 'c' to PTS_IS_COPY */
                else if ($numPts == 'c' || $numPts == 'opis') {
                    /* The student copied her or his work from someone else. */
                    $numPts = self::PTS_IS_COPY;
                } /* Transform 'x' to PTS_IS_EXCUSED */
                else if ($numPts == 'x' || $numPts == 'omluva') {
                    /* The student copied her or his work from someone else. */
                    $numPts = self::PTS_IS_EXCUSED;
                }
                /* Get the comment that has been submitted. */
                $comment = $this->comments[$studentId][$subtaskId];

                /* Check that we will not query an undefined offset in `origPts`
                   and `origComment` arrays. */
                $origPtsVal = NULL;
                if (array_key_exists($studentId, $origPts)) {
                    $studentPts = $origPts[$studentId];
                    if (array_key_exists($subtaskId, $studentPts)) {
                        $origPtsVal = $studentPts[$subtaskId];
                    }
                }
                $origCmtVal = NULL;
                if (array_key_exists($studentId, $origComment)) {
                    $studentCmt = $origComment[$studentId];
                    if (array_key_exists($subtaskId, $studentCmt)) {
                        $origCmtVal = $studentCmt[$subtaskId];
                    }
                }

                /* Conditionally replace the points if there was some change
                   in the point value or comment. */
                $this->replacePoints(
                    $studentId, $subtaskId, $this->schoolyear, $numPts,
                    $this->comments[$studentId][$subtaskId],
                    $origPtsVal, $origCmtVal, true
                );
            }
        }

        // echo "<!-- replace ok -->\n";
    }

    function dbQuerySingle()
    {
    }

    function updatePoints(
        $studentId, $subtaskId, $schoolYear, $numPts, $comment,
        $convert = true
    )
    {
        /* Determine the original points of the student to detect a possible
           change. */
        $rs = DatabaseBean::dbQuery(
            "SELECT * FROM points " .
            "WHERE student_id=" . $studentId . " " .
            "AND subtask_id=" . $subtaskId . " " .
            "AND year=" . $this->schoolyear);
        $this->dumpVar('student points', $rs);
        /* Resultset may be empty. In that case we have to pass an empty
           variable as a parameter to `replacePoints()`. */
        if (empty ($rs)) {
            $origPoints = NULL;
            $origComment = NULL;
        } else {
            $origPoints = $rs[0]['points'];
            $origComment = $rs[0]['comment'];
        }

        /* Use the result of the above query to find the number of points and
           possible previous comments and perform a conditional update of the
           points record. */
        $this->replacePoints(
            $studentId, $subtaskId, $this->schoolyear, $numPts, $comment,
            $origPoints, $origComment, $convert);

    }

    /* Assign POST variables to internal variables of this class and
       remove evil tags where applicable. We shall probably also remove
       evil attributes et cetera, but this will be done later if ever. */
    function processPostVars()
    {
        $this->points = $_POST['points'];
        $this->comments = $_POST['comments'];
        // $this->dumpVar ( 'points POST', $this->points );

        assignPostIfExists($this->type, $this->rs, 'type');
    }

    /**
     * Assign the GET components of the quesry to internal variables.
     */
    function processGetVars()
    {
        assignGetIfExists($this->type, $this->rs, 'type');
        assignGetIfExists($this->order, $this->order, 'order');
    }

    /**
     * Set the school year used when storing points.
     */
    function setSchoolYear($newSchoolYear)
    {
        $this->schoolyear = $newSchoolYear;
    }

    function getPoints($studentList, $subtaskList, $year = 0)
    {
        // $this->dumpVar ( 'studentList', $studentList );
        $this->dumpVar('subtaskList', $subtaskList);

        /* If the year has not been specified (or it has been but its value is <=0),
              the following SELECT will work on the complete `points` table.
              Otherwise, only point records for the given year will be returned. */
        $yearWhere = ($year <= 0) ? "" : " AND year=" . $year;

        $dbStudentList = arrayToDBString($studentList, false);
        $dbSubtaskList = arrayToDBString($subtaskList, false);

        $rs = DatabaseBean::dbQuery(
            "SELECT student_id, subtask_id, points, comment FROM points WHERE "
            . "student_id IN (" . $dbStudentList . ") AND "
            . "subtask_id IN (" . $dbSubtaskList . ")" . $yearWhere
        );

        $this->dumpVar('getPoints:points_rs', $rs);

        /* Initialise point list. */
        $ptlist = array();
        $ptelem = array('points' => '-', 'comment' => '');
        foreach ($studentList as $st) {
            foreach ($subtaskList as $su) {
                $ptlist[$st][$su] = $ptelem;
            }
        }

        /* Transfer the resultset into the pre-allocated array. */
        if (isset ($rs)) {
            foreach ($rs as $key => $val) {
                /* Output will be indexed by student_id */
                $st = $val['student_id'];
                $su = $val['subtask_id'];

                /* Get the point count from this subtask. */
                $fPts = $val['points'];

                /* Get the comment for this subtask. */
                $tCom = $val['comment'];

                /* Take care of students that were not classified from
                   this subtask yet. When '-' is converted to integer,
                   it will become '0'. */
                if ($fPts == self::PTS_NOT_CLASSIFIED) {
                    $tPts = '-';
                } else if ($fPts == self::PTS_IS_COPY) {
                    $tPts = 'opis';
                } else if ($fPts == self::PTS_IS_EXCUSED) {
                    $tPts = 'omluva';
                } else {
                    /* Do not display the part beyond the decimal point if
                       it is zero. */
                    $iPts = (integer)$fPts;
                    $tPts = ($fPts - $iPts == 0) ? $iPts : $fPts;
                }
                $ptlist[$st][$su]['points'] = $tPts;
                $ptlist[$st][$su]['comment'] = $tCom;
            }
        }

        $this->dumpVar('getPoints:ptlist', $ptlist);
        return $ptlist;
    }

    function addPointsToSubtaskList($studentId, $subtaskIdList, $subtaskList)
    {
        /* Get the points and comments from the database for the given subtasks.
           The result is indexed by the values of `$studentId` and in this case
           contains just a single value `$ptlist[$studentId]`.*/
        $ptlist = $this->getPoints(
            array($studentId),
            $subtaskIdList,
            SessionDataBean::getSchoolYear()
        );
        /* Get rid of the index. */
        $ptlist = $ptlist[$studentId];
        $this->dumpVar('addPointsToSubtaskList:ptlist', $ptlist);

        /* Convert the points to a map in the form subtask_id => points */
        $pmap = array();
        if (isset ($subtaskList)) {
            foreach ($subtaskList as $key => $val) {
                /* Fetch the point record for this subtask. The record contains
                   the number of points and maybe some comment to the evaluation
                   as well. */
                $subtaskId = $val['id'];
                if (array_key_exists($subtaskId, $ptlist)) {
                    $tsp = $ptlist[$subtaskId]['points'];
                    $tsc = $ptlist[$subtaskId]['comment'];
                } else {
                    /* Undefined record. */
                    $tsp = NULL;
                    $tsc = NULL;
                }

                /* TODO: Hack. Better to setup up PTS_NOT_CLASSIFIED for the student
                      and all subtasks when initialising the student.
                      Not classified means that the recod is completely empty. */
                if ((!isset ($tsp)) || ($tsp == self::PTS_NOT_CLASSIFIED)) {
                    $tsp = '-';
                }

                /* Store the fetched points and comment to subtaskList. */
                $subtaskList[$key]['pts'] = $tsp;
                $subtaskList[$key]['com'] = $tsc;
            }
        }

        return $subtaskList;
    }

    /**
     * Assign points of the given student and subtask.
     */
    function assignStudentSubtask($studentId, $subtaskId)
    {
        /* Query the database for points of the given student.
           Query the points assigned to the student and subtask in the
           currently active school year. */
        $ptlist = $this->getPoints(
            array($studentId),
            array($subtaskId),
            SessionDataBean::getSchoolYear());
        /* The returned array should contain just a single record indexed
           by the student id. This record contains just a single subtask
           evaluation, indexed by the subtask id. */
        $points = $ptlist[$studentId][$subtaskId];
        /* Publish the value. */
        $this->_smarty->assign('points', $points);
    }

    /* -------------------------------------------------------------------
       HANDLER: EDIT
       ------------------------------------------------------------------- */
    function doEdit()
    {
        /* Initialise variables that may be changed in mutexLock(). */
        $lockTime = -1;
        $lockLogin = '';

        /* Lock access to points.
           It is necessary to limit the number of users changing the point
           table to one - otherwise one lecturer could easily overwrite
           points edited at the same time by some other lecturer. */
        $res = mutexLock($this, self::$editId, $lockTime, $lockLogin);

        /* Assign lock time and lock user. */
        $this->_smarty->assign('locktime', $lockTime);
        $this->_smarty->assign('locklogin', $lockLogin);

        /* Call to mutexLock() may return several return codes and we have
           to react to all of them. */
        switch ($res) {
            case MUTEX_LOCK_STOLEN_OK;
                /* Stealing a stale lock is perfecty okay. On the other
                   hand we would better let the user know that someone
                   has started editing the data and did not save them for
                   more than 30 minutes. */
                $this->_smarty->assign('lockstolen', true);
            case MUTEX_OK:
                /* Pass to the next stage. */
                break;
            case MUTEX_E_ISLOCKED:
                /* Point table is locked. Refuse to edit. */
                $this->action = 'e_islocked';
                return;
            case MUTEX_E_FTOK:
                /* Could not construct a valid semaphore id. */
                $this->action = 'e_ftok';
                return;
            case MUTEX_E_CANTACQUIRE:
                /* Could not acquire the semaphore used to block acces to the
                   mutex file. */
                $this->action = 'e_cantacquire';
                return;
            default:
                $this->action = 'e_mutexval';
                return;
        }

        /* Determine the type of listing that shall be displayed and its sort type. */
        $this->processGetVars();
        $this->_smarty->assign('order', $this->order);

        /* Prepare the listing based on the type of lisitng that has
           been passed as a GET parameter. */
        switch ($this->type) {
            case 'exc':
                /* Prepare all the variables that are needed for filling in the
    	       	   edit template. These variables are equal to the set needed
    		       for showing the list of students for a single exercise. */
                $exerciseBean = new ExerciseBean ($this->id, $this->_smarty, "x", "x");
                $exerciseBean->dbQuerySingle();
                $exerciseBean->prepareexerciseData($this->order);
                break;

            case 'lec':
                $slBean = new StudentLectureBean ($this->id, $this->_smarty, "x", "x");
                /* Prepare the student list and points for this lecture. The function
                   returns 0 on success, or nonzero error status. */
                $ret = $slBean->prepareStudentLectureData($this->order);
                if ($ret) {
                    /* Identifier of the lecture is invalid. */
                    $this->action = 'e_inval';
                    return;
                }
                break;

            default:
                /* No type of output specified. Complain and unlock. */
                $this->action = 'e_action';
                mutexUnlock($this, self::$editId);
                return;
        }

        /* Modify template name. */
        $this->object = $this->object . '.' . $this->type;
    }

    /* -------------------------------------------------------------------
       HANDLER: SAVE
       ------------------------------------------------------------------- */
    function doSave()
    {
        /* Assign POST variables to internal variables of this class and
           remove evil tags where applicable. */
        $this->processPostVars();

        /* Update all the records. */
        $this->dbReplace();

        if ($this->type == 'exc') {
            /* Fetch information about the exercise. */
            $exerciseBean = new ExerciseBean ($this->id, $this->_smarty, "x", "x");
            $exerciseBean->dbQuerySingle();
            $this->_smarty->assign('exercise', $exerciseBean->rs);
        }

        /* Unlock the access to points. */
        $res = mutexUnlock($this, self::$editId);

        /* Call to mutexUnlock() may return several return codes and we have
           to react to all of them. */
        switch ($res) {
            case MUTEX_OK:
                /* Pass to the next stage. */
                break;
            case MUTEX_E_FTOK:
                /* Could not construct a valid semaphore id. */
                $this->action = 'e_ftok';
                return;
            case MUTEX_E_CANTACQUIRE:
                /* Could not acquire the semaphore used to block acces to the
                   mutex file. */
                $this->action = 'e_cantacquire';
                return;
            default:
                $this->action = 'e_mutexval';
                return;
        }

        /* Modify template name. */
        $this->object = $this->object . '.' . $this->type;
    }

}

?>
