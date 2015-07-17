<?php

class FormAssignmentBean extends DatabaseBean
{

    /* Ordering constants. */
    const FA_ORDER_BY_LOGIN = 1;
    const FA_ORDER_BY_NAME = 2;

    var $subtask_id;
    var $assignment_id;
    var $part;
    var $count;
    var $a, $b, $c, $d, $e, $f;
    var $regenerate;
    var $onlynew;
    var $catalogue;
    var $copysub;

    /* Fill in reasonable defaults. */
    function _setDefaults()
    {
        $this->subtask_id = $this->rs['subtask_id'] = 0;
        $this->assigmnent_id = $this->rs['assignment_id'] = 0;
        $this->part = $this->rs['part'] = '';
        $this->count = $this->rs['count'] = 0;

        $this->a = $this->rs['a'] = 0;
        $this->b = $this->rs['b'] = 0;
        $this->c = $this->rs['c'] = 0;
        $this->d = $this->rs['d'] = 0;
        $this->e = $this->rs['e'] = 0;
        $this->f = $this->rs['f'] = 0;

        $this->copysub = 0;
    }

    /* Constructor */
    function __construct($id, & $smarty, $action, $object)
    {
        /* Call parent's constructor first */
        parent::__construct($id, $smarty, "formassignmnt", $action, $object);
        /* Initialise new properties to their default values. */
        $this->_setDefaults();
    }

    function dbReplace()
    {
        DatabaseBean :: dbQuery("DELETE FROM formassignmnt WHERE " . "subtask_id=" . $this->subtask_id . " AND " . "assignmnt_id=" . $this->assignment_id . " AND part='" . mysql_escape_string($this->part) . "'");

        DatabaseBean :: dbQuery("REPLACE formassignmnt VALUES (" . $this->subtask_id . "," . $this->assignment_id . ",'" . mysql_escape_string($this->part) . "'," . $this->count . "," . $this->a . "," . $this->b . "," . $this->c . "," . $this->d . "," . $this->e . "," . $this->f . ")");
    }

    function dbQuerySingle()
    {
    }

    /**
     * Get the full list of records corresponding to the given WHERE clause.
     * If `$where` is empty, returns the full list of all form assignments.
     */
    function _getFullList($where = '')
    {
        $rs = DatabaseBean :: dbQuery("SELECT * FROM formassignmnt " . $where . " ORDER BY subtask_id,assignmnt_id,part");

        return $rs;
    }

    /**
     * Process POST variables.
     */
    function processPostVars()
    {
        $this->subtask_id = $_POST['subtask_id'];
    }

    function processGetVars()
    {
        assignGetIfExists($this->regenerate, $this->rs, 'regenerate');
        assignGetIfExists($this->catalogue, $this->rs, 'catalogue');
        assignGetIfExists($this->onlynew, $this->rs, 'onlynew');
        assignGetIfExists($this->copysub, $this->rs, 'copysub');
    }

    /**
     * Fetch a complete list of assigments for a list of subtasks.
     */
    function getFullSubtaskList($subtaskList)
    {
        $dbList = arrayToDBString($subtaskList);
        $rs = $this->_getFullList(" WHERE subtask_id IN (" . $dbList . ")");
        return $rs;
    }

    /**
     * Get the number of assignment parts for the guiven subtask.
     */
    function getParts($subtaskId)
    {
        $rs = DatabaseBean :: dbQuery(
            "SELECT part FROM formassignmnt WHERE subtask_id=" .
            $subtaskId . " GROUP BY part"
        );
        return $rs;
    }

