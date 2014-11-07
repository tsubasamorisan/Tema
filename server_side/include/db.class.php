<?php
/**
 * Contains the classes required to contact a MySQL server
 *
 * @author Gabriele Girelli <gabriele@filopoe.it>
 */

/**
 * MySQL ddatabase management
 * @since 0.2.0
 */
class C2MySQL {
	/**
	 * MySQL host name
	 * @var string
	 */
	var $host;
	
	/**
	 * MySQL host user
	 * @var string
	 */
	var $user;
	
	/**
	 * MySQL host password
	 * @var string
	 */
	var $pwd;
	
	/**
	 * MySQL database name
	 * @var string
	 */
	var $db_name;
	
	/**
	 * Connection id
	 * @var integer
	 */
	var $db_cnx_id;
	
	/**
	 * Error variable
	 * @var boolean
	 */
	var $connect_error;
	
	/**
	 * Connect to given database and server
     * @param string host (MySQL host name)
     * @param string dbUser (MySQL host user)
     * @param string dbPass (MySQL host password)
     * @param string dbName (MySQL database name)
	 * @return null
	 */
	function __construct($host, $user, $pwd, $db_name) {
		$this->host = $host;
		$this->user = $user;
		$this->pwd = $pwd;
		$this->db_name = $db_name;
		
		// Connect to MySQL server
		$this->connect2MySQL();
		// If no errors occurred, connect to database
		if( !$this->isError() ) { $this->connect2MySQL_db(); }
	}
	
	/**
	 * Connects to the server
	 * @return null (sets $this->connect_error)
	 */
	function connect2MySQL() {
		if(!$this->db_cnx_id = @mysql_connect($this->host, $this->user, $this->pwd)) {
            trigger_error('Impossible to contact the MySQL server.');
            $this->connectError = true;
		}
	}
	
	/**
	 * Connects to the database
	 * @return void (sets $this->connect_error)
	 */
	function connect2MySQL_db() {
		if(!@mysql_select_db($this->db_name, $this->db_cnx_id)) {
            trigger_error('Impossible to select the database.');
            $this->connectError = true;
		}
	}
	
	/**
	 * Identifies errors that occurred during the connection.
	 * @return boolean	true = an error has occured
	 */
	function isError() {
		// Evaluates errors based on the signal variable.
		if( $this->connect_error ) { return true; }
		
		// Evaluate MySQL errors
        $error = mysql_error($this->db_cnx_id);
        if ( empty($error) )
            return false;
        else
            return true;
	}
	
    /**
     * Returns a MySQLresult instance for data fetching
     * @param String $sql 	query to be executed
     * @return MySQLresult instance
     */
    function & query($sql) {
        if (!$queryResource = mysql_query($sql,$this->db_cnx_id))
            trigger_error ('Query fallita: ' . mysql_error($this->db_cnx_id) . ' SQL: ' . $sql);
        $result = new MySQLResult($this,$queryResource);
		return $result;
    }
	
	/**
	 * Locks a table
	 * @param String $table 	table name
	 * @param db instance 	$db
	 * @return null
	 */
	function lock($table, $db) {
		// Construct the query
		$sql = "LOCK TABLES " . $table . " WRITE";
		$db->query($sql);
	}
	
	/**
	 * Unlocks all tables
	 * @param db instance 	$db
	 * @return null
	 */
	function unlock($db) {
		$sql = "UNLOCK TABLES ";
		$db->query($sql);
	}
	
	/**
	 * Close the connection
	 * @return null
	 */
	function close(){
		if( !mysql_close($this->db_cnx_id) )
			trigger_error("Impossible to terminat the connection.\n\n" . mysql_error());
	}
}

/**
 * This class manages fetching data from MySQL databases
 * Called by C2MySQL->query($sql)
 * @access public
 * @see C2MySQL
 * @since 0.2.0
 */
class MySQLresult {
    /**
     * C2MySQL instance
     * @var MySQL instance
     */
    var $c2mysql;

    /**
     *  Result of the query, to fetch
     * @var resource
     */
    var $query;

    /**
     * @param C2MySQL instance $mysql
     * @param resource $query
     * @access public
     */
    function __construct(& $c2mysql,$query) {
        $this->c2mysql = & $c2mysql;
        $this->query = $query;
    }

    /**
     * Fetches a row
     * @return array 	fetched row
     * @return Boolean 	if no rows are left to be fetched, returns false
     */
    function fetch () {
        if ( $row = mysql_fetch_array($this->query,MYSQL_ASSOC) ) {
            return $row;
        } else if ( $this->size() > 0 ) {
            mysql_data_seek($this->query,0);
            return false;
        } else {
            return false;
        }
    }

    /**
     * @return int 	number of selected rows
     */
    function size () {
        return mysql_num_rows($this->query);
    }

    /**
     * @return int 	ID of the last inserted row
     */
    function insertID () {
        return mysql_insert_id($this->c2mysql->db_cnx_id);
    }
    
    /**
     * Looks for MySQL errors
     * @return Boolean
     */
    function isError () {
        return $this->c2mysql->isError();
    }
}

?>