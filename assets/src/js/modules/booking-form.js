import flatpickr from 'flatpickr';
import 'flatpickr/dist/themes/dark.css';

export function initBookingForm() {
  const inputs = document.querySelectorAll('[data-datepicker]');
  if (!inputs.length) return;

  const today = new Date();

  inputs.forEach((input) => {
    flatpickr(input, {
      dateFormat: 'd/m/Y',
      minDate: today,
      disableMobile: true,
    });
  });
}
