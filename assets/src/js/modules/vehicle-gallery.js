export function initVehicleGallery() {
  document.querySelectorAll('[data-vehicle-gallery]').forEach((gallery) => {
    const hero = gallery.querySelector('[data-gallery-hero]');
    const lightbox = gallery.querySelector('[data-gallery-lightbox]');
    const lightboxImage = gallery.querySelector('[data-gallery-lightbox-image]');
    const count = gallery.querySelector('[data-gallery-count]');
    const thumbs = Array.from(gallery.querySelectorAll('[data-gallery-thumb]'));
    if (!hero || !thumbs.length) return;

    let activeIndex = Math.max(0, thumbs.findIndex((thumb) => thumb.classList.contains('is-active')));

    const setActive = (index) => {
      activeIndex = (index + thumbs.length) % thumbs.length;
      const button = thumbs[activeIndex];
      const source = button.dataset.gallerySrc;
      if (!source) return;

      const alt = button.querySelector('img')?.alt || '';
      hero.src = source;
      hero.alt = alt;
      if (lightboxImage) {
        lightboxImage.src = source;
        lightboxImage.alt = alt;
      }
      if (count) count.textContent = `${activeIndex + 1} / ${thumbs.length}`;
      thumbs.forEach((item, thumbIndex) => item.classList.toggle('is-active', thumbIndex === activeIndex));
    };

    const move = (direction) => setActive(activeIndex + direction);
    const openLightbox = () => {
      if (!lightbox) return;
      setActive(activeIndex);
      lightbox.hidden = false;
      document.body.classList.add('has-gallery-lightbox');
      lightbox.querySelector('[data-gallery-close]')?.focus({ preventScroll: true });
    };
    const closeLightbox = () => {
      if (!lightbox || lightbox.hidden) return;
      lightbox.hidden = true;
      document.body.classList.remove('has-gallery-lightbox');
      gallery.querySelector('[data-gallery-open]')?.focus({ preventScroll: true });
    };

    thumbs.forEach((button, index) => {
      button.addEventListener('click', () => {
        setActive(index);
      });
    });

    gallery.querySelectorAll('[data-gallery-prev]').forEach((button) => button.addEventListener('click', () => move(-1)));
    gallery.querySelectorAll('[data-gallery-next]').forEach((button) => button.addEventListener('click', () => move(1)));
    gallery.querySelector('[data-gallery-open]')?.addEventListener('click', openLightbox);
    gallery.querySelector('[data-gallery-close]')?.addEventListener('click', closeLightbox);
    lightbox?.addEventListener('click', (event) => {
      if (event.target === lightbox) closeLightbox();
    });

    document.addEventListener('keydown', (event) => {
      if (!lightbox || lightbox.hidden) return;
      if (event.key === 'Escape') closeLightbox();
      if (event.key === 'ArrowLeft') move(-1);
      if (event.key === 'ArrowRight') move(1);
    });

    setActive(activeIndex);
  });
}
