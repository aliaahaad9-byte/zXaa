/* ============================================================
   See AEO — Main JavaScript
============================================================ */

'use strict';

/* ============================================================
   1. STICKY NAVBAR
============================================================ */
const navbar = document.getElementById('navbar');

function handleNavbarScroll() {
  if (window.scrollY > 40) {
    navbar.classList.add('scrolled');
  } else {
    navbar.classList.remove('scrolled');
  }
}

window.addEventListener('scroll', handleNavbarScroll, { passive: true });
handleNavbarScroll(); // run once on load

/* ============================================================
   2. MOBILE HAMBURGER MENU
============================================================ */
const hamburger  = document.getElementById('hamburger');
const mobileNav  = document.getElementById('mobileNav');

function openMobileNav() {
  hamburger.classList.add('open');
  mobileNav.classList.add('open');
  mobileNav.setAttribute('aria-hidden', 'false');
  hamburger.setAttribute('aria-expanded', 'true');
  document.body.style.overflow = 'hidden';
}

function closeMobileNav() {
  hamburger.classList.remove('open');
  mobileNav.classList.remove('open');
  mobileNav.setAttribute('aria-hidden', 'true');
  hamburger.setAttribute('aria-expanded', 'false');
  document.body.style.overflow = '';
}

hamburger.addEventListener('click', () => {
  const isOpen = mobileNav.classList.contains('open');
  isOpen ? closeMobileNav() : openMobileNav();
});

// Close on outside click
document.addEventListener('click', (e) => {
  if (
    mobileNav.classList.contains('open') &&
    !mobileNav.contains(e.target) &&
    !hamburger.contains(e.target)
  ) {
    closeMobileNav();
  }
});

// Close on resize to desktop
window.addEventListener('resize', () => {
  if (window.innerWidth > 768) closeMobileNav();
});

/* ============================================================
   3. PARTICLE CANVAS (hero background)
============================================================ */
(function initParticles() {
  const canvas = document.getElementById('particleCanvas');
  if (!canvas) return;
  const ctx = canvas.getContext('2d');

  let W, H, particles;
  const PARTICLE_COUNT = 90;
  const CYAN = 'rgba(0,167,233,';
  const PURPLE = 'rgba(106,64,144,';

  function resize() {
    W = canvas.width  = canvas.offsetWidth;
    H = canvas.height = canvas.offsetHeight;
  }

  function randomBetween(a, b) {
    return a + Math.random() * (b - a);
  }

  function createParticle() {
    return {
      x:    randomBetween(0, W),
      y:    randomBetween(0, H),
      r:    randomBetween(0.8, 2.2),
      vx:   randomBetween(-0.25, 0.25),
      vy:   randomBetween(-0.3, 0.1),
      a:    randomBetween(0.2, 0.7),
      color: Math.random() > 0.55 ? CYAN : PURPLE,
    };
  }

  function buildParticles() {
    particles = Array.from({ length: PARTICLE_COUNT }, createParticle);
  }

  function drawParticle(p) {
    ctx.beginPath();
    ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
    ctx.fillStyle = p.color + p.a + ')';
    ctx.fill();
  }

  // Draw subtle connection lines between nearby particles
  function drawConnections() {
    const MAX_DIST = 100;
    for (let i = 0; i < particles.length; i++) {
      for (let j = i + 1; j < particles.length; j++) {
        const dx = particles[i].x - particles[j].x;
        const dy = particles[i].y - particles[j].y;
        const dist = Math.sqrt(dx * dx + dy * dy);
        if (dist < MAX_DIST) {
          const alpha = (1 - dist / MAX_DIST) * 0.12;
          ctx.beginPath();
          ctx.moveTo(particles[i].x, particles[i].y);
          ctx.lineTo(particles[j].x, particles[j].y);
          ctx.strokeStyle = CYAN + alpha + ')';
          ctx.lineWidth = 0.5;
          ctx.stroke();
        }
      }
    }
  }

  function tick() {
    ctx.clearRect(0, 0, W, H);
    drawConnections();
    particles.forEach((p) => {
      drawParticle(p);
      p.x += p.vx;
      p.y += p.vy;
      // Wrap around edges
      if (p.x < -5)  p.x = W + 5;
      if (p.x > W+5) p.x = -5;
      if (p.y < -5)  p.y = H + 5;
      if (p.y > H+5) p.y = -5;
    });
    requestAnimationFrame(tick);
  }

  resize();
  buildParticles();
  tick();

  window.addEventListener('resize', () => {
    resize();
    buildParticles();
  });
})();

/* ============================================================
   4. INTERSECTION OBSERVER — FADE-IN ON SCROLL
============================================================ */
(function initFadeIn() {
  const els = document.querySelectorAll('.fade-in');

  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add('visible');
          observer.unobserve(entry.target);
        }
      });
    },
    { threshold: 0.12 }
  );

  els.forEach((el) => observer.observe(el));
})();

