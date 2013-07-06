<?php
/*
	[TEAMDOTA] (C) 2012 wanghui
	$Id: cp_people_settings.php 2012-03-31 09:59Z duty $
*/

if(!defined('IN_TEAMDOTA')) {
	exit('Access Denied');
}

if($_GET['op'] == 'edit') {
	$editvalue = empty($_GET['editvalue']) ? 0 : intval($_GET['editvalue']);
	if($editvalue == 0) {
		updatetable('member', array('issubscribe' => 0), array('uid'=>$_SGLOBAL['supe_uid']));
	} else {
		updatetable('member', array('issubscribe' => 1), array('uid'=>$_SGLOBAL['supe_uid']));
	}
	showmessage('do_success','cp.php?ac=people_settings');
}
include_once template("cp_people_settings");

?>