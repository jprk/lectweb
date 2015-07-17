<?php

class TaskListBean extends ExerciseBean
{
    function dbQuerySingle()
    {
        $lecturerBean = new LecturerBean (0, $this->_smarty, "x", "x");;
        $lecturers = $lecturerBean->dbQueryLecturerMap();

        $this->dumpVar('lecturers', $lecturers);

        $list = ExerciseBean::dbQueryexerciseList();

        foreach ($list as $key => $val) {
            $lctId = $val['lecturer'];
            $list[$key]['lecturer'] = $lecturers[$lctId]['firstname'] . " " . $lecturers[$lctId]['surname'];
        }

        $this->rs = $list;
    }

    /* -------------------------------------------------------------------
       HANDLER: SHOW
       ------------------------------------------------------------------- */
    function doShow()
    {
        /* Query data of this section */
        $this->dbQuerySingle();

        /* The function above sets $this->rs to values that shall be
           displayed. By assigning $this->rs to Smarty variable 'exercise'
           we can fill the values of $this->rs into a template. */
        $this->_smarty->assign('exerciseList', $this->rs);
    }
}

?>
