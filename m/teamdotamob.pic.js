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
                b.push('\n'),
                b.push('\n        </figcaption>\n      </div></div>\n    </figure>\n  ');
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
})(jQuery);