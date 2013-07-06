var gSetColorType = ""; 
var gIsIE = document.all; 
var gIEVer = fGetIEVer();
var gLoaded = false;
var ev = null;

var pos = 0;
var sLength = 0;

/**
 * 获取event对象
 */
function fGetEv(e){
	ev = e;
}

/**
 * 获取IE版本
 */
function fGetIEVer(){
	var iVerNo = 0;
	var sVer = navigator.userAgent;
	if(sVer.indexOf("MSIE")>-1){
		var sVerNo = sVer.split(";")[1];
		sVerNo = sVerNo.replace("MSIE","");
		iVerNo = parseFloat(sVerNo);
	}
	return iVerNo;
}
/**
 * 设置可编辑
 */
function fSetEditable(){
	var f = window.frames["HtmlEditor"];
	f.document.designMode="on";
	if(!gIsIE)
		f.document.execCommand("useCSS",false, true);
}
/**
 * 设置编辑区域的事件
 */
function fSetFrmClick(){
	var f = window.frames["HtmlEditor"];
	f.document.onclick = function(){
		fHideMenu();
	}
	if(gIsIE) {
		f.document.attachEvent("onkeydown", listenKeyDown);
	} else {
		f.addEventListener('keydown', function(e) {listenKeyDown(e);}, true);
	}
}

/**
 * 监听键盘按键事件
 */
function listenKeyDown(event) {
	parent.gIsEdited = true;
	parent.ctrlEnter(event, 'issuance');
}

/**
 * 设置onload事件
 */
window.onload = function(){
	try{
		gLoaded = true;
		fSetEditable();
		fSetFrmClick();
	}catch(e){
		// window.location.reload();
	}
}
window.onbeforeunload = parent.edit_save;

/**
 * 设置文字颜色
 */
function fSetColor(){
	var dvForeColor =document.getElementById("dvForeColor");
	if(dvForeColor.getElementsByTagName("TABLE").length == 1){
		dvForeColor.innerHTML = drawCube() + dvForeColor.innerHTML;
	}
}


/**
 * 设置mousemove事件
 */
document.onmousemove = function(e){
	if(gIsIE) var el = event.srcElement;
	else var el = e.target;
	var tdView = document.getElementById("tdView");
	var tdColorCode = document.getElementById("tdColorCode");

	if(el.tagName == "IMG"){
		try{
			if(fInObj(el, "dvForeColor")){
				tdView.bgColor = el.parentNode.bgColor;
				tdColorCode.innerHTML = el.parentNode.bgColor
			}
		}catch(e){}
	}
}
/**
 * 判断el对象是否在另一个节点里
 */
function fInObj(el, id){
	if(el){
		if(el.id == id){
			return true;
		}else{
			if(el.parentNode){
				return fInObj(el.parentNode, id);
			}else{
				return false;
			}
		}
	}
}
/**
 * 显示对象
 */
function fDisplayObj(id){
	var o = document.getElementById(id);
	if(o) o.style.display = "";
}
/**
 * 设置onclick事件
 */
document.onclick = function(e){
	if(gIsIE) var el = event.srcElement;
	else var el = e.target;
	var dvForeColor =document.getElementById("dvForeColor");

	if(el.tagName == "IMG"){
		try{
			if(fInObj(el, "dvForeColor")){
				format(gSetColorType, el.parentNode.bgColor);
				dvForeColor.style.display = "none";
				return;
			}
		}catch(e){}
	}
	//弹出层的二次操作禁止关闭
	fHideMenu();
	var hideId = "";
	if(arrMatch[el.id]){
		hideId = arrMatch[el.id];
		fDisplayObj(hideId);
	}
}
var arrMatch = {
	imgFontface:"fontface",
	imgFontsize:"fontsize",
	imgFontColor:"dvForeColor",
	imgBackColor:"dvForeColor",
	imgAlign:"divAlign",
	imgList:"divList",
	imgInOut:"divInOut",
	icoImg:"createImg"
}
/**
 * 执行格式化显示
 */
