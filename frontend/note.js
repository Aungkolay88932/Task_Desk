document.addEventListener('DOMContentLoaded', () => {
    const addNoteBtn = document.getElementById('add-note-btn');
    const addNoteModal = document.getElementById('add-note-modal');
    const confirmModal = document.getElementById('confirm-modal');
    const closeModal = document.getElementById('close-modal');
    const noteForm = document.getElementById('note-form');
    const notesContainer = document.getElementById('notes-container');
    const searchInput = document.getElementById('search-input');
    const filterSelect = document.getElementById('filter-select');
    const confirmDelBtn = document.getElementById('confirm-delete');
    const cancelDelBtn = document.getElementById('cancel-delete');

    let notes = JSON.parse(localStorage.getItem('elegant_notes')) || [];
    let noteToDeleteIndex = null;

    // Helper to save notes
    const saveToStorage = () => localStorage.setItem('elegant_notes', JSON.stringify(notes));

    const renderNotes = (filteredNotes = notes) => {
        notesContainer.innerHTML = '';
        if (filteredNotes.length === 0) {
            document.getElementById('empty-state').style.display = 'block';
        } else {
            document.getElementById('empty-state').style.display = 'none';
            filteredNotes.forEach((note, index) => {
                const card = document.createElement('div');
                card.className = 'note-card';
                card.innerHTML = `
                    <h3>${note.title}</h3>
                    <p>${note.content}</p>
                    <small>Tag: <strong>${note.tag}</strong> | ${note.date}</small>
                    <div style="margin-top:15px; text-align:right;">
                        <button class="delete-icon" onclick="openDeleteModal(${index})" style="background:none; border:none; color:#ff4d4d; cursor:pointer;"><i class="fas fa-trash"></i> Delete</button>
                    </div>
                `;
                notesContainer.appendChild(card);
            });
        }
    };

    // Modal Handlers
    addNoteBtn.onclick = () => addNoteModal.classList.add('active');
    closeModal.onclick = () => addNoteModal.classList.remove('active');

    window.openDeleteModal = (index) => {
        noteToDeleteIndex = index;
        confirmModal.classList.add('active');
    };

    cancelDelBtn.onclick = () => confirmModal.classList.remove('active');

    confirmDelBtn.onclick = () => {
        notes.splice(noteToDeleteIndex, 1);
        saveToStorage();
        renderNotes();
        confirmModal.classList.remove('active');
    };

    // Add Note Logic
    noteForm.onsubmit = (e) => {
        e.preventDefault();
        const newNote = {
            title: document.getElementById('note-title').value,
            content: document.getElementById('note-content').value,
            tag: document.querySelector('input[name="note-tag"]:checked').value,
            date: new Date().toLocaleDateString()
        };
        notes.unshift(newNote);
        saveToStorage();
        renderNotes();
        addNoteModal.classList.remove('active');
        noteForm.reset();
    };

    // Search and Filter Logic
    const filterNotes = () => {
        const searchTerm = searchInput.value.toLowerCase();
        const selectedTag = filterSelect.value;

        const filtered = notes.filter(note => {
            const matchesSearch = note.title.toLowerCase().includes(searchTerm) || note.content.toLowerCase().includes(searchTerm);
            const matchesTag = selectedTag === 'all' || note.tag === selectedTag;
            return matchesSearch && matchesTag;
        });
        renderNotes(filtered);
    };

    searchInput.oninput = filterNotes;
    filterSelect.onchange = filterNotes;

    renderNotes();
});

// Logout confirmation function
function confirmLogout() {
    // Show confirmation dialog
    const result = confirm("Are you sure do you want to log out?");
    
    // If user clicks OK (true), redirect to login page
    if (result) {
        window.location.href = "login.html";
    }
    // If user clicks Cancel (false), nothing happens - alert box closes automatically
}
