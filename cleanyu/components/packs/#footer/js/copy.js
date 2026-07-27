jQuery.event.special.touchstart={setup:function(e,t,a){this.addEventListener("touchstart",a,{passive:!t.includes("noPreventDefault")})}};
;(function($) {

    var b64 = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/",
        a256 = '',
        r64 = [256],
        r256 = [256],
        i = 0;

    var UTF8 = {

        /**
         * Encode multi-byte Unicode string into utf-8 multiple single-byte characters
         * (BMP / basic multilingual plane only)
         *
         * Chars in range U+0080 - U+07FF are encoded in 2 chars, U+0800 - U+FFFF in 3 chars
         *
         * @param {String} strUni Unicode string to be encoded as UTF-8
         * @returns {String} encoded string
         */
        encode: function(strUni) {
            // use regular expressions & String.replace callback function for better efficiency
            // than procedural approaches
            var strUtf = strUni.replace(/[\u0080-\u07ff]/g, // U+0080 - U+07FF => 2 bytes 110yyyyy, 10zzzzzz
            function(c) {
                var cc = c.charCodeAt(0);
                return String.fromCharCode(0xc0 | cc >> 6, 0x80 | cc & 0x3f);
            })
            .replace(/[\u0800-\uffff]/g, // U+0800 - U+FFFF => 3 bytes 1110xxxx, 10yyyyyy, 10zzzzzz
            function(c) {
                var cc = c.charCodeAt(0);
                return String.fromCharCode(0xe0 | cc >> 12, 0x80 | cc >> 6 & 0x3F, 0x80 | cc & 0x3f);
            });
            return strUtf;
        },

        /**
         * Decode utf-8 encoded string back into multi-byte Unicode characters
         *
         * @param {String} strUtf UTF-8 string to be decoded back to Unicode
         * @returns {String} decoded string
         */
        decode: function(strUtf) {
            // note: decode 3-byte chars first as decoded 2-byte strings could appear to be 3-byte char!
            var strUni = strUtf.replace(/[\u00e0-\u00ef][\u0080-\u00bf][\u0080-\u00bf]/g, // 3-byte chars
            function(c) { // (note parentheses for precence)
                var cc = ((c.charCodeAt(0) & 0x0f) << 12) | ((c.charCodeAt(1) & 0x3f) << 6) | (c.charCodeAt(2) & 0x3f);
                return String.fromCharCode(cc);
            })
            .replace(/[\u00c0-\u00df][\u0080-\u00bf]/g, // 2-byte chars
            function(c) { // (note parentheses for precence)
                var cc = (c.charCodeAt(0) & 0x1f) << 6 | c.charCodeAt(1) & 0x3f;
                return String.fromCharCode(cc);
            });
            return strUni;
        }
    };

    while(i < 256) {
      var c = String.fromCharCode(i);
      a256 += c;
      r256[i] = i;
      r64[i] = b64.indexOf(c);
      ++i;
    }

    function code(s, discard, alpha, beta, w1, w2) {
        s = String(s);
        var buffer = 0,
            i = 0,
            length = s.length,
            result = '',
            bitsInBuffer = 0;

        while(i < length) {
            var c = s.charCodeAt(i);
            c = c < 256 ? alpha[c] : -1;

            buffer = (buffer << w1) + c;
            bitsInBuffer += w1;

            while(bitsInBuffer >= w2) {
                bitsInBuffer -= w2;
                var tmp = buffer >> bitsInBuffer;
                result += beta.charAt(tmp);
                buffer ^= tmp << bitsInBuffer;
            }
            ++i;
        }
        if(!discard && bitsInBuffer > 0) result += beta.charAt(buffer << (w2 - bitsInBuffer));
        return result;
    }

    var Plugin = $.base64 = function(dir, input, encode) {
            return input ? Plugin[dir](input, encode) : dir ? null : this;
        };

    Plugin.btoa = Plugin.encode = function(plain, utf8encode) {
        plain = Plugin.raw === false || Plugin.utf8encode || utf8encode ? UTF8.encode(plain) : plain;
        plain = code(plain, false, r256, b64, 8, 6);
        return plain + '===='.slice((plain.length % 4) || 4);
    };

    Plugin.atob = Plugin.decode = function(coded, utf8decode) {
        coded = coded.replace(/[^A-Za-z0-9\+\/\=]/g, "");
        coded = String(coded).split('=');
        var i = coded.length;
        do {--i;
            coded[i] = code(coded[i], true, r64, a256, 6, 8);
        } while (i > 0);
        coded = coded.join('');
        return Plugin.raw === false || Plugin.utf8decode || utf8decode ? UTF8.decode(coded) : coded;
    };
}(jQuery));
$.base64.utf8encode = true;

