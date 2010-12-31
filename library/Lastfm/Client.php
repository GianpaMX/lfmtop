<?php

class Lastfm_Client {
	private $key;
	private $secret;
	
	private $method = '';
	
	const url = 'http://ws.audioscrobbler.com/2.0/';
	const urlAuth = 'http://www.last.fm/api/auth/';
	
	public function __construct($key = null, $secret = null) {
		$this->key = $key;
		$this->secret = $secret;
	}
	
	/**
	 * 
	 * @param string $name
	 * @return Lastfm_Client
	 */
	public function __get($name) {
		$lastfm = new Lastfm_Client($this->key, $this->secret);
		$lastfm->method = $name;
		return $lastfm;
	}
	
	/**
	 * 
	 * @param string $name
	 * @param array $arguments
	 * @return Lastfm_Response
	 */
	public function __call($name, $arguments) {
		$this->method .= ($this->method?'.':'') . $name;
		
		$params = $arguments[0];
		$params['api_key'] = $this->key;
		$params['method'] = $this->method;
		$params['api_sig'] = $this->_sign($params);
		
		
		return $this->_request(self::url . $this->_query($params));
	}
	
	/**
	 * 
	 * @param array $params
	 * @return string
	 */
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
	
	/**
	 * 
	 * @param array $params
	 * @return string
	 */
	private function _query($params) {
		$query = '';
		ksort($params);
		$key = array_keys($params);
		for($i = 0; $i < count($key); $i++) {
			$query .= ($i>0?'&':'?') . utf8_encode($key[$i]) . "=" . utf8_encode($params[$key[$i]]);
		}
		return $query;
	}
	
	/**
	 * 
	 * @param string $url
	 * @return Lastfm_Response
	 */
	private function _request($url) {
		$ch = curl_init($url);
		$fp = tmpfile();

		curl_setopt($ch, CURLOPT_FILE, $fp);
		curl_setopt($ch, CURLOPT_HEADER, 0);

		curl_exec($ch);
		$info = curl_getinfo($ch);
		curl_close($ch);

		rewind($fp);
		$response = fread($fp, $info['size_download']);
		fclose($fp);
		
		$response = utf8_encode($response);
		
		$dom = new DOMDocument();
		if(!$dom->loadXML($response)) {
			throw new Exception('Error while parsing the response');
		}
		return new Lastfm_Response(simplexml_import_dom($dom), $info);
	}
	
	public function getRequestAuthorizationUrl() {
		return self::urlAuth . '?api_key=' . $this->key;
	}
}

