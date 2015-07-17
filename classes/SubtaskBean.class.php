<?php

class SubtaskBean extends DatabaseBean
{
    var $title;
    var $ttitle;
    var $type;
    var $maxpts;
    var $position;
    var $lecture_id;

    /* Fill in reasonable defaults. */
    function _setDefaults()
    {
        $this->title = $this->rs['title'] = '';
        $this->ttitle = $this->rs['ttitle'] = '';
        $this->assignment = $this->rs['assignment'] = '';
        $this->type = $this->rs['type'] = '';
        $this->maxpts = $this->rs['maxpts'] = 0;
        $this->position = $this->rs['position'] = 0;
        $this->lecture_id = $this->rs['lecture_id'] = '';
    }

    /* Constructor */
    function SubtaskBean($id, &$smarty, $action, $object)
    {
        /* Call parent's constructor first */
        parent::__construct($id, $smarty, "subtask", $action, $object);
        /* Initialise new properties to their default values. */
        $this->_setDefaults();
    }

    function dbReplace()
    {
        DatabaseBean::dbQuery(
            "REPLACE subtask VALUES ('"
            . $this->id . "','"
            . $this->type . "','"
            . mysql_escape_string($this->title) . "','"
            . mysql_escape_string($this->ttitle) . "','"
            . mysql_escape_string($this->assignment) . "','"
            . $this->maxpts . "','"
            . $this->position . "','"
            . $this->lecture_id . "')"
        );
        /* Update the id of this record if necessary. */
        $this->updateId();
    }

    function _updateFromResultSet()
    {
        $this->type = $this->rs['type'];
        $this->title = vlnka(stripslashes($this->rs['title']));
        $this->ttitle = vlnka(stripslashes($this->rs['ttitle']));
        $this->assignment = vlnka(stripslashes($this->rs['assignment']));
        $this->maxpts = $this->rs['maxpts'];
        $this->position = $this->rs['position'];
        $this->lecture_id = $this->rs['lecture_id'];
        $this->active = $this->rs['active'];

        /* Update resultset */
        $this->rs['title'] = $this->title;
        $this->rs['ttitle'] = $this->ttitle;
        $this->rs['assignment'] = $this->assignment;
    }

    function dbQuerySingle()
    {
        /* Query the data of this section (ID has been already specified) */
        DatabaseBean::dbQuerySingle();
        /* Initialize the internal variables with the data queried from the
           database. */
        $this->_updateFromResultSet();
    }

    /* Assign POST variables to internal variables of this class and
       remove evil tags where applicable. We shall probably also remove
       evil attributes et cetera, but this will be done later if ever. */
    function processPostVars()
    {
        assignPostIfExists($this->id, $this->rs, 'id');
        assignPostIfExists($this->type, $this->rs, 'type');
        assignPostIfExists($this->title, $this->rs, 'title', true);
        assignPostIfExists($this->ttitle, $this->rs, 'ttitle', true);
        assignPostIfExists($this->assignment, $this->rs, 'assignment', true,
            "<a><b><br><i><p><ul><li><dl><dt><ol><em><strong><h2><h3><table><tbody><tr><td><th><img>");
        assignPostIfExists($this->maxpts, $this->rs, 'maxpts');
        assignPostIfExists($this->position, $this->rs, 'position');
        assignPostIfExists($this->lecture_id, $this->rs, 'lecture_id');


        /* Process 'returntoparent' directive */
        $this->processReturnToParent();

        return $this->rs;
    }

    /* Assign GET variables to internal variables of this class. This
       is intended for pre-setup purposes (supplying parent id and
       section type for new sections that will be edited afterwards).
       Only a subset of internal variables can be updated this way,
       hopefully none of them may open a security hole. */
    function processGetVars()
    {
        assignGetIfExists($this->lecture_id, $this->rs, 'lecture_id');
        assignGetIfExists($this->type, $this->rs, 'type');

        /* Process 'returntoparent' directive */
        $this->processReturnToParent();

        return $this->rs;
    }

