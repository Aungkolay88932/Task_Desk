<?php
require_once __DIR__ . '/../connect/check_auth.php';
require_once __DIR__ . '/../connect/db_connect.php';

$uid = $_SESSION['uid'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_note') {
    $title   = $_POST['title'];
    $content = $_POST['content'];

    $stmt = $conn->prepare(
        "INSERT INTO Note (uid, title, content) VALUES (?, ?, ?)"
    );
    $stmt->bind_param("iss", $uid, $title, $content);
    $stmt->execute();
    exit;
}

// Fetch notes
$stmt = $conn->prepare(
    "SELECT note_id, title, content, created_at
     FROM Note
     WHERE uid = ?
     ORDER BY created_at DESC"
);
$stmt->bind_param("i", $uid);
$stmt->execute();
$notes = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Elegant Note</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="note.css">
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
                <form method="post" action="/taskdesk/connect/logout.php">
                    <button type="submit" class="nav-btn">Logout</button>
                </form>
            </li>
        </ul>
    </nav>
</header>

<div class="container">

    <section class="controls">
        <input type="text" id="search-input" placeholder="Search notes...">
        <button id="add-note-btn" class="btn-main">
            <i class="fas fa-plus"></i> Add Note
        </button>
    </section>

    <div id="notes-container" class="notes-grid">
        <?php if ($notes->num_rows > 0): ?>
            <?php while ($row = $notes->fetch_assoc()): ?>
                <div class="note-card">
                    <h3><?= htmlspecialchars($row['title']) ?></h3>
                    <p><?= nl2br(htmlspecialchars($row['content'])) ?></p>
                    <small><?= $row['created_at'] ?></small>
                </div>
            <?php endwhile; ?>
        <?php endif; ?>
    </div>

    <?php if ($notes->num_rows === 0): ?>
        <div id="empty-state" class="empty-state">
            <i class="fas fa-sticky-note"></i>
            <h3>No notes yet</h3>
            <p>Click "Add Note" to get started!</p>
        </div>
    <?php endif; ?>
</div>

<!-- Add Note Modal -->
<div id="add-note-modal" class="modal-overlay">
    <div class="modal-body">
        <form id="note-form">
            <label>Title</label>
            <input type="text" id="note-title" required>

            <label>Description</label>
            <textarea id="note-content" rows="6" required></textarea>

            <div class="modal-buttons">
                <button type="button" id="close-modal" class="cancel-btn">Cancel</button>
                <button type="submit" class="submit-btn">Save Note</button>
            </div>
        </form>
    </div>
</div>

<script src="note.js"></script>

</body>
</html>
