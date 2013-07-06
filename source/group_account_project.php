<?php
/*
	[TEAMDOTA] (C) 2012 wanghui
	$Id: group_account_project.php 2012-03-31 09:59Z duty $
*/

if(!defined('IN_TEAMDOTA')) {
	exit('Access Denied');
}
if($_SGLOBAL['member']['ntype'] <= 0) {
	showmessage('no_privilege');
}
//读取帐户所有活跃项目
$query = $_SGLOBAL['db']->query("SELECT project_id,name,author,logtime FROM ".tname('project')." WHERE group_id='{$groupid}' AND status=0");
$list_project = array();
while($value = $_SGLOBAL['db']->fetch_array($query)) {
	$list_project[] = $value;
}
include_once template("group_account_project");
?>