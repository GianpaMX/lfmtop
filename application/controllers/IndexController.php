<?php

class IndexController extends Zend_Controller_Action {
	private $config = null;
	private $lfm;
	
	protected $_redirector = null;

	public function init() {
		$this->_redirector = $this->_helper->getHelper('Redirector');
		
		$bootstrap = $this->getInvokeArg('bootstrap');
		$this->config = $bootstrap->getOptions();
		
		$this->lfm = new Application_Model_Lastfm($this->config['lfmtop']['api']['key'], $this->config['lfmtop']['api']['secret']);
	}

	public function indexAction() {
	}

	public function getsessionAction() {
		$token = $this->getRequest()->getParam('token', FALSE);
		if( FALSE === $token ) {
			die('No hay token');
		}
		$array = $this->lfm->auth->getSession(array('token' => $token));
		 
		$info = $array['info'];
		$response = $array['response'];
		
		$sxml = simplexml_load_string($array['response']);
		
		
		die($sxml->getName());
		
		$this->_redirector->gotoSimple('index', null, null, array('token' => $token));
		
		$this->view->assign('info', $info);
		$this->view->assign('response', $response);
		
	}
}

