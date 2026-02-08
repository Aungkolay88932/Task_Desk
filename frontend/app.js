// Central app script for TaskDesk pages (home, note, calc, remainder, contact)
(function () {
    'use strict';

    // --- Shared utilities ---
    function qs(id) { return document.getElementById(id); }

    // Toggle dropdown (used by onclick on avatar)
    function toggleDropdown() {
        const dropdown = qs('profile-dropdown');
        if (!dropdown) return;
        dropdown.classList.toggle('active');
    }

    // Minimal handler for change-name-form
    document.addEventListener('DOMContentLoaded', function () {
        var form = document.getElementById('change-name-form');
        if (form) {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                var input = document.getElementById('change-name-input');
                var newName = input ? input.value.trim() : '';
                if (!newName) return;
                fetch('/taskdesk/connect/update_username.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ user_name: newName }),
                    credentials: 'same-origin'
                }).then(r => r.json()).then(data => {
                    if (data && data.success) {
                        window.location.reload();
                    } else {
                        alert('Update failed: ' + (data && data.message ? data.message : 'Unknown'));
                    }
                }).catch(() => alert('Update error'));
            });
        }
    });

    // Confirm logout and redirect to server logout
    function confirmLogout() {
        if (confirm('Are you sure you want to log out?')) {
            window.location.href = '/taskdesk/connect/logout.php';
        }
    }


    // Avatar upload: send to server endpoint and update previews
    function initAvatarUpload() {
        const uploadInput = qs('upload-photo');
        if (!uploadInput) return;

        uploadInput.addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (!file) return;

            if (file.size > 2 * 1024 * 1024) { alert('File too large (max 2MB)'); return; }
            const allowed = ['image/jpeg','image/png','image/gif','image/webp'];
            if (!allowed.includes(file.type)) { alert('Invalid file type'); return; }

            const form = new FormData();
            form.append('avatar', file);

            fetch('/taskdesk/connect/upload_avatar.php', { method: 'POST', body: form, credentials: 'same-origin' })
              .then(r => r.json())
              .then(data => {
                  if (data && data.success && data.url) {
                      const navImg = qs('nav-profile-img');
                      const dropImg = qs('dropdown-avatar-preview');
                      const url = data.url;
                      if (navImg) navImg.src = url;
                      if (dropImg) dropImg.src = url;
                      try { localStorage.setItem('userImage', url); } catch (e) {}
                  } else {
                      alert('Upload failed: ' + (data && data.message ? data.message : 'Unknown'));
                  }
              }).catch(err => { console.error(err); alert('Upload error'); });
        });
    }

    // Dropdown open/close behavior
    function initDropdownBehavior() {
        const avatar = qs('nav-profile-img');
        const dropdown = qs('profile-dropdown');
        if (!avatar || !dropdown) return;

        avatar.addEventListener('click', (e) => { e.stopPropagation(); dropdown.classList.toggle('active'); });
        dropdown.addEventListener('click', (e) => e.stopPropagation());
        document.addEventListener('click', () => dropdown.classList.remove('active'));
    }

    // --- Note page behavior ---
    function initNotes() {
        const addBtn = qs('add-note-btn');
        const modal = qs('add-note-modal');
        const closeBtn = qs('close-modal');
        const form = qs('note-form');
        const searchInput = qs('search-input');

        if (!addBtn || !modal || !form) return;

        addBtn.addEventListener('click', () => {
            form.reset();
            modal.classList.add('active');
            const title = qs('note-title'); if (title) title.focus();
        });

        if (closeBtn) closeBtn.addEventListener('click', () => modal.classList.remove('active'));

        form.addEventListener('submit', (e) => {
            e.preventDefault();
            const title = qs('note-title') ? qs('note-title').value.trim() : '';
            const content = qs('note-content') ? qs('note-content').value.trim() : '';
            const noteId = qs('note-id') ? qs('note-id').value.trim() : '';
            if (!title && !content) { alert('Please enter a title or content'); return; }

            if (noteId) {
                // update existing note
                const payload = { note_id: parseInt(noteId, 10), title: title, content: content };
                fetch('/taskdesk/connect/update_note.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload),
                    credentials: 'same-origin'
                }).then(r => r.json()).then(data => {
                    if (data && data.success) {
                        window.location.reload();
                    } else {
                        alert('Update failed');
                    }
                }).catch(err => { console.error(err); alert('Update error'); });
            } else {
                const fd = new FormData();
                fd.append('action', 'add_note');
                fd.append('title', title);
                fd.append('content', content);

                fetch(window.location.href, { method: 'POST', body: fd, credentials: 'same-origin' })
                  .then(r => {
                      if (r.ok) return r.text();
                      throw new Error('Network response not ok');
                  }).then(() => {
                      window.location.reload();
                  }).catch(err => { console.error(err); alert('Failed to save note'); });
            }
        });

        // search filter (debounced)
        if (searchInput) {
            let t;
            searchInput.addEventListener('input', () => {
                clearTimeout(t);
                t = setTimeout(() => {
                    const term = (searchInput.value || '').toLowerCase();
                    document.querySelectorAll('.note-card').forEach(card => {
                        const title = (card.querySelector('h3') || { innerText: '' }).innerText.toLowerCase();
                        card.style.display = title.includes(term) ? 'block' : 'none';
                    });
                }, 200);
            });
        }

            // Edit / Delete buttons (server-rendered notes)
            document.querySelectorAll('.edit-note').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    const id = btn.getAttribute('data-id');
                    const card = btn.closest('.note-card');
                    if (!card) return;
                    const title = (card.querySelector('h3')||{innerText:''}).innerText.trim();
                    const content = (card.querySelector('p')||{innerText:''}).innerText.trim();
                    if (qs('note-id')) qs('note-id').value = id;
                    if (qs('note-title')) qs('note-title').value = title;
                    if (qs('note-content')) qs('note-content').value = content;
                    modal.classList.add('active');
                });
            });

            document.querySelectorAll('.delete-note').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    const id = btn.getAttribute('data-id');
                    if (!id) return;
                    if (!confirm('Delete this note?')) return;
                    const payload = { note_id: parseInt(id, 10) };
                    fetch('/taskdesk/connect/delete_note.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(payload),
                        credentials: 'same-origin'
                    }).then(r => r.json()).then(data => {
                        if (data && data.success) {
                            window.location.reload();
                        } else {
                            alert('Delete failed');
                        }
                    }).catch(err => { console.error(err); alert('Delete error'); });
                });
            });
    }

    // --- Calculator page behavior (exposed globally for onclick handlers) ---
    const expenses = { data: JSON.parse(localStorage.getItem('myExpenses') || '[]') };

    function addExpense() {
        const itemEl = qs('item-name');
        const amountEl = qs('amount');
        const categoryEl = qs('category');
        const dateEl = qs('date-input');
        const item = itemEl ? itemEl.value.trim() : '';
        const amount = amountEl ? parseFloat(amountEl.value) : 0;
        const category = categoryEl ? categoryEl.value.trim() : '';
        const date = dateEl ? dateEl.value : '';

        if (!(item && amount && date)) {
            alert('Please fill in all fields');
            return;
        }

        const fd = new FormData();
        fd.append('name', item);
        fd.append('price', amount);
        fd.append('category', category);
        fd.append('date', date);

        fetch('/taskdesk/connect/save_cost.php', { method: 'POST', body: fd, credentials: 'same-origin' })
            .then(r => r.json())
            .then(data => {
                if (data && data.success) {
                    alert('Expense Added!');
                    clearInputs();
                    displayAll();
                } else {
                    alert('Save failed: ' + (data && data.message ? data.message : 'Unknown'));
                }
            }).catch(err => { console.error(err); alert('Network error'); });
    }

    function clearInputs() {
        if (qs('item-name')) qs('item-name').value = '';
        if (qs('amount')) qs('amount').value = '';
        if (qs('category')) qs('category').value = '';
        if (qs('date-input')) qs('date-input').value = '';
    }

    function displayAll() {
        const screen = qs('display-screen'); if (!screen) return;
        const footerDate = qs('footer-date');
        const date = footerDate && footerDate.value ? footerDate.value : '';
        const url = '/taskdesk/connect/fetch_cost.php?type=all' + (date ? '&date=' + encodeURIComponent(date) : '');
        screen.innerHTML = '<p>Loading...</p>';
        fetch(url, { credentials: 'same-origin' }).then(r => r.json()).then(rows => {
            if (!rows || rows.length === 0) {
                screen.innerHTML = '<p>No records</p>';
                return;
            }

            // Render results in a table for the calculate page
            let html = `<table class="cost-table" style="width:100%;background:white;padding:10px;border-radius:8px; border-collapse: collapse;">
                <thead>
                    <tr style="text-align:left;">
                        <th style="padding:8px;border-bottom:1px solid #eee;">Date</th>
                        <th style="padding:8px;border-bottom:1px solid #eee;">Item</th>
                        <th style="padding:8px;border-bottom:1px solid #eee;">Price</th>
                    </tr>
                </thead>
                <tbody>`;

            let total = 0;
            rows.forEach(row => {
                const d = new Date(row.cost_date);
                total += Number(row.price || 0);
                html += `<tr>
                    <td style="padding:8px;border-bottom:1px solid #f3f3f3;">${d.toLocaleDateString()}</td>
                    <td style="padding:8px;border-bottom:1px solid #f3f3f3;">${escapeHtml(row.cost_name)}</td>
                    <td style="padding:8px;border-bottom:1px solid #f3f3f3;">$${Number(row.price).toFixed(2)}</td>
                </tr>`;
            });

            html += `</tbody></table><div style="margin-top:12px;text-align:right;font-weight:700;">Total: $${total.toFixed(2)}</div>`;
            screen.innerHTML = html;
        }).catch(err => { console.error(err); screen.innerHTML = '<p>Error loading data</p>'; });
    }

    function calculateTotal(period) {
        const screen = qs('display-screen'); if (!screen) return;
        const footerDate = qs('footer-date');
        const date = footerDate && footerDate.value ? footerDate.value : '';
        const url = '/taskdesk/connect/fetch_cost.php?type=' + encodeURIComponent(period) + (date ? '&date=' + encodeURIComponent(date) : '');
        screen.innerHTML = '<p>Loading...</p>';
        fetch(url, { credentials: 'same-origin' }).then(r => r.json()).then(rows => {
            let total = 0;
            rows.forEach(row => { total += Number(row.price || 0); });
            screen.innerHTML = `<h2 style="background-color: white;">${period.toUpperCase()} TOTAL</h2><hr><br><h1 style="font-size:4rem; background-color: white;">$${total.toFixed(2)}</h1>`;
        }).catch(err => { console.error(err); screen.innerHTML = '<p>Error calculating total</p>'; });
    }

    function cleardata() {
        const screen = qs('display-screen'); if (screen) screen.innerHTML = '';
        if (qs('item-name')) qs('item-name').value = '';
        if (qs('amount')) qs('amount').value = '';
        if (qs('category')) qs('category').value = '';
        if (qs('date-input')) qs('date-input').value = '';
        const footerDate = qs('footer-date'); if (footerDate) footerDate.value = '';
        console.log('All data cleared and screen is now empty.');
    }

    // simple escape to avoid HTML injection in table
    function escapeHtml(s) {
        return String(s).replace(/[&<>\"']/g, function (c) { return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]; });
    }

    // expose calc functions globally for inline handlers in HTML
    window.addExpense = addExpense;
    window.calculateTotal = calculateTotal;
    window.displayAll = displayAll;
    window.cleardata = cleardata;

    // expose shared functions globally for HTML onclick attributes
    window.toggleDropdown = toggleDropdown;
    window.confirmLogout = confirmLogout;

    // initialize on DOM ready
    document.addEventListener('DOMContentLoaded', () => {
        initAvatarUpload();
        initDropdownBehavior();
        initNotes();
        // If on calculation page, load all data immediately
        if (document.body && document.body.classList.contains('calc-page')) {
            try { displayAll(); } catch (e) { console.error(e); }
        }

        // --- Reminder page behavior: browser notifications polling ---
        if (document.body && document.body.classList.contains('remainder-page')) {
            if (typeof window !== 'undefined' && 'Notification' in window && Notification.permission !== 'granted') {
                try { Notification.requestPermission(); } catch (e) { console.warn('Notification permission request failed', e); }
            }

            function checkReminders() {
                fetch('/taskdesk/connect/reminder_noti.php', { credentials: 'same-origin' })
                    .then(r => r.json())
                    .then(data => {
                        if (!data || !Array.isArray(data)) return;
                        data.forEach(r => {
                            try { showReminderNotification(r.reminder_title || r.reminder_title); } catch (e) { console.error(e); }
                        });
                    }).catch(err => console.error('Reminder fetch error', err));
            }

            function showReminderNotification(body) {
                if (!('Notification' in window)) return;
                if (Notification.permission !== 'granted') return;
                try {
                    new Notification('Task Desk Reminder', { body: String(body), icon: '/taskdesk/frontend/image/logo.jpg' });
                } catch (e) { console.error('Show notification failed', e); }
            }

            // run immediately and poll every 60s
            try { checkReminders(); } catch (e) { console.error(e); }
            setInterval(() => { try { checkReminders(); } catch (e) {} }, 60000);

            // Search/filter reminders (used by onkeyup in markup)
            window.searchReminders = function () {
                const term = (document.getElementById('search-input')?.value || '').toLowerCase();
                document.querySelectorAll('.reminder-card').forEach(card => {
                    const title = (card.querySelector('h3') || { innerText: '' }).innerText.toLowerCase();
                    card.style.display = title.includes(term) ? 'block' : 'none';
                });
            };
            // wire input event for live search
            const searchInput = document.getElementById('search-input');
            if (searchInput) searchInput.addEventListener('input', () => { try { window.searchReminders(); } catch (e) {} });

            // Modal open/close
            const modal = document.getElementById('reminder-modal');
            const addBtn = document.getElementById('add-reminder-btn');
            const closeBtn = document.getElementById('close-modal');
            if (addBtn && modal) addBtn.addEventListener('click', () => modal.classList.add('active'));
            if (closeBtn && modal) closeBtn.addEventListener('click', () => modal.classList.remove('active'));
        }
    });

})();
