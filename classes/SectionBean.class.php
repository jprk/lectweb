<?php

/* Home section will always have an id == 1 */
define ('HOME_PAGE', 1);

/* Define different section types. */
define ('ST_NORMAL', "section");
define ('ST_TASKS', "tasks");
define ('ST_TASKS_EN', "tasks_en");
define ('ST_URLS', "urls");
define ('ST_DOWNLOAD', "download");
define ('ST_EXCLIST', "exclistsect");

class SectionBean extends DatabaseBean
{
    var $parent;
    var $lecture_id;
    var $type;
    var $title;
    var $mtitle;
    var $text;
    var $position;
    var $redirect;
    var $lastmodified;
    var $ival1; // Section-type specific small integer value

    /* Fill in reasonable defaults. */
    function _setDefaults()
    {
        $this->type = $this->rs['type'] = "unknown";
        $this->parent = $this->rs['parent'] = 0;
        $this->lecture_id = $this->rs['lecture_id'] = SessionDataBean::getLectureId();
        $this->title = $this->rs['title'] = "";
        $this->mtitle = $this->rs['mtitle'] = "";
        $this->text = $this->rs['text'] = "<p></p>";
        $this->position = $this->rs['position'] = 0;
        $this->redirect = $this->rs['redirect'] = "";
        $this->ival1 = $this->rs['ival1'] = 0;
    }

    /* Retun a list of available section types. */
    function _getSectionTypes()
    {
        return array(
            ST_NORMAL => "Běžná sekce",
            ST_TASKS => "Úkoly (česky)",
            ST_TASKS_EN => "Úkoly (anglicky)",
            ST_URLS => "Odkazy",
            ST_DOWNLOAD => "Download",
            ST_EXCLIST => "Seznam cvičení"
        );
    }

    /* Constructor */
    function __construct($id, &$dbwrap, $action, $object)
    {
        /* Call parent's constructor */
        parent::__construct($id, $dbwrap, "section", $action, $object);
        /* Initialise new properties to their default values. */
        $this->_setDefaults();
    }

    function SectionBean4($id, &$dbwrap, &$acl, $action, $object)
    {
        /* Fill in default data */
        $this->_setDefaults();
        /* Call parent's constructor */
        DatabaseBean::DatabaseBean4($id, $dbwrap, $acl, "section", $action, $object);
    }

    /* Update the table 'section' with values given by internal variables of
       this class. If $this->id is equal to zero, a new record will be created. */
    function dbReplace()
    {
        DatabaseBean::dbQuery(
            "REPLACE section VALUES (" .
            $this->id . "," .
            $this->parent . "," .
            $this->lecture_id . ",'" .
            mysql_escape_string($this->type) . "','" .
            mysql_escape_string($this->title) . "','" .
            mysql_escape_string($this->mtitle) . "','" .
            mysql_escape_string($this->text) . "'," .
            $this->position . ",'" .
            mysql_escape_string($this->redirect) . "'," .
            "NULL" . "," .
            $this->ival1 . ")"
        );
        /* Update the id of this recored if necessary. */
        $this->updateId();
    }

    /* Query the data of section specified by $this->id. */
    function dbQuerySingle()
    {
        /* Query the data of this section (ID has been already specified) */
        DatabaseBean::dbQuerySingle();
        /* Update the internal variables with the data queried from the
           database. $this->id has been updated by the call to parent class. */
        $this->parent = $this->rs['parent'];
        $this->lecture_id = $this->rs['lecture_id'];
        $this->type = $this->rs['type'];
        $this->title = vlnka(stripslashes($this->rs['title']));
        $this->mtitle = vlnka(stripslashes($this->rs['mtitle']));
        $this->text = vlnka(stripslashes($this->rs['text']));
        $this->position = $this->rs['position'];
        $this->redirect = $this->rs['redirect'];
        $this->lastmodified = $this->rs['lastmodified'];
        $this->ival1 = $this->rs['ival1'];
        /* Update $this->rs as it will be used to publish the section data. */
        $this->rs['title'] = $this->title;
        $this->rs['mtitle'] = $this->mtitle;
        $this->rs['text'] = $this->text;
    }

