<?php
/*
	[TEAMDOTA] (C) 2012 wanghui
	$Id: cp_project.php 2012-03-31 09:59Z duty $
*/

if(!defined('IN_TEAMDOTA')) {
	exit('Access Denied');
}
//检查信息
$project_id = empty($_REQUEST['project_id'])?0:intval($_REQUEST['project_id']);
$op = empty($_GET['op'])?'':$_GET['op'];

$allowmanage = $_SGLOBAL['member']['ntype'];
if($project_id) {
	$manageproject = checkproject($project_id);
	if(!$manageproject) {
		showmessage('project_not_allowed_to_visit','group.php?do=home');
	}
	if(!check_project_manage($manageproject['uid'])) {
		showmessage('project_not_allowed_to_visit','group.php?do=home');
	}
	
}else{
	if($group['gtype']) {//套餐用户
		if($_SGLOBAL['group_is_time_end']) {//时间过期,如果项目不超过免费用户的项目数，则可以创建
			if($_SCONFIG['group_gtype'][0]) {
				if($group['project_num'] >= $_SCONFIG['group_gtype'][0]) {
					showmessage('project_not_allowed_to_create','group.php?do=home');
				}
			}
		} else {//时间未到期,检查项目数
			if($_SCONFIG['group_gtype'][$group['gtype']]) {
				if($group['project_num'] >= $_SCONFIG['group_gtype'][$group['gtype']]) {
					showmessage('project_not_allowed_to_create','group.php?do=home');
				}
			}
		}
	} else {//免费用户
		if($_SCONFIG['group_gtype'][$group['gtype']]) {
			if($group['project_num'] >= $_SCONFIG['group_gtype'][$group['gtype']]) {
				showmessage('project_not_allowed_to_create','group.php?do=home');
			}
		}
	}
	//判断是否能创建项目
	if(!$_SGLOBAL['member']['is_create_project'] && !$allowmanage) {
		showmessage('project_not_allowed_to_visit','group.php?do=home');
	}
}

