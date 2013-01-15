<?php

class Application_Model_SubActivity extends Application_Model_Base
{	
	
	public function __construct()
	{
		$this->dbTable = new Application_Model_DbTable_SubActivity();
	}
	
	public function getByIds($ids)
	{
		$dbTable = &$this->dbTable;
		//$where = $dbTable->getAdapter()->quoteInto('is_published=?', Application_Model_DbTable_SubActivity::IS_PUBLISHED_YES);
		return $this->dbTable->find($ids)->toArray();
	}
	
}