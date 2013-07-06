<?php
/*
	[TEAMDOTA] (C) 2012 wanghui
	$Id: cp_todos.php 2012-06-28 16:38Z duty $
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
if(submitcheck('todossubmit')) {
	//检查信息
	$todos_id = empty($_POST['todos_id'])?0:intval($_POST['todos_id']);
	$todos = array();
	if($todos_id) {
		$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('todos')." WHERE todos_id='{$todos_id}'");
		$todos = $_SGLOBAL['db']->fetch_array($query);
		if(!empty($todos)) {
			if($project_id != $todos['project_id']){
				showmessage('failed_to_operation');
			}
			if($todos['status'] != 0) {
				showmessage('failed_to_operation');
			}
		} else {
			showmessage('failed_to_operation');
		}
	}
	
	$subject = getstr($_POST['subject'], 80, 1, 1, 1);
	if(strlen($subject) < 1) showmessage('todos_subject_error');

	$setarr = array(
		'subject' => $subject
	);
	if(empty($todos_id)) {
		$max_orderid = $_SGLOBAL['db']->result($_SGLOBAL['db']->query("SELECT max(orderid) FROM ".tname('todos')." WHERE `project_id`='{$project_id}'"),0);
		$setarr['group_id'] = $groupid;
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
	} else {
		updatetable('todos', $setarr, array('todos_id'=>$todos_id));
		if($todos['subject'] != $setarr['subject']) {
			//添加事件
			notification_add('todos', 'update',  $project_id, $manageproject['name'], 'todosid', $todos_id, cplang('notification_todos_update'), array('subject' => $setarr['subject']));
		}
	}
	showmessage('do_success',"group.php?do=project&project_id={$project_id}");
}
//检查信息
$todos_id = empty($_GET['todos_id'])?0:intval($_GET['todos_id']);
$op = empty($_GET['op'])?'':$_GET['op'];

$todos = array();
if($todos_id) {
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('todos')." WHERE todos_id='$todos_id'");
	$todos = $_SGLOBAL['db']->fetch_array($query);
}
if(!empty($todos)) {
	if($project_id != $todos['project_id']){
		showmessage('project_not_allowed_to_visit');
	}
}
//编辑
if($_GET['op'] == 'edit') {
	if(!$todos) {
		showmessage('project_not_allowed_to_visit');
	}
} elseif($_GET['op'] == 'delete') {//送入回收站
	if($todos_id) {
		include_once(S_ROOT.'./source/function_delete.php');
		if(trash_can_todos($project_id,$todos_id)) {
			showmessage('do_success',"group.php?do=project&project_id={$project_id}",0);
		} else {
			showmessage('failed_to_delete_operation');
		}
	} else {
		showmessage('failed_to_delete_operation');
	}
} elseif($_GET['op'] == 'restored') {//恢复
	if($todos_id) {
		include_once(S_ROOT.'./source/function_delete.php');
		if(restored_todos($project_id,$todos_id)) {
			showmessage('do_success',"group.php?do=todos&todos_id={$todos_id}&project_id={$project_id}",0);
		} else {
			showmessage('failed_to_restored_operation');
		}
	} else {
		showmessage('failed_to_restored_operation');
	}
} elseif($_GET['op'] == 'realdelete') {//真正删除
	if($todos_id) {
		include_once(S_ROOT.'./source/function_delete.php');
		if(deletetodos($project_id,$todos_id)) {
			showmessage('do_success',"group.php?do=project&project_id={$project_id}",0);
		} else {
			showmessage('failed_to_delete_operation');
		}
	} else {
		showmessage('failed_to_delete_operation');
	}
} elseif($_GET['op'] == 'movetodos') {//todos移位
	$old_todolists_ids = empty($_GET['old_todolists_ids'])?'':$_GET['old_todolists_ids'];
	$new_todolists_ids = empty($_GET['new_todolists_ids'])?'':$_GET['new_todolists_ids'];
	if(!empty($old_todolists_ids) && !empty($new_todolists_ids)) {
		if($old_todolists_ids != $new_todolists_ids) {
			$arr_old_todolists_ids = explode(',', $old_todolists_ids);
			$arr_new_todolists_ids = explode(',', $new_todolists_ids);
			$arr_length = count($arr_new_todolists_ids);
			for ($i = 0; $i < count($arr_new_todolists_ids); $i++) {
				$arr_new_todolists_ids[$i] = intval($arr_new_todolists_ids[$i]);
				updatetable('todos', array('orderid'=>$arr_length), array('project_id'=>$project_id,'todos_id'=>$arr_new_todolists_ids[$i]));
				$arr_length--;
			}
		}
	}
	showmessage('do_success',"group.php?do=project&project_id={$project_id}");
} elseif($_GET['op'] == 'movetodoslist') {//todoslist移位
	$old_todos_ids = empty($_GET['old_todos_ids'])?'':$_GET['old_todos_ids'];
	$new_todos_ids = empty($_GET['new_todos_ids'])?'':$_GET['new_todos_ids'];
	if(!empty($old_todos_ids) && !empty($new_todos_ids)) {
		if($old_todos_ids != $new_todos_ids) {
			$arr_old_todos_ids = explode(',', $old_todos_ids);
			$arr_new_todos_ids = explode(',', $new_todos_ids);
			$arr_length = count($arr_new_todos_ids);
			for ($i = 0; $i < count($arr_new_todos_ids); $i++) {
				$arr_new_todos_ids[$i] = intval($arr_new_todos_ids[$i]);
				updatetable('todoslist', array('orderid'=>$arr_length), array('project_id'=>$project_id,'todoslist_id'=>$arr_new_todos_ids[$i]));
				$arr_length--;
			}
		}
	}
	showmessage('do_success',"group.php?do=project&project_id={$project_id}");
}
include_once template("cp_todos");

?>