<?php

$comment_num = get_comments_number($post->ID);

echo '<div class ="comments_label">';
	echo '<div class="single-post-parent-container-comments">';
		echo '<span><i class="fa fa-comments"></i> تعليقات ('.$comment_num.')</span>';
		echo '<form action="'.esc_url(home_url('/AjaxCenter/AddComment/')).'" data-parent="0" data-id="'.(int)$post->ID.'" class="CommentsFormInner" method="POST" novalidate onsubmit="return YCSubmitComment(this);">';
			echo '<div class="alerts" role="status" aria-live="polite"></div>';
			echo '<div class ="Comment_lise">';
				if( is_user_logged_in() ) { global $current_user;
					echo '<input type="text" disabled name="yourname" value="'.esc_attr($current_user->display_name).'" placeholder="إسمك الكريم *" />';
					echo '<input type="email" disabled name="email" value="'.esc_attr($current_user->user_email).'" placeholder="بريدك الإلكتروني *" />';
				}else {
					echo '<input type="text" name="user_name" required maxlength="80" autocomplete="name" placeholder="إسمك الكريم *" aria-label="الاسم" />';
					echo '<input type="email" name="email" required autocomplete="email" placeholder="بريدك الإلكتروني *" aria-label="البريد الإلكتروني" />';
				}
			echo '</div>';
			// حقل فخ للسبام: مخفي عن الزوار
			echo '<div style="position:absolute;left:-9999px;top:auto;width:1px;height:1px;overflow:hidden" aria-hidden="true"><label>الموقع<input type="text" name="website" tabindex="-1" autocomplete="off" value="" /></label></div>';
			echo '<div class ="textarea_form">';
				echo '<textarea name="comment" required maxlength="2000" placeholder="أكتب التعليق هنا .." aria-label="التعليق"></textarea>';
				echo '<button class="hoverable activable" type="submit">إرســال التعليق</button>';
			echo '</div>';
		echo '</form>';
		$arguments = array(

			'status' => 'approve',

			'number' => '15',

			'post_id' => $post->ID,

			'parent'  => 0

		);

		$comments = get_comments($arguments);

		$totalcomments = wp_count_comments($post->ID)->approved;

		if( count($comments) > 0 ) {

			echo '<div class="CommentsList" data-id="'.$post->ID.'">';

				echo '<div class="CommentsList__Title"><span class="comments_number">'.NumberReader($totalcomments).'</span><span>تعليقات  </span><i class="fas fa-arrow-down"></i></div>';

				echo '<ul class="CommentsListInner">';

					foreach ($comments as $comment) {

						$this->Part("CommentItem", array("comment"=>$comment, "post"=>$post));

					}

				echo '</ul>';

			echo '</div>';

		}else {

			echo '<div class="CommentsList" data-id="'.$post->ID.'">';

				echo '<ul class="CommentsListInner">';

					echo '<li class="NoComments">';

						echo '<i class="fal fa-info-circle"></i>';

						echo 'لم يتم إضافة تعليقات لهذا المقال.';

					echo '</li>';

				echo '</ul>';

			echo '</div>';

		}

	echo '</div>';

echo '</div>';

