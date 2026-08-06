import '@fontsource/anton/400.css';
import '@fontsource/lato/400.css';
import '@fontsource/lato/700.css';
import '@fontsource/lato/900.css';
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
import { initFleetFilters } from './modules/fleet.js';
import { initVehicleGallery } from './modules/vehicle-gallery.js';
import { initHomepageMotion } from './modules/homepage-motion.js';
import { initTimePickers } from './modules/time-picker.js';

document.addEventListener('DOMContentLoaded', () => {
  initNav();
  initReveal();
  initParallax();
  initCounters();
  initAccordions();
  initSliders();
  initHeroStrip();
  initBookingForm();
  initTimePickers();
  initMap();
  initReservationFlow();
  initFleetFilters();
  initVehicleGallery();
  initHomepageMotion();
});
