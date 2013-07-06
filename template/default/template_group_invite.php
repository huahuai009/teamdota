<?php if(!defined('IN_TEAMDOTA')) exit('Access Denied');?>
<!DOCTYPE html>
<html>
<head>
<title>邀请成员</title>
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
<div id="append_parent"></div>
<?php $position_hotkey = 1 ; include_once template("head"); ?>
<div id="workspace" class="workspace">
	<div style="width: 980px;" class="container stack_container">
		<div class="panel sheet_invite inactive blank_slate" data-status="<?=get_project_status();?>">
			<header>
				<h1><a href="group.php?do=project&project_id=<?=$project_id?>"><?=$manageproject['name']?></a> > 邀请成员</h1>
			</header>
			<div style="margin-left: 15px; margin-bottom: -20px;" class="panel sheet_invite accesses">
				<header>
				  <h1 style="display: none;">邀请成员加入该项目</h1>
				</header>
				<div class="sheet_body_invite">
					<div class="columns">
						<? if($manageproject['status'] == 0) { ?>
						<div class="column">
							<section class="invite">
								<header>
									<h1>邀请成员加入该项目</h1>
								</header>
								<form action="cp.php?ac=invite&project_id=<?=$project_id?>" class="invite" id="newinvite_<?=$project_id?>" method="post">
								<input type="hidden" name="emailinvite" value="true" />
								<input type="hidden" name="formhash" value="<?php echo formhash(); ?>" />
								<div style="margin:0;padding:0;display:inline">
									<p class="intro">他们将获得含有该项目链接的电子邮件，您邀请的人将添加到该项目，且能查看该项目的一切资源。</p>
									<h2>成员电子邮件地址:</h2>
									<div class="invitees" data-behavior="invitees">
										<div class="person invitee field blank" id="div_email_address_0">
											<div class="autocomplete_people">
												<div class="icon"></div>
												<div class="input">
													<input data-behavior="input_change_emitter" onfocus="email_onfocus(this,0);" onblur="email_onblur(this,0);" data-role="human_input" spellcheck="false" type="text" name="email_address[]">
												</div>
												<div class="suggestions" data-role="suggestions_view" id="div_suggestions_0">
												</div>
											</div>
										</div>
										<div class="person invitee field blank" id="div_email_address_1">
											<div class="autocomplete_people">
												<div class="icon"></div>
												<div class="input">
												  <input data-behavior="input_change_emitter" onfocus="email_onfocus(this,1);" onblur="email_onblur(this,1);" data-role="human_input" spellcheck="false" type="text" name="email_address[]">
												</div>
												<div class="suggestions" data-role="suggestions_view" id="div_suggestions_1"></div>
											</div>
										</div>
										<div class="person invitee field blank" id="div_email_address_2">
											<div class="autocomplete_people">
												<div class="icon"></div>
												<div class="input">
												  <input data-behavior="input_change_emitter" onfocus="email_onfocus(this,2);" onblur="email_onblur(this,2);" data-role="human_input" spellcheck="false" type="text" name="email_address[]">
												</div>
												<div class="suggestions" data-role="suggestions_view" id="div_suggestions_2"></div>
											</div>
										</div>
									</div>
									<p class="addinput">
									<a href="javascript:;" onclick="email_add();">++ 添加</a>
									</p>
									<p class="intro">
									* 邀请的成员将能够创建项目，您可以随时更改其权限。
									</p>
									<h2>发送内容 (可选)</h2>
									<textarea name="message"></textarea>
									<div class="submit">
										<input id="issuance" type="button" onclick="validateinvite('newinvite_<?=$project_id?>');" value="发送邀请"><span id="__newinvite_<?=$project_id?>"></span>
									</div>
								</div>
								</form>
							</section>
						</div>
						<? } ?>
						<div class="column">
							<section class="accesses" id="accesses">
								<header>
									<h1>参加该项目的成员</h1>
								</header>
								<article class="access" id="access_908158" data-person-id="686913">
									<?php if($members) { ?>
									<?php if(is_array($members)) { foreach($members as $key => $value) { ?>
									<div class="wrapper" id="div_project_member_<?=$value['uid']?>">
										<a href="group.php?do=people&uid=<?=$value['uid']?>" data-replace-stack="true"><img class="avatar" src="<? echo avatar($value['uid'],'40',true);?>" onerror="this.onerror=null;this.src='/image/avatar.gif'" title="<?=$value['fullname']?>"></a>
										<h1><a href="group.php?do=people&uid=<?=$value['uid']?>"><?=$value['fullname']?></a></h1>
										<div class="email"><a href="mailto:<?=$value['email']?>"><?=$value['email']?></a></div>
										<div class="last_invitee_event">
											<?php if($value['isactive']==1){?>
											邀请加入项目 <time data-time-ago="" datetime="<?php echo sgmdate('Y-m-d H:i:s', $value['logtime'],1);?>"><?php echo sgmdate('n-j', $value['logtime'],1);?></time>
											<?php } else { ?>
											<a href="group.php?do=people&uid=<?=$value['uid']?>">最后活动 <time data-time-ago="" datetime="<?php echo sgmdate('Y-m-d H:i:s', $value['logtime'],1);?>"><?php echo sgmdate('n-j', $value['logtime'],1);?></time></a>
											<?php } ?>
										</div>
										<div class="controls">
											<?php if($value['isactive']==1 && $manageproject['status'] == 0){?>
											<form action="cp.php?ac=invite&project_id=<?=$project_id?>&op=resend&uid=<?=$value['uid']?>" class="invite" id="resendinvite_<?=$value['uid']?>" method="post">
											<input type="hidden" name="resendsubmit" value="true" />
											<input type="hidden" name="formhash" value="<?php echo formhash(); ?>" />
											<div class="invite">
												<div id="onresendinvite_<?=$value['uid']?>"><a href="javascript:;" onclick="validateresendinvite('onresendinvite_<?=$value['uid']?>','resendinvite_<?=$value['uid']?>');" class="decorated" data-activated-text="再发送邀请" data-behavior="invite" data-method="post" data-remote="true">再发送邀请</a>
												</div>
												<div id="__onresendinvite_<?=$value['uid']?>"></div>
											</div>
											</form>
											<?php } ?>
											<?php if(check_project_manage($manageproject['uid']) && $value['uid'] != $_SGLOBAL['supe_uid'] && $value['uid'] != $manageproject['uid']  && $manageproject['status'] == 0){?>
											<form action="cp.php?ac=project&project_id=<?=$project_id?>&op=deletemember&deleteuid=<?=$value['uid']?>" class="invite" id="deletemember_<?=$value['uid']?>" method="post">
												<input type="hidden" name="deletemembersubmit" value="true" />
												<input type="hidden" name="formhash" value="<?php echo formhash(); ?>" />
												<div class="intro" id="div_delete_text_<?=$value['uid']?>">
													<a class="decorated" data-behavior="start" onclick="deleteproject_member(0,<?=$value['uid']?>);">删除</a>
												</div>
												<div class="confirm" id="div_delete_operate_<?=$value['uid']?>" style="display:none">
													<span class="question">确定从项目中删除?
													</span>
													<input name="commit" type="button" value="确定" onclick="validatedeletemember('div_delete_operate_<?=$value['uid']?>','deletemember_<?=$value['uid']?>','project_member_delete');" /> 或 <a class="decorated" data-behavior="stop"  onclick="deleteproject_member(1,<?=$value['uid']?>);">取消</a>
												</div>
												<div class="request" id="__div_delete_operate_<?=$value['uid']?>" style="display:none">
												删除 <?=$value['email']?>…
												</div>
											</form>
											<?php } ?>											
										</div>
									</div>
									<?php } } }?>
								</article>
							</section>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
</body>
</html>