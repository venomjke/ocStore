<?php

final class expresssms extends SmsGate {

	private $url = 'http://lcab.express-sms.ru/API/XML/send.php';

	private function GetPageByUrl($headers, $post_body) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->url); // урл страницы
		curl_setopt($ch, CURLOPT_FAILONERROR, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1); // return into a variable
		curl_setopt($ch, CURLOPT_TIMEOUT, 10); // times out after 4s
		curl_setopt($ch, CURLOPT_POST, 1); // set POST method
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_body); // передаём post-данные
		$result = curl_exec($ch); // получить результат в переменную
		curl_close($ch);
		return $result;
	}

	public function send() {
		$xml = "<?xml version='1.0' encoding='UTF-8'?>
		<data>
			<login>".$this->username."</login>
			<password>".$this->password."</password>
			<to number='".$this->to."'></to>
			<source>".$this->from."</source>
			<text>".$this->message."</text>
			<flash>".(int)$this->flash."</flash>
		</data>";

		$headers[] = 'Content-Type: text/xml; charset=utf-8';
		$headers[] = 'Content-Length: ' . strlen($xml);
		return array("answer" => $this->GetPageByUrl($headers, $xml));
	}

}