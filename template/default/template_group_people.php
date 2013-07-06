<?php if(!defined('IN_TEAMDOTA')) exit('Access Denied');?>
<!DOCTYPE html>
<html>
<head>
<title>成员</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link href="css/application.css" media="all" rel="stylesheet" type="text/css">
<link rel="shortcut icon" href="http://www.teamdota.com/favicon.ico" />
<meta charset="utf-8">
<meta name="viewport" content="width=1100">
<style type="text/css">div.panel { min-height: 886px };</style>
<script type="text/javascript">
    window.bbcx = {currentGroup: "<?=$group['group_id']?>",currentPerson: "<?=$_SGLOBAL['supe_uid']?>",currentProject: "0",currentDo: "<?=$do?>"};
</script>
<script src="js/jquery.js" type="text/javascript"></script>
<script src="js/global.js" type="text/javascript"></script>
</head>
<body class="windows">
<?php $position_hotkey = 2 ; include_once template("head"); ?>
<div id="workspace_people" class="workspace">
	<div class="container panel home_tab people_index">
		<title>成员</title>
		<nav class="people_nav">
			<a href="cp.php?ac=people_new" data-behavior="new_member bounce">
			<span></span>
</a>    </nav>
		<section class="peoples cards">
            <div class="row">
				<?php if($list) { ?>
				<?php if(is_array($list)) { foreach($list as $key => $value) { ?>
				<article class="person">
					<div class="avatar">
						<a href="group.php?do=people&uid=<?=$value['uid']?>">
						<img class="avatar" width="40" height="40" title="<?=$value['fullname']?>" src="<? echo avatar($value['uid'],'40',true);?>" onerror="this.onerror=null;this.src='/image/avatar.gif'"><h5><?=$value['fullname']?></h5>
						</a>
					</div>
					<p style="padding:8px 0 5px 16px;border-top:1px solid #fff;"><a href="mailto:<?=$value['email']?>"><?=$value['email']?></a></p>
					<p class="activity_detail_data">最后活动<?php echo sgmdate('n-j', $value['lastactivity'],1);?></p>			
				</article>
				<?php } } } ?>
			</div>
			<?=$pagenumbers?>
		</section>
		<?php if($_SGLOBAL['member']['ntype'] > 0) { ?>
		<div class="all_people_and_superpowers">
			<a href="cp.php?ac=people_permissions">成员权限分配</a>
		</div>
		<?php } ?>
	</div>
</div>
</body>
</html>