    function _getFullList($where = '')
    {
        $rs = DatabaseBean::dbQuery(
            "SELECT * FROM subtask" . $where . " " .
            "ORDER BY position,type");

        foreach ($rs as $key => $val) {
            $rs[$key]['title'] = vlnka(stripslashes($val['title']));
            $rs[$key]['ttitle'] = vlnka(stripslashes($val['ttitle']));
        }

        return $rs;
    }

    function getSubtaskCode($subtaskId)
    {
        $rs = $this->dbQuery(
            "SELECT ttitle FROM subtask WHERE id='" . $subtaskId . "'");
        return strtolower($rs[0]['ttitle']);
    }

    function getSubtaskType($subtaskId)
    {
        $rs = $this->dbQuery(
            "SELECT type FROM subtask WHERE id='" . $subtaskId . "'");
        return $rs[0]['type'];
    }

    function getForLecture($lectureId, $taskTypeList)
    {
        $dbList = arrayToDBString($taskTypeList, false);
        $rs = $this->_getFullList(
            ' WHERE type IN (' . $dbList . ')' .
            ' AND lecture_id=' . $lectureId . ' '
        );
        return $rs;
    }

    function assignForLecture($lectureId, $taskTypeList)
    {
        $rs = $this->getForLecture($lectureId, $taskTypeList);
        $this->_smarty->assign('subtaskList', $rs);
        return $rs;
    }

    /* Fetch a complete information for given subtask ids. */
    function getFullSubtaskList($subtaskList)
    {
        $dbList = arrayToDBString($subtaskList);
        $rs = $this->_getFullList(" WHERE id IN (" . $dbList . ")");
        return $rs;
    }

    function assignFullSubtaskList($subtaskList)
    {
        $fullSubtaskList = SubtaskBean::getFullSubtaskList($subtaskList);
        $this->_smarty->assign('subtaskList', $fullSubtaskList);
        return $fullSubtaskList;
    }

    /**
     * Returns `true` if the type of subtask does not require individual
     * assignments.
     */
    static function noAssignmentIdRequired($sType)
    {
        switch ($sType) {
            case TT_LECTURE_PDF:
            case TT_SEMESTRAL:
                return true;
        }

        return false;
    }

    /* Fetch a complete list of subtask that will have weekly or semestral
     assignments to be submitted. This list contains therefore only selected
     subtask types and it is extended with information about activity or
     inactivity of every item.
       @TODO@ add support for lectures. */
    function getStudentSubtaskList($lectureId, $studentId)
    {
        $rs = $this->dbQuery(
            "SELECT id,type,title,ttitle,maxpts,datefrom," .
            "IF(sd.dateto<se.dateto,se.dateto,sd.dateto) AS dateto," .
            "(sd.datefrom<=NOW() AND " .
            "(sd.dateto>NOW() OR se.dateto>NOW())) AS active " .
            "FROM subtask AS su " .
            "LEFT JOIN extension AS se " .
            "ON ( se.student_id=" . $studentId . " AND su.id=se.subtask_id ) " .
            "LEFT JOIN subtaskdates AS sd " .
            "ON ( su.id=sd.subtask_id AND sd.year=" . $this->schoolyear . " ) " .
            "LEFT JOIN tsksub AS ts " .
            "ON ( su.id=ts.subtask_id ) " .
            "WHERE type IN (" .
            TT_WEEKLY_FORM . "," .
            TT_WEEKLY_SIMU . "," .
            TT_WEEKLY_TF . "," .
            TT_WEEKLY_ZIP . "," .
            TT_WEEKLY_PDF . "," .
            TT_LECTURE_PDF . "," .
            TT_SEMESTRAL . ") " .
            "AND lecture_id=" . $lectureId . " " .
            "AND ts.year=" . $this->schoolyear . " " .
            "ORDER BY position,title");

        if (!empty ($rs)) {
            foreach ($rs as $key => $val) {
                $rs[$key]['title'] = vlnka(stripslashes($val['title']));
                $rs[$key]['ttitle'] = vlnka(stripslashes($val['ttitle']));
            }
        }

        return $rs;
    }

