<?php if(!defined('IN_TEAMDOTA')) exit('Access Denied');?>
<?php $detailurl = "group.php?project_id={$project_id}&do=todos&todos_id={$todos_id}"; ?>
<!DOCTYPE html>
<html>
<head>
<title><?=$manageproject['name']?>: <?php if(!empty($todos_id)) { echo $todos['subject'];}else{echo "创建待办事宜";}?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link href="css/application.css" media="screen" rel="stylesheet" type="text/css">
<link rel="shortcut icon" href="http://www.teamdota.com/favicon.ico" />
<link rel="stylesheet" href="js/datepicker/jquery-ui.css" type="text/css" media="all" />
<link rel="stylesheet" href="js/datepicker/ui.theme.css" type="text/css" media="all" />
<meta charset="utf-8">
<meta name="viewport" content="width=680">
<script type="text/javascript">
    window.bbcx = {currentGroup: "<?=$group['group_id']?>",currentPerson: "<?=$_SGLOBAL['supe_uid']?>",currentProject: "<?=$project_id?>",currentDo: "<?=$ac?>"};
</script>
<script src="js/jquery.js" type="text/javascript"></script>
<script src="js/global.js" type="text/javascript"></script>
<script src="js/datepicker/jquery-ui.min.js" type="text/javascript"></script>
<script src="js/datepicker/jquery-ui-i18n.min.js" type="text/javascript"></script>
</head>
<body class="windows">
<div id="append_parent"></div>
<input type="hidden" id="currentDo" value="<?=$ac?>" />
<div class="workspace_view">
	<div class="container panel" style="width: 662px; ">
		<div class="sheet_body_detail">
			<div class="expanded_content bubble">
				<form action="cp.php?ac=todoslist&project_id=<?=$project_id?>&todos_id=<?=$todos_id?>" class="new_message" id="newtodoslist_<?=$todoslist_id ?>" method="post">
				<header class="text_entry has_labels">
					<h3>
						<label for="message_subject"><b>标题：</b></label>
						<textarea autofocus="autofocus" cols="40"  id="subject" name="subject" rows="1" style="resize: none; overflow-x: hidden; overflow-y: hidden; min-height: 27px; " onkeydown="return ctrlEnter(event, 'issuance', 1);"><?=$todoslist['subject']?></textarea>
					</h3>
					<span class="balloon">
						<label>
							<b>分配该待办事宜给：</b>
							<select data-assignee-options-loaded="true" name="todo_assignee_code" id="todo_assignee_code" onkeydown="return ctrlEnter(event, 'issuance', 1);">
								<option value=""></option>
								<?php if($listuser) { ?>
								<?php if(is_array($listuser)) { foreach($listuser as $key => $value) { ?>
								<option value="<?=$value['uid']?>" <?php if($value['uid'] == $todoslist['assign_uid']) {echo 'selected';} ?> ><?=$value['fullname']?></option>
								<?php } } }?>
							</select>
						</label>

						<small><p>将通过电子邮件通知分配的成员</p></small>

						<label>
							<b>预计完成时间：</b>
							<br/>
							<input name="todo_due_at" type="text" maxlength="50" id="todo_due_at" style="height:25px;width:30%;margin:8px 0 20px;" value="<?=$todoslist['due_date']?>"  onkeydown="return ctrlEnter(event, 'issuance', 1);"/>
							<hr>
						</label>
					</span>
				</header>
				<footer>
					<div class="submit">
						<div class="chkdiv" id="__newtodoslist_<?=$todoslist_id?>"></div>
						<?php if(!empty($todoslist_id)) { ?>
						<input  id="issuance" type="button" onclick="validate_todos('newtodoslist_<?=$todoslist_id?>','window.parent.todoslist_edit');" value="修改">
						<a href="javascript:;" onclick="window.parent.tb_remove();" class="cancel">取消</a>
						<? } else { ?>
						<input  id="issuance" type="button" onclick="validate_todos('newtodoslist_<?=$todoslist_id?>','window.parent.todoslist_add');" value="添加">
						<a href="javascript:;" onclick="window.parent.tb_remove();" class="cancel">取消</a>
						<? } ?>
					</div>
				</footer>
				<input type="hidden" name="todoslist_id" value="<?=$todoslist_id?>" />
				<input type="hidden" name="todoslistsubmit" value="true" />
				<input type="hidden" name="formhash" value="<?php echo formhash(); ?>" />
				</form>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
$(function() {
	$("#todo_due_at").datepicker({
		dateFormat:'yy-mm-dd'
	});
});
</script>
</body>
</html>