<?php
/*
	[TEAMDOTA] (C) 2012 wanghui
	$Id: group_search.php 2012-03-31 09:59Z duty $
*/

if(!defined('IN_TEAMDOTA')) {
	exit('Access Denied');
}
if($_GET['op'] == 'emailcheck') {
	//默认为0，
	//-1 填写的 Email 格式有误
	//-2 填写的 Email 已属于另外一个分组
	//-3 填写的 Email 已加入了该项目
	//-4 该邮箱还未激活过该项目
	//1 该邮箱没人注册过
	//2 该邮箱还没激活
	//3 该邮箱没有绑定过该项目
	
	$result = 0;
	$emailaddress = trim($_GET['email']);
	$project_id = empty($_GET['project_id']) ? 0 : intval($_GET['project_id']);
	if(!isemail($emailaddress)) { 
		$result = -1;
	} else {
		$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('member')." WHERE `email`='{$emailaddress}' LIMIT 1");
		if(!$member = $_SGLOBAL['db']->fetch_array($query)) {
			$result = 1;
		} elseif($member['group_id'] != $group['group_id']) {
			$result = -2;
		}else {
			$querypro = $_SGLOBAL['db']->query("SELECT * FROM ".tname('project_member')." WHERE `project_id`='{$project_id}' AND `uid`='{$member[uid]}' LIMIT 1");
			if(!$memberpro = $_SGLOBAL['db']->fetch_array($querypro)) {
				$result = $project_id;
			} else {
				if($memberpro['isactive'] == 1){
					$result = -4;
				} else {
					$result = -3;
				}
			}
		}
	}
	echo $result;
	exit();
} elseif($_GET['op'] == 'globalsearch') {//全局搜索
	$searchkey = stripsearchkey($_GET['searchkey']);
	$randkey = eregi_replace("[^-0-9A-Za-z]","",($_GET['randkey']));
	$result = '';
	if(!empty($searchkey)) {
		$result .= '<dl data-query="'.$randkey.'" style="display: block;">';
		$query = $_SGLOBAL['db']->query("SELECT uid,fullname FROM ".tname('member')." WHERE `group_id`='{$groupid}' AND `fullname` LIKE '%{$searchkey}%' ORDER BY uid DESC LIMIT 10");
		$k = 0;
		while ($value = $_SGLOBAL['db']->fetch_array($query)) {
			if($k == 0) {
				$result .= '<dt>
								<span>成员</span>
							</dt>';
				$result .= '<dd class="selected" data-search-result="true">
								<a data-default-stack="true" href="group.php?do=people&uid='.$value['uid'].'">'.$value['fullname'].'</a>
							</dd>';
			} else {
				$result .= '<dd data-search-result="true">
								<a data-default-stack="true" href="group.php?do=people&uid='.$value['uid'].'">'.$value['fullname'].'</a>
							</dd>';
			}
			++$k;
		}
		$query = $_SGLOBAL['db']->query("SELECT project_id,name FROM ".tname('project')." WHERE `group_id`='{$groupid}' AND status<>2 AND `name` LIKE '%{$searchkey}%' ORDER BY project_id DESC LIMIT 10");
		$kk = 0;
		while ($value = $_SGLOBAL['db']->fetch_array($query)) {
			if($kk == 0) {
				$result .= '<dt>
								<span>项目</span>
							</dt>';
			}
			if($k == 0) {
				$result .= '<dd class="selected" data-search-result="true">
								<a data-default-stack="true" href="group.php?do=project&project_id='.$value['project_id'].'">'.$value['name'].'</a>
							</dd>';
			} else {
				$result .= '<dd data-search-result="true">
								<a data-default-stack="true" href="group.php?do=project&project_id='.$value['project_id'].'">'.$value['name'].'</a>
							</dd>';
			}
			++$k;
		}
		$result .= '	<dt class="show_all" data-search-result="true">
							<a data-stacker="false" href="group.php?do=search&q='.$searchkey.'">搜索主题，文档，评论…</a>
						</dt>';
		$result .= '</dl>';
	}
	echo $result;
	exit();
} elseif($_GET['op'] == 'email_project_member') {
	$project_id = empty($_GET['project_id']) ? 0 : intval($_GET['project_id']);
	if(empty($project_id)){
		echo '';
		exit;
	}
	$manageproject = checkproject($project_id);
	if(!$manageproject) {
		echo '';
		exit;
	}
	$result = '';
	$arr_uids = array();
	$string_uids = '';
	$discussion_id = empty($_GET['discussion_id'])?0:intval($_GET['discussion_id']);
	$objectid = empty($_GET['objectid'])?0:intval($_GET['objectid']);
	$method = empty($_GET['method'])? '' : $_GET['method'];
	if($discussion_id) {
		$query_notice = $_SGLOBAL['db']->query("SELECT uids,project_id FROM ".tname('notice_discussion')." WHERE discussion_id='{$discussion_id}' LIMIT 1");
		if($notice = $_SGLOBAL['db']->fetch_array($query_notice)) {//查找通知
			if($project_id != $notice['project_id']){
				echo '';
				exit;
			}
			foreach(split(",",$notice['uids']) as $key => $value) {
				$arr_uids[$value] = $value;
			}
		}
	} elseif($method == 'attachment' && $objectid) {
		$query_notice = $_SGLOBAL['db']->query("SELECT uids,project_id FROM ".tname('notice_attachment')." WHERE file_id='{$objectid}' LIMIT 1");
		if($notice = $_SGLOBAL['db']->fetch_array($query_notice)) {//查找通知
			if($project_id != $notice['project_id']){
				echo '';
				exit;
			}
			foreach(split(",",$notice['uids']) as $key => $value) {
				$arr_uids[$value] = $value;
			}
		}
	}
	
	$query = $_SGLOBAL['db']->query("SELECT m.fullname,m.uid FROM ".tname('project_member')." pm,".tname('member')." m WHERE pm.project_id='{$project_id}' AND m.isactive=0 AND pm.uid=m.uid  ORDER BY pm.id ASC");
	$members = array();
	$members_num = 0;
	while($value = $_SGLOBAL['db']->fetch_array($query)) {
		if($value['uid'] != $_SGLOBAL['supe_uid']) {
			if(isset($arr_uids[$value['uid']])) {
				$string_uids .= ' '.$value['fullname'];
			}
			$members[] = $value;
			$members_num ++;
		}
	}
	if(!empty($string_uids)) {
		$result .= '<div class="collapsed_content">
					  您的评论将通过电子邮件发送给:
					  <span data-behavior="subscribers_sentence">'.$string_uids.'</span>
					  (<a href="javascript:;" class="decorated" data-behavior="expand_on_click" onclick="select_change_subscribers();">修改</a>)
					</div>';
	}
	if($members_num) {
		$result .= '<div class="expanded_content"'.(empty($string_uids) ? '' : 'style="display:none;"').'>
						<div data-behavior="subscriber_list" data-subscribable="message">
							<table>
								<tr>
									<td class="subscribers">
										<h4>通过邮件发送评论给该项目的成员：</h4>
										<div class="select_all_or_none">
											<a href="javascript:;" class="decorated select_everyone" onclick="select_message_subscribers();" data-behavior="subscriber_select_all">全选</a> |
											<a href="javascript:;" class="decorated select_everyone" onclick="select_none_message_subscribers();" data-behavior="subscriber_select_none">取消</a>
										</div>

										<div class="subscribers">
											<div>';
		if(is_array($members)) {
			$i=0;
			foreach($members as $key => $value) {
				if(($i % 5)==0 && $i != 0) {
					$result .= '</div>';
					$result .= '<div>';
				}
				$result .= '<div class="column">
								<label data-subscriber-id="'.$value['uid'].' title="'.$value['fullname'].'">
									<input name="message_subscribers[]" type="checkbox" value="'.$value['uid'].'" '.(isset($arr_uids[$value['uid']]) ? 'checked' : '').'>
									'.$value['fullname'].'
								</label>
							</div>';
				$i++;
			}
		}
											
		$result .=' 							<div class="column">
												</div>
											</div>
										</div>
									</td>
								</tr>
							</table>
						</div>
					</div>';
	}
	echo $result;
	exit();
} elseif($_GET['op'] == 'get_project') {
	$project_id = empty($_GET['project_id']) ? 0 : intval($_GET['project_id']);
	if(empty($project_id)){
		echo '';
		exit;
	}
	$manageproject = checkproject($project_id);
	if(!$manageproject) {
		echo '';
		exit;
	}
	$result = '';
	$result .= '<div class="collapsed_content" id="project_data_view">
				<div class="position_reference">
					<h1 id="editable_field_prompt_name">'.$manageproject['name'].'
					<a href="javascript:;" data-behavior="click_project_edit" onclick="show_project_edit();">[编辑]</a></h1>
				</div>
				<div class="description"  id="editable_field_prompt_descript">'.$manageproject['description'].'</div>
			</div>
			<div class="expanded_content" id="project_data_edit" data-visible-to="admin creator" style="display:none;">
				<form action="cp.php?ac=project&project_id='.$project_id.'" class="new_message" id="newproject_'.$project_id.'"  method="post">
				<input type="hidden" name="project_id" value="'.$project_id.'" />
				<input type="hidden" name="projectsubmit" value="true" />
				<input type="hidden" name="formhash" value="'.formhash().'" />
				<div class="position_reference">
						<h1 class="field">
							<textarea cols="40" data-behavior="autoresize submit_on_enter" id="project_name" name="project_name" rows="1" style="resize: none; overflow: hidden; min-height: 19px;" onkeydown="return ctrlEnter(event, \'issuance\', 1);">'.$manageproject['name'].'</textarea>
						</h1>
				</div>

				<div class="description field">
						<textarea cols="40" data-behavior="autoresize submit_on_enter" id="project_description" name="project_description" placeholder="添加描述或额外的细节" rows="1" style="resize: none; overflow: hidden; min-height: 19px;" onkeydown="return ctrlEnter(event, \'issuance\', 1);">'.$manageproject['description'].'</textarea>
				</div>

				<div class="submit">
						<input id="issuance" name="commit" type="button" onclick="validate_create_project(\'newproject_'.$project_id.'\',\'project_edit\');" value="保 存" /> <a href="javascript:;" onclick="cancel_project_edit();" class="cancel" data-behavior="cancel" data-role="cancel">取消</a>
				</div>
				</form>  
			</div>
			<div class="header_links">
				<a href="group.php?project_id='.$project_id.'&do=invite">
					<span class="link"><img height="43" src="image/invite_button.jpg" title="邀请成员" width="165"></span><br>
					<span class="detail">已有'.$manageproject['member_num'].'位成员参与</span>
				</a>
			</div>';
	echo $result;
	exit();
} elseif($_GET['op'] == 'plans') {
	$plan_id = empty($_GET['plan_id']) ? 0 : intval($_GET['plan_id']);
	if(empty($plan_id) || $plan_id > 4) {
		echo '';
		exit;
	}
	$result = '<form action="cp.php?ac=plans" id="newplans_'.$groupid.'" method="post">
					<input type="hidden" name="plan_id" value="'.$plan_id.'" />
					<input type="hidden" name="planssubmit" value="true" />
					<input type="hidden" name="formhash" value="'.formhash().'" />';
	$result .= $_SCONFIG['group_gtype_subject'][$plan_id].'每个月价格为￥'.$_SCONFIG['group_gtype_price'][$plan_id].'，我们确认订单之后，您将会开始使用新的套餐。';
	$result .= '	<fieldset class="credit_card">
						<label for="alipay_orderid">支付宝交易号：</label>
						<input autocomplete="off" class="transaction_field" id="alipay_orderid" maxlength="16" name="alipay_orderid" size="17" type="text">
						
						<label for="expiration_date">到期时间：</label>
						<select class="transaction_field" id="expires_on_month" name="expires_on_month">
							<option value="1">1 - January</option>
							<option value="2">2 - February</option>
							<option value="3">3 - March</option>
							<option value="4">4 - April</option>
							<option selected="selected" value="5">5 - May</option>
							<option value="6">6 - June</option>
							<option value="7">7 - July</option>
							<option value="8">8 - August</option>
							<option value="9">9 - September</option>
							<option value="10">10 - October</option>
							<option value="11">11 - November</option>
							<option value="12">12 - December</option>
						</select>
						<select class="transaction_field" id="expires_on_year" name="expires_on_year">';
	$current_year = intval(sgmdate('Y', $_SGLOBAL['timestamp']));
	for($i = 0; $i < 16; $i++) {
		if($i == 0) {
			$result .= '	<option selected="selected" value="'.$current_year.'">'.$current_year.'</option>';
		} else {
			$result .= '	<option value="'.($current_year + $i).'">'.($current_year + $i).'</option>';
		}
	}
	$result .= '		</select>

						<label for="remarks">备注：</label>
						<input class="transaction_field" id="remarks" maxlength="50" name="remarks" size="11" type="text">
					</fieldset>

					<div class="confirm">
						<div id="buttonplans_'.$groupid.'">
						<input name="commit" type="button" value="确 认" id="issuance" onclick="validate_plans(\'buttonplans_'.$groupid.'\',\'newplans_'.$groupid.'\',\'\');"><span id="__newplans_'.$groupid.'"></span> 或 <a data-behavior="collapse_on_click" onclick="select_plans_hide();">取消</a>
						</div>
						<div id="__buttonplans_'.$groupid.'"></div>
					</div>
				</form>';
	echo $result;
	exit();
}
?>