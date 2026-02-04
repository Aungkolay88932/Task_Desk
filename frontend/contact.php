<?php require_once __DIR__ . '/../connect/check_auth.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="app.css">
    <title>Contact - TaskDesk</title>
</head>
<body class="contact-page">
    <?php include __DIR__ . '/partials/nav.php'; ?>

    <div class="contact-container">
        <form action="https://api.web3forms.com/submit" method="POST" class="contact-left">
            <div class="contact-left-title">
                <h2>Get in touch</h2>
                <hr>
            </div>
            <input type="hidden" name="access_key" value="7376d3c9-f91b-42f8-8f8c-0d8448d07d9d">
            <input type="text" name="name" placeholder="Your Name" class="contact-inputs" required>
            <input type="email" name="email" placeholder="Your Email" class="contact-inputs">
            <textarea name="message" placeholder="Your Message" class="contact-inputs" required></textarea>
            <button type="submit">Submit</button>
        </form>
        <div class="contact-right">
            <img src="image/mailbox_PNG76.png" alt="">
        </div>
    </div>

    <script src="app.js"></script>
</body>
</html>