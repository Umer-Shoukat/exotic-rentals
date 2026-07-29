import Swiper from 'swiper';
import { Navigation } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/navigation';

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

function initInstagramSlider() {
  const el = document.querySelector('.instagram-feed__slider[data-swiper]');
  if (!el) return;

  const slides = el.querySelectorAll('.swiper-slide');
  const shouldLoop = slides.length > 1;

  new Swiper(el, {
    slidesPerView: 'auto',
    centeredSlides: true,
    loop: shouldLoop,
    loopPreventsSliding: false,
    initialSlide: Math.floor(slides.length / 2),
    spaceBetween: 16,
    a11y: { enabled: true },
  });
}

export function initSliders() {
  initChooseRideSliders();
  initInstagramSlider();
}
