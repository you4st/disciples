<?php
class AjaxController extends Zend_Controller_Action
{
    public function init()
    {
        $this->_user = new Disciples_User();
        $this->_session = new Zend_Session_Namespace('DISCIPLES');
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        $this->view = new Zend_View();
        $this->view->addScriptPath('./application/views/overlays');
        $this->view->addScriptPath('./application/views/partials');
    }

    public function contentAction()
    {
        if ($this->_request->isXmlHttpRequest()) {
            $target = $this->_getParam('target');
            $param = $this->_getParam('param');

            //Invalid target name
            if($target == 'undefined' || empty($target)) {
                return false;
            }

            if ($target == 'new-member.phtml') {
                echo $this->view->partial($target, array(
                    'dutyOptions'    => $this->_getDutyOptions(),
                    'maritalOptions' => $this->_getMaritalOptions(),
                    'stateOptions'   => $this->_getStateOptions(),
                    'nurtureOptions'   => $this->_getNurtureOptions(),
                ));
            } else if ($target == 'change-family.phtml') {
                $familyMembers = $this->_loadFamilyInfo($param);
                $familyMember = reset($familyMembers);
                $familyId = $familyMember['familyId'];
                echo $this->view->partial($target, array('members' => $familyMembers, 'familyId' => $familyId));
            } else if ($param) {
                echo $this->view->partial($target, array('param' => $param));
            } else {
                echo $this->view->render($target);
            }
        }
    }

    public function loadMemberDetailsAction()
    {
        if ($this->_request->isXmlHttpRequest()) {
            $id = $this->_request->getParam('id');
			$reload = $this->_request->getParam('reload');
            //Invalid target name
            if ($id == 'undefined' || empty($id)) {
                return false;
            }
                        
            $personal = $this->_loadMemberInfo($id, $reload);
            $personal['isAdmin'] = $this->_user->isAdmin();
            $personal['nurture'] = $this->_getNurtureListById($id);
            $personal['nurtureOptions'] = $this->_getNurtureOptions($id);
            $family = $this->_loadFamilyInfo($id);
            
            // general tab
            $personalInfo = $this->view->partial('_member-info.phtml', $personal);
            
            // family tab
            $familyInfo = $this->view->partial('_family-info.phtml', array(
                'id'      => $id,
                'members' => $family,
            	'isAdmin' => $this->_user->isAdmin()
            ));
            
            // map tab
            $address = $personal['street'] . ', ' . $personal['city'] . ', ' . $personal['state'] . ' ' . $personal['zip'];
                        
            $this->_helper->json(array(
                'success'      => 1,
                'personalInfo' => $personalInfo,
                'familyInfo'   => $familyInfo,
                'address'      => $address,
                'name'         => $personal['name'],
            ));
        }
    }
    
    public function getFamilyRelationAction()
    {
        if ($this->_request->isXmlHttpRequest()) {
            $id = $this->_request->getParam('id');
            
            if ($id == 'undefined' || empty($id)) {
                $this->_helper->json(array(
                    'success'   => 1,
                    'relations' => $this->_getRelationOptions(),
                ));
            } else {
                $relation = new Disciples_Model_Relation();
                
                $this->_helper->json(array(
                    'success'      => 1,
                    'relationName' => $relation->getRelationNameById($id),
                ));
            }
        }
    }
    
