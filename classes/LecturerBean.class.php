<?php

class LecturerBean extends DatabaseBean
{
    var $role;
    var $surname;
    var $firstname;
    var $room;
    var $email;

    function _setDefaults()
    {
        $this->surname = $this->rs['surname'] = "";
        $this->firstname = $this->rs['firstname'] = "";
        $this->room = $this->rs['room'] = "";
        $this->email = $this->rs['email'] = "";
    }

    /* Constructor */
    function __construct($id, &$smarty, $action, $object)
    {
        /* Call parent's constructor first */
        parent::__construct($id, $smarty, "lecturer", $action, $object);
        /* Initialise new properties to their default values. */
        $this->_setDefaults();
    }

    function dbReplace()
    {
        DatabaseBean::dbQuery(
            "REPLACE lecturer VALUES ("
            . $this->id . ",'"
            . mysql_escape_string($this->surname) . "','"
            . mysql_escape_string($this->firstname) . "','"
            . mysql_escape_string($this->room) . "','"
            . mysql_escape_string($this->email) . "')"
        );

        $this->updateId();
    }

    function dbQuerySingle()
    {
        /* Query the data of this section (ID has been already specified) */
        DatabaseBean::dbQuerySingle();
        /* Initialize the internal variables with the data queried from the
           database. */
        $this->firstname = vlnka(stripslashes($this->rs['firstname']));
        $this->surname = vlnka(stripslashes($this->rs['surname']));
        $this->room = $this->rs['room'];
        $this->email = $this->rs['email'];
        /* Publish the student data */
        $this->rs['firstname'] = $this->firstname;
        $this->rs['surname'] = $this->surname;
    }

    /* Assign POST variables to internal variables of this class and
       remove evil tags where applicable. We shall probably also remove
       evil attributes et cetera, but this will be done later if ever. */
    function processPostVars()
    {
        $this->id = $_POST['id'];
        $this->surname = trimStrip($_POST['surname']);
        $this->firstname = trimStrip($_POST['firstname']);
        $this->room = trimStrip($_POST['room']);
        $this->email = trimStrip($_POST['email']);
    }

    function _getFullList($where = '')
    {
        $rs = DatabaseBean::dbQuery(
            "SELECT * FROM lecturer" . $where . " ORDER BY surname,firstname,room");

        foreach ($rs as $key => $val) {
            $rs[$key]['surname'] = vlnka(stripslashes($val['surname']));
            $rs[$key]['firstname'] = vlnka(stripslashes($val['firstname']));
            $rs[$key]['room'] = vlnka(stripslashes($val['room']));
            $rs[$key]['email'] = vlnka(stripslashes($val['email']));
        }

        return $rs;
    }

    function _dbQueryList($lectureId = 0)
    {
        if ($lectureId == 0) {
            return DatabaseBean::dbQuery(
                "SELECT id, surname, firstname, room, email FROM lecturer " .
                "ORDER BY surname"
            );
        } else {
            return DatabaseBean::dbQuery(
                "SELECT lr.id, surname, firstname, lr.room, email " .
                "FROM lecturer AS lr LEFT JOIN exercise ON lr.id=lecturer_id " .
                "WHERE lecture_id=" . $lectureId . " " .
                "GROUP BY lr.id ORDER BY surname"
            );
        }
    }

    function dbQueryLecturerMap()
    {
        $resultset = $this->_dbQueryList();

        $lecturerMap = array();
        if (isset ($resultset)) {
            foreach ($resultset as $key => $val) {
                $id = $val['id'];
                $lecturerMap[$id]['surname'] = stripslashes($val['surname']);
                $lecturerMap[$id]['firstname'] = stripslashes($val['firstname']);
                $lecturerMap[$id]['room'] = stripslashes($val['room']);
                $lecturerMap[$id]['email'] = stripslashes($val['email']);
            }
        }

        /* Append default record. */
        $lecturerMap[0]['surname'] = '-';
        $lecturerMap[0]['firstname'] = '-';
        $lecturerMap[0]['room'] = '-';
        $lecturerMap[0]['email'] = '-';

        return $lecturerMap;
    }

    function getSelectMap($lectureId = 0)
    {
        $resultset = $this->_dbQueryList($lectureId);

        $lecturerMap = array();
        if ($lectureId == 0) {
            $lecturerMap[0] = "Vyberte ze seznamu ...";
        }
        if (isset ($resultset)) {
            foreach ($resultset as $key => $val) {
                $lecturerMap[$val['id']] = stripslashes($val['firstname']) . " " . stripslashes($val['surname']);
            }
        }
        return $lecturerMap;
    }

    function assignSelectMap()
    {
        $lecturerMap = $this->getSelectMap();
        $this->_smarty->assign('lecturerSelect', $lecturerMap);
        return $lecturerMap;
    }

    /* Assign a full list of lecturer records. */
    function assignFull()
    {
        $rs = $this->_getFullList();
        $this->_smarty->assign('lecturerList', $rs);
        return $rs;
    }

    /* Assign a single lecturer record. */
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
        $this->_smarty->assign('lecturer', $this->rs);
    }

    /* -------------------------------------------------------------------
       HANDLER: ADMIN
       ------------------------------------------------------------------- */
    function doAdmin()
    {
        /* Get the list of all lecturers. */
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
           displayed. By assigning $this->rs to Smarty variable 'user'
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
