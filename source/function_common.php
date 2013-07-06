<?php
/*
	[TEAMDOTA] (C) 2012 wanghui
	$Id: function_common.php 2012-03-31 09:59Z duty $
*/

if(!defined('IN_TEAMDOTA')) {
	exit('Access Denied');
}
function checkUSER_AGENT() {
	$allow_version = array('msie'=>'9','chromeframe'=>'7','chrome'=>'7','safari'=>'5','firefox'=>'4','opera'=>'11');
	preg_match("/(Opera|MSIE|Firefox|Safari|Chrome)[\/\s]([\.\d]+)/", $_SERVER['HTTP_USER_AGENT'], $a);
	empty($a[1]) && preg_match("/Mozilla[\/\s]([\.\d]+)/", $_SERVER['HTTP_USER_AGENT'], $a);
	$browsers_name = strtolower($a[1]);
	$arr_version = split("\.",$a[2]);
	$is_browsers_true = false;
	if(count($arr_version) > 0) {
		$browsers_version = $arr_version[0];
		if($browsers_name == 'msie') {
			if($browsers_version >= $allow_version['msie']) {
				$is_browsers_true = true;
			} elseif(strpos($_SERVER["HTTP_USER_AGENT"],"chromeframe")) {
				$is_browsers_true = true;
				//header("X-UA-Compatible:IE=Edge,chrome=1");
			}
		} elseif($browsers_name == 'chrome') {
			if($browsers_version >= $allow_version['chrome']) {
				$is_browsers_true = true;
			}
		} elseif($browsers_name == 'safari') {
			if($browsers_version >= $allow_version['safari']) {
				$is_browsers_true = true;
			}
		} elseif($browsers_name == 'firefox') {
			if($browsers_version >= $allow_version['firefox']) {
				$is_browsers_true = true;
			}
		} elseif($browsers_name == 'opera') {
			preg_match("/(Version)[\/\s]([\.\d]+)/", $_SERVER['HTTP_USER_AGENT'], $o);
			$arr_version = split("\.",$o[2]);
			$browsers_version = $arr_version[0];
			if($browsers_version >= $allow_version['opera']) {
				$is_browsers_true = true;
			}
		}
	}
	if(!$is_browsers_true) {
		include_once template("browser");
		exit;
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

//字符串解密加密
function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {

	global $_SCONFIG;
	$ckey_length = 4;	// 随机密钥长度 取值 0-32;
				// 加入随机密钥，可以令密文无任何规律，即便是原文和密钥完全相同，加密结果也会每次不同，增大破解难度。
				// 取值越大，密文变动规律越大，密文变化 = 16 的 $ckey_length 次方
				// 当此值为 0 时，则不产生随机密钥

	$key = md5($key ? $key : $_SCONFIG['sitekey']);
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

//清空cookie
function clearcookie() {
	global $_SGLOBAL;
	obclean();
	ssetcookie('auth', '', -86400 * 365);
	$_SGLOBAL['supe_uid'] = 0;
	$_SGLOBAL['supe_username'] = '';
	$_SGLOBAL['member'] = array();
}

//cookie设置
function ssetcookie($var, $value, $life=0) {
	global $_SGLOBAL, $_SC, $_SERVER;
	$httponly = true;
	setcookie($_SC['cookiepre'].$var, $value, $life?($_SGLOBAL['timestamp']+$life):0, $_SC['cookiepath'], $_SC['cookiedomain'], $_SERVER['SERVER_PORT']==443?1:0, $httponly);
}

//数据库连接
function dbconnect() {
	global $_SGLOBAL, $_SC;

	include_once(S_ROOT.'./source/class_mysql.php');

	if(empty($_SGLOBAL['db'])) {
		$_SGLOBAL['db'] = new dbstuff;
		$_SGLOBAL['db']->charset = $_SC['dbcharset'];
		$_SGLOBAL['db']->connect($_SC['dbhost'], $_SC['dbuser'], $_SC['dbpw'], $_SC['dbname'], $_SC['pconnect']);
	}
}

//获取在线IP
function getonlineip($format=0) {
	global $_SGLOBAL;

	if(empty($_SGLOBAL['onlineip'])) {
		if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
			$onlineip = getenv('HTTP_CLIENT_IP');
		} elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
			$onlineip = getenv('HTTP_X_FORWARDED_FOR');
		} elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
			$onlineip = getenv('REMOTE_ADDR');
		} elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
			$onlineip = $_SERVER['REMOTE_ADDR'];
		}
		preg_match("/[\d\.]{7,15}/", $onlineip, $onlineipmatches);
		$_SGLOBAL['onlineip'] = $onlineipmatches[0] ? $onlineipmatches[0] : 'unknown';
	}
	if($format) {
		$ips = explode('.', $_SGLOBAL['onlineip']);
		for($i=0;$i<3;$i++) {
			$ips[$i] = intval($ips[$i]);
		}
		return sprintf('%03d%03d%03d', $ips[0], $ips[1], $ips[2]);
	} else {
		return $_SGLOBAL['onlineip'];
	}
}

//判断当前用户登录状态
function checkauth() {
	global $_SGLOBAL, $_SC, $_SCONFIG, $_SCOOKIE;
	if($_SCOOKIE['auth']) {
		@list($password, $uid) = explode("\t", authcode($_SCOOKIE['auth'], 'DECODE'));
		$_SGLOBAL['supe_uid'] = intval($uid);
		if($password && $_SGLOBAL['supe_uid']) {
			$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('member')." WHERE uid='$_SGLOBAL[supe_uid]'");
			if($member = $_SGLOBAL['db']->fetch_array($query)) {
				if($member['password'] == $password) {
					$_SGLOBAL['supe_username'] = addslashes($member['username']);
					if(empty($_SGLOBAL['member'])) {
						$_SGLOBAL['member'] = array('uid' => $member['uid'], 'group_id' => $member['group_id'], 'username' => $member['username'], 'email' => $member['email'], 'fullname' => $member['fullname'], 'ntype' => $member['ntype'], 'lastloginip' => $member['lastloginip'], 'lastlogintime' => $member['lastlogintime'], 'is_create_project' => $member['is_create_project'], 'timeoffset' => $member['timeoffset'], 'issubscribe' => $member['issubscribe']);
					}
				} else {
					$_SGLOBAL['supe_uid'] = 0;
				}
			} else {
				$_SGLOBAL['supe_uid'] = 0;
			}
		}
	}
	if(empty($_SGLOBAL['supe_uid'])) {
		clearcookie();
	} else {
		$session = array('uid' => $_SGLOBAL['supe_uid'], 'username' => $_SGLOBAL['supe_username'], 'password' => $password);
		$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('session')." WHERE `uid`='$_SGLOBAL[supe_uid]'");
		if(!$member = $_SGLOBAL['db']->fetch_array($query)) {
			insertsession($session);//登录
		}
	}
}

