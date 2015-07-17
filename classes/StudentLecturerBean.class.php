<?php

class StudentLecturerBean extends DatabaseBean
{
    var $role;
    var $surname;
    var $firstname;
    var $yearno;
    var $group;

    function _setDefaults()
    {
        $this->surname = $this->rs['surname'] = "";
        $this->firstname = $this->rs['firstname'] = "";
        $this->yearno = $this->rs['yearno'] = 0;
        $this->group = $this->rs['group'] = 0;
    }

    /* Constructor */
    function __construct($id, &$smarty, $action, $object)
    {
        /* Call parent's constructor first */
        parent::__construct($id, $smarty, "student", $action, $object);
        /* Initialise new properties to their default values. */
        $this->_setDefaults();
    }

    function dbReplace()
    {
        /* Standard replace does not replace passwords */
        DatabaseBean::dbQuery(
            "REPLACE user (id,surname,firstname,yearno,group) VALUES ("
            . $this->id . ",'"
            . mysql_escape_string($this->surname) . "','"
            . mysql_escape_string($this->firstname) . "','"
            . $this->yearno . "','"
            . $this->group . "')"
        );
        /* New records have initial 'id' equal to zero and the proper value is
           set by the database engine. We have to retrieve the 'id' back so that
           we can later try to update passwords as well. */
        if (!$this->id) {
            $this->id = mysql_insert_id();
        }
    }

    function dbQuerySingle()
    {
        /* Query the data of this section (ID has been already specified) */
        DatabaseBean::dbQuerySingle();
        /* Initialize the internal variables with the data queried from the
           database. */
        $this->firstname = vlnka(stripslashes($this->rs['firstname']));
        $this->surname = vlnka(stripslashes($this->rs['surname']));
        $this->yearno = $this->rs['yearno'];
        $this->group = $this->rs['group'];
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
        $this->yearno = trimStrip($_POST['yearno']);
        $this->group = trimStrip($_POST['group']);
    }

    function assignStudentList()
    {
        $studentList = array();

        $resultset = DatabaseBean::dbQuery("SELECT id, surname, firstname, yearno, group FROM student ORDER BY surname");

        if (isset ($resultset)) {
            foreach ($resultset as $key => $val) {
                $studentList[$key]['id'] = $val['id'];
                $studentList[$key]['surname'] = stripslashes($val['surname']);
                $studentList[$key]['firstname'] = stripslashes($val['firstname']);
                $studentList[$key]['yearno'] = $val['yearno'];
                $studentList[$key]['group'] = $val['group'];
            }

            $this->dumpVar("userList", $studentList);
            $this->dumpVar("resultset", $resultset);
        }

        $this->_smarty->assign('studentList', $studentList);
    }

    /* -------------------------------------------------------------------
       HANDLER: SHOW
       ------------------------------------------------------------------- */
    function doShow()
    {
        /* Query data of this section */
        $this->dbQuerySingle();
        /* The function above sets $this->rs to values that shall be
           displayed. By assigning $this->rs to Smarty variable 'section'
           we can fill the values of $this->rs into a template. */
        $this->_smarty->assign('person', $this->rs);
        /* Get left-hand menu, which will be an empty menu pointing to the
           parent level. */
        $this->_smarty->assign('leftcolumn', "leftempty.tpl");
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
        /* If the password is empty or equal to the password mask, do not
           update it. Otherwise update the database record with the new
           password specified by user. */
        $pass = $_POST["pass1"];
        $passCheck = $_POST["pass2"];

        $this->dumpVar("pass", $pass);
        $this->dumpVar("passCheck", $passCheck);

        if (!empty ($pass) && $pass != PASSWORD_MASK) {
            /* User has specified something. Although client-side Java
               script should have checked that both passwords are the same,
               let's check it once more. */
            if ($pass == $passCheck) {
                $this->dbUpdatePassword($pass);
            } else {
                $this->action = 'error';
            }
        } else {
            $this->action = 'passerror';
        }
        /* Saving can occur only in admin mode. Now that we have saved the
           data, return to the admin view by calling the appropriate action
           handler. */
        $this->doAdmin();
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
        /* Get the list of all people */
        $this->assignUserList();
        /* Left column contains administrative menu */
        $this->_smarty->assign('leftcolumn', "leftadmin.tpl");
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
        /* If id == 0, we shall create a new record. */
        if ($this->id) {
            /* Query data of this person. */
            $this->dbQuerySingle();
        } else {
            /* Initialize default values. */
            $this->_setDefaults();
        }
        /* Both above functions set $this->rs to values that shall be
           displayed. By assigning $this->rs to Smarty variable 'user'
           we can fill the values of $this->rs into a template. */
        $this->_smarty->assign('user', $this->rs);
        /* Get the list of all possible person categories. */
        $this->_smarty->assign('roles', $this->_getUserRoles());
        /* Left column contains administrative menu */
        $this->_smarty->assign('leftcolumn', "leftadmin.tpl");
    }
}

?>
