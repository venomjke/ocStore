<?php
final class MySQL_Cached {
	private $connection;
	private $cache;
	private $cachedquery;

	public function __construct($hostname, $username, $password, $database) {
		$this->cache = new Cache(DB_CACHED_EXPIRE);

		if (!$this->connection = mysql_pconnect($hostname, $username, $password)) {
      		exit('Error: Could not make a database connection using ' . $username . '@' . $hostname);

    	}

    	if (!mysql_select_db($database, $this->connection)) {
      		exit('Error: Could not connect to database ' . $database);
    	}

		mysql_query("SET NAMES 'utf8'", $this->connection);
		mysql_query("SET CHARACTER SET utf8", $this->connection);
		mysql_query("SET CHARACTER_SET_CONNECTION=utf8", $this->connection);
		mysql_query("SET SQL_MODE = ''", $this->connection);
  	}

  	public function query($sql) {
		    $isselect = 0;
		    $md5query = '';
		    $pos = stripos($sql, 'select ');
		    if ($pos == 0)
		    {
			$isselect = 1;
			$md5query = md5($sql);
			if ($query = $this->cache->get('sql_' . $md5query))
			{
			    if ($query->sql == $sql)
			    {
				if ($resetflag = $this->cache->get('sql_globalresetcache'))
				{
				    if ($resetflag <= $query->time)
				    {
					$this->cachedquery = $query;
					return($query);
				    };
				}
				else
				{
				    $this->cachedquery = $query;
				    return($query);
				};
			    };
			};
		    };



		    $resource = mysql_query($sql, $this->connection);

		    if ($resource) {
			    if (is_resource($resource)) {
				    $i = 0;

				    $data = array();

				    while ($result = mysql_fetch_assoc($resource)) {
					    $data[$i] = $result;

					    $i++;
				    }

				    mysql_free_result($resource);

				    $query = new stdClass();
				    $query->row = isset($data[0]) ? $data[0] : array();
				    $query->rows = $data;
				    $query->num_rows = $i;

				    unset($data);

				    if ($isselect == 1)
				    {
					$query->sql = $sql;
					$query->time = time();

					$this->cache->set('sql_' . $md5query, $query);
				    };
				    unset($this->cachedquery);

				    return $query;
			    } else {
				    return TRUE;
			    }
		    } else {
			exit('Error: ' . mysql_error($this->connection) . '<br />Error No: ' . mysql_errno($this->connection) . '<br />' . $sql);
		    }
  	}

	public function escape($value) {
		return mysql_real_escape_string($value, $this->connection);
	}

  	public function countAffected() {
	    if ($this->cachedquery)
	    {
		return $this->cachedquery->num_rows;
	    }
	    else
	    {
		return mysql_affected_rows($this->connection);
	    };
  	}

  	public function getLastId() {
    	return mysql_insert_id($this->connection);
  	}

	public function __destruct() {
		mysql_close($this->connection);
	}
}
?>