<?php

class ImportBean extends DatabaseBean
{
    const FORMAT_WEBKOS = 2;
    const FORMAT_TERMINAL = 1;

    private $firstname;
    private $surname;
    private $yearno;
    private $groupno;
    private $email;
    private $hash;
    private $login;
    private $cvutid;
    private $groups;

    function _setDefaults()
    {
        $this->firstname = array();
        $this->surname = array();
        $this->yearno = array();
        $this->groupno = array();
        $this->email = array();
        $this->hash = array();
        $this->login = array();
        $this->cvutid = array();
        $this->groups = array();
    }

    /* Constructor */
    function __construct($id, &$smarty, $action, $object)
    {
        /* Call parent's constructor first */
        parent::__construct($id, $smarty, NULL, $action, $object);
        /* Initialise new properties to their default values. */
        $this->_setDefaults();
    }

    function processPostVars()
    {
        $this->firstname = $_POST['firstname'];
        $this->surname = $_POST['surname'];
        $this->hash = $_POST['hash'];
        $this->yearno = $_POST['yearno'];
        $this->groupno = $_POST['groupno'];
        $this->login = $_POST['login'];
        $this->email = $_POST['email'];
        $this->cvutid = $_POST['cvutid'];
        $this->groups = $_POST['groups'];
    }

    function dbReplace()
    {

    }

    /* -------------------------------------------------------------------
       HANDLER: ADMIN
       ------------------------------------------------------------------- */
    function doAdmin()
    {
        /* Get the information about evaluation. We have to initialise
           points to PTS_NOT_CLASSIFIED. Abort if there is no evaluation. */
        $evaluationBean = new EvaluationBean (0, $this->_smarty, NULL, NULL);
        /* This will initialise EvaluationBean with evaluation scheme for
           the current lecture (the id of the lecture is stored in the
           session). The function returns 'true' if the evaluation scheme
           has been found and the object has been initialised. */
        $ret = $evaluationBean->initialiseFor(SessionDataBean::getLectureId(), $this->schoolyear);

        /* Check the initialisation status. */
        if (!$ret) {
            /* No evaluation for this school year, abort. */
            $this->action = 'e_init';
            return ERR_ADMIN_MODE;
        }
        /* If a valid evaluation scheme exists, this action only displays a
           static page template. */
    }

