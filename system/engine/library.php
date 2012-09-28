<?php defined("DIR_SYSTEM") or die("No direct access to script");

/**
 * Абстрактный класс для создания компонент системы
 *
 **/
abstract class Library
{
	/*
	* Прямая связь с реестром системы
	*/
	protected $registry = null;

	public function __construct($registry)
	{
		$this->registry = $registry;
	}

	public function __get($name)
	{
		return $this->registry->get($name);
	}

	abstract public function initialize($args=array());

} // END abstract class Library