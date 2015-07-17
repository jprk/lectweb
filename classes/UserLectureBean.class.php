<?php

class UserLectureBean extends DatabaseBean
{
    const E_INIT_FAILED = -1;
    const E_NO_TASKS = -2;
    const E_NO_SUBTASKS = -3;

    var $lecturerList; // List of student ids of this lecture
    var $resType; // Type of evaluation listing

    function _setDefaults()
    {
        $this->$lecturerList = array();
        $this->resType = SB_STUDENT_ANY; // Defined in StudentBean.class.php
    }

    /* Constructor */
    function __construct($id, &$smarty, $action, $object)
    {
        /* Call parent's constructor first */
        parent::__construct($id, $smarty, "user_lec", $action, $object);
        /* Initialise new properties to their default values. */
        $this->_setDefaults();
    }

    function dbAppend()
    {
        /* Loop over all user ids and store them into database. */
        foreach ($this->lecturerList as $val) {
            /* Assign every user id from relation to this lecture. */
            DatabaseBean::dbQuery(
                "REPLACE user_lec VALUES ("
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
            "DELETE FROM user_lec WHERE lecture_id="
            . $this->id . " AND year=" . $this->year);
        /* And append the data into the cleared table. */
        $this->dbAppend();
    }

    /**
     * Append (possibly replace) list of lecturers to the lecture.
     */
    function setLecturerList($lclist, $replace = false)
    {
        $this->lecturerList = $lclist;
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
        $this->relation = $_POST['ul_rel'];
    }

    function processGetVars()
    {
        assignGetIfExists(&$this->resType, &$this->rs, 'restype');
    }

    function getLecturerListForLecture()
    {
        $rs = DatabaseBean::dbQuery(
            "SELECT lecturer_id FROM user_lec WHERE" .
            " lecture_id=" . $this->id . " AND" .
            " year=" . $this->schoolyear);
        // $this->dumpVar ( 'rs',  $rs );

        $lecturerList = array();
        if (isset ($rs)) {
            foreach ($rs as $key => $val) {
                $lecturerList[] = $val['lecturer_id'];
            }
        }
        $this->dumpVar('lecturerList', $lecturerList);

        return $lecturerList;
    }

    /**
     * Verify that a lecturer lectures the given lecture.
     */
    function lecturerIsListed($lecturerId, $lectureId, $year)
    {
        $rs = DatabaseBean::dbQuery(
            "SELECT lecturer_id FROM user_lec " .
            "WHERE lecturer_id=" . $lecturerId . " " .
            "AND lecture_id=" . $lectureId . " " .
            "AND year=" . $year);

        return (count($rs) > 0);
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
        DatabaseBean::dbDeleteById('lecturer_id');
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
        $exerciseBean = new ExerciseBean (0, $this->_smarty, "x", "x");
        $exerciseList = $exerciseBean->assignFull($this->id);

        /* Get the lecture description, just to fill in some more-or-less
           usefull peieces of information. */
        $lectureBean = new LectureBean ($this->id, $this->_smarty, "x", "x");
        $lectureBean->assignSingle();

        /* Now create an array that contains student id as an key and _index_ to
           the $exerciseList as a value (that is, not the exercise ID, but the
           true index into the array. */
        $exerciseBinding = $this->getexerciseBinding($exerciseList);

        /* Get the list of all students. Additionally, create a field 'selected'
           that contains text ' selected' on the position of the exercise that
           the particular student visits, and '' otherwise. */
        $studentBean = new StudentBean (0, $this->_smarty, "x", "x");
        $studentBean->assignStudentListWithExercises(count($exerciseList), $exerciseBinding);

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
