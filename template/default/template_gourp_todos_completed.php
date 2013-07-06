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
				<header><h1><a href="group.php?do=project&project_id=<?=$project_id?>"><?=$manageproject['name']?></a> > <a href="group.php?do=todoslist&project_id=<?=$project_id?>">已完成的待办事项</a></h1></header>
			
			
			<div class="sheet_body_view">
				<section class="completed_todos grouped_by_date" data-behavior="infinite_page">
					<header data-behavior="remove_duplicates">
						<div>
						  <h2>July  3</h2>
						  <time data-days-ago="" datetime="2012-07-02T16:00:00Z">Today</time>
						</div>
					</header>

					<article class="todolist" id="todolist_1594773">
						<span class="completed_by">
							<a href="https://basecamp.com/1903229/projects/678606-explore-basecamp/todolists/1594773-to-do-list-basics">To-do list basics</a>
						</span>

						<ul class="todos">
							<li>
								<a href="https://basecamp.com/1903229/projects/678606-explore-basecamp/todos/9815724-a">a</a> <time>completed Jul  3</time>
							</li>
							<li>
								<a href="https://basecamp.com/1903229/projects/678606-explore-basecamp/todos/9815725-a">a</a> <time>completed Jul  3</time>
							</li>
							<li>
								<a href="https://basecamp.com/1903229/projects/678606-explore-basecamp/todos/9815726-a">a</a> <time>completed Jul  3</time>
							</li>
						</ul>
					</article>

					<header data-behavior="remove_duplicates" data-unique-id="date_header_2012-07-02">
						<div>
							<h2>July  2</h2>
							<time data-days-ago="" datetime="2012-07-01T16:00:00Z">Yesterday</time>
						</div>
					</header>

					<article class="todolist" id="todolist_1594773">
						<span class="completed_by">
							<a href="https://basecamp.com/1903229/projects/678606-explore-basecamp/todolists/1594773-to-do-list-basics">To-do list basics</a>
							<span> — <a href="https://basecamp.com/1903229/projects/678606-explore-basecamp" data-stacker="false">Explore Basecamp!</a></span>
						</span>

						<ul class="todos">
							<li>
								<a href="https://basecamp.com/1903229/projects/678606-explore-basecamp/todos/9326264-hover-over-me-and">Hover over me and click the button to my right to assign a due date &gt;</a> <time>completed Jul  2</time>
							</li>
						</ul>
					</article>
				</section>
			</div>
		</div>
	</div>
</div>
<div id="shade"></div>
</body>
</html>