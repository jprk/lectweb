<?php

class StudentLectureBean extends DatabaseBean
{
    const E_INIT_FAILED = -1;
    const E_NO_TASKS = -2;
    const E_NO_SUBTASKS = -3;

    var $studentList; // List of student ids of this lecture
    var $resType; // Type of evaluation listing

    function _setDefaults()
    {
        $this->studentList = array();
        $this->resType = SB_STUDENT_ANY; // Defined in StudentBean.class.php
    }

    /* Constructor */
    function __construct($id, &$smarty, $action, $object)
    {
        /* Call parent's constructor first */
        parent::__construct($id, $smarty, "stud_lec", $action, $object);
        /* Initialise new properties to their default values. */
        $this->_setDefaults();
    }

    function dbAppend()
    {
        /* Loop over all student ids and store them into database. */
        foreach ($this->studentList as $val) {
            /* Assign every student id from relation to this lecture. */
            DatabaseBean::dbQuery(
                "REPLACE stud_lec VALUES ("
                . $val . ","
                . $this->id . ","
                . $this->year . ")"
            );
        }
    }

    function dbReplace()
    {
        /* Delete all entries for the lecture and the year. */
        DatabaseBean::dbQuery(
            "DELETE FROM stud_lec WHERE lecture_id="
            . $this->id . " AND year=" . $this->year);
        /* And append the data into the cleared table. */
        $this->dbAppend();
    }

    /**
     * Append (possibly replace) list of students to the lecture.
     */
    function setStudentList( $stlist, $replace = false )
    {
        $this->studentList = $stlist;
        if ($replace) {
            $this->dbReplace();
        } else {
            $this->dbAppend();
        }
    }

    /* Assign POST variables to internal variables of this class and
       remove evil tags where applicable. We shall probably also remove
       evil attributes et cetera, but this will be done later if ever. */
    function processPostVars()
    {
        $this->relation = $_POST['sl_rel'];
    }

    function processGetVars()
    {
        assignGetIfExists ( $this->resType, $this->rs, 'restype' );
    }

    function getStudentListForLecture()
    {
        $rs = DatabaseBean::dbQuery(
            "SELECT student_id FROM stud_lec WHERE" .
            " lecture_id=" . $this->id . " AND" .
            " year=" . $this->schoolyear);
        // $this->dumpVar ( 'rs',  $rs );

        $studentList = array();
        if (isset ($rs)) {
            foreach ($rs as $key => $val) {
                $studentList[] = $val['student_id'];
            }
        }
        $this->dumpVar('studentList', $studentList);

        return $studentList;
    }

    /**
     * Verify that a student studies given lecture.
     */
    function studentIsListed($studentId, $lectureId, $year)
    {
        $rs = DatabaseBean::dbQuery(
            "SELECT student_id FROM stud_lec " .
            "WHERE student_id=" . $studentId . " " .
            "AND lecture_id=" . $lectureId . " " .
            "AND year=" . $year);

        return (count($rs) > 0);
    }

    /**
     * Prepare the list of students and their points for the lecture.
     * This is very similar to prepareexerciseData() in ExerciseBean.
     */
    function prepareStudentLectureData($sortType = SB_SORT_BY_NAME)
    {
        /* Get the list of students for this exercise. The list will contain
           only student IDs. */
        $studentList = $this->getStudentListForLecture();

        /* Fetch the evaluation scheme from the database.
           @TODO@ Allow for more than one scheme.
           @TODO@ Allow for more than one lecture !!!!! */
        $evalBean = new EvaluationBean (0, $this->_smarty, "x", "x");

        /* This will initialise EvaluationBean with the most recent evaluation
           scheme for lecture given by $this->lecture_id. The function returns
           'true' if the bean has been initialised. */
        $ret = $evalBean->initialiseFor($this->id, $this->schoolyear);
        /* Check the initialisation status. */
        if (!$ret) {
            echo "<!-- EvaluationBean: initialisation failed -->\n";
            /* Nope, the id references a nonexistent evaluation scheme. */
            return self::E_INIT_FAILED;
        }

        /* Get the list of tasks for evaluation of this exercise. The list will
           contain only task IDs and we will have to fetch task and subtask
           information by ourselves later. */
        $taskList = $evalBean->getTaskList();

        /* Fetch a verbose list of tasks. */
        $taskBean = new TaskBean (0, $this->_smarty, "x", "x");

        /* This will both create a full list of tasks corresponding to the
           evaluation scheme and assing this list to the Smarty variable
           'taskList'. */
        $fullTaskList = $taskBean->assignFullTaskList($taskList);

        /* Fetch a verbose list of subtasks. */
        $subtaskBean = new SubtaskBean (0, $this->_smarty, "x", "x");
        /* This will both create a full list of subtasks corresponding to the
           tasks of the chosen evaluation scheme and assign this list to the
           Smarty variable 'subtaskList'. */
        $tsBean = new TaskSubtasksBean (0, $this->_smarty, '', '');
        $subtaskMap = $tsBean->getSubtaskMapForTaskList($taskList, $evalBean->getEvalYear());
        $this->dumpVar('subtaskMap', $subtaskMap);
        if (empty ($subtaskMap)) {
            return self::E_NO_SUBTASKS;
        }
        $subtaskList = $tsBean->getSubtaskListFromSubtaskMap($subtaskMap);
        $this->dumpVar('subtaskList', $subtaskList);

        $subtaskBean = new SubtaskBean (0, $this->_smarty, "x", "x");
        $fullSubtaskList = $subtaskBean->assignFullSubtaskList($subtaskList);

        /* If there are any students in $studentList, get their points. */
        if (count($studentList) > 0) {
            $pointsBean = new PointsBean (0, $this->_smarty, '', '');
            $points = $pointsBean->getPoints($studentList, $subtaskList, $this->schoolyear);

            /* Generate a verbose list of students based on
               the ID list we got above. Combine this list with the points students
               achieved. */
            $studentBean = new StudentBean (0, $this->_smarty, "x", "x");
            $studentBean->assignStudentDataFromList(
                $studentList, $points, $evalBean, $subtaskMap,
                $fullSubtaskList, $fullTaskList,
                $this->resType, $sortType,
                $this->id);
        }

        return 0;
    }

