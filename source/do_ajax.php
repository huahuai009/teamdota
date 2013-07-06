<?php
/*
	[TEAMDOTA] (C) 2012 wanghui
	$Id: do.php 2012-03-31 09:59Z duty $
*/

if(!defined('IN_TEAMDOTA')) {
	exit('Access Denied');
}

$op = empty($_GET['op'])?'':$_GET['op'];

if($op == 'emailcheck') {
	//默认为0，
	//-1 填写的 Email 格式有误
	//-2 填写的 Email 已存在
	//1 该邮箱没人注册过
	$result = 0;
	$emailaddress = trim($_GET['email']);
	if(empty($emailaddress)){
		$result = -1;
	} elseif(!isemail($emailaddress)) { 
		$result = -1;
	} else {
		$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('member')." WHERE `email`='{$emailaddress}' LIMIT 1");
		if(!$member = $_SGLOBAL['db']->fetch_array($query)) {
			$result = 1;
		} else{
			$result = -2;
		}
	}
	echo $result;
	exit();
}
?>