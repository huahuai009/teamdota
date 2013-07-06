<?php
/*
	[TEAMDOTA] (C) 2012 wanghui
	$Id: function_cp.php  2012-03-31 09:59Z duty $
*/

if(!defined('IN_TEAMDOTA')) {
	exit('Access Denied');
}

//保存附件
function file_save($FILE, $project_id, $discussion_id=0,$post_id=0) {
	global $_SGLOBAL, $_SCONFIG, $group, $_SC;

	if($project_id<=0) $project_id = 0;
	
	//检查
	$FILE['size'] = sizecount(intval($FILE['size']));
	if(empty($FILE['size']) || empty($FILE['tmp_name']) || !empty($FILE['error'])) {
		return cplang('lack_of_access_to_upload_file_size');
	}

	//判断后缀
	$fileext = strtolower(fileext($FILE['name']));
	if(!in_array($fileext, $_SCONFIG['allowfiletype'])) {
		return cplang('only_allows_upload_file_types',array(implode(',',$_SCONFIG['allowfiletype'])));
	}

	//获取目录
	if(!$filepath = getfilepath($fileext,$project_id, true)) {
		return cplang('unable_to_create_upload_directory_server');
	}

	$maxattachsize = $group['maxattachsize'];//单位MB
	if($maxattachsize) {//0为不限制
		if($group['gtype']) {//套餐用户
			if($_SGLOBAL['group_is_time_end']) {//时间过期,如果项目不超过免费用户的附件空间，则可以上传	
				if($group['attachsize'] + $FILE['size'] > $_SCONFIG['group_attachsize'][0]) {
					@unlink($newfilename);
					return cplang('inadequate_capacity_space');
				}
			} else {//时间未到期,检查附件空间
				if($group['attachsize'] + $FILE['size'] > $maxattachsize) {
					@unlink($newfilename);
					return cplang('inadequate_capacity_space');
				}
			}
		} else {//免费用户
			if($group['attachsize'] + $FILE['size'] > $maxattachsize) {
				@unlink($newfilename);
				return cplang('inadequate_capacity_space');
			}
		}
	}

	//本地上传
	$new_name = $_SC['attachdir'].'./'.$filepath;
	$tmp_name = $FILE['tmp_name'];
	if(@copy($tmp_name, $new_name)) {
		@unlink($tmp_name);
	} elseif((function_exists('move_uploaded_file') && @move_uploaded_file($tmp_name, $new_name))) {
	} elseif(@rename($tmp_name, $new_name)) {
	} else {
		return cplang('mobile_picture_temporary_failure');
	}
	
	$file_width = 32;
	$file_height = 32;
	//检查是否图片
	$isimage = 0;
	if(in_array($fileext, $_SCONFIG['pictype'])) {
		if(function_exists('getimagesize')) {
			$tmp_imagesize = @getimagesize($new_name);
			list($tmp_width, $tmp_height, $tmp_type) = (array)$tmp_imagesize;
			$tmp_size = $tmp_width * $tmp_height;
			if($tmp_size > 16777216 || $tmp_size < 4 || empty($tmp_type) || strpos($tmp_imagesize['mime'], 'flash') > 0) {
				@unlink($new_name);
				return cplang('only_allows_upload_file_types',array(implode(',',$_SCONFIG['allowfiletype'])));
			}
			$file_width = $tmp_width;
			$file_height = $tmp_height;
		}
		$isimage = 1;
		//缩略图
		$thumbpath = makethumb($newfilename);
	}
	$thumb = empty($thumbpath)?0:1;

	//是否压缩
	//获取上传后图片大小
	if(@$newfilesize = filesize($new_name)) {
		$FILE['size'] = sizecount($newfilesize);
	}
	
	//进行ftp上传
	if($_SCONFIG['allowftp']) {
		include_once(S_ROOT.'./source/function_ftp.php');
		if(ftpupload($new_name, $filepath)) {
			$pic_remote = 1;
		} else {
			@unlink($new_name);
			runlog('ftp', 'Ftp Upload '.$new_name.' failed.');
			return cplang('ftp_upload_file_size');
		}
	} else {
		$pic_remote = 0;
	}
	
	//入库
	$setarr = array(
		'group_id' => $group['group_id'],
		'project_id' => $project_id,
		'uid' => $_SGLOBAL['supe_uid'],
		'filename' => addslashes($FILE['name']),
		'fileurl' => $filepath,
		'logtime' => $_SGLOBAL['timestamp'],
		'author' => $_SGLOBAL['member']['fullname'],
		'discussion_id' => $discussion_id,
		'useip' => getonlineip(),
		'type' => $fileext,
		'filetype' => addslashes($FILE['type']),
		'size' => $FILE['size'],
		'post_id' => $post_id,
		'remote' => $pic_remote,
		'width' => $file_width,
		'height' => $file_height,
		'invisible' => 0,
		'isimage' => $isimage,
		'thumb' => $thumb
	);
	$setarr['file_id'] = inserttable('file_short', $setarr, 1);
	
	//$_SGLOBAL['db']->query("UPDATE ".tname('project')." SET `file_num`=`file_num`+1,attachsize=attachsize+'$FILE[size]' WHERE project_id='{$project_id}'");
	//更新附件大小
	//$_SGLOBAL['db']->query("UPDATE ".tname('group')." SET attachsize=attachsize+'$FILE[size]' WHERE group_id='$group[group_id]'");
	return $setarr;
}

