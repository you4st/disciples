<?php
class IndexController extends Zend_Controller_Action
{
    protected $_user = null;

    public function init()
    {
        /* Initialize action controller here */
        $this->_user = new Disciples_User();
        $this->view->isAdmin = $this->_user->isAdmin();
        $this->_helper->layout->setLayout( 'index' );

        $this->_session = new Zend_Session_Namespace('DISCIPLES');
    }

    public function indexAction()
    {
    }

    public function loginAction()
    {
        $this->view->username = '';

        if ($this->_request->getParam('success')) {
            $this->view->loggedout = 1;
        }

        if ($this->_user->isAuthenticated()) {
            $this->_user->logout();
        }

        if ($this->_request->isPost()) {
            $postData = $this->_request->getParams();
            $formError = array(
                'username' => '',
                'password' => ''
            );

            if (!$postData['username'] || !$postData['password']) {
                if (!$postData['username']) {
                    $formError['username'] = 1;
                }
                if (!$postData['password']) {
                    $formError['password'] = 1;
                }
                $this->view->username = $postData['username'];
                $this->view->formError = $formError;
            } else {
                $result = $this->_user->login($postData['username'], $postData['password']);

                if ($result->isValid()) {
                    $this->_helper->redirector->gotoUrl('/');
                } else {
                    // Invalid login
                    $this->view->errorMessage = $this->view->partial('_errorMessage.phtml', array(
                        'errorMessage' => implode ('. ', $result->getMessages())
                    ));
                }
            }
        }
    }

    public function logoutAction()
	{
        $this->_user->logout();
        Zend_Session::namespaceUnset('DISCIPLES');
        $this->_helper->redirector->gotoUrl('/index/login/success/1');
    }

    public function registerAction()
	{
		$this->_user->isAdmin();
        if ($this->_request->isPost()) {
            $postData = $this->_request->getParams();
            $formError = array();

            if (!$postData['username'] || !$postData['password'] || !$postData['email'] || !$postData['firstName'] || !$postData['lastName']) {
                if (!$this->_request->getParam('username')) {
                    $formError['username'] = 1;
                }
                if (!$this->_request->getParam('email')) {
                	$formError['email'] = 1;
                }
                if (!$this->_request->getParam('firstName')) {
                	$formError['firstName'] = 1;
                }
                if (!$this->_request->getParam('lastName')) {
                	$formError['lastName'] = 1;
                }
                if (!$this->_request->getParam('password')) {
                    $formError['password'] = 1;
                }
                $this->view->formError = $formError;
            } else {
                if (!$this->_user->addUser($postData)) {
                    $this->view->errorMessage = $this->view->partial('_errorMessage.phtml', array(
                        'errorMessage' => 'There\'s a problem while registering a new user. Please try again.',
                    ));
                } else {
                    $this->_helper->redirector->gotoUrl('/');
                }
            }
        }
    }
    
    public function registerAdminAction()
	{
        $data = $this->_request->getParams();
        $required = array('username', 'email', 'firstName', 'lastName', 'password');
        $errorMessage = '';
        
        foreach ($required as $key) {
        	if (!array_key_exists('username', $data)) {
        		$errorMessage = 'Please provide the valid information...';
        	}
        }
        
        if (empty($errorMessage)) {
            if (!$this->_user->addUser($data)) {
				$errorMessage = 'Please provide the valid information...';
            } else {
            	$this->_helper->redirector->gotoUrl('/');
            }
        }
        
        $this->view->errorMessage = $errorMessage;
    }
}