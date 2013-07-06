var userAgent = navigator.userAgent.toLowerCase();
var is_opera = userAgent.indexOf('opera') != -1 && opera.version();
var is_moz = (navigator.product == 'Gecko') && userAgent.substr(userAgent.indexOf('firefox') + 8, 3);
var is_ie = (userAgent.indexOf('msie') != -1 && !is_opera) && userAgent.substr(userAgent.indexOf('msie') + 5, 3);
var is_safari = (userAgent.indexOf('webkit') != -1 || userAgent.indexOf('safari') != -1);
var note_oldtitle = document.title;
var loading = "<img src='image/dots-white-3483b69ff7c295c43d7d54acd612dab4.gif'>";
$(document).ready(function () {
	$('[placeholder]').focus(function () {
		var input = $(this);
		if (input.val() == input.attr('placeholder')) {
			input.val('');
			input.removeClass('placeholder');
		}
	}).blur(function () {
		var input = $(this);
		if (input.val() == '' || input.val() == input.attr('placeholder')) {
			 input.addClass('placeholder');
			 input.val(input.attr('placeholder'));
		}
	}).blur();
});
function cnCode(str) {
	return is_ie && document.charset == 'utf-8' ? encodeURIComponent(str) : str;
}

function isUndefined(variable) {
	return typeof variable == 'undefined' ? true : false;
}

function strlen(str) {
	return (is_ie && str.indexOf('\n') != -1) ? str.replace(/\r?\n/g, '_').length : str.length;
}

//Ctrl+Enter 发布
function ctrlEnter(event, btnId, onlyEnter) {
	if(isUndefined(onlyEnter)) onlyEnter = 0;
	if((event.ctrlKey || onlyEnter) && event.keyCode == 13) {
		document.getElementById(btnId).click();
		return false;
	}
	return true;
}
//缩放Textarea
function zoomTextarea(id, zoom) {
	zoomSize = zoom ? 10 : -10;
	obj = document.getElementById(id);
	if(obj.rows + zoomSize > 0 && obj.cols + zoomSize * 3 > 0) {
		obj.rows += zoomSize;
		obj.cols += zoomSize * 3;
	}
}

function checkFocus(target) {
	var obj = document.getElementById(target);
	if(!obj.hasfocus) {
		obj.focus();
	}
}

function checkImage(url) {
	var re = /^http\:\/\/.{5,200}\.(jpg|gif|png)$/i
	return url.match(re);
}

function trim(str) { 
	var re = /\s*(\S[^\0]*\S)\s*/; 
	re.exec(str); 
	return RegExp.$1; 
}

function display(id) {
	var obj = document.getElementById(id);
	obj.style.display = obj.style.display == '' ? 'none' : '';
}

function urlto(url) {
	window.location.href = url;
}

