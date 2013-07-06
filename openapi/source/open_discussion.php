<?php
/*
	[TEAMDOTA] (C) 2012 wanghui
	$Id: open_discussion.php 2012-07-25 09:59Z duty $
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

if($a == 'discussion_list') {//获取主题列表，获取前50条
	$limitnumber = 50;
	$listdiscussion = array();
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('discussion')." WHERE `project_id`='{$project_id}' AND `status`=0 ORDER BY lastpost DESC LIMIT $limitnumber");
	while ($value = $_SGLOBAL['db']->fetch_array($query)) {
		//$value['lastpost'] = sgmdate('Y-m-d H:i:s', $value['lastpost'],0);
		$listdiscussion[] = array(
			'id'=>$limitnumber,
			'discussionid'=>$value['discussion_id'],
			'projectid'=>$value['project_id'],
			'uid'=>$value['uid'],
			'subject'=>$value['subject'],
			'message'=>$value['message'],
			'author'=>$value['author'],
			'desp'=>$value['navidescription'],
			'timeline'=>$value['lastpost'],
			'post_num'=>$value['post_num']
		);
		$limitnumber--;
	}
	open_showmessage($_SGLOBAL['open_errorinfo'][0],'', $listdiscussion);
	
} elseif($a == 'discussion_attachment_list') {//获取主题附件
	$discussion_id = empty($_POST['discussionid'])?0:intval($_POST['discussionid']);
	if(empty($discussion_id)) {
		open_showmessage($_SGLOBAL['open_errorinfo'][1001]);
	}
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('discussion')." WHERE discussion_id='{$discussion_id}' LIMIT 1");
	if(!$discussion = $_SGLOBAL['db']->fetch_array($query)) {
		open_showmessage($_SGLOBAL['open_errorinfo'][1001]);
	}
	if($project_id != $discussion['project_id']){
		open_showmessage($_SGLOBAL['open_errorinfo'][1001]);
	}
	$list = array();
	$queryfile = $_SGLOBAL['db']->query("SELECT * FROM ".tname('file')." WHERE discussion_id='{$discussion_id}' AND post_id=0 ORDER BY logtime DESC");
	while ($row = $_SGLOBAL['db']->fetch_array($queryfile)) {
		if($row['isimage']) {
			$list['pic'][] = array('attachmentid'=>$row['file_id'],'discussionid'=>$row['discussion_id'],'filename'=>$row['filename'],'icon'=>$_SCONFIG['siteallurl'].pic_get($row['fileurl'], $row['thumb'], $row['remote']));
		}else {
			$list['file'][] = array('attachmentid'=>$row['file_id'],'discussionid'=>$row['discussion_id'],'filename'=>$row['filename'],'icon'=>$_SCONFIG['siteallurl'].file_icon_big($row['type']));
		}
	}
	open_showmessage($_SGLOBAL['open_errorinfo'][0],'', $list);
} elseif($a == 'discussion_comment_list') {//获取主题讨论列表，获取前50条
	$discussion_id = empty($_POST['discussionid'])?0:intval($_POST['discussionid']);
	if(empty($discussion_id)) {
		open_showmessage($_SGLOBAL['open_errorinfo'][1001]);
	}
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('discussion')." WHERE discussion_id='{$discussion_id}' LIMIT 1");
	if(!$discussion = $_SGLOBAL['db']->fetch_array($query)) {
		open_showmessage($_SGLOBAL['open_errorinfo'][1001]);
	}
	if($project_id != $discussion['project_id']){
		open_showmessage($_SGLOBAL['open_errorinfo'][1001]);
	}
	$list = array();
	$k = 0;
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('post')." WHERE discussion_id='{$discussion_id}' ORDER BY logtime DESC LIMIT 50");
	while ($value = $_SGLOBAL['db']->fetch_array($query)) {
		$list[$k] = array(
			'postid'=>$value['post_id'],
			'discussionid'=>$value['discussion_id'],
			'projectid'=>$value['project_id'],
			'uid'=>$value['uid'],
			'message'=>$value['message'],
			'author'=>$value['author'],
			'timeline'=>$value['logtime']
		);
		//读取评论附件
		$queryfile = $_SGLOBAL['db']->query("SELECT * FROM ".tname('file')." WHERE post_id='{$value[post_id]}' ORDER BY logtime DESC");
		while ($row = $_SGLOBAL['db']->fetch_array($queryfile)) {
			if($row['isimage']) {
				$list[$k]['pic'][] = array('attachmentid'=>$row['file_id'],'discussionid'=>$row['discussion_id'],'postid'=>$row['post_id'],'filename'=>$row['filename'],'icon'=>$_SCONFIG['siteallurl'].pic_get($row['fileurl'], $row['thumb'], $row['remote']));
			} else {
				$list[$k]['file'][] = array('attachmentid'=>$row['file_id'],'discussionid'=>$row['discussion_id'],'postid'=>$row['post_id'],'filename'=>$row['filename'],'icon'=>$_SCONFIG['siteallurl'].file_icon_big($row['type']));
			}
		}
		++$k;
	}
	open_showmessage($_SGLOBAL['open_errorinfo'][0],'', $list);
} elseif($a == 'send_comment') {//添加主题讨论
	include_once(S_ROOT.'./source/function_cp.php');
	$discussion_id = empty($_POST['discussionid'])?0:intval($_POST['discussionid']);
	if(empty($discussion_id)) {
		open_showmessage($_SGLOBAL['open_errorinfo'][1001]);
	}
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('discussion')." WHERE discussion_id='{$discussion_id}' LIMIT 1");
	if(!$discussion = $_SGLOBAL['db']->fetch_array($query)) {
		open_showmessage($_SGLOBAL['open_errorinfo'][1001]);
	}
	if($project_id != $discussion['project_id']){
		open_showmessage($_SGLOBAL['open_errorinfo'][1001]);
	}
	if($discussion['status'] != 0) {
		open_showmessage($_SGLOBAL['open_errorinfo'][1001]);
	}
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
		notification_add('post', 'create',  $project_id, $manageproject['name'], 'discussionid', $discussion_id, cplang('notification_discussion_post_create'), array('subject' => $discussion['subject']));
		//更新统计数据
		$_SGLOBAL['db']->query("UPDATE ".tname('discussion')." SET post_num=post_num+1,navidescription='{$summay}',lastpost='{$_SGLOBAL[timestamp]}',lastposter='{$setarr[author]}' WHERE discussion_id='$discussion_id'");
	}
	open_showmessage($_SGLOBAL['open_errorinfo'][0]);
} elseif($a == 'add_discussion') {//添加主题
	include_once(S_ROOT.'./source/function_cp.php');
	$subject = getstr($_POST['subject'], 80, 1, 1, 1);
	if(strlen($subject) < 1) open_showmessage($_SGLOBAL['open_errorinfo'][1000]);
	$_POST['message'] = checkhtml($_POST['message']);
	$_POST['message'] = getstr($_POST['message'], 0, 1, 0, 1, 0, 1);
	$_POST['message'] = preg_replace("/\<div\>\<\/div\>/i", '', $_POST['message']);	
	$message = $_POST['message'];
	$message = addslashes($message);
	//摘要
	$summay = getstr($message, 200, 1, 1 , 0 , 0 , -1);

	$setarr['subject'] = $subject;
	$setarr['message'] = $message;
	$setarr['navidescription'] = $summay;
	$setarr['lastpost'] = $_SGLOBAL['timestamp'];
	$setarr['group_id'] = $group['group_id'];
	$setarr['project_id'] = $project_id;
	$setarr['uid'] = $_SGLOBAL['supe_uid'];
	$setarr['author'] = $_SGLOBAL['member']['fullname'];
	$setarr['logtime'] = $_SGLOBAL['timestamp'];
	$setarr['useip'] = getonlineip();
	$setarr['lastposter'] = $_SGLOBAL['member']['fullname'];
	$discussion_id = inserttable('discussion', $setarr, 1);
	if($discussion_id) {
		//添加事件
		notification_add('discussion', 'create',  $project_id, $manageproject['name'], 'discussionid', $discussion_id, cplang('notification_discussion_create'), array('subject' => $setarr['subject']));
		//更新统计数据
		$_SGLOBAL['db']->query("UPDATE ".tname('project')." SET discussion_num=discussion_num+1 WHERE project_id='$project_id'");
	}
	open_showmessage($_SGLOBAL['open_errorinfo'][0]);
}
?>