<?php
/*
	[TEAMDOTA] (C) 2012 wanghui
	$Id: function_delete.php 2012-03-31 09:59Z duty $
*/

if(!defined('IN_TEAMDOTA')) {
	exit('Access Denied');
}

//项目进入回收站
function trash_can_projects($project_id) {
	global $_SGLOBAL;

	$newid = 0;
	$group_id = $_SGLOBAL['member']['group_id'];
	$attachsize = 0;

	$query = $_SGLOBAL['db']->query("SELECT uid,project_id,group_id,attachsize,name,status FROM ".tname('project')."  WHERE `project_id` ='{$project_id}' LIMIT 1");
	if($value = $_SGLOBAL['db']->fetch_array($query)){
		if(check_project_manage($value['uid']) && $value['status'] != 2) {
			$newid = $value['project_id'];
			$group_id = $value['group_id'];
			$attachsize = intval($value['attachsize']);
		}
	}
	if(empty($newid)) return 0;
	
	//修改项目为删除状态
	$_SGLOBAL['db']->query("UPDATE ".tname('project')." SET `status`=2 WHERE project_id ='{$project_id}'");
	//更新群组使用的附件大小
	if($value['status'] == 0) {
		$_SGLOBAL['db']->query("UPDATE ".tname('group')." SET attachsize=attachsize-{$attachsize},all_project_num=all_project_num-1,project_num=project_num-1 WHERE group_id ='{$group_id}'");
	} else {
		$_SGLOBAL['db']->query("UPDATE ".tname('group')." SET attachsize=attachsize-{$attachsize},all_project_num=all_project_num-1 WHERE group_id ='{$group_id}'");
	}
	
	//添加进回收站
	trash_can_add('delete',  $project_id, $value['name'], 'projectid', $project_id, cplang('trash_can_project_delete'), array('subject' => $value['name']));
	notification_add('project', 'delete',  $project_id, $value['name'], 'projectid', $project_id, cplang('notification_project_delete'), array('subject' => $value['name']));
	return $newid;
}
//从回收站中恢复项目
function restored_projects($project_id) {
	global $_SGLOBAL,$_SCONFIG,$group;
	
	//检查项目数
	if($_SCONFIG['group_gtype'][$group['gtype']]) {//判断项目数是否吻合
		if($group['project_num'] >= $_SCONFIG['group_gtype'][$group['gtype']]) {
			return 0;
		}
	}
	
	$newid = 0;
	$group_id = $_SGLOBAL['member']['group_id'];
	$attachsize = 0;

	$query = $_SGLOBAL['db']->query("SELECT uid,project_id,group_id,attachsize,name,status FROM ".tname('project')."  WHERE `project_id` ='{$project_id}' LIMIT 1");
	if($value = $_SGLOBAL['db']->fetch_array($query)){
		if(check_project_manage($value['uid']) && $value['status'] == 2) {
			$newid = $value['project_id'];
			$group_id = $value['group_id'];
			$attachsize = intval($value['attachsize']);
		}
	}
	if(empty($newid)) return 0;
	
	//检查空间大小
	$maxattachsize = $group['maxattachsize'];//单位KB
	if($maxattachsize) {//0为不限制
		if($group['attachsize'] + $attachsize > $maxattachsize) {
			return 0;
		}
	}
	
	//修改项目为正常状态
	$_SGLOBAL['db']->query("UPDATE ".tname('project')." SET `status`=0 WHERE project_id ='{$project_id}'");
	//更新群组使用的附件大小
	$_SGLOBAL['db']->query("UPDATE ".tname('group')." SET attachsize=attachsize+{$attachsize},all_project_num=all_project_num+1,project_num=project_num+1 WHERE group_id ='{$group_id}'");
	
	//添加进回收站
	trash_can_add('restored',  $project_id, $value['name'], 'projectid', $project_id, cplang('restored_project_delete'), array('subject' => $value['name']));
	notification_add('project', 'restored',  $project_id, $value['name'], 'projectid', $project_id, cplang('restored_project_delete'), array('subject' => $value['name']));
	return $newid;
}
//删除项目
function deleteprojects($project_id) {
	global $_SGLOBAL;

	$newid = 0;
	$group_id = $_SGLOBAL['member']['group_id'];
	$attachsize = 0;

	$query = $_SGLOBAL['db']->query("SELECT uid,project_id,group_id,attachsize,name,status FROM ".tname('project')."  WHERE `project_id` ='{$project_id}' LIMIT 1");
	if($value = $_SGLOBAL['db']->fetch_array($query)){
		if(check_project_manage($value['uid']) && $value['status'] == 2) {
			$newid = $value['project_id'];
			$group_id = $value['group_id'];
			$attachsize = intval($value['attachsize']);
		}
	}
	if(empty($newid)) return 0;
	
	//删除
	$_SGLOBAL['db']->query("DELETE FROM ".tname('document')." WHERE project_id ='{$newid}'");
	$_SGLOBAL['db']->query("DELETE FROM ".tname('discussion')." WHERE project_id ='{$newid}'");
	$_SGLOBAL['db']->query("DELETE FROM ".tname('file')." WHERE project_id ='{$newid}'");
	$_SGLOBAL['db']->query("DELETE FROM ".tname('post')." WHERE project_id ='{$newid}'");
	$_SGLOBAL['db']->query("DELETE FROM ".tname('project_member')." WHERE project_id ='{$newid}'");
	//删除feed
	$_SGLOBAL['db']->query("DELETE FROM ".tname('notification')." WHERE project_id ='{$newid}'");
	$_SGLOBAL['db']->query("DELETE FROM ".tname('trash_can')." WHERE project_id ='{$newid}'");
	$_SGLOBAL['db']->query("DELETE FROM ".tname('trash_can_log')." WHERE object_id ='{$newid}' AND object_type='projectid'");
	$_SGLOBAL['db']->query("DELETE FROM ".tname('notice_discussion')." WHERE project_id ='{$newid}'");
	$_SGLOBAL['db']->query("DELETE FROM ".tname('project')." WHERE project_id ='{$newid}'");
	return $newid;
}

