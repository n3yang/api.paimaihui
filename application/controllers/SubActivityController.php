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

		$mSubActivity = new Application_Model_SubActivity();
		$subActivity = $mSubActivity->getOneById($id);

		if (!$subActivity) {
			throw new Zend_Controller_Exception();
		} else {
			// get antique
			$condition = array('sub_id'=>$id);
			$mAntique = new Application_Model_Antique();
			$mAntique->setWithPhoto()->setWithActivity()->setWithCompany();
			$rs = $mAntique->getSearch($condition, $perpage, ($pageno-1)*$perpage, 'lotup');
			$this->view->antiques = $antiques = $rs['data'];
			$total = $rs['total'];
			// get activity
			$subActivity['activity'] = $antiques[0]['activity'];
			// get company
			$subActivity['company'] = $antiques[0]['company'];
			
		}
		$this->view->subActivity = $subActivity;
		
		$paginator = Zend_Paginator::factory(intval($total));
		$paginator->setDefaultItemCountPerPage($perpage);
		$paginator->setCurrentPageNumber($pageno);
		$this->view->paginator = $paginator;
	}
}