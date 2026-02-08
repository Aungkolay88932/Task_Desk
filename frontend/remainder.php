<?php
require_once __DIR__ . '/../connect/check_auth.php';
require_once __DIR__ . '/../connect/db_connect.php';
$uid = $_SESSION['uid'];
$query = "SELECT * FROM reminder WHERE uid = ? ORDER BY reminder_time DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $uid);
$stmt->execute();
$res = $stmt->get_result();
$reminders = $res->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Desk - Reminders</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="app.css">
    <link rel="stylesheet" href="reminder.css">
</head>

<body class="remainder-page">
    <?php include __DIR__ . '/partials/nav.php'; ?>

    <div class="reminder-page-container">
        <header class="reminder-page-header">
            <h1 class="reminder-page-title"><i class="fas fa-bell"></i> My Reminders</h1>
            <div class="reminder-controls">
                <div class="search-wrap">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" id="search-input" class="search-input" placeholder="Search reminders...">
                </div>
                <button type="button" id="add-reminder-btn" class="btn-add-reminder">
                    <i class="fas fa-plus"></i> Add Reminder
                </button>
            </div>
        </header>

        <?php if (empty($reminders)): ?>
            <div id="empty-state" class="reminder-empty-state">
                <div class="empty-state-icon"><i class="fas fa-bell-slash"></i></div>
                <h2>No reminders yet</h2>
                <p>Create a reminder so you never miss an important task or event.</p>
                <button type="button" id="empty-add-btn" class="btn-add-reminder btn-empty-cta">
                    <i class="fas fa-plus"></i> Add your first reminder
                </button>
            </div>
        <?php else: ?>
            <div id="reminders-container" class="reminders-grid">
                <?php foreach ($reminders as $r): ?>
                    <?php
                        $dt = strtotime($r['reminder_time']);
                        $date = $dt ? date('M j, Y', $dt) : '—';
                        $time = $dt ? date('g:i A', $dt) : '—';
                        $dateVal = $dt ? date('Y-m-d', $dt) : '';
                        $timeVal = $dt ? date('H:i', $dt) : '';
                        $desc = $r['reminder_description'] ?? $r['description'] ?? '';
                    ?>
                    <article class="reminder-card" data-reminder-id="<?= (int)$r['reminder_id'] ?>"
                        data-title="<?= htmlspecialchars($r['reminder_title']) ?>"
                        data-description="<?= htmlspecialchars($desc) ?>"
                        data-date="<?= $dateVal ?>"
                        data-time="<?= $timeVal ?>">
                        <div class="reminder-card-tools">
                            <button type="button" class="btn-edit" title="Edit"><i class="fas fa-edit"></i></button>
                            <form action="/taskdesk/connect/delete_reminder.php" method="POST" class="delete-form" onsubmit="return confirm('Delete this reminder?');">
                                <input type="hidden" name="reminder_id" value="<?= (int)$r['reminder_id'] ?>">
                                <button type="submit" class="btn-delete" title="Delete"><i class="fas fa-trash-alt"></i></button>
                            </form>
                        </div>
                        <h3 class="reminder-card-title"><?= htmlspecialchars($r['reminder_title']) ?></h3>
                        <?php if ($desc !== ''): ?>
                            <p class="reminder-card-desc"><?= htmlspecialchars($desc) ?></p>
                        <?php endif; ?>
                        <div class="reminder-card-time">
                            <span><i class="far fa-calendar-alt"></i> <?= $date ?></span>
                            <span><i class="far fa-clock"></i> <?= $time ?></span>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <div id="reminder-modal" class="reminder-modal-overlay" aria-hidden="true">
        <div class="reminder-modal">
            <div class="reminder-modal-header">
                <h2 id="modal-header">New Reminder</h2>
                <button type="button" id="close-modal" class="btn-modal-close" aria-label="Close"><i class="fas fa-times"></i></button>
            </div>
            <form id="reminder-form" action="/taskdesk/connect/save_reminder.php" method="POST">
                <input type="hidden" name="reminder_id" id="edit-reminder-id" value="">
                <div class="form-group">
                    <label for="title">Title <span class="required">*</span></label>
                    <input type="text" id="title" name="title" required placeholder="e.g. Team meeting">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="date">Date <span class="required">*</span></label>
                        <input type="date" id="date" name="date" required>
                    </div>
                    <div class="form-group">
                        <label for="remind">Remind at (time) <span class="required">*</span></label>
                        <input type="time" id="remind" name="remind" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="3" placeholder="Optional notes..."></textarea>
                </div>
                <div class="modal-buttons">
                    <button type="button" id="close-modal-2" class="btn-cancel">Cancel</button>
                    <button type="submit" name="save_reminder_btn" class="btn-save"><i class="fas fa-check"></i> Save Reminder</button>
                </div>
            </form>
        </div>
    </div>

    <script src="reminder.js"></script>
</body>

</html>
