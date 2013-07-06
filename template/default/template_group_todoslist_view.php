<?php if(!defined('IN_TEAMDOTA')) exit('Access Denied');?>
<!DOCTYPE html>
<html>
<head>
<title><?=$manageproject['name']?>: <?=$todoslist['subject'] ?></title>
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
		<div class="panel sheet_view todo inactive <?php if($todoslist['status'] == 1) { echo "has_notice is_trashed";}?>" data-behavior=" " data-creator-id="<?=$todoslist['uid'] ?>" data-status="<?=get_project_status();?>">
			<header><h1><a href="group.php?do=project&project_id=<?=$project_id?>"><?=$manageproject['name']?></a> > 查看待办事宜清单</h1></header>
			<header>
				<p>
				  所属待办事宜：
				  <a href="group.php?do=todos&project_id=<?=$project_id?>&todos_id=<?=$todos['todos_id']?>" data-replace-sheet="true"><?=$todos['subject']?></a>
				</p>
			</header>
			<?php if($todoslist['status'] == 1) {?>
			<header class="notice">
				该待办事宜清单已被 <?=$trash['sender_author']?> 于 <?=sgmdate('Y年m月d日', $trash['created_time'])?> 删除。
				<?php if($manageproject['status'] == 0) {?>
				<span data-visible-to="admin creator">
					<a href="cp.php?project_id=<?=$project_id?>&ac=<?=$do?>&op=restored&todoslist_id=<?=$todoslist_id ?>&rand=<?=$_SGLOBAL['timestamp'];?>" data-method="post" data-remote="true" rel="nofollow">点击恢复此待办事宜</a>

				或者 <a href="cp.php?project_id=<?=$project_id?>&ac=<?=$do?>&op=realdelete&todos_id=<?=$todoslist['todos_id']?>&todoslist_id=<?=$todoslist_id ?>&rand=<?=$_SGLOBAL['timestamp'];?>" data-method="delete" data-remote="true" rel="nofollow">永久删除</a> ，
					  永久删除之后将不可恢复。
				</span>
				<?php } ?>
			</header>
			<?php } ?>
			<div style="margin-left: 10px; margin-bottom: -50px;" class="panel message">
				<div class="sheet_body_view">
					<section class="perma">
						<article class="todolist" id="sortable_todolist_<?=$todoslist['todos_id']?>">
						<ul class="ui-sortable">
							<?php if($todoslist['status'] == 0) { ?>
							<?php if($todoslist['is_completed'] == 0) { ?>
							<li class="todo show" data-behavior="has_hover_content sortable" data-sortable-type="todo" id="sortable_todo_<?=$todoslist_id ?>">
								<div class="nubbin" data-behavior="nubbin hover_content" style="left: -61px; display: none; ">
									<div class="spacer"></div>
									<a href="javascript:;" onclick="deletetodoslist(this,<?=$todoslist_id ?>,<?=$todoslist['todos_id']?>);" class="image delete" data-confirm="您确定要删除此待办事宜吗？" data-method="post" data-remote="true" rel="nofollow" title="删除">删除</a>
									<a href="javascript:;" onclick="edit_todoslist(<?=$todoslist['todoslist_id']?>);" class="edit" data-remote="true">编辑</a>
								</div>

								<div>
									<span class="wrapper">
										<input data-behavior="toggle" name="todo_complete" type="checkbox" value="1" onclick="todoslist_completed(this,<?=$todoslist['todos_id']?>,<?=$todoslist_id ?>);">
										<span class="content" data-behavior="sortable_handle">
											<a href="javascript:;"><?=$todoslist['subject']?></a>
											<span class="content_for_perma"><?=$todoslist['subject']?></span>
										</span>
										<?php if($todoslist['post_num']) { ?>
										<span class="pill comments">
											<a href="#commentsdata_<?=$todoslist_id ?>"><?=$todoslist['post_num']?>条评论</a>
										</span>
										<?php } ?>
										<?php if(!empty($todoslist['assign_author']) || !empty($todoslist['due_date'])) { ?>
										<span style="position:relative">
											<span class="pill has_balloon" data-behavior="expandable expand_exclusively load_assignee_options">
												<a href="javascript:;" onclick="edit_todoslist(<?=$todoslist_id ?>);" data-behavior="expand_on_click">
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
							<?php } else { ?>
							<li class="todo show" data-behavior="has_hover_content sortable complete" data-sortable-type="todo" id="sortable_todo_<?=$todoslist_id ?>">
								<div class="nubbin" data-behavior="nubbin hover_content" style="left: -35px; display: none; ">
									<div class="spacer"></div>
									<a href="javascript:;" onclick="deletetodoslist(this,<?=$todoslist_id ?>,<?=$todoslist['todos_id']?>);" class="image delete" data-confirm="您确定要删除此待办事宜吗？" data-method="post" data-remote="true" rel="nofollow">删除</a>
								</div>
								<div class="complete">
									<span class="wrapper">
										<input checked="checked" data-behavior="toggle" name="todo_complete" type="checkbox" value="1" onclick="todoslist_nocompleted(this,<?=$todoslist['todos_id']?>,<?=$todoslist_id ?>);">
										<span class="content" data-behavior="sortable_handle">
											<a href="javascript:;"><?=$todoslist['subject']?></a>
											<span class="content_for_perma"><?=$todoslist['subject']?></span>
										</span>
										<span class="completed_by">
										(<?=$todoslist['completed_author']?> 完成于 <?=$todoslist['completed_date']?>)
										</span>
									</span>
								</div>
							</li>
							<?php } ?>
							<?php } elseif($todoslist['status'] == 1) { ?>
							<?php if($todoslist['is_completed'] == 0) { ?>
							<li class="todo show" data-behavior="has_hover_content sortable" data-sortable-type="todo" id="sortable_todo_<?=$todoslist_id ?>">
								<div class="">
									<span class="wrapper">
										<input disabled="disabled" data-behavior="toggle" name="todo_complete" value="1" type="checkbox">

										<span class="content" data-behavior="sortable_handle">
											<a href="javascript:;"><?=$todoslist['subject']?></a>
											<span class="content_for_perma"><?=$todoslist['subject']?></span>
										</span>
									</span>
								</div>
							</li>
							<?php } else { ?>
							<li class="todo show" data-behavior="has_hover_content sortable complete" data-sortable-type="todo" id="sortable_todo_<?=$todoslist_id ?>">
								<div class="complete">
									<span class="wrapper">
										<input disabled="disabled" checked="checked" data-behavior="toggle" name="todo_complete" type="checkbox" value="1">
										<span class="content" data-behavior="sortable_handle">
											<a href="javascript:;"><?=$todoslist['subject']?></a>
											<span class="content_for_perma"><?=$todoslist['subject']?></span>
										</span>
										<span class="completed_by">
										(<?=$todoslist['completed_author']?> 完成于 <?=$todoslist['completed_date']?>)
										</span>
									</span>
								</div>
							</li>
							<?php } ?>
							<?php } ?>
						</ul>
						<ul class="completed truncated" style="display: block; ">
						</ul>
						</article>
					</section>
					<section class="comments"  id="commentsdata_<?=$todoslist_id ?>">
					
					</section>
					<section class="event_stream" id="events_message_<?=$todoslist_id ?>">
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
	ajaxget('group.php?project_id='+bbcx.currentProject+'&do=todoslist&todoslist_id=<?=$todoslist_id ?>&inajax=1','commentsdata_<?=$todoslist_id ?>');
});
<?php if($todoslist['status'] == 0) {?>
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
<?php } ?>
</script>
</body>
</html>