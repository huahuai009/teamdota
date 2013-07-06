<?php if(!defined('IN_TEAMDOTA')) exit('Access Denied');?>
<?php $detailurl = "group.php?project_id={$project_id}&do=todos&todos_id={$todos_id}"; ?>
<!DOCTYPE html>
<html>
<head>
<title><?=$manageproject['name']?>: <?php if(!empty($todos_id)) { echo $todos['subject'];}else{echo "创建待办事宜";}?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link href="css/application.css" media="screen" rel="stylesheet" type="text/css">
<link rel="shortcut icon" href="http://www.teamdota.com/favicon.ico" />
<meta charset="utf-8">
<meta name="viewport" content="width=680">
<script type="text/javascript">
    window.bbcx = {currentGroup: "<?=$group['group_id']?>",currentPerson: "<?=$_SGLOBAL['supe_uid']?>",currentProject: "<?=$project_id?>",currentDo: "<?=$ac?>"};
</script>
<script src="js/jquery.js" type="text/javascript"></script>
<script src="js/global.js" type="text/javascript"></script>
</head>
<body class="windows">
<div id="append_parent"></div>
<input type="hidden" id="currentDo" value="<?=$ac?>" />
<div class="workspace_view">
	<div class="container panel" style="width: 662px; ">
		<div class="sheet_body_detail">
			<div class="expanded_content bubble">
				<form action="cp.php?ac=todos&project_id=<?=$project_id?>" class="new_message" id="newtodos_<?=$todos_id ?>" method="post">
				<header class="text_entry has_labels">
					<h3>
					  <label for="message_subject">标题：</label>
					 <textarea autofocus="autofocus" cols="40"  id="subject" name="subject" rows="1" style="resize: none; overflow-x: hidden; overflow-y: hidden; min-height: 27px; "  onkeydown="return ctrlEnter(event, 'issuance', 1);"><?=$todos['subject']?></textarea>
					</h3>
				</header>
				<footer>
					<div class="submit">
						<div class="chkdiv" id="__newtodos_<?=$todos_id?>"></div>
						<?php if(!empty($todos_id)) { ?>
						<input  id="issuance" type="button" onclick="validate_todos('newtodos_<?=$todos_id?>','window.parent.todos_edit');" value="修改">
						<a href="javascript:;" onclick="window.parent.tb_remove();" class="cancel">取消</a>
						<? } else { ?>
						<input  id="issuance" type="button" onclick="validate_todos('newtodos_<?=$todos_id?>','window.parent.todos_add');" value="添加">
						<a href="javascript:;" onclick="window.parent.tb_remove();" class="cancel">取消</a>
						<? } ?>
					</div>
				</footer>
				<input type="hidden" name="todos_id" value="<?=$todos_id?>" />
				<input type="hidden" name="todossubmit" value="true" />
				<input type="hidden" name="formhash" value="<?php echo formhash(); ?>" />
				</form>
			</div>
		</div>
	</div>
</div>
</body>
</html>