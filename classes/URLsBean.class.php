<?php

class URLsBean extends DatabaseBean
{
    var $url;
    var $title;
    var $description;
    var $position;

    function _setDefaults()
    {
        $this->url = $this->rs['url'] = '';
        $this->title = $this->rs['title'] = '';
        $this->description = $this->rs['description'] = '';
        $this->position = $this->rs['position'] = 0;
        $this->lecture_id = $this->rs['lectutre_id'] = 0;
    }

    /* Constructor */
    function __construct($id, &$smarty, $action, $object)
    {
        /* Call parent's constructor first */
        parent::__construct($id, $smarty, "urls", $action, $object);
        /* Initialise new properties to their default values. */
        $this->_setDefaults();
    }

    function dbReplace()
    {
        DatabaseBean::dbQuery(
            "REPLACE urls VALUES ("
            . $this->id . ",'"
            . mysql_escape_string($this->url) . "','"
            . mysql_escape_string($this->title) . "','"
            . mysql_escape_string($this->description) . "',"
            . mysql_escape_string($this->position) . ","
            . mysql_escape_string($this->lecture_id) . ")"
        );

        $this->updateId();
    }

    function dbQuerySingle()
    {
        /* Query the data of this section (ID has been already specified) */
        DatabaseBean::dbQuerySingle();
        /* Initialize the internal variables with the data queried from the
           database. */
        $this->title = vlnka(stripslashes($this->rs['title']));
        $this->description = vlnka(stripslashes($this->rs['description']));
        $this->position = $this->rs['position'];
        $this->url = $this->rs['url'];
        /* Write the updated 'title' and 'description' to '$this->rs' so that we
           will display the updated version. */
        $this->rs['title'] = $this->title;
        $this->rs['description'] = $this->description;
    }

    /* Assign POST variables to internal variables of this class and
       remove evil tags where applicable. We shall probably also remove
       evil attributes et cetera, but this will be done later if ever. */
    function processPostVars()
    {
        $this->id = (integer)$_POST['id'];
        $this->url = trimStrip($_POST['url']);
        $this->title = trimStrip($_POST['title']);
        $this->description = trimStrip($_POST['description']);
        $this->position = (integer)$_POST['position'];
        $this->lecture_id = SessionDataBean::getLectureId();
    }

    function _getFullList($where = '')
    {
        $rs = DatabaseBean::dbQuery(
            "SELECT * FROM urls" . $where . " ORDER BY position,title");
        if (isset ($rs)) {
            foreach ($rs as $key => $val) {
                $rs[$key]['description'] = vlnka(stripslashes($val['description']));
                $rs[$key]['position'] = vlnka(stripslashes($val['position']));
            }
        }
        return $rs;
    }

    /* Return a list of URLS for the given lecture id. */
    function getFullListForLectureId($lectureId)
    {
        return $this->_getFullList(' WHERE lecture_id=' . $lectureId);
    }

    /* Assign a full list of url records. */
    function assignFull()
    {
        $rs = $this->getFullListForLectureId(SessionDataBean::getLectureId());
        $this->_smarty->assign('urlsList', $rs);
        return $rs;
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
        $this->_smarty->assign('url', $this->rs);
    }

    /* -------------------------------------------------------------------
       HANDLER: ADMIN
       ------------------------------------------------------------------- */
    function doAdmin()
    {
        /* Get the list of all news entries. */
        $this->assignFull();
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
           displayed. By assigning $this->rs to Smarty variable 'news'
           we can fill the values of $this->rs into a template. */
        $this->assignSingle();
    }

    /* -------------------------------------------------------------------
       HANDLER: SAVE
       ------------------------------------------------------------------- */
    function doSave()
    {
        /* Assign POST variables to internal variables of this class and
           remove evil tags where applicable. */
        $this->processPostVars();

        /* Update the news record. */
        $this->dbReplace();

        /* Fetch the updated news from the database so that we can display
           something. */
        $this->assignSingle();
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
