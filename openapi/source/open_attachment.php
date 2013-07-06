<?php
/*
	[TEAMDOTA] (C) 2012 wanghui
	$Id: open_attachment.php 2012-07-26 09:59Z duty $
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

if($a == 'attachment_list') {//获取附件列表，获取前50条
	$limitnumber = 30;
	$listattachment = array();
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('file')." WHERE `project_id`='{$project_id}' AND `status`=0 ORDER BY logtime DESC LIMIT $limitnumber");
	while ($value = $_SGLOBAL['db']->fetch_array($query)) {
		$post_num = 0;
		if($value['discussion_id']){
			$query_discussion = $_SGLOBAL['db']->query("SELECT post_num FROM ".tname('discussion')." WHERE discussion_id='{$value[discussion_id]}' LIMIT 1");
			if($discussion = $_SGLOBAL['db']->fetch_array($query_discussion)) {
				$post_num = $discussion['post_num'];
			}
		}
		if($value['isimage']) {
			$value['icon'] =  $_SCONFIG['siteallurl'].pic_get($value['fileurl'], $value['thumb'], $value['remote']);
			$value['fileurl'] =  $_SCONFIG['siteallurl'].pic_get($value['fileurl'], '', $value['remote']);
		} else {
			$value['icon'] =  $_SCONFIG['siteallurl'].file_icon_jumbo($value['type']);
		}
		$listattachment[] = array(
			'id'=>$limitnumber,
			'attachmentid'=>$value['file_id'],
			'projectid'=>$value['project_id'],
			'uid'=>$value['uid'],
			'filename'=>$value['filename'],
			'icon'=>$value['icon'],
			'fileurl'=>$value['fileurl'],
			'author'=>$value['author'],
			'timeline'=>$value['logtime'],
			'isimage'=>$value['isimage'],
			'type'=>$value['type'],
			'discussionid'=>$value['discussion_id'],
			'post_num'=>$post_num
		);
		$limitnumber--;
	}
	open_showmessage($_SGLOBAL['open_errorinfo'][0],'', $listattachment);
	
} elseif($a == 'attachment_comment_list') {//获取附件讨论列表，获取前50条
	$file_id = empty($_POST['attachmentid'])?0:intval($_POST['attachmentid']);
	if(empty($file_id)) {
		open_showmessage($_SGLOBAL['open_errorinfo'][1001]);
	}
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('file')." WHERE file_id='{$file_id}' LIMIT 1");
	if(!$attachment = $_SGLOBAL['db']->fetch_array($query)) {
		open_showmessage($_SGLOBAL['open_errorinfo'][1001]);
	}
	if($project_id != $attachment['project_id']){
		open_showmessage($_SGLOBAL['open_errorinfo'][1001]);
	}
	$list = array();
	if($attachment['discussion_id']){
		$list = array();
		$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('post')." WHERE discussion_id='{$attachment[discussion_id]}' ORDER BY logtime DESC LIMIT 50");
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
} elseif($a == 'send_comment') {//添加附件讨论
	include_once(S_ROOT.'./source/function_cp.php');
	$file_id = empty($_POST['attachmentid'])?0:intval($_POST['attachmentid']);
	if(empty($file_id)) {
		open_showmessage($_SGLOBAL['open_errorinfo'][1001]);
	}
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('file')." WHERE file_id='{$file_id}' LIMIT 1");
	if(!$attachment = $_SGLOBAL['db']->fetch_array($query)) {
		open_showmessage($_SGLOBAL['open_errorinfo'][1001]);
	}
	if($project_id != $attachment['project_id']){
		open_showmessage($_SGLOBAL['open_errorinfo'][1001]);
	}
	if($attachment['status'] != 0) {
		open_showmessage($_SGLOBAL['open_errorinfo'][1001]);
	}
	$discussion_id = $attachment['discussion_id'];
	$discussion_subject = $attachment['filename'];
	if(empty($discussion_id)) {
		//添加主题
		$discussion_subject = $file['filename'];
		$setarrdiscussion['group_id'] = $file['group_id'];
		$setarrdiscussion['subject'] = $file['filename'];
		$setarrdiscussion['message'] = $file['filename'];
		$setarrdiscussion['navidescription'] = getstr($file['filename'], 200, 1, 1 , 0 , 0 , -1);
		$setarrdiscussion['lastpost'] = $_SGLOBAL['timestamp'];
		$setarrdiscussion['project_id'] = $project_id;
		$setarrdiscussion['uid'] = $file['uid'];
		$setarrdiscussion['author'] = $file['author'];
		$setarrdiscussion['logtime'] = $_SGLOBAL['timestamp'];
		$setarrdiscussion['useip'] = $file['useip'];
		$setarrdiscussion['othertype'] = 2;
		$setarrdiscussion['otherid'] = $file_id;
		$discussion_id = inserttable('discussion', $setarrdiscussion, 1);
		if($discussion_id) {
			//更新统计数据
			$_SGLOBAL['db']->query("UPDATE ".tname('file')." SET discussion_id='{$discussion_id}' WHERE file_id='{$file_id}'");
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
			notification_add('post', 'create',  $project_id, $manageproject['name'], 'attachmentid', $file_id, cplang('notification_attachment_post_create'), array('subject' => $discussion_subject));
			//更新统计数据
			$_SGLOBAL['db']->query("UPDATE ".tname('discussion')." SET post_num=post_num+1,navidescription='{$summay}',lastpost='{$_SGLOBAL[timestamp]}',lastposter='{$setarr[author]}' WHERE discussion_id='$discussion_id'");
		}
		open_showmessage($_SGLOBAL['open_errorinfo'][0]);
	} else {
		open_showmessage($_SGLOBAL['open_errorinfo'][1000]);
	}
}
?>