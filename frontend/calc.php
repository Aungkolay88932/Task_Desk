<?php require_once __DIR__ . '/../connect/check_auth.php';
$uid = $_SESSION['uid'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calculation Page</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="app.css">
</head>
<body class="calc-page">
    <?php include __DIR__ . '/partials/nav.php'; ?>
    <div class="container">

        <div class="input-section">
            <div class="form-cols">
                <div class="col">
                    <div class="input-group">
                        <label>Item Name</label>
                        <input type="text" id="item-name">
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

    <script>
        const uid = <?php echo json_encode($uid); ?>;
    </script>
    <script src="app.js"></script>
</body>
</html>
