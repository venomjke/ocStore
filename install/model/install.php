<?php
class ModelInstall extends Model {
	public function mysql($data) {
		$db = new DB('mysql', $data['db_host'], $data['db_user'], $data['db_password'], $data['db_name']);

		$file = DIR_APPLICATION . 'opencart.sql';

		if (!file_exists($file)) {
			exit('Could not load sql file: ' . $file);
		}

		$lines = file($file);

		// Если флаг есть, очистить БД
		if(isset($this->request->post['flushdbflag']) AND $this->request->post['flushdbflag'] == 1) {
			$this->flushDatabase($connection);
		}
		if ($lines) {
			$query = '';
			foreach($lines as $line) {
				if ($line && (substr($line, 0, 2) != '--') && (substr($line, 0, 1) != '#')) {
					$query .= $line;
					if (preg_match('/;\s*$/', $line)) {
						$query = str_replace("DROP TABLE IF EXISTS `oc_", "DROP TABLE IF EXISTS `" . $data['db_prefix'], $query);
						$query = str_replace("CREATE TABLE `oc_", "CREATE TABLE `" . $data['db_prefix'], $query);
						$query = str_replace("INSERT INTO `oc_", "INSERT INTO `" . $data['db_prefix'], $query);
						$db->query($query);
						$query = '';
					}
				}
			}
			$db->query("SET CHARACTER SET utf8")
			$db->query("SET @@session.sql_mode = 'MYSQL40'");
			$db->query("DELETE FROM `" . $data['db_prefix'] . "user` WHERE user_id = '1'");
			$db->query("INSERT INTO `" . $data['db_prefix'] . "user` SET user_id = '1', user_group_id = '1', username = '" . $db->escape($data['username']) . "', salt = '" . $db->escape($salt = substr(md5(uniqid(rand(), true)), 0, 9)) . "', password = '" . $db->escape(sha1($salt . sha1($salt . sha1($data['password'])))) . "', status = '1', email = '" . $db->escape($data['email']) . "', date_added = NOW()");
			$db->query("DELETE FROM `" . $data['db_prefix'] . "setting` WHERE `key` = 'config_email'");
			$db->query("INSERT INTO `" . $data['db_prefix'] . "setting` SET `group` = 'config', `key` = 'config_email', value = '" . $db->escape($data['email']) . "'");
			$db->query("DELETE FROM `" . $data['db_prefix'] . "setting` WHERE `key` = 'config_url'");
			$db->query("INSERT INTO `" . $data['db_prefix'] . "setting` SET `group` = 'config', `key` = 'config_url', value = '" . $db->escape(HTTP_OPENCART) . "'");
			$db->query("DELETE FROM `" . $data['db_prefix'] . "setting` WHERE `key` = 'config_encryption'");
			$db->query("INSERT INTO `" . $data['db_prefix'] . "setting` SET `group` = 'config', `key` = 'config_encryption', value = '" . $db->escape(md5(mt_rand())) . "'");
			$db->query("UPDATE `" . $data['db_prefix'] . "product` SET `viewed` = '0'");
		}
	}

	public function pgsql($data) {
		$connection = pg_pconnect('host='.$data['db_host'].' dbname='.$data['db_name'].' user='.$data['db_user'].' password='.$data['db_password']);

		// Если флаг есть, очистить БД
		if(isset($this->request->post['flushdbflag']) AND $this->request->post['flushdbflag'] == 1) {
			$this->flushDatabase($connection, 'pgsql');
		}

		$file = DIR_APPLICATION . 'opencart.pg.sql';
		if ($sql = file($file)) {
			$query = '';
			$isfunc = 0;
			foreach($sql as $line) {
				$tsl = trim($line);
				if (($sql != '') && (substr($tsl, 0, 2) != "--") && (substr($tsl, 0, 1) != '#')) {
					$query .= $line;
					if (preg_match('/^CREATE .*FUNCTION /', $line))
					{
						$isfunc = 1;
					};
					if (preg_match('/;\s*$/', $line)) {
						if (($isfunc == 0) || preg_match('/\$\$ LANGUAGE sql;$/', $line))
						{
							$query = str_replace("DROP TABLE IF EXISTS \"oc_", "DROP TABLE IF EXISTS \"" . $data['db_prefix'], $query);
							$query = str_replace("CREATE TABLE \"oc_", "CREATE TABLE \"" . $data['db_prefix'], $query);
							$query = str_replace("INSERT INTO \"oc_", "INSERT INTO \"" . $data['db_prefix'], $query);
							$result = pg_query($connection, $query);
							if (!$result) {
								die(pg_last_error());
							}
							$query = '';
							$isfunc = 0;
						};
					}
				}
			}
			pg_query($connection, "SET client_encoding TO \"UTF-8\";");
			pg_query($connection, "DELETE FROM \"" . $data['db_prefix'] . "user\" WHERE user_id = 1");
			pg_query($connection, "INSERT INTO \"" . $data['db_prefix'] . "user\" (user_id, user_group_id, username, password, status, date_added) VALUES (1, 1, '" . pg_escape_string($data['username']) . "', '" . pg_escape_string(md5($data['password'])) . "', 1, NOW())");
			pg_query($connection, "DELETE FROM \"" . $data['db_prefix'] . "setting\" WHERE \"key\" = 'config_email'");
			pg_query($connection, "INSERT INTO \"" . $data['db_prefix'] . "setting\" (\"group\", \"key\", \"value\") VALUES ('config', 'config_email', '" . pg_escape_string($data['email']) . "')");
			pg_query($connection, "DELETE FROM \"" . $data['db_prefix'] . "setting\" WHERE \"key\" = 'config_url'");
			pg_query($connection, "INSERT INTO \"" . $data['db_prefix'] . "setting\" (\"group\", \"key\", \"value\") VALUES ('config', 'config_url', '" . pg_escape_string(HTTP_OPENCART) . "')");
			pg_query($connection, "UPDATE \"" . $data['db_prefix'] . "product\" SET \"viewed\" = 0");
			pg_close($connection);
		}
	}

	private function flushDatabase($connection, $db_driver = 'mysql') {
		if ($db_driver == 'mysql')
		{
			$resource = mysql_query('SHOW TABLES', $connection);
			$data = array();
			$i = 0;
			while ($result = mysql_fetch_row($resource)) {
				$data[$i] = $result[0];
				$i++;
			}
			foreach ($data as $table) {
				mysql_query('DROP table `' . $table . '`', $connection);
			}
		}
		elseif ($db_driver == 'pgsql')
		{
			$resource = pg_query($connection, "SELECT table_name FROM information_schema.tables WHERE table_schema = 'public'");
			$data = array();
			$i = 0;
			while ($result = pg_fetch_row($resource)) {
				$data[$i] = $result[0];
				$i++;
			}
			foreach ($data as $table) {
				pg_query($connection, 'DROP table "' . $table . '"');
			}
		};
	}

}
?>
