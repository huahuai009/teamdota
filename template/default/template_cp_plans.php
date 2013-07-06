<?php if(!defined('IN_TEAMDOTA')) exit('Access Denied');?>
<!DOCTYPE html>
<html>
<head>
<title>升级套餐</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link href="css/application.css" media="all" rel="stylesheet" type="text/css">
<link rel="shortcut icon" href="http://www.teamdota.com/favicon.ico" />
<meta charset="utf-8">
<meta name="viewport" content="width=1100">
<style type="text/css">div.panel { min-height: 719px };</style>
<script type="text/javascript">
    window.bbcx = {currentGroup: "<?=$group['group_id']?>",currentPerson: "<?=$_SGLOBAL['supe_uid']?>",currentProject: "<?=$project_id?>",currentDo: "<?=$do?>"};
</script>
<script src="js/jquery.js" type="text/javascript"></script>
<script src="js/global.js" type="text/javascript"></script>
</head>
<body class="windows topnav_root">
<?php $position_hotkey = 4 ; include_once template("head"); ?>
<div id="append_parent"></div>
<div id="workspace" class="workspace">
	<div style="width: 980px;" class="container stack_container">
		<div class="panel sheet_view account plan" style="padding-bottom:100px;">
			
			<title>升级套餐</title>
			<div class="trial_notice">
				<?php if($_SGLOBAL['group_is_time_end']) { ?>
					<?php if($group['gtype'] == 0) { ?>
					<h1>感谢您使用Teamdota，您现在需要选择套餐进行升级，帐户编号：<?=$group['group_id']?></h1>
					<? } else {?>
					<h1>感谢您使用Teamdota，您的<span><?=$_SCONFIG['group_gtype_subject'][$group['gtype']]?>套餐</span>使用期已到，您需要选择套餐进行升级，帐户编号：<?=$group['group_id']?></h1>
					<? } ?>
					<p>请您从下面的列表中选择一个套餐。</p>
					<p>当前的项目和数据会保持在您的帐户下。</p>
					<p>我们确认之后，您的帐户将会进行升级。</p>
				<?php } else { ?>
					<?php if($group['gtype'] == 0) { ?>
					<h1>您是永久免费用户(可以创建的项目数<span><?=$_SCONFIG['group_gtype'][$group['gtype']]?>个</span>，可以使用的文件存储空间<span>256MB</span>)，帐户编号：<?=$group['group_id']?></h1>
					<? } else {?>
					<h1>您还有<span><?=subday($group['endtime'],$_SGLOBAL['timestamp'])?>天左右<?=$_SCONFIG['group_gtype_subject'][$group['gtype']]?>套餐</span>的使用期，帐户编号：<?=$group['group_id']?></h1>
					<? } ?>
					<p>您可以创造很多项目，邀请尽可能多的成员，尽情享受。</p>

					<p>如果您准备好了，请从下面的列表中选择一个套餐。</p>
				<?php } ?>
				<br/>
				<!--<p style="padding-top:20px;padding-bottom:10px;"><img src="image/pay_select_2.png"></p>-->
				<p><span style="padding:8px 10px;color:#000;">支付宝：账户名 >> teamdota@126.com</span></p>
				<p style="padding-top:20px;color:red;line-height:26px;">Teamdota提醒您，目前我们只支持支付宝支付，为识别汇款人，汇款时请多汇或者少汇几分钱，比如80元VIP2，可以汇80.01、80.02 。 <br/>
				汇款后请在第一时间内联系我们，<a target="_blank" href="http://wpa.qq.com/msgrd?v=3&uin=2477399123&site=qq&menu=yes"><img border="0" src="http://wpa.qq.com/pa?p=2:2477399123:49" alt="点击这里给我发消息" title="点击这里给我发消息">　QQ：2477399123</a> 。</p>
			 </div>

			<header>
				<h1><a href="cp.php?ac=plans">升级套餐</a></h1>
			</header>

			<div class="sheet_body_plans">
				<section class="plans">
					<div class="alert"></div>

					<article class="plan vip1" style="padding-bottom:50px;">
						<h2>
							VIP1 ：10 项目,
							3 GB 存储空间
						</h2>
						<h4>
							￥30/月
						</h4>

						<!--<div data-behavior="expandable expand_exclusively" data-subscription="ten_projects">
							<div class="collapsed_content" id="collapsed_content_plans_1">
								  <p class="submit">
									  <input name="commit" type="button" onclick="select_plans_show(1);" value="选择此套餐">
								  </p>   
							</div>
							<div class="expanded_content" id="expanded_content_plans_1">
							</div>
						</div>-->
					</article>

					<article class="plan vip2" style="padding-bottom:50px;">
						<h2>
							VIP2 ：40 项目,
							15 GB 存储空间
						</h2>
						<h4>
							￥80/月
						</h4>

						<!--<div data-behavior="expandable expand_exclusively" data-subscription="forty_projects">
							<div class="collapsed_content" id="collapsed_content_plans_2">
								  <p class="submit">
									  <input name="commit" type="button" onclick="select_plans_show(2);" value="选择此套餐">
								  </p>    
							</div>
							<div class="expanded_content" id="expanded_content_plans_2"></div>
						</div>-->
					</article>

					<article class="plan vip3" style="padding-bottom:50px;">
						<h2>
							VIP3 ：100 项目,
							40 GB 存储空间
						</h2>
						<h4>
							￥150/月
						</h4>

						<!--<div data-behavior="expandable expand_exclusively" data-subscription="one_hundred_projects">
							<div class="collapsed_content" id="collapsed_content_plans_3">
								  <p class="submit">
									  <input name="commit" type="button" onclick="select_plans_show(3);" value="选择此套餐">
								  </p>    
							</div>
							<div class="expanded_content" id="expanded_content_plans_3"></div>
						</div>-->
					</article>

					<article class="plan vip4" style="padding-bottom:50px;">
						<h2>
							VIP4 ：无限 项目,
							100 GB 存储空间
						</h2>
						<h4>
							￥250/月
						</h4>

						<!--<div data-behavior="expandable expand_exclusively" data-subscription="unlimited_projects">
							<div class="collapsed_content" id="collapsed_content_plans_4">
								  <p class="submit">
									  <input name="commit" type="button" onclick="select_plans_show(4);" value="选择此套餐">
								  </p>    
							</div>
							<div class="expanded_content" id="expanded_content_plans_4"></div>
						</div>-->
					</article>
				</section>

				<aside class="receipt trial">
					<h2>目前的使用:</h2>

					<h3>
						<a href="group.php?do=account_project" class="decorated">
					 <?=$group['project_num']?> 活跃项目</a>
					</h3>
					<h3>
						<?=sizecountname($group['attachsize'])?> 存储空间
					</h3>
				</aside>
			</div>
		</div>
	</div>
</div>
</body>
</html>