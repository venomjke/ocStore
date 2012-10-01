<?php
class Request {
	public $get = array();
	public $post = array();
	public $cookie = array();
	public $files = array();
	public $server = array();
	
  	public function __construct() {
		$_GET = $this->clean($_GET);
		$_POST = $this->clean($_POST);
		$_REQUEST = $this->clean($_REQUEST);
		$_COOKIE = $this->clean($_COOKIE);
		$_FILES = $this->clean($_FILES);
		$_SERVER = $this->clean($_SERVER);
		
		$this->get = $_GET;
		$this->post = $_POST;
		$this->request = $_REQUEST;
		$this->cookie = $_COOKIE;
		$this->files = $_FILES;
		$this->server = $_SERVER;
	}
	
  	public function clean($data) {
    	if (is_array($data)) {
	  		foreach ($data as $key => $value) {
				unset($data[$key]);
				
	    		$data[$this->clean($key)] = $this->clean($value);
	  		}
		} else { 
	  		$data = htmlspecialchars($data, ENT_COMPAT);
		}

		return $data;
	}

	public function is_ajax()
	{
		return !empty($this->server['HTTP_X_REQUESTED_WITH']) && strtolower($this->server['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
	}


	/*
	* Функция проверяет наличие параметра в контейнере, и если нет, то возвращает значение по умолчанию
	*/
	private function fetch_param($container,$key,$default='')
	{
		if(!empty($this->post[$key])) return $this->post[$key];
		return $default;
	}

	public function post($key,$default='')
	{
		return $this->fetch_param($this->post,$key,$default);
	}

	public function get($key,$default='')
	{
		return $this->fetch_param($this->get,$key,$default);
	}

	public function cookie($key,$default)
	{
		return $this->fetch_param($this->cookie,$key,$default);
	}


}
?>