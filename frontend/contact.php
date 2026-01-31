<?php require_once __DIR__ . '/../connect/check_auth.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">   
     <link rel="stylesheet" href="contact.css">
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
    <div class="contact-container">
        <form action="https://api.web3forms.com/submit" method="POST" class="contact-left">
            <div class="contact-left-title">
                <h2>Get in touch</h2>
                <hr>
            </div>
             <input type="hidden" name="access_key" value="7376d3c9-f91b-42f8-8f8c-0d8448d07d9d">
            <input type="text" name="name" placeholder="Your Name" class="contact-inputs" required >
            <input type="email" name="email" placeholder="Your Email" class="contact-inputs"  >
            <textarea name="message" placeholder="Your Message"class="contact-inputs" required></textarea>
            <button type="submit">Submit <img src="image/arrow_icon.png" ></button>
        </form>
        <div class="contact-right">
            <img src="image/mailbox_PNG76.png" alt="">
        </div>
    </div>

    <script src="home.js"></script>
</body>
</html>
