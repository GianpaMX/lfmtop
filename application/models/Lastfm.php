<?php

class Application_Model_Lastfm {
	private $key;
	private $secret;
	
	private $method = '';
	
	const url = 'http://ws.audioscrobbler.com/2.0/';
	
	public function __construct($key = null, $secret = null) {
		$this->key = $key;
		$this->secret = $secret;
	}
	 
	public function __get($name) {
		$class = get_class($this);
		$lastfm = new $class($this->key, $this->secret);
		$lastfm->method = $name;
		return $lastfm;
	}
	
	public function __call($name, $arguments) {
		$this->method .= ($this->method?'.':'') . $name;
		
		$params = $arguments[0];
		$params['api_key'] = $this->key;
		$params['method'] = $this->method;
		$params['api_sig'] = $this->_sign($params);
		
		
		return $this->_curl(self::url . $this->_query($params));
	}
	
	private function _sign($params) {
		$hashstring = '';
		ksort($params);
		foreach($params as $param => $value) {
			$hashstring .= "{$param}{$value}";
		}
		$hashstring .= $this->secret;

		$hashstring = utf8_encode($hashstring);
		return utf8_encode(md5($hashstring));
	}
	
	private function _query($params) {
		$query = '';
		ksort($params);
		$key = array_keys($params);
		for($i = 0; $i < count($key); $i++) {
			$query .= ($i>0?'&':'?') . utf8_encode($key[$i]) . "=" . utf8_encode($params[$key[$i]]);
		}
		return $query;
	}
	
	private function _curl($url) {
		$ch = curl_init($url);
		$fp = tmpfile();

		curl_setopt($ch, CURLOPT_FILE, $fp);
		curl_setopt($ch, CURLOPT_HEADER, 0);

		curl_exec($ch);
		$info = curl_getinfo($ch);
		curl_close($ch);

		rewind($fp);
		$response = fread($fp, 10240);
		fclose($fp);
		
		return array(
			'info' => $info,
			'response' => $response,
		);
	}
}
