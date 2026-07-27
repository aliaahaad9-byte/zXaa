$(document).ready(function(){
    var AllElements,GenralTapElem, LeaguesItems, RoundItems, TeamsItems, CoachItems, CuntryItems, MatchesItem, OffsersTapElem;
    RemoveItems = '#leaguesdiv,#tagsdiv-rounds,#tagsdiv-teams,#tagsdiv-players,#tagsdiv-coach,#countrydiv';
    AllElements = '#postdivrich,#titlediv,#APBMetaBox-PostOptions,#APBMetaBox-LeaguesPost,#APBMetaBox-RoundsPost,#APBMetaBox-TeamsPost,#APBMetaBox-PlayersPost,#APBMetaBox-CoachPost,#APBMetaBox-CountryPost,#APBMetaBox-MatchesPost';
    GenralTapElem = '#titlediv,#postdivrich,#APBMetaBox-PostOptions,#categorydiv,#postimagediv';
    LeaguesItems = '#APBMetaBox-LeaguesPost';
    RoundItems = '#APBMetaBox-RoundsPost';
    TeamsItems = '#APBMetaBox-TeamsPost';
    PlayersItems = '#APBMetaBox-PlayersPost';
    CoachItems = '#APBMetaBox-CoachPost';
    CuntryItems = '#APBMetaBox-CountryPost';
    MatchesItem = '#APBMetaBox-MatchesPost';
    var $ = jQuery;
    $('body').on("click",'ul.AddPostSwitch > li',function(){
        $('.AddPostSwitchParent > ul > li').removeClass('active');
        var el = this;
        $(el).addClass('active');
        $(AllElements).hide();
        if( $(el).data('type') == 'genral' ) {
            $(GenralTapElem).show();
        }else if( $(el).data('type') == 'leagues' ) {
            $(LeaguesItems).show();
        }else if( $(el).data('type') == 'rounds' ) {
            $(RoundItems).show();
        }else if( $(el).data('type') == 'teams' ) {
            $(TeamsItems).show();
        }else if( $(el).data('type') == 'players' ) {
            $(PlayersItems).show();
        }else if( $(el).data('type') == 'coach' ) {
            $(CoachItems).show();
        }else if( $(el).data('type') == 'country' ) {
            $(CuntryItems).show();
        }else if( $(el).data('type') == 'matches' ) {
            $(MatchesItem).show();
        }
    });

    if($('.AddPostSwitchParent').length > 0 ){
        $(RemoveItems).remove();
        $(AllElements).hide();
        $(GenralTapElem).show();
        $('#post-body-content').addClass('InsetProducts');  
    }


    if ($('.all-fields').length) {  
        $('.all-values').each(function(){
            var tp = $(this).find('.all-fields').height() - 44;
            $(this).find('.add-field').css('top',tp)
        })

    }

    /* Start  Upgrade Edits  */
    $('.add-field').click(function(){
    
        var count = $(this).closest('.all-values').find('.repeatable').length;
        var id = $(this).closest('.all-values').find('.first-element').attr('id').split('_yc_')[0];
        var el = $(this).closest('.all-values').find('.first-element').clone();
        el.attr('id',id+'_yc_'+count);
        el.val('');
        el.removeClass('first-element');
        $(this).closest('.all-values').find('.all-fields').append('<div class="el-field"><i class="fa fa-times"></i></div>');

        $(this).closest('.all-values').find('.el-field:last-child').prepend(el);
        var tp = $(this).closest('.all-values').find('.all-fields').height() - 44;
        $(this).css('top',tp)
    });

    function OembedFrame(el,value){
        if( value.trim() == '' ){
            el.closest('.apb-field-inner').find('textarea').html('');
            $('youtube--code').remove()
        }else{

            if (value.includes('youtube')) {
                YoutubeEmbed(el,value);
            }else if (value.includes('instagram')) {
                InstagramEmbed(el,value);
            } else if (value.includes('twitter')) {
                TwitterEmbed(el,value);
            }


        }

    }

    $('.select-tabs').change(function(){
        $('#'+$(this).val().replace(' ','')).show().siblings().hide()
    })
    
    function TwitterEmbed(el,value){
        el.closest("[class*='-field-inner']").find('youtube--code').remove()
        var src = value.split('status/')[1];

        var embedCode = `<blockquote class="twitter-tweet"><p lang="en" dir="ltr">Sunsets don&#39;t get much better than this one over <a href="https://twitter.com/GrandTetonNPS?ref_src=twsrc%5Etfw">@GrandTetonNPS</a>. <a href="https://twitter.com/hashtag/nature?src=hash&amp;ref_src=twsrc%5Etfw">#nature</a> <a href="https://twitter.com/hashtag/sunset?src=hash&amp;ref_src=twsrc%5Etfw">#sunset</a> <a href="http://t.co/YuKy2rcjyU">pic.twitter.com/YuKy2rcjyU</a></p>&mdash; US Department of the Interior (@Interior) <a href="https://twitter.com/Interior/status/${src}?ref_src=twsrc%5Etfw">May 5, 2014</a><script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script></blockquote>`;
        el.after(`<youtube--code class="twitter"><remove--youtube class="fa-solid fa-xmark"></remove--youtube>${embedCode.replace(/\/+$/, '')}</youtube--code>`)
        el.closest("[class*='-field-inner']").find('textarea').html(embedCode.replace(/\/+$/, ''))

    }

    function InstagramEmbed(el,value){
        el.closest("[class*='-field-inner']").find('youtube--code').remove()
        var src = value.split('p/')[1];
        src = src.split('/')[0];
        src = 'https://www.instagram.com/p/'+src+'/embed';
        var embedCode = `<iframe width="100%" height="800" src="${src}" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write;encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>`;

        if (el.closest("[class*='-field-inner']").find('youtube--code').length) {
            el.closest("[class*='-field-inner']").find('iframe').attr('src',src)
        }else {
            el.after(`<youtube--code class="instagram"><remove--youtube class="fa-solid fa-xmark"></remove--youtube>${embedCode.replace(/\/+$/, '')}</youtube--code>`)
        }
        el.closest("[class*='-field-inner']").find('textarea').html(embedCode.replace(/\/+$/, ''));

    }
    function YoutubeEmbed(el,value){
        el.closest("[class*='-field-inner']").find('youtube--code').remove()
        if ( value.split('?v=')[1].length) {
            var src = 'https://www.youtube.com/embed/'+value.split('?v=')[1];
            src = src.trim()
            var embedCode = `<iframe width="100%" height="450" src="${src}" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; 
                            encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>`;

            if (el.closest("[class*='-field-inner']").find('youtube--code').length) {
                el.closest("[class*='-field-inner']").find('iframe').attr('src',src)
            }else {
                el.after(`<youtube--code class="youtube"><remove--youtube class="fa-solid fa-xmark"></remove--youtube>${embedCode.replace(/\/+$/, '')}</youtube--code>`)
            }
            el.closest("[class*='-field-inner']").find('textarea').html(embedCode.replace(/\/+$/, ''));

        }
    }


    $('body').on('click','remove--youtube',function(){
        $(this).closest("[class*='-field-inner']").find('input').val('')
        $(this).closest("[class*='-field-inner']").find('textarea').val('')
        $(this).parent().remove();
    })

    $('.add-youtube-link').keyup(function(){
        OembedFrame($(this),$(this).val())
    });

    $('.add-youtube-link').bind("paste", function(e){
        OembedFrame($(this),$(this).val())
    } );



    $('body').on('click','.el-field i',function(){
        $(this).parent().remove();
        var st = 1;
        $('.el-field').each(function(){
            var id = $(this).find('>*').attr('id').split('_yc_')[0];
            $(this).find('>*').attr('id',id+'_yc_'+st);
            st = st + 1;
        })
    })


    $('all-terms term').on('click',function(){
        $(this).closest("[class*='-field-inner']").find('.choose-fonts').val($(this).text())
    })

    $('.search-terms input').on('keyup',function(){
       var searched = $(this).val().toLowerCase()
        $(this).closest('.terms-container').find('term').each(function(){
            if( $(this).text().toLowerCase().includes(searched) > 0 ){
                $(this).css('order','1')
           }else {
            $(this).css('order','2')
            }
        })
    })

    $('font--icons input').keyup(function(){
    

        var icons = $(this).closest('fonts--box').find('all-icons')
        icons.html('<loader class="lds-ellipsis"><div></div><div></div><div></div><div></div></loader>')
        $.ajax({
            type:'POST',
            url:$(this).data('url'),
            data:{
                action:'get_icon',
                s:$(this).val(),
                font:$(this).data('font')
            },
            success:function(data){
                icons.html(data)
            }
        })
    })

    $('check--tab').click(function(){
        $(this).addClass('active').siblings().removeClass('active');
        $($(this).data('id')).show().siblings().hide()
    })

    $('hour, minute, ampm-options').click(function(){
        $(this).parent().prev().val($(this).text())
    })

    $('.choose-date').click(function(){
        $('danger').remove()
        var hour = $(this).closest('time--box').find('.choose-hour input').val();
        var min = $(this).closest('time--box').find('.choose-min input').val();
        var am = $(this).closest('time--box').find('.choose-am-pm input').val();
        if (hour != '' && min != '' && am != '') {

            $(this).closest("[class*='-field-inner']").find('.show-time').val(am+' '+hour+':'+min);
            $(this).parent().removeClass('open')
        }else {
            $(this).after('<danger>لم يتم تحديد الوقت بشكل سليم</danger>')
        }
    })

    $('.show-time').focus(function(){
        $(this).next().addClass('open')
    })  
    $('.show-time').on('click',function(e){
        e.stopPropagation()
    })
    $('body').on('click',function(){
        $('time--box').removeClass('open')
    })

    $('time--box').on('click',function(e){
        e.stopPropagation()
    })

    $('.choose-fonts').focus(function(){
        $('fonts--box').removeClass('open')
        $(this).next('fonts--box').addClass('open')
    })
    $('.remove-font-icons').click(function(){
        $(this).parent().removeClass('open')
    })
    $('body').on('click','all-icons div',function(){
        $(this).closest('font--icons').find('input[type="hidden"]').val($(this).attr('class'));
        $(this).closest('font--icons').find('.choose-fonts').val($(this).attr('class').replace('fa ','').replace('fa-','').replace('ion-ios-',''));
        $(this).closest('font--icons').find('icons').html('<div class="'+$(this).attr('class')+'"></div>')
    })
     
    $('body').on('click','all-icons lord-icon',function(){
        $(this).closest('font--icons').find('input').val($(this).attr('data-id'));
        $(this).closest('font--icons').find('icons').html('<lord-icon src="https://cdn.lordicon.com/'+$(this).attr('data-id')+'.json"  trigger="loop" style="width:80px;height:80px"></lord-icon>')
    })
    /*  END Upgrade Edits  */
    $('#misc-publishing-actions').append('<input type="hidden" name="apbupdate" value="1" />');
    $('body').on('click', 'ul.APBLayouts > li', function(e){
        $('#'+$(this).data('fields')).val($(this).data('layoutval'));
        var APBBuilderParent = $(this).parent().parent().find('.APBLayoutsBuilder');
        $(this).parent().find('li').removeClass('layout-current');
        $(this).addClass('layout-current');
        APBBuilderParent.html('<div class="APBLoader"><svg version="1.1" id="loader-1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="40px" height="40px" viewBox="0 0 40 40" enable-background="new 0 0 40 40" xml:space="preserve"> <path opacity="0.2" fill="#000" d="M20.201,5.169c-8.254,0-14.946,6.692-14.946,14.946c0,8.255,6.692,14.946,14.946,14.946 s14.946-6.691,14.946-14.946C35.146,11.861,28.455,5.169,20.201,5.169z M20.201,31.749c-6.425,0-11.634-5.208-11.634-11.634 c0-6.425,5.209-11.634,11.634-11.634c6.425,0,11.633,5.209,11.633,11.634C31.834,26.541,26.626,31.749,20.201,31.749z"></path> <path fill="#000" d="M26.013,10.047l1.654-2.866c-2.198-1.272-4.743-2.012-7.466-2.012h0v3.312h0 C22.32,8.481,24.301,9.057,26.013,10.047z"> <animateTransform attributeType="xml" attributeName="transform" type="rotate" from="0 20 20" to="360 20 20" dur="0.5s" repeatCount="indefinite"></animateTransform> </path> </svg></div>');
        $.ajax({
            url:HomeURL+'/wp-admin/admin-ajax.php',
            data:{"action":'APBLayoutsBuilder', "fields":$(this).data('fields'), "numb":$(this).parent().parent().data('numb'), "layout":$(this).data('layout')},
            type:'POST',
            success: function(msg) {
                $('body, html').animate({"scrollTop":APBBuilderParent.offset().top - ($(window).height() / 2) + 100});
                APBBuilderParent.html(msg);
            },
            error: function() {
                alert(errorAjax);
            }
        });
    });
    $('body').on('click', '.AddMoreGroup', function(e){
        var APBParentObject = $(this).parent().parent();
        var APBParentParent = $(this).parent();
        $.ajax({
            url:HomeURL+'/wp-admin/admin-ajax.php',
            data:{"action":'APBAddGroupFields', "metabox":$(this).data('metabox'), "numb":$(this).data('numb'), "group":$(this).data('group')},
            type:'POST',
            success: function(msg) {
                APBParentObject.after(msg);
                APBParentParent.remove();
                $('.LayoutsBuilderFooter + .LayoutsBuilderFooter').remove();
            },
            error: function() {
                alert(errorAjax);
            }
        });
    });
    $('body').on('click', '.AddMoreLayout', function(e){
        var APBParent = $(this).parent();
        $.ajax({
            url:HomeURL+'/wp-admin/admin-ajax.php',
            data:{"action":'APBAddLayoutBuilder', "numb":$(this).data('numb'), "metabox":$(this).data('metabox')},
            type:'POST',
            success: function(msg) {
                APBParent.parent().after(msg);
                APBParent.remove();
            },
            error: function() {
                alert(errorAjax);
            }
        });
    });
});
function NextItem(el) {
    var $Parent=$(el).parent().parent();
    if( $Parent.prev().hasClass('APBMainLayout') ) {
        $Parent.insertBefore($Parent.prev());
    }
}
function PrevItem(el) {
    var $Parent=$(el).parent().parent();
    if( $Parent.next().hasClass('APBMainLayout') ) {
        $Parent.insertAfter($Parent.next());
    }
}
function RemoveGroupField(el) {
    $(el).parent().parent().remove();
}

