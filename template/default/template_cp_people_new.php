<?php if(!defined('IN_TEAMDOTA')) exit('Access Denied');?>
<!DOCTYPE html>
<html>
<head>
<title>Teamdota</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link href="css/application.css" media="all" rel="stylesheet" type="text/css">
<link rel="shortcut icon" href="http://www.teamdota.com/favicon.ico" />
<meta charset="utf-8">
<meta name="viewport" content="width=1100">
<style type="text/css">div.panel { min-height: 850px };</style>
<script type="text/javascript">
    window.bbcx = {currentGroup: "<?=$group['group_id']?>",currentPerson: "<?=$_SGLOBAL['supe_uid']?>",currentProject: "0",currentDo: "<?=$ac?>"};
</script>
<script src="js/jquery.js" type="text/javascript"></script>
<script src="js/global.js" type="text/javascript"></script>
</head>
<body class="windows">
<div id="append_parent"></div>
<?php $position_hotkey = 2 ; include_once template("head"); ?>
<div id="workspace" class="workspace">
	
	<div class="container" data-container-id="1">
		<div class="panel global_invite people" data-body-class="home flat_background has_user_menu topnav_everyone">
			<form action="cp.php?ac=people_new" class="invite" data-behavior="invite email-only" data-remote="true" method="post" id="newinvite_<?=$groupid?>">
				<input type="hidden" name="peoplenewsubmit" value="true" />
				<input type="hidden" name="formhash" value="<?php echo formhash(); ?>" />
				<div class="step"><span>1</span></div>
				<div class="row">
					<section class="invite">
						<header>
							<h1 style="font-weight:bold; margin-bottom: 10px">邀请成员到您的Teamdota帐户</h1>
							<h1>输入成员电子邮件地址：</h1>
						</header>
						<div class="invitees" data-behavior="invitees">
							<div class="person invitee field blank" id="div_email_address_0">
								<div class="autocomplete_people">
									<div class="icon"></div>
									<div class="input">
										<input type="text" name="email_address[]" value="" data-behavior="input_change_emitter" data-role="human_input" spellcheck="false" onfocus="email_onfocus(this,0);" onblur="email_onblur(this,0);">
									</div>
									<div class="suggestions" data-role="suggestions_view" id="div_suggestions_0"></div>
								</div>
							</div>
							<div class="person invitee field blank" id="div_email_address_1">
								<div class="autocomplete_people">
									<div class="icon"></div>
									<div class="input">
										<input type="text" name="email_address[]" value="" data-behavior="input_change_emitter" data-role="human_input" spellcheck="false" onfocus="email_onfocus(this,1);" onblur="email_onblur(this,1);">
									</div>
									<div class="suggestions" data-role="suggestions_view" id="div_suggestions_1"></div>
								</div>
							</div>
							<div class="person invitee field blank" id="div_email_address_2">
								<div class="autocomplete_people">
									<div class="icon"></div>
									<div class="input">
										<input type="text" name="email_address[]" value="" data-behavior="input_change_emitter" data-role="human_input" spellcheck="false" onfocus="email_onfocus(this,2);" onblur="email_onblur(this,2);">
									</div>
									<div class="suggestions" data-role="suggestions_view" id="div_suggestions_2"></div>
								</div>
							</div>
						</div>
						<p class="addinput" style="padding-left:100px;">
						<a href="javascript:;" onclick="email_add();">++ 添加</a>
						</p>
					</section>
					<div class="vertical"></div>
				</div>
				<div class="step"><span>2</span></div>
				<div class="row">
					<section>
						<header>
							<h1>可以访问哪些项目?</h1>
						</header>
						<p class="person_project_controls">
							<a href="javascript:;" class="decorated" data-behavior="add_to_all_projects">全选</a>
						</p>
						<table class="accesses" data-display="invite_projects">
							<tbody>
								<?php if($list_project) { ?>
								<?php if(is_array($list_project)) { foreach($list_project as $key => $value) { ?>
								<tr data-project-id="<?=$value['project_id']?>">
									<th>
										<input id="invite_project_<?=$value['project_id']?>" name="project_ids[]" type="checkbox" value="<?=$value['project_id']?>">
									</th>
									<td>
										<label for="invite_project_<?=$value['project_id']?>"><?=$value['name']?></label>
									</td>
								</tr>
								<?php } } } ?>
							</tbody>
						</table>
					</section>
					<div class="vertical"></div>
				</div>
				<?php if($_SGLOBAL['member']['ntype'] > 0) {?>
				<div class="step"><span>3</span></div>
				<div class="row">
					<section>
						<header>
							<h1>分配权限?</h1>
						</header>
						<table>
							<tbody>
								<tr>
									<th>
										<input checked="checked" data-behavior="can_create_projects_permission" id="checkbox_permissions_can_create_projects" name="permissions_can_create_projects" type="checkbox" value="1">
									</th>
									<td>
										<label for="checkbox_permissions_can_create_projects">能够创建项目</label>
									</td>
								</tr>
								<tr>
									<th>
										<input data-behavior="admin_permission" id="checkbox_permissions_admin" name="permissions_admin" type="checkbox" value="1">
									</th>
									<td>
										<label for="checkbox_permissions_admin">管理员 <span style="color:#777;">— 可以管理项目和成员。</span>
										</label>
									</td>
								</tr>
							</tbody>
						</table>
					</section>
					<div class="vertical"></div>
				</div>
				<div class="step"><span>4</span></div>
				<div class="row">
					<section class="done">
						<div id="buttoninvite_<?=$groupid?>">
							<p class="submit">
								<input name="commit" type="button" value="发送邀请"  onclick="validateinvite('buttoninvite_<?=$groupid?>','newinvite_<?=$groupid?>','get_invite_member');">
							</p>
							<p id="__newinvite_<?=$groupid?>">每位成员都会接收到一封含有邀请链接的电子邮件。</p>
						</div>
						<div id="__buttoninvite_<?=$groupid?>"></div>
					</section>
				</div>
				<?php } else { ?>
				<div class="step"><span>3</span></div>
				<div class="row">
					<section class="done">
						<div id="buttoninvite_<?=$groupid?>">
							<p class="submit">
								<input name="commit" type="button" value="发送邀请"  onclick="validateinvite('buttoninvite_<?=$groupid?>','newinvite_<?=$groupid?>','get_invite_member');">
							</p>
							<p id="__newinvite_<?=$groupid?>">每位成员都会接收到一封含有邀请链接的电子邮件。</p>
						</div>
						<div id="__buttoninvite_<?=$groupid?>"></div>
					</section>
				</div>
				<?php } ?>
			</form>
		</div>
	</div>
</div>
<script type="text/javascript">
$(document).ready(function() {
	$("[data-behavior~=add_to_all_projects]").click(function() {
		$("input[name='project_ids[]']").each(function(){
			if($(this).attr("checked")){
				$(this).removeAttr("checked");;
			} else {
				$(this).attr("checked",true);
			}
		});  
	});
});
</script>
</body>
</html>