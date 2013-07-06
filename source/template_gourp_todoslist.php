<?php if(!defined('IN_TEAMDOTA')) exit('Access Denied');?>
<!DOCTYPE html>
<html>
<head>
<title><?=$manageproject['name']?>: 待办事项列表</title>
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
</head>
<body class="windows">
<?php $position_hotkey = 1 ; include_once template("head"); ?>
<div id="append_parent"></div>
<input type="hidden" id="currentDo" value="<?=$do?>" />
<div class="workspace">
	<div style="width: 980px;" data-container-id="1" class="container stack_container">
		<div class="panel sheet_view inactive todolists has_sidebar">
				<header class="has_buttons">
					<div class="active_title">
						<h1>待办事项列表</h1>
					</div>

					<span class="position_reference">
						<button data-behavior="new_todolist">添加类型</button>
						<div class="blank_slate_arrow"></div>
					</span>
				</header>
				<div class="sheet_body_view">
					<section class="todos">
						<ul class="todolists" data-autoload-url="false" data-behavior="sortable_container" data-sortable-type="todolist">
							<?php if($listtodos) { ?>
							<?php if(is_array($listtodos)) { foreach($listtodos as $key => $value) { ?>
							<li data-behavior="sortable" data-sortable-type="todolist" id="sortable_todolist_<?=$value['todos_id']?>" style="display: list-item; ">  
								<article class="todolist" id="todolist_<?=$value['todos_id']?>" data-behavior="expandable">
									<header class="collapsed_content" data-behavior="has_hover_content">
										<div class="nubbin" data-behavior="nubbin hover_content" style="left: -61px; display: none; ">
											<div class="spacer"></div>
											<a href="#" class="image delete" data-confirm="Are you sure you want to delete this to-do list?" data-method="post" data-remote="true" rel="nofollow">删除</a>
											<a href="#" class="edit" data-behavior="edit">编辑</a>
										</div>
										<h3 data-behavior="sortable_handle">
											<a href="#" class="linked_title"><?=$value['subject']?></a>
											<span class="unlinked_title"><?=$value['subject']?></span>
										</h3>
									</header>

									<ul class="todos " data-behavior="sortable_container" data-sortable-type="todo">
										<?php if($value['theparent']) { ?>
										<?php if(is_array($value['theparent'])) { foreach($value['theparent'] as $keylist => $valuelist) { ?>
										<li class="todo show" data-assignee-code="p1774818" data-behavior="has_hover_content sortable" data-due-datetime="<?=$valuelist['due_date']?>" data-sortable-type="todo" id="todo_<?=$valuelist['todoslist_id']?>">
											<div class="nubbin" data-behavior="nubbin hover_content">
												<div class="spacer"></div>
												<a href="#" class="image delete" data-confirm="Are you sure you want to delete this to-do?" data-method="post" data-remote="true" rel="nofollow">删除</a>
												<a href="s#" class="edit" data-remote="true">编辑</a>
											</div>

											<div class="">
												<span class="wrapper">
													<input data-behavior="toggle" name="todo_complete" type="checkbox" value="1">
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
															<a href="#" data-behavior="expand_on_click">
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
											<a href="#" class="decorated" data-behavior="expand_on_click load_assignee_options">添加待办事项</a>
										</li>
									</ul>
								</article>
							</li>
							<?php } } }?>
						</ul>
					</section>
				</div>
				<aside>
					<p>
						分配用户：<br>
						<select data-behavior="todos_assignee_filter" data-options="[{&quot;value&quot;:&quot;unassigned&quot;,&quot;option&quot;:&quot;(Unassigned)&quot;},{&quot;value&quot;:&quot;g446329&quot;,&quot;option&quot;:&quot;wooduan&quot;},{&quot;value&quot;:&quot;p1774818&quot;,&quot;option&quot;:&quot;duty&quot;}]">
							<option value="">所有人</option>
							<option value="0">未分配(34)</option>
						</select>
					</p>

					<p>
						分配时间：<br>
						<select data-behavior="todos_date_filter" data-options="[{&quot;value&quot;:&quot;today&quot;,&quot;option&quot;:&quot;Today&quot;},{&quot;value&quot;:&quot;tomorrow&quot;,&quot;option&quot;:&quot;Tomorrow&quot;},{&quot;value&quot;:&quot;week&quot;,&quot;option&quot;:&quot;This week&quot;},{&quot;value&quot;:&quot;nextweek&quot;,&quot;option&quot;:&quot;Next week&quot;},{&quot;value&quot;:&quot;later&quot;,&quot;option&quot;:&quot;Later&quot;},{&quot;value&quot;:&quot;past&quot;,&quot;option&quot;:&quot;In the past (overdue)&quot;}]">
							<option value="">所有时间</option>
							<option value="past">逾期未完成</option>
						</select>
					</p>
					<p>
						<a href="#completed" class="decorated">查看已完成事项</a>
					</p>
					<h5>当前所有类型</h5>

					<ul>
						<?php if($listtodos) { ?>
						<?php if(is_array($listtodos)) { foreach($listtodos as $key => $value) { ?>
						<li><a href="#" class="decorated" id="sidebar_todolist_<?=$value['todos_id']?>"><?=$value['subject']?></a></li>
						<?php } } }?>
					</ul>
				</aside>
		</div>
	</div>
</div>
<div id="shade"></div>
</body>
</html>