<?php

/* File types
   If you modify this list, please, add the entry to the array in _getFileTypes()
   and visit template 'file.edit.tpl' to make sure proper parent will be selected.
 */
define ('FT_S_DATA', 1); // Section - data
define ('FT_S_IMAGE_L', 2); // Section - left header
define ('FT_S_IMAGE_R', 3); // Section - right header
define ('FT_A_IMAGE', 4); // Article - image within text
define ('FT_A_DATA', 5); // Article - data
define ('FT_P_IMAGE', 6); // Person - image
define ('FT_LAB_IMAGE', 7); // Physics - experiment photo
define ('FT_LAB_THUMB', 8); // Physics - thumbnail of the experiment photo
define ('FT_S_IMAGE', 9); // Image for use within section text

/* Private file types */
define ('FT_X_ASSIGNMENT', 254); // Assignment for students
define ('FT_X_SOLUTION', 255); // Solution to a subtask submitted by a student

/* File type lists for use in the SQL 'type IN (...)' excpressions */
define ('ARTICLE_FILE_TYPES', FT_A_IMAGE . "," . FT_A_DATA . "," . FT_P_IMAGE . "," . FT_LAB_IMAGE . "," . FT_LAB_THUMB);
define ('SECTION_FILE_TYPES', FT_S_DATA);
define ('SECTION_FILE_TYPES_ALL', FT_S_DATA . "," . FT_S_IMAGE_L . "," . FT_S_IMAGE_R . "," . FT_S_IMAGE);
define ('ARTICLE_FILE_LAB_THUMB', FT_LAB_THUMB);
define ('ARTICLE_FILE_LAB_IMAGE', FT_LAB_IMAGE);
define ('ALL_DATA_FILES', FT_S_DATA . "," . FT_A_DATA);

/* Default value that indicates we shall handle a default action
   that is more complex than just assigning a value in the function
   header. See _dbQueryObjectListForFirstFileId() for an example. */
define ('IMPLICIT_DEFAULT_VAL', -1);

require_once('external/zipstream.php');

class FileBean extends DatabaseBean
{
    var $type;
    var $objid;
    var $uid;
    var $fname;
    var $origfname;
    var $description;
    var $position;
    var $dozip;
    var $doall;
    var $timestamp;

    /* Fill in reasonable defaults. */
    function _setDefaults()
    {
        $this->type = $this->rs['type'] = 0;
        $this->objid = $this->rs['objid'] = 0;
        $this->uid = $this->rs['uid'] = 0;
        $this->fname = $this->rs['fname'] = "";
        $this->origfname = $this->rs['origfname'] = "";
        $this->description = $this->rs['description'] = "";
        $this->position = $this->rs['position'] = 0;
        $this->returntoparent = $this->rs['returntoparent'] = 0;
    }

    /* Return a list of available section types. */
    function _getFileTypes()
    {
        return array(
            FT_S_DATA => "Soubor k sekci",
            FT_A_IMAGE => "Obrázek ke článku",
            FT_A_DATA => "Soubor ke článku",
            FT_S_IMAGE => "Obrázek k sekci",
        );
    }

    /* Return an object type corresponding to the file type. */
    function _getObjectTypeString()
    {
        switch ($this->type) {
            case FT_S_DATA:
            case FT_S_IMAGE:
            case FT_S_IMAGE_L:
            case FT_S_IMAGE_R:
                return "section";
            case FT_A_DATA:
            case FT_A_IMAGE:
            case FT_LAB_IMAGE:
            case FT_LAB_THUMB:
                return "article";
            case FT_P_IMAGE:
                return "person";
        }

        return "error";
    }


    /* Constructor */
    function __construct($id, &$smarty, $action, $object)
    {
        /* Call parent's constructor first */
        parent::__construct($id, $smarty, "file", $action, $object);
        /* Create a default file ID array */
        $this->DEFAULT_FILE_ID = array();
        $this->DEFAULT_FILE_ID [FT_S_DATA] = 0;
        $this->DEFAULT_FILE_ID [FT_S_IMAGE_L] = 402;
        $this->DEFAULT_FILE_ID [FT_S_IMAGE_R] = 403;
        $this->DEFAULT_FILE_ID [FT_A_DATA] = 0;
        $this->DEFAULT_FILE_ID [FT_A_IMAGE] = 0;
    }

    function dbReplace()
    {
        DatabaseBean::dbQuery(
            "REPLACE file VALUES ("
            . $this->id . ","
            . $this->type . ","
            . $this->objid . ","
            . $this->uid . ","
            . "NULL,'"
            . mysql_escape_string($this->fname) . "','"
            . mysql_escape_string($this->origfname) . "','"
            . mysql_escape_string($this->description) . "',"
            . $this->position . ")"
        );

        /* Update $this->id in case that REPLACE actually inserten a new record. */
        $this->updateId();
    }

