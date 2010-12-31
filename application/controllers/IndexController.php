<?php

class IndexController extends Zend_Controller_Action {
	private $config = null;
	private $lfm;
	
	protected $_redirector = null;

	public function init() {
		$this->_redirector = $this->_helper->getHelper('Redirector');
		
		$bootstrap = $this->getInvokeArg('bootstrap');
		$this->config = $bootstrap->getOptions();
		
		$this->lfm = new Lastfm_Client($this->config['lfmtop']['api']['key'], $this->config['lfmtop']['api']['secret']);
	}

	public function indexAction() {
		$user = $this->getRequest()->getParam('user', null);
		$session = Lfm_Model_SessionMapper::find($user);
		if(is_null($session)) {
			$this->_redirector->gotoUrl($this->lfm->getRequestAuthorizationUrl());
		}

		$lfmResponse = $this->lfm->user->getWeeklyTrackChart(array(
			'user' => $session->getUser(),
			'from' => strtotime(),
			'to' => strtotime(),
			
		));
		echo "<pre>";
		print_r($lfmResponse);
		die();
	}

	public function getsessionAction() {
		$token = $this->getRequest()->getParam('token', FALSE);
		if( FALSE === $token ) {
			die('No hay token');
		}
		$lfmResponse = $this->lfm->auth->getSession(array('token' => $token));
		if($lfmResponse->status == 'ok') {
			$session = new Lfm_Model_Session(array(
				'user' => "{$lfmResponse->session()->name()}",
				'key' => "{$lfmResponse->session()->key()}", 
			));
			
			Lfm_Model_SessionMapper::save($session);
			
			$this->_redirector->gotoSimple('index', null, null, array('user' => $session->getUser()));
		}
		
		$error = $lfmResponse->error();
		throw new Exception("Code: {$error->code}. Text: $error");
	}
}

