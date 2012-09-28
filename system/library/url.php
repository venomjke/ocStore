<?php
class Url {
	private $url;
	private $ssl;
	private $rewrite = array();
	
	public function __construct($url, $ssl = '') {
		$this->url = $url;
		$this->ssl = $ssl;
	}

	public function getUrl()
	{
		return $this->url;
	}
		
	public function addRewrite($rewrite) {
		$this->rewrite[] = $rewrite;
	}
		
	public function link($route, $args = '', $connection = 'NONSSL') {
		if ($connection ==  'NONSSL') {
			$url = $this->url;	
		} else {
			$url = $this->ssl;	
		}
		
		$url .= 'index.php?route=' . $route;
			
		if ($args) {

			if(is_string($args)){
				$url .= '&' . ltrim($args,'&');		
			}else if(is_array($args)){
				$url .= '&' . http_build_query($args);
			}
			// $url .= str_replace('&', '&amp;', '&' . ltrim($args, '&')); 
		}
		
		foreach ($this->rewrite as $rewrite) {
			$url = $rewrite->rewrite($url);
		}
		return $url;
	}

	/**
	 * Функция преобразует get параметры в строку запроса используя http_build_query
	 *
	 * @return string
	 **/
	public function params_to_string($params = array())
	{
	}

}
?>