    function dbQuerySingle()
    {
        /* Query the data of this section (ID has been already specified) */
        DatabaseBean::dbQuerySingle();
        /* Initialize the internal variables with the data queried from the
           database. */
        $this->objid = $this->rs['objid'];
        $this->uid = $this->rs['uid'];
        $this->timestamp = $this->rs['timestamp'];
        $this->type = $this->rs['type'];
        $this->fname = $this->rs['fname'];
        $this->origfname = $this->rs['origfname'];
        $this->description = vlnka(stripslashes($this->rs['description']));
        $this->position = $this->rs['position'];
        $this->rs['description'] = $this->description;
        /* Return resultset */
        return $this->rs;
    }

    /* Update just the objid field */
    function dbUpdateObjId()
    {
        DatabaseBean::dbQuery(
            "UPDATE file SET objid=" . $this->objid .
            " WHERE id=" . $this->id);
    }

    /* Assign POST variables to internal variables of this class and
       remove evil tags where applicable. We shall probably also remove
       evil attributes et cetera, but this will be done later if ever. */
    function processPostVars()
    {
        assignPostIfExists($this->id, $this->rs, 'id');
        assignPostIfExists($this->type, $this->rs, 'type');
        assignPostIfExists($this->objid, $this->rs, 'objid');
        assignPostIfExists($this->uid, $this->rs, 'uid');
        assignPostIfExists($this->description, $this->rs, 'description', true);
        assignPostIfExists($this->position, $this->rs, 'position');
        /* File name is stored in $_FILES. I do not like this construct
           but for now we will have to live with it. */
        if (isset ($_FILES['userfile'])) {
            $f1 = $_FILES['userfile'];
            if (isset ($f1['name'])) {
                $this->origfname = $this->rs['origfname'] = $f1['name'];
            }
        }
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
        assignGetIfExists($this->objid, $this->rs, 'objid');
        assignGetIfExists($this->type, $this->rs, 'type');
        assignGetIfExists($this->dozip, $this->rs, 'zip');
        assignGetIfExists($this->doall, $this->rs, 'all');
        /* Process 'returntoparent' directive */
        $this->processReturnToParent();
        /* And return a modified 'rs' to the caller function. */
        return $this->rs;
    }

    function icon($filename)
    {
        $type = 'bin';

        $ext = strtolower(substr($filename, -3));
        switch ($ext) {
            case "rtf" :
                $ext = "doc";
            case "avi" :
                $ext = "mov";
            case "doc" :
            case "xls" :
            case "ppt" :
            case "pdf" :
            case "zip" :
            case "jpg" :
            case "png" :
            case "dwg" :
            case "mov" :
                $type = $ext;
            default :
        }

        return $type;
    }

    function fa_icon($filename)
    {
        $type = null;

        $ext = strtolower(substr($filename, -3));
        switch ($ext) {
            case "rtf" :
            case "txt" :
                $ext = "text"; break;
            case "avi" :
            case "wmv" :
            case "mov" :
                $ext = "video"; break;
            case "doc" :
            case "docx" :
                $ext = "word"; break;
            case "xls" :
            case "xlsx" :
                $ext = "excel"; break;
            case "ppt" :
            case "pptx" :
                $ext = "powerpoint"; break;
            case "rar" :
            case "zip" :
                $ext = "archive"; break;
            case "jpg" :
            case "png" :
            case "dwg" :
                $ext = "picture"; break;
            case "pdf":
                $ext = "pdf"; break;

            default :
                $type = "fa-file";
        }

        if ( ! $type )
        {
            $type = "fa-file-$ext-o";
        }

        return $type;
    }

    /** ------------------------------------------------------------------------
     * Clear all database record related to assigment files for the given
     * `$subtaskId`. It will remove all stale filed from the filesystem and
     * delete all records from the database.
     * ---------------------------------------------------------------------- */
    function clearAssignmentFiles($subtaskId)
    {
        /* Get the list of all assigment files in concern. */
        $where = "WHERE type=" . FT_X_ASSIGNMENT . " AND objid=" . $subtaskId;
        $rs = DatabaseBean::dbQuery("SELECT * FROM file " . $where);
        /* And if there are some, delete them. */
        if (!empty ($rs)) {
            foreach ($rs as $key => $val) {
                /* Get the full path to the file. */
                $filename = CMSFILES . "/" . $val['fname'];
                $this->dumpVar('filename to unlink', $filename);
                /* Delete the file and don't complain if it does not exist. */
                @unlink($filename);
            }
            /* Now erase the database records as well. */
            DatabaseBean::dbQuery("DELETE FROM file " . $where);
        }
    }

    /**
     * Add new file record to the database.
     * File has been uploaded already and we have to just update the parameters,
     * taking into account that the same file may exist in the database already.
     * @param enum $type File type.
     * @param integer $ibjid Identifier of the object that this file is bound to.
     * @param integer $uid User id of the file owner.
     * @param string $filename Filename of the local copy of the file.
     * @param string $origname Original filename of the uploaded file.
     * @param string $desc Description of the file.
     * @param integer $position Position of the file in case it will be listed.
     * @return integer File id.
     */
    function addFile($type, $objid, $uid, $filename, $origname, $description, $position = 0)
    {

        $this->id = $this->dbQueryFname($filename);
        $this->type = $type;
        $this->objid = $objid;
        $this->uid = $uid;
        $this->fname = $filename;
        $this->origfname = $origname;
        $this->description = $description;
        $this->position = $position;

        $this->dbReplace();

        return $this->id;
    }