//添加编辑操作
if(submitcheck('projectsubmit')) {
	$siteurl = getsiteurl();
	//检查信息
	$project_name = getstr($_POST['project_name'], 50, 1, 1, 1);
	if(strlen($project_name) < 1) showmessage('project_name_error');
	$project_description = getstr($_POST['project_description'], 200, 1, 1, 1);
	if($project_description == '添加描述或额外的细节（可选）' || $project_description == '添加描述或额外的细节') {
		$project_description = '';
	}
	$setarr = array(
		'name' => $project_name,
		'description' => $project_description
	);
	if(empty($project_id)) {
		$setarr['group_id'] = $group['group_id'];
		$setarr['uid'] = $_SGLOBAL['supe_uid'];
		$setarr['author'] = $_SGLOBAL['member']['fullname'];
		$setarr['logtime'] = $_SGLOBAL['timestamp'];
		$setarr['useip'] = getonlineip();
		$setarr['member_num'] = 1;
		$project_id = inserttable('project', $setarr, 1);
		if($project_id) {
			//添加事件
			notification_add('project', 'create',  $project_id, $project_name, 'projectid', $project_id, cplang('notification_project_create'), array('subject' => $setarr['name']));
			$_SGLOBAL['db']->query("UPDATE ".tname('group')." SET all_project_num=all_project_num+1, project_num=project_num+1 WHERE group_id='{$group[group_id]}'");
			inserttable('project_member', array('group_id'=>$group['group_id'],'project_id'=>$project_id,'logtime'=>$_SGLOBAL['timestamp'],'uid'=>$_SGLOBAL['supe_uid'],'type'=>1), 1);
			
			//发送邀请邮件
			$failingmail = array();
			$mails = $_POST['email_address'];
			if(is_array($mails)) {
				$mails = array_unique($mails);
				foreach($mails as $key => $value) {
					$value = saddslashes(trim($value));
					//检测email的合法性
					if(empty($value) || !isemail($value)) {
						$failingmail[] = $value;
						continue;
					}
					//检测email是否已存在
					$ishas_mem = 0;//用户是否已存在
					$isactive_mem = 1;//用户是否已激活
					$query = $_SGLOBAL['db']->query("SELECT uid,group_id,isactive FROM ".tname('member')." WHERE `email`='{$value}' LIMIT 1");
					if($member = $_SGLOBAL['db']->fetch_array($query)) {
						$ishas_mem = $member['uid'];
						$isactive_mem = $member['isactive'];
						if($member['group_id'] != $group['group_id'] || $member['uid'] == $_SGLOBAL['supe_uid']) {
							$failingmail[] = $value;
							continue;
						}
					}
					
					//获取唯一code
					$code = strtolower(md5(microtime().random(6)));
					$setarr = array(
						'uid' => $_SGLOBAL['supe_uid'],
						'author' => $_SGLOBAL['member']['fullname'],
						'code' => $code,
						'fuid' => $ishas_mem,
						'email' => $value,
						'logtime' => $_SGLOBAL['timestamp'],
						'sendtime' => $_SGLOBAL['timestamp'],
						'useip' => getonlineip(),
						'type' =>  1,
						'group_id' => $group['group_id']
					);
					if($ishas_mem) {//用户已存在
						if($isactive_mem) {//用户未激活
							$query_invite = $_SGLOBAL['db']->query("SELECT id,uid,code,sendtime FROM ".tname('invite')." WHERE fuid='{$ishas_mem}' ORDER BY id ASC LIMIT 1");
							$invite = $_SGLOBAL['db']->fetch_array($query_invite);
						}
						//插入用户项目关联表
						inserttable('project_member', array('group_id'=>$group['group_id'],'project_id' => $project_id,'logtime' => $_SGLOBAL['timestamp'],'uid' => $ishas_mem,'type' => 0,'isactive' => $isactive_mem), 1);
						//更新项目成员数
						$_SGLOBAL['db']->query("UPDATE ".tname('project')." SET member_num=member_num+1 WHERE project_id='{$project_id}'");
						//添加事件
						notification_add('invite', 'create',  $project_id, $project_name, 'projectid', $project_id, cplang('notification_project_invite'), array('invite' => $value,'subject' => $project_name));
						
						$mailvar[5] = $isactive_mem ? "{$siteurl}invite.php?{$invite[id]}{$invite[code]}" : "{$siteurl}group.php?do=project&project_id={$project_id}";
						createmail($value, $mailvar, array($project_id), $ishas_mem);
						
					} else {//用户未存在
						$inviteid = inserttable('invite', $setarr, 1);
						if($inviteid) {
							//插入用户表
							$is_create_project = $_SGLOBAL['member']['ntype'] > 0 ? 1 : 0;
							$setmemberarr = array(
								'group_id' => $group['group_id'],
								'username' => $value,
								'password' => $code,
								'email' => $value,
								'fullname' => $value,
								'ntype' => 0,
								'regip' => getonlineip(),
								'regdate' => $_SGLOBAL['timestamp'],
								'lastloginip' => 0,
								'lastlogintime' => $_SGLOBAL['timestamp'],
								'lastactivity' => $_SGLOBAL['timestamp'],
								'status' => 1,
								'isactive' => 1,
								'salt' => random(6),
								'is_create_project' => $is_create_project,
								'timeoffset' => 8,
							);
							//更新本地用户库
							$newuid = inserttable('member', $setmemberarr, 1);
							if($newuid) {
								//更新邀请表
								updatetable('invite', array('fuid'=>$newuid), array('id'=>$inviteid));
								//插入用户项目关联表
								inserttable('project_member', array('group_id'=>$group['group_id'],'project_id' => $project_id,'logtime' => $_SGLOBAL['timestamp'],'uid' => $newuid,'type' => 0,'isactive' => 1), 1);
								//更新项目成员数
								$_SGLOBAL['db']->query("UPDATE ".tname('project')." SET member_num=member_num+1 WHERE project_id='{$project_id}'");
								//添加事件
								notification_add('invite', 'create',  $project_id, $project_name, 'projectid', $project_id, cplang('notification_project_invite'), array('invite' => $value,'subject' => $project_name));
								
								$mailvar[5] = "{$siteurl}invite.php?{$inviteid}{$code}";
								createmail($value, $mailvar, array($project_id), 0);
								
							} else {
								$failingmail[] = $value;
							}
						} else {
							$failingmail[] = $value;
						}
					}
				}
			}
		}
	} else {
		if($manageproject['status'] != 0) {
			showmessage('failed_to_operation');
		}
		updatetable('project', $setarr, array('project_id'=>$project_id));
		//添加事件
		notification_add('project', 'update',  $project_id, $manageproject['name'], 'projectid', $project_id, cplang('notification_project_update'), array('subject' => $setarr['name']));
	}
	showmessage('do_success','group.php?do=home');
}
//存档或激活项目操作
if(submitcheck('projectarchivedsubmit')) {
	$status = empty($_POST['project_archived'])?0:intval($_POST['project_archived']);
	if($status == 0) {//把项目置为活跃
		if($manageproject['status'] == 1) {//项目当前处于锁定则进行操作
			if($_SCONFIG['group_gtype'][$group['gtype']]) {//判断项目数是否吻合
				if($group['project_num'] >= $_SCONFIG['group_gtype'][$group['gtype']]) {
					showmessage('project_not_allowed_to_create');
				}
			}
			updatetable('project', array('status'=>$status), array('project_id'=>$project_id));
			$_SGLOBAL['db']->query("UPDATE ".tname('group')." SET project_num=project_num+1 WHERE group_id='{$group[group_id]}'");
		}
	} elseif($status == 1) {//把项目置为锁定
		if($manageproject['status'] == 0) {//项目当前处于锁定则进行操作
			updatetable('project', array('status'=>$status), array('project_id'=>$project_id));
			$_SGLOBAL['db']->query("UPDATE ".tname('group')." SET project_num=project_num-1 WHERE group_id='{$group[group_id]}'");
		}
	}
	showmessage('do_success',"group.php?do=project&project_id={$project_id}");
}

