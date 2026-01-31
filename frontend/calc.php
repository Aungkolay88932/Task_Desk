<?php require_once __DIR__ . '/../connect/check_auth.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calculation Page</title>
    <link rel="stylesheet" href="calc.css">
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
    <div class="container">

        <div class="input-section">
            <div class="form-cols">
                <div class="col">
                    <div class="input-group">
                        <label>Item Name</label>
                        <input type="text" id="item-name">
                    </div>
                    <div class="input-group">
                        <label>Category</label>
                        <input type="text" id="category">
                    </div>
                </div>
                <div class="col">
                    <div class="input-group">
                        <label>Price</label>
                        <input type="number" id="amount">
                    </div>
                    <div class="input-group">
                        <label>Date</label>
                        <input type="date" id="date-input">
                    </div>
                </div>
                <div class="action-buttons">
                    <button class="btn-orange" onclick="addExpense()">Add Expense</button>
                    <button class="btn-clear" onclick="clearInputs()">Clear</button>
                    
                </div>
            </div>
        </div>

        <div class="display-area" id="display-screen">
            <p class="placeholder-text">Calculations will appear here...</p>
        </div>

        <div class="footer-controls">
            <input type="date" id="footer-date">
            <div class="footer-buttons">
                <button onclick="calculateTotal('day')">Today Total</button>
                <button onclick="calculateTotal('week')">Week Total</button>
                <button onclick="calculateTotal('month')">Month Total</button>
                <button onclick="displayAll()">Display</button>
                <button class="btn-close" onclick="cleardata()">Close</button>
            </div>
        </div>
    </div>

    <script src="calc.js"></script>
</body>
</html>
