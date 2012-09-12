<?php

class ActivityController extends Zend_Controller_Action
{

	public function init()
	{
		$this->_helper->viewRenderer->setNoRender(true);
	}

	/**
	 * 获取列表
	 * 
	 * $offset
	 * $count
	 * $company_id
	 * $is_published
	 * $is_completed
	 */
	public function indexAction()
	{
		$offset = $this->getRequest()->getParam('offset', 0);
		$count = $this->getRequest()->getParam('count', 20);
		$company_id = $this->getRequest()->getParam('company_id');
		$is_published = $this->getRequest()->getParam('is_published');
		$is_completed = $this->getRequest()->getParam('is_completed');
		
		$table = new Application_Model_DbTable_Activity();
		$where = '1';
		if ($company_id) {
			$where .= ' AND ' . $table->getAdapter()->quoteInto('company_id', $company_id);
		}
		if ($is_published) {
			$where .= ' AND ' . $table->getAdapter()->quoteInto('is_published', $is_published);
		}
		if ($is_completed) {
			$where .= ' AND ' . $table->getAdapter()->quoteInto('is_completed', $is_completed);
		}
		
		$data = $table->fetchAll(
			$table->select()
				->from($table, '*')
				->where($where)
				->limit($count, $offset)
			);
		$total = $table->fetchRow($table->select()->from($table, 'count(*) as total')->where($where));
		
		$rtn = json_encode(array(
			'activity'	=> $data->toArray(),
			'total'		=> $total['total']
		));
		echo $rtn;
	}

	public function showAction()
	{
		$id = $this->getRequest()->getParam('id');
		$slug = $this->getRequest()->getParam('slug');
		$table = new Application_Model_DbTable_Activity();
		if ($id) {
			$condition = $table->getAdapter()->quoteInto('id=?', $id);
		} else if ($slug) {
			$condition = $table->getAdapter()->quoteInto('slug=?', $slug);
		} else {
			throw new Application_Model_Controller_Exception(''
				, Application_Model_Controller_Exception::E_PARAM_REQUIRED);
		}
		
		$rs = $table->fetchAll($condition)->toArray();
		echo json_encode($rs);
	}
	
	public function destroyAction() 
	{
		$id = $this->getRequest()->getParam('id');
		$slug = $this->getRequest()->getParam('slug');
		$table = new Application_Model_DbTable_Activity();
		if ($id) {
			$condition = $table->getAdapter()->quoteInto('id=?', $id);
		} else if ($slug) {
			$condition = $table->getAdapter()->quoteInto('slug=?', $slug);
		} else {
			throw new Application_Model_Controller_Exception(''
				, Application_Model_Controller_Exception::E_PARAM_REQUIRED);
		}
		
		$rs = $table->delete($condition);
		if (!$rs) {
			throw new Application_Model_Controller_Exception(''
				, Application_Model_Controller_Exception::E_DB_UPDATE);
		}
		
		echo json_encode(array('result'=>1));
	}
	
	public function updateAction()
	{
		$id = $this->getRequest()->getParam('id');
		if (!$id) {
			throw new Application_Model_Controller_Exception('param required: id'
				, Application_Model_Controller_Exception::E_PARAM_REQUIRED);	
		}
		
		$accessKeys = array('company_id', 'slug', 'name', 'event_date', 'rate_usd', 'amount'
			, 'is_published', 'description', 'is_completed', 'address', 'rate_fee', 'dealed_rate');
		$params = $this->getRequest()->getParams();
		unset($params['action'], $params['controller'], $params['module']);
		$data = array();
		foreach ($params as $k=>$v) {
			if (in_array($k, $accessKeys)) {
				$data[$k] = $v;
			}
		}
		// 没有更新数据将提示错误
		if (empty($data)) {
			throw new Application_Model_Controller_Exception(''
				, Application_Model_Controller_Exception::E_PARAM_REQUIRED);
		}
		// todo:检测slug是否有重复
		// 更新数据
		$table = new Application_Model_DbTable_Activity();
		$where = $table->getAdapter()->quoteInto('id=?', $id);
		$rs = $table->update($data, $where);
		if (!$rs) {
			throw new Application_Model_Controller_Exception(''
				, Application_Model_Controller_Exception::E_DB_UPDATE);
		}
		
		echo json_encode(array('result'=>1));
	}
	
	
	public function createAction()
	{
		$slug = $this->getRequest()->getParam('slug');
		$name = $this->getRequest()->getParam('name');
		if (!$name || !$slug) {
			throw new Application_Model_Controller_Exception(''
				, Application_Model_Controller_Exception::E_PARAM_REQUIRED);
		}
		// 检测slug是否已经存在
		$table = new Application_Model_DbTable_Activity();
		$where = $table->getAdapter()->quoteInto('slug=?', $slug);
		if ($table->fetchAll($where)->count() > 0){
			throw new Application_Model_Controller_Exception(''
				, Application_Model_Controller_Exception::E_PARAM_DUPLICATE_KEY);
		}
		// insert to db;
		$data = array(
			'name'		=> $name,
			'slug'		=> $slug,
			'created'	=> new Zend_Db_Expr('CURRENT_TIMESTAMP()'),
		);
		$insert_id = $table->insert($data);
		if (!$insert_id){
			throw new Application_Model_Controller_Exception(''
				, Application_Model_Controller_Exception::E_DB_UPDATE);
		}
		$rtn = array(
			'result'	=> 1
		);
		echo json_encode($rtn);
	}
	
}