    /**
     * Find whether an fname exists.
     * Returns an id of an existing file record with `fname` value that
     * corresponds to the parameter, or 0.
     * @param string $fname File name as used in the system.
     */
    function dbQueryFname($fname)
    {
        $rs = self::dbQuery("SELECT id FROM file " .
            "WHERE fname='" . mysql_escape_string($fname) . "'");
        if (empty ($rs)) {
            return 0;
        }
        if (count($rs) > 1) {
            trigger_error('More than one files for assignment and student');
        }
        return $rs[0]['id'];
    }

    /* -------------------------------------------------------------------
       Return a list of files of particular type corresponding to a
       particular object (article,section).
       ------------------------------------------------------------------- */
    function dbQueryFiles($objectCondition, $fileTypeSet, $orderFields = "position,origfname")
    {
        $resultset = $this->dbQuery(
            "SELECT * FROM file WHERE " . $objectCondition .
            "type IN (" . $fileTypeSet . ") ORDER BY " . $orderFields
        );
        //$this->dumpVar ("object rs", $resultset);
        if (!empty ($resultset)) {
            foreach ($resultset as $key => $val) {
                $resultset[$key]['description'] = stripslashes($val['description']);
                $resultset[$key]['icon'] = $this->icon($val['fname']);
                $resultset[$key]['fa_icon'] = $this->fa_icon($val['fname']);
            }
        }

        //$this->dumpVar ("processed rs", $resultset);
        return $resultset;
    }

    /* -------------------------------------------------------------------
       Return a list of files of particular type corresponding to a
       particular object (article,section).
       ------------------------------------------------------------------- */
    function dbQueryAssignmentFile($subtaskId, $studentId)
    {
        $resultset = $this->dbQueryFiles(
            "objid=" . $subtaskId .
            " AND uid=" . $studentId .
            " AND ",
            FT_X_ASSIGNMENT
        );
        if (count($resultset) > 1) {
            trigger_error('More than one files for assignment and student');
        }
        if (!empty($resultset)) {
            $resultset = $resultset[0];
        }
        return $resultset;
    }

    /* -------------------------------------------------------------------
       Return a list of files of particular type corresponding to a
       particular object (article,section).
       ------------------------------------------------------------------- */
    function dbQueryObjectFiles($objectId, $fileTypeSet)
    {
        return $this->dbQueryFiles("objid=" . $objectId . " AND ", $fileTypeSet);
    }

    /* -------------------------------------------------------------------
       Return a list of files corresponding to a particular article.
       ------------------------------------------------------------------- */
    function dbQueryArticleFiles($articleId)
    {
        $resultset = $this->dbQueryObjectFiles($articleId, ARTICLE_FILE_TYPES);
        return $resultset;
    }

    /* -------------------------------------------------------------------
       Return a list of files of type FT_LAB_THUMB corresponding to
       a particular article.
       ------------------------------------------------------------------- */
    function dbQueryArticleFilesLabThumb($articleId)
    {
        $resultset = $this->dbQueryObjectFiles($articleId, ARTICLE_FILE_LAB_THUMB);
        return $resultset;
    }

    /* -------------------------------------------------------------------
       Return a list of files of type FT_LAB_IMAGE corresponding to
       a particular article.
       ------------------------------------------------------------------- */
    function dbQueryArticleFilesLabImage($articleId)
    {
        $resultset = $this->dbQueryObjectFiles($articleId, ARTICLE_FILE_LAB_IMAGE);
        return $resultset;
    }

    /* -------------------------------------------------------------------
       Return a list of files corresponding to a particular section.
       ------------------------------------------------------------------- */
    function dbQuerySectionFiles($sectionId)
    {
        $resultset = $this->dbQueryObjectFiles($sectionId, SECTION_FILE_TYPES);
        return $resultset;
    }

    /* -------------------------------------------------------------------
       Return a list of images for section text corresponding to
     a particular section.
       ------------------------------------------------------------------- */
    function dbQuerySectionTextImages($sectionId)
    {
        $resultset = $this->dbQueryObjectFiles($sectionId, FT_S_IMAGE);
        return $resultset;
    }

    /* -------------------------------------------------------------------
       Return a complete list of data files stored in the systemtion.
       ------------------------------------------------------------------- */
    function dbQueryAllDataFiles()
    {
        $resultset = $this->dbQueryFiles("", ALL_DATA_FILES, "type,objid,origfname");
        return $resultset;
    }

    /* -------------------------------------------------------------------
       Assign list of files corresponding to a particuler article into
       a Smarty variable 'articleFileList'.
       ------------------------------------------------------------------- */
    function assignArticleFiles($articleId)
    {
        $resultset = $this->dbQueryArticleFiles($articleId);
        $this->_smarty->assign('articleFileList', $resultset);
    }

    /* -------------------------------------------------------------------
       Assign list of files corresponding to a particuler article into
       a Smarty variable 'articleFileList'.
       ------------------------------------------------------------------- */
    function assignSectionFiles($sectionId)
    {
        $resultset = $this->dbQuerySectionFiles($sectionId);
        $this->_smarty->assign('sectionFileList', $resultset);
    }

