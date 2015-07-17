<?php

class LectureBean extends DatabaseBean
{
    protected $code;
    protected $title;
    protected $thanks;
    protected $syllabus;
    protected $locale;
    protected $rootsection;

    function _setDefaults()
    {
        /* Define default values for properties. */
        $this->code = "K611*";
        $this->title = "";
        $this->thanks = "";
        $this->syllabus = "";
        $this->locale = "cs";
        $this->rootsection = 0;
        /* Update the value of $this->rs. */
        $this->_update_rs();
    }

    /* Constructor */
    function __construct($id, &$smarty, $action, $object)
    {
        /* Call parent's constructor first */
        parent::__construct($id, $smarty, "lecture", $action, $object);
        /* Initialise new properties to their default values. */
        $this->_setDefaults();
    }

    /**
     * Getter function for lecture code.
     */
    function getCode()
    {
        return $this->code;
    }

    /**
     * Getter function for lecture root section.
     */
    function getRootSection()
    {
        return $this->rootsection;
    }

    /**
     * Return lecture code as stored in `rsData`.
     * Used by SessionDataBean.
     */
    static function getCodeFromData(&$rsData)
    {
        return $rsData['code'];
    }

    /**
     * Return lecture root section as stored in `rsData`.
     * Used by SessionDataBean.
     */
    static function getRootSectionFromData(&$rsData)
    {
        return $rsData['rootsection'];
    }

    function getLectureData()
    {
        return $this->rs;
    }

    function dbReplace()
    {
        /* When adding a new lecture we have to create also a top-level section
         * record. This record will be empty and we will use only its identifier
         * as a root identifier.  */
        if ($this->id == 0) {
            $secBean = new SectionBean ($this->id, $this->_smarty, "x", "x");
            $secBean->type = "root";
            $secBean->dbReplace();
            $this->rootsection = $secBean->id;
        }

        DatabaseBean::dbQuery(
            "REPLACE lecture VALUES ("
            . $this->id . ",'"
            . mysql_escape_string($this->code) . "','"
            . mysql_escape_string($this->title) . "','"
            . mysql_escape_string($this->syllabus) . "','"
            . mysql_escape_string($this->thanks) . "','"
            . mysql_escape_string($this->locale) . "',"
            . $this->rootsection . ")"
        );
    }

    function dbQuerySingle()
    {
        /* Query the data of this lecture (ID has been already specified) */
        DatabaseBean::dbQuerySingle();
        /* Initialize the internal variables with the data queried from the
           database. */
        $this->code = vlnka(stripslashes($this->rs['code']));
        $this->title = vlnka(stripslashes($this->rs['title']));
        $this->thanks = vlnka(stripslashes($this->rs['thanks']));
        $this->syllabus = vlnka(stripslashes($this->rs['syllabus']));
        $this->locale = $this->rs['locale'];
        $this->rootsection = $this->rs['rootsection'];
        /* Update the value of $this->rs. This will make the lecture data
         * available to the templating engine. */
        $this->_update_rs();
    }

    /* Assign POST variables to internal variables of this class and
       remove evil tags where applicable. We shall probably also remove
       evil attributes et cetera, but this will be done later if ever. */
    function processPostVars()
    {
        $this->id = $_POST['id'];
        $this->code = trimStrip($_POST['code']);
        $this->title = trimStrip($_POST['title']);
        $this->thanks = trimStrip($_POST['thanks']);
        $this->syllabus = trimStrip($_POST['syllabus']);
        $this->rootsection = $_POST['rootsection'];
    }

    function _dbQueryList()
    {
        return DatabaseBean::dbQuery("SELECT id, code, title, syllabus FROM lecture ORDER BY code,title");
    }

    /**
     * Return a map of lectures indexed by lecture id.
     */
    function dbQueryLectureMap()
    {
        $resultset = $this->_dbQueryList();

        $lectureMap = array();
        if (isset ($resultset)) {
            foreach ($resultset as $key => $val) {
                $id = $val['id'];
                $lectureMap[$id]['id'] = $id;
                $lectureMap[$id]['code'] = stripslashes($val['code']);
                $lectureMap[$id]['title'] = stripslashes($val['title']);
                $lectureMap[$id]['syllabus'] = stripslashes($val['syllabus']);
                $lectureMap[$id]['locale'] = stripslashes($val['locale']);
                $lectureMap[$id]['rootsection'] = $val['rootsection'];
            }
        }

        return $lectureMap;
    }

