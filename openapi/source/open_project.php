<?php
/*
	[TEAMDOTA] (C) 2012 wanghui
	$Id: open_project.php 2012-07-24 09:59Z duty $
*/
if(!defined('IN_TEAMDOTA')) {
	open_showmessage($_SGLOBAL['open_errorinfo'][1001]);
}

open_checklogin();

open_checkgroup();

//读取活跃项目
$query = $_SGLOBAL['db']->query("SELECT p.*, pm.type FROM ".tname('project')." p , ".tname('project_member')." pm WHERE p.project_id=pm.project_id AND p.group_id='{$group[group_id]}' AND pm.uid='".$_SGLOBAL['member']['uid']."' AND p.status=0");
$projects = array();//活跃项目
while($value = $_SGLOBAL['db']->fetch_array($query)) {
	$projects[] = array('id'=>$value['project_id'], 'name'=>$value['name'], 'description'=>$value['description']);
}
open_showmessage($_SGLOBAL['open_errorinfo'][0],'', $projects);
?>