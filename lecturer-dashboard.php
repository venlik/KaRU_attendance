<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'lecturer') {
    header("Location: login.php");
    exit();
}

$fullname = $_SESSION['fullname'];
$today = date('Y-m-d');

$total_students = $conn->query("SELECT COUNT(*) as total FROM students WHERE role='student' AND account_status='verified'")->fetch_assoc()['total'];
$signed_today = $conn->query("SELECT COUNT(DISTINCT student_id) as signed FROM attendance WHERE class_date = '$today' AND status = 'Present'")->fetch_assoc()['signed'];
$absent_today = $total_students - $signed_today;
$pending_count = $conn->query("SELECT COUNT(*) as count FROM students WHERE account_status = 'pending'")->fetch_assoc()['count'];
?>

<!DOCTYPE html>
<html lang="en"
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lecturer Dashboard - KaRU</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .badge {
            background: #e74c3c;
            color: white;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: bold;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .stat-card {
            text-align: center;
            padding: 25px;
            border-radius: 10px;
            color: white;
        }

        .stat-card.blue {
            background: linear-gradient(135deg, #2980b9, #3498db);
        }

        .stat-card.green {
            background: linear-gradient(135deg, #27ae60, #2ecc71);
        }

        .stat-card.red {
            background: linear-gradient(135deg, #c0392b, #e74c3c);
        }

        .stat-card.orange {
            background: linear-gradient(135deg, #e67e22, #f39c12);
        }

        .stat-card h2 {
            font-size: 48px;
            margin: 10px 0;
        }

        .stat-card p {
            font-size: 16px;
            opacity: 0.9;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>KaRU Attendance Tracker</h1>
        <p>Welcome, <?= htmlspecialchars($fullname) ?> (Lecturer)</p>
    </div>

    <div class="nav">
        <a href="lecturer-dashboard.php">Dashboard</a>
        <a href="attendance-report.php">Today's Report</a>
        <a href="create-students.php">Create Students</a>
        <a href="pending-approvals.php">Pending Approvals</a>
        <a href="all-students.php">All Students</a>
        <a href="change-password.php" style="color:#ffd700;">Change Password</a>
        <a href="logout.php" style="color:#ffaa66;">Logout</a>
    </div>

    <div class="container">
        <?php if ($pending_count > 0): ?>
            <div
                style="background: #fff3cd; border: 2px solid #f39c12; border-radius: 10px; padding: 20px; margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between;">
                <div>
                    <h3 style="color: #856404; margin: 0;">⚠️ Pending Student Approvals</h3>
                    <p style="margin: 5px 0 0 0;color: #2ecc71">There are <strong><?= $pending_count ?> student(s)</strong> waiting for
                        verification.</p>
                </div>
                <a href="pending-approvals.php" class="btn" style="background: #f39c12; font-size: 18px;">Review Now →</a>
            </div>
        <?php endif; ?>

        <div class="dashboard-grid">
            <div class="stat-card blue">
                <p>Total Students</p>
                <h2><?= $total_students ?></h2>
            </div>
            <div class="stat-card green">
                <p>Signed In Today</p>
                <h2><?= $signed_today ?></h2>
            </div>
            <div class="stat-card red">
                <p>Absent Today</p>
                <h2><?= $absent_today ?></h2>
            </div>
            <div class="stat-card orange">
                <p>Pending Approvals</p>
                <h2><?= $pending_count ?></h2>
            </div>
        </div>

        <div class="card" style="text-align:center; margin-top:30px;">
            <p style="font-size:18px;"><strong>Today:</strong> <?= date('d M, Y') ?> | Attendance Rate: <span
                    class="<?= $total_students > 0 && ($signed_today / $total_students) * 100 >= 75 ? 'success' : 'danger' ?>"><?= $total_students > 0 ? round(($signed_today / $total_students) * 100, 1) : 0 ?>%</span>
            </p>
            <a href="attendance-report.php" class="btn" style="font-size:18px; padding:15px 40px; margin-top:20px;">View
                Full Today's Report</a>
        </div>
    </div>
</body>

</html>