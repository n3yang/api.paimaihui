<?php

class Application_Model_DbTable_SubActivity extends Zend_Db_Table_Abstract
{

    protected $_name = 'sub';

	/**
	 * 检测ID是否存在
	 * 
	 * @param int $id
	 */
	public function isExistedId($id)
	{
		return $this->find(intval($id))->count() > 0 ? TRUE : FALSE ;
	}
	
}

