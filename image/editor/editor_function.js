//提交保存
function edit_save() {
	var p = window.frames['qinbaba-ifrHtmlEditor'];
	var obj = p.window.frames['HtmlEditor'];
	document.getElementById('qinbaba-ttHtmlEditor').value = obj.document.body.innerHTML;
}
function reset_edit() {
	var p = window.frames['qinbaba-ifrHtmlEditor'];
	var obj = p.window.frames['HtmlEditor'];
	obj.document.body.innerHTML = '';
	document.getElementById('qinbaba-ttHtmlEditor').value = '';
}