    /* -------------------------------------------------------------------
       HANDLER: SHOW
       ------------------------------------------------------------------- */
    function doShow()
    {
        /* Update internal parameters from data sent by GET method. */
        $this->processGetVars();

        /* Get lecture data */
        $lectureBean = new LectureBean ($this->id, $this->_smarty, "x", "x");
        $lectureBean->assignSingle();

        switch ($this->prepareStudentLectureData()) {
            case self::E_INIT_FAILED:
                $this->action = 'e_init';
                break;
            case self::E_NO_SUBTASKS:
                $this->action = 'e_subtasks';
                break;
        }
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
        /* Get the lecture description, just to fill in some more-or-less
           usefull peieces of information. */
        $lectureBean = new LectureBean ($this->id, $this->_smarty, "x", "x");
        $lectureBean->assignSingle();
    }

    /* -------------------------------------------------------------------
       HANDLER: DELETE
       ------------------------------------------------------------------- */
    function doDelete()
    {
        /* Just fetch the data of the user to be deleted and ask for
           confirmation. */
        $this->dbQuerySingle();
        $this->_smarty->assign('user', $this->rs);
        /* Left column contains administrative menu */
        $this->_smarty->assign('leftcolumn', "leftadmin.tpl");
    }

    /* -------------------------------------------------------------------
       HANDLER: REAL DELETE
       ------------------------------------------------------------------- */
    function doRealDelete()
    {
        /* Delete the record */
        DatabaseBean::dbDeleteById();
        /* Deleting a section can occur only in admin mode. Now that we
           have deleted the data, we shall return to the admin view by
           calling the appropriate action handler. */
        $this->doAdmin();
    }

    /* -------------------------------------------------------------------
       HANDLER: ADMIN
       ------------------------------------------------------------------- */
    function doAdmin()
    {
        /* Get the list of all exercises, assign it to the Smarty variable
           'exerciseList' and return it to us as well, we will need it later.
           $this->id will point to the lecture_id in this case. */
        $exerciseBean = new ExerciseBean ( NULL, $this->_smarty, NULL, NULL);
        $exerciseList = $exerciseBean->assignFull($this->id);

        /* Get the lecture description, just to fill in some more-or-less
           usefull peieces of information. */
        $lectureBean = new LectureBean ($this->id, $this->_smarty, NULL, NULL);
        $lectureBean->assignSingle();

        /* Now create an array that contains student id as an key and _index_ to
           the $exerciseList as a value (that is, not the exercise ID, but the
           true index into the array. */
        $studexcBean = new StudentExerciseBean (0, $this->_smarty, NULL, NULL);
        $exerciseBinding = $studexcBean->getExerciseBinding($exerciseList);

        /* Get the list of all students. Additionally, create a field 'selected'
           that contains text ' selected' on the position of the exercise that
           the particular student visits, and '' otherwise. */
        $studentBean = new StudentBean (0, $this->_smarty, "x", "x");
        $studentBean->assignStudentListWithExercises($this->id, count($exerciseList), $exerciseBinding);

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
    }
}

?>
