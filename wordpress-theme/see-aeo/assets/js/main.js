'use strict';
const navbar = document.getElementById('navbar');
function handleNavbarScroll(){ navbar.classList.toggle('scrolled', window.scrollY > 30); }
window.addEventListener('scroll', handleNavbarScroll, { passive:true }); handleNavbarScroll();

const hamburger = document.getElementById('hamburger');
const mobileNav = document.getElementById('mobileNav');
function openMobileNav(){ hamburger.classList.add('open'); mobileNav.classList.add('open'); mobileNav.setAttribute('aria-hidden','false'); hamburger.setAttribute('aria-expanded','true'); }
function closeMobileNav(){ hamburger.classList.remove('open'); mobileNav.classList.remove('open'); mobileNav.setAttribute('aria-hidden','true'); hamburger.setAttribute('aria-expanded','false'); }
hamburger.addEventListener('click', () => { mobileNav.classList.contains('open') ? closeMobileNav() : openMobileNav(); });
document.addEventListener('click', (e) => { if (mobileNav.classList.contains('open') && !mobileNav.contains(e.target) && !hamburger.contains(e.target)) closeMobileNav(); });
window.addEventListener('resize', () => { if (window.innerWidth > 1024) closeMobileNav(); });

(function initParticles(){
  const canvas = document.getElementById('particleCanvas'); if(!canvas) return;
  const ctx = canvas.getContext('2d'); let W,H,particles;
  const CYAN='rgba(0,167,233,', PURPLE='rgba(106,64,144,';
  function resize(){ W=canvas.width=canvas.offsetWidth; H=canvas.height=canvas.offsetHeight; }
  function rand(a,b){ return a+Math.random()*(b-a); }
  function mkP(){ return {x:rand(0,W),y:rand(0,H),r:rand(0.8,2.2),vx:rand(-0.25,0.25),vy:rand(-0.3,0.1),a:rand(0.2,0.7),color:Math.random()>0.55?CYAN:PURPLE}; }
  function build(){ particles=Array.from({length:80},mkP); }
  function draw(){
    ctx.clearRect(0,0,W,H);
    for(let i=0;i<particles.length;i++){ for(let j=i+1;j<particles.length;j++){ const dx=particles[i].x-particles[j].x,dy=particles[i].y-particles[j].y,d=Math.sqrt(dx*dx+dy*dy); if(d<100){ ctx.beginPath();ctx.moveTo(particles[i].x,particles[i].y);ctx.lineTo(particles[j].x,particles[j].y);ctx.strokeStyle=CYAN+(1-d/100)*0.12+')';ctx.lineWidth=0.5;ctx.stroke(); } } }
    particles.forEach(p=>{ ctx.beginPath();ctx.arc(p.x,p.y,p.r,0,Math.PI*2);ctx.fillStyle=p.color+p.a+')';ctx.fill(); p.x+=p.vx;p.y+=p.vy; if(p.x<-5)p.x=W+5; if(p.x>W+5)p.x=-5; if(p.y<-5)p.y=H+5; if(p.y>H+5)p.y=-5; });
    requestAnimationFrame(draw);
  }
  resize(); build(); draw();
  window.addEventListener('resize', () => { resize(); build(); });
})();

(function(){
  const els=document.querySelectorAll('.fade-in');
  const obs=new IntersectionObserver(es=>{ es.forEach(e=>{ if(e.isIntersecting){ e.target.classList.add('visible'); obs.unobserve(e.target);} }); },{threshold:0.12});
  els.forEach(el=>obs.observe(el));
})();

(function(){
  const counters=document.querySelectorAll('.stat__number'); let started=false;
  function animate(el){ const target=parseInt(el.dataset.target,10),steps=Math.ceil(1800/16); let c=0; const t=setInterval(()=>{ c++; const eased=1-Math.pow(1-c/steps,3); el.textContent=Math.round(eased*target).toLocaleString('ar'); if(c>=steps){ el.textContent=target.toLocaleString('ar'); clearInterval(t);} },16); }
  const hero=document.getElementById('hero');
  const obs=new IntersectionObserver(es=>{ es.forEach(e=>{ if(e.isIntersecting&&!started){ started=true; counters.forEach(animate); obs.disconnect();} }); },{threshold:0.3});
  if(hero) obs.observe(hero);
})();

(function(){
  const sections=document.querySelectorAll('section[id]');
  const links=document.querySelectorAll('.navbar__links .nav-link');
  function setActive(){ let cur=''; sections.forEach(s=>{ if(window.scrollY>=s.offsetTop-window.innerHeight*0.4) cur=s.id; }); links.forEach(l=>{ l.classList.toggle('active', l.getAttribute('href')==='#'+cur); }); }
  window.addEventListener('scroll', setActive, {passive:true}); setActive();
})();

(function(){
  const form=document.getElementById('contactForm'); if(!form) return;
  const nameEl=document.getElementById('name'),emailEl=document.getElementById('email'),msgEl=document.getElementById('message');
  const nameErr=document.getElementById('nameError'),emailErr=document.getElementById('emailError'),msgErr=document.getElementById('messageError'),successBox=document.getElementById('formSuccess');
  const err=(el,e,m)=>{el.style.borderColor='#ff6b6b';e.textContent=m;}, clr=(el,e)=>{el.style.borderColor='';e.textContent='';};
  const validEmail=v=>/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v.trim());
  function validate(){ let ok=true;
    if(!nameEl.value.trim()||nameEl.value.trim().length<2){err(nameEl,nameErr,'الرجاء إدخال اسمك الكامل');ok=false;}else clr(nameEl,nameErr);
    if(!validEmail(emailEl.value)){err(emailEl,emailErr,'صيغة البريد الإلكتروني غير صحيحة');ok=false;}else clr(emailEl,emailErr);
    if(!msgEl.value.trim()||msgEl.value.trim().length<10){err(msgEl,msgErr,'الرجاء كتابة رسالتك');ok=false;}else clr(msgEl,msgErr);
    return ok; }
  form.addEventListener('submit', e=>{ e.preventDefault(); if(!validate()) return;
    const btn=form.querySelector('button[type="submit"]'); btn.disabled=true; btn.querySelector('.btn-text').textContent='جاري الإرسال...';
    setTimeout(()=>{ btn.disabled=false; btn.querySelector('.btn-text').textContent='أرسل رسالتك'; successBox.classList.add('show'); form.reset(); setTimeout(()=>successBox.classList.remove('show'),5000); },1400);
  });
})();

document.querySelectorAll('.navbar__logo, .footer__logo').forEach(el=>{ el.addEventListener('click', e=>{ e.preventDefault(); window.scrollTo({top:0,behavior:'smooth'}); }); });
