<?php if(!defined('IN_TEAMDOTA')) exit('Access Denied');?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>teamdota提示</title>
<link rel="shortcut icon" href="http://www.teamdota.com/favicon.ico" />
<style type="text/css">
html {
  background: #eee;
}
body {
  margin: 0;
  padding: 120px 15px;
  font-family: "宋体", verdana, arial, helvetica, sans-serif;
  color: #222;
}
div.dialog {
  background: #fff;
  max-width: 440px;
  margin: 0 auto;
  -webkit-border-radius: 8px; -moz-border-radius: 8px; border-radius: 8px;
  -webkit-box-shadow: 0 0 6px #999; -moz-box-shadow: 0 0 6px #999; box-shadow: 0 0 6px #999;
  border: 1px solid #ccc;
}
body:first-of-type div.dialog {border: none;}
div.innercol {padding: 15px 30px;}
div.innercol h2 {
  font-size: 12px;
  font-weight: bold;
  margin: 15px 0 15px;
  color: #CC0000;
}
p {font-size: 12px; margin: 0 0 20px; line-height: 22px;}
li {font-size: 12px; list-style-type: square; margin-bottom: 6px;}
h2 em {text-decoration: underline; font-style: normal;}
p.back {margin: 18px 0 0;text-align: center;}
a, a:link, a:visited {text-decoration: none; padding: 1px; color: #52b149;}
a:hover {color: #52b149;text-decoration: underline;}
p.back a {
  color: #000;
  text-decoration: none;
  display: inline-block;
  padding: 8px 12px;
  border: 2px solid #ccc;
  background: #eee;
  background: -moz-linear-gradient(top, #fff 0%, #eee 70%);
  background: -webkit-gradient(linear, left top, left bottom, color-stop(0, #fff), color-stop(0.7, #eee));
  -webkit-border-radius: 18px; -moz-border-radius: 18px; border-radius: 18px;
}
p.back a:hover {
  background: #fff;
  background: -moz-linear-gradient(top, #ddd 0%, #fff 70%);
  background: -webkit-gradient(linear, left top, left bottom, color-stop(0, #ddd), color-stop(0.7, #fff));
}
@media screen and (max-width: 400px) {
  body {padding: 0;}
  div.dialog {margin: 0; border: none; -webkit-border-radius: 0; -moz-border-radius: 10; border-radius: 0;}
}
</style>
</head>
<body>
<div class="dialog">
    <div class="innercol">
		<h2>Teamdota提示信息：</h2>
		<p><?=$message?>
		<?php if($url_forward) { ?>
		<br />
		<br />系统将会在<b><span id='jumptme'></span></b>秒后自动跳转。
		<br /><a href="<?=$url_forward?>">如果您的浏览器没反应，请点击这里...</a>
		<script>
		var speed = 1000;
		var wait = <?=$second?>;
		function JumpUrl(){
			document.getElementById('jumptme').innerHTML = wait;
			wait--;
			if(wait < 0){ location = '<?=$url_forward?>';}
			else{window.setTimeout("JumpUrl()",speed);}
		}
		JumpUrl();
		</script>
		<?php }	 ?>
		</p>
    </div>
</div>
<p class="back">
	<a href="javascript:window.history.back();">← 返回上一页</a>
</p>
</body>
</html>
<?php ob_out();?>