function explode(sep, string) {
	return string.split(sep);
}
function validateEmail(emailaddress){  
   var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
   if(!emailReg.test(emailaddress)) {
        return false;
	}
	return true;
}
/*
	invite by duty
*/
var default_email_number = 3;
function email_add() {
	if(default_email_number > 20){
		alert('填写的电子邮箱最多20个');
		return;
	}
	var b= [];
	b.push('<div class="person invitee field blank" id="div_email_address_'+default_email_number+'">');
	b.push('	<div class="autocomplete_people">');
	b.push('		<div class="icon"></div>');
	b.push('			<div class="input">');
	b.push('				<input data-behavior="input_change_emitter" onfocus="email_onfocus(this,'+default_email_number+');" onblur="email_onblur(this,'+default_email_number+');" data-role="human_input" spellcheck="false" type="text" name="email_address[]">');
	b.push('			</div>');
	b.push('		<div class="suggestions" data-role="suggestions_view" id="div_suggestions_'+default_email_number+'"></div>');
	b.push('	</div>');
	b.push('</div>');
	++default_email_number;
	$("[data-behavior~=invitees]").append(b.join(""));
}
function email_onfocus(object,index) {
	$("#div_email_address_"+index).addClass("focused");
}
function email_onblur(object,index) {
	$("#div_email_address_"+index).removeClass("focused");
	emailaddress = $(object).val();
	if(emailaddress != ""){
		var b = [];
		
		if(!validateEmail(emailaddress)) {
			b.push('<ol class="suggestions">\n  ');
			b.push('\n      <li class="invalid hint suggestion">\n        填写的电子邮箱格式有误\n      </li>\n    ');
			b.push("\n</ol>\n");
			$("#div_email_address_"+index).addClass("unknown");
			$("#div_suggestions_"+index).html(b.join(""));
		} else {
			$.ajax({
				type: "get",
				url: 'group.php?project_id=' + bbcx.currentProject + '&do=ajax&op=emailcheck&email=' + emailaddress,
				data: { "rand":Math.random() },
				success: function(result) {
					b.push('<ol class="suggestions">\n  ');
					if(result == '-1') {
						$("#div_email_address_"+index).addClass("unknown");
						b.push('\n      <li class="invalid hint suggestion">\n        填写的电子邮箱格式有误\n      </li>\n    ');
					} else if(result == '-2') {
						$("#div_email_address_"+index).addClass("unknown");
						b.push('\n      <li class="invalid hint suggestion">\n        填写的电子邮箱不可用\n      </li>\n    ');
					} else if(result == '-3') {
						$("#div_email_address_"+index).addClass("unknown");
						b.push('\n      <li class="invalid hint suggestion">\n        填写的电子邮箱已加入了该项目\n      </li>\n    ');
					} else if(result == '-4') {
						$("#div_email_address_"+index).addClass("unknown");
						b.push('\n      <li class="invalid hint suggestion">\n        填写的电子邮箱已发送过邀请，您可以在右边点击"再发送邀请"\n      </li>\n    ');
					}else {
						$("#div_email_address_"+index).removeClass("unknown");
					}
					b.push("\n</ol>\n");
					$("#div_suggestions_"+index).html(b.join(""));
				}
			});
		}
	}
}
var global_search_result_key = new Array();
function global_search() {
	searchkey = $("[data-behavior~=placeholder]").val();
	if(searchkey != ""){
		$("#jumpto").addClass("waiting");
		md5searchkey = $.md5(searchkey);
		var issearch = true;
		for(var i = 0; i < global_search_result_key.length; i++){
			if(global_search_result_key[i] == md5searchkey){
				$("#jumpto").removeClass("waiting");
				$("[data-query~="+global_search_result_key[i]+"]").show();
				issearch = false;
			} else {
				$("[data-query~="+global_search_result_key[i]+"]").hide();
			}
		}
		if(issearch) {
			$.ajax({
				type: "get",
				url: 'group.php?do=ajax&op=globalsearch&randkey=' + md5searchkey + '&searchkey=' + searchkey,
				data: { "rand":Math.random() },
				success: function(result) {
					if(result != '') {
						$("#jumpto").append(result);
					}
					$("#jumpto").removeClass("waiting");
					global_search_result_key.push(md5searchkey);
				}
			});
		}
	}
}
function global_invite(uid) {
	$("[data-role~=invite_display]").html('正在发送邀请...');
	$("[data-role~=invite_display]").show();
	$.ajax({
		type: "get",
		url: 'cp.php?ac=people_new&op=resend&uid='+uid,
		data: { "rand":Math.random() },
		success: function(result) {
			$("[data-role~=invite_display]").html(result);
		}
	});
}
function global_search_hide(){
	return $('#jumpto').find("dl").hide();
}
/*
	show by duty
*/
function show_add_comment(discussion_id,method,objectid) {
	display('comment_before');
	display('comment_after');
	show_webupload(discussion_id);
	ajaxget('group.php?project_id=' + bbcx.currentProject + '&do=ajax&op=email_project_member&discussion_id=' + discussion_id + '&method=' + method + '&objectid=' + objectid + '&inajax=1','subscribable_data');
}
function show_webupload(discussion_id) {
	var params = {
		fileInput: $("#fileImage").get(0),
		//dragDrop: $("#fileDragArea").get(0),
		dragDrop: document.body,
		url:  "cp.php?ac=upload&project_id=" + bbcx.currentProject + "&discussion_id=" + discussion_id,
		onDelete: function(k) {
			$("#uploadList_" + k).fadeOut();
			var fileid = $("#uploadfileid_" + k).val();
			if(fileid != null && fileid !="") {
				var fileids = $("#fileids").val();
				var arrfile = new Array();
				var curfileids = "";
				if(fileids !="") {
					arrfile = fileids.split(",");
					for (i = 0; i < arrfile.length; i++) {
						if(arrfile[i] != fileid) {
							if(curfileids == ''){
								curfileids = arrfile[i];
							} else {
								curfileids = curfileids + ',' + arrfile[i];
							}
						}
					}
				}
				$("#fileids").val(curfileids);
			}
		},
		onDragOver: function() {
			$(this).addClass("upload_drag_hover");
		},
		onDragLeave: function() {
			$(this).removeClass("upload_drag_hover");
		}
	};
	webupload = $.extend(webupload, params);
	webupload.init();
}
function select_message_subscribers() {
	$("input[type=\"checkbox\"]").each(function(){
	   $(this).attr("checked",true);
	  }); 
}
function select_none_message_subscribers() {
	$("input[type=\"checkbox\"]").each(function(){
	   $(this).attr("checked",false);
	  }); 
}
function select_change_subscribers() {
	$(".subscribable .collapsed_content").hide();
	$(".subscribable .expanded_content").show();
}
function select_plans_show(plan_id) {
	$(".expanded_content").hide();
	$(".collapsed_content").show();
	$(".expanded_content").html("");
	if(document.getElementById('expanded_content_plans_'+plan_id)) {
		ajaxget('group.php?do=ajax&op=plans&plan_id=' + plan_id + '&inajax=1','expanded_content_plans_'+plan_id);
		$("#collapsed_content_plans_"+plan_id).hide();
		$("#expanded_content_plans_"+plan_id).show();
	}
}
function select_plans_hide() {
	$(".expanded_content").hide();
	$(".collapsed_content").show();
	$(".expanded_content").html("");
}
/*
	thinkbox by duty
*/
function showdialog_at_project(object,url){
	var title = object.title || null;
	var height=700,width=720;
	if(url.indexOf('?') != -1) {
		url = url + '&keepThis=true&TB_iframe=true&height=' + height + '&width=' + width;
	} else {
		url = url + '?keepThis=true&TB_iframe=true&height=' + height + '&width=' + width;
	}
	tb_show(title,url);
	return false;
}
/*
	html5 upload by duty
*/
//附件的上传
var webuploadnumber = new Date().getTime();
var webupload = {
	fileInput: null,				//html file控件
	dragDrop: null,					//拖拽敏感区域
	uploadstatus: 0,                //目前上传状态
	url: "",						//ajax地址
	unfileFilter: [],					//文件数组
	onDelete: function() {},		//文件删除后	//文件删除后
	onDragOver: function() {},		//文件拖拽到敏感区域时
	onDragLeave: function() {},	//文件离开到敏感区域时
	onProgress: function(file, loaded, total) {
		//$("#uploadList_" + file.index).addClass("uploading");
		var eleProgress = $("#uploadProgress_" + file.index), percent = (loaded / total * 100);
		eleProgress.show().html('<div style="width:'+percent+'%;"></div>');
	},
	onSuccess: function(file, fileid) {
		$("#uploadProgress_" + file.index).hide();
		$("#uploadfileid_" + file.index).val(fileid);
		this.funUploadFile();
	},
	onFailure: function(file, responseText) {
		$("#uploadProgress_" + file.index).show().html('<div style="width:0%;"></div>');
		alert(responseText);
	},
	
	/* 开发参数和内置方法分界线 */
	
	//文件拖放
	funDragHover: function(e) {
		e.stopPropagation();
		e.preventDefault();
		this[e.type === "dragover"? "onDragOver": "onDragLeave"].call(e.target);
		return this;
	},
	//获取选择文件，file控件或拖放
	funGetFiles: function(e) {
		var self = this;
		// 取消鼠标经过样式
		e.preventDefault();
	
		// 获取文件列表对象
		var files = e.target.files || e.dataTransfer.files;
		//继续添加文件
		if (files.length === 0) {return;};
		for (var i = 0, file; file = files[i]; i++) {
			if(document.getElementById('uploadList_'+webuploadnumber)) {
				continue;
			}
			(function(file) {
				//增加唯一索引值
				file.index = webuploadnumber;
				webuploadnumber++;
				self.unfileFilter.push(file);
				var extension = (a = file.name) != null ? (b = a.match(/\.([A-Za-z0-9]+?)$/)) != null ? b[1] : void 0: void 0;
				//if(/^image\/(?!x-|vnd)/.test(file.type))
				if (file.type.indexOf("image") === -1) {//非图片
					$("#preview").append('<li class="uploading" id="uploadList_'+file.index+'">'+
											'<div class="icon">'+
												'<img src="'+FileIcons.path(extension, "big")+'" class="file_icon" height="32" width="32">'+
											'</div>'+
											'<span class="name">'+file.name+'</span>'+
											'<a class="remove" href="javascript:;" id="uploadRemove_'+file.index+'"  onclick="webupload.funDeleteFile('+file.index+');" data-index="'+file.index+'"><span>删除</span></a>'+
											'<div class="progress" id="uploadProgress_'+file.index+'" data-behavior="progress"><div></div></div>'+
										'<input id="uploadfileid_'+file.index+'" value="" type="hidden"></li>');
				} else {//图片
					if (window.URL) {
						//FF4+
						$("#preview").append('<li class="image uploading" id="uploadList_'+file.index+'">'+
											'<img class="thumbnail" src="' + window.URL.createObjectURL(file) + '">'+
											'<div class="icon">'+
												'<img src="'+FileIcons.path(extension, "big")+'" class="file_icon" height="32" width="32">'+
											'</div>'+
											'<span class="name">'+file.name+'</span>'+
											'<a class="remove" href="javascript:;" onclick="webupload.funDeleteFile('+file.index+');" data-index="'+file.index+'"><span>删除</span></a>'+
											'<div class="progress" id="uploadProgress_'+file.index+'" data-behavior="progress"><div></div></div>'+
										'<input id="uploadfileid_'+file.index+'" value="" type="hidden"></li>');
					} else if (window.webkitURL) {
						//Chrome8+
						$("#preview").append('<li class="image uploading" id="uploadList_'+file.index+'">'+
											'<img class="thumbnail" src="' + window.webkitURL.createObjectURL(file) + '">'+
											'<div class="icon">'+
												'<img src="'+FileIcons.path(extension, "big")+'" class="file_icon" height="32" width="32">'+
											'</div>'+
											'<span class="name">'+file.name+'</span>'+
											'<a class="remove" href="javascript:;" onclick="webupload.funDeleteFile('+file.index+');" data-index="'+file.index+'"><span>删除</span></a>'+
											'<div class="progress" id="uploadProgress_'+file.index+'" data-behavior="progress"><div></div></div>'+
										'<input id="uploadfileid_'+file.index+'" value="" type="hidden"></li>');
					} else if(window.FileReader) {
						//实例化file reader对象
						var reader = new FileReader();
						reader.onload = function(e) {
							$("#preview").append('<li class="image uploading" id="uploadList_'+file.index+'">'+
											'<img class="thumbnail" src="'+e.target.result+'">'+
											'<div class="icon">'+
												'<img src="'+FileIcons.path(extension, "big")+'" class="file_icon" height="32" width="32">'+
											'</div>'+
											'<span class="name">'+file.name+'</span>'+
											'<a class="remove" href="javascript:;" onclick="webupload.funDeleteFile('+file.index+');" data-index="'+file.index+'"><span>删除</span></a>'+
											'<div class="progress" id="uploadProgress_'+file.index+'" data-behavior="progress"><div></div></div>'+
										'<input id="uploadfileid_'+file.index+'" value="" type="hidden"></li>');
						}
						reader.readAsDataURL(file);
					} else {
						$("#preview").append('<li class="uploading" id="uploadList_'+file.index+'">'+
										'<div class="icon">'+
											'<img src="'+FileIcons.path(extension, "big")+'" class="file_icon" height="32" width="32">'+
										'</div>'+
										'<span class="name">'+file.name+'</span>'+
										'<a class="remove" href="javascript:;" id="uploadRemove_'+file.index+'"  onclick="webupload.funDeleteFile('+file.index+');" data-index="'+file.index+'"><span>删除</span></a>'+
										'<div class="progress" id="uploadProgress_'+file.index+'" data-behavior="progress"><div></div></div>'+
									'<input id="uploadfileid_'+file.index+'" value="" type="hidden"></li>');
					}
				}
				/*if (window.FileReader) {
					var reader = new FileReader();
					reader.onload = function(e) {
						if(/^image\/(?!x-|vnd)/.test(file.type)){
							$("#preview").append('<li class="image uploading" id="uploadList_'+file.index+'">'+
											'<img class="thumbnail" src="'+e.target.result+'">'+
											'<div class="icon">'+
												'<img src="'+FileIcons.path(extension, "big")+'" class="file_icon" height="32" width="32">'+
											'</div>'+
											'<span class="name">'+file.name+'</span>'+
											'<a class="remove" href="javascript:;" onclick="webupload.funDeleteFile('+file.index+');" data-index="'+file.index+'"><span>删除</span></a>'+
											'<div class="progress" id="uploadProgress_'+file.index+'" data-behavior="progress"><div></div></div>'+
										'<input id="uploadfileid_'+file.index+'" value="" type="hidden"></li>');
						}else{
							$("#preview").append('<li class="uploading" id="uploadList_'+file.index+'">'+
											'<div class="icon">'+
												'<img src="'+FileIcons.path(extension, "big")+'" class="file_icon" height="32" width="32">'+
											'</div>'+
											'<span class="name">'+file.name+'</span>'+
											'<a class="remove" href="javascript:;" id="uploadRemove_'+file.index+'"  onclick="webupload.funDeleteFile('+file.index+');" data-index="'+file.index+'"><span>删除</span></a>'+
											'<div class="progress" id="uploadProgress_'+file.index+'" data-behavior="progress"><div></div></div>'+
										'<input id="uploadfileid_'+file.index+'" value="" type="hidden"></li>');
						}
					}
					reader.readAsDataURL(file);
				} else {
					$("#preview").append('<li class="uploading" id="uploadList_'+file.index+'">'+
											'<div class="icon">'+
												'<img src="'+FileIcons.path(extension, "big")+'" class="file_icon" height="32" width="32">'+
											'</div>'+
											'<span class="name">'+file.name+'</span>'+
											'<a class="remove" href="javascript:;" id="uploadRemove_'+file.index+'"  onclick="webupload.funDeleteFile('+file.index+');" data-index="'+file.index+'"><span>删除</span></a>'+
											'<div class="progress" id="uploadProgress_'+file.index+'" data-behavior="progress"><div></div></div>'+
										'<input id="uploadfileid_'+file.index+'" value="" type="hidden"></li>');
				}*/
			})(file);
		}
		if(self.uploadstatus == 0) {
			self.funUploadFile();
		}
	},
	buttonUploadFile: function() {
		var self = this;
		if(self.uploadstatus == 0) {
			self.funUploadFile();
		}
	},
	funUploadFile: function() {
		var self = this;
		//继续添加文件
		if(self.unfileFilter.length > 0) {
			self.uploadstatus = 1;
			$("[data-behavior~=issuance_upload]").show();
			$("[data-behavior~=issuance_save]").hide();
			var filesend = self.unfileFilter.shift();
			var xhr = new XMLHttpRequest();
				if (xhr.upload) {
					// 上传中
					xhr.upload.addEventListener("progress", function(e) {
						self.onProgress(filesend, e.loaded, e.total);
					}, false);
					// 文件上传成功或是失败
					xhr.onreadystatechange = function(e) {
						if (xhr.readyState == 4) {
							if (xhr.status == 200) {
								var responfileid = xhr.responseText;
								if(responfileid.indexOf('||') != -1) {
									var mes = responfileid.split("||");
									self.onFailure(filesend, mes[1]);
								} else {
									var fileids = document.getElementById("fileids");
									if(fileids !=null){
										if(fileids.value == ''){
											fileids.value = responfileid;
										} else {
											fileids.value = fileids.value + ',' + responfileid;
										}
									}
									self.onSuccess(filesend,responfileid);
								}
							}else {
								self.onFailure(filesend, xhr.responseText);
							}
						}
					};
		
					// 开始上传
					xhr.open("POST", self.url+"&name="+filesend.name+"&fileext="+filesend.type, !0);
					xhr.setRequestHeader("Content-Type", "application/octet-stream"),
					xhr.send(filesend);
				}
		} else {
			self.uploadstatus = 0;
			$("[data-behavior~=issuance_save]").show();
			$("[data-behavior~=issuance_upload]").hide();
		}
	},
	//删除对应的文件
	funDeleteFile: function(k) {
		this.onDelete(k);
		this.funDeleteUnFile(k);
		this.funUploadFile();
		return this;
	},
	funDeleteUnFile: function(k) {
		var arrFile = [];
		for (var i = 0, file; file = this.unfileFilter[i]; i++) {
			if (file.index != k) {
				arrFile.push(file);
			}
		}
		this.unfileFilter = arrFile;
	},
	init: function() {
		var self = this;
		if (this.dragDrop) {
			//拖进
			this.dragDrop.addEventListener("dragenter", function(e) { e.preventDefault(); }, false);
			//拖离
			this.dragDrop.addEventListener("dragleave", function(e) { e.preventDefault(); }, false);
			//拖来拖去 , 一定要注意dragover事件一定要清除默认事件
			this.dragDrop.addEventListener("dragover", function(e) { e.preventDefault(); }, false);
			//扔
			this.dragDrop.addEventListener("drop", function(e) { self.funGetFiles(e); }, false);
		}
		//文件选择控件选择
		if (this.fileInput) {
			this.fileInput.addEventListener("change", function(e) { self.funGetFiles(e); }, false);	
		}
	}
};
/*
	ajax send by duty
*/
/*
	validate by duty
*/
function validateproject(obj) {
	var project_name = document.getElementById('project_name');
    if (project_name) {
    	var slen = strlen(project_name.value);
        if (slen < 1 || slen > 50) {
            alert("项目名称长度(1~50字符)不符合要求");
			project_name.value = "项目名称";
            project_name.focus();
            return false;
        }
    }
    obj.form.submit();
	return true;
}
function validate(formid, func) {
	var subject = document.getElementById('subject');
    if (subject) {
    	var slen = strlen(subject.value);
        if (slen < 1 || slen > 80) {
            alert("标题长度(1~80字符)不符合要求");
            subject.focus();
            return false;
        }
    }
	edit_save();
    ajaxpost(formid, func);
	return true;
}
function validatedocument(formid, func) {
	var subject = document.getElementById('subject');
    if (subject) {
    	var slen = strlen(subject.value);
        if (slen < 1 || slen > 80) {
            alert("标题长度(1~80字符)不符合要求");
            subject.focus();
            return false;
        }
    }
	edit_save();
    ajaxpost(formid, func);
	return true;
}
function validateattachment(formid, func) {
	var fileids = $("#fileids").val();
    if (fileids=='') {
        alert("请选择要上传的附件");
        return false;
    }
    ajaxpost(formid, func);
	return true;
}
function validatepost(formid, func) {
	edit_save();
	var message = document.getElementById('qinbaba-ttHtmlEditor');
    if (message) {
    	var slen = strlen(message.value);
        if (slen < 2) {
            alert("评论内容不能少于2个字符");
            message.focus();
            return false;
        }
    }
    ajaxpost(formid, func);
	reset_edit();
	$("#fileids").val('');
	$("#preview").html('');
	return true;
}
function validate_edit_post(formid, func) {
	edit_save();
	var message = document.getElementById('qinbaba-ttHtmlEditor');
    if (message) {
    	var slen = strlen(message.value);
        if (slen < 2) {
            alert("评论内容不能少于2个字符");
            message.focus();
            return false;
        }
    }
    ajaxpost(formid, func);
	return true;
}
function validateinvite(formid, func) {
	ajaxpost(formid, func);
	return true;
}
function validateresendinvite(formid, func) {
	ajaxpost(formid, func);
	return true;
}
function validatedeletemember(formid, func) {
	ajaxpost(formid, func);
	return true;
}
function validate_create_project(formid, func) {
	var project_name = $("#project_name").val();
	var slen = strlen(project_name);
	if (slen < 1) {
		alert("请您输入项目名称");
		$("#project_name").focus();
		return false;
	}
    ajaxpost(formid, func);
	return true;
}
function validate_archived_project(formid, func) {
    ajaxpost(formid, func);
	return true;
}
function validate_plans(formid, func) {
	var alipay_orderid = document.getElementById('alipay_orderid');
    if (alipay_orderid) {
    	var slen = strlen(alipay_orderid.value);
        if (slen != 16) {
            alert("请您输入正确的支付宝交易号");
            alipay_orderid.focus();
            return false;
        }
    }
    ajaxpost(formid, func);
	return true;
}
function validate_todos(formid, func) {
	var subject = document.getElementById('subject');
    if (subject) {
    	var slen = strlen(subject.value);
        if (slen < 1 || slen > 80) {
            alert("标题长度(1~80字符)不符合要求");
            subject.focus();
            return false;
        }
    }
    ajaxpost(formid, func);
	return true;
}
/*
	get by duty
*/
function get_invite_member(id, result){
	if(result) {
		window.location.href='group.php?do=people';
	}
}
function project_member_delete(id, result){
	if(result) {
		var ids = explode('_', id);
		var cid = ids[1];
		$("#div_project_member_" + cid).fadeOut();
	}
}
function message_add(id, result) {
	if(result) {
		window.location.href='group.php?do=project&project_id='+bbcx.currentProject;
	}
}
function message_edit(id, result) {
	if(result) {
		var ids = explode('_', id);
		var cid = ids[1];
		var domethod = $("#currentDo").val();
		if(domethod=="discussion"){
			urlto('group.php?project_id='+bbcx.currentProject+'&do=discussion&discussion_id='+cid);
		} else if (domethod=="document"){
			urlto('group.php?project_id='+bbcx.currentProject+'&do=document&document_id='+cid);
		}else if (domethod=="attachment"){
			urlto('group.php?project_id='+bbcx.currentProject+'&do=attachment&file_id='+cid);
		}
	}
}
function todos_edit(id, result) {
	if(result) {
		if(document.getElementById("todos_data")) {
		    ajaxget('group.php?project_id='+bbcx.currentProject+'&do=todos&inajax=1','todos_data');
		} else {
			window.location.reload();
		}
		tb_remove();
	}
}
function todoslist_edit(id, result) {
	if(result) {
		if(document.getElementById("todos_data")) {
		    ajaxget('group.php?project_id='+bbcx.currentProject+'&do=todos&inajax=1','todos_data');
		} else {
			window.location.reload();
		}
		tb_remove();
	}
}
function post_add(id, result) {
	if(result) {
		var ids = explode('_', id);
		var cid = ids[1];
		var domethod = $("#currentDo").val();
		if(domethod=="discussion"){
			ajaxget('group.php?project_id='+bbcx.currentProject+'&do=discussion&discussion_id='+cid+'&inajax=1','commentsdata_'+cid);
		} else if (domethod=="document"){
			ajaxget('group.php?project_id='+bbcx.currentProject+'&do=document&document_id='+cid+'&inajax=1','commentsdata_'+cid);
		}else if (domethod=="attachment"){
			ajaxget('group.php?project_id='+bbcx.currentProject+'&do=attachment&file_id='+cid+'&inajax=1','commentsdata_'+cid);
		}else if (domethod=="todos"){
			ajaxget('group.php?project_id='+bbcx.currentProject+'&do=todos&todos_id='+cid+'&inajax=1','commentsdata_'+cid);
		}else if (domethod=="todoslist"){
			ajaxget('group.php?project_id='+bbcx.currentProject+'&do=todoslist&todoslist_id='+cid+'&inajax=1','commentsdata_'+cid);
		}
	}
}
function post_edit(id, result) {
	if(result) {
		post_add(id, result);
		tb_remove();
	}
}
function project_edit(id, result) {
	if(result) {
		ajaxget('group.php?project_id=' + bbcx.currentProject + '&do=ajax&op=get_project&inajax=1','project_data');
	}
}
function project_create(id, result) {
	if(result) {
		urlto('group.php?do=home');
	}
}
function show_project_edit() {
	$("#project_data_view").hide();
	$("#project_data_edit").show();
}
function cancel_project_edit() {
	$("#project_data_view").show();
	$("#project_data_edit").hide();
}
function project_archived(id, result) {
	if(result) {
		window.location.reload();
	}
}
function show_project_archived() {
	$(".project_settings_links").hide();
	$("#project_settings").show();
}
function cancel_project_archived() {
	$(".project_settings_links").show();
	$("#project_settings").hide();
}
function getdetail(url,m){
	if(url !=null){
		document.getElementById('workspace_'+m).style.display = 'none';
		document.getElementById('ajax_show_'+m).style.display = 'block';
		ajaxget(url,'ajax_show_'+m);
	}
	return false;
}
function deletedetail(object,m){
	object.innerHTML = loading;
	document.getElementById('workspace_'+m).style.display = 'block';
	document.getElementById('ajax_show_'+m).style.display = 'none';
	return false;
}
function deleteproject_member(type,k){
	if(type==0) {
		document.getElementById('div_delete_text_'+k).style.display = 'none';
		document.getElementById('div_delete_operate_'+k).style.display = 'block';
	}else{
		document.getElementById('div_delete_text_'+k).style.display = 'block';
		document.getElementById('div_delete_operate_'+k).style.display = 'none';
	}
}
function permissions(type,object,uid) {
	var perurl = 'cp.php?ac=people_permissions&op=edit&inajax=1&uid='+uid;
	if(type == 0) {//管理员 
		perurl += '&edittype=0';
		if (object.checked == false) {
			perurl += '&editvalue=0';
			$("#permissions_can_create_projects_"+uid).removeAttr('disabled', 'disabled');
		} else {
			perurl += '&editvalue=1';
			$("#permissions_can_create_projects_"+uid).attr('disabled', 'disabled');
			$("#permissions_can_create_projects_"+uid).attr('checked', 'checked');
		}
	}else{//可以创建项目
		perurl += '&edittype=1';
		if (object.checked == false) {
			perurl += '&editvalue=0';
		} else {
			perurl += '&editvalue=1';
		}
	}
	$.ajax({
		type: "get",
		url: perurl,
		data: { "rand":Math.random() },
		success: function(result) {
			if(is_ie) {
				var s = result.XMLDocument.text;
			} else {
				var s = result.documentElement.firstChild.nodeValue;
			}
			if(s.indexOf('ajaxok') == -1) {
				
				alert(s);
			}
		}
	});
}
function people_setting(object) {
	var perurl = 'cp.php?ac=people_settings&op=edit&inajax=1';
	if (object.checked == false) {
		perurl += '&editvalue=1';
	} else {
		perurl += '&editvalue=0';
	}
	$.ajax({
		type: "get",
		url: perurl,
		data: { "rand":Math.random() },
		success: function(result) {
			if(is_ie) {
				var s = result.XMLDocument.text;
			} else {
				var s = result.documentElement.firstChild.nodeValue;
			}
			if(s.indexOf('ajaxok') == -1) {
				
				alert(s);
			}
		}
	});
}
function todoslist_completed(object,todos_id,todoslist_id) {
	var perurl = 'cp.php?ac=todoslist&op=completed&inajax=1&project_id=' + bbcx.currentProject + '&todos_id=' + todos_id + '&todoslist_id=' + todoslist_id;
	$.ajax({
		type: "get",
		url: perurl,
		data: { "rand":Math.random() },
		success: function(result) {
			if(result == 0) {
				alert("系统繁忙，请您稍后再试。");
			} else {
				$("#sortable_todo_"+todoslist_id).remove();
				$("#sortable_todolist_"+todos_id+" .completed").append(result);
			}
		}
	});
}
function todoslist_nocompleted(object,todos_id,todoslist_id) {
	var perurl = 'cp.php?ac=todoslist&op=nocompleted&inajax=1&project_id=' + bbcx.currentProject + '&todos_id=' + todos_id + '&todoslist_id=' + todoslist_id;
	$.ajax({
		type: "get",
		url: perurl,
		data: { "rand":Math.random() },
		success: function(result) {
			if(result == 0) {
				alert("系统繁忙，请您稍后再试。");
			} else {
				$("#sortable_todo_"+todoslist_id).remove();
				$("#sortable_todolist_"+todos_id+" .ui-sortable").append(result);
			}
		}
	});
}
function delete_people(uid) {
	if (!confirm('您确定要从您的TeamDota帐户中删除该用户吗？')) {
		return;
	}
	urlto('cp.php?ac=people_view_permissions&op=delete&uid='+uid);
}
function permissions_project(type,object,uid,project_id) {
	var perurl = 'cp.php?ac=people_view_permissions&op=edit&inajax=1&uid='+uid+'&project_id='+project_id;
	if (object.checked == false) {
		if(type == 0) {
			if (!confirm('该用户将不能够访问这个项目，您确定吗？')) {
				$("#permissions_projects_"+project_id).attr('checked', 'checked');
				return;
			}
		}
		perurl += '&editvalue=0';
	} else {
		perurl += '&editvalue=1';
	}
	$("[data-bucket-id~="+project_id+"]").attr('class', 'busy');
	$.ajax({
		type: "get",
		url: perurl,
		data: { "rand":Math.random() },
		success: function(result) {
			if(is_ie) {
				var s = result.XMLDocument.text;
			} else {
				var s = result.documentElement.firstChild.nodeValue;
			}
			if(s.indexOf('ajaxok') == -1) {
				
				alert(s);
			}
			$("[data-bucket-id~="+project_id+"]").removeAttr('class', 'busy');
		}
	});
}
function permissions_all_project(uid) {
	if (!confirm('您确定要授予该用户能访问所有项目吗？')) {
		return;
	}
	var string_projects = $("#string_projects").val();
	var ids = explode(",",string_projects);
	for (var i=0 ; i < ids.length ; ++i ) {
		if(document.getElementById("permissions_projects_"+ids[i])) {
			if(document.getElementById("permissions_projects_"+ids[i]).checked == false) {
				$("#permissions_projects_"+ids[i]).attr('checked', 'checked');
				permissions_project(1,document.getElementById("permissions_projects_"+ids[i]),uid,ids[i]);
			}
		}
		
	}
}
function permissions_notall_project(uid) {
	if (!confirm('您确定要删除该用户所有项目的访问权限吗？')) {
		return;
	}
	var string_projects = $("#string_projects").val();
	var ids = explode(",",string_projects);
	for (var i=0 ; i < ids.length ; ++i ) {
		if(document.getElementById("permissions_projects_"+ids[i])) {
			if(document.getElementById("permissions_projects_"+ids[i]).checked == true) {
				$("#permissions_projects_"+ids[i]).removeAttr('checked', 'checked');
				permissions_project(1,document.getElementById("permissions_projects_"+ids[i]),uid,ids[i]);
			}
		}
		
	}
}
function todos_add(id, result) {
	if(result) {
		if(document.getElementById("todos_data")) {
		    ajaxget('group.php?project_id='+bbcx.currentProject+'&do=todos&inajax=1','todos_data');
		} else {
			window.location.reload();
		}
		tb_remove();
	}
}
function todoslist_add(id, result) {
	if(result) {
		if(document.getElementById("todos_data")) {
		    ajaxget('group.php?project_id='+bbcx.currentProject+'&do=todos&inajax=1','todos_data');
		} else {
			window.location.reload();
		}
		tb_remove();
	}
}
/*
	delete method by duty
*/
function deletepost(object_id,post_id) {
	if (confirm('确定要删除该评论吗？')) {
		if(document.getElementById("ajax_delete_post_"+post_id)) {
			document.getElementById("ajax_delete_post_"+post_id).innerHTML = loading;
		}
		$.ajax({
			type: "get",
			url: 'cp.php?project_id='+bbcx.currentProject+'&ac=post&op=delete&post_id='+post_id+'&inajax=1',
			data: { "rand":Math.random() },
			success: function(result) {
				post_add(object_id+'_'+object_id,1);
			}
		});
	}
}
function deletetodoslist(object,todoslist_id,todos_id) {
	if (confirm('确定要删除该待办事宜吗？')) {
		var perurl = 'cp.php?ac=todoslist&op=delete&inajax=1&project_id=' + bbcx.currentProject + '&todoslist_id=' + todoslist_id;
		$(object).parent('.nubbin').addClass("busy");
		$.ajax({
			type: "get",
			url: perurl,
			data: { "rand":Math.random() },
			success: function(result) {
				if(is_ie) {
					var s = result.XMLDocument.text;
				} else {
					var s = result.documentElement.firstChild.nodeValue;
				}
				if(s.indexOf('ajaxok') == -1) {
					alert(s);
				} else {
					if(isUndefined(todos_id)) {
						$("#sortable_todo_"+todoslist_id).remove();
					} else {
						urlto("group.php?do=todos&todos_id="+todos_id+"&project_id=" + bbcx.currentProject);
					}
				}
			}
		});
	}
}
function deletetodos(object,todos_id,isturn) {
	if (confirm('确定要删除该待办事宜清单吗？')) {
		var perurl = 'cp.php?ac=todos&op=delete&inajax=1&project_id=' + bbcx.currentProject + '&todos_id=' + todos_id;
		$(object).parent('.nubbin').addClass("busy");
		$.ajax({
			type: "get",
			url: perurl,
			data: { "rand":Math.random() },
			success: function(result) {
				if(is_ie) {
					var s = result.XMLDocument.text;
				} else {
					var s = result.documentElement.firstChild.nodeValue;
				}
				if(s.indexOf('ajaxok') == -1) {
					alert(s);
				} else {
					if(isUndefined(isturn)) {
						if(document.getElementById("sortable_todolist_"+todos_id)) {
							$("#sortable_todolist_"+todos_id).hide();
						} else {
							urlto("group.php?do=project&project_id="+ bbcx.currentProject);
						}
					} else {
						urlto("group.php?do=todoslist&project_id=" + bbcx.currentProject);
					}
				}
			}
		});
	}
}
/*
	edit method by duty
*/
function edit_post(objectid,post_id){
	tb_show('编辑评论','cp.php?project_id='+bbcx.currentProject+'&ac=post&op=edit&objectid='+objectid+'&post_id='+post_id+'&keepThis=true&TB_iframe=true&height=330&width=680');
}
function edit_project(){
	tb_show('编辑项目','cp.php?project_id='+bbcx.currentProject+'&ac=project&op=edit'+'&keepThis=true&TB_iframe=true&height=330&width=680');
}
function create_project(){
	tb_show('创建项目','cp.php?ac=project&keepThis=true&TB_iframe=true&height=420&width=680');
}
function edit_project(){
	tb_show('编辑项目','cp.php?project_id='+bbcx.currentProject+'&ac=project&op=edit&keepThis=true&TB_iframe=true&height=220&width=680');
}
function edit_todos(todos_id){
	tb_show('编辑类型','cp.php?project_id='+bbcx.currentProject+'&ac=todos&op=edit&todos_id='+todos_id+'&keepThis=true&TB_iframe=true&height=220&width=680');
}
function add_todos(){
	tb_show('添加待办事宜','cp.php?project_id='+bbcx.currentProject+'&ac=todos&keepThis=true&TB_iframe=true&height=220&width=680');
}
function edit_todoslist(todoslist_id){
	tb_show('编辑待办事宜清单','cp.php?project_id='+bbcx.currentProject+'&ac=todoslist&op=edit&todoslist_id='+todoslist_id+'&keepThis=true&TB_iframe=true&height=400&width=680');
}
function add_todoslist(todos_id){
	tb_show('添加待办事宜清单','cp.php?project_id='+bbcx.currentProject+'&ac=todoslist&todos_id='+todos_id+'&keepThis=true&TB_iframe=true&height=400&width=680');
}
/*
	show method by duty
*/
function show_pic(filename,fileurl){
	tb_show(filename,fileurl);
}
/*
	ajax method by duty
*/
function ajaxget(url,showid) {
	$("#"+showid).html(loading);
	$.ajax({
		type: "get",
		url: url,
		data: { "rand":Math.random() },
		success: function(result) {
			if(document.getElementById(showid)) {
				ajaxinnerhtml(document.getElementById(showid),result);
			}
		}
	});
}
function ajaxinnerhtml(showid, s) {
	showid.innerHTML = s;
}
function showloading(formid,ispost) {
	if(ispost){
		$("#"+formid).addClass("busy");
	} else {
		$("#"+formid).removeClass("busy");
	}
}
var ajaxpostHandle = 0;
function ajaxpost(formid, func) {
	showloading(formid,1);
	if(ajaxpostHandle != 0) {
		return false;
	}
	var ajaxframeid = 'ajaxframe';
	var ajaxframe = document.getElementById(ajaxframeid);
	if(ajaxframe == null) {
		if (is_ie && !is_opera) {
			ajaxframe = document.createElement("<iframe name='" + ajaxframeid + "' id='" + ajaxframeid + "'></iframe>");
		} else {
			ajaxframe = document.createElement("iframe");
			ajaxframe.name = ajaxframeid;
			ajaxframe.id = ajaxframeid;
		}
		ajaxframe.style.display = 'none';
		document.getElementById('append_parent').appendChild(ajaxframe);
	}
	document.getElementById(formid).target = ajaxframeid;
	document.getElementById(formid).action = document.getElementById(formid).action + '&inajax=1';
	ajaxpostHandle = [formid, func];
	if(ajaxframe.attachEvent) {
		ajaxframe.detachEvent ('onload', ajaxpost_load);
		ajaxframe.attachEvent('onload', ajaxpost_load);
	} else {
		document.removeEventListener('load', ajaxpost_load, true);
		ajaxframe.addEventListener('load', ajaxpost_load, false);
	}
	document.getElementById(formid).submit();
	return false;
}