    public function registerMemberAction()
    {
        if ($this->_request->isXmlHttpRequest()) {
            try {
                $data = $this->_request->getParams();
                $numFamily = $data['numFamily'];
                $familyMember = array();
                $nurture = $data['nurture'];

                if ($numFamily > 0) {
                    $familyMember = $data['family'];
                }

                unset($data['controller']);
                unset($data['action']);
                unset($data['module']);
                unset($data['numFamily']);
                unset($data['family']);
                unset($data['nurture']);

                if ($data['duty'] == '0') {
                    unset($data['duty']);
                }

                foreach ($data as $key => $val) {
                    if ($val == '') {
                        unset($data[$key]);
                    }
                }

                if ($error = $this->_validateFormData($data)) {
                    $this->_helper->json(array(
                        'success' => 0,
                        'message' => $error,
                    ));
                }

                $individual = new Disciples_Model_Individual();
                $primaryMemberId = $individual->addMember($data);

                // update nurture
                if (!empty($nurture)) {
                    $nurtureModel = new Disciples_Model_Nurture();
                    $nurtureModel->updateNurtureInfo($primaryMemberId, $nurture);
                }

                // add family member
                if ($numFamily > 0) {
                    $family = new Disciples_Model_Family();
                    $famId = $family->createFamily($primaryMemberId);

                    foreach ($familyMember as $idx => $f_data) {
                        $f_data['home_phone'] = $data['home_phone'];
                        $f_data['registered_on'] = $data['registered_on'];
                        $f_data['street'] = $data['street'];
                        $f_data['city'] = $data['city'];
                        $f_data['state'] = $data['state'];
                        $f_data['zip'] = $data['zip'];
                        $relation = $f_data['relation'];
                        unset($f_data['relation']);
                        $memberId = $individual->addMember($f_data);
                        $family->addFamilyMember($famId, $memberId, $relation);
                    }
                }

                $this->_helper->json(array('success' => 1));
            } catch (Exception $ex) {
                Disciples_Logger::getInstance(__CLASS__)->error($ex->getMessage());
                $this->_helper->json(array(
                    'success' => 0,
                    'message' => $ex->getMessage(),
                ));
            }
        }
    }

    public function updateMemberAction()
    {
        if ($this->_request->isXmlHttpRequest()) {
            try {
                $data = $this->_request->getParams();
                $nurture = $data['nurture'];
                
                unset($data['controller']);
                unset($data['action']);
                unset($data['module']);
                unset($data['nurture']);

                if ($data['duty'] == '0') {
                    unset($data['duty']);
                }

                if ($error = $this->_validateFormData($data)) {
                    $this->_helper->json(array(
                        'success' => 0,
                        'message' => $error,
                    ));
                }

                $individual = new Disciples_Model_Individual();
                $individual->updateMember($data);

                // update nurture
                if (!empty($nurture)) {
                	$nurtureModel = new Disciples_Model_Nurture();
                	$nurtureModel->updateNurtureInfo($data['id'], $nurture);
                }

                $this->_helper->json(array('success' => 1));
            } catch (Exception $ex) {
                Disciples_Logger::getInstance(__CLASS__)->error($ex->getMessage());
                $this->_helper->json(array(
                    'success' => 0,
                    'message' => $ex->getMessage(),
                ));
            }
        }
    }

    public function removeMemberAction()
    {
        if ($this->_request->isXmlHttpRequest()) {
            $data = $this->_request->getParam('data');
            $family = new Disciples_Model_Family();
            $individual = new Disciples_Model_Individual();

            foreach ($data as $memberId) {
                $family->removeFamilyMember($memberId);
                $individual->removeMember($memberId);
            }

            $this->_helper->json(array(
                'success' => 1,
            ));
        }
    }

    public function searchMemberAction()
    {
        if ($this->_request->isXmlHttpRequest()) {
            $name = $this->_request->getParam('name');

            $individual = new Disciples_Model_Individual();
            $members = $individual->getMemberByName($name);

            if ($this->_request->getParam('list')) {
                $result = array();

                foreach ($members as $member) {
                    $result[] = $member['id'];
                }

                $this->_helper->json(array(
                    'success' => 1,
                    'searchResult' => $result,
                ));
            }

            if (count($members) > 0) {
                $relations = $this->_getRelationOptions();
                $result = $this->view->partial('_search-result.phtml', array(
                    'members' => $members,
                    'relationOptions' => $relations
                ));
            } else {
                $result = $this->view->partial('_errorMessage.phtml', array(
                    'errorMessage' => 'No match found. Please make sure the member you try to find exists in our system.<br />(Please register the member into the system before adding as a family member if he/she is a new church member.)',
                ));
            }

            $this->_helper->json(array(
                'success' => 1,
                'searchResult' => $result,
            ));
        }
    }

