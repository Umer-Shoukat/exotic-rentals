(() => {
  const root = document.querySelector('[data-vehicle-gallery-admin]');
  if (!root || !window.wp?.media) return;

  const list = root.querySelector('[data-gallery-items]');
  const idsInput = root.querySelector('[data-gallery-ids]');
  const addButton = root.querySelector('[data-gallery-add]');
  let draggedItem = null;

  const syncIds = () => {
    idsInput.value = Array.from(list.querySelectorAll('[data-attachment-id]'))
      .map((item) => item.dataset.attachmentId)
      .join(',');
  };

  const bindItem = (item) => {
    item.querySelector('[data-gallery-remove]')?.addEventListener('click', () => {
      item.remove();
      syncIds();
    });
    item.addEventListener('dragstart', () => {
      draggedItem = item;
      item.classList.add('is-dragging');
    });
    item.addEventListener('dragend', () => {
      draggedItem = null;
      item.classList.remove('is-dragging');
      syncIds();
    });
  };

  list.querySelectorAll('[data-attachment-id]').forEach(bindItem);
  list.addEventListener('dragover', (event) => {
    event.preventDefault();
    const target = event.target.closest('[data-attachment-id]');
    if (!draggedItem || !target || target === draggedItem) return;
    const box = target.getBoundingClientRect();
    list.insertBefore(draggedItem, event.clientX < box.left + box.width / 2 ? target : target.nextSibling);
  });

  addButton.addEventListener('click', () => {
    const frame = wp.media({
      title: 'Select vehicle gallery images',
      button: { text: 'Add to gallery' },
      library: { type: 'image' },
      multiple: true,
    });

    frame.on('select', () => {
      const existing = new Set(Array.from(list.querySelectorAll('[data-attachment-id]')).map((item) => item.dataset.attachmentId));
      frame.state().get('selection').toJSON().forEach((attachment) => {
        const id = String(attachment.id);
        if (existing.has(id)) return;
        const item = document.createElement('li');
        item.className = 'echelon-vehicle-gallery__item';
        item.dataset.attachmentId = id;
        item.draggable = true;
        const image = document.createElement('img');
        image.src = attachment.sizes?.thumbnail?.url || attachment.url;
        image.alt = attachment.alt || '';
        const remove = document.createElement('button');
        remove.type = 'button';
        remove.className = 'button-link-delete';
        remove.dataset.galleryRemove = '';
        remove.setAttribute('aria-label', 'Remove image');
        remove.textContent = '×';
        item.append(image, remove);
        list.append(item);
        existing.add(id);
        bindItem(item);
      });
      syncIds();
    });

    frame.open();
  });
})();
