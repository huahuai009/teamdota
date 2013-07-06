<!DOCTYPE html>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<title>开始使用TeamDota</title>
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
				<h1>开始使用TeamDota</h1>
				<p>如果您已经设置了帐户，您可以<a href="do.php?ac=login">直接登录</a>
				</p>
				
				<form action="<?=$theurl?>" class="identity_form" id="new_identity" method="post">
				<div style="margin:0;padding:0;display:inline">
				<input type="hidden" name="invitesubmit" value="true" />
				<input type="hidden" name="formhash" value="<?php echo formhash(); ?>" />

				  <div class="pick">
					
					<div class="validated_field" id="div_validated_username">
					  <p class="">
						<input autocomplete="off" id="username" name="username" placeholder="用户名" size="30" value="<?=$invite['email']?>" type="text" disabled="disabled">
					  </p>
					  <p class="error" id="p_error_username"></p>
					</div>
					
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
					
					<div class="validated_field" id="div_validated_password">
					  <p class="">
						<input id="password" name="password" placeholder="登陆密码" size="30" tabindex="2" type="password">
					  </p>
					  <p class="error" id="p_error_password"></p>
					</div>

					<div class="validated_field" id="div_validated_password_confirmation">
					  <p class="">
						<input id="password_confirmation" name="password_confirmation" placeholder="确认密码" size="30" tabindex="3" type="password">
					  </p>
					  <p class="hint">密码至少6个字符，区分大小写，可以使用字母、数字或特殊字符</p>
					  <p class="error" id="p_error_password_confirmation"></p>
					</div>

					<div class="submit">
					  <p>
						<input class="submit" type="button" onclick="validateinvite(this);" value="开始使用">
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