export function initTabs(onPanelActivated) {
  document.querySelectorAll('[data-tabs]').forEach((tabList) => {
    const buttons = tabList.querySelectorAll('[data-tab-target]');
    const section = tabList.closest('section');
    if (!section) return;

    // Keep the component usable even when cached server markup does not carry
    // an initial active class. The first tab is the deterministic default.
    if (!tabList.querySelector('.is-active') && buttons[0]) {
      const initialTarget = buttons[0].dataset.tabTarget;
      buttons[0].classList.add('is-active');
      buttons[0].setAttribute('aria-selected', 'true');
      section.querySelectorAll('[data-tab-panel]').forEach((panel) => {
        panel.classList.toggle('is-active', panel.id === initialTarget);
      });
    }

    buttons.forEach((button) => {
      button.addEventListener('click', () => {
        if (button.classList.contains('is-active')) return;

        const targetId = button.dataset.tabTarget;

        buttons.forEach((btn) => {
          btn.classList.remove('is-active');
          btn.setAttribute('aria-selected', 'false');
        });
        button.classList.add('is-active');
        button.setAttribute('aria-selected', 'true');

        section.querySelectorAll('[data-tab-panel]').forEach((panel) => {
          const isTarget = panel.id === targetId;
          panel.classList.toggle('is-active', isTarget);
          if (isTarget && typeof onPanelActivated === 'function') {
            onPanelActivated(panel);
          }
        });
      });
    });
  });
}
