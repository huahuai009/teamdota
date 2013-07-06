<?php
/*
	[TEAMDOTA] (C) 2012 wanghui
	$Id: cp_invite.php 2012-03-31 09:59Z duty $
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
$siteurl = getsiteurl();
//添加
if(submitcheck('emailinvite')) {
	set_time_limit(0);//设置超时时间
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
				if($member['group_id'] == $group['group_id'] && $member['uid'] != $_SGLOBAL['supe_uid']) {
					$querypro = $_SGLOBAL['db']->query("SELECT * FROM ".tname('project_member')." WHERE `project_id`='{$project_id}' AND `uid`='{$member[uid]}' LIMIT 1");
					if($memberpro = $_SGLOBAL['db']->fetch_array($querypro)) {
						$failingmail[] = $value;
						continue;
					} 
				} else {
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
				notification_add('invite', 'create',  $project_id, $manageproject['name'], 'projectid', $project_id, cplang('notification_project_invite'), array('invite' => $value,'subject' => $manageproject['name']));
				
				$mailvar[5] = $isactive_mem ? "{$siteurl}invite.php?{$invite[id]}{$invite[code]}" : "{$siteurl}group.php?do=project&project_id={$project_id}";
				createmail($value, $mailvar, array($project_id), $ishas_mem, $_POST['message']);
				
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
						notification_add('invite', 'create',  $project_id, $manageproject['name'], 'projectid', $project_id, cplang('notification_project_invite'), array('invite' => $value,'subject' => $manageproject['name']));
					
						$mailvar[5] = "{$siteurl}invite.php?{$inviteid}{$code}";
						createmail($value, $mailvar, array($project_id), 0, $_POST['message']);
						
					} else {
						$failingmail[] = $value;
					}
				} else {
					$failingmail[] = $value;
				}
			}
		}
	}
	if($failingmail && count($failingmail) > 1) {
		//showmessage('send_result_2', '', 1, array(implode('&nbsp;&nbsp;', $failingmail)));
		showmessage('send_result_1');
	} else {
		showmessage('send_result_1');
	}
}

if($_GET['op'] == 'resend') {
	
	$uid = $_GET['uid'] ? intval($_GET['uid']) : 0;
	
	if(empty($uid)) {
		showmessage('send_result_3');
	}
	if(submitcheck('resendsubmit')) {
		$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('invite')." WHERE fuid='{$uid}' ORDER BY id ASC LIMIT 1");
		if($value = $_SGLOBAL['db']->fetch_array($query)) {
			if($value['type'] == 1) {
				if($_SGLOBAL['timestamp'] - $value['sendtime'] > 60) {
					$inviteurl = "{$siteurl}invite.php?{$value[id]}{$value[code]}";
					$mailvar[5] = $inviteurl;
					createmail($value['email'], $mailvar, array($project_id), $uid);
					updatetable('invite', array('sendtime'=>$_SGLOBAL['timestamp']), array('id'=>$value['id']));
				}
				showmessage('send_result_1', $_POST['refer']);
			}
		} 
		showmessage('send_result_3');
	}
}
?>