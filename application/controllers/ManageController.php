<?php
class ManageController extends Zend_Controller_Action
{
    public function init()
    {
        /* Initialize action controller here */
        $this->_user = new Disciples_User();
        $this->view->isAdmin = $this->_user->isAdmin();
        $this->_helper->layout->setLayout('index-list');

        $this->_session = new Zend_Session_Namespace('DISCIPLES');

        if ($this->_session->device->isMobile) {
            $this->_helper->redirector->gotoUrl('/mobile');
        }

        $this->_loadMembers();
    }

    public function indexAction()
    {
        $this->view->data = $this->_session->members;
        $this->view->dutyOptions = $this->_getDutyOptions();
    }

    public function reloadAction()
    {
        $this->_reloadMembers();
        $this->_helper->redirector->gotoUrl('/manage');
    }
    
    private function _loadMembers($force = false)
    {
        if (is_null($this->_session->members) || $force) {
            try {
                $indivisual = new Disciples_Model_Individual();
                $members = $indivisual->getAllMembers();
                $duty = new Disciples_Model_Duty();
                $marital = new Disciples_Model_Marital();
                
                $sessionMembers = array();

                foreach ($members as $id => $member) {
                    // use member key as an index
                    $sessionMembers[$member['id']] = $member;
                    // set the duty name
                    $sessionMembers[$member['id']]['dutyName'] = $duty->getDutyNameById($members[$id]['duty']);
                    $sessionMembers[$member['id']]['maritalStatus'] = $marital->getMaritalStatusNameById($members[$id]['marital_status']);
                }
                
                $this->_session->members = $sessionMembers;
                
            } catch(Exception $ex) {
                Disciples_Logger::getInstance(__CLASS__)->error($ex->getMessage());
            }
        }
    }

    private function _reloadMembers()
    {
        $this->_loadMembers(true);
    }
    
    public function uploadAction()
    {
        $this->_helper->layout->setLayout('index');
        $file = null;
        
        if ($this->_request->isPost()) {
            $errorMessage = '';
            $file = $_FILES['file'];
            $filename = basename($file['name']);
            $ext = substr($filename, strrpos($filename, '.') + 1);
            
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
                $excelTypes = array(
                    'text/xls',
                    'application/vnd.ms-excel',
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',                        
                );
                
                if ($file["size"] > 2000000) {
                    $errorMessage = 'Please reduce the image size and try again. (max: 2 MB)';
                } else if (!(($ext == 'xls' || $ext == 'xlsx') && in_array($file['type'], $excelTypes))) {
                    $errorMessage = 'Invalid file format. Please use a .xls or .xlsx file.';
                }
            }
            
            if ($errorMessage) {
                $this->view->errorMessage = $this->view->partial('_errorMessage.phtml', array(
                    'errorMessage' => $errorMessage,
                ));                
            } else {
                
                $this->view->fileData = $file;
                $excelData = $this->_parseExcelFile($file['tmp_name']);
                
                foreach ($excelData as $row) {
                	$member = array();
                	$spouse = array();
                	
                }
                
                
                echo "<pre>" . print_r($excelData,1) . "</pre>";
                exit();
            }
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
    
    private function _parseExcelFile($filename)
    {
    	$PHPExcel = PHPExcel_IOFactory::load($filename);
    	$data = array();
    	
    	foreach ($PHPExcel->getWorksheetIterator() as $worksheet) {
    		
    		foreach ($worksheet->getRowIterator() as $row) {
    			$rowData = array();
    			
    			if ($row->getRowIndex() == 1) {
    				// skip for headers
    				continue;
    			}
    	
    			$cellIterator = $row->getCellIterator();
    			$cellIterator->setIterateOnlyExistingCells(false); // Loop all cells, even if it is not set    			
    	
    			foreach ($cellIterator as $cell) {
    				if (!is_null($cell)) {
    					$alpha = preg_replace("/[^a-zA-Z]/", "", $cell->getCoordinate());
    					$rowData[$alpha] = $cell->getCalculatedValue();
    				}
    			}
    			
    			$data[] = $rowData;
    		}
    	}
    	
    	return $data;
    }
}