    /**
     * Match the submitted solution with the solution in database.
     */
    function matchSolution($assignmntId, $part, $type, $a, $b, $c, $d, $e, $f, $g = NULL)
    {
        $rs = $this->dbQuery("SELECT * FROM formassignmnt WHERE "
            . "subtask_id=" . $this->id . " AND "
            . "assignmnt_id=" . $assignmntId . " AND "
            . "part='" . $part . "'");
        $rs = $rs[0];
        $this->dumpVar('rs_solution', $rs);
        $this->dumpVar('task type', $type);

        $match = 0;
        if ($type == TT_WEEKLY_FORM) {
            if ($rs['d'] == 0) {
                if ($a == $rs['a'] || $rs['a'] == 0)
                    $match++;
                if ($b == $rs['b'] || $rs['b'] == 0)
                    $match++;
                if ($c == $rs['c'] || $rs['c'] == 0)
                    $match++;
                if ($e == $rs['e'] || $rs['e'] == 0)
                    $match++;
                if ($f == $rs['f'] || $rs['f'] == 0)
                    $match++;
                $this->dumpVar('match d==0 A', $match);
                $match = ($match / 5.0) * 6.0;
                $this->dumpVar('match d==0 B', $match);
            } else if ($rs['c'] == 0) {
                /* Partial fraction in the form of (Ax+D)/(x+B)+E/(x+F), no
                 * swapping of variables can occur here. */
                if ($a == $rs['a'] || $rs['a'] == 0)
                    $match++;
                if ($b == $rs['b'] || $rs['b'] == 0)
                    $match++;
                if ($d == $rs['d'] || $rs['d'] == 0)
                    $match++;
                if ($e == $rs['e'] || $rs['e'] == 0)
                    $match++;
                if ($f == $rs['f'] || $rs['f'] == 0)
                    $match++;
                $this->dumpVar('match c==0 A', $match);
                $match = ($match / 5.0) * 6.0;
                $this->dumpVar('match c==0 B', $match);
            } else {
                /* Partial fraction in the form of A/(x+B)+C/(x+D)+E/(x+F) and we
                 * do not know if AB, CD, and EF have not been swapped. */
                $cand = array(
                    array(
                        $b,
                        $a
                    ),
                    array(
                        $d,
                        $c
                    ),
                    array(
                        $f,
                        $e
                    )
                );
                $sols = array(
                    array(
                        $rs['b'],
                        $rs['a']
                    ),
                    array(
                        $rs['d'],
                        $rs['c']
                    ),
                    array(
                        $rs['f'],
                        $rs['e']
                    )
                );
                /* Loop over candidate pairs and try to match them with solutions. */
                foreach ($cand as $cv) {
                    /* Look for the given solution pair in the vector of real
                     * solutions. */
                    $idx = array_search($cv, $sols);
                    if ($idx !== false) {
                        /* Delete the existing pair from the real solutions. */
                        unset ($sols[$idx]);
                        $match += 2;
                    }
                }
            }
        } elseif ($type == TT_WEEKLY_TF) {
            /* Candidate pairs. */
            $cand = array(
                array($a, $b),
                array($c, $d),
                array($e, $f)
            );

            /* Possible solution pairs. */
            $sols = array(
                array($rs['a'], $rs['b']),
                array($rs['a'], -$rs['b']),
                array($rs['c'], 0)
            );

            $this->dumpVar('tf_cand', $cand);
            $this->dumpVar('tf_sols', $sols);
            $this->dumpVar('tf_g', $g);

            /* Loop over candidate pairs and try to match them with solutions. */
            foreach ($cand as $cv) {
                /* Look for the given solution pair in the vector of real
                 * solutions. */
                $idx = array_search($cv, $sols);
                if ($idx !== false) {
                    /* Delete the existing pair from the real solutions. */
                    unset ($sols[$idx]);
                    /* Add points. */
                    $match += 2;
                }
            }

            /* And match the stability answer. */
            if ($rs['d'] == $g) $match++;
        }
        return $match;
    }

    /**
     * Assign the list of assignment parts to Smarty variable 'parts'.
     */
    function assignParts($subtaskId)
    {
        $rs = $this->getParts($subtaskId);
        $this->_smarty->assign('parts', $rs);
        return $rs;
    }

    /**
     * Extend subtask list with the count of subtask parts and the counts of
     * available and generated assignments.
     */
    function updateSubtaskList($subtaskList)
    {
        /* Get the number of generated assignments for subtasks that were
           passed as parameters. */
        $asbean = new AssignmentsBean (0, $this->_smarty, NULL, NULL);
        $gen = $asbean->getNumberOfAssignments($subtaskList);

        /* Select the information about subtasks with assignments stored in the
           form database. */
        $rs = $this->dbQuery(
            "SELECT subtask_id,part,COUNT(*) AS mc " .
            "FROM formassignmnt GROUP BY subtask_id,part"
        );
        /* Result may be empty, in that case we will not update anything. */
        if (!empty ($rs)) {
            $num = array();
            foreach ($rs as $key => $val) {
                $num[$val['subtask_id']] = $val['mc'];
            }
            foreach ($subtaskList as $key => $val) {
                /* Temporary index. */
                $tid = $val['id'];
                $subtaskList[$key]['count'] = array_key_exists($tid, $num) ? $num[$tid] : 0;
                $subtaskList[$key]['generated'] = array_key_exists($tid, $gen) ? $gen[$tid] : 0;
            }
        }

        return $subtaskList;
    }

