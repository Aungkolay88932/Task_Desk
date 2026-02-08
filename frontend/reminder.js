document.addEventListener('DOMContentLoaded', function () {
  var addBtn = document.getElementById('add-reminder-btn');
  var emptyAddBtn = document.getElementById('empty-add-btn');
  var modal = document.getElementById('reminder-modal');
  var modalForm = document.getElementById('reminder-form');
  var modalHeader = document.getElementById('modal-header');
  var editIdInput = document.getElementById('edit-reminder-id');
  var closeModalBtn = document.getElementById('close-modal');
  var closeModalBtn2 = document.getElementById('close-modal-2');
  var searchInput = document.getElementById('search-input');

  function openModalForNew() {
    if (editIdInput) editIdInput.value = '';
    if (modalHeader) modalHeader.textContent = 'New Reminder';
    setFormValues('', '', '', '');
    openModal();
  }

  function openModalForEdit(card) {
    if (!card) return;
    var id = card.getAttribute('data-reminder-id');
    var title = card.getAttribute('data-title') || '';
    var desc = card.getAttribute('data-description') || '';
    var date = card.getAttribute('data-date') || '';
    var time = card.getAttribute('data-time') || '';
    if (editIdInput) editIdInput.value = id || '';
    if (modalHeader) modalHeader.textContent = 'Edit Reminder';
    setFormValues(title, date, time, desc);
    openModal();
  }

  function setFormValues(title, date, time, description) {
    var titleEl = modal && modal.querySelector('#title');
    var dateEl = modal && modal.querySelector('#date');
    var remindEl = modal && modal.querySelector('#remind');
    var descEl = modal && modal.querySelector('#description');
    if (titleEl) titleEl.value = title;
    if (dateEl) dateEl.value = date;
    if (remindEl) remindEl.value = time;
    if (descEl) descEl.value = description || '';
  }

  function openModal() {
    if (!modal) return;
    modal.classList.add('active');
    modal.setAttribute('aria-hidden', 'false');
    var first = modal.querySelector('#title');
    if (first) first.focus();
  }

  function closeModal() {
    if (!modal) return;
    modal.classList.remove('active');
    modal.setAttribute('aria-hidden', 'true');
  }

  if (addBtn) addBtn.addEventListener('click', openModalForNew);
  if (emptyAddBtn) emptyAddBtn.addEventListener('click', openModalForNew);

  // Edit reminder: delegate from grid to catch .btn-edit clicks
  var container = document.getElementById('reminders-container');
  if (container) {
    container.addEventListener('click', function (e) {
      var editBtn = e.target.closest('.btn-edit');
      if (!editBtn) return;
      var card = editBtn.closest('.reminder-card');
      if (card) openModalForEdit(card);
    });
  }
  if (closeModalBtn) closeModalBtn.addEventListener('click', closeModal);
  if (closeModalBtn2) closeModalBtn2.addEventListener('click', closeModal);

  // Close modal when clicking overlay (not the inner modal)
  if (modal) {
    modal.addEventListener('click', function (e) {
      if (e.target === modal) closeModal();
    });
  }

  // Close on Escape
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && modal && modal.classList.contains('active')) {
      closeModal();
    }
  });

  // Search filter
  function filterReminders() {
    var term = (searchInput && searchInput.value ? searchInput.value : '').toLowerCase();
    var cards = document.querySelectorAll('.reminder-card');
    cards.forEach(function (card) {
      var titleEl = card.querySelector('.reminder-card-title, h3');
      var title = (titleEl && titleEl.innerText) ? titleEl.innerText.toLowerCase() : '';
      var descEl = card.querySelector('.reminder-card-desc, p');
      var desc = (descEl && descEl.innerText) ? descEl.innerText.toLowerCase() : '';
      var show = title.indexOf(term) !== -1 || desc.indexOf(term) !== -1;
      card.style.display = show ? '' : 'none';
    });
  }

  if (searchInput) searchInput.addEventListener('input', filterReminders);

  // Form validation before submit
  if (modalForm) {
    modalForm.addEventListener('submit', function (e) {
      var titleEl = modalForm.querySelector('#title');
      var dateEl = modalForm.querySelector('#date');
      var remindEl = modalForm.querySelector('#remind');
      var title = titleEl && titleEl.value ? titleEl.value.trim() : '';
      var date = dateEl && dateEl.value ? dateEl.value : '';
      var remind = remindEl && remindEl.value ? remindEl.value : '';

      if (!title) {
        e.preventDefault();
        alert('Please enter a title.');
        if (titleEl) titleEl.focus();
        return;
      }
      if (!date || !remind) {
        e.preventDefault();
        alert('Please select both date and remind time.');
        return;
      }

      var combined = date + ' ' + remind + ':00';
      var remindDate = new Date(combined);
      if (isNaN(remindDate.getTime())) {
        e.preventDefault();
        alert('Please enter a valid date and time.');
        return;
      }
      // Allow form to submit
    });
  }

  // Reminder polling: when due reminders exist, show one alert
  setInterval(checkReminders, 30000);
  setTimeout(checkReminders, 500);

  function checkReminders() {
    fetch('/taskdesk/connect/reminder_noti.php', { credentials: 'same-origin' })
      .then(function (r) { return r.json(); })
      .then(function (arr) {
        if (!Array.isArray(arr) || arr.length === 0) return;
        var titles = arr.map(function (item) {
          return (item && (item.reminder_title || item.title)) ? (item.reminder_title || item.title) : 'Reminder';
        });
        var message = titles.length === 1
          ? 'Reminder: ' + titles[0]
          : 'Reminder: ' + titles.join('; ');
        alert(message);
      })
      .catch(function () {});
  }
});
