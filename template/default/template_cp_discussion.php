<?php if(!defined('IN_TEAMDOTA')) exit('Access Denied');?>
<?php $detailurl = "group.php?project_id={$project_id}&do=discussion&discussion_id={$discussion_id}"; ?>
<!DOCTYPE html>
<html>
<head>
<title><?=$manageproject['name']?>: <?php if(!empty($discussion_id)) { echo $discussion['subject'];}else{echo "创建主题";}?></title>
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
<script src="image/editor/editor_function.js" type="text/javascript"></script>
</head>
<body class="windows">
<?php $position_hotkey = 1 ; include_once template("head"); ?>
<div id="append_parent"></div>
<input type="hidden" id="currentDo" value="<?=$ac?>" />
<div class="workspace" id="fileDragArea">
	<div style="width: 980px;" class="container stack_container">
		<div class="panel sheet_view inactive ">
			<header><h1><a href="group.php?do=project&project_id=<?=$project_id?>&discussion_page=<?=$discussion_page?>"><?=$manageproject['name']?></a> > 交流区</h1></header>
			<div class="sheet_body_view">
				<div class="bubble">
					<form action="cp.php?ac=discussion&project_id=<?=$project_id?>" class="new_message" id="newdiscussion_<?=$discussion_id ?>" method="post">
					<header class="text_entry has_labels">
						<h3>
						  <label for="message_subject">标题：</label>
						 <textarea autofocus="autofocus" cols="40"  id="subject" name="subject" rows="1" style="resize: none; overflow-x: hidden; overflow-y: hidden; min-height: 27px; " onkeydown="return ctrlEnter(event, 'issuance', 1);"><?=$discussion['subject']?></textarea>
						</h3>
						<textarea class="userData" name="message" id="qinbaba-ttHtmlEditor" style="height:100%;width:100%;display:none;border:0px" onkeydown="return ctrlEnter(event, 'issuance');"><?=$discussion['message']?></textarea>
	<iframe src="editor.php" name="qinbaba-ifrHtmlEditor" id="qinbaba-ifrHtmlEditor" scrolling="no" border="0" frameborder="0" style="width:100%;border: 1px solid #C5C5C5;" height="300"></iframe>
							<div data-behavior="pending_attachments" class="attachments">
								<img alt="Paperclip" class="prompt_graphic" src="image/paperclip.png">
								<div class="file_input_button">
									<input id="fileImage" name="fileselect[]" type="file" multiple="" tabindex="-1">

							  拖动附件到此虚线区域 <a href="javascript:;" class="decorated" tabindex="-1">或者点击选择...</a>
								</div>
								<ul class="pending_attachments" id="preview">
									<?php $i=1000;$fileids=''; ?>
									<?php if($listdiscussionpic) { ?>
									<?php if(is_array($listdiscussionpic)) { foreach($listdiscussionpic as $key => $value) { ?>
										<li class="image" id="uploadList_<?=$i?>">
											<img class="thumbnail" src="<?=$value['fileurl']?>">
											<span class="name"><?=$value['filename']?></span>
											<a class="remove" href="javascript:;" onclick="webupload.funDeleteFile('<?=$i?>');" data-index="<?=$i?>"><span>删除</span></a>
											<div class="progress" data-behavior="progress"><div></div></div>
											<input id="uploadfileid_<?=$i?>" value="<?=$value['file_id']?>" type="hidden">
										</li>
									<?php if($fileids=='') {
										        $fileids = $value['file_id'];
									        } else {
												$fileids = $fileids.','.$value['file_id'];
											}
									?>
									<?php $i++;} } }?>
									
									<?php if($listdiscussionfile) { ?>
									<?php if(is_array($listdiscussionfile)) { foreach($listdiscussionfile as $keyfile => $valuefile) { ?>
										<li id="uploadList_<?=$i?>">
											<div class="icon">
												<img src="<?=$valuefile['fileurl']?>" class="file_icon" height="32" width="32">
											</div>
											<span class="name"><?=$valuefile['filename']?></span>
											<a class="remove" href="javascript:;" onclick="webupload.funDeleteFile('<?=$i?>');" data-index="<?=$i?>"><span>删除</span></a>
											<div class="progress" data-behavior="progress"><div></div></div>
											<input id="uploadfileid_<?=$i?>" value="<?=$valuefile['file_id']?>" type="hidden">
										</li>
									<?php if($fileids=='') {
										        $fileids = $valuefile['file_id'];
									        } else {
												$fileids = $fileids.','.$valuefile['file_id'];
											}
									?>
									<?php $i++;} } } ?>
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
												<h4>通过邮件发送主题给该项目的成员：</h4>
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
							<div class="chkdiv" id="__newdiscussion_<?=$discussion_id?>"></div>
							<?php if(!empty($discussion_id)) { ?>
							<input  id="issuance_upload" data-behavior="issuance_upload" type="button" onclick="webupload.buttonUploadFile();" value="附件上传中..." style="display:none;">
							<input  id="issuance" data-behavior="issuance_save" type="button" onclick="validate('newdiscussion_<?=$discussion_id?>','message_edit');" value="修改主题">
							<a href="<?=dreferer(); ?>" class="cancel">取消</a>
							<? } else { ?>
							<input  id="issuance_upload" data-behavior="issuance_upload" type="button" onclick="webupload.buttonUploadFile();" value="附件上传中..." style="display:none;">
							<input  id="issuance" data-behavior="issuance_save" type="button" onclick="validate('newdiscussion_<?=$discussion_id?>','message_add');" value="发布主题">
							<a href="<?=dreferer(); ?>" class="cancel">取消</a>
							<? } ?>
						</div>
					</footer>
					<input type="hidden" id="fileids" name="fileids" value="<?=$fileids?>" />
					<input type="hidden" name="discussion_id" value="<?=$discussion['discussion_id']?>" />
					<input type="hidden" name="discussionsubmit" value="true" />
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