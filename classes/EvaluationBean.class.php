<?php

class EvaluationBean extends DatabaseBean
{
    var $title;
    var $lecture_id;
    var $eval_year; // the same year is stored for tasks and subtasks
    var $do_grades; // is it just lab or de we give out any grades?
    var $pts_A; // minimum score for ECTS A
    var $pts_B; // minimum score for ECTS B
    var $pts_C;
    var $pts_D;
    var $pts_E; // minimum score for ECTS E (also minimum score to pass)

    function _setDefaults()
    {
        $this->title = $this->rs['title'] = '';
        $this->lecture_id = $this->rs['lecture_id'] = 0;
        $this->do_grades = $this->rs['do_grades'] = 0;
        $this->pts_A = $this->rs['pts_A'] = 0;
        $this->pts_B = $this->rs['pts_B'] = 0;
        $this->pts_C = $this->rs['pts_C'] = 0;
        $this->pts_D = $this->rs['pts_D'] = 0;
        $this->pts_E = $this->rs['pts_E'] = 0;
        /* Update information stored in $this->rs */
        $this->_update_rs();
    }

    /* Constructor */
    function __construct($id, &$smarty, $action, $object)
    {
        /* Call parent's constructor first */
        parent::__construct($id, $smarty, "evaluation", $action, $object);
        /* And initialise new object properties. */
        $this->_setDefaults();
    }

    function dbReplace()
    {
        DatabaseBean::dbQuery(
            "REPLACE evaluation VALUES ("
            . $this->id . ",'"
            . mysql_escape_string($this->title) . "','"
            . $this->year . "','"
            . $this->lecture_id . "','"
            . $this->do_grades . "','"
            . $this->pts_A . "','"
            . $this->pts_B . "','"
            . $this->pts_C . "','"
            . $this->pts_D . "','"
            . $this->pts_E . "')"
        );
        /* New records have initial 'id' equal to zero and the proper value is
           set by the database engine. We have to retrieve the 'id' back so that
           we can later update this record if needed. */
        if (!$this->id) {
            $this->id = mysql_insert_id();
        }
    }

    function _initFromResultSet()
    {
        $this->title = vlnka(stripslashes($this->rs['title']));
        $this->eval_year = $this->rs['year'];
        $this->lecture_id = $this->rs['lecture_id'];
        $this->do_grades = $this->rs['do_grades'];
        $this->pts_A = $this->rs['pts_A'];
        $this->pts_B = $this->rs['pts_B'];
        $this->pts_C = $this->rs['pts_C'];
        $this->pts_D = $this->rs['pts_D'];
        $this->pts_E = $this->rs['pts_E'];
        /* Update resultset */
        $this->rs['title'] = $this->title;
        /* Extend the year to schoolyear */
        $this->rs['schoolyearstr'] = $this->eval_year . "/" . ($this->eval_year + 1);
    }

    function dbQuerySingle()
    {
        /* Query the data of this section (ID has been already specified) */
        DatabaseBean::dbQuerySingle();
        /* Initialize the internal variables with the data queried from the
           database. */
        $this->_initFromResultSet();
    }

    /* Assign POST variables to internal variables of this class and
       remove evil tags where applicable. We shall probably also remove
       evil attributes et cetera, but this will be done later if ever. */
    function processPostVars()
    {
        $this->id = (integer)$_POST['id'];
        $this->title = trimStrip($_POST['title']);
        $this->year = (integer)$_POST['year'];
        $this->lecture_id = (integer)$_POST['lecture_id'];
        $this->do_grades = (integer)$_POST['do_grades'];
        $this->pts_A = (integer)$_POST['pts_A'];
        $this->pts_B = (integer)$_POST['pts_B'];
        $this->pts_C = (integer)$_POST['pts_C'];
        $this->pts_D = (integer)$_POST['pts_D'];
        $this->pts_E = (integer)$_POST['pts_E'];
    }

    /* Returns a list of tasks for the current evaluation scheme. */
    function getTaskList()
    {
        $evaluationTasksBean =
            new EvaluationTasksBean ($this->id, $this->_smarty, "x", "x");
        return $evaluationTasksBean->getTaskList();
    }

    function getEvalYear()
    {
        return $this->eval_year;
    }

    function _dbQueryFullList($where)
    {
        return DatabaseBean::dbQuery(
            "SELECT * FROM evaluation" . $where . " ORDER BY year,title"
        );
    }

