<?php
final class Loader {

	protected $registry;

	/**
	 * Список загруженных библиотек
	 *
	 * @var array
	 **/
	private $_oc_classes = array();
	/**
	 * Список загруженных моделей
	 *
	 * @var array
	 **/
	private $_oc_models = array();
	/**
	 * Список загруженных хелперов
	 *
	 * @var array
	 **/
	private $_oc_helpers = array();
	
	public function __construct($registry) {
		$this->registry = $registry;
	}
	
	public function __get($key) {
		return $this->registry->get($key);
	}

	public function __set($key, $value) {
		$this->registry->set($key, $value);
	}
	
	public function library($library) {

		/*
		* Загрузка списка библиотек
		*/
		if(is_array($library)){
			foreach($library as $lib){
				$this->library($lib);
			}
			return FALSE;
		}

		/*
		* Если либу уже загружали, то выходим отсюда
		*/
		if(in_array($library,$this->_oc_classes,true)){
			return FALSE;
		}

		$file = DIR_SYSTEM . 'library/' . $library . '.php';
		
		if (file_exists($file)) {
			include_once($file);	
			$this->_oc_classes[] = $library;
		} else {
			trigger_error('Error: Could not load library ' . $library . '!');
			exit();					
		}
	}
	
	public function helper($helper) {

		/*
		* Загрузки списка хелперов
		*/
		if(is_array($helper)){
			foreach($helper as $h){
				$this->helper($h);
			}
			return FALSE;
		}

		/*
		* Если хелпер уже ранее загружали, то ничего не делаем
		*/
		if(in_array($helper,$this->_oc_helpers,true)){
			return FALSE;
		}

		$file = DIR_SYSTEM . 'helper/' . $helper . '.php';
		if (file_exists($file)) {
			include_once($file);
			$this->_oc_helpers[] = $helper;
		} else {
			trigger_error('Error: Could not load helper ' . $helper . '!');
			exit();					
		}
	}
		
	public function model($model) {

		/*
		* Загрузка стопки моделей
		*/
		if(is_array($model)){
			foreach($model as $m){
				$this->model($m);
			}
			return FALSE;
		}
		/*
		* Если модель уже ранее загружали, то ничего не делаем.
		*/
		if(in_array($model,$this->_oc_models,true)){
			return FALSE;
		}

		$file  = DIR_APPLICATION . 'model/' . $model . '.php';
		$class = 'Model' . preg_replace('/[^a-zA-Z0-9]/', '', $model);
		if (file_exists($file)) {
			include_once($file);
			
			$this->registry->set('model_' . str_replace('/', '_', $model), new $class($this->registry));

			$this->_oc_models[] = $model;
		} else {
			trigger_error('Error: Could not load model ' . $model . '!');
			exit();					
		}
	}
	 
	public function database($driver, $hostname, $username, $password, $database, $prefix = NULL, $charset = 'UTF8') {
		$file  = DIR_SYSTEM . 'database/' . $driver . '.php';
		$class = 'Database' . preg_replace('/[^a-zA-Z0-9]/', '', $driver);
		
		if (file_exists($file)) {
			include_once($file);
			
			$this->registry->set(str_replace('/', '_', $driver), new $class());
		} else {
			trigger_error('Error: Could not load database ' . $driver . '!');
			exit();				
		}
	}
	
	public function config($config) {
		$this->config->load($config);
	}
	
	public function language($language) {
		return $this->language->load($language);
	}

	public function view($file,$params=array(),$return = FALSE)
	{
		
	}
} 
?>