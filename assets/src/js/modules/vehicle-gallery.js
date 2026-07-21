export function initVehicleGallery() {
  document.querySelectorAll('[data-vehicle-gallery]').forEach((gallery) => {
    const hero = gallery.querySelector('[data-gallery-hero]');
    if (!hero) return;

    gallery.querySelectorAll('[data-gallery-thumb]').forEach((button) => {
      button.addEventListener('click', () => {
        const source = button.dataset.gallerySrc;
        if (!source) return;
        hero.src = source;
        hero.alt = button.querySelector('img')?.alt || '';
        gallery.querySelectorAll('[data-gallery-thumb]').forEach((item) => item.classList.remove('is-active'));
        button.classList.add('is-active');
      });
    });
  });
}
