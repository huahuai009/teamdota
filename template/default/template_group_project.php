<?php if(!defined('IN_TEAMDOTA')) exit('Access Denied');?>
<!DOCTYPE html>
<html>
<head>
<title><?=$manageproject['name']?></title>
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
<div class="workspace" style="padding-bottom:30px;">
	<div class="container panel" style="width:1000px; " data-creator-id="<?=$_SGLOBAL['supe_uid']?>">
		<?php if($manageproject['status'] == 1) {?>
		<div class="archived_notice">
			<span>
				该项目已被锁定并存档。
			</span>
			<?php if(check_project_manage($manageproject['uid'])) { ?>
			<span data-visible-to="admin creator">
				<a href="#div_project_settings" onclick="show_project_archived();" data-remote="true" data-stacker="false">点击重新激活</a>
			</span>
			<? } ?>
		</div>
		<? } ?>
		<div class="sheet" id="project_data">
			<div class="collapsed_content" id="project_data_view">
				<div class="position_reference">
					<h1 id="editable_field_prompt_name"><?=$manageproject['name']?> 
					<? if(check_project_manage($manageproject['uid']) && $manageproject['status'] == 0) {?>
					<a href="javascript:;" data-behavior="click_project_edit" onclick="show_project_edit();">[编辑]</a></h1>
					<? } ?>
				</div>
				<div class="description" id="editable_field_prompt_descript">
				  <?=$manageproject['description']?>
				</div>
			</div>
			<?php if(check_project_manage($manageproject['uid'])) {?>
			<div class="expanded_content" id="project_data_edit" data-visible-to="admin creator" style="display:none;">
				<form action="cp.php?ac=project&project_id=<?=$project_id?>" class="new_message" id="newproject_<?=$project_id?>"  method="post">
				<input type="hidden" name="project_id" value="<?=$project_id?>" />
				<input type="hidden" name="projectsubmit" value="true" />
				<input type="hidden" name="formhash" value="<?php echo formhash(); ?>" />
				<div class="position_reference">
					<h1 class="field">
						<textarea cols="40" data-behavior="autoresize submit_on_enter" id="project_name" name="project_name" rows="1" style="resize: none; overflow: hidden; min-height: 19px;" onkeydown="return ctrlEnter(event, 'issuance', 1);"><?=$manageproject['name']?></textarea>
					</h1>
				</div>

				<div class="description field">
						<textarea cols="40" data-behavior="autoresize submit_on_enter" id="project_description" name="project_description" placeholder="添加描述或额外的细节" rows="1" style="resize: none; overflow: hidden; min-height: 19px;" onkeydown="return ctrlEnter(event, 'issuance', 1);"><?=$manageproject['description']?></textarea>
				</div>
				<div class="submit">
					<input id="issuance" name="commit" type="button" onclick="validate_create_project('newproject_<?=$project_id?>','project_edit');" value="保 存" /> <a href="javascript:;" onclick="cancel_project_edit();" class="cancel" data-behavior="cancel" data-role="cancel">取消</a>
				</div>
				</form>
			</div>
			<?php } ?>
			<div class="header_links">
				<a href="group.php?project_id=<?=$project_id?>&do=invite">
					<span class="link"><img height="43" src="image/invite_button.jpg" title="邀请成员" width="165"></span><br>
					<span class="detail">已有<?=$manageproject['member_num']?>位成员参与</span>
				</a>
			</div>
		</div>
		<div class="project_toolbar">
			<div class="tools">
				<div class="group">
					<span data-tool-name="topics">
						<strong><?=$manageproject['discussion_num']?>主题</strong>
					</span>
					<span><strong>|</strong></span>
					<span data-tool-name="todoslist">
						<strong><?=$manageproject['todoslist_num']?>待办事宜</strong>
					</span>
					<span><strong>|</strong></span>
					<span data-tool-name="attachments">
						<strong><?=$manageproject['file_num']?>附件</strong>
					</span>
					<span><strong>|</strong></span>
					<span data-tool-name="documents">
						<strong><?=$manageproject['document_num']?>文档</strong>
					</span>
				</div>
			</div>
		</div>
		<div class="sheet_body">
			<section class="topics " data-collection-name="topics" style="display: block; ">
				<header class="has_buttons">
					<h1></h1>
					<?php if($manageproject['status'] == 0) {?>
					<button data-behavior="new_message file_drop_target" onclick="urlto('cp.php?project_id=<?=$project_id?>&ac=discussion');">创建主题</button>
					<? } ?>
				</header>
				<div id="topics_data"></div>
			</section>
		</div>
		
		<div class="sheet_body_middle">
			<section class="todos" data-collection-name="todolists">
				<header class="has_buttons">
				  <h1></h1>
				  <button data-behavior="new_todolist" onclick="add_todos();">添加待办事宜</button>
				</header>
				<ul class="todolists" data-autoload-url="false" data-behavior="sortable_container" data-sortable-type="todolist">
					<?php if($listtodos) { ?>
					<?php if(is_array($listtodos)) { foreach($listtodos as $key => $value) { ?>
					<li data-behavior="sortable" data-sortable-type="todolist" id="sortable_todolist_<?=$value['todos_id']?>">
						<article class="todolist" id="todolist_<?=$value['todos_id']?>" data-behavior="expandable">
							<header class="collapsed_content" data-behavior="has_hover_content" style="padding-bottom:5px;">
								<div class="nubbin" data-behavior="nubbin hover_content" style="left: -65px; display: none; ">
									<div class="spacer"></div>
									<a href="javascript:;" onclick="deletetodos(this,<?=$value['todos_id']?>);" class="image delete" title="删除">删除</a>
									<a href="javascript:;" onclick="edit_todos(<?=$value['todos_id']?>);" class="edit" data-behavior="edit">编辑</a>
								</div>
								<h3 data-behavior="sortable_handle">
									<a href="group.php?do=todos&project_id=<?=$project_id?>&todos_id=<?=$value['todos_id']?>" class="linked_title"><?=$value['subject']?></a>
									<span class="unlinked_title"><?=$value['subject']?></span>
								</h3>
							</header>
							
							<ul class="todos  ui-sortable" data-behavior="sortable_container" data-sortable-type="todo">
								<?php if($value['theparent']) { ?>
								<?php if(is_array($value['theparent'])) { foreach($value['theparent'] as $keylist => $valuelist) { ?>
								<li class="todo show" data-behavior="has_hover_content sortable" data-sortable-type="todo" id="sortable_todo_<?=$valuelist['todoslist_id']?>">
									<div class="nubbin" data-behavior="nubbin hover_content" style="left: -61px; display: none; ">
									  <div class="spacer"></div>
									  <a href="javascript:;" onclick="deletetodoslist(this,<?=$valuelist['todoslist_id']?>);" class="image delete" data-confirm="您确定要删除此待办事宜吗？" rel="nofollow" title="删除">删除</a>
									  <a href="javascript:;" onclick="edit_todoslist(<?=$valuelist['todoslist_id']?>);" class="edit" data-remote="true">编辑</a>
									</div>
									<div>
										<span class="wrapper">
											<input data-behavior="toggle" name="todo_complete" type="checkbox" value="1" onclick="todoslist_completed(this,<?=$value['todos_id']?>,<?=$valuelist['todoslist_id']?>);">
											<span class="content" data-behavior="sortable_handle">
												<a href="group.php?do=todoslist&project_id=<?=$project_id?>&todoslist_id=<?=$valuelist['todoslist_id']?>"><?=$valuelist['subject']?></a>
												<span class="content_for_perma"><?=$valuelist['subject']?></span>
											</span>
											<?php if($valuelist['post_num']) { ?>
											<span class="pill comments">
												<a href="group.php?do=todoslist&project_id=<?=$project_id?>&todoslist_id=<?=$valuelist['todoslist_id']?>"><?=$valuelist['post_num']?>条评论</a>
											</span>
											<?php } ?>
											<?php if(!empty($valuelist['assign_author']) || !empty($valuelist['due_date'])) { ?>
											<span style="position:relative">
												<span class="pill has_balloon" data-behavior="expandable expand_exclusively load_assignee_options">
													<a href="javascript:;" onclick="edit_todoslist(<?=$valuelist['todoslist_id']?>);" data-behavior="expand_on_click">
														<?php if(!empty($valuelist['assign_author'])) { ?>
														<span data-behavior="assignee_name">
														  <?=$valuelist['assign_author']?>
														</span>
														<?php } ?>
														<?php if(!empty($valuelist['assign_author']) && !empty($valuelist['due_date'])) { ?>
														<span class="separator"> · </span>
														<?php } ?>
														<?php if(!empty($valuelist['due_date'])) { ?>
														<time data-behavior="due_date">
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
							<ul class="new" data-behavior="expandable load_assignee_options" style="padding-top:5px;">
								<li class="collapsed_content">
									<a href="javascript:;" onclick="add_todoslist(<?=$value['todos_id']?>);" class="decorated" data-behavior="expand_on_click load_assignee_options">添加待办事宜</a>
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
					  <a href="group.php?do=todoslist_completed&project_id=<?=$project_id?>" class="decorated">已完成<?=$completed_count?>项待办事宜</a>
					</p>
				</div>
				<? } ?>
			</section>
		</div>
		
		<div class="sheet_body_bottom">
			<div class="sheet_body_bottom_files">
				 <section class="attachments ">
					<header class="has_buttons">
						<h1></h1>
						<?php if($manageproject['status'] == 0) {?>
						<button data-behavior="new_upload file_drop_target" onclick="urlto('cp.php?project_id=<?=$project_id?>&ac=attachment');">上传附件</button>
						<? } ?>
					</header>
					<section class="attachments grouped_by_date">
						<div id="attachments_data"></div>
					</section>
				</section>
			</div>
			
			<div class="sheet_body_bottom_document">
				<section class="documents " data-collection-name="documents" style="display: block; ">
					<header class="has_buttons">
						<h1></h1>
						<?php if($manageproject['status'] == 0) {?>
						<button data-behavior="new_message file_drop_target" onclick="urlto('cp.php?project_id=<?=$project_id?>&ac=document&op=create');">创建文本</button>
						<? } ?>
					</header>
					<section class="documents grouped_by_date">
						<div id="document_data"></div>
					</section>
				</section>
			</div>
			<div class="sheet_body_bottom_member">
				<section class="members " data-collection-name="members" style="display: block; ">
					<header class="has_buttons">
						<h1></h1>
					</header>
					<section class="members grouped_by_date">
						<div style="min-height:84px;">
							<article class="member" data-behavior="link_container">
								<table class="cinbox">
									<tbody>
										<tr>
											<?php if($members) { ?>
											<?php if(is_array($members)) { $i=0;foreach($members as $key => $value) { ?>
											<?php if(($i % 12)==0 && $i != 0) { ?>
											</tr>
											<tr>
											<?php }?>
											<td class="avatar">
									<a href="group.php?do=people&uid=<?=$value['uid']?>"><img class="avatar" height="22" src="<? echo avatar($value['uid'],'40',true);?>" title="<?=$value['fullname']?>" width="24" onerror="this.onerror=null;this.src='/image/avatar.gif'"></a>
											</td>
											<?php $i++;} } } ?>
										</tr>
										
									</tbody>
								</table>
							</article>
						</div>
					</section>
				</section>
			</div>
		</div>
		<?php if(check_project_manage($manageproject['uid']) && $manageproject['status']!=2) { ?>
		<div class="project_settings" id="div_project_settings">
			<div class="project_settings_links">
				<a href="javascript:;" onclick="show_project_archived();" data-remote="true" data-stacker="false" data-visible-to="admin creator">
				<?php if($manageproject['status']==0) { ?>
					存档或删除此项目
				<?php } else { ?>
					激活或删除此项目
				<?php } ?>
				</a>
			</div>
			<div id="project_settings" style="display:none">
				<form action="cp.php?ac=project&project_id=<?=$project_id?>" class="edit_project" id="edit_project_<?=$project_id?>"  method="post">
				<input type="hidden" name="project_id" value="<?=$project_id?>" />
				<input type="hidden" name="projectarchivedsubmit" value="true" />
				<input type="hidden" name="formhash" value="<?php echo formhash(); ?>" />
					<div class="option">
						<small>
							<a href="cp.php?ac=<?=$do?>&project_id=<?=$project_id?>&op=delete&rand=<?=$_SGLOBAL['timestamp'];?>" class="admin"  onclick="if (confirm('你确定你要完全删除这整个项目吗？')) return true; else return false;" >删除此项目</a>
						</small>
						<h4>项目设置</h4>
						<ul>
							<li>
								<label>
									<input <?php if($manageproject['status']==0){echo 'checked="checked"';}?> id="project_archived_false" name="project_archived" type="radio" value="0">
									<strong>活跃</strong> —
								正在使用的项目，可以新增，编辑，邀请，共享等。
								</label>
							</li>

							<li>
								<label>
									<input <?php if($manageproject['status']==1){echo 'checked="checked"';}?> id="project_archived_true" name="project_archived" type="radio" value="1">
									<strong>存档</strong> —
								该项目已被锁定，不能改变，不会包含在套餐的项目数中。
								</label>
							</li>
						</ul>
					</div>
					<div class="submit">
						<input id="issuance_edit_project" name="commit" type="button" onclick="validate_archived_project('edit_project_<?=$project_id?>','project_archived');" value="修 改" /><span id="__edit_project_<?=$project_id?>"></span> <a href="javascript:;" onclick="cancel_project_archived();" class="cancel" data-behavior="cancel" data-role="cancel">取消</a>
					</div>
				</form>
			</div>
		</div>
		<? } ?>
		<?php if(!$manageproject['discussion_num'] && !$manageproject['file_num'] && !$manageproject['document_num'] && !$manageproject['todoslist_num']) {?>
		<article class="blank_slate">
			<div class="blank_slate_body">
				<h1>欢迎来到您的项目！</h1>
				<p>Teamdota可以帮您很好的进行项目协作，开始您的第一次“<a href="cp.php?project_id=<?=$project_id?>&ac=discussion">讨论</a>”，创建一个“<a href="javascript:;" onclick="add_todos();">待办事宜</a>”，或写一个“<a href="cp.php?project_id=<?=$project_id?>&ac=document&op=create">新的文件</a>“。</p>
			</div>
		</article>
		<?php } ?>
	</div>
</div>
<div id="shade"></div>
<script type="text/javascript">
$(document).ready(function() {
	ajaxget('group.php?project_id='+bbcx.currentProject+'&do=discussion&page=<?=$discussion_page?>&inajax=1','topics_data');
	ajaxget('group.php?project_id='+bbcx.currentProject+'&do=attachment&page=<?=$file_page?>&inajax=1','attachments_data');
	ajaxget('group.php?project_id='+bbcx.currentProject+'&do=document&page=<?=$document_page?>&inajax=1','document_data');
	var old_todos_ids = new_todos_ids = old_todolists_ids = new_todolists_ids = '';
	$(".todolists").sortable({
		start:function(event, ui) {
				var result = $(this).sortable('toArray');
				old_todolists_ids = result.join(",");
				old_todolists_ids = old_todolists_ids.replace(/sortable_todolist_/g, "");
			},
		stop:function(event, ui) {
				var result = $(this).sortable('toArray');
				new_todolists_ids = result.join(",");
				new_todolists_ids = new_todolists_ids.replace(/sortable_todolist_/g, "");
				if(new_todolists_ids != old_todolists_ids) {
					$.ajax({
						type: "get",
						url: 'cp.php',
						data: { "project_id":bbcx.currentProject,"ac":"todos","op":"movetodos","old_todolists_ids":old_todolists_ids,"new_todolists_ids":new_todolists_ids,"inajax":1,"rand":Math.random() },
						success: function(result) {
							
						}
					});
				}
			}
	}).disableSelection();
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