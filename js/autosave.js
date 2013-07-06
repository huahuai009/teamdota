var TEAMDOTA = TEAMDOTA || {};
TEAMDOTA.AutoSave = function() {
	function t() {
		v()
	}
	function u(a) {
		a ? window.onbeforeunload = null: window.onbeforeunload = function() {
			return "\u8fd8\u6709\u4e00\u4e9b\u5185\u5bb9\u5c1a\u672a\u53d1\u5e03\uff0c\u786e\u5b9a\u53d6\u6d88\u5417\uff1f\u672a\u4fdd\u5b58\u7684\u6570\u636e\u4f1a\u4e22\u5931\uff01"
		}
	}
	function v() {
		function a() {
			//u(),
			w()
		}
		a()
		//$(window).bind("keydown", a),
		//$(window).bind("click", a)
	}
	function w() {
		if (o) return;
		o = setTimeout(function() {
			o = null,
			x()
		},
		k)
	}
	function x() {
		if (s) {
			return
		}
		edit_save(),
		s = !0,
		o && (clearTimeout(o), o = null),
		p = $.ajax({
			type: "post",
			url: "cp.php?ac=document&inajax=1&autosave=1&project_id=" + window.bbcx.currentProject,
			data: "name="+$("#name").val()+"&description="+$("#qinbaba-ttHtmlEditor").val()+"&document_id="+$("#document_id").val()+"&documentsubmit="+$("#documentsubmit").val()+"&formhash="+$("#formhash").val(),
			success: function(c) {
				v(),
				//u(!0),
				s = !1,
				p = null
			},
			error: function() {
				s = !0,
				p = null
			}
		})
	}
	var j = 1e3,
	k = 15 * j,
	o,
	p,
	s = !1;
	return {
		init: t
	}
}();