//删除帐户成员
function delete_member($deleteuid) {
	global $_SGLOBAL;
	
	$newid = 0;
	$group_id = $_SGLOBAL['member']['group_id'];
	
	if($_SGLOBAL['supe_uid'] != $deleteuid) {
		if(check_project_manage()) {
			$querypro = $_SGLOBAL['db']->query("SELECT group_id,fullname,isactive FROM ".tname('member')." WHERE uid='{$deleteuid}' LIMIT 1");
			if($memberpro = $_SGLOBAL['db']->fetch_array($querypro)) {
				if($memberpro['group_id'] == $group_id) {
					$newid = $deleteuid;
				}
			}
		}
	}
	
	if(empty($newid)) return 0;
	
	if($_SGLOBAL['db']->query("DELETE FROM ".tname('member')." WHERE uid ='{$newid}'")) {
		$_SGLOBAL['db']->query("DELETE FROM ".tname('notification')." WHERE `sender_id` ='{$newid}'");
		$query_project = $_SGLOBAL['db']->query("SELECT project_id FROM ".tname('project_member')." WHERE uid='{$newid}'");
		while($value = $_SGLOBAL['db']->fetch_array($query_project)) {
			//删除项目关联表
			if($_SGLOBAL['db']->query("DELETE FROM ".tname('project_member')." WHERE `project_id` ='{$value[project_id]}' AND uid ='{$newid}'")) {
				//更新项目成员数
				$_SGLOBAL['db']->query("UPDATE ".tname('project')." SET member_num=member_num-1 WHERE project_id='{$value[project_id]}'");
			}
		}
	}
	return $newid;
}
//删除项目成员
function deleteprojects_member($project_id,$deleteuid) {
	global $_SGLOBAL;
	
	$newid = 0;
	$group_id = $_SGLOBAL['member']['group_id'];
	
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('project')." WHERE `project_id`='{$project_id}' LIMIT 1");
	if($manageproject = $_SGLOBAL['db']->fetch_array($query)) {
		if($manageproject['group_id'] == $group_id){
			if($_SGLOBAL['supe_uid'] != $deleteuid) {
				if(check_project_manage($manageproject['uid']) && $manageproject['status'] == 0) {
					$querypro = $_SGLOBAL['db']->query("SELECT m.fullname FROM ".tname('project_member')." p,".tname('member')." m WHERE p.uid=m.uid AND p.project_id='{$project_id}' AND p.uid='{$deleteuid}' LIMIT 1");
					if($memberpro = $_SGLOBAL['db']->fetch_array($querypro)) {
						$newid = $deleteuid;
					}
				}
			}
		}
	}
	
	if(empty($newid)) return 0;
	
	//删除项目关联表
	if($_SGLOBAL['db']->query("DELETE FROM ".tname('project_member')." WHERE `project_id` ='{$project_id}' AND uid ='{$newid}'")) {
		//更新项目成员数
		$_SGLOBAL['db']->query("UPDATE ".tname('project')." SET member_num=member_num-1 WHERE project_id='{$project_id}'");
		//添加事件
		notification_add('project_member', 'delete',  $project_id, $manageproject['name'], 'projectid', $project_id, cplang('notification_project_member_delete'), array('subject' => $manageproject['name'], 'member' => $memberpro['fullname']));
	}
	return $newid;
}
//添加项目成员
function restored_projects_member($project_id,$deleteuid) {
	global $_SGLOBAL;
	
	$newid = 0;
	$group_id = $_SGLOBAL['member']['group_id'];
	
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('project')." WHERE `project_id`='{$project_id}' LIMIT 1");
	if($manageproject = $_SGLOBAL['db']->fetch_array($query)) {
		if($manageproject['group_id'] == $group_id){
			if($_SGLOBAL['supe_uid'] != $deleteuid) {
				if(check_project_manage($manageproject['uid']) && $manageproject['status'] == 0) {
					$querypro = $_SGLOBAL['db']->query("SELECT group_id,fullname,isactive FROM ".tname('member')." WHERE uid='{$deleteuid}' LIMIT 1");
					if($memberpro = $_SGLOBAL['db']->fetch_array($querypro)) {
						if($memberpro['group_id'] == $group_id) {
							$newid = $deleteuid;
						}
					}
				}
			}
		}
	}
	
	if(empty($newid)) return 0;
	
	if(inserttable('project_member', array('group_id'=>$group_id,'project_id' => $project_id,'logtime' => $_SGLOBAL['timestamp'],'uid' => $newid,'type' => 0,'isactive' => $memberpro['isactive']), 1)) {
		//更新项目成员数
		$_SGLOBAL['db']->query("UPDATE ".tname('project')." SET member_num=member_num+1 WHERE project_id='{$project_id}'");
	
		//添加事件
		notification_add('project_member', 'create',  $project_id, $manageproject['name'], 'projectid', $project_id, cplang('notification_project_member_create'), array('subject' => $manageproject['name'], 'member' => $memberpro['fullname']));
	}
	return $newid;
}

//主题进入回收站
function trash_can_discussions($project_id,$discussion_id) {
	global $_SGLOBAL,$manageproject;

	$newid = 0;
	$group_id = $_SGLOBAL['member']['group_id'];
	$attachsize = 0;
	$file_num = 0;

	$query = $_SGLOBAL['db']->query("SELECT uid,discussion_id,project_id,subject,status FROM ".tname('discussion')."  WHERE `discussion_id` ='{$discussion_id}' LIMIT 1");
	if($valuediscussion = $_SGLOBAL['db']->fetch_array($query)){
		if(check_project_manage($manageproject['uid'],$valuediscussion['uid']) && $valuediscussion['status'] == 0) {
			$newid = $valuediscussion['discussion_id'];
			$project_id = $valuediscussion['project_id'];
		}
	}
	if(empty($newid)) return 0;
	
	//获取主题附件
	$queryfile = $_SGLOBAL['db']->query("SELECT size FROM ".tname('file')." WHERE discussion_id ='{$newid}'");
	while ($value = $_SGLOBAL['db']->fetch_array($queryfile)) {
		$attachsize += intval($value['size']);
		$file_num++;
	}
	//修改主题为删除状态
	//$_SGLOBAL['db']->query("UPDATE ".tname('notification')." SET `status`=1 WHERE object_id ='{$newid}' AND object_type='discussionid'");
	$_SGLOBAL['db']->query("UPDATE ".tname('file')." SET `status`=1 WHERE discussion_id ='{$newid}'");
	$_SGLOBAL['db']->query("UPDATE ".tname('discussion')." SET `status`=1 WHERE discussion_id ='{$newid}'");
	
	//更新项目主题数
	$_SGLOBAL['db']->query("UPDATE ".tname('project')." SET discussion_num=discussion_num-1,file_num=file_num-{$file_num},attachsize=attachsize-{$attachsize} WHERE project_id ='{$project_id}'");
	$_SGLOBAL['db']->query("UPDATE ".tname('group')." SET attachsize=attachsize-{$attachsize} WHERE group_id ='{$group_id}'");
	
	//添加进回收站
	trash_can_add('delete',  $project_id, $manageproject['name'], 'discussionid', $discussion_id, cplang('trash_can_discussion_delete'), array('subject' => $valuediscussion['subject']));
	notification_add('discussion', 'delete',  $project_id, $manageproject['name'], 'discussionid', $discussion_id, cplang('notification_discussion_delete'), array('subject' => $valuediscussion['subject']));
	return $newid;
}
//从回收站恢复主题
function restored_discussions($project_id,$discussion_id) {
	global $_SGLOBAL,$manageproject,$_SCONFIG,$group;

	$newid = 0;
	$group_id = $_SGLOBAL['member']['group_id'];
	$attachsize = 0;
	$file_num = 0;

	$query = $_SGLOBAL['db']->query("SELECT uid,discussion_id,project_id,subject,status FROM ".tname('discussion')."  WHERE `discussion_id` ='{$discussion_id}' LIMIT 1");
	if($valuediscussion = $_SGLOBAL['db']->fetch_array($query)){
		if(check_project_manage($manageproject['uid'],$valuediscussion['uid']) && $valuediscussion['status'] == 1) {
			$newid = $valuediscussion['discussion_id'];
			$project_id = $valuediscussion['project_id'];
		}
	}
	if(empty($newid)) return 0;
	
	//获取主题附件
	$queryfile = $_SGLOBAL['db']->query("SELECT size FROM ".tname('file')." WHERE discussion_id ='{$newid}'");
	while ($value = $_SGLOBAL['db']->fetch_array($queryfile)) {
		$attachsize += intval($value['size']);
		$file_num++;
	}
	
	//检查空间大小
	$maxattachsize = $group['maxattachsize'];//单位KB
	if($maxattachsize) {//0为不限制
		if($group['attachsize'] + $attachsize > $maxattachsize) {
			return 0;
		}
	}
	
	//恢复主题
	//$_SGLOBAL['db']->query("UPDATE ".tname('notification')." SET `status`=0 WHERE object_id ='{$newid}' AND object_type='discussionid'");
	$_SGLOBAL['db']->query("UPDATE ".tname('file')." SET `status`=0 WHERE discussion_id ='{$newid}'");
	$_SGLOBAL['db']->query("UPDATE ".tname('discussion')." SET `status`=0 WHERE discussion_id ='{$newid}'");
	
	//更新项目主题数
	$_SGLOBAL['db']->query("UPDATE ".tname('project')." SET discussion_num=discussion_num+1,file_num=file_num+{$file_num},attachsize=attachsize+{$attachsize} WHERE project_id ='{$project_id}'");
	$_SGLOBAL['db']->query("UPDATE ".tname('group')." SET attachsize=attachsize+{$attachsize} WHERE group_id ='{$group_id}'");
	
	//添加进回收站
	trash_can_add('restored',  $project_id, $manageproject['name'], 'discussionid', $discussion_id, cplang('restored_discussion_delete'), array('subject' => $valuediscussion['subject']));
	notification_add('discussion', 'restored',  $project_id, $manageproject['name'], 'discussionid', $discussion_id, cplang('restored_discussion_delete'), array('subject' => $valuediscussion['subject']));
	return $newid;
}
//删除主题
function deletediscussions($project_id,$discussion_id) {
	global $_SGLOBAL,$manageproject;

	$newid = 0;
	$group_id = $_SGLOBAL['member']['group_id'];

	$query = $_SGLOBAL['db']->query("SELECT uid,discussion_id,project_id,subject,status FROM ".tname('discussion')."  WHERE `discussion_id` ='{$discussion_id}' LIMIT 1");
	if($valuediscussion = $_SGLOBAL['db']->fetch_array($query)){
		if(check_project_manage($manageproject['uid'],$valuediscussion['uid']) && $valuediscussion['status'] == 1) {
			$newid = $valuediscussion['discussion_id'];
			$project_id = $valuediscussion['project_id'];
		}
	}
	if(empty($newid)) return 0;
	
	//删除
	$_SGLOBAL['db']->query("DELETE FROM ".tname('file')." WHERE discussion_id ='{$newid}'");
	$_SGLOBAL['db']->query("DELETE FROM ".tname('post')." WHERE discussion_id ='{$newid}'");
	$_SGLOBAL['db']->query("DELETE FROM ".tname('notification')." WHERE object_id ='{$newid}' AND object_type='discussionid'");
	$_SGLOBAL['db']->query("DELETE FROM ".tname('trash_can')." WHERE object_id ='{$newid}' AND object_type='discussionid'");
	$_SGLOBAL['db']->query("DELETE FROM ".tname('trash_can_log')." WHERE object_id ='{$newid}' AND object_type='discussionid'");
	$_SGLOBAL['db']->query("DELETE FROM ".tname('notice_discussion')." WHERE discussion_id ='{$newid}'");
	$_SGLOBAL['db']->query("DELETE FROM ".tname('discussion')." WHERE discussion_id ='{$newid}'");
	return $newid;
}

