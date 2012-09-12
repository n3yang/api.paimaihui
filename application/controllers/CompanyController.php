<?php

class CompanyController extends Zend_Controller_Action
{

	public function init()
	{
		$this->_helper->viewRenderer->setNoRender(true);
	}

	public function indexAction()
	{
		$table = new Application_Model_DbTable_Activity();
		$data = $table->fetchAll()->toArray();
		echo json_encode(array(
			'company'	=> $data,
			'total'		=> count($data),
		));
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
			throw new Application_Model_Controller_Exception(''
				, Application_Model_Controller_Exception::E_PARAM_REQUIRED);	
		}
		
		$data = array();
		$slug = $this->getRequest()->getParam('slug');
		if ($slug){
			$data['slug'] = $slug;
		}
		$name = $this->getRequest()->getParam('name');
		if ($name) {
			$data['name'] = $name;
		}
		$is_published = $this->getRequest()->getParam('is_published');
		if ($is_published) {
			$data['is_published'] = $is_published;
		}
		if (empty($data)) {
			throw new Application_Model_Controller_Exception(''
				, Application_Model_Controller_Exception::E_PARAM_REQUIRED);
		}
		
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

