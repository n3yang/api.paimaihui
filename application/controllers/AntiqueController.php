<?php

class AntiqueController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
        // get options from ini files
		// $this->getInvokeArg('bootstrap')->getOptions();
    }

    public function indexAction()
    {
    	
    	$mLink = new Application_Model_Link();
    	$this->view->subs = $mLink->get();
    }

    /**
     * 列表页
     * 
     */
	public function listAction()
	{
		//$this->view->title = '搜索结果页';
		$perpage = 8;
		$pageno = $this->getRequest()->getParam('page', 1);
		
		$mAntique = new Application_Model_Antique();
		$condition = array(
			'keyword'	=> $this->getRequest()->getParam('keyword')
		);
		$mAntique->setWithCompany()->setWithPhoto();
		$rs = $mAntique->getSearch($condition, $perpage, ($pageno-1)*$perpage);
		$antiques = $rs['data'];
		$total = $rs['total'];
		
		$this->view->assign('antiques', $antiques);
		$this->view->assign('total', $total);
		
		// paginator
		$paginator = Zend_Paginator::factory(intval($total));
		$paginator->setDefaultItemCountPerPage($perpage);
		$paginator->setCurrentPageNumber($pageno);
		$this->view->paginator = $paginator;
		
	}
	
	/**
	 * 详情页
	 * 
	 */
	public function showAction()
	{
		$id = $this->getRequest()->getParam('id');
		
		if (!$id) {
			throw new Zend_Controller_Exception();
		}
		$mAntique = new Application_Model_Antique();
		$mAntique->setWithActivity()
			->setWithCompany()
			->setWithSubActivity()
			->setWithPhoto();
		$this->view->antique = $mAntique->getOneById($id);
	}
}

