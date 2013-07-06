<?php
/*
	[TEAMDOTA] (C) 2012 wanghui
	$Id: group_todoslist.php 2012-03-31 09:59Z duty $
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
$todoslist_id = empty($_GET['todoslist_id'])?0:intval($_GET['todoslist_id']);
$objectid = $todoslist_id;//前端使用，服务端勿使用
if($todoslist_id) {
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('todoslist')." WHERE todoslist_id='{$todoslist_id}' LIMIT 1");
	if(!$todoslist = $_SGLOBAL['db']->fetch_array($query)) {
		showmessage('todoslist_does_not_exist',"group.php?do=project&project_id={$project_id}");
	}
	if($project_id != $todoslist['project_id']){
		showmessage('todoslist_does_not_exist',"group.php?do=project&project_id={$project_id}");
	}
	if(!$_SGLOBAL['inajax']) {
		$todos = $_SGLOBAL['db']->fetch_array($_SGLOBAL['db']->query("SELECT * FROM ".tname('todos')." WHERE todos_id='{$todoslist[todos_id]}' LIMIT 1"));
		if(!$todos) {
			showmessage('todoslist_does_not_exist',"group.php?do=project&project_id={$project_id}");
		}
		if($todos['status'] == 1) {
			showmessage('todoslist_does_not_exist','group.php?do=todos&todos_id='.$todoslist['todos_id'].'&project_id='.$project_id,0);
		}
		if($todoslist['due_date']) {
			$todoslist['due_date'] = sgmdate('Y-m-d', $todoslist['due_date']);
		}
		if($todoslist['status'] == 1) {//处于删除状态
			$query_trash = $_SGLOBAL['db']->query("SELECT sender_author,created_time FROM ".tname('trash_can')." WHERE object_id ='{$todoslist_id}' AND object_type='todoslistid' ORDER BY created_time DESC LIMIT 1");
			$trash = $_SGLOBAL['db']->fetch_array($query_trash);
		}
		//获取历史记录
		$listhistory = array();
		$query_history = $_SGLOBAL['db']->query("SELECT href,created_time,sender_author,title_html,title_text,sender_id FROM ".tname('notification')." WHERE object_id ='{$todoslist_id}' AND icon_url='todoslist' ORDER BY created_time ASC");
		while ($row = $_SGLOBAL['db']->fetch_array($query_history)) {
			$row = mknotification($row);
			$listhistory[] = $row;
		}
		
		include_once template("group_todoslist_view");
	} else {
		if($todoslist['discussion_id']){
			//讨论列表
			//分页
			$page = empty($_GET['page'])?1:intval($_GET['page']);
			if($page<1) $page=1;
			$perpage = 20;
			$start = ($page-1)*$perpage;

			$count = $_SGLOBAL['db']->result($_SGLOBAL['db']->query("SELECT COUNT(*) FROM ".tname('post')." WHERE discussion_id='{$todoslist[discussion_id]}'"),0);

			$list = array();
			$listpostpic = array();
			$listpostfile = array();
			$postnum = $start;
			$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('post')." WHERE discussion_id='{$todoslist[discussion_id]}' ORDER BY logtime DESC LIMIT $start,$perpage");
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
				$pagenumbers = '<div class="more_topics">'.getpageajax($page, $perpage, $count, array("group.php?project_id={$project_id}&do={$do}&todoslist_id={$todoslist_id}&inajax=1","commentsdata_{$todoslist_id}")).'</div>';
			}
		}
		$objectdata = $todoslist;
		include_once template("group_post_ajax");
	}
} else {
	$assign_uid = $_GET['assign_uid'] == '' ?-1:intval($_GET['assign_uid']);
	$is_due = empty($_GET['is_due'])?0:intval($_GET['is_due']);
	$where = '';
	if($assign_uid != -1) {
		$where .= " AND `assign_uid`='{$assign_uid}'";
	}
	if($is_due) {
		$where .= " AND `due_date` < '{$_SGLOBAL['timestamp']}'";
	}
	$listtodos = array();
	$listuser = array();
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('todos')." WHERE `project_id`='{$project_id}' AND `status`=0 ORDER BY orderid DESC");
	while ($value = $_SGLOBAL['db']->fetch_array($query)) {
		$querylist = $_SGLOBAL['db']->query("SELECT * FROM ".tname('todoslist')." WHERE todos_id='{$value[todos_id]}' AND `status`=0 AND `is_completed`=0 {$where} ORDER BY orderid DESC");
		while ($row = $_SGLOBAL['db']->fetch_array($querylist)) {
			if($row['due_date']) {
				$row['due_date'] = sgmdate('Y-m-d', $row['due_date']);
			}
			if($row['completed_date']) {
				$row['completed_date'] = sgmdate('Y-m-d H:i', $row['completed_date']);
			}
			$value['theparent'][] = $row;
			if($listuser[$row['assign_uid']]) {
				$listuser[$row['assign_uid']]['num'] = $listuser[$row['assign_uid']]['num'] + 1;
			} else {
				$row['assign_author'] = $row['assign_uid'] == 0 ?  '未分配' : $row['assign_author'];
				$listuser[$row['assign_uid']] = array('assign_uid'=>$row['assign_uid'],'assign_author'=> $row['assign_author'],'num'=>1);
			}
		}
		$listtodos[] = $value;
	}
	include_once template("group_todoslist");
}
?>