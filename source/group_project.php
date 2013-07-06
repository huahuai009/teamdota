<?php
/*
	[TEAMDOTA] (C) 2012 wanghui
	$Id: group_project.php 2012-03-31 09:59Z duty $
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
	$query = $_SGLOBAL['db']->query("SELECT sender_author,created_time FROM ".tname('trash_can')." WHERE object_id ='{$project_id}' AND object_type='projectid'  ORDER BY created_time DESC LIMIT 1");
	$trash = $_SGLOBAL['db']->fetch_array($query);
	include_once template("group_project_delete");
} else {
	$query = $_SGLOBAL['db']->query("SELECT m.fullname,m.uid FROM ".tname('project_member')." pm,".tname('member')." m WHERE pm.uid=m.uid AND pm.project_id='{$project_id}' ORDER BY pm.id ASC");
	$members = array();
	while($value = $_SGLOBAL['db']->fetch_array($query)) {
		$members[] = $value;
	}

	$discussion_page = empty($_GET['discussion_page'])?1:intval($_GET['discussion_page']);
	$document_page = empty($_GET['document_page'])?1:intval($_GET['document_page']);
	$file_page = empty($_GET['file_page'])?1:intval($_GET['file_page']);
	
	//获取待办事宜BEGIN
	$listtodos = array();
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('todos')." WHERE `project_id`='{$project_id}' AND `status`=0 ORDER BY orderid DESC");
	while ($value = $_SGLOBAL['db']->fetch_array($query)) {
		//读取待办事宜详细
		$querylist = $_SGLOBAL['db']->query("SELECT * FROM ".tname('todoslist')." WHERE todos_id='{$value[todos_id]}' AND `status`=0 AND `is_completed`=0 ORDER BY orderid DESC");
		while ($row = $_SGLOBAL['db']->fetch_array($querylist)) {
			if($row['due_date']) {
				$row['due_date'] = sgmdate('Y-m-d', $row['due_date']);
			}
			$value['theparent'][] = $row;
		}
		$listtodos[] = $value;
	}
	$completed_count = $_SGLOBAL['db']->result($_SGLOBAL['db']->query("SELECT COUNT(*) FROM ".tname('todoslist')." WHERE `project_id`='{$project_id}' AND `status`=0 AND `is_completed`=1"),0);
	//获取待办事宜END
	include_once template("group_project");
}
?>