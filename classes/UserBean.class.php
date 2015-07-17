<?php

/* User categories */
define ('USR_ANONYMOUS', 0);
define ('USR_ADMIN', 1);
define ('USR_LECTURER', 100);
define ('USR_STUDENT', 200);

/* Password mask */
define ('PASSWORD_MASK', 'b669540a842d11a3eed2f4bdb2c16cd7');

class UserBean extends DatabaseBean
{
    private static $_roles = array(
        USR_ANONYMOUS => 'Anonym',
        USR_ADMIN => 'Administrátor',
        USR_LECTURER => 'Učitel',
        USR_STUDENT => 'Student'
    );

    var $role;
    var $firstname;
    var $surname;
    var $email;
    var $login;
    var $password;
    var $lastlogin;
    var $failcount;

    function _setDefaults()
    {
        $this->role = $this->rs['role'] = USR_ANONYMOUS;
        $this->firstname = $this->rs['firstname'] = "";
        $this->surname = $this->rs['surname'] = "";
        $this->email = $this->rs['email'] = "";
        $this->password = $this->rs['password'] = "";
        $this->login = $this->rs['login'] = "";
        $this->lastlogin = $this->rs['lastlogin'] = "";
        $this->failcount = $this->rs['failcount'] = -1;
    }

    function _getUserRoles()
    {
        return self::$_roles;
    }

    /* Constructor */
    function __construct($id, &$smarty, $action, $object)
    {
        /* Call parent's constructor first */
        parent::__construct($id, $smarty, "user", $action, $object);
        /* Initialise new properties to their default values. */
        $this->_setDefaults();
    }

    /** Getter method for role data stored within session. */
    static function getRole(&$rsData)
    {
        return $rsData['role'];
    }

    /** Getter method for login data stored within session. */
    static function getLogin(&$rsData)
    {
        return $rsData['login'];
    }

    /** Getter method for full user name stored after login within ther session. */
    static function getFullName(&$rsData)
    {
        return $rsData['firstname'] . ' ' . $rsData['surname'];
    }

    /** Getter method for role name. */
    static function getRoleName($role)
    {
        return self::$_roles[$role];
    }

    /** Setter method for role data stored within session. */
    static function setRole(&$rsData, $role)
    {
        $rsData['role'] = $role;
    }

    /** Setter method for login data stored within session. */
    static function setLogin(&$rsData, $login)
    {
        $rsData['login'] = $login;
    }

    /**
     * Check if the role is at least the given level.
     * The funciton has to take into account the USR_ANONYMOUS role which is
     * due to legacy reasons represented by role id == 0.
     */
    static function isRoleAtLeast($role, $roleRef)
    {
        return (($role <= $roleRef and $role != USR_ANONYMOUS) or $roleRef == USR_ANONYMOUS);
    }

    function dbReplace()
    {
        if (!$this->id) {
            /* Standard replace does not replace passwords */
            DatabaseBean::dbQuery(
                "REPLACE user (id,login,role,firstname,surname,email) VALUES ("
                . $this->id . ",'"
                . mysql_escape_string($this->login) . "',"
                . $this->role . ",'"
                . mysql_escape_string($this->firstname) . "','"
                . mysql_escape_string($this->surname) . "','"
                . mysql_escape_string($this->email) . "')"
            );
            /* New records have initial 'id' equal to zero and the proper value is
               set by the database engine. We have to retrieve the 'id' back so that
               we can later try to update passwords as well. */
            $this->id = mysql_insert_id();
        } else {
            /* Update just selected elements of the record, except the hashed
               password. */
            DatabaseBean::dbQuery(
                "UPDATE user SET "
                . "login='" . mysql_escape_string($this->login) . "', "
                . "role=" . $this->role . ", "
                . "firstname='" . mysql_escape_string($this->firstname) . "', "
                . "surname='" . mysql_escape_string($this->surname) . "', "
                . "email='" . mysql_escape_string($this->email) . "' "
                . "WHERE id=" . $this->id
            );
        }
    }

