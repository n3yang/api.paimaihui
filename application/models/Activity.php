<?php

class Application_Model_Activity extends Application_Model_Base
{	
	
	public function __construct()
	{
		$this->dbTable = new Application_Model_DbTable_Activity();
	}

	public function getByIds($ids)
	{
		$dbTable = &$this->dbTable;
		//$where = $dbTable->getAdapter()->quoteInto('is_published=?', Application_Model_DbTable_Activity::IS_PUBLISHED_YES);
		return $this->dbTable->find($ids)->toArray();
	}

	public function getOneById($id)
	{
		$rs = $this->dbTable->find($id)->toArray();
		return $rs[0];
	}
	

	public function getSearch($condition, $number, $offset=0)
	{
		$companyId = $condition['company_id'];
		$dbTable = &$this->dbTable;
		$where = '1=1';
		if ($companyId) {
			$where .= ' AND ' . $dbTable->getAdapter()->quoteInto('company_id = ?', $companyId);
		}
		$activity = $dbTable->fetchAll(
			$dbTable->select()
				->from($dbTable, '*')
				->where($where)
				->limit($number, $offset)
			)->toArray();
		if (empty($activity)) {
			$total = 0;
		} else {
			$rs = $dbTable->fetchRow(
				$dbTable->select()->from($dbTable, 'count(*) as total')->where($where)
				)->toArray();
			$total = $rs['total'];
		}
		return array(
				'data'	=> $activity,
				'total'	=> $total,
		);
	}
}