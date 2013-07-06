<?php
/*
	[TEAMDOTA] (C) 2012 wanghui
	$Id: group_todoslist_completed.php 2012-03-31 09:59Z duty $
*/

if(!defined('IN_TEAMDOTA')) {
	exit('Access Denied');
}

$project_id = empty($_GET['project_id']) ? 0 : intval($_GET['project_id']);
if(empty($project_id)){
	showmessage('project_not_allowed_to_visit','group.php?do=home');
}
$manageproject = checkproject($project_id);
if(!$manageproject) {
	showmessage('project_not_allowed_to_visit','group.php?do=home');
}
if($manageproject['status'] == 2) {//处于删除状态
	dheader("location: group.php?do=project&project_id={$project_id}");
}
//获取所有的
$listtodos = array();
$query = $_SGLOBAL['db']->query("SELECT todos_id,subject FROM ".tname('todos')." WHERE `project_id`='{$project_id}' AND `status`=0");
while ($value = $_SGLOBAL['db']->fetch_array($query)) {
	$listtodos[$value['todos_id']] = $value['subject'];
}

//分页
$page = empty($_GET['page'])?1:intval($_GET['page']);
if($page<1) $page=1;
$perpage = 30;
$start = ($page-1)*$perpage;

$count = $_SGLOBAL['db']->result($_SGLOBAL['db']->query("SELECT COUNT(*) FROM ".tname('todoslist')." WHERE `project_id`='{$project_id}' AND `status`=0 AND `is_completed`=1"),0);
$listday = array();
$querylist = $_SGLOBAL['db']->query("SELECT * FROM ".tname('todoslist')." WHERE `project_id`='{$project_id}' AND `status`=0 AND `is_completed`=1 ORDER BY completed_date DESC LIMIT $start,$perpage");
while ($value = $_SGLOBAL['db']->fetch_array($querylist)) {
	if($listtodos[$value['todos_id']]) {
		$day = sgmdate('Y-m-d', $value['completed_date']);
		$listday[$day][$value['todos_id']][] = $value;
	}
}
//分页
if($count > $perpage) {
	$pagenumbers = getpage($page, $perpage, $count, "group.php?project_id={$project_id}&do={$do}");
}
include_once template("group_todoslist_completed");
?>