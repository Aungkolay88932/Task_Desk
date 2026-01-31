<?php require_once __DIR__ . '/../connect/check_auth.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="home.css">
    <title>Document</title>
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
    <div class="card-container">
        <div class="card">
            <img src="image/note.jpg" alt="">
            <div class="card-content">
                <p>Your Task Desk is ready type your first note here to get started</p>
                 <button type="button" class="btn" onclick="window.location.href='/taskdesk/frontend/note.php';">Note</button>
            </div>
        </div>
         <div class="card">
            <img src="image/balance.jpg" alt="">
            <div class="card-content">
                <p>Total your costs and balance your budget in one click</p>
                 <button type="button" class="btn" onclick="window.location.href='/taskdesk/frontend/calc.php';">Balance</button>
            </div>
        </div>
         <div class="card">
            <img src="image/remain.jpg" alt="">
            <div class="card-content">
                <p>Set it and forget it Task Desk will remind you when it's time</p>
                <button type="button" class="btn" onclick="window.location.href='/taskdesk/frontend/remainder.php';">Reaminder</button>
            </div>
        </div>  
    </div>
    <!-- Footer Section -->
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-content">
                <div class="footer-brand">
                    <h2>TaskDesk</h2>
                    <p>Your productivity companion for notes, calculations, and reminders.</p>
                </div>

                <div class="footer-contact">
                    <h3>Contact Us</h3>
                    <div class="contact-item">
                        <i class="fas fa-envelope"></i>
                        <a href="mailto:taskdesk41@gmail.com">taskdesk41@gmail.com</a>
                    </div>
                </div>

                <div class="footer-features">
                    <h3>Features</h3>
                    <ul>
                        <li>Take Notes</li>
                        <li>Calculation</li>
                        <li>Set Reminders</li>
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                <p>&copy; 2026 TaskDesk. All rights reserved.</p>
            </div>
        </div>
    </footer>
    <script src="home.js"></script>
</body>
</html>
