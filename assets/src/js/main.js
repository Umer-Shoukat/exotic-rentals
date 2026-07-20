import '@fontsource/anton/400.css';
import '@fontsource/inter/400.css';
import '@fontsource/inter/500.css';
import '@fontsource/inter/600.css';
import '@fontsource/inter/700.css';
import '../scss/main.scss';

import { initNav } from './modules/nav.js';
import { initReveal, initParallax } from './modules/reveal.js';
import { initCounters } from './modules/counters.js';
import { initAccordions } from './modules/accordion.js';
import { initSliders } from './modules/sliders.js';
import { initHeroStrip } from './modules/hero-strip.js';
import { initBookingForm } from './modules/booking-form.js';
import { initMap } from './modules/map.js';
import { initReservationFlow } from './modules/reservation.js';

document.addEventListener('DOMContentLoaded', () => {
  initNav();
  initReveal();
  initParallax();
  initCounters();
  initAccordions();
  initSliders();
  initHeroStrip();
  initBookingForm();
  initMap();
  initReservationFlow();
});
