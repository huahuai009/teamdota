<?php
/*
	[TEAMDOTA] (C) 2012 wanghui
	$Id: lang_showmessage.php 2012-03-31 09:59Z duty $
*/

if(!defined('IN_TEAMDOTA')) {
	exit('Access Denied');
}

$_SGLOBAL['msglang'] = array(

	'box_title' => '消息',
	'no_privilege' => '您目前没有权限进行此操作',
	'no_privilege_manage_group_members' =>'您没有权限管理该成员',
	'links_does_not_exist' => '<strong>网址错误</strong><br/>您可能输入错误的网址，请您检查下拼写，大小写等是否完全正确。',
	//index.php
	'enter_the_index' => '进入个人空间页面',
	//cp_task
	'submit_invalid' => '您的请求来路不正确或表单验证串不符，无法提交。请尝试使用标准的web浏览器进行操作。',
	'to_login' => '您需要先登录才能继续本操作',
	
	//source/function_common.php
	'site_temporarily_closed' => '站点暂时关闭',
	'ip_is_not_allowed_to_visit' => '不能访问，您当前的IP不在站点允许访问的范围内。',
	'group_not_allowed_to_visit' => '您没有做错任何事，我们可能已经移动了该页面，您试试别的地方。',
	'group_has_been_locked' => '空间已被锁定无法访问，如有疑问请您联系我们',
	'group_has_been_freetrial' => '空间的使用时间已过，如有疑问请您联系我们',
	'project_not_allowed_to_visit' => '您没有做错任何事，我们可能已经移动了该页面，您试试别的地方。',
	'project_not_allowed_to_create' => '您公司或组织的项目数已达上限。',

	//source/do_register.php
	'registered' => '注册成功',
	'not_open_registration' => '非常抱歉，本站目前暂时不开放注册',
	'password_format_is_wrong' => '请输入您的登录密码',
	'password_inconsistency' => '两次输入的密码不一致',
	'profile_passwd_illegal' => '密码空或包含非法字符，请重新填写。',
	'fullname_format_is_wrong' => '请输入您的姓名',
	'user_name_is_not_legitimate' => '用户名不合法',
	'user_name_already_exists' => '用户名已经存在',
	'include_not_registered_words' => '用户名包含不允许注册的词语',
	'email_not_registered' => '填写的电子邮箱不允许注册',
	'email_format_is_wrong' => '填写的电子邮箱格式有误',
	'email_has_been_registered' => '填写的电子邮箱已经被注册',
	'register_error' => '注册失败',
	
	//source/do_login.php
	'login_success' => '登录成功 \\1',
	'users_were_not_empty_please_re_login' => '请输入您的用户名',
	'password_were_not_empty_please_re_login' => '请输入您的登录密码',
	'login_failure_please_re_login' => '用户名或密码错误，请您重新输入',
	
	//source/cp_common.php
	'security_exit' => '你已经安全退出了',
	'project_name_error' => '项目名称不能少于1个字符',
	'discussion_subject_error' => '标题不能少于2个字符',
	'document_name_error' => '文档题不能少于2个字符',
	'post_message_error' => '评论内容不能少于2个字符',
	'todos_subject_error' => '标题不能少于1个字符',
	'todoslist_subject_error' => '标题不能少于1个字符',
	'do_success' => '进行的操作完成了',
	'failed_to_delete_operation' => '删除失败，请检查操作',
	'failed_to_restored_operation' => '恢复失败，请检查操作',
	'failed_to_operation' => '系统繁忙，请您稍后再试',
	//source/group_discussion.php
	'discussion_does_not_exist' => '指定的主题不存在',
	//source/group_document.php
	'document_does_not_exist' => '指定的文档不存在',
	//source/group_attachment.php
	'attachment_does_not_exist' => '指定的附件不存在',
	'attachment_referer_invalid' => '对不起，请不要从外部链接下载本站点的附件。',
	'attachment_nonexistence' => '附件文件不存在或无法读入，请与我们联系。',
	//source/group_todoslist_ajax.php
	'todoslist_does_not_exist' => '指定的待办事宜清单不存在',
	//source/group_todos_ajax.php
	'todos_does_not_exist' => '指定的待办事宜不存在',
	
	//source/cp_invite.php
	'send_result_1' => '<br/><br/>邀请邮件已经送出，您的好友可能需要几分钟后才能收到邮件，想要<a href="/help/people.html#inviting" style="color:#52b149;" target="_blank">重新发送邀请邮件给未激活的成员</a>?',
	'send_result_2' => '<br/><br/><strong>以下邮件发送失败:</strong>\\1',
	'send_result_3' => '未找到相应的邀请记录, 邮件重发失败.',
	
	//invite.php
	'invite_code_error' => '对不起，这个邀请已失效或链接不正确，
如果您已经设置了帐户，您可以<a href="do.php?ac=login">直接登录</a>。如果没有，请咨询邀请您的人，并要求他们再次发送邀请，对此带来的不便我们很抱歉。',

	//source/do_lostpasswd.php
	'getpasswd_email_notmatch' => '输入的电子邮箱不存在，请重新确认。',
	'getpasswd_send_succeed' => '重置密码的信息已经通过电子邮箱发送到您的信箱中，<br />请在 3 天之内修改您的密码。',
	'getpasswd_illegal' => '您所用的重置密码链接不可用或已经过期，无法取回密码。',
	'getpasswd_succeed' => '您的密码已重新设置，请使用新密码登录。',
	'getpasswd_send_error' => '系统繁忙，请您稍候再试。',
	
	//source/cp_group.php
	'group_format_is_wrong' => '请输入帐户名称',
	
	//source/cp_plans.php
	'plans_plan_id_format_is_wrong' => '请您选择相应的套餐',
	'plans_alipay_orderid_format_is_wrong' => '请您输入支付宝交易号',
	'plans_alipay_orderid_already_exists' => '该支付宝交易号已存在',
	'plans_alipay_username_format_is_wrong' => '请您输入支付宝账户名',
);

?>