    /* Assign POST variables to internal variables of this class and
       remove evil tags where applicable. We shall probably also remove
       evil attributes et cetera, but this will be done later if ever. */
    function processPostVars()
    {
        assignPostIfExists($this->id, $this->rs, 'id');
        assignPostIfExists($this->parent, $this->rs, 'parent');
        assignPostIfExists($this->lecture_id, $this->rs, 'lecture_id');
        assignPostIfExists($this->type, $this->rs, 'type');
        assignPostIfExists($this->title, $this->rs, 'title', true);
        assignPostIfExists($this->mtitle, $this->rs, 'mtitle', true);
        assignPostIfExists($this->text, $this->rs, 'text', true,
            "<a><b><br><i><p><ul><li><dl><dt><ol><em><strong><h2><h3><h4><table><thead><tbody><tt><tr><td><th><img><div><pre><span><iframe>");
        assignPostIfExists($this->position, $this->rs, 'position');
        assignPostIfExists($this->redirect, $this->rs, 'redirect');
        assignPostIfExists($this->ival1, $this->rs, 'ival1');

        /* Process 'returntoparent' directive */
        $this->processReturnToParent();

        return $this->rs;
    }

    /* Assign GET variables to internal variables of this class. This
       is intended for pre-setup purposes (supplying parent id and
       section type for new sections that will be edited afterwards).
       Only a subset of internal variables can be updated this way,
       hopefully none of them may open a security hole. */
    function processGetVars()
    {
        assignGetIfExists($this->parent, $this->rs, 'parent');
        assignGetIfExists($this->lecture_id, $this->rs, 'lecture_id');
        assignGetIfExists($this->type, $this->rs, 'type');

        /* Process 'returntoparent' directive */
        $this->processReturnToParent();

        return $this->rs;
    }

    /* -------------------------------------------------------------------
       Query the database for the list top level sections.
       ------------------------------------------------------------------- */
    function dbQueryTopLevel()
    {
        return DatabaseBean::dbQuery("SELECT * FROM section WHERE parent=0");
    }

    /* -------------------------------------------------------------------
       Query the database for the node path from this node to the root.
       TODO: Modify database to use preorder tree traversal to query the
       path in a single query.
       ------------------------------------------------------------------- */
    function dbQueryNodePath()
    {
        $nodePath = array();
        $parentId = $this->id;
        while ($parentId != 0) {
            $nodePath[] = $parentId;
            $parentId = $this->_dbQueryParent($parentId);
        }
        //$this->dumpVar ('nodePath', $nodePath );
        return $nodePath;
    }

    function _dbQueryParent($parentId)
    {
        $rs = DatabaseBean::dbQuery("SELECT parent FROM section WHERE id=" . $parentId);
        return (!empty($rs)) ? $rs[0]['parent'] : 0;
    }

    /* -------------------------------------------------------------------
       Retrieve the full section hierarchy.
       ------------------------------------------------------------------- */
    function __dbQuerySectionHierarchy($parentId, $level, &$hierarchy, &$maxlevel)
    {
        $resultset = $this->dbQuery("SELECT * FROM section WHERE parent=" . $parentId . ' ORDER BY position,mtitle');

        if (isset ($resultset)) {
            $index = 1;
            $size = sizeof($resultset);

            foreach ($resultset as $val) {
                $temp['id'] = $val['id'];
                $temp['mtitle'] = $val['mtitle'];
                $temp['level'] = $level;

                $temp['last'] = ($size == $index) ? true : false;

                $hierarchy[] = $temp;

                $this->__dbQuerySectionHierarchy($val['id'], $level + 1, $hierarchy, $maxlevel);

                $index++;
            }
        }

        if ($level > $maxlevel) $maxlevel = $level;
    }

