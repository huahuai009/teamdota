<?php if(!defined('IN_TEAMDOTA')) exit('Access Denied');?>
<!DOCTYPE html>
<html>
<head>
<title>成员权限分配</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link href="css/application.css" media="all" rel="stylesheet" type="text/css">
<link rel="shortcut icon" href="http://www.teamdota.com/favicon.ico" />
<meta charset="utf-8">
<meta name="viewport" content="width=1100">
<style type="text/css">div.panel { min-height: 886px };</style>
<script type="text/javascript">
    window.bbcx = {currentGroup: "<?=$group['group_id']?>",currentPerson: "<?=$_SGLOBAL['supe_uid']?>",currentProject: "0",currentDo: "<?=$ac?>"};
</script>
<script src="js/jquery.js" type="text/javascript"></script>
<script src="js/global.js" type="text/javascript"></script>
</head>
<body class="windows">
<?php $position_hotkey = 2 ; include_once template("head"); ?>
<div id="workspace" class="workspace">
	<div style="width: 980px;" class="container stack_container">
		<div class="panel sheet_view permissions inactive has_sidebar ">
			<title>成员权限分配</title>
			<header>
				<h1>成员权限分配</h1>
			</header>
			<div class="sheet_body_view">
				<table class="header">
					<tbody>
						<tr>
							<td></td>
							<td class="option admin">管理员</td>
							<td class="option projects">可以创建项目？</td>
						</tr>
					</tbody>
				</table>
				<?php if($list) { ?>
				<?php if(is_array($list)) { foreach($list as $key => $value) { ?>
				<table>
					<tbody>
						<tr>
							<td>
							<a href="#<?=$value['uid']?>" data-replace-stack="true"><img class="avatar" height="42" src="image/icon_topic.jpg" title="<?=$value['fullname']?>" width="42"></a>
							<b><a href="#<?=$value['uid']?>"><?=$value['fullname']?></a></b>
							</td>

							<td class="option admin">
								<?php if($value['ntype']==0) { ?>
								<input id="permissions_admin_<?=$value['uid']?>" name="permissions[admin]" type="checkbox" value="0" onclick="permissions(0,this,<?=$value['uid']?>)">
								<?php } elseif($value['ntype']==1 || $_SGLOBAL['supe_uid'] == $value['uid']) { ?>
								<input checked="checked" disabled="disabled" id="permissions_admin_<?=$value['uid']?>" name="permissions[admin]" type="checkbox" value="1"  onclick="permissions(0,this,<?=$value['uid']?>)">
								<?php } else { ?>
								<input checked="checked" id="permissions_admin_<?=$value['uid']?>" name="permissions[admin]" type="checkbox" value="1"  onclick="permissions(0,this,<?=$value['uid']?>)">
								<?php } ?>
							</td>

							<td class="option projects">
								<?php if($value['ntype'] > 0) { ?>
								<input checked="checked" disabled="disabled" id="permissions_can_create_projects_<?=$value['uid']?>" name="permissions[can_create_projects]" type="checkbox" value="1" onclick="permissions(1,this,<?=$value['uid']?>)">
								<?php } elseif($value['is_create_project'] > 0) { ?>
								<input checked="checked" id="permissions_can_create_projects_<?=$value['uid']?>" name="permissions[can_create_projects]" type="checkbox" value="1" onclick="permissions(1,this,<?=$value['uid']?>)">
								<?php } else { ?>
								<input id="permissions_can_create_projects_<?=$value['uid']?>" name="permissions[can_create_projects]" type="checkbox" value="0" onclick="permissions(1,this,<?=$value['uid']?>)">
								<?php } ?>
							</td>
						</tr>
					</tbody>
				</table>
				<?php } } } ?>
				<?=$pagenumbers?>
			</div>
			<aside>
				<h4>说明</h4>

				<h5>管理员</h5>
				<p>管理员可以创建项目、删除任何项目、删除成员帐户，并能给成员分配权限。我们建议拥有管理员权限的人必须可靠。</p>

				<h5>可以创建项目？</h5>
				<p>拥有此权限的成员能够创建新项目。</p>
				
			</aside>
		</div>
	</div>
</div>
</body>
</html>