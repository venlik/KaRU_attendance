<?php 
include 'config.php'; 
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'lecturer') {
    header("Location: login.php");
    exit();
}

$today = date('Y-m-d');

$sql = "SELECT s.reg_number, s.fullname, s.course, a.status 
        FROM students s 
        LEFT JOIN attendance a ON s.id = a.student_id AND a.class_date = '$today'
        WHERE s.role = 'student' AND s.account_status = 'verified'
        ORDER BY s.fullname";

$result = $conn->query($sql);

$total = $conn->query("SELECT COUNT(*) as t FROM students WHERE role='student' AND account_status='verified'")->fetch_assoc()['t'];
$present = $conn->query("SELECT COUNT(*) as p FROM attendance WHERE class_date='$today' AND status='Present'")->fetch_assoc()['p'];
$absent = $total - $present;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Today's Attendance Report - KaRU</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .summary-card { background: rgba(255,255,255,0.9); border-radius: 16px; padding: 15px; text-align: center; flex:1; min-width: 100px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        .summary-card h3 { margin:0; font-size: 32px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>KaRU Attendance Tracker</h1>
        <p>Lecturer View</p>
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
            <h2>Today's Attendance Report (<?= date('d M, Y') ?>)</h2>
            
            <div style="display: flex; gap: 20px; margin: 20px 0; flex-wrap: wrap;">
                <div class="summary-card"><h3><?= $total ?></h3><small>Expected</small></div>
                <div class="summary-card" style="background:#d4edda;"><h3 style="color:#155724;"><?= $present ?></h3><small>Present</small></div>
                <div class="summary-card" style="background:#f8d7da;"><h3 style="color:#721c24;"><?= $absent ?></h3><small>Absent</small></div>
            </div>
            
            <table>
                <tr><th>Reg Number</th><th>Student Name</th><th>Course</th><th>Status</th></tr>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['reg_number']) ?></td>
                    <td><?= htmlspecialchars($row['fullname']) ?></td>
                    <td><?= htmlspecialchars($row['course']) ?></td>
                    <td class="<?= $row['status'] == 'Present' ? 'success' : 'danger' ?>"><strong><?= $row['status'] ?? 'Absent' ?></strong></td>
                </tr>
                <?php endwhile; ?>
            </table>
        </div>
    </div>
</body>
</html>