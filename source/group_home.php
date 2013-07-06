<?php
/*
	[TEAMDOTA] (C) 2012 wanghui
	$Id: group_home.php 2012-03-31 09:59Z duty $
*/

if(!defined('IN_TEAMDOTA')) {
	exit('Access Denied');
}
//读取活跃项目
$query = $_SGLOBAL['db']->query("SELECT p.*, pm.type FROM ".tname('project')." p , ".tname('project_member')." pm WHERE p.project_id=pm.project_id AND p.group_id='{$groupid}' AND pm.uid='".$_SGLOBAL['member']['uid']."' AND p.status<>2");
$projects = array();//活跃项目
$archived_projects = array();//归档的项目
while($value = $_SGLOBAL['db']->fetch_array($query)) {
	if($value['status'] == '0') {
		$querypm = $_SGLOBAL['db']->query("SELECT m.fullname,m.uid FROM ".tname('project_member')." pm,".tname('member')." m WHERE pm.uid=m.uid AND pm.project_id='{$value[project_id]}' ORDER BY pm.id DESC LIMIT 10");
		while($valuepm = $_SGLOBAL['db']->fetch_array($querypm)) {
			$value['members'] .= '<img class="avatar" height="40" src="'.avatar($valuepm['uid'],'40',true).'" title="'.$valuepm['fullname'].'" width="40" onerror="this.onerror=null;this.src=\'/image/avatar.gif\'" />';
		}
		$projects[] = $value;
	} elseif($value['status'] == '1') {
		$archived_projects[] = $value;
	}
}
$projectnumber = count($projects);
$archived_projectnumber = count($archived_projects);
include_once template("group_home");
?>