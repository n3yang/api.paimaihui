<?php

ini_set('memory_limit', '256M');

set_include_path(implode(PATH_SEPARATOR, array(
    realpath(realpath(__DIR__).'/../library'),
    get_include_path(),
)));

require_once 'Zend/Db.php';

$config_pg = array(
	'host'	=> 'localhost',
	'username'	=> 'Sair',
	'password'	=> '',
	'dbname'	=> 'auction',
	'charset'	=> 'utf8'
);
$config_my = array(
	'host'	=> 'localhost',
	'username'	=> 'auction',
	'password'	=> 'auction',
	'dbname'	=> 'auction',
	'charset'	=> 'utf8',
);
$pg = zend_db::factory('Pdo_Pgsql', $config_pg);
$my = zend_db::factory('Pdo_Mysql', $config_my);

// 导入activity
//$rs = $pg->fetchAll('select * from activity');
//foreach ($rs as $k=>&$v){
//	unset($v['user_account_id']);
//	$v['is_published'] = $v['is_published']=='f' ? 0 : 1;
//	$v['is_completed'] = $v['is_completed']=='f' ? 0 : 1;
//	$my->insert('activity', $v);
//}
//unset($v);

//$rs = $pg->fetchAll('select * from sub_activity');
//foreach ($rs as $k=>&$v){
//	unset($v['user_account_id']);
//	$v['is_published'] = $v['is_published']=='f' ? 0 : 1;
//	$v['is_completed'] = $v['is_completed']=='f' ? 0 : 1;
//	$my->insert('sub_activity', $v);
//}
//unset($v);

$rs = $pg->fetchAll($pg->select()->from('antique')->limit(10000));
foreach ($rs as $k=>&$v){
	$v['lot'] = $v['number'];
	unset($v['number']);
	unset($v['prefix']);
	if ($v['withdrawn']=='t') {
		$v['status'] = 3;
	}
	unset($v['withdrawn']);
	unset($v['fulllot']);
	$my->insert('antique', $v);
}
unset($v);
