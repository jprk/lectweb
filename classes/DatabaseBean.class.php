<?php

class DatabaseBean extends BaseBean
{
    private $_table;
    protected $id;
    protected $rs;

    /**
     * Update the value of $this->rs with values of all visible variables
     * defined in the class.
     */
    protected function _update_rs()
    {
        /* Update $this->rs with visible values of this class. */
        foreach ($this as $key => $val) {
            /* Do not recursively update the value of `rs` and variables
               with underscore at the beginning.
               TODO: This is a potential (or even real) security threat as
               it lists also private and protected variables. */
            if ($key != 'rs' && $key[0] != '_') $this->rs[$key] = $val;
        }
    }

    function __construct($id, &$smarty, $table, $action, $object)
    {
        /* Construct the parent part of this instance */
        parent::__construct($smarty, $action, $object);

        $this->rs = array();
        $this->id = $this->rs['id'] = $id;
        $this->_table = $table;
    }


    /** Getter method for SessionDataBean. */
    static function getId(&$rsData)
    {
        if (!empty ($rsData)) {
            if (array_key_exists('id', $rsData))
                return $rsData['id'];
        }
        return 0;
    }

    function getObjectId()
    {
        return $this->id;
    }

    function getRsData()
    {
        return $this->rs;
    }

    /* -------------------------------------------------------------------
       DATABASE QUERIES
       ------------------------------------------------------------------- */
    function dbQuery($query, $idx = NULL)
    {
        return $this->_smarty->dbQuery($query, $idx);
    }

    function dbQuerySingle()
    {
        $this->rs = $this->_smarty->dbQuerySingle("SELECT * FROM " . $this->_table . " WHERE id=" . $this->id);
    }

    function dbDeleteById()
    {
        /* Delete the ID from the specified table. */
        $this->dbQuery("DELETE FROM " . $this->_table . " WHERE Id='" . $this->id . "'");
    }

    /* -------------------------------------------------------------------
       COUNTER QUERIES
       ------------------------------------------------------------------- */
    function dbCreateCounterById()
    {
        $this->dbQuery("REPLACE counter VALUES (LAST_INSERT_ID(),0)");
    }

    function dbDeleteCounterById()
    {
        $this->dbQuery("DELETE FROM counter WHERE Id='" . $this->id . "'");
    }

    function dbIncrementCounterById()
    {
        /* Increment access counter */
        $this->dbQuery('UPDATE counter SET count=count+1 WHERE Id=' . $this->id);
    }

    /* Returns SQL WHERE clause limiting queries only to records that are
       related to the given lecture id.
       @TODO@ the same piece as in EvaluationBean. */
    function _lectureIdToWhereClause($lectureId)
    {
        return ($lectureId > 0) ? " WHERE lecture_id=" . $lectureId : '';
    }


}

?>
