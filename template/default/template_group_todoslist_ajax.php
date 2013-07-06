<?php if(!defined('IN_TEAMDOTA')) exit('Access Denied');?>
<?php if($_GET['op'] == 'completed') {//获取处于完成状态的待办事宜 ?>
<li class="todo show" data-behavior="has_hover_content sortable complete" data-sortable-type="todo" id="sortable_todo_<?=$todoslist['todoslist_id']?>">
	<div class="nubbin" data-behavior="nubbin hover_content" style="left: -35px; display: none; ">
		<div class="spacer"></div>
		<a href="javascript:;" onclick="deletetodoslist(this,<?=$todoslist['todoslist_id']?>);" class="image delete" data-confirm="您确定要删除此待办事宜吗？" data-method="post" data-remote="true" rel="nofollow">删除</a>
	</div>
	<div class="complete">
		<span class="wrapper">
			<input checked="checked" data-behavior="toggle" name="todo_complete" type="checkbox" value="1" onclick="todoslist_nocompleted(this,<?=$todos_id?>,<?=$todoslist['todoslist_id']?>);">
			<span class="content" data-behavior="sortable_handle">
				<a href="group.php?do=todoslist&project_id=<?=$project_id?>&todoslist_id=<?=$todoslist['todoslist_id']?>"><?=$todoslist['subject']?></a>
				<span class="content_for_perma"><?=$todoslist['subject']?></span>
			</span>
			<span class="completed_by">
			(<?=$todoslist['completed_author']?> 完成于 <?=$todoslist['completed_date']?>)
			</span>
		</span>
	</div>
</li>
<?php } elseif($_GET['op'] == 'nocompleted') {  ?>
<li class="todo show" data-behavior="has_hover_content sortable" data-due-datetime="<?=$todoslist['due_date']?>" data-sortable-type="todo" id="sortable_todo_<?=$todoslist['todoslist_id']?>">
	<div class="nubbin" data-behavior="nubbin hover_content" style="left: -61px; display: none; ">
		<div class="spacer"></div>
		<a href="javascript:;" onclick="deletetodoslist(this,<?=$todoslist['todoslist_id']?>);" class="image delete" data-confirm="您确定要删除此待办事宜吗？" data-method="post" data-remote="true" rel="nofollow" title="删除">删除</a>
		<a href="javascript:;" onclick="edit_todoslist(<?=$todoslist['todoslist_id']?>);" class="edit" data-remote="true">编辑</a>
	</div>

	<div>
		<span class="wrapper">
			<input data-behavior="toggle" name="todo_complete" type="checkbox" value="1" onclick="todoslist_completed(this,<?=$todos_id?>,<?=$todoslist['todoslist_id']?>);">
			<span class="content" data-behavior="sortable_handle">
				<a href="group.php?do=todoslist&project_id=<?=$project_id?>&todoslist_id=<?=$todoslist['todoslist_id']?>"><?=$todoslist['subject']?></a>
				<span class="content_for_perma"><?=$todoslist['subject']?></span>
			</span>
			<?php if($todoslist['post_num']) { ?>
			<span class="pill comments">
				<a href="group.php?do=todoslist&project_id=<?=$project_id?>&todoslist_id=<?=$todoslist['todoslist_id']?>"><?=$todoslist['post_num']?>条评论</a>
			</span>
			<?php } ?>
			<?php if(!empty($todoslist['assign_author']) || !empty($todoslist['due_date'])) { ?>
			<span style="position:relative">
				<span class="pill has_balloon" data-behavior="expandable expand_exclusively load_assignee_options">
					<a href="javascript:;" onclick="edit_todoslist(<?=$todoslist['todoslist_id']?>);" data-behavior="expand_on_click">
						<?php if(!empty($todoslist['assign_author'])) { ?>
						<span data-behavior="assignee_name" data-blank-text="Unassigned">
						  <?=$todoslist['assign_author']?>
						</span>
						<?php } ?>
						<?php if(!empty($todoslist['assign_author']) && !empty($todoslist['due_date'])) { ?>
						<span class="separator"> · </span>
						<?php } ?>
						<?php if(!empty($todoslist['due_date'])) { ?>
						<time data-behavior="due_date" data-blank-text="No due date">
						  <?=$todoslist['due_date']?>
						</time>
						<?php } ?>
					</a>
				</span>
			</span>
			<?php } ?>
		</span> 
	</div>
</li>
<?php } ?>