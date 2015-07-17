<?php

class StudentPassGenBean extends DatabaseBean
{

    function _setDefaults()
    {
    }

    /* Constructor */
    function __construct($id, &$smarty, $action, $object)
    {
        /* Call parent's constructor first */
        parent::__construct($id, $smarty, "student", $action, $object);
    }

    /* ---------------------------------------------------------------------
       Generate a random password.
       --------------------------------------------------------------------- */
    function generateRandomPassword()
    {
        /* Password mask contains 'l' for lowecase letter, 'u' for uppercase
           letter, and 'n' for number. Our password will have four lowercase and
           uppercase letters and two numbers. */
        $pwMask = "lllluuuunn";

        /* Random shuffle of the password mask will move the position of some
           mask characters. */
        $pwMask = str_shuffle($pwMask);

        /* Prepare code boundaries. */
        $code_l_a = ord('a');
        $code_l_z = ord('z');
        $code_u_a = ord('A');
        $code_u_z = ord('Z');
        $code_0 = ord('0');
        $code_9 = ord('9');

        /* Loop over all characters in the mask string and generate random entries
           that correspond to the given mask character. */
        $ret = "";
        $length = strlen($pwMask);
        for ($i = 0; $i < $length; $i++) {
            switch ($pwMask[$i]) {
                case 'l':
                    $ret .= chr(mt_rand($code_l_a, $code_l_z));
                    break;
                case 'u':
                    $ret .= chr(mt_rand($code_u_a, $code_u_z));
                    break;
                case 'n':
                    $ret .= chr(mt_rand($code_0, $code_9));
                    break;
            }
            echo "<!-- '$pwMask' $i $pwMask[$i] '$ret' -->\n";
        }

        return $ret;
    }

    /* -------------------------------------------------------------------
       HANDLER: SHOW
       ------------------------------------------------------------------- */
    function doShow()
    {
        /* Check that the ID of the student may be displayed. */
        if (LoginBean::canShowStudentId($this->id)) {
            /* Query data of this student */
            $this->dbQuerySingle();

            /* The rest is taken care of by the following function. */
            $this->doShowWithoutQuery();
        } else {
            /* This user is not allowed to see private data of this particular
               student. Announce it. */
            $this->action = "error01";
        }
    }

    /* -------------------------------------------------------------------
       HANDLER: SAVE
       ------------------------------------------------------------------- */
    function doSave()
    {
        /* POST variable `pwreplace` contains a list of student ids that were
           marked for password (re)generation. */
        $pwreplace = $_POST["pwreplace"];
        /* The fuction shall provide a list of students with new passwords. The
           list generator will need a list of student ids as an input. */
        $ids = array();
        /* Loop over all items in `pwreplace`. */
        foreach ($pwreplace as $id => $val) {
            /* Generate random password. */
            $pass = $this->generateRandomPassword();
            /* Get the data of this student. */
            $sb = new StudentBean ($id, $this->_smarty, "", "");
            $sb->dbQuerySingle();
            /* Update the password in the database. */
            $sb->dbUpdatePassword($pass);
            /* And send an e-mail to the student that the password has been
               updated. */
            $sb->sendPassword($pass);
            /* Append the id to the list. */
            $ids[] = $id;
        }
        /* Use the last instance of StudentBean to assign a list of student with
           new passwords to Smarty variable `studentList`. */
        $sb->assignStudentIdList($ids);
    }

    /* -------------------------------------------------------------------
       HANDLER: DELETE
       ------------------------------------------------------------------- */
    function doDelete()
    {
    }

    /* -------------------------------------------------------------------
       HANDLER: REAL DELETE
       ------------------------------------------------------------------- */
    function doRealDelete()
    {
    }

    /* -------------------------------------------------------------------
       HANDLER: ADMIN
       ------------------------------------------------------------------- */
    function doAdmin()
    {
    }

    /* -------------------------------------------------------------------
       HANDLER: EDIT
       ------------------------------------------------------------------- */
    function doEdit()
    {
        /* Create a StudentBean instance and use it to fetch a list of students
           for the active lecture from the database. */
        $sb = new StudentBean (0, $this->_smarty, "", "");
        $sb->assignStudentListForLecture($this->id);
    }
}

?>
