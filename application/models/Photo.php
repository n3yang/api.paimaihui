<?php

class Application_Model_Photo extends Application_Model_Base
{	
	protected $dbTable = '';
	protected $domainImage = array();
	protected $mAntique = '';

	public function __construct()
	{
		$this->dbTable = new Application_Model_DbTable_Photo();
	}
	
	public function getDbTable()
	{
		return $this->dbTable;
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param array photo
	 */
	public function getByAntiqueIds($antiqueIds)
	{
		$where = $this->dbTable->getAdapter()->quoteInto('antique_id in (?)', $antiqueIds);
		$photoes = $this->dbTable->fetchAll($where)->toArray();
		foreach ($photoes as &$photo) {
			$photo['url'] = $this->getPhotoUrl($photo['photo']);
			$photo['url_thumb_160'] = $this->getPhotoUrl($photo['photo'], 160);
			$photo['url_thumb_345'] = $this->getPhotoUrl($photo['photo'], 345);
		}
		return $photoes;
	}
	
	/**
	 * 根据专场ID与LOT查找照片
	 * 
	 * @param int $subId
	 * @param string $lot
	 * @return array
	 */
	public function getBySubIdLot($subId, $lot)
	{
		if (empty($subId) || empty($lot)) {
			return array();
		}
		if (!$this->mAntique) {
			$this->mAntique = new Application_Model_Antique();
		}
		$antique = $this->mAntique->getOneBySubIdLot($subId, $lot);
		if (empty($antique['id'])) {
			return array();
		}
		return $this->getByAntiqueIds($antique['id']);
	}
	
	public function getPhotoUrl($path, $width='')
	{
		if (!$path) {
			return false;
		}
	
		// is thumb ?
		if (!empty($width)) {
			$path = preg_replace('/(.*)\.(jpg|gif|png)$/i', '$1-thumb-'.$width.'.$2', $path);
			if (empty($path)) {
				return FALSE;
			}
		}
		if (empty($this->domainImage)) {
			$bootstrap = Zend_Controller_Front::getInstance()->getParam('bootstrap');
			$options = $bootstrap->getOptions();
			$this->domainImage = $options['domains']['image'];
		}
		$domains = $this->domainImage;
		$index = crc32($path) % count($domains);
		$url = 'http://' . $domains[$index];
		$url .= strstr($path, '/')===0 ? $path : '/'.$path;
		return $url;
	}
	
}

