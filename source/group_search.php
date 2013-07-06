<?php
/*
	[TEAMDOTA] (C) 2012 wanghui
	$Id: group_search.php 2012-03-31 09:59Z duty $
*/

if(!defined('IN_TEAMDOTA')) {
	exit('Access Denied');
}

$searchkey = stripsearchkey($_GET['q']);
$searchkey_mark = "<mark>{$searchkey}</mark>";
//读取项目
$query = $_SGLOBAL['db']->query("SELECT p.project_id,p.name FROM ".tname('project')." p , ".tname('project_member')." pm WHERE p.project_id=pm.project_id AND pm.uid='".$_SGLOBAL['supe_uid']."' AND p.status<>2 ORDER BY project_id ASC");
$arr_project_id = array();
$arr_project = array();
while($value = $_SGLOBAL['db']->fetch_array($query)) {
	$arr_project_id[] = $value['project_id'];
	$arr_project[$value['project_id']] = $value['name'];
}

$cachelife_time = 300;		// Life span for cache of searching in specified range of time
$expiration = $_SGLOBAL['timestamp'] + $cachelife_time;
$num_discussion = $num_document = $num_post = 0;
$discussionids = $documentids = $postids = 0;
$result_discussion_list = $result_document_list = $result_post_list = array();

//搜索主题
$search_discussion_string = "group_id-{$groupid}|project_id-".implode(",", $arr_project_id)."|discussion|{$searchkey}";
$query = $_SGLOBAL['db']->query("SELECT keywords, num, ids FROM ".tname('searchindex')." WHERE searchstring='{$search_discussion_string}' AND expiration>'{$_SGLOBAL[timestamp]}' ORDER BY searchid DESC LIMIT 1");
if($search_discussion_index = $_SGLOBAL['db']->fetch_array($query)) {
	$query = $_SGLOBAL['db']->query("SELECT discussion_id,project_id,subject,message,logtime FROM ".tname('discussion')." WHERE `discussion_id` IN ({$search_discussion_index[ids]}) ORDER BY discussion_id DESC");
	
	while ($value = $_SGLOBAL['db']->fetch_array($query)) {
		$value['project_name'] = $arr_project[$value['project_id']];
		$value['subject'] = str_replace($searchkey, $searchkey_mark, $value['subject']);
		
		$value['message'] = preg_replace("/(\<[^\<]*\>|\r|\n|\s|\[.+?\])/is", '', $value['message']);
		$value['message'] = substr($value['message'],strpos($value['message'],$searchkey));
		$value['message'] = getstr($value['message'], 200, 1, 1 , 0 , 0 , -1);
		$value['message'] = str_replace($searchkey, $searchkey_mark, $value['message']);
		
		$result_discussion_list[] = $value;
	}
} else {
	$query = $_SGLOBAL['db']->query("SELECT discussion_id,project_id,subject,message,logtime FROM ".tname('discussion')." WHERE `project_id` IN (".simplode($arr_project_id).") AND (`subject` LIKE '%{$searchkey}%' OR `message` LIKE '%{$searchkey}%')  ORDER BY discussion_id DESC LIMIT 50");
	$result_discussion_list = array();

	while ($value = $_SGLOBAL['db']->fetch_array($query)) {
		$value['project_name'] = $arr_project[$value['project_id']];
		$value['subject'] = str_replace($searchkey, $searchkey_mark, $value['subject']);
		
		$value['message'] = preg_replace("/(\<[^\<]*\>|\r|\n|\s|\[.+?\])/is", '', $value['message']);
		$value['message'] = substr($value['message'],strpos($value['message'],$searchkey));
		$value['message'] = getstr($value['message'], 200, 1, 1 , 0 , 0 , -1);
		$value['message'] = str_replace($searchkey, $searchkey_mark, $value['message']);
		
		$result_discussion_list[] = $value;
		
		$discussionids .= ','.$value['discussion_id'];
		$num_discussion++;
	}
	$_SGLOBAL['db']->query("INSERT INTO ".tname('searchindex')." (srchmod, keywords, searchstring, group_id, uid, dateline, expiration, num, ids)
			VALUES ('3', '{$searchkey}', '{$search_discussion_string}', '{$groupid}', '{$_SGLOBAL[supe_uid]}', '{$_SGLOBAL[timestamp]}', '{$expiration}', '{$num_discussion}', '{$discussionids}')");
}
//搜索文档
$search_document_string = "group_id-{$groupid}|project_id-".implode(",", $arr_project_id)."|document|{$searchkey}";
$query = $_SGLOBAL['db']->query("SELECT keywords, num, ids FROM ".tname('searchindex')." WHERE searchstring='{$search_document_string}' AND expiration>'{$_SGLOBAL[timestamp]}' ORDER BY searchid DESC LIMIT 1");
if($search_document_index = $_SGLOBAL['db']->fetch_array($query)) {
	$query = $_SGLOBAL['db']->query("SELECT document_id,project_id,name,description,logtime FROM ".tname('document')." WHERE `document_id` IN ({$search_document_index[ids]}) ORDER BY document_id DESC");
	while ($value = $_SGLOBAL['db']->fetch_array($query)) {
		$value['project_name'] = $arr_project[$value['project_id']];
		$value['name'] = str_replace($searchkey, $searchkey_mark, $value['name']);
		
		$value['description'] = preg_replace("/(\<[^\<]*\>|\r|\n|\s|\[.+?\])/is", '', $value['description']);
		$value['description'] = substr($value['description'],strpos($value['description'],$searchkey));
		$value['description'] = getstr($value['description'], 200, 1, 1 , 0 , 0 , -1);
		$value['description'] = str_replace($searchkey, $searchkey_mark, $value['description']);
		
		$result_document_list[] = $value;
	}
} else {
	$query = $_SGLOBAL['db']->query("SELECT document_id,project_id,name,description,logtime FROM ".tname('document')." WHERE `project_id` IN (".simplode($arr_project_id).") AND (`name` LIKE '%{$searchkey}%' OR `description` LIKE '%{$searchkey}%')  ORDER BY document_id DESC LIMIT 50");
	while ($value = $_SGLOBAL['db']->fetch_array($query)) {
		$value['project_name'] = $arr_project[$value['project_id']];
		$value['name'] = str_replace($searchkey, $searchkey_mark, $value['name']);
		
		$value['description'] = preg_replace("/(\<[^\<]*\>|\r|\n|\s|\[.+?\])/is", '', $value['description']);
		$value['description'] = substr($value['description'],strpos($value['description'],$searchkey));
		$value['description'] = getstr($value['description'], 200, 1, 1 , 0 , 0 , -1);
		$value['description'] = str_replace($searchkey, $searchkey_mark, $value['description']);
		
		$result_document_list[] = $value;
		
		$documentids .= ','.$value['document_id'];
		$num_document++;
	}
	$_SGLOBAL['db']->query("INSERT INTO ".tname('searchindex')." (srchmod, keywords, searchstring, group_id, uid, dateline, expiration, num, ids)
				VALUES ('4', '{$searchkey}', '{$search_document_string}', '{$groupid}', '{$_SGLOBAL[supe_uid]}', '{$_SGLOBAL[timestamp]}', '{$expiration}', '{$num_document}', '{$documentids}')");
}
//搜索回复
$search_post_string = "group_id-{$groupid}|project_id-".implode(",", $arr_project_id)."|post|{$searchkey}";
$query = $_SGLOBAL['db']->query("SELECT keywords, num, ids FROM ".tname('searchindex')." WHERE searchstring='{$search_post_string}' AND expiration>'{$_SGLOBAL[timestamp]}' ORDER BY searchid DESC LIMIT 1");
if($search_post_index = $_SGLOBAL['db']->fetch_array($query)) {
	$query = $_SGLOBAL['db']->query("SELECT p.post_id,p.discussion_id,p.project_id,p.message,p.logtime,d.subject FROM ".tname('post')." p,".tname('discussion')." d WHERE p.post_id IN ({$search_post_index[ids]}) AND p.discussion_id=d.discussion_id ORDER BY p.post_id DESC");
	while ($value = $_SGLOBAL['db']->fetch_array($query)) {
		$value['project_name'] = $arr_project[$value['project_id']];
		
		$value['message'] = preg_replace("/(\<[^\<]*\>|\r|\n|\s|\[.+?\])/is", '', $value['message']);
		$value['message'] = substr($value['message'],strpos($value['message'],$searchkey));
		$value['message'] = getstr($value['message'], 200, 1, 1 , 0 , 0 , -1);
		$value['message'] = str_replace($searchkey, $searchkey_mark, $value['message']);
		
		$result_post_list[] = $value;
		
		$postids .= ','.$value['post_id'];
		$num_post++;
	}
}else {
	$query = $_SGLOBAL['db']->query("SELECT p.post_id,p.discussion_id,p.project_id,p.message,p.logtime,d.subject FROM ".tname('post')." p,".tname('discussion')." d WHERE p.project_id IN (".simplode($arr_project_id).") AND p.discussion_id=d.discussion_id AND p.message LIKE '%{$searchkey}%'  ORDER BY p.post_id DESC LIMIT 50");
	while ($value = $_SGLOBAL['db']->fetch_array($query)) {
		$value['project_name'] = $arr_project[$value['project_id']];
		
		$value['message'] = preg_replace("/(\<[^\<]*\>|\r|\n|\s|\[.+?\])/is", '', $value['message']);
		$value['message'] = substr($value['message'],strpos($value['message'],$searchkey));
		$value['message'] = getstr($value['message'], 200, 1, 1 , 0 , 0 , -1);
		$value['message'] = str_replace($searchkey, $searchkey_mark, $value['message']);
		
		$result_post_list[] = $value;
		
		$postids .= ','.$value['post_id'];
		$num_post++;
	}
	$_SGLOBAL['db']->query("INSERT INTO ".tname('searchindex')." (srchmod, keywords, searchstring, group_id, uid, dateline, expiration, num, ids)
				VALUES ('5', '{$searchkey}', '{$search_post_string}', '{$groupid}', '{$_SGLOBAL[supe_uid]}', '{$_SGLOBAL[timestamp]}', '{$expiration}', '{$num_post}', '{$postids}')");
}
include_once template("group_search");
?>