    public function filterMemberAction()
    {
        if ($this->_request->isXmlHttpRequest()) {
            $filter = $this->_request->getParam('data');
            $individual_keys = array('duty', 'gender', 'active');

            foreach ($filter as $key => $val) {
                if ($val == '-1') {
                    unset($filter[$key]);
                }
            }

            $individual = new Disciples_Model_Individual();

            $filtering = false;

            foreach ($individual_keys as $key) {
                if (array_key_exists($key, $filter)) {
                    $filtering = true;
                    break;
                }
            }

            if ($filtering) {
                $cloned = $filter;
                if (array_key_exists('head_of_house', $cloned)) {
                    unset($cloned['head_of_house']);
                }
                if (array_key_exists('age', $cloned)) {
                    unset($cloned['age']);
                }                
                if (array_key_exists('registered', $cloned)) {
                    unset($cloned['registered']);
                }
                $members = $individual->getMemberByFilter($cloned);
            } else {
                $members = $individual->getAllMembers();
            }

            if (array_key_exists('head_of_house', $filter)) {
                $family = new Disciples_Model_Family();
                foreach ($members as $id => $member) {
                    if ($filter['head_of_house'] != $family->isHeadOfHouse($member['id'])) {
                        unset($members[$id]);
                    }
                }
            }

            if (array_key_exists('age', $filter)) {
                foreach ($members as $id => $member) {
                    $age = (date("md", date("U", mktime(0, 0, 0, $member['birth_month'], $member['birth_day'], $member['birth_year']))) > date("md")
                         ? ((date("Y") - $member['birth_year']) - 1)
                         : (date("Y") - $member['birth_year']));

                    if ($filter['age'] == '0') {
                        if ($age >= 10) {
                            unset($members[$id]);
                        }
                    } else if ($filter['age'] == '80') {
                        if ($age < 80) {
                            unset($members[$id]);
                        }
                    } else {
                        if (!($filter['age'] <= $age && $age < $filter['age'] + 10)) {
                            unset($members[$id]);
                        }
                    }
                }
            }

            if (array_key_exists('registered', $filter)) {
                foreach ($members as $id => $member) {
                    $registered = ($member['registered_on'] && $member['registered_on'] != '0000-00-00') ? 1 : 0;

                    if ($registered != $filter['registered']) {
                        unset($members[$id]);
                    }
                }
            }

            $result = array();

            foreach ($members as $member) {
                $result[] = $member['id'];
            }

            $this->_helper->json(array(
                'success' => 1,
                'filterResult' => $result,
            ));
        }
    }

    public function addFamilyMemberAction()
    {
        if ($this->_request->isXmlHttpRequest()) {
            $memberId = $this->_request->getParam('id');
            $newMemberId = $this->_request->getParam('new_id');
            $relation = $this->_request->getParam('relation');

            $family = new Disciples_Model_Family();

            // already a member of family
            if ($family->getFamilyIdbyMemberId($newMemberId)) {
                $this->_helper->json(array(
                    'success' => 0,
                    'message' => 'The selected individual is already associated with another family.<br />Please verify the member name and try again.',
                ));
            }

            $familyId = $family->getFamilyIdbyMemberId($memberId);

            if (!$familyId) {
                $familyId = $family->createFamily($memberId);
            }

            $family->addFamilyMember($familyId, $newMemberId, $relation);

            $this->_helper->json(array('success' => 1));
        }
    }

    public function changeFamilyInfoAction()
    {
        if ($this->_request->isXmlHttpRequest()) {
            $familyId = $this->_request->getParam('familyId');
            $headId = $this->_request->getParam('head_of_house');
            $relation = $this->_request->getParam('relation');
            $remove = $this->_request->getParam('remove');

            $family = new Disciples_Model_Family();

            // set head of house
            $family->setHeadOfHouse($familyId, $headId);

            // update relation
            foreach ($relation as $id => $relation) {
                $family->updateRelation($id, $relation);
            }

            // remove members
            foreach ($remove as $memberId) {
                $family->removeFamilyMember($memberId);
            }

            $this->_helper->json(array('success' => 1));
        }
    }

