<?php
/*
	[TEAMDOTA] (C) 2012 wanghui
	$Id: group_trash.php 2012-05-26 10:39Z duty $
*/

if(!defined('IN_TEAMDOTA')) {
	exit('Access Denied');
}
//读取项目
$query = $_SGLOBAL['db']->query("SELECT project_id FROM ".tname('project_member')." WHERE uid='".$_SGLOBAL['supe_uid']."'");
$arr_project_id = array();
while($value = $_SGLOBAL['db']->fetch_array($query)) {
	$arr_project_id[] = $value['project_id'];
}

$wheresql = "project_id IN (".simplode($arr_project_id).")";
$theurl = "group.php?do=trash";

$listday = array();
$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('trash_can')." WHERE $wheresql ORDER BY created_time DESC");
while ($value = $_SGLOBAL['db']->fetch_array($query)) {
	$day = sgmdate('Y-m-d', $value['created_time']);
	$value = mknotification($value);
	$listday[$day][] = $value;
}
include_once template("group_trash");
?>