    function dbUpdatePassword($pass)
    {
        /* Set the password */
        $this->password = $pass;
        /* Standard replace does not replace passwords */
        DatabaseBean::dbQuery(
            "UPDATE user SET "
            . "password=MD5('" . mysql_escape_string($this->password) . "') "
            . "WHERE id='" . $this->id . "'"
        );
    }

    function dbCheckLogin($login, $password)
    {
        /* Query the database for the login and password tuple. */
        $rs = DatabaseBean::dbQuery(
            "SELECT * FROM user WHERE "
            . "login='" . mysql_escape_string($login) . "' AND "
            . "password=MD5('" . mysql_escape_string($password) . "')");

        /* If the result contains something, the check is positive. */
        if (!empty ($rs)) {
            /* There will be no other record than $rs[0] as logins have
               to be unique. */
            $this->id = $rs[0]['id'];
            /* Initialise the bean data of this user from database. */
            $this->dbQuerySingle();
            /* Reset the counter of unsuccesful logins and update the
               timestamp of the last succesful login. */
            DatabaseBean::dbQuery(
                "UPDATE user SET "
                . "lastlogin=NULL, "
                . "failcount=0 "
                . "WHERE id=" . $this->id
            );
            /* Set the returned record. */
            $rs = $this->rs;
        } else {
            /* Update the counter of unsuccesful logins. */
            DatabaseBean::dbQuery(
                "UPDATE user SET " .
                "failcount=failcount+1 " .
                "WHERE login='" . mysql_escape_string($login) . "'"
            );
        }

        return $rs;
    }

    function dbQuerySingle()
    {
        /* Query the data of this section (ID has been already specified) */
        DatabaseBean::dbQuerySingle();
        /* Initialize the internal variables with the data queried from the
           database. */
        //$this->category   = $this->rs['category'];
        $this->firstname = vlnka(stripslashes($this->rs['firstname']));
        $this->surname = vlnka(stripslashes($this->rs['surname']));
        $this->email = $this->rs['email'];
        $this->login = $this->rs['login'];
        $this->role = $this->rs['role'];
        /* Mask password if one exists */
        if (!empty ($this->rs['password'])) {
            $this->password = $this->rs['password'] = PASSWORD_MASK;
        } else {
            $this->password = $this->rs['password'];
        }
        $this->lastlogin = $this->rs['lastlogin'];
        $this->failcount = $this->rs['failcount'];

        /* Publish the mangled textual data */
        $this->rs['firstname'] = $this->firstname;
        $this->rs['surname'] = $this->surname;

        /* Publish the role name. */
        $roles = $this->_getUserRoles();
        $this->rs['roleName'] = $roles[$this->role];
    }

    /* Assign POST variables to internal variables of this class and
       remove evil tags where applicable. We shall probably also remove
       evil attributes et cetera, but this will be done later if ever. */
    function processPostVars()
    {
        $this->id = $_POST['id'];
        $this->role = $_POST['role'];
        $this->firstname = trimStrip($_POST['firstname']);
        $this->surname = trimStrip($_POST['surname']);
        $this->email = trimStrip($_POST['email']);
        $this->login = trimStrip($_POST['login']);
        $this->password = trimStrip($_POST['pass1']);
    }

    function _getFullList($where = '')
    {
        $roles = $this->_getUserRoles();
        $rs = DatabaseBean::dbQuery(
            "SELECT id, surname, firstname, login, role FROM user" . $where . " ORDER BY surname");
        if (isset ($rs)) {
            foreach ($rs as $key => $val) {
                $rs[$key]['surname'] = stripslashes($val['surname']);
                $rs[$key]['firstname'] = stripslashes($val['firstname']);
                $rs[$key]['login'] = stripslashes($val['login']);
                $rs[$key]['roleName'] = $roles[$val['role']];
            }
        }
        return $rs;
    }

    function assignFull()
    {
        $rs = $this->_getFullList();
        $this->_smarty->assign('userList', $rs);
        return $rs;
    }

    function getMap()
    {
        $rs = $this->_getFullList();

        $map = array();
        if (isset ($rs)) {
            foreach ($rs as $key => $val) {
                $map[$val['id']] = $rs[$key];
            }
            $map[0] = array('firstname' => '', 'surname' => '-');
        }
        return $map;
    }

