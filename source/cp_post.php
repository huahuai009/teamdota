<?php
/*
	[TEAMDOTA] (C) 2012 wanghui
	$Id: cp_post.php 2012-03-31 09:59Z duty $
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
if(submitcheck('postsubmit')) {
	$_POST['message'] = checkhtml($_POST['message']);
	$_POST['message'] = getstr($_POST['message'], 0, 1, 0, 1, 0, 1);
	$_POST['message'] = preg_replace("/\<div\>\<\/div\>/i", '', $_POST['message']);	
	$message = $_POST['message'];
	$message = addslashes($message);
	if(strlen($message) < 2) showmessage('post_message_error');
	$summay = getstr($message, 100, 1, 1 , 0 , 0 , -1);//摘要
	
	$posttype = empty($_POST['posttype']) ? '' : $_POST['posttype'];
	$post_id = empty($_POST['post_id'])?0:intval($_POST['post_id']);
	$post = array();
	if($post_id) {//编辑
		//检查信息
		$discussion_id = empty($_POST['discussion_id'])?0:intval($_POST['discussion_id']);
		$discussion = array();
		if($discussion_id) {
			$query = $_SGLOBAL['db']->query("SELECT discussion_id,project_id,subject FROM ".tname('discussion')." WHERE discussion_id='$discussion_id'");
			$discussion = $_SGLOBAL['db']->fetch_array($query);
			if(!empty($discussion)) {
				if($project_id != $discussion['project_id']){
					showmessage('failed_to_operation');
				}
				if($discussion['status'] != 0) {
					showmessage('failed_to_operation');
				}
			} else {
				showmessage('failed_to_operation');
			}
		} else {
			showmessage('failed_to_operation');
		}
		$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('post')." WHERE post_id='$post_id'");
		$post = $_SGLOBAL['db']->fetch_array($query);
		if(empty($post)) {
			showmessage('failed_to_operation');
		}else {
			if($project_id != $post['project_id']){
				showmessage('failed_to_operation');
			}
			//判断是否创建者
			if(!check_project_manage($manageproject['uid'],$post['uid'])) {
				showmessage('failed_to_operation');
			}
			$setarr = array(
				'message' => $message
			);
			updatetable('post', $setarr, array('post_id'=>$post_id));
			$_SGLOBAL['db']->query("UPDATE ".tname('discussion')." SET navidescription='{$summay}' WHERE discussion_id='$discussion_id'");
		}
	} else {//添加
		$discussion_id = empty($_POST['discussion_id'])?0:intval($_POST['discussion_id']);
		$document_id = empty($_POST['document_id'])?0:intval($_POST['document_id']);
		$file_id = empty($_POST['file_id'])?0:intval($_POST['file_id']);
		$todos_id = empty($_POST['todos_id'])?0:intval($_POST['todos_id']);
		$todoslist_id = empty($_POST['todoslist_id'])?0:intval($_POST['todoslist_id']);
		$discussion_subject = '';
		if($discussion_id) {//主题已存在
			$discussion = array();
			$query = $_SGLOBAL['db']->query("SELECT discussion_id,project_id,subject FROM ".tname('discussion')." WHERE discussion_id='$discussion_id'");
			$discussion = $_SGLOBAL['db']->fetch_array($query);
			if(!empty($discussion)) {
				if($project_id != $discussion['project_id']){
					showmessage('failed_to_operation');
				}
				if($discussion['status'] != 0) {
					showmessage('failed_to_operation');
				}
				$discussion_subject = $discussion['subject'];
			} else {
				showmessage('failed_to_operation');
			}
		} else {//主题未存在
			if($posttype == 'document') {//文档评论
				$document = array();
				if($document_id) {
					$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('document')." WHERE document_id='$document_id'");
					$document = $_SGLOBAL['db']->fetch_array($query);
					if(!empty($document)) {
						if($project_id != $document['project_id']){
							showmessage('failed_to_operation');
						}
						if($document['status'] != 0) {
							showmessage('failed_to_operation');
						}
					} else {
						showmessage('failed_to_operation');
					}
				} else {
					showmessage('failed_to_operation');
				}
				if(empty($document['discussion_id'])){
					//添加主题
					$discussion_subject = $document['name'];
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
				} else {
					$discussion_id = $document['discussion_id'];
				}
			} elseif($posttype == 'file') {//附件评论
				$file = array();
				if($file_id) {
					$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('file')." WHERE file_id='$file_id'");
					$file = $_SGLOBAL['db']->fetch_array($query);
					if(!empty($file)) {
						if($project_id != $file['project_id']){
							showmessage('failed_to_operation');
						}
						if($file['status'] != 0) {
							showmessage('failed_to_operation');
						}
					} else {
						showmessage('failed_to_operation');
					}
				} else {
					showmessage('failed_to_operation');
				}
				if(empty($file['discussion_id'])){
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
				} else {
					$discussion_id = $file['discussion_id'];
				}
			} elseif($posttype == 'todos') {//待办事宜类型
				$todos = array();
				if($todos_id) {
					$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('todos')." WHERE todos_id='$todos_id'");
					$todos = $_SGLOBAL['db']->fetch_array($query);
					if(!empty($todos)) {
						if($project_id != $todos['project_id']){
							showmessage('failed_to_operation');
						}
						if($todos['status'] == 1) {
							showmessage('failed_to_operation');
						}
					} else {
						showmessage('failed_to_operation');
					}
				} else {
					showmessage('failed_to_operation');
				}
				if(empty($todos['discussion_id'])){
					//添加主题
					$discussion_subject = $todos['subject'];
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
				} else {
					$discussion_id = $todos['discussion_id'];
				}
			} elseif($posttype == 'todoslist') {//待办事宜详细
				$todoslist = array();
				if($todoslist_id) {
					$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('todoslist')." WHERE todoslist_id='$todoslist_id'");
					$todoslist = $_SGLOBAL['db']->fetch_array($query);
					if(!empty($todoslist)) {
						if($project_id != $todoslist['project_id']){
							showmessage('failed_to_operation');
						}
						if($todoslist['status'] == 1) {
							showmessage('failed_to_operation');
						}
					} else {
						showmessage('failed_to_operation');
					}
				} else {
					showmessage('failed_to_operation');
				}
				if(empty($todoslist['discussion_id'])){
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
				} else {
					$discussion_id = $todoslist['discussion_id'];
				}
			} else{
				showmessage('failed_to_operation');
			}
		}
		if($discussion_id) {
			$setarr['message'] = $message;
			$setarr['group_id'] = $groupid;
			$setarr['project_id'] = $project_id;
			$setarr['discussion_id'] = $discussion_id;
			$setarr['uid'] = $_SGLOBAL['supe_uid'];
			$setarr['author'] = $_SGLOBAL['member']['fullname'];
			$setarr['logtime'] = $_SGLOBAL['timestamp'];
			$setarr['useip'] = getonlineip();
			$post_id = inserttable('post', $setarr, 1);
			if($post_id) {
				//添加事件
				if($posttype == 'document') {//文档评论
					notification_add('post', 'create',  $project_id, $manageproject['name'], 'documentid', $document_id, cplang('notification_document_post_create'), array('subject' => $discussion_subject));
				} elseif($posttype == 'file') {//附件评论
					notification_add('post', 'create',  $project_id, $manageproject['name'], 'attachmentid', $file_id, cplang('notification_attachment_post_create'), array('subject' => $discussion_subject));
				} elseif($posttype == 'todos') {//待办事宜类型评论
					notification_add('post', 'create',  $project_id, $manageproject['name'], 'todosid', $todos_id, cplang('notification_todos_post_create'), array('subject' => $discussion_subject));
				} elseif($posttype == 'todoslist') {//待办事宜评论
					notification_add('post', 'create',  $project_id, $manageproject['name'], 'todoslistid', $todoslist_id, cplang('notification_todoslist_post_create'), array('subject' => $discussion_subject));
				} else {
					notification_add('post', 'create',  $project_id, $manageproject['name'], 'discussionid', $discussion_id, cplang('notification_discussion_post_create'), array('subject' => $discussion_subject));
				}				
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
							inserttable('file', array('file_id' => $filesvalue['file_id'],'group_id' => $filesvalue['group_id'],'project_id' => $filesvalue['project_id'],'uid' => $filesvalue['uid'],'filename' => $filesvalue['filename'],'fileurl' => $filesvalue['fileurl'],'logtime' => $filesvalue['logtime'],'author' => $filesvalue['author'],'discussion_id' => $discussion_id,'useip' => $filesvalue['useip'],'type' => $filesvalue['type'],'size' => $filesvalue['size'],'post_id' => $post_id,'remote' => $filesvalue['remote'],'width' => $filesvalue['width'],'height' => $filesvalue['height'],'invisible' => $filesvalue['invisible'],'isimage' => $filesvalue['isimage'],'filetype' => $filesvalue['filetype'],'thumb' => $filesvalue['thumb']), 1);
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
				notice_discussion_add($project_id, $discussion_id, $post_id, $_POST['message_subscribers']);
				//更新统计数据
				$_SGLOBAL['db']->query("UPDATE ".tname('discussion')." SET post_num=post_num+1,navidescription='{$summay}',lastpost='{$_SGLOBAL[timestamp]}',lastposter='{$setarr[author]}' WHERE discussion_id='$discussion_id'");
				if($posttype == 'todoslist') {//待办事宜评论
					$_SGLOBAL['db']->query("UPDATE ".tname('todoslist')." SET post_num=post_num+1 WHERE todoslist_id='{$todoslist_id}'");
				}
			}
		}
	}
	showmessage('do_success',"group.php?do=project&project_id={$project_id}");
}
//检查信息
$objectid =empty($_GET['objectid'])?0:intval($_GET['objectid']);//前端使用，服务端勿使用
$post_id = empty($_GET['post_id'])?0:intval($_GET['post_id']);
$op = empty($_GET['op'])?'':$_GET['op'];

$post = array();
if($post_id) {
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('post')." WHERE post_id='$post_id'");
	$post = $_SGLOBAL['db']->fetch_array($query);
	if(!empty($post)) {
		if($project_id != $post['project_id']){
			showmessage('failed_to_operation');
		}
	} else {
		showmessage('failed_to_operation');
	}
} else {
	showmessage('failed_to_operation');
}

if($_GET['op'] == 'edit') {
	include_once template("cp_post");
}elseif($_GET['op'] == 'delete') {
	if($post_id) {
		//删除
		include_once(S_ROOT.'./source/function_delete.php');
		if(deleteposts($project_id,$post_id)) {
			showmessage('do_success',"group.php?do=project&project_id={$project_id}");
		} else {
			showmessage('failed_to_delete_operation');
		}
	} else {
		showmessage('failed_to_delete_operation');
	}
}

?>