    /* -------------------------------------------------------------------
       Assign a full list of data files stored in the database into
       a Smarty variable 'allDataFilesList'.
       ------------------------------------------------------------------- */
    function assignAllDataFiles()
    {
        $resultset = $this->dbQueryAllDataFiles();
        $this->_smarty->assign('allDataFilesList', $resultset);
    }

    /* -------------------------------------------------------------------
       Assign left and right header image identifiers for the given section
       to Smarty variables 'leftHeaderId' and 'rightHeaderId'.
       ------------------------------------------------------------------- */
    function assignHeaderImages($sectionId)
    {
        if ($sectionId > 0) {
            /* Some preparatory work will be necessary. */
            $sectionBean = new SectionBean ($sectionId, $this->_smarty, "x", "x");
            /* Query a path towards the root section. This will return a
               bottom-top path without the root id (0) */
            $pathToRoot = $sectionBean->dbQueryNodePath();
            $root = array(0);
            $nodePath = array_merge($pathToRoot, $root);
        } else {
            /* Home page. Take just root section (id 0) into account. */
            $nodePath = array(0);
        }

        /* Left header image with implicit default value. */
        $leftHeaderId = $this->_dbQueryObjectListForFirstFileId(
            $nodePath, FT_S_IMAGE_L);
        $this->_smarty->assign('leftHeaderId', $leftHeaderId);

        /* Right header image with implicit default value. */
        $rightHeaderId = $this->_dbQueryObjectListForFirstFileId(
            $nodePath, FT_S_IMAGE_R);
        $this->_smarty->assign('rightHeaderId', $rightHeaderId);
    }

    /* Travel node path and look for first existing file of given
       fileType.
       TODO: Use JOIN on section,file to accomplish this in a single
       querty. It will require creating another object that communicates
       with both 'section' and 'file' tables. Example query:
       "SELECT * FROM section,file WHERE section.id IN (2,7,9,11,12)
       AND file.type=2 AND section.id=file.objid" */
    function _dbQueryObjectListForFirstFileId(
        &$objectList, $fileType, $default = IMPLICIT_DEFAULT_VAL)
    {
        foreach ($objectList as $val) {
            /* Query the object for given file type. */
            $resultset = $this->dbQueryObjectFiles($val, $fileType);
            //$this->dumpVar ( 'resultset for FileBean', $resultset );
            if (!empty ($resultset)) {
                return $resultset[0]['id'];
            }
        }
        /* If there was no matching file, return a default. */
        $defRet = ($default == IMPLICIT_DEFAULT_VAL) ?
            $this->DEFAULT_FILE_ID [$fileType] :
            $default;
        //$this->dumpVar ( "defRet", $defRet );
        //$this->dumpVar ( "DEFAULT_FILE_ID", $this->DEFAULT_FILE_ID );
        return $defRet;
    }

    /**
     * Obtain a list of files stored in sections and articles.
     * Returns and assigns to a Smarty variable 'fileList' a list of files
     * that are bound to sections and articles of some lecture, or a complete
     * list of files if the lecture id has been omitted.
     *
     * @param $lectureId Identifier of the lecture, defaults to 0.
     */
    function assignSectionArticleFiles($lectureId = 0)
    {
        /* Some preparatory work will be necessary. */
        $sectionBean = new SectionBean (NULL, $this->_smarty, NULL, NULL);
        $articleBean = new ArticleBean (NULL, $this->_smarty, NULL, NULL);

        /* Get a list of all sections for the given lecture. */
        if ($lectureId > 0) {
            $sectionSet = $sectionBean->dbQuerySectionIdSetLecture($lectureId);
        } else {
            $sectionSet = $sectionBean->dbQuerySectionIdSet();
        }

        $output = array();
        foreach ($sectionSet as $key => $val) {
            /* Check articles */
            $r = array();
            $resultset = $articleBean->dbQueryArticlesForParent($key);
            if (!empty ($resultset)) {
                foreach ($resultset as $rkey => $rval) {
                    $rs2 = $this->dbQuery('SELECT id,origfname,description FROM file WHERE objid=' . $rval['id'] . ' AND type IN (' . ARTICLE_FILE_TYPES . ') ORDER BY origfname');
                    if (!empty ($rs2)) {
                        foreach ($rs2 as $rrkey => $rrval) {
                            $rs2[$rrkey]['description'] = stripslashes($rrval['description']);
                        }

                        $r['sname'] = $val;
                        $ra['article'] = stripslashes($rval['title']);
                        $ra['files'] = $rs2;
                        $r['afiles'][] = $ra;
                    }
                }
            }

            /* Check files that directly belong to a section. */
            $rs2 = $this->dbQuery('SELECT id,origfname,description FROM file WHERE objid=' . $key . ' AND type IN (' . SECTION_FILE_TYPES . ') ORDER BY origfname');
            if (!empty ($rs2)) {
                foreach ($rs2 as $rrkey => $rrval) {
                    $rs2[$rrkey]['description'] = stripslashes($rrval['description']);
                }

                $r['sname'] = $val;
                $r['sfiles'] = $rs2;
            }

            if (!empty ($r)) $output[] = $r;
        }

        $this->_smarty->assign('fileList', $output);
    }

