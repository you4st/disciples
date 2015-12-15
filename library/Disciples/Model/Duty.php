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

    public function exists($id)
    {
        return $this->find($id)->count() > 0 ? true : false;
    }

    public function updateDutyOptions($duties)
    {
        if (!empty($duties)) {
            foreach ($duties as $duty) {
                if ($this->exists($duty['id'])) {
                    if ($duty['remove'] == 'true') {
                        $this->deleteDutyOption($duty['id']);
                    } else {
                        $this->updateDutyOption($duty);
                    }
                } else {
                    if ($duty['remove'] != 'true') {
                        $this->addDutyOption($duty);
                    }
                }
            }
        }
    }

    /**
     * Add a duty option
     *
     * @return boolean
     */
    public function addDutyOption($data)
    {
        $data['created_on'] = new Zend_Db_Expr('NOW()');
        $this->insert($this->_normalize($data));

        return $this->getAdapter()->lastInsertId();
    }

    /**
     * Remove a duty option
     *
     * @return boolean
     */
    public function deleteDutyOption($id)
    {
        $this->delete($this->getAdapter()->quoteInto('id = ?', $id));

        return true;
    }

    /**
     * Update the duty option
     *
     * @return boolean
     */
    public function updateDutyOption($data)
    {
        $where = $this->getAdapter()->quoteInto('id = ?', $data['id']);
        $this->update($this->_normalize($data), $where);

        return true;
    }

    /**
     * Normalize the certain data values
     *
     * @return array
     */
    private function _normalize($data)
    {
        if (array_key_exists('remove', $data)) {
            unset($data['remove']);
        }

        if (array_key_exists('duty_name', $data)) {
            $data['duty_name'] = trim($data['duty_name']);
        }

        return $data;
    }
}