    /**
     *  Reduce student list to students without assignment.
     */
    function getReducedStudentIdList($subtaskId, $lectureId)
    {
        /* Get a list containing 'student_id' elements holding the
           student ids. */
        $rs = $this->dbQuery(
            "SELECT sl.student_id FROM stud_lec AS sl " .
            "LEFT JOIN assignmnts AS ag ON " .
            "( ag.subtask_id=" . $subtaskId . " " .
            "AND ag.year=sl.year " .
            "AND sl.student_id=ag.student_id ) WHERE " .
            "sl.lecture_id=" . $lectureId . " AND " .
            "sl.year=" . $this->schoolyear . " AND " .
            "ag.assignmnt_id IS NULL"
        );

        /* Result array. */
        $ret = array();

        /* Transform the above list into an array of student ids
           as expected by generateAssignments(). */
        if (!empty ($rs)) {
            foreach ($rs as $val) {
                $ret[] = $val['student_id'];
            }
        }

        $this->dumpVar('reduced rs', $ret);

        return $ret;
    }

    /**
     *  Generate a single file containing all assignments with solutions
     *  ordered by student login.
     */
    function generateAssignmentCatalogue($subtaskId, $orderType)
    {
        if ($orderType == self::FA_ORDER_BY_NAME) {
            $orderStr = "st.surname,st.firstname";
        } else {
            $orderStr = "st.login";
        }

        /* Get the code of the subtask id. */
        $subtaskBean = new SubtaskBean(0, $this->_smarty, "", "");
        $sCode = $subtaskBean->getSubtaskCode($subtaskId);

        /* Construct the file bean that implements also all operations on 
         assigment files. */
        $fileBean = new FileBean(0, $this->_smarty, "", "");

        /* Construct the assignments bean that interconnects the subtask
           and studetna dn dile data. */
        $assignmentsBean = new AssignmentsBean(0, $this->_smarty, "", "");

        /* Select the assignments for this subtask ans schoolyear, combine
           them with student data. */
        $stYear = SessionDataBean::getSchoolYear();
        $stagList = DatabaseBean::dbQuery(
            "SELECT st.login,st.firstname,st.surname,st.yearno,st.groupno,assignmnt_id " .
            "FROM assignmnts AS ag " .
            "LEFT JOIN student AS st ON ag.student_id=st.id " .
            "WHERE subtask_id=" . $subtaskId . " AND year=" . $stYear . " " .
            "ORDER BY " . $orderStr . ";"
        );

        /* Change to the directory where files shall be generated. */
        $tGeneBase = "generated/" . $sCode . "/";
        $base = CMSFILES . "/" . $tGeneBase;
        @ mkdir($base);
        chdir($base);

        /* Write the template tex file. */
        $cmsFileBase = $tGeneBase . $sCode . "_inc";
        $filename = CMSFILES . "/" . $cmsFileBase . ".tex";
        $handle = fopen($filename, "w");

        foreach ($stagList as $key => $val) {
            /* Prepare the student data. */
            $u8name = $val['firstname'] . " " . $val['surname'];
            $name = $val['login'] . " (" . iconv("utf-8", "windows-1250", $u8name) . ")";
            $group = $val['yearno'] . "/" . $val['groupno'];
            $id = sprintf("%05d", $val['assignmnt_id']);
            /* Transform the template into assignment file. */
            $texstr = "\\{$sCode}sol{{$id}}{{$name}}{{$group}}\n";
            fwrite($handle, $texstr);
        }

        fclose($handle);

        /* This is the base file that includes the file created above. */
        $tBaseDir = CMSFILES . "/assignments/" . $sCode . "/";
        $filename = $tBaseDir . $sCode . "_catalogue.tex";

        /* LaTeX it. */
        $ret = system("TEXINPUTS=`kpsexpand -p tex`:$tBaseDir pdflatex -interaction=batchmode " . $filename . " > /dev/null ");
        //$ret = system ( "TEXINPUTS=`kpsexpand -p tex`:$tBaseDir pdflatex ".$filename." " );
        echo "<!-- " . $ret . "-->\n";
        //system ( 'rm -f *.tex *.log *.aux');
    }

