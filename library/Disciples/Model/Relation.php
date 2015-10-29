<?php
/**
 * Relation.php
 * Database interface for relation table
 *
 * @package Disciples
 * @author Sangwoo <linkedkorean@gmail.com>
 */
/**
 *  Disciples_Model_Relation
 *
 * @package Disciples
 * @author Sangwoo <linkedkorean@gmail.com>
 */
class Disciples_Model_Relation extends Zend_Db_Table_Abstract
{
    protected $_name    = 'relation';
    protected $_primary = 'id';

    public function getRelationNameById($id)
    {
        return $this->find($id)->current()->relation_name;
    }
    
    public function getAllRelation()
    {
    	$rows = $this->fetchAll($this->select());
    	return $rows->toArray();
    }
}