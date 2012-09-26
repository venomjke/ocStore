<?php
class Log {

	private $filename;
	
	/**
	 * Дескриптор файла лога. Инциализируется в момент создания объекта Log,
	 * Очищается в момент удаления
	 *
	 * @var object
	 **/
	private $_oc_file_handle = null;

	/**
	 * Формтат доты сообщения
	 *
	 * @var string
	 **/
	private $_oc_dateformat = 'Y-m-d G:i:s';

	public function __construct($filename) {
		$this->filename = $filename;
	}

	public function __destruct()
	{
		if(!is_null($this->_oc_file_handle)){
			fclose($this->_oc_file_handle);
		}
	}

	public function set_filename($filename = '')
	{
		$this->filename = $filename;
	}

	public function get_filename()
	{
		return $this->filename;
	}

	public function get_log_filename()
	{
		return DIR_LOGS . $this->filename;
	}

	public function get_file_handle()
	{
		/*
		* Если файл не создан, то создаем
		*/
		if(is_null($this->_oc_file_handle)){
			$this->_oc_file_handle = fopen($this->get_log_filename(),'a+');
		}
		return $this->_oc_file_handle;
	}
	
	public function write($message) {
		fwrite($this->get_file_handle(), date($this->_oc_dateformat) . ' - ' . $message . "\n");			
	}
}
?>