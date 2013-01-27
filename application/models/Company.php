<?php

class Application_Model_Company extends Application_Model_Base
{
	
	public function __construct()
	{
		$this->dbTable = new Application_Model_DbTable_Company();
	}
	
	public function getByIds($ids)
	{
		$dbTable = &$this->dbTable;
		//$where = $dbTable->getAdapter()->quoteInto('is_published=?', Application_Model_DbTable_Company:IS_PUBLISHED_YES);
		return $this->dbTable->find($ids)->toArray();
	}
	
	public function getOneById($id)
	{
		$rs = $this->dbTable->find($id)->toArray();
		return $rs[0];
	}

	public function getOneBySlug($slug)
	{
		$rs = $this->dbTable->findBySlug($slug)->toArray();
		return $rs[0];
	}
}