    /**
     * Assign a complete list of subtasks for current term to Smarty variable
     * 'studentSubtaskList'. In case that $studentId has not been specified,
     * make a generic list of all subtask regardles of deadline extensions
     * that might apply for particular students. */
    function assignStudentSubtaskList($lectureId = 0, $studentId = 0)
    {
        if ($lectureId <= 0) $lectureId = SessionDataBean::getLectureId();
        $list = $this->getStudentSubtaskList($lectureId, $studentId);
        $this->_smarty->assign('studentSubtaskList', $list);
        return $list;
    }

    /**
     * Assign full list of subtasks related to the given lecture.
     */
    function assignFull($lectureId = 0)
    {
        $where = $this->_lectureIdToWhereClause($lectureId);
        $rs = $this->_getFullList($where);
        if (!empty ($rs)) {
            /* Query the subtask date info. The `datetimes` returned by the
               call to `getSubtaskDates()` is an array indexed by subtask
               ids, where every entry holds activity deadlines for that
               particular subtask in the given school year. */
            $sdBean = new SubtaskDatesBean ($lectureId, $this->_smarty, NULL, NULL);
            $datetimes = $sdBean->getSubtaskDates($rs, $this->schoolyear);
            /* Get the list of the task types. */
            $ttypes = $this->_getTaskTypes();
            /* Loop over all subtasks and update data what will
               be displayed. */
            foreach ($rs as $key => $val) {
                /* Remember the subtask type. */
                $type = $val['type'];
                /* Store the textual description of the taks type. */
                $rs[$key]['typestr'] = $ttypes [$type];
                /* If the subtask type does not denote a subtask that
                   has activity deadlines, replace the date string with
                   value that indicates this. */
                if ($type != TT_WEEKLY_FORM &&
                    $type != TT_WEEKLY_SIMU &&
                    $type != TT_WEEKLY_ZIP &&
                    $type != TT_WEEKLY_PDF &&
                    $type != TT_WEEKLY_TF &&
                    $type != TT_LECTURE_PDF &&
                    $type != TT_SEMESTRAL
                ) {
                    $rs[$key]['datefrom'] = '-';
                    $rs[$key]['dateto'] = '-';
                } else {
                    /* Remember the subtask id, we may need it more than once. */
                    $id = $val['id'];
                    /* Now we have to consult the mapping given by
                       SubtaskDates. If the mapping is empty, we will not add
                       anything, otherwise we will fill in the datetime
                       values. */
                    if (array_key_exists($id, $datetimes)) {
                        $rs[$key]['datefrom'] = $datetimes[$id]['datefrom'];
                        $rs[$key]['dateto'] = $datetimes[$id]['dateto'];
                    }
                }
            }
        }

        $this->_smarty->assign('subtaskList', $rs);
        return $rs;
    }

