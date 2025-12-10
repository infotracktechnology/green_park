// Simple slider logic (auto + manual)
         (function(){
  const slidesEl = document.getElementById('slides');
  const slides = slidesEl.children;
  const total = slides.length;
  let index = 0;
  const interval = 7000;
  let timer = null;

  function go(i){
    index = (i + total) % total;
    slidesEl.style.transform = `translateX(${ -index * 100 }%)`;
    updateDots();
  }
  function next(){ go(index+1) }
  function prev(){ go(index-1) }

  // wire up controls
  document.getElementById('next').addEventListener('click', ()=>{ next(); reset(); });
  document.getElementById('prev').addEventListener('click', ()=>{ prev(); reset(); });

  // safer start/pause/reset
  function start(){
    if (timer !== null) return; // already running
    timer = setInterval(next, interval);
  }
  function pause(){
    if (timer !== null){
      clearInterval(timer);
      timer = null;
    }
  }
  function reset(){ pause(); start(); }

  // dot controls
  const dots = document.querySelectorAll('#sliderDots .dot');
  function updateDots(){
    dots.forEach((d,i)=>{
      d.classList.toggle('active', i===index);
    });
  }
  dots.forEach(dot=>{
    dot.addEventListener('click', ()=>{
      go(parseInt(dot.dataset.index, 10));
      reset();
    });
  });
  updateDots();

  start();

  // Pause on hover / pointer / touch
  const slider = document.getElementById('slider');

  // pointerenter / pointerleave are more consistent across input types than mouseenter
  slider.addEventListener('pointerenter', pause);
  slider.addEventListener('pointerleave', start);

  // for touch devices: pause during touch interactions
  slider.addEventListener('touchstart', pause, {passive: true});
  slider.addEventListener('touchend', ()=> { /* small delay to avoid immediate restart while touch releasing */ setTimeout(start, 50); });

  // accessibility: allow keyboard arrows to control when slider focused
  // ensure slider can receive focus (set tabindex in HTML if not already)
  slider.addEventListener('keydown', (e)=>{
    if(e.key === 'ArrowRight') { next(); reset(); }
    if(e.key === 'ArrowLeft') { prev(); reset(); }
  });

  // pause when slider has focus (keyboard users)
  slider.addEventListener('focusin', pause);
  slider.addEventListener('focusout', start);

  // pause when document hidden (user switched tab) to save CPU and avoid jumpiness
  document.addEventListener('visibilitychange', ()=>{
    if (document.hidden) pause();
    else start();
  });

})();


         (function(){
                    const lightbox = document.getElementById('lightbox');
                    const stage = lightbox.querySelector('.lb-stage');
                    const imgEl = lightbox.querySelector('.lb-original');
                    const closeBtn = lightbox.querySelector('.lb-close');
                  
                    // Open buttons: any element with .open-lightbox and data-light attribute
                    document.querySelectorAll('.open-lightbox').forEach(btn=>{
                      btn.addEventListener('click', function(e){
                        e.preventDefault();
                        const url = this.getAttribute('data-light');
                        if(!url) return;
                  
                        // set src to URL (use your local file path if needed)
                        imgEl.src = url;
                  
                        // Show immediately (image will load at its natural size; stage scrolls if larger)
                        lightbox.classList.add('active');
                        lightbox.setAttribute('aria-hidden','false');
                  
                        // focus for keyboard close
                        closeBtn.focus();
                      });
                    });
                  
                    // Close handlers
                    function closeLB(){
                      lightbox.classList.remove('active');
                      lightbox.setAttribute('aria-hidden','true');
                      // release image src (optional, frees memory)
                      imgEl.src = '';
                    }
                    closeBtn.addEventListener('click', closeLB);
                    lightbox.addEventListener('click', function(e){
                      if(e.target === this) closeLB();
                    });
                    document.addEventListener('keydown', function(e){
                      if(!lightbox.classList.contains('active')) return;
                      if(e.key === 'Escape') closeLB();
                    });
                  
                    // Make sure stage receives keyboard focus when opened (allows scroll via keyboard)
                    // (When image loads, focus stage so user can use arrows/space)
                    imgEl.addEventListener('load', function(){
                      stage.focus();
                    });
                  
                  })();

         (function(){
           const toggler = document.getElementById('navToggler');
           const panel   = document.getElementById('sidePanel');
           const overlay = document.getElementById('sideOverlay');
           const closeBtn = panel.querySelector('.side-close');
           const singleMode = panel.dataset.single === "true";
         
           function openPanel(){
             toggler.classList.add('open');
             toggler.setAttribute('aria-expanded','true');
             panel.classList.add('active','pro');
             panel.setAttribute('aria-hidden','false');
             overlay.classList.add('active');
             // focus first accordion button
             const f = panel.querySelector('.acc-btn');
             if(f) f.focus();
           }
           function closePanel(){
             toggler.classList.remove('open');
             toggler.setAttribute('aria-expanded','false');
             panel.classList.remove('active');
             panel.setAttribute('aria-hidden','true');
             overlay.classList.remove('active');
           }
         
           toggler.addEventListener('click', e=>{
             e.preventDefault();
             if(panel.classList.contains('active')) closePanel(); else openPanel();
           });
           overlay.addEventListener('click', closePanel);
           closeBtn.addEventListener('click', closePanel);
           document.addEventListener('keydown', e=>{ if(e.key==='Escape' && panel.classList.contains('active')) closePanel(); });
         
           // accordion
           panel.querySelectorAll('.acc-btn').forEach(btn=>{
             btn.addEventListener('click', ()=>{
               const isActive = btn.classList.contains('active');
               if(singleMode){
                 // close others
                 panel.querySelectorAll('.acc-btn.active').forEach(o=>{
                   o.classList.remove('active');
                   o.setAttribute('aria-expanded','false');
                   const c = o.nextElementSibling; if(c) c.style.maxHeight = null;
                 });
               }
               if(isActive){
                 btn.classList.remove('active'); btn.setAttribute('aria-expanded','false');
                 const c = btn.nextElementSibling; if(c) c.style.maxHeight = null;
               } else {
                 btn.classList.add('active'); btn.setAttribute('aria-expanded','true');
                 const content = btn.nextElementSibling;
                 if(content){
                   content.style.maxHeight = content.scrollHeight + 'px';
                 }
               }
             });
           });
         
           // recompute open heights on resize
           window.addEventListener('resize', ()=>{
             panel.querySelectorAll('.acc-btn.active').forEach(btn=>{
               const c = btn.nextElementSibling; if(c) c.style.maxHeight = c.scrollHeight + 'px';
             });
           });
         
         })();