    function getSelectMap()
    {
        /* Initialise the map array. */
        $lectureMap = array();
        /* If the bean has been initialised with `id` equal to zero,
           we will list all lecures. Otherwise we will list just the
           lecture corresponding to the bean `id` value. */
        if ($this->id == 0) {
            $resultset = $this->_dbQueryList();
            $lectureMap[0] = "Vyberte ze seznamu ...";
        } else {
            $this->dbQuerySingle();
            $resultset[0] = $this->rs;
        }
        if (isset ($resultset)) {
            foreach ($resultset as $key => $val) {
                $lectureMap[$val['id']] = stripslashes($val['code']) . " (" . stripslashes($val['title']) . ")";
            }
        }
        // Use assignSelectMap instead.
        // $this->_smarty->assign ( 'lectureSelect', $lectureMap );
        return $lectureMap;
    }

    function assignSelectMap()
    {
        $lectureMap = $this->getSelectMap();
        $this->_smarty->assign('lectureSelect', $lectureMap);
        return $lectureMap;
    }

    /* Assign a full list of lectures to 'lectureList' */
    function assignFull()
    {
        $resultset = $this->_dbQueryList();

        self::dumpVar('resultset', $resultset);

        $lectureList = array();
        if (isset ($resultset)) {
            foreach ($resultset as $key => $val) {
                $lectureList[$key]['id'] = $val['id'];
                $lectureList[$key]['code'] = stripslashes($val['code']);
                $lectureList[$key]['title'] = stripslashes($val['title']);
                $lectureList[$key]['syllabus'] = stripslashes($val['syllabus']);
                //$lectureList[$key]['rootsection'] = $val['rootsection'];
            }
        }

        $this->_smarty->assign('lectureList', $lectureList);
    }

    function assignSingle()
    {
        /* If id == 0, we shall create a new record. */
        if ($this->id) {
            /* Query data of this lecture. */
            $this->dbQuerySingle();
            /* And if the result is an empty set, set defaults. */
            if (empty ($this->rs['id'])) {
                $this->_setDefaults();
            }
        } else {
            /* Initialize default values. */
            $this->_setDefaults();
        }
        /* And assign it as a 'lecture' to Smarty. */
        $this->_smarty->assign('lectureInfo', $this->rs);

        return $this->rs;
    }

    /* -------------------------------------------------------------------
       HANDLER: SHOW
       ------------------------------------------------------------------- */
    function doShow()
    {
        /* Query data of this lecture and assign them to 'lecture' */
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
        /* Fetch the updated data of the lecture so that we can write
           something out. */
        $this->assignSingle();
    }

    /* -------------------------------------------------------------------
       HANDLER: DELETE
       ------------------------------------------------------------------- */
    function doDelete()
    {
        /* Just fetch the data of the user to be deleted and ask for
           confirmation. */
        $this->assignSingle();
    }

    /* -------------------------------------------------------------------
       HANDLER: REAL DELETE
       ------------------------------------------------------------------- */
    function doRealDelete()
    {
        /* Fetch the data of the lecture to be deleted before they
           are deleted. */
        $this->assignSingle();
        /* Delete the record */
        DatabaseBean::dbDeleteById();
    }

    /* -------------------------------------------------------------------
       HANDLER: ADMIN
       ------------------------------------------------------------------- */
    function doAdmin()
    {
        /* Get the list of all lectures. */
        $this->assignFull();
        /* Query data of this lecture and assign them to 'lecture' */
        $this->assignSingle();
        SessionDataBean::setLecture($this);
        /* It could have been that doAdmin() has been called from another
           handler. Change the action to "admin" so that ctrl.php will
           know that it shall display the scriptlet for lecture.admin */
        $this->action = "admin";
    }

    /* -------------------------------------------------------------------
       HANDLER: EDIT
       ------------------------------------------------------------------- */
    function doEdit()
    {
        /* If id == 0, we will create a new record. Otherwise, we will
           fetch the lecture data from database. The result will be
           assigned to template variable 'lectureInfo'. */
        $this->assignSingle();
    }
}

?>
