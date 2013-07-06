<?php if(!defined('IN_TEAMDOTA')) exit('Access Denied');?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$_SC['charset']?>" />
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
<meta name="robots" content="noarchive" />
<meta name="viewport" content="width=device-width, user-scalable=yes, initial-scale=1.0" />
<title>忘记密码了?</title>
<link rel="shortcut icon" href="http://www.teamdota.com/favicon.ico" />
<style type="text/css">

/*-------------------------------------------------
Responsive CSS
-------------------------------------------------*/

html {background: #eee;}

body {
  font-family: "宋体",helvetica, arial, sans-serif !important;
  margin: 0;
  padding: 0 30px;
}

div#container {
  margin: 0 auto;
  width: 510px;
  text-align: center;
}

.clearfix {display: inline-block;}
.clearfix {display: block;}

/*-------------------------------------------------
LOGIN
-------------------------------------------------*/

body.login {
  padding: 0;
  text-align: center;
  border: none;
  background: #eee;
}

body.login div#login_content {
  margin: 120px auto 20px auto;
  text-align: center;
  width: 100%;
  max-width: 988px;
}

body.login div#login_content_inner {
  text-align: center;
  width: 100%;
  max-width: 988px;
  padding-bottom: 20px;
}

body.login.teamdota div#login_content_inner{
  background: none !important;
}

body.login div.dialog_contents {
  padding: 6px 0;
}

body.login div.login_dialog {
  width: 210px;
  margin: 0 auto;
  background: #fff;
  padding: 10px 39px 19px;
  -webkit-border-radius: 8px;
  -moz-border-radius: 8px;
  border-radius: 8px;
  border: 1px solid #aaa;
  -moz-box-shadow: 0 0 6px #999; /* firefox 3.5+ */
        -webkit-box-shadow: 0 0 6px #999; /* webkit */
        box-shadow: 0 0 6px #999;
        -ms-filter: "progid:DXImageTransform.Microsoft.Shadow(Strength=3, Direction=135, Color='#999999')"; 
        filter: progid:DXImageTransform.Microsoft.Shadow(Strength=3, Direction=135, Color='#999999'); 
}

body:first-of-type.login div.login_dialog {border: none;}

*:first-child+html body.login div.login_dialog {
  padding: 25px 39px 25px;
}

body.login div#login_content.wide div.login_dialog {
  width: 435px;
  padding: 10px 29px 25px;
}

body.login div.login_dialog p {
  margin: 0 0 10px;
}

body.login div.extras {
  text-align: left;
  margin: 10px auto;
  width: 300px;
  text-align: center;
  color: #000;
}

body.login div.extras ul {
  margin: 0;
  padding: 0;
}

body.login div.extras ul li {
  list-style: none;
  font-size: 12px;
  margin-bottom: 3px;
}

*:first-child+html body.login div.extras ul li {
  margin-bottom: 0;
}

body.login div.extras a {
  color: #33a02c;
}

/*-------------------------------------------------
AMNESIA
-------------------------------------------------*/

body.login div.wide div.login_dialog {
  text-align: left;
}

body.login div.wide div.login_dialog h2 {
  font-size: 14px;
  font-weight: bold;
  margin: 15px 0 3px;
  color: #CC0000;
}

body.login div.wide div.login_dialog h3 {
  font-size: 12px;
  margin: 0 0 5px;
}

body.login div.wide div.login_dialog #email_address {
  font-size: 12px;
  padding: 3px;
  margin: 0 0 5px;
  width: 300px;
}

body.login div.wide div.login_dialog p {
  margin: 0 0 15px;
  font-size: 12px;
  line-height: 18px;
}

body.login div.wide div.login_dialog div.note {
  padding-top: 5px;
  border-top: 2px solid #33a02c;
  font-size: 12px;
  line-height: 15px;
  margin: 10px 0 0;
  color: #666;
}

body.login div.wide div.login_dialog div.note p {
  margin-bottom: 0;
  color: #333;
}

