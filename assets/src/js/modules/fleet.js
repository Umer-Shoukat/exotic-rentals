export function initFleetFilters() {
  const toggle = document.querySelector('[data-fleet-filter-toggle]');
  const filters = document.querySelector('[data-fleet-filters]');
  if (!toggle || !filters) return;

  const sort = document.querySelector('[data-fleet-sort]');
  const sortValue = filters.elements.fleet_sort;
  let results = document.querySelector('[data-fleet-results]');
  let requestController = null;
  let debounceTimer = null;

  toggle.addEventListener('click', () => {
    const isOpen = filters.classList.toggle('is-open');
    toggle.setAttribute('aria-expanded', String(isOpen));
  });

  const buildUrl = () => {
    if (sort && sortValue) sortValue.value = sort.value;
    const parameters = new URLSearchParams(new FormData(filters));
    Array.from(parameters.entries()).forEach(([key, value]) => {
      if (value === '') parameters.delete(key);
    });
    const query = parameters.toString();
    return `${filters.action}${query ? `?${query}` : ''}`;
  };

  const updateResults = async (url, pushState = true) => {
    requestController?.abort();
    requestController = new AbortController();
    results.classList.add('is-loading');
    results.setAttribute('aria-busy', 'true');

    try {
      const response = await fetch(url, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        signal: requestController.signal,
      });
      if (!response.ok) throw new Error(`Fleet request failed: ${response.status}`);
      const page = new DOMParser().parseFromString(await response.text(), 'text/html');
      const nextResults = page.querySelector('[data-fleet-results]');
      if (!nextResults) throw new Error('Fleet results were missing from the response.');
      results.replaceWith(nextResults);
      results = nextResults;
      if (pushState) window.history.pushState({}, '', url);
    } catch (error) {
      if (error.name !== 'AbortError') window.location.assign(url);
    } finally {
      results.classList.remove('is-loading');
      results.removeAttribute('aria-busy');
    }
  };

  const queueUpdate = (delay = 0) => {
    window.clearTimeout(debounceTimer);
    debounceTimer = window.setTimeout(() => updateResults(buildUrl()), delay);
  };

  filters.addEventListener('submit', (event) => {
    event.preventDefault();
    queueUpdate();
  });
  filters.addEventListener('change', () => queueUpdate());
  filters.querySelectorAll('input[type="number"]').forEach((input) => {
    input.addEventListener('input', () => queueUpdate(350));
  });
  sort?.addEventListener('change', () => queueUpdate());

  document.addEventListener('click', (event) => {
    const paginationLink = event.target.closest('[data-fleet-results] .page-numbers[href]');
    if (paginationLink) {
      event.preventDefault();
      updateResults(paginationLink.href);
      return;
    }
    const clearLink = event.target.closest('.fleet-filter-actions a');
    if (clearLink && filters.contains(clearLink)) {
      event.preventDefault();
      filters.reset();
      filters.querySelectorAll('input[type="hidden"]:not([name="fleet_sort"])').forEach((input) => { input.value = ''; });
      if (sort) sort.value = 'recommended';
      updateResults(clearLink.href);
    }
  });

  window.addEventListener('popstate', () => window.location.reload());
}
