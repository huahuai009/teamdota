<?php if(!defined('IN_TEAMDOTA')) exit('Access Denied');?>
<!DOCTYPE html>
<html>
<head>
<title>TeamDota 设置</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link href="css/application.css" media="all" rel="stylesheet" type="text/css">
<link rel="shortcut icon" href="http://www.teamdota.com/favicon.ico" />
<meta charset="utf-8">
<meta name="viewport" content="width=1100">
<style type="text/css">div.panel { min-height: 683px };</style>
<script type="text/javascript">
    window.bbcx = {currentGroup: "<?=$group['group_id']?>",currentPerson: "<?=$_SGLOBAL['supe_uid']?>",currentProject: "0",currentDo: "<?=$ac?>"};
</script>
<script src="js/jquery.js" type="text/javascript"></script>
<script src="js/global.js" type="text/javascript"></script>
</head>
<body class="windows">
<?php $position_hotkey = 3 ; include_once template("head"); ?>
<div id="workspace" class="workspace">
	<div style="width: 980px;" class="container stack_container">
		
		
		<div class="panel sheet_view inactive person_projects">
			<header><h1><a href="group.php?do=people&uid=<?=$_SGLOBAL['supe_uid']?>"><?=$_SGLOBAL['member']['fullname'];?></a></h1></header>
			<div class="sheet_body_view">

				<section>
					<header>
					  <h2>订阅每日更新邮件</h2>
					</header>
					<table>
						<tr>
							<th>
								<?php if($_SGLOBAL['member']['issubscribe'] == 0) { ?>
								<input checked="checked" id="person_subscribed_to_global_summary" name="person[subscribed_to_global_summary]" type="checkbox" value="1" onclick="people_setting(this)">
								<?php } else { ?>
								<input id="person_subscribed_to_global_summary" name="person[subscribed_to_global_summary]" type="checkbox" value="0" onclick="people_setting(this)">
								<?php } ?>
							</th>
							<td>
						  每天早上，我们将会把昨天的更新通过邮件发送给您。
							</td>
						</tr>
					</table>
				</section>

			</div>
		</div>
	
	</div>
</div>
</body>
</html>