// دوال الإرسال والرد — مستقلة تمامًا عن setup.js حتى لا يتأثر تتبع المكالمات
echo '<style>.CommentsFormInner .alerts .ycc-a{border-radius:12px;padding:12px 16px;margin:0 0 14px;font-size:15px;font-weight:600}.CommentsFormInner .ycc-ok{background:#e6f6ec;color:#0f7a3b;border:1px solid #b8e2c6}.CommentsFormInner .ycc-err{background:#fdecec;color:#b42318;border:1px solid #f5c2c0}.CommentsFormInner .necessary{border-color:#b42318!important}.CommentsFormInner.ycc-busy button[type=submit]{opacity:.6;pointer-events:none}.ycc-pending{background:#fff8e6;border:1px dashed #e8b84a;border-radius:12px;padding:12px 14px;color:#7a4b00;font-size:14px;list-style:none;margin:0 0 12px}.ycc-pending b{display:block;margin-bottom:4px}</style>';
echo '<script>
(function(){
  function msg(f,t,ok){var a=f.querySelector(".alerts");if(!a)return;a.innerHTML="";var d=document.createElement("div");d.className="ycc-a "+(ok?"ycc-ok":"ycc-err");d.textContent=t;a.appendChild(d);}
  window.YCSubmitComment=function(f){
    if(f.getAttribute("data-busy")){return false;}
    var name=f.querySelector("[name=user_name]"),mail=f.querySelector("[name=email]:not([disabled])"),txt=f.querySelector("[name=comment]");
    [].forEach.call(f.querySelectorAll(".necessary"),function(e){e.classList.remove("necessary");});
    var bad=[];
    if(name&&!name.value.trim()){bad.push(name);}
    if(mail&&!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(mail.value.trim())){bad.push(mail);}
    if(!txt.value.trim()){bad.push(txt);}
    if(bad.length){bad.forEach(function(e){e.classList.add("necessary");});msg(f,"يرجى ملء الحقول المطلوبة (*) بشكل صحيح.",false);bad[0].focus();return false;}
    var fd=new FormData(f);
    fd.append("postID",f.getAttribute("data-id"));
    var par=f.getAttribute("data-parent");if(par&&par!=="0"){fd.append("parent",par);}
    f.setAttribute("data-busy","1");f.classList.add("ycc-busy");
    fetch(f.getAttribute("action"),{method:"POST",body:fd,credentials:"same-origin"})
      .then(function(r){return r.json().catch(function(){return {error:true,message:"حدث خطأ غير متوقع، حاول مرة أخرى."};});})
      .then(function(d){
        f.removeAttribute("data-busy");f.classList.remove("ycc-busy");
        if(!d||d.error){msg(f,(d&&d.message)||"تعذّر إرسال التعليق.",false);return;}
        msg(f,d.message||"تم الإرسال.",true);
        var list=document.querySelector(".CommentsListInner");
        var no=document.querySelector(".NoComments");if(no){no.remove();}
        var isReply=par&&par!=="0";
        if(list){list.style.display="block";} /* القائمة مطوية افتراضيًا في القالب: نفتحها ليرى الزائر تعليقه */
        if(d.status==="approved"&&d.output&&list){
          if(isReply){var t=document.getElementById("comment-"+par);if(t){var ul=document.createElement("ul");ul.className="ChildComments";ul.innerHTML=d.output;t.after(ul);}}
          else{list.insertAdjacentHTML("afterbegin",d.output);}
        }else if(list&&!isReply){
          var li=document.createElement("li");li.className="ycc-pending";
          var b=document.createElement("b");b.textContent="تعليقك قيد المراجعة";li.appendChild(b);
          li.appendChild(document.createTextNode(txt.value.replace(/<[^>]*>/g,"").trim()));list.insertBefore(li,list.firstChild);
        }
        txt.value="";
        var rp=f.querySelector(".ReplyCommentPreview");if(rp){rp.remove();}f.setAttribute("data-parent","0");
      })
      .catch(function(){f.removeAttribute("data-busy");f.classList.remove("ycc-busy");msg(f,"تعذّر الاتصال بالخادم، تأكد من الإنترنت وحاول مرة أخرى.",false);});
    return false;
  };
  window.YCReplyComment=function(a){
    var f=document.querySelector("form.CommentsFormInner");if(!f){return;}
    f.setAttribute("data-parent",a.getAttribute("data-comment"));
    var old=f.querySelector(".ReplyCommentPreview");if(old){old.remove();}
    var li=a.closest("li"),p=li&&li.querySelector(".contentcomment");
    var box=document.createElement("div");box.className="ReplyCommentPreview";
    var h=document.createElement("h2"),em=document.createElement("em"),sp=document.createElement("span");
    em.textContent="رد على ";sp.textContent=a.getAttribute("data-name")||"";h.appendChild(em);h.appendChild(sp);box.appendChild(h);
    if(p){var q=document.createElement("p");q.textContent=p.textContent;box.appendChild(q);}
    f.insertBefore(box,f.firstChild);
    var ta=f.querySelector("textarea");if(ta){ta.focus({preventScroll:true});}
    f.scrollIntoView({behavior:"smooth",block:"center"});
  };
})();
</script>';
