export function initFleetFilters() {
  const toggle = document.querySelector('[data-fleet-filter-toggle]');
  const filters = document.querySelector('[data-fleet-filters]');
  if (!toggle || !filters) return;

  toggle.addEventListener('click', () => {
    const isOpen = filters.classList.toggle('is-open');
    toggle.setAttribute('aria-expanded', String(isOpen));
  });
}
