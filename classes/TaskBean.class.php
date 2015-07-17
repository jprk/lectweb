<?php

class TaskBean extends DatabaseBean
{
    const NULL_TASK_ID = 0;

    /* Task types */
    const TT_WEEKLY_FORM = 100;
    const TT_WEEKLY_SIMU = 101;
    const TT_WEEKLY_ZIP = 102;
    const TT_WEEKLY_PDF = 103;
    const TT_WEEKLY_TF = 104;
    const TT_LECTURE_PDF = 105;
    const TT_SEMESTRAL = 200;
    const TT_ACTIVITY = 300;
    const TT_WRITTEN = 400;

    var $type;
    var $title;
    var $minpts;
    var $position;
    var $lectureId;

    /* Fill in reasonable defaults. */
    function _setDefaults()
    {
        $this->type = $this->rs['type'] = 0;
        $this->title = $this->rs['title'] = '';
        $this->minpts = $this->rs['minpts'] = 0;
        $this->position = $this->rs['position'] = 0;
        $this->lecture_id = $this->rs['lecture_id'] = 0;
    }

    static function getTaskTypes()
    {
        return array(
            self::NULL_TASK_ID => "Vyberte ze seznamu ...",
            self::TT_ACTIVITY => "Aktivita na cvičeních",
            self::TT_WRITTEN => "Písemný test",
            self::TT_WEEKLY_FORM => "Individuální týdenní úloha (vyplňovací formulář A-F)",
            self::TT_WEEKLY_SIMU => "Individuální týdenní úloha (Simulink *.mdl + *.pdf)",
            self::TT_WEEKLY_ZIP => "Individuální týdenní úloha (kód jako *.zip + *.pdf)",
            self::TT_WEEKLY_PDF => "Individuální týdenní úloha (jeden soubor *.pdf)",
            self::TT_LECTURE_PDF => "Hromadná týdenní úloha (jeden soubor *.pdf)",
            self::TT_WEEKLY_TF => "Povinná týdenní úloha (formulář s přenosovou fcí)",
            self::TT_SEMESTRAL => "Semestrální úloha"
        );
    }

    /* Constructor */
    function __construct($id, &$smarty, $action, $object)
    {
        /* Call parent's constructor first */
        parent::__construct($id, $smarty, "task", $action, $object);
        /* Initialise new properties to their default values. */
        $this->_setDefaults();
    }

    function dbReplace()
    {
        DatabaseBean::dbQuery(
            "REPLACE task VALUES ("
            . $this->id . ","
            . $this->type . ",'"
            . mysql_escape_string($this->title) . "',"
            . $this->minpts . ","
            . $this->position . ","
            . $this->lecture_id . ")"
        );
        /* Update the id of this record if necessary. */
        $this->updateId();
    }

    function dbQuerySingle()
    {
        /* Query the data of this section (ID has been already specified) */
        DatabaseBean::dbQuerySingle();
        /* Initialize the internal variables with the data queried from the
           database. */
        $this->type = $this->rs['type'];
        $this->title = vlnka(stripslashes($this->rs['title']));
        $this->minpts = $this->rs['minpts'];
        $this->position = $this->rs['position'];
        $this->lecture_id = $this->rs['lecture_id'];
        /* Update resultset */
        $this->rs['title'] = $this->title;
    }

    /* Assign POST variables to internal variables of this class and
       remove evil tags where applicable. We shall probably also remove
       evil attributes et cetera, but this will be done later if ever. */
    function processPostVars()
    {
        $this->id = $_POST['id'];
        $this->type = (integer)$_POST['type'];
        $this->title = trimStrip($_POST['title']);
        $this->minpts = (integer)$_POST['minpts'];
        $this->position = trimStrip($_POST['position']);
        $this->lecture_id = (integer)$_POST['lecture_id'];
    }

    function _dbQueryFullList($where)
    {
        return DatabaseBean::dbQuery(
            "SELECT * FROM task" . $where . " ORDER BY position,title"
        );
    }

    function _getFullList($where = '')
    {
        $rs = $this->_dbQueryFullList($where);
        if (isset ($rs)) {
            foreach ($rs as $key => $val) {
                $rs[$key]['title'] = vlnka(stripslashes($val['title']));
            }
        }
        return $rs;
    }

