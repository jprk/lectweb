<?php
// -----------------------------------------------------------------------
//   tools.php
//
//   Utility functions that do not deserve their own class.
// -----------------------------------------------------------------------

// Return current time in microseconds as a floating point number.
function getmicrotime()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}

// Remove HTML entities.
function unhtmlentities($string)
{
    $trans_tbl = get_html_translation_table(HTML_ENTITIES);
    $trans_tbl = array_flip($trans_tbl);
    return strtr($string, $trans_tbl);
}

// Add nonbreakable spaces after some Czech prepositions.
function vlnka($text)
{
    return preg_replace('/(\s[AIiUuKkSsVvOoZz])\s+(\w+)/', '$1&nbsp;$2', $text);
}

// Remove special HTML entities, convert all unallowed HTML characters into
// corresponding HTML entities and add nonbreakable spaces after some Czech
// prepositions.
function vlnkahtml($text)
{
    // Remove special HTML entities (&nbsp; is crucial for us)
    // PHP > 4.3.0 has a library function for this, but we are running 4.1.2
    $text = unhtmlentities($text);
    // Mask unallowed characters (&,<,>) as HTML entities
    $text = htmlspecialchars($text);
    return vlnka($text);
}

/* Remove all HTML tags (or subset defined by $entities) from the text in
   $field. */
function trimStrip($field, $entities = "")
{
    if ($entities == "") {
        return trim(strip_tags($field));
    } else {
        return trim(strip_tags($field, $entities));
    }
}

/* Determine MIME type of the file represented by $filename first by trying
   PHP internal mime_content_type() function and then by checking our own
   list of filename extensions. */
function mimetype($filename)
{
    $type = '';

    $ext = strtolower(substr($filename, -3));

    if (function_exists("mime_content_type")) {
        $type = mime_content_type($filename);
    }

    if ($type == '' || $type == 'unknown/unknown') {
        switch ($ext) {
            case "pdf" :
                $type = "application/pdf";
                break;
            case "doc" :
                $type = "application/vnd.ms-word";
                break;
            case "xls" :
                $type = "application/vnd.ms-excel";
                break;
            case "ppt" :
                $type = "application/vnd.ms-powerpoint";
                break;
            case "bmp" :
                $type = "image/bmp";
                break;
            case "gif" :
                $type = "image/gif";
                break;
            case "png" :
                $type = "image/x-png";
                break;
            case "jpg" :
                $type = "image/jpeg";
                break;
            case "dwg" :
                $type = "application/dwg";
                break;
            case "mdl" :
                $type = "application/vnd.matlab";
                break;
            default:
                $type = "unknown/unknown";
        }
    }

    if ($ext == "mdl") {
        $type = "application/vnd.matlab";
    }

    return $type;
}

/* Assign POST value to the specified variable. Call trimStrip() if
   required. */
function assignPostIfExists(&$var, &$rs, $postKey, $doTrim = false, $entities = "")
{
    if (isset ($_POST[$postKey])) {
        $postVal = $_POST[$postKey];
        if ($doTrim) {
            $postVal = trimStrip($postVal, $entities);
        }
        $var = $rs[$postKey] = $postVal;
    }
}

/* Assign an existing HTTP GET value to the specified variable. Call
   trimStrip() if required, optionally with a list of allowed entities. */
function assignGetIfExists(&$var, &$rs, $getKey, $doTrim = false, $entities = "", $defaultValue = "")
{
    if (isset ($_GET[$getKey])) {
        $getVal = $_GET[$getKey];
        if ($doTrim) {
            $getVal = trimStrip($getVal, $entities);
        }
        $var = $rs[$getKey] = $getVal;
    } else {
        if (!empty ($defaultValue)) {
            $var = $rs[$getKey] = $defaultValue;
        }
    }
}

/* Convert a list of entities into a comma-separated list of characters.
   Uses default value of '0' which can be ignored by specifying the
   second parameter to be `false`. */
