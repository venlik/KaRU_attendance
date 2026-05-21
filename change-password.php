<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$fullname = $_SESSION['fullname'];
$role = $_SESSION['role'];
$message = '';
$messageType = '';

if (isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    $result = $conn->query("SELECT password FROM students WHERE id = $user_id");
    $row = $result->fetch_assoc();
    
    if (!password_verify($current_password, $row['password'])) {
        $message = "Current password is incorrect!";
        $messageType = 'error';
    } elseif ($new_password !== $confirm_password) {
        $message = "New passwords do not match!";
        $messageType = 'error';
    } elseif (strlen($new_password) < 6) {
        $message = "Password must be at least 6 characters!";
        $messageType = 'error';
    } elseif ($current_password === $new_password) {
        $message = "New password must be different from current password!";
        $messageType = 'error';
    } else {
        $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
        $conn->query("UPDATE students SET password = '$new_hash', must_change_password = 0 WHERE id = $user_id");
        
        $message = "Password changed successfully!";
        $messageType = 'success';
        
        $redirect = ($role == 'lecturer') ? 'lecturer-dashboard.php' : 'dashboard.php';
        echo "<script>setTimeout(function(){ window.location = '$redirect'; }, 2000);</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password - KaRU</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .password-container { max-width: 500px; margin: 0 auto; }
        .alert-success { background: #d4edda; color: #155724; padding: 15px; border-radius: 12px; margin-bottom: 20px; border: 1px solid #c3e6cb; }
        .alert-error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 12px; margin-bottom: 20px; border: 1px solid #f5c6cb; }
        .req-box { background: #eef2fa; padding: 15px; border-radius: 14px; margin-bottom: 20px; border-left: 4px solid #2980b9; }
    </style>
</head>
<body>
    <div class="header">
        <h1>KaRU Attendance Tracker</h1>
        <p><?= htmlspecialchars($fullname) ?> (<?= ucfirst($role) ?>)</p>
    </div>

    <div class="nav">
        <?php if ($role == 'lecturer'): ?>
            <a href="lecturer-dashboard.php">Dashboard</a>
            <a href="attendance-report.php">Today's Report</a>
            <a href="create-students.php">Create Students</a>
            <a href="pending-approvals.php">Pending Approvals</a>
            <a href="all-students.php">All Students</a>
            <a href="change-password.php" style="color:#ffd700;">Change Password</a>
            <a href="logout.php" style="color:#ffaa66;">Logout</a>
        <?php else: ?>
            <a href="dashboard.php">Dashboard</a>
            <a href="mark-attendance.php">Sign Attendance</a>
            <a href="view-attendance.php">My Record</a>
            <a href="change-password.php" style="color:#ffd700;">Change Password</a>
            <a href="logout.php" style="color:#ffaa66;">Logout</a>
        <?php endif; ?>
    </div>

    <div class="container">
        <div class="card password-container">
            <h2>🔒 Change Password</h2>
            
            <?php if ($message): ?>
                <div class="alert-<?= $messageType === 'success' ? 'success' : 'error' ?>">
                    <?= htmlspecialchars($message) ?>
                    <?php if ($messageType === 'success'): ?><br><small>Redirecting...</small><?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="req-box">
                <strong>Password Requirements:</strong>
                <ul style="margin: 8px 0 0 20px;"><li>At least 8 characters long</li><li>Must be different from current password</li><li>Use a mix of letters, numbers, and symbols</li></ul>
            </div>

            <form method="POST">
                <div class="form-group"><label><strong>Current Password:</strong></label><br><input type="password" name="current_password" required placeholder="Enter current password"></div>
                <div class="form-group"><label><strong>New Password:</strong></label><br><input type="password" name="new_password" required placeholder="Enter new password" minlength="6"></div>
                <div class="form-group"><label><strong>Confirm New Password:</strong></label><br><input type="password" name="confirm_password" required placeholder="Confirm new password"></div>
                <button type="submit" name="change_password" class="btn" style="width:100%; margin-top:10px;">Update Password</button>
            </form>
            
            <p style="text-align:center; margin-top:15px;"><a href="<?= $role == 'lecturer' ? 'lecturer-dashboard.php' : 'dashboard.php' ?>">← Back to Dashboard</a></p>
        </div>
    </div>
</body>
</html>