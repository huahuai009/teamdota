<?php
/*
	[TEAMDOTA] (C) 2012 wanghui
	$Id: open_todos.php 2012-07-26 09:59Z duty $
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

if($a == 'todos_list') {//获取todo列表
	$k = 1;
	$listtodos = array();
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('todos')." WHERE `project_id`='{$project_id}' AND `status`=0 ORDER BY orderid ASC");
	while ($value = $_SGLOBAL['db']->fetch_array($query)) {
		$post_num = 0;
		if($value['discussion_id']){
			$query_discussion = $_SGLOBAL['db']->query("SELECT post_num FROM ".tname('discussion')." WHERE discussion_id='{$value[discussion_id]}' LIMIT 1");
			if($discussion = $_SGLOBAL['db']->fetch_array($query_discussion)) {
				$post_num = $discussion['post_num'];
			}
		}
		$listtodos[] = array(
			'id'=>$k,
			'todosid'=>$value['todos_id'],
			'projectid'=>$value['project_id'],
			'uid'=>$value['uid'],
			'subject'=>$value['subject'],
			'author'=>$value['author'],
			'post_num'=>$post_num
		);
		$k++;
	}
	open_showmessage($_SGLOBAL['open_errorinfo'][0],'', $listtodos);
	
} elseif($a == 'todos_comment_list') {//获取todo讨论列表，获取前50条
	$todos_id = empty($_POST['todosid'])?0:intval($_POST['todosid']);
	if(empty($todos_id)) {
		open_showmessage($_SGLOBAL['open_errorinfo'][1001]);
	}
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('todos')." WHERE todos_id='{$todos_id}' LIMIT 1");
	if(!$todos = $_SGLOBAL['db']->fetch_array($query)) {
		open_showmessage($_SGLOBAL['open_errorinfo'][1001]);
	}
	if($project_id != $todos['project_id']){
		open_showmessage($_SGLOBAL['open_errorinfo'][1001]);
	}
	$list = array();
	if($todos['discussion_id']){
		$list = array();
		$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('post')." WHERE discussion_id='{$todos[discussion_id]}' ORDER BY logtime DESC LIMIT 50");
		while ($value = $_SGLOBAL['db']->fetch_array($query)) {
			//$value['logtime'] = sgmdate('Y-m-d H:i:s', $value['logtime'],0);
			$list[] = array(
				'postid'=>$value['post_id'],
				'discussionid'=>$value['discussion_id'],
				'projectid'=>$value['project_id'],
				'uid'=>$value['uid'],
				'message'=>$value['message'],
				'author'=>$value['author'],
				'timeline'=>$value['logtime']
			);
		}
		
	} 
	open_showmessage($_SGLOBAL['open_errorinfo'][0],'', $list);
} elseif($a == 'send_comment') {//添加todo讨论
	include_once(S_ROOT.'./source/function_cp.php');
	$todos_id = empty($_POST['todosid'])?0:intval($_POST['todosid']);
	if(empty($todos_id)) {
		open_showmessage($_SGLOBAL['open_errorinfo'][1001]);
	}
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('todos')." WHERE todos_id='{$todos_id}' LIMIT 1");
	if(!$todos = $_SGLOBAL['db']->fetch_array($query)) {
		open_showmessage($_SGLOBAL['open_errorinfo'][1001]);
	}
	if($project_id != $todos['project_id']){
		open_showmessage($_SGLOBAL['open_errorinfo'][1001]);
	}
	if($todos['status'] != 0) {
		open_showmessage($_SGLOBAL['open_errorinfo'][1001]);
	}
	$discussion_id = $todos['discussion_id'];
	$discussion_subject = $todos['subject'];
	if(empty($discussion_id)) {
		//添加主题
		$setarrdiscussion['group_id'] = $todos['group_id'];
		$setarrdiscussion['subject'] = $todos['subject'];
		$setarrdiscussion['message'] = $todos['subject'];
		$setarrdiscussion['navidescription'] = getstr($todos['subject'], 200, 1, 1 , 0 , 0 , -1);
		$setarrdiscussion['lastpost'] = $_SGLOBAL['timestamp'];
		$setarrdiscussion['project_id'] = $project_id;
		$setarrdiscussion['uid'] = $todos['uid'];
		$setarrdiscussion['author'] = $todos['author'];
		$setarrdiscussion['logtime'] = $_SGLOBAL['timestamp'];
		$setarrdiscussion['useip'] = getonlineip();
		$setarrdiscussion['othertype'] = 3;
		$setarrdiscussion['otherid'] = $todos_id;
		$discussion_id = inserttable('discussion', $setarrdiscussion, 1);
		if($discussion_id) {
			//更新统计数据
			$_SGLOBAL['db']->query("UPDATE ".tname('todos')." SET discussion_id='{$discussion_id}' WHERE todos_id='{$todos_id}'");
			$_SGLOBAL['db']->query("UPDATE ".tname('project')." SET discussion_num=discussion_num+1 WHERE project_id='{$project_id}'");
		}
	}
	if($discussion_id) {
		$_POST['message'] = checkhtml($_POST['message']);
		$_POST['message'] = getstr($_POST['message'], 0, 1, 0, 1, 0, 1);
		$_POST['message'] = preg_replace("/\<div\>\<\/div\>/i", '', $_POST['message']);	
		$message = $_POST['message'];
		$message = addslashes($message);
		if(strlen($message) < 2) open_showmessage($_SGLOBAL['open_errorinfo'][1000]);
		$summay = getstr($message, 100, 1, 1 , 0 , 0 , -1);//摘要
		
		$setarr['message'] = $message;
		$setarr['group_id'] = $group['group_id'];
		$setarr['project_id'] = $project_id;
		$setarr['discussion_id'] = $discussion_id;
		$setarr['uid'] = $_SGLOBAL['supe_uid'];
		$setarr['author'] = $_SGLOBAL['member']['fullname'];
		$setarr['logtime'] = $_SGLOBAL['timestamp'];
		$setarr['useip'] = getonlineip();
		$post_id = inserttable('post', $setarr, 1);
		if($post_id) {
			//添加事件
			notification_add('post', 'create',  $project_id, $manageproject['name'], 'todosid', $todos_id, cplang('notification_todos_post_create'), array('subject' => $discussion_subject));
			//更新统计数据
			$_SGLOBAL['db']->query("UPDATE ".tname('discussion')." SET post_num=post_num+1,navidescription='{$summay}',lastpost='{$_SGLOBAL[timestamp]}',lastposter='{$setarr[author]}' WHERE discussion_id='$discussion_id'");
		}
		open_showmessage($_SGLOBAL['open_errorinfo'][0]);
	} else {
		open_showmessage($_SGLOBAL['open_errorinfo'][1000]);
	}
} elseif($a == 'add_todos') {//添加todo
	include_once(S_ROOT.'./source/function_cp.php');
	$subject = getstr($_POST['subject'], 80, 1, 1, 1);
	if(strlen($subject) < 1) open_showmessage($_SGLOBAL['open_errorinfo'][1000]);

	$max_orderid = $_SGLOBAL['db']->result($_SGLOBAL['db']->query("SELECT max(orderid) FROM ".tname('todos')." WHERE `project_id`='{$project_id}'"),0);
	$setarr['subject'] = $subject;
	$setarr['group_id'] = $group['group_id'];
	$setarr['project_id'] = $project_id;
	$setarr['uid'] = $_SGLOBAL['supe_uid'];
	$setarr['author'] = $_SGLOBAL['member']['fullname'];
	$setarr['logtime'] = $_SGLOBAL['timestamp'];
	$setarr['orderid'] = $max_orderid + 1;
	$todos_id = inserttable('todos', $setarr, 1);
	if($todos_id) {
		//添加事件
		notification_add('todos', 'create',  $project_id, $manageproject['name'], 'todosid', $todos_id, cplang('notification_todos_create'), array('subject' => $setarr['subject']));
	}
	open_showmessage($_SGLOBAL['open_errorinfo'][0]);
}
?>