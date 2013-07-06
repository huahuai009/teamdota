<?php if(!defined('IN_TEAMDOTA')) exit('Access Denied');?>
<h4>评论</h4>
<div>
	<?php if($list) { ?>
	<?php if(is_array($list)) { foreach($list as $key => $value) { ?>
	<article class="comment" id="comment_<?=$value['post_id']?>" data-creator-id="<?=$value['uid']?>">
	<a href="group.php?do=people&uid=<?=$value['uid'] ?>"><img class="avatar" src="<? echo avatar($value['uid'],'55',true);?>" title="<?=$value['author']?>" onerror="this.onerror=null;this.src='/image/avatar.gif'" /></a>
	<div class="formatted_content">
	  <strong><?=$value['author']?></strong></br>
	  <?=$value['message']?>
	</div>
	<footer class="time">
	  发表于 <a href="#comment_<?=$value['post_id']?>" data-stacker="false"><time data-time-ago="" datetime="<?php echo sgmdate('Y-m-d H:i:s', $value['logtime'],1);?>"><?php echo sgmdate('n-j', $value['logtime'],1);?></time></a>
	  <?php if(check_project_manage($manageproject['uid'],$value['uid']) && $manageproject['status'] == 0 && $objectdata['status'] == 0) {?>
	  <span data-visible-to="admin" style="">
		–
		<a href="javascript:;" onclick="edit_post(<?=$objectid ?>,<?=$value['post_id']?>);" class="edit" data-remote="true">编辑</a>
		<a href="javascript:;" class="delete" onclick="deletepost(<?=$objectid ?>,<?=$value['post_id']?>);">删除</a>
	  </span>
	  <? } ?>
	</footer>
		<div id="attachments_for_comment_<?=$value['post_id']?>">
			<?php if($listpostpic[$value['post_id']]) { ?>
			<div class=" image_grid_view" data-scaled="true">
				<table class="in_3_columns">
					<tbody>
						<tr class="images">
							<?php if(is_array($listpostpic[$value['post_id']])) { $i=0;foreach($listpostpic[$value['post_id']] as $keyfile => $valuefile) { ?>
							<?php if(($i % 3)==0 && $i != 0) { ?>
							</tr>
							<tr class="images">
							<?php }?>
							<td class="occupied">
								<article class="image">
									<figure class="thumbnail proportional" data-behavior="enlargeable">
										<a href="javascript:;" title="<?=$valuefile['filename']?>" data-stacker="false">
											<div class="background" style="height: 159px; width: 262px; ">
												<img alt="" class="thumbnail" data-container-id="comment_<?=$value['post_id']?>" data-filename="<?=$valuefile['filename']?>" data-image-id="<?=$valuefile['file_id']?>" src="<?=$valuefile['thumbfileurl']?>" style="width: <?=$valuefile['thumbwidth']?>px; height: <?=$valuefile['thumbheight']?>px; " data-scaled="true" data-height="<?=$valuefile['height'] ?>" data-width="<?=$valuefile['width'] ?>" data-original-src="<?=$valuefile['fileurl']?>">
											</div>
		</a>                 			<figcaption><?=$valuefile['filename']?></figcaption>
									</figure>
								</article>
							</td>
							<?php $i++;} } ?>
						</tr>
					</tbody>
				</table>
			</div>
			<?php }?>
			<?php if($listpostfile[$value['post_id']]) { ?>
			<ul class="attachments">
				<?php if(is_array($listpostfile[$value['post_id']])) { $i=0;foreach($listpostfile[$value['post_id']] as $keyfile => $valuefile) { ?>
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
	</article>
	<?php } } }?>
	<?=$pagenumbers?>
</div>
 <?php if($manageproject['status'] == 0 && $objectdata['status'] == 0) { ?>
<article class="comment new">
	<img alt="Avatar" class="avatar" src="<? echo avatar($_SGLOBAL['supe_uid'],'55',true);?>" onerror="this.onerror=null;this.src='/image/avatar.gif'"/>
	<div class="collapsed_content" id="comment_before">
		<header class="text_entry_view no_shadow">
			<? if($do=='discussion') {?>
			<div class="prompt" onclick="show_add_comment('<?=$discussion_id ?>','discussion',<?=$objectid ?>);">添加评论...</div>
			<? } elseif($do=='document') { ?>
			<div class="prompt" onclick="show_add_comment('<?=$document['discussion_id'] ?>','document',<?=$objectid ?>);">添加评论...</div>
			<? } elseif($do=='attachment') { ?>
			<div class="prompt" onclick="show_add_comment('<?=$attachment['discussion_id'] ?>','attachment',<?=$objectid ?>);">添加评论...</div>
			<? } elseif($do=='todos') { ?>
			<div class="prompt" onclick="show_add_comment('<?=$todos['discussion_id'] ?>','todos',<?=$objectid ?>);">添加评论...</div>
			<? } elseif($do=='todoslist') { ?>
			<div class="prompt" onclick="show_add_comment('<?=$todoslist['discussion_id'] ?>','todoslist',<?=$objectid ?>);">添加评论...</div>
			<? } ?>
		</header>
	</div>
	<div  class="expanded_content" id="comment_after" style="display:none;padding-top:20px;">
		<form action="cp.php?ac=post&project_id=<?=$project_id?>" class="new_comment" data-remote="true" id="newcomment_<?=$objectid ?>" method="post">
		<? if($do=='discussion') {?>
		<input type="hidden" name="discussion_id" value="<?=$objectid?>" />
		<? } elseif($do=='document') { ?>
		<input type="hidden" name="discussion_id" value="<?=$document['discussion_id'] ?>" />
		<input type="hidden" name="document_id" value="<?=$objectid ?>" />
		<input type="hidden" name="posttype" value="document" />
		<? } elseif($do=='attachment') { ?>
		<input type="hidden" name="discussion_id" value="<?=$attachment['discussion_id'] ?>" />
		<input type="hidden" name="file_id" value="<?=$objectid ?>" />
		<input type="hidden" name="posttype" value="file" />
		<? } elseif($do=='todos') { ?>
		<input type="hidden" name="discussion_id" value="<?=$todos['discussion_id'] ?>" />
		<input type="hidden" name="todos_id" value="<?=$objectid ?>" />
		<input type="hidden" name="posttype" value="todos" />
		<? } elseif($do=='todoslist') { ?>
		<input type="hidden" name="discussion_id" value="<?=$todoslist['discussion_id'] ?>" />
		<input type="hidden" name="todoslist_id" value="<?=$objectid ?>" />
		<input type="hidden" name="posttype" value="todoslist" />
		<? } ?>
		<input type="hidden" name="postsubmit" value="true" />
		<input type="hidden" name="formhash" value="<?php echo formhash(); ?>" />
		<input type="hidden" id="fileids" name="fileids" value="" />
		<header class="text_entry_view">
			<textarea class="userData" name="message" id="qinbaba-ttHtmlEditor" style="height:100%;width:100%;display:none;border:0px" placeholder="添加评论..."  onkeydown="return ctrlEnter(event, 'issuance');"></textarea>
			<iframe src="editor.php" name="qinbaba-ifrHtmlEditor" id="qinbaba-ifrHtmlEditor" scrolling="no" border="0" frameborder="0" style="width:100%;border: 1px solid #C5C5C5;" height="200"></iframe>

			<div data-behavior="pending_attachments" class="attachments">
				<img alt="Paperclip" class="prompt_graphic" src="image/paperclip.png">
				<div class="file_input_button">
					<input id="fileImage" name="fileselect[]" type="file" multiple="" tabindex="-1">

			  拖动附件到此虚线区域 <a href="javascript:;" class="decorated" tabindex="-1">或者点击选择...</a>
				</div>
				<ul class="pending_attachments" id="preview">
				</ul>
			</div>
		</header>

		<footer>
			<div class="subscribable" id="subscribable_data" style="padding-top:10px;">
				
			</div>
			<div class="submit">
				<input  id="issuance_upload" data-behavior="issuance_upload" type="button" onclick="webupload.buttonUploadFile();" value="附件上传中..." style="display:none;">
				<input type="button" id="issuance" data-behavior="issuance_save" onclick="validatepost('newcomment_<?=$objectid?>','post_add');" value="发表评论"><span id="__newcomment_<?=$objectid?>"></span>
			</div>
		</footer>
		</form>  
	</div>
</article>
<?php } ?>