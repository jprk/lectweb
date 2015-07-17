<?php

/* Article types */
define ('AT_S_NORMAL', 1);
define ('AT_S_LITERATURE', 2);
define ('AT_S_LECTURERS', 3);
define ('AT_S_EXERCISE', 4);

class ArticleBean extends DatabaseBean
{
    var $type;
    var $parent;
    var $title;
    var $authorid;
    var $abstract;
    var $text;
    var $position;
    var $lastmodified;
    var $activefrom;
    var $activeto;

    /* Fill in reasonable defaults. */
    function _setDefaults()
    {
        $this->id = $this->rs['id'] = 0;
        $this->type = $this->rs['type'] = 0;
        $this->parent = $this->rs['parent'] = 0;
        $this->title = $this->rs['title'] = "";
        $this->authorid = $this->rs['authorid'] = 0;
        $this->abstract = $this->rs['abstract'] = "";
        $this->text = $this->rs['text'] = "";
        $this->position = $this->rs['position'] = 0;
        $this->activefrom = $this->rs['activefrom'] = 0;
        $this->activeto = $this->rs['activeto'] = 0;
        $this->returntoparent = $this->rs['returntoparent'] = 0;
    }

    /* Return a list of available section types. */
    function _getArticleTypes()
    {
        return array(
            AT_S_NORMAL => "Norm�ln� �l�nek",
            AT_S_LITERATURE => "Seznam literatury k p�edm�tu",
            AT_S_LECTURERS => "Seznam vyu�uj�c�ch p�edm�tu",
            AT_S_EXERCISE => "Bodovan� p��klad"
        );
    }

    /* Constructor */
    function __construct($id, &$smarty, $action, $object)
    {
        /* Call parent's constructor first */
        parent::__construct($id, $smarty, "article", $action, $object);
    }

    /* Assign POST variables to internal variables of this class and
       remove evil tags where applicable. We shall probably also remove
       evil attributes et cetera, but this will be done later if ever. */
    function processPostVars()
    {
        assignPostIfExists($this->id, $this->rs, 'id');
        assignPostIfExists($this->type, $this->rs, 'type');
        assignPostIfExists($this->parent, $this->rs, 'parent');
        assignPostIfExists($this->title, $this->rs, 'title', true);
        assignPostIfExists($this->authorid, $this->rs, 'authorid');
        assignPostIfExists($this->abstract, $this->rs, 'abstract');
        assignPostIfExists($this->text, $this->rs, 'text', true,
            "<a><b><br><i><p><ul><li><dl><dt><ol><em><strong>");
        assignPostIfExists($this->position, $this->rs, 'position');
        assignPostIfExists($this->activefrom, $this->rs, 'activefrom');
        assignPostIfExists($this->activeto, $this->rs, 'activeto');
        /* Process 'returntoparent' directive */
        $this->processReturnToParent();
        /* And return a modified 'rs' to the caller function. */
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
        assignGetIfExists($this->type, $this->rs, 'type');
        /* Process 'returntoparent' directive */
        $this->processReturnToParent();
        /* And return a modified 'rs' to the caller function. */
        return $this->rs;
    }

    function dbReplace()
    {
        DatabaseBean::dbQuery(
            "REPLACE article VALUES ("
            . $this->id . ","
            . $this->type . ","
            . $this->parent . ",'"
            . mysql_escape_string($this->title) . "','"
            . $this->authorid . "','"
            . mysql_escape_string($this->abstract) . "','"
            . mysql_escape_string($this->text) . "',"
            . $this->position . ","
            . "NULL,'"
            . $this->activefrom . "','"
            . $this->activeto . "')"
        );
    }

    function dbQuerySingle()
    {
        /* Query the data of this section (ID has been already specified) */
        DatabaseBean::dbQuerySingle();
        /* Initialize the internal variables with the data queried from the
           database. */
        $this->type = $this->rs['type'];
        $this->parent = $this->rs['parent'];
        $this->title = vlnka(stripslashes($this->rs['title']));
        $this->authorid = $this->rs['authorid'];
        $this->abstract = vlnka(stripslashes($this->rs['abstract']));
        $this->text = vlnka(stripslashes($this->rs['text']));
        $this->position = $this->rs['position'];
        $this->lastmodified = $this->rs['lastmodified'];
        $this->activefrom = $this->rs['activefrom'];
        $this->activeto = $this->rs['activeto'];
        /* Publish the section data */
        $this->rs['title'] = $this->title;
        $this->rs['abstract'] = $this->abstract;
        $this->rs['parent'] = $this->parent;

        return $this->rs;
    }

    /* -------------------------------------------------------------------
       List all articles that belong to the given parent section.
       ------------------------------------------------------------------- */
    function dbQueryArticlesForParent($sectionId)
    {
        return $this->dbQuery(
            "SELECT id,title FROM article WHERE parent="
            . $sectionId
            . " ORDER BY position,title"
        );
    }

