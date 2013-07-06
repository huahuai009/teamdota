<?php if(!defined('IN_TEAMDOTA')) exit('Access Denied');?>
<!DOCTYPE html>
<html>
<head>
<title>项目</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link href="css/application.css" media="screen" rel="stylesheet" type="text/css">
<link href="css/thickbox.css" media="screen" rel="stylesheet" type="text/css">
<link rel="shortcut icon" href="http://www.teamdota.com/favicon.ico" />
<meta charset="utf-8">
<meta name="viewport" content="width=1100">
<style type="text/css">div.panel { min-height: 886px };</style></head>
<script src="js/jquery.js" type="text/javascript"></script>
<script src="js/global.js" type="text/javascript"></script>
<script src="js/thickbox.js" type="text/javascript"></script>
<body class="windows topnav_root">
<?php $position_hotkey = 1 ; include_once template("head"); ?>
<div id="workspace" class="workspace">
	<div class="container panel home_tab project_index">
		<title>Projects</title>
		<nav class="projects_nav">
			<a href="cp.php?ac=project" data-behavior="new_project bounce"><span></span></a>    
		</nav>
		<section class="projects cards">
            <div class="row">
				<?php if($projects) { ?>
				<?php if(is_array($projects)) { foreach($projects as $key => $value) { ?>
				<article class="card">
					<a href="group.php?do=project&project_id=<?=$value['project_id']?>" class="project_card" title="<?=$value['name']?>">
					<h5><?=$value['name']?></h5>
					<p style="margin-bottom: 5px;"><?=$value['description']?></p>
					<p class="project_detail_data"><?=$value['discussion_num']?>主题 | <?=$value['todoslist_num']?>待办事宜 | <?=$value['file_num']?>附件 | <?=$value['document_num']?>文档</p>
					<div class="people">
						<?=$value['members']?>
					</div>
					</a>          	
				</article>
				<?php } ?>
				<?php if(($projectnumber % 3) !=0){ for($i=0;$i < (3 - $projectnumber % 3);$i++) { ?>
				<article class="card blank " data-behavior="bounce_nav">&nbsp;</article>
				<?php } } ?>
				<?php } ?>
				<?php } else {?>
				<article class="card blank " data-behavior="bounce_nav">&nbsp;</article>
				<article class="card blank " data-behavior="bounce_nav">&nbsp;</article>
				<article class="card blank " data-behavior="bounce_nav">&nbsp;</article>
				<?php } ?>
			</div>
		</section>
		<a href="group.php?do=trash" class="trash" data-replace-stack="true">回收站</a>
		<?php if($archived_projectnumber) {?>
		<section class="projects alpha">
			<div class="archived" data-behavior="expandable">
				<div class="collapsed_content">
					<a href="javascript:;" data-behavior="expand_on_click"><?=$archived_projectnumber?>个归档项目</a>
				</div>

				<div class="expanded_content">
					<header>
						<h3><?=$archived_projectnumber?>个归档项目</h3>
					</header>
					<?php if($archived_projects) { ?>
					<?php if(is_array($archived_projects)) { foreach($archived_projects as $key => $value) { ?>
					<hr>
					<article class="project" data-behavior="has_hover_content">
						<h3><a href="group.php?do=project&project_id=<?=$value['project_id']?>"><?=$value['name']?></a></h3>
					</article>
					<?php } } } ?>
				</div>
			</div>
		</section>
		<?php } ?>
		<?php if(!$projectnumber) {?>
		<article class="sample_blank_slate">
			<div>
				<h1>欢迎您来到Teamdota！</h1>
				  <p>点击左边的“<a href="cp.php?ac=project">添加新项目</a>”按钮，创建好您的项目，她们就会出现在这里。</p>
			</div>
		</article>
		<?php } ?>
	</div>
</div>
<!--<footer>
	<p><a href="#">联系我们</a> &nbsp; <span>|</span> &nbsp; <a href="#">安全</a> &nbsp; <span>|</span> &nbsp; <a href="#">隐私</a><br>
	Copyright &copy;2012 teamdota, LLC. All rights reserved.</p>
</footer>-->
<script type="text/javascript">
$(document).ready(function() {
	$("[data-behavior~=expand_on_click]").click(function() {
		$(".expanded_content").show();
		$(".collapsed_content").hide();
	});
});
</script>
</body>
</html>