function arrayToDBString(&$list, $defRet = true)
{
    /* Default return value */
    $ret = $defRet ? "0" : "";
    if (!empty ($list)) {
        $strList = implode($list, ",");
        if ($defRet) $ret = $ret . ",";
        $ret = $ret . $strList;

        //$firstVal = true;
        //foreach ( $list as $val )
        //{
        //	$ret = ( $firstVal && ! $defRet ) ? $val : $ret . "," . $val ;
        //	$firstVal = false;
        //}
    }

    return $ret;
}

/* Convert a list of entities indexted by $index into a comma-separated list of
   characters. Uses the default value of '0' which can be overriden by specifying the
   third parameter. */
function array2ToDBString(&$list, $index, $defRet = true)
{
    /* Default return value */
    $ret = $defRet ? "0" : "";
    if (!empty ($list)) {
        $firstVal = true;
        foreach ($list as $val) {
            $ret = ($firstVal && !$defRet) ? $val[$index] : $ret . "," . $val[$index];
            $firstVal = false;
        }
    }

    return $ret;
}

function numToDayString($num)
{
    switch ($num) {
        case  1:
            $name = "pondělí";
            break;
        case  2:
            $name = "úterý";
            break;
        case  3:
            $name = "středa";
            break;
        case  4:
            $name = "čtvrtek";
            break;
        case  5:
            $name = "pátek";
            break;
        case  6:
            $name = "sobota";
            break;
        case  7:
            $name = "neděle";
            break;
        case 11:
            $name = "liché pondělí";
            break;
        case 12:
            $name = "liché úterý";
            break;
        case 13:
            $name = "lichá středa";
            break;
        case 14:
            $name = "lichý čtvrtek";
            break;
        case 15:
            $name = "lichý pátek";
            break;
        case 16:
            $name = "lichá sobota";
            break;
        case 17:
            $name = "lichá neděle";
            break;
        case 21:
            $name = "sudé pondělí";
            break;
        case 22:
            $name = "sudé úterý";
            break;
        case 23:
            $name = "sudá středa";
            break;
        case 24:
            $name = "sudý čtvrtek";
            break;
        case 25:
            $name = "sudý pátek";
            break;
        case 26:
            $name = "sudá sobota";
            break;
        case 27:
            $name = "sudá neděle";
            break;
        default:
            $name = "?????";
    }

    return $name;
}

function numToDay($num)
{
    $nd = array();
    $nd['num'] = $num;
    $nd['name'] = numToDayString($num);

    return $nd;
}

function boolToYesNo($val)
{
    return $val ? "ano" : "ne";
}

function dumpToString($mixed = null)
{
    ob_start();
    var_dump($mixed);
    $content = ob_get_contents();
    ob_end_clean();
    return $content;
}

/* Converts Czech date format 'DD.MM.YYYY HH:MM:SS' into classical SQL
   format 'YYYY-MM-DD HH:MM:SS' . Time portion of the dateTime is optional.
   DateTime is supposed to be trimmed. */
function czechToSQLDateTime($dateTime)
{
    /* Default date. */
    $date = '0000-00-00 00:00:00';

    if (strlen($dateTime) > 0) {
        $dateArray = explode(".", $dateTime);

        $year = $dateArray[2];
        $yearArray = explode(" ", $year);

        $date = $yearArray[0] . "-" . $dateArray[1] . "-" . $dateArray[0];

        if (count($yearArray) > 1) {
            $date = $date . " " . $yearArray[1];
        }
    }

    return $date;
}

/* Givent the timestamp, returns another timestamp corresponding to the
   last second of a Sunday of the preceeding week. */
function previousWeekEnd($timestamp)
{
    $a = getdate($timestamp);
    return mktime(23, 59, 59, $a['mon'], $a['mday'] - $a['wday'] + 1);
}

