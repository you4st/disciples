<?php
/**
 * Marital.php
 * Database interface for marital table
 *
 * @package Disciples
 * @author Sangwoo <linkedkorean@gmail.com>
 */
/**
 *  Disciples_Model_Marital
 *
 * @package Disciples
 * @author Sangwoo <linkedkorean@gmail.com>
 */
class Disciples_Model_Marital extends Zend_Db_Table_Abstract
{
    protected $_name    = 'marital';
    protected $_primary = 'id';

    public function getMaritalStatusNameById($id)
    {
        return $this->find($id)->current()->status_name;
    }
    
    public function getAllMaritalStatus()
    {
    	$rows = $this->fetchAll($this->select());
    	return $rows->toArray();
    }
}