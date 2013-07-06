<?php
/*
	[TEAMDOTA] (C) 2012 wanghui
	$Id: cp_document.php 2012-03-31 09:59Z duty $
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
if(submitcheck('documentsubmit')) {
	//检查信息
	$autosave = empty($_GET['autosave']) ? 0 : intval($_GET['autosave']);
	$document_id = empty($_POST['document_id'])?0:intval($_POST['document_id']);
	$document = array();
	if(empty($document_id)) {
		showmessage('failed_to_operation');
	}
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('document')." WHERE document_id='$document_id'");
	$document = $_SGLOBAL['db']->fetch_array($query);
	if(!empty($document)) {
		if($project_id != $document['project_id']){
			showmessage('failed_to_operation');
		}
		//判断是否创建者
		if(!check_project_manage($manageproject['uid'],$document['uid'])) {
			showmessage('failed_to_operation');
		}
		if($document['status'] != 0) {
			showmessage('failed_to_operation');
		}
	} else {
		showmessage('failed_to_operation');
	}
	
	
	$name = getstr($_POST['name'], 80, 1, 1, 1);
	if(strlen($name) < 2) showmessage('document_name_error');
	$_POST['description'] = checkhtml($_POST['description']);
	$_POST['description'] = getstr($_POST['description'], 0, 1, 0, 1, 0, 1);
	$_POST['description'] = preg_replace("/\<div\>\<\/div\>/i", '', $_POST['description']);	
	$description = $_POST['description'];
	$description = addslashes($description);

	$setarr = array(
		'name' => $name,
		'description' => $description,
		'uptime' => $_SGLOBAL['timestamp']
	);
	if($document['name'] != $setarr['name'] || $document['description'] != $setarr['description']) {
		updatetable('document', $setarr, array('document_id'=>$document_id));
		if(!empty($document['discussion_id'])){
			$summay = getstr($description, 200, 1, 1 , 0 , 0 , -1);
			updatetable('discussion', array('subject' => $name,'message' => $description,'navidescription' => $summay,'lastpost' => $_SGLOBAL['timestamp']
	), array('discussion_id'=>$document['discussion_id']));
		}
	}
	if(empty($autosave)) {
		//添加事件
		notification_add('document', 'update',  $project_id, $manageproject['name'], 'documentid', $document_id, cplang('notification_document_update'), array('subject' => $setarr['name']));
	}
	showmessage('do_success',"group.php?do=project&project_id={$project_id}");
}
//检查信息
$document_id = empty($_GET['document_id'])?0:intval($_GET['document_id']);
$op = empty($_GET['op'])?'':$_GET['op'];

$document = array();
if($document_id) {
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('document')." WHERE document_id='$document_id'");
	$document = $_SGLOBAL['db']->fetch_array($query);
}
if(!empty($document)) {
	if($project_id != $document['project_id']){
		showmessage('project_not_allowed_to_visit');
	}
}
//添加
if($_GET['op'] == 'create' && empty($document_id)) {
	$setarr['name'] = '未知文档';
	$setarr['description'] = '';
	$setarr['uptime'] = $_SGLOBAL['timestamp'];
	$setarr['group_id'] = $groupid;
	$setarr['project_id'] = $project_id;
	$setarr['uid'] = $_SGLOBAL['supe_uid'];
	$setarr['author'] = $_SGLOBAL['member']['fullname'];
	$setarr['logtime'] = $_SGLOBAL['timestamp'];
	$setarr['useip'] = getonlineip();
	$document_id = inserttable('document', $setarr, 1);
	if($document_id) {
		//更新统计数据
		$_SGLOBAL['db']->query("UPDATE ".tname('project')." SET document_num=document_num+1 WHERE project_id='{$project_id}'");
	}

	include_once template("cp_document");
} elseif($_GET['op'] == 'edit') {
	if(!$document) {
		showmessage('project_not_allowed_to_visit');
	}
	//判断是否创建者
	if(!check_project_manage($manageproject['uid'],$document['uid'])) {
		showmessage('failed_to_operation');
	}
	$document_page = empty($_GET['document_page'])?1:intval($_GET['document_page']);
	
	include_once template("cp_document");
} elseif($_GET['op'] == 'delete') {//送入回收站
	if($document_id) {
		include_once(S_ROOT.'./source/function_delete.php');
		if(trash_can_documents($project_id,$document_id)) {
			showmessage('do_success',"group.php?do=project&project_id={$project_id}",0);
		} else {
			showmessage('failed_to_delete_operation');
		}
	} else {
		showmessage('failed_to_delete_operation');
	}
} elseif($_GET['op'] == 'restored') {//恢复
	if($document_id) {
		include_once(S_ROOT.'./source/function_delete.php');
		if(restored_documents($project_id,$document_id)) {
			showmessage('do_success',"group.php?project_id={$project_id}&do=document&document_id={$document_id}",0);
		} else {
			showmessage('failed_to_restored_operation');
		}
	} else {
		showmessage('failed_to_restored_operation');
	}
} elseif($_GET['op'] == 'realdelete') {//真正删除
	if($document_id) {
		include_once(S_ROOT.'./source/function_delete.php');
		if(deletedocuments($project_id,$document_id)) {
			showmessage('do_success',"group.php?do=project&project_id={$project_id}",0);
		} else {
			showmessage('failed_to_delete_operation');
		}
	} else {
		showmessage('failed_to_delete_operation');
	}
}
?>