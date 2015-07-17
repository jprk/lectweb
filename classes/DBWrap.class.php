<?php

class DBWrap
{
    private $_config; // Holds configuration options
    /** @var  mysqli $mysqli */
    private $mysqli; // Holds the active database link
    private $_host; // Hostname of the database server
    private $_user; // Username of the database user
    private $_pass; // Password of the database user
    private $_database; // Name of the database that we shall connect to

    function __construct($host, $user, $pass, $database)
    {
        $this->_host = $host;
        $this->_user = $user;
        $this->_pass = $pass;
        $this->_database = $database;
    }

    /* Open a database connection */
    function dbOpen()
    {
        /* Try to connect to the database. Die if the database connection or database selection cannot be established. */
        $this->mysqli = new mysqli($this->_host, $this->_user, $this->_pass, $this->_database);
        if ($this->mysqli->connect_errno)
        {
            die ( "Failed to connect to MySQL: " . $this->mysqli->connect_error );
        }
    }

    /* Close database connection. */
    function dbClose()
    {
        $this->mysqli->close();
    }

    /* Query the database and process the result */
    function dbQuery($query)
    {
        $result = $this->mysqli->query($query);
        if (!$result)
        {
            die ("ERROR in query: " . $this->mysqli->error);
        }

        $asr = null;

        if ( ! is_bool ( $result ))
        {
            $asr = $result->fetch_all(MYSQLI_ASSOC);
            $result->close();
        }

        return $asr;
    }

    function dbQuerySingle($query)
    {
        $result = $this->mysqli->query($query);
        if (!$result)
        {
            die ("ERROR in query: " . $this->mysqli->error);
        }

        $row = $result->fetch_assoc();
        $result->close();

        return $row;
    }

    function getInstance()
    {
        return $this->mysqli;
    }
}

?>