//数据流保存，所有数据均为存放相册的所以写入的数据一定只能是图片
function stream_save($strdata, $name ,$filetype = 'application/octet-stream', $project_id, $discussion_id=0,$post_id=0) {
	global $_SGLOBAL, $group, $_SCONFIG, $_SC;

	if($project_id<0) $project_id = 0;
	
	//判断后缀
	$fileext = strtolower(fileext($name));
	if(empty($fileext)){
		return cplang('only_allows_upload_file_types',array(implode(',',$_SCONFIG['allowfiletype'])));
	}
	if(in_array($fileext, $_SCONFIG['notallowfiletype'])) {
		$fileext = 'web';
	}
	if(!in_array($filetype, $_SCONFIG['allowfiletypeexp'])) {
		$filetype = 'application/octet-stream';
	}
	if(!in_array($fileext, $_SCONFIG['allowfiletype'])) {
		return cplang('only_allows_upload_file_types',array(implode(',',$_SCONFIG['allowfiletype'])));
	}
	$setarr = array();
	$filepath = getfilepath($fileext, $project_id,true);
	$newfilename = $_SC['attachdir'].'./'.$filepath;

	if($handle = fopen($newfilename, 'wb')) {
		if(fwrite($handle, $strdata) !== FALSE) {
			fclose($handle);
			$size = sizecount(filesize($newfilename));
			//检查空间大小

			$maxattachsize = $group['maxattachsize'];//单位KB
			if($maxattachsize) {//0为不限制
				if($group['gtype']) {//套餐用户
					if($_SGLOBAL['group_is_time_end']) {//时间过期,如果项目不超过免费用户的附件空间，则可以上传	
						if($group['attachsize'] + $size > $_SCONFIG['group_attachsize'][0]) {
							@unlink($newfilename);
							return cplang('inadequate_capacity_space');
						}
					} else {//时间未到期,检查附件空间
						if($group['attachsize'] + $size > $maxattachsize) {
							@unlink($newfilename);
							return cplang('inadequate_capacity_space');
						}
					}
				} else {//免费用户
					if($group['attachsize'] + $size > $maxattachsize) {
						@unlink($newfilename);
						return cplang('inadequate_capacity_space');
					}
				}
			}
			
			$file_width = 32;
			$file_height = 32;
			//检查是否图片
			$isimage = 0;
			if(in_array($fileext, $_SCONFIG['pictype'])) {
				if(function_exists('getimagesize')) {	
					$tmp_imagesize = @getimagesize($newfilename);
					list($tmp_width, $tmp_height, $tmp_type) = (array)$tmp_imagesize;
					$tmp_size = $tmp_width * $tmp_height;
					if($tmp_size > 16777216 || $tmp_size < 4 || empty($tmp_type) || strpos($tmp_imagesize['mime'], 'flash') > 0) {
						@unlink($newfilename);
						return cplang('only_allows_upload_file_types',array(implode(',',$_SCONFIG['allowfiletype'])));
					}
					$file_width = $tmp_width;
					$file_height = $tmp_height;
				}
				$isimage = 1;
				//缩略图
				$thumbpath = makethumb($newfilename);
			}
			$thumb = empty($thumbpath)?0:1;
			
			//进行ftp上传
			if($_SCONFIG['allowftp']) {
				include_once(S_ROOT.'./source/function_ftp.php');
				if(ftpupload($newfilename, $filepath)) {
					$pic_remote = 1;
				} else {
					@unlink($newfilename);
					@unlink($newfilename.'.thumb.jpg');
					runlog('ftp', 'Ftp Upload '.$newfilename.' failed.');
					return cplang('ftp_upload_file_size');
				}
			} else {
				$pic_remote = 0;
			}
			
			//入库
			$filename = addslashes(($name ? $name : substr(strrchr($filepath, '/'), 1)));
			
			$setarr = array(
				'group_id' => $group['group_id'],
				'project_id' => $project_id,
				'uid' => $_SGLOBAL['supe_uid'],
				'filename' => $filename,
				'fileurl' => $filepath,
				'logtime' => $_SGLOBAL['timestamp'],
				'author' => $_SGLOBAL['member']['fullname'],
				'discussion_id' => $discussion_id,
				'useip' => getonlineip(),
				'type' => $fileext,
				'filetype' => $filetype,
				'size' => $size,
				'post_id' => $post_id,
				'remote' => $pic_remote,
				'width' => $file_width,
				'height' => $file_height,
				'invisible' => 0,
				'isimage' => $isimage,
				'thumb' => $thumb
			);
			$setarr['file_id'] = inserttable('file_short', $setarr, 1);
			
			//$_SGLOBAL['db']->query("UPDATE ".tname('project')." SET `file_num`=`file_num`+1,attachsize=attachsize+'$size' WHERE project_id='{$project_id}'");
			//更新附件大小
			//$_SGLOBAL['db']->query("UPDATE ".tname('group')." SET attachsize=attachsize+'$size' WHERE group_id='$group[group_id]'");

			return $setarr;
    	} else {
    		fclose($handle);
    	}
	}
	return cplang('system_upload_error');
}
//生成缩略图
function makethumb($srcfile) {
	global $_SGLOBAL;

	//判断文件是否存在
	if (!file_exists($srcfile)) {
		return '';
	}
	$dstfile = $srcfile.'.thumb.jpg';
	
	include_once(S_ROOT.'./data/data_setting.php');

	//缩略图大小
	$tow = intval($_SGLOBAL['setting']['thumbwidth']);
	$toh = intval($_SGLOBAL['setting']['thumbheight']);
	if($tow < 300) $tow = 300;
	if($toh < 300) $toh = 300;
	
	//获取图片信息
	$im = '';
	if($data = getimagesize($srcfile)) {
		if($data[2] == 1) {
			if(function_exists("imagecreatefromgif")) {
				$im = imagecreatefromgif($srcfile);
			}
		} elseif($data[2] == 2) {
			if(function_exists("imagecreatefromjpeg")) {
				$im = imagecreatefromjpeg($srcfile);
			}
		} elseif($data[2] == 3) {
			if(function_exists("imagecreatefrompng")) {
				$im = imagecreatefrompng($srcfile);
			}
		}
	}
	
	if(!$im) return '';
	
	$srcw = imagesx($im);
	$srch = imagesy($im);
	
	$towh = $tow/$toh;
	$srcwh = $srcw/$srch;
	if($towh <= $srcwh){
		$ftow = $tow;
		$ftoh = $ftow*($srch/$srcw);
	} else {
		$ftoh = $toh;
		$ftow = $ftoh*($srcw/$srch);
	}
	if($srcw > $tow || $srch > $toh) {
		if(function_exists("imagecreatetruecolor") && function_exists("imagecopyresampled") && @$ni = imagecreatetruecolor($ftow, $ftoh)) {
			imagecopyresampled($ni, $im, 0, 0, 0, 0, $ftow, $ftoh, $srcw, $srch);
		} elseif(function_exists("imagecreate") && function_exists("imagecopyresized") && @$ni = imagecreate($ftow, $ftoh)) {
			imagecopyresized($ni, $im, 0, 0, 0, 0, $ftow, $ftoh, $srcw, $srch);
		} else {
			return '';
		}
		if(function_exists('imagejpeg')) {
			imagejpeg($ni, $dstfile);
		} elseif(function_exists('imagepng')) {
			imagepng($ni, $dstfile);
		}
		imagedestroy($ni);
	}
	imagedestroy($im);

	if(!file_exists($dstfile)) {
		return '';
	} else {
		return $dstfile;
	}
}
//获取附件上传路径
function getfilepath($fileext,$project_id, $mkdir=false) {
	global $_SGLOBAL, $_SC,$group;

	$filepath = "{$_SGLOBAL['supe_uid']}_{$_SGLOBAL['timestamp']}".substr(md5(microtime().random(6)), 8, 16).".$fileext";
	$filepath = preg_replace("/(php|phtml|php3|php4|jsp|exe|dll|asp|cer|asa|shtml|shtm|aspx|asax|cgi|fcgi|pl)(\.|$)/i", "_\\1\\2",$filepath);
	$name1 = $group['group_id'];
	$name2 = 'projects';
	$name3 = $project_id;
	$name4 = gmdate('Ym');
	$name5 = gmdate('j');

	if($mkdir) {
		$newfilename = $_SC['attachdir'].'./'.$name1;
		if(!is_dir($newfilename)) {
			if(!@mkdir($newfilename)) {
				runlog('error', "DIR: $newfilename can not make");
			}
		}
		$newfilename .= '/'.$name2;
		if(!is_dir($newfilename)) {
			if(!@mkdir($newfilename)) {
				runlog('error', "DIR: $newfilename can not make");
			}
		}
		$newfilename .= '/'.$name3;
		if(!is_dir($newfilename)) {
			if(!@mkdir($newfilename)) {
				runlog('error', "DIR: $newfilename can not make");
			}
		}
		$newfilename .= '/'.$name4;
		if(!is_dir($newfilename)) {
			if(!@mkdir($newfilename)) {
				runlog('error', "DIR: $newfilename can not make");
			}
		}
		$newfilename .= '/'.$name5;
		if(!is_dir($newfilename)) {
			if(!@mkdir($newfilename)) {
				runlog('error', "DIR: $newfilename can not make");
			}
		}
	}
	return "{$name1}/{$name2}/{$name3}/{$name4}/{$name5}/{$filepath}";
}
//保存头像
function avatar_save($FILE) {
	global $_SGLOBAL, $_SCONFIG, $group, $_SC;
	
	//允许上传类型
	$avatar_allowpictype = array('jpg','jpeg','gif','png');
	
	//检查
	$FILE['size'] = intval($FILE['size']);
	if(empty($FILE['size']) || empty($FILE['tmp_name']) || !empty($FILE['error'])) {
		return cplang('lack_of_access_to_upload_file_size');
	}

	//判断后缀
	$fileext = strtolower(fileext($FILE['name']));
	if(!in_array($fileext, $avatar_allowpictype)) {
		return cplang('only_allows_upload_file_types',array(implode(',',$avatar_allowpictype)));
	}

	//获取目录
	if(!$filepath = get_avatar_filepath($fileext,true)) {
		return cplang('unable_to_create_upload_directory_server');
	}

	$maxavatarsize = $_SCONFIG['maxavatarsize'];//单位KB
	if($maxavatarsize) {//0为不限制
		if($FILE['size'] > $maxavatarsize*1024) {
			return cplang('inadequate_capacity_space');
		}
	}

	//本地上传
	$new_name = $_SC['attachdir'].'./'.$filepath;
	$tmp_name = $FILE['tmp_name'];
	if(@copy($tmp_name, $new_name)) {
		@unlink($tmp_name);
	} elseif((function_exists('move_uploaded_file') && @move_uploaded_file($tmp_name, $new_name))) {
	} elseif(@rename($tmp_name, $new_name)) {
	} else {
		return cplang('mobile_picture_temporary_failure');
	}
	
	//检查是否图片
	if(function_exists('getimagesize')) {
		$tmp_imagesize = @getimagesize($new_name);
		list($tmp_width, $tmp_height, $tmp_type) = (array)$tmp_imagesize;
		$tmp_size = $tmp_width * $tmp_height;
		if($tmp_size > 16777216 || $tmp_size < 4 || empty($tmp_type) || strpos($tmp_imagesize['mime'], 'flash') > 0) {
			@unlink($new_name);
			return cplang('only_allows_upload_file_types');
		}
	}
	
	//缩略图
	$thumbpath = make_avatar_thumb($new_name);
	if(empty($thumbpath)) {
		@unlink($new_name);
		return cplang('thumb_create_error');
	} 

	//进行ftp上传
	if($_SCONFIG['allowftp']) {
		include_once(S_ROOT.'./source/function_ftp.php');
		if(ftpupload($new_name, $filepath)) {
			$pic_remote = 1;
		} else {
			@unlink($new_name);
			runlog('ftp', 'Ftp Upload '.$new_name.' failed.');
			return cplang('ftp_upload_file_size');
		}
	} else {
		$pic_remote = 0;
	}
	
	//入库
	$setarr = array(
		'filename' => addslashes($FILE['name']),
		'fileurl' => $filepath,
		'filetype' => addslashes($FILE['type']),
		'size' => $FILE['size']
	);
	return $setarr;
}
//生成头像缩略图
function make_avatar_thumb($srcfile) {
	global $_SGLOBAL,$_SCONFIG;

	//生成正方形的图BEGIN
	//判断文件是否存在
	if (!file_exists($srcfile)) {
		return '';
	}
	$filedir = substr($srcfile, 0, strripos($srcfile,'/'));

	//获取图片信息
	$im = '';
	if($data = getimagesize($srcfile)) {
		if($data[2] == 1) {
			if(function_exists("imagecreatefromgif")) {
				$im = imagecreatefromgif($srcfile);
			}
		} elseif($data[2] == 2) {
			if(function_exists("imagecreatefromjpeg")) {
				$im = imagecreatefromjpeg($srcfile);
			}
		} elseif($data[2] == 3) {
			if(function_exists("imagecreatefrompng")) {
				$im = imagecreatefrompng($srcfile);
			}
		}
	}
	if(!$im) return '';

	$srcw = imagesx($im);
	$srch = imagesy($im);
	$txt_left = 0;
	$txt_top = 0;
	if($srcw <= $srch){
		$txt_top = floor(($srch-$srcw)/2);
		$srch = $srcw;
	} else {
		$txt_left = floor(($srcw-$srch)/2);
		$srcw = $srch;
	}
	if(function_exists("imagecreatetruecolor") && function_exists("imagecopyresampled")) {
		
		if(@$ni96 = imagecreatetruecolor($_SCONFIG['avatarname']['96'], $_SCONFIG['avatarname']['96'])) {
			imagecopyresampled($ni96, $im, 0, 0, $txt_left, $txt_top, $_SCONFIG['avatarname']['96'], $_SCONFIG['avatarname']['96'], $srcw, $srch);
		}
		if(@$ni55 = imagecreatetruecolor($_SCONFIG['avatarname']['55'], $_SCONFIG['avatarname']['55'])) {
			imagecopyresampled($ni55, $im, 0, 0, $txt_left, $txt_top, $_SCONFIG['avatarname']['55'], $_SCONFIG['avatarname']['55'], $srcw, $srch);
		}
		if(@$ni40 = imagecreatetruecolor($_SCONFIG['avatarname']['40'], $_SCONFIG['avatarname']['40'])) {
			imagecopyresampled($ni40, $im, 0, 0, $txt_left, $txt_top, $_SCONFIG['avatarname']['40'], $_SCONFIG['avatarname']['40'], $srcw, $srch);
		}
		
	} elseif(function_exists("imagecreate") && function_exists("imagecopyresized")) {
		if(@$ni96 = imagecreate($_SCONFIG['avatarname']['96'], $_SCONFIG['avatarname']['96'])) {
			imagecopyresized($ni96, $im, 0, 0, $txt_left, $txt_top, $_SCONFIG['avatarname']['96'], $_SCONFIG['avatarname']['96'], $srcw, $srch);
		}
		if(@$ni55 = imagecreate($_SCONFIG['avatarname']['55'], $_SCONFIG['avatarname']['55'])) {
			imagecopyresized($ni55, $im, 0, 0, $txt_left, $txt_top, $_SCONFIG['avatarname']['55'], $_SCONFIG['avatarname']['55'], $srcw, $srch);
		}
		if(@$ni40 = imagecreate($_SCONFIG['avatarname']['40'], $_SCONFIG['avatarname']['40'])) {
			imagecopyresized($ni40, $im, 0, 0, $txt_left, $txt_top, $_SCONFIG['avatarname']['40'], $_SCONFIG['avatarname']['40'], $srcw, $srch);
		}
	} else {
		return '';
	}
	if(function_exists('imagejpeg')) {
		imagejpeg($ni96, $filedir."/avatar.{$_SCONFIG[avatarname][96]}.jpg");
		imagejpeg($ni55, $filedir."/avatar.{$_SCONFIG[avatarname][55]}.jpg");
		imagejpeg($ni40, $filedir."/avatar.{$_SCONFIG[avatarname][40]}.jpg");
	} elseif(function_exists('imagepng')) {
		imagepng($ni96, $filedir."/avatar.{$_SCONFIG[avatarname][96]}.jpg");
		imagepng($ni55, $filedir."/avatar.{$_SCONFIG[avatarname][55]}.jpg");
		imagepng($ni40, $filedir."/avatar.{$_SCONFIG[avatarname][40]}.jpg");
	}
	imagedestroy($ni96);
	imagedestroy($ni55);
	imagedestroy($ni40);
	imagedestroy($im);

	return $srcfile;
}
//获取头像上传路径
function get_avatar_filepath($fileext, $mkdir=false) {
	global $_SGLOBAL, $_SC,$group,$_SCONFIG;

	$filepath = "avatar.{$_SCONFIG[avatarname][big]}.jpg";
	$filepath = preg_replace("/(php|phtml|php3|php4|jsp|exe|dll|asp|cer|asa|shtml|shtm|aspx|asax|cgi|fcgi|pl)(\.|$)/i", "_\\1\\2",$filepath);
	$name1 = $group['group_id'];
	$name2 = 'people';
	$name3 = $_SGLOBAL['supe_uid'];

	if($mkdir) {
		$newfilename = $_SC['attachdir'].'./'.$name1;
		if(!is_dir($newfilename)) {
			if(!@mkdir($newfilename)) {
				runlog('error', "DIR: $newfilename can not make");
			}
		}
		$newfilename .= '/'.$name2;
		if(!is_dir($newfilename)) {
			if(!@mkdir($newfilename)) {
				runlog('error', "DIR: $newfilename can not make");
			}
		}
		$newfilename .= '/'.$name3;
		if(!is_dir($newfilename)) {
			if(!@mkdir($newfilename)) {
				runlog('error', "DIR: $newfilename can not make");
			}
		}
	}
	return $name1.'/'.$name2.'/'.$name3.'/'.$filepath;
}