// ## Insert New Groups ## //
function GrpFieldsContext(arraydata=[]){
    var Retuner = '';
    if(arraydata.FieldType != undefined && arraydata.FieldType == 'taxonomy_select'){
        Retuner += '<ListsCatsSelecor data-action="'+arraydata.FieldName+'" class="ListsCatsSelecor">';
            Retuner += '<input type="hidden" value="'+arraydata.FieldValue+'" name="'+arraydata.InputName+'['+arraydata.GeKey+']['+arraydata.FieldName+']"/>';
            Retuner += '<h2 data-popparent="'+arraydata.FieldName+'"><span>'+$(arraydata.active_taxonomy).find('span').text()+'</span><i class="fas fa-angle-down"></i></h2>';
            Retuner += '<ul>';
                $.each(OffersTaxonomy, function(kiki, geky){
                    var TaxValueField;
                    if($(arraydata.active_taxonomy).data('valuefield') != undefined){
                        if($(arraydata.active_taxonomy).data('valuefield') == 'name'){
                            TaxValueField = geky.name;
                        }else if($(arraydata.active_taxonomy).data('valuefield') == 'term_id'){
                            TaxValueField = geky.term_id;
                        }else if($(arraydata.active_taxonomy).data('valuefield') == 'slug'){
                            TaxValueField = geky.slug;
                        }
                    }else{
                        TaxValueField = geky.term_id;
                    }
                    Retuner += '<li data-selecters="'+TaxValueField+'" '+((geky.term_id == arraydata.FieldValue) ? 'class="active"' : '')+' data-fill="all"><span>'+geky.name+'</span></li>';
                });
            Retuner += '</ul>';
        Retuner += '</ListsCatsSelecor>';
    }else if (arraydata.FieldType != undefined && arraydata.FieldType == 'textarea'){
        Retuner += '<textarea type="text" placeholder="الاجابة " autocomplete="off" autocorrect="off" name="'+arraydata.InputName+'['+arraydata.GeKey+']['+arraydata.FieldName+']">'+arraydata.FieldValue+'</textarea>';
    }else{
        Retuner += '<input type="text" name="'+arraydata.InputName+'['+arraydata.GeKey+']['+arraydata.FieldName+']" value="'+arraydata.FieldValue+'">';;
    }
    return Retuner;
}