    function dbQuerySectionHierarchy($parentId = 0)
    {
        $hierarchy = array();
        $maxlevel = 0;
        $this->__dbQuerySectionHierarchy($parentId, 1, $hierarchy, $maxlevel);

        // array_fill is not defined in php < 4.2.0
        $empty = array_fill(1, $maxlevel + 1, false);
        // $empty = array ();
        // for ( $i = 1 ; $i <= $maxlevel + 1 ; $i++ ) $empty[$i] = false;

        foreach ($hierarchy as $key => $val) {
            /* Fist N-1 images. Some of them may be empty. */
            $loops = $val['level'];
            for ($i = 1; $i < $loops; $i++) {
                $hierarchy[$key]['indents'][] = ($empty[$i] == true) ? ' ' : 'I';
            }

            /* Last image before the actual text. Either normal fork or an 'L' shape. */
            if ($hierarchy[$key]['last'] == true) {
                $hierarchy[$key]['indents'][] = 'L';
                $empty[$loops] = true;
            } else {
                $hierarchy[$key]['indents'][] = 'E';
                $empty[$loops] = false;
            }

            $hierarchy[$key]['numIndents'] = $maxlevel - $loops;
        }

        /* Remember maxlevel and topspan. They will be needed for proper
           displaying of the hierarchy. */
        $this->_smarty->assign('topspan', $maxlevel);
        $this->_smarty->assign('maxlevel', $maxlevel - 1);

        /* Remember section hierarchy. */
        $this->_smarty->assign('sections', $hierarchy);
    }

    /**
     * Create a nested list of sections that will form the left-hand menu.
     * The list contains the full list of sections that have the same parent, then all the parents
     * up to the top level, and a full list of top level sections.
     */
    function assignMenuHierarchy($root_id)
    {
        /* We assume that the $root_id is the lowest id that occurs in the hierarchy (all sections are
           added after the root has been created. */
        if ( $this->id > $root_id )
        {
            /* Fetch a parent id of this section */
            $rs = $this->dbQuery("SELECT parent FROM section WHERE id=" . $this->id);
            $parent = $rs[0]['parent'];
            /* Get a list of all child sections of this section. Additional field 'hilit' contains 0
               as all the records are surely not the currently displayed section. */
            $level_next = $this->dbQuery(
                "SELECT id, mtitle, 0 AS hilit, 0 AS level, 0 AS open, 0 AS submenu FROM section " .
                "WHERE parent=" . $this->id . " " .
                "ORDER BY position,mtitle");
            /* Actual level counter. If $level_next contains some data, the parent objects
               are in menu level 1, otherwise we are at the bottom-most level 0. */
            $levelCount = (isset ($level_next)) ? 1 : 0;

            $this->dumpVar('level_next', $level_next);

            /* Get a list of all sections at the same hierarchy level. Additional field 'hilit' contains
               1 for the record that is currently being displayed. */
            $level_current = $this->dbQuery(
                "SELECT id, mtitle, id=" . $this->id . " AS hilit, " . $levelCount . " AS level, 0 AS open, 0 AS submenu FROM section " .
                "WHERE parent=" . $parent . " " .
                "ORDER BY position,mtitle");
            /* First element of $level_next opens the next level. The last one closes it. As we want to display
               the menu using <ul> ... </ul> we need a special menu item for opening and closing the list. */
            $submenu_open = array('submenu' => true, 'open' => true, 'level' => 0);
            $submenu_close = array('submenu' => true, 'open' => false, 'level' => 0);
            /* Incorporate data from $level_next, if there are some child sections (in that case $level_next
               is not an empty array). */
            if (!empty($level_next))
            {
                /* $level_current is sorted correctly, we need to insert the elements of
                   $level_next after the element of $level_current that has 'hilit' == 1. */
                foreach ($level_current as $val)
                {
                    $menu[] = $val;
                    if ($val['hilit'] == 1)
                    {
                        $menu[] = $submenu_open;
                        $menu = array_merge($menu, $level_next);
                        $menu[] = $submenu_close;
                    }
                }
            }
            /* Otherwise just assign $menu to be $level_current */
            else
            {
                $menu = $level_current;
            }

            $this->dumpVar('menu part 1 (the same level and children)', $menu);
            $this->dumpVar('parent', $parent);
            $this->dumpVar('root id', $root_id);

            /* Add parents to this list until the parent is the root of the
               section tree with id 0. Skip the loop if the parent is
               already id 0. */
            $parent2 = $parent;
            while ($parent2 > $root_id)
            {
                /* Increment $levelCount */
                $levelCount++;
                /* Get the next level. */
                $rs = $this->dbQuery(
                    "SELECT id, mtitle, 0 AS hilit, " . $levelCount . " AS level, 0 AS open, 0 AS submenu, parent FROM section " .
                    "WHERE id=" . $parent2);
                /* Get the new parent id */
                $parent = $rs[0]['parent'];
                /* The parent entry may be a toplevel entry (whith parent id
                   equal to $root_id). In such a case, let's leave the loop now
                   as the toplevel entries shall be added as a complete list
                   and this will be accomplished in the next loop. */
                if ($parent == $root_id) break;
                /* First element of $menu opens the next level. The last one closes it. */
                array_unshift($menu, $submenu_open);
                $menu[] = $submenu_close;
                /* Prepend the query result to the $menu array */
                $menu = array_merge($rs, $menu);
                /* And update parent */
                $parent2 = $parent;
            }

            $this->dumpVar('menu part 2 (without top level entries)', $menu);
            $this->dumpVar('parent', $parent);
            $this->dumpVar('parent2', $parent2);
            $this->dumpVar('root id', $root_id);

            /* At this point, $parent should be $root_id. $parent2 contains the id
               of the last lower-level entry or $root_id, if the toplevel list
               has been alrady displayed. */
            if ($parent2 > $root_id)
            {
                /* Fetch the toplevel list. */
                $rs = $this->dbQuery(
                    "SELECT id, mtitle, 0 AS hilit, " . $levelCount . " AS level, 0 AS open, 0 AS submenu FROM section " .
                    "WHERE parent=" . $root_id . " " .
                    "ORDER BY position,mtitle");
                /* Determine the value where the current contents of $menu
                   shall be inserted. */
                $count = count($rs);
                for ($i = 0; $i < $count; $i++) {
                    if ($rs[$i]['id'] == $parent2) break;
                }
                /* Split the $rs into two parts and insert $menu. If there
                   is no matching id, ignore the toplevel menu. */
                if ($i < $count)
                {
                    /* First element of $menu opens the next level. The last one closes it. */
                    array_unshift($menu, $submenu_open);
                    $menu[] = $submenu_close;
                    /* Split the top level list */
                    $temp1 = array_slice($rs, 0, $i + 1);
                    $temp2 = array_slice($rs, $i + 1);
                    /* Insert the sub-menu */
                    $menu = array_merge($temp1, $menu, $temp2);
                }
            }
            /* Transform levels by subtracting from $levelCount. */
            foreach ($menu as $key => $val)
            {
                $menu[$key]['colspan'] = $val['level'] + 1;
                $menu[$key]['level'] = $levelCount - $val['level'];
            }

            $this->_smarty->assign('menuHierList', $menu);
            $this->_smarty->assign('colspan', $levelCount + 1);
        }
    }

