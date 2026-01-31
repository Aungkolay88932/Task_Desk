<?php require_once __DIR__ . '/../connect/check_auth.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Entry Form</title>
    <link rel="stylesheet" href="remainder.css">
</head>
<body>
     <header>
        <nav class="navbar">
            <a href="#" class="nav-logo">
                <h2 class="logo-text">Task Desk</h2>
            </a>
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="/taskdesk/frontend/home.php" class="nav-link">Home</a>
                </li>
                <li class="nav-item">
                    <a href="/taskdesk/frontend/contact.php" class="nav-link">Contact</a>
                </li>
                <li class="nav-item">
                <a href="/taskdesk/connect/logout.php" class="nav-btn" onclick="return confirm('Are you sure do you want to log out?')">Logout</a>
                </li>             
            </ul>
        </nav>
    </header>
    <div class="form-card">  
        <form>
            <div class="input-group">
                <label for="title">Title</label>
                <input type="text" id="title" name="title">
            </div>

            <div class="input-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="6"></textarea>
            </div>

            <div class="row">
                <div class="input-group">
                    <label for="date">Date</label>
                    <input type="date" id="date" name="date">
                </div>
                <div class="input-group">
                    <label for="time">Time</label>
                    <input type="time" id="time" name="time">
                </div>
            </div>

            <div class="input-group">
                <label for="remind">Remind Time</label>
                <input type="time" id="remind" name="remind">
            </div>

            <div class="button-container">
                <button type="button" class="btn-cancel">Cancel</button>
                <button type="submit" class="btn-save">Save</button>
            </div>
        </form>
    </div>
    <script>
    function confirmLogout() {
        const result = confirm("Are you sure do you want to log out?");
        if (result) {
            window.location.href = "/taskdesk/connect/logout.php";
        }
    }
    </script>
</body>
</html>