    /**
     * Initialises all internal and template variables using data stored in
     * $this->rs[]. The function expects the contents of $this->rs[] to be
     * initialised already. Main reason for this construct is the process of
     * logging into the application - the `LoginBean` instance calls the
     * password checker provided by this class first and immediately after the
     * process of password verification the information about the user is queried
     * (otherwise the date of last succesful login and the number of unsuccessful
     * logins would be lost).
     */
    function doShowWithoutQuery()
    {
        /* The function above sets $this->rs to values that shall be
           displayed. By assigning $this->rs to Smarty variable 'section'
           we can fill the values of $this->rs into a template. */
        $this->_smarty->assign('user', $this->rs);
        /* Get left-hand menu, which will be an empty menu pointing to the
           parent level. */
        $this->_smarty->assign('leftcolumn', "leftempty.tpl");
    }

    /* -------------------------------------------------------------------
       HANDLER: SHOW
       ------------------------------------------------------------------- */
    function doShow()
    {
        /* Query data of this section */
        $this->dbQuerySingle();

        /* The rest is taken care of by the following function. */
        $this->doShowWithoutQuery();
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

                /* Send an e-mail to the user saying that the password has been changed. */

                $header = "From: " . SENDER_FULL . "\r\n";
                // $header  .= "To: <" . $this->email . ">\r\n";
                $header .= "Content-Type: text/plain; charset=\"utf-8\"\r\n";
                $header .= "Errors-To: " . ADMIN_FULL . "\r\n";
                $header .= "Reply-To: " . ADMIN_FULL . "\r\n";
                $header .= "X-Mailer: PHP";

                $userLogin = SessionDataBean::getUserLogin();
                $userName = SessionDataBean::getUserFullName();

                $message = "Uživatel '" . $userLogin . "' (" . $userName . ") změnil vaše heslo\r\n";
                $message .= "pro přihlašování na katederní stránky předmětů.\r\n";
                $message .= "\r\n";
                $message .= "Vaše uživatelské jméno: " . $this->login . "\r\n";
                $message .= "Vaše nové heslo zní: " . $pass . "\r\n";
                $message .= "\r\n";
                $message .= "Spravovat stránky, na něž máte oprávnění, můžete po přihlášení\r\n";
                $message .= "vaším uživatelským jménem a heslem na URL\r\n";
                $message .= "\r\n";
                $message .= "\thttp://zolotarev.fd.cvut.cz/msap/\r\n";
                $message .= "\thttp://zolotarev.fd.cvut.cz/ma/\r\n";
                $message .= "\r\n";

                /* Now send compose the header and send the mail to the user
                   (in case that we can send mails to users) ... */
                if (SEND_MAIL) {
                    $subject = "=?utf-8?B?" . base64_encode('[K611KW] Nové heslo pro správu katederních předmětů a cvičení') . "?=";
                    mail($this->email, $subject, $message, $header);
                }

                /* ... and always send a copy to the administrator. */
                $subject = "=?utf-8?B?" . base64_encode('[K611LW] Uživatel <' . $this->email . '> - nové heslo pro přístup') . "?=";
                mail(ADMIN_EMAIL, $subject, $message, $header);

                $this->_smarty->assign('passchanged', true);
            } else {
                $this->action = 'error01';
            }
        } else {
            if (empty ($pass)) {
                $this->action = 'error02';
            }
        }

        /* Assign information about the user. */
        $this->dbQuerySingle();
        $this->_smarty->assign('user', $this->rs);

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
    }

    /* -------------------------------------------------------------------
       HANDLER: REAL DELETE
       ------------------------------------------------------------------- */
    function doRealDelete()
    {
        /* Delete the record */
        DatabaseBean::dbDeleteById();
    }

    /* -------------------------------------------------------------------
       HANDLER: ADMIN
       ------------------------------------------------------------------- */
    function doAdmin()
    {
        /* Get the list of all people */
        $this->assignFull();
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
    }
}

?>
