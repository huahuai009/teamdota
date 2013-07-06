<!DOCTYPE html>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<title>重置您的密码</title>
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
				<h1>重置您的TeamDota密码</h1>
				<p>请您设置新的密码
				</p>
				
				<form action="<?=$theurl?>" class="identity_form" id="new_identity" method="post">
				<div style="margin:0;padding:0;display:inline">
				<input type="hidden" name="resetsubmit" value="true" />
				<input type="hidden" name="formhash" value="<?php echo formhash(); ?>" />

				  <div class="pick">
					
					<div class="validated_field" id="div_validated_username">
					  <p class="">
						<input autocomplete="off" id="username" name="username" placeholder="用户名" size="30" value="<?=$log['email']?>" type="text" disabled="disabled">
					  </p>
					  <p class="error" id="p_error_username"></p>
					</div>
					
					<div class="validated_field" id="div_validated_password">
					  <p class="">
						<input id="password" name="password" placeholder="输入新密码" size="30" tabindex="2" type="password">
					  </p>
					  <p class="error" id="p_error_password"></p>
					</div>

					<div class="validated_field" id="div_validated_password_confirmation">
					  <p class="">
						<input id="password_confirmation" name="password_confirmation" placeholder="确认新密码" size="30" tabindex="3" type="password">
					  </p>
					  <p class="hint">密码至少6个字符，区分大小写，可以使用字母、数字或特殊字符</p>
					  <p class="error" id="p_error_password_confirmation"></p>
					</div>

					<div class="submit">
					  <p>
						<input class="submit" type="button" onclick="validatepassword_reset(this);" value="确认重置">
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