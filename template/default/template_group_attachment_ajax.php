<?php if(!defined('IN_TEAMDOTA')) exit('Access Denied');?>
<div style="min-height:521px;">
<?php if($listattachment) { ?>
<?php if(is_array($listattachment)) { foreach($listattachment as $key => $value) { ?>
<?php $detailurl = "group.php?project_id={$project_id}&do={$do}&file_id={$value[file_id]}&file_page={$page}";
	if(!empty($value['discussion_id'])){
		$detailurl = "group.php?project_id={$project_id}&do=discussion&discussion_id={$value[discussion_id]}&file_page={$page}";
	}
?>
<article class="attachment" id="attachment_<?=$value['file_id']?>">
	<table>
		<tbody>
			<tr>
				<td class="icon">
					<figure data-behavior="enlargeable">
						
						<?php if($value['isimage']) { ?>
						<a href="javascript:;">
						<img alt="<?=$value['filename']?>" class="thumbnail" data-container-id="<?php if($value['post_id']) { echo 'upload_comment_'.$value['post_id'];} elseif($value['discussion_id']) { echo 'upload_message_'.$value['discussion_id'];} else { echo 'upload_'.$value['file_id'];}?>" data-filename="<?=$value['filename']?>" title="<?=$value['filename']?>" src="<?=$value['icon']?>" style="width: <?=$value['thumbwidth']?>px; height: <?=$value['thumbheight']?>px; " data-scaled="true" data-original-src="<?=$value['fileurl']?>" data-height="<?=$value['height']?>" data-width="<?=$value['width']?>" data-image-id="<?=$value['file_id']?>"></a>
						<?php } else { ?>
						<a href="javascript:;"><img class="file_icon" width="86" height="100" border="0" title="<?=$value['filename']?>" src="<?=$value['icon']?>" alt="<?=$value['filename']?>"></a> 
						<?php } ?>
								
					</figure>
				</td>
				<td class="text">
					<h3 class="filename">
						 <?=$value['filename']?>
					</h3>
				</td>
				<td class="textdiscussion">
					<div class="authorship">
						<p class="actions">
							<a href="group.php?project_id=<?=$project_id?>&do=download&file_id=<?=$value['file_id']?>" target="_blank">下载</a>
							<a href="<?=$detailurl?>" class="actions_discussion">进行讨论</a>
						</p>
					</div>
				</td>
			</tr>
		</tbody>
	</table>
</article>
<?php } } }?>
</div>
<?=$pagenumbers?>