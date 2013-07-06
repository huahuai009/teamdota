<?php
@define('IN_TEAMDOTA', TRUE);
define('S_ROOT', dirname(__FILE__).DIRECTORY_SEPARATOR);
require_once S_ROOT.'./config.php';
require_once S_ROOT.'./source/class_mysql.php';
require_once S_ROOT.'./source/class_httpsqs.php';
require_once S_ROOT.'./source/function_common.php';

//防止超时
set_time_limit(0);

$httpsqs = new httpsqs;
while(true) {
	//时间
	$mtime = explode(' ', microtime());
	$_SC['timestamp'] = $mtime[1];
	
	$result = $httpsqs->gets($_SC['httpsqs']['server'], $_SC['httpsqs']['port'], $_SC['httpsqs']['charset'], $_SC['httpsqs']['datakey']);
	$pos = $result["pos"];   //当前队列消息的读取位置点
	$data = $result["data"]; //当前队列消息的内容
	if($data != "HTTPSQS_GET_END" && $data != "HTTPSQS_ERROR") {//应用操作BEGIN
		$senddata = json_decode(urldecode($data),true);//数据
		$email = $senddata['email'];
		$subject = $senddata['subject'];
		$message = $senddata['message'];
		$from = isset($senddata['from']) ? $senddata['from'] : '';
		
		$method = isset($senddata['method']) ? $senddata['method'] : '';
		if($method == 'db') {
			$objectid = isset($senddata['objectid']) ? intval($senddata['objectid']) : 0;
			if(!empty($objectid)) {
				$_SC['db'] = new dbstuff;
				$_SC['db']->charset = $_SC['dbcharset'];
				$_SC['db']->connect($_SC['dbhost'], $_SC['dbuser'], $_SC['dbpw'], $_SC['dbname'], $_SC['pconnect']);
				
				$query = $_SC['db']->query("SELECT * FROM ".tname('mailqueue')." WHERE qid='{$objectid}' LIMIT 1");
				if($mailqueue = $_SC['db']->fetch_array($query)) {
					$message = $mailqueue['message'];
				}
				$_SC['db']->close();
			}
		}
		
		if(sendmail($email, $subject, $message, $from)) {
			runlog('sendmail', "$email sendmail success.");
		} else {
			runlog('sendmail_error', "$email sendmail failed.");
		}
	}
	else {
		sleep(10); //暂停1秒钟后，再次循环
	}
}
?>