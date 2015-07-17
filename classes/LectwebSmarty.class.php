<?php

// load helper functions
require('tools.php');

// load Smarty library
require('smarty3/Smarty.class.php');

/* News types */
define ('NEWS_SECTION', 1);
define ('NEWS_ARTICLE', 2);

define ('IMG_SIZE', 190);

class LectwebSmarty extends Smarty
{
    private $userIsAdmin;
    private $_currencies;
    private $_submenu;
    private $_yesno;
    private $_dayMap;
    private $_yearMap;
    private $_config;

    public  $debug;


    /* Create mapping array for HTML <select> containing year as a key and school year
       identification as a value. */
    static function _assignYearMap()
    {
        /* Get the current year. */
        $date = getdate();

        $yearMap = array();
        foreach (range(MIN_YEAR, $date['year'] + 1) as $year) {
            $nextYear = $year + 1;
            $yearMap[$year] = $year . '/' . $nextYear;
        }

        return $yearMap;
    }

    /* Create mapping for working days. */
    static function _assignDayMap()
    {
        $days = array(1, 2, 3, 4, 5, 11, 12, 13, 14, 15, 21, 22, 23, 24, 25);
        $dayMap = array();
        foreach ($days as $day) {
            $dayMap[$day] = numToDayString($day);
        }
        return $dayMap;
    }

    /**
     * Class constructor
     */
    function __construct ( $config, $isPageOutput, $isAdmin)
    {
        /* Call parent constructor first. */
        parent::__construct();

        /* Set the application directories. Value of `APP_BASE_DIR`
           is defined in configuration file. */
        $this->setTemplateDir ( REQUIRE_DIR . '/../templates' );
        $this->setCompileDir ( APP_BASE_DIR . '/templates_c' );
        $this->setConfigDir ( APP_BASE_DIR . '/configs' );
        $this->setCacheDir ( APP_BASE_DIR . '/cache' );

        /* We will place (and look for) plugins into a subdirectory
           of directory where class files are located. */
        $this->addPluginsDir ( REQUIRE_DIR . '/plugins' );

        $this->_yesno = array(0 => '&nbsp;ne', 1 => '&nbsp;ano');
        $this->_dayMap = LectwebSmarty::_assignDayMap();
        $this->_yearMap = LectwebSmarty::_assignYearMap();

        $this->assign('app_name', 'LectwebSmarty');
        $this->assign('yesno', $this->_yesno);
        $this->assign('daySelect', $this->_dayMap);
        $this->assign('yearSelect', $this->_yearMap);
        $this->assign('basedir', BASE_DIR);
        $this->assign('BASE_DIR', BASE_DIR);

        $this->_config = $config;
        $this->userIsAdmin = $isAdmin;

        $this->compile_check = $this->_config['compile_check'];
        $this->use_sub_dirs = $this->_config['use_sub_dirs'];
        $this->caching = false;

        /* Get remote address. If the request came from certain computer,
           switch on the debugging. */
        $ra = $_SERVER['REMOTE_ADDR'];
        $this->debug = ($isPageOutput) ? in_array($ra, $this->_config['debug_hosts']) : false;
        $this->assign('debugmode', $this->debug);

        /* Hardcoded lecture id for cases where no lecture id is read from the
           database. */
        //$lecture = array ( 'id' => '1', 'code' => 'K611MSAP' );
        //$this->assign ( 'lecture', $lecture );

        /* More strict error checking and reporting in case of debugging session. */
        if ( $this->debug )
        {
            ini_set ( 'display_errors', 1 );
            error_reporting ( E_ALL | E_STRICT );
        }
    }

    function dbOpen()
    {
        /* Open connection to the database server. */
        $db = $this->_config['db'];
        $link = mysql_connect($db['host'], $db['user'], $db['pass']);
        if (!$link) {
            $error = "<p>Cannot connect to mySQL as <tt>'" .
                $db['user'] . "@" . $db['host'] . "'</tt></p>\n";
            logSystemError($error);
            throw new Exception ('Nelze se připojit k databázovému serveru.');
        }

        /* Select the database. */
        $res = mysql_select_db($db['data']);
        if (!$res) {
            $error = "<p>Cannot select database <tt>'" .
                $db['data'] . "'</tt> as <tt>'" . $db['user'] . "@" .
                $db['host'] . "'</tt></p>\n";
            logSystemError($error);
            throw new Exception ('Nelze se připojit k databázovému serveru.');
        }

        /* Support for UTF-8 data exchange. */
        $res = mysql_query("SET NAMES utf8");
        if (!$res) {
            $error = "<p>Cannot set charset to utf8: <tt>" . mysql_error() .
                "</tt></p>\n";
            logSystemError($error);
            throw new Exception ('Nelze zvolit znakovou sadu pro komunikaci s databází.');
        }

        return $link;
    }

    function dbClose($link)
    {
        if ($link) mysql_close($link);
    }

