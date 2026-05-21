<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'lecturer') {
    header("Location: login.php");
    exit();
}

$lecturer_name = $_SESSION['fullname'];
$message = '';
$messageType = '';

if (isset($_POST['bulk_create'])) {
    $students_text = $_POST['students_list'];
    $default_password = $_POST['default_password'];
    $course = mysqli_real_escape_string($conn, $_POST['course']);

    $lines = explode("\n", trim($students_text));
    $created = 0;
    $skipped = 0;
    $errors = [];

    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line)) continue;
        $parts = str_getcsv($line);
        if (count($parts) < 4) {
            $errors[] = "Invalid format: $line";
            continue;
        }
        $reg_number = mysqli_real_escape_string($conn, strtoupper(trim($parts[0])));
        $fullname = mysqli_real_escape_string($conn, trim($parts[1]));
        $email = mysqli_real_escape_string($conn, trim($parts[2]));
        $phone = mysqli_real_escape_string($conn, trim($parts[3]));
        $hashed_password = password_hash($default_password, PASSWORD_DEFAULT);

        $check = $conn->query("SELECT id FROM students WHERE reg_number='$reg_number' OR email='$email'");
        if ($check->num_rows > 0) {
            $skipped++;
            continue;
        }

        $sql = "INSERT INTO students (reg_number, fullname, email, phone, course, password, role, account_status, must_change_password, created_by) 
                VALUES ('$reg_number', '$fullname', '$email', '$phone', '$course', '$hashed_password', 'student', 'verified', 1, '$lecturer_name')";
        if ($conn->query($sql)) $created++;
        else $errors[] = "Failed to create $reg_number: " . $conn->error;
    }
    $message = "✅ Created: $created accounts | ⏭️ Skipped (exist): $skipped";
    if (!empty($errors)) { $message .= "\nErrors: " . implode(", ", $errors); $messageType = 'warning'; }
    else $messageType = 'success';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Student Accounts - KaRU</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        textarea { width: 100%; font-family: monospace; border-radius: 12px; padding: 10px; }
        .instruction-box { background: #eef2fa; padding: 15px; border-radius: 14px; margin-bottom: 20px; border-left: 4px solid #2980b9; }
        .instruction-box pre { background: #e0e7ef; padding: 10px; border-radius: 10px; overflow-x: auto; }
        .alert-success { background: #d4edda; color: #155724; padding: 15px; border-radius: 12px; margin-bottom: 20px; }
        .alert-warning { background: #fff3cd; color: #856404; padding: 15px; border-radius: 12px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>KaRU Attendance Tracker</h1>
        <p>Welcome, <?= htmlspecialchars($lecturer_name) ?> (Lecturer)</p>
    </div>

    <div class="nav">
        <a href="lecturer-dashboard.php">Dashboard</a>
        <a href="attendance-report.php">Today's Report</a>
        <a href="create-students.php">Create Students</a>
        <a href="pending-approvals.php">Pending Approvals</a>
        <a href="all-students.php">All Students</a>
        <a href="change-password.php">Change Password</a>
        <a href="logout.php" style="color:#ffaa66;">Logout</a>
    </div>

    <div class="container">
        <div class="card">
            <h2>Bulk Create Student Accounts</h2>
            <?php if ($message): ?>
                <div class="alert-<?= $messageType === 'success' ? 'success' : 'warning' ?>"><?= nl2br(htmlspecialchars($message)) ?></div>
            <?php endif; ?>
            <div class="instruction-box">
                <h3>📌 Instructions:</h3>
                <p>Paste student data below, <strong>one student per line</strong>, in CSV format:</p>
                <pre>REG_NUMBER,Full Name,Email,Phone</pre>
                <p><strong>Example:</strong></p>
                <pre>X123/4567Y/24,John Mwangi,john@example.com,0712345678
Y987/6543P/24,Jane Wanjiku,jane@example.com,0723456789</pre>
            </div>

            <form method="POST">
                <div class="form-group"><label><strong>Course / Program for ALL students:</strong></label><br><input type="text" name="course" required placeholder="e.g. Bachelor of Computer Science"></div>
                <div class="form-group"><label><strong>Default Password:</strong></label><br><input type="text" name="default_password" required value="Karu@2024"><small style="color:#e67e22;"> ⚠️ Students must change on first login</small></div>
                <div class="form-group"><label><strong>Student List (CSV):</strong></label><br><textarea name="students_list" rows="12" required placeholder="Paste student data here..."><?= htmlspecialchars($_POST['students_list'] ?? '') ?></textarea></div>
                <button type="submit" name="bulk_create" class="btn" style="font-size:18px; padding:15px 40px;">✅ Create Student Accounts</button>
            </form>
        </div>
    </div>
</body>
</html>