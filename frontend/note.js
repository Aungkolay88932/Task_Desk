document.addEventListener('DOMContentLoaded', () => {
    const addNoteBtn = document.getElementById('add-note-btn');
    const addNoteModal = document.getElementById('add-note-modal');
    const closeModal = document.getElementById('close-modal');
    const noteForm = document.getElementById('note-form');
    const notesContainer = document.getElementById('notes-container');
    const searchInput = document.getElementById('search-input');

    addNoteBtn.onclick = () => addNoteModal.classList.add('active');
    closeModal.onclick = () => addNoteModal.classList.remove('active');

    noteForm.addEventListener('submit', (e) => {
        e.preventDefault();

        const title = document.getElementById('note-title').value.trim();
        const content = document.getElementById('note-content').value.trim();

        if (!title || !content) return; // simple validation

        fetch('note.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `action=add_note&title=${encodeURIComponent(title)}&content=${encodeURIComponent(content)}`
        })
        .then(res => res.text())
        .then(() => {
          
            location.reload();
        })
        .catch(err => console.error('Error adding note:', err));
    });

     // Simple search
    searchInput.addEventListener('input', () => {
        const term = searchInput.value.toLowerCase();
        const notes = document.querySelectorAll('.note-card');

        notes.forEach(note => {
            const title = note.querySelector('h3').textContent.toLowerCase();
            const content = note.querySelector('p').textContent.toLowerCase();
            note.style.display = (title.includes(term) || content.includes(term)) ? 'block' : 'none';
        });

        const anyVisible = Array.from(notes).some(n => n.style.display !== 'none');
        const emptyState = document.getElementById('empty-state');
        if (emptyState) emptyState.style.display = anyVisible ? 'none' : 'block';
    });
});
