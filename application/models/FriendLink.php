<?php

class Application_Model_FriendLink extends Application_Model_Base
{

	public function __construct()
	{
		$this->dbTable = new Application_Model_DbTable_FriendLink();
	}

	public function getAll()
	{
		return $this->dbTable->fetchAll()->toArray();
	}
}

