<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Teamdota: 修改个人信息</title>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$_SC['charset']?>">
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7">
<meta name="viewport" content="width=1024">
<link href="css/common.css" media="screen" rel="stylesheet" type="text/css">
<link rel="shortcut icon" href="http://www.teamdota.com/favicon.ico" />
<script src="js/jquery.js" type="text/javascript"></script>
<script src="js/common.js" type="text/javascript"></script>
</head>
<body class="edit_identity identity_validation edit_only">
	<div id="container" class="clearfix">
		<div id="main">
			<div class="panel">
				<div class="page_header">
					<h2>修改个人信息</h2>
				</div>
  
				<form action="cp.php?ac=people" class="identity_form" enctype="multipart/form-data" id="new_edit" method="post">
				<input type="hidden" name="peoplesubmit" value="true" />
				<input type="hidden" name="formhash" value="<?php echo formhash(); ?>" />
				<table>
					<tbody>
						<tr class="avatar">
						  <th><? echo avatar($_SGLOBAL['supe_uid'],'55');?></th>
						  <td>
							<div class="change_avatar">
							  <p><label>上传头像(限制500KB以内,类型jpg/gif/png)</label></p>
							  <p><input id="signal_id_identity_avatar" name="signal_id_identity_avatar" type="file"></p>
							</div>
						  </td>
						</tr>
						<tr>
						  <th><label for="signal_id_identity_email">电子邮箱</label></th>
						  <td>
							<div class="validated_field" id="div_validated_email">
							  <p class="field"><input id="email" name="email" size="30" value="<?= $_SGLOBAL['member']['email']?>" tabindex="1" type="text" disabled="disabled"></p>
							  <p class="error" id="p_error_email"></p>
							</div>
						  </td>
						</tr>
						<tr>
						  <th><label for="signal_id_identity_last_name">您的姓名</label></th>
						  <td>
							<div class="validated_field" id="div_validated_fullname">
							  <p class="field"><input id="fullname" name="fullname" size="30" tabindex="2" type="text" value="<?= $_SGLOBAL['member']['fullname']?>"></p>
							  <p class="error" id="p_error_fullname"></p>
							</div>
						  </td>
						</tr>
					</tbody>
					<tbody>
						<tr class="username_password">
							<th><label for="signal_id_identity_password">密码</label></th>
							<td>
							  <div class="validated_field" id="div_validated_password">
								<p class="field"><input class="dummy" id="password" name="password" size="30" tabindex="3" type="password" data-dummy="true"></p>
								<p class="hint">密码至少6个字符，区分大小写，可以使用字母、数字或特殊字符。</p>
								<p class="error" id="p_error_password"></p>
							  </div>
							</td>
						</tr>
						<tr class="username_password confirm_password">
							<th><label for="signal_id_identity_password_confirmation">确认密码</label></th>
							<td>
							  <div class="validated_field" id="div_validated_password_confirmation">
								<p class="field"><input class="dummy" id="password_confirmation" name="password_confirmation" size="30" tabindex="4" type="password" data-dummy="true"></p>
								<p class="error" id="p_error_password_confirmation"></p>
							  </div>
							</td>
						</tr>
					</tbody>
					<tbody>
						<tr class="submit">
							<th></th>
							<td>
								<input type="button" onclick="validateedit(this);" value="保 存"> <a href="group.php?do=people&uid=<?=$_SGLOBAL['supe_uid']?>" class="admin">取消</a>
							</td>
						</tr>
					</tbody>
				</table>
			</form>
		</div>
	</div>
</div>
</body>
</html>