<?php
/*
	[TEAMDOTA] (C) 2012 wanghui
	$Id: open_todoslist.php 2012-07-26 09:59Z duty $
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

if($a == 'todoslist_list') {//获取todolist列表
	$todos_id = empty($_POST['todosid'])?0:intval($_POST['todosid']);
	$k = 1;
	$listtodoslist = array();
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('todoslist')." WHERE todos_id='{$todos_id}' AND `status`=0 AND `is_completed`=0 ORDER BY orderid ASC");
	while ($value = $_SGLOBAL['db']->fetch_array($query)) {
		if($value['due_date']) {
			$value['due_date'] = sgmdate('Y-m-d', $value['due_date']);
		}
		$listtodoslist[] = array(
			'id'=>$k,
			'todoslistid'=>$value['todoslist_id'],
			'todosid'=>$value['todos_id'],
			'projectid'=>$value['project_id'],
			'uid'=>$value['uid'],
			'subject'=>$value['subject'],
			'author'=>$value['author'],
			'assign_author'=>$value['assign_author'],
			'due_date'=>$value['due_date'],
			'post_num'=>$value['post_num']
		);
		$k++;
	}
	open_showmessage($_SGLOBAL['open_errorinfo'][0],'', $listtodoslist);
	
} elseif($a == 'todoslist_comment_list') {//获取todolist讨论列表，获取前50条
	$todoslist_id = empty($_POST['todoslistid'])?0:intval($_POST['todoslistid']);
	if(empty($todoslist_id)) {
		open_showmessage($_SGLOBAL['open_errorinfo'][1001]);
	}
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('todoslist')." WHERE todoslist_id='{$todoslist_id}' LIMIT 1");
	if(!$todoslist = $_SGLOBAL['db']->fetch_array($query)) {
		open_showmessage($_SGLOBAL['open_errorinfo'][1001]);
	}
	if($project_id != $todoslist['project_id']){
		open_showmessage($_SGLOBAL['open_errorinfo'][1001]);
	}
	$list = array();
	if($todoslist['discussion_id']){
		$list = array();
		$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('post')." WHERE discussion_id='{$todoslist[discussion_id]}' ORDER BY logtime DESC LIMIT 50");
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
} elseif($a == 'send_comment') {//添加todolist讨论
	include_once(S_ROOT.'./source/function_cp.php');
	$todoslist_id = empty($_POST['todoslistid'])?0:intval($_POST['todoslistid']);
	if(empty($todoslist_id)) {
		open_showmessage($_SGLOBAL['open_errorinfo'][1001]);
	}
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('todoslist')." WHERE todoslist_id='{$todoslist_id}' LIMIT 1");
	if(!$todoslist = $_SGLOBAL['db']->fetch_array($query)) {
		open_showmessage($_SGLOBAL['open_errorinfo'][1001]);
	}
	if($project_id != $todoslist['project_id']){
		open_showmessage($_SGLOBAL['open_errorinfo'][1001]);
	}
	if($todoslist['status'] != 0) {
		open_showmessage($_SGLOBAL['open_errorinfo'][1001]);
	}
	$discussion_id = $todoslist['discussion_id'];
	$discussion_subject = $todoslist['subject'];
	if(empty($discussion_id)) {
		//添加主题
		$discussion_subject = $todoslist['subject'];
		$setarrdiscussion['group_id'] = $todoslist['group_id'];
		$setarrdiscussion['subject'] = $todoslist['subject'];
		$setarrdiscussion['message'] = $todoslist['subject'];
		$setarrdiscussion['navidescription'] = getstr($todoslist['subject'], 200, 1, 1 , 0 , 0 , -1);
		$setarrdiscussion['lastpost'] = $_SGLOBAL['timestamp'];
		$setarrdiscussion['project_id'] = $project_id;
		$setarrdiscussion['uid'] = $todoslist['uid'];
		$setarrdiscussion['author'] = $todoslist['author'];
		$setarrdiscussion['logtime'] = $_SGLOBAL['timestamp'];
		$setarrdiscussion['useip'] = getonlineip();
		$setarrdiscussion['othertype'] = 4;
		$setarrdiscussion['otherid'] = $todoslist_id;
		$discussion_id = inserttable('discussion', $setarrdiscussion, 1);
		if($discussion_id) {
			//更新统计数据
			$_SGLOBAL['db']->query("UPDATE ".tname('todoslist')." SET discussion_id='{$discussion_id}' WHERE todoslist_id='{$todoslist_id}'");
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
			notification_add('post', 'create',  $project_id, $manageproject['name'], 'todoslistid', $todoslist_id, cplang('notification_todoslist_post_create'), array('subject' => $discussion_subject));
			//更新统计数据
			$_SGLOBAL['db']->query("UPDATE ".tname('discussion')." SET post_num=post_num+1,navidescription='{$summay}',lastpost='{$_SGLOBAL[timestamp]}',lastposter='{$setarr[author]}' WHERE discussion_id='$discussion_id'");
			$_SGLOBAL['db']->query("UPDATE ".tname('todoslist')." SET post_num=post_num+1 WHERE todoslist_id='{$todoslist_id}'");
		}
		open_showmessage($_SGLOBAL['open_errorinfo'][0]);
	} else {
		open_showmessage($_SGLOBAL['open_errorinfo'][1000]);
	}
} elseif($a == 'add_todoslist') {//添加todolist
	include_once(S_ROOT.'./source/function_cp.php');
	$todos_id = empty($_POST['todosid'])?0:intval($_POST['todosid']);
	$todos = array();
	if($todos_id) {
		$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('todos')." WHERE todos_id='{$todos_id}'");
		$todos = $_SGLOBAL['db']->fetch_array($query);
	}
	if(empty($todos)) {
		open_showmessage($_SGLOBAL['open_errorinfo'][1000]);
	}
	if($project_id != $todos['project_id']){
		open_showmessage($_SGLOBAL['open_errorinfo'][1000]);
	}
	if($todos['status'] != 0){
		open_showmessage($_SGLOBAL['open_errorinfo'][1000]);
	}
	$subject = getstr($_POST['subject'], 80, 1, 1, 1);
	if(strlen($subject) < 1) open_showmessage($_SGLOBAL['open_errorinfo'][1000]);
	$assign_uid = empty($_POST['todo_assignee_code'])?0:intval($_POST['todo_assignee_code']);//事项分配人员ID
	$due_date = 0;
	if(!empty($_POST['todo_due_at']) && is_date($_POST['todo_due_at'])) {
		$due_date = sstrtotime($_POST['todo_due_at']);//分配时间
	}

	$assign_author = '';
	if(!empty($assign_uid)) {
		$query = $_SGLOBAL['db']->query("SELECT group_id,fullname,uid,status,email FROM ".tname('member')." WHERE uid='{$assign_uid}'");
		if($member = $_SGLOBAL['db']->fetch_array($query)) {
			if($member['status'] == 0 && $member['group_id'] == $group['group_id']) {
				$assign_author = $member['fullname'];
			} else {
				$assign_uid = 0;
			}
		} else {
			$assign_uid = 0;
		}
	}
	$max_orderid = $_SGLOBAL['db']->result($_SGLOBAL['db']->query("SELECT max(orderid) FROM ".tname('todoslist')." WHERE todos_id='{$todos_id}'"),0);
	$setarr['subject'] = $subject;
	$setarr['assign_uid'] = $assign_uid;
	$setarr['assign_author'] = $assign_author;
	$setarr['due_date'] = $due_date;
	$setarr['group_id'] = $group['group_id'];
	$setarr['project_id'] = $project_id;
	$setarr['todos_id'] = $todos_id;
	$setarr['uid'] = $_SGLOBAL['supe_uid'];
	$setarr['author'] = $_SGLOBAL['member']['fullname'];
	$setarr['logtime'] = $_SGLOBAL['timestamp'];
	$setarr['orderid'] = $max_orderid + 1;
	$todoslist_id = inserttable('todoslist', $setarr, 1);
	if($todoslist_id) {
		//更新统计数据
		$_SGLOBAL['db']->query("UPDATE ".tname('project')." SET todoslist_num=todoslist_num+1 WHERE project_id='{$project_id}'");
		//添加事件
		notification_add('todoslist', 'create',  $project_id, $manageproject['name'], 'todoslistid', $todoslist_id, cplang('notification_todoslist_create'), array('subject' => $setarr['subject']));
		//发送通知邮件
		if(!empty($assign_uid) && ($setarr['assign_uid'] != $_SGLOBAL['supe_uid']) && !empty($member['email'])) {
			$datavar['project_id'] = $manageproject['project_id'];
			$datavar['project_name'] = $manageproject['name'];
			$datavar['todos_subject'] = $todos['subject'];
			$datavar['todoslist_id'] = $todoslist_id;
			$datavar['todoslist_subject'] = $subject;
			$datavar['todo_username'] = $assign_author;
			creat_todo_email($member['email'],$datavar);
		}
	}
	open_showmessage($_SGLOBAL['open_errorinfo'][0]);
}
?>