    /**
     *  In case of an error, this will generate new files for the given
     *  subtask using already generated assignment ids.
     */
    function regenerateAssignments($subtaskId)
    {
        /* Get the code of the subtask id. */
        $subtaskBean = new SubtaskBean(0, $this->_smarty, "", "");
        $sCode = $subtaskBean->getSubtaskCode($subtaskId);

        /* Construct the file bean that implements also all operations on
         assigment files. */
        $fileBean = new FileBean(0, $this->_smarty, "", "");

        /* Construct the assignments bean that interconnects the subtask
           and studetna dn dile data. */
        $assignmentsBean = new AssignmentsBean(0, $this->_smarty, "", "");

        /* Select the assignments for this subtask and the current year from
           the database. */
        $assignmentList = $this->dbQuery(
            "SELECT * FROM assignmnts WHERE subtask_id=" . $subtaskId .
            " AND year=" . $this->schoolyear
        );

        /* Read the template. */
        $tBaseDir = CMSFILES . "/assignments/" . $sCode . "/";
        $tGeneBase = "generated/" . $sCode . "r/";
        $tFileName = $tBaseDir . $sCode . ".tex";
        $handle = fopen($tFileName, "r");
        $templatestr = fread($handle, filesize($tFileName));
        fclose($handle);

        /* Change to the directory where files shall be generated. */
        $base = CMSFILES . "/" . $tGeneBase;
        @ mkdir($base);
        chdir($base);

        /* Erase all remaining files in the directory. */
        //system ( 'rm -f *');

        foreach ($assignmentList as $key => $val) {
            /* Get the student data. */
            $rs = $this->dbQuery(
                "SELECT * FROM student WHERE id=" . $val['student_id']);
            if (empty($rs)) {
                trigger_error(
                    "No record for student " . $val['student_id'] . " in `student` table?");
                continue;
            }
            /* Returned result set is an array, therefore we have to copy
               its first (and only) element out. */
            $sval = $rs[0];
            /* Prepare translation table. */
            $codes = array(
                "@DATE@",
                "@NAME@",
                "@GROUP@",
                "@ID@"
            );
            $date = date("d.m.Y");
            $u8name = $sval['firstname'] . " " . $sval['surname'];
            $name = iconv("utf-8", "windows-1250", $u8name);
            $group = $sval['yearno'] . "/" . $sval['groupno'];
            $id = sprintf("%05d", $val['assignmnt_id']);
            $replc = array(
                $date,
                $name,
                $group,
                $id
            );

            /* Transform the template into assignment file. */
            $texstr = str_replace($codes, $replc, $templatestr);

            /* Write the template tex file. */
            $cmsFileBase = $tGeneBase . $sCode . "_" . $id;
            $filename = CMSFILES . "/" . $cmsFileBase . ".tex";
            $handle = fopen($filename, "w");
            fwrite($handle, $texstr);
            fclose($handle);
            echo "<!-- written " . $filename . ", base dir " . $tBaseDir . " -->\n";

            /* And LaTeX it. */
            $ret = system("TEXINPUTS=`kpsexpand -p tex`:$tBaseDir pdflatex -interaction=batchmode " . $filename . " > /dev/null ");
            //$ret = system ( "TEXINPUTS=`kpsexpand -p tex`:$tBaseDir pdflatex ".$filename." " );
            echo "<!-- " . $ret . "-->\n";
            //system ( 'rm -f *.tex *.log *.aux');
        }
    }

