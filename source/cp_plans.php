<?php
/*
	[TEAMDOTA] (C) 2012 wanghui
	$Id: cp_discussion.php 2012-03-31 09:59Z duty $
*/

if(!defined('IN_TEAMDOTA')) {
	exit('Access Denied');
}
if($_SGLOBAL['supe_uid'] != $group['uid']) {
	showmessage('links_does_not_exist');
}
//编辑帐户名
if(submitcheck('groupsubmit')) {
	$new_group_name = trim($_POST['account_name']);
	if(empty($new_group_name)) {
		showmessage('group_format_is_wrong', 'cp.php?ac=group');
	}
	updatetable('group', array('group_name'=>$new_group_name), array('group_id'=>$groupid));
	showmessage('do_success','cp.php?ac=group',0);
}
//编辑帐户所有者
if(submitcheck('groupownersubmit')) {
	$owner_id = empty($_POST['owner_id']) ? 0 : intval($_POST['owner_id']);
	if(empty($owner_id)) {
		showmessage('links_does_not_exist','cp.php?ac=group');
	}
	if($owner_id == $group['uid']) {
		showmessage('do_success','cp.php?ac=group',0);
	}
	//用户信息
	$query = $_SGLOBAL['db']->query("SELECT uid,group_id FROM ".tname('member')." WHERE uid='{$owner_id}' LIMIT 1");
	if(!$member = $_SGLOBAL['db']->fetch_array($query)) {
		showmessage('links_does_not_exist');
	}
	if($groupid != $member['group_id']){
		showmessage('links_does_not_exist');
	}
	updatetable('group', array('uid'=>$owner_id), array('group_id'=>$groupid));
	updatetable('member', array('ntype'=>1), array('uid'=>$owner_id));
	updatetable('member', array('ntype'=>2), array('uid'=>$_SGLOBAL['supe_uid']));
	showmessage('do_success','group.php?do=home',0);
}
//提交套餐订单
if(submitcheck('planssubmit')) {
	$plan_id = empty($_POST['plan_id']) ? 0 : intval($_POST['plan_id']);
	$alipay_orderid = trim($_POST['alipay_orderid']);
	$alipay_username = trim($_POST['alipay_username']);
	$expires_on_year = empty($_POST['expires_on_year']) ? 0 : intval($_POST['expires_on_year']);
	$expires_on_month = empty($_POST['expires_on_month']) ? 0 : intval($_POST['expires_on_month']);
	$remarks = getstr(trim($_POST['remarks']), 100, 1, 1, 1);
	if(empty($plan_id) || $plan_id > 4) {
		showmessage('plans_plan_id_format_is_wrong');
	}
	if(empty($alipay_orderid) || strlen($alipay_orderid) != 16) {
		showmessage('plans_alipay_orderid_format_is_wrong');
	}
	if($count = getcount('plans_order', array('alipay_orderid'=>$alipay_orderid))) {
		showmessage('plans_alipay_orderid_already_exists');
	}
	$setarr['group_id'] = $groupid;
	$setarr['uid'] = $_SGLOBAL['supe_uid'];
	$setarr['plan_id'] = $plan_id;
	$setarr['alipay_orderid'] = $alipay_orderid;
	$setarr['alipay_username'] = $alipay_username;
	$setarr['expires_year'] = $expires_on_year;
	$setarr['expires_month'] = $expires_on_month;
	$setarr['remarks'] = $remarks;
	$setarr['logtime'] = $_SGLOBAL['timestamp'];
	$setarr['status'] = 0;
	inserttable('plans_order', $setarr, 1);
	showmessage('do_success','group.php?do=home',0);
}
//读取成员
$query = $_SGLOBAL['db']->query("SELECT uid,fullname FROM ".tname('member')." WHERE group_id='{$groupid}' ORDER BY regdate ASC");
$list = array();
while($value = $_SGLOBAL['db']->fetch_array($query)) {
	$list[] = $value;
}
include_once template("cp_plans");
?>