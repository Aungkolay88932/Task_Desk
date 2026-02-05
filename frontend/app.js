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

    // Change display name (sends update to server)
    function changeUsername() {
        const newName = prompt('Enter your new name:');
        if (!newName || newName.trim() === '') return;
        const payload = { user_name: newName.trim() };

        fetch('/taskdesk/connect/update_username.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload),
            credentials: 'same-origin'
        }).then(r => r.json()).then(data => {
            if (data && data.success) {
                const displayEl = qs('display-username');
                if (displayEl) displayEl.innerText = data.user_name + '!';
                try { localStorage.setItem('userName', data.user_name); } catch (e) {}
                alert('Name updated');
            } else {
                alert('Update failed: ' + (data && data.message ? data.message : 'Unknown'));
            }
        }).catch(err => { console.error(err); alert('Update error'); });
    }

    // Confirm logout and redirect to server logout
    function confirmLogout() {
        if (confirm('Are you sure you want to log out?')) {
            window.location.href = '/taskdesk/connect/logout.php';
        }
    }

    // Load saved avatar and name from localStorage (if present)
    function loadLocalProfile() {
        const savedImg = localStorage.getItem('userImage');
        const savedName = localStorage.getItem('userName');

        if (savedImg) {
            const navImg = qs('nav-profile-img');
            const dropImg = qs('dropdown-avatar-preview');
            if (navImg) navImg.src = savedImg;
            if (dropImg) dropImg.src = savedImg;
        }
        if (savedName) {
            const displayEl = qs('display-username');
            if (displayEl) displayEl.innerText = 'Hi, ' + savedName + '!';
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
            if (!title && !content) { alert('Please enter a title or content'); return; }

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

        if (item && amount && date) {
            const entry = { item, amount, category, date };
            expenses.data.push(entry);
            localStorage.setItem('myExpenses', JSON.stringify(expenses.data));
            alert('Expense Added!');
            clearInputs();
        } else {
            alert('Please fill in all fields');
        }
    }

    function clearInputs() {
        if (qs('item-name')) qs('item-name').value = '';
        if (qs('amount')) qs('amount').value = '';
        if (qs('category')) qs('category').value = '';
        if (qs('date-input')) qs('date-input').value = '';
    }

    function displayAll() {
        const screen = qs('display-screen'); if (!screen) return;
        screen.innerHTML = '<h3 style="background-color: white;">All Records:</h3><br>';
        expenses.data.forEach(ex => {
            screen.innerHTML += `<p style="background-color: white;">${new Date(ex.date).toLocaleDateString()} - ${ex.item}: $${ex.amount} (${ex.category})</p>`;
        });
    }

    function calculateTotal(period) {
        const screen = qs('display-screen'); if (!screen) return;
        const today = new Date();
        let total = 0;
        expenses.data.forEach(ex => {
            const exDate = new Date(ex.date);
            if (period === 'day' && exDate.toDateString() === today.toDateString()) total += ex.amount;
            else if (period === 'month' && exDate.getMonth() === today.getMonth() && exDate.getFullYear() === today.getFullYear()) total += ex.amount;
            else if (period === 'week' && (today - exDate) / (1000 * 60 * 60 * 24) <= 7) total += ex.amount;
        });
        screen.innerHTML = `<h2 style="background-color: white;">${period.toUpperCase()} TOTAL</h2><hr><br><h1 style="font-size:4rem; background-color: white;">$${total.toFixed(2)}</h1>`;
    }

    function cleardata() {
        const screen = qs('display-screen'); if (screen) screen.innerHTML = '';
        if (qs('item-name')) qs('item-name').value = '';
        if (qs('amount')) qs('amount').value = '';
        if (qs('category')) qs('category').value = '';
        if (qs('date')) qs('date').value = '';
        const footerDate = qs('footer-date'); if (footerDate) footerDate.value = '';
        console.log('All data cleared and screen is now empty.');
    }

    // expose calc functions globally for inline handlers in HTML
    window.addExpense = addExpense;
    window.calculateTotal = calculateTotal;
    window.displayAll = displayAll;
    window.cleardata = cleardata;

    // expose shared functions globally for HTML onclick attributes
    window.toggleDropdown = toggleDropdown;
    window.changeUsername = changeUsername;
    window.confirmLogout = confirmLogout;

    // initialize on DOM ready
    document.addEventListener('DOMContentLoaded', () => {
        loadLocalProfile();
        initAvatarUpload();
        initDropdownBehavior();
        initNotes();
    });

})();
