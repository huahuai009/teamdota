<?php if(!defined('IN_TEAMDOTA')) exit('Access Denied');?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Teamdota，异地在线协作软件，打破企业内和合作伙伴间的孤岛</title>
<meta charset="utf-8">
<meta name="keywords" content="teamdota,异地合作,在线协作,项目管理,项目管理软件,oa" />
<meta name="description" content="Teamdota是领先的基于网络的项目管理和协作工具，我们追求简单、沟通和信任。">
<link rel="shortcut icon" href="http://www.teamdota.com/favicon.ico" />
<meta name="viewport" content="width=1000">
<meta http-equiv="X-UA-Compatible" content="chrome=1">
<link rel="stylesheet" type="text/css" href="css/main.css?v=20120719">
<script src="js/jquery.js" type="text/javascript"></script>
<script src="js/common.js" type="text/javascript"></script>
</head>
<body class="statement">
<div class="container">
	<nav>
		<a href="/" class="logo">Teamdota</a>
	</nav>
    <header>
		<div class="supply sheet">
			<aside>TEAMDOTA给您提供，项目，文件，讨论，反馈等团队合作服务</aside>
		</div>
		<form action="do.php?ac=login" class="identity_form" id="new_register" method="post" onsubmit="return validatelogin_main(this);">
		<input type="hidden" name="loginsubmit" value="true" />
		<input type="hidden" name="formhash" value="<?php echo formhash(); ?>" />
		<input type="hidden" name="refer" value="<?=$refer?>" />
		<div class="login sheet">
			<span class="triangle"></span>
			<p class="login_email">
				<input id="username" name="username" placeholder="电子邮箱" size="100" tabindex="1" value="" type="text">
			</p>
			<p class="login_password">
				<input id="password" name="password" placeholder="登陆密码" size="30" tabindex="2" type="password">
			</p>
			<p class="login_submit">
				<input class="submit" type="submit" value="登 录">
				<span class="remember_container">
				<input type="radio" id="cookietime" name="cookietime" value="315360000" tabindex="3" /><label for="remember_me">记住登录状态</label>
				</span>
			</p>
			<p class="login_forget">
				<a href="do.php?ac=forgot_password">忘记密码了？</a>
			</p>
			<p class="login_reg">
				没有Teamdota的帐户？<a href="do.php?ac=register">开始注册</a>
			</p>
		</div>
		</form>
    </header>
	<div class="descript">
		<a href="do.php?ac=register"><div class="price">永久免费</div></a>
		<a href="do.php?ac=register"><div class="register">立即注册</div></a>
		<a href="http://weibo.com/teamdota" target="_blank"><div class="weibo">官方微博 http://weibo.com/teamdota</div></a>
	</div>
    <footer>
		<p>TEAMDOTA©2012 &nbsp; <a href="#" target="_blank" class="about">关于</a> &nbsp;<a href="#" target="_blank" class="safe">安全</a> &nbsp;<a href="/help/privacy.html" target="_blank" class="privacy">隐私</a> &nbsp;<a href="/help/tos.html" target="_blank" class="provision">使用条款</a> &nbsp;<a href="mailto:teamdota@126.com" target="_blank" class="contact">联系我们</a> &nbsp;<a href="http://blog.teamdota.com" target="_blank" class="blog">博客</a>  &nbsp;<!--浙ICP备05009163号-->
		</p>
	</footer>
</div>
<a class="bshareDiv" href="http://www.bshare.cn/share">分享按钮</a><script type="text/javascript" charset="utf-8" src="http://static.bshare.cn/b/buttonLite.js#uuid=fad78aaf-ed70-4398-8a01-45e4ef8f8f7c&amp;style=4&amp;fs=4&amp;bgcolor=Green"></script>
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-18052056-2']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
</body>
</html>