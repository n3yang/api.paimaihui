<?php

class SubActivityController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
    }
    
    
	public function showAction()
	{
		$perpage = 20;
		$pageno  = $this->getRequest()->getParam('page', 1);
		$id = $this->getRequest()->getParam('id');
		
		if (!$id) {
			throw new Zend_Controller_Exception();
		}
		// get sub-activity info
		$tSubActivity = new Application_Model_DbTable_SubActivity();
		$subActivity = $tSubActivity->find($id)->toArray();
		$subActivity = $subActivity[0];
		if (!$subActivity) {
			throw new Zend_Controller_Exception();
		} else {
			// get antique
			$condition = array('sub_id'=>$id);
			$mAntique = new Application_Model_Antique();
			$mAntique->setWithPhoto();
			$rs = $mAntique->getSearch($condition, $perpage, ($pageno-1)*$perpage);
			$this->view->antiques = $rs['data'];
			$total = $rs['total'];
			// get activity
			$tActivity = new Application_Model_DbTable_Activity();
			$activity = $tActivity->find($subActivity['activity_id'])->toArray();
			$subActivity['activity'] = $activity[0];
			// get company
			$tCompany = new Application_Model_DbTable_Company();
			$company = $tCompany->find($activity[0]['company_id'])->toArray();
			$subActivity['company'] = $company[0];
			
		}
		$this->view->subActivity = $subActivity;
		
		$paginator = Zend_Paginator::factory(intval($total));
		$paginator->setDefaultItemCountPerPage($perpage);
		$paginator->setCurrentPageNumber($pageno);
		$this->view->paginator = $paginator;
	}
}