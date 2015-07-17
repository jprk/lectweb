<?php

/* This class implements mapping from subtask solutions to files.
   Every solution holds three references: to particular student
   who submitted it, to the subtask it solves and to file which
   contains the solution. */

class SolutionBean extends DatabaseBean
{
    const SOL_ORDER_BY_LOGIN = 1;
    const SOL_ORDER_BY_NAME = 2;
    const SOL_ORDER_BY_SUBMIT = 3;

    var $subtaskId;
    var $studentId;
    var $fileId;
    var $lecturerId;
    var $timeStamp;
    var $doZip = false;
    var $order = self::SOL_ORDER_BY_LOGIN;

    function _setDefaults()
    {
        $this->subtask_id = $this->rs['subtask_id'] = 0;
        $this->student_id = $this->rs['student_id'] = 0;
        $this->file_id = $this->rs['file_id'] = 0;
        $this->lecturer_id = $this->rs['lecturer_id'] = 0;
        $this->doZip = $this->rs['dozip'] = false;
    }

    /* Constructor */
    function __construct($id, &$smarty, $action, $object)
    {
        /* Call parent's constructor first */
        parent::__construct($id, $smarty, "solution", $action, $object);
        /* Initialise new properties to their default values. */
        $this->_setDefaults();
    }

    function dbReplace()
    {
        DatabaseBean::dbQuery(
            "REPLACE solution VALUES ("
            . $this->id . ","
            . $this->subtask_id . ","
            . $this->student_id . ","
            . $this->file_id . ","
            . $this->lecturer_id . ","
            . "NULL)"
        );
        /* Fetch the correct id for new records */
        $this->updateId();
    }

    function dbQuerySingle()
    {
        /* Query the data of this section (ID has been already specified) */
        DatabaseBean::dbQuerySingle();
        /* Initialize the internal variables with the data queried from the
           database. */
        $this->subtask_id = $this->rs['subtask_id'];
        $this->student_id = $this->rs['student_id'];
        $this->file_id = $this->rs['file_id'];
        $this->lecturer_id = $this->rs['lecturer_id'];
    }

    /* Assign POST variables to internal variables of this class and
       remove evil tags where applicable. We shall probably also remove
       evil attributes et cetera, but this will be done later if ever. */
    function processPostVars()
    {
        assignPostIfExists($this->subtaskId, $this->rs, 'subtask_id');
        assignPostIfExists($this->studentId, $this->rs, 'student_id');
        assignPostIfExists($this->schoolyear, $this->rs, 'schoolyear');
        assignPostIfExists($this->points, $this->rs, 'points');
        assignPostIfExists($this->comment, $this->rs, 'comment');
        assignPostIfExists($this->order, $this->rs, 'order');
    }


    /* Assign GET variables to internal variables of this class and
       remove evil tags where applicable. We shall probably also remove
       evil attributes et cetera, but this will be done later if ever. */
    function processGetVars()
    {
        assignGetIfExists($this->dozip, $this->rs, 'downloadzip');
        assignGetIfExists($this->order, $this->rs, 'order');
    }


