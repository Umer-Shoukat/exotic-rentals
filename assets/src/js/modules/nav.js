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
    const focusableSelector = 'a[href], button:not([disabled])';

    const setOpen = (isOpen, returnFocus = false) => {
      mobileNav.classList.toggle('is-open', isOpen);
      toggle.setAttribute('aria-expanded', String(isOpen));
      mobileNav.setAttribute('aria-hidden', String(!isOpen));
      mobileNav.inert = !isOpen;
      document.body.classList.toggle('nav-open', isOpen);

      if (isOpen) {
        mobileNav.querySelector(focusableSelector)?.focus();
      } else if (returnFocus) {
        toggle.focus();
      }
    };

    setOpen(false);

    toggle.addEventListener('click', () => {
      setOpen(!mobileNav.classList.contains('is-open'), true);
    });

    mobileNav.addEventListener('click', (event) => {
      if (event.target.closest('a[href]')) {
        setOpen(false);
      }
    });

    document.addEventListener('keydown', (event) => {
      if (!mobileNav.classList.contains('is-open')) return;

      if (event.key === 'Escape') {
        event.preventDefault();
        setOpen(false, true);
        return;
      }

      if (event.key !== 'Tab') return;

      const focusable = [...mobileNav.querySelectorAll(focusableSelector)];
      focusable.push(toggle);
      if (!focusable.length) return;

      const first = focusable[0];
      const last = focusable[focusable.length - 1];
      if (event.shiftKey && document.activeElement === first) {
        event.preventDefault();
        last.focus();
      } else if (!event.shiftKey && document.activeElement === last) {
        event.preventDefault();
        first.focus();
      }
    });

    window.addEventListener('resize', () => {
      if (window.innerWidth >= 1024 && mobileNav.classList.contains('is-open')) {
        setOpen(false);
      }
    }, { passive: true });

    mobileNav.querySelectorAll('[data-mobile-submenu-toggle]').forEach((btn) => {
      btn.addEventListener('click', () => {
        const item = btn.closest('[data-mobile-nav-item]');
        const isOpen = item?.classList.toggle('is-open');
        btn.setAttribute('aria-expanded', String(!!isOpen));
      });
    });
  }

  const megaRoots = [...document.querySelectorAll('[data-mega-menu-root]')];
  const closeMegaMenus = (except = null) => {
    megaRoots.forEach((root) => {
      if (root === except) return;
      root.classList.remove('is-open');
      root.querySelector('[data-mega-menu-trigger]')?.setAttribute('aria-expanded', 'false');
    });
  };

  megaRoots.forEach((root) => {
    const trigger = root.querySelector('[data-mega-menu-trigger]');
    const panel = root.querySelector('[data-mega-menu-panel]');
    if (!trigger) return;
    let closeTimer = null;
    const openMega = () => {
      clearTimeout(closeTimer);
      alignMegaAnchor();
      closeMegaMenus(root);
      root.classList.add('is-open');
      trigger.setAttribute('aria-expanded', 'true');
    };
    const closeMega = () => {
      clearTimeout(closeTimer);
      root.classList.remove('is-open');
      trigger.setAttribute('aria-expanded', 'false');
    };
    const queueCloseMega = () => {
      clearTimeout(closeTimer);
      closeTimer = window.setTimeout(closeMega, 160);
    };
    const alignMegaAnchor = () => {
      if (!panel) return;
      const triggerRect = trigger.getBoundingClientRect();
      panel.style.setProperty('--mega-menu-anchor-x', `${triggerRect.left + (triggerRect.width / 2)}px`);
    };
    alignMegaAnchor();
    root.addEventListener('mouseenter', openMega);
    root.addEventListener('mouseleave', queueCloseMega);
    if (panel) {
      panel.addEventListener('mouseenter', openMega);
      panel.addEventListener('mouseleave', queueCloseMega);
    }
    root.addEventListener('focusin', openMega);
    root.addEventListener('focusout', (event) => { if (!root.contains(event.relatedTarget)) closeMega(); });
    trigger.addEventListener('click', (event) => {
      if (window.innerWidth < 1024) return;
      if (!root.classList.contains('is-open')) {
        event.preventDefault();
        openMega();
      }
    });
    window.addEventListener('resize', alignMegaAnchor, { passive: true });
  });

  document.addEventListener('keydown', (event) => { if (event.key === 'Escape') { closeMegaMenus(); document.activeElement?.blur(); } });
  document.addEventListener('click', (event) => { if (!event.target.closest('[data-mega-menu-root]')) closeMegaMenus(); });
}