var GeKey = 0;
$('body').on('click','[data-grp-groupmore]',function(){
    var ThatMore,InputName,MyInput,MasterUL,MasterGrouping,FieldValue,InsertOptions;
    ThatMore = $(this);
    MasterGrouping = ThatMore.parent().parent();
    MasterUL = ThatMore.parent();
    InputName = ThatMore.data('grp-groupmore');
    // FindCounter // 
    var Grawyek = $(MasterGrouping).find('li[attrfield="'+InputName+'"]:last-child');
    if(Grawyek.length > 0 ){
        GeKey = Grawyek.attr('attkey');
        GeKey++;
    }
    InsertOptions = false;
    ExtraxtInputs = '<li attkey="'+GeKey+'">';
        $(MasterUL).find('[attrtype]').each(function(ints, int){
            var FieldValue = (($(int).attr('attrtype') == 'textarea')) ? $(int).val() : $(int).val();
            if(FieldValue == ''){
                InsertOptions = true;
            }
            var FieldName = $(int).attr('name');
            var FieldType = $(int).attr('attrtype');
            if(FieldType == undefined){
                FieldType = 'text';
            }
            var sendera = {
                "FieldType":FieldType,
                "FieldValue":FieldValue,
                "FieldName":FieldName,
                "InputName":InputName,
                "GeKey":GeKey
            };
            if(FieldType != undefined && FieldType == 'taxonomy_select' && $(int).parent().find('li.active').length > 0){
                sendera['active_taxonomy'] = $(int).parent().find('li.active');
            }
            ExtraxtInputs += GrpFieldsContext(sendera);             
            //$(int).val(' ');
        });
    ExtraxtInputs += '<span onclick="$(this).parent().remove();"><i class="fa fa-times"></i></span></li>';
    if(InsertOptions == false){
        $(MasterGrouping).find('ul.ItemsGroupsUl').append(ExtraxtInputs);
        $('.ErrorAlertRelative').hide();
        $(MasterUL).find('input').val('');
        $(MasterUL).find('textarea').val('');
        $(MasterUL).find('textarea').html('');
    }else{
        $('.ErrorAlertRelative').html('<AlerterOrr class="Erroreee">لم تقم بإضافة جميع البيانات الاساسية .. برجاء اكمال البيانات </AlerterOrr>').show();
    }
    GeKey++;
}); 


