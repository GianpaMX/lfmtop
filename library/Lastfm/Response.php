<?php

class Lastfm_Response {
	/**
	 * @var SimpleXMLElement
	 */
	private $sxml;
	
	private $info;
	
	public function __construct(SimpleXMLElement $object = null, $info = null) {
		$this->sxml = $object;
		$this->info = $info;
	}
	
	/**
	 * 
	 * @param string $name
	 * @return string
	 */
	public function __get($name) {
		foreach($this->sxml->attributes() as $attr => $value) {
			if($attr == $name) {
				return "$value";
			}
		}
		
		return null;
	}
		
	/**
	 * 
	 * @param string $name
	 * @param array $arguments
	 * @return Lastfm_Response
	 */
	public function __call($name, $arguments) {
		$lfmResponse = new Lastfm_Response();
		
		
		$i = 0;
		foreach($this->sxml->children() as $child) {
			if($child->getName() == $name) {
				if(isset($arguments[0]) && $arguments[0] != $i) {
					$i++;
					continue;
				}	
				$lfmResponse->sxml = $child;
				break;
			}
		}
				
		return $lfmResponse;
	}
	
	/**
	 * Returns Response Info
	 * @return array
	 */
	public function getInfo() {
		return $this->info;
	}
	
	public function __toString() {
		return "{$this->sxml}";
	}
}