    /* -------------------------------------------------------------------
       Retrieve the full section hierarchy.
       ------------------------------------------------------------------- */
    function dbQuerySectionIdSetH($parentId, $parentName, &$hierarchy)
    {
        $resultset = $this->dbQuery("SELECT * FROM section WHERE parent=" . $parentId . ' ORDER BY mtitle');

        if (isset ($resultset)) {
            /* Add separator to the parent name */
            if ($parentName != "") $parentName .= " / ";

            foreach ($resultset as $val) {
                $newParentName = $parentName . $val['mtitle'];
                $hierarchy[$val['id']] = $newParentName;

                $this->dbQuerySectionIdSetH($val['id'], $newParentName, $hierarchy);
            }
        }
    }

    /* -------------------------------------------------------------------
       Query a list of all sections currently stored in the system.
       ------------------------------------------------------------------- */
    function dbQuerySectionIdSet()
    {
        $resultset = $this->dbQuery("SELECT id, mtitle FROM section ORDER BY mtitle");

        $parents[0] = 'Root';

        if (isset ($resultset)) {
            foreach ($resultset as $key => $val) {
                $parents[$val['id']] = stripslashes($val['mtitle']);
            }
        }

        return $parents;
    }

    /* -------------------------------------------------------------------
       Get id of the home section. Needed for displaying news.
     * ------------------------------------------------------------------- */
    function dbQueryHomeSection($rootSection)
    {
        /* No home section id the default. */
        $homeSection = NULL;

        /* Query the section list. */
        $rs = $this->dbQuery(
            "SELECT MIN(id) AS id FROM `section` " .
            "WHERE parent=" . $rootSection
        );
        if (!empty ($rs)) {
            $homeSection = $rs[0]['id'];
        }

        return $homeSection;
    }

