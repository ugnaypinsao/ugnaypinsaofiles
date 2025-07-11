<?php
include '../php/conn.php';
$db = new DatabaseHandler();
require 'head.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/admin_page.css">
</head>

<body>
    <div class="main-content">
        <h1>Welcome, Admin</h1>
        <nav class="button-container">
            <a href="barangay_data.php" class="button">🏠 Barangay Data</a>
            <a href="announcement.php" class="button">📢 Announcement</a>
            <a href="inbox.php" class="button">📩 Inbox</a>
            <a href="calendar.php" class="button">📅 Calendar</a>
            <a href="documents.php" class="button">📂 Documents</a>
            <a href="business.php" class="button">💰 Local Business</a>
            <a href="archive.php" class="button">📩 Message Archive</a>
            <a href="barangay_archive.php" class="button">🏠 Barangay Archive</a>
            <a href="logs.php" class="button">📂 System Logs</a>
            <a href="logout.php" class="button">🚪 Logout</a>
        </nav>
        <div class="activity">
            <p><strong>Last Activity:</strong> <span id="last-activity">No recent activity</span></p>
            <p><strong>Date & Time:</strong> <span id="last-activity-time">N/A</span></p>
        </div>
    </div>
    <script>
        // Update last activity dynamically (Example)
        document.addEventListener("DOMContentLoaded", function() {
            let lastActivity = localStorage.getItem("lastActivity") || "No recent activity";
            let lastActivityTime = localStorage.getItem("lastActivityTime") || "N/A";
            document.getElementById("last-activity").textContent = lastActivity;
            document.getElementById("last-activity-time").textContent = lastActivityTime;

            // Simulate updating activity (replace with real logic)
            document.querySelectorAll(".button").forEach(button => {
                button.addEventListener("click", function() {
                    localStorage.setItem("lastActivity", this.textContent.trim());
                    localStorage.setItem("lastActivityTime", new Date().toLocaleString());
                });
            });
        });
    </script>
    <script src="../assets/js/admin_page.js"></script>
</body>

</html>