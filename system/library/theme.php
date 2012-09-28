<?php defined("DIR_SYSTEM") or die("No direct access to script");

class Theme extends Library{

	/**
	 * Путь до темы приложения
	 *
	 * @var string
	 **/
	private $_oc_path_theme = '';

	/**
	 * Текущая тема приложения
	 *
	 * @var string
	 **/
	private $_oc_current_theme = '';

	/**
	 * Тема по умолчанию
	 *
	 * @var string
	 **/
	private $_oc_default_theme = '';

	/**
	 * Путь до "частичек"
	 *
	 * @var string
	 **/
	private $_oc_partials_path = '';

	public function initialize($args=array())
	{
		$this->set_path_theme();
		$this->set_current_theme();
		$this->set_default_theme();
		$this->set_partials_path();

		$this->log->write('Theme: Class loaded');
		return $this;
	}

	public function set_path_theme()
	{
		$this->_oc_path_theme = 'theme/';
	}

	public function set_current_theme()
	{
		$this->_oc_current_theme = $this->config->get('config_template').'/';
	}

	public function set_default_theme()
	{
		$this->_oc_default_theme = 'default/';
	}

	public function set_partials_path()
	{
		$this->_oc_partials_path = 'partials/';
	}

	public function get_current_theme()
	{
		return $this->_oc_path_theme.$this->_oc_current_theme;
	}

	public function get_default_theme()
	{
		return $this->_oc_path_theme.$this->_oc_default_theme;
	}

	public function get_partials_path()
	{
		return $this->_oc_partials_path;
	}



	/**
	 * Метод для загрузки файла шаблона в рамках текущей темы
	 *
	 * @return string
	 **/
	public function file($file_path,$args=array())
	{
		$output = '';

		if($this->load->is_view_exists($this->get_current_theme().$file_path)){
			$output = $this->load->view($this->get_current_theme().$file_path,$args);
		}else{ // иначе пытаемся загрузить частичку по умолчанию.
			// если и её нет, то будет выведено сообщение об ошибке
			$output = $this->load->view($this->get_default_theme().$file_path,$args);
		}
		return $output;
	}

	/**
	 * Метод для загрузки "Частички" представления в рамках шаблона текущей темы
	 *
	 * @param partial_path
	 * @param args
	 * @return string
	 **/
	public function partial($partial_path,$args=array())
	{
		return $this->template($this->get_partials_path().$partial_path,$args);
	}

	/**
	 * Метод для загрузки страницы шаблона в рамках текущей темы
	 *
	 * @return string
	 **/
	public function template($template_path,$args=array())
	{
		return $this->file('template/'.$template_path,$args);
	}

}