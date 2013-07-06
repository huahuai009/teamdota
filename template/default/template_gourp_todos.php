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
		<div class="panel sheet_view inactive todolist">
				<header><h1><a href="group.php?do=project&project_id=<?=$project_id?>"><?=$manageproject['name']?></a> > <a href="group.php?do=todoslist&project_id=<?=$project_id?>">查看所有待办事项</a></h1></header>
			
			<div class="sheet_body_view">
				<section class="todos perma has_tools" data-creator-id="<?=$todos['uid']?>">
					<article class="todolist" id="todolist_<?=$todos_id?>" data-behavior="expandable">
						<header class="collapsed_content" data-behavior="has_hover_content">
							<div class="nubbin" data-behavior="nubbin hover_content" style="left: -65px; display: none; ">
								<div class="spacer"></div>
								<a href="javascript:;" onclick="deletetodos(this,<?=$todos_id?>);" class="image delete" data-confirm="您确定要删除此待办事项清单吗？" data-method="post" data-remote="true" rel="nofollow" title="删除">删除</a>
								<a href="javascript:;" onclick="edit_todos(<?=$todos_id?>);" class="edit" data-behavior="edit">编辑</a>
							</div>
							<h3 data-behavior="sortable_handle">
								<a href="group.php?do=todos&project_id=<?=$project_id?>&todos_id=<?=$todos_id?>" class="linked_title"><?=$todos['subject']?></a>
								<span class="unlinked_title"><?=$todos['subject']?></span>

							</h3>
						</header>
						<ul class="todos ui-sortable" data-behavior="sortable_container" data-sortable-type="todo">
							<?php if($listtodos) { ?>
							<?php if(is_array($listtodos)) { foreach($listtodos as $keylist => $valuelist) { ?>
							<li class="todo show" data-behavior="has_hover_content sortable" data-due-datetime="<?=$valuelist['due_date']?>" data-sortable-type="todo" id="sortable_todo_<?=$valuelist['todoslist_id']?>">
								<div class="nubbin" data-behavior="nubbin hover_content" style="left: -61px; display: none; ">
									<div class="spacer"></div>
									<a href="javascript:;" onclick="deletetodoslist(this,<?=$valuelist['todoslist_id']?>);" class="image delete" data-confirm="您确定要删除此待办事项吗？" data-method="post" data-remote="true" rel="nofollow" title="删除">删除</a>
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
								<a href="javascript:;" onclick="add_todoslist(<?=$todos_id?>);" class="decorated" data-behavior="expand_on_click load_assignee_options">添加待办事项</a>
							</li>
						</ul>
						<ul class="completed truncated">
							<?php if($listtodos_completed) { ?>
							<?php if(is_array($listtodos_completed)) { foreach($listtodos_completed as $keylist => $valuelist) { ?>
							<li class="todo show" data-behavior="has_hover_content sortable complete" data-sortable-type="todo" id="sortable_todo_<?=$valuelist['todoslist_id']?>">
								<div class="nubbin" data-behavior="nubbin hover_content" style="left: -35px; display: none; ">
									<div class="spacer"></div>
									<a href="javascript:;" onclick="deletetodoslist(this,<?=$valuelist['todoslist_id']?>);" class="image delete" data-confirm="您确定要删除此待办事项吗？" data-method="post" data-remote="true" rel="nofollow">删除</a>
								  
								</div>

								<div class="complete">
									<span class="wrapper">
										<input checked="checked" data-behavior="toggle" name="todo_complete" type="checkbox" value="1" onclick="todoslist_completed(this,<?=$valuelist['todoslist_id']?>);">
										<span class="content" data-behavior="sortable_handle">
											<a href="#"><?=$valuelist['subject']?></a>
											<span class="content_for_perma"><?=$valuelist['subject']?></span>
										</span>
										<span class="completed_by">
										(<?=$valuelist['completed_author']?> 完成于 <?=$valuelist['completed_date']?>)
										</span>
									</span>
								</div>
							</li>
							<?php } } } ?>
						</ul>
					</article>

					<section class="comments" id="comments_for_todolist_1594773" data-comments-count="0">
						<h4>Discuss this to-do list</h4>
						<article class="comment new" data-behavior="expandable file_drop_target">
							<img alt="Avatar" class="avatar" data-current-person-avatar="true" src="./Explore Basecamp!  To-do list basics_files/avatar.96.gif" title="duty">

							<div class="collapsed_content">
								<header class="text_entry no_shadow">
									<div class="prompt" data-behavior="expand_on_click" onclick="">Add a comment...</div>
								</header>
							</div>

							<div class="expanded_content">
								<form accept-charset="UTF-8" action="https://basecamp.com/1903229/projects/678606-explore-basecamp/comments" class="new_comment" data-remote="true" id="new_comment" method="post"><div style="margin:0;padding:0;display:inline"><input name="utf8" type="hidden" value="✓"></div>
								<header class="text_entry">
									<input id="comment_commentable_type" name="comment[commentable_type]" type="hidden" value="Todolist">
									<input id="comment_commentable_id" name="comment[commentable_id]" type="hidden" value="1594773">

									<div data-behavior="wysiwyg_container" data-wysiwyg-follow-threshold="136">
										<textarea autofocus="autofocus" class="comment_content" cols="40" data-behavior="autosave autoresize wysiwyg dirty_tracking" id="comment_content" name="comment[content]" placeholder="Add a comment..." rows="4"></textarea>
									</div>

									<div data-behavior="pending_attachments" data-sortable="true" class="attachments">
										<img alt="Paperclip" class="prompt_graphic" src="./Explore Basecamp!  To-do list basics_files/paperclip-2f07a2a52e8bf563b72fb153087637e1.png">
										<div class="file_input_button">
											<span data-without-features="files_api">
											To attach files
											</span>
											<span data-with-features="files_api">
											  To attach files drag &amp; drop here or
											</span>
											<span class="file_input_container">
											  <input name="file" type="file" multiple="" onchange="$(document).trigger(&#39;ie:change&#39;, this)" tabindex="-1">
											  <a href="https://basecamp.com/1903229/projects/678606-explore-basecamp/todolists/1594773-to-do-list-basics#" class="decorated" data-behavior="local_file_picker" tabindex="-1">select files from your computer…</a>
											</span>
										</div>

										<ul class="pending_attachments ui-sortable" style=""></ul>
									</div>
								</header>
								<footer>
									<div data-subscribers="/1903229/projects/678606-explore-basecamp/subscribers?new_comment=true&amp;subscribable_id=1594773&amp;subscribable_type=Todolist"></div>

									<div class="submit">
										<input name="commit" type="submit" value="Add this comment">
									</div>
								</footer>
								</form>  
							</div>
						</article>
					</section>
					<section class="event_stream" id="events_todolist_1594773">
						<h4>历史记录</h4>

					</section>
				</section>
			</div>
		</div>
	</div>
</div>
<div id="shade"></div>
<script type="text/javascript">
$(document).ready(function() {
	var old_todos_ids = new_todos_ids = '';
	$(".todos").sortable({
		start:function(event, ui) {
				var result = $(this).sortable('toArray');
				old_todos_ids= result.join(",");
				old_todos_ids = old_todos_ids.replace(/sortable_todo_/g, "");
			},
		stop:function(event, ui) {
				var result = $(this).sortable('toArray');
				new_todos_ids = result.join(",");
				new_todos_ids = new_todos_ids.replace(/sortable_todo_/g, "");
				if(new_todos_ids != old_todos_ids) {
					$.ajax({
						type: "get",
						url: 'cp.php',
						data: { "project_id":bbcx.currentProject,"ac":"todos","op":"movetodoslist","old_todos_ids":old_todos_ids,"new_todos_ids":new_todos_ids,"inajax":1,"rand":Math.random() },
						success: function(result) {

						}
					});
				}
			}
	}).disableSelection();
});
$(document).ready(function() {
	var a,
    b,
    c,
    d,
    e;
    c = "[data-behavior~=has_hover_content]",
    d = "[data-behavior~=hover_content]:not(.expanded, .ignore_hover)",
    $.support.touch ? $(c).live("tap", 
    function() {
        if ($(this).find(d).filter(function() {
            return $(this).is(":hidden") || $(this).css("visibility") === "hidden"
        }).length) return event.preventDefault(),
        e(this, 
        function(a) {
            return $(a).addClass("popout")
        })
    }) : ($(c).live("mouseenter", 
    function() {
        return e(this)
    }), $(c).live("mouseleave", 
    function() {
        return b(this)
    })),
    e = function(b, c) {
        return $(b).find("" + d + ":not([data-hovercontent-strategy])").each(function() {
            if ($(this).css("visibility") === "hidden") return $(this).attr("data-hovercontent-strategy", "visibility")
        }),
        $(".hiding").each(function() {
            return a($(this), !1)
        }),
        $(b).find(d).each(function() {
            $(this).data("hovercontent-strategy") === "visibility" ? $(this).css("visibility", "visible") : ($.support.touch && $(this).hide(), $(this).show()),
            $(this).trigger("hovercontent:show");
            if (c) return c(this)
        })
    },
    a = function(a, b) {
        var c = this;
        return a.data("hovercontent-strategy") === "visibility" ? b ? (a.addClass("hiding"), setTimeout(function() {
            return a.css("visibility", "hidden"),
            a.removeClass("hiding")
        },
        120)) : a.css("visibility", "hidden") : b ? (a.addClass("hiding"), a.delay(100).fadeOut(100, 
        function() {
            return $(this).removeClass("hiding")
        })) : a.hide(),
        a.trigger("hovercontent:hide")
    },
    b = function(b, c) {
        return c == null && (c = !0),
        $(b).find("" + d + ":visible").each(function() {
            var b;
            b = $(this);
            if (b.css("visibility") === "hidden") return;
            return a(b, c)
        })
    };
});
</script>
</body>
</html>