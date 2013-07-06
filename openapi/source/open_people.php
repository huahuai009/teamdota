<?php
/*
	[TEAMDOTA] (C) 2012 wanghui
	$Id: open_people.php 2012-07-25 09:59Z duty $
*/
if(!defined('IN_TEAMDOTA')) {
	open_showmessage($_SGLOBAL['open_errorinfo'][1001]);
}

open_checklogin();

open_checkgroup();

$project_id = empty($_POST['projectid']) ? 0 : intval($_POST['projectid']);
if(empty($project_id)){
	open_showmessage($_SGLOBAL['open_errorinfo'][1001]);
}
$manageproject = open_checkproject($project_id);
if(!$manageproject) {
	open_showmessage($_SGLOBAL['open_errorinfo'][1001]);
}
if($manageproject['status'] == 2) {//处于删除状态
	open_showmessage($_SGLOBAL['open_errorinfo'][1001]);
}

if($a == 'isactive_people_list') {//获取主题列表，获取前50条
	$query = $_SGLOBAL['db']->query("SELECT m.fullname,m.uid FROM ".tname('project_member')." pm,".tname('member')." m WHERE pm.project_id='{$project_id}' AND m.isactive=0 AND pm.uid=m.uid  ORDER BY pm.id ASC");
	$listuser = array();
	while($value = $_SGLOBAL['db']->fetch_array($query)) {
		$listuser[] = array('uid'=>$value['uid'] , 'nickname'=>$value['fullname']);
	}
	open_showmessage($_SGLOBAL['open_errorinfo'][0],'', $listuser);
}