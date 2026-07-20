import Swiper from 'swiper';
import { Navigation, Pagination, Grid } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';
import 'swiper/css/grid';

import { initTabs } from './tabs.js';

function initVehicleSlider(panel) {
  const el = panel.querySelector('[data-swiper]');
  if (!el || el.swiper) return;

  new Swiper(el, {
    modules: [Navigation],
    slidesPerView: 'auto',
    spaceBetween: 24,
    navigation: {
      prevEl: panel.querySelector('[data-swiper-prev]'),
      nextEl: panel.querySelector('[data-swiper-next]'),
    },
    a11y: { enabled: true },
  });
}

function initChooseRideSliders() {
  const activePanel = document.querySelector('.choose-ride__panel.is-active');
  if (activePanel) initVehicleSlider(activePanel);

  initTabs((panel) => initVehicleSlider(panel));
}

function initTestimonialsSlider() {
  const el = document.querySelector('.testimonials__slider[data-swiper]');
  if (!el) return;

  new Swiper(el, {
    modules: [Pagination, Grid],
    slidesPerView: 3,
    grid: { rows: 2, fill: 'column' },
    spaceBetween: 16,
    pagination: {
      el: document.querySelector('[data-swiper-pagination]'),
      clickable: true,
      bulletClass: 'dots__dot',
      bulletActiveClass: 'is-active',
    },
    breakpoints: {
      0: { slidesPerView: 1, grid: { rows: 3, fill: 'column' } },
      640: { slidesPerView: 2, grid: { rows: 2, fill: 'column' } },
      1024: { slidesPerView: 3, grid: { rows: 2, fill: 'column' } },
    },
    a11y: { enabled: true },
  });
}

function initInstagramSlider() {
  const el = document.querySelector('.instagram-feed__slider[data-swiper]');
  if (!el) return;

  const slides = el.querySelectorAll('.swiper-slide');

  new Swiper(el, {
    slidesPerView: 'auto',
    centeredSlides: true,
    initialSlide: Math.floor(slides.length / 2),
    spaceBetween: 16,
    a11y: { enabled: true },
  });
}

export function initSliders() {
  initChooseRideSliders();
  initTestimonialsSlider();
  initInstagramSlider();
}
