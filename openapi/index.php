<?php
/*
	[TEAMDOTA] (C) 2012 wanghui
	$Id: openapi/index.php 2012-07-24 09:59Z duty $
*/

include_once('./common.php');

//允许动作
$ms = array('login' , 'project' , 'discussion' , 'document' , 'todos' , 'todoslist' , 'people' , 'attachment');

//获取变量
$m = (empty($_GET['m']) || !in_array($_GET['m'], $ms))? open_showmessage($_SGLOBAL['open_errorinfo'][1000]) :$_GET['m'];
$a = empty($_GET['a'])?'':$_GET['a'];

//处理
include_once(S_ROOT."./openapi/source/open_{$m}.php");

?>