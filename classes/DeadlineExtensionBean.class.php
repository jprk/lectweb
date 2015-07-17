<?php

/**
 * Extension of deadlines for student assignments.
 *
 * (c) Jan Prikryl, 2009
 */
class DeadlineExtensionBean extends DatabaseBean
{
    /* Two modes of operation. */
    const EXTEND_SUBTASK = 1; /*> Extend one subtask for a subset of students. */
    const EXTEND_STUDENT = 2; /*> Extend a subset of subtasks for a student. */

    /* Two textual extensions corresponding to the operation modes. */
    const TEMPLATE_SUBTASK = '.subtask';
    const TEMPLATE_STUDENT = '.student';

    private $subtaskId;
    private $studentId;
    private $dateTo;
    private $operation;
    private $objids;

    /* Fill in reasonable defaults. */
    function _setDefaults()
    {
        $this->subtaskId = $this->rs['subtask_id'] = NULL;
        $this->studentId = $this->rs['student_id'] = NULL;
        $this->dateTo = $this->rs['dateto'] = '';
        $this->operation = NULL;
        $this->objids = array();
    }

    /* Constructor */
    function __construct($id, &$smarty, $action, $object)
    {
        /* Call parent's constructor first */
        parent::__construct($id, $smarty, 'extension', $action, $object);
        /* Update internals. */
        self::_setDefaults();
    }

    function dbReplace()
    {
        if (empty ($this->objids)) {
            throw new Exception ('Cannot extend deadlines with exmpty object list!');
        }

        foreach ($this->objids as $val) {
            switch ($this->operation) {
                case self::EXTEND_SUBTASK:
                    /* Delete the extension record that will be replaced. */
                    DatabaseBean::dbQuery(
                        "DELETE FROM extension WHERE "
                        . "subtask_id='" . $this->subtaskId . "' AND "
                        . "student_id='" . $val . "' AND "
                        . "year='" . $this->schoolyear . "'"
                    );
                    /* Store the new final date. */
                    DatabaseBean::dbQuery(
                        "REPLACE extension VALUES ('"
                        . $this->subtaskId . "','"
                        . $val . "','"
                        . $this->schoolyear . "','"
                        . mysql_escape_string($this->dateTo) . "')"
                    );

                    break;

                case self::EXTEND_STUDENT:
                    /* Delete the extension record that will be replaced. */
                    DatabaseBean::dbQuery(
                        "DELETE FROM extension WHERE "
                        . "subtask_id='" . $val . "' AND "
                        . "student_id='" . $this->studentId . "' AND "
                        . "year='" . $this->schoolyear . "'"
                    );
                    /* Store the new final date. */
                    DatabaseBean::dbQuery(
                        "REPLACE extension VALUES ('"
                        . $val . "','"
                        . $this->studentId . "','"
                        . $this->schoolyear . "','"
                        . mysql_escape_string($this->dateTo) . "')"
                    );

                    break;

                default:
                    throw new Exception ('Unsupported operation mode!');
            }

        }
    }

    function dbQuerySingle()
    {
        throw new Exception ('Method not implemented in this context!');
    }

    /**
     * Set student id of the instance.
     * @param integer $studentId Student identifier.
     */
    function setStudentId($studentId)
    {
        $this->studentId = $studentId;
    }

    /**
     * Sets the subtask id of the instance.
     * @param integer $subtaskId Subtask identifier.
     */
    function setSubtaskId($subtaskId)
    {
        $this->subtaskId = $subtaskId;
    }

    /**
     * Process parameters supplied as GET part of the request.
     */
    function processGetVars()
    {
        assignGetIfExists($this->operation, $this->rs, 'mode');
    }

    /**
     * Process parameters supplied as GET part of the request.
     */
    function processPostVars()
    {
        assignPostIfExists($this->operation, $this->rs, 'mode');
        assignPostIfExists($this->objids, $this->rs, 'objids');
        assignPostIfExists($this->dateTo, $this->rs, 'dateto');
    }