    /* -------------------------------------------------------------------
       Assign list of articles corresponding to particular sections into
       a Smarty variable 'articleList'.
       ------------------------------------------------------------------- */
    function assignArticleList()
    {
        /* Some preparatory work will be necessary. */
        $sectionBean = new SectionBean (0, $this->_smarty, "x", "x");

        /* Get a hierarchical list of all sections. */
        $sectionBean->dbQuerySectionIdSetH(0, "", $sectionSet);

        $output = array();

        if (isset ($sectionSet)) {
            foreach ($sectionSet as $key => $val) {
                $resultset = $this->dbQuery('SELECT id,title FROM article WHERE parent=' . $key . ' ORDER BY position,title');
                if (!empty ($resultset)) {
                    foreach ($resultset as $rkey => $rval) {
                        $resultset[$rkey]['title'] = stripslashes($rval['title']);
                    }

                    $r['sid'] = $key;
                    $r['sname'] = $val;
                    $r['articles'] = $resultset;
                    $output[] = $r;
                }
            }
        }

        $this->_smarty->assign('articleList', $output);
    }

    /* -------------------------------------------------------------------
       Query the database for a list of articles corresponding to
       particular section id. All text fields of the returned data will
       have the backslashes removed (backslashed are used by SQL as escape
       characters for protection of ' and similar characters).
       ------------------------------------------------------------------- */
    function dbQuerySectionArticles($sectionId, $orderby = 'position,title')
    {
        $resultset = $this->dbQuery(
            "SELECT * FROM article WHERE parent=" . $sectionId .
            " ORDER BY " . $orderby
        );

        if (!empty ($resultset)) {
            foreach ($resultset as $key => $val) {
                $resultset[$key]['title'] = stripslashes($val['title']);
                $resultset[$key]['abstract'] = stripslashes($val['abstract']);
                $resultset[$key]['text'] = stripslashes($val['text']);
                /* Excercise articles have time limits. */
                if ($val['type'] == AT_S_EXERCISE) {
                    $atf = strtotime($val['activefrom']);
                    $att = strtotime($val['activeto']);
                    $tmp = gettimeofday();
                    $gmt = $tmp['sec'];
                    $resultset[$key]['active'] = ($atf <= $gmt) && ($att >= $gmt);
                } else {
                    $resultset[$key]['active'] = true;
                }
            }
        }

        return $resultset;
    }

    /* -------------------------------------------------------------------
       Query the database for a list of articles corresponding to
       particular section id. Returns true if some articles exist.
       ------------------------------------------------------------------- */
    function dbQueryArticlePresence($sectionId)
    {
        $ret = false;

        $resultset = $this->dbQuery(
            "SELECT COUNT(*) AS cnt FROM article WHERE parent=" . $sectionId
        );

        if (!empty ($resultset)) {
            $count = $resultset[0]['cnt'];
            $ret = ($count != 0);
            $this->dumpVar('resultset', $resultset);
            $this->dumpVar('count', $count);
        }

        return $ret;
    }

    /* -------------------------------------------------------------------
       Assign list of articles corresponding to particular section into
       a Smarty variable 'articleList'.
       ------------------------------------------------------------------- */
    function assignSectionArticles($sectionId)
    {
        $resultset = $this->dbQuerySectionArticles($sectionId);
        $this->_smarty->assign('articleList', $resultset);
    }

    /* -------------------------------------------------------------------
       Assign list of articles corresponding to particular section of
       ST_NEWS type into a Smarty variable 'articleList'.
       ------------------------------------------------------------------- */
    function assignSectionArticlesAsNews($sectionId)
    {
        $resultset = $this->dbQuerySectionArticles($sectionId, 'lastmodified DESC');
        $this->_smarty->assign('articleList', $resultset);
    }

    /* -------------------------------------------------------------------
       Assign list of articles corresponding to particular section into
       a Smarty variable 'articleListWithFiles'. This list contains also
       information about some file types bound to an article from this list -
       see fields 'lab_thumb', and 'lab_image'.
       ------------------------------------------------------------------- */
    function assignSectionArticlesWithFiles($sectionId)
    {
        /* Query the list of articles. */
        $resultset = $this->dbQuerySectionArticles($sectionId);
        /* If it is empty, there is nothing more to do. */
        if (!empty ($resultset)) {
            /* If not, we will use our FileBean instance do deliver us
               a list of files belonging to this particular article. */
            $fileBean = new FileBean (0, $this->_smarty, "x", "x");
            foreach ($resultset as $key => $val) {
                $frs = $fileBean->dbQueryArticleFilesLabThumb($val['id']);
                $resultset[$key]['lab_thumb'] = $frs[0];
                $frs = $fileBean->dbQueryArticleFilesLabImage($val['id']);
                $resultset[$key]['lab_image'] = $frs[0];
            }
        }
        $this->_smarty->assign('articleList', $resultset);
    }

    /* -------------------------------------------------------------------
       Query a list of all sections currently stored in the system and
       assign it to Smarty variable 'parent_articles'.
       ------------------------------------------------------------------- */
    function dbQueryArticleIdSet()
    {
        $resultset = $this->dbQuery("SELECT id,title FROM article ORDER BY title");

        /* Assure that $parents will exist even if the list of articles will be
           empty. */
        $parents = array();
        if (isset ($resultset)) {
            foreach ($resultset as $key => $val) {
                $parents[$val['id']] = stripslashes($val['title']);
            }
        }
        return $parents;
    }

