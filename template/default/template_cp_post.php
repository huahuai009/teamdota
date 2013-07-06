<?php if(!defined('IN_TEAMDOTA')) exit('Access Denied');?>
<!DOCTYPE html>
<html>
<head>
<title>修改评论</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link href="css/application.css" media="all" rel="stylesheet" type="text/css">
<link rel="shortcut icon" href="http://www.teamdota.com/favicon.ico" />
<meta charset="utf-8">
<meta name="viewport" content="width=680">
<script type="text/javascript">
    window.bbcx = {currentGroup: "<?=$group['group_id']?>",currentPerson: "<?=$_SGLOBAL['supe_uid']?>",currentProject: "<?=$project_id?>",currentDo: "<?=$ac?>"};
</script>
<script src="js/jquery.js" type="text/javascript"></script>
<script src="js/global.js" type="text/javascript"></script>
<script src="image/editor/editor_function.js" type="text/javascript"></script>
</head>
<body class="windows">
<div id="append_parent"></div>
<div id="workspace" class="workspace_view">
	<div class="container panel" style="width: 662px; ">
		<div class="sheet_body_detail">
			<div class="expanded_content bubble">
				<form action="cp.php?ac=post&project_id=<?=$project_id?>" class="new_message" id="newcomment_<?=$objectid?>"  method="post">
				<header class="text_entry has_labels">
					<textarea class="userData" name="message" id="qinbaba-ttHtmlEditor" style="height:100%;width:100%;display:none;border:0px" onkeydown="return ctrlEnter(event, 'issuance');"><?=$post['message']?></textarea>
<iframe src="editor.php" name="qinbaba-ifrHtmlEditor" id="qinbaba-ifrHtmlEditor" scrolling="no" border="0" frameborder="0" style="width:100%;border: 1px solid #C5C5C5;" height="200"></iframe>
				</header>
				<footer>
					<div class="submit">
						<div class="chkdiv" id="__newcomment_<?=$objectid?>"></div>
						<input id="issuance" type="button" onclick="validate_edit_post('newcomment_<?=$objectid?>','window.parent.post_edit');" value="修改评论">
						 <a href="#" onclick="window.parent.tb_remove();" class="cancel">取消</a>
					</div>
				</footer>
				<input type="hidden" name="discussion_id" value="<?=$post['discussion_id']?>" />
				<input type="hidden" name="post_id" value="<?=$post['post_id']?>" />
				<input type="hidden" name="postsubmit" value="true" />
				<input type="hidden" name="formhash" value="<?php echo formhash(); ?>" />
				</form>
			</div>
		</div>
	</div>
</div>
</body>
</html>