    /* -------------------------------------------------------------------
       Query the database if the given object links to some files.
       Returns true if some files exist.
       ------------------------------------------------------------------- */
    function dbQueryFilePresence($objId, $objTypes)
    {
        $ret = false;

        $resultset = $this->dbQuery(
            "SELECT COUNT(*) FROM file WHERE objid=" . $objId .
            " AND type IN (" . $objTypes . ")"
        );

        if (!empty ($resultset)) {
            $count = $resultset[0][0];
            $ret = ($count != 0);
        }

        return $ret;
    }

    /* Fetch an image of the person referenced by 'personId' from database and
       assign the data to the smarty variable 'personImg'. */
    function assignPersonImage($personId)
    {
        $image['id'] = 0;
        $this->_smarty->assign('personImg', $image);
    }

    /**
     * Assign single file given the id to Smarty variable `file`.
     */
    function assignSingle()
    {
        $this->dbQuerySingle();
        $this->_smarty->assign('file', $this->rs);
    }

    /**
     * Show a single file from the satabase reference by id.
     */
    function showSingleFile()
    {
        /* Query data of this file */
        $this->dbQuerySingle();

        /* In case that this is an assignment file, check that the file UID
              corresponds to the user UID. */
        $doServeFile = true;
        if ($this->type == FT_X_ASSIGNMENT || $this->type == FT_X_SOLUTION) {
            /* Do not allow access to anonymous users and to students with
               different uids. */
            $role = SessionDataBean::getUserRole();
            if ($role == USR_ANONYMOUS ||
                ($role == USR_STUDENT &&
                    $this->uid != SessionDataBean::getUserId())
            ) {
                /* Acess to assigments of other students is not allowed. */
                $doServeFile = false;
                $this->action = "err_03";
                return false;
            }
        }

        /* All other files are public. */
        if ($doServeFile) {
            /* Pass file to the client browser. */
            $fn = CMSFILES . '/' . $this->fname;
            /* But first check if the file exists. */
            if (is_file($fn)) {
                $type = mimetype($fn);
                $length = filesize($fn);
                /* MSIE has troubles with decoding the answer, even if it is RFC compliant.
                   Hence, we add the following headers, it should help. */
                header("Pragma: ");
                header("Cache-Control: ");
                /* MSIE specific header end here. */
                header("Content-Type: " . $type);
                if ($length > 0) header("Content-Length: " . $length);
                header("X-Filename: " . $this->origfname);
                if ($type != 'application/pdf') {
                    header("Content-Disposition: inline;filename=" . $this->origfname);
                } else {
                    header("Content-Disposition: attachment;filename=\"" . $this->origfname . "\"");
                }
                readfile($fn);
                exit ();
            } else {
                /* Nope. Flag an error. */
                $this->action = "err_04";
                return false;
            }
        }

        return true;
    }

    /**
     * Serve a ZIP file of solutions to subtask with the given ID.
     */
    function showZipFiles()
    {
        /* Check if the current user's role permits delivery of the file. */
        if (UserBean::isRoleAtLeast(SessionDataBean::getUserRole(), USR_LECTURER)) {
            /* Query data of the solution. */
            $solutionBean = new SolutionBean ($this->id, $this->_smarty, '', '');
            $solutionBean->assignFull();

            /* Process the list of submitted solutions into a file id list. */
            $fileList = array2ToDBString($solutionBean->rs, 'id');

            /* And query informaiton about all files. */
            $rs = $this->dbQuery(
                'SELECT * FROM file WHERE id IN (' . $fileList . ') ORDER BY fname;');

            /* Create a new ZIP stream object. */
            $zip = new ZipStream ('download.zip');

            /* Set the flag indicating no errors in the process. */
            $noerr = true;

            foreach ($rs as $val) {
                /* Local file name and directory. */
                $lname = $val['fname'];
                /* Pass file to the client browser. */
                $fn = CMSFILES . '/' . $lname;
                /* But first check if the file exists. */
                if (is_file($fn)) {
                    //$type = mimetype ($fn);
                    //$length = filesize ($fn);
                    /* MSIE has troubles with decoding the answer, even if it is RFC compliant.
                       Hence, we add the following headers, it should help. */
                    //header( "Pragma: " );
                    //header( "Cache-Control: " );
                    /* MSIE specific header end here. */
                    //header ("Content-Type: " . $type );
                    //if ( $length > 0 ) header ("Content-Length: " . $length );
                    //header ("X-Filename: " . $this->origfname );
                    //if ( $type != 'application/pdf')
                    //{
                    //  header ("Content-Disposition: inline;filename=" . $this->origfname);
                    //}
                    //else
                    //{
                    //  header ("Content-Disposition: attachment;filename=\"" . $this->origfname . "\"");
                    //}
                    /* Add the file to the ZIP strem. */
                    $data = file_get_contents($fn);
                    $zip->add_file($lname, $data);
                } else {
                    /* Nope. Flag an error. */
                    $this->action = "err_04";
                    $noerr = false;
                    break;
                }
            }

            if ($noerr) {
                /* Finish the stream and close it. */
                $zip->finish();
                exit();
            }
        } else {
            /* User is not allowed to download this. */
            $this->action = "err_05";
            return false;
        }

        return true;
    }

