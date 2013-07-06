<div style="min-height:380px;">
<?php if($listdocument) { ?>
<?php if(is_array($listdocument)) { foreach($listdocument as $key => $value) { ?>
<?php $detailurl = "group.php?project_id={$project_id}&do={$do}&document_id={$value[document_id]}&document_page={$page}"; ?>
<article class="document" id="document_<?=$value['document_id']?>" data-behavior="link_container">
	<table>
		<tbody>
			<tr>
				<td class="icon">
					<a href="<?=$detailurl?>"><img alt="Document_icon_jumbo" height="70" src="image/document_icon.png" width="60"></a>
				</td>
				<td class="text">
					<h3 class="title">
					<a href="<?=$detailurl?>"><?=$value['name']?></a>
					</h3>

					<p>
				  <?=$value['author']?> 发表于 <span class="time"><time data-time-ago="" datetime="<?=$value['logtime']?>"><?=$value['logtimemat']?></time></span>
					</p>
				</td>
			</tr>
		</tbody>
	</table>
</article>
<?php } } }?>
</div>
<?=$pagenumbers?>