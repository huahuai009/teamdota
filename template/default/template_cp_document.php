<?php if(!defined('IN_TEAMDOTA')) exit('Access Denied');?>
<?php $detailurl = "group.php?project_id={$project_id}&do=document&document_id={$document_id}"; ?>
<!DOCTYPE html>
<html>
<head>
<title><?=$manageproject['name']?>: <?php if($_GET['op'] == 'edit') { echo $document['name'];}else{echo "创建文本";}?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link href="css/application.css" media="screen" rel="stylesheet" type="text/css">
<link rel="shortcut icon" href="http://www.teamdota.com/favicon.ico" />
<meta charset="utf-8">
<meta name="viewport" content="width=1100">
<script type="text/javascript">
    window.bbcx = {currentGroup: "<?=$group['group_id']?>",currentPerson: "<?=$_SGLOBAL['supe_uid']?>",currentProject: "<?=$project_id?>",currentDo: "<?=$ac?>"};
</script>
<script src="js/jquery.js" type="text/javascript"></script>
<script src="js/global.js" type="text/javascript"></script>
<script src="js/autosave.js" type="text/javascript"></script>
<script src="image/editor/editor_function.js" type="text/javascript"></script>
</head>
<body class="windows">
<?php $position_hotkey = 1 ; include_once template("head"); ?>
<div id="append_parent"></div>
<input type="hidden" id="currentDo" value="<?=$ac?>" />
<div class="workspace">
	<div style="width: 980px;" class="container stack_container">
		<div class="panel sheet_view inactive ">
			<header><h1><a href="group.php?do=project&project_id=<?=$project_id?>&document_page=<?=$document_page?>"><?=$manageproject['name']?></a> > 文档区</h1></header>
			<div class="sheet_body_view">
				<div class="bubble">
					<form action="cp.php?ac=document&project_id=<?=$project_id?>" class="new_message" id="newdocument_<?=$document_id ?>" method="post">
					<header class="text_entry has_labels">
						<h3>
						  <label for="message_subject">标题：</label>
						 <textarea autofocus="autofocus" cols="40"  id="name" name="name" rows="1" style="resize: none; overflow-x: hidden; overflow-y: hidden; min-height: 27px; " placeholder="未知文档" onkeydown="return ctrlEnter(event, 'issuance', 1);"><?=$document['name']?></textarea>
						</h3>
						<textarea class="userData" name="description" id="qinbaba-ttHtmlEditor" style="height:100%;width:100%;display:none;border:0px" onkeydown="return ctrlEnter(event, 'issuance');"><?=$document['description']?></textarea>
	<iframe src="editor.php" name="qinbaba-ifrHtmlEditor" id="qinbaba-ifrHtmlEditor" scrolling="no" border="0" frameborder="0" style="width:100%;border: 1px solid #C5C5C5;" height="450"></iframe>
						保存时间：<?php echo sgmdate('Y年m月d日 H:i', $_GET['op'] == 'edit' ? $document['uptime'] : $_SGLOBAL['timestamp']);?>
					</header>
					<footer>
						<div class="submit">
							<div class="chkdiv" id="__newdocument_<?=$document_id?>"></div>
							<?php if(!empty($document_id)) { ?>
							<input  id="issuance" type="button" onclick="validatedocument('newdocument_<?=$document_id?>','message_edit');" value="保存文档">
							<a href="<?=dreferer(); ?>" class="cancel">取消</a>
							<? } else { ?>
							<input  id="issuance" type="button" onclick="validatedocument('newdocument_<?=$document_id?>','message_add');" value="发布文档">
							<a href="<?=dreferer(); ?>" class="cancel">取消</a>
							<? } ?>
						</div>
					</footer>
					<input type="hidden" id="fileids" name="fileids" value="<?=$fileids?>" />
					<input type="hidden" id="document_id" name="document_id" value="<?=$document_id ?>" />
					<input type="hidden" id="documentsubmit" name="documentsubmit" value="true" />
					<input type="hidden" id="formhash" name="formhash" value="<?php echo formhash(); ?>" />
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
$(document).ready(function () {
	TEAMDOTA.AutoSave.init();
});
</script>
</body>
</html>