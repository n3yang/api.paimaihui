<?php

class Application_Model_Link extends Application_Model_Base
{	
	
	public function __construct()
	{
		$this->dbTable = new Application_Model_DbTable_Link();
	}
	
	public function get()
	{
		$where = $this->dbTable->getAdapter()->quoteInto('is_published=?', Application_Model_DbTable_Link::IS_PUBLISHED_YES);
		$links = $this->dbTable->fetchAll($where)->toArray();
		foreach ($links as $l) {
			$subIds[] = $l['sub_id'];
		}
		$mSub = new Application_Model_SubActivity();
		$subs = $mSub->getByIds($subIds);
		$mPhoto = new Application_Model_Photo();
		$mAntique = new Application_Model_Antique();
		//
		foreach ($links as $k=>&$link) {
			foreach ($subs as $sk=>&$sub) {
				if ($link['sub_id']==$sub['id']){
					// 如果不为空则使用name作为首页显示名，为空直接使用对应专场自己的名字
					if (empty($link['name'])) {
						$link['name'] = $sub['label'];
					}
					// 如果存在则作为此专场的缩略图显示于首页，如果不存在则使用专场自己的封面(sub.cover)
					if (empty($link['image']) && $sub['cover']) {
						$link['image'] = $mPhoto->getPhotoUrl($sub['cover'], 160);
					}
					// 如果专场封面跟image都不存在，则此处使用本专场的这个lot号的图片作为首页的专场缩略图
					if (empty($link['image'])) {
						$photo = $mPhoto->getBySubIdLot($link['sub_id'], $link['lot']);
						$link['image'] = $photo[0]['url_thumb_160'];
					}
				}
			}
		}
		return $links;
	}
	
}