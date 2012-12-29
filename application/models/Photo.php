<?php

class Application_Model_Photo extends Application_Model_Base
{	
	protected $dbTable = '';
	protected $domainImage = array();
	
	public function __construct()
	{
		$this->dbTable = new Application_Model_DbTable_Photo();
	}
	
	public function getDbTable()
	{
		return $this->dbTable;
	}
	
	public function getByAntiqueIds($antiqueIds)
	{
		$where = $this->dbTable->getAdapter()->quoteInto('antique_id in (?)', $antiqueIds);
		$photoes = $this->dbTable->fetchAll($where)->toArray();
		foreach ($photoes as &$photo) {
			$photo['url'] = $this->getPhotoUrl($photo['photo']);
		}
		return $photoes;
	}
	
	
	public function getPhotoUrl($path, $widht='', $height='')
	{
		if (!$path) {
			return false;
		}
		
		if (empty($this->domainImage)) {
			$bootstrap = Zend_Controller_Front::getInstance()->getParam('bootstrap');
			$options = $bootstrap->getOptions();
			$this->domainImage = $options['domains']['image'];
		}
		$domains = $this->domainImage;
		$seed = substr(md5($path), 0, 1);
		$seed = hexdec($seed);
		$index = $seed % count($domains);
		$url = 'http://' . $domains[$index];
		$url .= strstr($path, '/')===0 ? $path : '/'.$path;
		return $url;
	}

}

