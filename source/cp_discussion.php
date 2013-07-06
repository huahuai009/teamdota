<?php
/*
	[TEAMDOTA] (C) 2012 wanghui
	$Id: cp_discussion.php 2012-03-31 09:59Z duty $
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
if(submitcheck('discussionsubmit')) {
	//检查信息
	$discussion_id = empty($_POST['discussion_id'])?0:intval($_POST['discussion_id']);
	$discussion = array();
	if($discussion_id) {
		$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('discussion')." WHERE discussion_id='$discussion_id'");
		$discussion = $_SGLOBAL['db']->fetch_array($query);
		if(!empty($discussion)) {
			if($project_id != $discussion['project_id']){
				showmessage('failed_to_operation');
			}
			//判断是否创建者
			if(!check_project_manage($manageproject['uid'],$discussion['uid'])) {
				showmessage('failed_to_operation');
			}
			if($discussion['status'] != 0) {
				showmessage('failed_to_operation');
			}
		} else {
			showmessage('failed_to_operation');
		}
	}
	
	$subject = getstr($_POST['subject'], 80, 1, 1, 1);
	if(strlen($subject) < 2) showmessage('discussion_subject_error');
	$_POST['message'] = checkhtml($_POST['message']);
	$_POST['message'] = getstr($_POST['message'], 0, 1, 0, 1, 0, 1);
	$_POST['message'] = preg_replace("/\<div\>\<\/div\>/i", '', $_POST['message']);	
	$message = $_POST['message'];
	$message = addslashes($message);
	//摘要
	$summay = getstr($message, 200, 1, 1 , 0 , 0 , -1);
	$setarr = array(
		'subject' => $subject,
		'message' => $message,
		'navidescription' => $summay,
		'lastpost' => $_SGLOBAL['timestamp']
	);
	if(empty($discussion_id)) {
		$setarr['group_id'] = $groupid;
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
						inserttable('file', array('file_id' => $filesvalue['file_id'],'group_id' => $filesvalue['group_id'],'project_id' => $filesvalue['project_id'],'uid' => $filesvalue['uid'],'filename' => $filesvalue['filename'],'fileurl' => $filesvalue['fileurl'],'logtime' => $filesvalue['logtime'],'author' => $filesvalue['author'],'discussion_id' => $discussion_id,'useip' => $filesvalue['useip'],'type' => $filesvalue['type'],'size' => $filesvalue['size'],'post_id' => $filesvalue['post_id'],'remote' => $filesvalue['remote'],'width' => $filesvalue['width'],'height' => $filesvalue['height'],'invisible' => $filesvalue['invisible'],'isimage' => $filesvalue['isimage'],'filetype' => $filesvalue['filetype'],'thumb' => $filesvalue['thumb']), 1);
						$attachsize += intval($filesvalue['size']);
						$file_num++;
					}
					if($file_num) {
						$_SGLOBAL['db']->query("UPDATE ".tname('project')." SET `file_num`=`file_num`+'{$file_num}',attachsize=attachsize+'{$attachsize}' WHERE project_id='{$project_id}'");
						$_SGLOBAL['db']->query("UPDATE ".tname('group')." SET attachsize=attachsize+'{$attachsize}' WHERE group_id='{$group[group_id]}'");
					}
				}
			}
			//给选中的成员发送邮件
			notice_discussion_add($project_id, $discussion_id, 0, $_POST['message_subscribers']);
			//更新统计数据
			$_SGLOBAL['db']->query("UPDATE ".tname('project')." SET discussion_num=discussion_num+1 WHERE project_id='$project_id'");
			
		}
	} else {
		updatetable('discussion', $setarr, array('discussion_id'=>$discussion_id));
		if($discussion['subject'] != $setarr['subject'] || $discussion['message'] != $setarr['message']) {
			//添加事件
			notification_add('discussion', 'update',  $project_id, $manageproject['name'], 'discussionid', $discussion_id, cplang('notification_discussion_update'), array('subject' => $setarr['subject']));
		}
		//获取上传的图片
		if(!empty($_POST['fileids'])) {
			$fileids = explode(',', $_POST['fileids']);
			foreach($fileids as $key => $value) {
				$value = intval($value);
				if($value > 0) {
					$fileidsarr[$value] = $value;
				}
			}
			if(is_array($fileidsarr)) {
				//获取所有的主题附件
				$notfileidsarr = array();
				$queryfile = $_SGLOBAL['db']->query("SELECT * FROM ".tname('file')." WHERE discussion_id ='{$discussion_id}' AND post_id=0");
				while ($dfvalue = $_SGLOBAL['db']->fetch_array($queryfile)) {
					if(isset($fileidsarr[$dfvalue['file_id']])){
						unset ($fileidsarr[$dfvalue['file_id']]);//去除已存在的
					} else {
						$notfileidsarr[] = $dfvalue['file_id'];//记录要删除的
					}
				}
				if(is_array($notfileidsarr)) {//删除废弃的
					include_once(S_ROOT.'./source/function_delete.php');
					deletediscussionfiles($project_id,$discussion_id,$notfileidsarr);
				}
				if(is_array($fileidsarr)) {//新增的
					$attachsize = 0;
					$file_num = 0;
					$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('file_short')." WHERE file_id IN (".simplode($fileidsarr).") AND uid='$_SGLOBAL[supe_uid]'");
					while ($filesvalue = $_SGLOBAL['db']->fetch_array($query)) {
						inserttable('file', array('file_id' => $filesvalue['file_id'],'group_id' => $filesvalue['group_id'],'project_id' => $filesvalue['project_id'],'uid' => $filesvalue['uid'],'filename' => $filesvalue['filename'],'fileurl' => $filesvalue['fileurl'],'logtime' => $filesvalue['logtime'],'author' => $filesvalue['author'],'discussion_id' => $discussion_id,'useip' => $filesvalue['useip'],'type' => $filesvalue['type'],'size' => $filesvalue['size'],'post_id' => $filesvalue['post_id'],'remote' => $filesvalue['remote'],'width' => $filesvalue['width'],'height' => $filesvalue['height'],'invisible' => $filesvalue['invisible'],'isimage' => $filesvalue['isimage'],'filetype' => $filesvalue['filetype'],'thumb' => $filesvalue['thumb']), 1);
						$attachsize += intval($filesvalue['size']);
						$file_num++;
					}
					if($file_num) {
						$_SGLOBAL['db']->query("UPDATE ".tname('project')." SET `file_num`=`file_num`+'{$file_num}',attachsize=attachsize+'{$attachsize}' WHERE project_id='{$project_id}'");
						$_SGLOBAL['db']->query("UPDATE ".tname('group')." SET attachsize=attachsize+'{$attachsize}' WHERE group_id='{$group[group_id]}'");
					}
				}
			}
		} else {
			//获取所有的主题附件
			$notfileidsarr = array();
			$queryfile = $_SGLOBAL['db']->query("SELECT * FROM ".tname('file')." WHERE discussion_id ='{$discussion_id}' AND post_id=0");
			while ($dfvalue = $_SGLOBAL['db']->fetch_array($queryfile)) {
				$notfileidsarr[] = $dfvalue['file_id'];//记录要删除的
			}
			if(is_array($notfileidsarr)) {//删除废弃的
				include_once(S_ROOT.'./source/function_delete.php');
				deletediscussionfiles($project_id,$discussion_id,$notfileidsarr);
			}
		}
	}
	showmessage('do_success',"group.php?do=project&project_id={$project_id}");
}
//检查信息
$discussion_id = empty($_GET['discussion_id'])?0:intval($_GET['discussion_id']);
$op = empty($_GET['op'])?'':$_GET['op'];

$discussion = array();
if($discussion_id) {
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('discussion')." WHERE discussion_id='$discussion_id'");
	$discussion = $_SGLOBAL['db']->fetch_array($query);
}
if(!empty($discussion)) {
	if($project_id != $discussion['project_id']){
		showmessage('project_not_allowed_to_visit');
	}
}
//回帖编辑
if($_GET['op'] == 'edit') {
	if(!$discussion) {
		showmessage('project_not_allowed_to_visit');
	}
	//判断是否创建者
	if(!check_project_manage($manageproject['uid'],$discussion['uid'])) {
		showmessage('failed_to_operation');
	}
	$discussion_page = empty($_GET['discussion_page'])?1:intval($_GET['discussion_page']);
	//获取附件
	$listdiscussionpic = array();
	$listdiscussionfile = array();
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('file')." WHERE discussion_id='{$discussion_id}' AND post_id=0 ORDER BY logtime DESC");
	while ($row = $_SGLOBAL['db']->fetch_array($query)) {
		if($row['isimage']) {
			$thumbwh = get_thumbwh($row['width'],$row['height']);
			$listdiscussionpic[] = array('file_id'=>$row['file_id'],'project_id'=>$row['project_id'],'discussion_id'=>$row['discussion_id'],'filename'=>$row['filename'],'fileurl'=>pic_get($row['fileurl'], $row['thumb'], $row['remote']),'width'=>$row['width'],'height'=>$row['height'],'thumbwidth'=>$thumbwh['thumbwidth'],'thumbheight'=>$thumbwh['thumbheight']);
		}else {
			$listdiscussionfile[] = array('file_id'=>$row['file_id'],'project_id'=>$row['project_id'],'discussion_id'=>$row['discussion_id'],'filename'=>$row['filename'],'fileurl'=>file_icon_big($row['type']));
		}
	}
} elseif($_GET['op'] == 'delete') {//送入回收站
	if($discussion_id) {
		include_once(S_ROOT.'./source/function_delete.php');
		if(trash_can_discussions($project_id,$discussion_id)) {
			showmessage('do_success',"group.php?do=project&project_id={$project_id}",0);
		} else {
			showmessage('failed_to_delete_operation');
		}
	} else {
		showmessage('failed_to_delete_operation');
	}
} elseif($_GET['op'] == 'restored') {//恢复
	if($discussion_id) {
		include_once(S_ROOT.'./source/function_delete.php');
		if(restored_discussions($project_id,$discussion_id)) {
			showmessage('do_success',"group.php?do=discussion&discussion_id={$discussion_id}&project_id={$project_id}",0);
		} else {
			showmessage('failed_to_restored_operation');
		}
	} else {
		showmessage('failed_to_restored_operation');
	}
} elseif($_GET['op'] == 'realdelete') {//真正删除
	if($discussion_id) {
		include_once(S_ROOT.'./source/function_delete.php');
		if(deletediscussions($project_id,$discussion_id)) {
			showmessage('do_success',"group.php?do=project&project_id={$project_id}",0);
		} else {
			showmessage('failed_to_delete_operation');
		}
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

include_once template("cp_discussion");

?>