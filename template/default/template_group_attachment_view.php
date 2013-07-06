<?php if(!defined('IN_TEAMDOTA')) exit('Access Denied');?>
<!DOCTYPE html>
<html>
<head>
<title><?=$manageproject['name']?>: <?=$attachment['filename'] ?></title>
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
		<div class="panel sheet_view inactive <?php if($attachment['status'] == 1) { echo "has_notice is_trashed";}?>" data-behavior=" " data-creator-id="<?=$attachment['uid'] ?>" data-status="<?=get_project_status();?>">
			<header><h1><a href="group.php?do=project&project_id=<?=$project_id?>&file_page=<?=$file_page?>"><?=$manageproject['name']?></a> > 资料区</h1></header>
			<?php if($attachment['status'] == 1) {?>
			<header class="notice">
				该附件已被 <?=$trash['sender_author']?> 于 <?=sgmdate('Y年m月d日', $trash['created_time'])?> 删除。
				<?php if(check_project_manage($manageproject['uid'],$attachment['uid']) && $manageproject['status'] == 0) {?>
				<span data-visible-to="admin creator">
					<a href="cp.php?project_id=<?=$project_id?>&ac=<?=$do?>&op=restored&file_id=<?=$file_id ?>&rand=<?=$_SGLOBAL['timestamp'];?>" data-method="post" data-remote="true" rel="nofollow">点击恢复此附件</a>

				或者 <a href="cp.php?project_id=<?=$project_id?>&ac=<?=$do?>&op=realdelete&file_id=<?=$file_id ?>&rand=<?=$_SGLOBAL['timestamp'];?>" data-method="delete" data-remote="true" rel="nofollow">永久删除</a> ，
					  永久删除之后将不可恢复。
				</span>
				<?php } ?>
			</header>
			<?php } ?>
			<div style="margin-left: 10px; margin-bottom: -50px;" class="panel message">
			<div class="sheet_body_view">
				<section>
					<div id="message">
						<article class="message" id="attachment_<?=$file_id ?>">
							<header>
								<h3><?=$attachment['filename'] ?></h3>
								<p data-creator-id="<?=$attachment['uid'] ?>">
									<?=$attachment['author'] ?> 上传于 <time data-time-ago="" datetime="<?php echo sgmdate('Y-m-d H:i:s', $attachment['logtime']);?>"><?php echo sgmdate('n-j', $attachment['logtime'],1);?></time>
									<?php if(check_project_manage($manageproject['uid'],$attachment['uid']) && $manageproject['status'] == 0 && $attachment['status'] == 0) {?>
									<span data-visible-to="creator admin">
									<a href="cp.php?project_id=<?=$project_id?>&ac=<?=$do?>&op=delete&file_id=<?=$file_id ?>&rand=<?=$_SGLOBAL['timestamp'];?>" class="delete" onclick="if (confirm('确定要删除该附件吗？')) return true; else return false;">删除</a>
									</span>
									<?php } ?>
								</p>
							</header>
							<a href="group.php?do=people&uid=<?=$attachment['uid'] ?>"><img class="avatar" height="59" src="<? echo avatar($attachment['uid'],'55',true);?>" title="<?=$attachment['author'] ?>" width="59" onerror="this.onerror=null;this.src='/image/avatar.gif'" /></a>
							<div class="attachments">
								<div id="attachments_for_upload_<?=$file_id ?>">
									<?php if($attachment['isimage']) { ?>
									<div class="single image_grid_view">
										<table class="in_3_columns">
											<tr class="images">
												<td class="occupied">
												  <article class="image">
													<figure class="thumbnail full_width" data-behavior="enlargeable">
													  <a href="javascript:;" data-stacker="false" title="<?=$attachment['filename']?>">
														<div class="background">
														  <img alt="<?=$attachment['filename']?>" data-container-id="upload_<?=$file_id ?>" class="thumbnail" data-height="<?=$attachment['height'] ?>" data-image-id="<?=$file_id ?>" data-width="<?=$attachment['width'] ?>" src="<?=$attachment['icon'] ?>" style="width:<?=$attachment['thumbwidth'] ?>px; height: <?=$attachment['thumbheight'] ?>px;" data-original-src="<?=$attachment['fileurl']?>" data-filename="<?=$attachment['filename']?>"/>
														</div>
									</a>                  <figcaption><?=$attachment['filename'] ?></figcaption>
													</figure>
												  </article>
												</td>
												<td class="empty">
												</td>
												<td class="empty">
												</td>
											</tr>
										</table>
									</div>
									<?php } else { ?>
									<ul class="attachments">
										<li>
											<a href="group.php?project_id=<?=$project_id?>&do=download&file_id=<?=$attachment['file_id']?>" target="_blank" data-stacker="false">
										  <img alt="Generic_big" border="0" class="file_icon" height="32" src="<?=$attachment['icon'] ?>" title="<?=$attachment['filename'] ?>" width="32"><br>
										 <?=$attachment['filename'] ?>
											</a>          
										</li>
									</ul>
									<?php } ?>
								</div>
							</div>
							<footer>
								<section class="comments" id="commentsdata_<?=$file_id ?>">
									
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
	ajaxget('group.php?project_id='+bbcx.currentProject+'&do=attachment&file_id=<?=$file_id ?>&inajax=1','commentsdata_<?=$file_id ?>');
});
</script>
</body>
</html>