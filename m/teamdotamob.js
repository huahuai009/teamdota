var openapi_server = "http://localhost:8001/openapi/";
var userAgent = navigator.userAgent.toLowerCase();
var is_opera = userAgent.indexOf('opera') != -1 && opera.version();
var is_ie = (userAgent.indexOf('msie') != -1 && !is_opera) && userAgent.substr(userAgent.indexOf('msie') + 5, 3);

function strlen(str) {
	return (is_ie && str.indexOf('\n') != -1) ? str.replace(/\r?\n/g, '_').length : str.length;
}
//LOGIN
function teamdota_login() {
	if( $('#email').val() == '' ) {
		alert("邮件地址不能为空");
		return false;
	} 
	if( $('#password').val() == '' ) {
		alert("密码不能为空");
		return false;
	}
	
	$("#login_btn .ui-btn-text").text('登入中...');
	
	$.post( openapi_server + '?m=login' , {'email':$('#email').val() , 'password':$('#password').val() } , function( data ) {
		
		if( data.code != 0 ) {
			$("#login_btn .ui-btn-text").text('重新登入');
			alert("电子邮箱或者密码错误，请重试");
		} else {
			$("#login_btn .ui-btn-text").text('成功登入，转向中');
			window.localStorage.clear();
			window.localStorage.setItem('token',data.data.token);
			window.localStorage.setItem('email', $('#email').val());
			window.localStorage.setItem('password', $('#password').val());
			
			goto("home_list");
		}
		
	} , 'json' );
}
//logout
function logout() {
	window.localStorage.clear();
	goto("index" );
}
//public
function goto( page , back ) {
	if( window.localStorage.getItem('change_type') == 'fast' ) {
		if( back == 1 )
			$.mobile.changePage(page+'.html' , {'reverse':true} );
		else
			$.mobile.changePage(page+'.html' );
	} else {
		location = page+'.html';
		return true;
	}
}

function db_error(err) {
	alert("Error processing SQL: "+err);
	//console.log( err );
}

function clean() {
	var db = window.openDatabase("TeamdotaDB", "1.0", "TeamdotaDB", 1024*1024);
	db.transaction( function(tx)
	{
		tx.executeSql('DROP TABLE IF EXISTS PROJECT ');
		tx.executeSql('DROP TABLE IF EXISTS DISCUSSION ');
		tx.executeSql('DROP TABLE IF EXISTS DOCUMENT ');
		tx.executeSql('DROP TABLE IF EXISTS TODOS ');
		tx.executeSql('DROP TABLE IF EXISTS TODOSLIST ');
		tx.executeSql('DROP TABLE IF EXISTS ATTACHMENT ');
		
	} , db_error , function(){ alert('清理完成'); }  );
}

function get_data( sql , darray , fn ) {
	var db = window.openDatabase("TeamdotaDB", "1.0", "TeamdotaDB", 1024*1024);
	db.transaction( function( tx ) {
		tx.executeSql( sql , darray , function( tx , results ) {
			var rdata = Array();
			var len = results.rows.length;
			for( var i = 0 ; i < len ; i++  ) {
				rdata[i] = results.rows.item(i);
				rdata[i]['timeline_format'] = get_datetime(rdata[i]['timeline']);
			}
			if( typeof fn == 'function' ) fn(rdata);
		}, db_error);
	}, db_error );
}

function get_datetime(_time) {
	_time = _time * 1000;
	var _tmin1 = 60 * 1000,
	_thor1 = 60 * _tmin1,
	_tday1 = 24 * _thor1;
	if (!_time) {
		return
	}
	if (TEAMDOTA._$isString(_time)) {
		_time = parseInt(_time)
	}
	var _date0 = new Date(),
	_date1 = new Date(_time),
	_delta = _date0.getTime() - _time;
	if (_delta > 13 * _thor1) {
		return TEAMDOTA._$format(_time, (_date1.getFullYear() != _date0.getFullYear() ? "yyyy-": "") + "MM-dd HH:mm")
	}
	for (var i = 12; i > 0; i--) {
		if (_delta > i * _thor1) {
			return i + "小时前"
		}
	}
	if (_delta > 30 * _tmin1) {
		return "半小时前"
	}
	if (_delta > 15 * _tmin1) {
		return "一刻钟前"
	}
	if (_delta > 10 * _tmin1) {
		return "10分钟前"
	}
	if (_delta > 5 * _tmin1) {
		return "5分钟前"
	}
	return "1分钟前"
}

