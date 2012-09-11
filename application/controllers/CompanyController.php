<?php

class CompanyController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    	$this->_helper->viewRenderer->setNoRender(true);
    }

    public function indexAction()
    {
        $this->showAction();
        
    }

    public function showAction()
    {
    	$id = $this->getRequest()->getParam('id');
    	$slug = $this->getRequest()->getParam('slug');
    	if (empty($id) || empty($slug)) {
    		throw new Application_Model_Controller_Exception(''
    			, Application_Model_Controller_Exception::EXCEPTION_PARAM_REQUIRED);
    	}
    	$dbtable = new Application_Model_DbTable_Company();
    	$rs = $dbtable->find($id)->toArray();
    	echo json_encode($rs);
    }
    
	public function createAction()
	{
		$slug = $this->getRequest()->getParam('slug');
		$name = $this->getRequest()->getParam('name');
		// 检测slug是否已经存在
		$dbtable = new Application_Model_DbTable_Company();
		$dbtable->fetchAll(array('slug'=>$slug))->toArray();
		
	}
	
	public function destroyAction() 
	{
		;
	}
	
	public function updateAction()
	{
		$id = $this->getRequest()->getParam('id');
		$slug = $this->getRequest()->getParam('slug');
		if (empty($id) || empty($slug)) {
			
		}
		
	}
}

