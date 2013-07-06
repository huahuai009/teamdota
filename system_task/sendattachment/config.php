<?php
/*
	[TEAMDOTA] (C) 2012 wanghui
	$Id: config.php 2012-03-31 09:59Z duty $
*/

//配置参数
$_SC = array();
$_SC['dbhost']  		= '127.0.0.1'; //服务器地址
$_SC['dbuser']  		= 'teamdota_www'; //用户
$_SC['dbpw'] 	 		= '63uzew8_h6l_4uah'; //密码
$_SC['dbcharset'] 		= 'utf8'; //字符集
$_SC['pconnect'] 		= 0; //是否持续连接
$_SC['dbname']  		= 'teamdota_www'; //数据库
$_SC['tablepre'] 		= 'e_'; //表名前缀

$_SC['charset'] 		= 'utf-8'; //页面字符集
$_SC['sitename'] 		= 'TeamDota'; //站点名称
$_SC['timeoffset'] 	= '8'; //时区

$_SC['siteurl']			= 'http://www.teamdota.com/'; //站点的访问URL地址
$_SC['sitekey']			= '7bab576rlR7El700'; //站点密钥

$_SC['mail']=Array
	(
	'from' => 'notifications@teamdota.com'
	);

//httpsqs队列配置
$_SC['httpsqs']=Array
	(
	'server' => '127.0.0.1',
	'port' => 1218,
	'charset' => 'utf-8',
	'datakey' => array('sendmail'=>'teamdota_sendmail_data','senddiscussion'=>'teamdota_senddiscussion_data','sendattachment'=>'teamdota_sendattachment_data')
	);

//发表附件时发送邮件的模板
$_SC['attachment_data_template'] = '<table style="background-color: #ffffff; width: 100%; text-align: left; border-collapse: collapse; border-spacing: 0; -premailer-cellspacing: 0; -premailer-cellpadding: 0; -premailer-width: 100%; margin: 0; padding: 0; border: 0;" bgcolor="#ffffff" width="100%" cellpadding="0" cellspacing="0">
		<tr>
			<td style="background-color: #ffffff; text-align: left; vertical-align: top; font-size: 12px; line-height: 19px; font-family: \'宋体\', helvetica, sans-serif; color: #000000;" align="left" bgcolor="#ffffff" valign="top">
				<p class="brown project_meta" style="font-size: 12px; line-height: 19px; font-family: \'宋体\', helvetica, sans-serif; color: brown; margin: 10px 0;">
				  <b>\\1 上传了附件:</b>
				</p>

				<p class="small grey project_meta" style="font-size: 12px; line-height: 16px; font-family: \'宋体\', helvetica, sans-serif; color: grey; margin: 10px 0;">
				  项目：\\2
				</p>

				<div class="main_content" style="border-top-width: 1px; border-top-color: #cccccc; border-top-style: solid; border-bottom-width: 1px; border-bottom-style: solid; border-bottom-color: #cccccc; background-color: #ffffff; width: 100%; margin: 19px 0; padding: 15px 0;">
					<table style="background-color: #ffffff; width: 100%; text-align: left; border-collapse: collapse; border-spacing: 0; -premailer-cellspacing: 0; -premailer-cellpadding: 0; -premailer-width: 100%; margin: 0; padding: 0; border: 0;" bgcolor="#ffffff" width="100%" cellpadding="0" cellspacing="0">
						<tr>
							<td class="avatar" style="text-align: left; vertical-align: top; font-size: 12px; line-height: 19px; font-family: \'宋体\', helvetica, sans-serif; color: #000000; width: 66px; background-color: #ffffff;" align="left" bgcolor="#ffffff" valign="top">
								\\8
							</td>
							<td style="background-color: #ffffff; text-align: left; vertical-align: top; font-size: 12px; line-height: 19px; font-family: \'宋体\', helvetica, sans-serif; color: #000000;" align="left" bgcolor="#ffffff" valign="top">
								<h3 style="font-weight: normal; font-family: \'宋体\', helvetica, sans-serif; font-size: 14px; line-height: 19px; margin: 6px 0 9px;"><b>\\3</b></h3>

								<div class="formatted_content" style="max-width: none !important; margin: 0; padding: 0;">
									\\4
								</div>

								<div class="attachments images" style="margin: 19px 0;">
									<table style="background-color: #ffffff; width: 100%; text-align: left; border-collapse: collapse; border-spacing: 0; -premailer-cellspacing: 0; -premailer-cellpadding: 0; -premailer-width: 100%; margin: 0; padding: 0; border: 0;" bgcolor="#ffffff" width="100%" cellpadding="0" cellspacing="0">
										\\5
									</table>
								</div>
								
								<p class="view_on_basecamp" style="font-size: 12px; line-height: 19px; font-family: \'宋体\', helvetica, sans-serif; color: #000000; margin: 19px 0 9px;">
									<a href="\\6">点击浏览该主题</a>
								</p>
							</td>
						</tr>
					</table>
				</div>
			</td>
		</tr>
		<tr>
			<td style="background-color: #ffffff; text-align: left; vertical-align: top; font-size: 14px; line-height: 19px; font-family: \'宋体\', helvetica, sans-serif; color: #000000;" align="left" bgcolor="#ffffff" valign="top">
				<p class="small grey footer" style="font-size: 12px; line-height: 16px; font-family: \'宋体\', helvetica, sans-serif; color: grey; margin: 0 0 8px;">
				  此电子邮件发送到：\\7。
				</p>
			</td>
		</tr>
		<tr>
			<td style="background-color: #ffffff; text-align: left; vertical-align: top; font-size: 14px; line-height: 19px; font-family: \'宋体\', helvetica, sans-serif; color: #000000;" align="left" bgcolor="#ffffff" valign="top">
				<p class="message small grey footer" style="font-size: 12px; line-height: 16px; font-family: \'宋体\', helvetica, sans-serif; color: grey; margin: 0 0 8px;">
				   当有关此主题发布评论时， <a href="\\9" style="color: grey;">停止接收电子邮件</a> 。
				</p>
			</td>
		</tr>
	</table>';
?>