    function isActive()
    {
        /* Query the date. */
        $rs = $this->dbQuery(
            'SELECT NOW()<dateto AS active FROM extension WHERE ' .
            'subtask_id=' . $this->subtaskId . ' AND ' .
            'student_id=' . $this->studentId . ' AND ' .
            'year=' . $this->schoolyear);

        /* Default return value is 'not active'. */
        $ret = false;

        if (!empty ($rs)) {
            /* There cannot be more than a single returned record. */
            $ret = $rs[0]['active'];
        }

        return $ret;
    }

    function assignStudentList()
    {
        /* Lecture id. */
        $lectureId = SessionDataBean::getLectureId();

        /* Query the date. */
        $rs = $this->dbQuery(
            'SELECT id,surname,firstname,yearno,groupno,dateto,lecture_id ' .
            'FROM student LEFT JOIN stud_lec AS sl ON id=sl.student_id ' .
            'LEFT JOIN extension AS ex ' .
            'ON ( ex.student_id=id ' .
            'AND ex.subtask_id=' . $this->subtaskId . ' ' .
            'AND ex.year=' . $this->schoolyear . ' ) ' .
            'WHERE sl.lecture_id=' . $lectureId . ' ' .
            'AND sl.year=' . $this->schoolyear . ' ' .
            'ORDER BY surname,firstname,yearno,groupno');

        $this->_smarty->assign('studentList', $rs);
    }

    function assignSubtaskList()
    {
        /* Lecture id. */
        $lectureId = SessionDataBean::getLectureId();

        /* Get a list of subtasks for the lecture and convert it to the list
           of subtask ids. */
        $subtaskBean = new SubtaskBean (NULL, $this->_smarty, NULL, NULL);
        $subtaskList = $subtaskBean->getForLecture($lectureId, array(
            TT_WEEKLY_FORM,
            TT_WEEKLY_SIMU,
            TT_WEEKLY_ZIP,
            TT_WEEKLY_PDF,
            TT_WEEKLY_TF,
            TT_LECTURE_PDF,
            TT_SEMESTRAL));
        self::dumpVar('subtaskList', $subtaskList);
        $subtaskIds = array2ToDBString($subtaskList, 'id');

        /* Query the extension dates. */
        $rs = $this->dbQuery(
            'SELECT id,type,title,ttitle,position,dateto ' .
            'FROM subtask LEFT JOIN extension AS ex ' .
            'ON ( id=ex.subtask_id AND ' .
            'ex.student_id=' . $this->studentId . ' )' .
            'WHERE id IN (' . $subtaskIds . ') ' .
            'ORDER BY position,title');

        $this->assign('subtaskList', $rs);
    }

    function doAdmin()
    {
        /* Fetch the operation type and check it for correctness. */
        $this->processGetVars();

        switch ($this->operation) {
            case self::EXTEND_SUBTASK:
                /* Id of the bean corresponds to the id of the subtask we want
                   to change deadlines for. */
                $this->setSubtaskId($this->id);
                $sb = new SubtaskBean ($this->subtaskId, $this->_smarty, NULL, NULL);
                $sb->assignSingle();

                /* Query the list of students for this lecture and combine it
                   with existing deadline extenstion data. */
                $this->assignStudentList();

                /* And modify the template name. */
                $this->object .= self::TEMPLATE_SUBTASK;
                break;

            case self::EXTEND_STUDENT:
                /* Id of the bean corresponds to the id of the student we want
                   to change deadlines for. */
                $this->setStudentId($this->id);
                $sb = new StudentBean ($this->studentId, $this->_smarty, NULL, NULL);
                $sb->assignSingle();

                /* Query the list of subtasks that the student in questions has
                   to fullfill. */
                $this->assignSubtaskList();

                /* And modify the template name. */
                $this->object .= self::TEMPLATE_STUDENT;
                break;
            default:
                throw new Exception ('Unsupported operation mode!');
        }

        /* Pass the operation mode */
        $this->assign('mode', $this->operation);
    }