    /**
     * Query a list of all sections for a lecture.
     * Returns a list of sections with hierarchical names for the currently
     * active lecture.
     */
    function dbQuerySectionIdSetLecture($lectureId = 0)
    {
        if ($lectureId > 0) {
            /* Query the database for the lecture. */
            $lectureBean = new LectureBean($lectureId, $this->_smarty, NULL, NULL);
            $lectureBean->dbQuerySingle();
            /* And fetch the root section of the lecture. */
            $rootId = $lectureBean->getRootSection();
        } else {
            /* Default is the current lecture which can be fetched from
               session data. We need the root section id. */
            $rootId = SessionDataBean::getRootSection();
        }

        /* Query a hierarchical list of sections beginning at $root_id. */
        $parents = array();
        $this->dbQuerySectionIdSetH($rootId, "", $parents);
        /* The root section has no name, assign a default. */
        $parents[$rootId] = "Root";

        self::dumpVar('parents', $parents);
        return $parents;
    }

    /**
     * Query a list of all sections for a lecture.
     * Queries the database for a list of sections for the currently
     * active lecture and assigns it to a Smarty variable `section_parents`.
     */
    function assignSectionIdSetLecture()
    {
        /* Query a list of sections for the current lecture. */
        $parents = $this->dbQuerySectionIdSetLecture();
        /* Remember it ... */
        $this->assign('section_parents', $parents);
    }

    /* -------------------------------------------------------------------
       Assign this section record to the smarty variable $section.
       ------------------------------------------------------------------- */
    function assignSection()
    {
        /* Query data of this section */
        $this->dbQuerySingle();
        /* The function above sets $this->rs to values that shall be
           displayed. By assigning $this->rs to Smarty variable 'section'
           we can fill the values of $this->rs into a template. */
        $this->_smarty->assign('section', $this->rs);
    }

    /**
     * Find the home page of a lecture.
     * Returns RET_OK or an error code. Updates $this->id with a valid
     * home pahe section number. May update $this->action as well in case of
     * an error.
     */
    function prepareLectureHomePage($lectureId)
    {
        /* The `id` passed to this instance is lecture id ... we have to
              query the lecture to get the root section id. */
        $lectureBean = new LectureBean ($lectureId, $this->_smarty, "x", "x");
        self::dumpVar('lectureBean', $lectureBean);
        $lectureBean->assignSingle();
        $rootsection = $lectureBean->getRootSection();
        /* And store the lecture information into session. */
        SessionDataBean::setLecture($lectureBean);
        /* Now find the section with minimal id that has $rootsection as
           a parent - this is our home section. The reason for this complicated
           construct lays in the origanisation of the lecture web: the home
           section is at the same level as other sections that follow it. */
        if ($rootsection > 0) {
            /* The lecture id was valid and the root section exists. */
            $homeSection = $this->dbQueryHomeSection($rootsection);

            if (empty ($homeSection)) {
                $this->action = 'e_nohome';
                return ERR_INVALID_ID;
            } else {
                $this->id = $homeSection;
            }
        } else {
            /* Fetch list of existing lectures. */
            $lectureBean->assignFull();
            $this->action = "err_01";
            return ERR_INVALID_ID;
        }

        return RET_OK;
    }