function format(type, para){
	var f = window.frames["HtmlEditor"];
	var sAlert = "";
	if(sAlert != ""){
		alert(sAlert);
		return;
	}
	f.focus();
	if(!para){
		if(gIsIE){
			f.document.execCommand(type);
		}else{
			f.document.execCommand(type,false,false);
		}
	}else{
		if(type == 'insertHTML') {
			if(window.Event){
				f.document.execCommand('insertHTML', false, para);
			} else {
				var obj = f.document.selection.createRange();
				obj.pasteHTML(para);
				obj.collapse(false);
				obj.select();
			}
		} else {
			f.document.execCommand(type,false,para);
		}
	}
	f.focus();
}
/**
 * 设置字体颜色
 */
function foreColor(e) {
	fDisplayColorBoard(e);
	gSetColorType = "foreColor";
}

/**
 * 设置背景色
 */
function backColor(e){
	var sColor = fDisplayColorBoard(e);
	if(gIsIE)
		gSetColorType = "backcolor";
	else
		gSetColorType = "backcolor";
}

/**
 * 显示颜色拾取器
 */
function fDisplayColorBoard(e){

	if(gIsIE){
		var e = window.event;
	}
	if(gIEVer<=5.01 && gIsIE){
		var arr = showModalDialog("ColorSelect.htm", "", "font-family:Verdana; font-size:12; status:no; dialogWidth:21em; dialogHeight:21em");
		if (arr != null) return arr;
		return;
	}
	var dvForeColor =document.getElementById("dvForeColor");
	// fSetColor();
	var iX = e.clientX;
	var iY = e.clientY;
	dvForeColor.style.display = "";
	dvForeColor.style.left = (iX-30) + "px";
	dvForeColor.style.top = 33 + "px";
	// EV.stopEvent();
	return true;
}

//获取起始焦点
function getCaret() {
	if(gIsIE){
		window.frames["HtmlEditor"].focus();
		var ran = window.frames["HtmlEditor"].document.selection.createRange();
		sLength = ran.text.replace(/\r?\n/g, ' ').length;
		if(!sLength) {
			ran = window.frames["HtmlEditor"].document.body.createTextRange();
		}
		var rang = document.selection.createRange(); 
		rang.setEndPoint("StartToStart", ran);
		pos = rang.text.replace(/\r?\n/g, ' ').length;
	}
}
//重设焦点
function setCaret() {
	if(gIsIE){
		window.frames["HtmlEditor"].focus();
		//重新选持字符串
		var r = window.frames["HtmlEditor"].document.body.createTextRange();
		var textLen = r.text.replace(/\r?\n/g, ' ').length;
		r.moveStart('character', pos);
		if(sLength) {
			var eLen = sLength - (textLen - pos);
			r.moveEnd('character', eLen);
		} else {
			r.collapse(true);
		}
		r.select();
	}
}

/**
 * 删除字符串两边空格
 */
String.prototype.trim = function(){
	return this.replace(/(^\s*)|(\s*$)/g, "");
}

/**
 * 显示下拉菜单
 */
function fDisplayElement(element,displayValue) {
	if(gIEVer<=5.01 && gIsIE){
		alert('只支持IE 5.01以上版本');
		return;
	}
	fHideMenu();
	if ( typeof element == "string" )
		element = document.getElementById(element);
	if (element == null) return;
	element.style.display = displayValue;
	if(gIsIE){
		var e = event;
		var target = e.srcElement;
	}else{
		var e = ev;
		var target = e.target;
	}
	var iX = f_GetX(target);
	element.style.display = "";
	element.style.left = (iX) + "px";
	element.style.top = 33 + "px";
	// EV.stopEvent();
	return true;
}

/**
 * 获取对象的x坐标
 */
function f_GetX(e)
{
	var l=e.offsetLeft;
	while(e=e.offsetParent){				
		l+=e.offsetLeft;
	}
	return l;
}
/**
 * 获取对象的y坐标
 */
function f_GetY(e)
{
	var t=e.offsetTop;
	while(e=e.offsetParent){
		t+=e.offsetTop;
	}
	return t;
}
/**
 * 隐藏下拉菜单
 */
function fHideMenu(){
	try{
		var arr = ["fontface", "fontsize", "dvForeColor", "dvPortrait", "divAlign", "divList" ,"divInOut"];
		for(var i=0;i<arr.length;i++){
			var obj = document.getElementById(arr[i]);
			if(obj){
				obj.style.display = "none";
			}
		}
		try{
			parent.LetterPaper.control(window, "hide");
		}catch(exp){}
	}catch(exp){}
}