/* ============================================================
   5. COUNTER ANIMATION (hero stats)
============================================================ */
(function initCounters() {
  const counters = document.querySelectorAll('.stat__number');
  let started = false;

  function animateCounter(el) {
    const target  = parseInt(el.dataset.target, 10);
    const duration = 1800; // ms
    const step     = 16;   // ~60fps
    const increments = Math.ceil(duration / step);
    let count = 0;

    const timer = setInterval(() => {
      count++;
      const progress = count / increments;
      // Ease out cubic
      const eased = 1 - Math.pow(1 - progress, 3);
      const current = Math.round(eased * target);
      el.textContent = current.toLocaleString('ar');
      if (count >= increments) {
        el.textContent = target.toLocaleString('ar');
        clearInterval(timer);
      }
    }, step);
  }

  function tryStart(entries) {
    entries.forEach((entry) => {
      if (entry.isIntersecting && !started) {
        started = true;
        counters.forEach(animateCounter);
        heroObserver.disconnect();
      }
    });
  }

  const heroSection = document.getElementById('hero');
  const heroObserver = new IntersectionObserver(tryStart, { threshold: 0.3 });
  if (heroSection) heroObserver.observe(heroSection);
})();

/* ============================================================
   6. ACTIVE NAV LINK on scroll
============================================================ */
(function initActiveNav() {
  const sections  = document.querySelectorAll('section[id]');
  const navLinks  = document.querySelectorAll('.navbar__links .nav-link, .mobile-nav .nav-link');

  function setActive() {
    let current = '';
    sections.forEach((sec) => {
      const offset = sec.offsetTop - (window.innerHeight * 0.4);
      if (window.scrollY >= offset) {
        current = sec.getAttribute('id');
      }
    });

    navLinks.forEach((link) => {
      link.style.color = '';
      const href = link.getAttribute('href');
      if (href === '#' + current) {
        link.style.color = 'var(--clr-cyan)';
      }
    });
  }

  window.addEventListener('scroll', setActive, { passive: true });
  setActive();
})();

/* ============================================================
   7. CONTACT FORM VALIDATION
============================================================ */
(function initContactForm() {
  const form        = document.getElementById('contactForm');
  if (!form) return;

  const nameEl      = document.getElementById('name');
  const emailEl     = document.getElementById('email');
  const messageEl   = document.getElementById('message');
  const nameErr     = document.getElementById('nameError');
  const emailErr    = document.getElementById('emailError');
  const msgErr      = document.getElementById('messageError');
  const successBox  = document.getElementById('formSuccess');

  function showError(el, errEl, msg) {
    el.style.borderColor = '#ff6b6b';
    errEl.textContent = msg;
  }

  function clearError(el, errEl) {
    el.style.borderColor = '';
    errEl.textContent = '';
  }

  function validateEmail(val) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val.trim());
  }

  function validate() {
    let valid = true;

    // Name
    if (!nameEl.value.trim()) {
      showError(nameEl, nameErr, 'الرجاء إدخال اسمك الكامل');
      valid = false;
    } else if (nameEl.value.trim().length < 2) {
      showError(nameEl, nameErr, 'الاسم يجب أن يكون حرفين على الأقل');
      valid = false;
    } else {
      clearError(nameEl, nameErr);
    }

    // Email
    if (!emailEl.value.trim()) {
      showError(emailEl, emailErr, 'الرجاء إدخال بريدك الإلكتروني');
      valid = false;
    } else if (!validateEmail(emailEl.value)) {
      showError(emailEl, emailErr, 'صيغة البريد الإلكتروني غير صحيحة');
      valid = false;
    } else {
      clearError(emailEl, emailErr);
    }

    // Message
    if (!messageEl.value.trim()) {
      showError(messageEl, msgErr, 'الرجاء كتابة رسالتك');
      valid = false;
    } else if (messageEl.value.trim().length < 10) {
      showError(messageEl, msgErr, 'رسالتك قصيرة جداً، الرجاء التوضيح أكثر');
      valid = false;
    } else {
      clearError(messageEl, msgErr);
    }

    return valid;
  }

  // Real-time validation on blur
  nameEl.addEventListener('blur', () => {
    if (nameEl.value.trim().length >= 2) clearError(nameEl, nameErr);
  });
  emailEl.addEventListener('blur', () => {
    if (validateEmail(emailEl.value)) clearError(emailEl, emailErr);
  });
  messageEl.addEventListener('blur', () => {
    if (messageEl.value.trim().length >= 10) clearError(messageEl, msgErr);
  });

  form.addEventListener('submit', (e) => {
    e.preventDefault();

    if (!validate()) return;

    // Simulate sending
    const submitBtn = form.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.querySelector('.btn-text').textContent = 'جاري الإرسال...';

    setTimeout(() => {
      submitBtn.disabled = false;
      submitBtn.querySelector('.btn-text').textContent = 'أرسل رسالتك';
      successBox.classList.add('show');
      form.reset();

      // Hide success after 5 seconds
      setTimeout(() => {
        successBox.classList.remove('show');
      }, 5000);
    }, 1400);
  });
})();

/* ============================================================
   8. SMOOTH HOVER HIGHLIGHT on service cards
============================================================ */
(function initCardGlow() {
  const cards = document.querySelectorAll('.service-card, .why-card');

  cards.forEach((card) => {
    card.addEventListener('mousemove', (e) => {
      const rect   = card.getBoundingClientRect();
      const x      = e.clientX - rect.left;
      const y      = e.clientY - rect.top;
      card.style.setProperty('--mx', x + 'px');
      card.style.setProperty('--my', y + 'px');
    });
  });
})();

/* ============================================================
   9. SCROLL TO TOP on logo click
============================================================ */
document.querySelectorAll('.navbar__logo, .footer__logo').forEach((logo) => {
  logo.addEventListener('click', (e) => {
    e.preventDefault();
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });
});
