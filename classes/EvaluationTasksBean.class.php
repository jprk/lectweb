<?php

class EvaluationTasksBean extends DatabaseBean
{
    var $relation;

    function _setDefaults()
    {
        $this->relation = array();
        /* Update $this->rs */
        $this->_update_rs();
    }

    /* Constructor */
    function __construct($id, &$smarty, $action, $object)
    {
        /* Call parent's constructor first */
        parent::__construct($id, $smarty, "evltsk", $action, $object);
    }

    function dbReplace()
    {
        foreach ($this->relation as $eval_id => $tasks) {
            /* Delete all existing mappings evaluation-task. We will re-create
             * those that are valid again. */
            DatabaseBean::dbQuery(
                "DELETE FROM evltsk WHERE evaluation_id=" . $eval_id
            );
            foreach ($tasks as $task_id) {
                DatabaseBean::dbQuery(
                    "REPLACE evltsk VALUES ("
                    . $eval_id . ","
                    . $task_id . ")"
                );
            }
        }
    }

    function dbQuerySingle()
    {
    }

    /* Assign POST variables to internal variables of this class and
       remove evil tags where applicable. We shall probably also remove
       evil attributes et cetera, but this will be done later if ever. */
    function processPostVars()
    {
        $this->relation = $_POST['te_rel'];
    }

    function getTaskList()
    {
        $rs = DatabaseBean::dbQuery(
            "SELECT task_id FROM evltsk WHERE evaluation_id="
            . $this->id);

        $taskList = array();
        if (isset ($rs)) {
            foreach ($rs as $key => $val) {
                $taskList[] = $val['task_id'];
            }
        }
        return $taskList;
    }

    function getTaskEvaluationMap($evaluationIdSq)
    {
        $dbString = arrayToDBString($evaluationIdSq);
        $rs = DatabaseBean::dbQuery(
            "SELECT * FROM evltsk "
            . "WHERE evaluation_id IN (" . $dbString . ")"
        );
        $map = array();
        if (isset ($rs)) {
            foreach ($rs as $key => $val) {
                $map[$val['task_id']] = $val['evaluation_id'];
            }
        }
        $this->dumpVar('map', $map);
        return $map;
    }

    function assignFull()
    {
        $rs = DatabaseBean::dbQuery(
            "SELECT * FROM evltsk WHERE evaluation_id="
            . $this->id);

        $taskList = array();
        foreach ($rs as $key => $val) {
            $taskList[] = $val['task_id'];
        }
        return $taskList;
    }

    /* -------------------------------------------------------------------
       HANDLER: EDIT
       ------------------------------------------------------------------- */
    function doEdit()
    {
        /* Initialise the selected evaluation scheme so that we know the id of
           the lecture. Calling `assignSingle()` also publishes the lecture
           record. */
        $evaluationBean = new EvaluationBean ($this->id, $this->_smarty, "", "");
        $evaluationBean->assignSingle();
        /* Now fetch the all evaluation schemes for the same lecture from the
           database so that user can edit the assignment matrix at once. We will
           also add an empty evaluation scheme that is used to accomodate all
           evaluation tasks that are not used. */
        $evaluationList = $evaluationBean->assignFull($evaluationBean->lecture_id);
        $evaluationList = $evaluationBean->addNullEvaluation($evaluationList);
        /* Get just an array of evaluation scheme `id` values. */
        $evaluationIdSq = $evaluationBean->getIdsFromList($evaluationList);

        /* Get the list of tasks defined for this lecture. */
        $taskBean = new TaskBean (0, $this->_smarty, "", "");
        $taskList = $taskBean->assignFull($evaluationBean->lecture_id);

        $this->dumpVar('evaluationList', $evaluationList);
        $this->dumpVar('evaluationIdSq', $evaluationIdSq);
        $this->dumpVar('taskList', $taskList);

        /* Now get the maping describing which tasks are used for which
           evaluation. */
        $teMap = $this->getTaskEvaluationMap($evaluationIdSq);
        $this->dumpVar('teMap', $teMap);

        /* Go through the 'evaluationList' and add a new field 'checked'
           to the entries of the list. The field is indexed by 'taskList'
           entries and contains either 'checked' or an empty string. */
        foreach ($evaluationList as $ek => $ev) {
            $eId = $evaluationList[$ek]['id'];
            $tChecked = array();
            foreach ($taskList as $tk => $tv) {
                $tId = $taskList[$tk]['id'];
                $tChecked[$tk] = ($teMap[$tId] == $eId) ? ' checked="checked"' : '';
            }
            $evaluationList[$ek]['checked'] = $tChecked;
        }
        $this->_smarty->assign('evaluationList', $evaluationList);
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
        /* Get the description of the lecture we are editing evaluation
           bindings for. */
        $lectureBean = new LectureBean ($this->id, $this->_smarty, "", "");
        $lectureBean->assignSingle();
    }
}

?>
