<?php
/*

На данный момент данный драйвер находится в стадии зачатка разработки.
Его использование КРАЙНЕ не рекомендуется.

*/
final class MySQLi {
	private $connection;

	public function __construct($hostname, $username, $password, $database) {
		if (!$this->connection = mysqliconnect($hostname, $username, $password)) {
      		exit('Error: Could not make a database connection using ' . $username . '@' . $hostname);
    	}

    	if (!mysqliselect_db($database, $this->connection)) {
      		exit('Error: Could not connect to database ' . $database);
    	}

		mysqliquery("SET NAMES 'utf8'", $this->connection);
		mysqliquery("SET CHARACTER SET utf8", $this->connection);
		mysqliquery("SET CHARACTER_SET_CONNECTION=utf8", $this->connection);
		mysqliquery("SET SQL_MODE = ''", $this->connection);
  	}

  	public function query($sql) {
		$resource = mysqliquery($sql, $this->connection);

		if ($resource) {
			if (is_resource($resource)) {
				$i = 0;

				$data = array();

				while ($result = mysqlifetch_assoc($resource)) {
					$data[$i] = $result;

					$i++;
				}

				mysqlifree_result($resource);

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
			exit('Error: ' . mysqlierror($this->connection) . '<br />Error No: ' . mysqlierrno($this->connection) . '<br />' . $sql);
    	}
  	}

	public function escape($value) {
		return mysqlireal_escape_string($value, $this->connection);
	}

  	public function countAffected() {
    	return mysqliaffected_rows($this->connection);
  	}

  	public function getLastId() {
    	return mysqliinsert_id($this->connection);
  	}

	public function __destruct() {
		mysqliclose($this->connection);
	}
}
?>