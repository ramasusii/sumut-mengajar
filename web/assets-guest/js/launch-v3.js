(function(){
  function initSlider(root){
    var slides=[].slice.call(root.querySelectorAll('.gsm-hero-slide'));
    var dots=[].slice.call(root.querySelectorAll('[data-slide-dot]'));
    if(slides.length<2) return;
    var current=0;
    var delay=parseInt(root.getAttribute('data-autoplay')||'6500',10);
    var timer=null;
    function show(index){
      current=(index+slides.length)%slides.length;
      slides.forEach(function(s,i){s.classList.toggle('is-active',i===current);});
      dots.forEach(function(d,i){d.classList.toggle('is-active',i===current);});
    }
    function restart(){
      if(timer) clearInterval(timer);
      if(window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
      timer=setInterval(function(){show(current+1);},delay);
    }
    var prev=root.querySelector('.gsm-hero-arrow.prev');
    var next=root.querySelector('.gsm-hero-arrow.next');
    if(prev) prev.addEventListener('click',function(){show(current-1);restart();});
    if(next) next.addEventListener('click',function(){show(current+1);restart();});
    dots.forEach(function(dot){dot.addEventListener('click',function(){show(parseInt(dot.getAttribute('data-slide-dot'),10)||0);restart();});});
    var startX=null;
    root.addEventListener('touchstart',function(e){startX=e.touches[0].clientX;},{passive:true});
    root.addEventListener('touchend',function(e){if(startX===null)return;var dx=e.changedTouches[0].clientX-startX;if(Math.abs(dx)>50){show(current+(dx<0?1:-1));restart();}startX=null;},{passive:true});
    root.addEventListener('mouseenter',function(){if(timer)clearInterval(timer);});
    root.addEventListener('mouseleave',restart);
    restart();
  }
  document.addEventListener('DOMContentLoaded',function(){document.querySelectorAll('[data-hero-slider]').forEach(initSlider);});
})();
