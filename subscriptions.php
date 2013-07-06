<?php
/*
	[TEAMDOTA] (C) 2012 wanghui
	$Id: subscriptions.php 2012-05-22 09:59Z duty $
*/

include_once('./common.php');
//参数

$objectid = intval($_GET['d']);
$uid = intval($_GET['u']);
$do = $_GET['do'];
$time = $_GET['time'];
$code = $_GET['code'];
if(empty($objectid) || empty($uid) || empty($time) || empty($code)) {
	showmessage('links_does_not_exist');
}
$sign = md5("d={$objectid}&u={$uid}&time={$time}{$_SCONFIG['sitekey']}");
if($sign != $code) {
	showmessage('links_does_not_exist');
}

include_once(S_ROOT.'./source/function_cp.php');
if($do == 'dialy') {
	subscriptions_dialy($uid);
} elseif($do == 'attachment') {
	subscriptions_notice_attachment($objectid, $uid);
} else {
	subscriptions_notice_discussion($objectid, $uid);
}
include_once template('subscriptions');
?>