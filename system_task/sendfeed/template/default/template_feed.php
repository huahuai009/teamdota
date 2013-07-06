<?php if(!defined('IN_QINBABA')) exit('Access Denied');?>
<table style="background-color: #ffffff; width: 100%; text-align: left; border-collapse: collapse; border-spacing: 0; -premailer-cellspacing: 0; -premailer-cellpadding: 0; -premailer-width: 100%; margin: 0; padding: 0; border: 0;" bgcolor="#ffffff" width="100%" cellpadding="0" cellspacing="0">
    <tr>
		<td style="text-align: left; vertical-align: top; font-size: 12px; line-height: 19px; font-family: '宋体', helvetica, sans-serif; color: #000000; background-color: #ffffff;" align="left" bgcolor="#ffffff" valign="top">
			<div class="global_events" style="margin-bottom: 19px; padding-bottom: 15px; border-bottom-width: 1px; border-bottom-color: #cccccc; border-bottom-style: solid;">
				<div class="logo" style="padding: 0 0 5px;">
					<table border="0" cellspacing="0" cellpadding="0" style="background-color: #ffffff; width: 100%; text-align: left; border-collapse: collapse; border-spacing: 0; -premailer-cellspacing: 0; -premailer-cellpadding: 0; -premailer-width: 100%; margin: 0; padding: 0; border: 0;" bgcolor="#ffffff" width="100%">
						<tr>
							<th style="vertical-align: middle; width: 1%;" valign="middle"><img alt="Teamdota" height="29" src="http://www.teamdota.com/image/logo_light.png" width="24" /></th>
							<td style="text-align: left; vertical-align: middle; font-size: 12px; line-height: 19px; font-family: '宋体', helvetica, sans-serif; color: #000000; width: 99%; background-color: #ffffff;" align="left" bgcolor="#ffffff" valign="middle"><h2 style="font-weight: bold; font-size: 14px; font-family: '宋体', helvetica, sans-serif; line-height: 24px; margin: 0; padding: 0 0 0 7px;">Teamdota</h2></td>
						</tr>
					</table>
				</div>
				<div class="header" style="font-family: '宋体', helvetica, sans-serif; margin: 0 0 15px; padding: 0;">
					<table border="0" cellspacing="0" cellpadding="0" style="background-color: #ffffff; width: 100%; text-align: left; border-collapse: collapse; border-spacing: 0; -premailer-cellspacing: 0; -premailer-cellpadding: 0; -premailer-width: 100%; margin: 0; padding: 0; border: 0;" bgcolor="#ffffff" width="100%">
						<tr>
							<td style="text-align: left; vertical-align: middle; font-size: 12px; line-height: 19px; font-family: '宋体', helvetica, sans-serif; color: #000000; width: 49%; background-color: #ffffff;" align="left" bgcolor="#ffffff" valign="middle"><hr style="border-bottom-width: 3px; border-bottom-color: #e5e5e5; margin: 0; padding: 0; border-style: none none solid;" /></td>
							<th style="width: 2%; white-space: nowrap; vertical-align: middle; padding: 0 10px;" valign="middle">
								<h1 style="background-color: #ffffff; font-family: '宋体', helvetica, sans-serif; font-size: 16px; line-height: normal; text-align: center; margin: 0 auto;" align="center">
								每日更新，<?=$project['name']?><br />
									<span class="inactive_title" style="font-weight: normal; font-size: 12px; font-family: '宋体', helvetica, sans-serif;"><?=sgmdate('Y年m月d日', $yesterday);?></span>
								</h1>
							</th>
							<td style="text-align: left; vertical-align: middle; font-size: 12px; line-height: 19px; font-family: '宋体', helvetica, sans-serif; color: #000000; width: 49%; background-color: #ffffff;" align="left" bgcolor="#ffffff" valign="middle"><hr style="border-bottom-width: 3px; border-bottom-color: #e5e5e5; margin: 0; padding: 0; border-style: none none solid;" /></td>
						</tr>
					</table>
				</div>
				<div class="section project" style="margin-bottom: 0; padding-bottom: 20px;">
					<div class="header" style="font-family: '宋体', helvetica, sans-serif; margin: 0 0 20px; padding: 0;">
						<table border="0" cellspacing="0" cellpadding="0" style="background-color: #ffffff; width: 100%; text-align: left; border-collapse: collapse; border-spacing: 0; -premailer-cellspacing: 0; -premailer-cellpadding: 0; -premailer-width: 100%; margin: 0; padding: 0; border: 0;" bgcolor="#ffffff" width="100%">
							<tr>
								<th style="width: 1%; white-space: nowrap; vertical-align: middle; padding: 0;" valign="middle">
									<h2 style="font-weight: bold; font-family: '宋体', helvetica, sans-serif; font-size: 14px; line-height: 24px; display: inline; color: black; padding-bottom: 15px; background-color: #ffffff; margin: 0;"><a href="<?=$_SC['siteurl']?>group.php?project_id=<?=$project_id?>" style="color: black; text-decoration: none;"><?=$project['name']?></a></h2>
								</th>
								<td style="text-align: left; vertical-align: middle; font-size: 12px; line-height: 19px; font-family: '宋体', helvetica, sans-serif; color: #000000; width: 49%; background-color: #ffffff;" align="left" bgcolor="#ffffff" valign="middle"><hr style="border-bottom-color: #e5e5e5; border-bottom-width: 1px; margin: 0; padding: 0; border-style: none none solid;" /></td>
							</tr>
						</table>
					</div>
					
					<div class="section contributors" style="margin: 0 0 10px; padding: 0;">
						<div class="header" style="font-family: '宋体', helvetica, sans-serif; overflow: visible; height: auto; margin: 0; padding: 0; border: none;">
							<h3 style="font-weight: bold; font-family: '宋体', helvetica, sans-serif; font-size: 12px; line-height: 20px; color: #AA9C84; margin: 4px 0;"><?=count($arr_member)?>位成员发表了意见</h3>
						</div>

						<div class="article event" style="font-size: 12px; margin-left: 20px; padding: 0 0 6px;">
							<?php if($arr_member) { ?>
							<?php if(is_array($arr_member)) { foreach($arr_member as $mkey => $member) { ?>
							<a href="<?=$_SC['siteurl']?>group.php?do=people&uid=<?=$mkey?>"  style="color: inherit; text-decoration: none;"><?=$member?></a>　
							<?php } } } ?>
						</div>
					</div>
					<?php if(count($arr_discussion) > 0) {?>
					<div class="section topics" style="margin: 0 0 10px; padding: 0;">
						<div class="header" style="font-family: '宋体', helvetica, sans-serif; overflow: visible; height: auto; margin: 0; padding: 0; border: none;">
							<h3 style="font-weight: bold; font-family: '宋体', helvetica, sans-serif; font-size: 12px; line-height: 20px; color: #AA9C84; margin: 4px 0;"><?=count($arr_discussion)?> 主题在讨论</h3>
						</div>
						<?php if($arr_discussion) { ?>
						<?php if(is_array($arr_discussion)) { foreach($arr_discussion as $dkey => $discussion) { ?>
						<div class="article event" style="font-size: 12px; margin-left: 20px; padding: 0 0 6px;">
							<p class="topic" style="font-size: 12px; line-height: 19px; font-family: '宋体', helvetica, sans-serif; color: #000000; font-weight: bold; margin: 0;">
								<a href="<?=$_SC['siteurl']?>group.php?project_id=<?=$project_id?>&do=discussion&discussion_id=<?=$dkey?>" style="color: inherit; text-decoration: none;"><?=$discussion['title']?></a>
							</p>
							<p class="byline" style="font-size: 12px; line-height: 15px; font-family: '宋体', helvetica, sans-serif; color: grey; margin: 0;">
							<?php 	$d = ''; 
								if($discussion['iscreate'] == 1) { 
									$d .= $discussion['author'].'创建了该主题';
								} elseif($discussion['iscreate'] == 2) {
									$d .= $discussion['author'].'修改了该主题';
								}
								if(is_array($discussion['sender'])) { 
									foreach($discussion['sender'] as $skey => $sender) {
										if($d != '') {
											$d .= ', ';
										}
										$d .= $sender['author'].'发表了'.$sender['number_comment'].'条评论';
									}
								}
								echo $d;
							?>
							</p>
							<div class="attachments images" style="margin: 19px 0 0;">
								<table style="background-color: #ffffff; width: 100%; text-align: left; border-collapse: collapse; border-spacing: 0; -premailer-cellspacing: 0; -premailer-cellpadding: 0; -premailer-width: 100%; margin: 0; padding: 0; border: 0;" bgcolor="#ffffff" width="100%" cellpadding="0" cellspacing="0">
									<?php if($discussion['attachment']) { ?>
									<?php if(is_array($discussion['attachment'])) { foreach($discussion['attachment'] as $akey => $file) { ?>
									<?php if($file['isimage']) { ?>
									<tr>
										<td class="image" style="text-align: left; vertical-align: top; font-size: 12px; line-height: 19px; font-family: '宋体', helvetica, sans-serif; color: #000000; background-color: #ffffff; padding: 8px 0;" align="left" bgcolor="#ffffff" valign="top">
											<a href="<?=$_SC['siteurl']?>group.php?project_id=<?=$project_id?>&do=discussion&discussion_id=<?=$dkey?>"><img alt="<?=$file['filename']?>" src="<?=$_SC['siteurl']?>mail_image_preview.php?file_id=<?=$file[file_id]?>&time=<?=$_SC[timestamp]?>&code=<?=md5("file_id={$file[file_id]}&time={$_SC[timestamp]}{$_SC['sitekey']}")?>" style="max-width: 100%; padding: 1px; border: 1px solid #ccc;"></a>
											<br/>
											<a href="<?=$_SC['siteurl']?>group.php?project_id=<?=$project_id?>&do=discussion&discussion_id=<?=$dkey?>" class="caption grey" style="color: grey; font-size: 12px; text-decoration: none;"><?=$file['filename']?></a>
										</td>
									</tr>
									<?php } else { ?>
									<tr>
										<td class="file_icon" style="text-align: left; vertical-align: middle; font-size: 12px; line-height: 19px; font-family: '宋体', helvetica, sans-serif; color: #000000; width: 40px; background-color: #ffffff; padding: 0 0 8px;" align="left" bgcolor="#ffffff" valign="middle">
											<a href="<?=$_SC['siteurl']?>group.php?project_id=<?=$project_id?>&do=discussion&discussion_id=<?=$dkey?>"><img alt="<?=$file['filename']?>" src="<?=$_SC['siteurl'].file_icon_big($file['type']);?>" style="padding: 0; border: 0;"></a>
											<br/>
											<a href="<?=$_SC['siteurl']?>group.php?project_id=<?=$project_id?>&do=discussion&discussion_id=<?=$dkey?>"><?=$file['filename']?></a>
										</td>
									</tr>
									<?php } } } } ?>
								</table>
							</div>
						</div>
						<?php } } } ?>
					</div>
					<?php } ?>
					
					<?php if(count($arr_document) > 0) {?>
					<div class="section topics" style="margin: 0 0 10px; padding: 0;">
						<div class="header" style="font-family: '宋体', helvetica, sans-serif; overflow: visible; height: auto; margin: 0; padding: 0; border: none;">
							<h3 style="font-weight: bold; font-family: '宋体', helvetica, sans-serif; font-size: 12px; line-height: 20px; color: #AA9C84; margin: 4px 0;"><?=count($arr_document)?> 文档在讨论</h3>
						</div>
						<?php if($arr_document) { ?>
						<?php if(is_array($arr_document)) { foreach($arr_document as $dkey => $document) { ?>
						<div class="article event" style="font-size: 12px; margin-left: 20px; padding: 0 0 6px;">
							<p class="topic" style="font-size: 12px; line-height: 19px; font-family: '宋体', helvetica, sans-serif; color: #000000; font-weight: bold; margin: 0;">
							<a href="<?=$_SC['siteurl']?>group.php?project_id=<?=$project_id?>&do=document&document_id=<?=$dkey?>" style="color: inherit; text-decoration: none;"><?=$document['title']?></a>
							</p>
							<p class="byline" style="font-size: 12px; line-height: 15px; font-family: '宋体', helvetica, sans-serif; color: grey; margin: 0;">
							<?php 	$d = ''; 
								if($document['iscreate'] == 1) { 
									$d .= $document['author'].'创建了该文档';
								} elseif($document['iscreate'] == 2) {
									$d .= $document['author'].'修改了该文档';
								}
								if(is_array($document['sender'])) { 
									foreach($document['sender'] as $skey => $sender) {
										if($d != '') {
											$d .= ', ';
										}
										$d .= $sender['author'].'发表了'.$sender['number_comment'].'条评论';
									}
								}
								echo $d;
							?>
							</p>
							<div class="attachments images" style="margin: 19px 0 0;">
							<table style="background-color: #ffffff; width: 100%; text-align: left; border-collapse: collapse; border-spacing: 0; -premailer-cellspacing: 0; -premailer-cellpadding: 0; -premailer-width: 100%; margin: 0; padding: 0; border: 0;" bgcolor="#ffffff" width="100%" cellpadding="0" cellspacing="0">
									<?php if($document['attachment']) { ?>
									<?php if(is_array($document['attachment'])) { foreach($document['attachment'] as $akey => $file) { ?>
									<?php if($file['isimage']) { ?>
									<tr>
										<td class="image" style="text-align: left; vertical-align: top; font-size: 12px; line-height: 19px; font-family: '宋体', helvetica, sans-serif; color: #000000; background-color: #ffffff; padding: 8px 0;" align="left" bgcolor="#ffffff" valign="top">
											<a href="<?=$_SC['siteurl']?>group.php?project_id=<?=$project_id?>&do=document&document_id=<?=$dkey?>"><img alt="<?=$file['filename']?>" src="<?=$_SC['siteurl']?>mail_image_preview.php?file_id=<?=$file[file_id]?>&time=<?=$_SC[timestamp]?>&code=<?=md5("file_id={$file[file_id]}&time={$_SC[timestamp]}{$_SC['sitekey']}")?>" style="max-width: 100%; padding: 1px; border: 1px solid #ccc;"></a>
											<br>
											<a href="<?=$_SC['siteurl']?>group.php?project_id=<?=$project_id?>&do=document&document_id=<?=$dkey?>" class="caption grey" style="color: grey; font-size: 12px; text-decoration: none;"><?=$file['filename']?></a>
										</td>
									</tr>
									<?php } else { ?>
									<tr>
										<td class="file_icon" style="text-align: left; vertical-align: middle; font-size: 12px; line-height: 19px; font-family: '宋体', helvetica, sans-serif; color: #000000; width: 40px; background-color: #ffffff; padding: 0 0 8px;" align="left" bgcolor="#ffffff" valign="middle">
											<a href="<?=$_SC['siteurl']?>group.php?project_id=<?=$project_id?>&do=document&document_id=<?=$dkey?>"><img alt="<?=$file['filename']?>" src="<?=$_SC['siteurl'].file_icon_big($file['type']);?>" style="padding: 0; border: 0;"></a>
										</td>

										<td class="file_name" style="text-align: left; vertical-align: middle; font-size: 12px; line-height: 19px; font-family: '宋体', helvetica, sans-serif; color: #000000; background-color: #ffffff; padding: 0 0 8px;" align="left" bgcolor="#ffffff" valign="middle">
											<a href="<?=$_SC['siteurl']?>group.php?project_id=<?=$project_id?>&do=document&document_id=<?=$dkey?>"><?=$file['filename']?></a>
										</td>
									</tr>
									<?php } } } } ?>
								</table>
							</div>
						</div>
						<?php } } } ?>
					</div>
					<?php } ?>
					<?if(count($arr_attachment) > 0) {?>
					<div class="section uploads" style="margin: 0 0 10px; padding: 0;">
					<div class="header" style="font-family: '宋体', helvetica, sans-serif; overflow: visible; height: auto; margin: 0; padding: 0; border: none;">
							<h3 style="font-weight: bold; font-family: '宋体', helvetica, sans-serif; font-size: 12px; line-height: 20px; color: #AA9C84; margin: 4px 0;"><?=count($arr_attachment)?> 附件上传</h3>
						</div>
						<?php if($arr_attachment) { ?>
						<?php if(is_array($arr_attachment)) { foreach($arr_attachment as $akey => $file) { ?>
						
						<div class="article event" style="font-size: 12px; margin-left: 20px; padding: 0 0 6px;">
							<?php if(in_array(fileext($file['title']), array('jpg','jpeg','gif','png','bmp'))) { ?>
							<div class="attachments images" style="margin: 5px 0 0;">
								<table style="background-color: #ffffff; width: 100%; text-align: left; border-collapse: collapse; border-spacing: 0; -premailer-cellspacing: 0; -premailer-cellpadding: 0; -premailer-width: 100%; margin: 0; padding: 0; border: 0;" bgcolor="#ffffff" width="100%" cellpadding="0" cellspacing="0">
									<tr>
										<td class="image" style="text-align: left; vertical-align: top; font-size: 12px; line-height: 19px; font-family: '宋体', helvetica, sans-serif; color: #666; background-color: #ffffff; padding: 8px 0 0;" align="left" bgcolor="#ffffff" valign="top">
									<a href="<?=$_SC['siteurl']?>group.php?project_id=<?=$project_id?>&do=attachment&file_id=<?=$akey?>" style="color: inherit; text-decoration: none;"><img alt="<?=$file['title']?>" src="<?=$_SC['siteurl']?>mail_image_preview.php?file_id=<?=$akey?>&time=<?=$_SC[timestamp]?>&code=<?=md5("file_id={$akey}&time={$_SC[timestamp]}{$_SC['sitekey']}")?>" style="max-width: 100%; padding: 1px; border: 1px solid #ccc;" /></a>
									<br>
									<a href="<?=$_SC['siteurl']?>group.php?project_id=<?=$project_id?>&do=attachment&file_id=<?=$akey?>" class="caption grey" style="color: inherit; font-size: 12px; text-decoration: none;"><?=$file['title']?></a>
										</td>
									</tr>
								</table>
							</div>
							<?php } else {?>
							<div class="attachments" style="margin: 5px 0 0;">
								<table style="background-color: #ffffff; width: 100%; text-align: left; border-collapse: collapse; border-spacing: 0; -premailer-cellspacing: 0; -premailer-cellpadding: 0; -premailer-width: 100%; margin: 0; padding: 0; border: 0;" bgcolor="#ffffff" width="100%" cellpadding="0" cellspacing="0">
									<tr>
										<td class="file_icon" style="text-align: left; vertical-align: middle; font-size: 14px; line-height: 19px; font-family: 'Helvetica Neue', helvetica, sans-serif; color: #000000; width: 40px; background-color: #ffffff; padding: 0 0 4px;" align="left" bgcolor="#ffffff" valign="middle">
										  <a href="<?=$_SC['siteurl']?>group.php?project_id=<?=$project_id?>&do=attachment&file_id=<?=$akey?>" style="color: inherit; text-decoration: none;"><img alt="<?=$file['title']?>" src="<?=$_SC['siteurl'].file_icon_big(fileext($file['title']));?>" style="padding: 0; border: 0;"></a>
										</td>

										<td class="file_name" style="text-align: left; vertical-align: middle; font-size: 14px; line-height: 19px; font-family: 'Helvetica Neue', helvetica, sans-serif; color: #000000; background-color: #ffffff; padding: 0 0 4px;" align="left" bgcolor="#ffffff" valign="middle">
										  <a href="<?=$_SC['siteurl']?>group.php?project_id=<?=$project_id?>&do=attachment&file_id=<?=$akey?>" style="color: inherit; text-decoration: none;"><?=$file['title']?></a>
										</td>
									</tr>
								</table>
							</div>
							<?php } ?>
							<p class="creator" style="font-size: 12px; line-height: 15px; font-family: '宋体', helvetica, sans-serif; color: #666; padding-bottom: 10px; margin: 0;">
							<?php 	$d = ''; 
								if($file['iscreate']) { 
									$d .= $file['author'].'上传了该附件';
								}
								if(is_array($file['sender'])) { 
									foreach($file['sender'] as $skey => $sender) {
										if($d != '') {
											$d .= ', ';
										}
										$d .= $sender['author'].'发表了'.$sender['number_comment'].'条评论';
									}
								}
								echo $d;
							?>
							</p>
						</div>
						<?php } } } ?>
					</div>
					<?php } ?>
				</div>
			</div>
		</td>
	</tr>
	<tr>
		<td style="background-color: #ffffff; text-align: left; vertical-align: top; font-size: 12px; line-height: 19px; font-family: '宋体', helvetica, sans-serif; color: #000000;" align="left" bgcolor="#ffffff" valign="top">
			<p class="small grey footer" style="font-size: 12px; line-height: 16px; font-family: '宋体', helvetica, sans-serif; color: grey; margin: 0 0 8px;">
		  <a href="{#subscriptions_url}" style="color: grey;">停止接收每日更新邮件</a>。
			</p>
		</td>
	</tr>
</table>