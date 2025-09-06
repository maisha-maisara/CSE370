<?php
session_start();
if (!isset($_SESSION['email'])) {
    header('Location: login.php');  // Redirect if not logged in
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Welcome to your Dashboard</h1>
        <p>Hello, <?php echo $_SESSION['email']; ?></p>
        <div class="buttons">
            <a href="health_factors.php"><button>Health Factors</button></a>
            <a href="progress_report.php"><button>Progress Report</button></a>
            <a href="journal.php"><button>Journal</button></a>
            <a href="profile.php"><button>Profile</button></a>
			<!-- AI Analyze Button -->
            <button onclick="window.location.href='ai_analysis.php'">AI Analyze</button>
        </div>
    </div>
</body>
</html>
