<?php

class AntiqueController extends Zend_Controller_Action
{
	/**
	 * Application_Model_DbTable_Antique
	 * 
	 * @var Application_Model_DbTable_Antique
	 */
	protected $_dbTable = NULL;

	public function init()
	{
		$this->_helper->viewRenderer->setNoRender(true);
		$this->_dbTable = new Application_Model_DbTable_Antique();
	}

	/**
	 * 获取列表
	 * 
	 * $offset
	 * $count
	 * $activity_id
	 * $sub_activity_id
	 * $category_id
	 * $is_published
	 * $is_completed
	 */
	public function indexAction()
	{
		$offset = $this->getRequest()->getParam('offset', 0);
		$count = $this->getRequest()->getParam('count', 20);
		$activity_id = $this->getRequest()->getParam('activity_id');
		$sub_activity_id = $this->getRequest()->getParam('sub_activity_id');
		$category_id = $this->getRequest()->getParam('category_id');
		$is_published = $this->getRequest()->getParam('is_published');
		$is_completed = $this->getRequest()->getParam('is_completed');
		
		$table = $this->_dbTable;
		$where = '1';
		if ($activity_id !== '') {
			$where .= ' AND ' . $table->getAdapter()->quoteInto('activity_id', $activity_id);
		}
		if ($sub_activity_id !== '') {
			$where .= ' AND ' . $table->getAdapter()->quoteInto('sub_activity_id', $sub_activity_id);
		}
		if ($category_id !== '') {
			$where .= ' AND ' . $table->getAdapter()->quoteInto('category_id', $category_id);
		}
		if ($is_published !== '') {
			$where .= ' AND ' . $table->getAdapter()->quoteInto('is_published', $is_published);
		}
		if ($is_completed !== '') {
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
			'antique'	=> $data->toArray(),
			'total'		=> $total['total']
		));
		echo $rtn;
	}

	/**
	 * 
	 * 
	 * @throws Application_Model_Controller_Exception
	 */
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
	
	/**
	 * 
	 * 
	 * @throws Application_Model_Controller_Exception
	 */
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
		
		// 不允许为空的字段
		$noEmptyFields = array('lot');
		
		// 允许录入的字段
		$allowFields = array('lot', 'activity_id', 'sub_activity_id', 'category_id', 'condition_report', 'name', 'status', 
			'period', 'author', 'size', 'size_desc', 'appraisal_price', 'price_low', 'price_high', 'deal_price',
			'weight', 'texture', 'signature', 'brief', 'description', 'provenance', 'literature', 'exhibition',
			'publish', 'not2export', 'artist_biographies', 'signature_stamp', 'record', 'name2', 'period2',
			'author2', 'thumbnail', 'collection', 'inscription', 'documentary', 'special_bidding');
		
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
		
		// 检测不能为空的字段
		foreach ($noEmptyFields as $v) {
			if ( !isset($data[$v]) || $data[$v]==='') {
				throw new Application_Model_Controller_Exception('param required: ' . $v
					, Application_Model_Controller_Exception::E_PARAM_REQUIRED);
			}
		}
		
		// 检测activity_id是否存在
//		$dbTableCompany = new Application_Model_DbTable_Company();
//		if (!$dbTableCompany->isExistedId($data['activity_id'])){
//			throw new Application_Model_Controller_Exception('param faild: activity_id'
//				, Application_Model_Controller_Exception::E_PARAM_REQUIRED);
//		}
		
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
		$noEmptyFields = array('lot');
		
		// 允许录入的字段
		$allowFields = array('lot', 'activity_id', 'sub_activity_id', 'category_id', 'condition_report', 'name', 'status', 
			'period', 'author', 'size', 'size_desc', 'appraisal_price', 'price_low', 'price_high', 'deal_price',
			'weight', 'texture', 'signature', 'brief', 'description', 'provenance', 'literature', 'exhibition',
			'publish', 'not2export', 'artist_biographies', 'signature_stamp', 'record', 'name2', 'period2',
			'author2', 'thumbnail', 'collection', 'inscription', 'documentary', 'special_bidding');
		
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
			if ( !isset($data[$v]) || $data[$v]==='') {
				throw new Application_Model_Controller_Exception('param required: ' . $v
					, Application_Model_Controller_Exception::E_PARAM_REQUIRED);
			}
		}
		
		// 检测activity_id是否存在
//		$dbTableCompany = new Application_Model_DbTable_Company();
//		if (!$dbTableCompany->isExistedId($data['activity_id'])){
//			throw new Application_Model_Controller_Exception('param faild: activity_id'
//				, Application_Model_Controller_Exception::E_PARAM_REQUIRED);
//		}
		
		// insert to db;
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

