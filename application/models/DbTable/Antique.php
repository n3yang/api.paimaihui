<?php

class Application_Model_DbTable_Antique extends Zend_Db_Table_Abstract
{

    protected $_name = 'antique';

	/**
	 * 根据subActivityId标记查询记录
	 * 
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
		
		if (!empty($antiques)) {
			// get photoes
			foreach ($antiques as $v) {
				$ids[] = $v['id'];
			}
			$photoTable = new Application_Model_DbTable_Photo();
			$where = $photoTable->getAdapter()->quoteInto('antique_id in (?)', $ids);
			$photoes = $photoTable->fetchAll($where)->toArray();
			// format antique
			foreach ($antiques as $k=>$v) {
				foreach ($photoes as $pk=>$pv) {
					if ($v['id'] == $pv['antique_id']) {
						$antiques[$k]['photo'] = $pv;
					}
				}
			}
		}
		
		return $antiques;
	}
	
	public function countBySubAcitivityId($subActivityId)
	{
		$where = $this->getAdapter()->quoteInto('sub_id=?', $subActivityId);
		$rs = $this->fetchRow($this->select()->from($this, 'count(*) as total')->where($where))->toArray();
		return $rs['total'];
	}
	
}

