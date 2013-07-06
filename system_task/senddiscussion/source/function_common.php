<?php
/*
	[TEAMDOTA] (C) 2012 wanghui
	$Id: function_common.php 2012-03-31 09:59Z duty $
*/

if(!defined('IN_TEAMDOTA')) {
	exit('Access Denied');
}
//给邮件头加上base64_encode
function encode_emailfrom($from){
	global $_SC;
	if($from == '') {
		$from = $_SC['sitename'];
	} else {
		$from = "{$from}({$_SC[sitename]})";
	}
	$email_from = '=?'.$_SC['charset'].'?B?'.base64_encode($from)."?= <".$_SC['mail']['from'].">";
	return $email_from;
}
//获取到表名
function tname($name) {
	global $_SC;
	return $_SC['tablepre'].$name;
}
//语言替换
function lang_replace($text, $vars) {
	if($vars) {
		foreach ($vars as $k => $v) {
			$rk = $k + 1;
			$text = str_replace('\\'.$rk, $v, $text);
		}
	}
	return $text;
}
//发送邮件到队列
function smail($email, $subject, $message='', $from='') {
	global $_SC;
	
	$cid = 0;
	if($email) {
		//检查是否存在当前队列
		$hash_data = md5($email."\t".$subject."\t".$message);//合并hash
		$cid = 0;
		$query = $_SC['db']->query("SELECT * FROM ".tname('mailcron')." WHERE email='{$email}' AND hash_data='{$hash_data}' LIMIT 1");
		if($value = $_SC['db']->fetch_array($query)) {
			$cid = $value['cid'];
		} else {
			$cid = inserttable('mailcron', array('email'=>$email,'sendtime'=>$_SC['timestamp'],'hash_data'=>$hash_data), 1);
		}
	}
	
	if($cid) {
		//插入邮件内容队列
		$setarr = array(
			'cid' => $cid,
			'subject' => addslashes(stripslashes($subject)),
			'message' => addslashes(stripslashes($message)),
			'dateline' => $_SC['timestamp']
		);
		inserttable('mailqueue', $setarr);
		//送入httpsql队列
		include_once(S_ROOT.'./source/class_httpsqs.php');
		$httpsqs = new httpsqs;
		$httpsqs->put($_SC['httpsqs']['server'], $_SC['httpsqs']['port'], $_SC['httpsqs']['charset'], $_SC['httpsqs']['datakey']['sendmail'],urlencode(json_encode(array('email'=>$email,'subject'=>$subject,'message'=>$message,'from'=>$from))));
	}
}
//添加数据
function inserttable($tablename, $insertsqlarr, $returnid=0, $replace = false, $silent=0) {
	global $_SC;

	$insertkeysql = $insertvaluesql = $comma = '';
	foreach ($insertsqlarr as $insert_key => $insert_value) {
		$insertkeysql .= $comma.'`'.$insert_key.'`';
		$insertvaluesql .= $comma.'\''.$insert_value.'\'';
		$comma = ', ';
	}
	$method = $replace?'REPLACE':'INSERT';
	$_SC['db']->query($method.' INTO '.tname($tablename).' ('.$insertkeysql.') VALUES ('.$insertvaluesql.')', $silent?'SILENT':'');
	if($returnid && !$replace) {
		return $_SC['db']->insert_id();
	}
}
//SQL ADDSLASHES
function saddslashes($string) {
	if(is_array($string)) {
		foreach($string as $key => $val) {
			$string[$key] = saddslashes($val);
		}
	} else {
		$string = addslashes($string);
	}
	return $string;
}