    /**
     * Process a database query with possible custom field as an index.
     * Enter description here ...
     * @param string $query The query string.
     * @param string $idx The index in case of custom indexing.
     */
    function dbQuery($query, $idx = NULL)
    {
        if ($this->debug) echo "<!-- Q:'" . $query . "' -->\n";
        $result = mysql_query($query);

        /* Is the result a meaningful `resource` or did an error occur? */
        if (!$result) {
            $error = "<p>Invalid query: <tt>" . mysql_error() . "</tt></p>\n";
            $error .= "<p>Query string: <tt>" . $query . "</tt></p>\n";
            logSystemError($error);
            throw new Exception ('Neplatný SQL dotaz.');
        }

        /* Allocate an array for the query result. */
        $asr = array();

        /* If dbQuery was used to update some information, the result is irrelevant. */
        if (!is_bool($result)) {
            /* If normal indexing has been requested, copy the returned rows exactly
             * in the order they have been retured by the database. */
            if ($idx == NULL) {
                while ($row = mysql_fetch_assoc($result)) {
                    $asr[] = $row;
                }
            } else {
                /* Assume that every fetched row contains a field with name given by
                 * $idx and use the value of that field as an index. */
                while ($row = mysql_fetch_assoc($result)) {
                    $asr[$row[$idx]] = $row;
                }
            }

            mysql_free_result($result);
        }

        return $asr;
    }

    function dbQuerySingle($query)
    {
        if ($this->debug) echo "<!-- QS:'" . $query . "' -->\n";
        $result = mysql_query($query);

        /* Is the result a meaningful `resource` or did an error occur? */
        if (!$result) {
            $error = "<p>Invalid query: <tt>" . mysql_error() . "</tt></p>\n";
            $error .= "<p>Query string: <tt>" . $query . "</tt></p>\n";
            logSystemError($error);
            throw new Exception ('Neplatný SQL dotaz.');
        }

        if (!($row = mysql_fetch_assoc($result))) {
            $row = NULL;
        }

        mysql_free_result($result);

        return $row;
    }

    function dbQueryMenuHier($parentId)
    {
        $resultset = $this->dbQuery("SELECT * FROM section WHERE parent='" . $parentId . "' ORDER BY position,title");

        if (isset ($resultset)) {
            foreach ($resultset as $key => $val) {
                $resultset[$key]['children'] = $this->dbQueryMenuHier($val['Id']);
            }
        }

        return $resultset;
    }

    function dbQueryArticleIdSet()
    {
        $resultset = $this->dbQuery("SELECT Id, title FROM articles ORDER BY title");

        $idSet = array();

        if (isset ($resultset)) {
            foreach ($resultset as $key => $val) {
                $idSet[$val['Id']] = stripslashes($val['title']);
            }
        }

        return $idSet;
    }

    function getNewsTypes()
    {
        return array(NEWS_SECTION => "Novinka k sekci",
            NEWS_ARTICLE => "Novinka ke článku");
    }

    function getNewsTypeString($newsTypeId)
    {
        switch ($newsTypeId) {
            case NEWS_SECTION:
                return "section";
            case NEWS_ARTICLE:
                return "article";
        }
        return "error";
    }

    function getCurrencies()
    {
        return $this->_currencies;
    }

    function getCurrencyString($currencyId)
    {
        return $this->_currencies[$currencyId];
    }

    function icon($filename)
    {
        $type = 'bin';

        $ext = strtolower(substr($filename, -3));
        switch ($ext) {
            case "rtf" :
                $ext = "doc";
            case "doc" :
            case "xls" :
            case "ppt" :
            case "pdf" :
                $type = $ext;
            default :
        }

        return $type;
    }

    function assignSettings()
    {
        /* Get settings data */
        $settings = $this->dbQuerySingle('SELECT * FROM settings WHERE Id=1');
        $this->assign('settings', $settings);
        return $settings;
    }

    function assignFileList($intId, $fileType)
    {
        $resultset = $this->dbQuery('SELECT * FROM files WHERE parent=' . $intId . ' AND type=' . $fileType . ' ORDER BY position,description');
        if (!empty ($resultset)) {
            foreach ($resultset as $key => $val) {
                $resultset[$key]['description'] = stripslashes($val['description']);
                $resultset[$key]['icon'] = $this->icon($val['filename']);
            }
            $this->assign('fileList', $resultset);
        }
    }

    function getArticleFiles($intId)
    {
        $resultset = $this->dbQuery('SELECT * FROM files WHERE parent=' . $intId . ' AND type=' . FT_A_DATA . ' ORDER BY position,description');
        if (!empty ($resultset)) {
            foreach ($resultset as $key => $val) {
                $resultset[$key]['description'] = stripslashes($val['description']);
                $resultset[$key]['icon'] = $this->icon($val['filename']);
            }
        }

        return $resultset;
    }

    function assignSectionArticles($smartyVar)
    {
        /* Get all sections */
        $sectionSet = $this->dbQuerySectionIdSet();

        $output = array();
        foreach ($sectionSet as $key => $val) {
            $resultset = $this->dbQuery('SELECT Id,title FROM articles WHERE parent=' . $key . ' ORDER BY position,title');
            if (!empty ($resultset)) {
                foreach ($resultset as $rkey => $rval) {
                    $resultset[$rkey]['title'] = stripslashes($rval['title']);
                }

                $r['sname'] = $val;
                $r['articles'] = $resultset;
                $output[] = $r;
            }
        }

        $this->assign($smartyVar, $output);
        return $output;
    }

    /**
     * @return boolean true if the current user has administrative privileges
     */
    function isAdmin()
    {
        return $this->userIsAdmin;
    }
}

?>
