<?php
/*
	[TEAMDOTA] (C) 2012 wanghui
	$Id: index.php 2012-03-31 09:59Z duty $
*/

include_once('./common.php');
if($_SGLOBAL['supe_uid']) {
	showmessage('do_success', "group.php?do=home", 0);
}
include_once(S_ROOT.'./source/default.php');
?>