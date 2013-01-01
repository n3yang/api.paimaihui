<?php

class Application_Model_DbTable_Antique extends Zend_Db_Table_Abstract
{

    protected $_name = 'antique';

	/**
	 * 根据subActivityId标记查询记录
	 * @deprecated
	 * @param int $subActivityId
	 * @param int $count
	 * @param int $offset
	 * @return Zend_Db_Table_Rowset_Abstract
	 */
	public function findBySubActivityId($subActivityId, $count=20, $offset=0, $order='')
	{
		$where = $this->getAdapter()->quoteInto('sub_id=?', $subActivityId);
		
		$antiques = $this->fetchAll(
			$this->select()
				->from($this, '*')
				->where($where)
				->limit($count, $offset)
				->order($order)
			)->toArray();
		
		return $antiques;
	}

}

