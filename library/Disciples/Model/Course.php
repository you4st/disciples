<?php
/**
 * Course.php
 * Database interface for Course table
 *
 * @package Disciples
 * @author Sangwoo Han<linkedkorean@gmail.com>
 */
/**
 *  Disciples_Model_Course
 *
 * @package Disciples
 * @author Sangwoo Han<linkedkorean@gmail.com>
 */
class Disciples_Model_Course extends Zend_Db_Table_Abstract
{
    protected $_name    = 'course';
    protected $_primary = 'id';

    public function getCourseNameById($id)
    {
        return $this->find($id)->current()->course_name;
    }
    
    public function getAllCourses()
    {
    	$rows = $this->fetchAll($this->select());
    	return $rows->toArray();
    }
}