    /* -------------------------------------------------------------------
       HANDLER: EDIT
       ------------------------------------------------------------------- */
    function doEdit()
    {
        /* Check the version of the imported data.
           'v1' is the old textfile from terminal version of KOS,
           'v2' is the newer format provided by WebKOS that unfortunately
                lacks student e-mails and logins. */
        $format = (int)$_POST['format'];

        /* Check if the automatic study year increment has been requested. */
        $addyear = (isset ($_POST['addyear']));

        /* Open an LDAP connection. */
        $ldap = new LDAPConnection ($this->_smarty);

        /* Initialise the list of students that will be imported. */
        $studentList = array();

        /* Remember the file information and publish it. */
        $kosfile = $_FILES['kosfile'];
        $this->assign('file', $kosfile);

        if (is_uploaded_file($kosfile['tmp_name'])) {
            $handle = @fopen($kosfile['tmp_name'], "r");
            if ($handle) {
                /* In case of FORMAT_WEBKOS we have to skip the first line of
                   the imported CSV file - it contains header information. */
                $skipHeader = ($format == self::FORMAT_WEBKOS);
                /* First row of the resulting list. */
                $row = 0;
                /* And loop while we have something to chew on ... */
                while (!feof($handle)) {
                    /* Read a line of text from the submitted file. */
                    $buffer = fgets($handle, 4096);
                    /* The file contains sometimes also form feed character
                       (^L, 0x0c) which shall be removed as well. */
                    $trimmed = trim($buffer, " \t\n\r\0\x0b\x0c");
                    /* The file may also contain some empty lines, and trimming
                       the form feed will generate another empty line. */
                    if (empty ($trimmed)) {
                        /* Skip empty lines. */
                        continue;
                    }
                    if ($skipHeader) {
                        /* Skip the header line in case of FORMAT_WEBKOS. */
                        $skipHeader = false;
                        continue;
                    }
                    /* The line contains several fields separated by semicolon. */
                    $la = explode(";", $trimmed);
                    self::dumpVar('la', $la);

                    /* Convert data from the file. */
                    $data = array();
                    switch ($format) {
                        case self::FORMAT_TERMINAL:
                            $data['surname'] = iconv("windows-1250", "utf-8", trim($la[1], " \t\n\r\""));
                            $data['firstname'] = iconv("windows-1250", "utf-8", trim($la[2], " \t\n\r\""));
                            $data['yearno'] = trim($la[3], " \t\n\r\"");
                            $data['groupno'] = trim($la[4], " \t\n\r\"");
                            $data['email'] = trim($la[5], " \t\n\r\"");
                            $emailex = explode("@", $data['email']);
                            $data['login'] = trim($emailex[0], " \t\n\r\"");
                            $data['hash'] = trim($la[6], " \t\n\r\"");
                            $data['cvutid'] = 0;
                            /* In some years, students from Decin do not have a group
                               number. We will assign them with group id 0 which is not
                               used anywhere. */
                            if (empty ($data['groupno'])) $data['groupno'] = "0";
                            break;
                        case self::FORMAT_WEBKOS:
                            $data['surname'] = iconv("windows-1250", "utf-8", trim($la[0], " \t\n\r\""));
                            $data['firstname'] = iconv("windows-1250", "utf-8", trim($la[1], " \t\n\r\""));
                            $data['yearno'] = trim($la[8], " \t\n\r\"");
                            $data['groupno'] = trim($la[10], " \t\n\r\"");
                            $cvutid = trim($la[2], " \t\n\r\"");
                            $data['cvutid'] = $cvutid;
                            $data['hash'] = $cvutid;
                            /* In some years, students from Decin do not have a group
                             number. We will assign them with group id 0 which is not
                            used anywhere. */
                            if (empty ($data['groupno'])) $data['groupno'] = "0";

                            /* Fetch information from LDAP about this student. */
                            $info = $ldap->searchSingle("cvutid=$cvutid");
                            /* ... and fill in the missing information. */
                            $data['email'] = $info['mail'][0];
                            $data['login'] = $info['uid'][1];

                            break;
                        default:
                            throw new Exception ('Neplatný formát vstupního souboru.');
                    }

                    /* If requested, increment the year number. */
                    if ($addyear) {
                        $data['yearno']++;
                    }

                    /* Append the group number to the list of group numbers. */
                    $group = (int)$data['groupno'];
                    $groupList[$group] = $group;

                    /* Check the format of the file. */

                    /* Append the record to the list of displayed names. */
                    $studentList[$row] = $data;
                    $row++;
                }

                /* Close the input file. */
                fclose($handle);
            } else {
                /* The file cannot be opened for reading. */
                $this->action = 'e_open';
                return ERR_ADMIN_MODE;
            }
        } else {
            /* Possible file upload attack.. */
            $this->action = 'e_upload';
            return ERR_FILE_UPLOAD_ATTACK;
        }

        /* Close the LDAP connection. */
        $ldap->close();

        /* Make the group list sorted. */
        sort($groupList);

        /* Publish the list of students and groups. */
        $this->assign('studentList', $studentList);
        $this->assign('groupList', $groupList);
        return RET_OK;
    }

