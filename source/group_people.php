<?php
/*
	[TEAMDOTA] (C) 2012 wanghui
	$Id: group_people.php 2012-03-31 09:59Z duty $
*/

if(!defined('IN_TEAMDOTA')) {
	exit('Access Denied');
}

//分页
$page = empty($_GET['page'])?1:intval($_GET['page']);
if($page<1) $page=1;
$uid = empty($_GET['uid'])?0:intval($_GET['uid']);
if($uid) {
	//用户信息
	$query = $_SGLOBAL['db']->query("SELECT uid,group_id,fullname,email,lastactivity,isactive FROM ".tname('member')." WHERE uid='{$uid}' LIMIT 1");
	if(!$member = $_SGLOBAL['db']->fetch_array($query)) {
		showmessage('links_does_not_exist');
	}
	if($groupid != $member['group_id']){
		showmessage('links_does_not_exist');
	}
	if($member['isactive'] == 0) {//用户已激活
		//读取项目
		$query = $_SGLOBAL['db']->query("SELECT project_id FROM ".tname('project_member')." WHERE uid='".$_SGLOBAL['supe_uid']."'");
		$arr_project_id = array();
		while($value = $_SGLOBAL['db']->fetch_array($query)) {
			$arr_project_id[] = $value['project_id'];
		}
		
		
		//用户操作列表
		$perpage = 25;
		$start = ($page-1)*$perpage;

		$wheresql = "`sender_id`='{$uid}' AND project_id IN (".simplode($arr_project_id).") AND `status`=0";
		$theurl = "group.php?do=people&uid={$uid}";

		$list = array();
		$count = 0;
		$count = $_SGLOBAL['db']->result($_SGLOBAL['db']->query("SELECT COUNT(*) FROM ".tname('notification')." WHERE $wheresql"),0);
		if($count) {
			$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('notification')." WHERE $wheresql ORDER BY created_time DESC LIMIT $start,$perpage");
			while ($value = $_SGLOBAL['db']->fetch_array($query)) {
				$value = mknotification($value);
				$list[] = $value;
			}
		}
		if($count > $perpage) {
			//分页
			$pagenumbers = getpage($page, $perpage, $count, $theurl);
		}
	} else {
		$query_invite = $_SGLOBAL['db']->query("SELECT id,uid,code,sendtime,author FROM ".tname('invite')." WHERE fuid='{$uid}' ORDER BY id ASC LIMIT 1");
		if($invite = $_SGLOBAL['db']->fetch_array($query_invite)) {
			$inviteurl = getsiteurl()."invite.php?{$invite[id]}{$invite[code]}";
		}
	}
	include_once template("group_people_view");
} else {
	$perpage = 20;
	$start = ($page-1)*$perpage;
	$list = array();
	$count = 0;

	if($_SGLOBAL['member']['ntype'] > 0) {
		$wheresql = "`group_id`='{$groupid}'";
	} else {
		//读取项目
		$query = $_SGLOBAL['db']->query("SELECT project_id FROM ".tname('project_member')." WHERE uid='".$_SGLOBAL['supe_uid']."'");
		$arr_project_id = array();
		while($value = $_SGLOBAL['db']->fetch_array($query)) {
			$arr_project_id[] = $value['project_id'];
		}
		$query = $_SGLOBAL['db']->query("SELECT distinct(uid) FROM ".tname('project_member')." WHERE project_id IN (".simplode($arr_project_id).")");
		$arr_uid = array();
		while($value = $_SGLOBAL['db']->fetch_array($query)) {
			$arr_uid[] = $value['uid'];
		}
		$wheresql = "uid IN (".simplode($arr_uid).")";
	}
	$theurl = "group.php?do=people";

	$count = $_SGLOBAL['db']->result($_SGLOBAL['db']->query("SELECT COUNT(*) FROM ".tname('member')." WHERE $wheresql"),0);
	if($count) {
		$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('member')." WHERE $wheresql ORDER BY regdate ASC LIMIT $start,$perpage");
		while ($value = $_SGLOBAL['db']->fetch_array($query)) {
			$list[] = $value;
		}
	}
	if($count > $perpage) {
		//分页
		$pagenumbers = getpage($page, $perpage, $count, $theurl);
	}
	include_once template("group_people");
}
?>