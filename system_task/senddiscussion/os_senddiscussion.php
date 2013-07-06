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
	
	$result = $httpsqs->gets($_SC['httpsqs']['server'], $_SC['httpsqs']['port'], $_SC['httpsqs']['charset'], $_SC['httpsqs']['datakey']['senddiscussion']);
	$pos = $result["pos"];   //当前队列消息的读取位置点
	$data = $result["data"]; //当前队列消息的内容
	if($data != "HTTPSQS_GET_END" && $data != "HTTPSQS_ERROR") {//应用操作BEGIN
		$senddata = json_decode(urldecode($data),true);//数据
		$group_id = intval($senddata['group_id']);
		$project_id = intval($senddata['project_id']);
		$discussion_id = intval($senddata['discussion_id']);
		$post_id = intval($senddata['post_id']);
		if(!empty($project_id) && !empty($discussion_id)) {
			
			$_SC['db'] = new dbstuff;
			$_SC['db']->charset = $_SC['dbcharset'];
			$_SC['db']->connect($_SC['dbhost'], $_SC['dbuser'], $_SC['dbpw'], $_SC['dbname'], $_SC['pconnect']);
			
			$query = $_SC['db']->query("SELECT name,group_id FROM ".tname('project')." WHERE `project_id`='{$project_id}' LIMIT 1");
			if($project = $_SC['db']->fetch_array($query)) {//查找项目
				if($project['group_id'] == $group_id){
					$query_discussion = $_SC['db']->query("SELECT discussion_id,subject,message,uid,author FROM ".tname('discussion')." WHERE discussion_id='{$discussion_id}' LIMIT 1");
					if($discussion = $_SC['db']->fetch_array($query_discussion)) {//查找主题
						$sendauthor = '';//作者
						$senduser = '';//发送邮件的用户列表
						$filedata = '';//附件数据
						$mails = array();//邮件发送列表
						$mailvar = array();//邮件发送数据
						
						if(empty($post_id)) {
							$sendauthor = $discussion['author'];
							$senduser = $discussion['author'];
							$query_notice = $_SC['db']->query("SELECT uids FROM ".tname('notice_discussion')." WHERE discussion_id='{$discussion_id}' LIMIT 1");
							if($notice = $_SC['db']->fetch_array($query_notice)) {//查找通知
								$uids = $notice['uids'];
								$query_mem = $_SC['db']->query("SELECT username,uid,email,fullname FROM ".tname('member')." WHERE uid IN({$uids})");
								while ($mem = $_SC['db']->fetch_array($query_mem)) {//获取要发送的邮箱
									if($mem['uid'] != $discussion['uid']) {
										$mails[] = array('uid'=>$mem['uid'],'email'=>$mem['email']);
										$senduser .= ' , '.emailreplace($mem['fullname']);
									}
								}
							}
							//获取主题附件
							$discussion_url = "{$_SC[siteurl]}group.php?project_id={$project_id}&do=discussion&discussion_id={$discussion_id}";//主题链接
							$query_file = $_SC['db']->query("SELECT isimage,file_id,filename FROM ".tname('file')." WHERE discussion_id='{$discussion_id}' AND post_id=0");
							while ($file = $_SC['db']->fetch_array($query_file)) {
								if($file['isimage']) {
									$code = "file_id={$file[file_id]}&time={$_SC[timestamp]}&code=".md5("file_id={$file[file_id]}&time={$_SC[timestamp]}{$_SC['sitekey']}");
									$filedata .= '<tr>
												<td class="image" style="text-align: left; vertical-align: top; font-size: 12px; line-height: 19px; font-family: \'宋体\', helvetica, sans-serif; color: #000000; background-color: #ffffff; padding: 8px 0;" align="left" bgcolor="#ffffff" valign="top">
													<a href="'.$discussion_url.'"><img alt="'.$file['filename'].'" src="'.$_SC['siteurl'].'mail_image_preview.php?'.$code.'" style="max-width: 100%; padding: 1px; border: 1px solid #ccc;"></a>
													<br>
													<a href="'.$discussion_url.'" class="caption grey" style="color: grey; font-size: 12px; text-decoration: none;">'.$file['filename'].'</a>
												</td>
											</tr>';
								}else {
									$filedata .= '<tr>
												<td class="file_icon" style="text-align: left; vertical-align: middle; font-size: 12px; line-height: 19px; font-family: \'宋体\', helvetica, sans-serif; color: #000000; width: 40px; background-color: #ffffff; padding: 0 0 8px;" align="left" bgcolor="#ffffff" valign="middle">
													<a href="'.$discussion_url.'"><img alt="'.$file['filename'].'" src="'.$_SC['siteurl'].file_icon_big($file['type']).'" style="padding: 0; border: 0;"></a>
												</td>

												<td class="file_name" style="text-align: left; vertical-align: middle; font-size: 12px; line-height: 19px; font-family: \'宋体\', helvetica, sans-serif; color: #000000; background-color: #ffffff; padding: 0 0 8px;" align="left" bgcolor="#ffffff" valign="middle">
													<a href="'.$discussion_url.'">'.$file['filename'].'</a>
												</td>
											</tr>';
								}
							}
							
							$mailvar[0] = $sendauthor;//作者
							$mailvar[1] = $project['name'];//项目名字
							$mailvar[2] = $discussion['subject'];//主题标题
							$mailvar[3] = $discussion['message'];//主题内容
							$mailvar[4] = $filedata;//附件内容
							$mailvar[5] = $discussion_url;
							$mailvar[6] = $senduser;//一共发送邮件的用户
							$mailvar[7] = '<img class="avatar" height="42" src="'.$_SC['siteurl'].'image/avatar.gif" style="width: 52px; height: 52px; -webkit-border-radius: 100px; -moz-border-radius: 100px; border-radius: 100px; padding: 0; border: 0 solid #cccccc;" title="'.$sendauthor.'" width="42" onerror="this.onerror=null;this.src=\''.$_SC['siteurl'].'image/avatar.gif\'">';//作者的头像
						} else {
							$query_post = $_SC['db']->query("SELECT discussion_id,message,uid,author,post_id FROM ".tname('post')." WHERE post_id='{$post_id}' LIMIT 1");
							if($post = $_SC['db']->fetch_array($query_post)) {//查找回复
								$sendauthor = $post['author'];
								$senduser = $post['author'];
								$query_notice = $_SC['db']->query("SELECT uids FROM ".tname('notice_discussion')." WHERE discussion_id='{$discussion_id}' LIMIT 1");
								if($notice = $_SC['db']->fetch_array($query_notice)) {//查找通知
									$uids = $notice['uids'];
									$query_mem = $_SC['db']->query("SELECT username,uid,email,fullname FROM ".tname('member')." WHERE uid IN({$uids})");
									while ($mem = $_SC['db']->fetch_array($query_mem)) {//获取要发送的邮箱
										if($mem['uid'] != $post['uid']) {
											$mails[] = array('uid'=>$mem['uid'],'email'=>$mem['email']);
											$senduser .= ' , '.emailreplace($mem['fullname']);
										}
									}
								}
								//获取回复附件
								$discussion_url = "{$_SC[siteurl]}group.php?project_id={$project_id}&do=discussion&discussion_id={$discussion_id}#comment_{$post[post_id]}";//主题链接
								$query_file = $_SC['db']->query("SELECT isimage,file_id,filename FROM ".tname('file')." WHERE post_id='{$post_id}'");
								while ($file = $_SC['db']->fetch_array($query_file)) {
									if($file['isimage']) {
										$code = "file_id={$file[file_id]}&time={$_SC[timestamp]}&code=".md5("file_id={$file[file_id]}&time={$_SC[timestamp]}{$_SC['sitekey']}");
										$filedata .= '<tr>
													<td class="image" style="text-align: left; vertical-align: top; font-size: 12px; line-height: 19px; font-family: \'宋体\', helvetica, sans-serif; color: #000000; background-color: #ffffff; padding: 8px 0;" align="left" bgcolor="#ffffff" valign="top">
														<a href="'.$discussion_url.'"><img alt="'.$file['filename'].'" src="'.$_SC['siteurl'].'mail_image_preview.php?'.$code.'" style="max-width: 100%; padding: 1px; border: 1px solid #ccc;"></a>
														<br>
														<a href="'.$discussion_url.'" class="caption grey" style="color: grey; font-size: 12px; text-decoration: none;">'.$file['filename'].'</a>
													</td>
												</tr>';
									}else {
										$filedata .= '<tr>
													<td class="file_icon" style="text-align: left; vertical-align: middle; font-size: 12px; line-height: 19px; font-family: \'宋体\', helvetica, sans-serif; color: #000000; width: 40px; background-color: #ffffff; padding: 0 0 8px;" align="left" bgcolor="#ffffff" valign="middle">
														<a href="'.$discussion_url.'"><img alt="'.$file['filename'].'" src="'.$_SC['siteurl'].file_icon_big($file['type']).'" style="padding: 0; border: 0;"></a>
													</td>

													<td class="file_name" style="text-align: left; vertical-align: middle; font-size: 12px; line-height: 19px; font-family: \'宋体\', helvetica, sans-serif; color: #000000; background-color: #ffffff; padding: 0 0 8px;" align="left" bgcolor="#ffffff" valign="middle">
														<a href="'.$discussion_url.'">'.$file['filename'].'</a>
													</td>
												</tr>';
									}
								}
							}
							
							$mailvar[0] = $project['name'];//项目名字
							$mailvar[1] = $discussion['subject'];//主题标题
							$mailvar[2] = '<img class="avatar" height="42" src="'.$_SC['siteurl'].'image/avatar.gif" style="width: 52px; height: 52px; -webkit-border-radius: 100px; -moz-border-radius: 100px; border-radius: 100px; padding: 0; border: 0 solid #cccccc;" title="'.$sendauthor.'" width="42" onerror="this.onerror=null;this.src=\''.$_SC['siteurl'].'image/avatar.gif\'">';//作者的头像
							$mailvar[3] = $sendauthor;//作者的名字
							$mailvar[4] = $post['message'];//回复内容
							$mailvar[5] = $filedata;//附件内容
							$mailvar[6] = $discussion_url;//主题链接
							$mailvar[7] = $senduser;//一共发送邮件的用户
							
						}
						
						foreach($mails as $key => $mail) {
							$mailvar[8] = "{$_SC[siteurl]}subscriptions.php?do=discussion&d={$discussion_id}&u={$mail[uid]}&time={$_SC[timestamp]}&code=".md5("d={$discussion_id}&u={$mail[uid]}&time={$_SC[timestamp]}{$_SC['sitekey']}");//退订链接
							$maildata = lang_replace(empty($post_id) ? $_SC['discussion_data_template'] : $_SC['post_data_template'], $mailvar);
							smail($mail['email'], "[{$project[name]}]".(empty($post_id) ? '' : '回复：')."{$discussion[subject]}", $maildata, encode_emailfrom($sendauthor));
						}
					}
				}
			}
			$_SC['db']->close();
			runlog('senddiscussion', "group_id-{$group_id},project_id-{$project_id},discussion_id-{$discussion_id},post_id-{$post_id} senddiscussion success.");
		}
	}
	else {
		sleep(10); //暂停1秒钟后，再次循环
	}
}
?>