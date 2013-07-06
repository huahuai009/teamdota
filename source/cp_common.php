<?php
/*
	[TEAMDOTA] (C) 2012 wanghui
	$Id: cp_common.php 2012-03-31 09:59Z duty $
*/

if(!defined('IN_TEAMDOTA')) {
	exit('Access Denied');
}

$op = empty($_GET['op'])?'':trim($_GET['op']);

if($op == 'logout') {
	if($_GET['uhash'] == $_SGLOBAL['uhash']) {
		//删除session
		if($_SGLOBAL['supe_uid']) {
			$_SGLOBAL['db']->query("DELETE FROM ".tname('session')." WHERE uid='$_SGLOBAL[supe_uid]'");
		}
		clearcookie();
		ssetcookie('_refer', '');
	}
	showmessage('security_exit', 'index.php', 0);
}
include template('cp_common');

?>
