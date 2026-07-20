const prefersReducedMotion = () =>
  window.matchMedia('(prefers-reduced-motion: reduce)').matches;

function animateCount(el) {
  const target = el.dataset.countTo || el.textContent;
  const match = target.match(/^([^\d]*)(\d[\d,.]*)(.*)$/);

  if (!match || prefersReducedMotion()) {
    el.textContent = target;
    return;
  }

  const [, prefix, numberStr, suffix] = match;
  const hasComma = numberStr.includes(',');
  const endValue = parseFloat(numberStr.replace(/,/g, ''));
  const duration = 1400;
  const start = performance.now();

  function frame(now) {
    const progress = Math.min((now - start) / duration, 1);
    const eased = 1 - Math.pow(1 - progress, 3);
    const current = endValue * eased;
    const formatted = Number.isInteger(endValue)
      ? Math.round(current).toString()
      : current.toFixed(1);
    el.textContent = prefix + (hasComma ? Number(formatted).toLocaleString() : formatted) + suffix;

    if (progress < 1) {
      requestAnimationFrame(frame);
    } else {
      el.textContent = target;
    }
  }

  requestAnimationFrame(frame);
}

export function initCounters() {
  const elements = document.querySelectorAll('[data-count-to]');
  if (!elements.length) return;

  if (!('IntersectionObserver' in window)) {
    elements.forEach(animateCount);
    return;
  }

  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (!entry.isIntersecting) return;
        animateCount(entry.target);
        observer.unobserve(entry.target);
      });
    },
    { threshold: 0.5 }
  );

  elements.forEach((el) => observer.observe(el));
}
