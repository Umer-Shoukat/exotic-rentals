import flatpickr from 'flatpickr';

export function initReservationFlow() {
  const root = document.querySelector('[data-reservation-flow]');
  const form = root?.querySelector('.reservation__form');
  if (!form) return;

  const steps = Array.from(form.querySelectorAll('[data-step]'));
  const progress = Array.from(form.querySelectorAll('[data-progress-step]'));
  const back = form.querySelector('[data-reservation-back]');
  const next = form.querySelector('[data-reservation-next]');
  let current = 1;

  const pickup = form.elements.pickup_date;
  const returnDate = form.elements.return_date;
  const pickupPicker = flatpickr(pickup, { dateFormat: 'd/m/Y', minDate: 'today', disableMobile: true });
  const returnPicker = flatpickr(returnDate, { dateFormat: 'd/m/Y', minDate: new Date(Date.now() + 86400000), disableMobile: true });
  pickup.addEventListener('change', () => {
    if (pickupPicker.selectedDates[0]) {
      const minimum = new Date(pickupPicker.selectedDates[0]);
      minimum.setDate(minimum.getDate() + 1);
      returnPicker.set('minDate', minimum);
    }
    updateSummary();
  });
  returnDate.addEventListener('change', updateSummary);
  flatpickr(form.elements.date_of_birth, { dateFormat: 'd/m/Y', maxDate: 'today', disableMobile: true });

  const selectedVehicle = () => form.querySelector('input[name="vehicle_id"]:checked');
  const parseDate = (value) => {
    const [day, month, year] = value.split('/').map(Number);
    return day && month && year ? new Date(year, month - 1, day) : null;
  };
  const selectedText = (control) => control?.options?.[control.selectedIndex]?.text || '—';
  const currency = (value) => new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD', maximumFractionDigits: 0 }).format(value || 0);

  function updateSummary() {
    const vehicle = selectedVehicle();
    if (!vehicle) return;
    const image = root.querySelector('[data-summary-image]');
    image.src = vehicle.dataset.vehicleImage || '';
    image.closest('.reservation-summary__media').hidden = !vehicle.dataset.vehicleImage;
    root.querySelector('[data-summary-vehicle]').textContent = vehicle.dataset.vehicleName;
    const start = parseDate(pickup.value);
    const end = parseDate(returnDate.value);
    const days = start && end ? Math.max(0, Math.round((end - start) / 86400000)) : 0;
    root.querySelector('[data-summary-dates]').textContent = pickup.value && returnDate.value ? `${pickup.value} → ${returnDate.value}` : '—';
    root.querySelector('[data-summary-days]').textContent = days || '—';
    root.querySelector('[data-summary-pickup]').textContent = selectedText(form.elements.pickup_location_id);
    root.querySelector('[data-summary-return]').textContent = selectedText(form.elements.return_location_id);
    root.querySelector('[data-summary-total]').textContent = days ? currency(Number(vehicle.dataset.vehiclePrice) * days) : currency(Number(vehicle.dataset.vehiclePrice));
  }

  function updateReview() {
    const vehicle = selectedVehicle();
    const days = Math.max(1, Math.round((parseDate(returnDate.value) - parseDate(pickup.value)) / 86400000));
    const total = Number(vehicle.dataset.vehiclePrice) * days;
    root.querySelector('[data-reservation-review]').innerHTML = `
      <div><span>Vehicle</span><strong>${escapeHtml(vehicle.dataset.vehicleName)}</strong></div>
      <div><span>Dates</span><strong>${escapeHtml(pickup.value)} → ${escapeHtml(returnDate.value)}</strong></div>
      <div><span>Name</span><strong>${escapeHtml(form.elements.customer_name.value)}</strong></div>
      <div><span>Contact</span><strong>${escapeHtml(form.elements.customer_phone.value)}</strong></div>
      <div class="reservation-review__total"><span>Estimated total</span><strong>${currency(total)}</strong></div>`;
  }

  function escapeHtml(value) {
    const node = document.createElement('span');
    node.textContent = value || '';
    return node.innerHTML;
  }

  function showStep(step) {
    current = Math.min(4, Math.max(1, step));
    steps.forEach((panel) => { panel.hidden = Number(panel.dataset.step) !== current; panel.classList.toggle('is-active', Number(panel.dataset.step) === current); });
    progress.forEach((item) => { const number = Number(item.dataset.progressStep); item.classList.toggle('is-active', number === current); item.classList.toggle('is-complete', number < current); });
    back.hidden = current === 1;
    next.hidden = current === 4;
    if (current === 4) updateReview();
    updateSummary();
    root.querySelector(`[data-step="${current}"] h2`)?.focus({ preventScroll: true });
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }

  function validateStep() {
    const fields = Array.from(steps[current - 1].querySelectorAll('input, select'));
    for (const field of fields) {
      if (!field.checkValidity()) { field.reportValidity(); return false; }
    }
    if (current === 2) {
      const start = parseDate(pickup.value); const end = parseDate(returnDate.value);
      if (!start || !end || end <= start) { returnDate.setCustomValidity('Return must be after pick-up.'); returnDate.reportValidity(); return false; }
      returnDate.setCustomValidity('');
    }
    return true;
  }

  form.querySelectorAll('input[name="vehicle_id"]').forEach((radio) => radio.addEventListener('change', () => {
    form.querySelectorAll('[data-vehicle-card]').forEach((card) => card.classList.toggle('is-selected', card.contains(radio)));
    updateSummary();
  }));
  [form.elements.pickup_location_id, form.elements.return_location_id].forEach((select) => select.addEventListener('change', updateSummary));
  next.addEventListener('click', () => { if (validateStep()) showStep(current + 1); });
  back.addEventListener('click', () => showStep(current - 1));
  form.addEventListener('submit', (event) => { if (!validateStep()) event.preventDefault(); });
  showStep(1);
}
