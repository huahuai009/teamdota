<?php if(!defined('IN_TEAMDOTA')) exit('Access Denied');?>
<!DOCTYPE html>
<html>
<head>
<title><?=$manageproject['name']?>: 上传附件</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link href="css/application.css" media="screen" rel="stylesheet" type="text/css">
<link rel="shortcut icon" href="http://www.teamdota.com/favicon.ico" />
<meta charset="utf-8">
<meta name="viewport" content="width=1100">
<style type="text/css">div.panel { min-height: 850px };</style>
<script type="text/javascript">
    window.bbcx = {currentGroup: "<?=$group['group_id']?>",currentPerson: "<?=$_SGLOBAL['supe_uid']?>",currentProject: "<?=$project_id?>",currentDo: "<?=$ac?>"};
</script>
<script src="js/jquery.js" type="text/javascript"></script>
<script src="js/global.js" type="text/javascript"></script>
</head>
<body class="windows">
<?php $position_hotkey = 1 ; include_once template("head"); ?>
<div id="append_parent"></div>
<input type="hidden" id="currentDo" value="<?=$ac?>" />
<div class="workspace" id="fileDragArea">
	<div style="width: 980px;" class="container stack_container">
		<div class="panel sheet_view inactive ">
			<header><h1><a href="group.php?do=project&project_id=<?=$project_id?>&file_page=<?=$file_page?>"><?=$manageproject['name']?></a> > 资料区</h1></header>
			<div class="sheet_body_view">
				<div class="bubble">
					<form action="cp.php?ac=attachment&project_id=<?=$project_id?>" class="new_message" id="newattachment_<?=$file_id ?>" method="post">
					<header class="text_entry has_labels">
							<div data-behavior="pending_attachments" class="attachments">
								<img alt="Paperclip" class="prompt_graphic" src="image/paperclip.png">
								<div class="file_input_button">
									<input id="fileImage" name="fileselect[]" type="file" multiple="" tabindex="-1" autofocus="autofocus">

							  拖动附件到此虚线区域 <a href="javascript:;" class="decorated" tabindex="-1">或者点击选择...</a>
								</div>
								<ul class="pending_attachments" id="preview">
								</ul>
							</div>
					</header>
					<footer>
						<? if($members_num) {?>
						<div class="subscribable">
							<div class="expanded_content">
								<div data-behavior="subscriber_list" data-subscribable="message" data-people="[{&quot;id&quot;:&quot;person_1250533&quot;,&quot;person_id&quot;:1250533,&quot;name&quot;:&quot;duty&quot;},{&quot;id&quot;:&quot;person_1250552&quot;,&quot;person_id&quot;:1250552,&quot;name&quot;:&quot;mr duty&quot;},{&quot;id&quot;:&quot;person_1360509&quot;,&quot;person_id&quot;:1360509,&quot;name&quot;:&quot;287211215@qq.com&quot;}]" data-groups="[]" data-subscriber-ids="[]">
									<table>
										<tr>
											<td class="subscribers">
												<h4>通过邮件发送附件给该项目的成员：</h4>
												<div class="select_all_or_none">
													<a href="javascript:;" class="decorated select_everyone" onclick="select_message_subscribers();" data-behavior="subscriber_select_all">全选</a> |
													<a href="javascript:;" class="decorated select_everyone" onclick="select_none_message_subscribers();" data-behavior="subscriber_select_none">取消</a>
												</div>

												<div class="subscribers">
													<div>
														<?php if($members) { ?>
														<?php if(is_array($members)) {$i=0;foreach($members as $key => $value) { ?>
														<?php if(($i % 5)==0 && $i != 0) { ?>
														</div>
														<div>
														<?php }?>
														<div class="column">
														
														  <label data-subscriber-id="<?=$value['uid']?>" title="<?=$value['fullname']?>">
															<input name="message_subscribers[]" type="checkbox" value="<?=$value['uid']?>">
															<?=$value['fullname']?>
														  </label>
														
														</div>
														<?php  $i++;} } } ?>
													
														<div class="column">
														
														</div>
													
													</div>
												</div>
											</td>
										</tr>
									</table>
								</div>
							</div>
						</div>
						<? } ?>
						<div class="submit">
								<div class="chkdiv" id="__newattachment_<?=$file_id?>"></div>
								<input  id="issuance_upload" data-behavior="issuance_upload" type="button" onclick="webupload.buttonUploadFile();" value="附件上传中..." style="display:none;">
								<input  id="issuance" data-behavior="issuance_save" type="button" onclick="validateattachment('newattachment_<?=$file_id?>','message_add');" value="保存附件">
								<a href="<?=dreferer(); ?>" class="cancel">取消</a>
						</div>
					</footer>
					<input type="hidden" id="fileids" name="fileids" value=""/>
					<input type="hidden" name="attachmentsubmit" value="true" />
					<input type="hidden" name="formhash" value="<?php echo formhash(); ?>" />
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
$(document).ready(function() {
	show_webupload(0);
});
</script>
</body>
</html>