    public function uploadPhotoAction()
    {
    	if ($this->_request->isXmlHttpRequest()) {
	        $allowedExts = array('gif', 'jpeg', 'jpg', 'png');
	        $allowedTypes = array('image/gif', 'image/jpeg', 'image/pjpeg', 'image/jpg', 'image/png', 'image/x-png');
	        $file = $_FILES['file'];
	        $memberId = $this->_request->getParam('id');
	        $temp = explode('.', $file['name']);
	        $extension = end($temp);
	        $errorMessage = '';

	        if (!isset($file['error']) || is_array($file['error'])) {
	            $errorMessage = 'There\'s an error while uploading the image file. Please try again.';
	        } else {
	            switch ($file['error']) {
	                case UPLOAD_ERR_OK:
	                    break;
	                case UPLOAD_ERR_NO_FILE:
	                    $errorMessage = 'No file sent. Please select a valid image file.';
	                case UPLOAD_ERR_INI_SIZE:
	                case UPLOAD_ERR_FORM_SIZE:
	                    $errorMessage = 'Please reduce the image size and try again. (max: 200 kbytes)';
	                default:
	                    $errorMessage = 'There\'s an error while uploading the image file. Please try again.';
	            }
	        }

	        if (!$errorMessage) {
		        if ($file["size"] > 200000) {
		        	$errorMessage = 'Please reduce the image size and try again. (max: 200 kbytes)';
		        } else if (!(in_array($file['type'], $allowedTypes) || in_array($extension, $allowedExts))) {
		        	$errorMessage = 'Invalid image format. Please use a valid image types. (jpg, gif, or png)';
		        }
	        }

	        if ($errorMessage) {
	            $this->_helper->json(array(
	                'success' => 0,
	                'message' => $errorMessage,
	            ));
	        } else {
	            // get a unique file name
	            do {
	            	$filename = $this->_generateFileName($file['tmp_name'], $extension);
	            } while (file_exists("upload/" . $filename));

	            // copy file to 'upload' directory
	            move_uploaded_file($file["tmp_name"], "upload/" . $filename);

	            // update table with the photo name
	            $individual = new Disciples_Model_Individual();
	            $individual->updateMemberPhoto($memberId, $filename);

	            $this->_helper->json(array('success' => 1));
	        }
        }
    }

    private function _loadMemberInfo($id, $reload = false)
    {
        try {
            if (array_key_exists($id, $this->_session->members) && !$reload) {
                $memberDetails = $this->_session->members[$id];
            } else {
                $individual = new Disciples_Model_Individual();
                $memberDetails = $individual->getMemberDetails($id);
                $duty = new Disciples_Model_Duty();
                $memberDetails['dutyName'] = $duty->getDutyNameById($memberDetails['duty']);
                $marital = new Disciples_Model_Marital();
                $memberDetails['maritalStatus'] = $marital->getMaritalStatusNameById($memberDetails['marital_status']);
                $this->_session->members[$id] = $memberDetails;
            }

            $memberDetails['dutyOptions'] = $this->_getDutyOptions($memberDetails['duty']);
            $memberDetails['maritalOptions'] = $this->_getMaritalOptions($memberDetails['marital_status']);
            $memberDetails['stateOptions'] = $this->_getStateOptions($memberDetails['state']);

            return $memberDetails;
        } catch(Exception $ex) {
            Disciples_Logger::getInstance(__CLASS__)->error($ex->getMessage());
        }
    }

    private function _loadFamilyInfo($id)
    {
        try {
            $family = new Disciples_Model_Family();
            $indivisual = new Disciples_Model_Individual();
            $relation = new Disciples_Model_Relation();
            $familyList = $family->getFamilyListByMemberId($id);
            $members = array();

            if (count($familyList) > 0) {
                foreach ($familyList as $member) {
                    $members[$member['ind_id']] = $indivisual->getMemberDetails($member['ind_id']);
                    $members[$member['ind_id']]['relationName'] = $relation->getRelationNameById($member['fam_relation']);
                    $members[$member['ind_id']]['relationOptions'] = $this->_getRelationOptions($member['fam_relation']);
                    $members[$member['ind_id']]['head_of_house'] = $member['head_of_house'];
                    $members[$member['ind_id']]['familyId'] = $member['fam_id'];
                }
            } else {
                $members[$id] = $indivisual->getMemberDetails($id);
            }
            return $members;
        } catch(Exception $ex) {
            Disciples_Logger::getInstance(__CLASS__)->error($ex->getMessage());
        }
    }

    private function _getDutyOptions($id = 0)
    {
        $duty = new Disciples_Model_Duty();
        $options = '';

        foreach ($duty->getAllDuty() as $row) {
            $selected = ($id == $row['id'] ? ' selected="selected"' : '');
            $options = $options . '<option value="' . $row['id'] . '"' . $selected . '>' . $row['duty_name'] . '</option>';
        }

        return $options;
    }

    private function _getMaritalOptions($id = 0)
    {
        $marital = new Disciples_Model_Marital();
        $options = '';

        foreach ($marital->getAllMaritalStatus() as $row) {
            $selected = ($id == $row['id'] ? ' selected="selected"' : '');
            $options = $options . '<option value="' . $row['id'] . '"' . $selected . '>' . $row['status_name'] . '</option>';
        }

        return $options;
    }

