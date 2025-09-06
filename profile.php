<?php
session_start();
include('DBconnect.php');

// Fetch user details from the database
$user_id = $_SESSION['email'];
$query = "SELECT * FROM user WHERE Email = '$user_id'";
$result = $conn->query($query);
$user = $result->fetch_assoc();

// Calculate BMI
$bmi = $user['Weight'] / (($user['Height'] / 100) ** 2);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Your Profile</h1>
        <p>Name: <?php echo $user['Name']; ?></p>
        <p>Email: <?php echo $user['Email']; ?></p>
        <p>Date of Birth: <?php echo $user['Date_of_birth']; ?></p>
        <p>Height: <?php echo $user['Height']; ?> cm</p>
        <p>Weight: <?php echo $user['Weight']; ?> kg</p>
        <p>BMI: <?php echo round($bmi, 2); ?></p>
        <a href="home.php"><button>Back to Home</button></a>
    </div>
</body>
</html>
