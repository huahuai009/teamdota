var TEAMDOTA = TEAMDOTA || {};
TEAMDOTA.InitSidebar = function() {
    if (!$("#toppanel").length) {
        $(document.body).prepend('<div class="w ld" id="toppanel"></div>');
    }
    $("#toppanel").append('<div id="sidepanel" class="hide"></div>');
    var a = $("#sidepanel");
    this.scroll = function() {
        var b = this;
        $(window).bind("scroll", 
        function() {
            var c = document.body.scrollTop || document.documentElement.scrollTop;
            if (c == 0) {
                a.hide();
            } else {
                a.show();
            }
        });
        b.initCss();
        $(window).bind("resize", 
        function() {
            b.initCss();
        });
    };
    this.initCss = function() {
        var b,
        c = 880;
        if (screen.width >= 880) {
            if ($.browser.msie && $.browser.version <= 6) {
                b = {
                    right: "-26px"
                };
            } else {
                b = {
                    right: (document.documentElement.clientWidth - c) / 2 - 26 + "px"
                };
            }
            a.css(b);
        }
    };
    this.addCss = function(b) {
        a.css(b);
    };
    this.addItem = function(b) {
        a.append(b);
    };
    this.setTop = function() {
        this.addItem("<a href='#' class='gotop' title='使用快捷键T也可返回顶部哦！'><b></b>返回顶部</a>");
    };
};
var td = new TEAMDOTA.InitSidebar();
td.setTop();td.scroll();