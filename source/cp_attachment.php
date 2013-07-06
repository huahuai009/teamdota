<?php
/*
	[TEAMDOTA] (C) 2012 wanghui
	$Id: cp_attachment.php 2012-03-31 09:59Z duty $
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
if(submitcheck('attachmentsubmit')) {
	//获取上传的图片
	if(!empty($_POST['fileids'])) {
		$fileids = explode(',', $_POST['fileids']);
		foreach($fileids as $key => $value) {
			$value = intval($value);
			if($value > 0) {
				$fileidsarr[] = $value;
			}
		}
		if(is_array($fileidsarr)) {
			$attachsize = 0;
			$file_num = 0;
			$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('file_short')." WHERE file_id IN (".simplode($fileidsarr).") AND uid='$_SGLOBAL[supe_uid]'");
			while ($filesvalue = $_SGLOBAL['db']->fetch_array($query)) {
				inserttable('file', array('file_id' => $filesvalue['file_id'],'group_id' => $filesvalue['group_id'],'project_id' => $filesvalue['project_id'],'uid' => $filesvalue['uid'],'filename' => $filesvalue['filename'],'fileurl' => $filesvalue['fileurl'],'logtime' => $filesvalue['logtime'],'author' => $filesvalue['author'],'discussion_id' =>  $filesvalue['discussion_id'],'useip' => $filesvalue['useip'],'type' => $filesvalue['type'],'size' => $filesvalue['size'],'post_id' => $filesvalue['post_id'],'remote' => $filesvalue['remote'],'width' => $filesvalue['width'],'height' => $filesvalue['height'],'invisible' => $filesvalue['invisible'],'isimage' => $filesvalue['isimage'],'filetype' => $filesvalue['filetype'],'thumb' => $filesvalue['thumb']), 1);
				//添加事件
				notification_add('attachment', 'create',  $project_id, $manageproject['name'], 'attachmentid', $filesvalue['file_id'], cplang('notification_attachment_create'), array('subject' => $filesvalue['filename']));
				//给选中的成员发送邮件
				notice_attachment_add($project_id, $filesvalue['file_id'], $_POST['message_subscribers']);
				$attachsize += intval($filesvalue['size']);
				$file_num++;
			}
			if($file_num) {
				$_SGLOBAL['db']->query("UPDATE ".tname('project')." SET `file_num`=`file_num`+'{$file_num}',attachsize=attachsize+'{$attachsize}' WHERE project_id='{$project_id}'");
				$_SGLOBAL['db']->query("UPDATE ".tname('group')." SET attachsize=attachsize+'{$attachsize}' WHERE group_id='{$group[group_id]}'");
			}
		}
	}
	showmessage('do_success',"group.php?do=project&project_id={$project_id}");
}

//检查信息
$file_id = empty($_GET['file_id'])?0:intval($_GET['file_id']);
$op = empty($_GET['op'])?'':$_GET['op'];

$attachment = array();
if($file_id) {
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('file')." WHERE file_id='$file_id'");
	$attachment = $_SGLOBAL['db']->fetch_array($query);
}
if(!empty($attachment)) {
	if($project_id != $attachment['project_id']){
		showmessage('project_not_allowed_to_visit');
	}
}

if($_GET['op'] == 'delete') {//送入回收站
	include_once(S_ROOT.'./source/function_delete.php');
	if(trash_can_files($project_id,$file_id)) {
		showmessage('do_success',"group.php?do=project&project_id={$project_id}",0);
	} else {
		showmessage('failed_to_delete_operation');
	}
} elseif($_GET['op'] == 'restored') {//恢复
	include_once(S_ROOT.'./source/function_delete.php');
	if(restored_files($project_id,$file_id)) {
		showmessage('do_success',"group.php?project_id={$project_id}&do=attachment&file_id={$file_id}",0);
	} else {
		showmessage('failed_to_restored_operation');
	}
} elseif($_GET['op'] == 'realdelete') {//真正删除
	include_once(S_ROOT.'./source/function_delete.php');
	if(deletefiles($project_id,$file_id)) {
		showmessage('do_success',"group.php?do=project&project_id={$project_id}",0);
	} else {
		showmessage('failed_to_delete_operation');
	}
} else {
	$query = $_SGLOBAL['db']->query("SELECT m.fullname,m.uid FROM ".tname('project_member')." pm,".tname('member')." m WHERE pm.project_id='{$project_id}' AND m.isactive=0 AND pm.uid=m.uid  ORDER BY pm.id ASC");
	$members = array();
	$members_num = 0;
	while($value = $_SGLOBAL['db']->fetch_array($query)) {
		if($value['uid'] != $_SGLOBAL['supe_uid']) {
			$members[] = $value;
			$members_num ++;
		}
	}
}

include_once template("cp_attachment");
?>