<?php

class Lfm_Model_Session {
    protected $_user;
    protected $_key;
    
    public function __construct(array $options = null) {
    	if (is_array($options)) {
    		$this->setOptions($options);
    	}
    }
    
    public function __set($name, $value) {
    	$method = 'set' . $name;
    	if (('mapper' == $name) || !method_exists($this, $method)) {
    		throw new Exception('Invalid guestbook property');
    	}
    	
    	$this->$method($value);
    }
    
    public function __get($name) {
    	$method = 'get' . $name;
    	if (('mapper' == $name) || !method_exists($this, $method)) {
    		throw new Exception('Invalid guestbook property');
    	}
    	return $this->$method();
    }
    
    public function setOptions(array $options) {
    	$methods = get_class_methods($this);
    	foreach ($options as $key => $value) {
    		$method = 'set' . ucfirst($key);
    		if (in_array($method, $methods)) {
    			$this->$method($value);
    		}
    	}
    	return $this;
    }
    
    /**
     * 
     * @param string $text
     * @return Lfm_Model_Session
     */
    public function setUser($text) {
    	$this->_user = (string) $text;
    	return $this;
    }
    
    public function getUser() {
    	return $this->_user;
    }
    
    /**
     * 
     * @param string $text
     * @return Lfm_Model_Session
     */
    public function setKey($text) {
    	$this->_key = (string) $text;
    	return $this;
    }
    
    public function getKey() {
    	return $this->_key;
    }
    
}