    /* -------------------------------------------------------------------
       HANDLER: SAVE
       ------------------------------------------------------------------- */
    function doSave()
    {
        /* Assign POST variables to internal variables of this class and
           remove evil tags where applicable. */
        $this->processPostVars();

        $errstr = '';
        if (empty ($this->firstname)) $errstr .= "<li>Chybí křestní jména</li>\n";
        if (empty ($this->surname)) $errstr .= "<li>Chybí příjmení</li>\n";
        if (empty ($this->yearno)) $errstr .= "<li>Chybí ročníky</li>\n";
        if (empty ($this->groupno)) $errstr .= "<li>Chybí skupiny</li>\n";
        if (empty ($this->login)) $errstr .= "<li>Chybí loginy</li>\n";
        if (empty ($this->email)) $errstr .= "<li>Chybí emaily</li>\n";

        if (empty ($this->hash)) $errstr .= "<li>Chybí heše</li>\n";
        if (empty ($this->cvutid)) $errstr .= "<li>Chybí ČVUT ID</li>\n";
        if (empty ($this->groups)) $errstr .= "<li>Chybí seznam importovaných studijních skupin</li>\n";

        if (empty ($errstr)) {
            //self::dumpVar('cvutid', $this->cvutid );
            //self::dumpVar('cvutid', $this->firstname );

            /* Get the information about evaluation. We have to initialise
               points to PTS_NOT_CLASSIFIED. Abort if there is no evaluation. */
            $evaluationBean = new EvaluationBean (0, $this->_smarty, NULL, NULL);
            /* This will initialise EvaluationBean with evaluation scheme for
               the current lecture (the id of the lecture is stored in the
               session). The function returns 'true' if the evaluation scheme
               has been found and the object has been initialised. */
            $ret = $evaluationBean->initialiseFor(SessionDataBean::getLectureId(), $this->schoolyear);

            /* Check the initialisation status. */
            if (!$ret) {
                /* No evaluation for this school year, abort. */
                $this->action = 'e_init';
                return ERR_ADMIN_MODE;
            }

            $num = count($this->firstname);

            $urole = SessionDataBean::getUserRole();
            $ulogin = SessionDataBean::getUserLogin();

            /* Get the list of tasks for evaluation of this exercise. The list
               will contain only task IDs and we will have to fetch task and
               subtask information by ourselves later. */
            $taskList = $evaluationBean->getTaskList();

            /* This will both create a full list of subtasks corresponding to
               the tasks of the chosen evaluation scheme and assign this list
               to the Smarty variable 'subtaskList'. */
            $tsbean = new TaskSubtasksBean (0, $this->_smarty, "x", "x");
            $subtaskMap = $tsbean->getSubtaskMapForTaskList($taskList, $evaluationBean->eval_year);
            $subtasks = array_keys($subtaskMap);

            self::dumpVar("subtaskMap", $subtaskMap);
            self::dumpVar("subtasks", $subtasks);

            /* Flip the groups[] array so that we can identify the group using
               simple isset() call. */
            $groups = array_flip($this->groups);

            $row = 0;
            $date = getdate();
            $stlist = array();

            $ptbean = new PointsBean (0, $this->_smarty, NULL, NULL);
            while ($row < $num) {
                /* Get the numeric group number. */
                $groupno = intval($this->groupno[$row]);
                /* Check that this group is to be imported. */
                if (!isset ($groups[$groupno])) {
                    $row++;
                    continue;
                }

                $sb = new StudentBean (0, $this->_smarty, NULL, NULL);
                $sb->id = $this->cvutid[$row];
                $sb->hash = $this->hash[$row];
                $sb->surname = $this->surname[$row];
                $sb->firstname = $this->firstname[$row];
                $sb->groupno = $groupno;
                $sb->yearno = intval($this->yearno[$row]);
                $sb->calendaryear = $this->schoolyear;
                $sb->login = $this->login[$row];
                $sb->email = $this->email[$row];
                $sb->active = 1;
                $sb->password = "";
                $sb->dbReplace();
                $id = $sb->getObjectId();

                /* Now that the id of the student is valid we can add it to the
                   list of students that attend the lecture. */
                $stlist[] = $id;

                /* Mark all the subtasks as "not classified". */
                foreach ($subtasks as $val) {
                    /* Do not fonvert point value to its corresponding numeric
                       representation as it has been passed as a numberic
                       value. */
                    $ptbean->updatePoints(
                        $id, $val, $this->schoolyear,
                        PointsBean::PTS_NOT_CLASSIFIED, '', false);
                }

                $row++;
            }

            self::dumpVar('stlist', $stlist);

            /* Write the list of students attending the lecture into the
               database. */
            $seb = new StudentLectureBean (SessionDataBean::getLectureId(), $this->_smarty, NULL, NULL);
            $seb->year = $this->schoolyear;
            $seb->setStudentList($stlist, false);
        } else {
            /* Cannot process the submitted data. */
            $this->action = 'e_process';
            $this->assign('errormsg', $errstr);
            return ERR_ADMIN_MODE;
        }
    }

    /* -------------------------------------------------------------------
       HANDLER: DELETE
       ------------------------------------------------------------------- */
    function doDelete()
    {
        trigger_error(
            "Delete action not implemented - " .
            " (" . get_class($this) . ")");
        return ERR_INVALID_ACTION;
    }

    /* -------------------------------------------------------------------
       HANDLER: REAL DELETE
       ------------------------------------------------------------------- */
    function doRealDelete()
    {
        trigger_error(
            "Real delete action not implemented - " .
            " (" . get_class($this) . ")");
        return ERR_INVALID_ACTION;
    }
}

?>
