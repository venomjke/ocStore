<?php
final class PostgreSQL {
    private $connection;

/*
TODO:
Сделать с классами коннекта нечто типа такого (и обсудить с babushka):

$conn_url = parse_url($config['url']);

$scheme	= urldecode($conn_url['scheme'])
if ($scheme =~ "pg" or $scheme =~ "postgr") {
	$cf_scheme = "pg";
} else {
	$cf_scheme = $scheme;
}

if (defined('USE_PCONNECT') && (USE_PCONNECT == 'true')) {
	$cf_pers = "p";
} else {
	$cf_pers = "";
}

$cf = $cf_scheme.'_'.$cf_pers.'connect';
$conn_str = "";

if (isset($url['host'])) {
	$conn_str .= 'host='. urldecode($url['host']);
}
if (isset($url['port'])) {
	$conn_str .= ' port='. urldecode($url['port']);
}
if (isset($url['user'])) {
	$conn_str .= ' user='. urldecode($url['user']);
}
if (isset($url['pass'])) {
	$conn_str .= ' password='. urldecode($url['pass']);
}
if (isset($url['path'])) {
	$conn_str .= ' dbname='. substr(urldecode($url['path']), 1);
}

blabla connect = @$cf($conn_str)

TODO2:
Сделать проверки версии и т.п. типа такого:
function version($scheme) {
	if ($scheme == "pg") {
		return query("SHOW SERVER_VERSION");
	} else {
		<... для других ...>
	}
}

if (!function_exists('pg_connect')) {
exit('Unable to use the PostgreSQL database because the PostgreSQL extension for PHP is not installed. Check your <code>php.ini</code> to see how you can enable it.';
}

*/

public function __construct($hostname, $username, $password, $database) {
	if (!$this->is_connected === true) {
		$this->connect($hostname, $username, $password, $database);
	}
}

public function connect($hostname, $username, $password, $database)
	if (defined('USE_PCONNECT') && (USE_PCONNECT == 'true')) {
		$connect_function = 'pg_pconnect';
	} else {
		$connect_function = 'pg_connect';
	}

	if ($this->connection = @$connect_function('host='.$hostname.' dbname='.$database.' user='.$username.' password='.$password)) {
		$this->is_connected=true;
		pg_query($this->connection,'set client_encoding="UTF8"');
	} else {
		exit('Error: Could not make a database connection using ' . $username . '@' . $hostname);
	}
}

/*
    public function __construct($hostname, $username, $password, $database) {
	if (!$this->connection = pg_pconnect('host='.$hostname.' dbname='.$database.' user='.$username.' password='.$password)) {
	    exit('Error: Could not make a database connection using ' . $username . '@' . $hostname);

	}
    }
*/

    public function query($sql) {

	    $newsql = $sql;
	    $isselect = 0;
	    $md5query = '';
	    $pos = stripos($sql, 'select ');
	    if ($pos == 0)
	    {
		$isselect = 1;
		$newsql = preg_replace('/^(.+) LIMIT ([0-9]+),([0-9]+)$/i', '\1 LIMIT \3 OFFSET \2', $sql);
		$sql = $newsql;
	    };

	    $sql = preg_replace('/"/', '\'', $sql);
	    $sql = preg_replace('/`/', '"', $sql);
	    $sql = preg_replace('/&&&quote&&&/', '"', $sql);
	    $sql = preg_replace('/\'0000-00-00\'/', '\'0001-01-01\'', $sql);
	    $sql = preg_replace('/\'0000-00-00 ([0-9\:]+)\'/', '\'0001-01-01 \1\'', $sql);

	    $resource = pg_query($this->connection, $sql);

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
		exit('Error: ' . pg_last_error($this->connection) . ': ' . $sql);
	    }
    }

    public function escape($value) {
	$s = pg_escape_string($value);
	$s = preg_replace('/"/', '&&&quote&&&', $s);

	return $s;
    }

    public function countAffected() {
	return pg_affected_rows($this->connection);
    }

    public function getLastId() {
	$lastval = $this->query('select lastval() as lastval');
	return $lastval->row['lastval'];
    }

    public function __destruct() {
	pg_close($this->connection);
    }

}
?>