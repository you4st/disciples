<?php
class AdminController extends Zend_Controller_Action
{
    public function init()
    {
        /* Initialize action controller here */
        $this->_user = new Disciples_User();
        $this->view->isAdmin = $this->_user->isAdmin();
        $this->_helper->layout->setLayout('index');

        $this->_session = new Zend_Session_Namespace('DISCIPLES');        
    }

    public function indexAction()
    {
    	//$this->_helper->mail->sendMail('sangwoo.han@sprint.com', 'test again', 'this is the test mail from Disciples.');
    	
    }
    
    public function dutyAction()
    {
    	$duty = new Disciples_Model_Duty();
    	$this->view->duties = $duty->getAllDuty();
    }
}