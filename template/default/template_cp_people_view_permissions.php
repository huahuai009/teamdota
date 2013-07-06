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
<style type="text/css">div.panel { min-height: 683px };</style>
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
		
		
		<div class="panel sheet_view inactive person_projects">
			<header><h1><a href="group.php?do=people&uid=<?=$uid?>"><?=$member['fullname'];?></a></h1></header>
			<div class="sheet_body_view">

				<section>
					<header>
						<a href="javascript:" onclick="delete_people(<?=$member['uid']?>);" class="person_remove" data-method="delete" data-stacker="false" rel="nofollow">完全删除此用户</a>

					  <h2>帐户权限</h2>
					</header>
					<table>
						<tr>
							<th>
								<?php if($member['ntype'] > 0) { ?>
								<input checked="checked" id="permissions_admin_<?=$member['uid']?>" name="permissions[admin]" type="checkbox" value="1" onclick="permissions(0,this,<?=$member['uid']?>)">
								<?php } else { ?>
								<input id="permissions_admin_<?=$member['uid']?>" name="permissions[admin]" type="checkbox" value="0" onclick="permissions(0,this,<?=$member['uid']?>)">
								<?php } ?>
							</th>
							<td>
						  管理员 <span style="color:#777;">— 管理员可以创建项目、删除任何项目、删除成员帐户，并能给成员分配权限。我们建议拥有管理员权限的人必须可靠。</span>
							</td>
						</tr>

						<tr class="can_create ">
							<th>
								<?php if($member['ntype'] > 0) { ?>
								<input checked="checked" disabled="disabled" id="permissions_can_create_projects_<?=$member['uid']?>" name="permissions[can_create_projects]" type="checkbox" value="1" onclick="permissions(1,this,<?=$member['uid']?>)">
								<?php } elseif($member['is_create_project'] > 0) { ?>
								<input checked="checked" id="permissions_can_create_projects_<?=$member['uid']?>" name="permissions[can_create_projects]" type="checkbox" value="1" onclick="permissions(1,this,<?=$member['uid']?>)">
								<?php } else { ?>
								<input id="permissions_can_create_projects_<?=$member['uid']?>" name="permissions[can_create_projects]" type="checkbox" value="0" onclick="permissions(1,this,<?=$member['uid']?>)">
								<?php } ?>
							</th>
							<td>
						  可以创建项目 <span style="color:#777;">— 拥有此权限的成员能够创建新项目。</span>
							</td>
						</tr>
					</table>
				</section>

				<section class="controls_column">
					<header>
					  <h2>项目的权限</h2>
					</header>

					<p class="person_project_controls">
					  <a href="javascript:;" onclick="permissions_all_project(<?=$member['uid']?>);" data-behavior="grant-all" data-bucket-type="project" data-stacker="false">选择所有项目</a>
					  |
					  <a href="javascript:;" onclick="permissions_notall_project(<?=$member['uid']?>);" data-behavior="revoke-all" data-bucket-type="project" data-stacker="false">取消所有项目</a>
					</p>
					<input type="hidden" id="string_projects" name="string_projects" value="<?=$string_projects?>" />
					<?php if($projects) { ?>
					<?php if(is_array($projects)) { foreach($projects as $key => $value) { ?>
					<table class="accesses" data-person-id="<?=$uid?>">
						<tr data-bucket-type="project" data-bucket-id="<?=$value['project_id']?>">
							<th data-behavior="access-toggle">
								<?php if($value['isexist']) {?>
								<input checked="checked" id="permissions_projects_<?=$value['project_id']?>" name="permissions_projects" type="checkbox" value="1" onclick="permissions_project(0,this,<?=$member['uid']?>,<?=$value['project_id']?>)">
								<?php } else { ?>
								<input id="permissions_projects_<?=$value['project_id']?>" type="checkbox" name="permissions_projects" value="0" onclick="permissions_project(0,this,<?=$member['uid']?>,<?=$value['project_id']?>)">
								<?php } ?>
							</th>
							<td><a href="group.php?do=project&project_id=<?=$value['project_id']?>" data-replace-stack="true"><?=$value['name']?></a></td>
						</tr>
					</table>
					<?php } } } ?>
				</section>

			</div>
		</div>
	
	</div>
</div>
</body>
</html>