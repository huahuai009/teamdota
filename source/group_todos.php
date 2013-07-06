<?php
/*
	[TEAMDOTA] (C) 2012 wanghui
	$Id: group_todos.php 2012-07-02 23:59Z duty $
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

$todos_id = empty($_GET['todos_id'])?0:intval($_GET['todos_id']);
$objectid = $todos_id;//前端使用，服务端勿使用
if($todos_id) {
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('todos')." WHERE todos_id='{$todos_id}' LIMIT 1");
	if(!$todos = $_SGLOBAL['db']->fetch_array($query)) {
		showmessage('todos_does_not_exist',"group.php?do=project&project_id={$project_id}");
	}
	if($project_id != $todos['project_id']){
		showmessage('todos_does_not_exist',"group.php?do=project&project_id={$project_id}");
	}
	if(!$_SGLOBAL['inajax']) {
		$listtodos = array();
		$listtodos_completed = array();
		$querylist = $_SGLOBAL['db']->query("SELECT * FROM ".tname('todoslist')." WHERE todos_id='{$todos_id}' AND `status`=0 ORDER BY orderid DESC");
		while ($row = $_SGLOBAL['db']->fetch_array($querylist)) {
			if($row['due_date']) {
				$row['due_date'] = sgmdate('Y-m-d', $row['due_date']);
			}
			if($row['completed_date']) {
				$row['completed_date'] = sgmdate('Y-m-d H:i', $row['completed_date']);
			}
			if($row['is_completed'] == 0) {
				$listtodos[] = $row;
			} else {
				$listtodos_completed[] = $row;
			}
		}
		if($todos['status'] == 1) {//处于删除状态
			$query_trash = $_SGLOBAL['db']->query("SELECT sender_author,created_time FROM ".tname('trash_can')." WHERE object_id ='{$todos_id}' AND object_type='todosid' ORDER BY created_time DESC LIMIT 1");
			$trash = $_SGLOBAL['db']->fetch_array($query_trash);
		}
		//获取历史记录
		$listhistory = array();
		$query_history = $_SGLOBAL['db']->query("SELECT href,created_time,sender_author,title_html,title_text,sender_id FROM ".tname('notification')." WHERE object_id ='{$todos_id}' AND icon_url='todos' ORDER BY created_time ASC");
		while ($row = $_SGLOBAL['db']->fetch_array($query_history)) {
			$row = mknotification($row);
			$listhistory[] = $row;
		}
		include_once template("group_todos");
	} else {
		if($todos['discussion_id']){
			//讨论列表
			//分页
			$page = empty($_GET['page'])?1:intval($_GET['page']);
			if($page<1) $page=1;
			$perpage = 20;
			$start = ($page-1)*$perpage;

			$count = $_SGLOBAL['db']->result($_SGLOBAL['db']->query("SELECT COUNT(*) FROM ".tname('post')." WHERE discussion_id='{$todos[discussion_id]}'"),0);

			$list = array();
			$listpostpic = array();
			$listpostfile = array();
			$postnum = $start;
			$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('post')." WHERE discussion_id='{$todos[discussion_id]}' ORDER BY logtime DESC LIMIT $start,$perpage");
			while ($value = $_SGLOBAL['db']->fetch_array($query)) {
				$list[] = $value;
				$queryfile = $_SGLOBAL['db']->query("SELECT * FROM ".tname('file')." WHERE post_id='{$value[post_id]}' ORDER BY logtime DESC");
				while ($row = $_SGLOBAL['db']->fetch_array($queryfile)) {
					if($row['isimage']) {
						$thumbwh = get_thumbwh($row['width'],$row['height']);
						$listpostpic[$value['post_id']][] = array('file_id'=>$row['file_id'],'project_id'=>$row['project_id'],'discussion_id'=>$row['discussion_id'],'filename'=>$row['filename'],'fileurl'=>pic_get($row['fileurl'], '', $row['remote']),'thumbfileurl'=>pic_get($row['fileurl'], $row['thumb'], $row['remote']),'width'=>$row['width'],'height'=>$row['height'],'thumbwidth'=>$thumbwh['thumbwidth'],'thumbheight'=>$thumbwh['thumbheight']);
					} else {
						$listpostfile[$value['post_id']][] = array('file_id'=>$row['file_id'],'project_id'=>$row['project_id'],'discussion_id'=>$row['discussion_id'],'filename'=>$row['filename'],'thumbfileurl'=>file_icon_big($row['type']));
					}
				}
				$postnum++;
			}
			//分页
			if($count > $perpage) {
				$pagenumbers = '<div class="more_topics">'.getpageajax($page, $perpage, $count, array("group.php?project_id={$project_id}&do={$do}&todos_id={$todos_id}&inajax=1","commentsdata_{$todos_id}")).'</div>';
			}
		}
		$objectdata = $todos;
		include_once template("group_post_ajax");
	}
}else {
	//获取待办事宜BEGIN
	$listtodos = array();
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('todos')." WHERE `project_id`='{$project_id}' AND `status`=0 ORDER BY orderid DESC");
	while ($value = $_SGLOBAL['db']->fetch_array($query)) {
		//读取待办事宜详细
		$querylist = $_SGLOBAL['db']->query("SELECT * FROM ".tname('todoslist')." WHERE todos_id='{$value[todos_id]}' AND `status`=0 ORDER BY orderid DESC");
		while ($row = $_SGLOBAL['db']->fetch_array($querylist)) {
			if($row['due_date']) {
				$row['due_date'] = sgmdate('Y-m-d', $row['due_date']);
			}
			$value['theparent'][] = $row;
		}
		$listtodos[] = $value;
	}
	$completed_count = $_SGLOBAL['db']->result($_SGLOBAL['db']->query("SELECT COUNT(*) FROM ".tname('todoslist')." WHERE `project_id`='{$project_id}' AND `is_completed`=1"),0);
	//获取待办事宜END
	include_once template("group_todos_ajax");
}
?>