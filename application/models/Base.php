<?php

class Application_Model_Base
{
	/**
	 * 
	 * Enter description here ...
	 * @var Zend_Db_Table
	 */
	protected $dbTable = '';
	
	public function __construct()
	{
		
	}
	
	public function getDbTable()
	{
		return $this->dbTable;
	}
}