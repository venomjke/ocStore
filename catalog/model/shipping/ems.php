<?php

/**
@ EMS Почта России (автоматизированный) - 5.0
@ + с расширенной админ-панелью + международный
@ Разработчик: Эльхан Исаев
@ ICQ: 27-27-27-27
@ Сайт: http://7777777.pro
**/

class ModelShippingEms extends Model {

	function getQuote($address)
	{
		$this->load->language('shipping/ems');

		if ($this->config->get('ems_status'))
		{

			$dops = '';

			//FROM CITY
      		$query = $this->db->query("SELECT name, country_id FROM " . DB_PREFIX . "zone WHERE zone_id = '" . $this->config->get('config_zone_id') . "'");
			$emscity = $this->config->get('ems_city_from');
			$checkemscity = $this->rus_reg($emscity);
			if($emscity == "0") $city_from = $this->dest_check('FALSE', $query->row['name']);
			else { if($checkemscity != "error") $city_from = $this->config->get('ems_city_from'); else $city_from = "error"; }
			$country_id_from = $query->row['country_id'];

			//TO CITY
			$query = $this->db->query("SELECT name, country_id FROM " . DB_PREFIX . "zone WHERE zone_id = '" . (int)$address['zone_id'] . "'");
			$city_to = $this->dest_check($query->row['name'],$address['city']);
			$country_id = $query->row['country_id'];
			$tociid = $query->row['name'];
			//$city_to = $tociid;

			//FROM COUNTRY
			$query = $this->db->query("SELECT name, iso_code_2 FROM " . DB_PREFIX . "country WHERE country_id = '" . (int)$country_id_from . "'");
			$country_name_from = $query->row['name'];
			$country_iso_from = $query->row['iso_code_2'];

			//TO COUNTRY
			$query = $this->db->query("SELECT name, iso_code_2 FROM " . DB_PREFIX . "country WHERE country_id = '" . (int)$country_id . "'");
			$country_name = $query->row['name'];
			$country_iso = $query->row['iso_code_2'];

			if($country_iso != 'RU') { $dops = '&type=att'; $city_to = $this->country_check($country_iso); }


			if($city_from=="error" || $city_to=="error") $status = FALSE; else $status = TRUE;
			if ($city_from == $city_to && $this->config->get('ems_in')=="0") $status = FALSE; else $status = TRUE;

		} else $status = FALSE;

		$method_data = array();

		if($this->config->get('config_weight_class_id') == 1)
		{
		$ems_dopl_ves = $this->config->get('ems_dopl_ves')/1000;
		$normves = number_format(preg_replace("/[^0-9-.]+/is", '', $this->weight->format($this->cart->getWeight(), $this->config->get('config_weight_class_id'))), 4, '.', '') / 1 + $ems_dopl_ves; //основа кг
		$normves_text = $normves.' кг.';
		}
		else
		{
		$ems_dopl_ves = $this->config->get('ems_dopl_ves');
		$normves = number_format(preg_replace("/[^0-9-.]+/is", '', $this->weight->format($this->cart->getWeight(), $this->config->get('config_weight_class_id'))), 4, '.', '') / 1000 + $ems_dopl_ves; //основа граммы
			$normves_text = ($normves*1000).' г.';
		}

		if ($status && ( $this->config->get('ems_max_weight') >= $normves ) )
		{


		// Ценовая сумма товаров годных для объявления ценности
		$product_total_obl = 0;
		$products = $this->cart->getProducts();
	    foreach ($products as $product)
		{
		if($product['price']<'50000') $product_total_obl += $product['price'];
		}
		//*****//



			$url = 'http://emspost.ru/api/rest/?method=ems.calculate&from=' . $city_from . '&to=' . $city_to . '&weight=' .  $normves .$dops;

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

			if($city_to=="error")
			{



					if( empty($ems_mname) || !isset($ems_mname) ) $mname = $this->language->get('text_title'); else $mname = $this->config->get('ems_mname');

								$method_data = array
					(
					'title'      	=> $mname,
					'sort_order' 	=> $this->config->get('ems_sort_order'),
					'error'      	=> 'Расчет и выбор недоступен, по причине ввода некорректного пункта (города или области) доставки!'
					);



		return $method_data;

			}else{

		if($response_array['rsp']['stat'] == 'ok')
			{

				$ems_vidd = $this->config->get('ems_vid');
				$ems_vidd_out = $this->config->get('ems_vid_out');
				$ems_mname = $this->config->get('ems_mname');
				$ems_plus = $this->config->get('ems_plus');
				$ems_dopl = $this->config->get('ems_dopl');
				if(!intval($ems_plus)) $ems_plus = 0;
				if(!intval($ems_dopl)) $ems_dopl = 0;
				$dopl = $this->cart->countProducts()*$ems_dopl;
				if($ems_dopl_ves>0) $ves_tx = "( c учетом ".($ems_dopl_ves*1000)." г. - вес упаковки )"; else $ves_tx = "";

				if( empty($ems_vidd) || !isset($ems_vidd) ) $ems_vidd = '[EMS] %from% - %to%, сроки: %mind% - %maxd% дней, вес: %ves%';
				if( $country_iso != 'RU' && empty($ems_vidd_out) || !isset($ems_vidd_out) ) $ems_vidd = '[EMS] %from% - %to%, сроки: 5 - 14 дней, вес: %ves% кг.';

				if($response_array['rsp']['term']['min'] == $response_array['rsp']['term']['max'])
				{
				$ems_vidd = str_replace(array('дня', 'дней', 'дн.'), "", $ems_vidd);
				$ems_vidd = preg_replace("#%mind%(.+?)%maxd%#si", "в течение ". ($response_array['rsp']['term']['max']+$ems_plus) ." дн.", $ems_vidd);
				}


				if($country_iso == 'RU')
				{
				$endtext = str_replace
					(
				array('%from%', '%to%', '%mind%', '%maxd%', '%ves%'),
				array( $this->rus_reg($city_from), $this->rus_reg($city_to), $response_array['rsp']['term']['min']+$ems_plus, $response_array['rsp']['term']['max']+$ems_plus, $normves_text ),
				$ems_vidd
					);
				}
					else
				{
				$endtext = str_replace
					(
				array('%from%', '%to%', '%ves%', '%from_city%', '%to_city%'),
				array( $country_name_from, $country_name, $normves, $this->config->get('ems_city_from'), $tociid ),
				$ems_vidd_out
					);
				}



				$quote_data['ems'] = array
					(
					'code'         	=> 'ems.ems',
					'title'        	=> $endtext.' '.$ves_tx,
					'cost'         	=> $response_array['rsp']['price']+$dopl,
					'tax_class_id' 	=> 0,
					'text'         	=> $this->currency->format($response_array['rsp']['price']+$dopl)
					);

				if ($this->config->get('ems_ob')=="1")
				{
				$quote_data['ems_2'] = array
					(
					'code'         	=> 'ems.ems_2',
					'title'        	=> $endtext.' '.$ves_tx.' <b>(с объявленной ценностью)</b>',
					'cost'         	=> $response_array['rsp']['price'] + $product_total_obl/100 + $dopl,
					'tax_class_id' 	=> 0,
					'text'         	=> $this->currency->format($response_array['rsp']['price'] + $product_total_obl/100 + $dopl)
					);
				}

				if( empty($ems_mname) || !isset($ems_mname) )
				$mname = $this->language->get('text_title'); else $mname = $this->config->get('ems_mname');

				$method_data = array
					(
					'code'       	=> 'ems',
					'title'      	=> $mname,
					'quote'      	=> $quote_data,
					'sort_order' 	=> $this->config->get('ems_sort_order'),
					'error'      	=> FALSE
					);

			}

		}
		return $method_data;
	}

				}


/*Города в нужном формате*/
function transl($str)
{
    $tr = array
    	(
    "А"=>"a","Б"=>"b","В"=>"v","Г"=>"g", "Д"=>"d","Е"=>"e","Ж"=>"zh","З"=>"z","И"=>"i","Й"=>"i","К"=>"k","Л"=>"l","М"=>"m","Н"=>"n","О"=>"o","П"=>"p","Р"=>"r","С"=>"s","Т"=>"t",
    "У"=>"u","Ф"=>"f","Х"=>"kh","Ц"=>"c","Ч"=>"ch","Ш"=>"sh","Щ"=>"shh","Ъ"=>"","Ы"=>"y","Ь"=>"","Э"=>"e","Ю"=>"yu","Я"=>"ya","а"=>"a","б"=>"b","в"=>"v","г"=>"g","д"=>"d","е"=>"e","ж"=>"zh",
    "з"=>"z","и"=>"i","й"=>"j","к"=>"k","л"=>"l","м"=>"m","н"=>"n","о"=>"o","п"=>"p","р"=>"r","с"=>"s","т"=>"t","у"=>"u","ф"=>"f","х"=>"h","ц"=>"c","ч"=>"ch","ш"=>"sh","щ"=>"shh","ъ"=>"","ы"=>"y","ь"=>"","э"=>"je","ю"=>"ju","я"=>"ja"," "=>"-"
		);
    return strtr($str,$tr);
}

function kolvo($slovo) { $k=1; for ($i=0;$i<strlen($slovo);$i++){if ($slovo[$i]==" ") $k++;} return $k; }

function dest_check($str_reg, $str_city)
{

	if($this->kolvo(iconv('utf-8','windows-1251',$str_city)) > 1) $res_1 = 'region--'.$this->transl($str_city); else $res_1 = 'city--'.$this->transl($str_city);
	if($this->kolvo(iconv('utf-8','windows-1251',$str_reg)) > 1) $res_2 = 'region--'.$this->transl($str_reg); else $res_2 = 'city--'.$this->transl($str_reg);

//----------------
$urlRussia = 'http://emspost.ru/api/rest/?method=ems.get.locations&type=russia&plain=true';
$ch = curl_init();
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_URL, $urlRussia);
$outRussia = curl_exec($ch);
$RussiaList = json_decode($outRussia, TRUE);
//----------------
	$gorod = "";
	$reg = "";
	foreach($RussiaList['rsp']['locations'] as $key=>$val)
	{
	foreach($val as $i=>$j)
        {

	if( in_array ($res_1, $RussiaList['rsp']['locations'][$key]) ) $gorod = $res_1;
    if( in_array ($res_2, $RussiaList['rsp']['locations'][$key]) ) $reg = $res_2;
    					$ress = $gorod;
    if(empty($ress)) 	$ress = $reg;
    if(empty($ress)) 	$ress = 'error';

		}
	}
	return $ress;

}

