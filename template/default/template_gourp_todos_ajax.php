<?php if(!defined('IN_TEAMDOTA')) exit('Access Denied');?>
<ul class="todolists" data-autoload-url="false" data-behavior="sortable_container" data-sortable-type="todolist">
<?php if($listtodos) { ?>
<?php if(is_array($listtodos)) { foreach($listtodos as $key => $value) { ?>
<li data-behavior="sortable" data-sortable-type="todolist" id="sortable_todolist_<?=$value['todos_id']?>">
	<article class="todolist" id="todolist_<?=$value['todos_id']?>" data-behavior="expandable">
		<header class="collapsed_content" data-behavior="has_hover_content">
			<div class="nubbin" data-behavior="nubbin hover_content" style="left: -45px; display: none; ">
				<div class="spacer"></div>
				<a href="#" class="image delete" data-confirm="您确定要删除此待办事项清单吗？" rel="nofollow" title="删除">删除</a>
				<a href="javascript:;" onclick="edit_todos(<?=$value['todos_id']?>);" class="edit" data-behavior="edit">编辑</a>
			</div>
			<h3 data-behavior="sortable_handle">
				<a href="#" class="linked_title"><?=$value['subject']?></a>
				<span class="unlinked_title"><?=$value['subject']?></span>
			</h3>
		</header>
		
		<ul class="todos  ui-sortable" data-behavior="sortable_container" data-sortable-type="todo">
			<?php if($value['theparent']) { ?>
			<?php if(is_array($value['theparent'])) { foreach($value['theparent'] as $keylist => $valuelist) { ?>
			<li class="todo show" data-behavior="has_hover_content sortable" data-sortable-type="todo" id="todo_<?=$valuelist['todoslist_id']?>">
				<div class="nubbin" data-behavior="nubbin hover_content" style="left: -61px; display: none; ">
				  <div class="spacer"></div>
				  <a href="javascript:;" class="image delete" data-confirm="您确定要删除此待办事项吗？" rel="nofollow" title="删除">删除</a>
				  <a href="javascript:;" onclick="edit_todoslist(<?=$valuelist['todoslist_id']?>);" class="edit" data-remote="true">编辑</a>
				</div>
				<div>
					<span class="wrapper">
						<input data-behavior="toggle" name="todo_complete" type="checkbox" value="1" onclick="todoslist_completed(this,<?=$valuelist['todoslist_id']?>);">
						<span class="content" data-behavior="sortable_handle">
							<a href="#"><?=$valuelist['subject']?></a>
							<span class="content_for_perma"><?=$valuelist['subject']?></span>
						</span>
						<?php if($valuelist['post_num']) { ?>
						<span class="pill comments">
							<a href="#"><?=$valuelist['post_num']?>条评论</a>
						</span>
						<?php } ?>
						<?php if(!empty($valuelist['assign_author']) || !empty($valuelist['due_date'])) { ?>
						<span style="position:relative">
							<span class="pill has_balloon" data-behavior="expandable expand_exclusively load_assignee_options">
								<a href="javascript:;" onclick="edit_todoslist(<?=$valuelist['todoslist_id']?>);" data-behavior="expand_on_click">
									<?php if(!empty($valuelist['assign_author'])) { ?>
									<span data-behavior="assignee_name" data-blank-text="Unassigned">
									  <?=$valuelist['assign_author']?>
									</span>
									<?php } ?>
									<?php if(!empty($valuelist['assign_author']) && !empty($valuelist['due_date'])) { ?>
									<span class="separator"> · </span>
									<?php } ?>
									<?php if(!empty($valuelist['due_date'])) { ?>
									<time data-behavior="due_date" data-blank-text="No due date">
									  <?=$valuelist['due_date']?>
									</time>
									<?php } ?>
								</a>
							</span>
						</span>
						<?php } ?>
					</span> 
				</div>
				
			</li>
			<?php } } } ?>
		</ul>
		<ul class="new" data-behavior="expandable load_assignee_options">
			<li class="collapsed_content">
				<a href="javascript:;" onclick="add_todoslist(<?=$value['todos_id']?>);" class="decorated" data-behavior="expand_on_click load_assignee_options">添加事项</a>
			</li>
		</ul>
		<ul class="completed truncated" style="display: block; "> 
		</ul>
	</article>
</li>
<?php } } } ?>
</ul>
<?php if($completed_count) {?>
<div class="more_lists">
	<p class="completed">
	  <a href="#completed" class="decorated">已完成<?=$completed_count?>项待办事项</a>
	</p>
</div>
<? } ?>