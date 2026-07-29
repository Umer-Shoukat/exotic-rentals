const reducedMotion = () =>
  window.matchMedia('(prefers-reduced-motion: reduce)').matches;

function initSectionProgress() {
  const sections = [...document.querySelectorAll('[data-scroll-progress-section]')];
  if (!sections.length) return;

  let ticking = false;
  const update = () => {
    sections.forEach((section) => {
      const progress = section.querySelector('[data-scroll-progress]');
      if (!progress) return;

      const rect = section.getBoundingClientRect();
      const start = window.innerHeight * 0.72;
      const distance = rect.height + start - (window.innerHeight * 0.28);
      const value = Math.max(0, Math.min(1, (start - rect.top) / distance));
      progress.style.transform = `scaleY(${value.toFixed(4)})`;
      progress.setAttribute('aria-valuenow', String(Math.round(value * 100)));
    });
    ticking = false;
  };

  const requestUpdate = () => {
    if (ticking) return;
    ticking = true;
    requestAnimationFrame(update);
  };

  window.addEventListener('scroll', requestUpdate, { passive: true });
  window.addEventListener('resize', requestUpdate, { passive: true });
  update();
}

function initConciergeChat() {
  const chat = document.querySelector('[data-concierge-chat]');
  if (!chat) return;

  const messages = [...chat.querySelectorAll('[data-chat-message]')];
  const typing = chat.querySelector('[data-chat-typing]');
  const replay = chat.querySelector('[data-chat-replay]');
  if (!messages.length) return;

  let timers = [];
  let hasPlayed = false;
  let observer = null;

  const clear = () => {
    timers.forEach(window.clearTimeout);
    timers = [];
  };

  const stopWatching = () => {
    observer?.disconnect();
    window.removeEventListener('scroll', checkViewport);
    window.removeEventListener('resize', checkViewport);
  };

  const isNearViewport = () => {
    const rect = chat.getBoundingClientRect();
    return rect.top < window.innerHeight * 0.9 && rect.bottom > window.innerHeight * 0.1;
  };

  function checkViewport() {
    if (!isNearViewport()) return;
    start();
  }

  const showAll = () => {
    messages.forEach((message) => message.classList.add('is-visible'));
    typing?.classList.remove('is-visible');
  };

  const play = () => {
    clear();
    messages.forEach((message) => message.classList.remove('is-visible'));
    typing?.classList.remove('is-visible');

    if (reducedMotion()) {
      showAll();
      return;
    }

    messages.forEach((message, index) => {
      timers.push(window.setTimeout(() => {
        typing?.classList.toggle('is-visible', index > 0);
      }, Math.max(0, (index * 1300) - 650)));
      timers.push(window.setTimeout(() => {
        typing?.classList.remove('is-visible');
        message.classList.add('is-visible');
      }, index * 1300));
    });
  };

  function start() {
    if (hasPlayed) return;
    hasPlayed = true;
    stopWatching();
    play();
  }

  replay?.addEventListener('click', play);

  if ('IntersectionObserver' in window) {
    observer = new IntersectionObserver((entries) => {
      if (!entries.some((entry) => entry.isIntersecting)) return;
      start();
    }, { threshold: 0.05, rootMargin: '0px 0px -10% 0px' });
    observer.observe(chat);

    window.addEventListener('scroll', checkViewport, { passive: true });
    window.addEventListener('resize', checkViewport, { passive: true });
    checkViewport();
  } else {
    play();
  }
}

export function initHomepageMotion() {
  initSectionProgress();
  initConciergeChat();
}
