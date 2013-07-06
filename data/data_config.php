<?php
if(!defined('IN_TEAMDOTA')) exit('Access Denied');
$_SCONFIG=Array
	(
	//系统负载控制参数,当服务器的负载参数超过设定值时，自动开启CC防护模式，建议设置为3
	'db_loadavg' => '3',
	//CC攻击防护,0为关闭CC攻击防护；
	//1为普通模式:建议使用这种模式，可预防站点遭受cc攻击；
	//2为加强模式:当网站正遭受CC攻击而导致访问速度很慢时，请开启此种模式可阻止攻击
	'db_cc' => '0',
	'sitename' => 'Teamdota',
	'template' => 'default',
	'serviceemail' => 'teamdota@126.com',
	'notificationsemail' => 'notifications@teamdota.com',
	'onlinehold' => 1800,
	'timeoffset' => 8,
	'allowftp' => '0',
	'ftpurl' => '',
	'closeregister' => '0',
	'close' => '0',
	'closereason' => '',
	'sitekey' => '7bab576rlR7El700',
	'siteallurl' => 'http://localhost:8001/',
	'debuginfo' => '0',
	'headercharset' => '0',
	'domainroot' => '',
	'feedtargetblank' => 1,
	'freetrial' => 45,
	//帐户过期的过渡时间 10 天
	'freetrial_access' => 864000,
	//头像文件的最大,单位为KB
	'maxavatarsize' => '500',
	'avatarname' => array('big'=>'big','96'=>'96','55'=>'55','40'=>'40'),
	'pictype' => array('jpg','jpeg','gif','png','bmp'),
	'allowfiletype' => array('ai','aiff','bmp','csv','chm','dmg','doc','docx','eps','fla','gif','htm','html','indd','jpg','jpeg','key','link','m4a','m4v','mov','mp3','mpeg','mpg','numbers','odp','ods','odt','pages','pdf','png','pot','ppt','pptx','psd','rar','rm','rtf','sit','swf','tar','tgz','tif','tiff','txt','vsd','wav','web','wma','wmv','xls','xlsx','zip','sql','gz'),
	'allowfiletypeexp' => array('application/postscript','audio/x-aiff','image/bmp','application/msword','image/gif','text/html','image/jpeg','image/pjpeg','video/quicktime','audio/mpeg','video/mpeg','application/pdf','image/png','image/x-png','application/vnd.ms-powerpoint','application/octet-stream','application/rtf','application/x-stuffit','application/x-tar','application/x-compressed','image/tiff','text/plain','audio/wav','application/vnd.ms-excel','application/zip','application/x-zip-compressed','application/x-gzip'),
	'notallowfiletype' => array('php','phtml','php3','php4','jsp','exe','dll','asp','cer','asa','shtml','shtm','aspx','asax','cgi','fcgi','pl'),
	'post_thumb'=> array('maxthumbwidth' => 262,'maxthumbheight' => 262),
	'project_discussion_thumb'=> array('maxthumbwidth' => 25,'maxthumbheight' => 35),
	'project_attachment_thumb'=> array('maxthumbwidth' => 120,'maxthumbheight' => 140),
	//套餐对应的项目数
	'group_gtype' => array('0'=>5,'1'=>10,'2'=>40,'3'=>100,'4'=>0),
	//套餐对应空间大小，已B为单位(256M,3GB,15GB,40GB,100GB)
	'group_attachsize' => array('0'=>268435456,'1'=>3221225472,'2'=>16106127360,'3'=>42949672960,'4'=>107374182400),
	'group_gtype_subject' => array('0'=>'免费用户','1'=>'VIP1','2'=>'VIP2','3'=>'VIP3','4'=>'VIP4'),
	//套餐对应的价钱 /月
	'group_gtype_price' => array('0'=>0,'1'=>30,'2'=>80,'3'=>150,'4'=>250),
	//httpsqs
	'httpsqs' => array('server'=>'127.0.0.1', 'port'=>1218, 'charset'=>'utf-8', 'datakey'=>array('sendmail'=>'teamdota_sendmail_data','senddiscussion'=>'teamdota_senddiscussion_data','sendattachment'=>'teamdota_sendattachment_data')),
	)
?>