    function assignSingle($newId = NULL)
    {
        /* Change the id of the bean, if requested. */
        if ($newId != NULL) {
            $this->id = $newId;
        }

        /* If id == 0, we shall create a new record. */
        if ($this->id) {
            /* Get the id of the student so that we can additionally
               request information about subtask extension. */
            $studentId = SessionDataBean::getUserId();
            /* Query data of this subtask. */
            $this->rs = $this->dbQuery(
                "SELECT *," .
                "(sd.datefrom<=NOW() AND " .
                "(sd.dateto>NOW() OR se.dateto>NOW())) AS active " .
                "FROM subtask AS su " .
                "LEFT JOIN extension AS se " .
                "ON ( se.student_id=" . $studentId . " AND su.id=se.subtask_id ) " .
                "LEFT JOIN subtaskdates AS sd " .
                "ON ( su.id=sd.subtask_id AND sd.year=" . $this->schoolyear . " ) " .
                "WHERE su.id=" . $this->id);

            $this->rs = $this->rs[0];
            $this->_updateFromResultSet();

            if ($this->type == TT_WEEKLY_FORM || $this->type == TT_WEEKLY_TF) {
                $this->rs['isformassignment'] = true;
            } else if ($this->type == TT_WEEKLY_SIMU) {
                $this->rs['issimuassignment'] = true;
            } else if ($this->type == TT_WEEKLY_PDF) {
                $this->rs['ispdfassignment'] = true;
            } else if ($this->type == TT_LECTURE_PDF) {
                $this->rs['islpdfassignment'] = true;
            } else if ($this->type == TT_WEEKLY_ZIP) {
                $this->rs['iszipassignment'] = true;
            }

            //$this->dumpVar ( 'rs1', $this->rs );

            /* Get a lecture that this subtask is related to. */
            $lectureBean = new LectureBean (
                $this->lecture_id, $this->_smarty, NULL, NULL);
            $lectureBean->assignSingle();
        } else {
            /* Initialize default values. */
            $this->_setDefaults();
            /* Have a look at HTTP GET parameters if there is some
               additional information we could use ( lecture id or
               task type). */
            $this->processGetVars();
        }

        $this->_smarty->assign('subtask', $this->rs);
    }

    /* -------------------------------------------------------------------
       HANDLER: SHOW
       ------------------------------------------------------------------- */
    function doShow()
    {
        /* Get the subtask data. */
        $this->assignSingle();
        /* Get the assignment information. */
        $assignmentBean = new AssignmentsBean ($this->id, $this->_smarty, "", "");
        $assignmentBean->assignSingle();
    }

    /* -------------------------------------------------------------------
       HANDLER: ADMIN
       ------------------------------------------------------------------- */
    function doAdmin()
    {
        /* Get the list of all subtasks for the given lecture id. */
        $this->assignFull($this->id);
        /* Get a lecture that this subtask is related to. */
        $lectureBean = new LectureBean ($this->id, $this->_smarty, "", "");
        $lectureBean->assignSingle();
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
        /* Both above functions set $this->rs to values that shall be
           displayed. By assigning $this->rs to Smarty variable 'user'
           we can fill the values of $this->rs into a template. */
        $this->assignSingle();

        /* Publish a list of task types. */
        $this->assignTaskTypeSelect();

        /* Get a list of lectures. */
        $lectureBean = new LectureBean (0, $this->_smarty, "", "");
        $lectureBean->assignSelectMap();
    }

    /* -------------------------------------------------------------------
       HANDLER: SAVE
       ------------------------------------------------------------------- */
    function doSave()
    {
        /* Assign POST variables to internal variables of this class and
           remove evil tags where applicable. */
        $this->processPostVars();
        /* Update the record, but do not update the subtask dates. */
        $this->dbReplace();

        /* Finally fetch the data of the subtask so that it can be
           displayed as a confirmation. */
        $this->assignSingle();
    }

    /* -------------------------------------------------------------------
       HANDLER: DELETE
       ------------------------------------------------------------------- */
    function doDelete()
    {
        $this->assignSingle();

        /* Get a lecture that this subtask is related to. */
        $lectureBean = new LectureBean ($this->id, $this->_smarty, "", "");
        $lectureBean->assignSingle();
    }

    /* -------------------------------------------------------------------
       HANDLER: REAL DELETE
       ------------------------------------------------------------------- */
    function doRealDelete()
    {
        $this->assignSingle();
        /* Delete the record */
        DatabaseBean::dbDeleteById();

        /* Get a lecture that this subtask is related to. */
        $lectureBean = new LectureBean ($this->id, $this->_smarty, "", "");
        $lectureBean->assignSingle();
    }
}

?>
