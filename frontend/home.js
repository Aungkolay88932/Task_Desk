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
