<?php

class SessionDataBean
{

    /* Define all entries that will be stored in the session. */
    const SDB_LECTURE_DATA = 'lecture';
    const SDB_LAST_SECTION_ID = 'last_section_id';
    const SDB_SCHOOL_YEAR = 'school_year';
    const SDB_USER_DATA = 'user';

    /**
     * Constructor is empty in this case. This class has only static methods.
     */
    function __construct()
    {
    }

    /**
     * Contidionally initialise some parts of session storage.
     */
    static function conditionalInit($schoolYearStart)
    {
        /* Check the school year stored in the session. */
        if (!self::hasSchoolYear()) {
            /* If the school year is empty, set it to the current
               school year. */
            self::setSchoolYear($schoolYearStart);
        }

        /* Check the lecture id stored in the session. */
        if (!self::hasLecture()) {
            /* Make sure that we have some lecture identifier. The
               default layout has an id==0. */
            self::setDefaultLecture();
        }
    }

    /**
     * Clear the user information record stored in session data block.
     */
    static function clearUserInformation()
    {
        unset ($_SESSION[self::SDB_USER_DATA]);
        self::setUserRole(USR_ANONYMOUS);
    }

    /**
     * Return the array with parameters of the currently selected lecture.
     */
    static function getLecture()
    {
        return $_SESSION[self::SDB_LECTURE_DATA];
    }

    /**
     * Return the identifier of the currently selected lecture.
     */
    static function getLectureId()
    {
        return LectureBean::getId($_SESSION[self::SDB_LECTURE_DATA]);
    }

    /**
     * Return the identifier of the root section of the currently selected lecture.
     */
    static function getRootSection()
    {
        return LectureBean::getRootSectionFromData($_SESSION[self::SDB_LECTURE_DATA]);
    }

    /**
     * Return the code of the currently selected lecture.
     */
    static function getCode()
    {
        return LectureBean::getCode($_SESSION[self::SDB_LECTURE_DATA]);
    }

    /**
     * Return the user role number.
     */
    static function getUserRole()
    {
        return UserBean::getRole($_SESSION[self::SDB_USER_DATA]);
    }

    /**
     * Return the user's login.
     */
    static function getUserLogin()
    {
        return UserBean::getLogin($_SESSION[self::SDB_USER_DATA]);
    }

    /**
     * Return the user's login.
     */
    static function getUserFullName()
    {
        return UserBean::getFullName($_SESSION[self::SDB_USER_DATA]);
    }

    /**
     * Return the user's system identifier.
     */
    static function getUserId()
    {
        return UserBean::getId($_SESSION[self::SDB_USER_DATA]);
    }

    /**
     * Get the last visited section identifier.
     */
    static function getLastSectionId()
    {
        if (array_key_exists(self::SDB_LAST_SECTION_ID, $_SESSION))
            return $_SESSION[self::SDB_LAST_SECTION_ID];
        else
            return NULL;
    }

    /**
     * Return the current school year.
     */
    static function getSchoolYear()
    {
        return $_SESSION[self::SDB_SCHOOL_YEAR];
    }

    /**
     * Return true if the current school year has been set.
     */
    static function hasSchoolYear()
    {
        return isset ($_SESSION[self::SDB_SCHOOL_YEAR]);
    }

    /**
     * Return true if the current lecture has been set.
     */
    static function hasLecture()
    {
        return isset ($_SESSION[self::SDB_LECTURE_DATA]);
    }

    /**
     * Return true if we have a valid user data record.
     */
    static function hasUserData()
    {
        return isset ($_SESSION[self::SDB_USER_DATA]);
    }

    /**
     * Set the array with parameters of the currently selected lecture.
     */
    static function setLecture(&$lectureBean)
    {
        $_SESSION[self::SDB_LECTURE_DATA] = $lectureBean->getLectureData();
    }

    /**
     * Set or update the user role.
     */
    static function setUserRole($role)
    {
        UserBean::setRole($_SESSION[self::SDB_USER_DATA], $role);
        UserBean::setLogin($_SESSION[self::SDB_USER_DATA], 'anonymní');
    }

    /**
     * Update user data provided by login bean.
     */
    static function setUserInformation($loginBean)
    {
        $_SESSION[self::SDB_USER_DATA] = $loginBean->rs;
    }

    /**
     * Set the lecture id to be empty.
     */
    static function setDefaultLecture()
    {
        $fakeSmartyInstance = NULL;
        $lectureBean = new LectureBean (NULL, $fakeSmartyInstance, NULL, NULL);
        $lectureBean->_setDefaults();
        self::setLecture($lectureBean);
    }

    /**
     * Set the last visited section identifier.
     */
    static function setLastSectionId($lastSectionId)
    {
        $_SESSION[self::SDB_LAST_SECTION_ID] = $lastSectionId;
    }

    /**
     * Set the current school year.
     * The value is the year of the beginning of the current school year,
     * i.e. 2008 for 2008/2009.
     */
    static function setSchoolYear($schoolYear)
    {
        $_SESSION[self::SDB_SCHOOL_YEAR] = $schoolYear;
    }
}

?>