/* Returns timestamp of the end of the current term. */
function termEnd()
{
    /* Timestamp of the end of this term. */
    $eTime = 0;

    /* Month beginnings. Winter term starts in the week that
       contains 1.10., summer term in the first week that
       contains 1.3. But maybe the algorithm is different, this
       is just a guess. */
    $mar01 = mktime(0, 0, 0, 3, 1);
    $oct01 = mktime(0, 0, 0, 10, 1);

    /* These are ends of winter and summer terms: Winter term
       ends the last Sunday before summer term starts and vice
       versa. */
    $wEnd = previousWeekEnd($mar01);
    $sEnd = previousWeekEnd($oct01);

    /* This is the current time. */
    $cTime = time();

    /* Now find out which part of the shool year we have today. */
    if ($cTime <= $wEnd) {
        /* Winter term of the school year that ends in this year. */
        $eTime = $wEnd;
    } elseif ($cTime <= $sEnd) {
        /* Summer term. */
        $eTime = $sEnd;
    } else {
        /* Winter term of the school year that started in this
           year. We have to find out when this term ends. */
        $cDate = getdate($cTime);
        $mar01ny = mktime(0, 0, 0, 3, 1, $cDate['year'] + 1);
        $eTime = previousWeekEnd($mar01ny);
    }

    return $eTime;
}

/* Return year in which the current term started. */
function yearOfTermStart()
{
    /* This is the current time. */
    $cTime = time();
    $cDate = getdate($cTime);

    /* The crucial point is the 1st of March of this year.
     Anything that started before the March 1st needs
     the youer number to be decreased by one.  */
    $mar01 = mktime(0, 0, 0, 3, 1);
    $wEnd = previousWeekEnd($mar01);

    /* Now find if we have passed the date or not. */
    if ($cTime < $wEnd) {
        /* Winter term. */
        $tYear = $cDate['year'] - 1;
    } else {
        /* Summer term. */
        $tYear = $cDate['year'];
    }
    return $tYear;
}

/* Return the year where the schoolyear started. */
function schoolYearStart()
{
    /* This is the current time. */
    $cTime = time();
    $cDate = getdate($cTime);

    /* Up to 2008/2009 we start the year on Monday of the week containing
     October 1. So if we call this function before Monday of that particular
     week, we shall return the current year decreased by one. */
    $oct01 = mktime(0, 0, 0, 10, 1);
    $wEnd = previousWeekEnd($oct01);

    /* Now find if we have passed the date or not. */
    if ($cTime < $wEnd) {
        /* We are still in the schoolyear that ends this year. */
        $tYear = $cDate['year'] - 1;
    } else {
        /* We are already in the schoolyear that started this year. */
        $tYear = $cDate['year'];
    }
    return $tYear;
}

/* Return a boolean identifier of the role. */
function isRole($roleId)
{
    return ((integer)SessionDataBean::getUserRole() == (integer)$roleId) ? TRUE : FALSE;
}

/**
 * Return `true` if the current user has logged in.
 * Logged-in users have some role assigned and the role is not USR_ANONYMOUS.
 */
function isLoggedIn()
{
    /* Role may be also empty. */
    return (SessionDataBean::getUserRole() !== USR_ANONYMOUS);
}

/**
 * Return a default value if the first parameter is an empty string.
 * Used to give a meaningful value to student responses.
 */
function returnDefault($var, $default = 0)
{
    $var = strtr($var, ',', '.');
    return (trim($var) === '' ? $default : $var);
}

/**
 * aryel at iku dot com dot br
 * 29-Jan-2008 11:40
 * http://cz2.php.net/manual/en/function.debug-backtrace.php
 */
function getDebugBacktrace($stackTrace = NULL, $pfx = "<li>", $sfx = "</li>\n")
{
    $dbgMsg = '';
    $dbgMsgList = getDebugBacktraceList($stackTrace);
    foreach ($dbgMsgList as $dbgInfo) {
        $dbgMsg .= $pfx . $dbgInfo . $sfx;
    }
    /* Return the backtrace. */
    return $dbgMsg;
}

