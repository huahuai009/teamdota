<?php if(!defined('IN_TEAMDOTA')) exit('Access Denied');?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=<?=$_SC['charset']?>">
<title>登录Teamdota</title>
<meta name="keywords" content="teamdota,没有任务监控,在线协作,没有任务监控的项目管理软件,项目,项目软件,项目管理软件" />
<meta name="description" content="TeamDota是领先的基于网络的项目管理和协作工具，信任，沟通百分百。">
<link href="css/common.css" media="screen" rel="stylesheet" type="text/css">
<script src="js/jquery.js" type="text/javascript"></script>
<script src="js/common.js" type="text/javascript"></script>
<link rel="shortcut icon" href="http://www.teamdota.com/favicon.ico" />
<!--[if lte IE 9]>
<style type="text/css" media="screen">
    body.rsvp.new div.validated_field { padding: 6px 0; }
</style>
<![endif]-->
</head>

<body class="identifications rsvp new simple_form identity_validation">
<div class="wrapper">
    <div class="container">
		<div class="extra_space_for_a_sidebar">
			<div class="col">
				<div class="banner">
					<h1>登陆Teamdota</h1>
					<p style="padding:10px 0;">如果您还没有帐户，<a href="do.php?ac=register">立即注册</a>，永久免费使用Teamdota。
					</p>
					<?php if($errmessage != '') { ?>
						<div class="chkdiv" id="div_global_err"><?=$errmessage;?></div>
					<?php } ?>
					<form action="do.php?ac=login" class="identity_form" id="new_register" method="post" onsubmit="return validatelogin(this);">
					<input type="hidden" name="loginsubmit" value="true" />
					<input type="hidden" name="formhash" value="<?php echo formhash(); ?>" />
					<input type="hidden" name="refer" value="<?=$refer?>" />
					<div class="pick">
						
						<div class="validated_field" id="div_validated_email">
						  <p class="">
							<input id="username" name="username" placeholder="电子邮箱" size="100" tabindex="1" value="<?=$username;?>" type="text">
						  </p>
						  <p class="error" id="p_error_email"></p>
						</div>
						
						<div class="validated_field" id="div_validated_password">
						  <p class="">
							<input id="password" name="password" placeholder="登陆密码" size="30" tabindex="2" type="password">
						  </p>
						  <p class="error" id="p_error_password"></p>
						</div>
						
						<div class="remember_container">
							<input id="cookietime" type="checkbox" value="315360000" name="cookietime" tabindex="3" <?=$cookiecheck?>>
							<label for="remember_me">记住登录状态</label>
							<a href="do.php?ac=forgot_password">忘记密码了？</a>
						</div>
						
						<div class="submit">
						  <p>
							<input class="submit" type="submit" value="登 录">
						  </p>
						</div>
					</div>
					</form>
				</div>
			</div>
			<div class="footer">
			</div>
		</div>
	</div>
</div>
</body>
</html>