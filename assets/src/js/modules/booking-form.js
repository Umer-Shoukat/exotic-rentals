import flatpickr from 'flatpickr';
import 'flatpickr/dist/themes/dark.css';

export function initBookingForm() {
  const inputs = document.querySelectorAll('[data-datepicker]');
  const today = new Date();

  inputs.forEach((input) => {
    flatpickr(input, {
      dateFormat: 'd/m/Y',
      minDate: today,
      disableMobile: true,
    });
  });

  document.querySelectorAll('input[type="time"]').forEach((input) => {
    input.addEventListener('click', () => {
      if (typeof input.showPicker !== 'function') return;

      try {
        input.showPicker();
      } catch {
        // Some browsers only allow showPicker during trusted user activation.
      }
    });
  });

  document.querySelectorAll('[data-vehicle-combobox]').forEach((combobox) => {
    const search = combobox.querySelector('[data-vehicle-search]');
    const value = combobox.querySelector('[data-vehicle-value]');
    const results = combobox.querySelector('[data-vehicle-results]');
    const empty = combobox.querySelector('[data-vehicle-empty]');
    const options = Array.from(combobox.querySelectorAll('[data-vehicle-option]'));
    if (!search || !value || !results || !options.length) return;

    let visibleOptions = [];
    let activeIndex = -1;

    const setOpen = (open) => {
      results.hidden = !open;
      search.setAttribute('aria-expanded', String(open));
      if (!open) {
        activeIndex = -1;
        search.removeAttribute('aria-activedescendant');
        options.forEach((option) => option.classList.remove('is-active'));
      }
    };

    const setActive = (index) => {
      if (!visibleOptions.length) return;
      activeIndex = (index + visibleOptions.length) % visibleOptions.length;
      visibleOptions.forEach((option, optionIndex) => option.classList.toggle('is-active', optionIndex === activeIndex));
      const active = visibleOptions[activeIndex];
      search.setAttribute('aria-activedescendant', active.id);
      active.scrollIntoView({ block: 'nearest' });
    };

    const filterOptions = () => {
      const query = search.value.trim().toLowerCase();
      const matches = options.filter((option) => !query || option.dataset.vehicleSearchText.includes(query));
      visibleOptions = matches.slice(0, 4);
      options.forEach((option) => { option.hidden = !visibleOptions.includes(option); });
      empty.hidden = matches.length !== 0;
      activeIndex = -1;
      search.removeAttribute('aria-activedescendant');
    };

    const selectOption = (option) => {
      value.value = option.dataset.vehicleId;
      search.value = option.dataset.vehicleLabel;
      options.forEach((item) => item.setAttribute('aria-selected', String(item === option)));
      setOpen(false);
    };

    search.addEventListener('focus', () => { filterOptions(); setOpen(true); });
    search.addEventListener('click', () => { filterOptions(); setOpen(true); });
    search.addEventListener('input', () => {
      value.value = '';
      options.forEach((option) => option.setAttribute('aria-selected', 'false'));
      filterOptions();
      setOpen(true);
    });
    search.addEventListener('keydown', (event) => {
      if (event.key === 'ArrowDown') { event.preventDefault(); if (results.hidden) setOpen(true); setActive(activeIndex + 1); }
      if (event.key === 'ArrowUp') { event.preventDefault(); if (results.hidden) setOpen(true); setActive(activeIndex - 1); }
      if (event.key === 'Enter' && activeIndex >= 0) { event.preventDefault(); selectOption(visibleOptions[activeIndex]); }
      if (event.key === 'Escape') { event.preventDefault(); setOpen(false); }
    });
    options.forEach((option) => option.addEventListener('click', () => selectOption(option)));
    document.addEventListener('click', (event) => { if (!combobox.contains(event.target)) setOpen(false); });
    filterOptions();
  });
}
