// Nav mobile toggle
const toggle = document.querySelector('.nav__toggle');
const links  = document.querySelector('.nav__links');

if (toggle && links) {
  toggle.addEventListener('click', () => {
    links.classList.toggle('open');
    toggle.textContent = links.classList.contains('open') ? '✕' : '☰';
  });

  // Close on link click
  links.querySelectorAll('a').forEach(a => {
    a.addEventListener('click', () => {
      links.classList.remove('open');
      toggle.textContent = '☰';
    });
  });
}

// Scroll: shrink nav
window.addEventListener('scroll', () => {
  document.querySelector('.nav')?.classList.toggle('nav--scrolled', window.scrollY > 50);
});

// Contact form → contact.php → aims_notify
const form = document.getElementById('contactForm');
if (form) {
  form.addEventListener('submit', async e => {
    e.preventDefault();

    const btn     = form.querySelector('button[type=submit]');
    const success = document.getElementById('formSuccess');
    const errorEl = document.getElementById('formError');

    // Reset state
    btn.disabled = true;
    btn.textContent = 'Versturen…';
    if (errorEl) errorEl.hidden = true;

    try {
      const res  = await fetch('contact_send.php', {
        method: 'POST',
        body: new FormData(form),
      });
      const data = await res.json();

      if (data.ok) {
        form.reset();
        if (success) success.hidden = false;
      } else {
        const msg = data.errors ? data.errors.join(', ') : (data.error ?? 'Er ging iets mis');
        if (errorEl) { errorEl.textContent = msg; errorEl.hidden = false; }
        btn.disabled = false;
        btn.textContent = 'Verstuur bericht';
      }
    } catch {
      if (errorEl) { errorEl.textContent = 'Verbindingsfout, probeer opnieuw.'; errorEl.hidden = false; }
      btn.disabled = false;
      btn.textContent = 'Verstuur bericht';
    }
  });
}

// Lightbox
(function () {
  const imgs = Array.from(document.querySelectorAll('.card__img-wrap img'));
  if (!imgs.length) return;

  // Build DOM once
  const lb = document.createElement('div');
  lb.className = 'lightbox';
  lb.setAttribute('role', 'dialog');
  lb.setAttribute('aria-modal', 'true');
  lb.innerHTML =
    '<button class="lightbox__close" aria-label="Sluiten">&times;</button>' +
    '<button class="lightbox__prev"  aria-label="Vorige">&#8249;</button>' +
    '<img class="lightbox__img" src="" alt="">' +
    '<button class="lightbox__next"  aria-label="Volgende">&#8250;</button>' +
    '<span class="lightbox__caption"></span>';
  document.body.appendChild(lb);

  const lbImg     = lb.querySelector('.lightbox__img');
  const lbCaption = lb.querySelector('.lightbox__caption');
  let current = 0;

  function show(i) {
    current = (i + imgs.length) % imgs.length;
    lbImg.src = imgs[current].src;
    lbImg.alt = imgs[current].alt;
    lbCaption.textContent = imgs[current].alt;
  }

  function open(i) {
    show(i);
    lb.classList.add('is-open');
    document.body.style.overflow = 'hidden';
  }

  function close() {
    lb.classList.remove('is-open');
    document.body.style.overflow = '';
  }

  // Click on card image
  imgs.forEach((img, i) => {
    img.closest('.card__img-wrap').addEventListener('click', () => open(i));
  });

  lb.querySelector('.lightbox__close').addEventListener('click', close);
  lb.querySelector('.lightbox__prev').addEventListener('click', (e) => { e.stopPropagation(); show(current - 1); });
  lb.querySelector('.lightbox__next').addEventListener('click', (e) => { e.stopPropagation(); show(current + 1); });

  // Close on backdrop click
  lb.addEventListener('click', (e) => { if (e.target === lb) close(); });

  // Keyboard
  document.addEventListener('keydown', (e) => {
    if (!lb.classList.contains('is-open')) return;
    if (e.key === 'Escape')     close();
    if (e.key === 'ArrowLeft')  show(current - 1);
    if (e.key === 'ArrowRight') show(current + 1);
  });

  // Touch swipe
  let touchX = null;
  lb.addEventListener('touchstart', (e) => { touchX = e.touches[0].clientX; }, { passive: true });
  lb.addEventListener('touchend', (e) => {
    if (touchX === null) return;
    const dx = e.changedTouches[0].clientX - touchX;
    if (Math.abs(dx) > 50) show(dx < 0 ? current + 1 : current - 1);
    touchX = null;
  });
})();

// Scroll reveal - cards
const observer = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      entry.target.style.opacity = '1';
      entry.target.style.transform = 'translateY(0)';
      observer.unobserve(entry.target);
    }
  });
}, { threshold: 0.1 });

document.querySelectorAll('.card, .care-item, .contact-detail').forEach((el, i) => {
  el.style.opacity = '0';
  el.style.transform = 'translateY(24px)';
  el.style.transition = `opacity 0.6s ease ${i * 0.07}s, transform 0.6s ease ${i * 0.07}s`;
  observer.observe(el);
});
