<?php
/*
	[TEAMDOTA] (C) 2012 wanghui
	$Id: function_cp.php  2012-03-31 09:59Z duty $
*/

if(!defined('IN_TEAMDOTA')) {
	exit('Access Denied');
}

$pernum = 1;//一次发送邮件个数，太多容易超时和服务器被封杀

ssetcookie('sendmail', '1', 300);//用户每5分钟调用本程序
$lockfile = S_ROOT.'./data/sendmail.lock';
@$filemtime = filemtime($lockfile);

if($_SGLOBAL['timestamp'] - $filemtime < 5) exit();

touch($lockfile);

//防止超时
set_time_limit(0);

//获取发送队列
$list = $sublist = $cids = $touids = array();
$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('mailcron')." WHERE sendtime<='$_SGLOBAL[timestamp]' ORDER BY sendtime LIMIT 0,$pernum");
while ($value = $_SGLOBAL['db']->fetch_array($query)) {
	if($value['touid']) $touids[$value['touid']] = $value['touid'];
	$cids[] = $value['cid'];
	$list[$value['cid']] = $value;
}

if(empty($cids)) exit();

//邮件内容
$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('mailqueue')." WHERE cid IN (".simplode($cids).")");
while ($value = $_SGLOBAL['db']->fetch_array($query)) {
	$sublist[$value['cid']][] = $value;
}

//删除邮件
$_SGLOBAL['db']->query("DELETE FROM ".tname('mailcron')." WHERE cid IN (".simplode($cids).")");
$_SGLOBAL['db']->query("DELETE FROM ".tname('mailqueue')." WHERE cid IN (".simplode($cids).")");

//开始发送
include_once(S_ROOT.'./source/function_sendmail.php');
foreach ($list as $cid => $value) {
	$mlist = $sublist[$cid];
	if($value['email'] && $mlist) {
		$subject = getstr($mlist[0]['subject'], 80, 0, 0, 0, 0, -1);
		$message = $mlist[0]['message'];
		if(!sendmail($value['email'], $subject, $message)) {
			runlog('sendmail', "$value[email] sendmail failed.");
		}
	}
}

?>