<?php

class NewsBean extends DatabaseBean
{
    const NEWS_exercise = 1;
    const NEWS_LECTURE = 2;
    const NEWS_LECTURER = 3;
    const NEWS_ALLLCTEX = 4;
    const NEWS_DISPLAY_COUNT = 5; // last entry

    var $title;
    var $text;
    var $author_id;
    var $type;
    var $object_id;
    var $datefrom;
    var $dateto;

    function _setDefaults()
    {
        $this->title = '';
        $this->text = '';
        $this->author_id = 0;
        $this->type = 0;
        $this->object_id = 0;

        /* 'datefrom' is the current date and time. */
        $this->datefrom = date("Y-m-d H:i");

        /* 'dateto' is the end of this term - it is a bit difficult
           to find it out programatically, so let's call a utility function. */
        $endTime = termEnd();
        $this->dateto = date("Y-m-d H:i", $endTime);

        /* And set $this->rs to actual values of all visible variables. */
        $this->_update_rs();
    }

    function _getNewsTypes()
    {
        return array(
            self::NEWS_exercise => 'Novinka k jednotlivému cvičení',
            self::NEWS_ALLLCTEX => 'Novinka ke všem cvičením předmětu',
            self::NEWS_LECTURE => 'Novinka k předmětu',
            self::NEWS_LECTURER => 'Novinka k učiteli',
        );
    }

    function _getNewsIcons()
    {
        return array(
            self::NEWS_exercise => 'news-lctoneexe.gif',
            self::NEWS_ALLLCTEX => 'news-lctallexe.gif',
            self::NEWS_LECTURE => 'news-lecture.gif',
            self::NEWS_LECTURER => 'news-lecturer.gif',
        );
    }

    /* Constructor */
    function __construct($id, &$smarty, $action, $object)
    {
        /* Call parent's constructor first */
        parent::__construct($id, $smarty, "news", $action, $object);
        /* Initialise new properties to their default values. */
        $this->_setDefaults();
    }

