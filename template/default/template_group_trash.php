<?php if(!defined('IN_TEAMDOTA')) exit('Access Denied');?>
<!DOCTYPE html>
<html>
<head>
<title><?=$member['fullname']?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link href="css/application.css" media="all" rel="stylesheet" type="text/css">
<link rel="shortcut icon" href="http://www.teamdota.com/favicon.ico" />
<meta charset="utf-8">
<meta name="viewport" content="width=1100">
<style type="text/css">div.panel { min-height: 727px };</style>
<script type="text/javascript">
    window.bbcx = {currentGroup: "<?=$group['group_id']?>",currentPerson: "<?=$_SGLOBAL['supe_uid']?>",currentProject: "<?=$project_id?>",currentDo: "<?=$do?>"};
</script>
<script src="js/jquery.js" type="text/javascript"></script>
<script src="js/global.js" type="text/javascript"></script>
</head>
<body class="windows topnav_root">
<?php $position_hotkey = 4 ; include_once template("head"); ?>
<div id="ajax_show_project" style="display:none;"></div>
<div id="workspace_project" class="workspace">
	<div style="width: 980px;" class="container stack_container">
		<div class="panel sheet_view inactive trash">
			<title>回收站</title>
			<header>
				<h1>回收站</h1>
				<p>如果有误删，可以点击进行检查，且可以进行恢复。<br>15天之后，回收站里的东西将被系统永久删除。</p>
			</header>
			<div class="sheet_body_view">
				<section class="event_stream grouped_by_date" data-behavior="infinite_page" data-infinite-page="3">
					<?php if($listday) { ?>
					<?php if(is_array($listday)) { foreach($listday as $lkey => $lvalue) { ?>
					<header data-behavior="remove_duplicates" data-unique-id="date_header_2012-05-26">
						<div>
						  <h2><?=$lkey?></h2>
						  <time data-days-ago=""><?php echo daysgmdate(sstrtotime($lkey));?></time>
						</div>
					</header>
					<?php if(is_array($lvalue)) { foreach($lvalue as $key => $value) { ?>
					<article class="event">
						<a href="<?=$value['href']?>">
							<span class="month_day"><time data-local-date="" datetime="<?php echo sgmdate('Y-m-d H:i:s', $value['created_time']);?>"><?=$lkey?></time></span>
							<span class="at"> 在 </span>
							<span class="time"><time data-local-time="" datetime="<?php echo sgmdate('Y-m-d H:i:s', $value['created_time']);?>"><?php echo sgmdate('H:i', $value['created_time']);?></time></span>：<span class="creator"><?=$value['sender_author']?></span>

							<span class="summary"><?=$value['title_html']?></span>
							<span class="bucket"> [所在项目：<?=$value['project_name']?>]</span>
						</a>
						<span class="subscribers"></span> 
					</article>
					<?php } } } } }?>
				</section>
			</div>
		</div>
	</div>
</div>
</body>
</html>