    /**
     * Serve a full PDF with assignments and solutions to subtask with the given ID.
     */
    function showAllSolutions()
    {
        /* Check if the current user's role permits delivery of the file. */
        if (UserBean::isRoleAtLeast(SessionDataBean::getUserRole(), USR_LECTURER)) {
            //die ( 'not implemented yet' );

            /* Query data of the subtask. */
            $sub = new SubtaskBean (NULL, $this->_smarty, '', '');
            //$sub->assignSingle();
            $sCode = $sub->getSubtaskCode($this->id);
            $sType = $sub->getSubtaskType($this->id);

            /* Query data of the solution. */
            $solutionBean = new SolutionBean ($this->id, $this->_smarty, '', '');
            //$solutionBean->order = SolutionBean::SOL_ORDER_BY_NAME;
            $solutionBean->assignFull();

            /* Prepare for querying the student data. */
            $stb = new StudentBean (NULL, $this->_smarty, '', '');

            /* Process the list of submitted solutions into a file id list. */
            $fileList = array2ToDBString($solutionBean->rs, 'id');

            /* And query informaiton about all files that are solutions of this
               subtask. */
            $rs = $this->dbQuery(
                'SELECT * FROM file LEFT JOIN student AS st ON file.uid=st.id WHERE file.id IN (' . $fileList . ') ORDER BY st.surname,st.firstname,fname;');
            $this->dumpVar('rs', $rs);

            /* Prepare the assignment bean. */
            $asb = new AssignmentsBean ($this->id, $this->_smarty, '', '');

            /* Construct the header template file name. The template is bound
               to have the filename of the form `<subtask_code>_header.tex`
               and reside in the directory `<subtask_code>`. */
            $tBaseDir = CMSFILES . "/assignments/";
            $tFileName = $tBaseDir . $sCode . "/" . $sCode . "_header.tex";
            /* Check if the file exists and is readable. */
            if (!is_readable($tFileName)) {
                /* The file does not exist or it is not readable. In such a
                   case we will try a backup file - its name is
                   `solution_header_template.tex` and it is stored in the
                   directory holding assignment directories. */
                $this->assign('texTemplate', $tFileName);
                $tFileName = $tBaseDir . "solution_header.tex";
            }

            /* Check if the file exists and is readable. */
            if (!is_readable($tFileName)) {
                /* Even the global template does not exist or it is not
                   readable. Exit with error. */
                $this->assign('solTemplate', $tFileName);
                $this->action = "err_06";
                return false;
            }

            /* Yes, the header template exitst and it is readable. */
            $handle = fopen($tFileName, 'r');
            $headerstr = fread($handle, filesize($tFileName));
            fclose($handle);

            /* Remember the current schoolyear. */
            $year = SessionDataBean::getSchoolYear();

            /* Change to the directory where files shall be generated. */
            $solBaseDir = CMSFILES . "/solutions/" . $sCode . "/" . $year;
            $genBaseDir = $solBaseDir . "/postprocess";
            @mkdir($genBaseDir);
            chdir($genBaseDir);

            /* Initialise the commandline list of all generated stamped PDFs. */
            $solBuffer = "";

            foreach ($rs as $val) {
                /* Local file name and directory. */
                $lname = $val['fname'];
                /* Convert it to the full path. */
                $fn = CMSFILES . '/' . $lname;
                /* But first check if the file exists. */
                if (is_file($fn)) {
                    /* Get the student data. */
                    $stb->id = $val['uid'];
                    $stb->dbQuerySingle();
                    $u8name = $stb->firstname . " " . $stb->surname;
                    $name = iconv("utf-8", "windows-1250", $u8name);
                    $login = $stb->login;

                    /* Get the id of this assignment. It could be some true id
                       or values AssignmentBean::ID_UNDEFINED or
                       AssignmentBean::ID_INVALID. */
                    $aid = $asb->getAssignmentId($val['uid'], $this->id, $sType);

                    /* Get the part number. The part number is the last letter
                       before the last '.' in the file name. */
                    $separPos = strrpos($lname, '.');
                    $part = $lname[$separPos - 1];
                    /* But ... the last letter does not have to be a letter. */
                    if ($part < 'a' || $part > 'z') $part = '';

                    /* Should the subtask type be the TT_WEEKLY_SIMU type, remove
                       the extension of the file as it has been converted to EPS/PDF
                       format prior to calling this function. */
                    if ($sType == TT_WEEKLY_SIMU) {
                        $lname = substr($lname, 0, $separPos);
                    }

                    /* Generate header - Prepare template replacement. */
                    $codes = array(
                        "@CODE@",
                        "@LOGIN@",
                        "@NAME@"
                    );
                    $replc = array(
                        strtoupper($sCode),
                        $login,
                        $name
                    );

                    /* Transform the template into assignment file. */
                    $texstr = str_replace($codes, $replc, $headerstr);

                    /* Write the template tex file for this student. */
                    $studBase = $login;
                    $headBase = $studBase . "_header";
                    $headTex = $headBase . ".tex";
                    $headPdf = $headBase . ".pdf";
                    $handle = fopen($headTex, "w");
                    fwrite($handle, $texstr);
                    fclose($handle);

                    /* And LaTeX it. */
                    //$ret = system("TEXINPUTS=`kpsexpand -p tex`:$tBaseDir pdflatex -interaction=batchmode " . $filename . " > /dev/null ");
                    $ret = system("pdflatex $headTex");
                    $this->dumpVar("system returns", $ret);
                    system('rm -f *.tex *.log *.aux');

                    /* Use pdftk to stamp the document */
                    $stampPdf = $studBase . "_stamp.pdf";
                    $solutPdf = CMSFILES . "/" . $lname;
                    $cmdLine = "pdftk $solutPdf stamp $headPdf output $stampPdf";
                    $ret = system($cmdLine);
                    $this->dumpVar("pdftk call:", "pdftk $solutPdf stamp $headPdf output $stampPdf");
                    $this->dumpVar("pdftk system returns", $ret);

                    /* Append the stamped PDF name to the future commandline of
                       `pdftk ... cat all_solutions.pdf`. */
                    $solBuffer .= " " . $stampPdf;
                } else {
                    /* Nope. Flag an error. */
                    $this->action = "err_04";
                    return false;
                }
            }

            $this->dumpVar('solBuffer', $solBuffer);
            $cmdLine = "pdftk $solBuffer cat output 00_all_solutions.pdf";
            $ret = system($cmdLine);
            $this->dumpVar("pdftk system returns", $ret);

            /* Construct the template file name. The template is bound to
               have name of the form `<subtask_code>_all.tex`. */
            //$tBaseDir = CMSFILES . "/assignments/" . $sCode . "/";
            //$tFileName = $tBaseDir . $sCode . "_all.tex";
            /* Check if the file exists and is reafable. */
//            if ( ! is_readable ( $tFileName ))
//            {
//            	/* Nope. Flag an error and return immediately. */
//            	$this->assign('texTemplate', $tFileName);
//            	$this->action = "err_06";
//            	return false;
//            }

            /* Yest, the template exitst and it is readable. */
//            $handle = fopen ( $tFileName, 'r' );
//            $templatestr = fread ( $handle, filesize ( $tFileName ));
//            fclose ( $handle );

            /* Change to the directory where files shall be generated. */
//            $solBaseDir = CMSFILES . "/solutions";
//            chdir ( $base );

        } else {
            /* User is not allowed to download this. */
            $this->action = "err_05";
            return false;
        }

        return true;
    }

