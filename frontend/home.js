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
