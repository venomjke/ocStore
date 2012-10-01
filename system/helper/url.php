<?php


if(!function_exists("base_url")){
	/**
	* Функция обращается к объекту url за ссылкой на главный домен
	*
	* @return string
	*/
	function base_url(){
		global $registry;
		return $registry->get('url')->getUrl();
	}
}

if(!function_exists("site_url")){
	/**
	 * Функция обращается к url->link для генерации url
	 *
	 * @return string
	 **/
	function site_url($url,$args=array(),$connection = 'NOSSL')
	{
		global $registry;
		return $registry->get('url')->link($url,$args,$connection);
	}
}