    /* -------------------------------------------------------------------
       HANDLER: SHOW
       ------------------------------------------------------------------- */
    function doShow()
    {
        /* Make sure that the `sectionImg` variable exists and is initialised to NULL. */
        $this->assign('sectionImg',NULL);
        /* Assign this section record to the smarty variable $section. */
        $this->assignSection();
        /* Remember the section id so that we can always show appropriate
           items in menus. */
        SessionDataBean::setLastSectionId($this->id);
        /* Query the lecture that this section belongs to. */
        $lectureBean = new LectureBean ($this->lecture_id, $this->_smarty, "x", "x");
        $lectureBean->assignSingle();
        /* And store the lecture information into session. */
        SessionDataBean::setLecture($lectureBean);
        /* Create a FileBean instance that will be used to get information
           about section file data bound to this section. Later the same
           instance will be employed to get information about article files
           that belong to articles belonging to this section. */
        $fileBean = new FileBean (0, $this->_smarty, "x", "x");
        /* Assign all section files that are bound to this section to
           Smarty variable 'sectionFileList'. */
        $fileBean->assignSectionFiles($this->id);
        /* Look for image headers and assign their ids into 'leftHeaderId'
           and 'rightHeaderId'. */
        $fileBean->assignHeaderImages($this->id);
        /* Now create an ArticleBean instance that will be used to query
           the list of articles belonging to this section. This list will
           then be used to get the list of files bound to every article. */
        $articleBean = new ArticleBean (0, $this->_smarty, "x", "x");
        /* Assign all articles that are bound to this section to Smarty
           variable 'articleList'. For some section types this list will
           be augmented with article file information. */
        if ($this->type == ST_TASKS || $this->type == ST_TASKS_EN) {
            /* Create a SubtaskBean instance that will be used to fetch
               a list of tasks that shall be displayed. */
            $taskBean = new SubtaskBean (0, $this->_smarty, "x", "x");
            /* Assign a list of all home tasks for students of this lecture. */
            $taskBean->assignStudentSubtaskList();
        } else if ($this->type == ST_URLS) {
            /* Create a URLsBean instance that will be used to fetch
               a list of urls that shall be displayed. */
            $urlsBean = new URLsBean (0, $this->_smarty, "x", "x");
            /* This will list all urls in the database, order them, and
               assign the result to Smarty variable 'urlsList'. */
            $urlsBean->assignFull();
        } else if ($this->type == ST_DOWNLOAD) {
            /* Create a FileBean instance that will be used to fetch
               a list of files that shall be displayed. */
            $fileBean = new FileBean (0, $this->_smarty, "x", "x");
            /* This will list all data files in the database (ignoring
               images) and assign the result to Smarty variable
               'allDataFilesList'. */
            $fileBean->assignAllDataFiles();
        } else if ($this->type == ST_EXCLIST) {
            /* Create an exerciseListBean instance that will be used to fetch
               a list of exercises for the lecture given as the $id parameter. */
            $exclistBean = new exerciseListBean ($this->ival1, $this->_smarty, "x", "x");
            $exclistBean->doShow();
        } else {
            $articleBean->assignSectionArticles($this->id);
        }

        /* Fetch news for the home page of the current lecture. */
        $homeSection = $this->dbQueryHomeSection($lectureBean->getRootSection());
        if ($this->id == $homeSection) {
            $newsBean = new NewsBean (0, $this->_smarty, "x", "x");
            /* Default is to assign all news for the current lecture that
             * are available. */
            $newsBean->assignNewsForTypes($lectureBean->id);
        }

        /* Change the object type according to the section type. This will
           trigger displaying a template appropriate for the contents of
           this section. */
        $this->object = $this->type;
        /* We could have landed here also from doSave handler. Assure that
           the proper template action will be used. */
        if (!empty ($this->redirect)) {
            $this->action = "redirect";
        } else {
            $this->action = "show";
        }
        /* Show the timestamp as a part of the page footer. */
        $this->_smarty->assign('lastmod', $this->lastmodified);
    }

    /* -------------------------------------------------------------------
       HANDLER: SAVE
       ------------------------------------------------------------------- */
    function doSave()
    {
        /* Assign POST variables to internal variables of this class and
           remove evil tags where applicable. */
        $this->processPostVars();
        /* Update the record */
        $this->dbReplace();
        /* Create the counter record, if necessary */
        if ((integer)$_POST['id'] == 0) {
            DatabaseBean::dbCreateCounterById();
        }
        /* Now we have to decide where to go for the next page. We can either
           return to the 'admin' page or 'show' the edited section. This
           method has been defined in BaseBean class. */
        $this->doShowOrAdmin();
    }

    /* -------------------------------------------------------------------
       HANDLER: REAL DELETE
       ------------------------------------------------------------------- */
    function doRealDelete()
    {
        /* Query data of this section */
        $this->dbQuerySingle();
        /* Check the presence of GET or POST parameter 'returntoparent'. */
        $this->processReturnToParent();
        /* The function above sets $this->rs to values that shall be
           displayed. By assigning $this->rs to Smarty variable 'section'
           we can fill the values of $this->rs into a template. */
        $this->_smarty->assign('section', $this->rs);

        /* Delete the record */
        $this->dbDeleteById();
        /* Delete the corresponding counter */
        $this->dbDeleteCounterById();

        /* Left column contains administrative menu */
        $this->_smarty->assign('leftcolumn', "leftadmin.tpl");
    }

