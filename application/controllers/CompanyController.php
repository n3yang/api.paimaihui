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
        $this->_redirect('/antique/list');
    }

    /**
     * 列表页
     * 
     */
	public function listAction()
	{
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

		$mCompany = new Application_Model_Company();
		$company = $mCompany->getOneBySlug($slug);

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
					->order('event_date desc')
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
					->order('event_date desc')
				)->toArray();
			// format data 
			$mAntique = new Application_Model_Antique();
			foreach ($activity as $k=>$v) {
				$fullCount = 0;
				foreach ($subActivity as $sk=>$sv) {
					if ($v['id']==$sv['activity_id']) {
						$sv['antique_count'] = $mAntique->getCountBySubAcitivityId($sv['id']);
						$activity[$k]['sub_activity'][] = $sv;
						$fullCount += $sv['antique_count'];
					}
				}
				$activity[$k]['antique_count'] = $fullCount;
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

