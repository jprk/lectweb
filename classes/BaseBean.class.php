<?php

/* Task types.
 * Copy of this list is a list of constants in TaskBean. This place is
 * considered deprecated but unless a significant redesign of the system
 * takes place we still need these constants here. */
define ('TT_WEEKLY_FORM', 100);
define ('TT_WEEKLY_SIMU', 101);
define ('TT_WEEKLY_ZIP', 102);
define ('TT_WEEKLY_PDF', 103);
define ('TT_WEEKLY_TF', 104);
define ('TT_LECTURE_PDF', 105);
define ('TT_SEMESTRAL', 200);
define ('TT_ACTIVITY', 300);
define ('TT_WRITTEN', 400);

/* Handler return values. */
define ('RET_OK', 0);
define ('ERR_INVALID_ACTION', 1);
define ('ERR_FILE_UPLOAD_ATTACK', 2);
define ('ERR_NO_FILE_SPECIFIED', 3);
define ('ERR_ADMIN_MODE', 4); // error catched, display admin menu
define ('ERR_INVALID_ID', 5);

class BaseBean
{
    private $_assignList;

    protected $_debugMode;
    protected $_smarty;

    protected $action;
    protected $object;
    protected $returntoparent;
    protected $errmsg;
    protected $schoolyear;


    /* -------------------------------------------------------------------
       INTERNAL FUNCTIONS
       ------------------------------------------------------------------- */
    /** Retun a list of available task types.
     * TODO: Remove this instance, it belongs to TaskBean.
     */
    function _getTaskTypes()
    {
        return array(
            0 => "Vyberte ze seznamu ...",
            TT_ACTIVITY => "Aktivita na cvičeních",
            TT_WRITTEN => "Písemný test",
            TT_WEEKLY_FORM => "Individuální týdenní úloha (vyplňovací formulář A-F)",
            TT_WEEKLY_SIMU => "Individuální týdenní úloha (Simulink *.mdl + *.pdf)",
            TT_WEEKLY_ZIP => "Individuální týdenní úloha (kód jako *.zip + *.pdf)",
            TT_WEEKLY_PDF => "Individuální týdenní úloha (jeden soubor *.pdf)",
            TT_LECTURE_PDF => "Hromadná týdenní úloha (jeden soubor *.pdf)",
            TT_WEEKLY_TF => "Povinná týdenní úloha (formulář s přenosovou fcí)",
            TT_SEMESTRAL => "Semestrální úloha"
        );
    }

    /* -------------------------------------------------------------------
	   CONSTRUCTOR
	   ------------------------------------------------------------------- */
    function __construct(&$smarty, $action, $object)
    {
        $this->action = $action;
        $this->object = $object;
        $this->_smarty = & $smarty;
        $this->errmsg = '';

        $this->_assignList = array();
        if ($smarty) $this->_debugMode = $smarty->debug;

        /* School year is stored as a session variable. */
        $this->schoolyear = SessionDataBean::getSchoolYear();
    }

    /* -------------------------------------------------------------------
       GETTERS
       ------------------------------------------------------------------- */
    function getAction()
    {
        return $this->action;
    }

    function getObject()
    {
        return $this->object;
    }

    function getErrorMessage()
    {
        return $this->errmsg;
    }

    /* -------------------------------------------------------------------
       HANDLERS
       ------------------------------------------------------------------- */
    function doShow()
    {
    }

    function doEdit()
    {
    }

    function doSave()
    {
    }

    function doDelete()
    {
    }

    function doRealDelete()
    {
    }

    function doAdmin()
    {
    }

    function doVerify()
    {
    }

    function preHandlerExec()
    {
    }

    /* Based on the value of HTTP POST variable 'doShow', call either
       ADMIN or SHOW handler. This can be used by child classes to
       gove users choce what shall the application do after editing
       data ( => either display administrative interface or display
       the edited object again ). */
    function doShowOrAdmin()
    {
        if (!empty ($_POST['doShow'])) {
            $this->doShow();
        } else {
            $this->doAdmin();
        }
    }

    /* Processes just the $_GET or $_POST parameter 'returntoparent'.
       This is typically called from doDelete(), doRealDelete() and
       processGetVars(), that is why we have it as a separate function. */
    function processReturnToParent()
    {
        assignGetIfExists(
            $this->returntoparent, $this->rs,
            'returntoparent', false, '', 0);
        if (!empty ($_POST['returntoparent'])) {
            $this->returntoparent =
                $this->rs['returntoparent'] = $_POST['returntoparent'];
        }
    }

    function actionHandler()
    {
        /* This function can execute code that has to be executed
           every time the handler is called. */
        $this->preHandlerExec();
        /* Now handle the specific action and return an error code
           if there is no handler available. */
        switch ($this->action) {
            case "show":
                $this->doShow();
                break;
            case "edit":
                $this->doEdit();
                break;
            case "save":
                $this->doSave();
                break;
            case "delete":
                $this->doDelete();
                break;
            case "realdelete":
                $this->doRealDelete();
                break;
            case "admin":
                $this->doAdmin();
                break;
            case "verify":
                $this->doVerify();
                break;
            default:
                $msg = "Unknown action '" . $this->action .
                    "' (" . get_class($this) . ")";
                trigger_error($msg);
                logSystemError($msg);
                return ERR_INVALID_ACTION;
        }

        /* Process pending variable assignments. */
        $this->processAssignments();

        return RET_OK;
    }

    /* -------------------------------------------------------------------
       ASSIGNMENT
       ------------------------------------------------------------------- */
    function assign($name, $var)
    {
        $this->_smarty->assign($name, $var);
    }

    function processAssignments()
    {
        foreach ($this->_assignList as $name => $var) {
            $this->_smarty->assign($name, $var);
        }
    }

    function assignTaskTypeSelect()
    {
        $this->_smarty->assign('taskTypeSelect', $this->_getTaskTypes());
    }

    function updateId()
    {
        /* New records have initial 'id' equal to zero and the proper value is
           set by the database engine. Sometimes however we need the new 'id'
           after we have written the data into the database. */
        if ((integer)($this->id) == 0) {
            $this->id = $this->rs['id'] = mysql_insert_id();
        }
    }

    /* -------------------------------------------------------------------
       HELPER FUNCTIONS
       ------------------------------------------------------------------- */
    function dumpSmarty()
    {
        if ($this->_debugMode) {
            echo "<!--";
            print_r($this->_smarty);
            echo "-->";
        }
    }

    function dumpThis()
    {
        if ($this->_debugMode) {
            echo "<!-- CLASS\n";
            print_r($this);
            echo "\n-->";
        }
    }

    function dumpVar($name, $val)
    {
        if ($this->_debugMode) {
            echo "<!--\n";
            echo $name . ":\n";
            print_r($val);
            echo "\n-->\n";
        }
    }

    function comment($text)
    {
        echo "<!-- $text -->\n";
    }
}

?>
