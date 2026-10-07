/* PROPFIRM CONCLAVE — KOCHI 2026 · main.js */
(function(){
"use strict";
var reduceMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

/* ---------- header scroll ---------- */
var header = document.querySelector(".site-header");
function onScroll(){ if(header) header.classList.toggle("scrolled", window.scrollY > 24); }
window.addEventListener("scroll", onScroll, {passive:true}); onScroll();

/* ---------- mobile menu ---------- */
var burger = document.querySelector(".burger");
if(burger){
  burger.addEventListener("click", function(){
    document.body.classList.toggle("menu-open");
  });
  document.querySelectorAll(".mobile-menu a").forEach(function(a){
    a.addEventListener("click", function(){ document.body.classList.remove("menu-open"); });
  });
}
/* stagger mobile links */
document.querySelectorAll(".mobile-menu a").forEach(function(a,i){
  a.style.transitionDelay = (0.05 + i*0.05) + "s";
});

/* ---------- scroll reveals ---------- */
var io = new IntersectionObserver(function(entries){
  entries.forEach(function(e){
    if(e.isIntersecting){ e.target.classList.add("in"); io.unobserve(e.target); }
  });
},{threshold:0.12, rootMargin:"0px 0px -40px 0px"});
document.querySelectorAll(".rv").forEach(function(el){ io.observe(el); });

/* ---------- counters ---------- */
var cio = new IntersectionObserver(function(entries){
  entries.forEach(function(e){
    if(!e.isIntersecting) return;
    var el = e.target, target = parseFloat(el.dataset.count), dec = parseInt(el.dataset.dec||"0",10);
    var t0 = null, dur = 1600;
    function step(t){
      if(!t0) t0 = t;
      var p = Math.min((t-t0)/dur, 1);
      var eased = 1 - Math.pow(1-p, 3);
      el.textContent = (target*eased).toFixed(dec);
      if(p < 1) requestAnimationFrame(step); else el.textContent = target.toFixed(dec);
    }
    if(reduceMotion){ el.textContent = target.toFixed(dec); }
    else requestAnimationFrame(step);
    cio.unobserve(el);
  });
},{threshold:0.5});
document.querySelectorAll("[data-count]").forEach(function(el){ cio.observe(el); });

/* ---------- countdown ---------- */
var cd = document.getElementById("countdown");
if(cd){
  var target = new Date("2026-12-12T09:00:00+05:30").getTime();
  var dEl=cd.querySelector("[data-d]"),hEl=cd.querySelector("[data-h]"),
      mEl=cd.querySelector("[data-m]"),sEl=cd.querySelector("[data-s]");
  function pad(n){ return (n<10?"0":"")+n; }
  function tick(){
    var diff = target - Date.now();
    if(diff <= 0){ dEl.textContent="00";hEl.textContent="00";mEl.textContent="00";sEl.textContent="00"; return; }
    dEl.textContent = pad(Math.floor(diff/864e5));
    hEl.textContent = pad(Math.floor(diff/36e5)%24);
    mEl.textContent = pad(Math.floor(diff/6e4)%60);
    sEl.textContent = pad(Math.floor(diff/1e3)%60);
  }
  tick(); setInterval(tick, 1000);
}

/* ---------- gold dust particles (hero) ---------- */
var canvas = document.getElementById("dust");
if(canvas && !reduceMotion){
  var ctx = canvas.getContext("2d"), W, H, parts = [];
  var N = window.innerWidth < 760 ? 45 : 90;
  function size(){ W = canvas.width = canvas.offsetWidth; H = canvas.height = canvas.offsetHeight; }
  size(); window.addEventListener("resize", size);
  function spawn(anyY){
    return { x: Math.random()*W, y: anyY ? Math.random()*H : H + 10,
      r: 0.6 + Math.random()*2.2, vy: 0.15 + Math.random()*0.5,
      vx: (Math.random()-0.5)*0.25, a: 0.15 + Math.random()*0.55,
      tw: Math.random()*Math.PI*2 };
  }
  for(var i=0;i<N;i++) parts.push(spawn(true));
  var mx = -999, my = -999;
  canvas.parentElement.addEventListener("pointermove", function(e){
    var r = canvas.getBoundingClientRect(); mx = e.clientX - r.left; my = e.clientY - r.top;
  });
  (function loop(){
    ctx.clearRect(0,0,W,H);
    for(var j=0;j<parts.length;j++){
      var p = parts[j];
      p.tw += 0.02; p.y -= p.vy; p.x += p.vx + Math.sin(p.tw)*0.15;
      var dx = p.x-mx, dy = p.y-my, d = Math.sqrt(dx*dx+dy*dy);
      if(d < 130 && d > 1){ p.x += dx/d*0.9; p.y += dy/d*0.9; }
      if(p.y < -12) parts[j] = spawn(false);
      var alpha = p.a * (0.6 + 0.4*Math.sin(p.tw));
      ctx.beginPath(); ctx.arc(p.x, p.y, p.r, 0, 6.283);
      ctx.fillStyle = "rgba(233,205,140," + alpha.toFixed(3) + ")";
      ctx.shadowColor = "rgba(201,162,75,.8)"; ctx.shadowBlur = 8;
      ctx.fill(); ctx.shadowBlur = 0;
    }
    requestAnimationFrame(loop);
  })();
}

/* ---------- agenda day tabs ---------- */
document.querySelectorAll(".day-tab").forEach(function(tab){
  tab.addEventListener("click", function(){
    document.querySelectorAll(".day-tab").forEach(function(t){ t.classList.remove("active"); });
    tab.classList.add("active");
    var day = tab.dataset.day;
    document.querySelectorAll(".timeline").forEach(function(tl){
      tl.style.display = (tl.dataset.day === day) ? "" : "none";
    });
  });
});

/* ---------- FAQ accordion ---------- */
document.querySelectorAll(".faq-q").forEach(function(btn){
  btn.addEventListener("click", function(){
    var item = btn.closest(".faq-item"), ans = item.querySelector(".faq-a");
    var open = item.classList.contains("open");
    document.querySelectorAll(".faq-item.open").forEach(function(o){
      o.classList.remove("open"); o.querySelector(".faq-a").style.maxHeight = null;
    });
    if(!open){ item.classList.add("open"); ans.style.maxHeight = ans.scrollHeight + "px"; }
  });
});

/* ---------- language switcher (Google Translate, English default) ---------- */
var LANGS = [
  ["en","English"],["hi","Hindi (हिन्दी)"],["ml","Malayalam (മലയാളം)"],
  ["ta","Tamil (தமிழ்)"],["te","Telugu (తెలుగు)"],["kn","Kannada (ಕನ್ನಡ)"],
  ["ar","Arabic (العربية)"],["ur","Urdu (اردو)"],["fr","French"],["es","Spanish"],
  ["pt","Portuguese"],["ru","Russian"],["tr","Turkish"]
];
function currentLang(){
  var m = document.cookie.match(/(?:^|; )googtrans=\/en\/([a-z-]+)/);
  return m ? m[1] : "en";
}
function setLang(code){
  if(code === "en"){
    document.cookie = "googtrans=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
    document.cookie = "googtrans=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/; domain=" + location.hostname + ";";
  }else{
    document.cookie = "googtrans=/en/" + code + "; path=/;";
    document.cookie = "googtrans=/en/" + code + "; path=/; domain=" + location.hostname + ";";
  }
  location.reload();
}
window.__pfcSetLang = setLang;
function buildLangMenus(){
  document.querySelectorAll(".lang-menu").forEach(function(menu){
    if(menu.dataset.built) return; menu.dataset.built = "1";
    var cur = currentLang();
    LANGS.forEach(function(l){
      var b = document.createElement("button");
      b.type = "button"; b.textContent = l[1];
      if(l[0] === cur || (cur === "en" && l[0] === "en")) b.classList.add("current");
      b.addEventListener("click", function(){ setLang(l[0]); });
      menu.appendChild(b);
    });
  });
}
function googleTranslateElementInit(){
  new google.translate.TranslateElement({pageLanguage:"en", autoDisplay:false}, "google_translate_element");
  buildLangMenus();
  var cur = currentLang(), label = "English";
  LANGS.forEach(function(l){ if(l[0]===cur) label = l[1]; });
  document.querySelectorAll(".lang-btn .lang-label").forEach(function(s){ s.textContent = label; });
}
window.googleTranslateElementInit = googleTranslateElementInit;
document.querySelectorAll(".lang-btn").forEach(function(btn){
  btn.addEventListener("click", function(e){
    e.stopPropagation();
    var menu = btn.parentElement.querySelector(".lang-menu");
    var wasOpen = menu.classList.contains("open");
    document.querySelectorAll(".lang-menu.open").forEach(function(m){ m.classList.remove("open"); });
    if(!wasOpen){ buildLangMenus(); menu.classList.add("open"); }
  });
});
document.addEventListener("click", function(){
  document.querySelectorAll(".lang-menu.open").forEach(function(m){ m.classList.remove("open"); });
});
/* clear stale translate cookie so the site always opens in English */
(function(){
  var m = document.cookie.match(/(?:^|; )googtrans=([^;]*)/);
  if(m && !/^\/en(\/|$)/.test(decodeURIComponent(m[1])) && !/%2Fen(%2F|$)/.test(m[1])){
    /* keep user's explicit choice; only default fresh visits to English */
  }
})();

/* ---------- ticket / contact forms (mailto fallback) ---------- */
document.querySelectorAll("form[data-mailto]").forEach(function(f){
  f.addEventListener("submit", function(e){
    e.preventDefault();
    var to = f.dataset.mailto, parts = [];
    f.querySelectorAll("input,select,textarea").forEach(function(el){
      if(el.name && el.value) parts.push(el.name + ": " + el.value);
    });
    var subject = encodeURIComponent("PropFirm Conclave Kochi 2026 — " + (f.dataset.subject || "New enquiry"));
    window.location.href = "mailto:" + to + "?subject=" + subject + "&body=" + encodeURIComponent(parts.join("\n"));
    var note = f.querySelector(".form-note");
    if(note) note.textContent = "Opening your email app — just hit send and our team will reply within 24 hours.";
  });
});
})();

/* Newsletter -> mailto */
(function(){
  var nf = document.getElementById('newsForm');
  if(!nf) return;
  nf.addEventListener('submit', function(e){
    e.preventDefault();
    var n = (document.getElementById('newsName')||{}).value || '';
    var em = (document.getElementById('newsEmail')||{}).value || '';
    if(!em) return;
    var body = 'Name: ' + n + '\nEmail: ' + em + '\n\nPlease add me to the PropFirm Conclave Kochi 2026 updates list.';
    window.location.href = 'mailto:events@finfluenze.com?subject=' + encodeURIComponent('Conclave updates signup') + '&body=' + encodeURIComponent(body);
    var ok = document.getElementById('newsOk');
    if(ok) ok.style.display = 'block';
  });
})();
