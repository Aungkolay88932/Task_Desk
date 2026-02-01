// 1. Toggle the Dropdown Menu
function toggleDropdown() {
    const dropdown = document.getElementById('profile-dropdown');
    dropdown.classList.toggle('active');
}

// 2. Load User Data on Page Start (localStorage overrides for avatar/display name)
document.addEventListener("DOMContentLoaded", () => {
    const savedImg = localStorage.getItem('userImage');
    const savedName = localStorage.getItem('userName');

    if (savedImg) {
        const navImg = document.getElementById('nav-profile-img');
        const dropImg = document.getElementById('dropdown-avatar-preview');
        if (navImg) navImg.src = savedImg;
        if (dropImg) dropImg.src = savedImg;
    }
    if (savedName) {
        const displayEl = document.getElementById('display-username');
        if (displayEl) displayEl.innerText = "Hi, " + savedName + "!";
    }
});

// 3. Update Username (localStorage only; server session unchanged until next login)
function changeUsername() {
    const newName = prompt("Enter your new name:");
    if (newName && newName.trim() !== "") {
        localStorage.setItem('userName', newName);
        const displayEl = document.getElementById('display-username');
        if (displayEl) displayEl.innerText = "Hi, " + newName + "!";
    }
}

// 4. Update Photo (save to localStorage, update UI)
document.getElementById('upload-photo').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(event) {
            const imageData = event.target.result;
            localStorage.setItem('userImage', imageData);
            const navImg = document.getElementById('nav-profile-img');
            const dropImg = document.getElementById('dropdown-avatar-preview');
            if (navImg) navImg.src = imageData;
            if (dropImg) dropImg.src = imageData;
        };
        reader.readAsDataURL(file);
    }
});

// 5. Logout: go to server logout so session is destroyed
function confirmLogout() {
    if (confirm("Are you sure you want to log out?")) {
        window.location.href = "/taskdesk/connect/logout.php";
    }
}

// 6. Close dropdown when clicking outside
window.addEventListener('click', function(event) {
    const navImg = document.getElementById('nav-profile-img');
    const dropdown = document.getElementById('profile-dropdown');
    if (dropdown && dropdown.classList.contains('active')) {
        if (navImg && !navImg.contains(event.target) && !dropdown.contains(event.target)) {
            dropdown.classList.remove('active');
        }
    }
});
