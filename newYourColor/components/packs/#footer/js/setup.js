jQuery.event.special.touchstart = {
        setup: function(e, t, a) {
            this.addEventListener("touchstart", a, {
                passive: !t.includes("noPreventDefault")
            })
        }
    },
    function(e) {
        for (var t = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/", a = "", r = [256], o = [256], s = 0, i = {
                encode: function(e) {
                    return e.replace(/[\u0080-\u07ff]/g, (function(e) {
                        var t = e.charCodeAt(0);
                        return String.fromCharCode(192 | t >> 6, 128 | 63 & t)
                    })).replace(/[\u0800-\uffff]/g, (function(e) {
                        var t = e.charCodeAt(0);
                        return String.fromCharCode(224 | t >> 12, 128 | t >> 6 & 63, 128 | 63 & t)
                    }))
                },
                decode: function(e) {
                    return e.replace(/[\u00e0-\u00ef][\u0080-\u00bf][\u0080-\u00bf]/g, (function(e) {
                        return String.fromCharCode((15 & e.charCodeAt(0)) << 12 | (63 & e.charCodeAt(1)) << 6 | 63 & e.charCodeAt(2))
                    })).replace(/[\u00c0-\u00df][\u0080-\u00bf]/g, (function(e) {
                        return String.fromCharCode((31 & e.charCodeAt(0)) << 6 | 63 & e.charCodeAt(1))
                    }))
                }
            }; s < 256;) {
            var n = String.fromCharCode(s);
            a += n, o[s] = s, r[s] = t.indexOf(n), ++s
        }

        function l(e, t, a, r, o, s) {
            for (var i = 0, n = 0, l = (e = String(e)).length, d = "", u = 0; n < l;) {
                var c = e.charCodeAt(n);
                for (i = (i << o) + (c = c < 256 ? a[c] : -1), u += o; u >= s;) {
                    var m = i >> (u -= s);
                    d += r.charAt(m), i ^= m << u
                }++n
            }
            return !t && u > 0 && (d += r.charAt(i << s - u)), d
        }
        var d = e.base64 = function(e, t, a) {
            return t ? d[e](t, a) : e ? null : this
        };
        d.btoa = d.encode = function(e, a) {
            return (e = l(e = !1 === d.raw || d.utf8encode || a ? i.encode(e) : e, !1, o, t, 8, 6)) + "====".slice(e.length % 4 || 4)
        }, d.atob = d.decode = function(e, t) {
            var o = (e = String(e = e.replace(/[^A-Za-z0-9\+\/\=]/g, "")).split("=")).length;
            do {
                e[--o] = l(e[o], !0, r, a, 6, 8)
            } while (o > 0);
            return e = e.join(""), !1 === d.raw || d.utf8decode || t ? i.decode(e) : e
        }
    }(jQuery), $.base64.utf8encode = !0,
    function(e) {
        "function" == typeof define && define.amd ? define(["jquery"], e) : "object" == typeof exports ? module.exports = e(require("jquery")) : e(jQuery)
    }((function(e) {
        var t = /\+/g;

        function a(e) {
            return s.raw ? e : encodeURIComponent(e)
        }

        function r(e) {
            return s.raw ? e : decodeURIComponent(e)
        }

        function o(a, r) {
            var o = s.raw ? a : function(e) {
                0 === e.indexOf('"') && (e = e.slice(1, -1).replace(/\\"/g, '"').replace(/\\\\/g, "\\"));
                try {
                    return e = decodeURIComponent(e.replace(t, " ")), s.json ? JSON.parse(e) : e
                } catch (e) {}
            }(a);
            return e.isFunction(r) ? r(o) : o
        }
        var s = e.cookie = function(t, i, n) {
            if (arguments.length > 1 && !e.isFunction(i)) {
                if ("number" == typeof(n = e.extend({}, s.defaults, n)).expires) {
                    var l, d = n.expires,
                        u = n.expires = new Date;
                    u.setMilliseconds(u.getMilliseconds() + 864e5 * d)
                }
                return document.cookie = [a(t), "=", (l = i, a(s.json ? JSON.stringify(l) : String(l))), n.expires ? "; expires=" + n.expires.toUTCString() : "", n.path ? "; path=" + n.path : "", n.domain ? "; domain=" + n.domain : "", n.secure ? "; secure" : ""].join("")
            }
            for (var c = t ? void 0 : {}, m = document.cookie ? document.cookie.split("; ") : [], h = 0, p = m.length; h < p; h++) {
                var g = m[h].split("="),
                    f = r(g.shift()),
                    v = g.join("=");
                if (t === f) {
                    c = o(v, i);
                    break
                }
                t || void 0 === (v = o(v)) || (c[f] = v)
            }
            return c
        };
        s.defaults = {}, e.removeCookie = function(t, a) {
            return e.cookie(t, "", e.extend({}, a, {
                expires: -1
            })), !e.cookie(t)
        }
    }));
var RetryInterval, mouseY, mouseX, CookiedAjax = [];

function ensureCssFileInclusion(e) {
    for (var t = document.styleSheets, a = 0, r = t.length; a < r; a++)
        if (t[a].href == e) return;
    var o = document.createElement("link");
    o.rel = "stylesheet", o.href = e, document.getElementsByTagName("head")[0].appendChild(o)
}
$("body").on("click", ".ez-toc-title", (function() {
    $(this).parent().toggleClass("-opentable-")
})), $("body").on("click", "#openSEarch i.far.fa-search", (function() {
    $(".search_header form").addClass("active"), $("#openSEarch").addClass("close"), $(".menu-nav").removeClass("open"), $(".menu_bar").removeClass("icon")
})), $("body").on("click", "#openSEarch .fa-xmark", (function() {
    $(".search_header form").removeClass("active"), $("#openSEarch").removeClass("close")
})), 1 == ISMobile && $("body").on("click", ".menu-item-has-children>a", (function(e) {
    e.preventDefault(), e.stopPropagation(), $(this).parent().find(">i").toggleClass("trans"), $(".menu-item-has-children").toggleClass("hover"), $(this).parent().find("> ul.sub-menu").toggleClass("active"), $(this).not($(this).find("> ul.sub-menu"))
}));
var AjaxHandlerXHR = !1,
    AjaxHandlerLastData = !1;

function AjaxRequest(e) {
    return 0 != AjaxHandlerXHR && AjaxHandlerLastData.url == e.url && AjaxHandlerXHR.abort(), e.error = function(t, a) {
        var r = "";
        404 == t.status ? (r = "لم يتم العثور على الصفحة.", AjaxHandlerXHR = !1) : 500 == t.status ? (r = "حدث خطأ اثناء معاينة الملف.", AjaxHandlerXHR = !1) : "timeout" === a && (r = "إنتهت مهلة الإتصال.", AjaxHandlerXHR = !1), "" != r && null != r && (RetryInterval = setInterval(AjaxRequest(e), 5e3))
    }, AjaxHandlerLastData = e, AjaxHandlerXHR = $.ajax(e).done((function() {
        clearInterval(RetryInterval), AjaxHandlerXHR = !1, InitializeTrig(), LazyloaderHook()
    })), !0
}
var LoadedPages = [];

function ChangeTitle(e) {
    $(document).prop("title", e)
}

function outerHTML(e) {
    return e.outerHTML || (new XMLSerializer).serializeToString(e)
}

function BackToURL(e) {
    null != LoadedPages[e] ? ($("root").after(LoadedPages[e].output).remove(), $("body, html").animate({
        scrollTop: LoadedPages[e].scroll
    }, 0), LoadedPages[location.href] = {
        title: $(document).prop("title"),
        output: outerHTML(document.querySelector("root")),
        scroll: $(document).scrollTop() || $(window).scrollTop()
    }, ChangeTitle(LoadedPages[e].title), ChangeURL(e), InitializeTrig(), $(DeletedPostsItems).each((function(e, t) {
        $('.PostItem[cpd="' + t + '"]').remove()
    }))) : ($("#BackPopstateURL").remove(), $("root").append('<a href="' + e + '" id="BackPopstateURL" style="display:none;">eee</a>'), $("#BackPopstateURL").trigger("click"))
}

function AjaxNavigate(e, t = "page") {
    0 != AjaxHandlerXHR && AjaxHandlerXHR.abort(), Count = 0, LoadingRelated = !1, Loadingsingle = !1, LoadingMore = !1, 0 != sharepopover && sharepopover.trigger("destroy.owl.carousel").css("opacity", "0"), LoadedPages[location.href] = {
        title: $(document).prop("title"),
        output: outerHTML(document.querySelector("root")),
        scroll: $(document).scrollTop() || $(window).scrollTop()
    }, $(".--news-loader").remove(), mouseY -= $(window).scrollTop(), $("body").append('<div style="left:' + (mouseX - 35) + "px;top:" + (mouseY - 35) + 'px;" class="--news-loader"><svg version="1.1" id="loader-1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="30px" height="30px" viewBox="0 0 50 50" style="enable-background:new 0 0 50 50;" xml:space="preserve"> <path fill="#005fa3" d="M43.935,25.145c0-10.318-8.364-18.683-18.683-18.683c-10.318,0-18.683,8.365-18.683,18.683h4.068c0-8.071,6.543-14.615,14.615-14.615c8.072,0,14.615,6.543,14.615,14.615H43.935z"> <animateTransform attributeType="xml" attributeName="transform" type="rotate" from="0 25 25" to="360 25 25" dur="0.6s" repeatCount="indefinite"></animateTransform> </path> </svg></div>'), AjaxRequest({
        url: e,
        type: "GET",
        data: {
            ajax: !0
        },
        dataType: "json",
        success: function(t) {
            $(".--news-loader").remove(), $("root").after(t.output).remove();
            var a = !1;
            e.indexOf("#") > -1 && (a = e.split("#")[1]), 0 == a ? $("body, html").animate({
                scrollTop: 0
            }, 0) : $("body, html").animate({
                scrollTop: $("#" + a).offset().top
            }, 0), ChangeURL(e), ChangeTitle(t.title), InitializeTrig()
        }
    })
}
window.onpopstate = function(e) {
    BackToURL(url = location.href), e.preventDefault()
}, $(window).on("mousemove", (function(e) {
    mouseX = e.pageX, mouseY = e.pageY
}));
var charstoformid = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz".split(""),
    UniqID = function() {
        for (var e = "", t = 0; t < 10; t++) e += charstoformid[Math.floor(Math.random() * charstoformid.length)];
        return e
    };

function isEmpty(e) {
    return !$.trim(e)
}

function stripslashes(e) {
    return (e + "").replace(/\\(.?)/g, (function(e, t) {
        switch (t) {
            case "\\":
                return "\\";
            case "0":
                return "\0";
            case "":
                return "";
            default:
                return t
        }
    }))
}

function strip_tags(e, t) {
    return t = (((t || "") + "").toLowerCase().match(/<[a-z][a-z0-9]*>/g) || []).join(""), e.replace(/<!--[\s\S]*?-->|<\?(?:php)?[\s\S]*?\?>/gi, "").replace(/<\/?([a-z][a-z0-9]*)\b[^>]*>/gi, (function(e, a) {
        return t.indexOf("<" + a.toLowerCase() + ">") > -1 ? e : ""
    }))
}

function loadImage(e, t) {
    return new Promise((function(a, r) {
        e.onload = a, e.onerror = r, e.setAttribute("src", t)
    }))
}
var IPInfo = !1,
    SidebarTerms = !1,
    CurrenciesSlider = !1,
    Scrollingslider = !1,
    sharepopover = !1,
    articlesmodel3 = !1,
    matchesbar = !1;

function InitializeTrig() {
    $(window).width(), "" == $("[autofocus]").val() && $("[autofocus]").focus(), $(window).scroll(ScrollListener), ScrollListener(), $(".album-slider").length > 0 && 0 == $(".album-slider.owl-loaded").length && $(".album-slider").not(".-owl-loaded").each((function(e, t) {
        sharepopover = $(t).owlCarousel({
            margin: 15,
            rtl: !0,
            loop: !1,
            center: !1,
            nav: !0,
            dots: !1,
            navSpeed: 300,
            navText: ['<i class="fa fa-angle-left"></i>', '<i class="fa fa-angle-right"></i>'],
            autoplay: !1,
            responsiveClass: !0,
            items: 1
        }).css("opacity", "1")
    })), $(".price-block").length > 0 && 0 == $(".price-block.owl-loaded").length && $(".price-block").not(".-owl-loaded").each((function(e, t) {
        sharepopover = $(t).owlCarousel({
            rtl: !0,
            center: !0,
            nav: !0,
            dots: !0,
            items: 1,
            margin: 30,
            navSpeed: 300,
            navText: ['<i class="fa fa-angle-left"></i>', '<i class="fa fa-angle-right"></i>'],
            autoplay: !1,
            responsiveClass: !0,
            items: 1
        }).css("opacity", "1")
    })), $(".works-single-img-").length > 0 && 0 == $(".works-single-img-.owl-loaded").length && $(".works-single-img-").not(".-owl-loaded").each((function(e, t) {
        sharepopover = $(t).owlCarousel({
            margin: 15,
            rtl: !0,
            loop: !0,
            center: !1,
            nav: !0,
            dots: !1,
            navSpeed: 1e3,
            navText: ['<i class="fa fa-angle-left"></i>', '<i class="fa fa-angle-right"></i>'],
            autoplay: !0,
            autoplayTimeout: 5e3,
            autoplayHoverPause: !1,
            responsiveClass: !0,
            items: 1
        }).css("opacity", "1")
    })), $(".container-page-intro").length > 0 && 0 == $(".container-page-intro.owl-loaded").length && $(".container-page-intro").not(".-owl-loaded").each((function(e, t) {
        sharepopover = $(t).owlCarousel({
            margin: 15,
            rtl: !0,
            loop: !1,
            center: !1,
            nav: !0,
            dots: !0,
            navSpeed: 1e3,
            autoplayTimeout: 5e3,
            autoplayHoverPause: !0,
            navText: ['<i class="fa fa-angle-left"></i>', '<i class="fa fa-angle-right"></i>'],
            autoplay: !1,
            responsiveClass: !0,
            items: 1
        }).css("opacity", "1")
    })), $(".imagecover-").length > 0 && 0 == $(".imagecover-.owl-loaded").length && $(".imagecover-").not(".-owl-loaded").each((function(e, t) {
        sharepopover = $(t).owlCarousel({
            margin: 15,
            rtl: !0,
            loop: !1,
            center: !1,
            nav: !0,
            dots: !0,
            navSpeed: 1e3,
            autoplayTimeout: 5e3,
            autoplayHoverPause: !0,
            navText: ['<i class="fa fa-angle-left"></i>', '<i class="fa fa-angle-right"></i>'],
            autoplay: !1,
            responsiveClass: !0,
            items: 1
        }).css("opacity", "1")
    }))
}
InitializeTrig();
var Count = 0,
    LoadingRelated = !1,
    LoadingMore = !1,
    LoadedCounter = 1,
    SingleCounterMore = 0,
    SingleScrollAllowed = !1;

function ScrollListener() {
    $("[data-loadmore]").length > 0 && $(window).scrollTop() + $(window).height() > $("[data-loadmore]").offset().top + $("[data-loadmore]").height() - 600 && 0 == LoadingMore && 0 != $("[data-loadmore]").data("autoloaded") && LoadedCounter < 3 && 0 == $("[data-loadmore]").data("finish") && (LoadedCounter += 1, MoreAjax($("[data-loadmore]")))
}

function __loc(e) {
    return e
}

function SubmitComment(e) {
    var t = 0;
    if ($(e).find(".alerts").html(""), $(e).find(".necessary").removeClass("necessary"), 1 == Currentuser_Logged) {
        var a = {
            action: "AddComment",
            type: "عضو",
            yourname: Currentuser_display_name,
            email: Currentuser_email,
            message: $(e).find("textarea").val(),
            rate: $(e).find(".RateValue").val(),
            postID: $(e).data("id"),
            parent: $(e).attr("data-parent")
        };
        "" == $(e).find("textarea").val() && (t++, $(e).find("textarea").addClass("necessary")), "" == $(e).find(".RateValue").val() && (t++, $(e).find(".RateComment").addClass("necessary"))
    } else a = {
        action: "AddComment",
        type: "عضو",
        yourname: $(e).find('[name="yourname"]').val(),
        email: $(e).find('[name="email"]').val(),
        message: $(e).find("textarea").val(),
        rate: $(e).find(".RateValue").val(),
        postID: $(e).data("id"),
        parent: $(e).attr("data-parent")
    }, "" == $(e).find("textarea").val() && (t++, $(e).find("textarea").addClass("necessary")), "" == $(e).find(".RateValue").val() && (t++, $(e).find(".RateComment").addClass("necessary")), "" == $(e).find('[name="yourname"]').val() && (t++, $(e).find('[name="yourname"]').addClass("necessary")), "" == $(e).find('[name="email"]').val() && (t++, $(e).find('[name="email"]').addClass("necessary"));
    t > 0 ? $(e).find(".alerts").append('<div class="alert alert-danger">يرجي ملأ الحقول المطلوبة (*)</div>') : ($(".NoComments").remove(), $(e).addClass("loading"), $(e).append('<div class="loader"><div class="line"></div> <div class="line"></div> <div class="line"></div> <div class="line"></div> </div>'), AjaxRequest({
        url: HomeURL + "/AjaxCenter/AddComment/",
        type: "POST",
        data: a,
        dataType: "json",
        success: function(t) {
            null == t.error && ($(e).removeClass("loading"), $(e).find(".loader").remove(), $(e).trigger("reset"), $(e).find(".RatingReview > i").removeClass("fixedactive active"), $(e).find(".RateValue").val(""), "0" == $(e).attr("data-parent") ? $(".CommentsListInner").closest("under-post-comments").length > 0 ? $(".CommentsListInner").prepend(t.output) : $(".CommentsListInner").append(t.output) : $("#comment-" + $(e).attr("data-parent")).after('<ul class="ChildComments">' + t.output + "</ul>"))
        }
    }))
}

function ReplyComment(e) {
    $("form.CommentsFormInner").attr("data-parent", $(e).data("comment")), $(".ReplyCommentPreview").remove(), $("form.CommentsFormInner").prepend('<div class="ReplyCommentPreview"><h2><i class="fas fa-reply"></i><em>' + __loc("رد علي") + "</em> <span>" + $(e).parent().find(".NameArea").text() + "</span></h2><p>" + $(e).parent().find(".CommentContent > p").text() + "</p></div>"), $("form.CommentsFormInner > textarea").focus()
}

function MoreAjax(e) {
    $("[data-more-click]").append('<div class="-newsLoaded"><div class="loader"><svg class="circular" viewBox="25 25 50 50"><circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/></svg></div></div>'), LoadingMore = !0;
    var t = HomeURL + "/AjaxCenter/Loadmore/" + e.data("loadmore") + "/";
    $.ajax({
        url: t,
        dataType: "json",
        success: function(t) {
            $(".-newsLoaded").remove(), 3 == LoadedCounter ? (e.attr("data-autoloaded", !1), e.data("autoloaded", !1), $('.PostsScrollLoader[data-more-click="' + e.data("uniqid") + '"]').show()) : 5 == LoadedCounter && (LoadedCounter = 1, e.attr("data-autoloaded", !0), e.data("autoloaded", !0), $('[data-more-click="' + e.data("uniqid") + '"]').hide()), $('[data-more-click="' + e.data("uniqid") + '"]').removeClass("isloader"), LoadingMore = !1, e.append(t.output), e.data("loadmore", t.arguments), null != t.end && 1 == t.end && (e.attr("data-autoloaded", !1), e.data("autoloaded", !1), e.attr("data-finish", !0), $('[data-more-click="' + e.data("uniqid") + '"]').hide())
        }
    })
}
$(window).scroll(ScrollListener), $("body").on("click", ".referance-title", (function() {
    $(".referance-Content").toggleClass("active"), $(".referance-Content").hasClass("active") ? $(".referance-title i:nth-child(3)").attr("class", "fa-solid fa-minus") : $(".referance-title i:nth-child(3)").attr("class", "fa-solid fa-plus")
})), $("body").on("click", "[data-open-faq]", (function() {
    var e, t, a;
    e = $(this).parent(), t = $(".answer"), a = $(".answer p").outerHeight(), a += 6, e.hasClass("active") || (t.attr("style", "--outheight:" + a + "px"), setTimeout((function() {
        e.addClass("active").siblings().removeClass("active")
    }), 100))
})), LoadingRelated = !1, LoadingMore = !1, Count = 0, LoadingRelated = !1, LoadingMore = !1, LoadedCounter = 1, $("body").on("click", "[data-more-click]", (function(e) {
    e.preventDefault();
    var t = $('.-ScrollerCenter[data-uniqid="' + $(this).data("more-click") + '"]');
    0 == LoadingMore && 0 == t.data("finish") && (LoadedCounter = LoadedCounter < 3 ? 4 : LoadedCounter + 1, $(this).addClass("isloader"), MoreAjax(t))
}));
var TabIsClick = !1,
    Timeout = !1;

function SubmitComment(e, t = !1) {
    if (0 != t && 13 == t.keyCode || 0 == t) {
        $(e).find(".alerts").html(""), $(e).find(".necessary").removeClass("necessary");
        var a = $(e).serialize(),
            r = $(e).serializeArray(),
            o = {};
        $.each(r, (function(e, t) {
            o[t.name] = t.value
        })), $(e).find(".alerts").html("");
        var s = !0;
        return null == o.user_name || null != o.user_name && "" == o.user_name ? 0 == Currentuser_Logged ? (s = !1, $(e).find('.-comments-form-inputs-area[data-comment-field="user_name"]').addClass("necessary")) : (a += "&user_name=" + Currentuser_display_name, o.user_name = Currentuser_display_name) : $(e).find('.-comments-form-inputs-area[data-comment-field="user_name"]').removeClass("necessary"), null == o.email || null != o.email && "" == o.email ? 0 == Currentuser_Logged ? (s = !1, $(e).find('.-comments-form-inputs-area[data-comment-field="email"]').addClass("necessary")) : (a += "&email=" + Currentuser_email, o.email = Currentuser_email) : $(e).find('.-comments-form-inputs-area[data-comment-field="email"]').removeClass("necessary"), null == o.comment || null != o.comment && "" == o.comment ? (s = !1, $(e).find('.-comments-form-inputs-area[data-comment-field="comment"]').addClass("necessary")) : $(e).find('.-comments-form-inputs-area[data-comment-field="comment"]').removeClass("necessary"), (null == $(e).data("id") || null != $(e).data("id") && "" == $(e).data("id")) && (s = !1), "" == $(e).find(".RateValue").val() && 0 == $(e).attr("data-parent") && (s = !1, $(e).find(".RateComment").addClass("necessary")), 0 == s ? $(e).find(".alerts").append('<div class="alert alert-danger">' + __loc("يرجي ملأ الحقول المطلوبة (*)") + "</div>") : (a += "&postID=" + $(e).data("id"), null != $(e).data("parent") && "" != $(e).data("parent") && (a += "&parent=" + $(e).data("parent")), $(".NoComments").remove(), $(e).addClass("loading"), $(e).append('<div class="loader"> <div class="line"></div> <div class="line"></div> <div class="line"></div> <div class="line"></div> </div>'), AjaxRequest({
            url: HomeURL + "/AjaxCenter/AddComment/",
            type: "POST",
            data: a,
            dataType: "json",
            success: function(t) {
                null == t.error && ($(e).removeClass("loading"), $(e).find(".loader").remove(), $(e).trigger("reset"), "0" == $(e).attr("data-parent") ? $(".CommentsListInner").prepend(t.output) : $("#comment-" + $(e).attr("data-parent")).after('<ul class="ChildComments">' + t.output + "</ul>"))
            }
        }), 0 == Currentuser_Logged && ($.cookie("user-data-email", o.email), $.cookie("user-data-name", o.name))), !1
    }
}

function ReplyComment(e) {
    var t = $(e).data("replay-arguments");
    t = atob(t), t = jQuery.parseJSON(t), $("form.CommentsFormInner").attr("data-parent", $(e).data("comment")), $(".ReplyCommentPreview").remove(), $("form.CommentsFormInner").prepend('<div class="ReplyCommentPreview"><h2><i class="fas fa-reply"></i><em>' + __loc("رد علي") + "</em> <span>" + t.UserName + "</span></h2><p>" + t.Comment_content + "</p></div>"), $("form.CommentsFormInner > textarea").focus(), setTimeout((function() {
        var e = $(".ReplyCommentPreview").offset().top;
        $("body,html").animate({
            scrollTop: e
        }, 500)
    }), 100)
}
$("body").on("click", "[data-tabs]", (function() {
        tabType = (e = $(this)).data("tabs"), a = e.data("type"), r = e.data("cats"), o = e.data("number"), $(this).data("uniq"), t = $(".post-model-" + a), e.addClass("active").siblings().removeClass("active");
        var e, t, a, r, o, s = HomeURL + "/AjaxCenter/RelatedPosts/";
        t.addClass("-loading"), TabIsClick = !0, clearTimeout(Timeout), Timeout = setTimeout((function() {
            AjaxRequest({
                url: s,
                dataType: "json",
                type: "POST",
                data: {
                    tabs: tabType,
                    type: a,
                    cats: r,
                    number: o
                },
                success: function(e) {
                    $(t).html(e.output), $(t).removeClass("-loading")
                }
            })
        }), 150)
    })), TabIsClick = !1, Timeout = !1, $("body").on("click", "[data-hometab]", (function() {
        tabType = (e = $(this)).data("hometab"), NowUniq = $(this).data("uniq"), term = e.data("term"), t = $('.-ScrollerCenter[data-uniqid="' + NowUniq + '"]'), e.addClass("active").siblings().removeClass("active");
        var e, t, a = HomeURL + "/AjaxCenter/singletabs/";
        tabType != TabIsClick && (t.addClass("-loading"), TabIsClick = !0, clearTimeout(Timeout), Timeout = setTimeout((function() {
            AjaxRequest({
                url: a,
                dataType: "json",
                type: "POST",
                data: {
                    hometab: tabType,
                    term: term
                },
                success: function(e) {
                    tabType = TabIsClick, $(t).html(e.output), $(t).removeClass("-loading")
                }
            })
        }), 150))
    })), $(window).on("scroll", (function() {
        $(window).scrollTop() >= $("header").outerHeight() ? ($("header").addClass("fixed"), $(".btn-phone").addClass("show"), $(".btn-whatsapp").addClass("show")) : ($("header").removeClass("fixed"), $(".btn-phone").removeClass("show"), $(".btn-whatsapp").removeClass("show"))
    })), $("body").on("click", ".menu_bar .fa-bars", (function(e) {
        $(".menu-nav").addClass("open"), $(".menu_bar").addClass("icon"), $(".search_header form").removeClass("active"), $("#openSEarch").removeClass("close")
    })), $("body").on("click", ".menu_bar .fa-xmark", (function(e) {
        $(".menu-nav").removeClass("open"), $(".menu_bar").removeClass("icon")
    })),
    function(e, t) {
        "use strict";
        var a = function(e) {
            this.elem = e
        };
        a.init = function() {
            var e, r = t.querySelectorAll("[data-sharer]"),
                o = r.length;
            for (e = 0; e < o; e++) r[e].addEventListener("click", a.add)
        }, a.add = function(e) {
            var t = e.currentTarget || e.srcElement;
            new a(t).share()
        }, a.prototype = {
            constructor: a,
            getValue: function(e) {
                var t = this.elem.getAttribute("data-" + e);
                return t && "hashtag" === e && !t.startsWith("#") && (t = "#" + t), null === t ? "" : t
            },
            share: function() {
                var e = this.getValue("sharer").toLowerCase(),
                    t = {
                        facebook: {
                            shareUrl: "https://www.facebook.com/sharer/sharer.php",
                            params: {
                                u: this.getValue("url"),
                                hashtag: this.getValue("hashtag"),
                                quote: this.getValue("quote")
                            }
                        },
                        linkedin: {
                            shareUrl: "https://www.linkedin.com/shareArticle",
                            params: {
                                url: this.getValue("url"),
                                mini: !0
                            }
                        },
                        twitter: {
                            shareUrl: "https://twitter.com/intent/tweet/",
                            params: {
                                text: this.getValue("title"),
                                url: this.getValue("url"),
                                hashtags: this.getValue("hashtags"),
                                via: this.getValue("via")
                            }
                        },
                        email: {
                            shareUrl: "mailto:" + this.getValue("to"),
                            params: {
                                subject: this.getValue("subject"),
                                body: this.getValue("title") + "\n" + this.getValue("url")
                            }
                        },
                        whatsapp: {
                            shareUrl: "true" === this.getValue("web") ? "https://web.whatsapp.com/send" : "https://wa.me/",
                            params: {
                                phone: this.getValue("to"),
                                text: this.getValue("title") + " " + this.getValue("url"),
                                hashtags: this.getValue("hashtags")
                            }
                        },
                        telegram: {
                            shareUrl: "https://t.me/share",
                            params: {
                                text: this.getValue("title"),
                                url: this.getValue("url")
                            }
                        },
                        viber: {
                            shareUrl: "viber://forward",
                            params: {
                                text: this.getValue("title") + " " + this.getValue("url")
                            }
                        },
                        line: {
                            shareUrl: "http://line.me/R/msg/text/?" + encodeURIComponent(this.getValue("title") + " " + this.getValue("url"))
                        },
                        pinterest: {
                            shareUrl: "https://www.pinterest.com/pin/create/button/",
                            params: {
                                url: this.getValue("url"),
                                media: this.getValue("image"),
                                description: this.getValue("description")
                            }
                        },
                        tumblr: {
                            shareUrl: "http://tumblr.com/widgets/share/tool",
                            params: {
                                canonicalUrl: this.getValue("url"),
                                content: this.getValue("url"),
                                posttype: "link",
                                title: this.getValue("title"),
                                caption: this.getValue("caption"),
                                tags: this.getValue("tags")
                            }
                        },
                        hackernews: {
                            shareUrl: "https://news.ycombinator.com/submitlink",
                            params: {
                                u: this.getValue("url"),
                                t: this.getValue("title")
                            }
                        },
                        reddit: {
                            shareUrl: "https://www.reddit.com/submit",
                            params: {
                                url: this.getValue("url"),
                                title: this.getValue("title")
                            }
                        },
                        vk: {
                            shareUrl: "http://vk.com/share.php",
                            params: {
                                url: this.getValue("url"),
                                title: this.getValue("title"),
                                description: this.getValue("caption"),
                                image: this.getValue("image")
                            }
                        },
                        xing: {
                            shareUrl: "https://www.xing.com/social/share/spi",
                            params: {
                                url: this.getValue("url")
                            }
                        },
                        buffer: {
                            shareUrl: "https://buffer.com/add",
                            params: {
                                url: this.getValue("url"),
                                title: this.getValue("title"),
                                via: this.getValue("via"),
                                picture: this.getValue("picture")
                            }
                        },
                        instapaper: {
                            shareUrl: "http://www.instapaper.com/edit",
                            params: {
                                url: this.getValue("url"),
                                title: this.getValue("title"),
                                description: this.getValue("description")
                            }
                        },
                        pocket: {
                            shareUrl: "https://getpocket.com/save",
                            params: {
                                url: this.getValue("url")
                            }
                        },
                        mashable: {
                            shareUrl: "https://mashable.com/submit",
                            params: {
                                url: this.getValue("url"),
                                title: this.getValue("title")
                            }
                        },
                        mix: {
                            shareUrl: "https://mix.com/add",
                            params: {
                                url: this.getValue("url")
                            }
                        },
                        flipboard: {
                            shareUrl: "https://share.flipboard.com/bookmarklet/popout",
                            params: {
                                v: 2,
                                title: this.getValue("title"),
                                url: this.getValue("url"),
                                t: Date.now()
                            }
                        },
                        weibo: {
                            shareUrl: "http://service.weibo.com/share/share.php",
                            params: {
                                url: this.getValue("url"),
                                title: this.getValue("title"),
                                pic: this.getValue("image"),
                                appkey: this.getValue("appkey"),
                                ralateUid: this.getValue("ralateuid"),
                                language: "zh_cn"
                            }
                        },
                        blogger: {
                            shareUrl: "https://www.blogger.com/blog-this.g",
                            params: {
                                u: this.getValue("url"),
                                n: this.getValue("title"),
                                t: this.getValue("description")
                            }
                        },
                        baidu: {
                            shareUrl: "http://cang.baidu.com/do/add",
                            params: {
                                it: this.getValue("title"),
                                iu: this.getValue("url")
                            }
                        },
                        douban: {
                            shareUrl: "https://www.douban.com/share/service",
                            params: {
                                name: this.getValue("name"),
                                href: this.getValue("url"),
                                image: this.getValue("image"),
                                comment: this.getValue("description")
                            }
                        },
                        okru: {
                            shareUrl: "https://connect.ok.ru/dk",
                            params: {
                                "st.cmd": "WidgetSharePreview",
                                "st.shareUrl": this.getValue("url"),
                                title: this.getValue("title")
                            }
                        },
                        mailru: {
                            shareUrl: "http://connect.mail.ru/share",
                            params: {
                                share_url: this.getValue("url"),
                                linkname: this.getValue("title"),
                                linknote: this.getValue("description"),
                                type: "page"
                            }
                        },
                        evernote: {
                            shareUrl: "https://www.evernote.com/clip.action",
                            params: {
                                url: this.getValue("url"),
                                title: this.getValue("title")
                            }
                        },
                        skype: {
                            shareUrl: "https://web.skype.com/share",
                            params: {
                                url: this.getValue("url"),
                                title: this.getValue("title")
                            }
                        },
                        delicious: {
                            shareUrl: "https://del.icio.us/post",
                            params: {
                                url: this.getValue("url"),
                                title: this.getValue("title")
                            }
                        },
                        sms: {
                            shareUrl: "sms://",
                            params: {
                                body: this.getValue("body")
                            }
                        },
                        trello: {
                            shareUrl: "https://trello.com/add-card",
                            params: {
                                url: this.getValue("url"),
                                name: this.getValue("title"),
                                desc: this.getValue("description"),
                                mode: "popup"
                            }
                        },
                        messenger: {
                            shareUrl: "fb-messenger://share",
                            params: {
                                link: this.getValue("url"),
                                title: this.getValue("title")
                            }
                        },
                        odnoklassniki: {
                            shareUrl: "https://connect.ok.ru/dk",
                            params: {
                                st: {
                                    cmd: "WidgetSharePreview",
                                    deprecated: 1,
                                    shareUrl: this.getValue("url")
                                }
                            }
                        },
                        meneame: {
                            shareUrl: "https://www.meneame.net/submit",
                            params: {
                                url: this.getValue("url")
                            }
                        },
                        diaspora: {
                            shareUrl: "https://share.diasporafoundation.org",
                            params: {
                                title: this.getValue("title"),
                                url: this.getValue("url")
                            }
                        },
                        googlebookmarks: {
                            shareUrl: "https://www.google.com/bookmarks/mark",
                            params: {
                                op: "edit",
                                bkmk: this.getValue("url"),
                                title: this.getValue("title")
                            }
                        },
                        qzone: {
                            shareUrl: "https://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzshare_onekey",
                            params: {
                                url: this.getValue("url")
                            }
                        },
                        refind: {
                            shareUrl: "https://refind.com",
                            params: {
                                url: this.getValue("url")
                            }
                        },
                        surfingbird: {
                            shareUrl: "https://surfingbird.ru/share",
                            params: {
                                url: this.getValue("url"),
                                title: this.getValue("title"),
                                description: this.getValue("description")
                            }
                        },
                        yahoomail: {
                            shareUrl: "http://compose.mail.yahoo.com",
                            params: {
                                to: this.getValue("to"),
                                subject: this.getValue("subject"),
                                body: this.getValue("body")
                            }
                        },
                        wordpress: {
                            shareUrl: "https://wordpress.com/wp-admin/press-this.php",
                            params: {
                                u: this.getValue("url"),
                                t: this.getValue("title"),
                                s: this.getValue("title")
                            }
                        },
                        amazon: {
                            shareUrl: "https://www.amazon.com/gp/wishlist/static-add",
                            params: {
                                u: this.getValue("url"),
                                t: this.getValue("title")
                            }
                        },
                        pinboard: {
                            shareUrl: "https://pinboard.in/add",
                            params: {
                                url: this.getValue("url"),
                                title: this.getValue("title"),
                                description: this.getValue("description")
                            }
                        },
                        threema: {
                            shareUrl: "threema://compose",
                            params: {
                                text: this.getValue("text"),
                                id: this.getValue("id")
                            }
                        },
                        kakaostory: {
                            shareUrl: "https://story.kakao.com/share",
                            params: {
                                url: this.getValue("url")
                            }
                        },
                        yummly: {
                            shareUrl: "http://www.yummly.com/urb/verify",
                            params: {
                                url: this.getValue("url"),
                                title: this.getValue("title"),
                                yumtype: "button"
                            }
                        }
                    } [e];
                return t && (t.width = this.getValue("width"), t.height = this.getValue("height")), void 0 !== t && this.urlSharer(t)
            },
            urlSharer: function(t) {
                var a, r = t.params || {},
                    o = Object.keys(r),
                    s = o.length > 0 ? "?" : "";
                for (a = 0; a < o.length; a++) "?" !== s && (s += "&"), r[o[a]] && (s += o[a] + "=" + encodeURIComponent(r[o[a]]));
                t.shareUrl += s;
                var i = "true" === this.getValue("link"),
                    n = "true" === this.getValue("blank");
                if (i) n ? e.open(t.shareUrl, "_blank") : e.location.href = t.shareUrl;
                else {
                    console.log(t.shareUrl);
                    var l = t.width || 600,
                        d = t.height || 480,
                        u = e.innerWidth / 2 - l / 2 + e.screenX,
                        c = e.innerHeight / 2 - d / 2 + e.screenY,
                        m = e.open(t.shareUrl, "", "scrollbars=no, width=" + l + ", height=" + d + ", top=" + c + ", left=" + u);
                    e.focus && m.focus()
                }
            }
        }, "complete" === t.readyState || "loading" !== t.readyState ? a.init() : t.addEventListener("DOMContentLoaded", a.init), e.Sharer = a
    }(window, document), $("body").on("click", "[data-sharer] a", (function(e) {
        e.preventDefault()
    })), $("body").on("click", ".ez-toc-title-container", (function() {
        $("ul.ez-toc-list").toggleClass("open")
    })), $("body").on("click", ".album-holder .close", (function() {
        $(".album-holder").removeClass("open")
    })), $("body").on("click", "post--albums .img", (function() {
        $(".album-holder").addClass("open")
    })), $("body").on("click", "span#Close", (function() {
        $("[data-close]").remove(), $(".form-contact form").removeClass("opctiy")
    })), $(".form-contact form").submit((function() {
        return DataJSON = new FormData(this), $.ajax({
            url: HomeURL + "/AjaxCenter/sendinfo",
            type: "POST",
            contentType: !1,
            processData: !1,
            cache: !1,
            processData: !1,
            data: DataJSON,
            success: function(e) {
                $(".form-contact").append(e.output), $(".form-contact form input").val(""), $(".form-contact form textarea").val(""), $(".form-contact form").addClass("opctiy")
            }
        }), !1
    })), $("body").on("click", ".bottun", (function(e) {
        $(this).closest(".catArticleDetails").find(".ArticleDetails.details").toggleClass("height").siblings(), $(this).toggleClass("transform")
    })), $("body").on("mouseover", (function(e) {
        $(".RatingReview > i").each((function(t, a) {
            $(a).is(e.target) || 0 !== $(a).has(e.target).length || $(a).nextAll().hasClass("active") && ($(a).parent().is(e.target) || 0 !== $(a).parent().has(e.target).length) || $(a).removeClass("active")
        }))
    })), $("body").on("mouseover", ".RatingReview > i", (function(e) {
        $(this).prevAll().addClass("active"), $(this).addClass("active")
    })), $("body").on("click", ".RatingReview:not(.justView) > i", (function() {
        var e = $(this);
        if ($(".RatingReview > i").removeClass("fixedactive active"), e.prevAll().addClass("fixedactive"), e.addClass("fixedactive"), null != e.closest(".RatingReview").data("save-id")) {
            var t = e.closest(".RatingReview").data("save-id"),
                a = e.closest(".RatingReview").data("save-type"),
                r = e.data("rate"),
                o = $('.-rating-value[data-post-id="' + t + '"]'),
                s = $.cookie("RateV1-" + a + "-" + t);
            AjaxRequest({
                url: HomeURL + "/AjaxCenter/RateAjax/",
                dataType: "json",
                type: "POST",
                data: {
                    id: t,
                    Type: a,
                    LastValueRate: s,
                    RateValue: r
                },
                success: function(e) {
                    o.html(e.TotalValue), $(".-single-bottom-list-Rate").length > 0 && $(".-single-bottom-list-Rate").show(), $(".Rate-New-Mixers").length > 0 && $(".Rate-New-Mixers").show(), $('.-Js-Rate-AverageItems[data-post-id="' + t + '"]').length > 0 && null != e.output && $('.-Js-Rate-AverageItems[data-post-id="' + t + '"]').html(e.output), $('.-rating-suptitle[data-post-id="' + t + '"]').length > 0 && null != e.RateUserCount_v1 && $('.-rating-suptitle[data-post-id="' + t + '"] em').html(e.RateUserCount_v1), $('.-YC-Review-Change[data-review-change="' + t + '"]').length > 0 && $('.-YC-Review-Change[data-review-change="' + t + '"] > i').each((function(t, a) {
                        $(a).data("rate") <= e.TotalValue ? $(a).addClass("fixedactive") : $(a).removeClass("fixedactive")
                    }))
                }
            }), $.cookie("RateV1-" + a + "-" + t, r)
        } else $(this).closest(".RateComment").find(".product-item-info-stats-ratings > p > .-rating-value").text($(this).data("rate") + ".0"), $(this).closest(".RateComment").find("input").val($(this).data("rate"))
    })), $("body").on("click", ".closepopup", (function() {
        $(this), $(".popup-call").remove()
    })), $(window).on("scroll", (function() {
        $(window).scrollTop() >= 800 ? $(".popup-call").addClass("show") : $(".popup-call").removeClass("show")
    })), $("body").on("click", ".CommentsList>.CommentsList__Title", (function() {
        $(this).toggleClass("clickcomment"), $("ul.CommentsListInner").toggleClass("openComment")
    }));
var isButtonClicked = !1;
var isButtonClicked = false;



function LazyloaderHook() {
    $("[data-loader-src]").each((function(e, t) {
        $(t).attr("src", $(t).attr("data-loader-src")).removeAttr("data-loader-src"), $(t).parent().find(".-YC-Loader-Cover").remove()
    })), $("[data-loader-srcset]").each((function(e, t) {
        $(t).attr("srcset", $(t).attr("data-loader-srcset")).removeAttr("data-loader-srcset"), $(t).parent().find(".-YC-Loader-Cover").remove()
    })), $("[data-loader-style]").each((function(e, t) {
        $(t).attr("style", $(t).attr("data-loader-style")).removeAttr("data-loader-style"), $(t).parent().find(".-YC-Loader-Cover").remove()
    })), $("[data-loader-href]").each((function(e, t) {
        $(t).attr("href", $(t).attr("data-loader-href")).removeAttr("data-loader-href"), $(t).parent().find(".-YC-Loader-Cover").remove()
    })), $("[data-loader-style], [data-loader-src], [data-loader-srcset], [data-loader-href]").fadeIn(0)
}
0 == IsSpeed && LazyloaderHook();




// ============================================================
// تسجيل المكالمات : اتصال هاتفي أو واتساب
// معالج واحد يغطي كل أزرار الاتصال في القالب و يرسل نوع الاتصال
// ============================================================
var YCCallSelectors = [
    ".btn-phone",
    ".btn-whatsapp",
    ".ads-phone-box",
    ".ads-whatsapp-box",
    "[data-call]",
    'a[href^="tel:"]',
    'a[href*="wa.me"]',
    'a[href*="api.whatsapp.com"]',
    'a[href*="web.whatsapp.com"]'
].join(", ");

$("body").on("click", YCCallSelectors, function(e) {
    // الضغطة الواحدة قد تمر على أكثر من عنصر مطابق ( الرابط ثم الصندوق الحاوي )
    // لذلك نسجل المكالمة مرة واحدة فقط لكل ضغطة
    if (e.YCCallLogged) {
        return;
    }
    e.YCCallLogged = true;

    var $el = $(this);
    var href = $el.attr("href") || "";
    var declared = String($el.attr("data-call") || "").toLowerCase();
    var callType;

    if (declared === "whatsapp" || $el.is(".btn-whatsapp, .ads-whatsapp-box") || $el.closest(".btn-whatsapp, .ads-whatsapp-box").length || /wa\.me|whatsapp/i.test(href)) {
        callType = "whatsapp";
    } else if (declared === "phone" || $el.is(".btn-phone, .ads-phone-box") || $el.closest(".btn-phone, .ads-phone-box").length || /^tel:/i.test(href)) {
        callType = "phone";
    } else {
        // عنصر غير معروف النوع - لا نسجل شيئا
        return;
    }

    $.ajax({
        url: HomeURL + "/AjaxCenter/callupdate",
        type: "POST",
        data: {
            page: document.title,
            calltype: callType
        },
        success: function(response) {
            console.log(response.output);
        },
        error: function(xhr, status, error) {
            console.error("Error: " + error);
        }
    });
});

// معرض الصور - تبديل التصنيفات
document.addEventListener("DOMContentLoaded", function() {
    var titles = document.querySelectorAll(".category-title");
    titles.forEach(function(title) {
        title.addEventListener("click", function() {
            titles.forEach(function(item) {
                item.classList.remove("active");
            });
            document.querySelectorAll(".photo-gallery").forEach(function(gallery) {
                gallery.style.display = "none";
            });
            this.classList.add("active");
            var section = this.closest(".category-section");
            var gallery = section && section.querySelector(".photo-gallery");
            if (gallery) {
                gallery.style.display = "flex";
            }
        });
    });
});
