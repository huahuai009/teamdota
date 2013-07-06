<?php
/*
	[TEAMDOTA] (C) 2012 wanghui
	$Id: cp_people_new.php 2012-03-31 09:59Z duty $
*/

if(!defined('IN_TEAMDOTA')) {
	exit('Access Denied');
}
$theurl = 'cp.php?ac=people_new';
$siteurl = getsiteurl();
//添加编辑操作
if(submitcheck('peoplenewsubmit')) {
	$mails = $_POST['email_address'];//电子邮件
	$project_ids = $_POST['project_ids'];//项目
	$permissions_can_create_projects = empty($_POST['permissions_can_create_projects']) ? 0 : ($_SGLOBAL['member']['ntype'] > 0 ? 1 : 0);//创建项目
	$permissions_admin = empty($_POST['permissions_admin']) ? 0 : ($_SGLOBAL['member']['ntype'] > 0 ? 1 : 0);//管理员
	//过滤项目id
	if(is_array($project_ids)) {
		$query = $_SGLOBAL['db']->query("SELECT project_id FROM ".tname('project_member')." WHERE uid='".$_SGLOBAL['supe_uid']."'");
		$list_project = array();
		while($value = $_SGLOBAL['db']->fetch_array($query)) {
			$list_project[] = $value['project_id'];
		}
		$project_ids = array_intersect($project_ids,$list_project);//取交集
		$project_ids = array_unique($project_ids);//去重
	}
	//送入邮件队列
	set_time_limit(0);//设置超时时间
	$failingmail = array();
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
			$query = $_SGLOBAL['db']->query("SELECT uid,group_id,isactive FROM ".tname('member')." WHERE `email`='{$value}' LIMIT 1");
			if($member = $_SGLOBAL['db']->fetch_array($query)) {
				$failingmail[] = $value;
				continue;
			}
			
			//获取唯一code
			$code = strtolower(md5(microtime().random(6)));
			$setarr = array(
				'uid' => $_SGLOBAL['supe_uid'],
				'author' => $_SGLOBAL['member']['fullname'],
				'code' => $code,
				'fuid' => 0,
				'email' => $value,
				'logtime' => $_SGLOBAL['timestamp'],
				'sendtime' => $_SGLOBAL['timestamp'],
				'useip' => getonlineip(),
				'type' =>  1,
				'group_id' => $group['group_id']
			);
			$inviteid = inserttable('invite', $setarr, 1);
			if($inviteid) {
				//插入用户表
				$setmemberarr = array(
					'group_id' => $group['group_id'],
					'username' => $value,
					'password' => $code,
					'email' => $value,
					'fullname' => $value,
					'ntype' => $permissions_admin,
					'regip' => getonlineip(),
					'regdate' => $_SGLOBAL['timestamp'],
					'lastloginip' => 0,
					'lastlogintime' => $_SGLOBAL['timestamp'],
					'lastactivity' => $_SGLOBAL['timestamp'],
					'status' => 1,
					'isactive' => 1,
					'salt' => random(6),
					'is_create_project' => $permissions_can_create_projects,
					'timeoffset' => 8,
				);
				//更新本地用户库
				$newuid = inserttable('member', $setmemberarr, 1);
				if($newuid) {
					//更新邀请表
					updatetable('invite', array('fuid'=>$newuid), array('id'=>$inviteid));
					//插入用户项目关联表
					foreach($project_ids as $keypro => $valuepro) {
						inserttable('project_member', array('group_id'=>$group['group_id'],'project_id' => $valuepro,'logtime' => $_SGLOBAL['timestamp'],'uid' => $newuid,'type' => 0,'isactive' => 1), 1);
						//更新项目成员数
						$_SGLOBAL['db']->query("UPDATE ".tname('project')." SET member_num=member_num+1 WHERE project_id='{$valuepro}'");
					}
					
					$mailvar[5] = "{$siteurl}invite.php?{$inviteid}{$code}";
					createmail($value, $mailvar, $project_ids);
					
				} else {
					$failingmail[] = $value;
				}
			} else {
				$failingmail[] = $value;
			}
		}
	}
	if($failingmail && count($failingmail) > 1) {
		//showmessage('send_result_2', '', 1, array(implode('&nbsp;&nbsp;', $failingmail)));
		showmessage('send_result_1','group.php?do=people');
	} else {
		showmessage('send_result_1','group.php?do=people');
	}
}
if($_GET['op'] == 'resend') {
	
	$uid = $_GET['uid'] ? intval($_GET['uid']) : 0;
	
	if(empty($uid)) {
		echo cplang('send_result_3');
		exit;
	}
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('invite')." WHERE fuid='{$uid}' ORDER BY id ASC LIMIT 1");
	if($value = $_SGLOBAL['db']->fetch_array($query)) {
		if($value['type'] == 1) {
			if($_SGLOBAL['timestamp'] - $value['sendtime'] > 60) {
				$inviteurl = "{$siteurl}invite.php?{$value[id]}{$value[code]}";
				$mailvar[5] = $inviteurl;
				createmail($value['email'], $mailvar, 0);
				updatetable('invite', array('sendtime'=>$_SGLOBAL['timestamp']), array('id'=>$value['id']));
			}
		}
		echo cplang('send_result_1');
		exit;
	} else {
		echo cplang('send_result_3');
		exit;
	}
}
//读取项目
$query = $_SGLOBAL['db']->query("SELECT p.name, p.project_id FROM ".tname('project')." p , ".tname('project_member')." pm WHERE p.project_id=pm.project_id AND p.group_id='{$groupid}' AND pm.uid='".$_SGLOBAL['supe_uid']."' AND p.status=0");
$list_project = array();
while($value = $_SGLOBAL['db']->fetch_array($query)) {
	$list_project[] = $value;
}
include_once template("cp_people_new");
?>