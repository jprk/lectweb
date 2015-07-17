<?php

class exerciseListBean extends ExerciseBean
{
    /* Constructor.
     The $id is the identifier of lecture that we list exercises for. */
    function __construct($id, &$smarty, $action, $object)
    {
        /* Call parent's constructor first */
        parent::__construct($id, $smarty, $action, $object);
    }

    function dbQuerySingle()
    {
    }

    function prepareexerciseListData()
    {
        /* Query the complete list of exercises. The ID of this bean denotes
           lecture that we shall display exercises for. */
        $exerciseBean = new ExerciseBean (0, $this->_smarty, "x", "x");
        $exerciseBean->assignFull($this->id, $this->schoolyear);

        /* Get the lecture data. */
        $lectureBean = new LectureBean ($this->id, $this->_smarty, "x", "x");
        $lectureBean->assignSingle();

        /* Get all active news for this lecture. We will display news that are
           related to the actual lecture, and news that are related to all
           exercises of this lecture. */
        $newsBean = new NewsBean (0, $this->_smarty, "x", "x");
        $newsBean->assignNewsForTypes($this->id, 0, 0, $this->id);

        /* If user is logged in, and has a role "Lecturer" or stronger,
           display also lecturer notes assigned to this lecture and also notes
           that are assigned to all exercises for this lecture. Do not
           display notes that are assigned to single exercises. */
        if (isRole(USR_LECTURER)) {
            $noteBean = new NoteBean (0, $this->_smarty, "x", "x");
            $noteBean->assignNotesForTypes($this->id, $this->id, 0);
        }
    }

    /* -------------------------------------------------------------------
       HANDLER: SHOW
       ------------------------------------------------------------------- */
    function doShow()
    {
        $this->prepareexerciseListData();
    }

    /* -------------------------------------------------------------------
       HANDLER: ADMIN
       ------------------------------------------------------------------- */
    function doAdmin()
    {
        $this->prepareexerciseListData();
    }
}

?>