    /* -------------------------------------------------------------------
       HANDLER: DELETE
       ------------------------------------------------------------------- */
    function doDelete()
    {
        /* Query data of this section */
        $this->dbQuerySingle();
        /* Check the presence of GET or POST parameter 'returntoparent'. */
        $this->processReturnToParent();
        /* The function above sets $this->rs to values that shall be
           displayed. By assigning $this->rs to Smarty variable 'section'
           we can fill the values of $this->rs into a template. */
        $this->_smarty->assign('section', $this->rs);

        /* Create a FileBean instance that will be used to get information
           about section file data bound to this section. */
        $fileBean = new FileBean (0, $this->_smarty, "x", "x");
        /* Look for image headers and assign their ids into 'leftHeaderId'
           and 'rightHeaderId'. */
        $haveFiles = $fileBean->dbQueryFilePresence($this->id, SECTION_FILE_TYPES_ALL);

        /* Now create an ArticleBean instance that will be used to query
           the list of articles belonging to this section. This list will
           then be used to get the list of files bound to every article. */
        $articleBean = new ArticleBean (0, $this->_smarty, "x", "x");
        /* Assign all articles that are bound to this section to Smarty
           variable 'articleList'. For some section types this list will
           be augmented with article file information. */
        $haveArticles = $articleBean->dbQueryArticlePresence($this->id);

        /* Check whether the section does not have anything linked to ... */
        if ($haveArticles || $haveFiles || $noPermission) {
            /* Section contains some articles which have to be deleted
               before deleting section itself. */
            $this->action = "e_cantdel";
            /* Publis flags so that we can display proper message. */
            $this->assign('haveArticles', $haveArticles);
            $this->assign('haveFiles', $haveFiles);
            $this->assign('noPermission', $noPermission);
        }

        /* Left column contains administrative menu */
        $this->assign('leftcolumn', "leftadmin.tpl");
    }

    /* -------------------------------------------------------------------
       HANDLER: ADMIN
       ------------------------------------------------------------------- */
    function doAdmin()
    {
        /* Positive `id` denotes lecture. `id` equal to zero suggests
           session timeout. */
        if ($this->id > 0) {
            /* The `id` passed to this instance is lecture id ... we have
               to query the lecture to get the root section id. */
            $lectureBean = new LectureBean ($this->id, $this->_smarty, "x", "x");
            $lectureBean->assignSingle();
            /* Get the list of all sections with root at actual section id. */
            $this->dbQuerySectionHierarchy($lectureBean->getRootSection());
            /* It could have been that doAdmin() has been called from another
               handler. Change the action to "admin" so that ctrl.php will
    	       know that it shall display the scriptlet for section.admin */
            $this->action = "admin";
        } else {
            $this->prepareLectureHomePage(0);
        }

        /* Left column contains administrative menu */
        $this->_smarty->assign('leftcolumn', "leftadmin.tpl");
    }

    /* -------------------------------------------------------------------
       HANDLER: EDIT
       ------------------------------------------------------------------- */
    function doEdit()
    {
        /* If id == 0, we shall create a new section. */
        if ($this->id) {
            /* Query data of this section */
            $this->dbQuerySingle();
        } else {
            /* New section: initialize default values. */
            $this->_setDefaults();
            /* Have a look at HTTP GET parameters if there is some
               additional information we could use ( parent id or
               section type). */
            $this->processGetVars();
        }
        /* Remember the section id so that we can always show appropriate
           files in file link selection box when editing. */
        SessionDataBean::setLastSectionId($this->id);
        /* Both above functions set $this->rs to values that shall be
           displayed. By assigning $this->rs to Smarty variable 'section'
           we can fill the values of $this->rs into a template. */
        $this->_smarty->assign('section', $this->rs);
        /* Get the list of all possible parent sections. */
        $this->assignSectionIdSetLecture();
        /* Get the list of all possible section types. */
        $this->_smarty->assign('sectypes', $this->_getSectionTypes());
        /* Get the list of all possible person categories. */
        // $this->_smarty->assign ( 'personcats', PersonBean::_getPersonCategories ());
        /* Get the list of lectures. */
        $lectureBean = new LectureBean (0, $this->_smarty, "x", "x");
        $lectureBean->assignSelectMap();
    }
}

?>
