<?php if(!defined('IN_TEAMDOTA')) exit('Access Denied');?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=<?=$_SC['charset']?>">
<title>Teamdota: 修改个人信息</title>
<link href="css/common.css" media="screen" rel="stylesheet" type="text/css">
<link rel="shortcut icon" href="http://www.teamdota.com/favicon.ico" />
<script src="js/jquery.js" type="text/javascript"></script>
<script src="js/common.js" type="text/javascript"></script>
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
				<h1>修改个人信息</h1>
				</p>
				
				<form action="cp.php?ac=people" class="identity_form" id="new_edit" method="post">
				<div style="margin:0;padding:0;display:inline">
				<input type="hidden" name="peoplesubmit" value="true" />
				<input type="hidden" name="formhash" value="<?php echo formhash(); ?>" />

				  <div class="pick">
					
					<div class="validated_field" id="div_validated_email">
					  <p class="">
						<input autocomplete="off" id="email" name="email" placeholder="电子邮箱" size="100" value="<?= $_SGLOBAL['member']['email']?>" type="text" disabled="disabled">
					  </p>
					  <p class="error" id="p_error_email"></p>
					</div>
					
					<div class="validated_field" id="div_validated_fullname">
					  <table border="0" cellpadding="0" cellspacing="0">
						<tbody><tr>
						  <td class="first_name">
							<p class="">
							  <input autocomplete="off" id="fullname" name="fullname" placeholder="您的姓名" size="30" tabindex="1" type="text" value="<?= $_SGLOBAL['member']['fullname']?>">
							</p>
							<p class="error" id="p_error_fullname"></p>
						  </td>
						</tr>
					  </tbody></table>
					</div>
					
					<div class="validated_field" id="div_validated_password">
					  <p class="">
						<input id="password" name="password" placeholder="密码" size="30" tabindex="2" type="password">
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
						<input class="submit" type="button" onclick="validateedit(this);" value="提 交"> <a href="group.php?do=people&uid=<?=$_SGLOBAL['supe_uid']?>" class="cancel">取消</a>
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