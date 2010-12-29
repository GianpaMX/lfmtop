<?php

class Lfm_Model_SessionMapper {
    protected static $_dbTable;

    /**
     * 
     * @param Lfm_Model_DbTable_Session|string $dbTable
     * @return Lfm_Model_SessionMapper
     */
    public static function setDbTable($dbTable) {
    	if (is_string($dbTable)) {
    		$dbTable = new $dbTable();
    	}
    	
    	if (!$dbTable instanceof Zend_Db_Table_Abstract) {
    		throw new Exception('Invalid table data gateway provided');
    	}
    	
    	self::$_dbTable = $dbTable;
    	return self;
    }
    
    /**
     * 
     * @return Lfm_Model_DbTable_Session
     */
    public static function getDbTable() {
    	if (null === self::$_dbTable) {
    		self::setDbTable('Lfm_Model_DbTable_Session');
    	}
    	return self::$_dbTable;
    }
    
    /**
     * 
     * @param Lfm_Model_Session $session
     * @return bool
     */
    public static function save(Lfm_Model_Session $session) {
    	$data = array(
    		'user'   => $session->getUser(),
    		'key' => $session->getKey(),
    	);
    	
    	if ( null === self::find($user = $session->getUser()) ) {
    		self::getDbTable()->insert($data);
    	} else {
    		unset($data['user']);
    		self::getDbTable()->update($data, array('user = ?' => $user));
    	}
    	
    	return true;
    }
    
    /**
     * 
     * @param string $user
     * @return Lfm_Model_Session $session
     */
    public static function find($user) {
    	$result = self::getDbTable()->find($user);
    	if (0 == count($result)) {
    		return null;
    	}
    	
    	$row = $result->current();
    	return new Lfm_Model_Session($row->toArray());
    }
    
    public static function fetchAll() {
    	$resultSet = self::getDbTable()->fetchAll();
    	$entries   = array();
    	
    	foreach ($resultSet as $row) {
    		$entries[] = new Lfm_Model_Session($row);
    	}
    	return $entries;
    }
}