//获取到表名
function tname($name) {
	global $_SC;
	return $_SC['tablepre'].$name;
}

//对话框
function showmessage($msgkey, $url_forward='', $second=5, $values=array()) {
	global $_SGLOBAL, $_SC, $_SCONFIG, $group;

	obclean();
	
	//语言
	include_once(S_ROOT.'./language/lang_showmessage.php');
	if(isset($_SGLOBAL['msglang'][$msgkey])) {
		$message = lang_replace($_SGLOBAL['msglang'][$msgkey], $values);
	} else {
		$message = $msgkey;
	}
	//显示
	if(empty($_SGLOBAL['inajax']) && $url_forward && empty($second)) {
		header("HTTP/1.1 301 Moved Permanently");
		header("Location: $url_forward");
	} else {
		if($_SGLOBAL['inajax']) {
			if($url_forward) {
				$message = "<a href=\"$url_forward\">$message</a><ajaxok>";
			}
			echo $message;
			ob_out();
		} else {
			if($url_forward) {
				//$message = "<a href=\"$url_forward\">$message</a><script>setTimeout(\"window.location.href ='$url_forward';\", ".($second*1000).");</script>";
			}
			include template('showmessage');
		}
	}
	exit();
}

//添加session
function insertsession($setarr) {
	global $_SGLOBAL, $_SCONFIG;

	$_SCONFIG['onlinehold'] = intval($_SCONFIG['onlinehold']);
	if($_SCONFIG['onlinehold'] < 300) $_SCONFIG['onlinehold'] = 300;
	$_SGLOBAL['db']->query("DELETE FROM ".tname('session')." WHERE uid='$setarr[uid]' OR lastactivity<'".($_SGLOBAL['timestamp']-$_SCONFIG['onlinehold'])."'");

	//添加在线
	$ip = getonlineip(1);
	$setarr['lastactivity'] = $_SGLOBAL['timestamp'];
	$setarr['ip'] = $ip;

	inserttable('session', $setarr, 0, true, 1);

	$loginarr = array(
		'lastlogintime'=>"lastlogintime=lastactivity",
		'lastactivity'=>"lastactivity='$_SGLOBAL[timestamp]'",
		'lastloginip' => "lastloginip='$ip'"
	);
	$_SGLOBAL['supe_uid'] = $setarr['uid'];

	//更新用户
	$_SGLOBAL['db']->query("UPDATE ".tname('member')." SET ".implode(',', $loginarr)." WHERE uid='$setarr[uid]'");
	
}

//判断提交是否正确
function submitcheck($var) {
	if(!empty($_POST[$var]) && $_SERVER['REQUEST_METHOD'] == 'POST') {
		if((empty($_SERVER['HTTP_REFERER']) || preg_replace("/https?:\/\/([^\:\/]+).*/i", "\\1", $_SERVER['HTTP_REFERER']) == preg_replace("/([^\:]+).*/", "\\1", $_SERVER['HTTP_HOST'])) && $_POST['formhash'] == formhash()) {
			return true;
		} else {
			showmessage('submit_invalid');
		}
	} else {
		return false;
	}
}

//添加数据
function inserttable($tablename, $insertsqlarr, $returnid=0, $replace = false, $silent=0) {
	global $_SGLOBAL;

	$insertkeysql = $insertvaluesql = $comma = '';
	foreach ($insertsqlarr as $insert_key => $insert_value) {
		$insertkeysql .= $comma.'`'.$insert_key.'`';
		$insertvaluesql .= $comma.'\''.$insert_value.'\'';
		$comma = ', ';
	}
	$method = $replace?'REPLACE':'INSERT';
	$_SGLOBAL['db']->query($method.' INTO '.tname($tablename).' ('.$insertkeysql.') VALUES ('.$insertvaluesql.')', $silent?'SILENT':'');
	if($returnid && !$replace) {
		return $_SGLOBAL['db']->insert_id();
	}
}

//更新数据
function updatetable($tablename, $setsqlarr, $wheresqlarr, $silent=0) {
	global $_SGLOBAL;

	$setsql = $comma = '';
	foreach ($setsqlarr as $set_key => $set_value) {//fix
		$setsql .= $comma.'`'.$set_key.'`'.'=\''.$set_value.'\'';
		$comma = ', ';
	}
	$where = $comma = '';
	if(empty($wheresqlarr)) {
		$where = '1';
	} elseif(is_array($wheresqlarr)) {
		foreach ($wheresqlarr as $key => $value) {
			$where .= $comma.'`'.$key.'`'.'=\''.$value.'\'';
			$comma = ' AND ';
		}
	} else {
		$where = $wheresqlarr;
	}
	$_SGLOBAL['db']->query('UPDATE '.tname($tablename).' SET '.$setsql.' WHERE '.$where, $silent?'SILENT':'');
}

//获取账户信息
function getgroup($groupid) {
	global $_SGLOBAL, $_SCONFIG;

	$var = "group_{$groupid}";
	if(empty($_SGLOBAL[$var])) {
		$group = array();
		$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('group')." WHERE `group_id`='{$groupid}' LIMIT 1");
		if($value = $_SGLOBAL['db']->fetch_array($query)) {	
			$group['group_id'] = $value['group_id'];
			$group['group_name'] = $value['group_name'];
			$group['uid'] = $value['uid'];
			$group['gtype'] = $value['gtype'];
			$group['attachsize'] = $value['attachsize'];
			$group['maxattachsize'] = $value['maxattachsize'];
			$group['flag'] = $value['flag'];
			$group['logtime'] = $value['logtime'];
			$group['all_project_num'] = $value['all_project_num'];
			$group['project_num'] = $value['project_num'];
			$group['endtime'] = $value['endtime'];
		}
		$_SGLOBAL[$var] = $group;
	}
	return $_SGLOBAL[$var];
}
//已废弃，2012-07-12 author duty
function checkgroup_status($groupid) {
	global $_SGLOBAL, $_SCONFIG;
	//获取空间信息
	$group = getgroup($groupid);
	if($group) {
		//验证空间是否被锁定
		if($group['flag'] == 1) {
			showmessage('group_has_been_locked');
		}
		//如果是免费用户，判断试用时间
		if($group['gtype'] == 0){
			if(($_SGLOBAL['timestamp'] - $group['logtime']) > ($_SCONFIG['freetrial']*24*3600)){
				showmessage('group_has_been_freetrial');
			}
		}else{//判断使用时间是否到期
			if($_SGLOBAL['timestamp'] > $group['endtime']){
				showmessage('group_has_been_freetrial');
			}
		}
	} else{
		showmessage('group_not_allowed_to_visit');
	}
}