    /**
     * Create the files with assignment text (one for each student), possibly
     * generating only assignments for new students (in case that $isUpdate is
     * true) and possibly not generating a new random set of assignments, but
     * copying the ids of assignments from another assignment (this might make
     * sense if we have a sequence of assignments where students work on the
     * same problem from different viewpoints).
     */
    function generateAssignments($subtaskId, $studentList, $isUpdate, $fromSubtask)
    {
        /* Get the code of the subtask id. */
        $subtaskBean = new SubtaskBean(0, $this->_smarty, "", "");
        $sCode = $subtaskBean->getSubtaskCode($subtaskId);

        /* Construct the file bean that implements also all operations on
         assigment files. */
        $fileBean = new FileBean(0, $this->_smarty, "", "");

        /* Construct the assignments bean that interconnects the subtask
           and students and the file data. */
        $assignmentsBean = new AssignmentsBean(0, $this->_smarty, "", "");

        /* The number of assignments to generate is given by the number of
         students in the studentList. */
        $numAssignments = count($studentList);

        /* Check the mode of new assignment selection. */
        if ($fromSubtask > 0) {
            /* Copy records from assignments of another task. */
            $rs = $assignmentsBean->getAssignmentList($fromSubtask);
            /* We have to transform the list into a list indexed by student
               id. */
            $studentAssignments = array();
            foreach ($rs as $val) {
                $studentAssignments[$val['student_id']] = $val['assignmnt_id'];
            }
            self::dumpVar('studentAssignments', $studentAssignments);
        } else {
            /* Randomly select a number of records from the database. */
            $rs = DatabaseBean :: dbQuery("SELECT DISTINCT(assignmnt_id) FROM formassignmnt WHERE " .
                "subtask_id=" . $subtaskId . " ORDER BY count,RAND() " .
                "LIMIT " . $numAssignments);
            self::dumpVar("assignment ids", $rs);
        }

        /* Read the template. */
        $tBaseDir = CMSFILES . "/assignments/" . $sCode . "/";
        $tGeneBase = "generated/" . $sCode . "/" . $this->schoolyear . "/";
        $tFileName = $tBaseDir . $sCode . ".tex";
        $handle = fopen($tFileName, "r");
        $templatestr = fread($handle, filesize($tFileName));
        fclose($handle);

        /* Change to the directory where files shall be generated. */
        $base = CMSFILES . "/" . $tGeneBase;
        if (!is_dir($base)) mkdir($base, 0775, true);
        chdir($base);

        if (!$isUpdate) {
            /* Erase all file records for this task. */
            $fileBean->clearAssignmentFiles($subtaskId);

            /* Erase all remaining files in the directory. */
            system('rm -f *');
        }

        $pos = 0;
        foreach ($studentList as $key => $val) {
            $codes = array(
                "@DATE@",
                "@NAME@",
                "@GROUP@",
                "@ID@"
            );
            $date = date("d.m.Y");
            $u8name = $val['firstname'] . " " . $val['surname'];
            $name = iconv("utf-8", "windows-1250", $u8name);
            $group = $val['yearno'] . "/" . $val['groupno'];
            /* Allow copying assignment ids from other subtasks. */
            if ($fromSubtask > 0) {
                $id = $studentAssignments[$val['id']];
            } else {
                $id = $rs[$pos]['assignmnt_id'];
            }
            $replc = array(
                $date,
                $name,
                $group,
                $id
            );

            /* Transform the template into assignment file. */
            $texstr = str_replace($codes, $replc, $templatestr);

            /* Record the assignment id for this student. */
            $studentList[$key]['assignmnt_id'] = $id;

            /* Write the template tex file. */
            $cmsFileBase = $tGeneBase . $sCode . "_" . $id;
            $filename = CMSFILES . "/" . $cmsFileBase . ".tex";
            $handle = fopen($filename, "w");
            fwrite($handle, $texstr);
            fclose($handle);

            /* And LaTeX it. */
            $ret = system("TEXINPUTS=`kpsexpand -p tex`:$tBaseDir pdflatex -interaction=batchmode " . $filename . " > /dev/null ");
            //$ret = system ( "TEXINPUTS=`kpsexpand -p tex`:$tBaseDir pdflatex ".$filename." " );
            //echo "<!-- ".$ret."-->";
            system('rm -f *.tex *.log *.aux');

            /* Remember the student id. */
            $studentId = $val['id'];

            /* Store information about the generated file in file table. */
            $cmsFile = $cmsFileBase . ".pdf";
            $fileId = $fileBean->addFile(FT_X_ASSIGNMENT, $subtaskId, $studentId, $cmsFile, $cmsFile, "Úloha " .
                $sCode . ", příklad " . $id . ", student " . $u8name);

            /* And finally update information about this assignment in the
               assignment mapping table. */
            $assignmentsBean->setAssignment($studentId, $subtaskId, $id, $fileId);

            /* The last step is counter update - we have to increase the counter for
               all records in `formassignmnt` table with the given `$subtaskId` and
               `$assignment_id`, */
            if ($fromSubtask == 0) {
                $this->dbQuery(
                    "UPDATE formassignmnt SET count=count+1 " .
                    "WHERE subtask_id=" . $subtaskId . " " .
                    "AND assignmnt_id=" . $id
                );
            }

            /* Move to the next assigment record in the `$rs` array. */
            $pos++;
        }

    }