/**
 * Return debug backtrace as an array of strings.
 * Modified from:
 * aryel at iku dot com dot br
 * 29-Jan-2008 11:40
 * http://cz2.php.net/manual/en/function.debug-backtrace.php
 */
function getDebugBacktraceList($dbgTrace = NULL)
{
    /* If user did not supply their own stack trace (for example, the backtrace
     * associated with an exception), fetch it here. */
    if ($dbgTrace === NULL) {
        $dbgTrace = debug_backtrace();
    }
    $dbgList = array();
    foreach ($dbgTrace as $dbgIndex => $dbgInfo) {
        /* As $dbgInfo['args'] could be an array, we have to preprocess
           it to a string. */
        $args = array();
        foreach ($dbgInfo['args'] as $key => $val) {
            $args[$key] = print_r($val, true);
        }
        $dbgList[] = "at " . $dbgInfo['file'] . " (line {$dbgInfo['line']}) -> {$dbgInfo['function']}(" . join(",", $args) . ")";
    }
    /* Return the backtrace list. */
    return $dbgList;
}

/**
 * Log an error into the webserver error log and send an e-mail.
 */
function logSystemError($errorMsgHTML, $stackTrace = NULL)
{
    /* Get the backtrace in HTML and plain text format. */
    $dbgTraceHTML = getDebugBacktrace($stackTrace);
    $dbgTraceList = getDebugBacktraceList($stackTrace);

    /* Get the lecture data. */
    $lecData = SessionDataBean::getLecture();
    $lecCode = $lecData['code'];

    /* Get the user login and role. */
    $login = SessionDataBean::getUserLogin();
    $role = UserBean::getRoleName(SessionDataBean::getUserRole());

    /* Log the text version of the log the error log. It will occupy several
       log lines so we will distinguish the information by using the request
       time as the first part of the information.*/
    $timestamp = $_SERVER["REQUEST_TIME"];
    foreach ($dbgTraceList as $stackElem) {
        error_log("[$timestamp] " . $stackElem, 0);
    }
    error_log("[$timestamp] lecture=$lecCode", 0);
    error_log("[$timestamp] user=$login, role=$role", 0);

    /* Send an error e-mail in HTML form as well. */
    $errorMsgHTML = "<html>\n<body>\n" . $errorMsgHTML;
    if (!empty ($dbgTraceList)) {
        $errorMsgHTML .= "<p>Stack trace:</p>";
        $errorMsgHTML .= "<ol>\n";
        $errorMsgHTML .= $dbgTraceHTML;
        $errorMsgHTML .= "</ol>\n";
    } else {
        $errorMsgHTML .= "<p>No stack trace available for this error.</p>";
    }
    $errorMsgHTML .= "<p>Lecture: <tt>" . $lecCode . "</tt><br/>\n";
    $errorMsgHTML .= "User login: <tt>" . $login . "</tt><br/>\n";
    $errorMsgHTML .= "User role: <tt>" . $role . "</tt></p>\n";
    $errorMsgHTML .= "Request URI: <tt>" . $_SERVER['REQUEST_URI'] . "</tt></p>\n";
    $errorMsgHTML .= "</body>\n</html>\n";
    error_log($errorMsgHTML, 1, ADMIN_EMAIL, 'Content-type: text/html; charset=utf-8');
}

/* -------------------------------------------------------------------------
 *  MUTEX CODE 
 * ------------------------------------------------------------------------- */

/** Define return codes of mutex routines. */
define ('MUTEX_OK', 0);
define ('MUTEX_E_CANTACQUIRE', -1);
define ('MUTEX_E_ISLOCKED', -2);
define ('MUTEX_E_FTOK', -3);
define ('MUTEX_LOCK_STOLEN_OK', 1);

/**
 * Get the lock file name.
 */
function lockFileName($className, $resourceId)
{
    return '/tmp/_' . $className . '.' . $resourceId . '.lock';

}

/**
 * Lock the access to a resource.
 * Uses a rather complicated non-blocking mutex construct requiring a
 * semaphore and a temporary file.
 */
