<?php

class IndexController extends Zend_Controller_Action {
	private $config = null;
	private $lfm;

	public function init() {
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
		
		$this->view->assign('info', $info);
		$this->view->assign('response', $response);
	}
}

