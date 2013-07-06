<?php if(!defined('IN_TEAMDOTA')) exit('Access Denied');?>
<!DOCTYPE html>
<html>
<head>
<title><?=$group['group_name']?></title>
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
<div id="workspace" class="workspace">
	<div style="width: 980px;" class="container stack_container">
		<div class="panel sheet_view account">
			<title>帐户资料</title>
			<header>

				<h1 class="inactive_title">帐户资料</h1>
				<h2>帐户编号：<?=$group['group_id']?></h2>
			</header>

			<div class="sheet_body_group">
				<section class="account_name">
					<header data-behavior="account_name_header" style="display: block; ">
						<h2>
						<span data-behavior="account_name_header_name"><?=$group['group_name']?> 的Teamdota</span>
						<a href="#" data-behavior="account_name_link">点击更改帐户名</a>
						</h2>
					</header>
					<div data-behavior="account_name_form" style="display: none; ">
						<form action="cp.php?ac=group" class="edit_account" method="post">
							<input type="hidden" name="groupsubmit" value="true" />
							<input type="hidden" name="formhash" value="<?php echo formhash(); ?>" />
							<p>
								<input id="account_name" name="account_name" size="30" type="text" value="<?=$group['group_name']?>">
								<span class="submit">
									<input name="commit" type="submit" value="保存">
									<a href="#" data-behavior="account_name_cancel">取消</a>
								</span>
							</p>
						</form>        		
					</div>
					<p>创建于 <?php echo sgmdate('Y年m月d日', $group['logtime']);?></p>
				</section>

				<section class="charges">
					<header>
						<h2>目前的使用情况: </h2>
					</header>
					<p>
						目前的使用：<a href="group.php?do=account_project" class="decorated"><?=$group['project_num']?>个活跃项目</a>，<?=sizecountname($group['attachsize'])?>存储空间；
					</p>
				</section>

				<section class="account_owner">
					<header>
						<h2>帐户所有者</h2>
					</header>

					<p>该帐户的所有者是唯一的，可以访问该帐户页面，升级，改变计费信息，并取消帐户。一旦您进行修改，您将不再是帐户的所有者。</p>

					<form action="cp.php?ac=group" method="post">
						<input type="hidden" name="groupownersubmit" value="true" />
						<input type="hidden" name="formhash" value="<?php echo formhash(); ?>" />
						<p>
							<select id="owner_id" name="owner_id">
								<?php if($list) { ?>
								<?php if(is_array($list)) { foreach($list as $key => $value) { ?>
								<option value="<?=$value['uid']?>" <?php if($value['uid']==$group['uid']) { echo 'selected="selected"';}?>><?=$value['fullname']?></option>
								<?php } } } ?>
							</select>
							<input name="commit" type="submit" value="修 改">
						</p>
					</form>        
				</section>

				<!--<section class="cancel">
					<header>
						<h2>需要注消吗？</h2>
					</header>

					<p>一旦您进行注销， 30天之后，你的数据将被永久删除。</p>
					<p><a href="#" class="decorated" data-method="delete" data-stacker="false" rel="nofollow">注销</a></p>
				</section>-->
			</div>
		</div>
	</div>
</div>
<script type="text/javascript"> 
$(document).ready(function() { 
	$("[data-behavior~=account_name_link]").click(function() {
		$("[data-behavior~=account_name_header]").hide();
		$("[data-behavior~=account_name_form]").show();
	});
	$("[data-behavior~=account_name_cancel]").click(function() {
		$("[data-behavior~=account_name_header]").show();
		$("[data-behavior~=account_name_form]").hide();
	});
}); 
</script>
</body>
</html>