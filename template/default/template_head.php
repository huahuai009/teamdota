<?php if(!defined('IN_TEAMDOTA')) exit('Access Denied');?>
<div class="contact_support_button" style="display: block; ">
	<a href="mailto:teamdota@126.com" target="_blank" title="Teamdota 意见反馈">意见反馈</a>
</div>
<header>
	<nav data-behavior="hover_global_nav">
		<div class="logo">
          <a href="group.php?do=home">teamdota</a>
        </div>
        <div class="global_links">
			<?php if($position_hotkey == 1) { ?>
			<span class="triangle triangle_project"></span>
			<a href="group.php?do=home" class="topnav_root currentlink" data-hotkey="1">项目</a>
			<a href="group.php?do=people" class="topnav_everyone notcurrentlink" data-hotkey="2">成员</a>
			<a href="group.php?do=people&uid=<?=$_SGLOBAL['supe_uid']?>" class="topnav_me notcurrentlink" data-hotkey="3" >个人中心</a>
			<a href="/help/" target="_blank" class="topnav_service notcurrentlink" data-hotkey="4" data-restore-position="true">服务中心</a>
			<?php } elseif($position_hotkey == 2) { ?>
			<span class="triangle triangle_member"></span>
			<a href="group.php?do=home" class="topnav_root notcurrentlink" data-hotkey="1">项目</a>
			<a href="group.php?do=people" class="topnav_everyone currentlink" data-hotkey="2">成员</a>
			<a href="group.php?do=people&uid=<?=$_SGLOBAL['supe_uid']?>" class="topnav_me notcurrentlink" data-hotkey="3" >个人中心</a>
			<a href="/help/" target="_blank" class="topnav_service notcurrentlink" data-hotkey="4" data-restore-position="true">服务中心</a>
			<?php } elseif($position_hotkey == 3) {?>
			<span class="triangle triangle_myfeed"></span>
			<a href="group.php?do=home" class="topnav_root notcurrentlink" data-hotkey="1">项目</a>
			<a href="group.php?do=people" class="topnav_everyone notcurrentlink" data-hotkey="2">成员</a>
			<a href="group.php?do=people&uid=<?=$_SGLOBAL['supe_uid']?>" class="topnav_me currentlink" data-hotkey="3" >个人中心</a>
			<a href="/help/" target="_blank" class="topnav_service notcurrentlink" data-hotkey="4" data-restore-position="true">服务中心</a>
			<?php } else { ?>
			<span class="triangle triangle_service"></span>
			<a href="group.php?do=home" class="topnav_root notcurrentlink" data-hotkey="1">项目</a>
			<a href="group.php?do=people" class="topnav_everyone notcurrentlink" data-hotkey="2">成员</a>
			<a href="group.php?do=people&uid=<?=$_SGLOBAL['supe_uid']?>" class="topnav_me notcurrentlink" data-hotkey="3" >个人中心</a>
			<a href="/help/" target="_blank" class="topnav_service currentlink" data-hotkey="4" data-restore-position="true">服务中心</a>
			<?php } ?>
        </div>
		
		<div id="jumpto">
			<input type="text" data-behavior="placeholder" placeholder="搜索..." data-hotkey="f" onKeyUp="global_search(this);">
			<dl style="display: none; "></dl>
		</div>
		
        <div class="current_user">
			<ul>
				<?php if($_SGLOBAL['supe_uid'] == $group['uid']) { ?>
				<li class="account"><a href="cp.php?ac=group" data-replace-stack="true">帐号</a></li>
				<?php } ?>
				<li class="session"><a href="cp.php?ac=common&op=logout&uhash=<?=$_SGLOBAL['uhash'];?>" data-method="delete" data-role="sign_out" data-stacker="false" rel="nofollow">退出</a></li>
			</ul>
        </div>
	</nav>
</header>
<script type="text/javascript">
$(document).click(function(){
	global_search_hide();
});
$("[data-behavior~=placeholder]").click(function(e){
	global_search();
	if (e && e.stopPropagation) {
        e.stopPropagation();
	}
    else {
        window.event.cancelBubble = true;
	}
});  
</script>