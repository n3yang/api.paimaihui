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
        // $this->_redirect('/antique/list');
    }

    /**
     * 列表页
     * 
     */
	public function listAction()
	{
		$perpage = 8;
		$pageno = $this->getRequest()->getParam('page', 1);
		$activityId = $this->getRequest()->getParam('activity_id');
		$subActivityId = $this->getRequest()->getParam('sub_activity_id');
		$categoryId = $this->getRequest()->getParam('category');
		$kw = $this->getRequest()->getParam('keyword');
		
		$this->view->title = '搜索结果页';
		
		$table = new Application_Model_DbTable_Antique();
		$where = '1=1';
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
		
		switch ($this->getRequest()->getParam('sort')) {
			case 'priceup':
				$order = 'price';
				break;
			case 'pricedown': 
				$order = 'price desc';
				break;
				
			default:
				$order = '';
				break;
		}
		
		$antiques = $table->fetchAll(
			$table->select()
				->from($table, '*')
				->where($where)
				->limit($perpage, ($pageno-1)*$perpage)
				->order($order)
			)->toArray();
		$rs = $table->fetchRow($table->select()->from($table, 'count(*) as total')->where($where))->toArray();
		$total = $rs['total'];
		
		if ($total > 0) {
			
			// get activity info
			foreach ($antiques as $v) {
				$activityIds[] = $v['activity_id'];
				$antiqueIds[] = $v['id'];
			}
			$tableActivity = new Application_Model_DbTable_Activity();
			$activityInfo = $tableActivity->find($activityIds)->toArray();
			foreach ($activityInfo as $k=>$v) {
				$companyIds[] = $v['company_id'];
			}
			
			// get company info
			$tableCompany = new Application_Model_DbTable_Company();
			$companyInfo = $tableCompany->find($companyIds)->toArray();
			foreach ($activityInfo as $k => $v) {
				foreach ($companyInfo as $ck=>$cv) {
					if ($v['company_id']==$cv['id']) {
						$activityInfo[$k]['company'] = $cv;
					}
				}
			}
			
			// get photo info
			$photoTable = new Application_Model_DbTable_Photo();
			$where = $photoTable->getAdapter()->quoteInto('antique_id in (?)', $antiqueIds);
			$photoes = $photoTable->fetchAll($where)->toArray();
			
			foreach ($antiques as $k=>$v) {
				
				foreach ($activityInfo as $ak=>$av) {
					if ($v['activity_id'] == $av['id']) {
						$antiques[$k]['activity'] = $av;
					}
				}
				
				foreach ($photoes as $pk=>$pv) {
					if ($v['id'] == $pv['antique_id']) {
						$antiques[$k]['photo'] = $pv;
					}
				}
			}
			
		}
		
		
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
		
		$table = new Application_Model_DbTable_Antique();
		$antique = $table->find(intval($id))->toArray();
		$antique = $antique[0];
		
		$tableActivity = new Application_Model_DbTable_Activity();
		$activity = $tableActivity->find($antique['activity_id'])->toArray();
		$antique['activity'] = $activity[0];
		
		$tableSubActivity = new Application_Model_DbTable_SubActivity();
		$subActivity = $tableSubActivity->find($antique['sub_activity_id'])->toArray();
		$antique['sub_activity'] = $subActivity[0];
		
		$tableCompany = new Application_Model_DbTable_Company();
		$company = $tableCompany->find($antique['activity']['company_id'])->toArray();
		$antique['company'] = $company[0];
		
		$tablePhoto = new Application_Model_DbTable_Photo();
		$where = $tablePhoto->getAdapter()->quoteInto('antique_id=?', $id);
		$photo = $tablePhoto->fetchAll($where)->toArray();
		$antique['photo'] = $photo;
		
		$this->view->antique = $antique;
	}
}

