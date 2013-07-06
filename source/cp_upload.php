<?php
/*
	[TEAMDOTA] (C) 2012 wanghui
	$Id: cp_upload.php 2012-03-31 09:59Z duty $
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
$discussion_id = empty($_GET['discussion_id']) ? 0 : intval($_GET['discussion_id']);
$post_id = empty($_GET['post_id']) ? 0 : intval($_GET['post_id']);
$discussion_id = 0;
$post_id = 0;
//上传
$fileid = 0;
$name = $_GET['name'];
$fileext = $_GET['fileext'];

$uploadfiles = stream_save(file_get_contents('php://input'), $name ,$fileext, $project_id, $discussion_id, $post_id);
if($uploadfiles && is_array($uploadfiles)) {
	$fileid = $uploadfiles['file_id'];
	echo $fileid;
	exit();
} else {
	echo $fileid.'||'.$uploadfiles;
	exit();
}


?>