function createmail($mail, $mailvar,$project_ids,$isexist = 0,$message = '') {
	global $_SGLOBAL, $_SCONFIG;
	$count_project_ids = 0;
	if(is_array($project_ids)) {
		$count_project_ids = count($project_ids);
	}
	if($count_project_ids == 0) {//没有选择项目
		$mailvar[0] = $_SGLOBAL['member']['fullname'].'邀请您加入 Teamdota';
		$mailvar[1] = '';
		$mailvar[2] = $_SGLOBAL['member']['email'];
		$mailvar[3] = '';
		$mailvar[4] = '';
		$mailvar[6] = $_SGLOBAL['member']['fullname'];
		
		smail($mail, cplang('invite_project_subject', array("您被邀请去Teamdota")), cplang('invite_group_massage', $mailvar));
		
	} elseif($count_project_ids == 1) {
		$query = $_SGLOBAL['db']->query("SELECT name,project_id FROM ".tname('project')." WHERE project_id='{$project_ids[0]}' LIMIT 1");
		$project = $_SGLOBAL['db']->fetch_array($query);
		
		$mailvar[0] = $_SGLOBAL['member']['fullname'].'邀请您加入此项目';
		$mailvar[1] = ' <h2 style="font-weight: normal; font-family: \'宋体\', helvetica, sans-serif; font-size: 12px; line-height: 24px; margin: 6px 0 9px;">
							<strong>'.$project['name'].'</strong>
						</h2>';
		$mailvar[2] = $_SGLOBAL['member']['email'];
		$mailvar[3] = empty($message) ? '' : cplang('invite_project_says',array($_SGLOBAL['member']['fullname'],getstr($message, 500)));
		$projectmember = array();
		$query = $_SGLOBAL['db']->query("SELECT m.fullname FROM ".tname('project_member')." pm,".tname('member')." m WHERE pm.project_id='{$project[project_id]}' AND pm.uid=m.uid ORDER BY id DESC LIMIT 0,10");
		while($value = $_SGLOBAL['db']->fetch_array($query)) {
			$projectmember[] = $value['fullname'];
		}
		$mailvar[4] = implode("&nbsp;&nbsp;", $projectmember);
		$mailvar[6] = $_SGLOBAL['member']['fullname'];
		
		smail($mail, cplang('invite_project_subject', array("您被邀请参与 {$project[name]} 项目")), cplang($isexist ? 'invite_project_massage' : 'invite_group_massage', $mailvar));
		
	} else {
		$query = $_SGLOBAL['db']->query("SELECT name,project_id FROM ".tname('project')." WHERE project_id IN (".simplode($project_ids).")");
		$projectlistdata = '<ul class="project_list">';
		while($value = $_SGLOBAL['db']->fetch_array($query)) {
			$projectlistdata .= '<li style="line-height: 16px; margin-bottom: 7px;">
									<strong>'.$value['name'].'</strong>
								</li>';
		}
		$projectlistdata .= '</ul>';
		$mailvar[0] = $_SGLOBAL['member']['fullname']."邀请您加入{$count_project_ids}个Teamdota项目";
		$mailvar[1] = $projectlistdata;
		$mailvar[2] = $_SGLOBAL['member']['email'];
		$mailvar[3] = '';
		$projectmember = array();
		$query = $_SGLOBAL['db']->query("SELECT m.fullname FROM ".tname('project_member')." pm,".tname('member')." m WHERE pm.project_id IN (".simplode($project_ids).") AND pm.uid=m.uid ORDER BY id DESC LIMIT 0,20");
		while($value = $_SGLOBAL['db']->fetch_array($query)) {
			$projectmember[] = $value['fullname'];
		}
		$projectmember = array_unique($projectmember);//去重
		$mailvar[4] = implode("&nbsp;&nbsp;", $projectmember);
		$mailvar[6] = $_SGLOBAL['member']['fullname'];
		
		smail($mail, cplang('invite_project_subject', array("您被邀请参与{$count_project_ids}个Teamdota项目")), cplang('invite_group_massage', $mailvar));
		
	}
	
}
//发送邮件到队列
function smail($email, $subject, $message='', $mailtype='', $from='') {
	global $_SGLOBAL, $_SCONFIG;
	
	$cid = 0;
	if($email) {
		//直接插入邮件
		$email = getstr($email, 100, 1, 1);
		
		//检查是否存在当前队列
		$hash_data = md5($email."\t".$subject."\t".$message);//合并hash
		$cid = 0;
		$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('mailcron')." WHERE email='{$email}' AND hash_data='{$hash_data}' LIMIT 1");
		if($value = $_SGLOBAL['db']->fetch_array($query)) {
			$cid = $value['cid'];
		} else {
			$cid = inserttable('mailcron', array('email'=>$email,'sendtime'=>$_SGLOBAL['timestamp'],'hash_data'=>$hash_data), 1);
		}
	}
	
	if($cid) {
		//插入邮件内容队列
		$setarr = array(
			'cid' => $cid,
			'subject' => addslashes(stripslashes($subject)),
			'message' => addslashes(stripslashes($message)),
			'dateline' => $_SGLOBAL['timestamp']
		);
		inserttable('mailqueue', $setarr);
		//送入httpsql队列
		//fastcgi_finish_request();
		//sleep(5);
		$email_content = array();
		$email_content['email'] = $email;
		$email_content['subject'] = $subject;
		$email_content['message'] = $message;
		if($from != '') {
			$email_content['from'] = encode_emailfrom($from);
		}
		include_once(S_ROOT.'./source/class_httpsqs.php');
		$httpsqs = new httpsqs;
		$httpsqs->put($_SCONFIG['httpsqs']['server'], $_SCONFIG['httpsqs']['port'], $_SCONFIG['httpsqs']['charset'], $_SCONFIG['httpsqs']['datakey']['sendmail'],urlencode(json_encode($email_content)));
	}
}

