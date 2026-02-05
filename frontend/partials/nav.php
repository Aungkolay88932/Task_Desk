<?php
// Reusable navigation partial. Assumes session is started and user authenticated if needed.
// Fetch fresh username from DB to ensure updates are reflected immediately.
require_once __DIR__ . '/../../connect/db_connect.php';
// If session uid is available, query the DB for the latest user_name
$display_name = 'User';
if (!empty($_SESSION['uid'])) {
    $uid = intval($_SESSION['uid']);
    $stmt = $conn->prepare('SELECT user_name FROM user_info WHERE uid = ? LIMIT 1');
    if ($stmt) {
        $stmt->bind_param('i', $uid);
        if ($stmt->execute()) {
            $res = $stmt->get_result();
            if ($row = $res->fetch_assoc()) {
                $display_name = htmlspecialchars($row['user_name']);
            }
        }
        $stmt->close();
    }
}
?>
<header>
    <nav class="navbar">
        <a href="/taskdesk/frontend/home.php" class="nav-logo">
            <h2 class="logo-text">Task Desk</h2>
        </a>

        <ul class="nav-menu">
            <li class="nav-item"><a href="/taskdesk/frontend/home.php" class="nav-link">Home</a></li>
            <li class="nav-item"><a href="/taskdesk/frontend/contact.php" class="nav-link">Contact</a></li>

            <li class="nav-item" style="position: relative;">
                <img src="/taskdesk/connect/avatar.php" id="nav-profile-img" alt="Profile" style="width: 45px; height: 45px; border-radius: 50%; object-fit: cover; border: 2px solid #5E7161; cursor: pointer;">

                <div id="profile-dropdown" class="profile-popup">
                    <div class="popup-header">
                        <div class="avatar-edit-box">
                            <img src="/taskdesk/connect/avatar.php" id="dropdown-avatar-preview" alt="">
                            <label for="upload-photo" class="camera-badge"><i class="fas fa-camera"></i></label>
                            <input type="file" id="upload-photo" hidden accept="image/*">
                        </div>
                        <h3 id="display-username"><?php echo $display_name; ?>!</h3>
                    </div>
                    <div class="popup-body">
                        <button type="button" class="popup-btn" onclick="changeUsername()">
                            <i class="fas fa-edit"></i> Change Name
                        </button>
                        <button type="button" class="popup-btn logout-btn" onclick="confirmLogout()">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </button>
                    </div>
                </div>
            </li>
        </ul>
    </nav>
</header>
