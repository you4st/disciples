<?php
class CalendarController extends Zend_Controller_Action
{
	public function init()
	{
		/* Initialize action controller here */
		$this->_user = new Disciples_User();
		$this->view->isAdmin = $this->_user->isAdmin();
		$this->_helper->layout->setLayout('index-list');

		$this->_session = new Zend_Session_Namespace('DISCIPLES');		
	}

	public function indexAction()
	{
		$this->view->data = $this->_session->people;
	}
}