<?php
/*
	[TEAMDOTA] (C) 2012 wanghui
	$Id: open_document.php 2012-07-26 09:59Z duty $
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

if($a == 'document_list') {//获取文档列表，获取前50条
	$limitnumber = 30;
	$listdocument = array();
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('document')." WHERE `project_id`='{$project_id}' AND `status`=0 ORDER BY uptime DESC LIMIT $limitnumber");
	while ($value = $_SGLOBAL['db']->fetch_array($query)) {
		$post_num = 0;
		if($value['discussion_id']){
			$query_discussion = $_SGLOBAL['db']->query("SELECT post_num FROM ".tname('discussion')." WHERE discussion_id='{$value[discussion_id]}' LIMIT 1");
			if($discussion = $_SGLOBAL['db']->fetch_array($query_discussion)) {
				$post_num = $discussion['post_num'];
			}
		}
		//$value['uptime'] = sgmdate('Y-m-d H:i:s', $value['uptime'],0);
		$listdocument[] = array(
			'id'=>$limitnumber,
			'documentid'=>$value['document_id'],
			'projectid'=>$value['project_id'],
			'uid'=>$value['uid'],
			'subject'=>$value['name'],
			'message'=>$value['description'],
			'author'=>$value['author'],
			'timeline'=>$value['uptime'],
			'post_num'=>$post_num
		);
		$limitnumber--;
	}
	open_showmessage($_SGLOBAL['open_errorinfo'][0],'', $listdocument);
	
} elseif($a == 'document_comment_list') {//获取文档讨论列表，获取前50条
	$document_id = empty($_POST['documentid'])?0:intval($_POST['documentid']);
	if(empty($document_id)) {
		open_showmessage($_SGLOBAL['open_errorinfo'][1001]);
	}
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('document')." WHERE document_id='{$document_id}' LIMIT 1");
	if(!$document = $_SGLOBAL['db']->fetch_array($query)) {
		open_showmessage($_SGLOBAL['open_errorinfo'][1001]);
	}
	if($project_id != $document['project_id']){
		open_showmessage($_SGLOBAL['open_errorinfo'][1001]);
	}
	$list = array();
	if($document['discussion_id']){
		$list = array();
		$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('post')." WHERE discussion_id='{$document[discussion_id]}' ORDER BY logtime DESC LIMIT 50");
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
} elseif($a == 'send_comment') {//添加文档讨论
	include_once(S_ROOT.'./source/function_cp.php');
	$document_id = empty($_POST['documentid'])?0:intval($_POST['documentid']);
	if(empty($document_id)) {
		open_showmessage($_SGLOBAL['open_errorinfo'][1001]);
	}
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('document')." WHERE document_id='{$document_id}' LIMIT 1");
	if(!$document = $_SGLOBAL['db']->fetch_array($query)) {
		open_showmessage($_SGLOBAL['open_errorinfo'][1001]);
	}
	if($project_id != $document['project_id']){
		open_showmessage($_SGLOBAL['open_errorinfo'][1001]);
	}
	if($document['status'] != 0) {
		open_showmessage($_SGLOBAL['open_errorinfo'][1001]);
	}
	$discussion_id = $document['discussion_id'];
	$discussion_subject = $document['name'];
	if(empty($discussion_id)) {
		//添加主题
		$setarrdiscussion['group_id'] = $document['group_id'];
		$setarrdiscussion['subject'] = $document['name'];
		$setarrdiscussion['message'] = $document['description'];
		$setarrdiscussion['navidescription'] = getstr($document['description'], 200, 1, 1 , 0 , 0 , -1);
		$setarrdiscussion['lastpost'] = $_SGLOBAL['timestamp'];
		$setarrdiscussion['project_id'] = $project_id;
		$setarrdiscussion['uid'] = $document['uid'];
		$setarrdiscussion['author'] = $document['author'];
		$setarrdiscussion['logtime'] = $_SGLOBAL['timestamp'];
		$setarrdiscussion['useip'] = $document['useip'];
		$setarrdiscussion['othertype'] = 1;
		$setarrdiscussion['otherid'] = $document_id;
		$discussion_id = inserttable('discussion', $setarrdiscussion, 1);
		if($discussion_id) {
			//更新统计数据
			$_SGLOBAL['db']->query("UPDATE ".tname('document')." SET discussion_id='{$discussion_id}' WHERE document_id='{$document_id}'");
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
			notification_add('post', 'create',  $project_id, $manageproject['name'], 'documentid', $document_id, cplang('notification_document_post_create'), array('subject' => $discussion_subject));
			//更新统计数据
			$_SGLOBAL['db']->query("UPDATE ".tname('discussion')." SET post_num=post_num+1,navidescription='{$summay}',lastpost='{$_SGLOBAL[timestamp]}',lastposter='{$setarr[author]}' WHERE discussion_id='$discussion_id'");
		}
		open_showmessage($_SGLOBAL['open_errorinfo'][0]);
	} else {
		open_showmessage($_SGLOBAL['open_errorinfo'][1000]);
	}
} elseif($a == 'add_document') {//添加文档
	include_once(S_ROOT.'./source/function_cp.php');
	$subject = getstr($_POST['subject'], 80, 1, 1, 1);
	if(strlen($subject) < 1) open_showmessage($_SGLOBAL['open_errorinfo'][1000]);
	$_POST['message'] = checkhtml($_POST['message']);
	$_POST['message'] = getstr($_POST['message'], 0, 1, 0, 1, 0, 1);
	$_POST['message'] = preg_replace("/\<div\>\<\/div\>/i", '', $_POST['message']);	
	$message = $_POST['message'];
	$message = addslashes($message);
	
	$setarr['name'] = $subject;
	$setarr['description'] = $message;
	$setarr['uptime'] = $_SGLOBAL['timestamp'];
	$setarr['group_id'] = $group['group_id'];
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
	open_showmessage($_SGLOBAL['open_errorinfo'][0]);
}
?>