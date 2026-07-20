export function initNav() {
  const header = document.querySelector('[data-site-header]');
  const toggle = document.querySelector('[data-nav-toggle]');
  const mobileNav = document.querySelector('[data-mobile-nav]');

  if (header) {
    const onScroll = () => {
      header.classList.toggle('is-scrolled', window.scrollY > 40);
    };
    onScroll();
    window.addEventListener('scroll', onScroll, { passive: true });
  }

  if (toggle && mobileNav) {
    toggle.addEventListener('click', () => {
      const isOpen = mobileNav.classList.toggle('is-open');
      toggle.setAttribute('aria-expanded', String(isOpen));
      document.body.classList.toggle('nav-open', isOpen);
    });

    mobileNav.querySelectorAll('[data-mobile-submenu-toggle]').forEach((btn) => {
      btn.addEventListener('click', () => {
        const item = btn.closest('[data-mobile-nav-item]');
        const isOpen = item?.classList.toggle('is-open');
        btn.setAttribute('aria-expanded', String(!!isOpen));
      });
    });
  }
}