    /* Get a sequence of ids representing either all records in the evaluation
       table, or (if $lectureId > 0) only those evlaution recipes that are
       bound to certain lecture id.
       @TODO@ the same piece as in EvaluationBean. */
    function getIdSequence($lectureId = 0)
    {
        $where = $this->_lectureIdToWhereClause($lectureId);
        $rs = $this->_dbQueryFullList($where);
        $seq = array();
        if (isset ($rs)) {
            foreach ($rs as $key => $val) {
                $seq[] = $val['id'];
            }
        }
        return $seq;
    }

    static function assignTypeSelect()
    {
        $this->_smarty->assign('taskTypeSelect', self::getTaskTypes());
    }

    function assignFullTaskList($taskList)
    {
        $dbList = arrayToDBString($taskList);

        $this->dumpVar('taskList', $taskList);
        $this->dumpVar('dbList', $dbList);

        $rs = DatabaseBean::dbQuery(
            "SELECT * FROM task WHERE id IN ("
            . $dbList
            . ") ORDER BY position,title");

        if (isset ($rs)) {
            foreach ($rs as $key => $val) {
                $rs[$key]['title'] = vlnka(stripslashes($val['title']));
            }
        }

        $this->dumpVar('fullTaskList', $rs);
        $this->_smarty->assign('taskList', $rs);
        return $rs;
    }

    /* Add an empty task to a sequence of tasks. Used for assignments
       of unused subtasks. */
    function addNullTask(&$taskList)
    {
        $task = $this->rs;
        $task['id'] = 0;
        $task['title'] = 'Nepoužité dílčí úlohy';
        $taskList[] = $task;

        return $taskList;
    }

    /* Assign a full list of task records. */
    function assignFull($lectureId = 0)
    {
        $where = $this->_lectureIdToWhereClause($lectureId);
        $rs = $this->_getFullList($where);
        if (!empty ($rs)) {
            /* Query the task date info. The `datetimes` returned by the
               call to `getTaskDates()` is an array indexed by task
               ids, where every entry holds activity deadlines for that
               particular task in the given school year. */
            $tdBean = new TaskDatesBean ($lectureId, $this->_smarty, NULL, NULL);
            $datetimes = $tdBean->getTaskDates($rs, $this->schoolyear);
            /* Get the list of the task types. */
            $ttypes = $this->_getTaskTypes();
            /* Loop over all tasks and update data what will be displayed. */
            foreach ($rs as $key => $val) {
                /* Remember the task type. */
                $type = $val['type'];
                /* Store the textual description of the task type. */
                $rs[$key]['typestr'] = $ttypes [$type];
                /* If the task type does not denote a task that has activity
                   deadlines, replace the date string with value that indicates
                   this. */
                if ($type != TT_WEEKLY_FORM &&
                    $type != TT_WEEKLY_SIMU &&
                    $type != TT_WEEKLY_ZIP &&
                    $type != TT_WEEKLY_PDF &&
                    $type != TT_WEEKLY_TF &&
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

        $this->_smarty->assign('taskList', $rs);
        return $rs;
    }

    /* Assign single task record. */
    function assignSingle()
    {
        /* If id == 0, we shall create a new record. */
        if ($this->id) {
            /* Query data of this person. */
            $this->dbQuerySingle();

            /* Get a lecture that this subtask is related to. */
            $lectureBean = new LectureBean ($this->lecture_id, $this->_smarty, "", "");
            $lectureBean->assignSingle();
        } else {
            /* Initialize default values. */
            $this->_setDefaults();
        }
        $this->_smarty->assign('task', $this->rs);
    }

    /* -------------------------------------------------------------------
       HANDLER: ADMIN
       ------------------------------------------------------------------- */
    function doAdmin()
    {
        /* Get the list of all tasks for the exercises of this lecture. */
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
        /* Update the record, but do not update the password. */
        $this->dbReplace();

        /* Just fetch the data of the user to be deleted and ask for
           confirmation. */
        $this->assignSingle();
    }

    /* -------------------------------------------------------------------
       HANDLER: DELETE
       ------------------------------------------------------------------- */
    function doDelete()
    {
        $this->assignSingle();
    }

    /* -------------------------------------------------------------------
       HANDLER: REAL DELETE
       ------------------------------------------------------------------- */
    function doRealDelete()
    {
        $this->assignSingle();
        /* Delete the record */
        DatabaseBean::dbDeleteById();
    }
}

?>
