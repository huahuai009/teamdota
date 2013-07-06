<?php if(!defined('IN_TEAMDOTA')) exit('Access Denied');?>
<!DOCTYPE html>
<html>
<head>
<title><?=$manageproject['name']?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link href="css/application.css" media="screen" rel="stylesheet" type="text/css">
<link rel="shortcut icon" href="http://www.teamdota.com/favicon.ico" />
<meta charset="utf-8">
<meta name="viewport" content="width=1100">
<style type="text/css">div.panel { min-height: 350px };</style>
<script src="js/jquery.js" type="text/javascript"></script>
<script src="js/global.js" type="text/javascript"></script>
</head>
<body class="windows">
<?php $position_hotkey = 1 ; include_once template("head"); ?>
<div class="workspace" style="padding-bottom:30px;">
	<div class="container panel" style="width:1000px; ">
		<div class="panel sheet_view project mini has_notice is_trashed trashed_bucket" data-behavior="read_only " data-status="active">
			<header class="notice">
				该项目已被 <?=$trash['sender_author']?> 于 <?=sgmdate('Y年m月d日', $trash['created_time'])?> 删除。
				<? if(check_project_manage($manageproject['uid'])) { ?>
				<span data-visible-to="admin creator">
					<a href="cp.php?ac=<?=$do?>&project_id=<?=$project_id?>&op=restored&rand=<?=$_SGLOBAL['timestamp'];?>" data-method="post" data-remote="true" rel="nofollow">点击恢复此项目</a>

					或者 <a href="cp.php?ac=<?=$do?>&project_id=<?=$project_id?>&op=realdelete&rand=<?=$_SGLOBAL['timestamp'];?>" onclick="if (confirm('永久删除此项目后将不可恢复，您确定吗？')) return true; else return false;" data-method="delete" data-remote="true" rel="nofollow">永久删除</a> ，
          永久删除之后将不可恢复。
				</span>
				<? } ?>
			</header>
			<div class="sheet_body_view">
				<article>
					<h1><?=$manageproject['name']?></h1>
					<h2><?=$manageproject['description']?></h2>
					<p><?=$manageproject['discussion_num']?> 主题，<?=$manageproject['file_num']?> 附件，<?=$manageproject['document_num']?> 文档，<?=$manageproject['member_num']?> 成员</p>
				</article>
			</div>
		</div>
	</div>
</div>
</body>
</html>