//检查项目权限
function checkproject($project_id) {
	global $_SGLOBAL, $group;
	
	$var = 'checkproject_'.$project_id;
	if(!isset($_SGLOBAL[$var])) {
		if(empty($_SGLOBAL['supe_uid'])) {
			$_SGLOBAL[$var] = '';
		} else {
			$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('project')." WHERE `project_id`='{$project_id}' LIMIT 1");
			if($value = $_SGLOBAL['db']->fetch_array($query)) {	
				if($value['group_id'] == $group['group_id']){
					$setarr = array('project_id'=>$value['project_id'],'group_id'=>$value['group_id'],'uid'=>$value['uid'],'name'=>$value['name'],'description'=>$value['description'],'discussion_num'=>$value['discussion_num'],'file_num'=>$value['file_num'],'document_num'=>$value['document_num'],'member_num'=>$value['member_num'],'todoslist_num'=>$value['todoslist_num'],'status'=>$value['status']);
					$querycheck = $_SGLOBAL['db']->query("SELECT * FROM ".tname('project_member')." WHERE `project_id`='{$project_id}' AND `uid`='".$_SGLOBAL['member']['uid']."' LIMIT 1");
					if($querycheck = $_SGLOBAL['db']->fetch_array($querycheck)) {
						$_SGLOBAL[$var] = $setarr;
					}else{
						$_SGLOBAL[$var] = '';
					}
				}else{
					$_SGLOBAL[$var] = '';
				}
			}else{
				$_SGLOBAL[$var] = '';
			}
		}
	}
	return $_SGLOBAL[$var];
}

