<?php
include('DBconnect.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $medication        = $_POST['medication'] ?? null;
    $exercise_duration = $_POST['exercise_duration'] ?? null;
    $sleep_hours       = $_POST['sleep_hours'] ?? null;
    $diet_type         = $_POST['diet_type'] ?? null;

    // Resolve the actual numeric UserID
    $userID = null;

    if (isset($_SESSION['UserID'])) {
        $userID = $_SESSION['UserID'];
    } elseif (isset($_SESSION['email'])) {
        // Look up UserID by email (since login.php stores only 'email')
        $stmt = $conn->prepare("SELECT UserID FROM user WHERE Email = ? LIMIT 1");
        $stmt->bind_param("s", $_SESSION['email']);
        $stmt->execute();
        $stmt->bind_result($foundID);
        if ($stmt->fetch()) {
            $userID = $foundID;
            // Optional: cache it so other pages can use it
            $_SESSION['UserID'] = $userID;
        }
        $stmt->close();
    }

    if (!$userID) {
        echo "You must be logged in.";
        exit;
    }

    // Insert using the real UserID (prepared statement)
    $stmt = $conn->prepare("
        INSERT INTO health_factor (UserID, Medication, Exercise_duration, Sleep_Hours, Diet_Type)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("isdds", $userID, $medication, $exercise_duration, $sleep_hours, $diet_type);

    if ($stmt->execute()) {
        echo "Health factors updated!";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Health Factors</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Health Factors</h1>
        <form method="POST">
            <input type="text" name="medication" placeholder="Current Medications">
            <input type="number" name="exercise_duration" placeholder="Exercise Duration (in hours)">
            <input type="number" name="sleep_hours" placeholder="Sleep Hours">
            <input type="text" name="diet_type" placeholder="Diet Type">
            <button type="submit">Save Health Factors</button>
        </form>
		
		<a href="home.php"><button>Back to Home</button></a>
    </div>
</body>
</html>