//邀请加入
function invite_project($invite,$fullname,$password) {
	global $_SGLOBAL;
	
	if($invite) {
		//更新邀请状态
		updatetable('invite', array('type'=>0), array('id'=>$invite['id']));
		$query = $_SGLOBAL['db']->query("SELECT isactive FROM ".tname('member')." WHERE uid='{$invite[fuid]}'");
		if($value = $_SGLOBAL['db']->fetch_array($query)) {
			if($value['isactive']==0)
				return false;
		}
		$salt = substr(uniqid(rand()), -6);
		$mpwd = md5(md5($password).$salt);
		//更新用户密码
		updatetable('member', array('fullname'=>$fullname,'password'=>$mpwd,'salt'=>$salt,'status'=>0,'isactive'=>0,'lastlogintime' => $_SGLOBAL[timestamp],'lastactivity' => $_SGLOBAL[timestamp],'regip' => getonlineip()), array('uid'=>$invite['fuid']));
		//更新项目关联状态
		updatetable('project_member', array('isactive'=>0), array('uid'=>$invite['fuid']));
		
		//在线session
		$session = array('uid' => $invite['fuid'], 'username' => $invite['email'] , 'password' => $mpwd);
		insertsession($session);

		//设置cookie
		ssetcookie('auth', authcode("$session[password]\t$session[uid]", 'ENCODE'), 2592000);
		ssetcookie('_refer', '');
		return true;
	}
	return false;
}

