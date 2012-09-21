<?php

class AntiqueController extends Zend_Controller_Action
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
		$activityId = $this->getRequest()->getParam('activity_id');
		$subActivityId = $this->getRequest()->getParam('sub_activity_id');
		$categoryId = $this->getRequest()->getParam('category');
		$kw = $this->getRequest()->getParam('kw');
		
		$this->view->title = '搜索结果页';
		
		$table = new Application_Model_DbTable_Antique();
		$where = '1';
		if ($activityId !== null) {
			$where .= ' AND ' . $table->getAdapter()->quoteInto('activity_id=?', $activityId);
		}
		if ($subActivityId !== NULL) {
			$where .= ' AND ' . $table->getAdapter()->quoteInto('sub_activity_id=?', $subActivityId);
		}
		if ($categoryId !== NULL) {
			$where .= ' AND ' . $table->getAdapter()->quoteInto('category_id=?', $categoryId);
		}
		if ($kw !== null ) {
			$where .= ' AND name like ' . $table->getAdapter()->quote('%'.$kw.'%');
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
		
		// get activity info
		foreach ($antiques as $v) {
			$activityIds[] = $v['activity_id'];
		}
		$tableActivity = new Application_Model_DbTable_Activity();
		$activityInfo = $tableActivity->find($activityIds)->toArray();
		foreach ($activityInfo as $k=>$v) {
			$data[$v['id']] = $v;
		}
		$this->view->activityInfo = $data;
		
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
		
		$table = new Application_Model_DbTable_Antique();
		$antique = $table->find(intval($id))->toArray();
		$antique = $antique[0];
		$tableActivity = new Application_Model_DbTable_Activity();
		$activity = $tableActivity->find($antique['activity_id']);
		$antique['activity'] = $activity[0];
		$this->view->antique = $antique;
	}
}

