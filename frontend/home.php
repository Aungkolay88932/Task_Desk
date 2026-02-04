<?php require_once __DIR__ . '/../connect/check_auth.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="app.css">
    <title>Task Desk | Home</title>
</head>
<body class="home-page">
    <?php include __DIR__ . '/partials/nav.php'; ?>

    <div class="card-container">
        <div class="card">
            <img src="image/note.jpg" alt="Notes">
            <div class="card-content">
                <p>Your Task Desk is ready. Type your first note here to get started.</p>
                <button type="button" class="btn" onclick="window.location.href='/taskdesk/frontend/note.php';">Note</button>
            </div>
        </div>
        <div class="card">
            <img src="image/balance.jpg" alt="Budget">
            <div class="card-content">
                <p>Total your costs and balance your budget in one click.</p>
                <button type="button" class="btn" onclick="window.location.href='/taskdesk/frontend/calc.php';">Balance</button>
            </div>
        </div>
        <div class="card">
            <img src="image/remain.jpg" alt="Reminders">
            <div class="card-content">
                <p>Set it and forget it. Task Desk will remind you when it's time.</p>
                <button type="button" class="btn" onclick="window.location.href='/taskdesk/frontend/remainder.php';">Reminder</button>
            </div>
        </div>
    </div>

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

    <script src="app.js"></script>
</body>
</html>
