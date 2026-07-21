const prefersReducedMotion = () =>
  window.matchMedia('(prefers-reduced-motion: reduce)').matches;

export function initHeroStrip() {
  const strip = document.querySelector('[data-hero-strip]');
  if (!strip) return;

  const slidesWrap = strip.querySelector('[data-hero-strip-slides]');
  const slides = Array.from(slidesWrap?.children || []);
  const progress = strip.querySelector('[data-hero-progress]');
  const prevBtn = strip.querySelector('[data-hero-prev]');
  const nextBtn = strip.querySelector('[data-hero-next]');
  const cta = strip.querySelector('.hero__strip-cta');
  const vehicleLinks = JSON.parse(strip.dataset.vehicleLinks || '[]');

  if (slides.length < 2) {
    slides[0]?.classList.add('is-active');
    return;
  }

  let current = 0;
  let timer = null;

  function render() {
    slides.forEach((slide, i) => slide.classList.toggle('is-active', i === current));
    if (cta && vehicleLinks[current]) {
      cta.setAttribute('href', vehicleLinks[current]);
    }
    restartProgress();
  }

  function restartProgress() {
    if (!progress) return;
    progress.classList.remove('is-running', 'is-paused');
    progress.style.transform = '';
    void progress.offsetWidth;
    if (prefersReducedMotion()) {
      progress.style.transform = `scaleX(${(current + 1) / slides.length})`;
    } else {
      progress.classList.add('is-running');
    }
  }

  function goTo(index) {
    current = (index + slides.length) % slides.length;
    render();
    restartAutoplay();
  }

  function restartAutoplay() {
    if (prefersReducedMotion()) return;
    clearInterval(timer);
    timer = setInterval(() => goTo(current + 1), 5000);
  }

  prevBtn?.addEventListener('click', () => goTo(current - 1));
  nextBtn?.addEventListener('click', () => goTo(current + 1));
  strip.addEventListener('mouseenter', () => {
    clearInterval(timer);
    progress?.classList.add('is-paused');
  });
  strip.addEventListener('mouseleave', () => {
    restartProgress();
    restartAutoplay();
  });

  render();
  restartAutoplay();
}
