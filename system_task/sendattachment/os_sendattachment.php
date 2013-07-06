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
	
	$result = $httpsqs->gets($_SC['httpsqs']['server'], $_SC['httpsqs']['port'], $_SC['httpsqs']['charset'], $_SC['httpsqs']['datakey']['sendattachment']);
	$pos = $result["pos"];   //当前队列消息的读取位置点
	$data = $result["data"]; //当前队列消息的内容
	if($data != "HTTPSQS_GET_END" && $data != "HTTPSQS_ERROR") {//应用操作BEGIN
		$senddata = json_decode(urldecode($data),true);//数据
		$group_id = intval($senddata['group_id']);
		$project_id = intval($senddata['project_id']);
		$file_id = intval($senddata['file_id']);
		if(!empty($project_id) && !empty($file_id)) {
			
			$_SC['db'] = new dbstuff;
			$_SC['db']->charset = $_SC['dbcharset'];
			$_SC['db']->connect($_SC['dbhost'], $_SC['dbuser'], $_SC['dbpw'], $_SC['dbname'], $_SC['pconnect']);
			
			$query = $_SC['db']->query("SELECT name,group_id FROM ".tname('project')." WHERE `project_id`='{$project_id}' LIMIT 1");
			if($project = $_SC['db']->fetch_array($query)) {//查找项目
				if($project['group_id'] == $group_id){
					$query_file = $_SC['db']->query("SELECT isimage,file_id,filename,uid,author FROM ".tname('file')." WHERE file_id='{$file_id}' LIMIT 1");
					if($file = $_SC['db']->fetch_array($query_file)) {//查找主题
						$sendauthor = '';//作者
						$senduser = '';//发送邮件的用户列表
						$filedata = '';//附件数据
						$mails = array();//邮件发送列表
						$mailvar = array();//邮件发送数据
						
						$sendauthor = $file['author'];
						$senduser = $file['author'];
						$query_notice = $_SC['db']->query("SELECT uids FROM ".tname('notice_attachment')." WHERE file_id='{$file_id}' LIMIT 1");
						if($notice = $_SC['db']->fetch_array($query_notice)) {//查找通知
							$uids = $notice['uids'];
							$query_mem = $_SC['db']->query("SELECT username,uid,email,fullname FROM ".tname('member')." WHERE uid IN({$uids})");
							while ($mem = $_SC['db']->fetch_array($query_mem)) {//获取要发送的邮箱
								if($mem['uid'] != $file['uid']) {
									$mails[] = array('uid'=>$mem['uid'],'email'=>$mem['email']);
									$senduser .= ' , '.emailreplace($mem['fullname']);
								}
							}
						}
						$file_url = "{$_SC[siteurl]}group.php?project_id={$project_id}&do=attachment&file_id={$file_id}";//附件链接
						if($file['isimage']) {
							$code = "file_id={$file[file_id]}&time={$_SC[timestamp]}&code=".md5("file_id={$file[file_id]}&time={$_SC[timestamp]}{$_SC['sitekey']}");
							$filedata .= '<tr>
										<td class="image" style="text-align: left; vertical-align: top; font-size: 12px; line-height: 19px; font-family: \'宋体\', helvetica, sans-serif; color: #000000; background-color: #ffffff; padding: 8px 0;" align="left" bgcolor="#ffffff" valign="top">
											<a href="'.$file_url.'"><img alt="'.$file['filename'].'" src="'.$_SC['siteurl'].'mail_image_preview.php?'.$code.'" style="max-width: 100%; padding: 1px; border: 1px solid #ccc;"></a>
											<br>
											<a href="'.$file_url.'" class="caption grey" style="color: grey; font-size: 12px; text-decoration: none;">'.$file['filename'].'</a>
										</td>
									</tr>';
						}else {
							$filedata .= '<tr>
										<td class="file_icon" style="text-align: left; vertical-align: middle; font-size: 12px; line-height: 19px; font-family: \'宋体\', helvetica, sans-serif; color: #000000; width: 40px; background-color: #ffffff; padding: 0 0 8px;" align="left" bgcolor="#ffffff" valign="middle">
											<a href="'.$file_url.'"><img alt="'.$file['filename'].'" src="'.$_SC['siteurl'].file_icon_big($file['type']).'" style="padding: 0; border: 0;"></a>
										</td>

										<td class="file_name" style="text-align: left; vertical-align: middle; font-size: 12px; line-height: 19px; font-family: \'宋体\', helvetica, sans-serif; color: #000000; background-color: #ffffff; padding: 0 0 8px;" align="left" bgcolor="#ffffff" valign="middle">
											<a href="'.$file_url.'">'.$file['filename'].'</a>
										</td>
									</tr>';
						}
						
						$mailvar[0] = $sendauthor;//作者
						$mailvar[1] = $project['name'];//项目名字
						$mailvar[2] = $file['filename'];//标题
						$mailvar[3] = '';//内容
						$mailvar[4] = $filedata;//附件内容
						$mailvar[5] = $file_url;
						$mailvar[6] = $senduser;//一共发送邮件的用户
						$mailvar[7] = '<img class="avatar" height="42" src="'.$_SC['siteurl'].'image/avatar.gif" style="width: 52px; height: 52px; -webkit-border-radius: 100px; -moz-border-radius: 100px; border-radius: 100px; padding: 0; border: 0 solid #cccccc;" title="'.$sendauthor.'" width="42" onerror="this.onerror=null;this.src=\''.$_SC['siteurl'].'image/avatar.gif\'">';
						
						foreach($mails as $key => $mail) {
							$mailvar[8] = "{$_SC[siteurl]}subscriptions.php?do=attachment&d={$file_id}&u={$mail[uid]}&time={$_SC[timestamp]}&code=".md5("d={$file_id}&u={$mail[uid]}&time={$_SC[timestamp]}{$_SC['sitekey']}");//退订链接
							$maildata = lang_replace($_SC['attachment_data_template'], $mailvar);
							smail($mail['email'], "[{$project[name]}]{$file[filename]}", $maildata, encode_emailfrom($sendauthor));
						}
					}
				}
			}
			$_SC['db']->close();
			runlog('sendattachment', "group_id-{$group_id},project_id-{$project_id},file_id-{$file_id} sendattachment success.");
		}
	}
	else {
		sleep(10); //暂停1秒钟后，再次循环
	}
}
?>