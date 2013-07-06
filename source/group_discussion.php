<?php
/*
	[TEAMDOTA] (C) 2012 wanghui
	$Id: group_discussion.php 2012-03-31 09:59Z duty $
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
$discussion_id = empty($_GET['discussion_id'])?0:intval($_GET['discussion_id']);
$objectid = $discussion_id;//前端使用，服务端勿使用
if($discussion_id) {
	//主题
	$discussion_page = empty($_GET['discussion_page'])?1:intval($_GET['discussion_page']);
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('discussion')." WHERE discussion_id='{$discussion_id}' LIMIT 1");
	if(!$discussion = $_SGLOBAL['db']->fetch_array($query)) {
		showmessage('discussion_does_not_exist',"group.php?do=project&project_id={$project_id}");
	}
	if($project_id != $discussion['project_id']){
		showmessage('discussion_does_not_exist',"group.php?do=project&project_id={$project_id}");
	}
	if($discussion['othertype'] == 1){
		$document_page = empty($_GET['document_page'])?1:intval($_GET['document_page']);
		$detailurl = "group.php?project_id={$project_id}&do=document&document_id={$discussion[otherid]}&document_page={$document_page}";
		dheader("location: {$detailurl}");
	} else if($discussion['othertype'] == 2){
		$file_page = empty($_GET['file_page'])?1:intval($_GET['file_page']);
		$detailurl = "group.php?project_id={$project_id}&do=attachment&file_id={$discussion[otherid]}&file_page={$file_page}";
		dheader("location: {$detailurl}");
	} else if($discussion['othertype'] == 3){
		$detailurl = "group.php?project_id={$project_id}&do=todos&todos_id={$discussion[otherid]}";
		dheader("location: {$detailurl}");
	} else if($discussion['othertype'] == 4){
		$detailurl = "group.php?project_id={$project_id}&do=todoslist&todoslist_id={$discussion[otherid]}";
		dheader("location: {$detailurl}");
	}
	if(!$_SGLOBAL['inajax']) {
		if($discussion['status'] == 1){//处于删除状态
			$query_trash = $_SGLOBAL['db']->query("SELECT sender_author,created_time FROM ".tname('trash_can')." WHERE object_id ='{$discussion_id}' AND  object_type='discussionid'  ORDER BY created_time DESC LIMIT 1");
			$trash = $_SGLOBAL['db']->fetch_array($query_trash);
		}
		//获取附件
		$listdiscussionpic = array();
		$listdiscussionfile = array();
		$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('file')." WHERE discussion_id='{$discussion_id}' AND post_id=0 ORDER BY logtime DESC");
		while ($row = $_SGLOBAL['db']->fetch_array($query)) {
			if($row['isimage']) {
				$thumbwh = get_thumbwh($row['width'],$row['height']);
				$listdiscussionpic[] = array('file_id'=>$row['file_id'],'project_id'=>$row['project_id'],'discussion_id'=>$row['discussion_id'],'filename'=>$row['filename'],'fileurl'=>pic_get($row['fileurl'], '', $row['remote']),'thumbfileurl'=>pic_get($row['fileurl'], $row['thumb'], $row['remote']),'width'=>$row['width'],'height'=>$row['height'],'thumbwidth'=>$thumbwh['thumbwidth'],'thumbheight'=>$thumbwh['thumbheight']);
			}else {
				$listdiscussionfile[] = array('file_id'=>$row['file_id'],'project_id'=>$row['project_id'],'discussion_id'=>$row['discussion_id'],'filename'=>$row['filename'],'thumbfileurl'=>file_icon_big($row['type']));
			}
		}
		//获取历史记录
		$listhistory = array();
		$query_history = $_SGLOBAL['db']->query("SELECT href,created_time,sender_author,title_html,title_text,sender_id FROM ".tname('notification')." WHERE object_id ='{$discussion_id}' AND icon_url='discussion' ORDER BY created_time ASC");
		while ($row = $_SGLOBAL['db']->fetch_array($query_history)) {
			$row = mknotification($row);
			$listhistory[] = $row;
		}
		
		include_once template("group_discussion_view");
	} else {
		//讨论列表
		$perpage = 20;
		$start = ($page-1)*$perpage;

		$count = $discussion['post_num'];

		$list = array();
		$listpostpic = array();
		$listpostfile = array();
		$postnum = $start;
		$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('post')." WHERE discussion_id='{$discussion_id}' ORDER BY logtime DESC LIMIT $start,$perpage");
		while ($value = $_SGLOBAL['db']->fetch_array($query)) {
			$list[] = $value;
			//读取评论附件
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
			$pagenumbers = '<div class="more_topics">'.getpageajax($page, $perpage, $count, array("group.php?project_id={$project_id}&do={$do}&discussion_id={$discussion_id}&inajax=1","commentsdata_{$discussion_id}")).'</div>';
		}
		$objectdata = $discussion;
		include_once template("group_post_ajax");
	}
} else {
	$perpage = 5;
	$start = ($page-1)*$perpage;

	$listdiscussion = array();
	$listdiscussionpic = array();
	$listdiscussionfile = array();
	
	$count = 0;
	$count = $_SGLOBAL['db']->result($_SGLOBAL['db']->query("SELECT COUNT(*) FROM ".tname('discussion')." WHERE `project_id`='{$project_id}' AND `status`=0"),0);
	if($count) {
		$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('discussion')." WHERE `project_id`='{$project_id}' AND `status`=0 ORDER BY lastpost DESC LIMIT $start,$perpage");
		while ($value = $_SGLOBAL['db']->fetch_array($query)) {
			$value['lastpostmat'] = sgmdate('n-j', $value['lastpost'],1);
			$value['lastpost'] = sgmdate('Y-m-d H:i:s', $value['lastpost'],0);
			$listdiscussion[] = $value;
			
			//读取评论附件
			$queryfile = $_SGLOBAL['db']->query("SELECT * FROM ".tname('file')." WHERE discussion_id='{$value[discussion_id]}' ORDER BY logtime DESC LIMIT 0,10");
			while ($row = $_SGLOBAL['db']->fetch_array($queryfile)) {
				if($row['isimage']) {
					$listdiscussionpic[$value['discussion_id']][] = array('file_id'=>$row['file_id'],'filename'=>$row['filename'],'width'=>$row['width'],'height'=>$row['height'],'fileurl'=>pic_get($row['fileurl'], '', $row['remote']),'thumbfileurl'=>pic_get($row['fileurl'], $row['thumb'], $row['remote']),'discussion_id'=>$row['discussion_id'],'post_id'=>$row['post_id']);
				} else {
					$listdiscussionfile[$value['discussion_id']][] = array('file_id'=>$row['file_id'],'filename'=>$row['filename'],'fileurl'=>file_icon_big($row['type']));
				}
			}
		}
	}
	if($count > $perpage) {
		//分页
		$pagenumbers = '<div class="more_topics">'.getpageajax($page, $perpage, $count, array("group.php?project_id={$project_id}&do={$do}&inajax=1","topics_data")).'</div>';
	}
	include_once template("group_discussion_ajax");
}
?>