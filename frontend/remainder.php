<?php require_once __DIR__ . '/../connect/check_auth.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Entry Form</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="app.css">
</head>
<body class="remainder-page">
    <?php include __DIR__ . '/partials/nav.php'; ?>
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
    <script src="app.js"></script>
</body>
</html>
