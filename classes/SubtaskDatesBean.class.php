<?php

/*
 * Created on 24.2.2010
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

class SubtaskDatesBean extends DatabaseBean
{
    protected $subtask_id;
    protected $year; //! Probably the same as in session!!!
    protected $datefrom;
    protected $dateto;

    /**
     * Any subtask is implicitly from noon of `datefrom` until noon of `dateto`.
     */
    const SUBTASK_LIMIT_TIME = "12:00:00";

    /**
     * Defaults for the properties of this subtask.
     */
    function _setDefaults()
    {
        /* Update defaults. */
        $this->subtask_id = $this->id;
        $this->year = SessionDataBean::getSchoolYear();
        $this->datefrom = date('d.m.Y');
        $this->dateto = date('d.m.Y');
        /* And reflect these changes in $this->rs */
        $this->_update_rs();
    }

    /**
     * Create an instance of SubtaskDateBean.
     * Calls also the partent constructor.
     */
    function __construct($id, &$smarty, $action, $object)
    {
        /* Call parent's constructor first */
        parent::__construct($id, $smarty, "subtaskdates", $action, $object);
    }

    /**
     * Replace the database content with information from this object.
     */
    function dbReplace()
    {
        /* Fist we have to delete old data. We cannot use REPLACE as
           there is no primary key in the table. */
        $this->dbQuery(
            "DELETE FROM subtaskdates " .
            "WHERE year=" . $this->year . " " .
            "AND subtask_id=" . $this->subtask_id);
        /* And now insert the updated record. */
        $this->dbQuery(
            "INSERT INTO subtaskdates VALUES (" .
            $this->subtask_id . "," .
            $this->year . ",'" .
            $this->datefrom . "','" .
            $this->dateto . "')");
    }

    /**
     * Process properties updatev via HTTP POST request.
     */
    function processPostVars()
    {
        assignPostIfExists($this->subtask_id, $this->rs, 'subtask_id');
        assignPostIfExists($this->year, $this->rs, 'year');

        $this->datefrom = $this->rs['datefrom']
            = czechToSQLDateTime(
                trimStrip($_POST['datefrom'])) .
            " " . self::SUBTASK_LIMIT_TIME;
        $this->dateto = $this->rs['dateto']
            = czechToSQLDateTime(
                trimStrip($_POST['dateto'])) .
            " " . self::SUBTASK_LIMIT_TIME;
    }

    /**
     * Query the database for a list of subtask dates.
     * Assumes an array of records where `id` index represents the subtask id
     * and non-zero schoolyear.
     */
    function getSubtaskDates($resultSet, $schoolYear)
    {
        /* Default returned value is an empty array. */
        $ret = array();
        /* Convert the values in `resultSet` into a list of ids. */
        $idList = array2ToDBString($resultSet, 'id');
        /* Query the dates. */
        $rs = $this->dbQuery(
            'SELECT * FROM subtaskdates ' .
            'WHERE subtask_id IN (' . $idList . ') AND year=' . $schoolYear);
        /* If we have some returned data, we have to process them. */
        if (!empty ($rs)) {
            /* We will reindex the result by subtask id. */
            foreach ($rs as $val) {
                $ret[$val['subtask_id']] = $val;
            }
        }
        return $ret;
    }

    /**
     * Query the database for information about the activity dates and assign it
     * to Smarty variable `subtaskdates`.
     */
    function assignSingle()
    {
        /* Value of `rs` may be empty. It may even contain more than
           a single record, but this is an error. */
        $rs = $this->dbQuery(
            'SELECT * FROM subtaskdates ' .
            'WHERE subtask_id=' . $this->subtask_id . ' ' .
            'AND year=' . $this->schoolyear);
        if (empty ($rs)) {
            /* Empty result, replace rs with default values. */
            $this->_setDefaults();
            $dates = $this->rs;
        } elseif (count($rs) > 1) {
            /* More than one record for this subtask and schoolyear. */
            trigger_error("Cannot have more than one `subtaskdates` record.");
        } else {
            $dates = $rs[0];
        }

        /* Make the date information avalable to Smarty. */
        $this->_smarty->assign('subtaskdates', $dates);
    }

    /**
     * Query the database for data of the subtask specified in the `id`
     * atrtribute and assign it as a Smarty variable `subtask`.
     */
    function assignSubtask()
    {
        $subtaskBean = new SubtaskBean($this->id, $this->_smarty, NULL, NULL);
        $subtaskBean->assignSingle();
    }

    /**
     * Handle edit requests.
     */
    function doEdit()
    {
        /* Fetch the information about the subtask we are changing dates for. */
        $this->assignSubtask();
        /* Initialise the school year. */
        $this->schoolyear = SessionDataBean::getSchoolYear();
        /* The id value is actually the same as subtask_id .... ??? */
        $this->subtask_id = $this->id;
        /* Set the information about the current subtask dates. */
        $this->assignSingle();
    }

    /**
     * Handle save requests.
     */
    function doSave()
    {
        /* Update the attributes of this object from data passed to us in
           a POST request. */
        $this->processPostVars();
        /* Save the sumbitted data. */
        $this->dbReplace();
        /* Fetch the information about the subtask we are changing dates for. */
        $this->assignSubtask();
    }
}

?>
