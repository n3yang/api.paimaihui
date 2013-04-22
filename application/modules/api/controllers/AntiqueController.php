<?php

class Api_AntiqueController extends Zend_Controller_Action
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
	}

	/**
	 * 获取列表
	 * 
	 * $offset
	 * $count
	 * $activity_id
	 * $sub_activity_id
	 * $category_id
	 */
	public function indexAction()
	{
		$offset = $this->getRequest()->getParam('offset', 0);
		$count = $this->getRequest()->getParam('count', 20);
		$keyword = $this->getRequest()->getParam('keyword');
		$companyId = $this->getRequest()->getParam('company_id');
		$acitvityId = $this->getRequest()->getParam('activity_id');
		$subId = $this->getRequest()->getParam('sub_id');
		// not used
		$isPublished = $this->getRequest()->getParam('is_published');
		$isCompleted = $this->getRequest()->getParam('is_completed');
		
		$mAntique = new Application_Model_Antique();
		
		$condition = array(
			'keyword'	=> $keyword,
			'company_id'	=> $companyId,
			'activity_id'	=> $activityId,
			'sub_id'		=> $subId,
		);
		$mAntique->setWithCompany()->setWithPhoto();
		$rs = $mAntique->getSearch($condition, $count, $offset);
		echo json_encode($rs);
	}

	/**
	 * 
	 * 
	 * @throws Api_Model_Exception
	 */
	public function showAction()
	{
		$id = $this->getRequest()->getParam('id');
		
		if (!$id) {
			throw new Api_Model_Exception(''
				, Api_Model_Exception::E_PARAM_REQUIRED);
		}
		$ids = explode(',', $id);
		$mAntique = new Application_Model_Antique();
		$mAntique->setWithActivity()
			// ->setWithCompany()
			// ->setWithSubActivity()
			->setWithPhoto();
		foreach ($ids as $id) {
			$rs[] = $mAntique->getOneById($id);
		}
		echo json_encode($rs);
	}
	
	/**
	 * 
	 * 
	 * @throws Api_Model_Exception
	 */
	public function destroyAction() 
	{
		$id = $this->getRequest()->getParam('id');
		$table = $this->_dbTable;
		if ($id) {
			$condition = $table->getAdapter()->quoteInto('id=?', $id);
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
			throw new Api_Model_Exception(''
				, Api_Model_Exception::E_PARAM_REQUIRED);
		}
		
		// 检测不能为空的字段
		foreach ($noEmptyFields as $v) {
			if ( !isset($data[$v]) || $data[$v]==='') {
				throw new Api_Model_Exception('param required: ' . $v
					, Api_Model_Exception::E_PARAM_REQUIRED);
			}
		}
		
		// 检测activity_id是否存在
//		$dbTableCompany = new Application_Model_DbTable_Company();
//		if (!$dbTableCompany->isExistedId($data['activity_id'])){
//			throw new Api_Model_Exception('param faild: activity_id'
//				, Api_Model_Exception::E_PARAM_REQUIRED);
//		}
		
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
				throw new Api_Model_Exception('param required: ' . $v
					, Api_Model_Exception::E_PARAM_REQUIRED);
			}
		}
		
		// 检测activity_id是否存在
//		$dbTableCompany = new Application_Model_DbTable_Company();
//		if (!$dbTableCompany->isExistedId($data['activity_id'])){
//			throw new Api_Model_Exception('param faild: activity_id'
//				, Api_Model_Exception::E_PARAM_REQUIRED);
//		}
		
		// insert to db;
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
	
}

