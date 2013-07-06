var userAgent = navigator.userAgent.toLowerCase();
var is_opera = userAgent.indexOf('opera') != -1 && opera.version();
var is_moz = (navigator.product == 'Gecko') && userAgent.substr(userAgent.indexOf('firefox') + 8, 3);
var is_ie = (userAgent.indexOf('msie') != -1 && !is_opera) && userAgent.substr(userAgent.indexOf('msie') + 5, 3);
var is_safari = (userAgent.indexOf('webkit') != -1 || userAgent.indexOf('safari') != -1);
var note_oldtitle = document.title;
var loading = "<img src='image/dots-white-3483b69ff7c295c43d7d54acd612dab4.gif'>";
$(document).ready(function () {
	$('[placeholder]').focus(function () {
		var input = $(this);
		if (input.val() == input.attr('placeholder')) {
			input.val('');
			input.removeClass('placeholder');
		}
	}).blur(function () {
		 var input = $(this);
		if (input.val() == '' || input.val() == input.attr('placeholder')) {
			input.addClass('placeholder');
			input.val(input.attr('placeholder'));
		}
	}).blur();
});
function cnCode(str) {
	return is_ie && document.charset == 'utf-8' ? encodeURIComponent(str) : str;
}

function isUndefined(variable) {
	return typeof variable == 'undefined' ? true : false;
}

function strlen(str) {
	return (is_ie && str.indexOf('\n') != -1) ? str.replace(/\r?\n/g, '_').length : str.length;
}

function checkFocus(target) {
	var obj = document.getElementById(target);
	if(!obj.hasfocus) {
		obj.focus();
	}
}