//删除主题附件(编辑主题时发生)
function deletediscussionfiles($project_id,$discussion_id,$fileids) {
	global $_SGLOBAL,$manageproject;

	//统计
	$newids = array();
	$group_id = $_SGLOBAL['member']['group_id'];
	$attachsize = 0;
	$file_num = 0;
	
	$query = $_SGLOBAL['db']->query("SELECT uid,file_id,size FROM ".tname('file')." WHERE file_id IN (".simplode($fileids).")");
	while ($value = $_SGLOBAL['db']->fetch_array($query)) {
		if(check_project_manage($manageproject['uid'],$value['uid'])) {
			$newids[] = $value['file_id'];
			$attachsize += intval($value['size']);
			$file_num++;
		}
	}
	if(empty($newids)) return 0;

	//删除
	$_SGLOBAL['db']->query("DELETE FROM ".tname('file')." WHERE file_id IN (".simplode($newids).")");
	//更新项目附件数
	$_SGLOBAL['db']->query("UPDATE ".tname('project')." SET file_num=file_num-{$file_num},attachsize=attachsize-{$attachsize} WHERE project_id ='{$project_id}'");
	$_SGLOBAL['db']->query("UPDATE ".tname('group')." SET attachsize=attachsize-{$attachsize} WHERE group_id ='{$group_id}'");

	return $newids;
}

//删除讨论
function deleteposts($project_id,$post_id) {
	global $_SGLOBAL,$manageproject;

	//统计
	$newid = 0;
	$group_id = $_SGLOBAL['member']['group_id'];
	$discussion_id = 0;
	$attachsize = 0;
	$file_num = 0;

	$query = $_SGLOBAL['db']->query("SELECT uid,post_id,project_id,discussion_id FROM ".tname('post')." WHERE post_id ='{$post_id}' LIMIT 1");
	if ($value = $_SGLOBAL['db']->fetch_array($query)) {
		if(check_project_manage($manageproject['uid'],$value['uid'])) {
			$newid = $value['post_id'];
			$project_id = $value['project_id'];
			$discussion_id = $value['discussion_id'];
		}
	}
	if(empty($newid)) return 0;
	
	$querydiscussion = $_SGLOBAL['db']->query("SELECT subject,othertype,otherid FROM ".tname('discussion')."  WHERE `discussion_id` ='{$discussion_id}' LIMIT 1");
	$valuediscussion = $_SGLOBAL['db']->fetch_array($querydiscussion);
	
	//获取主题回复附件
	$queryfile = $_SGLOBAL['db']->query("SELECT size FROM ".tname('file')." WHERE post_id ='{$newid}'");
	while ($value = $_SGLOBAL['db']->fetch_array($queryfile)) {
		$attachsize += intval($value['size']);
		$file_num++;
	}
	//删除
	$_SGLOBAL['db']->query("DELETE FROM ".tname('file')." WHERE post_id ='{$newid}'");
	$_SGLOBAL['db']->query("DELETE FROM ".tname('post')." WHERE post_id ='{$newid}'");
	//更新主题回复数
	$_SGLOBAL['db']->query("UPDATE ".tname('discussion')." SET post_num=post_num-1 WHERE discussion_id ='{$discussion_id}'");
	//更新项目主题数
	$_SGLOBAL['db']->query("UPDATE ".tname('project')." SET file_num=file_num-{$file_num},attachsize=attachsize-{$attachsize} WHERE project_id ='{$project_id}'");
	$_SGLOBAL['db']->query("UPDATE ".tname('group')." SET attachsize=attachsize-{$attachsize} WHERE group_id ='{$group_id}'");

	//添加事件
	if($valuediscussion['othertype'] == 1){
		notification_add('post', 'delete',  $project_id, $manageproject['name'], 'documentid', $valuediscussion['otherid'], cplang('notification_document_post_delete'), array('subject' => $valuediscussion['subject']));
	} else if($valuediscussion['othertype'] == 2){
		notification_add('post', 'delete',  $project_id, $manageproject['name'], 'attachmentid', $valuediscussion['otherid'], cplang('notification_attachment_post_delete'), array('subject' => $valuediscussion['subject']));
	}  else if($valuediscussion['othertype'] == 3){
		notification_add('post', 'delete',  $project_id, $manageproject['name'], 'todosid', $valuediscussion['otherid'], cplang('notification_todos_post_delete'), array('subject' => $valuediscussion['subject']));
	}  else if($valuediscussion['othertype'] == 4){
		notification_add('post', 'delete',  $project_id, $manageproject['name'], 'todoslistid', $valuediscussion['otherid'], cplang('notification_todoslist_post_delete'), array('subject' => $valuediscussion['subject']));
		$_SGLOBAL['db']->query("UPDATE ".tname('todoslist')." SET post_num=post_num-1 WHERE todoslist_id ='{$valuediscussion[otherid]}'");
	} else {
		notification_add('post', 'delete',  $project_id, $manageproject['name'], 'discussionid', $discussion_id, cplang('notification_discussion_post_delete'), array('subject' => $valuediscussion['subject']));
	}
	return $newid;
}