    function _getFullList($where = '')
    {
        $rs = $this->_dbQueryFullList($where);
        if (isset ($rs)) {
            foreach ($rs as $key => $val) {
                $rs[$key]['title'] = vlnka(stripslashes($val['title']));
                /* Extend the year to schoolyear */
                $year = $val['year'];
                $rs[$key]['schoolyear'] = $year . "/" . ($year + 1);
            }
        }
        return $rs;
    }

    /**
     * Create an array of ids representing either all records in the evaluation
     * table, or (if $lectureId > 0) only those evaluation recipes that are
     * bound to certain lecture id.
     */
    function getIds($lectureId = 0)
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

    /**
     * Create an array of ids representing all records in the evaluation list.
     * The list may be created for example by calling `assignFull()` of this
     * object.
     */
    function getIdsFromList($evaluationList)
    {
        /* Default returned value is an empty array. */
        $seq = array();

        /* Do the list processing only in case that the list is not empty. */
        if (isset ($evaluationList)) {
            /* Loop over the list and add all `id` indices into the output
               array. */
            foreach ($evaluationList as $key => $val) {
                /* But do so only in cases when `id` key really exists. */
                if (array_key_exists('id', $val)) {
                    $seq[] = $val['id'];
                } else {
                    /* Complain in cases when an incompatible list has been
                       passed as input to this function. */
                    trigger_error('Evaluation list needs an `id` key.');
                }
            }
        }
        return $seq;
    }

    /**
     * Initialise the evaluation bean by the evaluation scheme for the given
     * schoolyear.
     * The function will return boolean 'true' in case that the evaluation
     * was succesful.
     */
    function initialiseFor($lectureId, $schoolYear)
    {
        /* Limit evaluations to those of the given lecture ... */
        $where = $this->_lectureIdToWhereClause($lectureId);
        /* ... and valid in this or previous years (see below). */
        $where = $where . ' AND year<=' . $schoolYear;
        /* Get the evaluation that is valid for the given schoolyear. This
           evaluation will be the most current one with year <= schoolyear.
           Example: If there are different evaluation schemes in 2006, 2009,
           and 2010 and schoolyear is 2008, evaluation for 2006 will be
           returned as this was the scheme active in 2008 as well. For 2009,
           evaluation 2009 will be returned, for 2011, evaluation from 2010
           will be returned. */
        $rs = $this->dbQuery(
            'SELECT * FROM evaluation' . $where . ' ORDER BY year DESC LIMIT 1'
        );
        $ret = isset ($rs[0]);
        $this->dumpVar('rs', $rs);
        $this->dumpVar('ret', $ret);
        if ($ret) {
            $this->dumpVar('EvaluationBean::initialiseFor - rs[0]', $rs[0]);
            $this->rs = $rs[0];
            /* The ID is normally updated by dbQuerySingle() call which is not used here. */
            $this->id = $this->rs['id'];
            /* And update internal object variables with data provided by the resultset. */
            $this->_initFromResultSet();
            /* Make the data available to our templating engine. */
            $this->_smarty->assign('evaluation', $this->rs);
        }

        return $ret;
    }

    /* Add an empty evaluation scheme to the list of evaluation schemes. Used for assignments
       of unused tasks. */
    function addNullEvaluation(&$evaluationList)
    {
        $evaluation = $this->rs;
        $evaluation['id'] = 0;
        $evaluation['title'] = 'Nepoužito pro vyhodnocení';
        $evaluationList[] = $evaluation;

        return $evaluationList;
    }

    /* Assign a full list of task records. */
    function assignFull($lectureId = 0)
    {
        $where = $this->_lectureIdToWhereClause($lectureId);
        $rs = $this->_getFullList($where);
        $this->_smarty->assign('evaluationList', $rs);
        return $rs;
    }

    /* Assigns single value of evluation record to the Smarty variable
       'evaluation'. */
    function assignSingle()
    {
        /* If id == 0, we shall create a new record. */
        if ($this->id) {
            /* Query data of this person. */
            $this->dbQuerySingle();

            /* Get a lecture that this evaluation is related to. */
            $lectureBean = new LectureBean ($this->lecture_id, $this->_smarty, "", "");
            $lectureBean->assignSingle();
        } else {
            /* Initialize default values. */
            $this->_setDefaults();
        }
        $this->_smarty->assign('evaluation', $this->rs);
    }

    /* -------------------------------------------------------------------
       HANDLER: ADMIN
       ------------------------------------------------------------------- */
    function doAdmin()
    {
        /* Get the list of all exercises for the given lecture id. */
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
        /* Update the record. */
        $this->dbReplace();
        /* Fetch the data of the updated record and show them to the user. */
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
