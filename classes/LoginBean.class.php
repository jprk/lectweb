<?php

/* IT MIGHT BE NEEDED TO CALL SESSION_START() IN PREHANDLEREXEC() */

class LoginBean extends BaseBean
{
    function __construct(&$smarty, $action, $object)
    {
        parent::__construct($smarty, $action, $object);
    }

    /* Student private pages can be shown to teachers, administrators, and
       the student itself. */
    static function canShowStudentId($id)
    {
        /* User role. */
        $roleId = SessionDataBean::getUserRole();
        $userId = SessionDataBean::getUserId();
        if ($roleId >= USR_ADMIN && $roleId < USR_STUDENT) {
            return true;
        }
        if ($roleId == USR_STUDENT && $userId == $id) {
            return true;
        }
        return false;
    }

    /**
     * Prepare home page for the user that has just logged in.
     * @param array $rs List of parameters of StudentBean or UserBean.
     */
    function prepareHomePage($rs, &$userBean, &$studentBean)
    {
        $this->dumpVar('rs1', $rs);

        /* Session bean expects a BaseBean sibling as a parameter, we do not
         want to limit the parameter to an array. */
        $this->rs = $rs;

        /* Remember some information about user in the current session. */
        SessionDataBean::setUserInformation($this);

        $this->dumpVar('SESSION\n', $_SESSION);

        /* Fetch data to display as an administrative menu. */
        $lectureBean = new LectureBean (0, $this->_smarty, "", "");
        $lectureBean->assignFull();

        /* Switch to the corresponding home page */
        $homeBean = 0;
        //if ( $_SESSION['role'] == USR_STUDENT )
        if (SessionDataBean::getUserRole() == USR_STUDENT) {
            $homeBean = $studentBean;
            $homeBean->action = 'show';
            $homeBean->object = 'student';
        } else {
            //$homeBean = new UserBean ( $_SESSION['uid'], $this->_smarty, 'show', 'user' );
            $homeBean = $userBean;
            $homeBean->action = 'show';
            $homeBean->object = 'user';
        }

        /* Fetch and assign to Smarty all data needed for display of the home page. */
        $homeBean->doShowWithoutQuery();

        /* Update action and object identifiers to that the proper template gets called
         by the controller. */
        $this->action = $homeBean->getAction();
        $this->object = $homeBean->getObject();
    }

    /**
     * Switch context to some student.
     * @see BaseBean::doAdmin()
     */
    function doAdmin()
    {
        /* For some reason the LoginBean does not accept `id` as the first
           parameter of the constructor and does not contain is as an internal
           variable. We have to explode the act=... parameter once more.
           TODO: This is not pretty.*/
        list ($action, $object, $stringId) = explode(',', $_GET['act']);
        $id = intval($stringId);

        if (UserBean::isRoleAtLeast(SessionDataBean::getUserRole(), USR_LECTURER)) {
            /* Fetch information about the student. */
            $studentBean = new StudentBean ($id, $this->_smarty, NULL, NULL);
            $studentBean->dbQuerySingle();

            /* Check that the person in question realy exists. */
            if (empty ($studentBean->login)) {
                $this->object .= '.e_noid';
            } else {
                /* And pretend she or he has just logged in. The instances of
                   UserBean and StudentBean are passed by reference and therefore
                   both variables have to exist. We know that our user in question
                   is a student, we will therefore set UserBean instance to NULL. */
                $userBean = NULL;
                $this->prepareHomePage(
                    $studentBean->getRsData(),
                    $userBean,
                    $studentBean);
            }
        } else {
            throw new Exception ('Only power users may take over student logins!');
        }
    }

    function doShow()
    {
        $this->assign('leftcolumn', "leftempty.tpl");
        /* Set the failed login flag to false and old username to empty so that the debug version of PHP does not
           complain about a non-existent variables. */
        $this->_smarty->assign('loginfailed', 0);
        $this->_smarty->assign('oldusername', '');
        /* It could have been that doShow() has been called from another
           handler. Change the action to "show" so that ctrl.php will
           know that it shall display the scriptlet for login.show */
        $this->action = "show";
    }

    function doVerify()
    {
        /* Initialise class instance of `StudentBean` class to none. */
        $studentBean = NULL;

        /* Create an instance of UserBean which will be used to verify the
           supplied login credentials. */
        $userBean = new UserBean (0, $this->_smarty, $this->action, $this->object);

        /* Look the user up in the list of system users (not students) first.
           The appropriate user will match the username and passowrd. */
        $rs = $userBean->dbCheckLogin($_POST['username'], $_POST['password']);

        /* Empty result set indicates that this user is not a lecturer or administrator.
           But still it might be a student. */
        if (empty ($rs)) {
            /* Create an instance of StudentBean which will be used to verify the
               supplied student login credentials. */
            $studentBean = new StudentBean (0, $this->_smarty, $this->action, $this->object);

            /* Try to authenticate user against our database or against an
               LDAP server given in config. */
            $rs = $studentBean->dbCheckLogin($_POST['username'], $_POST['password']);
        }

        $this->dumpVar('login verification rs', $rs);

        /* If the result set is empty now, it would indicate a login error. A result set
           containing some data will mean the user has successfully logged in. */
        if (!empty ($rs)) {
            $this->prepareHomePage($rs, $userBean, $studentBean);
        } else {
            $this->_smarty->assign('oldusername', $_POST['username']);
            $this->_smarty->assign('loginfailed', 1);
            /* Login has failed. Display the message and show the login
               screen again. */
            $this->doShow();
            $this->dumpThis();
        }
    }

    function doDelete()
    {
        /* Clear information about the current user stored in the
           session data storage and initialise the user information to
           indicate an anonymous user. */
        SessionDataBean::clearUserInformation();
    }
}

?>
