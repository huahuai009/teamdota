<?php if(!defined('IN_TEAMDOTA')) exit('Access Denied');?>
<!DOCTYPE html>
<html>
<head>
<title>Teamdota:搜索结果</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link href="css/application.css" media="screen" rel="stylesheet" type="text/css">
<link rel="shortcut icon" href="http://www.teamdota.com/favicon.ico" />
<meta charset="utf-8">
<meta name="viewport" content="width=1100">
<style type="text/css">div.panel { min-height: 850px };</style></head>
<script type="text/javascript">
    window.bbcx = {currentGroup: "<?=$group['group_id']?>",currentPerson: "<?=$_SGLOBAL['supe_uid']?>",currentProject: "0",currentDo: "<?=$do?>"};
</script>
<script src="js/jquery.js" type="text/javascript"></script>
<script src="js/global.js" type="text/javascript"></script>
<body class="windows">
<?php $position_hotkey = 1 ; include_once template("head"); ?>
<div id="workspace_project" class="workspace" style="padding-bottom:30px;">
	<div class="container stack_container" style="width:960px; " data-creator-id="<?=$_SGLOBAL['supe_uid']?>">
		
		<div class="panel sheet_view search" data-behavior=" prevent_reload_when_stacked">
			<title>搜索结果</title>
			<header>
				<h2>搜索结果匹配 <mark><?=$searchkey?></mark></h2>
			</header>
			<div class="sheet_body_search">
				<table class="results" data-behavior="infinite_page" data-infinite-page="3">
					<tbody>
						<?php if($result_discussion_list) { ?>
						<?php if(is_array($result_discussion_list)) { foreach($result_discussion_list as $key => $value) { ?>
						<tr class="discussion">
							<td class="icon" id="discussion_<?=$value['discussion_id']?>">
								<h4>主题：</h4>
							</td>
							<td class="summary">
								<h4><a href="group.php?project_id=<?=$value['project_id']?>&do=discussion&discussion_id=<?=$value['discussion_id']?>"><?=$value['subject']?></a></h4>

								<p><?=$value['message']?></p>

								<h5>项目： <?=$value['project_name']?></h5>

								<time data-time-ago="" datetime="<?php echo sgmdate('Y-m-d H:i:s', $value['logtime']);?>"><?php echo sgmdate('n-j', $value['logtime'],1);?></time>
							</td>
						</tr>
						<?php } } }?>
						
						<?php if($result_document_list) { ?>
						<?php if(is_array($result_document_list)) { foreach($result_document_list as $key => $value) { ?>
						<tr class="document">
							<td class="icon" id="document_<?=$value['document_id']?>">
								<h4>文档：</h4>
							</td>
							<td class="summary">
								<h4><a href="group.php?project_id=<?=$value['project_id']?>&do=document&document_id=<?=$value['document_id']?>"><?=$value['name']?></a></h4>

								<p><?=$value['description']?></p>

								<h5>项目： <?=$value['project_name']?></h5>

								<time data-time-ago="" datetime="<?php echo sgmdate('Y-m-d H:i:s', $value['logtime']);?>"><?php echo sgmdate('n-j', $value['logtime'],1);?></time>
							</td>
						</tr>
						<?php } } }?>
						
						<?php if($result_post_list) { ?>
						<?php if(is_array($result_post_list)) { foreach($result_post_list as $key => $value) { ?>
						<tr class="post">
							<td class="icon" id="post_<?=$value['post_id']?>">
								<h4>回复：</h4>
							</td>
							<td class="summary">
								<h4><a href="group.php?project_id=<?=$value['project_id']?>&do=discussion&discussion_id=<?=$value['discussion_id']?>"><?=$value['subject']?></a></h4>

								<p><?=$value['message']?></p>

								<h5>项目： <?=$value['project_name']?></h5>

								<time data-time-ago="" datetime="<?php echo sgmdate('Y-m-d H:i:s', $value['logtime']);?>"><?php echo sgmdate('n-j', $value['logtime'],1);?></time>
							</td>
						</tr>
						<?php } } }?>
					</tbody> 
				</table>
			</div>
		</div>
		
	</div>
</div>
</body>
</html>