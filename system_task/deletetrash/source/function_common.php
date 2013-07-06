<?php
/*
	[TEAMDOTA] (C) 2012 wanghui
	$Id: function_common.php 2012-03-31 09:59Z duty $
*/

if(!defined('IN_TEAMDOTA')) {
	exit('Access Denied');
}
//获取到表名
function tname($name) {
	global $_SC;
	return $_SC['tablepre'].$name;
}

//删除项目
function deleteprojects($project_id) {
	global $_SC;
	$newid = 0;
	$query = $_SC['db']->query("SELECT project_id,status FROM ".tname('project')."  WHERE `project_id` ='{$project_id}' LIMIT 1");
	if($value = $_SC['db']->fetch_array($query)){
		if($value['status'] == 2) {
			$newid = $value['project_id'];
		}
	}
	if(empty($newid)) return 0;
	
	//删除
	$_SC['db']->query("DELETE FROM ".tname('document')." WHERE project_id ='{$newid}'");
	$_SC['db']->query("DELETE FROM ".tname('discussion')." WHERE project_id ='{$newid}'");
	$_SC['db']->query("DELETE FROM ".tname('file')." WHERE project_id ='{$newid}'");
	$_SC['db']->query("DELETE FROM ".tname('post')." WHERE project_id ='{$newid}'");
	$_SC['db']->query("DELETE FROM ".tname('project_member')." WHERE project_id ='{$newid}'");
	//删除feed
	$_SC['db']->query("DELETE FROM ".tname('notification')." WHERE project_id ='{$newid}'");
	$_SC['db']->query("DELETE FROM ".tname('trash_can')." WHERE project_id ='{$newid}'");
	$_SC['db']->query("DELETE FROM ".tname('trash_can_log')." WHERE object_id ='{$newid}' AND object_type='projectid'");
	$_SC['db']->query("DELETE FROM ".tname('notice_discussion')." WHERE project_id ='{$newid}'");
	$_SC['db']->query("DELETE FROM ".tname('project')." WHERE project_id ='{$newid}'");
	return $newid;
}
//删除主题
function deletediscussions($discussion_id) {
	global $_SC;
	$newid = 0;
	$query = $_SC['db']->query("SELECT discussion_id,status FROM ".tname('discussion')."  WHERE `discussion_id` ='{$discussion_id}' LIMIT 1");
	if($valuediscussion = $_SC['db']->fetch_array($query)){
		if($valuediscussion['status'] == 1) {
			$newid = $valuediscussion['discussion_id'];
		}
	}
	if(empty($newid)) return 0;
	
	//删除
	$_SC['db']->query("DELETE FROM ".tname('file')." WHERE discussion_id ='{$newid}'");
	$_SC['db']->query("DELETE FROM ".tname('post')." WHERE discussion_id ='{$newid}'");
	$_SC['db']->query("DELETE FROM ".tname('notification')." WHERE object_id ='{$newid}' AND object_type='discussionid'");
	$_SC['db']->query("DELETE FROM ".tname('trash_can')." WHERE object_id ='{$newid}' AND object_type='discussionid'");
	$_SC['db']->query("DELETE FROM ".tname('trash_can_log')." WHERE object_id ='{$newid}' AND object_type='discussionid'");
	$_SC['db']->query("DELETE FROM ".tname('notice_discussion')." WHERE discussion_id ='{$newid}'");
	$_SC['db']->query("DELETE FROM ".tname('discussion')." WHERE discussion_id ='{$newid}'");
	return $newid;
}
//删除文档
function deletedocuments($document_id) {
	global $_SC;

	//统计
	$newid = 0;
	$discussion_id = 0;

	$query = $_SC['db']->query("SELECT document_id,discussion_id,status FROM ".tname('document')." WHERE document_id ='{$document_id}' LIMIT 1");
	if ($valuedocument = $_SC['db']->fetch_array($query)) {
		if($valuedocument['status'] == 1) {
			$newid = $valuedocument['document_id'];
			$discussion_id = $valuedocument['discussion_id'];
		}
	}
	if(empty($newid)) return 0;
	
	//删除
	if($discussion_id){
		$_SC['db']->query("DELETE FROM ".tname('file')." WHERE discussion_id ='{$discussion_id}'");
		$_SC['db']->query("DELETE FROM ".tname('post')." WHERE discussion_id ='{$discussion_id}'");
		$_SC['db']->query("DELETE FROM ".tname('discussion')." WHERE discussion_id ='{$discussion_id}'");
		$_SC['db']->query("DELETE FROM ".tname('notice_discussion')." WHERE discussion_id ='{$discussion_id}'");
	}
	//删除feed
	$_SC['db']->query("DELETE FROM ".tname('notification')." WHERE object_id ='{$newid}' AND object_type='documentid'");
	$_SC['db']->query("DELETE FROM ".tname('trash_can')." WHERE object_id ='{$newid}' AND object_type='documentid'");
	$_SC['db']->query("DELETE FROM ".tname('trash_can_log')." WHERE object_id ='{$newid}' AND object_type='documentid'");
	$_SC['db']->query("DELETE FROM ".tname('document')." WHERE document_id ='{$newid}'");

	return $newid;
}
//删除附件
function deletefiles($file_id) {
	global $_SC;

	//统计
	$newid = 0;
	$discussion_id = 0;

	$query = $_SC['db']->query("SELECT file_id,discussion_id,status FROM ".tname('file')." WHERE file_id ='{$file_id}' LIMIT 1");
	if ($valuefile = $_SC['db']->fetch_array($query)) {
		if($valuefile['status'] == 1) {
			$newid = $valuefile['file_id'];
			$discussion_id = $valuefile['discussion_id'];
		}
	}
	if(empty($newid)) return 0;
	
	//删除
	if($discussion_id){
		$_SC['db']->query("DELETE FROM ".tname('post')." WHERE discussion_id ='{$discussion_id}'");
		$_SC['db']->query("DELETE FROM ".tname('discussion')." WHERE discussion_id ='{$discussion_id}'");
		$_SC['db']->query("DELETE FROM ".tname('file')." WHERE discussion_id ='{$discussion_id}'");
		$_SC['db']->query("DELETE FROM ".tname('notice_discussion')." WHERE discussion_id ='{$discussion_id}'");
	}
	//删除feed
	$_SC['db']->query("DELETE FROM ".tname('notification')." WHERE object_id ='{$newid}' AND object_type='attachmentid'");
	$_SC['db']->query("DELETE FROM ".tname('trash_can')." WHERE object_id ='{$newid}' AND object_type='attachmentid'");
	$_SC['db']->query("DELETE FROM ".tname('trash_can_log')." WHERE object_id ='{$newid}' AND object_type='attachmentid'");
	$_SC['db']->query("DELETE FROM ".tname('notice_attachment')." WHERE file_id ='{$newid}'");
	$_SC['db']->query("DELETE FROM ".tname('file')." WHERE file_id ='{$newid}'");
	return $newid;
}
//删除待办事宜类型
function deletetodos($todos_id) {
	global $_SC;

	//统计
	$newid = 0;
	$in_discussion_id = '';
	$in_todoslist_id = '';

	$query = $_SC['db']->query("SELECT uid,todos_id,discussion_id,subject,status FROM ".tname('todos')." WHERE todos_id ='{$todos_id}' LIMIT 1");
	if ($valuetodos = $_SC['db']->fetch_array($query)) {
		if($valuetodos['status'] == 1) {
			$newid = $valuetodos['todos_id'];
			if(!empty($valuetodos['discussion_id'])) {
				$in_discussion_id .= $valuetodos['discussion_id'];
			}
		}
	}
	if(empty($newid)) return 0;
	
	//获取主题附件
	$querytodoslist = $_SC['db']->query("SELECT discussion_id,todoslist_id FROM ".tname('todoslist')." WHERE todos_id ='{$todos_id}'");
	while ($value = $_SC['db']->fetch_array($querytodoslist)) {
		if($in_todoslist_id == '') {
			$in_todoslist_id .= $value['todoslist_id'];
		} else {
			$in_todoslist_id .= ','.$value['todoslist_id'];
		}
		if(!empty($value['discussion_id'])) {
			if($in_discussion_id == '') {
				$in_discussion_id .= $value['discussion_id'];
			} else {
				$in_discussion_id .= ','.$value['discussion_id'];
			}
		}
	}
	//删除主题
	if(!empty($in_discussion_id)){
		$_SC['db']->query("DELETE FROM ".tname('file')." WHERE discussion_id IN({$in_discussion_id})");
		$_SC['db']->query("DELETE FROM ".tname('post')." WHERE discussion_id IN({$in_discussion_id})");
		$_SC['db']->query("DELETE FROM ".tname('discussion')." WHERE discussion_id IN({$in_discussion_id})");
		$_SC['db']->query("DELETE FROM ".tname('notice_discussion')." WHERE discussion_id IN({$in_discussion_id})");
	}
	//删除待办事宜清单记录
	if(!empty($in_todoslist_id)){
		$_SC['db']->query("DELETE FROM ".tname('notification')." WHERE object_id IN({$in_todoslist_id}) AND object_type='todoslistid'");
		$_SC['db']->query("DELETE FROM ".tname('trash_can')." WHERE object_id IN({$in_todoslist_id}) AND object_type='todoslistid'");
		$_SC['db']->query("DELETE FROM ".tname('trash_can_log')." WHERE object_id IN({$in_todoslist_id}) AND object_type='todoslistid'");
	}
	//删除feed
	$_SC['db']->query("DELETE FROM ".tname('notification')." WHERE object_id ='{$newid}' AND object_type='todosid'");
	$_SC['db']->query("DELETE FROM ".tname('trash_can')." WHERE object_id ='{$newid}' AND object_type='todosid'");
	$_SC['db']->query("DELETE FROM ".tname('trash_can_log')." WHERE object_id ='{$newid}' AND object_type='todosid'");
	$_SC['db']->query("DELETE FROM ".tname('todoslist')." WHERE todos_id ='{$newid}'");
	$_SC['db']->query("DELETE FROM ".tname('todos')." WHERE todos_id ='{$newid}'");
	return $newid;
}
//删除待办事宜清单
function deletetodoslist($todoslist_id) {
	global $_SC;

	//统计
	$newid = 0;
	$discussion_id = 0;

	$query = $_SC['db']->query("SELECT uid,todoslist_id,discussion_id,subject,status FROM ".tname('todoslist')." WHERE todoslist_id ='{$todoslist_id}' LIMIT 1");
	if ($valuetodoslist = $_SC['db']->fetch_array($query)) {
		if($valuetodoslist['status'] == 1) {
			$newid = $valuetodoslist['todoslist_id'];
			$discussion_id = $valuetodoslist['discussion_id'];
		}
	}
	if(empty($newid)) return 0;
	
	//删除
	if($discussion_id){
		$_SC['db']->query("DELETE FROM ".tname('file')." WHERE discussion_id ='{$discussion_id}'");
		$_SC['db']->query("DELETE FROM ".tname('post')." WHERE discussion_id ='{$discussion_id}'");
		$_SC['db']->query("DELETE FROM ".tname('discussion')." WHERE discussion_id ='{$discussion_id}'");
		$_SC['db']->query("DELETE FROM ".tname('notice_discussion')." WHERE discussion_id ='{$discussion_id}'");
	}
	//删除feed
	$_SC['db']->query("DELETE FROM ".tname('notification')." WHERE object_id ='{$newid}' AND object_type='todoslistid'");
	$_SC['db']->query("DELETE FROM ".tname('trash_can')." WHERE object_id ='{$newid}' AND object_type='todoslistid'");
	$_SC['db']->query("DELETE FROM ".tname('trash_can_log')." WHERE object_id ='{$newid}' AND object_type='todoslistid'");
	$_SC['db']->query("DELETE FROM ".tname('todoslist')." WHERE todoslist_id ='{$newid}'");
	return $newid;
}
//时间格式化
function sgmdate($dateformat, $timestamp='', $format=0) {
	global $_SC;
	if(empty($timestamp)) {
		$timestamp = $_SC['timestamp'];
	}
	$timeoffset = intval($_SC['timeoffset']);
	$result = '';
	if($format) {
		$time = $_SC['timestamp'] - $timestamp;
		if($time > 24*3600) {
			$result = gmdate($dateformat, $timestamp + $timeoffset * 3600);
		} elseif ($time > 3600) {
			$result = intval($time/3600).lang('hour').lang('before');
		} elseif ($time > 60) {
			$result = intval($time/60).lang('minute').lang('before');
		} elseif ($time > 0) {
			$result = $time.lang('second').lang('before');
		} else {
			$result = lang('now');
		}
	} else {
		$result = gmdate($dateformat, $timestamp + $timeoffset * 3600);
	}
	return $result;
}
//写运行日志
function runlog($file, $log, $halt=0) {
	global $_SC;

	$log = sgmdate('Y-m-d H:i:s', $_SC['timestamp'])."\t".str_replace(array("\r", "\n"), array(' ', ' '), trim($log))."\n";
	$yearmonth = sgmdate('Ymd', $_SC['timestamp']);
	$logdir = './data/log/';
	if(!is_dir($logdir)) mkdir($logdir, 0777);
	$logfile = $logdir.$yearmonth.'_'.$file.'.php';
	if(@filesize($logfile) > 2048000) {
		$dir = opendir($logdir);
		$length = strlen($file);
		$maxid = $id = 0;
		while($entry = readdir($dir)) {
			if(strexists($entry, $yearmonth.'_'.$file)) {
				$id = intval(substr($entry, $length + 8, -4));
				$id > $maxid && $maxid = $id;
			}
		}
		closedir($dir);
		$logfilebak = $logdir.$yearmonth.'_'.$file.'_'.($maxid + 1).'.php';
		@rename($logfile, $logfilebak);
	}
	if($fp = @fopen($logfile, 'a')) {
		@flock($fp, 2);
		fwrite($fp, "<?PHP exit;?>\t".str_replace(array('<?', '?>', "\r", "\n"), '', $log)."\n");
		fclose($fp);
	}
	if($halt) exit();
}
//判断字符串是否存在
function strexists($haystack, $needle) {
	return !(strpos($haystack, $needle) === FALSE);
}
?>