    /* -------------------------------------------------------------------
	   HANDLER: SHOW
	   ------------------------------------------------------------------- */
    function doShow()
    {
        /* Process GET parameters of the call. */
        $this->processGetVars();
        /* Did the id represent a subtask identifier (resulting in an on-the-fly
           compression of subtask results)? */
        if ($this->dozip) {
            $ret = $this->showZipFiles();
        } elseif ($this->doall) {
            $ret = $this->showAllSolutions();
        } else {
            $ret = $this->showSingleFile();
        }

        if (!$ret) {
            /* The system will output an error message. This HTML text needs
               proper Content-Type header, but the header has been suppressed
               by the controller as it dit not know the type of content that
               will be server to the client. */
            header("Content-Type: text/html; charset=utf-8");
        }
    }

    /* -------------------------------------------------------------------
       HANDLER: SAVE
       ------------------------------------------------------------------- */
    function doSave()
    {
        $fn = $_FILES['userfile']['tmp_name'];
        $nn = $_FILES['userfile']['name'];

        //$this->id = (integer) $_POST['id'];

        /* Assign all relevant POST variables to their counterparts in this
              object. Ignore the others. */
        $this->processPostVars();

        //echo "<!-- HTTP_POST_FILES";
        //print_r ( $_HTTP_POST_FILES );
        //echo "-->";
        echo "<!-- _FILES[]";
        print_r($_FILES);
        echo "-->";
        $this->dumpVar("fn", $fn);
        $this->dumpVar("nn", $nn);
        //$this->dumpVar ( "intId", $intId );

        if (!empty ($fn) && !empty ($nn)) {
            /* Some file has been specified, test if it is not a fake upload */
            if (is_uploaded_file($fn)) {
                /* No, it is a real file. Now we have to check if the record
                  for this file exists. If yes, we'll delete the old file specified
                  by the record. */
                if ($this->id > 0) $this->dbQuerySingle();
                /* Update the record first. */
                $this->dbReplace();
                if (!empty ($this->fname)) {
                    /* File exists, delete it */
                    unlink(CMSFILES . '/' . $this->fname);
                } else {
                    /* No file exists, the REPLACE above inserted a new record into the database.
                       Get its Id. */
                    $this->id = mysql_insert_id();
                }

                /* Now copy the uploaded file, changing its name. */
                $ftype = (FT_A_IMAGE == (integer)$_POST['type']) ? 'a' : 's';
                $dbname = sprintf("%06d", $this->id) . $ftype . '_' . $nn;
                copy($fn, CMSFILES . '/' . $dbname);

                /* And reflect the change in the database. */
                $this->dbQuery("UPDATE file SET fname='" . $dbname . "' WHERE id=" . $this->id);
            } else {
                $this->action = "err_01";
                $this->errmsg = "Possible file upload attack. Filename: '" . $fn . "'";
                return ERR_FILE_UPLOAD_ATTACK;
            }
        } else {
            /* No file specified. If it is a new record (Id=0), flag an error,
               if not, update the existing record. */
            if ($this->id == 0) {
                $this->action = "err_02";
                return ERR_NO_FILE_SPECIFIED;
            } else {
                /* And reflect the change in the database. */
                DatabaseBean::dbQuery(
                    "UPDATE file SET " .
                    "type=" . $_POST['type'] . ", " .
                    "objid=" . $_POST['objid'] . ", " .
                    "position=" . $_POST['position'] . ", " .
                    "description='" . mysql_escape_string($_POST['description']) . "' " .
                    "WHERE id=" . $this->id);
            }
        }

        /* Query data of this file. */
        $this->dbQuerySingle();
        /* Check the presence of GET or POST parameter 'returntoparent'. */
        $this->processReturnToParent();
        /* The above function set $this->rs to values that shall be
           displayed. By assigning $this->rs to Smarty variable 'article'
           we can fill the values of $this->rs into a template. */
        $this->_smarty->assign('file', $this->rs);
        /* Obtain object type string. */
        $this->_smarty->assign('objtypestring', $this->_getObjectTypeString());
        /* Left column contains administrative menu */
        $this->_smarty->assign('leftcolumn', "leftadmin.tpl");

        return RET_OK;
    }

