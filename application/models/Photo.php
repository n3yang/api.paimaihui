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
			$photo['url_thumb_160'] = $this->getPhotoUrl($photo['photo'], 160);
			$photo['url_thumb_345'] = $this->getPhotoUrl($photo['photo'], 345);
		}
		return $photoes;
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
		$seed = hexdec(substr(md5($path), 0, 1));
		$index = $seed % count($domains);
		$url = 'http://' . $domains[$index];
		$url .= strstr($path, '/')===0 ? $path : '/'.$path;
		return $url;
	}
	
}

