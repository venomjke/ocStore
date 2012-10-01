<?php
namespace Text;

if(!function_exists('truncate')){
	/**
	 *	Усекает $text до размеров $size. По умолчанию усечение html отключено. 
	 *	Функция обрабатывает текст в режмие mb_*, а также использует ENT_QUOTES + UTF-8 если указан use_html
	 *  
	 * @return string
	 * @author alex.strigin <apstrigin@gmail.com>
	 **/
	function truncate($text,$size,$offset=0,$use_html=false)
	{
		if($use_html){
			return mb_substr(strip_tags(html_entity_decode($text, ENT_QUOTES, 'UTF-8')),$offset,$size,'UTF-8').' ...';
		}
		return mb_substr($text,$offset,$size,'UTF-8') . ' ...';
	}
}

?>