//文档进入回收站
function trash_can_documents($project_id,$document_id) {
	global $_SGLOBAL,$manageproject;

	//统计
	$newid = 0;
	$group_id = $_SGLOBAL['member']['group_id'];
	$discussion_id = 0;
	$attachsize = 0;
	$file_num = 0;

	$query = $_SGLOBAL['db']->query("SELECT uid,document_id,project_id,discussion_id,name,status FROM ".tname('document')." WHERE document_id ='{$document_id}' LIMIT 1");
	if ($valuedocument = $_SGLOBAL['db']->fetch_array($query)) {
		if(check_project_manage($manageproject['uid'],$valuedocument['uid']) && $valuedocument['status'] == 0) {
			$newid = $valuedocument['document_id'];
			$project_id = $valuedocument['project_id'];
			$discussion_id = $valuedocument['discussion_id'];
		}
	}
	if(empty($newid)) return 0;
	
	//获取主题附件
	if($discussion_id){
		$queryfile = $_SGLOBAL['db']->query("SELECT size FROM ".tname('file')." WHERE discussion_id ='{$discussion_id}'");
		while ($value = $_SGLOBAL['db']->fetch_array($queryfile)) {
			$attachsize += intval($value['size']);
			$file_num++;
		}
	}
	//删除
	if($discussion_id){
		$_SGLOBAL['db']->query("UPDATE ".tname('file')." SET `status`=1 WHERE discussion_id ='{$discussion_id}'");
		$_SGLOBAL['db']->query("UPDATE ".tname('discussion')." SET `status`=1 WHERE discussion_id ='{$discussion_id}'");
		$_SGLOBAL['db']->query("UPDATE ".tname('document')." SET `status`=1 WHERE document_id ='{$document_id}'");
		//更新项目主题数和文档数
		$_SGLOBAL['db']->query("UPDATE ".tname('project')." SET document_num=document_num-1,file_num=file_num-{$file_num},discussion_num=discussion_num-1 ,attachsize=attachsize-{$attachsize} WHERE project_id ='{$project_id}'");
		$_SGLOBAL['db']->query("UPDATE ".tname('group')." SET attachsize=attachsize-{$attachsize} WHERE group_id ='{$group_id}'");
	}else{
		//更新项目文档数
		$_SGLOBAL['db']->query("UPDATE ".tname('document')." SET `status`=1 WHERE document_id ='{$document_id}'");
		$_SGLOBAL['db']->query("UPDATE ".tname('project')." SET document_num=document_num-1 WHERE project_id ='{$project_id}'");
	}
	//$_SGLOBAL['db']->query("UPDATE ".tname('notification')." SET `status`=1 WHERE object_id ='{$document_id}' AND object_type='documentid'");
	
	//添加事件
	trash_can_add('delete',  $project_id, $manageproject['name'], 'documentid', $document_id, cplang('trash_can_document_delete'), array('subject' => $valuedocument['name']));
	notification_add('document', 'delete',  $project_id, $manageproject['name'], 'documentid', $document_id, cplang('notification_document_delete'), array('subject' => $valuedocument['name']));
	return $newid;
}
//从回收站恢复文档
function restored_documents($project_id,$document_id) {
	global $_SGLOBAL,$manageproject,$_SCONFIG,$group;

	//统计
	$newid = 0;
	$group_id = $_SGLOBAL['member']['group_id'];
	$discussion_id = 0;
	$attachsize = 0;
	$file_num = 0;

	$query = $_SGLOBAL['db']->query("SELECT uid,document_id,project_id,discussion_id,name,status FROM ".tname('document')." WHERE document_id ='{$document_id}' LIMIT 1");
	if ($valuedocument = $_SGLOBAL['db']->fetch_array($query)) {
		if(check_project_manage($manageproject['uid'],$valuedocument['uid']) && $valuedocument['status'] == 1) {
			$newid = $valuedocument['document_id'];
			$project_id = $valuedocument['project_id'];
			$discussion_id = $valuedocument['discussion_id'];
		}
	}
	if(empty($newid)) return 0;
	
	//获取主题附件
	if($discussion_id){
		$queryfile = $_SGLOBAL['db']->query("SELECT size FROM ".tname('file')." WHERE discussion_id ='{$discussion_id}'");
		while ($value = $_SGLOBAL['db']->fetch_array($queryfile)) {
			$attachsize += intval($value['size']);
			$file_num++;
		}

		//检查空间大小
		$maxattachsize = $group['maxattachsize'];//单位KB
		if($maxattachsize) {//0为不限制
			if($group['attachsize'] + $attachsize > $maxattachsize) {
				return 0;
			}
		}
	}
	
	//恢复
	if($discussion_id){
		$_SGLOBAL['db']->query("UPDATE ".tname('file')." SET `status`=0 WHERE discussion_id ='{$discussion_id}'");
		$_SGLOBAL['db']->query("UPDATE ".tname('discussion')." SET `status`=0 WHERE discussion_id ='{$discussion_id}'");
		$_SGLOBAL['db']->query("UPDATE ".tname('document')." SET `status`=0 WHERE document_id ='{$document_id}'");
		//更新项目主题数和文档数
		$_SGLOBAL['db']->query("UPDATE ".tname('project')." SET document_num=document_num+1,file_num=file_num+{$file_num},discussion_num=discussion_num+1 ,attachsize=attachsize+{$attachsize} WHERE project_id ='{$project_id}'");
		$_SGLOBAL['db']->query("UPDATE ".tname('group')." SET attachsize=attachsize+{$attachsize} WHERE group_id ='{$group_id}'");
	}else{
		//更新项目文档数
		$_SGLOBAL['db']->query("UPDATE ".tname('document')." SET `status`=0 WHERE document_id ='{$document_id}'");
		$_SGLOBAL['db']->query("UPDATE ".tname('project')." SET document_num=document_num+1 WHERE project_id ='{$project_id}'");
	}
	//$_SGLOBAL['db']->query("UPDATE ".tname('notification')." SET `status`=0 WHERE object_id ='{$document_id}' AND object_type='documentid'");
	
	//添加事件
	trash_can_add('restored',  $project_id, $manageproject['name'], 'documentid', $document_id, cplang('restored_document_delete'), array('subject' => $valuedocument['name']));
	notification_add('document', 'restored',  $project_id, $manageproject['name'], 'documentid', $document_id, cplang('restored_document_delete'), array('subject' => $valuedocument['name']));
	return $newid;
}
//删除文档
function deletedocuments($project_id,$document_id) {
	global $_SGLOBAL,$manageproject;

	//统计
	$newid = 0;
	$group_id = $_SGLOBAL['member']['group_id'];
	$discussion_id = 0;
	$attachsize = 0;
	$file_num = 0;

	$query = $_SGLOBAL['db']->query("SELECT uid,document_id,project_id,discussion_id,name,status FROM ".tname('document')." WHERE document_id ='{$document_id}' LIMIT 1");
	if ($valuedocument = $_SGLOBAL['db']->fetch_array($query)) {
		if(check_project_manage($manageproject['uid'],$valuedocument['uid']) && $valuedocument['status'] == 1) {
			$newid = $valuedocument['document_id'];
			$project_id = $valuedocument['project_id'];
			$discussion_id = $valuedocument['discussion_id'];
		}
	}
	if(empty($newid)) return 0;
	
	//删除
	if($discussion_id){
		$_SGLOBAL['db']->query("DELETE FROM ".tname('file')." WHERE discussion_id ='{$discussion_id}'");
		$_SGLOBAL['db']->query("DELETE FROM ".tname('post')." WHERE discussion_id ='{$discussion_id}'");
		$_SGLOBAL['db']->query("DELETE FROM ".tname('discussion')." WHERE discussion_id ='{$discussion_id}'");
		$_SGLOBAL['db']->query("DELETE FROM ".tname('notice_discussion')." WHERE discussion_id ='{$discussion_id}'");
	}
	//删除feed
	$_SGLOBAL['db']->query("DELETE FROM ".tname('notification')." WHERE object_id ='{$newid}' AND object_type='documentid'");
	$_SGLOBAL['db']->query("DELETE FROM ".tname('trash_can')." WHERE object_id ='{$newid}' AND object_type='documentid'");
	$_SGLOBAL['db']->query("DELETE FROM ".tname('trash_can_log')." WHERE object_id ='{$newid}' AND object_type='documentid'");
	$_SGLOBAL['db']->query("DELETE FROM ".tname('document')." WHERE document_id ='{$newid}'");

	return $newid;
}

