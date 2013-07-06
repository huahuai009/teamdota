<?php if(!defined('IN_TEAMDOTA')) exit('Access Denied');?>
<!DOCTYPE html>
<html>
<head>
<title><?=$member['fullname']?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link href="css/application.css" media="all" rel="stylesheet" type="text/css">
<link rel="shortcut icon" href="http://www.teamdota.com/favicon.ico" />
<meta charset="utf-8">
<meta name="viewport" content="width=1100">
<style type="text/css">div.panel { min-height: 727px };</style>
<script type="text/javascript">
    window.bbcx = {currentGroup: "<?=$group['group_id']?>",currentPerson: "<?=$_SGLOBAL['supe_uid']?>",currentProject: "<?=$project_id?>",currentDo: "<?=$do?>"};
</script>
<script src="js/jquery.js" type="text/javascript"></script>
<script src="js/global.js" type="text/javascript"></script>
</head>
<body class="windows topnav_root">
<?php $position_hotkey = 3 ; include_once template("head"); ?>
<div id="ajax_show_project" style="display:none;"></div>
<div id="workspace_project" class="workspace">
	<div style="width: 980px;" class="container stack_container">
		<div class="panel sheet_view inactive person overview">
			<title><?=$member['fullname']?></title>
			<header class="person">
				<figure class="full_width" data-behavior="enlargeable">
					<img class="avatar" id="avatar_person_<?=$member['uid']?>" src="<? echo avatar($member['uid'],'96',true);?>" title="<?=$member['fullname']?>" onerror="this.onerror=null;this.src='/image/avatar.gif'" />
				</figure>
				<?php if($_SGLOBAL['supe_uid']==$member['uid']) { ?>
				<div class="header_links">
					<a href="cp.php?ac=people&op=edit">
						<span class="link">修改个人信息</span><br>
						<span class="detail">姓名, 密码…</span>
					</a>
					<a href="cp.php?ac=people_settings">
						<span class="link">订阅设置</span><br>
						<span class="detail">通知, 订阅…</span>
					</a>
				</div>
				<?php } elseif($_SGLOBAL['member']['ntype'] > 0) { ?>
				 <a href="cp.php?ac=people_view_permissions&uid=<?=$uid?>" class="project_access" data-role="project_access">用户权限管理</a>
				<?php } ?>
				<h2><?=$member['fullname']?></h2>
				<h3><a href="mailto:<?=$member['email']?>"><?=$member['email']?></a></h3>
				<?php if($member['isactive'] == 0) {?>
				<p class="last_active">
					<span>最后活动 <time data-time-ago="" datetime="<?php echo sgmdate('Y-m-d H:i:s', $member['lastactivity']);?>"><?php echo sgmdate('n-j', $member['lastactivity'],1);?></time></span>
				</p>
				<?php } else {?>
				<p class="last_active">
					<span>由<?=$invite['author']?>邀请的成员</span>
				</p>
				<div class="resend_invite">
					<h5><?=$member['fullname']?> 还没有激活TeamDota账户</h5>
					<p><span data-role="invite_status">最后一次发送邀请的时间为 <time data-time-ago="" datetime="<?php echo sgmdate('Y-m-d H:i:s', $invite['sendtime']);?>"><?php echo sgmdate('Y-m-d H:i', $invite['sendtime']);?></time>。</span> <a href="javascript:;" onclick="global_invite(<?=$uid?>);" class="decorated" data-behavior="resend_invitation" data-method="post" data-remote="true" rel="nofollow">重新发送电子邮件</a> 或者您可以发送邀请链接给 <?=$member['fullname']?>：</p>
					<p>
					<input type="text" readonly="readonly" data-behavior="select_on_focus" value="<?=$inviteurl?>">
					<span data-role="invite_display" data-activated-text="发送邀请..." style="display:none;">邀请邮件发送成功！</span>
					</p>
				</div>
				<?php }?>
			</header>

			<div class="sheet_body_people">
				<section class="event_stream">
					<?php if($list) { ?>
					<?php if(is_array($list)) { foreach($list as $key => $value) { ?>
					<article class="event">
						<?php if($value['href'] == '') { ?>
							<span class="month_day"><time data-time-ago="" datetime="<?php echo sgmdate('Y-m-d H:i:s', $value['created_time']);?>"><?php echo sgmdate('n-j', $value['created_time']);?></time></span>
							<span class="at"> </span>
							<span class="time"><time data-local-time="" datetime="<?php echo sgmdate('Y-m-d H:i:s', $value['created_time']);?>"><?php echo sgmdate('H:i', $value['created_time']);?></time></span>：<span class="creator"><?=$value['sender_author']?></span>

							<span class="summary"><?=$value['title_html']?></span>

							<span class="bucket"> [所在项目：<?=$value['project_name']?>]</span>
						<?php } else { ?>
						<a href="<?=$value['href']?>">
							<span class="month_day"><time data-time-ago="" datetime="<?php echo sgmdate('Y-m-d H:i:s', $value['created_time']);?>"><?php echo sgmdate('n-j', $value['created_time']);?></time></span>
							<span class="at"> </span>
							<span class="time"><time data-local-time="" datetime="<?php echo sgmdate('Y-m-d H:i:s', $value['created_time']);?>"><?php echo sgmdate('H:i', $value['created_time']);?></time></span>：<span class="creator"><?=$value['sender_author']?></span>

							<span class="summary"><?=$value['title_html']?></span>

							<span class="bucket"> [所在项目：<?=$value['project_name']?>]</span>
						</a>
						<?php } ?>
					</article>
					<?php } } }?>
					<?=$pagenumbers?>
				</section>
			</div>
		</div>
	</div>
</div>
</body>
</html>