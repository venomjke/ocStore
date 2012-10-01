<?php 


if(!function_exists("lang")){
	function lang($key){
		global $registry;
		return $registry->get('language')->get($key);
	}
} 