//附件进入回收站
function trash_can_files($project_id,$file_id) {
	global $_SGLOBAL,$manageproject;

	//统计
	$newid = 0;
	$group_id = $_SGLOBAL['member']['group_id'];
	$discussion_id = 0;
	$attachsize = 0;

	$query = $_SGLOBAL['db']->query("SELECT uid,file_id,project_id,discussion_id,size,filename,status FROM ".tname('file')." WHERE file_id ='{$file_id}' LIMIT 1");
	if ($valuefile = $_SGLOBAL['db']->fetch_array($query)) {
		if(check_project_manage($manageproject['uid'],$valuefile['uid']) && $valuefile['status'] == 0) {
			$newid = $valuefile['file_id'];
			$project_id = $valuefile['project_id'];
			$discussion_id = $valuefile['discussion_id'];
			$attachsize = intval($valuefile['size']);
		}
	}
	if(empty($newid)) return 0;
	
	//获取主题附件
	if($discussion_id){
		$attachsize = 0;
		$queryfile = $_SGLOBAL['db']->query("SELECT size FROM ".tname('file')." WHERE discussion_id ='{$discussion_id}'");
		while ($value = $_SGLOBAL['db']->fetch_array($queryfile)) {
			$attachsize += intval($value['size']);
		}
	}
	//删除
	if($discussion_id){
		$_SGLOBAL['db']->query("UPDATE ".tname('discussion')." SET `status`=1 WHERE discussion_id ='{$discussion_id}'");
		$_SGLOBAL['db']->query("UPDATE ".tname('file')." SET `status`=1 WHERE discussion_id ='{$discussion_id}'");
		$_SGLOBAL['db']->query("UPDATE ".tname('file')." SET `status`=1 WHERE file_id ='{$file_id}'");
		//更新项目主题数和附件数
		$_SGLOBAL['db']->query("UPDATE ".tname('project')." SET file_num=file_num-1,discussion_num=discussion_num-1,attachsize=attachsize-{$attachsize} WHERE project_id ='{$project_id}'");
	}else{
		$_SGLOBAL['db']->query("UPDATE ".tname('file')." SET `status`=1 WHERE file_id ='{$file_id}'");
		//更新项目附件数
		$_SGLOBAL['db']->query("UPDATE ".tname('project')." SET file_num=file_num-1,attachsize=attachsize-{$attachsize} WHERE project_id ='{$project_id}'");
	}
	//$_SGLOBAL['db']->query("UPDATE ".tname('notification')." SET `status`=1 WHERE object_id ='{$file_id}' AND object_type='attachmentid'");
	$_SGLOBAL['db']->query("UPDATE ".tname('group')." SET attachsize=attachsize-{$attachsize} WHERE group_id ='{$group_id}'");

	//添加事件
	trash_can_add('delete',  $project_id, $manageproject['name'], 'attachmentid', $file_id, cplang('trash_can_attachment_delete'), array('subject' => $valuefile['filename']));
	notification_add('attachment', 'delete',  $project_id, $manageproject['name'], 'attachmentid', $file_id, cplang('notification_attachment_delete'), array('subject' => $valuefile['filename']));
	return $newid;
}
//从回收站恢复附件
function restored_files($project_id,$file_id) {
	global $_SGLOBAL,$manageproject,$_SCONFIG,$group;

	//统计
	$newid = 0;
	$group_id = $_SGLOBAL['member']['group_id'];
	$discussion_id = 0;
	$attachsize = 0;

	$query = $_SGLOBAL['db']->query("SELECT uid,file_id,project_id,discussion_id,size,filename,status FROM ".tname('file')." WHERE file_id ='{$file_id}' LIMIT 1");
	if ($valuefile = $_SGLOBAL['db']->fetch_array($query)) {
		if(check_project_manage($manageproject['uid'],$valuefile['uid']) && $valuefile['status'] == 1) {
			$newid = $valuefile['file_id'];
			$project_id = $valuefile['project_id'];
			$discussion_id = $valuefile['discussion_id'];
			$attachsize = intval($valuefile['size']);
		}
	}
	if(empty($newid)) return 0;
	
	//获取主题附件
	if($discussion_id){
		$attachsize = 0;
		$queryfile = $_SGLOBAL['db']->query("SELECT size FROM ".tname('file')." WHERE discussion_id ='{$discussion_id}'");
		while ($value = $_SGLOBAL['db']->fetch_array($queryfile)) {
			$attachsize += intval($value['size']);
		}
	}
	
	//检查空间大小
	$maxattachsize = $group['maxattachsize'];//单位KB
	if($maxattachsize) {//0为不限制
		if($group['attachsize'] + $attachsize > $maxattachsize) {
			return 0;
		}
	}
	
	//恢复
	if($discussion_id){
		$_SGLOBAL['db']->query("UPDATE ".tname('discussion')." SET `status`=0 WHERE discussion_id ='{$discussion_id}'");
		$_SGLOBAL['db']->query("UPDATE ".tname('file')." SET `status`=0 WHERE discussion_id ='{$discussion_id}'");
		$_SGLOBAL['db']->query("UPDATE ".tname('file')." SET `status`=0 WHERE file_id ='{$file_id}'");
		//更新项目主题数和附件数
		$_SGLOBAL['db']->query("UPDATE ".tname('project')." SET file_num=file_num+1,discussion_num=discussion_num+1,attachsize=attachsize+{$attachsize} WHERE project_id ='{$project_id}'");
	}else{
		$_SGLOBAL['db']->query("UPDATE ".tname('file')." SET `status`=0 WHERE file_id ='{$file_id}'");
		//更新项目附件数
		$_SGLOBAL['db']->query("UPDATE ".tname('project')." SET file_num=file_num+1,attachsize=attachsize+{$attachsize} WHERE project_id ='{$project_id}'");
	}
	//$_SGLOBAL['db']->query("UPDATE ".tname('notification')." SET `status`=0 WHERE object_id ='{$file_id}' AND object_type='attachmentid'");
	$_SGLOBAL['db']->query("UPDATE ".tname('group')." SET attachsize=attachsize+{$attachsize} WHERE group_id ='{$group_id}'");

	//添加事件
	trash_can_add('restored',  $project_id, $manageproject['name'], 'attachmentid', $file_id, cplang('restored_attachment_delete'), array('subject' => $valuefile['filename']));
	notification_add('attachment', 'restored',  $project_id, $manageproject['name'], 'attachmentid', $file_id, cplang('restored_attachment_delete'), array('subject' => $valuefile['filename']));
	return $newid;
}
//删除附件
function deletefiles($project_id,$file_id) {
	global $_SGLOBAL,$manageproject;

	//统计
	$newid = 0;
	$group_id = $_SGLOBAL['member']['group_id'];
	$discussion_id = 0;
	$attachsize = 0;

	$query = $_SGLOBAL['db']->query("SELECT uid,file_id,project_id,discussion_id,size,filename,status FROM ".tname('file')." WHERE file_id ='{$file_id}' LIMIT 1");
	if ($valuefile = $_SGLOBAL['db']->fetch_array($query)) {
		if(check_project_manage($manageproject['uid'],$valuefile['uid']) && $valuefile['status'] == 1) {
			$newid = $valuefile['file_id'];
			$project_id = $valuefile['project_id'];
			$discussion_id = $valuefile['discussion_id'];
			$attachsize = intval($valuefile['size']);
		}
	}
	if(empty($newid)) return 0;
	
	//删除
	if($discussion_id){
		$_SGLOBAL['db']->query("DELETE FROM ".tname('post')." WHERE discussion_id ='{$discussion_id}'");
		$_SGLOBAL['db']->query("DELETE FROM ".tname('discussion')." WHERE discussion_id ='{$discussion_id}'");
		$_SGLOBAL['db']->query("DELETE FROM ".tname('file')." WHERE discussion_id ='{$discussion_id}'");
		$_SGLOBAL['db']->query("DELETE FROM ".tname('notice_discussion')." WHERE discussion_id ='{$discussion_id}'");
	}
	//删除feed
	$_SGLOBAL['db']->query("DELETE FROM ".tname('notification')." WHERE object_id ='{$newid}' AND object_type='attachmentid'");
	$_SGLOBAL['db']->query("DELETE FROM ".tname('trash_can')." WHERE object_id ='{$newid}' AND object_type='attachmentid'");
	$_SGLOBAL['db']->query("DELETE FROM ".tname('trash_can_log')." WHERE object_id ='{$newid}' AND object_type='attachmentid'");
	$_SGLOBAL['db']->query("DELETE FROM ".tname('notice_attachment')." WHERE file_id ='{$newid}'");
	$_SGLOBAL['db']->query("DELETE FROM ".tname('file')." WHERE file_id ='{$newid}'");
	return $newid;
}