    function assignFull()
    {
        $rs = $this->_getFullList();
        $this->_smarty->assign('subtaskList', $rs);
        return $rs;
    }

    function assignSingle()
    {
    }

    /* -------------------------------------------------------------------
       HANDLER: SHOW
       ------------------------------------------------------------------- */
    function doShow()
    {
        /* Permission check - show() is not allowed for everyone. */
        if (!UserBean::isRoleAtLeast(SessionDataBean::getUserRole(), USR_LECTURER)) {
            /* Signalise that the current user does not have permission
               for this action. */
            $this->action = 'e_perm';
            return;
        }

        /* Process and assign additional varibles that we submitted as a part
           of a GET query. */
        $this->processGetVars();

        /* Load information of the substask. */
        $subtaskBean = new SubtaskBean($this->id, $this->_smarty, "", "");
        $subtaskBean->assignSingle();

        /* Generate new task set. First get the list of students. */
        /* Get the list of all exercises, assign it to the Smarty variable
           'exerciseList' and return it to us as well, we will need it later.
           $this->id will point to the lecture_id in this case. */
        $exerciseBean = new ExerciseBean (0, $this->_smarty, "x", "x");
        $exerciseList = $exerciseBean->assignFull(1);

        /* Get the lecture description, just to fill in some more-or-less
           useful peieces of information. */
        $lectureId = SessionDataBean::getLectureId();
        $lectureBean = new LectureBean ($lectureId, $this->_smarty, "x", "x");
        $lectureBean->assignSingle();

        if (!empty ($this->catalogue)) {
            /* Create a PDF catalogue with assignment solutions, ordered
               by student login. */
            $this->generateAssignmentCatalogue($this->id, $this->catalogue);
        } elseif (!empty ($this->regenerate)) {
            /* Create again the PDF files with assignment text (useful in
               cases where the original .tex files contain some errors). */
            $this->regenerateAssignments($this->id);
        } else {
            /* Create an instance of StudentBean that will be used to query
               information about students. */
            $studentBean = new StudentBean(0, $this->_smarty, "x", "x");

            /* Check if we shall just update assignments for students that
               were added later or generate a completely new set of assignments. */
            $isUpdate = (!empty ($this->onlynew));

            if ($isUpdate) {
                /* Create assignments only for students that have been added
                   later and do not have the assignments generated yet. */
                $ids = $this->getReducedStudentIdList($this->id, $lectureId);
                $studentList = $studentBean->assignStudentIdList($ids);
            } else {
                /* Now create an array that contains student id as an key and _index_ to
                   the $exerciseList as a value (that is, not the exercise ID, but the
                   true index into that array. */
                //$studexcBean = new StudentExerciseBean(0, $this->_smarty, "x", "x");
                //$exerciseBinding = $studexcBean->getExerciseBinding($exerciseList);
                //$this->dumpVar('exerciseBinding', $exerciseBinding);

                /* Get the list of all students. Additionally, create a field 'checked'
                   that contains text ' checked="checked"' on the position of the exercise
                   that the particular student visits, and '' otherwise. */
                $studentList = $studentBean->dbQueryStudentListForLecture($lectureId);
                //$studentList = $studentBean->assignStudentListWithExercises (
                //  $lectureId,
                //  count($exerciseList),
                //  $exerciseBinding
                //  );            	
            }

            /* Create the files with assignment text (one for each student),
               possibly generating only assignments for new students (in case
               that $isUpdate is true) and possibly not generating a new random
               set of assignments, but copying the ids of assignments from
               another assignment (this might make sense if we have a sequence
               of assignments where students work on the same problem from
               different viewpoints). */
            $this->generateAssignments($this->id, $studentList, $isUpdate,
                $this->copysub);
        }

        $this->_smarty->assign('formassignment', $this->rs);
    }