body.login div.wide div.login_dialog div.note h3 {
  font-size: 12px;
  line-height: 15px;
  margin: 0;
}

body.login div.wide div.login_dialog input.button {
  font-size: 12px;
  margin: 5px 0 10px;
  cursor:pointer;
  background-color: #339933;
  color: white;
  padding: 3px 8px;
}

/*-------------------------------------------------
Clearfix
-------------------------------------------------*/

.clearfix:after {
    content: ".";
    display: block;
    height: 0;
    clear: both;
    visibility: hidden;
}

.clearfix {display: inline-block;}
.clearfix {display: block;}

/*-------------------------------------------------
Responsive CSS
-------------------------------------------------*/

/* iPhone */
@media only screen and (max-device-width: 480px) {
  /* general */
  html, body.login {height: auto; min-height: 120%;}
    
  /* handle standard login screen */
  body.login div#signin_button {margin-top: 15px;}
  
  body.login { height: auto; background-position: left -1050px !important;}
  body.login div.container { padding: 10px 20px 0;}
  body.login div.login_dialog { padding: 10px 10px 25px 10px; }
  body.login div.login_dialog {width: auto;}
  body.login div#login_content, body.login div#login_content_inner { margin: 10px auto 0; background: none; }
  body.login div#other_products div {padding: 12px 0;}
  body.login div#other_products div li {white-space: normal; font-size: 12px;}
  body.login div#other_products {position: static; bottom: auto; left: auto; background: none; text-shadow: 0 1px 1px #fff; border-top: 1px dashed #ccc;}
  body.login div.extras ul li + li {margin-top: 7px;}
  body.login div.extras ul li {font-size: 12px; list-style: square outside; margin-left: 15px;}
  body.login div.extras { width: auto; padding: 0 15px; text-shadow: 0 1px 1px #fff; text-align: left;}
  
  /* wide dialogs */
  body.login div#login_content.wide div.login_dialog { width: auto !important;  }
  body.login div.wide div.login_dialog #email_address,
  body.login div.wide div.login_dialog h2 { font-size: 14px; line-height: normal; }
  body.login div.wide div.login_dialog h3 { font-size: 12px; line-height: normal; }
  body.login div.wide div.login_dialog p { font-size: 12px; line-height:1.4em; }
}

/* hide the product logos when the screen is very narrow */
@media only screen and (max-width: 540px) {
  body.login div#login_content, body.login div#login_content_inner { background: none !important; }
}

</style>
</head>
<body class="login">
<div class="container">
  <div id="login_content" class="wide ">
    <div id="login_content_inner">  
      <div class="dialog_contents">
        <div id="login_dialog" class="login_dialog clearfix">
          <div>
            <h2>无法登录? 忘记密码了?</h2>
<p>输入您的电子邮箱，我们会向您发送重置密码的信息。</p>


            <form action="do.php?ac=forgot_password" method="post"  id="new_forgot_password">
            <input type="hidden" name="emailsubmit" value="true" />
			<input type="hidden" name="formhash" value="<?php echo formhash(); ?>" />

              <h3>输入您的电子邮箱</h3>
              <input autocapitalize="off" autocorrect="off" id="email_address" name="email_address" type="email" /><br />

              <input class="button" name="commit" type="button" onclick="validateforgot_password(this);"  value="确 定" />
</form>
            <div class="note">
  <h3>关于垃圾邮件的说明：</h3>
  如果您没有收到我们发送的电子邮件，在几分钟之内，请务必检查您的垃圾邮件；
  我们发送邮件的邮箱为do-not-reply@teamdota.com。
</div>

          </div>        
        </div>
      </div>
      <div class="extras">
        <ul>
          <li><a href="/">返回首页</a></li>
        </ul>
      </div>
    </div>
  </div>

</div>
<script src="js/jquery.js" type="text/javascript"></script>
<script src="js/common.js" type="text/javascript"></script>
</body>
</html>
