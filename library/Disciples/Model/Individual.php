<?php
/**
 * Individual.php
 * Database interface for individual table
 *
 * @package Disciples
 * @author Sangwoo <linkedkorean@gmail.com>
 */
/**
 *  Disciples_Model_Individual
 *
 * @package Disciples
 * @author Sangwoo <linkedkorean@gmail.com>
 */
class Disciples_Model_Individual extends Zend_Db_Table_Abstract
{
    protected $_name    = 'individual';
    protected $_primary = 'id';

    /**
      * Get member details
      *
      * @return array
      */
    public function getMemberDetails($id)
    {
        $row = $this->find($id)->current();
        return $row->toArray();
    }
    
    /**
     * Get all members
     *
     * @return array
     */
    public function getAllMembers()
    {
    	$rows = $this->fetchAll($this->select()->order('name'));
    	return $rows->toArray();
    }
    
    /**
     * Get active members
     *
     * @return array
     */
    public function getActive()
    {
    	$rows = $this->fetchAll($this->select()->where('active = ?', true)->order('name'));
    	return $rows->toArray();
    }

    /**
     * Get inactive members
     *
     * @return array
     */
    public function getInactive()
    {
        $rows = $this->fetchAll($this->select()->where('active = ?', false)->order('name'));
        return $rows->toArray();
    }
    
    /**
     * Add a member
     *
     * @return boolean
     */
    public function addMember($data)
    {
    	$data['created_on'] = new Zend_Db_Expr('NOW()');
    	$this->insert($this->_normalize($data));
    	
    	return $this->getAdapter()->lastInsertId();
    }
    
    /**
     * Remove a member
     *
     * @return boolean
     */
    public function removeMember($id)
    {
    	$this->delete($this->getAdapter()->quoteInto('id = ?', $id));
    	 
    	return true;
    }
    
    /**
     * Update the member info
     *
     * @return boolean
     */
    public function updateMember($data)
    {
    	$where = $this->getAdapter()->quoteInto('id = ?', $data['id']);
    	$this->update($this->_normalize($data), $where);
    
    	return true;
    }
    
    /**
     * Update the member photo
     *
     * @return boolean
     */
    public function updateMemberPhoto($id, $filename)
    {
    	$data = array(
    		'id' => $id,
    		'photo' => $filename,
    	);
    	
    	$this->updateMember($data);
    	
    	return true;
    }
    
    /**
     * Find members by name
     *
     * @return array
     */
    public function getMemberByName($name)
    {    	
    	$rows = $this->fetchAll(
    		$this->select()
    			 ->where('name = ?', $name)
    			 ->orWhere('e_first = ?', $name)
		);
    	return $rows->toArray();
    }
        
    /**
     * Find members by filter
     *
     * @return array
     */
    public function getMemberByFilter(array $filter)
    {
    	$where = array();
    	
    	foreach ($filter as $key => $val) {
    		$where[$key . ' = ?'] = $val;
    	}
    	
    	$rows = $this->fetchAll($where);
    	
    	return $rows->toArray();
    }
    
    /**
     * Normalize the certain data values  
     *
     * @return array
     */
    private function _normalize($data)
    {
    	if (array_key_exists('e_first', $data)) {
    		$data['e_first'] = trim(strtoupper($data['e_first']));
    	}
    	if (array_key_exists('e_last', $data)) {
    		$data['e_last'] = trim(strtoupper($data['e_last']));
    	}
    	if (array_key_exists('e_middle', $data)) {
    		$data['e_middle'] = trim(strtoupper($data['e_middle']));
    	}
    	if (array_key_exists('street', $data)) {
    		$data['street'] = trim(strtoupper($data['street']));
    	}
    	if (array_key_exists('city', $data)) {
    		$data['city'] = trim(strtoupper($data['city']));
    	}
    	if (array_key_exists('home_phone', $data)) {
    		$data['home_phone'] = preg_replace("/[^0-9]/", "", $data['home_phone']);
    	}
    	if (array_key_exists('mobile_phone', $data)) {
    		$data['mobile_phone'] = preg_replace("/[^0-9]/", "", $data['mobile_phone']);
    	}
    	if (array_key_exists('business_phone', $data)) {
    		$data['business_phone'] = preg_replace("/[^0-9]/", "", $data['business_phone']);
    	}
    	
    	return $data;
    }
}