//待办事宜类型进入回收站
function trash_can_todos($project_id,$todos_id) {
	global $_SGLOBAL,$manageproject;

	//统计
	$newid = 0;
	$group_id = $_SGLOBAL['member']['group_id'];
	$in_discussion_id = '';
	$attachsize = 0;
	$file_num = 0;
	$todoslist_num = 0;
	$discussion_num = 0;

	$query = $_SGLOBAL['db']->query("SELECT uid,todos_id,project_id,discussion_id,subject,status FROM ".tname('todos')." WHERE todos_id ='{$todos_id}' LIMIT 1");
	if ($valuetodos = $_SGLOBAL['db']->fetch_array($query)) {
		if($project_id ==  $valuetodos['project_id'] && $valuetodos['status'] == 0) {
			$newid = $valuetodos['todos_id'];
			if(!empty($valuetodos['discussion_id'])) {
				$in_discussion_id .= $valuetodos['discussion_id'];
				$discussion_num++;
			}
		}
	}
	if(empty($newid)) return 0;
	
	//获取主题附件
	$querytodoslist = $_SGLOBAL['db']->query("SELECT discussion_id,status,is_completed FROM ".tname('todoslist')." WHERE todos_id ='{$todos_id}' AND `status`=0");
	while ($value = $_SGLOBAL['db']->fetch_array($querytodoslist)) {
		if($value['is_completed'] == 0) {
			$todoslist_num++;
		}
		if(!empty($value['discussion_id'])) {
			if($in_discussion_id == '') {
				$in_discussion_id .= $value['discussion_id'];
			} else {
				$in_discussion_id .= ','.$value['discussion_id'];
			}
			$discussion_num++;
		}
	}
	if(!empty($in_discussion_id)){
		$queryfile = $_SGLOBAL['db']->query("SELECT size FROM ".tname('file')." WHERE discussion_id IN({$in_discussion_id})");
		while ($value = $_SGLOBAL['db']->fetch_array($queryfile)) {
			$attachsize += intval($value['size']);
			$file_num++;
		}
	}
	//删除
	if(!empty($in_discussion_id)){
		$_SGLOBAL['db']->query("UPDATE ".tname('file')." SET `status`=1 WHERE discussion_id IN({$in_discussion_id})");
		$_SGLOBAL['db']->query("UPDATE ".tname('discussion')." SET `status`=1 WHERE discussion_id IN({$in_discussion_id})");
		$_SGLOBAL['db']->query("UPDATE ".tname('todos')." SET `status`=1 WHERE todos_id ='{$todos_id}'");
		//更新项目主题数和文档数
		$_SGLOBAL['db']->query("UPDATE ".tname('project')." SET todoslist_num=todoslist_num-{$todoslist_num},file_num=file_num-{$file_num},discussion_num=discussion_num-{$discussion_num} ,attachsize=attachsize-{$attachsize} WHERE project_id ='{$project_id}'");
		$_SGLOBAL['db']->query("UPDATE ".tname('group')." SET attachsize=attachsize-{$attachsize} WHERE group_id ='{$group_id}'");
	}else{
		//更新项目文档数
		$_SGLOBAL['db']->query("UPDATE ".tname('todos')." SET `status`=1 WHERE todos_id ='{$todos_id}'");
		$_SGLOBAL['db']->query("UPDATE ".tname('project')." SET todoslist_num=todoslist_num-{$todoslist_num} WHERE project_id ='{$project_id}'");
	}
	
	//添加事件
	trash_can_add('delete',  $project_id, $manageproject['name'], 'todosid', $todos_id, cplang('trash_can_todos_delete'), array('subject' => $valuetodos['subject']));
	notification_add('todos', 'delete',  $project_id, $manageproject['name'], 'todosid', $todos_id, cplang('notification_todos_delete'), array('subject' => $valuetodos['subject']));
	return $newid;
}
//从回收站恢复待办事宜类型
function restored_todos($project_id,$todos_id) {
	global $_SGLOBAL,$manageproject,$_SCONFIG,$group;

	//统计
	$newid = 0;
	$group_id = $_SGLOBAL['member']['group_id'];
	$in_discussion_id = '';
	$attachsize = 0;
	$file_num = 0;
	$todoslist_num = 0;
	$discussion_num = 0;

	$query = $_SGLOBAL['db']->query("SELECT uid,todos_id,project_id,discussion_id,subject,status FROM ".tname('todos')." WHERE todos_id ='{$todos_id}' LIMIT 1");
	if ($valuetodos = $_SGLOBAL['db']->fetch_array($query)) {
		if($project_id ==  $valuetodos['project_id'] && $valuetodos['status'] == 1) {
			$newid = $valuetodos['todos_id'];
			if(!empty($valuetodos['discussion_id'])) {
				$in_discussion_id .= $valuetodos['discussion_id'];
				$discussion_num++;
			}
		}
	}
	if(empty($newid)) return 0;
	
	//获取主题附件
	$querytodoslist = $_SGLOBAL['db']->query("SELECT discussion_id,status,is_completed FROM ".tname('todoslist')." WHERE todos_id ='{$todos_id}' AND `status`=0");
	while ($value = $_SGLOBAL['db']->fetch_array($querytodoslist)) {
		if($value['is_completed'] == 0) {
			$todoslist_num++;
		}
		if(!empty($value['discussion_id'])) {
			if($in_discussion_id == '') {
				$in_discussion_id .= $value['discussion_id'];
			} else {
				$in_discussion_id .= ','.$value['discussion_id'];
			}
			$discussion_num++;
		}
	}
	
	if(!empty($in_discussion_id)){
		$queryfile = $_SGLOBAL['db']->query("SELECT size FROM ".tname('file')." WHERE discussion_id IN({$in_discussion_id})");
		while ($value = $_SGLOBAL['db']->fetch_array($queryfile)) {
			$attachsize += intval($value['size']);
			$file_num++;
		}	
		//检查空间大小
		$maxattachsize = $group['maxattachsize'];//单位KB
		if($maxattachsize) {//0为不限制
			if($group['attachsize'] + $attachsize > $maxattachsize) {
				return 0;
			}
		}
	}
	
	//恢复
	if(!empty($in_discussion_id)){
		$_SGLOBAL['db']->query("UPDATE ".tname('file')." SET `status`=0 WHERE discussion_id IN({$in_discussion_id})");
		$_SGLOBAL['db']->query("UPDATE ".tname('discussion')." SET `status`=0 WHERE discussion_id IN({$in_discussion_id})");
		$_SGLOBAL['db']->query("UPDATE ".tname('todos')." SET `status`=0 WHERE todos_id ='{$todos_id}'");
		//更新项目主题数和文档数
		$_SGLOBAL['db']->query("UPDATE ".tname('project')." SET todoslist_num=todoslist_num+{$todoslist_num},file_num=file_num+{$file_num},discussion_num=discussion_num+{$discussion_num} ,attachsize=attachsize+{$attachsize} WHERE project_id ='{$project_id}'");
		$_SGLOBAL['db']->query("UPDATE ".tname('group')." SET attachsize=attachsize+{$attachsize} WHERE group_id ='{$group_id}'");
	}else{
		//更新项目文档数
		$_SGLOBAL['db']->query("UPDATE ".tname('todos')." SET `status`=0 WHERE todos_id ='{$todos_id}'");
		$_SGLOBAL['db']->query("UPDATE ".tname('project')." SET todoslist_num=todoslist_num+{$todoslist_num} WHERE project_id ='{$project_id}'");
	}
	
	//添加事件
	trash_can_add('restored',  $project_id, $manageproject['name'], 'todosid', $todos_id, cplang('restored_todos_delete'), array('subject' => $valuetodos['subject']));
	notification_add('todos', 'restored',  $project_id, $manageproject['name'], 'todosid', $todos_id, cplang('restored_todos_delete'), array('subject' => $valuetodos['subject']));
	return $newid;
}
//删除待办事宜类型
function deletetodos($project_id,$todos_id) {
	global $_SGLOBAL,$manageproject;

	//统计
	$newid = 0;
	$group_id = $_SGLOBAL['member']['group_id'];
	$in_discussion_id = '';
	$in_todoslist_id = '';
	$attachsize = 0;
	$file_num = 0;

	$query = $_SGLOBAL['db']->query("SELECT uid,todos_id,project_id,discussion_id,subject,status FROM ".tname('todos')." WHERE todos_id ='{$todos_id}' LIMIT 1");
	if ($valuetodos = $_SGLOBAL['db']->fetch_array($query)) {
		if($project_id ==  $valuetodos['project_id'] && $valuetodos['status'] == 1) {
			$newid = $valuetodos['todos_id'];
			if(!empty($valuetodos['discussion_id'])) {
				$in_discussion_id .= $valuetodos['discussion_id'];
			}
		}
	}
	if(empty($newid)) return 0;
	
	//获取主题附件
	$querytodoslist = $_SGLOBAL['db']->query("SELECT discussion_id,todoslist_id FROM ".tname('todoslist')." WHERE todos_id ='{$todos_id}'");
	while ($value = $_SGLOBAL['db']->fetch_array($querytodoslist)) {
		if($in_todoslist_id == '') {
			$in_todoslist_id .= $value['todoslist_id'];
		} else {
			$in_todoslist_id .= ','.$value['todoslist_id'];
		}
		if(!empty($value['discussion_id'])) {
			if($in_discussion_id == '') {
				$in_discussion_id .= $value['discussion_id'];
			} else {
				$in_discussion_id .= ','.$value['discussion_id'];
			}
		}
	}
	//删除主题
	if(!empty($in_discussion_id)){
		$_SGLOBAL['db']->query("DELETE FROM ".tname('file')." WHERE discussion_id IN({$in_discussion_id})");
		$_SGLOBAL['db']->query("DELETE FROM ".tname('post')." WHERE discussion_id IN({$in_discussion_id})");
		$_SGLOBAL['db']->query("DELETE FROM ".tname('discussion')." WHERE discussion_id IN({$in_discussion_id})");
		$_SGLOBAL['db']->query("DELETE FROM ".tname('notice_discussion')." WHERE discussion_id IN({$in_discussion_id})");
	}
	//删除待办事宜清单记录
	if(!empty($in_todoslist_id)){
		$_SGLOBAL['db']->query("DELETE FROM ".tname('notification')." WHERE object_id IN({$in_todoslist_id}) AND object_type='todoslistid'");
		$_SGLOBAL['db']->query("DELETE FROM ".tname('trash_can')." WHERE object_id IN({$in_todoslist_id}) AND object_type='todoslistid'");
		$_SGLOBAL['db']->query("DELETE FROM ".tname('trash_can_log')." WHERE object_id IN({$in_todoslist_id}) AND object_type='todoslistid'");
	}
	//删除feed
	$_SGLOBAL['db']->query("DELETE FROM ".tname('notification')." WHERE object_id ='{$newid}' AND object_type='todosid'");
	$_SGLOBAL['db']->query("DELETE FROM ".tname('trash_can')." WHERE object_id ='{$newid}' AND object_type='todosid'");
	$_SGLOBAL['db']->query("DELETE FROM ".tname('trash_can_log')." WHERE object_id ='{$newid}' AND object_type='todosid'");
	$_SGLOBAL['db']->query("DELETE FROM ".tname('todoslist')." WHERE todos_id ='{$newid}'");
	$_SGLOBAL['db']->query("DELETE FROM ".tname('todos')." WHERE todos_id ='{$newid}'");

	return $newid;
}