    /* Assign a full list of solution records. */
    function assignAdminList()
    {
        /* List all subtasks of the current lecture that are candidates for
           manual evaluation (that is, they are of type TT_WEEKLY_SIMU,
           TT_WEEKLY_PDF, TT_LECTURE_PDF, TT_WEEKLY_ZIP or TT_SEMESTRAL) and
           count the number of submitted solutions and the number of solutions
           that have been already evaluated.
           @TODO@ The list of subtask is probably a subset of the one that is
           used when listing active/inactive subtasks for students.
           */
        $ta = array(
            TT_WEEKLY_SIMU,
            TT_WEEKLY_PDF,
            TT_WEEKLY_ZIP,
            TT_LECTURE_PDF,
            TT_SEMESTRAL);
        $tLst = arrayToDBString($ta);
        $lId = SessionDataBean::getLectureId();
        /* TODO: Very awkward hack to display only solutions submitted this year
         * via `fi.fname LIKE '/<year>/'. The same is used below.
         * 2012/02/14 jprk corrected the join so that also subtask with zero
         * submitted solutions are listed */
        $rs = DatabaseBean::dbQuery(
            'SELECT ' .
            'su.id, su.title, ' .
            'COUNT(fi.id) AS submitted, ' .
            'COUNT(IF(pt.points>' . PointsBean::PTS_NOT_CLASSIFIED . ',1,NULL)) AS corrected ' .
            'FROM subtask     AS su ' .
            'LEFT JOIN points AS pt ON pt.subtask_id=su.id ' .
            'LEFT JOIN file   AS fi ON (fi.objid=su.id AND fi.type=' . FT_X_SOLUTION . ' AND fi.fname LIKE \'%/' . SessionDataBean::getSchoolYear() . '/%\' AND fi.uid=pt.student_id) ' .
            'WHERE su.lecture_id=' . $lId . ' ' .
            'AND su.type IN (' . $tLst . ') ' .
            'AND pt.year=' . SessionDataBean::getSchoolYear() . ' ' .
            'AND fi.id IS NOT NULL ' .
            'GROUP BY su.id');

        /* Thanks to LEFT JOIN clause we will get everything at one, the
           unassigned lecturers and missing points being NULL. */
        /*$rs = DatabaseBean::dbQuery (
          'SELECT so.*, su.title, su.maxpts, ' .
                 'IF(po.points<>NULL,po.points,\'-\') AS pts, ' .
                 'TO_DAYS(su.dateto)-to_days(so.lastmodified) AS lead, ' .
                 'CONCAT(st.surname,\' \',st.firstname) AS student_name, ' .
                 'IF(le.surname<>NULL,CONCAT(le.surname,\' \',le.firstname),\'-\') AS lecturer_name ' .
          'FROM solution AS so LEFT JOIN subtask AS su ON so.subtask_id=su.id ' .
          'LEFT JOIN student AS st ON so.student_id=st.id ' .
          'LEFT JOIN user AS le ON so.lecturer_id=le.id ' .
          'LEFT JOIN points AS po ON (so.subtask_id=po.subtask_id AND so.student_id=po.student_id) ' .
          'ORDER BY su.title, student_name, so.lastmodified' );*/

        $this->_smarty->assign('solutionList', $rs);
        return $rs;
    }