function trim(str) { 
	var re = /\s*(\S[^\0]*\S)\s*/; 
	re.exec(str); 
	return RegExp.$1; 
}
function validateEmail(emailaddress){  
   var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
   if(!emailReg.test(emailaddress)) {
        return false;
	}
	return true;
}
function validateinvite(obj) {
	var ischeck = true;
	var fullname = trim($("#fullname").val());
	var password = $("#password").val();
	var password_confirmation = $("#password_confirmation").val();
	if (strlen(fullname) < 2) {
		$("#div_validated_fullname").addClass("invalid");
		$("#p_error_fullname").html("请输入您的姓名");
		ischeck = false;
	} else {
		$("#div_validated_fullname").removeClass("invalid");
		$("#p_error_fullname").html("");
	}
	if (strlen(password) < 6) {
		$("#div_validated_password").addClass("invalid");
		$("#p_error_password").html("出于安全考虑，密码至少6个字符");
		ischeck = false;
	} else {
		$("#div_validated_password").removeClass("invalid");
		$("#p_error_password").html("");
	}
	if (strlen(password_confirmation) < 6) {
		$("#div_validated_password_confirmation").addClass("invalid");
		$("#p_error_password_confirmation").html("出于安全考虑，密码至少6个字符");
		ischeck = false;
	} else {
		if(password != password_confirmation) {
			$("#div_validated_password_confirmation").addClass("invalid");
			$("#p_error_password_confirmation").html("与登陆密码不匹配，请您重新输入");
			ischeck = false;
		} else {
			$("#div_validated_password_confirmation").removeClass("invalid");
			$("#p_error_password_confirmation").html("");
		}
	}
	if(ischeck){
		obj.form.submit();
	}
	return false;
}
function email_onblur(object) {
	emailaddress = $(object).val();
	if(emailaddress != ""){
		if(!validateEmail(emailaddress)) {
			$("#div_validated_email").addClass("invalid");
			$("#p_error_email").html("输入的电子邮箱格式有误");
			ischeck = false;
		} else {
			$.ajax({
				type: "get",
				url: 'do.php?ac=ajax&op=emailcheck&email=' + emailaddress,
				data: { "rand":Math.random() },
				success: function(result) {
					if(result == '-1') {
						$("#div_validated_email").addClass("invalid");
						$("#p_error_email").html("输入的电子邮箱格式有误");
					} else if(result == '-2') {
						$("#div_validated_email").addClass("invalid");
						$("#p_error_email").html("输入的电子邮箱已存在");
					} else {
						$("#div_validated_email").removeClass("invalid");
						$("#p_error_email").html("");
					}
				}
			});
		}
	}
}
function validateregister(obj) {
	var ischeck = true;
	var fullname = trim($("#fullname").val());
	var group_name = trim($("#group_name").val());
	var email = trim($("#email").val());
	var password = $("#password").val();
	if (strlen(fullname) < 2) {
		$("#div_validated_fullname").addClass("invalid");
		$("#p_error_fullname").html("请输入您的姓名");
		ischeck = false;
	} else {
		$("#div_validated_fullname").removeClass("invalid");
		$("#p_error_fullname").html("");
	}
	if (strlen(group_name) < 1) {
		$("#div_validated_group_name").addClass("invalid");
		$("#p_error_group_name").html("请输入公司或组织名称");
		ischeck = false;
	} else {
		$("#div_validated_group_name").removeClass("invalid");
		$("#p_error_group_name").html("");
	}
	if(strlen(email) < 1) {
		$("#div_validated_email").addClass("invalid");
		$("#p_error_email").html("请输入电子邮箱");
		ischeck = false;
	}else if(!validateEmail(email)) {
		$("#div_validated_email").addClass("invalid");
		$("#p_error_email").html("输入的电子邮箱格式有误");
		ischeck = false;
	} else {
		$("#div_validated_email").removeClass("invalid");
		$("#p_error_email").html("");
	}
	if (strlen(password) < 6) {
		$("#div_validated_password").addClass("invalid");
		$("#p_error_password").html("出于安全考虑，密码至少6个字符");
		ischeck = false;
	} else {
		$("#div_validated_password").removeClass("invalid");
		$("#p_error_password").html("");
	}
	if(ischeck){
		obj.form.submit();
	}
	return false;
}
function validatelogin(obj) {
	var ischeck = true;
	var username = trim($("#username").val());
	var password = $("#password").val();
	$("#div_global_err").hide();
	if(strlen(username) < 1) {
		$("#div_validated_email").addClass("invalid");
		$("#p_error_email").html("请输入电子邮箱");
		ischeck = false;
	}else if(!validateEmail(username)) {
		$("#div_validated_email").addClass("invalid");
		$("#p_error_email").html("输入的电子邮箱格式有误");
		ischeck = false;
	} else {
		$("#div_validated_email").removeClass("invalid");
		$("#p_error_email").html("");
	}
	if (strlen(password) < 6) {
		$("#div_validated_password").addClass("invalid");
		$("#p_error_password").html("出于安全考虑，密码至少6个字符");
		ischeck = false;
	} else {
		$("#div_validated_password").removeClass("invalid");
		$("#p_error_password").html("");
	}
	if(ischeck){
		obj.form.submit();
	}
	return false;
}
function validatelogin_main(obj) {
	var ischeck = true;
	var username = trim($("#username").val());
	var password = $("#password").val();
	if(strlen(username) < 1) {
		alert("请输入电子邮箱");
		$("#username").focus();
		ischeck = false;
	}else if(!validateEmail(username)) {
		alert("输入的电子邮箱格式有误");
		$("#username").focus();
		ischeck = false;
	}else if (strlen(password) < 6) {
		alert("出于安全考虑，密码至少6个字符");
		$("#password").focus();
		ischeck = false;
	}
	if(ischeck){
		obj.form.submit();
	}
	return false;
}
function validateforgot_password(obj) {
	var ischeck = true;
	var email_address = trim($("#email_address").val());
	if(strlen(email_address) < 1) {
		alert("请输入电子邮箱");
		$("#email_address").focus();
		ischeck = false;
	}else if(!validateEmail(email_address)) {
		alert("输入的电子邮箱格式有误");
		$("#email_address").focus();
		ischeck = false;
	}
	if(ischeck){
		obj.form.submit();
	}
	return false;
}
function validatepassword_reset(obj) {
	var ischeck = true;
	var password = $("#password").val();
	var password_confirmation = $("#password_confirmation").val();
	if (strlen(password) < 6) {
		$("#div_validated_password").addClass("invalid");
		$("#p_error_password").html("出于安全考虑，密码至少6个字符");
		ischeck = false;
	} else {
		$("#div_validated_password").removeClass("invalid");
		$("#p_error_password").html("");
	}
	if (strlen(password_confirmation) < 6) {
		$("#div_validated_password_confirmation").addClass("invalid");
		$("#p_error_password_confirmation").html("出于安全考虑，密码至少6个字符");
		ischeck = false;
	} else {
		if(password != password_confirmation) {
			$("#div_validated_password_confirmation").addClass("invalid");
			$("#p_error_password_confirmation").html("与密码不匹配，请您重新输入");
			ischeck = false;
		} else {
			$("#div_validated_password_confirmation").removeClass("invalid");
			$("#p_error_password_confirmation").html("");
		}
	}
	if(ischeck){
		obj.form.submit();
	}
	return false;
}
function validateedit(obj) {
	var ischeck = true;
	var fullname = trim($("#fullname").val());
	var password = $("#password").val();
	var password_confirmation = $("#password_confirmation").val();
	if (strlen(fullname) < 2) {
		$("#div_validated_fullname").addClass("invalid");
		$("#p_error_fullname").html("请输入您的姓名");
		ischeck = false;
	} else {
		$("#div_validated_fullname").removeClass("invalid");
		$("#p_error_fullname").html("");
	}
	if (strlen(password) > 1) {
		if (strlen(password) < 6) {
			$("#div_validated_password").addClass("invalid");
			$("#p_error_password").html("出于安全考虑，密码至少6个字符");
			ischeck = false;
		} else {
			$("#div_validated_password").removeClass("invalid");
			$("#p_error_password").html("");
		}
		if (strlen(password_confirmation) < 6) {
			$("#div_validated_password_confirmation").addClass("invalid");
			$("#p_error_password_confirmation").html("出于安全考虑，密码至少6个字符");
			ischeck = false;
		} else {
			if(password != password_confirmation) {
				$("#div_validated_password_confirmation").addClass("invalid");
				$("#p_error_password_confirmation").html("与登陆密码不匹配，请您重新输入");
				ischeck = false;
			} else {
				$("#div_validated_password_confirmation").removeClass("invalid");
				$("#p_error_password_confirmation").html("");
			}
		}
	}
	if(ischeck){
		obj.form.submit();
	}
	return false;
}