    /* -------------------------------------------------------------------
       HANDLER: REAL DELETE
       ------------------------------------------------------------------- */
    function doRealDelete()
    {
        /* Query data of this file */
        $this->dbQuerySingle();
        /* Check the presence of HTTP parameter 'returntoparent'. */
        $this->processReturnToParent();
        /* The above function set $this->rs to values that shall be
           displayed. By assigning $this->rs to Smarty variable 'file'
           we can fill the values of $this->rs into a template. */
        $this->_smarty->assign('file', $this->rs);
        /* Obtain object type string. */
        $this->_smarty->assign('objtypestring', $this->_getObjectTypeString());
        /* Delete the file from the disk. */
        if (!empty ($this->fname)) {
            unlink(CMSFILES . '/' . $this->fname);
        }
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
        /* Create a list of all files ordered by their corresponding
           sections. */
        $this->assignSectionArticleFiles($this->id);
        /* Left column contains administrative menu */
        $this->_smarty->assign('leftcolumn', "leftadmin.tpl");
        /* It could have been that doAdmin() has been called from another
           handler. Change the action to "admin" so that ctrl.php will
           know that it shall display the scriptlet for section.admin */
        $this->action = "admin";
    }

    /* -------------------------------------------------------------------
       HANDLER: DELETE
       ------------------------------------------------------------------- */
    function doDelete()
    {
        /* Query data of this file. */
        $this->dbQuerySingle();
        /* Check the presence of GET or POST parameter 'returntoparent'. */
        $this->processReturnToParent();
        /* The above function set $this->rs to values that shall be
           displayed. By assigning $this->rs to Smarty variable 'file'
           we can fill the values of $this->rs into a template. */
        $this->_smarty->assign('file', $this->rs);
        /* Left column contains administrative menu */
        $this->_smarty->assign('leftcolumn', "leftadmin.tpl");
    }

    /* -------------------------------------------------------------------
       HANDLER: EDIT
       ------------------------------------------------------------------- */
    function doEdit()
    {
        /* If id == 0, we shall create a new file record. */
        if ($this->id) {
            /* Query data of this file. */
            $this->dbQuerySingle();
            /* Check the presence of GET or POST parameter 'returntoparent'. */
            $this->processReturnToParent();
        } else {
            /* New file: initialize default values. */
            $this->_setDefaults();
            /* Have a look at HTTP GET parameters if there is some
               additional information we could use ( parent id or
               file type). */
            $this->processGetVars();
        }
        /* Both above functions set $this->rs to values that shall be
           displayed. By assigning $this->rs to Smarty variable 'file'
           we can fill the values of $this->rs into a template. */
        $this->_smarty->assign('file', $this->rs);
        /* Get the list of possible parent sections. */
        $section = new SectionBean (0, $this->_smarty, "", "");
        $section->assignSectionIdSetLecture();
        /* Get the list of possible parent articles. */
        $article = new ArticleBean (0, $this->_smarty, "", "");
        $article->assignArticleIdSet();
        /* Get the list of possible file types. */
        $this->_smarty->assign('fileTypes', $this->_getFileTypes());
        /* Left column contains administrative menu */
        $this->_smarty->assign('leftcolumn', "leftadmin.tpl");
    }
}

?>