function ajaxpost_load() {
	
	var formid = ajaxpostHandle[0];
	var func = ajaxpostHandle[1];
	
	var formstatus = '__' + formid;
	
	showloading(formid);
	
	if(is_ie) {
		var s = document.getElementById('ajaxframe').contentWindow.document.XMLDocument.text;
	} else {
		var s = document.getElementById('ajaxframe').contentWindow.document.documentElement.firstChild.nodeValue;
	}
	evaled = false;
	if(s.indexOf('ajaxerror') != -1) {
		evaled = true;
	}
	if(s.indexOf('ajaxok') != -1) {
		ajaxpostresult = 1;
	} else {
		ajaxpostresult = 0;
	}
	//function
	if(func) {
		setTimeout(func + '(\'' + formid + '\',' + ajaxpostresult + ')', 10);
	}
	if(!evaled && document.getElementById(formstatus)) {
		document.getElementById(formstatus).style.display = 'block';		
		ajaxinnerhtml(document.getElementById(formstatus), s);
	}
	formid.target = 'ajaxframe';
	ajaxpostHandle = 0;
}
/*
	FileIcons by duty
*/
window.FileIcons = {
	LIB: {
		AI: "AI",
		AIFF: "AIFF",
		ASP: "WEB",
		CFM: "WEB",
		CGI: "WEB",
		CSV: "CSV",
		DMG: "DMG",
		DOC: "DOC",
		DOCX: "DOCX",
		EPS: "EPS",
		FLA: "FLA",
		GIF: "GIF",
		GZ: "TGZ",
		HTML: "HTML",
		HTM: "HTM",
		INDD: "INDD",
		JPG: "JPG",
		JPEG: "JPEG",
		JSP: "WEB",
		KEY: "KEY",
		LINK: "WEB",
		M4A: "M4A",
		M4V: "M4V",
		MOV: "MOV",
		MP3: "MP3",
		MPEG: "MPEG",
		MPG: "MPG",
		NUMBERS: "NUMBERS",
		ODP: "ODP",
		ODS: "ODS",
		ODT: "ODT",
		PAGES: "PAGES",
		PDF: "PDF",
		PHP: "WEB",
		PL: "WEB",
		PNG: "PNG",
		POT: "POT",
		PPT: "PPT",
		PPTX: "PPTX",
		PS: "EPS",
		PSD: "PSD",
		RAR: "RAR",
		RM: "RM",
		RTF: "RTF",
		SIT: "SIT",
		SWF: "SWF",
		TAR: "TAR",
		TGZ: "TGZ",
		TIF: "TIF",
		TIFF: "TIFF",
		TXT: "TXT",
		VSD: "VSD",
		WAV: "WAV",
		WEB: "WEB",
		WMA: "WMA",
		WMV: "WMV",
		XLS: "XLS",
		XLSX: "XLSX",
		XLSM: "XLS",
		ZIP: "ZIP"

	},
	SIZES: {
		small: "24x18",
		big: "32x32",
		jumbo: "86x100"

	},
	URLS: {
		"AIFF_big.png": "/image/file_icons/AIFF_big.png",
		"AIFF_jumbo.png": "/image/file_icons/AIFF_jumbo.png",
		"AIFF_small.png": "/image/file_icons/AIFF_small.png",
		"AI_big.png": "/image/file_icons/AI_big.png",
		"AI_jumbo.png": "/image/file_icons/AI_jumbo.png",
		"AI_small.png": "/image/file_icons/AI_small.png",
		"CSV_big.png": "/image/file_icons/CSV_big.png",
		"CSV_jumbo.png": "/image/file_icons/CSV_jumbo.png",
		"CSV_small.png": "/image/file_icons/CSV_small.png",
		"DMG_big.png": "/image/file_icons/DMG_big.png",
		"DMG_jumbo.png": "/image/file_icons/DMG_jumbo.png",
		"DMG_small.png": "/image/file_icons/DMG_small.png",
		"DOCX_big.png": "/image/file_icons/DOCX_big.png",
		"DOCX_jumbo.png": "/image/file_icons/DOCX_jumbo.png",
		"DOCX_small.png": "/image/file_icons/DOCX_small.png",
		"DOC_big.png": "/image/file_icons/DOC_big.png",
		"DOC_jumbo.png": "/image/file_icons/DOC_jumbo.png",
		"DOC_small.png": "/image/file_icons/DOC_small.png",
		"Deleted_big.png": "/image/file_icons/Deleted_big.png",
		"Deleted_jumbo.png": "/image/file_icons/Deleted_jumbo.png",
		"EPS_big.png": "/image/file_icons/EPS_big.png",
		"EPS_jumbo.png": "/image/file_icons/EPS_jumbo.png",
		"EPS_small.png": "/image/file_icons/EPS_small.png",
		"FLA_big.png": "/image/file_icons/FLA_big.png",
		"FLA_jumbo.png": "/image/file_icons/FLA_jumbo.png",
		"FLA_small.png": "/image/file_icons/FLA_small.png",
		"GIF_big.png": "/image/file_icons/GIF_big.png",
		"GIF_jumbo.png": "/image/file_icons/GIF_jumbo.png",
		"GIF_small.png": "/image/file_icons/GIF_small.png",
		"Generic_big.png": "/image/file_icons/Generic_big.png",
		"Generic_jumbo.png": "/image/file_icons/Generic_jumbo.png",
		"Generic_small.png": "/image/file_icons/Generic_small.png",
		"HTML_big.png": "/image/file_icons/HTML_big.png",
		"HTML_jumbo.png": "/image/file_icons/HTML_jumbo.png",
		"HTML_small.png": "/image/file_icons/HTML_small.png",
		"HTM_big.png": "/image/file_icons/HTM_big.png",
		"HTM_jumbo.png": "/image/file_icons/HTM_jumbo.png",
		"HTM_small.png": "/image/file_icons/HTM_small.png",
		"INDD_big.png": "/image/file_icons/INDD_big.png",
		"INDD_jumbo.png": "/image/file_icons/INDD_jumbo.png",
		"INDD_small.png": "/image/file_icons/INDD_small.png",
		"JPEG_big.png": "/image/file_icons/JPEG_big.png",
		"JPEG_jumbo.png": "/image/file_icons/JPEG_jumbo.png",
		"JPEG_small.png": "/image/file_icons/JPEG_small.png",
		"JPG_big.png": "/image/file_icons/JPG_big.png"
		,
		"JPG_jumbo.png": "/image/file_icons/JPG_jumbo.png",
		"JPG_small.png": "/image/file_icons/JPG_small.png",
		"KEY_big.png": "/image/file_icons/KEY_big.png",
		"KEY_jumbo.png": "/image/file_icons/KEY_jumbo.png",
		"KEY_small.png": "/image/file_icons/KEY_small.png",
		"LINK_big.png": "/image/file_icons/LINK_big.png",
		"LINK_jumbo.png": "/image/file_icons/LINK_jumbo.png",
		"LINK_small.png": "/image/file_icons/LINK_small.png",
		"M4A_big.png": "/image/file_icons/M4A_big.png",
		"M4A_jumbo.png": "/image/file_icons/M4A_jumbo.png",
		"M4A_small.png": "/image/file_icons/M4A_small.png",
		"M4V_big.png": "/image/file_icons/M4V_big.png",
		"M4V_jumbo.png": "/image/file_icons/M4V_jumbo.png",
		"M4V_small.png": "/image/file_icons/M4V_small.png",
		"MOV_big.png": "/image/file_icons/MOV_big.png",
		"MOV_jumbo.png": "/image/file_icons/MOV_jumbo.png",
		"MOV_small.png": "/image/file_icons/MOV_small.png",
		"MP3_big.png": "/image/file_icons/MP3_big.png",
		"MP3_jumbo.png": "/image/file_icons/MP3_jumbo.png",
		"MP3_small.png": "/image/file_icons/MP3_small.png",
		"MPEG_big.png": "/image/file_icons/MPEG_big.png",
		"MPEG_jumbo.png": "/image/file_icons/MPEG_jumbo.png",
		"MPEG_small.png": "/image/file_icons/MPEG_small.png",
		"MPG_big.png": "/image/file_icons/MPG_big.png",
		"MPG_jumbo.png": "/image/file_icons/MPG_jumbo.png",
		"MPG_small.png": "/image/file_icons/MPG_small.png",
		"NUMBERS_big.png": "/image/file_icons/NUMBERS_big.png",
		"NUMBERS_jumbo.png": "/image/file_icons/NUMBERS_jumbo.png",
		"NUMBERS_small.png": "/image/file_icons/NUMBERS_small.png",
		"ODP_big.png": "/image/file_icons/ODP_big.png",
		"ODP_jumbo.png": "/image/file_icons/ODP_jumbo.png",
		"ODP_small.png": "/image/file_icons/ODP_small.png",
		"ODS_big.png": "/image/file_icons/ODS_big.png",
		"ODS_jumbo.png": "/image/file_icons/ODS_jumbo.png",
		"ODS_small.png": "/image/file_icons/ODS_small.png",
		"ODT_big.png": "/image/file_icons/ODT_big.png",
		"ODT_jumbo.png": "/image/file_icons/ODT_jumbo.png",
		"ODT_small.png": "/image/file_icons/ODT_small.png",
		"PAGES_big.png": "/image/file_icons/PAGES_big.png",
		"PAGES_jumbo.png": "/image/file_icons/PAGES_jumbo.png",
		"PAGES_small.png": "/image/file_icons/PAGES_small.png",
		"PDF_big.png": "/image/file_icons/PDF_big.png",
		"PDF_jumbo.png": "/image/file_icons/PDF_jumbo.png",
		"PDF_small.png": "/image/file_icons/PDF_small.png",
		"PNG_big.png": "/image/file_icons/PNG_big.png",
		"PNG_jumbo.png": "/image/file_icons/PNG_jumbo.png",
		"PNG_small.png": "/image/file_icons/PNG_small.png",
		"POT_big.png": "/image/file_icons/POT_big.png",
		"POT_jumbo.png": "/image/file_icons/POT_jumbo.png",
		"POT_small.png": "/image/file_icons/POT_small.png",
		"PPTX_big.png": "/image/file_icons/PPTX_big.png",
		"PPTX_jumbo.png": "/image/file_icons/PPTX_jumbo.png",
		"PPTX_small.png": "/image/file_icons/PPTX_small.png",
		"PPT_big.png": "/image/file_icons/PPT_big.png",
		"PPT_jumbo.png": "/image/file_icons/PPT_jumbo.png",
		"PPT_small.png": "/image/file_icons/PPT_small.png",
		"PSD_big.png": "/image/file_icons/PSD_big.png",
		"PSD_jumbo.png": "/image/file_icons/PSD_jumbo.png",
		"PSD_small.png": "/image/file_icons/PSD_small.png",
		"RAR_big.png": "/image/file_icons/RAR_big.png",
		"RAR_jumbo.png": "/image/file_icons/RAR_jumbo.png",
		"RAR_small.png": "/image/file_icons/RAR_small.png",
		"RM_big.png": "/image/file_icons/RM_big.png",
		"RM_jumbo.png": "/image/file_icons/RM_jumbo.png",
		"RM_small.png": "/image/file_icons/RM_small.png",
		"RTF_big.png": "/image/file_icons/RTF_big.png",
		"RTF_jumbo.png": "/image/file_icons/RTF_jumbo.png",
		"RTF_small.png": "/image/file_icons/RTF_small.png",
		"SIT_big.png": "/image/file_icons/SIT_big.png",
		"SIT_jumbo.png": "/image/file_icons/SIT_jumbo.png",
		"SIT_small.png": "/image/file_icons/SIT_small.png",
		"SWF_big.png": "/image/file_icons/SWF_big.png",
		"SWF_jumbo.png": "/image/file_icons/SWF_jumbo.png",
		"SWF_small.png": "/image/file_icons/SWF_small.png",
		"TAR_big.png": "/image/file_icons/TAR_big.png",
		"TAR_jumbo.png": "/image/file_icons/TAR_jumbo.png",
		"TAR_small.png": "/image/file_icons/TAR_small.png",
		"TGZ_big.png": "/image/file_icons/TGZ_big.png",
		"TGZ_jumbo.png": "/image/file_icons/TGZ_jumbo.png",
		"TGZ_small.png": "/image/file_icons/TGZ_small.png",
		"TIFF_big.png": "/image/file_icons/TIFF_big.png",
		"TIFF_jumbo.png": "/image/file_icons/TIFF_jumbo.png",
		"TIFF_small.png": "/image/file_icons/TIFF_small.png",
		"TIF_big.png": "/image/file_icons/TIF_big.png",
		"TIF_jumbo.png": "/image/file_icons/TIF_jumbo.png",
		"TIF_small.png": "/image/file_icons/TIF_small.png",
		"TXT_big.png": "/image/file_icons/TXT_big.png",
		"TXT_jumbo.png": "/image/file_icons/TXT_jumbo.png",
		"TXT_small.png": "/image/file_icons/TXT_small.png",
		"VSD_big.png": "/image/file_icons/VSD_big.png",
		"VSD_jumbo.png": "/image/file_icons/VSD_jumbo.png",
		"VSD_small.png": "/image/file_icons/VSD_small.png",
		"WAV_big.png": "/image/file_icons/WAV_big.png",
		"WAV_jumbo.png": "/image/file_icons/WAV_jumbo.png",
		"WAV_small.png": "/image/file_icons/WAV_small.png",
		"WEB_big.png": "/image/file_icons/WEB_big.png",
		"WEB_jumbo.png": "/image/file_icons/WEB_jumbo.png",
		"WEB_small.png": "/image/file_icons/WEB_small.png",
		"WMA_big.png": "/image/file_icons/WMA_big.png",
		"WMA_jumbo.png": "/image/file_icons/WMA_jumbo.png",
		"WMA_small.png": "/image/file_icons/WMA_small.png",
		"WMV_big.png": "/image/file_icons/WMV_big.png",
		"WMV_jumbo.png": "/image/file_icons/WMV_jumbo.png",
		"WMV_small.png": "/image/file_icons/WMV_small.png",
		"XLSX_big.png": "/image/file_icons/XLSX_big.png",
		"XLSX_jumbo.png": "/image/file_icons/XLSX_jumbo.png",
		"XLSX_small.png": "/image/file_icons/XLSX_small.png",
		"XLS_big.png": "/image/file_icons/XLS_big.png",
		"XLS_jumbo.png": "/image/file_icons/XLS_jumbo.png",
		"XLS_small.png": "/image/file_icons/XLS_small.png",
		"ZIP_big.png": "/image/file_icons/ZIP_big.png",
		"ZIP_jumbo.png": "/image/file_icons/ZIP_jumbo.png",
		"ZIP_small.png": "/image/file_icons/ZIP_small.png"
	},
	path: function(a, b) {
		var c,
		d,
		e;
		return b == null && (b = "small"),
		c = "" + ((d = FileIcons.find(a)) != null ? d: "Generic") + "_" + b + ".png",
		(e = FileIcons.URLS[c]) != null ? e: "/assets/file_icons/" + c

	},
	find: function(a) {
		return FileIcons.LIB[a != null ? a.toUpperCase() : void 0]

	}
}
jQuery.extend({
    b : function(a, b) {
        return a << b | a >>> 32 - b
    },
    c : function(a, b) {
        var c,
        d,
        e,
        f,
        g;
        return e = a & 2147483648,
        f = b & 2147483648,
        c = a & 1073741824,
        d = b & 1073741824,
        g = (a & 1073741823) + (b & 1073741823),
        c & d ? g ^ 2147483648 ^ e ^ f: c | d ? g & 1073741824 ? g ^ 3221225472 ^ e ^ f: g ^ 1073741824 ^ e ^ f: g ^ e ^ f
    },
    d : function(a, b, c) {
        return a & b | ~a & c
    },
    e : function(a, b, c) {
        return a & c | b & ~c
    },
    f : function(a, b, c) {
        return a ^ b ^ c
    },
    g : function(a, b, c) {
        return b ^ (a | ~c)
    },
    h : function(a, e, f, g, h, i, j) {
        return a = $.c(a, $.c($.c($.d(e, f, g), h), j)),
        $.c($.b(a, i), e)
    },
    i : function(a, d, f, g, h, i, j) {
        return a = $.c(a, $.c($.c($.e(d, f, g), h), j)),
        $.c($.b(a, i), d)
    },
    j : function(a, d, e, g, h, i, j) {
        return a = $.c(a, $.c($.c($.f(d, e, g), h), j)),
        $.c($.b(a, i), d)
    },
    k : function(a, d, e, f, h, i, j) {
        return a = $.c(a, $.c($.c($.g(d, e, f), h), j)),
        $.c($.b(a, i), d)
    },
    l : function(a) {
        var b,
        c = a.length,
        d = c + 8,
        e = (d - d % 64) / 64,
        f = (e + 1) * 16,
        g = Array(f - 1),
        h = 0,
        i = 0;
        while (i < c) b = (i - i % 4) / 4,
        h = i % 4 * 8,
        g[b] = g[b] | a.charCodeAt(i) << h,
        i++;
        return b = (i - i % 4) / 4,
        h = i % 4 * 8,
        g[b] = g[b] | 128 << h,
        g[f - 2] = c << 3,
        g[f - 1] = c >>> 29,
        g
    },
    m : function(a) {
        var b = "",
        c = "",
        d,
        e;
        for (e = 0; e <= 3; e++) d = a >>> e * 8 & 255,
        c = "0" + d.toString(16),
        b += c.substr(c.length - 2, 2);
        return b
    },
    n : function(a) {
        a = a.replace(/\x0d\x0a/g, "\n");
        var b = "";
        for (var c = 0; c < a.length; c++) {
            var d = a.charCodeAt(c);
            d < 128 ? b += String.fromCharCode(d) : d > 127 && d < 2048 ? (b += String.fromCharCode(d >> 6 | 192), b += String.fromCharCode(d & 63 | 128)) : (b += String.fromCharCode(d >> 12 | 224), b += String.fromCharCode(d >> 6 & 63 | 128), b += String.fromCharCode(d & 63 | 128))
        }
        return b
    },
	md5: function(a) {
            var b = Array(),
            d,
            e,
            f,
            g,
            o,
            p,
            q,
            r,
            s,
            t = 7,
            u = 12,
            v = 17,
            w = 22,
            x = 5,
            y = 9,
            z = 14,
            A = 20,
            B = 4,
            C = 11,
            D = 16,
            E = 23,
            F = 6,
            G = 10,
            H = 15,
            I = 21;
            a = $.n(a),
            b = $.l(a),
            p = 1732584193,
            q = 4023233417,
            r = 2562383102,
            s = 271733878;
            for (d = 0; d < b.length; d += 16) e = p,
            f = q,
            g = r,
            o = s,
            p = $.h(p, q, r, s, b[d + 0], t, 3614090360),
            s = $.h(s, p, q, r, b[d + 1], u, 3905402710),
            r = $.h(r, s, p, q, b[d + 2], v, 606105819),
            q = $.h(q, r, s, p, b[d + 3], w, 3250441966),
            p = $.h(p, q, r, s, b[d + 4], t, 4118548399),
            s = $.h(s, p, q, r, b[d + 5], u, 1200080426),
            r = $.h(r, s, p, q, b[d + 6], v, 2821735955),
            q = $.h(q, r, s, p, b[d + 7], w, 4249261313),
            p = $.h(p, q, r, s, b[d + 8], t, 1770035416),
            s = $.h(s, p, q, r, b[d + 9], u, 2336552879),
            r = $.h(r, s, p, q, b[d + 10], v, 4294925233),
            q = $.h(q, r, s, p, b[d + 11], w, 2304563134),
            p = $.h(p, q, r, s, b[d + 12], t, 1804603682),
            s = $.h(s, p, q, r, b[d + 13], u, 4254626195),
            r = $.h(r, s, p, q, b[d + 14], v, 2792965006),
            q = $.h(q, r, s, p, b[d + 15], w, 1236535329),
            p = $.i(p, q, r, s, b[d + 1], x, 4129170786),
            s = $.i(s, p, q, r, b[d + 6], y, 3225465664),
            r = $.i(r, s, p, q, b[d + 11], z, 643717713),
            q = $.i(q, r, s, p, b[d + 0], A, 3921069994),
            p = $.i(p, q, r, s, b[d + 5], x, 3593408605),
            s = $.i(s, p, q, r, b[d + 10], y, 38016083),
            r = $.i(r, s, p, q, b[d + 15], z, 3634488961),
            q = $.i(q, r, s, p, b[d + 4], A, 3889429448),
            p = $.i(p, q, r, s, b[d + 9], x, 568446438),
            s = $.i(s, p, q, r, b[d + 14], y, 3275163606),
            r = $.i(r, s, p, q, b[d + 3], z, 4107603335),
            q = $.i(q, r, s, p, b[d + 8], A, 1163531501),
            p = $.i(p, q, r, s, b[d + 13], x, 2850285829),
            s = $.i(s, p, q, r, b[d + 2], y, 4243563512),
            r = $.i(r, s, p, q, b[d + 7], z, 1735328473),
            q = $.i(q, r, s, p, b[d + 12], A, 2368359562),
            p = $.j(p, q, r, s, b[d + 5], B, 4294588738)
            ,
            s = $.j(s, p, q, r, b[d + 8], C, 2272392833),
            r = $.j(r, s, p, q, b[d + 11], D, 1839030562),
            q = $.j(q, r, s, p, b[d + 14], E, 4259657740),
            p = $.j(p, q, r, s, b[d + 1], B, 2763975236),
            s = $.j(s, p, q, r, b[d + 4], C, 1272893353),
            r = $.j(r, s, p, q, b[d + 7], D, 4139469664),
            q = $.j(q, r, s, p, b[d + 10], E, 3200236656),
            p = $.j(p, q, r, s, b[d + 13], B, 681279174),
            s = $.j(s, p, q, r, b[d + 0], C, 3936430074),
            r = $.j(r, s, p, q, b[d + 3], D, 3572445317),
            q = $.j(q, r, s, p, b[d + 6], E, 76029189),
            p = $.j(p, q, r, s, b[d + 9], B, 3654602809),
            s = $.j(s, p, q, r, b[d + 12], C, 3873151461),
            r = $.j(r, s, p, q, b[d + 15], D, 530742520),
            q = $.j(q, r, s, p, b[d + 2], E, 3299628645),
            p = $.k(p, q, r, s, b[d + 0], F, 4096336452),
            s = $.k(s, p, q, r, b[d + 7], G, 1126891415),
            r = $.k(r, s, p, q, b[d + 14], H, 2878612391),
            q = $.k(q, r, s, p, b[d + 5], I, 4237533241),
            p = $.k(p, q, r, s, b[d + 12], F, 1700485571),
            s = $.k(s, p, q, r, b[d + 3], G, 2399980690),
            r = $.k(r, s, p, q, b[d + 10], H, 4293915773),
            q = $.k(q, r, s, p, b[d + 1], I, 2240044497),
            p = $.k(p, q, r, s, b[d + 8], F, 1873313359),
            s = $.k(s, p, q, r, b[d + 15], G, 4264355552),
            r = $.k(r, s, p, q, b[d + 6], H, 2734768916),
            q = $.k(q, r, s, p, b[d + 13], I, 1309151649),
            p = $.k(p, q, r, s, b[d + 4], F, 4149444226),
            s = $.k(s, p, q, r, b[d + 11], G, 3174756917),
            r = $.k(r, s, p, q, b[d + 2], H, 718787259),
            q = $.k(q, r, s, p, b[d + 9], I, 3951481745),
            p = $.c(p, e),
            q = $.c(q, f),
            r = $.c(r, g),
            s = $.c(s, o);
            var J = $.m(p) + $.m(q) + $.m(r) + $.m(s);
            return J.toLowerCase()
        }
});