var __remap = {
	a: {
		r: /\<|\>|\&|\r|\n|\s|\'|\"/g,
		"<": "&lt;",
		">": "&gt;",
		"&": "&amp;",
		" ": "&nbsp;",
		'"': "&quot;",
		"'": "&#39;",
		"\n": "<br/>",
		"\r": ""
	},
	b: {
		r: /\&(?:lt|gt|amp|nbsp|#39|quot)\;|\<br\/\>/gi,
		"&lt;": "<",
		"&gt;": ">",
		"&amp;": "&",
		"&nbsp;": " ",
		"&#39;": "'",
		"&quot;": '"',
		"<br/>": "\n"
	},
	c: {
		i: true,
		r: /\byyyy|yy|MM|M|dd|d|HH|H|mm|ms|ss|m|s\b/g
	}
};
var __isType = function(_data, _type) {
	_type = _type.toLowerCase();
	if (_data === null) {
		return _type == "null"
	}
	if (_data === undefined) {
		return _type == "undefined"
	}
	return Object.prototype.toString.call(_data).toLowerCase() == "[object " + _type + "]"
};
var TEAMDOTA = TEAMDOTA || {};
TEAMDOTA._$isFunction = function(_data) {
		return __isType(_data, "function")
};
TEAMDOTA._$isString = function(_data) {
	return __isType(_data, "string")
};
TEAMDOTA._$isNumber = function(_data) {
	return __isType(_data, "number")
};
TEAMDOTA._$isBoolean = function(_data) {
	return __isType(_data, "boolean")
};
TEAMDOTA._$isDate = function(_data) {
	return __isType(_data, "date")
};
TEAMDOTA._$isArray = function(_data) {
	return __isType(_data, "array")
};
TEAMDOTA._$isObject = function(_data) {
	return __isType(_data, "object")
};
TEAMDOTA._$encode = function(_map, _content) {
	if (!_map || !_content || !_content.replace) {
		return _content || ""
	}
	return _content.replace(_map.r,
	function($1) {
		var _result = _map[!_map.i ? $1.toLowerCase() : $1];
		return _result != null ? _result: $1
	})
};
TEAMDOTA._$escape = function(_content) {
	return TEAMDOTA._$encode(__remap.a, _content)
};
TEAMDOTA._$unescape = function(_content) {
	return TEAMDOTA._$encode(__remap.b, _content)
};
TEAMDOTA._$format = (function() {
	var _fmtnmb = function(_number) {
		_number = parseInt(_number) || 0;
		return (_number < 10 ? "0": "") + _number
	};
	return function(_time, _format) {
		if (!_time || !_format) {
			return ""
		}
		if (TEAMDOTA._$isString(_time)) {
			_time = new Date(Date.parse(_time))
		}
		if (!TEAMDOTA._$isDate(_time)) {
			_time = new Date(_time)
		}
		var _map = __remap.c;
		_map.yyyy = _time.getFullYear();
		_map.yy = ("" + _map.yyyy).substr(2);
		_map.M = _time.getMonth() + 1;
		_map.MM = _fmtnmb(_map.M);
		_map.d = _time.getDate();
		_map.dd = _fmtnmb(_map.d);
		_map.H = _time.getHours();
		_map.HH = _fmtnmb(_map.H);
		_map.m = _time.getMinutes();
		_map.mm = _fmtnmb(_map.m);
		_map.s = _time.getSeconds();
		_map.ss = _fmtnmb(_map.s);
		_map.ms = _time.getMilliseconds();
		return TEAMDOTA._$encode(_map, _format)
	}
})();
//project
function project_refresh() {
	refresh_remote_project();
}

function refresh_remote_project( refresh ) {

	$.post( openapi_server + '?m=project' , {'token':window.localStorage.getItem('token') } , function( data ) {
		if( data.code != 0 ) {
			if( data.code == 1001 ) {
				// auth error
				logout();
			} else {
				alert("读取项目列表接口失败");
			}
			
		} else {
			var db = window.openDatabase("TeamdotaDB", "1.0", "TeamdotaDB", 1024*1024);
			db.transaction( function( tx )
			{
				tx.executeSql('DROP TABLE IF EXISTS PROJECT ');
				tx.executeSql('CREATE TABLE IF NOT EXISTS PROJECT ( id unique , name , description )');
				
				for( var i = 0 ; i < data.data.length ; i++  )
				{
					tx.executeSql('INSERT INTO PROJECT ( id , name , description ) VALUES ( ? , ? , ? )' , 
					[
					 data.data[i].id  , 
					 data.data[i].name ,
					 data.data[i].description 
					]
					
					);
				}
			}, db_error , function(){ 
			
			show_local_project(); }  );
		}
		
	} , 'json' );		
}

function show_local_project( refresh ) {
	var db = window.openDatabase("TeamdotaDB", "1.0", "TeamdotaDB", 1024*1024);
	db.transaction( listDB , db_error  );


	function listDB( tx ) {
		tx.executeSql('CREATE TABLE IF NOT EXISTS PROJECT ( id unique , name , description )');
		tx.executeSql('SELECT * FROM PROJECT', [  ], query_success, db_error);
	}

	function query_success(tx, results) {
		var len = results.rows.length;
		if( len > 0 ) {
			var rdata = Array();
			for( var i = 0 ; i < len ; i++ ) {
				rdata[i] = results.rows.item(i);
			}
			$("ul#home_list_ul").empty();
			$("#home_list_temp").tmpl(rdata).appendTo( "ul#home_list_ul" );
			$('ul#home_list_ul').listview('refresh');
			
			$.mobile.fixedToolbars.show(true );
			if( $("#home_list").data("iscroll-plugin") )
				$("#home_list").data("iscroll-plugin").refresh();
				
			if( refresh ) {
				refresh_remote_project( true );
			}
		} else {
			refresh_remote_project( true );
		}
	} 
}

function change_project( projectid , pname , type ) {
	window.localStorage.setItem( 'projectid' , projectid );
	window.localStorage.setItem( 'pname' , pname );
	if(type == 'discussion')
		goto("discussion_list" );
	else if(type == 'todo')
		goto("todos_list" );
	else if(type == 'attachment')
		goto("attachment_list" );
	else if(type == 'document')
		goto("document_list" );
}

function back_home() {
	goto("home_list" , 1 );
}
//discussion
function back_discussion_list() {
	window.localStorage.removeItem('discussionid');
	window.localStorage.removeItem('discussion_post_num');
	goto("discussion_list" , 1 );
}

function discussion_refresh() {
	refresh_remote_discussion(true);
}
function refresh_remote_discussion( refresh ) {
	
	$("#discussion_page_refresh").html('正在读取数据<img src="images/dots.gif"/>');
	
	$.post( openapi_server + '?m=discussion&a=discussion_list' , {'token':window.localStorage.getItem('token'),'projectid':window.localStorage.getItem('projectid') } , function( data ) {
		
		if( data.code != 0 ){
			alert("读取主题列表接口失败");
		} else {
			if( !data.data ) {
				// empty result set
				$("ul#discussion_list_ul").html("<center>暂无数据</center>");
				$("#discussion_page_refresh").html('点击刷新交流区');
				return false;
			}
			
			var db = window.openDatabase("TeamdotaDB", "1.0", "TeamdotaDB", 1024*1024);
			db.transaction( function( tx ) {
				tx.executeSql('DROP TABLE IF EXISTS DISCUSSION');
				tx.executeSql('CREATE TABLE IF NOT EXISTS DISCUSSION ( id unique , discussionid , projectid , uid  , subject , message , author , desp ,timeline , post_num )' );

				for( var i = 0 ; i < data.data.length ; i++  )
				{
					tx.executeSql('INSERT OR REPLACE INTO DISCUSSION ( id , discussionid , projectid , uid , subject , message , author , desp ,timeline , post_num ) VALUES ( ? ,? , ? , ? , ? , ? , ? , ? , ? , ? )' , [
					data.data[i].id
					, data.data[i].discussionid
					, window.localStorage.getItem('projectid')
					, data.data[i].uid
					, data.data[i].subject
					, data.data[i].message
					, data.data[i].author
					, data.data[i].desp 
					, data.data[i].timeline 
					, data.data[i].post_num
					] );
				}
			}, db_error , function() {
				if( refresh ) show_local_discussion();
			});
		}
		
		$("#discussion_page_refresh").html('点击刷新交流区');
		
	} , 'json' );		
}

function show_local_discussion() {
	
	var db = window.openDatabase("TeamdotaDB", "1.0", "TeamdotaDB", 1024*1024);
	db.transaction( listDB , db_error  );

	function listDB( tx ) {
		tx.executeSql('CREATE TABLE IF NOT EXISTS DISCUSSION ( id unique , discussionid , projectid , uid  , subject , message , author , desp ,timeline , post_num )' );
		tx.executeSql('SELECT * FROM DISCUSSION WHERE projectid = ? ORDER BY id DESC LIMIT 10', [  window.localStorage.getItem('projectid') ], query_success, db_error);
	}

	function query_success(tx, results) {
	
		var len = results.rows.length;
		if( len > 0 ) {
			var rdata = Array();
			for( var i = 0 ; i < len ; i++ ) {
				rdata[i] = results.rows.item(i);
				rdata[i]['timeline_format'] = get_datetime(rdata[i]['timeline']);
			}
			window.localStorage.setItem('discussion_order_id' , rdata[len-1].id);
			
			$("ul#discussion_list_ul").empty();
			$("#discussion_list_temp").tmpl(rdata).appendTo( "ul#discussion_list_ul" );
			$('ul#discussion_list_ul').listview('refresh');
			
			$.mobile.fixedToolbars.show(true );
			
			if( $("#discussion_list").data("iscroll-plugin") )
				$("#discussion_list").data("iscroll-plugin").refresh();
		} else {
			refresh_remote_discussion(true);
		}
	}
}
function discussion_more() {
	var discussion_order_id = window.localStorage.getItem('discussion_order_id');
	
	var db = window.openDatabase("TeamdotaDB", "1.0", "TeamdotaDB", 1024*1024);
	db.transaction( listDB , db_error  );

	var wheresql = '';
	var more = 0;
	
	if( discussion_order_id && parseInt(discussion_order_id) > 0 ) {
		wheresql = ' AND id < ? ';
		more = 1;
	}
		
	$("#discussion_page_more").html('正在读取数据<img src="images/dots.gif"/>');
	
	function listDB( tx ) {
		tx.executeSql('CREATE TABLE IF NOT EXISTS DISCUSSION ( id unique , discussionid , projectid , uid  , subject , message , author , desp ,timeline , post_num ) ');
		tx.executeSql('SELECT * FROM DISCUSSION WHERE projectid = ? ' + wheresql + ' ORDER BY id DESC LIMIT 10', [ window.localStorage.getItem('projectid') , parseInt(discussion_order_id) ], query_success, db_error);
	}

	function query_success(tx, results) {
		 
		$("#discussion_page_more").html('加载更多...');
		var len = results.rows.length;
		 
		if( len > 0 ) {
			var rdata = Array();
			for( var i = 0 ; i < len ; i++ ) {
				rdata[i] = results.rows.item(i);
				rdata[i]['timeline_format'] = get_datetime(rdata[i]['timeline']);
			}
			
			window.localStorage.setItem('discussion_order_id' , rdata[len-1].id);
			
			$("#discussion_list_temp").tmpl(rdata).appendTo( "ul#discussion_list_ul" );
			$('ul#discussion_list_ul').listview('refresh');
			$.mobile.fixedToolbars.show(true );	
		}
	}
}
function discussion_content( discussionid , post_num ) {
	window.localStorage.setItem( 'discussionid' , discussionid );
	window.localStorage.setItem( 'discussion_post_num' , post_num );
	goto("discussion_content"  );

}
function show_discussion_content( discussionid ) {
	get_data( "SELECT * FROM DISCUSSION WHERE discussionid = ?" , [discussionid] , function( tx ) {
		$("div#discussion_content_text").empty();
		$("#discussion_content_text_temp").tmpl(tx).appendTo( "div#discussion_content_text" );
	});
}

function show_discussion_attachment( discussionid ) {
	$.post( openapi_server + '?m=discussion&a=discussion_attachment_list' , {'token':window.localStorage.getItem('token'),'projectid':window.localStorage.getItem('projectid') , 'discussionid':discussionid } , function( data ) {
		if( data.code != 0 ) {
			alert("读取主题附件接口失败");
		} else {
			if( !data.data ) {
				$("ul#discussion_attachement_pic").empty();
				$("ul#discussion_attachement_file").empty();
				return false;
			}
			$("ul#discussion_attachement_pic").empty();
			if( data.data.pic ) {
				for( var i = 0 ; i < data.data.pic.length ; i++ ) {
					$("#discussion_attachement_pic").append('<li><figure data-behavior="enlargeable"><a href="javascript:;" title="'+data.data.pic[i].filename+'" ><img alt="" data-container-id="message_'+discussionid+'" data-filename="'+data.data.pic[i].filename+'" data-image-id="'+data.data.pic[i].attachmentid+'" src="'+data.data.pic[i].icon+'" data-scaled="true" data-original-src="'+data.data.pic[i].icon+'"></a></figure></li>');
				}
			}
			$("ul#discussion_attachement_file").empty();
			if( data.data.file ) {
				for( var i = 0 ; i < data.data.file.length ; i++ ) {
					$("#discussion_attachement_file").append('<li><img class="file_icon"  border="0" src="'+data.data.file[i].icon+'" height="32" width="32""><br>'+data.data.file[i].filename+'</li>');
				}
			}
		}		
	} , 'json' );
}

function show_discussion_comment( discussionid ) {
	$.post( openapi_server + '?m=discussion&a=discussion_comment_list' , {'token':window.localStorage.getItem('token'),'projectid':window.localStorage.getItem('projectid') , 'discussionid':discussionid } , function( data ) {
		if( data.code != 0 ) {
			alert("读取评论列表接口失败");
		} else {
			if( !data.data ) {
				$("ul#discussion_comment_list").empty();
				return false;
			}
			$("ul#discussion_comment_list").empty();
			for( var i = 0 ; i < data.data.length ; i++ ) {
				data.data[i]['timeline_format'] = get_datetime(data.data[i]['timeline']);
				$("ul#discussion_comment_list").append('<li class="formatted_content">'+data.data[i]['author']+'&nbsp;:&nbsp;'+data.data[i]['message']+'&nbsp;<span class="time">'+data.data[i]['timeline_format']+'</span>');
				if( data.data[i].pic ) {
					$("ul#discussion_comment_list").append('<ul class="gallery">');
					for( var k = 0 ; k < data.data[i].pic.length ; k++ ) {
						$("ul#discussion_comment_list").append('<li><figure data-behavior="enlargeable"><a href="javascript:;" title="'+data.data[i].pic[k].filename+'" ><img alt="" data-container-id="message_'+discussionid+'" data-filename="'+data.data[i].pic[k].filename+'" data-image-id="'+data.data[i].pic[k].attachmentid+'" src="'+data.data[i].pic[k].icon+'" data-scaled="true" data-original-src="'+data.data[i].pic[k].icon+'"></a></figure></li>');
					}
					$("ul#discussion_comment_list").append('</ul>');
				}
				$("ul#discussion_comment_list").append('</li>');
			}
			$('ul#discussion_comment_list').listview('refresh');
		}		
	} , 'json' );
}

function send_discussion_comment() {
	var discussionid = window.localStorage.getItem('discussionid');
	var message = $('#comment_text').val();
	var slen = strlen(message);
	if (slen < 2) {
		alert("评论内容不能少于2个字符");
		return false;
	}
	$.post( openapi_server + '?m=discussion&a=send_comment' , {'token':window.localStorage.getItem('token'),'projectid':window.localStorage.getItem('projectid') , 'discussionid':discussionid , 'message':message } , function( data ) {
		if( data.code != 0 ) {
			alert(data.code);
			alert("发送评论失败");
		} else {
			$('#comment_text').val('');
			show_discussion_comment(discussionid);
		}		
	} , 'json' );
}

function add_discussion() {
	var subject = $('#subject').val();
	var message = $('#message').val();
	var slen = strlen(subject);
	if (slen < 1 || slen > 80) {
		alert("标题长度(1~80字符)不符合要求");
		return false;
	}
	$.post( openapi_server + '?m=discussion&a=add_discussion' , {'token':window.localStorage.getItem('token'),'projectid':window.localStorage.getItem('projectid') , 'subject':subject , 'message':message } , function( data ) {
		if( data.code != 0 ) {
			alert("添加主题失败");
		} else {
			$('#subject').val('');
			$('#message').val('');
			discussion_refresh();
			goto("discussion_list" , 1);
		}		
	} , 'json' );	
}
//document
function back_document_list() {
	window.localStorage.removeItem('documentid');
	window.localStorage.removeItem('document_post_num');
	goto("document_list" , 1 );
}

function document_refresh() {
	refresh_remote_document(true);
}
function refresh_remote_document( refresh ) {
	
	$("#document_page_refresh").html('正在读取数据<img src="images/dots.gif"/>');
	
	$.post( openapi_server + '?m=document&a=document_list' , {'token':window.localStorage.getItem('token'),'projectid':window.localStorage.getItem('projectid') } , function( data ) {
		
		if( data.code != 0 ){
			alert("读取文档列表接口失败");
		} else {
			if( !data.data ) {
				// empty result set
				$("ul#document_list_ul").html("<center>暂无数据</center>");
				$("#document_page_refresh").html('点击刷新文档区');
				return false;
			}
			
			var db = window.openDatabase("TeamdotaDB", "1.0", "TeamdotaDB", 1024*1024);
			db.transaction( function( tx ) {
				tx.executeSql('DROP TABLE IF EXISTS DOCUMENT');
				tx.executeSql('CREATE TABLE IF NOT EXISTS DOCUMENT ( id unique , documentid , projectid , uid  , subject , message , author ,timeline , post_num )' );

				for( var i = 0 ; i < data.data.length ; i++  )
				{
					tx.executeSql('INSERT OR REPLACE INTO DOCUMENT ( id , documentid , projectid , uid , subject , message , author ,timeline , post_num ) VALUES ( ? ,? , ? , ? , ? , ? , ? , ? , ? )' , [
					data.data[i].id
					, data.data[i].documentid
					, window.localStorage.getItem('projectid')
					, data.data[i].uid
					, data.data[i].subject
					, data.data[i].message
					, data.data[i].author
					, data.data[i].timeline 
					, data.data[i].post_num
					] );
				}
			}, db_error , function() {
				if( refresh ) show_local_document();
			});
		}
		
		$("#document_page_refresh").html('点击刷新文档区');
		
	} , 'json' );		
}

function show_local_document() {
	
	var db = window.openDatabase("TeamdotaDB", "1.0", "TeamdotaDB", 1024*1024);
	db.transaction( listDB , db_error  );

	function listDB( tx ) {
		tx.executeSql('CREATE TABLE IF NOT EXISTS DOCUMENT ( id unique , documentid , projectid , uid  , subject , message , author ,timeline , post_num )' );
		tx.executeSql('SELECT * FROM DOCUMENT WHERE projectid = ? ORDER BY id DESC LIMIT 10', [  window.localStorage.getItem('projectid') ], query_success, db_error);
	}

	function query_success(tx, results) {
	
		var len = results.rows.length;
		if( len > 0 ) {
			var rdata = Array();
			for( var i = 0 ; i < len ; i++ ) {
				rdata[i] = results.rows.item(i);
				rdata[i]['timeline_format'] = get_datetime(rdata[i]['timeline']);
			}
			window.localStorage.setItem('document_order_id' , rdata[len-1].id);
			
			$("ul#document_list_ul").empty();
			$("#document_list_temp").tmpl(rdata).appendTo( "ul#document_list_ul" );
			$('ul#document_list_ul').listview('refresh');
			
			$.mobile.fixedToolbars.show(true );
			
			if( $("#document_list").data("iscroll-plugin") )
				$("#document_list").data("iscroll-plugin").refresh();
		} else {
			refresh_remote_document(true);
		}
	}
}
function document_more() {
	var document_order_id = window.localStorage.getItem('document_order_id');
	
	var db = window.openDatabase("TeamdotaDB", "1.0", "TeamdotaDB", 1024*1024);
	db.transaction( listDB , db_error  );

	var wheresql = '';
	var more = 0;
	
	if( document_order_id && parseInt(document_order_id) > 0 ) {
		wheresql = ' AND id < ? ';
		more = 1;
	}
		
	$("#document_page_more").html('正在读取数据<img src="images/dots.gif"/>');
	
	function listDB( tx ) {
		tx.executeSql('CREATE TABLE IF NOT EXISTS DOCUMENT ( id unique , documentid , projectid , uid  , subject , message , author ,timeline , post_num ) ');
		tx.executeSql('SELECT * FROM DOCUMENT WHERE projectid = ? ' + wheresql + ' ORDER BY id DESC LIMIT 10', [ window.localStorage.getItem('projectid') , parseInt(document_order_id) ], query_success, db_error);
	}

	function query_success(tx, results) {
		 
		$("#document_page_more").html('加载更多...');
		var len = results.rows.length;
		 
		if( len > 0 ) {
			var rdata = Array();
			for( var i = 0 ; i < len ; i++ ) {
				rdata[i] = results.rows.item(i);
				rdata[i]['timeline_format'] = get_datetime(rdata[i]['timeline']);
			}
			
			window.localStorage.setItem('document_order_id' , rdata[len-1].id);
			
			$("#document_list_temp").tmpl(rdata).appendTo( "ul#document_list_ul" );
			$('ul#document_list_ul').listview('refresh');
			$.mobile.fixedToolbars.show(true );	
		}
	}
}
function document_content( documentid , post_num ) {
	window.localStorage.setItem( 'documentid' , documentid );
	window.localStorage.setItem( 'document_post_num' , post_num );
	goto("document_content"  );

}
function show_document_content( documentid ) {
	get_data( "SELECT * FROM DOCUMENT WHERE documentid = ?" , [documentid] , function( tx ) {
		$("div#document_content_text").empty();
		$("#document_content_text_temp").tmpl(tx).appendTo( "div#document_content_text" );
	});
}

function show_document_comment( documentid ) {
	$.post( openapi_server + '?m=document&a=document_comment_list' , {'token':window.localStorage.getItem('token'),'projectid':window.localStorage.getItem('projectid') , 'documentid':documentid } , function( data ) {
		if( data.code != 0 ) {
			alert("读取评论列表接口失败");
		} else {
			if( !data.data ) {
				$("ul#document_comment_list").empty();
				return false;
			}
			for( var i = 0 ; i < data.data.length ; i++ ) {
				data.data[i]['timeline_format'] = get_datetime(data.data[i]['timeline']);
			}
			$("ul#document_comment_list").empty();
			$("#document_comment_list_temp").tmpl(data.data).appendTo( "ul#document_comment_list" );
			$('ul#document_comment_list').listview('refresh');
		}		
	} , 'json' );
}

function send_document_comment() {
	var documentid = window.localStorage.getItem('documentid');
	var message = $('#comment_text').val();
	var slen = strlen(message);
	if (slen < 2) {
		alert("评论内容不能少于2个字符");
		return false;
	}
	$.post( openapi_server + '?m=document&a=send_comment' , {'token':window.localStorage.getItem('token'),'projectid':window.localStorage.getItem('projectid') , 'documentid':documentid , 'message':message } , function( data ) {
		if( data.code != 0 ) {
			alert(data.code);
			alert("发送评论失败");
		} else {
			$('#comment_text').val('');
			show_document_comment(documentid);
		}		
	} , 'json' );
}

function add_document() {
	var subject = $('#subject').val();
	var message = $('#message').val();
	var slen = strlen(subject);
	if (slen < 1 || slen > 80) {
		alert("标题长度(1~80字符)不符合要求");
		return false;
	}
	$.post( openapi_server + '?m=document&a=add_document' , {'token':window.localStorage.getItem('token'),'projectid':window.localStorage.getItem('projectid') , 'subject':subject , 'message':message } , function( data ) {
		if( data.code != 0 ) {
			alert("添加文档失败");
		} else {
			$('#subject').val('');
			$('#message').val('');
			document_refresh();
			goto("document_list" , 1);
		}		
	} , 'json' );	
}
//todos
function back_todos_list() {
	window.localStorage.removeItem('todosid');
	window.localStorage.removeItem('todos_post_num');
	goto("todos_list" , 1 );
}

function todos_refresh() {
	refresh_remote_todos(true);
}
function refresh_remote_todos( refresh ) {
	
	$("#todos_page_refresh").html('正在读取数据<img src="images/dots.gif"/>');
	
	$.post( openapi_server + '?m=todos&a=todos_list' , {'token':window.localStorage.getItem('token'),'projectid':window.localStorage.getItem('projectid') } , function( data ) {
		
		if( data.code != 0 ){
			alert("读取文档列表接口失败");
		} else {
			if( !data.data ) {
				// empty result set
				$("ul#todos_list_ul").html("<center>暂无数据</center>");
				$("#todos_page_refresh").html('点击刷新待办事宜');
				return false;
			}
			
			var db = window.openDatabase("TeamdotaDB", "1.0", "TeamdotaDB", 1024*1024);
			db.transaction( function( tx ) {
				tx.executeSql('DROP TABLE IF EXISTS TODOS');
				tx.executeSql('CREATE TABLE IF NOT EXISTS TODOS ( id unique , todosid , projectid , uid  , subject , author , post_num )' );

				for( var i = 0 ; i < data.data.length ; i++  )
				{
					tx.executeSql('INSERT OR REPLACE INTO TODOS ( id , todosid , projectid , uid , subject , author , post_num ) VALUES ( ? ,? , ? , ? , ? , ? , ? )' , [
					data.data[i].id
					, data.data[i].todosid
					, window.localStorage.getItem('projectid')
					, data.data[i].uid
					, data.data[i].subject
					, data.data[i].author
					, data.data[i].post_num
					] );
				}
			}, db_error , function() {
				if( refresh ) show_local_todos();
			});
		}
		
		$("#todos_page_refresh").html('点击刷新待办事宜');
		
	} , 'json' );		
}

function show_local_todos() {
	
	var db = window.openDatabase("TeamdotaDB", "1.0", "TeamdotaDB", 1024*1024);
	db.transaction( listDB , db_error  );

	function listDB( tx ) {
		tx.executeSql('CREATE TABLE IF NOT EXISTS TODOS ( id unique , todosid , projectid , uid  , subject , author , post_num )' );
		tx.executeSql('SELECT * FROM TODOS WHERE projectid = ? ORDER BY id DESC', [  window.localStorage.getItem('projectid') ], query_success, db_error);
	}

	function query_success(tx, results) {
	
		var len = results.rows.length;
		if( len > 0 ) {
			var rdata = Array();
			for( var i = 0 ; i < len ; i++ ) {
				rdata[i] = results.rows.item(i);
			}
			
			$("ul#todos_list_ul").empty();
			$("#todos_list_temp").tmpl(rdata).appendTo( "ul#todos_list_ul" );
			$('ul#todos_list_ul').listview('refresh');
			
			$.mobile.fixedToolbars.show(true );
			
			if( $("#todos_list").data("iscroll-plugin") )
				$("#todos_list").data("iscroll-plugin").refresh();
		} else {
			refresh_remote_todos(true);
		}
	}
}
function todos_content( todosid , post_num , subject ) {
	window.localStorage.setItem( 'todosid' , todosid );
	window.localStorage.setItem( 'todos_post_num' , post_num );
	window.localStorage.setItem( 'todosname' , subject );
	goto("todoslist_list"  );

}
function show_todos_content( todosid ) {
	get_data( "SELECT * FROM TODOS WHERE todosid = ?" , [todosid] , function( tx ) {
		$("div#todos_content_text").empty();
		$("#todos_content_text_temp").tmpl(tx).appendTo( "div#todos_content_text" );
	});
}

function show_todos_comment( todosid ) {
	$.post( openapi_server + '?m=todos&a=todos_comment_list' , {'token':window.localStorage.getItem('token'),'projectid':window.localStorage.getItem('projectid') , 'todosid':todosid } , function( data ) {
		if( data.code != 0 ) {
			alert("读取评论列表接口失败");
		} else {
			if( !data.data ) {
				$("ul#todos_comment_list").empty();
				return false;
			}
			for( var i = 0 ; i < data.data.length ; i++ ) {
				data.data[i]['timeline_format'] = get_datetime(data.data[i]['timeline']);
			}
			$("ul#todos_comment_list").empty();
			$("#todos_comment_list_temp").tmpl(data.data).appendTo( "ul#todos_comment_list" );
			$('ul#todos_comment_list').listview('refresh');
		}		
	} , 'json' );
}

function send_todos_comment() {
	var todosid = window.localStorage.getItem('todosid');
	var message = $('#comment_text').val();
	var slen = strlen(message);
	if (slen < 2) {
		alert("评论内容不能少于2个字符");
		return false;
	}
	$.post( openapi_server + '?m=todos&a=send_comment' , {'token':window.localStorage.getItem('token'),'projectid':window.localStorage.getItem('projectid') , 'todosid':todosid , 'message':message } , function( data ) {
		if( data.code != 0 ) {
			alert(data.code);
			alert("发送评论失败");
		} else {
			$('#comment_text').val('');
			show_todos_comment(todosid);
		}		
	} , 'json' );
}

function add_todos() {
	var subject = $('#subject').val();
	var slen = strlen(subject);
	if (slen < 1 || slen > 80) {
		alert("内容长度(1~80字符)不符合要求");
		return false;
	}
	$.post( openapi_server + '?m=todos&a=add_todos' , {'token':window.localStorage.getItem('token'),'projectid':window.localStorage.getItem('projectid') , 'subject':subject } , function( data ) {
		if( data.code != 0 ) {
			alert("添加待办事宜失败");
		} else {
			$('#subject').val('');
			$('#message').val('');
			todos_refresh();
			goto("todos_list" , 1);
		}		
	} , 'json' );	
}
//todoslist
function back_todoslist_list() {
	window.localStorage.removeItem('todoslistid');
	window.localStorage.removeItem('todoslist_post_num');
	goto("todoslist_list" , 1 );
}

function todoslist_refresh() {
	refresh_remote_todoslist(true);
}
function refresh_remote_todoslist( refresh ) {
	
	$("#todoslist_page_refresh").html('正在读取数据<img src="images/dots.gif"/>');
	
	$.post( openapi_server + '?m=todoslist&a=todoslist_list' , {'token':window.localStorage.getItem('token'),'projectid':window.localStorage.getItem('projectid'),'todosid':window.localStorage.getItem('todosid') } , function( data ) {

		if( data.code != 0 ){
			alert("读取待办事宜详情接口失败");
		} else {
			if( !data.data ) {
				// empty result set
				$("ul#todoslist_list_ul").html("<center>暂无数据</center>");
				$("#todoslist_page_refresh").html('点击刷新待办事宜详情');
				return false;
			}
			
			var db = window.openDatabase("TeamdotaDB", "1.0", "TeamdotaDB", 1024*1024);
			db.transaction( function( tx ) {
				tx.executeSql('DROP TABLE IF EXISTS TODOSLIST');
				tx.executeSql('CREATE TABLE IF NOT EXISTS TODOSLIST ( id unique , todoslistid , todosid , projectid , uid  , subject , author , assign_author , due_date , post_num )' );

				for( var i = 0 ; i < data.data.length ; i++  )
				{
					tx.executeSql('INSERT OR REPLACE INTO TODOSLIST ( id , todoslistid , todosid , projectid , uid , subject , author , assign_author , due_date , post_num ) VALUES ( ? ,? , ? , ? , ? , ? , ? , ? , ? , ? )' , [
					data.data[i].id
					, data.data[i].todoslistid
					, data.data[i].todosid
					, window.localStorage.getItem('projectid')
					, data.data[i].uid
					, data.data[i].subject
					, data.data[i].author
					, data.data[i].assign_author
					, data.data[i].due_date
					, data.data[i].post_num
					] );
				}
			}, db_error , function() {
				if( refresh ) show_local_todoslist();
			});
		}
		
		$("#todoslist_page_refresh").html('点击刷新待办事宜详情');
		
	} , 'json' );		
}

function show_local_todoslist() {
	
	var db = window.openDatabase("TeamdotaDB", "1.0", "TeamdotaDB", 1024*1024);
	db.transaction( listDB , db_error  );

	function listDB( tx ) {
		tx.executeSql('CREATE TABLE IF NOT EXISTS TODOSLIST ( id unique , todoslistid , todosid , projectid , uid  , subject , author , assign_author , due_date , post_num )' );
		tx.executeSql('SELECT * FROM TODOSLIST WHERE projectid = ? AND todosid = ? ORDER BY id DESC', [  window.localStorage.getItem('projectid') , window.localStorage.getItem('todosid') ], query_success, db_error);
	}

	function query_success(tx, results) {
	
		var len = results.rows.length;
		if( len > 0 ) {
			var rdata = Array();
			for( var i = 0 ; i < len ; i++ ) {
				rdata[i] = results.rows.item(i);
			}
			
			$("ul#todoslist_list_ul").empty();
			$("#todoslist_list_temp").tmpl(rdata).appendTo( "ul#todoslist_list_ul" );
			$('ul#todoslist_list_ul').listview('refresh');
			
			$.mobile.fixedToolbars.show(true );
			
			if( $("#todoslist_list").data("iscroll-plugin") )
				$("#todoslist_list").data("iscroll-plugin").refresh();
		} else {
			refresh_remote_todoslist(true);
		}
	}
}
function todoslist_content( todoslistid , post_num ) {
	window.localStorage.setItem( 'todoslistid' , todoslistid );
	window.localStorage.setItem( 'todoslist_post_num' , post_num );
	goto("todoslist_content"  );

}
function show_todoslist_content( todoslistid ) {
	get_data( "SELECT * FROM TODOSLIST WHERE todoslistid = ?" , [todoslistid] , function( tx ) {
		$("div#todoslist_content_text").empty();
		$("#todoslist_content_text_temp").tmpl(tx).appendTo( "div#todoslist_content_text" );
	});
}

function show_todoslist_comment( todoslistid ) {
	$.post( openapi_server + '?m=todoslist&a=todoslist_comment_list' , {'token':window.localStorage.getItem('token'),'projectid':window.localStorage.getItem('projectid') , 'todoslistid':todoslistid } , function( data ) {
		if( data.code != 0 ) {
			alert("读取评论列表接口失败");
		} else {
			if( !data.data ) {
				$("ul#todoslist_comment_list").empty();
				return false;
			}
			for( var i = 0 ; i < data.data.length ; i++ ) {
				data.data[i]['timeline_format'] = get_datetime(data.data[i]['timeline']);
			}
			$("ul#todoslist_comment_list").empty();
			$("#todoslist_comment_list_temp").tmpl(data.data).appendTo( "ul#todoslist_comment_list" );
			$('ul#todoslist_comment_list').listview('refresh');
		}		
	} , 'json' );
}

function send_todoslist_comment() {
	var todoslistid = window.localStorage.getItem('todoslistid');
	var message = $('#comment_text').val();
	var slen = strlen(message);
	if (slen < 2) {
		alert("评论内容不能少于2个字符");
		return false;
	}
	$.post( openapi_server + '?m=todoslist&a=send_comment' , {'token':window.localStorage.getItem('token'),'projectid':window.localStorage.getItem('projectid') , 'todoslistid':todoslistid , 'message':message } , function( data ) {
		if( data.code != 0 ) {
			alert(data.code);
			alert("发送评论失败");
		} else {
			$('#comment_text').val('');
			show_todoslist_comment(todoslistid);
		}		
	} , 'json' );
}

function add_todoslist() {
	var subject = $('#subject').val();
	var todo_due_at = $('#todo_due_at').val();
	var todo_assignee_code = $('#todo_assignee_code').val();
	var slen = strlen(subject);
	if (slen < 1 || slen > 80) {
		alert("内容长度(1~80字符)不符合要求");
		return false;
	}
	$.post( openapi_server + '?m=todoslist&a=add_todoslist' , {'token':window.localStorage.getItem('token'),'projectid':window.localStorage.getItem('projectid') ,'todosid':window.localStorage.getItem('todosid') , 'subject':subject , 'todo_due_at':todo_due_at , 'todo_assignee_code':todo_assignee_code } , function( data ) {

		if( data.code != 0 ) {
			alert("添加待办事宜详情失败");
		} else {
			$('#subject').val('');
			todoslist_refresh();
			goto("todoslist_list" , 1);
		}		
	} , 'json' );	
}

function show_project_memeber( ) {
	$("#todo_assignee_code").empty();
	$("#todo_assignee_code").append("<option value=''>分配该待办事宜给:</option>");
	$.post( openapi_server + '?m=people&a=isactive_people_list' , {'token':window.localStorage.getItem('token'),'projectid':window.localStorage.getItem('projectid') } , function( data ) {
		if( data.code == 0 ) {
			if( !data.data ) {
				return false;
			}
			for( var i = 0 ; i < data.data.length ; i++ ) {
				data.data[i]['timeline_format'] = get_datetime(data.data[i]['timeline']);
				$("#todo_assignee_code").append("<option value='"+data.data[i]['uid']+"'>"+data.data[i]['nickname']+"</option>");
			}
			$("#todo_assignee_code").selectmenu("refresh");
		}		
	} , 'json' );
}
//attachment
function back_attachment_list() {
	window.localStorage.removeItem('attachmentid');
	window.localStorage.removeItem('attachment_post_num');
	goto("attachment_list" , 1 );
}

function attachment_refresh() {
	refresh_remote_attachment(true);
}
function refresh_remote_attachment( refresh ) {
	
	$("#attachment_page_refresh").html('正在读取数据<img src="images/dots.gif"/>');
	
	$.post( openapi_server + '?m=attachment&a=attachment_list' , {'token':window.localStorage.getItem('token'),'projectid':window.localStorage.getItem('projectid') } , function( data ) {
		
		if( data.code != 0 ){
			alert("读取文档列表接口失败");
		} else {
			if( !data.data ) {
				// empty result set
				$("ul#attachment_list_ul").html("<center>暂无数据</center>");
				$("#attachment_page_refresh").html('点击刷新附件');
				return false;
			}
			
			var db = window.openDatabase("TeamdotaDB", "1.0", "TeamdotaDB", 1024*1024);
			db.transaction( function( tx ) {
				tx.executeSql('DROP TABLE IF EXISTS ATTACHMENT');
				tx.executeSql('CREATE TABLE IF NOT EXISTS ATTACHMENT ( id unique , attachmentid , projectid , uid  , filename , icon , fileurl , author , timeline , isimage , type ,discussionid , post_num )' );

				for( var i = 0 ; i < data.data.length ; i++  )
				{
					tx.executeSql('INSERT OR REPLACE INTO ATTACHMENT ( id , attachmentid , projectid , uid , filename , icon , fileurl , author , timeline , isimage , type , discussionid , post_num ) VALUES ( ? ,? , ? , ? , ? , ? , ? , ? , ? , ? , ? , ? , ? )' , [
					data.data[i].id
					, data.data[i].attachmentid
					, window.localStorage.getItem('projectid')
					, data.data[i].uid
					, data.data[i].filename
					, data.data[i].icon
					, data.data[i].fileurl
					, data.data[i].author
					, data.data[i].timeline
					, data.data[i].isimage 
					, data.data[i].type 
					, data.data[i].discussionid 
					, data.data[i].post_num
					] );
				}
			}, db_error , function() {
				if( refresh ) show_local_attachment();
			});
		}
		
		$("#attachment_page_refresh").html('点击刷新附件区');
		
	} , 'json' );		
}

function show_local_attachment() {
	
	var db = window.openDatabase("TeamdotaDB", "1.0", "TeamdotaDB", 1024*1024);
	db.transaction( listDB , db_error  );

	function listDB( tx ) {
		tx.executeSql('CREATE TABLE IF NOT EXISTS ATTACHMENT ( id unique , attachmentid , projectid , uid  , filename , icon , fileurl , author , timeline , isimage , type ,discussionid , post_num )' );
		tx.executeSql('SELECT * FROM ATTACHMENT WHERE projectid = ? ORDER BY id DESC LIMIT 10', [  window.localStorage.getItem('projectid') ], query_success, db_error);
	}

	function query_success(tx, results) {
	
		var len = results.rows.length;
		if( len > 0 ) {
			var rdata = Array();
			for( var i = 0 ; i < len ; i++ ) {
				rdata[i] = results.rows.item(i);
				rdata[i]['timeline_format'] = get_datetime(rdata[i]['timeline']);
			}
			window.localStorage.setItem('attachment_order_id' , rdata[len-1].id);
			
			$("ul#attachment_list_ul").empty();
			$("#attachment_list_temp").tmpl(rdata).appendTo( "ul#attachment_list_ul" );
			$('ul#attachment_list_ul').listview('refresh');
			
			$.mobile.fixedToolbars.show(true );
			
			if( $("#attachment_list").data("iscroll-plugin") )
				$("#attachment_list").data("iscroll-plugin").refresh();
		} else {
			refresh_remote_attachment(true);
		}
	}
}
function attachment_more() {
	var attachment_order_id = window.localStorage.getItem('attachment_order_id');
	
	var db = window.openDatabase("TeamdotaDB", "1.0", "TeamdotaDB", 1024*1024);
	db.transaction( listDB , db_error  );

	var wheresql = '';
	var more = 0;
	
	if( attachment_order_id && parseInt(attachment_order_id) > 0 ) {
		wheresql = ' AND id < ? ';
		more = 1;
	}
		
	$("#attachment_page_more").html('正在读取数据<img src="images/dots.gif"/>');
	
	function listDB( tx ) {
		tx.executeSql('CREATE TABLE IF NOT EXISTS ATTACHMENT ( id unique , attachmentid , projectid , uid  , filename , icon , fileurl , author , timeline , isimage , type ,discussionid , post_num ) ');
		tx.executeSql('SELECT * FROM ATTACHMENT WHERE projectid = ? ' + wheresql + ' ORDER BY id DESC LIMIT 10', [ window.localStorage.getItem('projectid') , parseInt(attachment_order_id) ], query_success, db_error);
	}

	function query_success(tx, results) {
		 
		$("#attachment_page_more").html('加载更多...');
		var len = results.rows.length;
		 
		if( len > 0 ) {
			var rdata = Array();
			for( var i = 0 ; i < len ; i++ ) {
				rdata[i] = results.rows.item(i);
				rdata[i]['timeline_format'] = get_datetime(rdata[i]['timeline']);
			}
			
			window.localStorage.setItem('attachment_order_id' , rdata[len-1].id);
			
			$("#attachment_list_temp").tmpl(rdata).appendTo( "ul#attachment_list_ul" );
			$('ul#attachment_list_ul').listview('refresh');
			$.mobile.fixedToolbars.show(true );	
		}
	}
}
function attachment_content( attachmentid , post_num ) {
	window.localStorage.setItem( 'attachmentid' , attachmentid );
	window.localStorage.setItem( 'attachment_post_num' , post_num );
	goto("attachment_content"  );

}
function show_attachment_content( attachmentid ) {
	get_data( "SELECT * FROM ATTACHMENT WHERE attachmentid = ?" , [attachmentid] , function( tx ) {
		$("div#attachment_content_text").empty();
		$("#attachment_content_text_temp").tmpl(tx).appendTo( "div#attachment_content_text" );
	});
}

function show_attachment_comment( attachmentid ) {
	$.post( openapi_server + '?m=attachment&a=attachment_comment_list' , {'token':window.localStorage.getItem('token'),'projectid':window.localStorage.getItem('projectid') , 'attachmentid':attachmentid } , function( data ) {
		if( data.code != 0 ) {
			alert("读取评论列表接口失败");
		} else {
			if( !data.data ) {
				$("ul#attachment_comment_list").empty();
				return false;
			}
			for( var i = 0 ; i < data.data.length ; i++ ) {
				data.data[i]['timeline_format'] = get_datetime(data.data[i]['timeline']);
			}
			$("ul#attachment_comment_list").empty();
			$("#attachment_comment_list_temp").tmpl(data.data).appendTo( "ul#attachment_comment_list" );
			$('ul#attachment_comment_list').listview('refresh');
		}		
	} , 'json' );
}

function send_attachment_comment() {
	var attachmentid = window.localStorage.getItem('attachmentid');
	var message = $('#comment_text').val();
	var slen = strlen(message);
	if (slen < 2) {
		alert("评论内容不能少于2个字符");
		return false;
	}
	$.post( openapi_server + '?m=attachment&a=send_comment' , {'token':window.localStorage.getItem('token'),'projectid':window.localStorage.getItem('projectid') , 'attachmentid':attachmentid , 'message':message } , function( data ) {
		if( data.code != 0 ) {
			alert(data.code);
			alert("发送评论失败");
		} else {
			$('#comment_text').val('');
			show_attachment_comment(attachmentid);
		}		
	} , 'json' );
}

function add_attachment() {
	var subject = $('#subject').val();
	var message = $('#message').val();
	var slen = strlen(subject);
	if (slen < 1 || slen > 80) {
		alert("标题长度(1~80字符)不符合要求");
		return false;
	}
	$.post( openapi_server + '?m=attachment&a=add_attachment' , {'token':window.localStorage.getItem('token'),'projectid':window.localStorage.getItem('projectid') , 'subject':subject , 'message':message } , function( data ) {
		if( data.code != 0 ) {
			alert("添加文档失败");
		} else {
			$('#subject').val('');
			$('#message').val('');
			attachment_refresh();
			goto("attachment_list" , 1);
		}		
	} , 'json' );	
}