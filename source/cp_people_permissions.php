<?php
/*
	[TEAMDOTA] (C) 2012 wanghui
	$Id: cp_people_permissions 2012-03-31 09:59Z duty $
*/

if(!defined('IN_TEAMDOTA')) {
	exit('Access Denied');
}
if($_SGLOBAL['member']['ntype'] <= 0) {
	showmessage('no_privilege');
}

if($_GET['op'] == 'edit') {
	$uid = empty($_GET['uid']) ? 0 : intval($_GET['uid']);
	$edittype = empty($_GET['edittype']) ? 0 : intval($_GET['edittype']);//0为修改为管理员，1位可以创建项目
	$editvalue = empty($_GET['editvalue']) ? 0 : intval($_GET['editvalue']);//
	if(empty($uid)){
		showmessage('no_privilege_manage_group_members');
	}
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('member')." WHERE uid='$uid'");
	$emember = $_SGLOBAL['db']->fetch_array($query);
	if(!empty($emember)) {
		if($groupid != $emember['group_id']) {
			showmessage('no_privilege_manage_group_members');
		}
		if($emember['ntype'] == 1) {
			showmessage('no_privilege_manage_group_members');
		}
		if($edittype == 0) { 
			if($editvalue == 0) {
				updatetable('member', array('ntype' => 0), array('uid'=>$uid));
			} else {
				updatetable('member', array('ntype' => 2,'is_create_project' => 1), array('uid'=>$uid));
			}
		} else {
			if($editvalue == 0) {
				updatetable('member', array('is_create_project' => 0), array('uid'=>$uid));
			} else {
				updatetable('member', array('is_create_project' => 1), array('uid'=>$uid));
			}
		}
		showmessage('do_success','cp.php?ac=people_permissions');
	} else {
		showmessage('no_privilege_manage_group_members');
	}
}
//分页
$page = empty($_GET['page'])?1:intval($_GET['page']);
if($page<1) $page=1;

$perpage = 30;
$start = ($page-1)*$perpage;

$wheresql = "`group_id`='{$groupid}'";
$theurl = "cp.php?ac=people_permissions";

$list = array();
$count = 0;
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
include_once template("cp_people_permissions");

?>