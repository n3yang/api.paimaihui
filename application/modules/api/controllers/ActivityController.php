<?php

class Api_ActivityController extends Zend_Controller_Action
{
	/**
	 * Application_Model_DbTable_Activity
	 * 
	 * @var Application_Model_DbTable_Activity
	 */
	protected $_dbTable = NULL;

	public function init()
	{
		$this->_helper->viewRenderer->setNoRender(true);
		$this->_dbTable = new Application_Model_DbTable_Activity();
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
		$companyId = $this->getRequest()->getParam('company_id');
		// not used
		$isPublished = $this->getRequest()->getParam('is_published');
		$isCompleted = $this->getRequest()->getParam('is_completed');

		$mActivity = new Application_Model_Activity();
		$rs = $mActivity->getSearch(array('company_id'=>$companyId), $count, $offset);
		
		echo json_encode($rs);
	}

	public function showAction()
	{
		$id = $this->getRequest()->getParam('id');

		if (!$id) {
			throw new Api_Model_Exception(''
				, Api_Model_Exception::E_PARAM_REQUIRED);
		}
		$ids = explode(',', $id);
		$mActivity = new Application_Model_Activity();
		$rs = $mActivity->getByIds($ids);
		echo json_encode($rs);
	}
	/*
	public function destroyAction() 
	{
		$id = $this->getRequest()->getParam('id');
		$slug = $this->getRequest()->getParam('slug');
		$table = $this->_dbTable;
		if ($id) {
			$condition = $table->getAdapter()->quoteInto('id=?', $id);
		} else if ($slug) {
			$condition = $table->getAdapter()->quoteInto('slug=?', $slug);
		} else {
			throw new Api_Model_Exception(''
				, Api_Model_Exception::E_PARAM_REQUIRED);
		}
		
		$rs = $table->delete($condition);
		if (!$rs) {
			throw new Api_Model_Exception(''
				, Api_Model_Exception::E_DB_NOT_UPDATED);
		}
		
		echo json_encode(array('result'=>1));
	}
	
	public function updateAction()
	{
		$id = $this->getRequest()->getParam('id');
		if (!$id) {
			throw new Api_Model_Exception('param required: id'
				, Api_Model_Exception::E_PARAM_REQUIRED);	
		}
		
		$allowFields = array('company_id', 'slug', 'name', 'event_date', 'rate_usd', 'amount'
			, 'is_published', 'description', 'is_completed', 'address', 'rate_fee', 'dealed_rate');
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
			throw new Api_Model_Exception(''
				, Api_Model_Exception::E_PARAM_REQUIRED);
		}
		
		// 检测slug是否已经存在
		if ( isset($data['slug']) && $this->_dbTable->isExistedSlug($data['slug']) ){
			throw new Api_Model_Exception(''
				, Api_Model_Exception::E_PARAM_DUPLICATE_KEY);
		}
		
		// 检测company_id是否存在
		$dbTableCompany = new Application_Model_DbTable_Company();
		if (!$dbTableCompany->isExistedId($data['company_id'])){
			throw new Api_Model_Exception('param faild: company_id'
				, Api_Model_Exception::E_PARAM_REQUIRED);
		}
		
		// 更新数据
		$where = $this->_dbTable->getAdapter()->quoteInto('id=?', $id);
		$rs = $this->_dbTable->update($data, $where);
		if (!$rs) {
			throw new Api_Model_Exception(''
				, Api_Model_Exception::E_DB_NOT_UPDATED);
		}
		
		echo json_encode(array('result'=>1));
	}
	
	
	public function createAction()
	{
		// 不允许为空的字段
		$noEmptyFields = array('company_id', 'slug', 'name', 'event_date', 'is_published', 'is_completed');
		
		// 允许录入的字段
		$allowFields = array('company_id', 'slug', 'name', 'event_date', 'rate_usd', 'amount'
			, 'is_published', 'description', 'is_completed', 'address', 'rate_fee', 'dealed_rate');
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
			if ( !isset($data[$v]) || $data[$v]=='') {
				throw new Api_Model_Exception('param required: ' . $v
					, Api_Model_Exception::E_PARAM_REQUIRED);
			}
		}

		// 检测slug是否已经存在
		if ($this->_dbTable->isExistedSlug($data['slug'])){
			throw new Api_Model_Exception(''
				, Api_Model_Exception::E_PARAM_DUPLICATE_KEY);
		}
		
		// 检测company_id是否存在
		$dbTableCompany = new Application_Model_DbTable_Company();
		if (!$dbTableCompany->isExistedId($data['company_id'])){
			throw new Api_Model_Exception('param faild: company_id'
				, Api_Model_Exception::E_PARAM_REQUIRED);
		}
		
		// insert to db;
		$data['created'] = new Zend_Db_Expr('CURRENT_TIMESTAMP()');
		$insert_id = $this->_dbTable->insert($data);
		if (!$insert_id){
			throw new Api_Model_Exception(''
				, Api_Model_Exception::E_DB_NOT_UPDATED);
		}
		$rtn = array(
			'result'	=> 1
		);
		echo json_encode($rtn);
	}
	*/
}