    /**
     * Update $this->rs with a list of all submitted solutions to a subtask.
     *
     * The subtask is given by the value of $this->id, the solution is a file
     * with type FT_X_SOLUTION and proper objid.
     *
     */
    function assignFull()
    {
        /* Check how we shall order the output. */
        if ($this->order == self::SOL_ORDER_BY_NAME) {
            $orderStr = "st.surname,st.firstname";
        } elseif ($this->order == self::SOL_ORDER_BY_SUBMIT) {
            $orderStr = "fi.timestamp";
        } else {
            $orderStr = "st.login";
        }

        /* TODO: Very awkward hack to display only solutions submitted this year
         * via `fi.fname LIKE '/<year>/'. The same is used above.
         * 2012/02/13 jprk Changed back to the list without responsible lecturers.
         *                 Lecturers are added in the next round. */
        $this->rs = DatabaseBean::dbQuery(
            'SELECT ' .
            'fi.uid, fi.id, fi.timestamp, ' .
            'st.login, st.surname, st.firstname, st.yearno, st.groupno, ' .
            'CASE pt.points ' .
            '  WHEN NULL THEN \'-\' ' .
            '  WHEN ' . PointsBean::PTS_NOT_CLASSIFIED . ' THEN \'-\' ' .
            '  WHEN ' . PointsBean::PTS_IS_COPY . ' THEN \'opis\' ' .
            '  ELSE pt.points END AS pts, ' .
            'pt.comment, pt.user_id, ' .
            'us.login AS ulogin, us.email AS uemail, ' .
            'us.firstname AS ufirst, us.surname AS ulast ' .
            'FROM      file      AS fi ' .
            'LEFT JOIN student   AS st ON ( st.id = fi.uid ) ' .
            'LEFT JOIN points    AS pt ON ( pt.subtask_id = fi.objid AND fi.uid = pt.student_id ) ' .
            'LEFT JOIN user      AS us ON ( us.id = pt.user_id ) ' .
            'WHERE fi.type=' . FT_X_SOLUTION . ' ' .
            'AND fi.objid=' . $this->id . ' ' .
            'AND fi.fname LIKE \'%/' . SessionDataBean::getSchoolYear() . '/%\' ' .
            'AND pt.year=' . SessionDataBean::getSchoolYear() . ' ' .
            'ORDER BY ' . $orderStr
        );

        /* The resultset above contains in `uid` entry all ids of students that
         * have been listed. If any of these students attends a laboratory or
         * practical exercise, we shall list the particular tutor of that
         * seminar as a person that corrects that particular solution. */
        $studentIdList = array2ToDBString($this->rs, 'uid');
        $lecturers = DatabaseBean::dbQuery(
            'SELECT st.id AS uid, le.* ' .
            'FROM      student   AS st ' .
            'LEFT JOIN exercise AS ex ON ( ex.lecture_id=' . SessionDataBean::getLectureId() . ' AND ex.year=' . SessionDataBean::getSchoolYear() . ' ) ' .
            'LEFT JOIN stud_exc  AS se ON ( st.id=se.student_id  AND se.exercise_id=ex.id ) ' .
            'LEFT JOIN lecturer  AS le ON ( ex.lecturer_id=le.id AND se.exercise_id=ex.id ) ' .
            'WHERE st.id IN (' . $studentIdList . ') ' .
            'AND (( ex.id IS NULL AND le.id IS NULL ) OR ( ex.id IS NOT NULL AND le.id IS NOT NULL ))',
            'uid'
        );
        $this->dumpVar('lecturers', $lecturers);

        /* Delete us.* entries from the fetched data in case that the `pts`
         * entry does indicate that the points have not been awarded yet.
         * This is necessary as sometimes PTS_NOT_CLASSIFIED is deliberately
         * stored in the `points` table by some user. */
        foreach ($this->rs as $key => $val) {
            if ($val['pts'] == '-') {
                $this->rs[$key]['ulogin'] = NULL;
                $this->rs[$key]['uemail'] = NULL;
                $this->rs[$key]['ufirst'] = NULL;
                $this->rs[$key]['ulast'] = NULL;
            }

            /* Update the information about the responsible lecturer. */
            $uid = $val['uid'];
            if (array_key_exists($uid, $lecturers)) {
                $lecturer = $lecturers[$uid];
                if (isset($lecturer['id'])) {
                    $this->rs[$key]['efirst'] = $lecturer['firstname'];
                    $this->rs[$key]['elast'] = $lecturer['surname'];
                } else {
                    /* No exercises exist for this lecture. */
                    $this->rs[$key]['efirst'] = NULL;
                    $this->rs[$key]['elast'] = '-';
                }
            } else {
                /* exercises exist, but this student did not register for any
                 * of them. */
                $this->rs[$key]['efirst'] = NULL;
                $this->rs[$key]['elast'] = '?';
            }
        }

        $this->_smarty->assign('solutionFileList', $this->rs);
        $this->_smarty->assign('order', $this->order);
        return $this->rs;
    }

    /* Assign a single url record. */
    function assignSingle()
    {
        /* If id == 0, we shall create a new record. */
        if ($this->id) {
            /* Query data of this person. */
            $this->dbQuerySingle();
        } else {
            /* Initialize default values. */
            $this->_setDefaults();
        }
        $this->_smarty->assign('solution', $this->rs);
    }

