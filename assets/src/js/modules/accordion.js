export function initAccordions() {
  document.querySelectorAll('[data-accordion]').forEach((accordion) => {
    const triggers = accordion.querySelectorAll('.accordion__trigger');

    triggers.forEach((trigger) => {
      const panel = trigger.nextElementSibling;
      const inner = panel?.querySelector('.accordion__panel-inner');

      if (panel && inner && trigger.getAttribute('aria-expanded') === 'true') {
        panel.style.maxHeight = `${inner.offsetHeight}px`;
      }

      trigger.addEventListener('click', () => {
        const isOpen = trigger.getAttribute('aria-expanded') === 'true';

        triggers.forEach((otherTrigger) => {
          if (otherTrigger === trigger) return;
          otherTrigger.setAttribute('aria-expanded', 'false');
          const otherPanel = otherTrigger.nextElementSibling;
          if (otherPanel) otherPanel.style.maxHeight = '0px';
        });

        trigger.setAttribute('aria-expanded', String(!isOpen));
        if (panel) {
          panel.style.maxHeight = isOpen ? '0px' : `${inner.offsetHeight}px`;
        }
      });
    });
  });
}