//待办事宜清单进入回收站
function trash_can_todoslist($project_id,$todoslist_id) {
	global $_SGLOBAL,$manageproject;

	//统计
	$newid = 0;
	$group_id = $_SGLOBAL['member']['group_id'];
	$discussion_id = 0;
	$attachsize = 0;
	$file_num = 0;
	$todoslist_num = 0;

	$query = $_SGLOBAL['db']->query("SELECT uid,todoslist_id,project_id,discussion_id,subject,status,is_completed FROM ".tname('todoslist')." WHERE todoslist_id ='{$todoslist_id}' LIMIT 1");
	if ($valuetodoslist = $_SGLOBAL['db']->fetch_array($query)) {
		if($project_id ==  $valuetodoslist['project_id'] && $valuetodoslist['status'] == 0) {
			$newid = $valuetodoslist['todoslist_id'];
			$discussion_id = $valuetodoslist['discussion_id'];
		}
	}
	if(empty($newid)) return 0;
	
	//获取主题附件
	if($discussion_id){
		$queryfile = $_SGLOBAL['db']->query("SELECT size FROM ".tname('file')." WHERE discussion_id ='{$discussion_id}'");
		while ($value = $_SGLOBAL['db']->fetch_array($queryfile)) {
			$attachsize += intval($value['size']);
			$file_num++;
		}
	}
	if($valuetodoslist['is_completed'] == 0) {
		$todoslist_num = 1;
	}
	//删除
	if($discussion_id){
		$_SGLOBAL['db']->query("UPDATE ".tname('file')." SET `status`=1 WHERE discussion_id ='{$discussion_id}'");
		$_SGLOBAL['db']->query("UPDATE ".tname('discussion')." SET `status`=1 WHERE discussion_id ='{$discussion_id}'");
		$_SGLOBAL['db']->query("UPDATE ".tname('todoslist')." SET `status`=1 WHERE todoslist_id ='{$todoslist_id}'");
		//更新项目主题数和文档数
		$_SGLOBAL['db']->query("UPDATE ".tname('project')." SET todoslist_num=todoslist_num-{$todoslist_num},file_num=file_num-{$file_num},discussion_num=discussion_num-1 ,attachsize=attachsize-{$attachsize} WHERE project_id ='{$project_id}'");
		$_SGLOBAL['db']->query("UPDATE ".tname('group')." SET attachsize=attachsize-{$attachsize} WHERE group_id ='{$group_id}'");
	}else{
		//更新项目文档数
		$_SGLOBAL['db']->query("UPDATE ".tname('todoslist')." SET `status`=1 WHERE todoslist_id ='{$todoslist_id}'");
		$_SGLOBAL['db']->query("UPDATE ".tname('project')." SET todoslist_num=todoslist_num-{$todoslist_num} WHERE project_id ='{$project_id}'");
	}
	
	//添加事件
	trash_can_add('delete',  $project_id, $manageproject['name'], 'todoslistid', $todoslist_id, cplang('trash_can_todoslist_delete'), array('subject' => $valuetodoslist['subject']));
	notification_add('todoslist', 'delete',  $project_id, $manageproject['name'], 'todoslistid', $todoslist_id, cplang('notification_todoslist_delete'), array('subject' => $valuetodoslist['subject']));
	return $newid;
}
//从回收站恢复待办事宜清单
function restored_todoslist($project_id,$todoslist_id) {
	global $_SGLOBAL,$manageproject,$_SCONFIG,$group;

	//统计
	$newid = 0;
	$group_id = $_SGLOBAL['member']['group_id'];
	$discussion_id = 0;
	$attachsize = 0;
	$file_num = 0;
	$todoslist_num = 0;

	$query = $_SGLOBAL['db']->query("SELECT uid,todoslist_id,project_id,discussion_id,subject,status,is_completed FROM ".tname('todoslist')." WHERE todoslist_id ='{$todoslist_id}' LIMIT 1");
	if ($valuetodoslist = $_SGLOBAL['db']->fetch_array($query)) {
		if($project_id ==  $valuetodoslist['project_id'] && $valuetodoslist['status'] == 1) {
			$newid = $valuetodoslist['todoslist_id'];
			$discussion_id = $valuetodoslist['discussion_id'];
		}
	}
	if(empty($newid)) return 0;
	
	//获取主题附件
	if($discussion_id){
		$queryfile = $_SGLOBAL['db']->query("SELECT size FROM ".tname('file')." WHERE discussion_id ='{$discussion_id}'");
		while ($value = $_SGLOBAL['db']->fetch_array($queryfile)) {
			$attachsize += intval($value['size']);
			$file_num++;
		}

		//检查空间大小
		$maxattachsize = $group['maxattachsize'];//单位KB
		if($maxattachsize) {//0为不限制
			if($group['attachsize'] + $attachsize > $maxattachsize) {
				return 0;
			}
		}
	}
	if($valuetodoslist['is_completed'] == 0) {
		$todoslist_num = 1;
	}
	
	//恢复
	if($discussion_id){
		$_SGLOBAL['db']->query("UPDATE ".tname('file')." SET `status`=0 WHERE discussion_id ='{$discussion_id}'");
		$_SGLOBAL['db']->query("UPDATE ".tname('discussion')." SET `status`=0 WHERE discussion_id ='{$discussion_id}'");
		$_SGLOBAL['db']->query("UPDATE ".tname('todoslist')." SET `status`=0 WHERE todoslist_id ='{$todoslist_id}'");
		//更新项目主题数和文档数
		$_SGLOBAL['db']->query("UPDATE ".tname('project')." SET todoslist_num=todoslist_num+{$todoslist_num},file_num=file_num+{$file_num},discussion_num=discussion_num+1 ,attachsize=attachsize+{$attachsize} WHERE project_id ='{$project_id}'");
		$_SGLOBAL['db']->query("UPDATE ".tname('group')." SET attachsize=attachsize+{$attachsize} WHERE group_id ='{$group_id}'");
	}else{
		//更新项目文档数
		$_SGLOBAL['db']->query("UPDATE ".tname('todoslist')." SET `status`=0 WHERE todoslist_id ='{$todoslist_id}'");
		$_SGLOBAL['db']->query("UPDATE ".tname('project')." SET todoslist_num=todoslist_num+{$todoslist_num} WHERE project_id ='{$project_id}'");
	}
	
	//添加事件
	trash_can_add('restored',  $project_id, $manageproject['name'], 'todoslistid', $todoslist_id, cplang('restored_todoslist_delete'), array('subject' => $valuetodoslist['subject']));
	notification_add('todoslist', 'restored',  $project_id, $manageproject['name'], 'todoslistid', $todoslist_id, cplang('restored_todoslist_delete'), array('subject' => $valuetodoslist['subject']));
	return $newid;
}
//删除待办事宜清单
function deletetodoslist($project_id,$todoslist_id) {
	global $_SGLOBAL,$manageproject;

	//统计
	$newid = 0;
	$group_id = $_SGLOBAL['member']['group_id'];
	$discussion_id = 0;
	$attachsize = 0;
	$file_num = 0;

	$query = $_SGLOBAL['db']->query("SELECT uid,todoslist_id,project_id,discussion_id,subject,status FROM ".tname('todoslist')." WHERE todoslist_id ='{$todoslist_id}' LIMIT 1");
	if ($valuetodoslist = $_SGLOBAL['db']->fetch_array($query)) {
		if($project_id ==  $valuetodoslist['project_id'] && $valuetodoslist['status'] == 1) {
			$newid = $valuetodoslist['todoslist_id'];
			$discussion_id = $valuetodoslist['discussion_id'];
		}
	}
	if(empty($newid)) return 0;
	
	//删除
	if($discussion_id){
		$_SGLOBAL['db']->query("DELETE FROM ".tname('file')." WHERE discussion_id ='{$discussion_id}'");
		$_SGLOBAL['db']->query("DELETE FROM ".tname('post')." WHERE discussion_id ='{$discussion_id}'");
		$_SGLOBAL['db']->query("DELETE FROM ".tname('discussion')." WHERE discussion_id ='{$discussion_id}'");
		$_SGLOBAL['db']->query("DELETE FROM ".tname('notice_discussion')." WHERE discussion_id ='{$discussion_id}'");
	}
	//删除feed
	$_SGLOBAL['db']->query("DELETE FROM ".tname('notification')." WHERE object_id ='{$newid}' AND object_type='todoslistid'");
	$_SGLOBAL['db']->query("DELETE FROM ".tname('trash_can')." WHERE object_id ='{$newid}' AND object_type='todoslistid'");
	$_SGLOBAL['db']->query("DELETE FROM ".tname('trash_can_log')." WHERE object_id ='{$newid}' AND object_type='todoslistid'");
	$_SGLOBAL['db']->query("DELETE FROM ".tname('todoslist')." WHERE todoslist_id ='{$newid}'");

	return $newid;
}
//待办事宜清单已完成
function completed_can_todoslist($project_id,$todoslist_id) {
	global $_SGLOBAL,$manageproject;

	//统计
	$newid = 0;

	$query = $_SGLOBAL['db']->query("SELECT uid,todoslist_id,project_id,subject,status,is_completed FROM ".tname('todoslist')." WHERE todoslist_id ='{$todoslist_id}' LIMIT 1");
	if ($valuetodoslist = $_SGLOBAL['db']->fetch_array($query)) {
		if($project_id ==  $valuetodoslist['project_id'] && $valuetodoslist['status'] == 0) {
			$newid = $valuetodoslist['todoslist_id'];
		}
	}
	if(empty($newid)) return 0;
	
	if($valuetodoslist['is_completed'] == 0) {//待办事宜清单已完成
		//更新项目文档数
		$_SGLOBAL['db']->query("UPDATE ".tname('todoslist')." SET `is_completed`=1,`completed_uid`='{$_SGLOBAL['supe_uid']}',`completed_author`='{$_SGLOBAL['member']['fullname']}',`completed_date`='{$_SGLOBAL['timestamp']}' WHERE todoslist_id ='{$todoslist_id}'");
		$_SGLOBAL['db']->query("UPDATE ".tname('project')." SET todoslist_num=todoslist_num-1 WHERE project_id ='{$project_id}'");
		
		//添加事件
		notification_add('todoslist', 'completed',  $project_id, $manageproject['name'], 'todoslistid', $todoslist_id, cplang('notification_todoslist_completed'), array('subject' => $valuetodoslist['subject']));
	} else if($valuetodoslist['is_completed'] == 1) {//待办事宜清单取消完成
		//更新项目文档数
		$_SGLOBAL['db']->query("UPDATE ".tname('todoslist')." SET `is_completed`=0,`completed_uid`='0',`completed_author`='',`completed_date`='0' WHERE todoslist_id ='{$todoslist_id}'");
		$_SGLOBAL['db']->query("UPDATE ".tname('project')." SET todoslist_num=todoslist_num+1 WHERE project_id ='{$project_id}'");
		
		//添加事件
		notification_add('todoslist', 'nocompleted',  $project_id, $manageproject['name'], 'todoslistid', $todoslist_id, cplang('notification_todoslist_nocompleted'), array('subject' => $valuetodoslist['subject']));
	}
	return $newid;
}
?>