//重置密码
function password_reset($log,$password) {
	global $_SGLOBAL;
	
	if($log) {
		$salt = substr(uniqid(rand()), -6);
		$mpwd = md5(md5($password).$salt);
		//更新邀请状态
		updatetable('log_forgot_password', array('type'=>0), array('id'=>$log['id']));
		//更新用户密码
		updatetable('member', array('password'=>$mpwd,'salt'=>$salt), array('uid'=>$log['uid']));

		return true;
	}
	return false;
}

//事件发布
function notification_add($icon_url, $icon_op, $project_id, $project_name,$object_type, $object_id,  $title_html='', $title_text=array(), $body_html='', $body_text=array()) {
	global $_SGLOBAL,$group;
	$feedarr = array(
		'group_id' => $group['group_id'],
		'project_id' => $project_id,
		'project_name' => $project_name,
		'sender_id' => $_SGLOBAL['supe_uid'],
		'sender_author' => $_SGLOBAL['member']['fullname'],
		'title_html' => $title_html,
		'title_text' => serialize($title_text),
		'body_html' => $body_html,
		'body_text' => serialize($body_text),
		'icon_url' => $icon_url,
		'icon_op' => $icon_op,
		'object_id' => $object_id,
		'object_type' => $object_type,
		'created_time' => $_SGLOBAL['timestamp'],
		'href' => geturlbyobject_type($project_id, $object_type,$object_id)
	);
	$feedarr = saddslashes($feedarr);//增加转义
	return inserttable('notification', $feedarr, $returnid);
}
//推入回收站
function trash_can_add($icon_op, $project_id, $project_name,$object_type, $object_id,  $title_html='', $title_text=array(), $body_html='', $body_text=array()) {
	global $_SGLOBAL,$group;
	$feedarr = array(
		'group_id' => $group['group_id'],
		'project_id' => $project_id,
		'project_name' => $project_name,
		'sender_id' => $_SGLOBAL['supe_uid'],
		'sender_author' => $_SGLOBAL['member']['fullname'],
		'title_html' => $title_html,
		'title_text' => serialize($title_text),
		'body_html' => $body_html,
		'body_text' => serialize($body_text),
		'icon_op' => $icon_op,
		'object_id' => $object_id,
		'object_type' => $object_type,
		'created_time' => $_SGLOBAL['timestamp'],
		'href' => geturlbyobject_type($project_id, $object_type,$object_id)
	);
	$feedarr = saddslashes($feedarr);//增加转义
	$trash_id = inserttable('trash_can', $feedarr, 1);
	if($trash_id) {
		if($icon_op == 'delete') {
			inserttable('trash_can_log', array('object_id' => $object_id,'object_type' => $object_type,'created_time' => $_SGLOBAL['timestamp']), 0);
		} elseif($icon_op == 'restored') {
			$_SGLOBAL['db']->query("DELETE FROM ".tname('trash_can_log')." WHERE object_id ='{$object_id}' AND object_type='{$object_type}'");
		}
	}
	return $trash_id;
}
//添加主题发送通知操作
function notice_discussion_add($project_id, $discussion_id, $post_id, $uids) {
	global $_SGLOBAL,$group,$_SCONFIG;
	$k = 0;
	$uidstring = $_SGLOBAL['supe_uid'];
	foreach($uids as $key => $value) {
		$value = intval($value);
		if($value > 0) {
			$uidstring .= ",{$value}";
			$k++;
		}
	}
	if($k && $project_id && $discussion_id) {
		inserttable('notice_discussion', array('group_id'=>$group['group_id'],'project_id'=>$project_id,'discussion_id'=>$discussion_id,'uids'=>$uidstring), 0, true, 1);
		//送入httpsql队列
		include_once(S_ROOT.'./source/class_httpsqs.php');
		$httpsqs = new httpsqs;
		$httpsqs->put($_SCONFIG['httpsqs']['server'], $_SCONFIG['httpsqs']['port'], $_SCONFIG['httpsqs']['charset'], $_SCONFIG['httpsqs']['datakey']['senddiscussion'],urlencode(json_encode(array('group_id'=>$group['group_id'],'project_id'=>$project_id,'discussion_id'=>$discussion_id,'post_id'=>$post_id))));
	}
}
//添加附件发送通知操作
function notice_attachment_add($project_id, $file_id, $uids) {
	global $_SGLOBAL,$group,$_SCONFIG;
	$k = 0;
	$uidstring = $_SGLOBAL['supe_uid'];
	foreach($uids as $key => $value) {
		$value = intval($value);
		if($value > 0) {
			$uidstring .= ",{$value}";
			$k++;
		}
	}
	if($k && $project_id && $file_id) {
		inserttable('notice_attachment', array('group_id'=>$group['group_id'],'project_id'=>$project_id,'file_id'=>$file_id,'uids'=>$uidstring), 0, true, 1);
		//送入httpsql队列
		include_once(S_ROOT.'./source/class_httpsqs.php');
		$httpsqs = new httpsqs;
		$httpsqs->put($_SCONFIG['httpsqs']['server'], $_SCONFIG['httpsqs']['port'], $_SCONFIG['httpsqs']['charset'], $_SCONFIG['httpsqs']['datakey']['sendattachment'],urlencode(json_encode(array('group_id'=>$group['group_id'],'project_id'=>$project_id,'file_id'=>$file_id))));
	}
}
//根据idtype获得表
function geturlbyobject_type($project_id, $object_type,$object_id) {
	if($object_type == 'projectid') {
		$url = cplang('project_links',array($project_id));
	} elseif($object_type == 'discussionid') {
		$url = cplang('discussion_links',array($project_id,$object_id));
	} elseif($object_type == 'documentid') {
		$url = cplang('document_links',array($project_id,$object_id));
	} elseif($object_type == 'attachmentid') {
		$url = cplang('attachment_links',array($project_id,$object_id));
	} elseif($object_type == 'todosid') {
		$url = cplang('todos_links',array($project_id,$object_id));
	} elseif($object_type == 'todoslistid') {
		$url = cplang('todoslist_links',array($project_id,$object_id));
	}
	return $url;
}
//退订主题消息
function subscriptions_notice_discussion($discussion_id, $uid) {
	global $_SGLOBAL;
	$query = $_SGLOBAL['db']->query("SELECT uids,discussion_id FROM ".tname('notice_discussion')." WHERE discussion_id='{$discussion_id}' LIMIT 1");
	$uidstring = '';
	if($notice = $_SGLOBAL['db']->fetch_array($query)) {
		$k = 0;
		foreach(split(",",$notice['uids']) as $key => $value) {
			if($value != $uid) {
				if($k == 0) {
					$uidstring = $value;
				} else {
					$uidstring .= ",{$value}";
				}
			}
			$k++;
		}
		updatetable('notice_discussion', array('uids'=>$uidstring), array('discussion_id'=>$discussion_id));
	}
}
//退订附件消息
function subscriptions_notice_attachment($file_id, $uid) {
	global $_SGLOBAL;
	$query = $_SGLOBAL['db']->query("SELECT uids,file_id FROM ".tname('notice_attachment')." WHERE file_id='{$file_id}' LIMIT 1");
	$uidstring = '';
	if($notice = $_SGLOBAL['db']->fetch_array($query)) {
		$k = 0;
		foreach(split(",",$notice['uids']) as $key => $value) {
			if($value != $uid) {
				if($k == 0) {
					$uidstring = $value;
				} else {
					$uidstring .= ",{$value}";
				}
			}
			$k++;
		}
		updatetable('notice_attachment', array('uids'=>$uidstring), array('file_id'=>$file_id));
	}
}
//退订每日更新消息
function subscriptions_dialy($uid) {
	updatetable('member', array('issubscribe' => 1), array('uid'=>$uid));
}
//给邮件头加上base64_encode
function encode_emailfrom($from){
	global $_SC, $_SCONFIG;
	if($from == '') {
		$from = $_SCONFIG['sitename'];
	} else {
		$from = "{$from}({$_SCONFIG[sitename]})";
	}
	$email_from = '=?'.$_SC['charset'].'?B?'.base64_encode($from)."?= <".$_SCONFIG['notificationsemail'].">";
	return $email_from;
}
//添加待办事宜时同时发送分配邮件
function creat_todo_email($mail, $datavar) {
	global $_SGLOBAL, $_SCONFIG;
	$mail_subject_var[0] = $datavar['project_name'];//项目名称
	$mail_subject_var[1] =  $datavar['todoslist_subject'];//待办事宜清单标题
	
	$mail_message_var[0] = $_SGLOBAL['member']['fullname'];//创建待办事宜的作者
	$mail_message_var[1] = $datavar['project_name'];//项目名称
	$mail_message_var[2] = $datavar['todos_subject'];//待办事宜标题
	$mail_message_var[3] = $datavar['todoslist_subject'];//待办事宜清单标题
	$mail_message_var[4] = getsiteurl().cplang('todoslist_links', array( $datavar['project_id'], $datavar['todoslist_id']));//待办事宜访问地址
	$mail_message_var[5] = $datavar['todo_username'];//分配成员名字
	smail($mail, cplang('to_do_subject', $mail_subject_var), cplang('to_do_message', $mail_message_var), '',$_SGLOBAL['member']['fullname']);
}
?>