//写运行日志
function runlog($file, $log, $halt=0) {
	global $_SGLOBAL, $_SERVER;

	$nowurl = $_SERVER['REQUEST_URI']?$_SERVER['REQUEST_URI']:($_SERVER['PHP_SELF']?$_SERVER['PHP_SELF']:$_SERVER['SCRIPT_NAME']);
	$log = sgmdate('Y-m-d H:i:s', $_SGLOBAL['timestamp'])."\t$type\t".getonlineip()."\t$_SGLOBAL[supe_uid]\t{$nowurl}\t".str_replace(array("\r", "\n"), array(' ', ' '), trim($log))."\n";
	$yearmonth = sgmdate('Ym', $_SGLOBAL['timestamp']);
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

//获取字符串
function getstr($string, $length, $in_slashes=0, $out_slashes=0, $censor=0, $bbcode=0, $html=0) {
	global $_SC, $_SGLOBAL;

	$string = trim($string);

	if($in_slashes) {
		//传入的字符有slashes
		$string = sstripslashes($string);
	}
	if($html < 0) {
		//去掉html标签
		$string = preg_replace("/(\<[^\<]*\>|\r|\n|\s|\[.+?\])/is", ' ', $string);
		$string = shtmlspecialchars($string);
	} elseif ($html == 0) {
		//转换html标签
		$string = shtmlspecialchars($string);
	}
	if($length && strlen($string) > $length) {
		//截断字符
		$wordscut = '';
		if(strtolower($_SC['charset']) == 'utf-8') {
			//utf8编码
			$n = 0;
			$tn = 0;
			$noc = 0;
			while ($n < strlen($string)) {
				$t = ord($string[$n]);
				if($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
					$tn = 1;
					$n++;
					$noc++;
				} elseif(194 <= $t && $t <= 223) {
					$tn = 2;
					$n += 2;
					$noc += 2;
				} elseif(224 <= $t && $t < 239) {
					$tn = 3;
					$n += 3;
					$noc += 2;
				} elseif(240 <= $t && $t <= 247) {
					$tn = 4;
					$n += 4;
					$noc += 2;
				} elseif(248 <= $t && $t <= 251) {
					$tn = 5;
					$n += 5;
					$noc += 2;
				} elseif($t == 252 || $t == 253) {
					$tn = 6;
					$n += 6;
					$noc += 2;
				} else {
					$n++;
				}
				if ($noc >= $length) {
					break;
				}
			}
			if ($noc > $length) {
				$n -= $tn;
			}
			$wordscut = substr($string, 0, $n);
		} else {
			for($i = 0; $i < $length - 1; $i++) {
				if(ord($string[$i]) > 127) {
					$wordscut .= $string[$i].$string[$i + 1];
					$i++;
				} else {
					$wordscut .= $string[$i];
				}
			}
		}
		$string = $wordscut;
	}
	if($bbcode) {
		include_once(S_ROOT.'./source/function_bbcode.php');
		$string = bbcode($string, $bbcode);
	}
	if($out_slashes) {
		$string = saddslashes($string);
	}
	return trim($string);
}

//时间格式化
function sgmdate($dateformat, $timestamp='', $format=0) {
	global $_SCONFIG, $_SGLOBAL;
	if(empty($timestamp)) {
		$timestamp = $_SGLOBAL['timestamp'];
	}
	$timeoffset = strlen($_SGLOBAL['member']['timeoffset'])>0?intval($_SGLOBAL['member']['timeoffset']):intval($_SCONFIG['timeoffset']);
	$result = '';
	if($format) {
		$time = $_SGLOBAL['timestamp'] - $timestamp;
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
//时间格式化
function daysgmdate($timestamp='') {
	global $_SGLOBAL;
	if(empty($timestamp)) {
		$timestamp = $_SGLOBAL['timestamp'];
	}
	$result = '';
	$time = $_SGLOBAL['timestamp'] - $timestamp;
	if($time <= 48*3600) {
		if ($time > 24*3600) {
			$result = lang('yesterday');
		} else {
			$result = lang('today');
		}
	}
	return $result;
}

//字符串时间化
function sstrtotime($string) {
	global $_SGLOBAL, $_SCONFIG;
	$timeoffset = strlen($_SGLOBAL['member']['timeoffset'])>0?intval($_SGLOBAL['member']['timeoffset']):intval($_SCONFIG['timeoffset']);
	$time = '';
	if($string) {
		$time = strtotime($string);
		if(gmdate('H:i', $_SGLOBAL['timestamp'] + $timeoffset * 3600) != date('H:i', $_SGLOBAL['timestamp'])) {
			$time = $time - $timeoffset * 3600;
		}
	}
	return $time;
}

//分页
function getpage($curPage=1, $pagesize=10, $total=0,  $url, $pagetag='page', $anchor='') {
	$countPage=0;
	if (($total % $pagesize) == 0 && $total !=0) {
      $countPage = $total / $pagesize;
	}else {
		$countPage = intval($total / $pagesize) + 1;
	}
	if ($pagetag == '') {
		$pagetag = 'page';
	}
	
	if (sizeof(explode('?',$url)) > 1) {
		$url .= '&';
	}else {
		$url .= '?';
	}
	if($curPage < 1)
		$curPage=1;
	if($curPage > $countPage)
		$curPage = $countPage;
	
	$pagedata = '';
	$j = ($curPage - 5 < 1) ? 1 : $curPage - 5;
	$k = ($curPage + 2 > $countPage) ? $countPage : $curPage + 2;
	
	$pagedata .= '<div class=global-page>';
	if ($curPage == 1) {
		$pagedata .= '<span>&laquo;上一页</span>&nbsp;';
	}else {
		$pagedata .= '<a href="';
		$pagedata .= $url;
		$pagedata .= $pagetag;
		$pagedata .= '=';
		$pagedata .= $curPage - 1;
		if ($anchor != '') {
			$pagedata .= $anchor;
		}
		$pagedata .= '" class="global-page-previous">';
		$pagedata .= '&laquo;上一页';
		$pagedata .= '</a>&nbsp;';
	}
	if ($j > 1) {
		$pagedata .= '<a href="';
		$pagedata .= $url;
		$pagedata .= $pagetag;
		$pagedata .= '=';
		$pagedata .= 1;
		if ($anchor != ''){
			$pagedata .= $anchor;
		}
		$pagedata .= '">';
		$pagedata .= 1;
		$pagedata .= '</a><span class="global-page-break"> ... </span>';
	}
	
	for ($i = $j; $i < $k + 1; $i++) {
		if ($curPage == $i) {
			$pagedata .= '<span class="select">';
			$pagedata .= $i;
			$pagedata .= '</span>&nbsp;';
			continue;
		}
		$pagedata .= '<a href="';
		$pagedata .= $url;
		$pagedata .= $pagetag;
		$pagedata .= '=';
		$pagedata .= $i;
		if ($anchor != '') {
			$pagedata .= $anchor;
		}
		$pagedata .= '">';
		$pagedata .= $i;
		$pagedata .= '</a>&nbsp;';
	}
	
	if ($countPage > $k) {
		$pagedata .= '<span class="global-page-break"> ... </span><a href="';
		$pagedata .= $url;
		$pagedata .= $pagetag;
		$pagedata .= '=';
		$pagedata .= $countPage;
		if ($anchor != '') {
			$pagedata .= $anchor;
		}
		$pagedata .= '">';
		$pagedata .= $countPage;
		$pagedata .= '</a>&nbsp;';
	}
	
	if ($curPage == $countPage) {
		$pagedata .= '<span>下一页&raquo;</span>';
	}else {
		$pagedata .= '<a href="';
		$pagedata .= $url;
		$pagedata .= $pagetag;
		$pagedata .= '=';
		$pagedata .= $curPage + 1;
		if ($anchor != '') {
			$pagedata .= $anchor;
		}
		$pagedata .= '" class="global-page-next">';
		$pagedata .= '下一页&raquo;';
		$pagedata .= '</a>';
	}
	$pagedata .= '</div>';
	return $pagedata;
}
//分页
function getpageajax($curPage=1, $pagesize=10, $total=0,  $urlarr, $pagetag='page', $anchor='') {
	$countPage=0;
	if (($total % $pagesize) == 0 && $total !=0) {
      $countPage = $total / $pagesize;
	}else {
		$countPage = intval($total / $pagesize) + 1;
	}
	if ($pagetag == '') {
		$pagetag = 'page';
	}
	$url = $urlarr[0];//ajax链接
	$showid = $urlarr[1];//显示的div id
	if (sizeof(explode('?',$url)) > 1) {
		$url .= '&';
	}else {
		$url .= '?';
	}
	if($curPage < 1)
		$curPage=1;
	if($curPage > $countPage)
		$curPage = $countPage;
	
	$pagedata = '';
	$j = ($curPage - 2 < 1) ? 1 : $curPage - 2;
	$k = ($curPage + 2 > $countPage) ? $countPage : $curPage + 2;
	
	$pagedata .= '<div class=global-page>';
	if ($curPage == 1) {
		$pagedata .= '<span>&laquo;上一页</span>&nbsp;';
	}else {
		$pagedata .= '<a href="javascript:;" onclick="ajaxget(\'';
		$pagedata .= $url;
		$pagedata .= $pagetag;
		$pagedata .= '=';
		$pagedata .= $curPage - 1;
		if ($anchor != '') {
			$pagedata .= $anchor;
		}
		$pagedata .= "','{$showid}')\"";
		$pagedata .= ' class="global-page-previous">';
		$pagedata .= '&laquo;上一页';
		$pagedata .= '</a>&nbsp;';
	}
	if ($j > 1) {
		$pagedata .= '<a href="javascript:;" onclick="ajaxget(\'';
		$pagedata .= $url;
		$pagedata .= $pagetag;
		$pagedata .= '=';
		$pagedata .= 1;
		if ($anchor != ''){
			$pagedata .= $anchor;
		}
		$pagedata .= "','{$showid}')\">";
		$pagedata .= 1;
		$pagedata .= '</a><span class="global-page-break">...</span>';
	}
	
	for ($i = $j; $i < $k + 1; $i++) {
		if ($curPage == $i) {
			$pagedata .= '<span class="select">';
			$pagedata .= $i;
			$pagedata .= '</span>&nbsp;';
			continue;
		}
		$pagedata .= '<a href="javascript:;" onclick="ajaxget(\'';
		$pagedata .= $url;
		$pagedata .= $pagetag;
		$pagedata .= '=';
		$pagedata .= $i;
		if ($anchor != '') {
			$pagedata .= $anchor;
		}
		$pagedata .= "','{$showid}')\">";
		$pagedata .= $i;
		$pagedata .= '</a>&nbsp;';
	}
	
	if ($countPage > $k) {
		$pagedata .= '<span class="global-page-break">...</span><a href="javascript:;" onclick="ajaxget(\'';
		$pagedata .= $url;
		$pagedata .= $pagetag;
		$pagedata .= '=';
		$pagedata .= $countPage;
		if ($anchor != '') {
			$pagedata .= $anchor;
		}
		$pagedata .= "','{$showid}')\">";
		$pagedata .= $countPage;
		$pagedata .= '</a>&nbsp;';
	}
	
	if ($curPage == $countPage) {
		$pagedata .= '<span>下一页&raquo;</span>';
	}else {
		$pagedata .= '<a href="javascript:;" onclick="ajaxget(\'';
		$pagedata .= $url;
		$pagedata .= $pagetag;
		$pagedata .= '=';
		$pagedata .= $curPage + 1;
		if ($anchor != '') {
			$pagedata .= $anchor;
		}
		$pagedata .= "','{$showid}')\"";
		$pagedata .= ' class="global-page-next">';
		$pagedata .= '下一页&raquo;';
		$pagedata .= '</a>';
	}
	$pagedata .= '</div>';
	return $pagedata;
}
//ob
function obclean() {
	global $_SC;

	ob_end_clean();
	if ($_SC['gzipcompress'] && function_exists('ob_gzhandler')) {
		ob_start('ob_gzhandler');
	} else {
		ob_start();
	}
}

//模板调用
function template($name) {
	global $_SCONFIG, $_SGLOBAL;
	if(strexists($name,'/')) {
		$tpl = $name;
	} else {
		$tpl = "template/$_SCONFIG[template]/template_$name";
	}
	$objfile = S_ROOT.'./'.$tpl.'.php';
	return $objfile;
}

//获取数目
function getcount($tablename, $wherearr=array(), $get='COUNT(*)') {
	global $_SGLOBAL;
	if(empty($wherearr)) {
		$wheresql = '1';
	} else {
		$wheresql = $mod = '';
		foreach ($wherearr as $key => $value) {
			$wheresql .= $mod."`$key`='$value'";
			$mod = ' AND ';
		}
	}
	return $_SGLOBAL['db']->result($_SGLOBAL['db']->query("SELECT $get FROM ".tname($tablename)." WHERE $wheresql LIMIT 1"), 0);
}

//调整输出
function ob_out() {
	global $_SGLOBAL, $_SCONFIG, $_SC;
	$content = ob_get_contents();
	$preg_searchs = $preg_replaces = $str_searchs = $str_replaces = array();
	
	if($_SGLOBAL['inajax']) {
		$preg_searchs[] = "/([\x01-\x09\x0b-\x0c\x0e-\x1f])+/";
		$preg_replaces[] = ' ';

		$str_searchs[] = ']]>';
		$str_replaces[] = ']]&gt;';
	}
	if($preg_searchs) {
		$content = preg_replace($preg_searchs, $preg_replaces, $content);
	}
	if($str_searchs) {
		$content = trim(str_replace($str_searchs, $str_replaces, $content));
	}
	
	obclean();
	if($_SGLOBAL['inajax']) {
		xml_out($content);
	} else{
		if($_SCONFIG['headercharset']) {
			@header('Content-Type: text/html; charset='.$_SC['charset']);
		}
		echo $content;
	}
}

function xml_out($content) {
	global $_SC;
	@header("Expires: -1");
	@header("Cache-Control: no-store, private, post-check=0, pre-check=0, max-age=0", FALSE);
	@header("Pragma: no-cache");
	@header("Content-type: application/xml; charset=$_SC[charset]");
	echo '<'."?xml version=\"1.0\" encoding=\"$_SC[charset]\"?>\n";
	echo "<root><![CDATA[".trim($content)."]]></root>";
	exit();
}
//处理搜索关键字
function stripsearchkey($string) {
	$string = trim($string);
	$string = str_replace('*', '%', addcslashes($string, '%_'));
	$string = str_replace('_', '\_', $string);
	return $string;
}

//连接字符
function simplode($ids) {
	return "'".implode("','", $ids)."'";
}

//显示进程处理时间
function debuginfo() {
	global $_SGLOBAL, $_SC, $_SCONFIG;

	if(empty($_SCONFIG['debuginfo'])) {
		$info = '';
	} else {
		$mtime = explode(' ', microtime());
		$totaltime = number_format(($mtime[1] + $mtime[0] - $_SGLOBAL['supe_starttime']), 4);
		$info = 'Processed in '.$totaltime.' second(s), '.$_SGLOBAL['db']->querynum.' queries'.
				($_SC['gzipcompress'] ? ', Gzip enabled' : NULL);
	}

	return $info;
}

//格式化大小函数
function formatsize($size) {
	$prec=3;
	$size = round(abs($size));
	$units = array(0=>" B ", 1=>" KB", 2=>" MB", 3=>" GB", 4=>" TB");
	if ($size==0) return str_repeat(" ", $prec)."0$units[0]";
	$unit = min(4, floor(log($size)/log(2)/10));
	$size = $size * pow(2, -10*$unit);
	$digi = $prec - 1 - floor(log($size)/log(10));
	$size = round($size * pow(10, $digi)) * pow(10, -$digi);
	return $size.$units[$unit];
}

//获取文件内容
function sreadfile($filename) {
	$content = '';
	if(function_exists('file_get_contents')) {
		@$content = file_get_contents($filename);
	} else {
		if(@$fp = fopen($filename, 'r')) {
			@$content = fread($fp, filesize($filename));
			@fclose($fp);
		}
	}
	return $content;
}

//写入文件
function swritefile($filename, $writetext, $openmod='w') {
	if(@$fp = fopen($filename, $openmod)) {
		flock($fp, 2);
		fwrite($fp, $writetext);
		fclose($fp);
		return true;
	} else {
		runlog('error', "File: $filename write error.");
		return false;
	}
}

//产生随机字符
function random($length, $numeric = 0) {
	PHP_VERSION < '4.2.0' ? mt_srand((double)microtime() * 1000000) : mt_srand();
	$seed = base_convert(md5(print_r($_SERVER, 1).microtime()), 16, $numeric ? 10 : 35);
	$seed = $numeric ? (str_replace('0', '', $seed).'012340567890') : ($seed.'zZ'.strtoupper($seed));
	$hash = '';
	$max = strlen($seed) - 1;
	for($i = 0; $i < $length; $i++) {
		$hash .= $seed[mt_rand(0, $max)];
	}
	return $hash;
}

//判断字符串是否存在
function strexists($haystack, $needle) {
	return !(strpos($haystack, $needle) === FALSE);
}

//检查站点是否关闭
function checkclose() {
	global $_SGLOBAL, $_SCONFIG;

	//站点关闭
	if($_SCONFIG['close']) {
		if(empty($_SCONFIG['closereason'])) {
			showmessage('site_temporarily_closed');
		} else {
			showmessage($_SCONFIG['closereason']);
		}
	}
}

//站点链接
function getsiteurl() {
	global $_SCONFIG;

	if(empty($_SCONFIG['siteallurl'])) {
		$uri = $_SERVER['REQUEST_URI']?$_SERVER['REQUEST_URI']:($_SERVER['PHP_SELF']?$_SERVER['PHP_SELF']:$_SERVER['SCRIPT_NAME']);
		return shtmlspecialchars('http://'.$_SERVER['HTTP_HOST'].substr($uri, 0, strrpos($uri, '/')+1));
	} else {
		return $_SCONFIG['siteallurl'];
	}
}

//获取文件名后缀
function fileext($filename) {
	return strtolower(trim(substr(strrchr($filename, '.'), 1)));
}

//去掉slassh
function sstripslashes($string) {
	if(is_array($string)) {
		foreach($string as $key => $val) {
			$string[$key] = sstripslashes($val);
		}
	} else {
		$string = stripslashes($string);
	}
	return $string;
}

//编码转换
function siconv($str, $out_charset, $in_charset='') {
	global $_SC;

	$in_charset = empty($in_charset)?strtoupper($_SC['charset']):strtoupper($in_charset);
	$out_charset = strtoupper($out_charset);
	if($in_charset != $out_charset) {
		if (function_exists('iconv') && (@$outstr = iconv("$in_charset//IGNORE", "$out_charset//IGNORE", $str))) {
			return $outstr;
		} elseif (function_exists('mb_convert_encoding') && (@$outstr = mb_convert_encoding($str, $out_charset, $in_charset))) {
			return $outstr;
		}
	}
	return $str;//转换失败
}

//处理上传图片连接
function pic_get($filepath, $thumb, $remote) {
	global $_SCONFIG, $_SC;

	if(empty($filepath)) {
		$url = 'image/nopic.gif';
	} else {
		$url = $filepath;
		if($thumb) $url .= '.thumb.jpg';
		if($remote) {
			$url = $_SCONFIG['ftpurl'].$url;
		} else {
			$url = $_SC['attachurl'].$url;
		}
	}
	return $url;
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

//重新组建
function renum($array) {
	$newnums = $nums = array();
	foreach ($array as $id => $num) {
		$newnums[$num][] = $id;
		$nums[$num] = $num;
	}
	return array($nums, $newnums);
}

//整理notification
function mknotification($feed) {
	global $_SGLOBAL, $_SN, $_SCONFIG;

	$feed['title_text'] = empty($feed['title_text'])?array():unserialize($feed['title_text']);
	if(!is_array($feed['title_text'])) $feed['title_text'] = array();
	$feed['body_text'] = empty($feed['body_text'])?array():unserialize($feed['body_text']);
	if(!is_array($feed['body_text'])) $feed['body_text'] = array();

	//title
	$searchs = $replaces = array();
	if($feed['title_text'] && is_array($feed['title_text'])) {
		foreach (array_keys($feed['title_text']) as $key) {
			$searchs[] = '{'.$key.'}';
			$replaces[] = $feed['title_text'][$key];
		}
	}

	$searchs[] = '{actor}';
	$replaces[] = ($_SGLOBAL['supe_uid'] == $feed['sender_id'] ? '' : $feed['sender_author']);
	
	$feed['title_html'] = str_replace($searchs, $replaces, $feed['title_html']);

	//body
	$searchs = $replaces = array();
	if($feed['body_text'] && is_array($feed['body_text'])) {
		foreach (array_keys($feed['body_text']) as $key) {
			$searchs[] = '{'.$key.'}';
			$replaces[] = $feed['body_text'][$key];
		}
	}
	
	$searchs[] = '{actor}';
	$replaces[] = "<a href=\"group.php?do=people&uid=$feed[sender_id]\">$feed[sender_author]</a>";
	$feed['body_html'] = mktarget(str_replace($searchs, $replaces, $feed['body_html']));

	return $feed;
}

//整理feed的链接
function mktarget($html) {
	if($html) {
		$html = preg_replace("/<a(.+?)href=([\'\"]?)([^>\s]+)\\2([^>]*)>/i", '<a target="_blank" \\1 href="\\3" \\4>', $html);
	}
	return $html;
}

//ip访问允许
function ipaccess($ipaccess) {
	return empty($ipaccess)?true:preg_match("/^(".str_replace(array("\r\n", ' '), array('|', ''), preg_quote($ipaccess, '/')).")/", getonlineip());
}

//ip访问禁止
function ipbanned($ipbanned) {
	return empty($ipbanned)?false:preg_match("/^(".str_replace(array("\r\n", ' '), array('|', ''), preg_quote($ipbanned, '/')).")/", getonlineip());
}

//检查是否登录
function checklogin() {
	global $_SGLOBAL, $_SCONFIG;

	if(empty($_SGLOBAL['supe_uid'])) {
		ssetcookie('_refer', rawurlencode($_SERVER['REQUEST_URI']));
		showmessage('to_login', 'do.php?ac=login');
	}
}

//获得前台语言
function lang($key, $vars=array()) {
	global $_SGLOBAL;

	include_once(S_ROOT.'./language/lang_source.php');
	if(isset($_SGLOBAL['sourcelang'][$key])) {
		$result = lang_replace($_SGLOBAL['sourcelang'][$key], $vars);
	} else {
		$result = $key;
	}
	return $result;
}

//获得后台语言
function cplang($key, $vars=array()) {
	global $_SGLOBAL;

	include_once(S_ROOT.'./language/lang_cp.php');
	if(isset($_SGLOBAL['cplang'][$key])) {
		$result = lang_replace($_SGLOBAL['cplang'][$key], $vars);
	} else {
		$result = $key;
	}
	return $result;
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
//截取链接
function sub_url($url, $length) {
	if(strlen($url) > $length) {
		$url = str_replace(array('%3A', '%2F'), array(':', '/'), rawurlencode($url));
		$url = substr($url, 0, intval($length * 0.5)).' ... '.substr($url, - intval($length * 0.3));
	}
	return $url;
}

//取数组中的随机个
function sarray_rand($arr, $num=1) {
	$r_values = array();
	if($arr && count($arr) > $num) {
		if($num > 1) {
			$r_keys = array_rand($arr, $num);
			foreach ($r_keys as $key) {
				$r_values[$key] = $arr[$key];
			}
		} else {
			$r_key = array_rand($arr, 1);
			$r_values[$r_key] = $arr[$r_key];
		}
	} else {
		$r_values = $arr;
	}
	return $r_values;
}

//产生form防伪码
function formhash() {
	global $_SGLOBAL, $_SCONFIG;

	if(empty($_SGLOBAL['formhash'])) {
		$_SGLOBAL['formhash'] = substr(md5(substr($_SGLOBAL['timestamp'], 0, -7).'|'.$_SGLOBAL['supe_uid'].'|'.md5($_SCONFIG['sitekey'])), 8, 8);
	}
	return $_SGLOBAL['formhash'];
}

//检查邮箱是否有效
function isemail($email) {
	return strlen($email) > 6 && preg_match("/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/", $email);
}

//获取目录
function sreaddir($dir, $extarr=array()) {
	$dirs = array();
	if($dh = opendir($dir)) {
		while (($file = readdir($dh)) !== false) {
			if(!empty($extarr) && is_array($extarr)) {
				if(in_array(strtolower(fileext($file)), $extarr)) {
					$dirs[] = $file;
				}
			} else if($file != '.' && $file != '..') {
				$dirs[] = $file;
			}
		}
		closedir($dh);
	}
	return $dirs;
}
//屏蔽html
function checkhtml($html) {
	$html = stripslashes($html);
	preg_match_all("/\<([^\<]+)\>/is", $html, $ms);

	$searchs[] = '<';
	$replaces[] = '&lt;';
	$searchs[] = '>';
	$replaces[] = '&gt;';
	
	if($ms[1]) {
		$allowtags = 'img|a|font|div|table|tbody|caption|tr|td|th|br|p|b|strong|i|u|em|span|ol|ul|li|blockquote';//允许的标签
		$ms[1] = array_unique($ms[1]);
		foreach ($ms[1] as $value) {
			$searchs[] = "&lt;".$value."&gt;";
			$value = shtmlspecialchars($value);
			$value = str_replace(array('\\','/*'), array('.','/.'), $value);
			$skipkeys = array('onabort','onactivate','onafterprint','onafterupdate','onbeforeactivate','onbeforecopy','onbeforecut','onbeforedeactivate',
					'onbeforeeditfocus','onbeforepaste','onbeforeprint','onbeforeunload','onbeforeupdate','onblur','onbounce','oncellchange','onchange',
					'onclick','oncontextmenu','oncontrolselect','oncopy','oncut','ondataavailable','ondatasetchanged','ondatasetcomplete','ondblclick',
					'ondeactivate','ondrag','ondragend','ondragenter','ondragleave','ondragover','ondragstart','ondrop','onerror','onerrorupdate',
					'onfilterchange','onfinish','onfocus','onfocusin','onfocusout','onhelp','onkeydown','onkeypress','onkeyup','onlayoutcomplete',
					'onload','onlosecapture','onmousedown','onmouseenter','onmouseleave','onmousemove','onmouseout','onmouseover','onmouseup','onmousewheel',
					'onmove','onmoveend','onmovestart','onpaste','onpropertychange','onreadystatechange','onreset','onresize','onresizeend','onresizestart',
					'onrowenter','onrowexit','onrowsdelete','onrowsinserted','onscroll','onselect','onselectionchange','onselectstart','onstart','onstop',
					'onsubmit','onunload','javascript','script','eval','behaviour','expression','style','class');
			$skipstr = implode('|', $skipkeys);
			$value = preg_replace(array("/($skipstr)/i"), '.', $value);
			if(!preg_match("/^[\/|\s]?($allowtags)(\s+|$)/is", $value)) {
				$value = '';
			}
			$replaces[] = empty($value)?'':"<".str_replace('&quot;', '"', $value).">";
		}
	}
	$html = str_replace($searchs, $replaces, $html);
	$html = addslashes($html);
	
	return $html;
}
//获取图片列表中高度和宽度
function get_thumbwh($srcw,$srch,$maxconfig=array()){
	//缩略图大小
	global $_SCONFIG;
	if(empty($maxconfig)){
		$maxtow = $_SCONFIG['post_thumb']['maxthumbwidth'];
		$maxtoh = $_SCONFIG['post_thumb']['maxthumbheight'];
	} else {
		$maxtow = $maxconfig['maxthumbwidth'];
		$maxtoh = $maxconfig['maxthumbheight'];
	}
	if($srcw <= $maxtow && $srch <= $maxtoh) {
		return array('thumbwidth'=>$srcw,'thumbheight'=>$srch);
	}
	$towh = $maxtow/$maxtoh;
	$srcwh = $srcw/$srch;
	if($towh <= $srcwh){
		$fmaxtow = $maxtow;
		$fmaxtoh = $fmaxtow*($srch/$srcw);
	} else {
		$fmaxtoh = $maxtoh;
		$fmaxtow = $fmaxtoh*($srcw/$srch);
	}
	return array('thumbwidth'=>intval($fmaxtow),'thumbheight'=>intval($fmaxtoh));
}
function dheader($string, $replace = true, $http_response_code = 0) {
	$string = str_replace(array("\r", "\n"), array('', ''), $string);
	if(empty($http_response_code)) {
		@header($string, $replace);
	} else {
		@header($string, $replace, $http_response_code);
	}
	if(preg_match('/^\s*location:/is', $string)) {
		exit();
	}
}
//判断用户的项目管理权限
function check_project_manage($projectuid=0,$ownuid=0) {
	global $_SGLOBAL;
	$allowmanage = $_SGLOBAL['member']['ntype'];//判断是否群组的创建者
	if($allowmanage) {
		return $allowmanage;
	}
	if(empty($ownuid)) {
		return $_SGLOBAL['supe_uid'] == $projectuid;
	} else {
		return $_SGLOBAL['supe_uid'] == $projectuid || $_SGLOBAL['supe_uid'] == $ownuid;
	}
}
function sizecount($filesize) {
	//$filesize = ceil($filesize / 1024);
	//return intval($filesize);
	return $filesize;
}

function sizecount_decode($filesize) {
	//return $filesize * 1024;
	return $filesize;
}

function sizecountname($filesize) {
	if($filesize >= 1073741824) {
		$filesize = round($filesize / 1073741824 * 100) / 100 . ' GB';
	} elseif($filesize >= 1048576) {
		$filesize = round($filesize / 1048576 * 100) / 100 . ' MB';
	} elseif($filesize >= 1024) {
		$filesize = round($filesize / 1024 * 100) / 100 . ' KB';
	} else {
		$filesize = $filesize . ' Bytes';
	}
	return $filesize;
}
/**
 * 分析服务器负载
 *
 * 只针对*unix服务器有效
 *
 * @param int $maxLoadAvg 负载最大值
 * @return boolean 是否超过最大负载
 */
function sloadAvg($maxLoadAvg) {
	$avgstats = 0;
	if (@file_exists('/proc/loadavg')) {
		if ($fp = @fopen('/proc/loadavg', 'r')) {
			$avgdata = @fread($fp, 6);
			@fclose($fp);
			list($avgstats) = explode(' ', $avgdata);
		}
	}
	if ($avgstats > $maxLoadAvg) {
		return true;
	} else {
		return false;
	}
}
/**
 * CC攻击处理
 *
 * CC攻击会导致服务器负载过大,对相关客户端请求进行处理并日志
 *
 * @global int $timestamp
 * @global string $onlineip
 * @global array $pwServer
 * @global string $db_xforwardip
 * @param int $ccLoad 服务器负载参数
 * @return void
 */
function sDefendCc($ccLoad) {
	global $_SGLOBAL, $_SCOOKIE;
	$onlineip = getonlineip();
	if ($ccLoad == 2 && !empty($_SERVER['HTTP_USER_AGENT'])) {
		$userAgent = strtolower($_SERVER['HTTP_USER_AGENT']);
		if (str_replace(array('spider', 'google', 'msn', 'yodao', 'yahoo', 'http:'), '', $userAgent) != $userAgent) {
			$ccLoad = 1;
		}
	}
	ssetcookie('_stamp', $_SGLOBAL['timestamp'], 0);
	$ccTimestamp = $_SCOOKIE['_stamp'];
	$ccCrc32 = substr(md5($ccTimestamp . $_SERVER['HTTP_REFERER']), 0, 10);
	$ccBanedIp = readover(S_ROOT.'./data/ccbanip.txt');
	if ($ccBanedIp && $ipOffset = strpos("$ccBanedIp\n", "\t$onlineip\n")) {
		$ccLtt = substr($ccBanedIp, $ipOffset - 10, 10);
		$ccCrc32 == $ccLtt && exit('Forbidden, Please turn off CC');
		writeover(S_ROOT.'./data/ccbanip.txt', str_replace("\n$ccLtt\t$onlineip", '', $ccBanedIp));
	}
	if ($ccLoad && ($_SGLOBAL['timestamp'] - $ccTimestamp > 3 || $_SGLOBAL['timestamp'] < $ccTimestamp)) {
		$isCc = false;
		if ($fp = @fopen(S_ROOT.'./data/ccip.txt', 'rb')) {
			flock($fp, LOCK_SH);
			$size = 27 * 800;
			fseek($fp, -$size, SEEK_END);
			while (!feof($fp)) {
				$value = explode("\t", fgets($fp, 29));
				if (trim($value[1]) == $onlineip && $ccCrc32 == $value[0]) {
					$isCc = true;
					break;
				}
			}
			fclose($fp);
		}
		if ($isCc) {
			echo 'Forbidden, Please Refresh';
			$banIps = '';
			$ccBanedIp && $banIps .= implode("\n", array_slice(explode("\n", $ccBanedIp), -999));
			$banIps .= "\n" . $ccCrc32 . "\t" . $onlineip;
			writeover(S_ROOT.'./data/ccbanip.txt', $banIps);
			exit();
		}
		@filesize(S_ROOT.'./data/ccip.txt') > 27 * 1000 && sunlink(S_ROOT.'./data/ccip.txt');
		writeover(S_ROOT.'./data/ccip.txt', "$ccCrc32\t$onlineip\n", 'ab');
	}
}
//文件处理

/**
 * 删除文件
 *
 * @param string $fileName 文件绝对路径
 * @return bool
 */
function sunlink($fileName) {
	return @unlink(escapePath($fileName));
}

/**
 * 读取文件
 *
 * @param string $fileName 文件绝对路径
 * @param string $method 读取模式
 */
function readover($fileName, $method = 'rb') {
	$fileName = escapePath($fileName);
	$data = '';
	if ($handle = @fopen($fileName, $method)) {
		flock($handle, LOCK_SH);
		$data = @fread($handle, filesize($fileName));
		fclose($handle);
	}
	return $data;
}

/**
 * 写文件
 *
 * @param string $fileName 文件绝对路径
 * @param string $data 数据
 * @param string $method 读写模式
 * @param bool $ifLock 是否锁文件
 * @param bool $ifCheckPath 是否检查文件名中的“..”
 * @param bool $ifChmod 是否将文件属性改为可读写
 * @return bool 是否写入成功   :注意rb+创建新文件均返回的false,请用wb+
 */
function writeover($fileName, $data, $method = 'rb+', $ifLock = true, $ifCheckPath = true, $ifChmod = true) {
	$fileName = escapePath($fileName, $ifCheckPath);
	touch($fileName);
	$handle = fopen($fileName, $method);
	$ifLock && flock($handle, LOCK_EX);
	$writeCheck = fwrite($handle, $data);
	$method == 'rb+' && ftruncate($handle, strlen($data));
	fclose($handle);
	$ifChmod && @chmod($fileName, 0777);
	return $writeCheck;
}
/**
 * 路径转换
 * @param $fileName
 * @param $ifCheck
 * @return string
 */
function escapePath($fileName, $ifCheck = true) {
	if (!_escapePath($fileName, $ifCheck)) {
		exit('Forbidden');
	}
	return $fileName;
}
/**
 * 私用路径转换
 * @param $fileName
 * @param $ifCheck
 * @return boolean
 */
function _escapePath($fileName, $ifCheck = true) {
	$tmpname = strtolower($fileName);
	$tmparray = array('://',"\0");
	$ifCheck && $tmparray[] = '..';
	if (str_replace($tmparray, '', $tmpname) != $tmpname) {
		return false;
	}
	return true;
}
function subday($ntime,$ctime) {
	$dayst = 3600 * 24;
	$cday = ceil(($ntime-$ctime)/$dayst);
	return $cday;
}
//处理头像
function avatar($uid, $size='40', $returnsrc = FALSE) {
	global $_SCONFIG,$_SC;
	
	$size = in_array($size, $_SCONFIG['avatarname']) ? $size : '40';
	$avatarfile = avatar_file($uid, $size);
	return $returnsrc ? $_SC['attachurl'].$avatarfile : '<img src="'.$_SC['attachurl'].$avatarfile.'" onerror="this.onerror=null;this.src=\'/image/avatar.gif\'">';
}

//得到头像
function avatar_file($uid, $size) {
	global $_SGLOBAL,$group;

	$var = "avatarfile_{$uid}_{$size}";
	if(empty($_SGLOBAL[$var])) {
		$_SGLOBAL[$var] = "{$group[group_id]}/people/{$uid}/avatar.{$size}.jpg";
	}
	return $_SGLOBAL[$var];
}
function dreferer($default = '') {
	$indexname = 'group.php?do=home';
	$referer='';

	$default = empty($default) ? $indexname : '';
	if(empty($referer) && isset($_SERVER['HTTP_REFERER'])) {
		$referer = preg_replace("/([\?&])((sid\=[a-z0-9]{6})(&|$))/i", '\\1', $_SERVER['HTTP_REFERER']);
		$referer = substr($referer, -1) == '?' ? substr($referer, 0, -1) : $referer;
	} else {
		$referer = shtmlspecialchars($referer);
	}
	return $referer;
}
function get_project_status() {
	global $manageproject;
	if($manageproject['status'] == 0) {
		return 'active';
	} elseif($manageproject['status'] == 1) {
		return 'archived';
	}
	return 'active';
}
function is_date($ymd, $sep='-') {
	if(empty($ymd)) return false;
	list($year, $month, $day) = explode($sep, $ymd);
	return checkdate($month, $day, $year);
}
function is_time($his, $sep=':') {
	if(empty($his)) return false;
	list($hour, $minute, $second) = explode($sep, $his);
	return (intval($hour)<=24) && (intval($minute)<=59) && (intval($second)<=59);
}
//-----------------------------------------------------------------------------------     
// 函数名：is_phone($c_telephone)     
// 作 用：判断是否为合法电话号码     
// 参 数：$C_telephone（待检测的电话号码）     
// 返回值：布尔值     
// 备 注：无     
//-----------------------------------------------------------------------------------     
function is_phone($c_telephone) {   
	if (!ereg("^[+]?[0-9]+([xX-][0-9]+)*$", $c_telephone)) return false;     
	return true;     
}
?>