    /* -------------------------------------------------------------------
       HANDLER: ADMIN
       ------------------------------------------------------------------- */
    function doShow()
    {
        /* Check if there has been some additional paramerter in the GET
           request (dozip,order). */
        $this->processGetVars();
        /* Get the list of all submitted solutions for the given subtask
           as $this->rs and assign it to Smarty variable `solutionList`. */
        $this->assignFull();
        /* Fetch the information about the subtask so that we can write
           something into the header. */
        $subtaskBean = new SubtaskBean ($this->id, $this->_smarty, '', '');
        $subtaskBean->assignSingle();
    }

    /* -------------------------------------------------------------------
       HANDLER: ADMIN
       ------------------------------------------------------------------- */
    function doAdmin()
    {
        /* Get the list of all solution entries for this lecture. */
        $this->assignAdminList();
        /* It could have been that doAdmin() has been called from another
           handler. Change the action to "admin" so that ctrl.php will
           know that it shall display the scriptlet for section.admin */
        $this->action = "admin";
    }

    /* -------------------------------------------------------------------
       HANDLER: EDIT
       ------------------------------------------------------------------- */
    function doEdit()
    {
        /* We shall set points for a submitted file. The file id has been
           passed to us as $this->id, we have to fetch the whole file record
           first. */
        $fileBean = new FileBean ($this->id, $this->_smarty, '', '');
        $fileBean->dbQuerySingle();
        $this->_smarty->assign('files', $fileBean->rs);
        $rs = $fileBean->dbQueryAssignmentFile($fileBean->objid, $fileBean->uid);
        $this->_smarty->assign('filea', $rs);
        /* We also have to fetch the points in case the submission is being
           re-evaulated. */
        $pointsBean = new PointsBean (0, $this->_smarty, '', '');
        $pointsBean->assignStudentSubtask($fileBean->uid, $fileBean->objid);
        /* And we also want to display some details of the student. */
        $studentBean = new StudentBean ($fileBean->uid, $this->_smarty, '', '');
        $studentBean->assignSingle();
        /* And also display some details about the subtask. */
        $subtaskBean = new SubtaskBean ($fileBean->objid, $this->_smarty, '', '');
        $subtaskBean->assignSingle();
        /* Keep information about ordering. */
        $this->processGetVars();
        $this->_smarty->assign('order', $this->order);
    }

    /* -------------------------------------------------------------------
       HANDLER: SAVE
       ------------------------------------------------------------------- */
    function doSave()
    {
        /* Proces the post variables. */
        $this->processPostVars();
        /* Update the points. */
        $pointsBean = new PointsBean (0, $this->_smarty, '', '');
        $pointsBean->updatePoints(
            $this->studentId,
            $this->subtaskId,
            SessionDataBean::getSchoolYear(),
            $this->points,
            $this->comment);
        /* And we also want to display some details of the student. */
        $studentBean = new StudentBean ($this->studentId, $this->_smarty, '', '');
        $studentBean->assignSingle();
        /* And also display some details about the subtask. */
        $subtaskBean = new SubtaskBean ($this->subtaskId, $this->_smarty, '', '');
        $subtaskBean->assignSingle();
        /* And we need also information about the file. */
        $fileBean = new FileBean ($this->id, $this->_smarty, '', '');
        $fileBean->assignSingle();
        /* Keep information about ordering. */
        $this->_smarty->assign('order', $this->order);
    }

    /* -------------------------------------------------------------------
       HANDLER: DELETE
       ------------------------------------------------------------------- */
    function doDelete()
    {
        /* Just fetch the news record to be deleted from the database so
           that we can display some information about it and ask for
           confirmation. */
        $this->assignSingle();
    }

    /* -------------------------------------------------------------------
       HANDLER: REAL DELETE
       ------------------------------------------------------------------- */
    function doRealDelete()
    {
        /* Fetch the news record to be deleted from the database before
           we actually delete it so that we can display something. */
        $this->assignSingle();

        /* Delete the record */
        DatabaseBean::dbDeleteById();
    }
}

?>
