<?php

class SiteController extends Zend_Controller_Action
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
		$perpage = 32;
		$pageno = $this->getRequest()->getParam('page', 1);
		$activity_id = $this->getRequest()->getParam('activity_id');
		$sub_activity_id = $this->getRequest()->getParam('sub_activity_id');
		$category_id = $this->getRequest()->getParam('category');
		
		$this->view->title = '搜索结果页';
		
		
		$table = new Application_Model_DbTable_Antique();
		$where = '1';
		if ($activity_id !== null) {
			$where .= ' AND ' . $table->getAdapter()->quoteInto('activity_id=?', $activity_id);
		}
		if ($sub_activity_id !== NULL) {
			$where .= ' AND ' . $table->getAdapter()->quoteInto('sub_activity_id=?', $sub_activity_id);
		}
		if ($category_id !== NULL) {
			$where .= ' AND ' . $table->getAdapter()->quoteInto('category_id=?', $category_id);
		}

		$antiques = $table->fetchAll(
			$table->select()
				->from($table, '*')
				->where($where)
				->limit($perpage, ($pageno-1)*$perpage)
			)->toArray();
		$rs = $table->fetchRow($table->select()->from($table, 'count(*) as total')->where($where))->toArray();
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
		
	}
}

