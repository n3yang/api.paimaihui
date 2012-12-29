<?php

class Application_Model_Antique extends Application_Model_Base
{
	
	/**
	 * get search result with company
	 * 
	 * @var bool
	 */
	protected $withCompany = FALSE;
	/**
	 * get search result with activity
	 * 
	 * @var bool
	 */
	protected $withActivity = FALSE;
	/**
	 * get search result with sub-activity
	 * 
	 * @var bool
	 */
	protected $withSubActivity = FALSE;
	/**
	 * get search result with photo
	 * 
	 * @var bool
	 */
	protected $withPhoto = FALSE;
	
	public function __construct()
	{
		$this->dbTable = new Application_Model_DbTable_Antique();
	}
	
	/**
	 * 查询搜索结果
	 * 
	 * @param array $condition
	 * @param int $number
	 * @param int $offset
	 * @param string $sort
	 */
	public function getSearch($condition, $number, $offset=0, $sort='')
	{
		$table = &$this->dbTable;
		// build condition
		$kw = $condition['keyword'];
		$subId = $condition['sub_id'];
		$where = '1=1';
		if ($subId !== null) {
			$where .= ' AND ' . $table->getAdapter()->quoteInto('sub_id=?', $subId);
		}
		if ($kw !== null ) {
			$where .= ' AND name like ' . $table->getAdapter()->quote('%'.$kw.'%');
		}
		
		switch ($sort) {
			case 'priceup':
				$order = 'price';
				break;
			case 'pricedown': 
				$order = 'price desc';
				break;
				
			default:
				$order = 'id desc';
				break;
		}
		
		$antiques = $table->fetchAll(
			$table->select()
				->from($table, '*')
				->where($where)
				->limit($number, $offset)
				->order($order)
			)->toArray();
		
		if (empty($antiques)) {
			$total = 0;
		} else {
			$rs = $table->fetchRow($table->select()->from($table, 'count(*) as total')->where($where))->toArray();
			$total = $rs['total'];
			foreach ($antiques as $v) {
				$antiqueIds[] = $v['id'];
				$activityIds[] = $v['activity_id'];
				$subIds = $v['sub_id'];
			}
			// get activity info 
			if ($this->withActivity || $this->withCompany) {
				$tableActivity = new Application_Model_DbTable_Activity();
				$activityInfo = $tableActivity->find($activityIds)->toArray();
				foreach ($activityInfo as $k=>$v) {
					$companyIds[] = $v['company_id'];
				}
			}
			// get company info
			if ($this->withCompany) {
				$tableCompany = new Application_Model_DbTable_Company();
				$companyInfo = $tableCompany->find($companyIds)->toArray();
				foreach ($activityInfo as $k => $v) {
					foreach ($companyInfo as $ck=>$cv) {
						if ($v['company_id']==$cv['id']) {
							$activityInfo[$k]['company'] = $cv;
						}
					}
				}
			}
			// get sub-activity info
			if ($this->withSubActivity) {
				$tableSubActivity = new Application_Model_DbTable_SubActivity();
				$subInfo = $tableSubActivity->find($subIds)->toArray();
			}
			
			// get photo info
			if ($this->withPhoto) {
				$photoModel = new Application_Model_Photo();
				$photoes = $photoModel->getByAntiqueIds($antiqueIds);
			}
			
			// format antique
			
			foreach ($antiques as $k=>$v) {
				
				foreach ($activityInfo as $ak=>$av) {
					if ($v['activity_id'] == $av['id']) {
						$antiques[$k]['activity'] = $av;
					}
				}
				
				foreach ($photoes as $pk=>$pv) {
					if ($v['id'] == $pv['antique_id']) {
						$antiques[$k]['photo'] = $pv;
					}
				}
				
				foreach ($subInfo as $sk=>$sv) {
					if ($v['sub_id']==$sv['id']){
						$antiques[$k]['sub'] = $sv;
					}
				}
			}
			
		}
		
		return array(
			'data'	=> $antiques,
			'total'	=> $total
		);
	}
	
	public function getOneById($id)
	{
		if (empty($id)) {
			return array();
		}
		
		$table = new Application_Model_DbTable_Antique();
		$antique = $table->find(intval($id))->toArray();
		$antique = $antique[0];
		if (!empty($antique)) {
			if ($this->withActivity) {
				$tableActivity = new Application_Model_DbTable_Activity();
				$activity = $tableActivity->find($antique['activity_id'])->toArray();
				$antique['activity'] = $activity[0];
			}
			if ($this->withSubActivity) {
				$tableSubActivity = new Application_Model_DbTable_SubActivity();
				$subActivity = $tableSubActivity->find($antique['sub_activity_id'])->toArray();
				$antique['sub_activity'] = $subActivity[0];
			}
			if ($this->withCompany) {
				$tableCompany = new Application_Model_DbTable_Company();
				$company = $tableCompany->find($antique['activity']['company_id'])->toArray();
				$antique['company'] = $company[0];
			}
			if ($this->withPhoto) {
				$modelPhoto = new Application_Model_Photo();
				$photo = $modelPhoto->getByAntiqueIds($id);
				$antique['photo'] = $photo;
			}
		}
		return $antique;
	}
	
	
	/**
	 * @param bool $withCompany
	 */
	public function setWithCompany($withCompany=TRUE)
	{
		$this->withCompany = $withCompany ? TRUE : FALSE;
		return $this;
	}

	/**
	 * @param bool $withActivity
	 */
	public function setWithActivity($withActivity=TRUE)
	{
		$this->withActivity = $withActivity ? TRUE : FALSE;
		return $this;
	}

	/**
	 * @param bool $withSubActivity
	 */
	public function setWithSubActivity($withSubActivity=TRUE)
	{
		$this->withSubActivity = $withSubActivity ? TRUE : FALSE;
		return $this;
	}
	
	/**
	 * @param bool $withPhoto
	 */
	public function setWithPhoto($withPhoto=TRUE)
	{
		$this->withPhoto = $withPhoto ? TRUE : FALSE;
		return $this;
	}


	
}
