<?php

class Application_Model_Base
{
	protected $dbTable = '';
	
	public function __construct()
	{
		
	}
	
	public function getDbTable()
	{
		return $this->dbTable;
	}
}