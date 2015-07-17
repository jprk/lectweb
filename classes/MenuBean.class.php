<?php

class MenuBean
{
    var $_smarty;
    var $_sectionBean;

    function __construct(&$smarty, $object, $action)
    {
        $this->_smarty = & $smarty;
        $this->_sectionBean = new SectionBean (0, $smarty, $object, $action);
    }

    /* Sets the Smarty variable $topmenu to the list of toplevel sections. */
    function setTopLevel()
    {
        $topmenu = $this->_sectionBean->dbQueryTopLevel();
        $this->_smarty->assign('topmenu', $topmenu);
    }
}

?>