    private function _getNurtureOptions($id = 0)
    {
        $list = '';

        if ($id > 0) {
            $nurtureModel = new Disciples_Model_Nurture();
            $nurtureList = $nurtureModel->getNurtureListByMemberId($id);

            foreach ($nurtureList as $courseId => $data) {
                $checked = $data['completed'] ? ' checked="checked"' : '';
                $list = $list . '<input type="checkbox" name="nurture_' . $id . '" value="' . $courseId . '"' . $checked . ' />' . $data['courseName'] . '<br>';
            }
        } else {
            $course = new Disciples_Model_Course();

            foreach ($course->getAllCourses() as $row) {
                $list = $list . '<input type="checkbox" name="nurture" value="' . $row['id'] . '" />' . $row['course_name'] . '<br>';
            }
        }

        return $list;
    }
    
    private function _getNurtureListById($id)
    {
    	$list = '<ul>';
    
    	if ($id > 0) {
    		$nurtureModel = new Disciples_Model_Nurture();
    		$nurtureList = $nurtureModel->getNurtureListByMemberId($id);

    		foreach ($nurtureList as $courseId => $data) {
    			if ($data['completed']) {
    				$list .= '<li>' . $data['courseName'] . '</li>';
    			}
    		}
    	}

    	$list .= '</ul>';

    	return $list;
    }

    private function _getRelationOptions($id = 0)
    {
        $relation = new Disciples_Model_Relation();
        $options = '';

        foreach ($relation->getAllRelation() as $row) {
            $selected = ($id == $row['id'] ? ' selected="selected"' : '');
            $options = $options . '<option value="' . $row['id'] . '"' . $selected . '>' . $row['relation_name'] . '</option>';
        }

        return $options;
    }

    private function _validateFormData($data)
    {
        $message = '';

        // @TODO: format validation

        return $message;
    }

    private function _getStateOptions($state = 'WA')
    {
        $states = array(
            'AL' => 'Alabama', 'AK' => 'Alaska', 'AZ' => 'Arizona', 'AR' => 'Arkansas', 'CA' => 'California',
            'CO' => 'Colorado', 'CT' => 'Connecticut', 'DE' => 'Delaware', 'DC' => 'District of Columbia',
            'FL' => 'Florida', 'GA' => 'Georgia', 'HI' => 'Hawaii', 'ID' => 'Idaho', 'IL' => 'Illinois',
            'IN' => 'Indiana', 'IA' => 'Iowa', 'KS' => 'Kansas', 'KY' => 'Kentucky', 'LA' => 'Louisiana',
            'ME' => 'Maine', 'MD' => 'Maryland', 'MA' => 'Massachusetts', 'MI' => 'Michigan', 'MN' => 'Minnesota',
            'MS' => 'Mississippi', 'MO' => 'Missouri', 'MT' => 'Montana', 'NE' => 'Nebraska', 'NV' => 'Nevada',
            'NH' => 'New Hampshire', 'NJ' => 'New Jersey', 'NM' => 'New Mexico', 'NY' => 'New York',
            'NC' => 'North Carolina', 'ND' => 'North Dakota', 'OH' => 'Ohio', 'OK' => 'Oklahoma', 'OR' => 'Oregon',
            'PA' => 'Pennsylvania', 'RI' => 'Rhode Island', 'SC' => 'South Carolina', 'SD' => 'South Dakota',
            'TN' => 'Tennessee', 'TX' => 'Texas', 'UT' => 'Utah', 'VT' => 'Vermont', 'VA' => 'Virginia',
            'WA' => 'Washington', 'WV' => 'West Virginia', 'WI' => 'Wisconsin', 'WY' => 'Wyoming'
        );

        $stateOptions = '';

        foreach ($states as $abr => $fullString) {
            $selected = ($abr == $state ? ' selected="selected"' : '');
            $stateOptions .= '<option value="' . $abr . '"' . $selected . '>' . $fullString . '</option>';
        }

        return $stateOptions;
    }

    private function _generateFileName($base, $ext)
    {
    	$filename = sprintf('%s.%s', sha1_file($base) . mt_rand(), $ext);

    	return $filename;
    }
}