<?php
/*
	[TEAMDOTA] (C) 2012 wanghui
	$Id: group_download.php 2012-04-26 09:59Z duty $
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

$file_id = empty($_GET['file_id'])?0:intval($_GET['file_id']);

// read local file's function: 1=fread 2=readfile 3=fpassthru 4=fpassthru+multiple
$readmod = 2;

$attachexists = FALSE;
if(!empty($file_id)) {
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('file')." WHERE file_id='$file_id'");
	$attachment = $_SGLOBAL['db']->fetch_array($query);
	if($attachment && $attachment['invisible'] == 0 && $attachment['project_id'] == $project_id) {
		$attachexists = TRUE;
	}
}
!$attachexists && showmessage('attachment_nonexistence');

$isimage = $attachment['isimage'];

$filename = $_SC['attachurl'].'/'.$attachment['fileurl'];
if(!$attachment['remote'] && !is_readable($filename)) {
	showmessage('attachment_nonexistence');
}

$range = 0;
if($readmod == 4 && !empty($_SERVER['HTTP_RANGE'])) {
	list($range) = explode('-',(str_replace('bytes=', '', $_SERVER['HTTP_RANGE'])));
}

$_SGLOBAL['db']->query("UPDATE ".tname('file')." SET downloads=downloads+'1' WHERE file_id='$file_id'", 'UNBUFFERED');
$_SGLOBAL['db']->close(); ob_end_clean();

$filesize = !$attachment['remote'] ? filesize($filename) : sizecount_decode($attachment['size']);
$attachment['filename'] = '"'.(strtolower($_SC['charset']) == 'utf-8' && strexists($_SERVER['HTTP_USER_AGENT'], 'MSIE') ? urlencode($attachment['filename']) : urlencode($attachment['filename'])).'"';

dheader('Date: '.gmdate('D, d M Y H:i:s', $attachment['logtime']).' GMT');
dheader('Last-Modified: '.gmdate('D, d M Y H:i:s', $attachment['logtime']).' GMT');
dheader('Content-Encoding: none');

if($isimage) {
	dheader('Content-Disposition: inline; filename='.$attachment['filename']);
} else {
	dheader('Content-Disposition: attachment; filename='.$attachment['filename']);
}

dheader('Content-Type: '.$attachment['filetype']);
//dheader('Content-Type: application/octet-stream');
dheader('Content-Length: '.$filesize);

if($readmod == 4) {
	dheader('Accept-Ranges: bytes');
	if(!empty($_SERVER['HTTP_RANGE'])) {
		$rangesize = ($filesize - $range) > 0 ?  ($filesize - $range) : 0;
		dheader('Content-Length: '.$rangesize);
		dheader('HTTP/1.1 206 Partial Content');
		dheader('Content-Range: bytes='.$range.'-'.($filesize-1).'/'.($filesize));
	}
}

$attachment['remote'] ? getremotefile($attachment['fileurl']) : getlocalfile($filename, $readmod, $range);

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