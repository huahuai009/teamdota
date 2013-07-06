<?php
/*
	[TEAMDOTA] (C) 2012 wanghui
	$Id: cp_todoslist.php 2012-06-28 16:38Z duty $
*/

if(!defined('IN_TEAMDOTA')) {
	exit('Access Denied');
}

$project_id = empty($_GET['project_id']) ? 0 : intval($_GET['project_id']);
if(empty($project_id)){
	showmessage('project_not_allowed_to_visit');
}
$manageproject = checkproject($project_id);
if(!$manageproject) {
	showmessage('project_not_allowed_to_visit');
}
if($manageproject['status'] != 0) {
	showmessage('failed_to_operation');
}

//添加编辑操作
if(submitcheck('todoslistsubmit')) {
	//检查信息
	$todos_id = empty($_GET['todos_id'])?0:intval($_GET['todos_id']);
	$todoslist_id = empty($_POST['todoslist_id'])?0:intval($_POST['todoslist_id']);
	$todoslist = array();
	if($todoslist_id) {
		$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('todoslist')." WHERE todoslist_id='{$todoslist_id}'");
		$todoslist = $_SGLOBAL['db']->fetch_array($query);
		if(!empty($todoslist)) {
			if($project_id != $todoslist['project_id']){
				showmessage('failed_to_operation');
			}
			if($todoslist['status'] != 0) {
				showmessage('failed_to_operation');
			}
		} else {
			showmessage('failed_to_operation');
		}
		$todos_id = $todoslist['todos_id'];
	}
	
	$todos = array();
	if($todos_id) {
		$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('todos')." WHERE todos_id='{$todos_id}'");
		$todos = $_SGLOBAL['db']->fetch_array($query);
	}
	if(empty($todos)) {
		showmessage('failed_to_operation');
	}
	if($project_id != $todos['project_id']){
		showmessage('failed_to_operation');
	}
	if($todos['status'] != 0){
		showmessage('failed_to_operation');
	}
	
	$subject = getstr($_POST['subject'], 80, 1, 1, 1);
	if(strlen($subject) < 1) showmessage('todoslist_subject_error');
	$assign_uid = empty($_POST['todo_assignee_code'])?0:intval($_POST['todo_assignee_code']);//事项分配人员ID
	$due_date = 0;
	if(!empty($_POST['todo_due_at']) && is_date($_POST['todo_due_at'])) {
		$due_date = sstrtotime($_POST['todo_due_at']);//分配时间
	}

	$assign_author = '';
	if(!empty($assign_uid)) {
		$query = $_SGLOBAL['db']->query("SELECT group_id,fullname,uid,status,email FROM ".tname('member')." WHERE uid='{$assign_uid}'");
		if($member = $_SGLOBAL['db']->fetch_array($query)) {
			if($member['status'] == 0 && $member['group_id'] == $groupid) {
				$assign_author = $member['fullname'];
			} else {
				$assign_uid = 0;
			}
		} else {
			$assign_uid = 0;
		}
	}
	$setarr = array(
		'subject' => $subject,
		'assign_uid' => $assign_uid,
		'assign_author' => $assign_author,
		'due_date' => $due_date
	);
	if(empty($todoslist_id)) {
		$max_orderid = $_SGLOBAL['db']->result($_SGLOBAL['db']->query("SELECT max(orderid) FROM ".tname('todoslist')." WHERE todos_id='{$todos_id}'"),0);
		$setarr['group_id'] = $groupid;
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
	} else {
		updatetable('todoslist', $setarr, array('todoslist_id'=>$todoslist_id));
		if($todoslist['subject'] != $setarr['subject'] || $todoslist['assign_uid'] != $setarr['assign_uid'] || $todoslist['due_date'] != $setarr['due_date']) {
			//添加事件
			notification_add('todoslist', 'update',  $project_id, $manageproject['name'], 'todoslistid', $todoslist_id, cplang('notification_todoslist_update'), array('subject' => $setarr['subject']));
			//发送通知邮件
			if(!empty($assign_uid) && ($todoslist['assign_uid'] != $setarr['assign_uid']) && ($setarr['assign_uid'] != $_SGLOBAL['supe_uid']) && !empty($member['email'])) {
				$datavar['project_id'] = $manageproject['project_id'];
				$datavar['project_name'] = $manageproject['name'];
				$datavar['todos_subject'] = $todos['subject'];
				$datavar['todoslist_id'] = $todoslist_id;
				$datavar['todoslist_subject'] = $subject;
				$datavar['todo_username'] = $assign_author;
				creat_todo_email($member['email'],$datavar);
			}
		}
	}
	showmessage('do_success',"group.php?do=project&project_id={$project_id}");
}
//检查信息
$todos_id = empty($_GET['todos_id'])?0:intval($_GET['todos_id']);
$todoslist_id = empty($_GET['todoslist_id'])?0:intval($_GET['todoslist_id']);
$op = empty($_GET['op'])?'':$_GET['op'];

