<?php
/*
	[TEAMDOTA] (C) 2012 wanghui
	$Id: mail_image_preview.php 2012-03-31 09:59Z duty $
*/

include_once('./common.php');
//参数

$file_id = intval($_GET['file_id']);
$time = $_GET['time'];
$code = $_GET['code'];
if(empty($file_id)) {
	exit;
}
if(empty($code)) {
	exit;
}
$sign = md5("file_id={$file_id}&time={$time}{$_SCONFIG['sitekey']}");
if($sign != $code) {
	exit;
}
$query = $_SGLOBAL['db']->query("SELECT project_id,file_id,fileurl,remote,filetype,logtime,thumb,isimage,type FROM ".tname('file')." WHERE file_id='{$file_id}' LIMIT 1");
if($attachment = $_SGLOBAL['db']->fetch_array($query)) {
	if(!$attachment['isimage']) {
		dheader('Location: '.$_SC['siteurl'].file_icon_jumbo($attachment['type']));
	}
	
	if(empty($attachment['thumb'])) {
		$filename = $_SC['attachurl'].'/'.$attachment['fileurl'];
	} else {
		$filename = $_SC['attachurl'].'/'.$attachment['fileurl'].'.thumb.jpg';
	}
	if(!$attachment['remote'] && !is_readable($filename)) {
		dheader('Location: '.$_SC['siteurl'].file_icon_jumbo($attachment['type']));
	}
	$filesize = sizecount_decode($attachment['size']);
	dheader('Date: '.gmdate('D, d M Y H:i:s', $_SGLOBAL['timestamp']).' GMT');
	dheader('Last-Modified: '.gmdate('D, d M Y H:i:s', $attachment['logtime']).' GMT');
	dheader('Content-Encoding: none');
	dheader('Content-Type: '.$attachment['filetype']);
	$attachment['remote'] ? getremotefile($attachment['fileurl']) : getlocalfile($filename);
	
} else {
	dheader('Location: '.$_SC['siteurl'].file_icon_jumbo('jpg'));
}
function getremotefile($file) {
	global $_SCONFIG, $_SGLOBAL, $_SC;
	@set_time_limit(0);
	if(!@readfile($_SCONFIG['ftpurl'].$file)) {
		require_once S_ROOT.'./source/function_ftp.php';
		if(!($_SGLOBAL['ftpconnid'] = sftp_connect())) {
			return FALSE;
		}
		$tmpfile = @tempnam(S_ROOT.$_SC['attachdirtemp'], '');
		if(sftp_get($_SGLOBAL['ftpconnid'], $tmpfile, $file, FTP_BINARY)) {
			@readfile($tmpfile);
			@unlink($tmpfile);
		} else {
			@unlink($tmpfile);
			return FALSE;
		}
	}
	return TRUE;
}

function getlocalfile($filename, $readmod = 2, $range = 0) {
	if($readmod == 1 || $readmod == 3 || $readmod == 4) {
		if($fp = @fopen($filename, 'rb')) {
			@fseek($fp, $range);
			if(function_exists('fpassthru') && ($readmod == 3 || $readmod == 4)) {
				@fpassthru($fp);
			} else {
				echo @fread($fp, filesize($filename));
			}
		}
		@fclose($fp);
	} else {
		@readfile($filename);
	}
	@flush(); @ob_flush();
}
?>