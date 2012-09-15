<?php

class SubActivityController extends Zend_Controller_Action
{
	/**
	 * Application_Model_DbTable_SubActivity
	 * 
	 * @var Application_Model_DbTable_SubActivity
	 */
	protected $_dbTable = NULL;
	
	
	public function init()
	{
		/* Initialize action controller here */
		$this->_helper->viewRenderer->setNoRender(true);
		$this->_dbTable = new Application_Model_DbTable_SubActivity();
	}


	/**
	 * 获取列表
	 * 
	 * $offset
	 * $count
	 * $activity_id
	 * $is_published
	 * $is_completed
	 */
	public function indexAction()
	{
		$offset = $this->getRequest()->getParam('offset', 0);
		$count = $this->getRequest()->getParam('count', 20);
		$activity_id = $this->getRequest()->getParam('activity_id');
		$is_published = $this->getRequest()->getParam('is_published');
		$is_completed = $this->getRequest()->getParam('is_completed');
		
		$table = $this->_dbTable;
		$where = '1';
		if ($activity_id) {
			$where .= ' AND ' . $table->getAdapter()->quoteInto('activity_id', $activity_id);
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
				->order('sort_order DESC')
				->limit($count, $offset)
			);
		$total = $table->fetchRow($table->select()->from($table, 'count(*) as total')->where($where));
		
		$rtn = json_encode(array(
			'sub_activity'	=> $data->toArray(),
			'total'			=> $total['total']
		));
		echo $rtn;
	}

	public function showAction()
	{
		$id = $this->getRequest()->getParam('id');
		$table = $this->_dbTable;
		if ($id) {
			$condition = $table->getAdapter()->quoteInto('id=?', $id);
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
		$table = $this->_dbTable;
		if ($id) {
			$condition = $table->getAdapter()->quoteInto('id=?', $id);
		} else {
			throw new Application_Model_Controller_Exception(''
				, Application_Model_Controller_Exception::E_PARAM_REQUIRED);
		}
		
		$rs = $table->delete($condition);
		if (!$rs) {
			throw new Application_Model_Controller_Exception(''
				, Application_Model_Controller_Exception::E_DB_NOT_UPDATED);
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
		
		$allowFields = array('activity_id', 'name', 'event_date', 'amount', 'is_published'
			, 'description', 'sort_order', 'dealed_rate', 'is_completed');
		$params = $this->getRequest()->getParams();
		unset($params['action'], $params['controller'], $params['module']);
		$data = array();
		foreach ($params as $k=>$v) {
			if (in_array($k, $allowFields)) {
				$data[$k] = $v;
			}
		}
		
		// 没有更新数据将提示错误
		if (empty($data)) {
			throw new Application_Model_Controller_Exception(''
				, Application_Model_Controller_Exception::E_PARAM_REQUIRED);
		}
		
		// 检测activity_id是否存在
		$dbTableActivity = new Application_Model_DbTable_Company();
		if (!$dbTableActivity->isExistedId($data['activity_id'])){
			throw new Application_Model_Controller_Exception('param faild: activity_id'
				, Application_Model_Controller_Exception::E_PARAM_REQUIRED);
		}
		
		// 更新数据
		$where = $this->_dbTable->getAdapter()->quoteInto('id=?', $id);
		$rs = $this->_dbTable->update($data, $where);
		if (!$rs) {
			throw new Application_Model_Controller_Exception(''
				, Application_Model_Controller_Exception::E_DB_NOT_UPDATED);
		}
		
		echo json_encode(array('result'=>1));
	}
	
	
	public function createAction()
	{
		// 不允许为空的字段
		$noEmptyFields = array('activity_id', 'name', 'is_published', 'is_completed');
		
		// 允许录入的字段
		$allowFields = array('activity_id', 'name', 'event_date', 'amount', 'is_published'
			, 'description', 'sort_order', 'dealed_rate', 'is_completed');
		$params = $this->getRequest()->getParams();
		unset($params['action'], $params['controller'], $params['module']);
		$data = array();
		foreach ($params as $k=>$v) {
			if (in_array($k, $allowFields)) {
				$data[$k] = $v;
			}
		}
		
		// 检测不能为空的字段
		foreach ($noEmptyFields as $v) {
			if ( !isset($data[$v]) || $data[$v] !== '') {
				throw new Application_Model_Controller_Exception('param required: ' . $v
					, Application_Model_Controller_Exception::E_PARAM_REQUIRED);
			}
		}
		
		// 检测activity_id是否存在
		$dbTableActivity = new Application_Model_DbTable_Company();
		if (!$dbTableActivity->isExistedId($data['activity_id'])){
			throw new Application_Model_Controller_Exception('param faild: activity_id'
				, Application_Model_Controller_Exception::E_PARAM_REQUIRED);
		}
		
		// insert to db;
		$data['created'] = new Zend_Db_Expr('CURRENT_TIMESTAMP()');
		$insert_id = $this->_dbTable->insert($data);
		if (!$insert_id){
			throw new Application_Model_Controller_Exception(''
				, Application_Model_Controller_Exception::E_DB_NOT_UPDATED);
		}
		$rtn = array(
			'result'	=> 1
		);
		echo json_encode($rtn);
	}
	
}

