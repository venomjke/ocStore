<?php
/*

На данный момент данный драйвер находится в состоянии зачатка разработки
и скорее всего абсолютно неработоспособен.
Его использование КРАЙНЕ не рекомендуется.

*/

final class PostgreSQL {
	private $connection;

	public function __construct($hostname, $username, $password, $database) {
		if (!$this->connection = pg_connect($hostname, $username, $password)) {
      		exit('Error: Could not make a database connection using ' . $username . '@' . $hostname);
    	}

    	if (!pg_select_db($database, $this->connection)) {
      		exit('Error: Could not connect to database ' . $database);
    	}

		pg_query("SET NAMES 'utf8'", $this->connection);
		pg_query("SET CHARACTER SET utf8", $this->connection);
		pg_query("SET CHARACTER_SET_CONNECTION=utf8", $this->connection);
		pg_query("SET SQL_MODE = ''", $this->connection);
  	}

  	public function query($sql) {
		$resource = pg_query($sql, $this->connection);

		if ($resource) {
			if (is_resource($resource)) {
				$i = 0;

				$data = array();

				while ($result = pg_fetch_assoc($resource)) {
					$data[$i] = $result;

					$i++;
				}

				pg_free_result($resource);

				$query = new stdClass();
				$query->row = isset($data[0]) ? $data[0] : array();
				$query->rows = $data;
				$query->num_rows = $i;

				unset($data);

				return $query;
    		} else {
				return TRUE;
			}
		} else {
			exit('Error: ' . pg_error($this->connection) . '<br />Error No: ' . pg_errno($this->connection) . '<br />' . $sql);
    	}
  	}

	public function escape($value) {
		return pg_real_escape_string($value, $this->connection);
	}

  	public function countAffected() {
    	return pg_affected_rows($this->connection);
  	}

  	public function getLastId() {
    	return pg_insert_id($this->connection);
  	}

	public function __destruct() {
		pg_close($this->connection);
	}
}
?>