const prefersReducedMotion = () =>
  window.matchMedia('(prefers-reduced-motion: reduce)').matches;

export function initReveal() {
  const elements = document.querySelectorAll('[data-reveal]');
  if (!elements.length) return;

  document.documentElement.classList.add('reveal-init');
  document.querySelectorAll('[data-reveal-group]').forEach((group) => {
    group.querySelectorAll('[data-reveal]').forEach((el, index) => {
      el.style.setProperty('--reveal-index', index);
    });
  });

  if (prefersReducedMotion() || !('IntersectionObserver' in window)) {
    elements.forEach((el) => el.classList.add('is-revealed'));
    return;
  }

  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (!entry.isIntersecting) return;
        const el = entry.target;
        el.classList.add('is-revealed');
        observer.unobserve(el);
      });
    },
    { threshold: 0.15, rootMargin: '0px 0px -8% 0px' }
  );

  elements.forEach((el) => observer.observe(el));
}

export function initParallax() {
  const media = document.querySelector('.hero__media');
  const stats = document.querySelector('[data-parallax-stats]');
  if ((!media && !stats) || prefersReducedMotion()) return;

  let ticking = false;

  function update() {
    if (media) {
      const offset = Math.min(window.scrollY, 400) * 0.12;
      media.style.transform = `translate3d(0, ${offset}px, 0)`;
    }

    if (stats && window.innerWidth >= 1024) {
      const rect = stats.getBoundingClientRect();
      const distanceFromCenter = (window.innerHeight / 2) - (rect.top + rect.height / 2);
      const offset = Math.max(-42, Math.min(42, distanceFromCenter * 0.08));
      stats.style.transform = `translate3d(0, ${offset.toFixed(2)}px, 0)`;
    } else if (stats) {
      stats.style.transform = '';
    }

    ticking = false;
  }

  window.addEventListener(
    'scroll',
    () => {
      if (!ticking) {
        requestAnimationFrame(update);
        ticking = true;
      }
    },
    { passive: true }
  );

  window.addEventListener('resize', update, { passive: true });
  update();
}
