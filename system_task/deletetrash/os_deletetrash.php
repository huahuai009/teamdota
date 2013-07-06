<?php
@define('IN_TEAMDOTA', TRUE);
define('S_ROOT', dirname(__FILE__).DIRECTORY_SEPARATOR);
require_once S_ROOT.'./config.php';
require_once S_ROOT.'./source/class_mysql.php';
require_once S_ROOT.'./source/function_common.php';

//防止超时
set_time_limit(0);
 
while(true) {
	//时间
	$mtime = explode(' ', microtime());
	$_SC['timestamp'] = $mtime[1];
	
	$lockfile = S_ROOT.'./data/deletetrash.lock';
	@$filemtime = filemtime($lockfile);

	if($_SC['timestamp'] - $filemtime > $_SC['deletewaittime']) {
		touch($lockfile);
		$_SC['db'] = new dbstuff;
		$_SC['db']->charset = $_SC['dbcharset'];
		$_SC['db']->connect($_SC['dbhost'], $_SC['dbuser'], $_SC['dbpw'], $_SC['dbname'], $_SC['pconnect']);
		
		//查找需要删除的数据
		$deletetime = $_SC['timestamp'] - $_SC['deletetime'];
		$query = $_SC['db']->query("SELECT trash_id,object_id,object_type,created_time FROM ".tname('trash_can_log')." WHERE created_time < {$deletetime} ORDER BY created_time ASC");
		while($value = $_SC['db']->fetch_array($query)) {
			if($value['object_type'] == 'projectid') {
				deleteprojects($value['object_id']);
			} elseif($value['object_type'] == 'discussionid') {
				deletediscussions($value['object_id']);
			} elseif($value['object_type'] == 'documentid') {
				deletedocuments($value['object_id']);
			} elseif($value['object_type'] == 'attachmentid') {
				deletefiles($value['object_id']);
			} elseif($value['object_type'] == 'todosid') {
				deletetodos($value['object_id']);
			} elseif($value['object_type'] == 'todoslistid') {
				deletetodoslist($value['object_id']);
			}
			runlog('delete', "trash_id-{$value[trash_id]},object_id-{$value[object_id]},object_type-{$value[object_type]},created_time-{$value[created_time]} delete success.");
		}
		$_SC['db']->close();
	}
	sleep(10); //暂停10秒钟后，再次循环
}
?>