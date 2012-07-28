<?php

/*
by dj-avtosh a.k.a. Эльхан Исаев
**/

header ("Content-Type: text/html; charset=utf-8");

if(isset($_REQUEST['city_from']) && !empty($_REQUEST['city_from'])) $city_from = $_REQUEST['city_from']; else $error .= '<li> Выберите город отправления';
if(isset($_REQUEST['city_to']) && !empty($_REQUEST['city_to'])) $city_to = $_REQUEST['city_to']; else $error .= '<li> Выберите город доставки';;
if(isset($_REQUEST['weight']) && !empty($_REQUEST['weight'])) $weight = number_format($_REQUEST['weight'], 1, '.', ''); else $error .= '<li> Укажите вес';
if(empty($weight)) $error .= '<li> Вес в неверной форме';
if(!empty($weight) && $weight>'31.5') $error .= '<li> Максимально допустимый вес 31.5 кг.';
if(isset($_REQUEST['val']) && !empty($_REQUEST['val']) && intval($_REQUEST['val'])) $ob_val = $_REQUEST['val']/100; else $ob_val = 0;
if($_REQUEST['val']>'50000') $error .= '<li> Максимальная объявляемая ценность 50000 руб.';

if(empty($error))
{

			$url = 'http://emspost.ru/api/rest/?method=ems.calculate&from=' . $city_from . '&to=' . $city_to . '&weight=' . $weight;

			//----------------
			$quote_data = array();
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_TIMEOUT, 15);
			$response = curl_exec($ch);
			$response_array = json_decode($response, TRUE);
			curl_close($ch);
			//----------------

			if(empty($response_array['rsp']['term']['min']) && empty($response_array['rsp']['term']['max']))
			echo '<li> Нет соединение с API EMS, повторите попытку!';
			else
			echo 'Цена: '. ($response_array['rsp']['price'] + $ob_val) . ' р., в сроки: ' . $response_array['rsp']['term']['min'] . ' - ' . $response_array['rsp']['term']['max'] .' дн.';
} else echo $error;


?>