$todoslist = array();
if($todoslist_id) {
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('todoslist')." WHERE todoslist_id='{$todoslist_id}'");
	$todoslist = $_SGLOBAL['db']->fetch_array($query);
	if(empty($todoslist)) {
		showmessage('project_not_allowed_to_visit');
	}
	if($project_id != $todoslist['project_id']){
		showmessage('project_not_allowed_to_visit');
	}
} else {
	$todos = array();
	if($todos_id) {
		$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('todos')." WHERE todos_id='{$todos_id}'");
		$todos = $_SGLOBAL['db']->fetch_array($query);
	}
	if(empty($todos)) {
		showmessage('project_not_allowed_to_visit');
	}
	if($project_id != $todos['project_id']){
		showmessage('project_not_allowed_to_visit');
	}
	if($todos['status'] != 0){
		showmessage('project_not_allowed_to_visit');
	}
}

//编辑
if($_GET['op'] == 'edit') {
	if($todoslist['status'] != 0) {
		showmessage('failed_to_operation');
	}
	$todoslist['due_date'] = $todoslist['due_date'] == 0 ? '' : sgmdate('Y-m-d', $todoslist['due_date']);
} elseif($_GET['op'] == 'completed') {
	if($todoslist_id) {
		include_once(S_ROOT.'./source/function_delete.php');
		if(completed_can_todoslist($project_id,$todoslist_id)) {
			$todoslist = $_SGLOBAL['db']->fetch_array($_SGLOBAL['db']->query("SELECT * FROM ".tname('todoslist')." WHERE todoslist_id='{$todoslist_id}'"));
			$todoslist['completed_date'] = sgmdate('Y-m-d H:i', $todoslist['completed_date']);
			include_once template("group_todoslist_ajax");
			exit;
		} else {
			echo '0';
			exit;
		}
	} else {
		echo '0';
		exit;
	}
} elseif($_GET['op'] == 'nocompleted') {
	if($todoslist_id) {
		include_once(S_ROOT.'./source/function_delete.php');
		if(completed_can_todoslist($project_id,$todoslist_id)) {
			$todoslist = $_SGLOBAL['db']->fetch_array($_SGLOBAL['db']->query("SELECT * FROM ".tname('todoslist')." WHERE todoslist_id='{$todoslist_id}'"));
			if($todoslist['due_date']) {
				$todoslist['due_date'] = sgmdate('Y-m-d', $todoslist['due_date']);
			}
			include_once template("group_todoslist_ajax");
			exit;
		} else {
			echo '0';
			exit;
		}
	} else {
		echo '0';
		exit;
	}
} elseif($_GET['op'] == 'delete') {//送入回收站
	if($todoslist_id) {
		include_once(S_ROOT.'./source/function_delete.php');
		if(trash_can_todoslist($project_id,$todoslist_id)) {
			showmessage('do_success',"group.php?do=todos&todos_id={$todos_id}&project_id={$project_id}",0);
		} else {
			showmessage('failed_to_delete_operation');
		}
	} else {
		showmessage('failed_to_delete_operation');
	}
} elseif($_GET['op'] == 'restored') {//恢复
	if($todoslist_id) {
		include_once(S_ROOT.'./source/function_delete.php');
		if(restored_todoslist($project_id,$todoslist_id)) {
			showmessage('do_success',"group.php?do=todoslist&todoslist_id={$todoslist_id}&project_id={$project_id}",0);
		} else {
			showmessage('failed_to_restored_operation');
		}
	} else {
		showmessage('failed_to_restored_operation');
	}
} elseif($_GET['op'] == 'realdelete') {//真正删除
	if($todoslist_id) {
		include_once(S_ROOT.'./source/function_delete.php');
		if(deletetodoslist($project_id,$todoslist_id)) {
			showmessage('do_success',"group.php?do=todos&todos_id={$todos_id}&project_id={$project_id}",0);
		} else {
			showmessage('failed_to_delete_operation');
		}
	} else {
		showmessage('failed_to_delete_operation');
	}
}
$query = $_SGLOBAL['db']->query("SELECT m.fullname,m.uid FROM ".tname('project_member')." pm,".tname('member')." m WHERE pm.project_id='{$project_id}' AND m.isactive=0 AND pm.uid=m.uid  ORDER BY pm.id ASC");
$listuser = array();
while($value = $_SGLOBAL['db']->fetch_array($query)) {
	$listuser[] = $value;
}
include_once template("cp_todoslist");

?>