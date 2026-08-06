const pad = (value) => String(value).padStart(2, '0');

function parseTime(value) {
  const match = String(value || '').match(/^(\d{1,2}):(\d{2})$/);
  if (!match) return null;
  const hours = Number(match[1]);
  const minutes = Number(match[2]);
  return hours <= 23 && minutes <= 59 ? { hours, minutes } : null;
}

function displayTime(hours, minutes) {
  const period = hours >= 12 ? 'PM' : 'AM';
  const hour = hours % 12 || 12;
  return `${pad(hour)}:${pad(minutes)} ${period}`;
}

const clockIcon = '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="12" r="8.5" stroke="currentColor" stroke-width="1.5"/><path d="M12 7.5V12L15 14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>';

function setTriggerContent(trigger, text) {
  trigger.innerHTML = `<span>${text}</span>${clockIcon}`;
}

function createColumn(label, values, selected, format) {
  const column = document.createElement('div');
  column.className = 'time-picker__column';
  column.setAttribute('role', 'listbox');
  column.setAttribute('aria-label', label);

  values.forEach((value) => {
    const option = document.createElement('button');
    option.type = 'button';
    option.className = 'time-picker__option';
    option.dataset.value = String(value);
    option.setAttribute('role', 'option');
    option.setAttribute('aria-selected', String(value === selected));
    option.textContent = format(value);
    if (value === selected) option.classList.add('is-selected');
    column.appendChild(option);
  });
  return column;
}

export function initTimePickers() {
  const inputs = document.querySelectorAll('[data-time-picker], input[type="time"]');
  const instances = [];

  inputs.forEach((input, index) => {
    if (input.dataset.timePickerReady === 'true') return;
    input.dataset.timePickerReady = 'true';
    const triggerClassName = input.className;
    input.type = 'text';
    input.classList.add('time-picker__native-input');
    input.tabIndex = -1;
    input.setAttribute('aria-hidden', 'true');

    const picker = document.createElement('div');
    picker.className = 'time-picker';
    const trigger = document.createElement('button');
    trigger.type = 'button';
    trigger.className = `${triggerClassName} time-picker__trigger`.trim();
    trigger.setAttribute('aria-haspopup', 'dialog');
    trigger.setAttribute('aria-expanded', 'false');
    const wrappingLabel = input.closest('label')?.querySelector(':scope > span');
    const associatedLabel = input.id ? document.querySelector(`label[for="${CSS.escape(input.id)}"]`) : null;
    const fieldLabel = (wrappingLabel || associatedLabel)?.textContent.trim().replace(/\s*\*$/, '') || 'Time';
    trigger.setAttribute('aria-label', input.getAttribute('aria-label') || `${fieldLabel}, choose time`);
    if (input.required) trigger.setAttribute('aria-required', 'true');
    const placeholder = input.dataset.timePlaceholder || 'Select time';
    setTriggerContent(trigger, parseTime(input.value) ? displayTime(parseTime(input.value).hours, parseTime(input.value).minutes) : placeholder);

    const panel = document.createElement('div');
    panel.className = 'time-picker__panel';
    panel.id = `time-picker-${index}`;
    panel.setAttribute('role', 'dialog');
    panel.setAttribute('aria-label', 'Choose time');
    panel.hidden = true;
    trigger.setAttribute('aria-controls', panel.id);

    const heading = document.createElement('div');
    heading.className = 'time-picker__headings';
    heading.innerHTML = '<span>Hour</span><span>Minute</span><span>AM/PM</span>';
    const columns = document.createElement('div');
    columns.className = 'time-picker__columns';
    const actions = document.createElement('div');
    actions.className = 'time-picker__actions';
    actions.innerHTML = '<button type="button" data-time-clear>Clear</button><button type="button" data-time-done>Done</button>';
    panel.append(heading, columns, actions);
    picker.append(trigger, panel);
    input.after(picker);
    input._timePickerTrigger = trigger;
    input.addEventListener('invalid', (event) => {
      event.preventDefault();
      trigger.focus();
    });

    let current = parseTime(input.value) || { hours: 12, minutes: 0 };
    let selectedHour = current.hours % 12 || 12;
    let selectedMinute = current.minutes;
    let selectedPeriod = current.hours >= 12 ? 'PM' : 'AM';

    const renderColumns = () => {
      columns.replaceChildren(
        createColumn('Hour', Array.from({ length: 12 }, (_, i) => i + 1), selectedHour, pad),
        createColumn('Minute', Array.from({ length: 60 }, (_, i) => i), selectedMinute, pad),
        createColumn('AM or PM', ['AM', 'PM'], selectedPeriod, String),
      );
      requestAnimationFrame(() => columns.querySelectorAll('.is-selected').forEach((option) => option.scrollIntoView({ block: 'center' })));
    };

    const commit = () => {
      const hours = (selectedHour % 12) + (selectedPeriod === 'PM' ? 12 : 0);
      const nextValue = `${pad(hours)}:${pad(selectedMinute)}`;
      const changed = input.value !== nextValue;
      input.value = nextValue;
      setTriggerContent(trigger, displayTime(hours, selectedMinute));
      trigger.classList.add('has-value');
      if (changed) {
        input.dispatchEvent(new Event('input', { bubbles: true }));
        input.dispatchEvent(new Event('change', { bubbles: true }));
      }
    };

    const close = (restoreFocus = false) => {
      panel.hidden = true;
      picker.classList.remove('is-open');
      trigger.setAttribute('aria-expanded', 'false');
      if (restoreFocus) trigger.focus();
    };
    const open = () => {
      instances.forEach((instance) => { if (instance.picker !== picker) instance.close(); });
      current = parseTime(input.value) || current;
      selectedHour = current.hours % 12 || 12;
      selectedMinute = current.minutes;
      selectedPeriod = current.hours >= 12 ? 'PM' : 'AM';
      renderColumns();
      panel.hidden = false;
      picker.classList.add('is-open');
      trigger.setAttribute('aria-expanded', 'true');
    };

    trigger.addEventListener('click', () => panel.hidden ? open() : close());
    columns.addEventListener('click', (event) => {
      const option = event.target.closest('.time-picker__option');
      if (!option) return;
      const columnIndex = Array.from(columns.children).indexOf(option.parentElement);
      if (columnIndex === 0) selectedHour = Number(option.dataset.value);
      if (columnIndex === 1) selectedMinute = Number(option.dataset.value);
      if (columnIndex === 2) selectedPeriod = option.dataset.value;
      option.parentElement.querySelectorAll('.time-picker__option').forEach((item) => {
        const active = item === option;
        item.classList.toggle('is-selected', active);
        item.setAttribute('aria-selected', String(active));
      });
      commit();
    });
    panel.querySelector('[data-time-done]').addEventListener('click', () => { commit(); close(true); });
    panel.querySelector('[data-time-clear]').addEventListener('click', () => {
      const changed = Boolean(input.value);
      input.value = '';
      setTriggerContent(trigger, placeholder);
      trigger.classList.remove('has-value');
      if (changed) input.dispatchEvent(new Event('change', { bubbles: true }));
      close(true);
    });
    picker.addEventListener('keydown', (event) => { if (event.key === 'Escape') close(true); });
    instances.push({ picker, close });
  });

  document.addEventListener('pointerdown', (event) => {
    instances.forEach((instance) => { if (!instance.picker.contains(event.target)) instance.close(); });
  });
}