    /* -------------------------------------------------------------------
       HANDLER: ADMIN
       ------------------------------------------------------------------- */
    function doAdmin()
    {
        /* Get a lecture that this subtask is related to. */
        $lectureBean = new LectureBean($this->id, $this->_smarty, "", "");
        $lectureBean->assignSingle();
        /* Get a list of subtask types. */
        $subtaskBean = new SubtaskBean(0, $this->_smarty, "", "");
        $subtaskList = $subtaskBean->getForLecture($this->id, array(
            TT_WEEKLY_FORM,
            TT_WEEKLY_SIMU,
            TT_WEEKLY_PDF,
            TT_WEEKLY_ZIP,
            TT_WEEKLY_TF
        ));
        /* Add count and publish it. */
        $subtaskList = $this->updateSubtaskList($subtaskList);
        $this->_smarty->assign('subtaskList', $subtaskList);
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
        /* There is no edit for this type of object. User may just import
        another set of subtasks. */
        /* Get a lecture that this subtask is related to. */
        $lectureBean = new LectureBean(1, $this->_smarty, "", "");
        $lectureBean->assignSingle();
        /* Get a list of subtask types. */
        $subtaskBean = new SubtaskBean($this->id, $this->_smarty, "", "");
        $subtaskBean->assignSingle();

        /* Process and assign additional varibles that we submitted as a part
           of a GET query. */
        $this->processGetVars();

        /* Check what sub-action we were asked to perform. */
        if (!empty ($this->copysub)) {
            /* Display a list of all subtasks that may server as a source
               of "copy subtask assignments" operation. */
            $subtaskBean->assignStudentSubtaskList();
        }

        $this->_smarty->assign('formassignment', $this->rs);
    }

    /* -------------------------------------------------------------------
       HANDLER: SAVE
       ------------------------------------------------------------------- */
    function doSave()
    {
        /* Assign POST variables to internal variables of this class and
           remove evil tags where applicable. */
        $this->processPostVars();
        /* Get the description of the current subtask. */
        $subtaskBean = new SubtaskBean($this->id, $this->_smarty, "", "");
        $subtaskBean->assignSingle();
        /* Check the uploaded file. */
        if (is_uploaded_file($_FILES['assignfile']['tmp_name'])) {
            /* Upload ok, open it. */
            $handle = @ fopen($_FILES['assignfile']['tmp_name'], "r");
            if ($handle) {
                /* Can be opened, it shall be a CSV, so delete all previous data
                   from the table and parse the lines. */
                $this->dbQuery(
                    'DELETE FROM formassignmnt WHERE subtask_id=' .
                    $this->subtask_id
                );
                /* CSV loop. */
                while (!feof($handle)) {
                    $buffer = fgets($handle, 4096);
                    $trimmed = trim($buffer);

                    /* Ignore empty lines. */
                    if (empty ($trimmed))
                        continue;

                    $la = explode(";", $trimmed);
                    echo "\n<!-- la=";
                    print_r($la);
                    echo " -->";

                    /* The record has to have 8 elements. Skip it otherwise. */
                    if (count($la) != 8)
                        continue;

                    $this->assignment_id = trim($la[0], " \t\n\r\"");
                    $this->part = trim($la[1], " \t\n\r\"");
                    $this->a = trim($la[2], " \t\n\r\"");
                    $this->b = trim($la[3], " \t\n\r\"");
                    $this->c = trim($la[4], " \t\n\r\"");
                    $this->d = trim($la[5], " \t\n\r\"");
                    $this->e = trim($la[6], " \t\n\r\"");
                    $this->f = trim($la[7], " \t\n\r\"");
                    $this->count = 0;

                    $this->dbReplace();
                }

                fclose($handle);
            } else {
                /* Cannot open the file for reading. */
                $this->action = "err01";
            }
        } else {
            /* Possible file uplad attack. */
            $this->action = "err02";
        }
    }

    /* -------------------------------------------------------------------
       HANDLER: DELETE
       ------------------------------------------------------------------- */
    function doDelete()
    {
        $this->assignSingle();

        /* Get a lecture that this subtask is related to. */
        $lectureBean = new LectureBean($this->id, $this->_smarty, "", "");
        $lectureBean->assignSingle();
    }

    /* -------------------------------------------------------------------
       HANDLER: REAL DELETE
       ------------------------------------------------------------------- */
    function doRealDelete()
    {
        $this->assignSingle();
        /* Delete the record */
        DatabaseBean :: dbDeleteById();

        /* Get a lecture that this subtask is related to. */
        $lectureBean = new LectureBean($this->id, $this->_smarty, "", "");
        $lectureBean->assignSingle();
    }
}

?>