    function dbReplace()
    {
        DatabaseBean::dbQuery(
            "REPLACE news VALUES ("
            . $this->id . ",'"
            . mysql_escape_string($this->title) . "','"
            . mysql_escape_string($this->text) . "',"
            . $this->author_id . ","
            . $this->type . ","
            . $this->object_id . ",'"
            . mysql_escape_string($this->datefrom) . "','"
            . mysql_escape_string($this->dateto) . "')"
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
        $this->text = vlnka(stripslashes($this->rs['text']));
        $this->author_id = $this->rs['author_id'];
        $this->type = $this->rs['type'];
        $this->object_id = $this->rs['object_id'];
        $this->datefrom = $this->rs['datefrom'];
        $this->dateto = $this->rs['dateto'];
        /* Write the updated 'title' and 'text' to '$this->rs' so that we
           will display the updated version. */
        $this->rs['title'] = $this->title;
        $this->rs['text'] = $this->text;
    }

    /* Assign POST variables to internal variables of this class and
       remove evil tags where applicable. We shall probably also remove
       evil attributes et cetera, but this will be done later if ever. */
    function processPostVars()
    {
        $this->id = (integer)$_POST['id'];
        $this->title = trimStrip($_POST['title']);
        $this->text = trimStrip($_POST['text']);
        $this->type = (integer)$_POST['type'];
        $this->object_id = (integer)$_POST['object_id'];
        $this->datefrom = czechToSQLDateTime(trimStrip($_POST['datefrom']));
        $this->dateto = czechToSQLDateTime(trimStrip($_POST['dateto']));
        /* Handle user id. If someone changes the news record, she or he
           will automatically take over the ownership. */
        if (SessionDataBean::getUserRole() == USR_ADMIN) {
            $authorId = $_POST['author_id'];
            if (empty ($authorId)) {
                $this->author_id = 0;
            } else {
                $this->author_id = (integer)$authorId;
            }
        } else {
            $this->author_id = SessionDataBean::getUserId();
        }
    }

    function _getFullList($where = '')
    {
        $rs = DatabaseBean::dbQuery(
            "SELECT * FROM news" . $where . " ORDER BY datefrom DESC,dateto DESC,title");
        if (isset ($rs)) {
            foreach ($rs as $key => $val) {
                $rs[$key]['title'] = vlnka(stripslashes($val['title']));
                $rs[$key]['text'] = vlnka(stripslashes($val['text']));
            }
        }
        return $rs;
    }

    /* Fill in values needed for $newsTypeSelect structure. These values are:
       'options' array containing particular news type options, and 'loop'
       array, indexed by the same keys as 'options' and containing data
       for particular object ids that the news type can be related to. */
    function assignTypeSelectData()
    {
        /* Get the lecture id. */
        $lectureId = SessionDataBean::getLectureId();

        $options = $this->_getNewsTypes();
        $loop = array();
        foreach ($options as $ok => $ov) {
            $map = array();
            switch ($ok) {
                case self::NEWS_exercise:
                    $bean = new ExerciseBean (0, $this->_smarty, "x", "x");
                    $map = $bean->getSelectMap($lectureId, $this->schoolyear);
                    $rowId = 'exercise';
                    $title = 'Cvičení';
                    break;
                case self::NEWS_LECTURE:
                case self::NEWS_ALLLCTEX:
                    $bean = new LectureBean ($lectureId, $this->_smarty, "x", "x");
                    $map = $bean->getSelectMap();
                    $rowId = ($ok == self::NEWS_LECTURE) ? 'lecture' : 'all_lct_ex';
                    $title = 'Předmět';
                    break;
                case self::NEWS_LECTURER:
                    $bean = new LecturerBean (0, $this->_smarty, "x", "x");
                    $map = $bean->getSelectMap($lectureId);
                    $rowId = 'lecturer';
                    $title = 'Učitel';
                    break;
                default:
                    return;
            }
            $sel = array();
            $sel['rowid'] = $rowId;
            $sel['title'] = $title;
            $sel['options'] = $map;
            $loop[$ok] = $sel;
        }

        $nts = array();
        $nts['options'] = $options;
        $nts['loop'] = $loop;

        $this->_smarty->assign('newsTypeSelect', $nts);
    }

    /* Assign a list of valid news of the given type. */
    function assignNewsForTypes($lectureId = 0, $lecturerId = 0, $exerciseId = 0, $excLctId = 0)
    {
        // $dbList = arrayToDBString ( $taskList );

        $where = '';
        $doOR = FALSE;

        if ($lectureId > 0) {
            $where .= "( type=" . self::NEWS_LECTURE . " AND object_id=" . $lectureId . ")";
            $doOR = TRUE;
        }
        if ($exerciseId > 0) {
            if ($doOR) $where .= " OR ";
            $where .= "( type=" . self::NEWS_exercise . " AND object_id=" . $exerciseId . ")";
            $doOR = TRUE;
        }
        if ($lecturerId > 0) {
            if ($doOR) $where .= " OR ";
            $where .= "( type=" . self::NEWS_LECTURER . " AND object_id=" . $lecturerId . ")";
            $doOR = TRUE;
        }
        if ($excLctId > 0) {
            if ($doOR) $where .= " OR ";
            $where .= "( type=" . self::NEWS_ALLLCTEX . " AND object_id=" . $excLctId . ")";
            $doOR = TRUE;
        }

        if ($doOR) $where = " AND (" . $where . ")";

        $where = " WHERE datefrom<=NOW() AND dateto>=NOW()" . $where;

        $rs = DatabaseBean::dbQuery(
            "SELECT * FROM news" . $where . " " .
            "ORDER BY datefrom DESC,dateto DESC, title " .
            "LIMIT " . self::NEWS_DISPLAY_COUNT);

        if (isset ($rs)) {
            /* Fetch the mapping author_id -> user record */
            $userBean = new UserBean (0, $this->_smarty, "", "");
            $userMap = $userBean->getMap();

            foreach ($rs as $key => $val) {
                $rs[$key]['title'] = vlnka(stripslashes($val['title']));
                $rs[$key]['text'] = vlnka(stripslashes($val['text']));

                $userRec = $userMap[$val['author_id']];
                $rs[$key]['author'] = $userRec;
            }
        }

        $this->_smarty->assign('newsList', $rs);
        return $rs;
    }

    function _whereForCurrentLecture()
    {
        /* Get the lecture id. */
        $lectureId = SessionDataBean::getLectureId();

        /* WHERE string for the lecture news. */
        $where = " WHERE (type=" . self::NEWS_LECTURE .
            " AND object_id=" . $lectureId . ")";
        /* Connect it to the string for 'all exercises for this lecture'. */
        $where .= " OR (type=" . self::NEWS_ALLLCTEX .
            " AND object_id=" . $lectureId . ")";

        /* Get the list of exercises for this lecture. */
        $eb = new ExerciseBean (0, $this->_smarty, "", "");
        $exerciseList = $eb->getexercisesForLecture($lectureId);

        $this->dumpVar('excersisteList', $exerciseList);

        $where .= " OR (type=" . self::NEWS_exercise .
            " AND object_id IN (" .
            array2ToDBString($exerciseList, 'id') . "))";

        /* Get the list of lecturers for this lecture. */
        $lecturerList = $eb->getexerciseLecturersForLecture($lectureId);
        $this->dumpVar('lecturerList', $lecturerList);

        $where .= " OR (type=" . self::NEWS_LECTURER .
            " AND object_id IN (" .
            array2ToDBString($lecturerList, 'lecturer_id') . "))";

        return $where;
    }

    /**
     * Assign a full list of news records for the current lecture.
     * The list will contain all lecture-related news, all news that
     * are bound to particular exercises for this lecture, all news
     * that are bound to lecturers of this lecture.
     */
    function assignFull($queryAuthors = false)
    {
        /* Construct the WHERE clause. */
        $where = $this->_whereForCurrentLecture();

        /* Query the news. */
        $rs = $this->_getFullList($where);

        if ($queryAuthors && isset ($rs)) {
            /* Fetch the mapping user_id -> user record */
            $userBean = new UserBean (0, $this->_smarty, "", "");
            $userMap = $userBean->getMap();

            /* Icon types */
            $icons = $this->_getNewsIcons();
            $alts = $this->_getNewsTypes();

            foreach ($rs as $key => $val) {
                $userRec = $userMap[$val['author_id']];
                $rs[$key]['author'] = $userRec;

                $iType = $val['type'];
                $rs[$key]['i_src'] = $icons[$iType];
                $rs[$key]['i_alt'] = $alts[$iType];
            }
        }

        $this->_smarty->assign('fullNewsList', $rs);
        return $rs;
    }

    /* Assign a single news record. */
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
        $this->_smarty->assign('news', $this->rs);
    }

    /* -------------------------------------------------------------------
       HANDLER: ADMIN
       ------------------------------------------------------------------- */
    function doAdmin()
    {
        /* Get the list of all news entries. */
        $this->assignFull(true);
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
        /* Assign all data needed for generating <select> inputs. */
        $this->assignTypeSelectData();
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
