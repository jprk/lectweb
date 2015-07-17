<?php

/*
 * Created on 13.2.2010
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

class SchoolYearBean extends BaseBean
{
    protected $schoolyear_start = -1;

    function doAdmin()
    {
        $this->schoolyear_start = SessionDataBean::getSchoolYear();
        $this->_smarty->assign('schoolyear_start', $this->schoolyear_start);
    }

    function doSave()
    {
        $this->schoolyear_start = $_POST['schoolyear_start'];
        SessionDataBean::setSchoolYear($_POST['schoolyear_start']);
        $this->_smarty->assign('schoolyear_start', $this->schoolyear_start);
    }
}

?>
