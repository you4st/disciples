<?php
/**
 * Nurture.php
 * Database interface for nurture table
 *
 * @package Disciples
 * @author Sangwoo Han<linkedkorean@gmail.com>
 */
/**
 *  Disciples_Model_Nurture
 *
 * @package Disciples
 * @author Sangwoo Han<linkedkorean@gmail.com>
 */
class Disciples_Model_Nurture extends Zend_Db_Table_Abstract
{
    protected $_name    = 'nurture';
    protected $_primary = 'id';
    
    /**
     * Get list of nurture course taken by member id
     *
     * @return array
     */
    public function getNurtureListByMemberId($memberId)
    {
    	$rows = $this->fetchAll($this->select()->where('ind_id = ?', $memberId));
		$courseModel = new Disciples_Model_Course();
		$courses = $courseModel->getAllCourses();
		$nurtureList = array();

		foreach ($courses as $course) {
			$nurtureList[$course['id']] = array(
				'courseName' => $course['course_name'],
				'completed'   => false
			);
		}

    	if (!is_null($rows)) {
    		foreach ($rows as $row) {
				$nurtureList[$row->course_id]['completed'] = true;
			}
    	}
    	
    	return $nurtureList;
    }

    public function updateNurtureInfo($id, $courseList)
    {
        // remove all first
        $this->deleteAllNurtureInfo($id);

        if (!empty($courseList)) {
            // re-insert the rows
            foreach ($courseList as $courseId) {
                $data = array(
                    'ind_id' => $id,
                    'course_id' => $courseId
                );
                $this->insert($data);
            }
        }

    	return true;
    }

	public function deleteAllNurtureInfo($id)
	{
		$this->delete($this->getAdapter()->quoteInto('ind_id = ?', $id));

		return true;
	}
}