<?php if(!defined('IN_TEAMDOTA')) exit('Access Denied');?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=<?=$_SC['charset']?>">
<title>Teamdota: 永久免费使用</title>
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
				<h1>永久免费使用Teamdota</h1>
				<p style="padding-top:10px;">如果您已经设置了帐户，您可以<a href="do.php?ac=login">直接登录</a>。
				</p>
				
				<form action="do.php?ac=register" class="identity_form" id="new_register" method="post" onsubmit="return validateregister(this);">
				<div style="margin:0;padding:0;display:inline">
				<input type="hidden" name="registersubmit" value="true" />
				<input type="hidden" name="formhash" value="<?php echo formhash(); ?>" />

				  <div class="pick">
					
					<div class="validated_field" id="div_validated_fullname">
					  <table border="0" cellpadding="0" cellspacing="0">
						<tbody><tr>
						  <td class="first_name">
							<p class="">
							  <input autocomplete="off" id="fullname" name="fullname" placeholder="您的姓名" size="30" tabindex="1" type="text">
							</p>
							<p class="error" id="p_error_fullname"></p>
						  </td>
						</tr>
					  </tbody></table>
					</div>
					
					<div class="validated_field" id="div_validated_group_name">
					  <p class="">
						<input autocomplete="off" id="group_name" name="group_name" placeholder="公司或组织名称" size="30" tabindex="2" value="" type="text" >
					  </p>
					  <p class="error" id="p_error_group_name"></p>
					</div>
					
					<div class="validated_field" id="div_validated_email">
					  <p class="">
						<input autocomplete="off" id="email" name="email" placeholder="电子邮箱" size="100" tabindex="3" value="" type="text" onblur="email_onblur(this);">
					  </p>
					  <p class="error" id="p_error_email"></p>
					</div>
					
					<div class="validated_field" id="div_validated_password">
					  <p class="">
						<input id="password" name="password" placeholder="登陆密码" size="30" tabindex="4" type="password">
					  </p>
					   <p class="hint">密码至少6个字符，区分大小写，可以使用字母、数字或特殊字符</p>
					  <p class="error" id="p_error_password"></p>
					</div>
					
					<div class="submit">
					  <p>
						<input class="submit" type="submit" value="立即注册">
					  </p>
					  <p><br>点击“立即注册”按钮表示您已知悉并同意遵守<a href="/help/tos.html" target="_blank">Teamdota服务条款</a></p>
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