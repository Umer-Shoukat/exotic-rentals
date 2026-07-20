export function initMap() {
  document.querySelectorAll('[data-city-map]').forEach((section) => {
    const pins = Array.from(section.querySelectorAll('[data-city-pin]'));
    const cards = Array.from(section.querySelectorAll('[data-city-card]'));
    if (!pins.length) return;

    const selectLocation = (locationId, shouldFocusPin = false) => {
      pins.forEach((pin) => {
        const selected = pin.dataset.locationId === locationId;
        pin.classList.toggle('is-open', selected);
        pin.setAttribute('aria-expanded', String(selected));
        if (selected && shouldFocusPin) pin.focus({ preventScroll: true });
      });

      cards.forEach((card) => {
        const selected = card.dataset.locationId === locationId;
        card.classList.toggle('is-active', selected);
        card.setAttribute('aria-pressed', String(selected));
      });
    };

    pins.forEach((pin) => pin.addEventListener('click', (event) => {
      event.stopPropagation();
      selectLocation(pin.dataset.locationId);
    }));

    cards.forEach((card) => card.addEventListener('click', () => {
      selectLocation(card.dataset.locationId, true);
    }));

    section.addEventListener('keydown', (event) => {
      if (event.key === 'Escape') selectLocation('');
    });

    document.addEventListener('click', (event) => {
      if (!section.contains(event.target)) selectLocation('');
    });
  });
}