(function() {
    function A(a, b, c) {
        if (a === b) return a !== 0 || 1 / a == 1 / b;
        if (a == null || b == null) return a === b;
        a._chain && (a = a._wrapped),
        b._chain && (b = b._wrapped);
        if (a.isEqual && w.isFunction(a.isEqual)) return a.isEqual(b);
        if (b.isEqual && w.isFunction(b.isEqual)) return b.isEqual(a);
        var d = i.call(a);
        if (d != i.call(b)) return ! 1;
        switch (d) {
        case "[object String]":
            return a == String(b);
        case "[object Number]":
            return a != +a ? b != +b: a == 0 ? 1 / a == 1 / b: a == +b;
        case "[object Date]":
        case "[object Boolean]":
            return + a == +b;
        case "[object RegExp]":
            return a.source == b.source && a.global == b.global && a.multiline == b.multiline && a.ignoreCase == b.ignoreCase
        }
        if (typeof a != "object" || typeof b != "object") return ! 1;
        var e = c.length;
        while (e--) if (c[e] == a) return ! 0;
        c.push(a);
        var f = 0,
        g = !0;
        if (d == "[object Array]") {
            f = a.length,
            g = f == b.length;
            if (g) while (f--) if (! (g = f in a == f in b && A(a[f], b[f], c))) break
        } else {
            if ("constructor" in a != "constructor" in b || a.constructor != b.constructor) return ! 1;
            for (var h in a) if (j.call(a, h)) {
                f++;
                if (! (g = j.call(b, h) && A(a[h], b[h], c))) break
            }
            if (g) {
                for (h in b) if (j.call(b, h) && !(f--)) break;
                g = !f
            }
        }
        return c.pop(),
        g
    }
    var a = this,
    b = a._,
    c = {},
    d = Array.prototype,
    e = Object.prototype,
    f = Function.prototype,
    g = d.slice,
    h = d.unshift,
    i = e.toString,
    j = e.hasOwnProperty,
    k = d.forEach,
    l = d.map,
    m = d.reduce,
    n = d.reduceRight,
    o = d.filter,
    p = d.every,
    q = d.some,
    r = d.indexOf,
    s = d.lastIndexOf,
    t = Array.isArray,
    u = Object.keys,
    v = f.bind,
    w = function(a) {
        return new D(a)
    };
    typeof exports != "undefined" ? (typeof module != "undefined" && module.exports && (exports = module.exports = w), exports._ = w) : typeof define == "function" && define.amd ? define("underscore", 
    function() {
        return w
    }) : a._ = w,
    w.VERSION = "1.2.4";
    var x = w.each = w.forEach = function(a, b, d) {
        if (a == null) return;
        if (k && a.forEach === k) a.forEach(b, d);
        else if (a.length === +a.length) {
            for (var e = 0, f = a.length; e < f; e++) if (e in a && b.call(d, a[e], e, a) === c) return
        } else for (var g in a) if (j.call(a, g) && b.call(d, a[g], g, a) === c) return
    };
    w.map = function(a, b, c) {
        var d = [];
        return a == null ? d: l && a.map === l ? a.map(b, c) : (x(a, 
        function(a, e, f) {
            d[d.length] = b.call(c, a, e, f)
        }), a.length === +a.length && (d.length = a.length), d)
    },
    w.reduce = w.foldl = w.inject = function(a, b, c, d) {
        var e = arguments.length > 2;
        a == null && (a = []);
        if (m && a.reduce === m) return d && (b = w.bind(b, d)),
        e ? a.reduce(b, c) : a.reduce(b);
        x(a, 
        function(a, f, g) {
            e ? c = b.call(d, c, a, f, g) : (c = a, e = !0)
        });
        if (!e) throw new TypeError("初始化失败");
        return c
    },
    w.reduceRight = w.foldr = function(a, b, c, d) {
        var e = arguments.length > 2;
        a == null && (a = []);
        if (n && a.reduceRight === n) return d && (b = w.bind(b, d)),
        e ? a.reduceRight(b, c) : a.reduceRight(b);
        var f = w.toArray(a).reverse();
        return d && !e && (b = w.bind(b, d)),
        e ? w.reduce(f, b, c, d) : w.reduce(f, b)
    },
    w.find = w.detect = function(a, b, c) {
        var d;
        return y(a, 
        function(a, e, f) {
            if (b.call(c, a, e, f)) return d = a,
            !0
        }),
        d
    },
    w.filter = w.select = function(a, b, c) {
        var d = [];
        return a == null ? d: o && a.filter === o ? a.filter(b, c) : (x(a, 
        function(a, e, f) {
            b.call(c, a, e, f) && (d[d.length] = a)
        }), d)
    },
    w.reject = function(a, b, c) {
        var d = [];
        return a == null ? d: (x(a, 
        function(a, e, f) {
            b.call(c, a, e, f) || (d[d.length] = a)
        }), d)
    },
    w.every = w.all = function(a, b, d) {
        var e = !0;
        return a == null ? e: p && a.every === p ? a.every(b, d) : (x(a, 
        function(a, f, g) {
            if (! (e = e && b.call(d, a, f, g))) return c
        }), e)
    };
    var y = w.some = w.any = function(a, b, d) {
        b || (b = w.identity);
        var e = !1;
        return a == null ? e: q && a.some === q ? a.some(b, d) : (x(a, 
        function(a, f, g) {
            if (e || (e = b.call(d, a, f, g))) return c
        }), !!e)
    };
    w.include = w.contains = function(a, b) {
        var c = !1;
        return a == null ? c: r && a.indexOf === r ? a.indexOf(b) != -1: (c = y(a, 
        function(a) {
            return a === b
        }), c)
    },
    w.invoke = function(a, b) {
        var c = g.call(arguments, 2);
        return w.map(a, 
        function(a) {
            return (w.isFunction(b) ? b || a: a[b]).apply(a, c)
        })
    },
    w.pluck = function(a, b) {
        return w.map(a, 
        function(a) {
            return a[b]
        })
    },
    w.max = function(a, b, c) {
        if (!b && w.isArray(a)) return Math.max.apply(Math, a);
        if (!b && w.isEmpty(a)) return - Infinity;
        var d = {
            computed: -Infinity
        };
        return x(a, 
        function(a, e, f) {
            var g = b ? b.call(c, a, e, f) : a;
            g >= d.computed && (d = {
                value: a,
                computed: g
            })
        }),
        d.value
    },
    w.min = function(a, b, c) {
        if (!b && w.isArray(a)) return Math.min.apply(Math, a);
        if (!b && w.isEmpty(a)) return Infinity;
        var d = {
            computed: Infinity
        };
        return x(a, 
        function(a, e, f) {
            var g = b ? b.call(c, a, e, f) : a;
            g < d.computed && (d = {
                value: a,
                computed: g
            })
        }),
        d.value
    },
    w.shuffle = function(a) {
        var b = [],
        c;
        return x(a, 
        function(a, d, e) {
            d == 0 ? b[0] = a: (c = Math.floor(Math.random() * (d + 1)), b[d] = b[c], b[c] = a)
        }),
        b
    },
    w.sortBy = function(a, b, c) {
        return w.pluck(w.map(a, 
        function(a, d, e) {
            return {
                value: a,
                criteria: b.call(c, a, d, e)
            }
        }).sort(function(a, b) {
            var c = a.criteria,
            d = b.criteria;
            return c < d ? -1: c > d ? 1: 0
        }), "value")
    },
    w.groupBy = function(a, b) {
        var c = {},
        d = w.isFunction(b) ? b: function(a) {
            return a[b]
        };
        return x(a, 
        function(a, b) {
            var e = d(a, b); (c[e] || (c[e] = [])).push(a)
        }),
        c
    },
    w.sortedIndex = function(a, b, c) {
        c || (c = w.identity);
        var d = 0,
        e = a.length;
        while (d < e) {
            var f = d + e >> 1;
            c(a[f]) < c(b) ? d = f + 1: e = f
        }
        return d
    },
    w.toArray = function(a) {
        return a ? a.toArray ? a.toArray() : w.isArray(a) ? g.call(a) : w.isArguments(a) ? g.call(a) : w.values(a) : []
    },
    w.size = function(a) {
        return w.toArray(a).length
    },
    w.first = w.head = function(a, b, c) {
        return b != null && !c ? g.call(a, 0, b) : a[0]
    },
    w.initial = function(a, b, c) {
        return g.call(a, 0, a.length - (b == null || c ? 1: b))
    },
    w.last = function(a, b, c) {
        return b != null && !c ? g.call(a, Math.max(a.length - b, 0)) : a[a.length - 1]
    },
    w.rest = w.tail = function(a, b, c) {
        return g.call(a, b == null || c ? 1: b)
    },
    w.compact = function(a) {
        return w.filter(a, 
        function(a) {
            return !! a
        })
    },
    w.flatten = function(a, b) {
        return w.reduce(a, 
        function(a, c) {
            return w.isArray(c) ? a.concat(b ? c: w.flatten(c)) : (a[a.length] = c, a)
        },
        [])
    },
    w.without = function(a) {
        return w.difference(a, g.call(arguments, 1))
    },
    w.uniq = w.unique = function(a, b, c) {
        var d = c ? w.map(a, c) : a,
        e = [];
        return w.reduce(d, 
        function(c, d, f) {
            if (0 == f || (b === !0 ? w.last(c) != d: !w.include(c, d))) c[c.length] = d,
            e[e.length] = a[f];
            return c
        },
        []),
        e
    },
    w.union = function() {
        return w.uniq(w.flatten(arguments, !0))
    },
    w.intersection = w.intersect = function(a) {
        var b = g.call(arguments, 1);
        return w.filter(w.uniq(a), 
        function(a) {
            return w.every(b, 
            function(b) {
                return w.indexOf(b, a) >= 0
            })
        })
    },
    w.difference = function(a) {
        var b = w.flatten(g.call(arguments, 1));
        return w.filter(a, 
        function(a) {
            return ! w.include(b, a)
        })
    },
    w.zip = function() {
        var a = g.call(arguments),
        b = w.max(w.pluck(a, "length")),
        c = new Array(b);
        for (var d = 0; d < b; d++) c[d] = w.pluck(a, "" + d);
        return c
    },
    w.indexOf = function(a, b, c) {
        if (a == null) return - 1;
        var d,
        e;
        if (c) return d = w.sortedIndex(a, b),
        a[d] === b ? d: -1;
        if (r && a.indexOf === r) return a.indexOf(b);
        for (d = 0, e = a.length; d < e; d++) if (d in a && a[d] === b) return d;
        return - 1
    },
    w.lastIndexOf = function(a, b) {
        if (a == null) return - 1;
        if (s && a.lastIndexOf === s) return a.lastIndexOf(b);
        var c = a.length;
        while (c--) if (c in a && a[c] === b) return c;
        return - 1
    },
    w.range = function(a, b, c) {
        arguments.length <= 1 && (b = a || 0, a = 0),
        c = arguments[2] || 1;
        var d = Math.max(Math.ceil((b - a) / c), 0),
        e = 0,
        f = new Array(d);
        while (e < d) f[e++] = a,
        a += c;
        return f
    };
    var z = function() {};
    w.bind = function(b, c) {
        var d,
        e;
        if (b.bind === v && v) return v.apply(b, g.call(arguments, 1));
        if (!w.isFunction(b)) throw new TypeError;
        return e = g.call(arguments, 2),
        d = function() {
            if (this instanceof d) {
                z.prototype = b.prototype;
                var a = new z,
                f = b.apply(a, e.concat(g.call(arguments)));
                return Object(f) === f ? f: a
            }
            return b.apply(c, e.concat(g.call(arguments)))
        }
    },
    w.bindAll = function(a) {
        var b = g.call(arguments, 1);
        return b.length == 0 && (b = w.functions(a)),
        x(b, 
        function(b) {
            a[b] = w.bind(a[b], a)
        }),
        a
    },
    w.memoize = function(a, b) {
        var c = {};
        return b || (b = w.identity),
        function() {
            var d = b.apply(this, arguments);
            return j.call(c, d) ? c[d] : c[d] = a.apply(this, arguments)
        }
    },
    w.delay = function(a, b) {
        var c = g.call(arguments, 2);
        return setTimeout(function() {
            return a.apply(a, c)
        },
        b)
    },
    w.defer = function(a) {
        return w.delay.apply(w, [a, 1].concat(g.call(arguments, 1)))
    },
    w.throttle = function(a, b) {
        var c,
        d,
        e,
        f,
        g,
        h = w.debounce(function() {
            g = f = !1
        },
        b);
        return function() {
            c = this,
            d = arguments;
            var i = function() {
                e = null,
                g && a.apply(c, d),
                h()
            };
            e || (e = setTimeout(i, b)),
            f ? g = !0: a.apply(c, d),
            h(),
            f = !0
        }
    },
    w.debounce = function(a, b) {
        var c;
        return function() {
            var d = this,
            e = arguments,
            f = function() {
                c = null,
                a.apply(d, e)
            };
            clearTimeout(c),
            c = setTimeout(f, b)
        }
    },
    w.once = function(a) {
        var b = !1,
        c;
        return function() {
            return b ? c: (b = !0, c = a.apply(this, arguments))
        }
    },
    w.wrap = function(a, b) {
        return function() {
            var c = [a].concat(g.call(arguments, 0));
            return b.apply(this, c)
        }
    },
    w.compose = function() {
        var a = arguments;
        return function() {
            var b = arguments;
            for (var c = a.length - 1; c >= 0; c--) b = [a[c].apply(this, b)];
            return b[0]
        }
    },
    w.after = function(a, b) {
        return a <= 0 ? b() : function() {
            if (--a < 1) return b.apply(this, arguments)
        }
    },
    w.keys = u || 
    function(a) {
        if (a !== Object(a)) throw new TypeError("Invalid object");
        var b = [];
        for (var c in a) j.call(a, c) && (b[b.length] = c);
        return b
    },
    w.values = function(a) {
        return w.map(a, w.identity)
    },
    w.functions = w.methods = function(a) {
        var b = [];
        for (var c in a) w.isFunction(a[c]) && b.push(c);
        return b.sort()
    },
    w.extend = function(a) {
        return x(g.call(arguments, 1), 
        function(b) {
            for (var c in b) b[c] !== void 0 && (a[c] = b[c])
        }),
        a
    },
    w.defaults = function(a) {
        return x(g.call(arguments, 1), 
        function(b) {
            for (var c in b) a[c] == null && (a[c] = b[c])
        }),
        a
    },
    w.clone = function(a) {
        return w.isObject(a) ? w.isArray(a) ? a.slice() : w.extend({},
        a) : a
    },
    w.tap = function(a, b) {
        return b(a),
        a
    },
    w.isEqual = function(a, b) {
        return A(a, b, [])
    },
    w.isEmpty = function(a) {
        if (w.isArray(a) || w.isString(a)) return a.length === 0;
        for (var b in a) if (j.call(a, b)) return ! 1;
        return ! 0
    },
    w.isElement = function(a) {
        return !! a && a.nodeType == 1
    },
    w.isArray = t || 
    function(a) {
        return i.call(a) == "[object Array]"
    },
    w.isObject = function(a) {
        return a === Object(a)
    },
    w.isArguments = function(a) {
        return i.call(a) == "[object Arguments]"
    },
    w.isArguments(arguments) || (w.isArguments = function(a) {
        return !! a && !!j.call(a, "callee")
    }),
    w.isFunction = function(a) {
        return i.call(a) == "[object Function]"
    },
    w.isString = function(a) {
        return i.call(a) == "[object String]"
    },
    w.isNumber = function(a) {
        return i.call(a) == "[object Number]"
    },
    w.isNaN = function(a) {
        return a !== a
    },
    w.isBoolean = function(a) {
        return a === !0 || a === !1 || i.call(a) == "[object Boolean]"
    },
    w.isDate = function(a) {
        return i.call(a) == "[object Date]"
    },
    w.isRegExp = function(a) {
        return i.call(a) == "[object RegExp]"
    },
    w.isNull = function(a) {
        return a === null
    },
    w.isUndefined = function(a) {
        return a === void 0
    },
    w.noConflict = function() {
        return a._ = b,
        this
    },
    w.identity = function(a) {
        return a
    },
    w.times = function(a, b, c) {
        for (var d = 0; d < a; d++) b.call(c, d)
    },
    w.escape = function(a) {
        return ("" + a).replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#x27;").replace(/\//g, "&#x2F;")
    },
    w.mixin = function(a) {
        x(w.functions(a), 
        function(b) {
            F(b, w[b] = a[b])
        })
    };
    var B = 0;
    w.uniqueId = function(a) {
        var b = B++;
        return a ? a + b: b
    },
    w.templateSettings = {
        evaluate: /<%([\s\S]+?)%>/g,
        interpolate: /<%=([\s\S]+?)%>/g,
        escape: /<%-([\s\S]+?)%>/g
    };
    var C = /.^/;
    w.template = function(a, b) {
        var c = w.templateSettings,
        d = "var __p=[],print=function(){__p.push.apply(__p,arguments);};with(obj||{}){__p.push('" + a.replace(/\\/g, "\\\\").replace(/'/g, "\\'").replace(c.escape || C, 
        function(a, b) {
            return "',_.escape(" + b.replace(/\\'/g, "'") + "),'"
        }).replace(c.interpolate || C, 
        function(a, b) {
            return "'," + b.replace(/\\'/g, "'") + ",'"
        }).replace(c.evaluate || C, 
        function(a, b) {
            return "');" + b.replace(/\\'/g, "'").replace(/[\r\n\t]/g, " ").replace(/\\\\/g, "\\") + ";__p.push('"
        }).replace(/\r/g, "\\r").replace(/\n/g, "\\n").replace(/\t/g, "\\t") + "');}return __p.join('');",
        e = new Function("obj", "_", d);
        return b ? e(b, w) : function(a) {
            return e.call(this, a, w)
        }
    },
    w.chain = function(a) {
        return w(a).chain()
    };
    var D = function(a) {
        this._wrapped = a
    };
    w.prototype = D.prototype;
    var E = function(a, b) {
        return b ? w(a).chain() : a
    },
    F = function(a, b) {
        D.prototype[a] = function() {
            var a = g.call(arguments);
            return h.call(a, this._wrapped),
            E(b.apply(w, a), this._chain)
        }
    };
    w.mixin(w),
    x(["pop", "push", "reverse", "shift", "sort", "splice", "unshift"], 
    function(a) {
        var b = d[a];
        D.prototype[a] = function() {
            var c = this._wrapped;
            b.apply(c, arguments);
            var d = c.length;
            return (a == "shift" || a == "splice") && d === 0 && delete c[0],
            E(c, this._chain)
        }
    }),
    x(["concat", "join", "slice"], 
    function(a) {
        var b = d[a];
        D.prototype[a] = function() {
            return E(b.apply(this._wrapped, arguments), this._chain)
        }
    }),
    D.prototype.chain = function() {
        return this._chain = !0,
        this
    },
    D.prototype.value = function() {
        return this._wrapped
    }
})(jQuery);
(function() {
    this.JST || (this.JST = {}),
    this.JST["templates/image_enlarger"] = function(a) {
        a || (a = {});
        var b = [],
        c = function(a) {
            var c = b,
            d;
            return b = [],
            a.call(this),
            d = b.join(""),
            b = c,
            e(d)
        },
        d = function(a) {
            return a && a.ecoSafe ? a: typeof a != "undefined" && a != null ? g(a) : ""
        },
        e,
        f = a.safe,
        g = a.escape;
        return e = a.safe = function(a) {
            if (a && a.ecoSafe) return a;
            if (typeof a == "undefined" || a == null) a = "";
            var b = new String(a);
            return b.ecoSafe = !0,
            b
        },
        g || (g = a.escape = function(a) {
            return ("" + a).replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;")
        }),
        function() { (function() {
                var a,
                c,
                e,
                f,
                g,
                h,
                i;
                b.push('<div id="image_enlarger" class="'),
                this.images.length > 1 && b.push(d("with_nav")),
                b.push('">\n  <button class="close"></button>\n\n  '),
                h = this.images;
                for (c = 0, f = h.length; c < f; c++) a = h[c],
                b.push('\n    <figure id="enlarged_image_'),
                b.push(d(a.id)),
                b.push('" style="'),
                a !== this.active && b.push(d("display:none;")),
                b.push('">\n      <div class="table_wrapper"><div class="cell_wrapper">\n        <img class="enlarged" data-behavior="enlarged_image" src="'),
                b.push(d(a.thumbnail)),
                b.push('" data-original-src="'),
                b.push(d(a.original)),
                b.push('" width="'),
                b.push(d(a.width)),
                b.push('" height="'),
                b.push(d(a.height)),
                b.push('">\n        <figcaption>\n          '),
                b.push(d(a.filename)),
                b.push('\n          <a href="'),
                b.push(d(a.original)),
                b.push('" class="download simple_outline" data-stacker="false" target="_blank">查看原图</a>\n        </figcaption>\n      </div></div>\n    </figure>\n  ');
                b.push("\n\n  ");
                if (this.images.length > 1) {
                    b.push('\n    <nav>\n      <div class="images" data-behavior="scroll_view">\n        <table data-behavior="scroll_content">\n          <tr>\n            '),
                    i = this.images;
                    for (e = 0, g = i.length; e < g; e++) a = i[e],
                    b.push('\n              <td>\n                <img\n                  class="'),
                    a === this.active && b.push(d("activated")),
                    b.push('"\n                  src="'),
                    b.push(d(a.thumbnail)),
                    b.push('"\n                  title="'),
                    b.push(d(a.filename)),
                    b.push('"\n                  width="'),
                    b.push(d(a.width)),
                    b.push('"\n                  height="'),
                    b.push(d(a.height)),
                    b.push('"\n                  data-behavior="activate"\n                  data-image-id="'),
                    b.push(d(a.id)),
                    b.push('"\n                >\n              </td>\n            ');
                    b.push('\n          </tr>\n        </table>\n      </div>\n\n      <button class="left arrow" data-behavior="scroll_reverse"></button>\n      <button class="right arrow" data-behavior="scroll_forward"></button>\n    </nav>\n  ')
                }
                b.push("\n</div>\n")
            }).call(this)
        }.call(a),
        a.safe = f,
        a.escape = g,
        b.join("")
    }
})(jQuery);
(function($){
    var a,b,c,d,e,f,g,h,i,j;
    $(document).ready(function() {
        var a;
        if (a = window.location.hash.match(/\#_enlarge_(\d+)/)) return $("[data-behavior~=enlargeable] [data-image-id~=" + a[1] + "]").click()
    }),
    $("[data-behavior~=enlargeable]").live("click", 
    function(b) {
        var c,
        d;
        if (b.metaKey) return;
        return b.preventDefault(),
        b.stopPropagation(),
        c = $(this).is("img") ? $(this) : $(this).find("img"),
        d = c.closest("section, html"),
        new a({
            activeImageId: c.data("image-id"),
            $thumbnails: d.find("img[data-container-id=" + c.data("container-id") + "]")
        })
    }),
    a = function() {
        function a(a) {
            var b,
            c,
            d;
            a == null && (a = {}),
            b = a.$thumbnails,
            c = a.activeImageId,
            this.images = function() {
                var a,
                c,
                e;
                e = [];
                for (a = 0, c = b.length; a < c; a++) d = b[a],
                e.push(f($(d)));
                return e
            } (),
            this.activate(c),
            this.install()
        }
        return a.prototype.bind = function() {
            var a,
            b = this;
            return a = $(this.el),
            a.delegate("[data-behavior~=activate]", "click", 
            function(a) {
                return b.activate($(a.target).data("image-id"))
            }),
            a.delegate("[data-behavior~=scroll_forward]", "click", 
            function(a) {
                if (!$(a.target).is("[disabled]")) return b.nav.scrollForward()
            }),
            a.delegate("[data-behavior~=scroll_reverse]", "click", 
            function(a) {
                if (!$(a.target).is("[disabled]")) return b.nav.scrollReverse()
            }),
            a.bind("click", 
            function(a) {
                if (!$(a.target).is("a, [data-behavior]")) return b.uninstall()
            }),
            $(document).bind("keydown.image_enlarger", 
            function(a) {
                switch (a.which) {
                case $.ui.keyCode.ESCAPE:
                    return b.uninstall(),
                    a.preventDefault();
                case $.ui.keyCode.RIGHT:
                    return b.next(),
                    a.preventDefault();
                case $.ui.keyCode.LEFT:
                    return b.previous(),
                    a.preventDefault();
                case $.ui.keyCode.UP:
                    return a.preventDefault();
                case $.ui.keyCode.DOWN:
                    return a.preventDefault()
                }
            }),
            $(document).bind("workspace:beforeload.image_enlarger", 
            function() {
                return b.uninstall({
                    withAnimation: !1
                })
            }),
            $(window).bind("resize.image_enlarger", 
            function(a) {
                return b.resize()
            })
        },
        a.prototype.unbind = function() {
            return $(document).unbind(".image_enlarger"),
            $(window).unbind(".image_enlarger")
        },
        a.prototype.install = function() {
            var a,
            b = this;
            return this.insert(),
            i(this.el),
            this.resize(),
            this.bind(),
            this.activateShade(),
            $(this.el).css("visibility", "hidden"),
            a = d(this.el),
            j($(this.active.imgInPage), {
                $to: a,
                easing: "ease-out"
            },
            function(a) {
                return a.css("z-index", -1),
                a.remove(),
                $(b.el).css("visibility", "visible")
            })
        },
        a.prototype.uninstall = function(a) {
            var b,
            c,
            e = this;
            return a == null && (a = {}),
            this.unbind(),
            b = d(this.el),
            c = $.extend({},
            {
                $from: b,
                $to: $(this.active.imgInPage)
            },
            a),
            j($(this.active.imgInPage), c, 
            function(a) {
                return a.css("z-index", -1),
                a.remove()
            }),
            this.remove(),
            this.deactivateShade()
        },
        a.prototype.activate = function(a) {
            return this.active = _(this.images).detect(function(b) {
                return b.id === a
            }),
            this.redraw()
        },
        a.prototype.next = function() {
            if (this.images.length > 1) return this.activate((this.images[this.images.indexOf(this.active) + 1] || this.images[0]).id),
            this.nav.scrollActivatedThumbnailIntoView()
        },
        a.prototype.previous = function() {
            if (this.images.length > 1) return this.activate((this.images[this.images.indexOf(this.active) - 1] || this.images[this.images.length - 1]).id),
            this.nav.scrollActivatedThumbnailIntoView()
        },
        a.prototype.render = function() {
            return JST["templates/image_enlarger"]({
                images: this.images,
                active: this.active
            })
        },
        a.prototype.insert = function() {
            return $("html").append(this.render()),
            this.el = $("#image_enlarger")[0],
            this.nav = new b($(this.el).find("nav:first")[0]),
            this.nav.center($(this.el).find("nav img.activated")),
            this.loadActiveImage()
        },
        a.prototype.resize = function() {
            return h(this.el),
            this.nav.resize()
        },
        a.prototype.redraw = function() {
            return $(this.el).find("figure:visible").hide(),
            $(this.el).find("#enlarged_image_" + this.active.id).show(),
            $(this.el).find("nav img.activated").removeClass("activated"),
            $(this.el).find("nav img[data-image-id=" + this.active.id + "]").addClass("activated"),
            this.loadActiveImage()
        },
        a.prototype.remove = function() {
            return $(this.el).remove()
        },
        a.prototype.activateShade = function() {
            return $("html").addClass("shaded")
        },
        a.prototype.deactivateShade = function() {
            return $("html").removeClass("shaded")
        },
        a.prototype.loadActiveImage = function() {
            var a,
            b,
            c;
            return a = d(this.el),
            c = a.data("original-src"),
            b = new Image,
            b.onload = function() {
                return a.attr("src", c)
            }
        },
        a
    } (),
    b = function() {
        function a(a) {
            this.el = $(a),
            this.updateScrollButtons(),
            this.resize()
        }
        return a.prototype.getView = function() {
            return $(this.el).find("[data-behavior~=scroll_view]:first")
        },
        a.prototype.getContent = function() {
            return $(this.el).find("[data-behavior~=scroll_content]:first")
        },
        a.prototype.activateScrollButton = function(a) {
            return $(this.el).find("[data-behavior~=scroll_" + a + "]").addClass("active")
        },
        a.prototype.deactivateScrollButton = function(a) {
            return $(this.el).find("[data-behavior~=scroll_" + a + "]").removeClass("active")
        },
        a.prototype.enableScrollButton = function(a) {
            return $(this.el).find("[data-behavior~=scroll_" + a + "]").attr("disabled", !1)
        },
        a.prototype.disableScrollButton = function(a) {
            return $(this.el).find("[data-behavior~=scroll_" + a + "]").attr("disabled", !0)
        },
        a.prototype.scrollForward = function() {
            return this.activateScrollButton("forward"),
            this.scroll(this.distanceToEnd())
        },
        a.prototype.scrollReverse = function() {
            return this.activateScrollButton("reverse"),
            this.scroll(this.distanceToStart())
        },
        a.prototype.scrollActivatedThumbnailIntoView = function() {
            var a,
            b,
            c,
            d,
            f,
            g,
            h,
            i;
            b = this.getView(),
            a = $(this.el).find("img.activated"),
            i = e(b),
            f = e(a),
            g = i.left,
            h = i.left + i.width,
            c = f.left - f.width,
            d = f.left + f.width + f.width,
            c < g && this.scroll(c - g);
            if (d > h) return this.scroll(d - h)
        },
        a.prototype.center = function(a) {
            var b,
            c;
            if (this.scrolling()) return c = this.centerPositionFor(a),
            c = Math.max(this.maxScrollPosition(), c),
            c = Math.min(this.minScrollPosition(), c),
            b = this.getContent(),
            b.css({
                left: c
            }),
            this.updateScrollButtons()
        },
        a.prototype.centerPositionFor = function(a) {
            var b,
            c;
            return b = this.getView(),
            c = a.position().left + a.width() / 2,
            -c + b.width() / 2
        },
        a.prototype.maxScrollPosition = function() {
            var a,
            b;
            return b = this.getView(),
            a = this.getContent(),
            b.width() - a.width()
        },
        a.prototype.minScrollPosition = function() {
            return 0
        },
        a.prototype.scroll = function(a) {
            var b,
            c,
            d,
            e,
            f,
            g = this;
            return b = this.getContent(),
            d = parseInt(b.css("left")),
            c = this.adjustedDistance(a),
            e = a > 0 ? -1: 1,
            f = d + c * e,
            f = Math.max(this.maxScrollPosition(), f),
            f = Math.min(this.minScrollPosition(), f),
            b.animate({
                left: f
            },
            function() {
                return g.updateScrollButtons()
            })
        },
        a.prototype.updateScrollButtons = function() {
            return this.deactivateScrollButton("reverse"),
            this.deactivateScrollButton("forward"),
            this.distanceToEnd() === 0 ? this.disableScrollButton("forward") : this.enableScrollButton("forward"),
            this.distanceToStart() === 0 ? this.disableScrollButton("reverse") : this.enableScrollButton("reverse")
        },
        a.prototype.resize = function() {
            var a;
            return a = this.getView(),
            this.scrolling() ? a.addClass("scrolling") : a.removeClass("scrolling")
        },
        a.prototype.scrolling = function() {
            var a,
            b;
            return b = this.getView(),
            a = this.getContent(),
            b.width() < a.width()
        },
        a.prototype.distanceToEnd = function() {
            var a,
            b,
            c,
            d,
            e,
            f;
            return b = this.getView(),
            a = this.getContent(),
            f = b.width(),
            d = a.width(),
            c = parseInt(a.css("left")),
            e = d + c - f,
            Math.max(e, 0)
        },
        a.prototype.distanceToStart = function() {
            var a;
            return a = this.getContent(),
            parseInt(a.css("left"))
        },
        a.prototype.adjustedDistance = function(a) {
            return Math.min(this.maxScrollDistance(), Math.abs(a))
        },
        a.prototype.maxScrollDistance = function() {
            var a;
            return a = this.getView(),
            Math.floor(a.width() * .5)
        },
        a
    } (),
    d = function(a) {
        return $(a).find("figure:visible img.enlarged")
    },
    h = function(a) {
        var b,
        c;
        return b = $(a).find("figure:visible"),
        c = {
            height: b.height(),
            width: b.width()
        },
        $(a).find("figure img.enlarged").each(function() {
            var a;
            return a = $(this),
            a.css(g(a, c))
        })
    },
    i = function(a) {
        var b,
        c;
        return b = $(a).find("nav img"),
        c = {
            height: b.css("max-height"),
            width: b.css("max-width")
        },
        b.each(function() {
            var a;
            return a = $(this),
            a.css(g(a, c))
        })
    },
    f = function(a) {
        return {
            imgInPage: a.get(0),
            thumbnail: a.attr("src"),
            original: a.data("original-src"),
            filename: a.data("filename"),
            id: a.data("image-id"),
            width: a.attr("width") || a.data("width"),
            height: a.attr("height") || a.data("height")
        }
    },
    g = function(a, b) {
        var c,
        d,
        e,
        f,
        g,
        h,
        i,
        j;
        f = parseInt(a.attr("width") || a.data("width")),
        d = parseInt(a.attr("height") || a.data("height")),
        i = parseInt(b.width),
        g = parseInt(b.height),
        e = f / d,
        h = i / g,
        j = f,
        c = d;
        if (j > i || c > g) e > h ? (j = i, c = parseInt(j / e)) : (c = g, j = parseInt(c * e));
        return {
            width: j,
            height: c
        }
    },
    j = function(a, b, d) {
        var f,
        g,
        h;
        b == null && (b = {}),
        h = e(b.$from || a),
        g = e(b.$to || window),
        f = c(a),
        f.css(h);
        if (b.withAnimation === !1) {
            typeof d == "function" && d(f);
            return
        }
        return $("html").append(f),
        "onwebkittransitionend" in window ? (setTimeout(function() {
            return f.css({
                "-webkit-transition-property": "left right width height",
                "-webkit-transition-animation": "ease-out",
                "-webkit-transition-duration": "200ms"
            }),
            f.css(g)
        }), f.bind("webkitTransitionEnd", 
        function(a) {
            return f.unbind("webkitTransitionEnd"),
            typeof d == "function" ? d(f) : void 0
        })) : f.animate(g, 200, "easeOutQuad", 
        function() {
            return typeof d == "function" ? d(f) : void 0
        })
    },
    c = function(a) {
        return a.clone().attr("data-behavior", "").css({
            "-webkit-transform": "translate3D(0,0,0)",
            position: "fixed",
            zIndex: 2e3
        })
    },
    e = function(a) {
        var b,
        c,
        d,
        e,
        f,
        g,
        h,
        i,
        j,
        k,
        l;
        return e = a.offset(),
        b = parseInt((i = a.css("border-left-width")) != null ? i: 0),
        f = parseInt((j = a.css("border-top-width")) != null ? j: 0),
        c = parseInt((k = a.css("padding-left")) != null ? k: 0),
        g = parseInt((l = a.css("padding-top")) != null ? l: 0),
        d = $(window).scrollLeft(),
        h = $(window).scrollTop(),
        {
            left: e.left + b + c - d,
            top: e.top + f + g - h,
            width: a.width(),
            height: a.height()
        }
    }
})(jQuery),
function(a, b) {
    function c(b, c) {
        var e = b.nodeName.toLowerCase();
        return "area" === e ? (c = b.parentNode, e = c.name, !b.href || !e || c.nodeName.toLowerCase() !== "map" ? !1: (b = a("img[usemap=#" + e + "]")[0], !!b && d(b))) : (/input|select|textarea|button|object/.test(e) ? !b.disabled: "a" == e ? b.href || c: c) && d(b)
    }
    function d(b) {
        return ! a(b).parents().andSelf().filter(function() {
            return a.curCSS(this, "visibility") === "hidden" || a.expr.filters.hidden(this)
        }).length
    }
    a.ui = a.ui || {},
    a.ui.version || (a.extend(a.ui, {
        version: "1.8.16",
        keyCode: {
            ALT: 18,
            BACKSPACE: 8,
            CAPS_LOCK: 20,
            COMMA: 188,
            COMMAND: 91,
            COMMAND_LEFT: 91,
            COMMAND_RIGHT: 93,
            CONTROL: 17,
            DELETE: 46,
            DOWN: 40,
            END: 35,
            ENTER: 13,
            ESCAPE: 27,
            HOME: 36,
            INSERT: 45,
            LEFT: 37,
            MENU: 93,
            NUMPAD_ADD: 107,
            NUMPAD_DECIMAL: 110,
            NUMPAD_DIVIDE: 111,
            NUMPAD_ENTER: 108,
            NUMPAD_MULTIPLY: 106,
            NUMPAD_SUBTRACT: 109,
            PAGE_DOWN: 34,
            PAGE_UP: 33,
            PERIOD: 190,
            RIGHT: 39,
            SHIFT: 16,
            SPACE: 32,
            TAB: 9,
            UP: 38,
            WINDOWS: 91
        }
    }), a.fn.extend({
        propAttr: a.fn.prop || a.fn.attr,
        _focus: a.fn.focus,
        focus: function(b, c) {
            return typeof b == "number" ? this.each(function() {
                var d = this;
                setTimeout(function() {
                    a(d).focus(),
                    c && c.call(d)
                },
                b)
            }) : this._focus.apply(this, arguments)
        },
        scrollParent: function() {
            var b;
            return b = a.browser.msie && /(static|relative)/.test(this.css("position")) || /absolute/.test(this.css("position")) ? this.parents().filter(function() {
                return /(relative|absolute|fixed)/.test(a.curCSS(this, "position", 1)) && /(auto|scroll)/.test(a.curCSS(this, "overflow", 1) + a.curCSS(this, "overflow-y", 1) + a.curCSS(this, "overflow-x", 1))
            }).eq(0) : this.parents().filter(function() {
                return /(auto|scroll)/.test(a.curCSS(this, "overflow", 1) + a.curCSS(this, "overflow-y", 1) + a.curCSS(this, "overflow-x", 1))
            }).eq(0),
            /fixed/.test(this.css("position")) || !b.length ? a(document) : b
        },
        zIndex: function(c) {
            if (c !== b) return this.css("zIndex", c);
            if (this.length) {
                c = a(this[0]);
                for (var d; c.length && c[0] !== document;) {
                    d = c.css("position");
                    if (d === "absolute" || d === "relative" || d === "fixed") {
                        d = parseInt(c.css("zIndex"), 10);
                        if (!isNaN(d) && d !== 0) return d
                    }
                    c = c.parent()
                }
            }
            return 0
        },
        disableSelection: function() {
			return this.bind((a.support.selectstart ? "selectstart": "mousedown") + ".ui-disableSelection", 
            function(a) {
                a.preventDefault()
            })
        },
        enableSelection: function() {
            return this.unbind(".ui-disableSelection")
        }
    }), a.each(["Width", "Height"], 
    function(c, d) {
        function e(b, c, d, e) {
            return a.each(f, 
            function() {
                c -= parseFloat(a.curCSS(b, "padding" + this, !0)) || 0,
                d && (c -= parseFloat(a.curCSS(b, "border" + this + "Width", !0)) || 0),
                e && (c -= parseFloat(a.curCSS(b, "margin" + this, !0)) || 0)
            }),
            c
        }
        var f = d === "Width" ? ["Left", "Right"] : ["Top", "Bottom"],
        g = d.toLowerCase(),
        h = {
            innerWidth: a.fn.innerWidth,
            innerHeight: a.fn.innerHeight,
            outerWidth: a.fn.outerWidth,
            outerHeight: a.fn.outerHeight
        };
        a.fn["inner" + d] = function(c) {
            return c === b ? h["inner" + d].call(this) : this.each(function() {
                a(this).css(g, e(this, c) + "px")
            })
        },
        a.fn["outer" + d] = function(b, c) {
            return typeof b != "number" ? h["outer" + d].call(this, b) : this.each(function() {
                a(this).css(g, e(this, b, !0, c) + "px")
            })
        }
    }), a.extend(a.expr[":"], {
        data: function(b, c, d) {
            return !! a.data(b, d[3])
        },
        focusable: function(b) {
            return c(b, !isNaN(a.attr(b, "tabindex")))
        },
        tabbable: function(b) {
            var d = a.attr(b, "tabindex"),
            e = isNaN(d);
            return (e || d >= 0) && c(b, !e)
        }
    }), a(function() {
        var b = document.body,
        c = b.appendChild(c = document.createElement("div"));
        a.extend(c.style, {
            minHeight: "100px",
            height: "auto",
            padding: 0,
            borderWidth: 0
        }),
        a.support.minHeight = c.offsetHeight === 100,
        a.support.selectstart = "onselectstart" in c,
        b.removeChild(c).style.display = "none"
    }), a.extend(a.ui, {
        plugin: {
            add: function(b, c, d) {
                b = a.ui[b].prototype;
                for (var e in d) b.plugins[e] = b.plugins[e] || [],
                b.plugins[e].push([c, d[e]])
            },
            call: function(a, b, c) {
                if ((b = a.plugins[b]) && a.element[0].parentNode) for (var d = 0; d < b.length; d++) a.options[b[d][0]] && b[d][1].apply(a.element, c)
            }
        },
        contains: function(a, b) {
            return document.compareDocumentPosition ? a.compareDocumentPosition(b) & 16: a !== b && a.contains(b)
        },
        hasScroll: function(b, c) {
            if (a(b).css("overflow") === "hidden") return ! 1;
            c = c && c === "left" ? "scrollLeft": "scrollTop";
            var d = !1;
            return b[c] > 0 ? !0: (b[c] = 1, d = b[c] > 0, b[c] = 0, d)
        },
        isOverAxis: function(a, b, c) {
            return a > b && a < b + c
        },
        isOver: function(b, c, d, e, f, g) {
            return a.ui.isOverAxis(b, d, f) && a.ui.isOverAxis(c, e, g)
        }
    }))
} (jQuery),
function(a, b) {
    if (a.cleanData) {
        var c = a.cleanData;
        a.cleanData = function(b) {
            for (var d = 0, e; (e = b[d]) != null; d++) try {
                a(e).triggerHandler("remove")
            } catch(f) {}
            c(b)
        }
    } else {
        var d = a.fn.remove;
        a.fn.remove = function(b, c) {
            return this.each(function() {
                return c || (!b || a.filter(b, [this]).length) && a("*", this).add([this]).each(function() {
                    try {
                        a(this).triggerHandler("remove")
                    } catch(b) {}
                }),
                d.call(a(this), b, c)
            })
        }
    }
    a.widget = function(b, c, d) {
        var e = b.split(".")[0],
        f;
        b = b.split(".")[1],
        f = e + "-" + b,
        d || (d = c, c = a.Widget),
        a.expr[":"][f] = function(c) {
            return !! a.data(c, b)
        },
        a[e] = a[e] || {},
        a[e][b] = function(a, b) {
            arguments.length && this._createWidget(a, b)
        },
        c = new c,
        c.options = a.extend(!0, {},
        c.options),
        a[e][b].prototype = a.extend(!0, c, {
            namespace: e,
            widgetName: b,
            widgetEventPrefix: a[e][b].prototype.widgetEventPrefix || b,
            widgetBaseClass: f
        },
        d),
        a.widget.bridge(b, a[e][b])
    },
    a.widget.bridge = function(c, d) {
        a.fn[c] = function(e) {
            var f = typeof e == "string",
            g = Array.prototype.slice.call(arguments, 1),
            h = this;
            return e = !f && g.length ? a.extend.apply(null, [!0, e].concat(g)) : e,
            f && e.charAt(0) === "_" ? h: (f ? this.each(function() {
                var d = a.data(this, c),
                f = d && a.isFunction(d[e]) ? d[e].apply(d, g) : d;
                if (f !== d && f !== b) return h = f,
                !1
            }) : this.each(function() {
                var b = a.data(this, c);
                b ? b.option(e || {})._init() : a.data(this, c, new d(e, this))
            }), h)
        }
    },
    a.Widget = function(a, b) {
        arguments.length && this._createWidget(a, b)
    },
    a.Widget.prototype = {
        widgetName: "widget",
        widgetEventPrefix: "",
        options: {
            disabled: !1
        },
        _createWidget: function(b, c) {
            a.data(c, this.widgetName, this),
            this.element = a(c),
            this.options = a.extend(!0, {},
            this.options, this._getCreateOptions(), b);
            var d = this;
            this.element.bind("remove." + this.widgetName, 
            function() {
                d.destroy()
            }),
            this._create(),
            this._trigger("create"),
            this._init()
        },
        _getCreateOptions: function() {
            return a.metadata && a.metadata.get(this.element[0])[this.widgetName]
        },
        _create: function() {},
        _init: function() {},
        destroy: function() {
            this.element.unbind("." + this.widgetName).removeData(this.widgetName),
            this.widget().unbind("." + this.widgetName).removeAttr("aria-disabled").removeClass(this.widgetBaseClass + "-disabled ui-state-disabled")
        },
        widget: function() {
            return this.element
        },
        option: function(c, d) {
            var e = c;
            if (arguments.length === 0) return a.extend({},
            this.options);
            if (typeof c == "string") {
                if (d === b) return this.options[c];
                e = {},
                e[c] = d
            }
            return this._setOptions(e),
            this
        },
        _setOptions: function(b) {
            var c = this;
            return a.each(b, 
            function(a, b) {
                c._setOption(a, b)
            }),
            this
        },
        _setOption: function(a, b) {
            return this.options[a] = b,
            a === "disabled" && this.widget()[b ? "addClass": "removeClass"](this.widgetBaseClass + "-disabled ui-state-disabled").attr("aria-disabled", b),
            this
        },
        enable: function() {
            return this._setOption("disabled", !1)
        },
        disable: function() {
            return this._setOption("disabled", !0)
        },
        _trigger: function(b, c, d) {
            var e = this.options[b];
            c = a.Event(c),
            c.type = (b === this.widgetEventPrefix ? b: this.widgetEventPrefix + b).toLowerCase(),
            d = d || {};
            if (c.originalEvent) {
                b = a.event.props.length;
                for (var f; b;) f = a.event.props[--b],
                c[f] = c.originalEvent[f]
            }
            return this.element.trigger(c, d),
            !(a.isFunction(e) && e.call(this.element[0], c, d) === !1 || c.isDefaultPrevented())
        }
    }
} (jQuery),
function(a) {
    var b = !1;
    a(document).mouseup(function() {
        b = !1
    }),
    a.widget("ui.mouse", {
        options: {
            cancel: ":input,option",
            distance: 1,
            delay: 0
        },
        _mouseInit: function() {
            var b = this;
            this.element.bind("mousedown." + this.widgetName, 
            function(a) {
                return b._mouseDown(a)
            }).bind("click." + this.widgetName, 
            function(c) {
                if (!0 === a.data(c.target, b.widgetName + ".preventClickEvent")) return a.removeData(c.target, b.widgetName + ".preventClickEvent"),
                c.stopImmediatePropagation(),
                !1
            }),
            this.started = !1
        },
        _mouseDestroy: function() {
            this.element.unbind("." + this.widgetName)
        },
        _mouseDown: function(c) {
            if (!b) {
                this._mouseStarted && this._mouseUp(c),
                this._mouseDownEvent = c;
                var e = this,
                f = c.which == 1,
                g = typeof this.options.cancel == "string" && c.target.nodeName ? a(c.target).closest(this.options.cancel).length: !1;
                if (!f || g || !this._mouseCapture(c)) return ! 0;
                this.mouseDelayMet = !this.options.delay,
                this.mouseDelayMet || (this._mouseDelayTimer = setTimeout(function() {
                    e.mouseDelayMet = !0
                },
                this.options.delay));
                if (this._mouseDistanceMet(c) && this._mouseDelayMet(c)) {
                    this._mouseStarted = this._mouseStart(c) !== !1;
                    if (!this._mouseStarted) return c.preventDefault(),
                    !0
                }
                return ! 0 === a.data(c.target, this.widgetName + ".preventClickEvent") && a.removeData(c.target, this.widgetName + ".preventClickEvent"),
                this._mouseMoveDelegate = function(a) {
                    return e._mouseMove(a)
                },
                this._mouseUpDelegate = function(a) {
                    return e._mouseUp(a)
                },
                a(document).bind("mousemove." + this.widgetName, this._mouseMoveDelegate).bind("mouseup." + this.widgetName, this._mouseUpDelegate),
                c.preventDefault(),
                b = !0
            }
        },
        _mouseMove: function(b) {
            return ! a.browser.msie || document.documentMode >= 9 || !!b.button ? this._mouseStarted ? (this._mouseDrag(b), b.preventDefault()) : (this._mouseDistanceMet(b) && this._mouseDelayMet(b) && ((this._mouseStarted = this._mouseStart(this._mouseDownEvent, b) !== !1) ? this._mouseDrag(b) : this._mouseUp(b)), !this._mouseStarted) : this._mouseUp(b)
        },
        _mouseUp: function(b) {
            return a(document).unbind("mousemove." + this.widgetName, this._mouseMoveDelegate).unbind("mouseup." + this.widgetName, this._mouseUpDelegate),
            this._mouseStarted && (this._mouseStarted = !1, b.target == this._mouseDownEvent.target && a.data(b.target, this.widgetName + ".preventClickEvent", !0), this._mouseStop(b)),
            !1
        },
        _mouseDistanceMet: function(a) {
            return Math.max(Math.abs(this._mouseDownEvent.pageX - a.pageX), Math.abs(this._mouseDownEvent.pageY - a.pageY)) >= this.options.distance
        },
        _mouseDelayMet: function() {
            return this.mouseDelayMet
        },
        _mouseStart: function() {},
        _mouseDrag: function() {},
        _mouseStop: function() {},
        _mouseCapture: function() {
            return ! 0
        }
    })
} (jQuery),
function(a) {
    a.widget("ui.sortable", a.ui.mouse, {
        widgetEventPrefix: "sort",
        options: {
            appendTo: "parent",
            axis: !1,
            connectWith: !1,
            containment: !1,
            cursor: "auto",
            cursorAt: !1,
            dropOnEmpty: !0,
            forcePlaceholderSize: !1,
            forceHelperSize: !1,
            grid: !1,
            handle: !1,
            helper: "original",
            items: "> *",
            opacity: !1,
            placeholder: !1,
            revert: !1,
            scroll: !0,
            scrollSensitivity: 20,
            scrollSpeed: 20,
            scope: "default",
            tolerance: "intersect",
            zIndex: 1e3
        },
        _create: function() {
            var a = this.options;
            this.containerCache = {},
            this.element.addClass("ui-sortable"),
            this.refresh(),
            this.floating = this.items.length ? a.axis === "x" || /left|right/.test(this.items[0].item.css("float")) || /inline|table-cell/.test(this.items[0].item.css("display")) : !1,
            this.offset = this.element.offset(),
            this._mouseInit()
        },
        destroy: function() {
            this.element.removeClass("ui-sortable ui-sortable-disabled").removeData("sortable").unbind(".sortable"),
            this._mouseDestroy();
            for (var a = this.items.length - 1; a >= 0; a--) this.items[a].item.removeData("sortable-item");
            return this
        },
        _setOption: function(b, c) {
            b === "disabled" ? (this.options[b] = c, this.widget()[c ? "addClass": "removeClass"]("ui-sortable-disabled")) : a.Widget.prototype._setOption.apply(this, arguments)
        },
        _mouseCapture: function(b, c) {
            if (this.reverting) return ! 1;
            if (this.options.disabled || this.options.type == "static") return ! 1;
            this._refreshItems(b);
            var e = null,
            f = this;
            a(b.target).parents().each(function() {
                if (a.data(this, "sortable-item") == f) return e = a(this),
                !1
            }),
            a.data(b.target, "sortable-item") == f && (e = a(b.target));
            if (!e) return ! 1;
            if (this.options.handle && !c) {
                var g = !1;
                a(this.options.handle, e).find("*").andSelf().each(function() {
                    this == b.target && (g = !0)
                });
                if (!g) return ! 1
            }
            return this.currentItem = e,
            this._removeCurrentsFromItems(),
            !0
        },
        _mouseStart: function(b, c, e) {
            c = this.options;
            var f = this;
            this.currentContainer = this,
            this.refreshPositions(),
            this.helper = this._createHelper(b),
            this._cacheHelperProportions(),
            this._cacheMargins(),
            this.scrollParent = this.helper.scrollParent(),
            this.offset = this.currentItem.offset(),
            this.offset = {
                top: this.offset.top - this.margins.top,
                left: this.offset.left - this.margins.left
            },
            this.helper.css("position", "absolute"),
            this.cssPosition = this.helper.css("position"),
            a.extend(this.offset, {
                click: {
                    left: b.pageX - this.offset.left,
                    top: b.pageY - this.offset.top
                },
                parent: this._getParentOffset(),
                relative: this._getRelativeOffset()
            }),
            this.originalPosition = this._generatePosition(b),
            this.originalPageX = b.pageX,
            this.originalPageY = b.pageY,
            c.cursorAt && this._adjustOffsetFromHelper(c.cursorAt),
            this.domPosition = {
                prev: this.currentItem.prev()[0],
                parent: this.currentItem.parent()[0]
            },
            this.helper[0] != this.currentItem[0] && this.currentItem.hide(),
            this._createPlaceholder(),
            c.containment && this._setContainment(),
            c.cursor && (a("body").css("cursor") && (this._storedCursor = a("body").css("cursor")), a("body").css("cursor", c.cursor)),
            c.opacity && (this.helper.css("opacity") && (this._storedOpacity = this.helper.css("opacity")), this.helper.css("opacity", c.opacity)),
            c.zIndex && (this.helper.css("zIndex") && (this._storedZIndex = this.helper.css("zIndex")), this.helper.css("zIndex", c.zIndex)),
            this.scrollParent[0] != document && this.scrollParent[0].tagName != "HTML" && (this.overflowOffset = this.scrollParent.offset()),
            this._trigger("start", b, this._uiHash()),
            this._preserveHelperProportions || this._cacheHelperProportions();
            if (!e) for (e = this.containers.length - 1; e >= 0; e--) this.containers[e]._trigger("activate", b, f._uiHash(this));
            return a.ui.ddmanager && (a.ui.ddmanager.current = this),
            a.ui.ddmanager && !c.dropBehaviour && a.ui.ddmanager.prepareOffsets(this, b),
            this.dragging = !0,
            this.helper.addClass("ui-sortable-helper"),
            this._mouseDrag(b),
            !0
        },
        _mouseDrag: function(b) {
            this.position = this._generatePosition(b),
            this.positionAbs = this._convertPositionTo("absolute"),
            this.lastPositionAbs || (this.lastPositionAbs = this.positionAbs);
            if (this.options.scroll) {
                var c = this.options,
                e = !1;
                this.scrollParent[0] != document && this.scrollParent[0].tagName != "HTML" ? (this.overflowOffset.top + this.scrollParent[0].offsetHeight - b.pageY < c.scrollSensitivity ? this.scrollParent[0].scrollTop = e = this.scrollParent[0].scrollTop + c.scrollSpeed: b.pageY - this.overflowOffset.top < c.scrollSensitivity && (this.scrollParent[0].scrollTop = e = this.scrollParent[0].scrollTop - c.scrollSpeed), this.overflowOffset.left + this.scrollParent[0].offsetWidth - b.pageX < c.scrollSensitivity ? this.scrollParent[0].scrollLeft = e = this.scrollParent[0].scrollLeft + c.scrollSpeed: b.pageX - this.overflowOffset.left < c.scrollSensitivity && (this.scrollParent[0].scrollLeft = e = this.scrollParent[0].scrollLeft - c.scrollSpeed)) : (b.pageY - a(document).scrollTop() < c.scrollSensitivity ? e = a(document).scrollTop(a(document).scrollTop() - c.scrollSpeed) : a(window).height() - (b.pageY - a(document).scrollTop()) < c.scrollSensitivity && (e = a(document).scrollTop(a(document).scrollTop() + c.scrollSpeed)), b.pageX - a(document).scrollLeft() < c.scrollSensitivity ? e = a(document).scrollLeft(a(document).scrollLeft() - c.scrollSpeed) : a(window).width() - (b.pageX - a(document).scrollLeft()) < c.scrollSensitivity && (e = a(document).scrollLeft(a(document).scrollLeft() + c.scrollSpeed))),
                e !== !1 && a.ui.ddmanager && !c.dropBehaviour && a.ui.ddmanager.prepareOffsets(this, b)
            }
            this.positionAbs = this._convertPositionTo("absolute");
            if (!this.options.axis || this.options.axis != "y") this.helper[0].style.left = this.position.left + "px";
            if (!this.options.axis || this.options.axis != "x") this.helper[0].style.top = this.position.top + "px";
            for (c = this.items.length - 1; c >= 0; c--) {
                e = this.items[c];
                var f = e.item[0],
                g = this._intersectsWithPointer(e);
                if (g && f != this.currentItem[0] && this.placeholder[g == 1 ? "next": "prev"]()[0] != f && !a.ui.contains(this.placeholder[0], f) && (this.options.type == "semi-dynamic" ? !a.ui.contains(this.element[0], f) : !0)) {
                    this.direction = g == 1 ? "down": "up";
                    if (this.options.tolerance != "pointer" && !this._intersectsWithSides(e)) break;
                    this._rearrange(b, e),
                    this._trigger("change", b, this._uiHash());
                    break
                }
            }
            return this._contactContainers(b),
            a.ui.ddmanager && a.ui.ddmanager.drag(this, b),
            this._trigger("sort", b, this._uiHash()),
            this.lastPositionAbs = this.positionAbs,
            !1
        },
        _mouseStop: function(b, c) {
            if (b) {
                a.ui.ddmanager && !this.options.dropBehaviour && a.ui.ddmanager.drop(this, b);
				if (this.options.revert) {
                    var e = this;
                    c = e.placeholder.offset(),
                    e.reverting = !0,
                    a(this.helper).animate({
                        left: c.left - this.offset.parent.left - e.margins.left + (this.offsetParent[0] == document.body ? 0: this.offsetParent[0].scrollLeft),
                        top: c.top - this.offset.parent.top - e.margins.top + (this.offsetParent[0] == document.body ? 0: this.offsetParent[0].scrollTop)
                    },
                    parseInt(this.options.revert, 10) || 500, 
                    function() {
                        e._clear(b)
                    })
                } else this._clear(b, c);
                return ! 1
            }
        },
        cancel: function() {
            var b = this;
            if (this.dragging) {
                this._mouseUp({
                    target: null
                }),
                this.options.helper == "original" ? this.currentItem.css(this._storedCSS).removeClass("ui-sortable-helper") : this.currentItem.show();
                for (var c = this.containers.length - 1; c >= 0; c--) this.containers[c]._trigger("deactivate", null, b._uiHash(this)),
                this.containers[c].containerCache.over && (this.containers[c]._trigger("out", null, b._uiHash(this)), this.containers[c].containerCache.over = 0)
            }
            return this.placeholder && (this.placeholder[0].parentNode && this.placeholder[0].parentNode.removeChild(this.placeholder[0]), this.options.helper != "original" && this.helper && this.helper[0].parentNode && this.helper.remove(), a.extend(this, {
                helper: null,
                dragging: !1,
                reverting: !1,
                _noFinalSort: null
            }), this.domPosition.prev ? a(this.domPosition.prev).after(this.currentItem) : a(this.domPosition.parent).prepend(this.currentItem)),
            this
        },
        serialize: function(b) {
            var c = this._getItemsAsjQuery(b && b.connected),
            e = [];
            return b = b || {},
            a(c).each(function() {
                var c = (a(b.item || this).attr(b.attribute || "id") || "").match(b.expression || /(.+)[-=_](.+)/);
                c && e.push((b.key || c[1] + "[]") + "=" + (b.key && b.expression ? c[1] : c[2]))
            }),
            !e.length && b.key && e.push(b.key + "="),
            e.join("&")
        },
        toArray: function(b) {
            var c = this._getItemsAsjQuery(b && b.connected),
            e = [];
            return b = b || {},
            c.each(function() {
                e.push(a(b.item || this).attr(b.attribute || "id") || "")
            }),
            e
        },
        _intersectsWith: function(a) {
            var b = this.positionAbs.left,
            c = b + this.helperProportions.width,
            d = this.positionAbs.top,
            e = d + this.helperProportions.height,
            f = a.left,
            g = f + a.width,
            h = a.top,
            i = h + a.height,
            j = this.offset.click.top,
            k = this.offset.click.left;
            return j = d + j > h && d + j < i && b + k > f && b + k < g,
            this.options.tolerance == "pointer" || this.options.forcePointerForContainers || this.options.tolerance != "pointer" && this.helperProportions[this.floating ? "width": "height"] > a[this.floating ? "width": "height"] ? j: f < b + this.helperProportions.width / 2 && c - this.helperProportions.width / 2 < g && h < d + this.helperProportions.height / 2 && e - this.helperProportions.height / 2 < i
        },
        _intersectsWithPointer: function(b) {
            var c = a.ui.isOverAxis(this.positionAbs.top + this.offset.click.top, b.top, b.height);
            b = a.ui.isOverAxis(this.positionAbs.left + this.offset.click.left, b.left, b.width),
            c = c && b,
            b = this._getDragVerticalDirection();
            var e = this._getDragHorizontalDirection();
            return c ? this.floating ? e && e == "right" || b == "down" ? 2: 1: b && (b == "down" ? 2: 1) : !1
        },
        _intersectsWithSides: function(b) {
            var c = a.ui.isOverAxis(this.positionAbs.top + this.offset.click.top, b.top + b.height / 2, b.height);
            b = a.ui.isOverAxis(this.positionAbs.left + this.offset.click.left, b.left + b.width / 2, b.width);
            var e = this._getDragVerticalDirection(),
            f = this._getDragHorizontalDirection();
            return this.floating && f ? f == "right" && b || f == "left" && !b: e && (e == "down" && c || e == "up" && !c)
        },
        _getDragVerticalDirection: function() {
            var a = this.positionAbs.top - this.lastPositionAbs.top;
            return a != 0 && (a > 0 ? "down": "up")
        },
        _getDragHorizontalDirection: function() {
            var a = this.positionAbs.left - this.lastPositionAbs.left;
            return a != 0 && (a > 0 ? "right": "left")
        },
        refresh: function(a) {
            return this._refreshItems(a),
            this.refreshPositions(),
            this
        },
        _connectWith: function() {
            var a = this.options;
            return a.connectWith.constructor == String ? [a.connectWith] : a.connectWith
        },
        _getItemsAsjQuery: function(b) {
            var c = [],
            e = [],
            f = this._connectWith();
            if (f && b) for (b = f.length - 1; b >= 0; b--) for (var g = a(f[b]), h = g.length - 1; h >= 0; h--) {
                var i = a.data(g[h], "sortable");
                i && i != this && !i.options.disabled && e.push([a.isFunction(i.options.items) ? i.options.items.call(i.element) : a(i.options.items, i.element).not(".ui-sortable-helper").not(".ui-sortable-placeholder"), i])
            }
            e.push([a.isFunction(this.options.items) ? this.options.items.call(this.element, null, {
                options: this.options,
                item: this.currentItem
            }) : a(this.options.items, this.element).not(".ui-sortable-helper").not(".ui-sortable-placeholder"), this]);
            for (b = e.length - 1; b >= 0; b--) e[b][0].each(function() {
                c.push(this)
            });
            return a(c)
        },
        _removeCurrentsFromItems: function() {
            for (var a = this.currentItem.find(":data(sortable-item)"), b = 0; b < this.items.length; b++) for (var c = 0; c < a.length; c++) a[c] == this.items[b].item[0] && this.items.splice(b, 1)
        },
        _refreshItems: function(b) {
            this.items = [],
            this.containers = [this];
            var c = this.items,
            e = [[a.isFunction(this.options.items) ? this.options.items.call(this.element[0], b, {
                item: this.currentItem
            }) : a(this.options.items, this.element), this]],
            f = this._connectWith();
            if (f) for (var g = f.length - 1; g >= 0; g--) for (var h = a(f[g]), i = h.length - 1; i >= 0; i--) {
                var j = a.data(h[i], "sortable");
                j && j != this && !j.options.disabled && (e.push([a.isFunction(j.options.items) ? j.options.items.call(j.element[0], b, {
                    item: this.currentItem
                }) : a(j.options.items, j.element), j]), this.containers.push(j))
            }
            for (g = e.length - 1; g >= 0; g--) {
                b = e[g][1],
                f = e[g][0],
                i = 0;
                for (h = f.length; i < h; i++) j = a(f[i]),
                j.data("sortable-item", b),
                c.push({
                    item: j,
                    instance: b,
                    width: 0,
                    height: 0,
                    left: 0,
                    top: 0
                })
            }
        },
        refreshPositions: function(b) {
            this.offsetParent && this.helper && (this.offset.parent = this._getParentOffset());
            for (var c = this.items.length - 1; c >= 0; c--) {
                var e = this.items[c];
                if (e.instance == this.currentContainer || !this.currentContainer || e.item[0] == this.currentItem[0]) {
                    var f = this.options.toleranceElement ? a(this.options.toleranceElement, e.item) : e.item;
                    b || (e.width = f.outerWidth(), e.height = f.outerHeight()),
                    f = f.offset(),
                    e.left = f.left,
                    e.top = f.top
                }
            }
            if (this.options.custom && this.options.custom.refreshContainers) this.options.custom.refreshContainers.call(this);
            else for (c = this.containers.length - 1; c >= 0; c--) f = this.containers[c].element.offset(),
            this.containers[c].containerCache.left = f.left,
            this.containers[c].containerCache.top = f.top,
            this.containers[c].containerCache.width = this.containers[c].element.outerWidth(),
            this.containers[c].containerCache.height = this.containers[c].element.outerHeight();
            return this
        },
        _createPlaceholder: function(b) {
            var c = b || this,
            e = c.options;
            if (!e.placeholder || e.placeholder.constructor == String) {
                var f = e.placeholder;
                e.placeholder = {
                    element: function() {
                        var b = a(document.createElement(c.currentItem[0].nodeName)).addClass(f || c.currentItem[0].className + " ui-sortable-placeholder").removeClass("ui-sortable-helper")[0];
                        return f || (b.style.visibility = "hidden"),
                        b
                    },
                    update: function(a, b) {
                        if (!f || !!e.forcePlaceholderSize) b.height() || b.height(c.currentItem.innerHeight() - parseInt(c.currentItem.css("paddingTop") || 0, 10) - parseInt(c.currentItem.css("paddingBottom") || 0, 10)),
                        b.width() || b.width(c.currentItem.innerWidth() - parseInt(c.currentItem.css("paddingLeft") || 0, 10) - parseInt(c.currentItem.css("paddingRight") || 0, 10))
                    }
                }
            }
            c.placeholder = a(e.placeholder.element.call(c.element, c.currentItem)),
            c.currentItem.after(c.placeholder),
            e.placeholder.update(c, c.placeholder)
        },
        _contactContainers: function(b) {
            for (var c = null, e = null, f = this.containers.length - 1; f >= 0; f--) if (!a.ui.contains(this.currentItem[0], this.containers[f].element[0])) if (this._intersectsWith(this.containers[f].containerCache)) {
                if (!c || !a.ui.contains(this.containers[f].element[0], c.element[0])) c = this.containers[f],
                e = f
            } else this.containers[f].containerCache.over && (this.containers[f]._trigger("out", b, this._uiHash(this)), this.containers[f].containerCache.over = 0);
            if (c) if (this.containers.length === 1) this.containers[e]._trigger("over", b, this._uiHash(this)),
            this.containers[e].containerCache.over = 1;
            else if (this.currentContainer != this.containers[e]) {
                c = 1e4,
                f = null;
                for (var g = this.positionAbs[this.containers[e].floating ? "left": "top"], h = this.items.length - 1; h >= 0; h--) if (a.ui.contains(this.containers[e].element[0], this.items[h].item[0])) {
                    var i = this.items[h][this.containers[e].floating ? "left": "top"];
                    Math.abs(i - g) < c && (c = Math.abs(i - g), f = this.items[h])
                }
                if (f || this.options.dropOnEmpty) this.currentContainer = this.containers[e],
                f ? this._rearrange(b, f, null, !0) : this._rearrange(b, null, this.containers[e].element, !0),
                this._trigger("change", b, this._uiHash()),
                this.containers[e]._trigger("change", b, this._uiHash(this)),
                this.options.placeholder.update(this.currentContainer, this.placeholder),
                this.containers[e]._trigger("over", b, this._uiHash(this)),
                this.containers[e].containerCache.over = 1
            }
        },
        _createHelper: function(b) {
            var c = this.options;
            return b = a.isFunction(c.helper) ? a(c.helper.apply(this.element[0], [b, this.currentItem])) : c.helper == "clone" ? this.currentItem.clone() : this.currentItem,
            b.parents("body").length || a(c.appendTo != "parent" ? c.appendTo: this.currentItem[0].parentNode)[0].appendChild(b[0]),
            b[0] == this.currentItem[0] && (this._storedCSS = {
                width: this.currentItem[0].style.width,
                height: this.currentItem[0].style.height,
                position: this.currentItem.css("position"),
                top: this.currentItem.css("top"),
                left: this.currentItem.css("left")
            }),
            (b[0].style.width == "" || c.forceHelperSize) && b.width(this.currentItem.width()),
            (b[0].style.height == "" || c.forceHelperSize) && b.height(this.currentItem.height()),
            b
        },
        _adjustOffsetFromHelper: function(b) {
            typeof b == "string" && (b = b.split(" ")),
            a.isArray(b) && (b = {
                left: +b[0],
                top: +b[1] || 0
            }),
            "left" in b && (this.offset.click.left = b.left + this.margins.left),
            "right" in b && (this.offset.click.left = this.helperProportions.width - b.right + this.margins.left),
            "top" in b && (this.offset.click.top = b.top + this.margins.top),
            "bottom" in b && (this.offset.click.top = this.helperProportions.height - b.bottom + this.margins.top)
        },
        _getParentOffset: function() {
            this.offsetParent = this.helper.offsetParent();
            var b = this.offsetParent.offset();
            this.cssPosition == "absolute" && this.scrollParent[0] != document && a.ui.contains(this.scrollParent[0], this.offsetParent[0]) && (b.left += this.scrollParent.scrollLeft(), b.top += this.scrollParent.scrollTop());
            if (this.offsetParent[0] == document.body || this.offsetParent[0].tagName && this.offsetParent[0].tagName.toLowerCase() == "html" && a.browser.msie) b = {
                top: 0,
                left: 0
            };
            return {
                top: b.top + (parseInt(this.offsetParent.css("borderTopWidth"), 10) || 0),
                left: b.left + (parseInt(this.offsetParent.css("borderLeftWidth"), 10) || 0)
            }
        },
        _getRelativeOffset: function() {
            if (this.cssPosition == "relative") {
                var a = this.currentItem.position();
                return {
                    top: a.top - (parseInt(this.helper.css("top"), 10) || 0) + this.scrollParent.scrollTop(),
                    left: a.left - (parseInt(this.helper.css("left"), 10) || 0) + this.scrollParent.scrollLeft()
                }
            }
            return {
                top: 0,
                left: 0
            }
        },
        _cacheMargins: function() {
            this.margins = {
                left: parseInt(this.currentItem.css("marginLeft"), 10) || 0,
                top: parseInt(this.currentItem.css("marginTop"), 10) || 0
            }
        },
        _cacheHelperProportions: function() {
            this.helperProportions = {
                width: this.helper.outerWidth(),
                height: this.helper.outerHeight()
            }
        },
        _setContainment: function() {
            var b = this.options;
            b.containment == "parent" && (b.containment = this.helper[0].parentNode);
            if (b.containment == "document" || b.containment == "window") this.containment = [0 - this.offset.relative.left - this.offset.parent.left, 0 - this.offset.relative.top - this.offset.parent.top, a(b.containment == "document" ? document: window).width() - this.helperProportions.width - this.margins.left, (a(b.containment == "document" ? document: window).height() || document.body.parentNode.scrollHeight) - this.helperProportions.height - this.margins.top];
            if (!/^(document|window|parent)$/.test(b.containment)) {
                var c = a(b.containment)[0];
                b = a(b.containment).offset();
                var e = a(c).css("overflow") != "hidden";
                this.containment = [b.left + (parseInt(a(c).css("borderLeftWidth"), 10) || 0) + (parseInt(a(c).css("paddingLeft"), 10) || 0) - this.margins.left, b.top + (parseInt(a(c).css("borderTopWidth"), 10) || 0) + (parseInt(a(c).css("paddingTop"), 10) || 0) - this.margins.top, b.left + (e ? Math.max(c.scrollWidth, c.offsetWidth) : c.offsetWidth) - (parseInt(a(c).css("borderLeftWidth"), 10) || 0) - (parseInt(a(c).css("paddingRight"), 10) || 0) - this.helperProportions.width - this.margins.left, b.top + (e ? Math.max(c.scrollHeight, c.offsetHeight) : c.offsetHeight) - (parseInt(a(c).css("borderTopWidth"), 10) || 0) - (parseInt(a(c).css("paddingBottom"), 10) || 0) - this.helperProportions.height - this.margins.top]
            }
        },
        _convertPositionTo: function(b, c) {
            c || (c = this.position),
            b = b == "absolute" ? 1: -1;
            var e = this.cssPosition != "absolute" || this.scrollParent[0] != document && !!a.ui.contains(this.scrollParent[0], this.offsetParent[0]) ? this.scrollParent: this.offsetParent,
            f = /(html|body)/i.test(e[0].tagName);
            return {
                top: c.top + this.offset.relative.top * b + this.offset.parent.top * b - (a.browser.safari && this.cssPosition == "fixed" ? 0: (this.cssPosition == "fixed" ? -this.scrollParent.scrollTop() : f ? 0: e.scrollTop()) * b),
                left: c.left + this.offset.relative.left * b + this.offset.parent.left * b - (a.browser.safari && this.cssPosition == "fixed" ? 0: (this.cssPosition == "fixed" ? -this.scrollParent.scrollLeft() : f ? 0: e.scrollLeft()) * b)
            }
        },
        _generatePosition: function(b) {
            var c = this.options,
            e = this.cssPosition != "absolute" || this.scrollParent[0] != document && !!a.ui.contains(this.scrollParent[0], this.offsetParent[0]) ? this.scrollParent: this.offsetParent,
            f = /(html|body)/i.test(e[0].tagName);
            this.cssPosition == "relative" && (this.scrollParent[0] == document || this.scrollParent[0] == this.offsetParent[0]) && (this.offset.relative = this._getRelativeOffset());
            var g = b.pageX,
            h = b.pageY;
            return this.originalPosition && (this.containment && (b.pageX - this.offset.click.left < this.containment[0] && (g = this.containment[0] + this.offset.click.left), b.pageY - this.offset.click.top < this.containment[1] && (h = this.containment[1] + this.offset.click.top), b.pageX - this.offset.click.left > this.containment[2] && (g = this.containment[2] + this.offset.click.left), b.pageY - this.offset.click.top > this.containment[3] && (h = this.containment[3] + this.offset.click.top)), c.grid && (h = this.originalPageY + Math.round((h - this.originalPageY) / c.grid[1]) * c.grid[1], h = this.containment ? h - this.offset.click.top < this.containment[1] || h - this.offset.click.top > this.containment[3] ? h - this.offset.click.top < this.containment[1] ? h + c.grid[1] : h - c.grid[1] : h: h, g = this.originalPageX + Math.round((g - this.originalPageX) / c.grid[0]) * c.grid[0], g = this.containment ? g - this.offset.click.left < this.containment[0] || g - this.offset.click.left > this.containment[2] ? g - this.offset.click.left < this.containment[0] ? g + c.grid[0] : g - c.grid[0] : g: g)),
            {
                top: h - this.offset.click.top - this.offset.relative.top - this.offset.parent.top + (a.browser.safari && this.cssPosition == "fixed" ? 0: this.cssPosition == "fixed" ? -this.scrollParent.scrollTop() : f ? 0: e.scrollTop()),
                left: g - this.offset.click.left - this.offset.relative.left - this.offset.parent.left + (a.browser.safari && this.cssPosition == "fixed" ? 0: this.cssPosition == "fixed" ? -this.scrollParent.scrollLeft() : f ? 0: e.scrollLeft())
            }
        },
        _rearrange: function(a, b, c, d) {
            c ? c[0].appendChild(this.placeholder[0]) : b.item[0].parentNode.insertBefore(this.placeholder[0], this.direction == "down" ? b.item[0] : b.item[0].nextSibling),
            this.counter = this.counter ? ++this.counter: 1;
            var e = this,
            f = this.counter;
            window.setTimeout(function() {
                f == e.counter && e.refreshPositions(!d)
            },
            0)
        },
        _clear: function(b, c) {
            this.reverting = !1;
            var e = []; ! this._noFinalSort && this.currentItem.parent().length && this.placeholder.before(this.currentItem),
            this._noFinalSort = null;
            if (this.helper[0] == this.currentItem[0]) {
                for (var f in this._storedCSS) if (this._storedCSS[f] == "auto" || this._storedCSS[f] == "static") this._storedCSS[f] = "";
                this.currentItem.css(this._storedCSS).removeClass("ui-sortable-helper")
            } else this.currentItem.show();
            this.fromOutside && !c && e.push(function(a) {
                this._trigger("receive", a, this._uiHash(this.fromOutside))
            }),
            (this.fromOutside || this.domPosition.prev != this.currentItem.prev().not(".ui-sortable-helper")[0] || this.domPosition.parent != this.currentItem.parent()[0]) && !c && e.push(function(a) {
                this._trigger("update", a, this._uiHash())
            });
            if (!a.ui.contains(this.element[0], this.currentItem[0])) {
                c || e.push(function(a) {
                    this._trigger("remove", a, this._uiHash())
                });
                for (f = this.containers.length - 1; f >= 0; f--) a.ui.contains(this.containers[f].element[0], this.currentItem[0]) && !c && (e.push(function(a) {
                    return function(b) {
                        a._trigger("receive", b, this._uiHash(this))
                    }
                }.call(this, this.containers[f])), e.push(function(a) {
                    return function(b) {
                        a._trigger("update", b, this._uiHash(this))
                    }
                }.call(this, this.containers[f])))
            }
            for (f = this.containers.length - 1; f >= 0; f--) c || e.push(function(a) {
                return function(b) {
                    a
                    ._trigger("deactivate", b, this._uiHash(this))
                }
            }.call(this, this.containers[f])),
            this.containers[f].containerCache.over && (e.push(function(a) {
                return function(b) {
                    a._trigger("out", b, this._uiHash(this))
                }
            }.call(this, this.containers[f])), this.containers[f].containerCache.over = 0);
            this._storedCursor && a("body").css("cursor", this._storedCursor),
            this._storedOpacity && this.helper.css("opacity", this._storedOpacity),
            this._storedZIndex && this.helper.css("zIndex", this._storedZIndex == "auto" ? "": this._storedZIndex),
            this.dragging = !1;
            if (this.cancelHelperRemoval) {
                if (!c) {
                    this._trigger("beforeStop", b, this._uiHash());
                    for (f = 0; f < e.length; f++) e[f].call(this, b);
                    this._trigger("stop", b, this._uiHash())
                }
                return ! 1
            }
            c || this._trigger("beforeStop", b, this._uiHash()),
            this.placeholder[0].parentNode.removeChild(this.placeholder[0]),
            this.helper[0] != this.currentItem[0] && this.helper.remove(),
            this.helper = null;
            if (!c) {
                for (f = 0; f < e.length; f++) e[f].call(this, b);
                this._trigger("stop", b, this._uiHash())
            }
            return this.fromOutside = !1,
            !0
        },
        _trigger: function() {
            a.Widget.prototype._trigger.apply(this, arguments) === !1 && this.cancel()
        },
        _uiHash: function(b) {
            var c = b || this;
            return {
                helper: c.helper,
                placeholder: c.placeholder || a([]),
                position: c.position,
                originalPosition: c.originalPosition,
                offset: c.positionAbs,
                item: c.currentItem,
                sender: b ? b.element: null
            }
        }
    }),
    a.extend(a.ui.sortable, {
        version: "1.8.16"
    })
} (jQuery);
