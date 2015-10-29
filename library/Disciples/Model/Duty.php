<?php
/**
 * Duty.php
 * Database interface for duty table
 *
 * @package Disciples
 * @author Sangwoo <linkedkorean@gmail.com>
 */
/**
 *  Disciples_Model_Duty
 *
 * @package Disciples
 * @author Sangwoo <linkedkorean@gmail.com>
 */
class Disciples_Model_Duty extends Zend_Db_Table_Abstract
{
    protected $_name    = 'duty';
    protected $_primary = 'id';

    public function getDutyNameById($id)
    {
        return $this->find($id)->current()->duty_name;
    }

    public function getAllDuty()
    {
    	$rows = $this->fetchAll($this->select()->order('display_priority'));
    	return $rows->toArray();
    }
}