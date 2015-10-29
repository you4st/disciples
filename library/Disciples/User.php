<?php
/**
 * Contains the generic User class
 *
 * @author  Sangwoo Han <linkedkorean@gmail.com>
 * @package Disciples
  */
class Disciples_User
{
    protected $_permissions;

    public function __construct()
    {
    }

    /**
     * Checks whether the user is authenticated or not
     *
     * @return boolean
     */
    public function isAuthenticated()
    {
        $auth = Zend_Auth::getInstance();
        return $auth->hasIdentity();
    }

    /**
     * Gets user data for authenticated user
     *
     * @return array
     */
    public function getUserData()
    {
        $auth = Zend_Auth::getInstance();

        if ($auth->hasIdentity()) {
            return $auth->getIdentity();
        } else {
            return array();
        }
    }

    /**
     * Sets user's permissions
     */
    public function setPermissions()
    {
    }

    /**
     * Checks if a user has specific permission
     *
     * @param string $permission
     *        The name of the permission to test
     * @return bool
     */
    public function hasPermission($permission)
    {
    }
    
    public function isAdmin()
    {
    	$userData = $this->getUserData();
    	
    	return $userData['level'];
    }

    /**
     * Logs the user in
     *
     * @param string $username
     *        The user's username
     * @param string $password
     *        The user's password
     * @return Zend_Auth_Result
     */
    public function login($username, $password)
    {
        $auth = Zend_Auth::getInstance();
        $auth->clearIdentity();

        return $auth->authenticate(new Disciples_Authenticator($username, $password));
    }

    /**
     * Logs the user out
     */
    public function logout()
    {
        Zend_Auth::getInstance()->clearIdentity();
        unset($this->_permissions);
    }

    public function addUser($data)
    {
        try {
            $userTable = new Zend_Db_Table('user');

            $new = array(
                'username'   => $data['username'],
            	'email'      => $data['email'],
                'first_name' => $data['firstName'],
                'last_name'  => $data['lastName'],
                'hash'       => Disciples_User::generateHash($data['password']),
            	'level'      => $data['admin'],
                'created_on' => new Zend_Db_Expr('NOW()'),
            );

            $userTable->insert($new);
        } catch (Exception $ex) {
            Disciples_Logger::getInstance(__CLASS__)->error($ex->getMessage());
            return false;
        }

        return true;
    }

    public static function generateHash($password)
    {
        $cost = 10;
        $salt = strtr(base64_encode(mcrypt_create_iv(16, MCRYPT_DEV_URANDOM)), '+', '.');
        $salt = sprintf("$2a$%02d$", $cost) . $salt;

        return crypt($password, $salt);
    }
}