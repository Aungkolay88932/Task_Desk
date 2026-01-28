let notes = JSON.parse(localStorage.getItem('my_final_notes')) || [];
const modal = document.getElementById('note-modal');
const form = document.getElementById('note-form');

// Show/Hide Modal
document.getElementById('add-note-btn').onclick = () => {
    document.getElementById('edit-id').value = "";
    form.reset();
    modal.classList.add('active');
};
document.getElementById('close-modal').onclick = () => modal.classList.remove('active');

// Save or Update Note
form.onsubmit = (e) => {
    e.preventDefault();
    const id = document.getElementById('edit-id').value;
    const noteData = {
        id: id ? Number(id) : Date.now(),
        title: document.getElementById('note-title').value,
        content: document.getElementById('note-content').value,
        tag: document.querySelector('input[name="note-tag"]:checked').value,
        date: new Date().toLocaleDateString()
    };

    if (id) {
        const index = notes.findIndex(n => n.id == id);
        notes[index] = noteData;
    } else {
        notes.push(noteData);
    }

    localStorage.setItem('my_final_notes', JSON.stringify(notes));
    modal.classList.remove('active');
    renderNotes();
};

// Edit Function
function editNote(id) {
    const note = notes.find(n => n.id === id);
    document.getElementById('edit-id').value = note.id;
    document.getElementById('note-title').value = note.title;
    document.getElementById('note-content').value = note.content;
    document.querySelector(`input[name="note-tag"][value="${note.tag}"]`).checked = true;
    modal.classList.add('active');
}

// Delete Function
function deleteNote(id) {
    notes = notes.filter(n => n.id !== id);
    localStorage.setItem('my_final_notes', JSON.stringify(notes));
    renderNotes();
}

// Render with Search and Tag Filter
function renderNotes() {
    const container = document.getElementById('notes-container');
    const filter = document.getElementById('filter-select').value;
    container.innerHTML = "";

    const filtered = filter === 'all' ? notes : notes.filter(n => n.tag === filter);

    filtered.forEach(n => {
        container.innerHTML += `
            <div class="note-card">
                <div class="note-tools">
                    <i class="fas fa-pencil-alt" onclick="editNote(${n.id})"></i>
                    <i class="fas fa-times" onclick="deleteNote(${n.id})"></i>
                </div>
                <h3>${n.title}</h3>
                <p>${n.content}</p>
                <div style="margin-top:10px; font-size:12px; color:#aaa;">${n.tag} | ${n.date}</div>
            </div>`;
    });
}

// Search function
function searchNotes() {
    const term = document.getElementById('search-input').value.toLowerCase();
    const cards = document.querySelectorAll('.note-card');
    cards.forEach(card => {
        const title = card.querySelector('h3').innerText.toLowerCase();
        card.style.display = title.includes(term) ? "block" : "none";
    });
}

renderNotes();

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
// 1. Toggle the Dropdown Menu
function toggleDropdown() {
    const dropdown = document.getElementById('profile-dropdown');
    dropdown.classList.toggle('active');
}

// 2. Load User Data on Page Start
document.addEventListener("DOMContentLoaded", () => {
    const savedImg = localStorage.getItem('userImage');
    const savedName = localStorage.getItem('userName');

    if (savedImg) {
        document.getElementById('nav-profile-img').src = savedImg;
        document.getElementById('dropdown-avatar-preview').src = savedImg;
    }
    if (savedName) {
        document.getElementById('display-username').innerText = "Hi, " + savedName;
    }
});

// 3. Update Username Function
function changeUsername() {
    const newName = prompt("Enter your new name:");
    if (newName && newName.trim() !== "") {
        localStorage.setItem('userName', newName);
        document.getElementById('display-username').innerText = "Hi, " + newName;
    }
}

// 4. Update Photo Function
document.getElementById('upload-photo').onchange = function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(event) {
            const imageData = event.target.result;
            // Save to memory
            localStorage.setItem('userImage', imageData);
            // Update UI
            document.getElementById('nav-profile-img').src = imageData;
            document.getElementById('dropdown-avatar-preview').src = imageData;
        };
        reader.readAsDataURL(file);
    }
};

// 5. Logout Function
function confirmLogout() {
    if (confirm("Are you sure you want to log out?")) {
        window.location.href = "login.html";
    }
}

// 6. Close dropdown if user clicks outside
window.onclick = function(event) {
    if (!event.target.matches('#nav-profile-img')) {
        const dropdown = document.getElementById('profile-dropdown');
        if (dropdown.classList.contains('active')) {
            dropdown.classList.remove('active');
        }
    }
}
