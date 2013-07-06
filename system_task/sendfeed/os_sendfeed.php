<?php
//每天凌晨00:10执行
@define('IN_TEAMDOTA', TRUE);
define('S_ROOT', dirname(__FILE__).DIRECTORY_SEPARATOR);
require_once S_ROOT.'./config.php';
require_once S_ROOT.'./source/class_mysql.php';
require_once S_ROOT.'./source/function_common.php';

//防止超时
set_time_limit(0);

while(true) {
	//时间
	$mtime = explode(' ', microtime());
	$_SC['timestamp'] = $mtime[1];
	$_SC['supe_starttime'] = $_SC['timestamp'] + $mtime[0];
	
	$sqlstarttime = $sqlendttime = 0;
	$sqlstarttime = number_format(($mtime[1] + $mtime[0] - $_SC['supe_starttime']), 6) * 1000;
	
	
	$lockfile = S_ROOT.'./data/sendfeed.lock';
	@$filemtime = filemtime($lockfile);

	if($_SC['timestamp'] - $filemtime > $_SC['deletewaittime']) {
		touch($lockfile,sgmmktime(sgmdate('Y-m-d', $_SC['timestamp']).' 00:10:00'));
		$_SC['db'] = new dbstuff;
		$_SC['db']->charset = $_SC['dbcharset'];
		$_SC['db']->connect($_SC['dbhost'], $_SC['dbuser'], $_SC['dbpw'], $_SC['dbname'], $_SC['pconnect']);
		
		//查找需要发送邮件的项目
		$yesterday = addday($_SC['timestamp'],-1);
		$send_begin_time = sgmmktime(sgmdate('Y-m-d',$yesterday).' 00:00:00');
		$send_end_time = sgmmktime(sgmdate('Y-m-d',$yesterday).' 23:59:59');

		$query_notification = $_SC['db']->query("SELECT distinct(project_id) FROM ".tname('notification')." WHERE created_time >= {$send_begin_time} AND created_time <= {$send_end_time} AND (icon_op='create' OR icon_op='update') ORDER BY created_time ASC");
		$k = 0;
		while($value_project = $_SC['db']->fetch_array($query_notification)) {
			++$k;
			$project_id = $value_project['project_id'];
			$query_project = $_SC['db']->query("SELECT group_id,project_id,name FROM ".tname('project')." WHERE `project_id`='{$project_id}' LIMIT 1");
			if($project = $_SC['db']->fetch_array($query_project)) {
				$arr_member = array();//用户
				$arr_discussion = array();//主题
				$arr_document = array();//文档
				$arr_attachment = array();//附件
				
				$query_feed = $_SC['db']->query("SELECT * FROM ".tname('notification')." WHERE created_time >= {$send_begin_time} AND created_time <= {$send_end_time} AND  project_id='{$project_id}' AND (icon_op='create' OR icon_op='update') ORDER BY created_time ASC");
				while($row = $_SC['db']->fetch_array($query_feed)) {
					$row['title_text'] = unserialize($row['title_text']);
					//用户
					$arr_member[$row['sender_id']] = $row['sender_author'];
					
					//主题
					if($row['object_type'] == 'discussionid') {
						$arr_discussion[$row['object_id']]['title'] = $row['title_text']['subject'];
						if($row['icon_url'] == 'discussion') {
							$arr_discussion[$row['object_id']]['iscreate'] = ($row['icon_op'] == 'create' ? 1 : 2 );
							$arr_discussion[$row['object_id']]['author'] = $row['sender_author'];
						} else {
							$arr_discussion[$row['object_id']]['sender'][$row['sender_id']]['author'] = $row['sender_author'];
							$arr_discussion[$row['object_id']]['sender'][$row['sender_id']]['number_comment'] = $arr_discussion[$row['object_id']]['sender'][$row['sender_id']]['number_comment'] + 1;
						}
						$arr_discussion[$row['object_id']]['attachment'] = array();
						//获取附件
						$query_file = $_SC['db']->query("SELECT isimage,file_id,filename FROM ".tname('file')." WHERE discussion_id='{$row['object_id']}'  AND project_id='{$project_id}' AND logtime >= {$send_begin_time} AND logtime <= {$send_end_time} ORDER BY logtime ASC");
						while ($file = $_SC['db']->fetch_array($query_file)) {
							$arr_discussion[$row['object_id']]['attachment'][] = $file;
						}
					}
					
					//文档
					if($row['object_type'] == 'documentid') {
						$arr_document[$row['object_id']]['title'] = $row['title_text']['subject'];
						if($row['icon_url'] == 'document') {
							$arr_document[$row['object_id']]['iscreate'] = ($row['icon_op'] == 'create' ? 1 : 2 );
							$arr_document[$row['object_id']]['author'] = $row['sender_author'];
						} else {
							$arr_document[$row['object_id']]['sender'][$row['sender_id']]['author'] = $row['sender_author'];
							$arr_document[$row['object_id']]['sender'][$row['sender_id']]['number_comment'] = $arr_document[$row['object_id']]['sender'][$row['sender_id']]['number_comment'] + 1;
						}
						$arr_document[$row['object_id']]['attachment'] = array();
						//获取附件
						$query_document = $_SC['db']->query("SELECT * FROM ".tname('document')." WHERE document_id='{$row['object_id']}' LIMIT 1");
						if($document = $_SC['db']->fetch_array($query_document)) {
							if(!empty($document['discussion_id'])) {
								$query_file = $_SC['db']->query("SELECT isimage,file_id,filename FROM ".tname('file')." WHERE discussion_id='{$document['discussion_id']}'  AND project_id='{$project_id}' AND logtime >= {$send_begin_time} AND logtime <= {$send_end_time} ORDER BY logtime ASC");
								while ($file = $_SC['db']->fetch_array($query_file)) {
									$arr_document[$row['object_id']]['attachment'][] = $file;
								}
							}
						}
					}
					
					//附件
					if($row['object_type'] == 'attachmentid') {
						$arr_attachment[$row['object_id']]['title'] = $row['title_text']['subject'];
						if($row['icon_url'] == 'attachment') {
							$arr_attachment[$row['object_id']]['iscreate'] = 1;
							$arr_attachment[$row['object_id']]['author'] = $row['sender_author'];
						} else {
							$arr_attachment[$row['object_id']]['sender'][$row['sender_id']]['author'] = $row['sender_author'];
							$arr_attachment[$row['object_id']]['sender'][$row['sender_id']]['number_comment'] = $arr_attachment[$row['object_id']]['sender'][$row['sender_id']]['number_comment'] + 1;
						}
					}
				}
				$maildata = '';
				include template('feed');
				$maildata = ob_get_contents();
				obclean();
				//查找项目的成员，然后进行邮件发送
				$query_pm = $_SC['db']->query("SELECT m.email,m.uid,pm.project_id FROM ".tname('project_member')." pm,".tname('member')." m WHERE pm.uid=m.uid AND pm.project_id ='{$project_id}' AND m.isactive=0 AND m.issubscribe=0");
				while($value = $_SC['db']->fetch_array($query_pm)) {
					$subscriptions_url = "{$_SC[siteurl]}subscriptions.php?do=dialy&d={$value[uid]}&u={$value[uid]}&time={$_SC[timestamp]}&code=".md5("d={$value[uid]}&u={$value[uid]}&time={$_SC[timestamp]}{$_SC['sitekey']}");//退订链接
					
					smail($value['email'], '每日更新('.$project['name'].'-'.sgmdate('Y年m月d日', $yesterday).')', str_replace('{#subscriptions_url}', $subscriptions_url, $maildata), encode_emailfrom());
				}
				$mtime = explode(' ', microtime());
				$sqlendttime = number_format(($mtime[1] + $mtime[0] - $_SC['supe_starttime']), 6) * 1000;
				$sqltime = round(($sqlendttime - $sqlstarttime), 3);
				runlog('sendfeed_project', "sqltime-{$sqltime},time-{$_SC['timestamp']},project_id-{$project_id} sendfeed_project success.");
			}
		}
		$_SC['db']->close();
		$mtime = explode(' ', microtime());
		$sqlendttime = number_format(($mtime[1] + $mtime[0] - $_SC['supe_starttime']), 6) * 1000;
		$sqltime = round(($sqlendttime - $sqlstarttime), 3);
		runlog('sendfeed', "sqltime-{$sqltime},time-{$_SC['timestamp']},projectnumber-{$k} sendfeed success.");
	}
	sleep(10); //暂停10秒钟后，再次循环
}
?>