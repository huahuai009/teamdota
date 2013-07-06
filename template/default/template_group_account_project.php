<?php if(!defined('IN_TEAMDOTA')) exit('Access Denied');?>
<!DOCTYPE html>
<html>
<head>
<title>帐户所有活跃项目</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link href="css/application.css" media="all" rel="stylesheet" type="text/css">
<link rel="shortcut icon" href="http://www.teamdota.com/favicon.ico" />
<meta charset="utf-8">
<meta name="viewport" content="width=1100">
<style type="text/css">div.panel { min-height: 683px };</style>
<script type="text/javascript">
    window.bbcx = {currentGroup: "<?=$group['group_id']?>",currentPerson: "<?=$_SGLOBAL['supe_uid']?>",currentProject: "0",currentDo: "<?=$ac?>"};
</script>
<script src="js/jquery.js" type="text/javascript"></script>
<script src="js/global.js" type="text/javascript"></script>
</head>
<body class="windows">
<?php $position_hotkey = 2 ; include_once template("head"); ?>
<div id="workspace" class="workspace">
	<div style="width: 980px;" class="container stack_container">
		<div class="panel sheet_view inactive person_projects">
			<header><h1><a href="cp.php?ac=group">帐号管理</a></h1></header>
			<div class="sheet_body_view" style="margin-left: 20px; margin-bottom: -20px; ">
				<title>帐户所有活跃项目</title>
				<header>
					<h3>帐户所有活跃项目</h3>
				</header>

				<ol style="padding-left: 20px;padding-top:10px;">
					<?php if($list_project) { ?>
					<?php if(is_array($list_project)) { foreach($list_project as $key => $value) { ?>
					<li><strong><?=$value['name']?></strong>　作者：<?=$value['author']?>，创建时间：<?php echo sgmdate('Y-m-d', $value['logtime']);?></li>
					<?php } } } ?>
				</ol>
			</div>
		</div>
	</div>
</div>
</body>
</html>