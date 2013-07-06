<?php
/*
	[TEAMDOTA] (C) 2012 wanghui
	$Id: do.php 2012-03-31 09:59Z duty $
*/

include_once('./common.php');

//获取方法
$ac = empty($_GET['ac'])?'':$_GET['ac'];

//允许的方法
$acs = array('login', 'register', 'forgot_password', 'lostpasswd', 'seccode', 'sendmail', 'emailcheck','ajax');
if(empty($ac) || !in_array($ac, $acs)) {
	showmessage('enter_the_index', 'index.php', 0);
}

//链接
$theurl = 'do.php?ac='.$ac;

include_once(S_ROOT.'./source/do_'.$ac.'.php');

?>