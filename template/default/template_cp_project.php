<?php if(!defined('IN_TEAMDOTA')) exit('Access Denied');?>
<!DOCTYPE html>
<html>
<head>
<title>创建项目</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link href="css/application.css" media="all" rel="stylesheet" type="text/css">
<link rel="shortcut icon" href="http://www.teamdota.com/favicon.ico" />
<meta charset="utf-8">
<meta name="viewport" content="width=1100">
<script type="text/javascript">
    window.bbcx = {currentGroup: "<?=$group['group_id']?>",currentPerson: "<?=$_SGLOBAL['supe_uid']?>",currentProject: "<?=$project_id?>",currentDo: "<?=$ac?>"};
</script>
<script src="js/jquery.js" type="text/javascript"></script>
<script src="js/global.js" type="text/javascript"></script>
</head>
<body class="windows">
<?php $position_hotkey = 1 ; include_once template("head"); ?>
<div id="append_parent"></div>
<div id="workspace" class="workspace">
	<div style="width: 980px;" class="container stack_container">
		<div class="panel sheet_view inactive ">
			<header><h1><a href="group.php?do=home">返回项目列表</a></h1></header>
			<div class="sheet_body_view">
				<div class="project">
					<form action="cp.php?ac=project&project_id=<?=$project_id?>" class="new_message" id="newproject_<?=$project_id?>"  method="post">
					<header class="text_entry has_labels">
						<textarea autofocus="autofocus" cols="40"  id="project_name" name="project_name" rows="1" style="resize: none; overflow-x: hidden; overflow-y: hidden; min-height: 27px; margin-bottom: 15px;padding-left:5px;" placeholder="项目名称"></textarea>
						<textarea cols="40" id="project_description" name="project_description" placeholder="添加描述或额外的细节（可选）" rows="1" style="resize: none; overflow-x: hidden; overflow-y: hidden; min-height: 27px; padding-left:5px;margin-bottom: 10px;"></textarea>
						<div class="sheet_body_invite">
							<div class="columns">
								<div class="column">
									<section class="invite">
										<div style="margin:0;padding:0;display:inline">
											<p class="intro">使用电子邮件邀请成员加入（可选）</p>
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
											<p class="addinput" style="margin-left:520px;">
											<a href="javascript:;" onclick="email_add();">++ 添加</a>
											</p>
										</div>
									</section>
								</div>
							</div>
						</div>
					</header>
					<footer>
						<div class="submit">
							<input id="issuance" type="button" onclick="validate_create_project('newproject_<?=$project_id?>','project_create');" value="创建项目"> <a href="<?=dreferer(); ?>" class="cancel">取消</a>
						</div>
					</footer>
					<input type="hidden" name="project_id" value="<?=$project_id?>" />
					<input type="hidden" name="projectsubmit" value="true" />
					<input type="hidden" name="formhash" value="<?php echo formhash(); ?>" />
					</form>
				</div>
				<div class="project_gallery">
					<header>
						<h2>例如项目名称及描述：</h2>
					</header>

					<h3>网站改版</h3>
					<p>主页思路</p>

					<h3>电子邮件营销</h3>
					<p>2012年的计划</p>

					<h3>广告推广</h3>
					<p>预算：￥30,000</p>
				</div>
			</div>
		</div>
	</div>
</div>
</body>
</html>