// # Cookies Integration
  (function (factory) {
        if (typeof define === 'function' && define.amd) {
            // AMD (Register as an anonymous module)
            define(['jquery'], factory);
        } else if (typeof exports === 'object') {
            // Node/CommonJS
            module.exports = factory(require('jquery'));
        } else {
            // Browser globals
            factory(jQuery);
        }
    }(function ($) {
        var pluses = /\+/g;
        function encode(s) {
            return config.raw ? s : encodeURIComponent(s);
        }
        function decode(s) {
            return config.raw ? s : decodeURIComponent(s);
        }
        function stringifyCookieValue(value) {
            return encode(config.json ? JSON.stringify(value) : String(value));
        }
        function parseCookieValue(s) {
            if (s.indexOf('"') === 0) {
                // This is a quoted cookie as according to RFC2068, unescape...
                s = s.slice(1, -1).replace(/\\"/g, '"').replace(/\\\\/g, '\\');
            }
            try {
                // Replace server-side written pluses with spaces.
                // If we can't decode the cookie, ignore it, it's unusable.
                // If we can't parse the cookie, ignore it, it's unusable.
                s = decodeURIComponent(s.replace(pluses, ' '));
                return config.json ? JSON.parse(s) : s;
            } catch(e) {}
        }
        function read(s, converter) {
            var value = config.raw ? s : parseCookieValue(s);
            return $.isFunction(converter) ? converter(value) : value;
        }
        var config = $.cookie = function (key, value, options) {
            // Write
            if (arguments.length > 1 && !$.isFunction(value)) {
                options = $.extend({}, config.defaults, options);
                if (typeof options.expires === 'number') {
                    var days = options.expires, t = options.expires = new Date();
                    t.setMilliseconds(t.getMilliseconds() + days * 864e+5);
                }
                return (document.cookie = [
                    encode(key), '=', stringifyCookieValue(value),
                    options.expires ? '; expires=' + options.expires.toUTCString() : '', // use expires attribute, max-age is not supported by IE
                    options.path    ? '; path=' + options.path : '',
                    options.domain  ? '; domain=' + options.domain : '',
                    options.secure  ? '; secure' : ''
                ].join(''));
            }
            // Read
            var result = key ? undefined : {},
                // To prevent the for loop in the first place assign an empty array
                // in case there are no cookies at all. Also prevents odd result when
                // calling $.cookie().
                cookies = document.cookie ? document.cookie.split('; ') : [],
                i = 0,
                l = cookies.length;
            for (; i < l; i++) {
                var parts = cookies[i].split('='),
                    name = decode(parts.shift()),
                    cookie = parts.join('=');
                if (key === name) {
                    // If second argument (value) is a function it's a converter...
                    result = read(cookie, value);
                    break;
                }
                // Prevent storing a cookie that we couldn't decode.
                if (!key && (cookie = read(cookie)) !== undefined) {
                    result[name] = cookie;
                }
            }
            return result;
        };
        config.defaults = {};
        $.removeCookie = function (key, options) {
            // Must not alter options, thus extending a fresh object...
            $.cookie(key, '', $.extend({}, options, { expires: -1 }));
            return !$.cookie(key);
        };
    }));

// # Page Speed Boost
   var CookiedAjax = [];
   function ensureCssFileInclusion(cssFileToCheck) {
       var styleSheets = document.styleSheets;
       for (var i = 0, max = styleSheets.length; i < max; i++) {
           if (styleSheets[i].href == cssFileToCheck) {
               return;
           }
       }
       // because no matching stylesheets were found, we will add a new HTML link element to the HEAD section of the page.
       var link = document.createElement("link");
       link.rel = "stylesheet";
       link.href = cssFileToCheck;
       document.getElementsByTagName("head")[0].appendChild(link);
   }
   // # 
  
   $('body').on("click",'.ez-toc-title',function(){
      $(this).parent().toggleClass('-opentable-');
   });
   $('body').on("click", '#openSEarch i.far.fa-search', function(){
      $('.search_header form').addClass('active');
      $('#openSEarch').addClass('close');
      $('.menu-nav').removeClass('open');
      $(".menu_bar").removeClass("icon");
     
   });
   $('body').on("click", '#openSEarch .fa-xmark', function(){
      $('.search_header form').removeClass('active');
      $('#openSEarch').removeClass('close');
   });

// #  children
   if( ISMobile == true ) {
       $('body').on("click",'.menu-item-has-children>a',function(a){  
          a.preventDefault();
          a.stopPropagation();
           $(this).parent().find('>i').toggleClass('trans');
           $('.menu-item-has-children').toggleClass('hover')
             $(this).parent().find("> ul.sub-menu").toggleClass("active"), $(this).not($(this).find("> ul.sub-menu"));
    
          
       });
   }
// # Ajax Handler
   var AjaxHandlerXHR = false;
   var AjaxHandlerLastData = false;
   var RetryInterval;
   function AjaxRequest(data) {
      if( AjaxHandlerXHR != false && AjaxHandlerLastData.url == data.url ) {
         AjaxHandlerXHR.abort();
      }
      data.error = function (jqXHR, exception) {
         var msg = '';
         if (jqXHR.status == 404) {
            msg = 'لم يتم العثور على الصفحة.';
            AjaxHandlerXHR = false;
         } else if (jqXHR.status == 500) {
            msg = 'حدث خطأ اثناء معاينة الملف.';
            AjaxHandlerXHR = false;
         } else if (exception === 'timeout') {
            msg = 'إنتهت مهلة الإتصال.';
            AjaxHandlerXHR = false;
         }
         if( msg != '' && msg != undefined ) {
            RetryInterval = setInterval(AjaxRequest(data), 5000);
         }
      }
      AjaxHandlerLastData = data;
      AjaxHandlerXHR = $.ajax(data).done(function(){
         clearInterval(RetryInterval);
         AjaxHandlerXHR = false;
         InitializeTrig();
         LazyloaderHook();
      });
      return true;
   }

// # Ajaxify
   var LoadedPages = [];
   function ChangeTitle(title) {
      $(document).prop("title", title);
   }
   function ChangeURL(url) {
      url = url.replace('/?ajax=1', '');
      if(url!=window.location){
         window.history.pushState({path:url},'',url);
       }
   }
   window.onpopstate = function(e) {
      url = location.href;
      BackToURL(url);
      e.preventDefault();
   }
   function outerHTML(node){
       return node.outerHTML || new XMLSerializer().serializeToString(node);
   }
   function BackToURL(url) {
      var owl, elements;
      if( LoadedPages[url] != undefined ) {
         $('root').after(LoadedPages[url].output).remove();
         $('body, html').animate({"scrollTop":LoadedPages[url].scroll}, 0);
         LoadedPages[location.href] = {
            "title":$(document).prop('title'),
            "output":outerHTML(document.querySelector('root')),
            "scroll":$(document).scrollTop() || $(window).scrollTop(),
         };
         //
         ChangeTitle(LoadedPages[url].title);
         ChangeURL(url);
         InitializeTrig();
         $(DeletedPostsItems).each(function(k, v){
            $('.PostItem[cpd="'+v+'"]').remove();
         });
      }else {
         $('#BackPopstateURL').remove();
         $('root').append('<a href="'+url+'" id="BackPopstateURL" style="display:none;">eee</a>');
         //
         $('#BackPopstateURL').trigger("click");
      }
   }
   var mouseY, mouseX;
   $(window).on("mousemove", function(e) {
       mouseX = e.pageX;
       mouseY = e.pageY;
   })
   function AjaxNavigate(url,s='page') {
      if( AjaxHandlerXHR != false ) {
         AjaxHandlerXHR.abort();
      }
      Count = 0;
      LoadingRelated = false;
      Loadingsingle = false;
      LoadingMore = false;

      if( sharepopover != false ) {
         sharepopover.trigger('destroy.owl.carousel').css("opacity", '0');
      }
      //
      LoadedPages[location.href] = {
         "title":$(document).prop('title'),
         "output":outerHTML(document.querySelector('root')),
         "scroll":$(document).scrollTop() || $(window).scrollTop(),
      };
      $('.--news-loader').remove();
      mouseY = mouseY - $(window).scrollTop()   ;
      $('body').append('<div style="left:'+(mouseX - 35)+'px;top:'+(mouseY - 35)+'px;" class="--news-loader"><svg version="1.1" id="loader-1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="30px" height="30px" viewBox="0 0 50 50" style="enable-background:new 0 0 50 50;" xml:space="preserve"> <path fill="#005fa3" d="M43.935,25.145c0-10.318-8.364-18.683-18.683-18.683c-10.318,0-18.683,8.365-18.683,18.683h4.068c0-8.071,6.543-14.615,14.615-14.615c8.072,0,14.615,6.543,14.615,14.615H43.935z"> <animateTransform attributeType="xml" attributeName="transform" type="rotate" from="0 25 25" to="360 25 25" dur="0.6s" repeatCount="indefinite"></animateTransform> </path> </svg></div>');
         var AjaxURL = url;
         AjaxRequest({
            url: AjaxURL,
            type: "GET",
            data: {"ajax":true},
            dataType: 'json',
            success: function(msg) {
               $('.--news-loader').remove();
               $('root').after(msg.output).remove();
               //
               var ScrolltoElem = false;
               if( url.indexOf('#') > -1 ) {
                  var Element = url.split('#')[1];
                  ScrolltoElem = Element;
               }
               if( ScrolltoElem == false ) {
                  $('body, html').animate({"scrollTop":0}, 0);
               }else {
                  $('body, html').animate({"scrollTop":$('#'+ScrolltoElem).offset().top}, 0);
               }
               ChangeURL(url);
               ChangeTitle(msg.title);
               InitializeTrig();

            },
         });
   }


// ## UniqID ## //
  var charstoformid = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz'.split('');
  var UniqID = function() {
    var idlength = 10;
      var uniqid = '';
      for (var i = 0; i < idlength; i++) {
        uniqid += charstoformid[Math.floor(Math.random() * charstoformid.length)];
      }
      return uniqid;
  }

// # THEME CONSTRACT FUNCTIONS
  function isEmpty(value){
    return !$.trim(value);
  }

  function stripslashes (str) {
    return (str + '').replace(/\\(.?)/g, function (s,n1) {
      switch (n1) {
        case '\\':
          return '\\';
        case '0':
          return '\u0000';
        case '':
          return '';
        default:
          return n1;
      }
    });
  }

  function strip_tags(str, allow){
    allow = (((allow || '') + '').toLowerCase().match(/<[a-z][a-z0-9]*>/g) || []).join('');

    var tags = /<\/?([a-z][a-z0-9]*)\b[^>]*>/gi;
    var commentsAndPhpTags = /<!--[\s\S]*?-->|<\?(?:php)?[\s\S]*?\?>/gi;
    return str.replace(commentsAndPhpTags, '').replace(tags, function ($0, $1) {
      return allow.indexOf('<' + $1.toLowerCase() + '>') > -1 ? $0 :'';
    });
  }
    
    function loadImage(img, url) {
  return new Promise(function(resolve, reject) {
    img.onload = resolve;
    img.onerror = reject;
    img.setAttribute("src", url);
  });
}

   // # LazyloaderHook
    function LazyloaderHook() {
        $("[data-loader-src]").each(function(els, el){
            $(el).attr("src", $(el).attr("data-loader-src")).removeAttr("data-loader-src");
            $(el).parent().find('.-YC-Loader-Cover').remove();
        });
        $("[data-loader-srcset]").each(function(els, el){
            $(el).attr("srcset", $(el).attr("data-loader-srcset")).removeAttr("data-loader-srcset");
            $(el).parent().find('.-YC-Loader-Cover').remove();
        });
        $("[data-loader-style]").each(function(els, el){
            $(el).attr("style", $(el).attr("data-loader-style")).removeAttr("data-loader-style");
            $(el).parent().find('.-YC-Loader-Cover').remove();
        });
        $("[data-loader-href]").each(function(els, el){
            $(el).attr("href", $(el).attr("data-loader-href")).removeAttr("data-loader-href");
            $(el).parent().find('.-YC-Loader-Cover').remove();
        });
        $("[data-loader-style], [data-loader-src], [data-loader-srcset], [data-loader-href]").fadeIn(0);
    }

    if( IsSpeed == false ){
      LazyloaderHook();
    }




   var IPInfo = false;
   var SidebarTerms = false, CurrenciesSlider = false, Scrollingslider = false, sharepopover = false, articlesmodel3 = false, matchesbar = false;
   function InitializeTrig(){
      
     
      var WindowWidth = $(window).width();
      // # Auto Focus
         if( $('[autofocus]').val() == '' ) {
            $('[autofocus]').focus();
         }
         $(window).scroll(ScrollListener);
         ScrollListener();
         
      // # Owl Carousel
        // # ads-widght
        if( $('.album-slider').length > 0 && $('.album-slider.owl-loaded').length == 0 ) {
            $('.album-slider').not('.-owl-loaded').each(function(els, el){
              sharepopover = $(el).owlCarousel({
              margin:15,
                rtl: true,
                loop: false,
                center: false,
                nav: true,
                dots:false,
                navSpeed:300,
                navText: ['<i class="fa fa-angle-left"></i>', '<i class="fa fa-angle-right"></i>'],
                autoplay: false,
                responsiveClass:true,
                items:1,
            }).css("opacity", '1');
            });
        }   
        if( $('.works-single-img-').length > 0 && $('.works-single-img-.owl-loaded').length == 0 ) {
            $('.works-single-img-').not('.-owl-loaded').each(function(els, el){
              sharepopover = $(el).owlCarousel({
              margin:15,
                rtl: true,
                loop: true,
                center: false,
                nav: true,
                dots:false,
                navSpeed:1000,
                navText: ['<i class="fa fa-angle-left"></i>', '<i class="fa fa-angle-right"></i>'],
                autoplay:true,
                autoplayTimeout:5000,
                autoplayHoverPause:false,
                responsiveClass:true,
                items:1,
            }).css("opacity", '1');
            });
        }
        
        if( $('.intro-yu').length > 0 && $('.intro-yu.owl-loaded').length == 0 ) {
            $('.intro-yu').not('.-owl-loaded').each(function(els, el){
              sharepopover = $(el).owlCarousel({
              margin:15,
                    rtl: true,
                    loop: false,
                    center: false,
                    nav: true,
                    dots:true,
                    navSpeed:1000,
                    autoplayTimeout: 5000,
                    autoplayHoverPause: true,
                    navText: ['<i class="fa fa-angle-left"></i>', '<i class="fa fa-angle-right"></i>'],
                    autoplay: false,
                    responsiveClass:true,
                    items:1,
                }).css("opacity", '1');
            });
        }
        if( $('.imagecover-').length > 0 && $('.imagecover-.owl-loaded').length == 0 ) {
            $('.imagecover-').not('.-owl-loaded').each(function(els, el){
              sharepopover = $(el).owlCarousel({
              margin:15,
                    rtl: true,
                    loop: false,
                    center: false,
                    nav: true,
                    dots:true,
                    navSpeed:1000,
                    autoplayTimeout: 5000,
                    autoplayHoverPause: true,
                    navText: ['<i class="fa fa-angle-left"></i>', '<i class="fa fa-angle-right"></i>'],
                    autoplay: false,
                    responsiveClass:true,
                   
                     items: 1,
                }).css("opacity", '1');
            });
        }
      
   }
   InitializeTrig();
// # Scroll Listener
   var Count = 0;
   var LoadingRelated = false;
   var LoadingMore = false;
   var LoadedCounter = 1;
   //
   

   var SingleCounterMore = 0;
   var SingleScrollAllowed = false;

   function ScrollListener() {
      
      if ($("[data-loadmore]").length > 0 && $(window).scrollTop() + $(window).height() > ($('[data-loadmore]').offset().top + $('[data-loadmore]').height()) - 600) {
         if(LoadingMore == false && $("[data-loadmore]").data('autoloaded') != false && LoadedCounter < 3 && $('[data-loadmore]').data('finish') == false){
            LoadedCounter = LoadedCounter + 1;
            MoreAjax($("[data-loadmore]"));
         }
      }
      
   
   
      //
      
   }
   $(window).scroll(ScrollListener);

  
// # Comments
   function __loc(e) {
      return e
   }
   function SubmitComment(form) {
      var alerts = 0;
      $(form).find('.alerts').html('');
      $(form).find('.necessary').removeClass('necessary');
      if( Currentuser_Logged == true ) {
         var Data = {
            "action":'AddComment',
            "type":'عضو',
            "yourname":Currentuser_display_name,
            "email":Currentuser_email,
            "message":$(form).find('textarea').val(),
            "rate":$(form).find('.RateValue').val(),
            "postID":$(form).data('id'),
            "parent":$(form).attr('data-parent'),
         };
         if( $(form).find('textarea').val() == '' ) {
            alerts++;
            $(form).find('textarea').addClass('necessary');
         }
         if( $(form).find('.RateValue').val() == '' ) {
            alerts++;
            $(form).find('.RateComment').addClass('necessary');
         }
      }else {
         var Data = {
            "action":'AddComment',
            "type":'عضو',
            "yourname":$(form).find('[name="yourname"]').val(),
            "email":$(form).find('[name="email"]').val(),
            "message":$(form).find('textarea').val(),
            "rate":$(form).find('.RateValue').val(),
            "postID":$(form).data('id'),
            "parent":$(form).attr('data-parent'),
         };
         if( $(form).find('textarea').val() == '' ) {
            alerts++;
            $(form).find('textarea').addClass('necessary');
         }
         if( $(form).find('.RateValue').val() == '' ) {
            alerts++;
            $(form).find('.RateComment').addClass('necessary');
         }
         if( $(form).find('[name="yourname"]').val() == '' ) {
            alerts++;
            $(form).find('[name="yourname"]').addClass('necessary');
         }
         if( $(form).find('[name="email"]').val() == '' ) {
            alerts++;
            $(form).find('[name="email"]').addClass('necessary');
         }
      }
      if( alerts > 0 ) {
         $(form).find('.alerts').append('<div class="alert alert-danger">يرجي ملأ الحقول المطلوبة (*)</div>');
      }else {
         $('.NoComments').remove();
         $(form).addClass('loading');
         $(form).append('<div class="loader"><div class="line"></div> <div class="line"></div> <div class="line"></div> <div class="line"></div> </div>');
         AjaxRequest({
            url: HomeURL+'/AjaxCenter/AddComment/',
            type:'POST',
            data:Data,
            dataType: 'json',
            success: function(msg) {
               if( msg.error == undefined ) {
                  $(form).removeClass('loading');
                  $(form).find('.loader').remove();
                  $(form).trigger("reset");
                  $(form).find('.RatingReview > i').removeClass('fixedactive active');
                  $(form).find('.RateValue').val('');
                  if( $(form).attr('data-parent') == '0' ) {
                     if( $('.CommentsListInner').closest('under-post-comments').length > 0 ) {
                        $('.CommentsListInner').prepend(msg.output);
                     }else {
                        $('.CommentsListInner').append(msg.output);
                     }
                  }else {
                     $('#comment-'+$(form).attr('data-parent')).after('<ul class="ChildComments">'+msg.output+'</ul>');
                  }
               }
            }
         });
      }
   }
   function ReplyComment(e) {
      $("form.CommentsFormInner").attr("data-parent", $(e).data("comment")), $(".ReplyCommentPreview").remove(), $("form.CommentsFormInner").prepend('<div class="ReplyCommentPreview"><h2><i class="fas fa-reply"></i><em>' + __loc("رد علي") + "</em> <span>" + $(e).parent().find(".NameArea").text() + "</span></h2><p>" + $(e).parent().find(".CommentContent > p").text() + "</p></div>"), $("form.CommentsFormInner > textarea").focus()
   }
// # Post references
            
    $('body').on('click','.referance-title',function(){
        $('.referance-Content').toggleClass('active');
        if($('.referance-Content').hasClass('active')){
            $('.referance-title i:nth-child(3)').attr('class','fa-solid fa-minus');
        }else{
            $('.referance-title i:nth-child(3)').attr('class','fa-solid fa-plus');
        }
    })
// # FaqQuestion
   $('body').on("click",'[data-open-faq]',function(){
      var BTN,MyLI,FaqsAnswers,AnswerContext,BoxHeight;
      BTN = $(this);
      MyLI = BTN.parent();
      FaqsAnswers = $('.answer');
      AnswerContext = $('.answer p');
      BoxHeight = AnswerContext.outerHeight();
      BoxHeight = BoxHeight + 6;
      //
      if(!MyLI.hasClass('active')){
         FaqsAnswers.attr("style",'--outheight:'+BoxHeight+'px');
         setTimeout(function(){
            MyLI.addClass('active').siblings().removeClass('active');
         },100);
      }
   });

// #  children
    

// # LoadMore Function 
   LoadingRelated = false;
   LoadingMore = false;
   var Count = 0;
   var LoadingRelated = false;
   var LoadingMore = false;
   var LoadedCounter = 1;
   function MoreAjax(argument) {
      $('[data-more-click]').append('<div class="-newsLoaded"><div class="loader"><svg class="circular" viewBox="25 25 50 50"><circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/></svg></div></div>');
      LoadingMore = true;
      var Args = argument.data("loadmore");
      var AjaxURL = HomeURL+'/AjaxCenter/Loadmore/'+Args+'/';
      $.ajax({
         url: AjaxURL,
         dataType: 'json',
         success: function(msg) {         
            $('.-newsLoaded').remove();
            if(LoadedCounter == 3){
               argument.attr("data-autoloaded",false);
               argument.data("autoloaded",false);
               $('.PostsScrollLoader[data-more-click="'+argument.data("uniqid")+'"]').show();
            }else if(LoadedCounter == 5){
               LoadedCounter = 1;
               argument.attr("data-autoloaded",true);
               argument.data("autoloaded",true);
               $('[data-more-click="'+argument.data("uniqid")+'"]').hide();
            }
            $('[data-more-click="'+argument.data("uniqid")+'"]').removeClass('isloader');
            LoadingMore = false;
            argument.append(msg.output);
            argument.data("loadmore", msg.arguments);

            if(msg.end != undefined && msg.end == true){
               argument.attr("data-autoloaded",false);
               argument.data("autoloaded",false);
               argument.attr("data-finish",true);
               $('[data-more-click="'+argument.data("uniqid")+'"]').hide();
            }
         }
      });
   }
// # More Click 
   $('body').on('click','[data-more-click]',function(e){e.preventDefault();
      var ScrollerCenter = $('.-ScrollerCenter[data-uniqid="'+$(this).data('more-click')+'"]');
      if(LoadingMore == false && ScrollerCenter.data('finish') == false ){
         LoadedCounter = ((LoadedCounter < 3)) ? 4 : LoadedCounter + 1;
         $(this).addClass('isloader');
         MoreAjax(ScrollerCenter);
      }
   });
// # data-tabs
   var TabIsClick = false;
   var Timeout = false;
   $('body').on('click','[data-tabs]',function(){
      var BTN,ActionType,AppenderElement,type,NowUniq,cats,number;
      BTN = $(this);
      tabType = BTN.data('tabs');
      type = BTN.data('type');
      cats = BTN.data('cats');
      number = BTN.data('number');
      NowUniq = $(this).data('uniq');
      AppenderElement = $('.post-model-'+type+'');
      BTN.addClass('active').siblings().removeClass('active');
      var AjaxURL = HomeURL+'/AjaxCenter/RelatedPosts/';
         AppenderElement.addClass("-loading");
         
      
         TabIsClick = true;
         clearTimeout(Timeout);
         Timeout = setTimeout(function(){
            AjaxRequest({
               url: AjaxURL,
               dataType: 'json',
               type: 'POST',
               data: {
                  "tabs":tabType,
                  "type":type,
                  "cats":cats,
                  "number":number,
               },
               success: function(msg) {
                  $(AppenderElement).html(msg.output);
                  $(AppenderElement).removeClass("-loading");
               }
            });
         },150);
      
      
   });

// # data-tabs
   var TabIsClick = false;
   var Timeout = false;
   $('body').on('click','[data-hometab]',function(){
      var BTN,ActionType,AppenderElement;
      BTN = $(this);
      tabType = BTN.data('hometab');
      NowUniq = $(this).data('uniq');
      term = BTN.data('term');
      AppenderElement = $('.-ScrollerCenter[data-uniqid="'+NowUniq+'"]');
      BTN.addClass('active').siblings().removeClass('active');
      
      var AjaxURL = HomeURL+'/AjaxCenter/singletabs/';
      
      if( tabType != TabIsClick ) {
         AppenderElement.addClass("-loading");
         
      
         TabIsClick = true;
         clearTimeout(Timeout);
         Timeout = setTimeout(function(){
            AjaxRequest({
               url: AjaxURL,
               dataType: 'json',
               type: 'POST',
               data: {
                  "hometab":tabType,
                  "term":term,
               },
               success: function(msg) {
                  tabType = TabIsClick;
                  $(AppenderElement).html(msg.output);
                  $(AppenderElement).removeClass("-loading");
               }
            });
         },150);
      }
      
   });

   $(window).on("scroll", function () {
      $(window).scrollTop() >= $('header').outerHeight() ? (

      $("header").addClass("fixed"),
      $(".btn-phone").addClass("show"),
      $(".btn-whatsapp").addClass("show")) : (
      $("header").removeClass("fixed"),
      $(".btn-phone").removeClass("show"),
      $(".btn-whatsapp").removeClass("show"))
   })
   $('body').on('click','.menu_bar .fa-bars',function(e){
      $(".menu-nav").addClass("open");
      $(".menu_bar").addClass("icon");
      $('.search_header form').removeClass('active');
      $('#openSEarch').removeClass('close');
   });
   $('body').on('click','.menu_bar .fa-xmark',function(e){
      $(".menu-nav").removeClass("open");
      $(".menu_bar").removeClass("icon");
   });
// # share
     // https://ellisonleao.github.io/sharer.js
     // # data-title - sharer text
     // # data-url - url to be shared
     // # data-width - popup width
     // # data-height - popup height
     // # data-link - share element will work as a link
    // # data-blank (requires data-link combined) - share element will work as a link in a new tab
      (function(m, r) {
       "use strict";
       var s = function(t) {
         this.elem = t
       };
       s.init = function() {
         var t = r.querySelectorAll("[data-sharer]"),
           e, a = t.length;
         for (e = 0; e < a; e++) {
           t[e].addEventListener("click", s.add)
         }
       };
       s.add = function(t) {
         var e = t.currentTarget || t.srcElement;
         var a = new s(e);
         a.share()
       };
       s.prototype = {
         constructor: s,
         getValue: function(t) {
           var e = this.elem.getAttribute("data-" + t);
           if (e && t === "hashtag") {
             if (!e.startsWith("#")) {
               e = "#" + e
             }
           }
           return e === null ? "" : e
         },
         share: function() {
           var t = this.getValue("sharer").toLowerCase(),
             e = {
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
                   mini: true
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
                 shareUrl: this.getValue("web") === "true" ? "https://web.whatsapp.com/send" : "https://wa.me/",
                 params: {
                   phone: this.getValue("to"),
                   text: this.getValue("title") + " " + this.getValue("url"),
                   hashtags: this.getValue("hashtags"),
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
             },
             a = e[t];
           if (a) {
             a.width = this.getValue("width");
             a.height = this.getValue("height")
           }
           return a !== undefined ? this.urlSharer(a) : false
         },
         urlSharer: function(t) {
           var e = t.params || {},
           a = Object.keys(e),
           r, 
           s = a.length > 0 ? "?" : "";
           
           for (r = 0; r < a.length; r++) {
             if (s !== "?") {
               s += "&"
             }
             if (e[a[r]]) {
               s += a[r] + "=" + encodeURIComponent(e[a[r]])
             }
           }
           t.shareUrl += s;
           var l = this.getValue("link") === "true";
           var i = this.getValue("blank") === "true";
           if (l) {
             if (i) {
               m.open(t.shareUrl, "_blank")
             } else {
               m.location.href = t.shareUrl
             }
           } else {
             console.log(t.shareUrl);
             var h = t.width || 600,
               u = t.height || 480,
               o = m.innerWidth / 2 - h / 2 + m.screenX,
               p = m.innerHeight / 2 - u / 2 + m.screenY,
               g = "scrollbars=no, width=" + h + ", height=" + u + ", top=" + p + ", left=" + o,
               n = m.open(t.shareUrl, "", g);
             if (m.focus) {
               n.focus()
             }
           }
         }
       };
       if (r.readyState === "complete" || r.readyState !== "loading") {
         s.init()
       } else {
         r.addEventListener("DOMContentLoaded", s.init)
       }
       m.Sharer = s
     })(window, document);
      $('body').on("click",'[data-sharer] a',function(e) {e.preventDefault();});
    $('body').on("click", '.ez-toc-title-container', function(){
      $('ul.ez-toc-list').toggleClass("open");
   });

    $('body').on("click", '.album-holder .close', function(){
        $('.album-holder').removeClass('open')
    })    
    $('body').on("click", 'post--albums .img', function(){
        $('.album-holder').addClass('open');
    })

    
   $('body').on('click','span#Close',function(){
      $('[data-close]').remove();
      $('.form-contact form').removeClass('opctiy');
   })
  $('.form-contact form').submit(function(){
      
    DataJSON = new FormData(this);
      $.ajax({
        url: HomeURL+'/AjaxCenter/sendinfo',
        type:'POST',
           contentType: false,
           processData: false,
           cache:false,
           processData:false,
           data: DataJSON,
            success: function(msg) {
            $('.form-contact').append(msg.output);
            $('.form-contact form input').val('');
            $('.form-contact form textarea').val('');
            $('.form-contact form').addClass('opctiy');
        },

    })
  return false;

})
$('body').on("click",'.bottun',function(w){
        $(this).closest('.catArticleDetails').find('.ArticleDetails.details').toggleClass('height').siblings();
        $(this).toggleClass('transform'); 
    });

$('body').on("mouseover", function(e){
    $(".RatingReview > i").each(function(r,dropdown) {
      if (!$(dropdown).is(e.target) && $(dropdown).has(e.target).length === 0 ) {
        if( !$(dropdown).nextAll().hasClass('active') || !$(dropdown).parent().is(e.target) && $(dropdown).parent().has(e.target).length === 0 ){
          $(dropdown).removeClass('active');
        }
      }
    });
  });
$('body').on("mouseover",'.RatingReview > i', function(e){
    $(this).prevAll().addClass('active');
    $(this).addClass('active');
});

$('body').on("click",'.RatingReview:not(.justView) > i',function(){
var BTN = $(this);
// # CHANGE REVIEW.
  $('.RatingReview > i').removeClass('fixedactive active');
  BTN.prevAll().addClass('fixedactive');
  BTN.addClass('fixedactive');

if( BTN.closest('.RatingReview').data('save-id') != undefined ){
  var ID = BTN.closest('.RatingReview').data('save-id');
  var Type = BTN.closest('.RatingReview').data('save-type');
  var RateValue = BTN.data('rate');
  var AppenderValue = $('.-rating-value[data-post-id="'+ID+'"]');
  var LastValueRate  = $.cookie( "RateV1-"+Type+"-"+ID );
  var AjaxURL = HomeURL+'/AjaxCenter/RateAjax/';

  AjaxRequest({
    url: AjaxURL,
    dataType: 'json',
    type: 'POST',
    data: {
      "id":ID,
      "Type":Type,
      "LastValueRate":LastValueRate,
      "RateValue":RateValue,
    },
    success: function(msg) {
      AppenderValue.html(msg.TotalValue);
      if( $('.-single-bottom-list-Rate').length > 0 ){
        $('.-single-bottom-list-Rate').show();
      }
      if( $('.Rate-New-Mixers').length > 0 ){
        $('.Rate-New-Mixers').show();
      }
      
      if( $('.-Js-Rate-AverageItems[data-post-id="'+ID+'"]').length > 0 && msg.output != undefined ){
        $('.-Js-Rate-AverageItems[data-post-id="'+ID+'"]').html(msg.output);
      }

      if( $('.-rating-suptitle[data-post-id="'+ID+'"]').length > 0 && msg.RateUserCount_v1 != undefined ){
        $('.-rating-suptitle[data-post-id="'+ID+'"] em').html(msg.RateUserCount_v1);
      }

      if( $('.-YC-Review-Change[data-review-change="'+ID+'"]').length > 0 ){
        $('.-YC-Review-Change[data-review-change="'+ID+'"] > i').each(function(www,ee) {
          if( $(ee).data('rate') <= msg.TotalValue ) {
            $(ee).addClass('fixedactive');
          }else{
            $(ee).removeClass('fixedactive');
          }
        });
      }

    }
  });
  $.cookie( "RateV1-"+Type+"-"+ID, RateValue );
}else{
  $(this).closest('.RateComment').find('.product-item-info-stats-ratings > p > .-rating-value').text( $(this).data('rate')+'.0' );
  $(this).closest('.RateComment').find('input').val($(this).data('rate'));
}
});

// # COMMENTS AREA
function SubmitComment(elem,event=false) {
    var t = 0;
    if( event != false && event.keyCode == 13 || event == false ){

      $(elem).find(".alerts").html("");
      $(elem).find(".necessary").removeClass("necessary");

      var data = $(elem).serialize();
      var Data_arr = $(elem).serializeArray();

      var NewFormData = {};
      $.each(Data_arr,function(z,r) {
        NewFormData[ r['name'] ] = r['value'];
      });

      $(elem).find('.alerts').html('');
      var Validate = true;      
      if( NewFormData['user_name'] == undefined || NewFormData['user_name'] != undefined && NewFormData['user_name'] == '' ) { 
        if ( Currentuser_Logged == false ) {
          Validate = false;
          $(elem).find('.-comments-form-inputs-area[data-comment-field="user_name"]').addClass('necessary');
        }else{
          data += '&user_name='+Currentuser_display_name;
          NewFormData[ 'user_name' ] = Currentuser_display_name;
        }
      }else{
        $(elem).find('.-comments-form-inputs-area[data-comment-field="user_name"]').removeClass('necessary');
      }

      if( NewFormData['email'] == undefined || NewFormData['email'] != undefined && NewFormData['email'] == '' ) { 
        if( Currentuser_Logged == false ) {
          Validate = false;
          $(elem).find('.-comments-form-inputs-area[data-comment-field="email"]').addClass('necessary');
        }else{
          data += '&email='+Currentuser_email;
          NewFormData[ 'email' ] = Currentuser_email;
        }
      }else{
        $(elem).find('.-comments-form-inputs-area[data-comment-field="email"]').removeClass('necessary');
      }

      if( NewFormData['comment'] == undefined || NewFormData['comment'] != undefined && NewFormData['comment'] == '' ) { 
        Validate = false;
        $(elem).find('.-comments-form-inputs-area[data-comment-field="comment"]').addClass('necessary');
      }else{
        $(elem).find('.-comments-form-inputs-area[data-comment-field="comment"]').removeClass('necessary');
      }

      if( $(elem).data('id') == undefined || $(elem).data('id') != undefined && $(elem).data('id') == '' ) { 
        Validate = false;
      }

      if( $(elem).find('.RateValue').val() == '' && $(elem).attr('data-parent') == 0 ) {
        Validate = false;
        $(elem).find('.RateComment').addClass('necessary');
      }

      if( Validate == false ){
        $(elem).find(".alerts").append('<div class="alert alert-danger">' + __loc("يرجي ملأ الحقول المطلوبة (*)") + "</div>");
      }else{
        data += '&postID='+$(elem).data('id');
        if( $(elem).data('parent') != undefined && $(elem).data('parent') != '' ){
          data += '&parent='+$(elem).data('parent');
        }

        $(".NoComments").remove();
        $(elem).addClass("loading");
        $(elem).append('<div class="loader"> <div class="line"></div> <div class="line"></div> <div class="line"></div> <div class="line"></div> </div>');
        AjaxRequest({
          url: HomeURL + "/AjaxCenter/AddComment/",
          type: "POST",
          data: data,
          dataType: "json",
          success: function (t) {
            if( t.error == null ){
              $(elem).removeClass("loading");
              $(elem).find(".loader").remove();
              $(elem).trigger("reset");
              if( $(elem).attr("data-parent") == "0" ){
                $(".CommentsListInner").prepend(t.output);
              }else{
                $("#comment-" + $(elem).attr("data-parent") ).after('<ul class="ChildComments">' + t.output + "</ul>");
              }
              
            } 
          }
        });

        if ( Currentuser_Logged == false ) {
          $.cookie("user-data-email", NewFormData['email']);
          $.cookie("user-data-name", NewFormData['name']);
        }
      }
      return false;
    }
}
function ReplyComment(e) {
    var ReplayArgums = $(e).data('replay-arguments');
    ReplayArgums = atob( ReplayArgums );
    ReplayArgums = jQuery.parseJSON( ReplayArgums );
    $("form.CommentsFormInner").attr("data-parent", $(e).data("comment"));
    $(".ReplyCommentPreview").remove();
    $("form.CommentsFormInner").prepend('<div class="ReplyCommentPreview"><h2><i class="fas fa-reply"></i><em>' + __loc("رد علي") + "</em> <span>"+ReplayArgums.UserName+"</span></h2><p>"+ReplayArgums.Comment_content+"</p></div>");
    $("form.CommentsFormInner > textarea").focus();
    var CommentScrollTimeOut = setTimeout(function(){
      var comment_scroll_offset = $(".ReplyCommentPreview").offset().top;
      $('body,html').animate({"scrollTop":comment_scroll_offset }, 500);
    },100);
}
    $('body').on('click','.closepopup',function(){
        var BTn = $(this);
        $('.popup-call').remove();
   })
  $(window).on("scroll", function() {
    $(window).scrollTop() >= 800 ? $(".popup-call").addClass("show") : $(".popup-call").removeClass("show")
})
    $('body').on('click','.CommentsList>.CommentsList__Title',function(){
         $(this).toggleClass('clickcomment');
        $('ul.CommentsListInner').toggleClass('openComment');
    });

var isButtonClicked = false; 
$('body').on('click', '.btn-phone', function() {
    if (!isButtonClicked) { 
        isButtonClicked = true; 
        var BTn = $(this);
        var pageName = document.title;
        $.ajax({
            url: HomeURL + '/AjaxCenter/callupdate',
            type: 'POST',
            data: {
                page: pageName,
            },
            success: function(msg) {
                console.log(msg.output);
            },
        });
    }
});
var isButtonClickeds = false; 
$('body').on('click', '.btn-whatsapp', function() {
    if (!isButtonClickeds) { 
        isButtonClicked = true; 
        var BTn = $(this);
        var pageName = document.title;
        $.ajax({
            url: HomeURL + '/AjaxCenter/callupdate',
            type: 'POST',
            data: {
                page: pageName,
            },
            success: function(msg) {
                console.log(msg.output);
            },
        });
    }
});