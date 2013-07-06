<?php
/*
	[TEAMDOTA] (C) 2012 wanghui
	$Id: open_function_common.php 2012-03-31 09:59Z duty $
*/
//判断当前用户登录状态
function open_checkauth() {
	global $_SGLOBAL;
	if($_POST['token']) {
		@list($password, $uid) = explode("\t", authcode($_POST['token'], 'DECODE'));
		$_SGLOBAL['supe_uid'] = intval($uid);
		if($password && $_SGLOBAL['supe_uid']) {
			$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('member')." WHERE uid='$_SGLOBAL[supe_uid]'");
			if($member = $_SGLOBAL['db']->fetch_array($query)) {
				if($member['password'] == $password) {
					$_SGLOBAL['supe_username'] = addslashes($member['username']);
					if(empty($_SGLOBAL['member'])) {
						$_SGLOBAL['member'] = array('uid' => $member['uid'], 'group_id' => $member['group_id'], 'username' => $member['username'], 'email' => $member['email'], 'fullname' => $member['fullname'], 'ntype' => $member['ntype'], 'lastloginip' => $member['lastloginip'], 'lastlogintime' => $member['lastlogintime'], 'is_create_project' => $member['is_create_project'], 'timeoffset' => $member['timeoffset'], 'issubscribe' => $member['issubscribe']);
					}
				} else {
					$_SGLOBAL['supe_uid'] = 0;
				}
			} else {
				$_SGLOBAL['supe_uid'] = 0;
			}
		}
	}
	if(!empty($_SGLOBAL['supe_uid'])) {
		$session = array('uid' => $_SGLOBAL['supe_uid'], 'username' => $_SGLOBAL['supe_username'], 'password' => $password);
		$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('session')." WHERE `uid`='$_SGLOBAL[supe_uid]'");
		if(!$member = $_SGLOBAL['db']->fetch_array($query)) {
			insertsession($session);//登录
		}
	}
}
//检查是否登录
function open_checklogin() {
	global $_SGLOBAL;

	if(empty($_SGLOBAL['supe_uid'])) {
		open_showmessage($_SGLOBAL['open_errorinfo'][1001]);
	}
}
//对话框
function open_showmessage($msgkey, $content='',$values=array()) {
	if(!empty($content)) {
		$msgkey['content'] = $content;
	}
	if(!empty($values)) {
		$msgkey['data'] = $values;
	}
	echo json_encode($msgkey);
	exit();
}
function open_checkgroup() {
	global $_SGLOBAL,$group;
	//获取空间
	$groupid = $_SGLOBAL['member']['group_id'];
	$group = getgroup($groupid);

	if($group) {
		
		$_SGLOBAL['group_is_time_end'] = 0;

		//验证空间是否被锁定
		if($group['flag'] == 1) {
			open_showmessage($_SGLOBAL['open_errorinfo'][1003]);
		}
	} else{
		open_showmessage($_SGLOBAL['open_errorinfo'][1002]);
	}

	//更新活动session
	if($_SGLOBAL['supe_uid']) {
		updatetable('session', array('lastactivity' => $_SGLOBAL['timestamp']), array('uid'=>$_SGLOBAL['supe_uid']));
	}
}
//检查项目权限
function open_checkproject($project_id) {
	global $_SGLOBAL, $group;
	
	$var = 'checkproject_'.$project_id;
	if(!isset($_SGLOBAL[$var])) {
		if(empty($_SGLOBAL['supe_uid'])) {
			$_SGLOBAL[$var] = '';
		} else {
			$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('project')." WHERE `project_id`='{$project_id}' LIMIT 1");
			if($value = $_SGLOBAL['db']->fetch_array($query)) {	
				if($value['group_id'] == $group['group_id']){
					$setarr = array('project_id'=>$value['project_id'],'group_id'=>$value['group_id'],'uid'=>$value['uid'],'name'=>$value['name'],'description'=>$value['description'],'discussion_num'=>$value['discussion_num'],'file_num'=>$value['file_num'],'document_num'=>$value['document_num'],'member_num'=>$value['member_num'],'todoslist_num'=>$value['todoslist_num'],'status'=>$value['status']);
					$querycheck = $_SGLOBAL['db']->query("SELECT * FROM ".tname('project_member')." WHERE `project_id`='{$project_id}' AND `uid`='".$_SGLOBAL['member']['uid']."' LIMIT 1");
					if($querycheck = $_SGLOBAL['db']->fetch_array($querycheck)) {
						$_SGLOBAL[$var] = $setarr;
					}else{
						$_SGLOBAL[$var] = '';
					}
				}else{
					$_SGLOBAL[$var] = '';
				}
			}else{
				$_SGLOBAL[$var] = '';
			}
		}
	}
	return $_SGLOBAL[$var];
}
?>