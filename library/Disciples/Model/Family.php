<?php
/**
 * Family.php
 * Database interface for family table
 *
 * @package Disciples
 * @author Sangwoo <linkedkorean@gmail.com>
 */
/**
 *  Disciples_Model_Family
 *
 * @package Disciples
 * @author Sangwoo <linkedkorean@gmail.com>
 */
class Disciples_Model_Family extends Zend_Db_Table_Abstract
{
    protected $_name    = 'family';
    protected $_primary = 'id';
    
    /**
     * Get list of family by member id
     *
     * @return array
     */
    public function getFamilyListByMemberId($memberId)
    {
    	$row = $this->fetchRow($this->select()->where('ind_id = ?', $memberId));
    
    	if (!is_null($row)) {
    		$familyList = $this->getFamilyListById($row->fam_id);
    	} else {
    		$familyList = array();
    	}
    	
    	return $familyList;
    }
    
    /**
     * Get list of family by family id
     *
     * @return array
     */
    public function getFamilyListById($id)
    {
    	$rows = $this->fetchAll($this->select()->where('fam_id = ?', $id));
    	
    	return $rows->toArray();
    }

    /**
     * Create a family
     * 
     * @return int
     */
    public function createFamily($primaryMemberId)
    {
    	$row = $this->fetchRow($this->select()->from($this, array(new Zend_Db_Expr('max(fam_id) as maxId'))));
    	
    	if (!is_null($row)) {
	    	$maxFamId = $row->maxId;
	    	
	    	$this->addFamilyMember($maxFamId + 1, $primaryMemberId, 1);    	
    	} else {
    		$maxFamId = 0;
    	}	    	    	
    	// return family id
    	return $maxFamId + 1;
    }
    
    /**
     * Add a family member
     *
     * @return boolean
     */
    public function addFamilyMember($famId, $memberId, $relation)
    {
    	$data = array(
    		'ind_id'       => $memberId,
    		'fam_id'       => $famId,
    		'fam_relation' => $relation,
    		'created_on'   => new Zend_Db_Expr('NOW()'),
    	);
    	
    	return $this->insert($data);
    }
    
    /**
     * Remove all family member by family id 
     *
     * @return bool
     */
    public function removeFamily($familyId)
    {
    	$this->delete($this->getAdapter()->quoteInto('fam_id = ?', $familyId));
    
    	return true;
    }
    
    /**
     * Remove a family member
     *
     * @return bool
     */
    public function removeFamilyMember($memberId)
    {
    	$row = $this->fetchRow($this->select()->where('ind_id = ?', $memberId));
    	
    	if (!is_null($row)) {
    		$this->delete($this->getAdapter()->quoteInto('id = ?', $row->id));
    		
    		if ($this->isEmptyFamily($row->fam_id)) {
    			$this->removeFamily($row->fam_id);
    		}
    	}    	
    	 
    	return true;
    }
    
    /**
     * Check any family member exists 
     *
     * @return boolean
     */
    public function isEmptyFamily($famId)
    {
    	$familyList = $this->getFamilyListById($famId);
    	 
		return count($familyList) < 2 ? true : false;
    }

    /**
     * Get a family id by member id
     *
     * @return int
     */
    public function getFamilyIdbyMemberId($memberId)
    {
    	$row = $this->fetchRow($this->select()->where('ind_id = ?', $memberId));
    
    	if (!is_null($row)) {
    		return $row->fam_id;
    	}
    	
    	return 0;
    }
    
    /**
     * Set head of house
     *
     * @return boolean
     */
    public function setHeadOfHouse($familyId, $headId)
    {    	
    	$row = $this->fetchRow($this->select()->where('fam_id = ?', $familyId)->where('head_of_house = ?', 1));
    	$skip = false;
    
    	if (!is_null($row)) {
    		if ($headId == $row->ind_id) {
    			$skip = true;
    		} else {
	    		$where = $this->getAdapter()->quoteInto('id = ?', $row->id);
	    		// unset the current head of house
	    		$this->update(array('head_of_house' => 0), $where);
    		}
    	}    	
    	
    	if (!$skip) {
	    	$where = $this->getAdapter()->quoteInto('ind_id = ?', $headId);
	    	$this->update(array('head_of_house' => 1), $where);
    	}
    	
    	return true;
    }
    
    /**
     * Set head of house
     *
     * @return boolean
     */
    public function isHeadOfHouse($id)
    {    	 
    	$row = $this->fetchRow($this->select()->where('ind_id = ?', $id));
    	    
    	if (!is_null($row)) {
    		return $row->head_of_house;
    	}
    	 
    	return 1;
    }
    
    /**
     * Update relation
     *
     * @return boolean
     */
    public function updateRelation($id, $relation)
    {
    	$where = $this->getAdapter()->quoteInto('ind_id = ?', $id);
    	$this->update(array('fam_relation' => $relation), $where);

    	return true;
    }
}