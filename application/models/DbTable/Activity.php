<?php

class Application_Model_DbTable_Activity extends Zend_Db_Table_Abstract
{

    protected $_name = 'activity';

	const IS_PUBLISHED_YES = 1;
	const IS_PUBLISHED_NO = 0;
	
	const IS_COMPLETED_YES = 1;
	const IS_COMPLETED_NO = 0;
	
	/**
	 * 检测ID是否存在
	 * 
	 * @param int $id
	 */
	public function isExistedId($id)
	{
		return $this->find(intval($id))->count() > 0 ? TRUE : FALSE ;
	}
	
	/**
	 * 检测slug是否存在
	 * 
	 * @param string $slug
	 */
	public function isExistedSlug($slug)
	{
		$where = $this->getAdapter()->quoteInto('slug=?', strval($slug));
		return $this->fetchAll($where)->count() > 0 ? TRUE : FALSE ;
	}
}

