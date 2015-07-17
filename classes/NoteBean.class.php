<?php

define ('NOTE_SINGLE_exercise', 1);
define ('NOTE_ALL_exerciseS', 2);
define ('NOTE_LECTURE', 3);

define ('NOTES_DISPLAY_COUNT', 5);

class NoteBean extends DatabaseBean
{
    var $text;
    var $type;
    var $object_id;
    var $author_id;
    var $date;

    function _setDefaults()
    {
        $this->text = $this->rs['text'] = '';
        $this->type = $this->rs['type'] = 0;
        $this->object_id = $this->rs['object_id'] = 0;
        $this->author_id = $this->rs['author_id'] = 0;
        $this->date = $this->rs['date'] = '';
    }

    function _getNoteTypes()
    {
        return array(
            NOTE_SINGLE_exercise => 'Poznámka k jednomu cvičení',
            NOTE_ALL_exerciseS => 'Poznámka ke všem cvičením',
            NOTE_LECTURE => 'Poznámka k předmětu'
        );
    }

    /* Constructor */
    function __construct($id, &$smarty, $action, $object)
    {
        /* Call parent's constructor first */
        parent::__construct($id, $smarty, "note", $action, $object);
        /* Initialise new properties to their default values. */
        $this->_setDefaults();
    }

    function dbReplace()
    {
        DatabaseBean::dbQuery(
            "REPLACE note VALUES ("
            . $this->id . ",'"
            . mysql_escape_string($this->text) . "',"
            . $this->type . ","
            . $this->object_id . ","
            . $this->author_id . ","
            . "NOW())"
        );

        $this->updateId();
    }

    function dbQuerySingle()
    {
        /* Query the data of this section (ID has been already specified) */
        DatabaseBean::dbQuerySingle();
        /* Initialize the internal variables with the data queried from the
           database. */
        $this->text = vlnka(stripslashes($this->rs['text']));
        $this->object_id = $this->rs['object_id'];
        $this->author_id = $this->rs['author_id'];
        $this->type = $this->rs['type'];
        $this->date = $this->rs['date'];
        /* Write the updated 'title' and 'text' to '$this->rs' so that we
           will display the updated version. */
        $this->rs['text'] = $this->text;
    }

    /* Assign POST variables to internal variables of this class and
       remove evil tags where applicable. We shall probably also remove
       evil attributes et cetera, but this will be done later if ever. */
    function processPostVars()
    {
        $this->id = (integer)$_POST['id'];
        $this->text = trimStrip($_POST['text']);
        $this->type = (integer)$_POST['type'];
        $this->object_id = (integer)$_POST['object_id'];
        /* The value of `author_id` will be read from session storage. */
        $this->author_id = SessionDataBean::getUserId();
        if (empty ($this->author_id)) $this->author_id = 0;
    }

    /* When calling "admin" method, the lecture identifier is specified using
       additional parameter of the URL. */
    function processGetVars()
    {
        assignGetIfExists($this->object_id, $this->rs, 'object_id');
    }

    function _getFullList($where = '')
    {
        $rs = DatabaseBean::dbQuery(
            "SELECT * FROM note" . $where . " ORDER BY date,text");
        if (isset ($rs)) {
            foreach ($rs as $key => $val) {
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
        $this->_smarty->assign('noteTypeSelect', $this->_getNoteTypes());
    }

    /* Assign a list of valid notes of the given combination of `lecture` and
       `exercise`. If some of the inputs is zero, it will be ignored.
       */
    function assignNotesForTypes(
        $lectureId,
        $exercisesForLectureId,
        $exerciseId
    )
    {
        /* Initial string representing the "where" clause of an SQL quary will
           be empty. */
        $where = '';
        $doOR = FALSE;

        /* Compose particula "where" sub-clauses and combine them. */
        if ($lectureId > 0) {
            $where .= "( type=" . NOTE_LECTURE . " AND object_id=" . $lectureId . ")";
            $doOR = TRUE;
        }
        if ($exercisesForLectureId > 0) {
            if ($doOR) $where .= " OR ";
            $where .= "( type=" . NOTE_ALL_exerciseS . " AND object_id=" . $exercisesForLectureId . ")";
            $doOR = TRUE;
        }
        if ($exerciseId > 0) {
            if ($doOR) $where .= " OR ";
            $where .= "( type=" . NEWS_SINGLE_exercise . " AND object_id=" . $exerciseId . ")";
            $doOR = TRUE;
        }

        /* If there was some change to $where, $doOR will be set to TRUE. */
        if ($doOR) $where = " WHERE (" . $where . ")";

        $rs = DatabaseBean::dbQuery(
            "SELECT * FROM note" . $where . " ORDER BY date DESC, id LIMIT " . NOTES_DISPLAY_COUNT);

        if (isset ($rs)) {
            /* Fetch the mapping user_id -> user record */
            $userBean = new UserBean (0, $this->_smarty, "", "");
            $userMap = $userBean->getMap();

            foreach ($rs as $key => $val) {
                $rs[$key]['text'] = vlnka(stripslashes($val['text']));

                $userRec = $userMap[$val['author_id']];
                $rs[$key]['author'] = $userRec;
            }
        }

        $this->_smarty->assign('noteList', $rs);
        return $rs;
    }

    /* Assign a full list of note records. */
    function assignFull()
    {
        $rs = $this->_getFullList(' WHERE object_id=' . $this->object_id);
        $this->_smarty->assign('noteList', $rs);
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
            /* Fetch possible parameters (lecture id) sent as GET parameters.
               This is useful only if $this->id is zero. */
            $this->processGetVars();
        }
        $this->_smarty->assign('note', $this->rs);
    }

    /* -------------------------------------------------------------------
       HANDLER: ADMIN
       ------------------------------------------------------------------- */
    function doAdmin()
    {
        /* In admin mode the id of this object corresponds to the identifier
           of the lecture that we do list the notes for.
           @TODO@ this is not correct, use another parameter. */
        $this->object_id = $this->id;
        $this->id = 0;

        /* Query the lecture data. */
        $lecture = new LectureBean ($this->object_id, $this->_smarty, "x", "x");
        $lecture->assignSingle();

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

        /* Assign the lecture information. */
        $lecture = new LectureBean ($this->object_id, $this->_smarty, "x", "x");
        $lecture->assignSingle();

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