//取消HTML代码
function shtmlspecialchars($string) {
	if(is_array($string)) {
		foreach($string as $key => $val) {
			$string[$key] = shtmlspecialchars($val);
		}
	} else {
		$string = preg_replace('/&amp;((#(\d{3,5}|x[a-fA-F0-9]{4})|[a-zA-Z][a-z0-9]{2,5});)/', '&\\1',
			str_replace(array('&', '"', '<', '>'), array('&amp;', '&quot;', '&lt;', '&gt;'), $string));
	}
	return $string;
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
	$yearmonth = sgmdate('Ym', $_SC['timestamp']);
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
function emailreplace($word) {
	if(isemail($word)) {
		$word = "<A href=\"mailto:{$word}\">{$word}</A>";
	}
	return $word;
}
//检查邮箱是否有效
function isemail($email) {
	return strlen($email) > 6 && preg_match("/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/", $email);
}
//字符串解密加密
function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {

	global $_SC;
	$ckey_length = 4;	// 随机密钥长度 取值 0-32;
				// 加入随机密钥，可以令密文无任何规律，即便是原文和密钥完全相同，加密结果也会每次不同，增大破解难度。
				// 取值越大，密文变动规律越大，密文变化 = 16 的 $ckey_length 次方
				// 当此值为 0 时，则不产生随机密钥

	$key = md5($key ? $key : $_SC['sitekey']);
	$keya = md5(substr($key, 0, 16));
	$keyb = md5(substr($key, 16, 16));
	$keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';

	$cryptkey = $keya.md5($keya.$keyc);
	$key_length = strlen($cryptkey);

	$string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
	$string_length = strlen($string);

	$result = '';
	$box = range(0, 255);

	$rndkey = array();
	for($i = 0; $i <= 255; $i++) {
		$rndkey[$i] = ord($cryptkey[$i % $key_length]);
	}

	for($j = $i = 0; $i < 256; $i++) {
		$j = ($j + $box[$i] + $rndkey[$i]) % 256;
		$tmp = $box[$i];
		$box[$i] = $box[$j];
		$box[$j] = $tmp;
	}

	for($a = $j = $i = 0; $i < $string_length; $i++) {
		$a = ($a + 1) % 256;
		$j = ($j + $box[$a]) % 256;
		$tmp = $box[$a];
		$box[$a] = $box[$j];
		$box[$j] = $tmp;
		$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
	}

	if($operation == 'DECODE') {
		if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
			return substr($result, 26);
		} else {
			return '';
		}
	} else {
		return $keyc.str_replace('=', '', base64_encode($result));
	}
}
//处理附件的图标
function file_icon_big($type) {
	$basepath = '/image/file_icons/';
	$icons_big = array('ai'=>'AI_big.png','aiff'=>'AIFF_big.png','csv'=>'CSV_big.png','dmg'=>'DMG_big.png','doc'=>'DOC_big.png','docx'=>'DOCX_big.png','eps'=>'EPS_big.png','fla'=>'FLA_big.png','gif'=>'GIF_big.png','htm'=>'HTM_big.png','html'=>'HTML_big.png','indd'=>'INDD_big.png','jpg'=>'JPG_big.png','jpeg'=>'JPEG_big.png','key'=>'KEY_big.png','link'=>'LINK_big.png','m4a'=>'M4A_big.png','m4v'=>'M4V_big.png','mov'=>'MOV_big.png','mp3'=>'MP3_big.png','mpeg'=>'MPEG_big.png','mpg'=>'MPG_big.png','numbers'=>'NUMBERS_big.png','odp'=>'ODP_big.png','ods'=>'ODS_big.png','odt'=>'ODT_big.png','pages'=>'PAGES_big.png','pdf'=>'PDF_big.png','png'=>'PNG_big.png','pot'=>'POT_big.png','ppt'=>'PPT_big.png','pptx'=>'PPTX_big.png','psd'=>'PSD_big.png','rar'=>'RAR_big.png','rm'=>'RM_big.png','rtf'=>'RTF_big.png','sit'=>'SIT_big.png','swf'=>'SWF_big.png','tar'=>'TAR_big.png','tgz'=>'TGZ_big.png','tif'=>'TIF_big.png','tiff'=>'TIFF_big.png','txt'=>'TXT_big.png','vsd'=>'VSD_big.png','wav'=>'WAV_big.png','web'=>'WEB_big.png','wma'=>'WMA_big.png','wmv'=>'WMV_big.png','xls'=>'XLS_big.png','xlsx'=>'XLSX_big.png','zip'=>'ZIP_big.png');
	if(!$icons_big[$type]) {
		return $basepath.'Generic_big.png';
	}
	return $basepath.$icons_big[$type];
}
function file_icon_jumbo($type) {
	$basepath = '/image/file_icons/';
	$icons_jumbo = array('ai'=>'AI_jumbo.png','aiff'=>'AIFF_jumbo.png','csv'=>'CSV_jumbo.png','dmg'=>'DMG_jumbo.png','doc'=>'DOC_jumbo.png','docx'=>'DOCX_jumbo.png','eps'=>'EPS_jumbo.png','fla'=>'FLA_jumbo.png','gif'=>'GIF_jumbo.png','htm'=>'HTM_jumbo.png','html'=>'HTML_jumbo.png','indd'=>'INDD_jumbo.png','jpg'=>'JPG_jumbo.png','jpeg'=>'JPEG_jumbo.png','key'=>'KEY_jumbo.png','link'=>'LINK_jumbo.png','m4a'=>'M4A_jumbo.png','m4v'=>'M4V_jumbo.png','mov'=>'MOV_jumbo.png','mp3'=>'MP3_jumbo.png','mpeg'=>'MPEG_jumbo.png','mpg'=>'MPG_jumbo.png','numbers'=>'NUMBERS_jumbo.png','odp'=>'ODP_jumbo.png','ods'=>'ODS_jumbo.png','odt'=>'ODT_jumbo.png','pages'=>'PAGES_jumbo.png','pdf'=>'PDF_jumbo.png','png'=>'PNG_jumbo.png','pot'=>'POT_jumbo.png','ppt'=>'PPT_jumbo.png','pptx'=>'PPTX_jumbo.png','psd'=>'PSD_jumbo.png','rar'=>'RAR_jumbo.png','rm'=>'RM_jumbo.png','rtf'=>'RTF_jumbo.png','sit'=>'SIT_jumbo.png','swf'=>'SWF_jumbo.png','tar'=>'TAR_jumbo.png','tgz'=>'TGZ_jumbo.png','tif'=>'TIF_jumbo.png','tiff'=>'TIFF_jumbo.png','txt'=>'TXT_jumbo.png','vsd'=>'VSD_jumbo.png','wav'=>'WAV_jumbo.png','web'=>'WEB_jumbo.png','wma'=>'WMA_jumbo.png','wmv'=>'WMV_jumbo.png','xls'=>'XLS_jumbo.png','xlsx'=>'XLSX_jumbo.png','zip'=>'ZIP_jumbo.png');
	if(!$icons_jumbo[$type]) {
		return $basepath.'Generic_jumbo.png';
	}
	return $basepath.$icons_jumbo[$type];
}
?>
