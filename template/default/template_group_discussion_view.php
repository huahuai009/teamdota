<?php if(!defined('IN_TEAMDOTA')) exit('Access Denied');?>
<!DOCTYPE html>
<html>
<head>
<title><?=$manageproject['name']?>: <?=$discussion['subject'] ?></title>
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
		<div class="panel sheet_view inactive <?php if($discussion['status'] == 1) { echo "has_notice is_trashed";}?>" data-behavior=" " data-creator-id="<?=$discussion['uid'] ?>" data-status="<?=get_project_status();?>">
			<header><h1><a href="group.php?do=project&project_id=<?=$project_id?>&discussion_page=<?=$discussion_page?>"><?=$manageproject['name']?></a> > 交流区</h1></header>
			<?php if($discussion['status'] == 1) {?>
			<header class="notice">
				该主题已被 <?=$trash['sender_author']?> 于 <?=sgmdate('Y年m月d日', $trash['created_time'])?> 删除。
				<?php if(check_project_manage($manageproject['uid'],$discussion['uid']) && $manageproject['status'] == 0) {?>
				<span data-visible-to="admin creator">
					<a href="cp.php?project_id=<?=$project_id?>&ac=<?=$do?>&op=restored&discussion_id=<?=$discussion_id ?>&rand=<?=$_SGLOBAL['timestamp'];?>" data-method="post" data-remote="true" rel="nofollow">点击恢复此主题</a>

				或者 <a href="cp.php?project_id=<?=$project_id?>&ac=<?=$do?>&op=realdelete&discussion_id=<?=$discussion_id ?>&rand=<?=$_SGLOBAL['timestamp'];?>" data-method="delete" data-remote="true" rel="nofollow">永久删除</a> ，
					  永久删除之后将不可恢复。
				</span>
				<?php } ?>
			</header>
			<?php } ?>
			<div style="margin-left: 10px; margin-bottom: -50px;" class="panel message">
			<div class="sheet_body_view">
				<section>
					<div id="message">
						<article class="message" id="message_<?=$discussion_id ?>">
							<header>
								<h3><?=$discussion['subject'] ?></h3>
								<p data-creator-id="<?=$discussion['uid'] ?>">
									作者：<?=$discussion['author'] ?>，<time data-time-ago="" datetime="<?php echo sgmdate('Y-m-d H:i:s', $discussion['logtime']);?>"><?php echo sgmdate('n-j', $discussion['logtime'],1);?></time>
									<?php if(check_project_manage($manageproject['uid'],$discussion['uid']) && $manageproject['status'] == 0 && $discussion['status'] == 0) {?>
									<span data-visible-to="creator admin">
									<a href="cp.php?project_id=<?=$project_id?>&ac=<?=$do?>&op=edit&discussion_page=<?=$discussion_page ?>&discussion_id=<?=$discussion_id ?>" class="edit" name="编辑">编辑</a>
									|
									<a href="cp.php?project_id=<?=$project_id?>&ac=<?=$do?>&op=delete&discussion_id=<?=$discussion_id ?>&rand=<?=$_SGLOBAL['timestamp'];?>" class="delete" onclick="if (confirm('确定要删除该主题吗？')) return true; else return false;">删除</a>
									</span>
									<?php } ?>
								</p>
							</header>
							 <a href="group.php?do=people&uid=<?=$discussion['uid'] ?>"><img class="avatar" height="59" src="<? echo avatar($discussion['uid'],'55',true);?>" title="<?=$discussion['author'] ?>" width="59" onerror="this.onerror=null;this.src='/image/avatar.gif'" /></a>
							<div class="formatted_content">
								<?=$discussion['message'] ?>
							</div>
							<div class="attachments">
								<div id="attachments_for_message_<?=$discussion_id ?>">
									<?php if($listdiscussionpic) { ?>
									<div class=" image_grid_view" data-scaled="true">
										<table class="in_3_columns">
											<tbody>
												<tr class="images">
													<?php if(is_array($listdiscussionpic)) { $i=0;foreach($listdiscussionpic as $key => $value) { ?>
													<?php if(($i % 3)==0 && $i != 0) { ?>
													</tr>
													<tr class="images">
													<?php }?>
													<td class="occupied">
														<article class="image">
															<figure class="thumbnail proportional" data-behavior="enlargeable">
																<a href="javascript:;" title="<?=$value['filename']?>" >
																	<div class="background" style="height: 159px; width: 262px; ">
																		<img alt="" class="thumbnail" data-container-id="message_<?=$discussion_id?>" data-filename="<?=$value['filename']?>" data-image-id="<?=$value['file_id']?>" src="<?=$value['thumbfileurl']?>" style="width: <?=$value['thumbwidth']?>px; height: <?=$value['thumbheight']?>px; " data-scaled="true" data-original-src="<?=$value['fileurl']?>" data-height="<?=$value['height']?>" data-width="<?=$value['width']?>">
																	</div>
								</a>                 			<figcaption><?=$value['filename']?></figcaption>
															</figure>
														</article>
													</td>
													<?php $i++;} } ?>
												</tr>
											</tbody>
										</table>
									</div>
									<?php }?>
									<?php if($listdiscussionfile) { ?>
									<ul class="attachments">
										<?php if(is_array($listdiscussionfile)) { foreach($listdiscussionfile as $keyfile => $valuefile) { ?>
										<li>
											<a href="group.php?project_id=<?=$project_id?>&do=download&file_id=<?=$valuefile['file_id']?>" data-stacker="false" target="_blank">
											<img alt="Generic_big" border="0" class="file_icon" height="32" src="<?=$valuefile['thumbfileurl']?>" title="<?=$valuefile['filename']?>" width="32"><br>
										 <?=$valuefile['filename']?>
											</a>          
										</li>
										<?php $i++;} } ?>
									</ul>
									<?php }?>
								</div>
							</div>
							<footer>
								<section class="comments" id="commentsdata_<?=$discussion_id ?>">
									
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
	ajaxget('group.php?project_id='+bbcx.currentProject+'&do=discussion&discussion_id=<?=$discussion_id ?>&inajax=1','commentsdata_<?=$discussion_id ?>');
});
</script>
</body>
</html>