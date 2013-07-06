<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>TeamDota</title>
<link href="css/application.css" media="screen" rel="stylesheet" type="text/css">
<meta name="viewport" content="width=device-width"> 
<meta charset="utf-8">
<link rel="shortcut icon" href="http://www.teamdota.com/favicon.ico" />
<style type="text/css">div.panel { min-height: 893px };</style>
</head>
<body class="minimal windows" style="BACKGROUND: #e5e5e5;">
<div id="workspace" class="workspace_minimal">
	<div class="container flat_container" data-container-id="1">
		<div class="panel sheet_mini mini" data-behavior="unsubscribed">
			<header>
				<h2>您已经退订此消息</h2>
			</header>
			<div class="sheet_body_mini">
				<?php if($do == 'dialy') { ?>
				<h4>每天早上，您将不再收到电子邮件通知。</h4>
				<?php } else { ?>
				<h4>发布新的评论时，您将不再收到电子邮件通知。</h4>
				<?php } ?>
			</div>
		</div>
	</div>
</div>
</body>
</html>