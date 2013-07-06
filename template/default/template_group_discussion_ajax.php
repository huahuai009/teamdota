<?php if(!defined('IN_TEAMDOTA')) exit('Access Denied');?>
<table class="inbox" data-behavior="file_drop_target">
	<tbody>
		<?php if($listdiscussion) { ?>
		<?php if(is_array($listdiscussion)) { foreach($listdiscussion as $key => $value) { ?>
		<?php $detailurl = "group.php?project_id={$project_id}&do={$do}&discussion_id={$value[discussion_id]}&discussion_page={$page}"; 
			if($value['othertype'] == 1){
				$detailurl = "group.php?project_id={$project_id}&do=document&document_id={$value[otherid]}&discussion_page={$page}";
			} else if($value['othertype'] == 2){
				$detailurl = "group.php?project_id={$project_id}&do=attachment&file_id={$value[otherid]}&discussion_page={$page}";
			}
		?>
		<tr class="topic" id="topic_<?=$value['discussion_id']?>">
			<td class="avatar">
				<a href="<?=$detailurl?>"><img class="avatar" height="30" src="<? echo avatar($value['uid'],'40',true);?>" title="<?=$value['author']?>" width="30" onerror="this.onerror=null;this.src='/image/avatar.gif'"></a>
			</td>
			<td class="who">
				<a href="<?=$detailurl?>"><?=$value['author']?></a>
			</td>
			<td class="what">
				<div class="attachments">
					 <?php if($listdiscussionpic[$value['discussion_id']]) { ?>
					 <?php if(is_array($listdiscussionpic[$value['discussion_id']])) { foreach($listdiscussionpic[$value['discussion_id']] as $keyfile => $valuefile) { ?>
					<figure data-behavior="enlargeable">
						<img alt="" class="thumbnail" data-container-id="<?php if($valuefile['post_id']) {echo 'comment_'.$valuefile['post_id'];} else {echo 'message_'.$valuefile['discussion_id'];}?>" data-filename="<?=$valuefile['filename']?>" data-image-id="<?=$valuefile['file_id']?>" src="<?=$valuefile['thumbfileurl']?>"  data-original-src="<?=$valuefile['fileurl']?>" data-height="<?=$valuefile['height']?>" data-width="<?=$valuefile['width']?>">
					</figure>
					<?php } } }?>
					<?php if($listdiscussionfile[$value['discussion_id']]) { ?>
					<?php if(is_array($listdiscussionfile[$value['discussion_id']])) { foreach($listdiscussionfile[$value['discussion_id']] as $keyfile => $valuefile) { ?>
					<figure data-behavior="enlargeable">
						<img alt="Generic_small" border="0" class="file_icon" height="18" src="<?=$valuefile['fileurl']?>" title="<?=$valuefile['filename']?>" width="24">
					</figure>
					<?php } } } ?>
				</div>
				<a href="<?=$detailurl?>"><strong><?=$value['subject']?></strong><span class="excerpt">
				<?php if(!empty($value['navidescription'])){ ?>
				- <?=$value['navidescription']?>
				<?php } ?>
				</span></a>
			</td>

			<td class="when">
				<a href="<?=$detailurl?>"><time data-compact="" datetime="<?=$value['lastpost']?>"><?=$value['lastpostmat']?></time></a>
			</td>
			<td class="comments">
				<a href="<?=$detailurl?>"><span class="pill comments circle"><?=$value['post_num']?></span></a>
			</td>
		</tr>
		<?php } } }?>
	</tbody>
</table>
<?=$pagenumbers?>