function mutexLock($class, $resourceId, &$lockTime, &$lockLogin)
{
    /* Get the name of the class. */
    $className = get_class($class);

    /* Get the name of the class file. */
    $classFile = REQUIRE_DIR . $className . '.class.php';

    /* Construct a semaphore id. */
    $semId = ftok($classFile, $resourceId);
    if ($semId < 0) {
        /* Call to ftok() failed. Return with error. */
        return MUTEX_E_FTOK;
    }

    /* Get the semaphore. */
    $semaphore = sem_get($semId);

    /* Unconditional blocking wait for the semaphore. */
    if (sem_acquire($semaphore)) {
        /* Create the name of a temporary file that will be used to implement
           the lock. */
        $lockFile = lockFileName($className, $resourceId);
        echo "<!-- lock file: " . $lockFile . " -->\n";

        /* Timestamp of the lock file. */
        $lockTime = @filemtime($lockFile);

        /* Check if the file exists and if the lock is not stale. */
        if (file_exists($lockFile) && ((time() - $lockTime) < 1800)) {
            /* Read the login of the user that owns the lock. It is
               stored in the file. */
            $fs = fopen($lockFile, 'r');
            $lockLogin = fgets($fs);
            fclose($fs);

            /* Compare it to the name of the current user. If they are
               the same, let the user continue editing. */
            if (strcmp($lockLogin, SessionDataBean::getUserLogin())) {
                /* Different logins, the resource is locked. */
                $ret = MUTEX_E_ISLOCKED;
            } else {
                /* Same logins, update the modification time of the lock
                   file and allow access to the resource. */
                @touch($lockFile);
                $ret = MUTEX_OK;
            }
        } else {
            /* The resource is not locked at all or the lock is stale. */
            if (file_exists($lockFile)) {
                /* Read the login of the user that owns the lock. It is
                   stored in the file. */
                $fs = fopen($lockFile, 'r');
                $lockLogin = fgets($fs);
                fclose($fs);

                $ret = MUTEX_LOCK_STOLEN_OK;
            } else {
                $ret = MUTEX_OK;
            }

            /* Write the login of the user that own the lock into the
               lock file. */
            $fs = fopen($lockFile, 'w');
            fputs($fs, SessionDataBean::getUserLogin());
            fclose($fs);
        }

        /* And finally release the semaphore so that other threads may check
           the existence of the lock file. */
        sem_release($semaphore);
    } else {
        /* We could not acquire the semaphore. */
        $ret = MUTEX_E_CANTACQUIRE;
    }

    return $ret;
}

/**
 * Unlock the shared resource is locked.
 * Uses a rather complicated non-blocking mutex construct requiring a
 * semaphore and a temporary file.
 */
function mutexUnlock($class, $resourceId)
{
    /* Get the name of the class. */
    $className = get_class($class);

    /* Get the name of the class file. */
    $classFile = REQUIRE_DIR . $className . '.class.php';

    /* Construct a semaphore id. */
    $semId = ftok($classFile, $resourceId);
    if ($semId < 0) {
        /* Call to ftok() failed. Return with error. */
        return MUTEX_E_FTOK;
    }

    /* Get the semaphore. */
    $semaphore = sem_get($semId);

    /* Unconditional blocking wait for the semaphore. */
    if (sem_acquire($semaphore)) {
        /* Create the name of a temporary file that will be used to implement
           the lock. */
        $lockFile = lockFileName($className, $resourceId);
        echo "<!-- unlock file: " . $lockFile . " -->\n";

        /* Release the lock. */
        @unlink($lockFile);

        /* Return success. */
        $ret = MUTEX_OK;

        /* And finally release the semaphore so that other threads may check
           the existence of the lock file. */
        sem_release($semaphore);
    } else {
        /* We could not acquire the semaphore. */
        $ret = MUTEX_E_CANTACQUIRE;
    }

    return $ret;
}

?>