/**
 * 隐藏对象
 */
function fHide(obj){
	obj.style.display="none";
}


// Draw color selector
// create 6-element array
var s = "";
var hex = new Array(6)
// assign non-dithered descriptors
hex[0] = "FF"
hex[1] = "CC"
hex[2] = "99"
hex[3] = "66"
hex[4] = "33"
hex[5] = "00"
// draw a single table cell based on all descriptors
function drawCell(red, green, blue) {
	// open cell with specified hexadecimal triplet background color
	var color = '#' + red + green + blue;
	if(color == "#000066") color = "#000000";
	s += '<TD BGCOLOR="' + color + '" style="height:12px;width:12px;" >';
	// print transparent image (use any height and width)
	s += '<IMG '+ ((document.all)?"":"src='editor_none.gif'") +' HEIGHT=12 WIDTH=12>';
	// close table cell
	s += '</TD>';
}
// draw table row based on red and blue descriptors
function drawRow(red, blue) {
	// open table row
	s += '<TR>';

	// loop through all non-dithered color descripters as green hex
	for (var i = 0; i < 6; ++i) {
		drawCell(red, hex[i], blue)
	}
	// close current table row
	s += '</TR>';
}
// draw table for one of six color cube panels
function drawTable(blue) {
	// open table (one of six cube panels)
	s += '<TABLE CELLPADDING=0 CELLSPACING=0 BORDER=0>';
	// loop through all non-dithered color descripters as red hex
	for (var i = 0; i < 6; ++i) {
		drawRow(hex[i], blue)
	}
	// close current table
	s += '</TABLE>';
}
// draw all cube panels inside table cells
function drawCube() {
	// open table
	s += '<TABLE CELLPADDING=0 CELLSPACING=0 style="border:1px #888888 solid"><TR>';
	// loop through all non-dithered color descripters as blue hex
	for (var i = 0; i < 2; ++i) {
		// open table cell with white background color
		s += '<TD BGCOLOR="#FFFFFF">';
		// call function to create cube panel with hex[i] blue hex
		drawTable(hex[i])
		// close current table cell
		s += '</TD>';
	}
	s += '</TR><TR>';
	for (var i = 2; i < 4; ++i) {
		// open table cell with white background color
		s += '<TD BGCOLOR="#FFFFFF">';
		// call function to create cube panel with hex[i] blue hex
		drawTable(hex[i])
		// close current table cell
		s += '</TD>';
	}
	// close table row and table
	s += '</TR></TABLE>';
	return s;
}

/**
 * event函数集合
 * 
 * @class event对象
 */
function EV(){}
EV.getTarget		= fGetTarget;			// 获取target
EV.getEvent			= fGetEvent;			// 获取event
EV.stopEvent		= fStopEvent;			// 取消事件和事件冒泡
EV.stopPropagation	= fStopPropagation;		// 取消事件冒泡
EV.preventDefault	= fPreventDefault;		// 取消事件


function fGetTarget(ev, resolveTextNode){
	if(!ev) ev = this.getEvent();
	var t = ev.target || ev.srcElement;

	if (resolveTextNode && t && "#text" == t.nodeName) {
		return t.parentNode;
	} else {
		return t;
	}
}

function fGetEvent (e) {
	var ev = e || window.event;

	if (!ev) {
		var c = this.getEvent.caller;
		while (c) {
			ev = c.arguments[0];
			if (ev && Event == ev.constructor) {
				break;
			}
			c = c.caller;
		}
	}

	return ev;
}

function fStopEvent(ev) {
	if(!ev) ev = this.getEvent();
	this.stopPropagation(ev);
	this.preventDefault(ev);
}


function fStopPropagation(ev) {
	if(!ev) ev = this.getEvent();
	if (ev.stopPropagation) {
		ev.stopPropagation();
	} else {
		ev.cancelBubble = true;
	}
}


function fPreventDefault(ev) {
	if(!ev) ev = this.getEvent();
	if (ev.preventDefault) {
		ev.preventDefault();
	} else {
		ev.returnValue = false;
	}
}

function getExt(path) {
	return path.lastIndexOf('.') == -1 ? '' : path.substr(path.lastIndexOf('.') + 1, path.length).toLowerCase();
}