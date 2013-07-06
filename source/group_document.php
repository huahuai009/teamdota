<?php
/*
	[TEAMDOTA] (C) 2012 wanghui
	$Id: group_document.php 2012-03-31 09:59Z duty $
*/

if(!defined('IN_TEAMDOTA')) {
	exit('Access Denied');
}

$project_id = empty($_GET['project_id']) ? 0 : intval($_GET['project_id']);
if(empty($project_id)){
	showmessage('project_not_allowed_to_visit','group.php?do=home');
}
$manageproject = checkproject($project_id);
if(!$manageproject) {
	showmessage('project_not_allowed_to_visit','group.php?do=home');
}
if($manageproject['status'] == 2) {//处于删除状态
	dheader("location: group.php?do=project&project_id={$project_id}");
}

//分页
$page = empty($_GET['page'])?1:intval($_GET['page']);
if($page<1) $page=1;
$document_id = empty($_GET['document_id'])?0:intval($_GET['document_id']);
$objectid = $document_id;//前端使用，服务端勿使用
if($document_id) {
	//文档
	$document_page = empty($_GET['document_page'])?1:intval($_GET['document_page']);
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('document')." WHERE document_id='{$document_id}' LIMIT 1");
	if(!$document = $_SGLOBAL['db']->fetch_array($query)) {
		showmessage('document_does_not_exist',"group.php?do=project&project_id={$project_id}");
	}
	if($project_id != $document['project_id']){
		showmessage('document_does_not_exist',"group.php?do=project&project_id={$project_id}");
	}
	if(!$_SGLOBAL['inajax']) {
		if($document['status'] == 1) {//处于删除状态
			$query_trash = $_SGLOBAL['db']->query("SELECT sender_author,created_time FROM ".tname('trash_can')." WHERE object_id ='{$document_id}' AND  object_type='documentid' ORDER BY created_time DESC LIMIT 1");
			$trash = $_SGLOBAL['db']->fetch_array($query_trash);
		}
		//获取历史记录
		$listhistory = array();
		$query_history = $_SGLOBAL['db']->query("SELECT href,created_time,sender_author,title_html,title_text,sender_id FROM ".tname('notification')." WHERE object_id ='{$document_id}' AND icon_url='document' ORDER BY created_time ASC");
		while ($row = $_SGLOBAL['db']->fetch_array($query_history)) {
			$row = mknotification($row);
			$listhistory[] = $row;
		}
		
		include_once template("group_document_view");
	} else {
		if($document['discussion_id']){
			//讨论列表
			$perpage = 20;
			$start = ($page-1)*$perpage;

			$count = $_SGLOBAL['db']->result($_SGLOBAL['db']->query("SELECT COUNT(*) FROM ".tname('post')." WHERE discussion_id='{$document[discussion_id]}'"),0);

			$list = array();
			$listpostpic = array();
			$listpostfile = array();
			$postnum = $start;
			$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('post')." WHERE discussion_id='{$document[discussion_id]}' ORDER BY logtime DESC LIMIT $start,$perpage");
			while ($value = $_SGLOBAL['db']->fetch_array($query)) {
				$list[] = $value;
				$queryfile = $_SGLOBAL['db']->query("SELECT * FROM ".tname('file')." WHERE post_id='{$value[post_id]}' ORDER BY logtime DESC");
				while ($row = $_SGLOBAL['db']->fetch_array($queryfile)) {
					if($row['isimage']) {
						$thumbwh = get_thumbwh($row['width'],$row['height']);
						$listpostpic[$value['post_id']][] = array('file_id'=>$row['file_id'],'project_id'=>$row['project_id'],'discussion_id'=>$row['discussion_id'],'filename'=>$row['filename'],'fileurl'=>pic_get($row['fileurl'], '', $row['remote']),'thumbfileurl'=>pic_get($row['fileurl'], $row['thumb'], $row['remote']),'width'=>$row['width'],'height'=>$row['height'],'thumbwidth'=>$thumbwh['thumbwidth'],'thumbheight'=>$thumbwh['thumbheight']);
					} else {
						$listpostfile[$value['post_id']][] = array('file_id'=>$row['file_id'],'project_id'=>$row['project_id'],'discussion_id'=>$row['discussion_id'],'filename'=>$row['filename'],'thumbfileurl'=>file_icon_big($row['type']));
					}
				}
				$postnum++;
			}
			//分页
			if($count > $perpage) {
				$pagenumbers = '<div class="more_topics">'.getpageajax($page, $perpage, $count, array("group.php?project_id={$project_id}&do={$do}&document_id={$document_id}&inajax=1","commentsdata_{$document_id}")).'</div>';
			}
		}
		$objectdata = $document;
		include_once template("group_post_ajax");
	}
} else {
	$perpage = 5;
	$start = ($page-1)*$perpage;

	//任务列表
	$wheresql = "`project_id`='{$project_id}'";

	$listdocument = array();
	
	$count = 0;
	$count = $_SGLOBAL['db']->result($_SGLOBAL['db']->query("SELECT COUNT(*) FROM ".tname('document')." WHERE `project_id`='{$project_id}' AND `status`=0"),0);
	
	if($count) {
		$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('document')." WHERE `project_id`='{$project_id}' AND `status`=0 ORDER BY uptime DESC LIMIT $start,$perpage");
		while ($value = $_SGLOBAL['db']->fetch_array($query)) {
			$value['logtimemat'] = sgmdate('n-j H:i', $value['logtime'],1);
			$value['logtime'] = sgmdate('Y-m-d H:i:s', $value['logtime'],0);
			$listdocument[] = $value;
		}
	}
	if($count > $perpage) {
		//分页
		$pagenumbers = '<div class="more_documents">'.getpageajax($page, $perpage, $count, array("group.php?project_id={$project_id}&do={$do}&inajax=1","document_data")).'</div>';
	}
	include_once template("group_document_ajax");
}
?>