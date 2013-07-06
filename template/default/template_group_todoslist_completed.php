<?php if(!defined('IN_TEAMDOTA')) exit('Access Denied');?>
<!DOCTYPE html>
<html>
<head>
<title><?=$manageproject['name']?>: 已完成的待办事宜</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link href="css/application.css" media="screen" rel="stylesheet" type="text/css">
<link href="css/thickbox.css" media="screen" rel="stylesheet" type="text/css">
<link rel="shortcut icon" href="http://www.teamdota.com/favicon.ico" />
<meta charset="utf-8">
<meta name="viewport" content="width=1100">
<style type="text/css">div.panel { min-height: 850px };</style>
<script type="text/javascript">
    window.bbcx = {currentGroup: "<?=$group['group_id']?>",currentPerson: "<?=$_SGLOBAL['supe_uid']?>",currentProject: "<?=$project_id?>",currentDo: "<?=$do?>"};
</script>
<script src="js/jquery.js" type="text/javascript"></script>
<script src="js/global.js" type="text/javascript"></script>
<script src="js/thickbox.js" type="text/javascript"></script>
</head>
<body class="windows">
<?php $position_hotkey = 1 ; include_once template("head"); ?>
<div id="append_parent"></div>
<input type="hidden" id="currentDo" value="<?=$do?>" />
<div class="workspace">
	<div style="width: 980px;" data-container-id="1" class="container stack_container">
		<div class="panel sheet_view inactive todolist">
			<header><h1><a href="group.php?do=project&project_id=<?=$project_id?>"><?=$manageproject['name']?></a> > <a href="group.php?do=todoslist&project_id=<?=$project_id?>">所有待办事宜</a> > 已完成的待办事宜</h1></header>
			<div class="sheet_body_view">
				<section class="completed_todos grouped_by_date" data-behavior="infinite_page">
					<?php if($listday) { ?>
					<?php if(is_array($listday)) { foreach($listday as $lkey => $lvalue) { ?>
					<header data-behavior="remove_duplicates">
						<div>
						  <h2><?=$lkey?></h2>
						  <time><?php echo daysgmdate(sstrtotime($lkey));?></time>
						</div>
					</header>
					<?php if(is_array($lvalue)) { foreach($lvalue as $key => $value) { ?>
					<article class="todolist" id="todolist_1594773">
						<span class="completed_by">
							<a href="group.php?do=todos&project_id=<?=$project_id?>&todos_id=<?=$key?>"><?=$listtodos[$key]?></a>
						</span>

						<ul class="todos">
							<?php if(is_array($value)) { foreach($value as $kkey => $kvalue) { ?>
							<li>
								<a href="group.php?do=todoslist&project_id=<?=$project_id?>&todoslist_id=<?=$kvalue['todoslist_id']?>"><?=$kvalue['subject']?></a>
							</li>
							<?php } } ?>
						</ul>
					</article>
					<?php } } } } } ?>
					<?=$pagenumbers?>
				</section>
			</div>
		</div>
	</div>
</div>
<div id="shade"></div>
</body>
</html>