function rus_reg($ems_format_reg)
{

//----------------
$urlRussia = 'http://emspost.ru/api/rest/?method=ems.get.locations&type=russia&plain=true';
$ch = curl_init();
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_URL, $urlRussia);
$outRussia = curl_exec($ch);
$RussiaList = json_decode($outRussia, TRUE);
//----------------

	foreach($RussiaList['rsp']['locations'] as $key=>$val)
	{
	foreach($val as $i=>$j)
        {
	if( in_array ($ems_format_reg, $RussiaList['rsp']['locations'][$key]) ) $res = $RussiaList['rsp']['locations'][$key]['name'];
    if(empty($res) || !isset($res)) $res = 'error'; //not found ot out Russia
		}
	}
	if($res != "error") { return mb_convert_case(mb_strtolower($res,'utf-8'), MB_CASE_TITLE, 'utf-8');} else return $res;

}



function country_check($ciso)
{

//----------------
$urlc = 'http://emspost.ru/api/rest/?method=ems.get.locations&type=countries';
$ch = curl_init();
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_URL, $urlc);
$outc = curl_exec($ch);
$listc = json_decode($outc, TRUE);
//----------------

	foreach($listc['rsp']['locations'] as $key=>$val)
	{
	foreach($val as $i=>$j)
        {
	if( in_array ($ciso, $listc['rsp']['locations'][$key]) ) $res = $ciso;
    if(empty($res) || !isset($res)) $res = 'error'; //not found
		}
	}
	return $res;

}
/**Города в нужном формате**/

}
?>