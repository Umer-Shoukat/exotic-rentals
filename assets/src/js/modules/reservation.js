import flatpickr from 'flatpickr';

export function initReservationFlow() {
  const root = document.querySelector('[data-reservation-flow]');
  const form = root?.querySelector('.reservation__form');
  if (!form) return;

  const steps = Array.from(form.querySelectorAll('[data-step]'));
  const progress = Array.from(form.querySelectorAll('[data-progress-step]'));
  const actions = form.querySelector('.reservation-actions');
  const backButtons = Array.from(form.querySelectorAll('[data-reservation-back]'));
  const nextButtons = Array.from(form.querySelectorAll('[data-reservation-next]'));
  let current = Number(root.dataset.initialStep) === 2 ? 2 : 1;

  const pickup = form.elements.pickup_date;
  const returnDate = form.elements.return_date;
  const pickupTime = form.elements.pickup_time;
  const returnTime = form.elements.return_time;
  const pickupPicker = flatpickr(pickup, { dateFormat: 'd/m/Y', minDate: 'today', disableMobile: true });
  const returnPicker = flatpickr(returnDate, { dateFormat: 'd/m/Y', minDate: 'today', disableMobile: true });
  [pickupTime, returnTime].forEach((input) => flatpickr(input, {
    enableTime: true,
    noCalendar: true,
    dateFormat: 'H:i',
    altInput: true,
    altFormat: 'h:i K',
    minuteIncrement: 30,
    disableMobile: true,
    onChange: updateSummary,
  }));
  pickup.addEventListener('change', () => {
    if (pickupPicker.selectedDates[0]) {
      const minimum = new Date(pickupPicker.selectedDates[0]);
      returnPicker.set('minDate', minimum);
    }
    updateSummary();
  });
  returnDate.addEventListener('change', updateSummary);
  flatpickr(form.elements.date_of_birth, { dateFormat: 'd/m/Y', maxDate: 'today', disableMobile: true });

  const selectedVehicle = () => form.querySelector('input[name="vehicle_id"]:checked');
  const parseDate = (value) => {
    const [day, month, year] = value.split('/').map(Number);
    if (!day || !month || !year) return null;
    const date = new Date(year, month - 1, day);
    return date.getFullYear() === year && date.getMonth() === month - 1 && date.getDate() === day ? date : null;
  };
  const selectedText = (control) => control?.options?.[control.selectedIndex]?.text || '—';
  const currency = (value) => new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD', maximumFractionDigits: 0 }).format(value || 0);
  const dateTime = (dateValue, timeValue) => {
    const date = parseDate(dateValue);
    const [hours, minutes] = (timeValue || '').split(':').map(Number);
    if (!date || Number.isNaN(hours) || Number.isNaN(minutes)) return null;
    date.setHours(hours, minutes, 0, 0);
    return date;
  };
  const bookingHours = () => {
    const start = dateTime(pickup.value, pickupTime.value);
    const end = dateTime(returnDate.value, returnTime.value);
    return start && end ? Math.max(0, Math.ceil((end - start) / 3600000)) : 0;
  };

  function updateSummary() {
    const vehicle = selectedVehicle();
    if (!vehicle) return;
    const image = root.querySelector('[data-summary-image]');
    image.src = vehicle.dataset.vehicleImage || '';
    image.closest('.reservation-summary__media').hidden = !vehicle.dataset.vehicleImage;
    root.querySelector('[data-summary-vehicle]').textContent = vehicle.dataset.vehicleName;
    const hours = bookingHours();
    root.querySelector('[data-summary-dates]').textContent = pickup.value && returnDate.value && pickupTime.value && returnTime.value ? `${pickup.value} ${pickupTime.value} → ${returnDate.value} ${returnTime.value}` : '—';
    root.querySelector('[data-summary-hours]').textContent = hours || '—';
    root.querySelector('[data-summary-pickup]').textContent = selectedText(form.elements.pickup_location_id);
    root.querySelector('[data-summary-return]').textContent = selectedText(form.elements.return_location_id);
    root.querySelector('[data-summary-total]').textContent = hours ? currency(Number(vehicle.dataset.vehiclePrice) * hours) : currency(Number(vehicle.dataset.vehiclePrice) * Number(vehicle.dataset.vehicleMinimumHours || 3));
  }

  function updateReview() {
    const vehicle = selectedVehicle();
    const hours = bookingHours();
    const total = Number(vehicle.dataset.vehiclePrice) * hours;
    root.querySelector('[data-reservation-review]').innerHTML = `
      <div><span>Vehicle</span><strong>${escapeHtml(vehicle.dataset.vehicleName)}</strong></div>
      <div><span>Trip type</span><strong>${escapeHtml(selectedText(form.elements.trip_type))}</strong></div>
      <div><span>Hours required</span><strong>${escapeHtml(form.elements.hours_required.value)} hours</strong></div>
      <div><span>Schedule</span><strong>${escapeHtml(pickup.value)} ${escapeHtml(pickupTime.value)} → ${escapeHtml(returnDate.value)} ${escapeHtml(returnTime.value)}</strong></div>
      <div><span>Duration</span><strong>${hours} hours</strong></div>
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
    root.dataset.currentStep = String(current);
    steps.forEach((panel) => { panel.hidden = Number(panel.dataset.step) !== current; panel.classList.toggle('is-active', Number(panel.dataset.step) === current); });
    progress.forEach((item) => { const number = Number(item.dataset.progressStep); item.classList.toggle('is-active', number === current); item.classList.toggle('is-complete', number < current); });
    actions.hidden = current === 4;
    backButtons.forEach((button) => { button.hidden = current === 1; });
    nextButtons.forEach((button) => { button.hidden = current === 4; });
    if (current === 4) updateReview();
    updateSummary();
    root.querySelector(`[data-step="${current}"] h2`)?.focus({ preventScroll: true });
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }

  const today = () => {
    const date = new Date();
    return new Date(date.getFullYear(), date.getMonth(), date.getDate());
  };

  function fieldMessage(field) {
    const value = field.type === 'checkbox' ? field.checked : field.value.trim();
    if (field.type === 'file') {
      const file = field.files?.[0];
      if (!file) return 'Upload this required document.';
      if (file.size > 10 * 1024 * 1024) return 'Each document must be 10 MB or smaller.';
      const extensions = field.name === 'insurance_document' ? ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'] : ['jpg', 'jpeg', 'png'];
      const extension = file.name.split('.').pop()?.toLowerCase();
      if (!extensions.includes(extension)) return `Use one of these file types: ${extensions.join(', ').toUpperCase()}.`;
    }
    if (field.name === 'vehicle_id') return selectedVehicle() ? '' : 'Choose a vehicle to continue.';
    if (field.name === 'pickup_date') {
      if (!value) return 'Enter a pick-up date.';
      const date = parseDate(value);
      if (!date) return 'Enter a valid pick-up date in DD/MM/YYYY format.';
      if (date < today()) return 'Pick-up date cannot be in the past.';
    }
    if (field.name === 'return_date') {
      if (!value) return 'Enter a return date.';
      const end = parseDate(value); const start = parseDate(pickup.value);
      if (!end) return 'Enter a valid return date in DD/MM/YYYY format.';
      if (start && end < start) return 'Return date cannot be before the pick-up date.';
    }
    if (field.name === 'pickup_time' || field.name === 'return_time') {
      if (!value) return `Enter a ${field.name === 'pickup_time' ? 'pick-up' : 'return'} time.`;
      const start = dateTime(pickup.value, pickupTime.value);
      const end = dateTime(returnDate.value, returnTime.value);
      if (start && end) {
        const minimum = Number(selectedVehicle()?.dataset.vehicleMinimumHours || 3);
        if (end <= start) return 'Return must be after pick-up.';
        if ((end - start) / 3600000 < minimum) return `This vehicle requires a minimum booking of ${minimum} hours.`;
      }
    }
    if ((field.name === 'pickup_location_id' || field.name === 'return_location_id') && !value) return 'Select a location.';
    if (field.name === 'trip_type' && !value) return 'Select a trip type.';
    if (field.name === 'hours_required' && (!Number.isInteger(Number(value)) || Number(value) < 3 || Number(value) > 24)) return 'Select the number of hours required.';
    if (field.name === 'customer_name' && value.length < 2) return 'Enter your full name.';
    if (field.name === 'customer_email' && (!value || !field.validity.valid)) return 'Enter a valid email address.';
    if (field.name === 'customer_phone') {
      const digits = value.replace(/\D/g, '');
      if (digits.length < 7 || digits.length > 15) return 'Enter a valid phone number with 7 to 15 digits.';
    }
    if (field.name === 'licence_number' && value.length < 4) return 'Enter a valid driving licence number.';
    if (field.name === 'date_of_birth' && value) {
      const birth = parseDate(value);
      if (!birth || birth >= today()) return 'Enter a valid date of birth.';
      let age = today().getFullYear() - birth.getFullYear();
      const birthdayPending = today().getMonth() < birth.getMonth() || (today().getMonth() === birth.getMonth() && today().getDate() < birth.getDate());
      if (birthdayPending) age -= 1;
      if (age < 25) return 'Drivers must be at least 25 years old.';
    }
    if (field.name === 'terms_accepted' && !field.checked) return 'Accept the rental terms and insurance policy to continue.';
    if (field.required && !value) return 'This field is required.';
    return '';
  }

  function clearFieldError(field) {
    const visibleField = field._flatpickr?.altInput || field;
    field.removeAttribute('aria-invalid');
    visibleField.removeAttribute('aria-invalid');
    const errorId = `reservation-error-${field.name}`;
    form.querySelector(`#${errorId}`)?.remove();
    if (field.getAttribute('aria-describedby') === errorId) field.removeAttribute('aria-describedby');
    if (visibleField.getAttribute('aria-describedby') === errorId) visibleField.removeAttribute('aria-describedby');
    field.closest('label')?.classList.remove('has-error');
  }

  function showFieldError(field, message) {
    clearFieldError(field);
    const error = document.createElement('span');
    error.className = 'reservation-field-error';
    error.id = `reservation-error-${field.name}`;
    error.textContent = message;
    const visibleField = field._flatpickr?.altInput || field;
    field.setAttribute('aria-invalid', 'true');
    visibleField.setAttribute('aria-invalid', 'true');
    visibleField.setAttribute('aria-describedby', error.id);
    const label = field.closest('label');
    label?.classList.add('has-error');
    (label || field.parentElement).appendChild(error);
  }

  function showValidationSummary(panel, messages) {
    panel.querySelector('[data-validation-summary]')?.remove();
    if (!messages.length) return;
    const summary = document.createElement('div');
    summary.className = 'reservation-validation-summary';
    summary.dataset.validationSummary = '';
    summary.setAttribute('role', 'alert');
    const heading = document.createElement('strong');
    heading.textContent = 'Please correct the following:';
    const list = document.createElement('ul');
    messages.forEach((message) => { const item = document.createElement('li'); item.textContent = message; list.appendChild(item); });
    summary.append(heading, list);
    panel.querySelector('.reservation-step__heading')?.after(summary);
  }

  function validateStep(stepNumber = current, shouldFocus = true) {
    const panel = steps[stepNumber - 1];
    const fields = Array.from(panel.querySelectorAll('input, select')).filter((field, index, all) => field.name !== 'vehicle_id' || index === all.findIndex((candidate) => candidate.name === 'vehicle_id'));
    const invalid = [];
    fields.forEach((field) => {
      clearFieldError(field);
      const message = fieldMessage(field);
      if (message) { invalid.push({ field, message }); showFieldError(field, message); }
    });
    showValidationSummary(panel, invalid.map(({ message }) => message));
    if (invalid.length && shouldFocus) (invalid[0].field._flatpickr?.altInput || invalid[0].field).focus({ preventScroll: true });
    return invalid.length === 0;
  }

  form.querySelectorAll('input[name="vehicle_id"]').forEach((radio) => radio.addEventListener('change', () => {
    form.querySelectorAll('[data-vehicle-card]').forEach((card) => card.classList.toggle('is-selected', card.contains(radio)));
    updateSummary();
  }));
  [pickupTime, returnTime, form.elements.pickup_location_id, form.elements.return_location_id].forEach((control) => control.addEventListener('change', updateSummary));
  form.querySelectorAll('input, select').forEach((field) => {
    const eventName = field.type === 'radio' || field.type === 'checkbox' || field.tagName === 'SELECT' ? 'change' : 'input';
    field.addEventListener(eventName, () => {
      clearFieldError(field);
      const panel = field.closest('[data-step]');
      if (panel && !panel.querySelector('[aria-invalid="true"]')) panel.querySelector('[data-validation-summary]')?.remove();
    });
  });
  form.addEventListener('click', (event) => {
    const backButton = event.target.closest('[data-reservation-back]');
    if (backButton) {
      event.preventDefault();
      showStep(current - 1);
      return;
    }
    const nextButton = event.target.closest('[data-reservation-next]');
    if (nextButton) {
      event.preventDefault();
      if (validateStep()) showStep(current + 1);
    }
  });
  form.addEventListener('submit', (event) => {
    const firstInvalidStep = steps.findIndex((step, index) => !validateStep(index + 1, false));
    if (firstInvalidStep >= 0) {
      event.preventDefault();
      showStep(firstInvalidStep + 1);
      const invalidField = steps[firstInvalidStep].querySelector('[aria-invalid="true"]');
      (invalidField?._flatpickr?.altInput || invalidField)?.focus({ preventScroll: true });
    }
  });
  showStep(current);
}
