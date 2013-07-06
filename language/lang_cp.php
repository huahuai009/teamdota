<?php
/*
	[TEAMDOTA] (C) 2012 wanghui
	$Id: lang_cp.php 2012-03-31 09:59Z duty $
*/

if(!defined('IN_TEAMDOTA')) {
	exit('Access Denied');
}

$_SGLOBAL['cplang'] = array(

	//source/do_login.php
	'email_format_is_wrong' => '填写的电子邮箱格式有误',
	'users_were_not_empty_please_re_login' => '请输入您的用户名',
	'password_were_not_empty_please_re_login' => '请输入您的登录密码',
	'login_failure_please_re_login' => '用户名或密码错误，请您重新输入',
	
	'lack_of_access_to_upload_file_size' => '无法获取上传文件大小',
	'only_allows_upload_file_types' => '只允许上传 \\1 格式的附件',
	'unable_to_create_upload_directory_server' => '服务器无法创建上传目录',
	'inadequate_capacity_space' => '空间容量不足，不能上传新附件',
	'mobile_picture_temporary_failure' => '无法转移临时图片到服务器指定目录',
	'ftp_upload_file_size' => '远程上传图片失败',
	'thumb_create_error' => '缩略图生成失败',
	'system_upload_error' => '系统繁忙，请您稍后再试',
	
	'invite_project_subject' => '\\1',
	'invite_project_says' => '<h4 style="font-weight: normal; font-family: \'宋体\', helvetica, sans-serif; font-size: 12px; line-height: 19px; margin: 19px 0 3px;">
        <b>\\1 说:</b>
		</h4>
		<p style="font-size: 12px; line-height: 19px; font-family: \'宋体\', helvetica, sans-serif; color: #000000; margin: 0 0 19px;">
			\\2
		</p>',
	'invite_group_massage' => '<table style="background-color: #ffffff; width: 100%; text-align: left; border-collapse: collapse; border-spacing: 0; -premailer-cellspacing: 0; -premailer-cellpadding: 0; -premailer-width: 100%; margin: 0; padding: 0; border: 0;" bgcolor="#ffffff" width="100%" cellpadding="0" cellspacing="0">
			<tr>
				<td style="background-color: #ffffff; text-align: left; vertical-align: top; font-size: 12px; line-height: 19px; font-family: \'宋体\', helvetica, sans-serif; color: #000000;" align="left" bgcolor="#ffffff" valign="top">
					<h3 style="font-weight: normal; font-family: \'宋体\', helvetica, sans-serif; font-size: 12px; line-height: 19px; margin: 6px 0 9px;">\\1：</h3>
					\\2
					<p style="font-size: 12px; line-height: 19px; font-family: \'宋体\', helvetica, sans-serif; color: #000000; margin: 0 0 19px;">
					我们正在使用teamdota合作，这是一个网站，我们有讨论，共享文件和撰写文档。
					</p>

					<h2 style="font-weight: normal; font-family: \'宋体\', helvetica, sans-serif; font-size: 12px; line-height: 24px; margin: 6px 0 9px;">
						<b><a href="\\6">点击接受该邀请</a></b>
					</h2>

					\\4

					<h4 style="font-weight: normal; font-family: \'宋体\', helvetica, sans-serif; font-size: 12px; line-height: 19px; margin: 19px 0 3px;">
					<b>有疑问?</b>
					</h4>
					<p style="font-size: 12px; line-height: 19px; font-family: \'宋体\', helvetica, sans-serif; color: #000000; margin: 0 0 19px;">
					联系 \\7<<a href="mailto:\\3">\\3</a>>
					</p>

					<p class="small grey" style="font-size: 12px; line-height: 16px; font-family: \'宋体\', helvetica, sans-serif; color: grey; margin: 0 0 19px;">
						<b>项目成员:</b><br>

						\\5
					</p>
				</td>
			</tr>
		</table>',
	'invite_project_massage' => '<table style="background-color: #ffffff; width: 100%; text-align: left; border-collapse: collapse; border-spacing: 0; -premailer-cellspacing: 0; -premailer-cellpadding: 0; -premailer-width: 100%; margin: 0; padding: 0; border: 0;" bgcolor="#ffffff" width="100%" cellpadding="0" cellspacing="0">
			<tr>
				<td style="background-color: #ffffff; text-align: left; vertical-align: top; font-size: 12px; line-height: 19px; font-family: \'宋体\', helvetica, sans-serif; color: #000000;" align="left" bgcolor="#ffffff" valign="top">
					<h3 style="font-weight: normal; font-family: \'宋体\', helvetica, sans-serif; font-size: 14px; line-height: 19px; margin: 6px 0 9px;">\\1 :</h3>
					\\2

					<h2 style="font-weight: normal; font-family: \'宋体\', helvetica, sans-serif; font-size: 18px; line-height: 24px; margin: 6px 0 9px;">
						<b><a href="\\6">点击访问该项目</a></b>
					</h2>

					\\4

					<h4 style="font-weight: normal; font-family: \'宋体\', helvetica, sans-serif; font-size: 14px; line-height: 19px; margin: 19px 0 3px;">
					<b>有疑问?</b>
					</h4>
					<p style="font-size: 12px; line-height: 19px; font-family: \'宋体\', helvetica, sans-serif; color: #000000; margin: 0 0 19px;">
					联系 \\7<<a href="mailto:\\3">\\3</a>>
					</p>

					<p class="small grey" style="font-size: 12px; line-height: 16px; font-family: \'宋体\', helvetica, sans-serif; color: grey; margin: 0 0 19px;">
						<b>项目成员:</b><br>

						\\5
					</p>
				</td>
			</tr>
		</table>',
	'get_passwd_subject' => '重置您的密码',
	'get_passwd_message' => '<table cellpadding="0" cellspacing="0" border="0" width="98%">
			<tr>
				<td style="padding: 15px; font-family: Helvetica, Arial, sans-serif; font-size: 14px; line-height: 1.3em; text-align: left;">
					<p>Teamdota</p></td>
			</tr>
			<tr>
				<td style="padding: 15px; padding-top: 10px; padding-bottom: 40px; font-family: Helvetica, Arial, sans-serif; font-size: 12px; line-height: 1.3em; text-align: left;" valign="top">
					<h1 style="font-family: Helvetica, Arial, sans-serif; color: #222222; font-size: 14px; line-height: normal; letter-spacing: -1px;">
						重置您在Teamdota的密码
					</h1>
					<p>您好 \\1,</p>
					<p>您的用户名是: <strong><A href="mailto:\\2">\\3</A></strong></p>
					<p><b>点击此链接重置密码:</b><br>
					<a href="\\4">\\5</a></p>
					<hr style="margin-top: 30px; border:none; border-top: 1px solid #ccc;">
					<p style="font-size: 12px; line-height: 1.3em;"><b>没有要求重置密码？</b><br/>如果您没有要求重置密码，可能是其他用户输入您的用户名或电子邮件地址错误而尝试重置其密码，您可以忽略此邮件。</p>
				</td>
			</tr>
		</table>',
		
		//links
		'project_links' => 'group.php?do=project&project_id=\\1',
		'discussion_links' => 'group.php?do=discussion&project_id=\\1&discussion_id=\\2',
		'document_links' => 'group.php?do=document&project_id=\\1&document_id=\\2',
		'attachment_links' => 'group.php?do=attachment&project_id=\\1&file_id=\\2',
		'todos_links' => 'group.php?do=todos&project_id=\\1&todos_id=\\2',
		'todoslist_links' => 'group.php?do=todoslist&project_id=\\1&todoslist_id=\\2',
		
		//notification
		'notification_project_create' => '{actor} 创建了新项目 :{subject}',
		'notification_project_update' => '{actor} 更新了项目 :{subject}',
		'notification_project_delete' => '{actor} 删除了项目 :{subject}',
		'notification_project_member_delete' => '{actor} 删除了成员 {member} ,项目 :{subject}',
		'notification_project_member_create' => '{actor} 添加了成员 {member} ,项目 :{subject}',
		'notification_project_invite' => '{actor} 邀请 {invite} 参与项目 :{subject}',
		'notification_discussion_create' => '{actor} 发起了新主题 :{subject}',
		'notification_discussion_update' => '{actor} 更新了主题 :{subject}',
		'notification_discussion_delete' => '{actor} 删除了主题 :{subject}',
		'notification_discussion_post_create' => '{actor} 评论了主题 :{subject}',
		'notification_discussion_post_delete' => '{actor} 删除了一条评论，主题 :{subject}',
		'notification_document_create' => '{actor} 发表了新文档 :{subject}',
		'notification_document_update' => '{actor} 更新了文档 :{subject}',
		'notification_document_delete' => '{actor} 删除了文档 :{subject}',
		'notification_document_post_create' => '{actor} 评论了文档 :{subject}',
		'notification_document_post_delete' => '{actor} 删除了一条评论，文档 :{subject}',
		'notification_attachment_create' => '{actor} 上传了新附件 :{subject}',
		'notification_attachment_delete' => '{actor} 删除了附件 :{subject}',
		'notification_attachment_post_create' => '{actor} 评论了附件 :{subject}',
		'notification_attachment_post_delete' => '{actor} 删除了一条评论，附件 :{subject}',
		'notification_todos_create' => '{actor} 创建了待办事宜 :{subject}',
		'notification_todos_update' => '{actor} 更新了待办事宜 :{subject}',
		'notification_todos_delete' => '{actor} 删除了待办事宜 :{subject}',
		'notification_todos_post_create' => '{actor} 评论了待办事宜 :{subject}',
		'notification_todos_post_delete' => '{actor} 删除了一条评论，待办事宜 :{subject}',
		'notification_todoslist_create' => '{actor} 创建了待办事宜清单 :{subject}',
		'notification_todoslist_update' => '{actor} 更新了待办事宜清单 :{subject}',
		'notification_todoslist_delete' => '{actor} 删除了待办事宜清单 :{subject}',
		'notification_todoslist_completed' => '{actor} 完成了待办事宜清单 :{subject}',
		'notification_todoslist_nocompleted' => '{actor} 取消完成待办事宜清单 :{subject}',
		'notification_todoslist_post_create' => '{actor} 评论了待办事宜清单 :{subject}',
		'notification_todoslist_post_delete' => '{actor} 删除了一条评论，待办事宜清单 :{subject}',
		
		//trash_can
		'trash_can_project_delete' => '{actor} 删除了项目 :{subject}',
		'restored_project_delete' => '{actor} 恢复了项目 :{subject}',
		'trash_can_discussion_delete' => '{actor} 删除了主题 :{subject}',
		'restored_discussion_delete' => '{actor} 恢复了主题 :{subject}',
		'trash_can_document_delete' => '{actor} 删除了文档 :{subject}',
		'restored_document_delete' => '{actor} 恢复了文档 :{subject}',
		'trash_can_attachment_delete' => '{actor} 删除了附件 :{subject}',
		'restored_attachment_delete' => '{actor} 恢复了附件 :{subject}',
		'trash_can_todos_delete' => '{actor} 删除了待办事宜 :{subject}',
		'restored_todos_delete' => '{actor} 恢复了待办事宜 :{subject}',
		'trash_can_todoslist_delete' => '{actor} 删除了待办事宜清单 :{subject}',
		'restored_todoslist_delete' => '{actor} 恢复了待办事宜清单 :{subject}',
		
		//source/cp_people_new.php
		'send_result_1' => '邀请邮件发送成功',
		'send_result_3' => '未找到相应的邀请记录, 邮件重发失败.',
		
		//待办事宜通知
		'to_do_subject' => '[\\1]待办事宜分配给您: \\2。',
		'to_do_message' => '<table style="background-color: #ffffff; width: 100%; text-align: left; border-collapse: collapse; border-spacing: 0; -premailer-cellspacing: 0; -premailer-cellpadding: 0; -premailer-width: 100%; margin: 0; padding: 0; border: 0;" bgcolor="#ffffff" width="100%" cellpadding="0" cellspacing="0">
			<tr>
				<td style="background-color: #ffffff; text-align: left; vertical-align: top; font-size: 12px; line-height: 19px; font-family: \'宋体\', helvetica, sans-serif; color: #000000;" align="left" bgcolor="#ffffff" valign="top">
					<p class="brown project_meta" style="font-size: 12px; line-height: 19px; font-family: \'宋体\', helvetica, sans-serif; color: brown; margin: 10px 0;">
					  <b>\\1分配给您做的待办事宜：</b>
					</p>
					<p class="small grey project_meta" style="font-size: 12px; line-height: 16px; font-family: \'宋体\', helvetica, sans-serif; color: grey; margin: 10px 0;">
					  项目： \\2<br>
					  待办事宜： \\3
					</p>
					<div class="main_content" style="border-top-width: 1px; border-top-color: #cccccc; border-top-style: solid; border-bottom-width: 1px; border-bottom-style: solid; border-bottom-color: #cccccc; background-color: #ffffff; width: 100%; margin: 19px 0; padding: 15px 0;">
						<table style="background-color: #ffffff; width: 100%; text-align: left; border-collapse: collapse; border-spacing: 0; -premailer-cellspacing: 0; -premailer-cellpadding: 0; -premailer-width: 100%; margin: 0; padding: 0; border: 0;" bgcolor="#ffffff" width="100%" cellpadding="0" cellspacing="0">
							<tr>
								<td class="todo" style="text-align: left; vertical-align: top; font-size: 14px; line-height: 19px; font-family: \'宋体\', helvetica, sans-serif; color: #000000; width: 20px; padding-left: 10px; background-color: #ffffff;" align="left" bgcolor="#ffffff" valign="top">
									<h3 style="font-weight: normal; font-family: \'宋体\', helvetica, sans-serif; font-size: 14px; line-height: 19px; margin: 6px 0 9px;">&#10063;</h3>
								</td>
								<td style="background-color: #ffffff; text-align: left; vertical-align: top; font-size: 14px; line-height: 19px; font-family: \'宋体\', helvetica, sans-serif; color: #000000;" align="left" bgcolor="#ffffff" valign="top">
									<h3 style="font-weight: normal; font-family: \'宋体\', helvetica, sans-serif; font-size: 14px; line-height: 19px; margin: 6px 0 9px;">
										<b>\\4</b>
									</h3>
									<p class="view_on_teamdota" style="font-size: 14px; line-height: 19px; font-family: \'宋体\', helvetica, sans-serif; color: #000000; margin: 19px 0 9px;">
										<a href="\\5">点击访问该待办事宜清单</a>
									</p>
								</td>
							</tr>
						</table>
					</div>
				</td>
			</tr>
			<tr>
				<td style="background-color: #ffffff; text-align: left; vertical-align: top; font-size: 12px; line-height: 19px; font-family: \'宋体\', helvetica, sans-serif; color: #000000;" align="left" bgcolor="#ffffff" valign="top">
					<p class="small grey footer" style="font-size: 12px; line-height: 16px; font-family: \'宋体\', helvetica, sans-serif; color: grey; margin: 0 0 8px;">
				  此电子邮件发送给: \\1 和 \\6。
					</p>
				</td>
			</tr>
		</table>',
);

?>