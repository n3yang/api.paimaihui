<?php

class CompanyController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
        // $this->_redirect('/antique/list');
    }

    /**
     * 列表页
     * 
     */
	public function listAction()
	{
		$perpage = 10;
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
		$perpageActivity = 4;
		$slug = $this->getRequest()->getParam('slug');
		$pageno = $this->getRequest()->getParam('page', 1);
		
		if (!$slug) {
			throw new Zend_Controller_Exception();
		}
		
		$tableCompany = new Application_Model_DbTable_Company();
		$company = $tableCompany->findBySlug($slug)->toArray();
		$company = $company[0];
		if (!$company) {
			throw new Zend_Controller_Exception();
			
		} else {
			// get activity info from db
			$tableActivity = new Application_Model_DbTable_Activity();
			$where = $tableActivity->getAdapter()->quoteInto('company_id=?', $company['id']);
			// do query
			$activity = $tableActivity->fetchAll(
				$tableActivity->select()
					->from($tableActivity, '*')
					->where($where)
					->limit($perpageActivity, ($pageno-1)*$perpageActivity)
				)->toArray();
			// get activity id
			foreach ($activity as $ak=>$av) {
				$activityIds[] = $av['id'];
			}
			// get total of activity
			$rs = $tableActivity->fetchRow($tableActivity->select()->from($tableActivity, 'count(*) as total')->where($where))->toArray();
			$total = $rs['total'];
			
			//  get sub activity info from db
			$tableSubActivity = new Application_Model_DbTable_SubActivity();
			$where = $tableActivity->getAdapter()->quoteInto('activity_id in (?)', $activityIds);
//			$where  .= $tableActivity->getAdapter()->quoteInto(' AND is_published=?');
			$subActivity = $tableSubActivity->fetchAll(
				$tableSubActivity->select()
					->from($tableSubActivity)
					->where($where)
				)->toArray();
			// format data 
			foreach ($activity as $k=>$v) {
				foreach ($subActivity as $sk=>$sv) {
					if ($v['id']==$sv['activity_id']) {
						$activity[$k]['sub_activity'][] = $sv;
					}
				}
			}
			
			$this->view->activity = $activity;
			$this->view->company = $company;
			
			// paginator
			$paginator = Zend_Paginator::factory(intval($total));
			$paginator->setDefaultItemCountPerPage($perpageActivity);
			$paginator->setCurrentPageNumber($pageno);
			$this->view->paginator = $paginator;
		}

	}
}

