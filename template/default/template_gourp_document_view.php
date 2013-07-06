<?php if(!defined('IN_TEAMDOTA')) exit('Access Denied');?>
<!DOCTYPE html>
<html>
<head>
<title><?=$manageproject['name']?>: <?=$document['name'] ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link href="css/application.css" media="screen" rel="stylesheet" type="text/css">
<link href="css/thickbox.css" media="screen" rel="stylesheet" type="text/css">
<link rel="shortcut icon" href="http://www.teamdota.com/favicon.ico" />
<meta charset="utf-8">
<meta name="viewport" content="width=1100">
<style type="text/css">div.panel { min-height: 850px };</style>
<script type="text/javascript">
    window.bbcx = {currentGroup: "<?=$group['group_id']?>",currentPerson: "<?=$_SGLOBAL['supe_uid']?>",currentProject: "<?=$project_id?>",currentDo: "<?=$do?>"};
</script>
<script src="js/jquery.js" type="text/javascript"></script>
<script src="js/global.js" type="text/javascript"></script>
<script src="js/thickbox.js" type="text/javascript"></script>
<script src="image/editor/editor_function.js" type="text/javascript"></script>
</head>
<body class="windows">
<?php $position_hotkey = 1 ; include_once template("head"); ?>
<div id="append_parent"></div>
<input type="hidden" id="currentDo" value="<?=$do?>" />
<div class="workspace" id="fileDragArea">
	<div style="width: 980px;" data-container-id="1" class="container stack_container">
		<div class="panel sheet_view inactive <?php if($document['status'] == 1) { echo "has_notice is_trashed";}?>" data-behavior=" " data-creator-id="<?=$document['uid'] ?>" data-status="<?=get_project_status();?>">
			<header><h1><a href="group.php?do=project&project_id=<?=$project_id?>&document_page=<?=$document_page?>"><?=$manageproject['name']?> - 文档区</a></h1></header>
			<?php if($document['status'] == 1) {?>
			<header class="notice">
				该文档已被 <?=$trash['sender_author']?> 于 <?=sgmdate('Y年m月d日', $trash['created_time'])?> 删除。
				<?php if(check_project_manage($manageproject['uid'],$document['uid']) && $manageproject['status'] == 0) {?>
				<span data-visible-to="admin creator">
					<a href="cp.php?project_id=<?=$project_id?>&ac=<?=$do?>&op=restored&document_id=<?=$document_id ?>&rand=<?=$_SGLOBAL['timestamp'];?>" data-method="post" data-remote="true" rel="nofollow">点击恢复此文档</a>

				或者 <a href="cp.php?project_id=<?=$project_id?>&ac=<?=$do?>&op=realdelete&document_id=<?=$document_id ?>&rand=<?=$_SGLOBAL['timestamp'];?>" data-confirm="There is no undo from this action. The content will be lost for good. Are you sure?" data-method="delete" data-remote="true" rel="nofollow">永久删除</a> ，
					  永久删除之后将不可恢复。
				</span>
				<?php } ?>
			</header>
			<?php } ?>
			<div style="margin-left: 10px; margin-bottom: -50px;" class="panel message">
				<div class="sheet_body_view">
					<section>
						<div id="message">
							<article class="message" id="document_<?=$document['document_id'] ?>">
								<header>
									<h3><?=$document['name'] ?></h3>
									<p data-creator-id="<?=$document['uid'] ?>">
										作者：<?=$document['author'] ?>，<time data-time-ago="" datetime="<?php echo sgmdate('Y-m-d H:i:s', $document['logtime']);?>"><?php echo sgmdate('n-j', $document['logtime'],1);?></time>
										<?php if(check_project_manage($manageproject['uid'],$document['uid']) && $manageproject['status'] == 0 && $document['status'] == 0) {?>
										<span data-visible-to="creator admin">
										<a href="cp.php?project_id=<?=$project_id?>&ac=<?=$do?>&op=edit&document_page=<?=$document_page ?>&document_id=<?=$document_id ?>" class="edit" name="编辑">编辑</a>
										|
										<a href="cp.php?project_id=<?=$project_id?>&ac=<?=$do?>&op=delete&document_id=<?=$document_id ?>&rand=<?=$_SGLOBAL['timestamp'];?>" class="delete" onclick="if (confirm('确定要删除该文档吗？')) return true; else return false;">删除</a>
										</span>
										<?php } ?>
									</p>
								</header>
								 <a href="group.php?do=people&uid=<?=$document['uid'] ?>"><img class="avatar" height="59" src="<? echo avatar($document['uid'],'55',true);?>" title="<?=$document['author'] ?>" width="59" onerror="this.onerror=null;this.src='/image/avatar.gif'" /></a>
								<div class="formatted_content">
									<?=$document['description'] ?>
								</div>
								<footer>
									<section class="comments" id="commentsdata_<?=$document_id ?>">
										
									</section>
								</footer>
							</article>
						</div>
					</section>
					<section class="event_stream" id="events_message_2293039">
						<h4>历史记录</h4>
						<?php if($listhistory) { ?>
						<?php if(is_array($listhistory)) { foreach($listhistory as $keyhistory => $valuehistory) { ?>
						<article class="event">
							<a href="<?=$valuehistory['href']?>">
								<span class="month_day"><time data-local-date="" datetime="<?php echo sgmdate('Y-m-d H:i:s', $valuehistory['created_time']);?>"><?php echo sgmdate('n-j', $valuehistory['created_time']);?></time></span>
								<span class="at"> </span>
								<span class="time"><time data-local-time="" datetime="<?php echo sgmdate('Y-m-d H:i:s', $valuehistory['created_time']);?>"><?php echo sgmdate('H:i', $valuehistory['created_time']);?></time></span>：<span class="creator"><?=$valuehistory['sender_author']?></span>

								<span class="summary"><?=$valuehistory['title_html']?></span>
								<span class="summary_perma"></span>

								<span class="bucket"></span>
							</a>
							<span class="subscribers"></span>
						</article>
						<?php } } } ?>
					</section>
				</div>
			</div>
		</div>
	</div>
</div>
<div id="shade"></div>
<script type="text/javascript">
$(document).ready(function() {
	ajaxget('group.php?project_id='+bbcx.currentProject+'&do=document&document_id=<?=$document_id ?>&inajax=1','commentsdata_<?=$document_id ?>');
});
</script>
</body>
</html>