    /* -------------------------------------------------------------------
       Query a list of all sections currently stored in the system and
       assign it to Smarty variable 'article_parents'.
       ------------------------------------------------------------------- */
    function assignArticleIdSet()
    {
        $parents = $this->dbQueryArticleIdSet();
        /* Remember article list hierarchy. */
        $this->_smarty->assign('article_parents', $parents);
    }

    /* -------------------------------------------------------------------
       HANDLER: SHOW
       ------------------------------------------------------------------- */
    function doShow()
    {
        /* Query data of this article */
        $this->dbQuerySingle();
        /* The function above sets $this->rs to values that shall be
           displayed. By assigning $this->rs to Smarty variable 'article'
           we can fill the values of $this->rs into a template. */
        $this->_smarty->assign('article', $this->rs);
        /* Create a FileBean instance that will be used to get information
           about article file data bound to this article. */
        $fileBean = new FileBean (0, $this->_smarty, "x", "x");
        /* Assign all section files that are bound to this section to
           Smarty variable 'sectionFileList'. */
        $fileBean->assignArticleFiles($this->id);
        /* Look for image headers and assign their ids into 'leftHeaderId'
           and 'rightHeaderId'. */
        $fileBean->assignHeaderImages($this->parent);
        /* Get parent section. */
        $sectionBean = new SectionBean ($this->parent, $this->_smarty, "x", "x");
        /* Publish section data as $section in Smarty. */
        $sectionBean->assignSection();
        /* Get left-hand menu. The menu will contain just the parent
           section name.. */
        $this->_smarty->assign('leftcolumn', "leftarticle.tpl");
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
        /* Query data of this article. */
        // why??? not needed when adding article from section ... $this->dbQuerySingle ();
        /* Check the presence of GET or POST parameter 'returntoparent'. */
        $this->processReturnToParent();
        /* The above function set $this->rs to values that shall be
           displayed. By assigning $this->rs to Smarty variable 'article'
           we can fill the values of $this->rs into a template. */
        $this->_smarty->assign('article', $this->rs);
        /* Left column contains administrative menu */
        $this->_smarty->assign('leftcolumn', "leftadmin.tpl");
    }

    /* -------------------------------------------------------------------
       HANDLER: DELETE
       ------------------------------------------------------------------- */
    function doDelete()
    {
        /* Query data of this article. */
        $this->dbQuerySingle();
        /* Check the presence of GET or POST parameter 'returntoparent'. */
        $this->processReturnToParent();
        /* The above function set $this->rs to values that shall be
           displayed. By assigning $this->rs to Smarty variable 'article'
           we can fill the values of $this->rs into a template. */
        $this->_smarty->assign('article', $this->rs);
        /* Left column contains administrative menu */
        $this->_smarty->assign('leftcolumn', "leftadmin.tpl");
    }

    /* -------------------------------------------------------------------
       HANDLER: REAL DELETE
       ------------------------------------------------------------------- */
    function doRealDelete()
    {
        /* Query the record of this article */
        $this->dbQuerySingle();
        /* Check the presence of HTTP parameter 'returntoparent'. */
        $this->processReturnToParent();
        /* The above function set $this->rs to values that shall be
           displayed. By assigning $this->rs to Smarty variable 'file'
           we can fill the values of $this->rs into a template. */
        $this->_smarty->assign('article', $this->rs);
        /* Delete the record */
        $this->dbDeleteById();
        /* Left column contains administrative menu */
        $this->_smarty->assign('leftcolumn', "leftadmin.tpl");
    }

    /* -------------------------------------------------------------------
       HANDLER: ADMIN
       ------------------------------------------------------------------- */
    function doAdmin()
    {
        /* Assign list of section/article hierarchy into 'articleList'. */
        $this->assignArticleList();
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
        /* If id == 0, we shall create a new article. */
        if ($this->id) {
            /* Query data of this article. */
            $this->dbQuerySingle();
            /* Check the presence of GET or POST parameter 'returntoparent'. */
            $this->processReturnToParent();
        } else {
            /* New article: Initialize default values. */
            $this->_setDefaults();
            /* Have a look at HTTP GET parameters if there is some
               additional information we could use ( parent id or
               section type). */
            $this->processGetVars();
        }
        /* Both above calls set $this->rs to values that shall be
           displayed. By assigning $this->rs to Smarty variable 'article'
           we can fill the values of $this->rs into a template. */
        $this->_smarty->assign('article', $this->rs);
        /* Get the list of all possible parent sections. */
        $sectionBean = new SectionBean (0, $this->_smarty, "x", "x");
        $sectionBean->assignSectionIdSetLecture();
        /* Get the list of all possible article types. */
        $this->_smarty->assign('articleTypes', $this->_getArticleTypes());
        /* Left column contains administrative menu */
        $this->_smarty->assign('leftcolumn', "leftadmin.tpl");
    }
}

?>
