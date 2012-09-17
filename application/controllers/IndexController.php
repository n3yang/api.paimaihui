<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
    }

    /**
     * 列表页
     * 
     */
	public function listAction()
	{
		$data = array();
		for ($i = 0; $i < 100; $i++) {
			$data[$i] = $i . 'xxx';
		}
		$paginator = Zend_Paginator::factory(100);
		$paginator->setDefaultItemCountPerPage(2);
		$paginator->setCurrentPageNumber($this->_getParam('page'));
		$this->view->paginator = $paginator;
	}
	
	/**
	 * 详情页
	 * 
	 */
	public function showAction()
	{
		
	}
}