    function doEdit()
    {
        /* POST variable `objid` contains a list of student ids that were
           marked for password (re)generation. */
        $this->processPostVars();

        /* The fuction shall provide a list of students or subtasks. The list
           generator will need a list of student ids or subtask ids as an
           input. The array of object identifiers uses contains `on` elements
           only, indexed by the corresponding id. */
        $ids = array_keys($this->objids);
        self::dumpVar('ids', $ids);

        switch ($this->operation) {
            case self::EXTEND_SUBTASK:
                /* Id of the bean corresponds to the id of the subtask we want
                   to change deadlines for. */
                $this->setSubtaskId($this->id);
                $sb = new SubtaskBean ($this->subtaskId, $this->_smarty, NULL, NULL);
                $sb->assignSingle();

                /* Use an instance of StudentBean to assign a list of students
                      to Smarty variable `studentList`. */
                $sb = new StudentBean (NULL, $this->_smarty, NULL, NULL);
                $sb->assignStudentIdList($ids);

                /* And modify the template name. */
                $this->object .= self::TEMPLATE_SUBTASK;

                break;

            case self::EXTEND_STUDENT:
                /* Id of the bean corresponds to the id of the student we want
                   to change deadlines for. */
                $this->setStudentId($this->id);
                $sb = new StudentBean ($this->studentId, $this->_smarty, NULL, NULL);
                $sb->assignSingle();

                /* Use an instance of SubtaskBean to assign a list of subtasks
                      to Smarty variable `subtaskList`. */
                $sb = new SubtaskBean (NULL, $this->_smarty, NULL, NULL);
                $sb->assignFullSubtaskList($ids);

                /* And modify the template name. */
                $this->object .= self::TEMPLATE_STUDENT;

                break;

            default:
                throw new Exception ('Unsupported operation mode!');
        }

        /* Pass the operation mode */
        $this->assign('mode', $this->operation);
    }

    function doSave()
    {
        /* POST variable `objid` contains a list of student ids that were
           marked for password (re)generation. */
        $this->processPostVars();

        /* POST variable `dateto` contains the date of deadline extension. */
        if (empty ($this->dateTo)) {
            /* Empty date does us no good. */
            $this->action = 'e_date';
            return;
        }

        /* Convert the date from the Czech notation to the SQL format. */
        $this->dateTo = czechToSQLDateTime(
                trimStrip($this->dateTo)) .
            " " . SubtaskDatesBean::SUBTASK_LIMIT_TIME;

        /* The fuction shall provide a list of students or subtasks. The list
           generator will need a list of student ids or subtask ids as an
           input. The array of object identifiers uses contains `on` elements
           only, indexed by the corresponding id. */
        $this->objids = array_keys($this->objids);
        self::dumpVar('$this->objids', $this->objids);

        switch ($this->operation) {
            case self::EXTEND_SUBTASK:
                /* Id of the bean corresponds to the id of the subtask we want
                   to change deadlines for. */
                $this->setSubtaskId($this->id);
                $sb = new SubtaskBean ($this->subtaskId, $this->_smarty, NULL, NULL);
                $sb->assignSingle();

                /* Use an instance of StudentBean to assign a list of students
                      to Smarty variable `studentList`. */
                $sb = new StudentBean (NULL, $this->_smarty, NULL, NULL);
                $sb->assignStudentIdList($this->objids);

                /* And modify the template name. */
                $this->object .= self::TEMPLATE_SUBTASK;

                break;

            case self::EXTEND_STUDENT:
                /* Id of the bean corresponds to the id of the student we want
                   to change deadlines for. */
                $this->setStudentId($this->id);
                $sb = new StudentBean ($this->studentId, $this->_smarty, NULL, NULL);
                $sb->assignSingle();

                /* Use an instance of SubtaskBean to assign a list of subtasks
                      to Smarty variable `subtaskList`. */
                $sb = new SubtaskBean (NULL, $this->_smarty, NULL, NULL);
                $sb->assignFullSubtaskList($this->objids);

                /* And modify the template name. */
                $this->object .= self::TEMPLATE_STUDENT;

                break;

            default:
                throw new Exception ('Unsupported operation mode!');
        }

        /* The dbReplace() method expects that the objids, operation, and
           studentId or subtaskId have been ppropriately set. */
        $this->dbReplace();

        /* Export also `dateto`. */
        $this->assign('dateto', $this->dateTo);
    }
}

?>