if($_GET['op'] == 'delete') {//送入回收站
	if($project_id) {
		include_once(S_ROOT.'./source/function_delete.php');
		if(trash_can_projects($project_id)) {
			showmessage('do_success','group.php?do=home',0);
		} else {
			showmessage('failed_to_delete_operation');
		}
	} else {
		showmessage('failed_to_delete_operation');
	}
} elseif($_GET['op'] == 'restored') {//恢复
	if($project_id) {
		include_once(S_ROOT.'./source/function_delete.php');
		if(restored_projects($project_id)) {
			showmessage('do_success',"group.php?do=project&project_id={$project_id}",0);
		} else {
			showmessage('failed_to_restored_operation');
		}
	} else {
		showmessage('failed_to_restored_operation');
	}
} elseif($_GET['op'] == 'realdelete') {//真正删除
	if($project_id) {
		include_once(S_ROOT.'./source/function_delete.php');
		if(deleteprojects($project_id)) {
			showmessage('do_success','group.php?do=home',0);
		} else {
			showmessage('failed_to_delete_operation');
		}
	} else {
		showmessage('failed_to_delete_operation');
	}
} elseif($_GET['op'] == 'deletemember') {//删除项目成员
	$deleteuid = $_GET['deleteuid'] ? intval($_GET['deleteuid']) : 0;
	if(submitcheck('deletemembersubmit')) {
		if(!empty($deleteuid) && !empty($project_id)) {
			if($manageproject['status'] != 0) {
				showmessage('failed_to_operation');
			}
			include_once(S_ROOT.'./source/function_delete.php');
			if(deleteprojects_member($project_id,$deleteuid)) {
				showmessage('do_success',"group.php?do=project&project_id={$project_id}");
			} else {
				showmessage('failed_to_delete_operation');
			}
		} else {
			showmessage('failed_to_delete_operation');
		}
	}
}

include_once template("cp_project");

?>