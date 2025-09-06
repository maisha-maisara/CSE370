<?php
include('DBconnect.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $gender = $_POST['gender'];
    $dob = $_POST['dob'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $height = $_POST['height'];
    $weight = $_POST['weight'];

    $query = "INSERT INTO user (Name, Gender, Date_of_birth, Email, Password, Height, Weight) 
              VALUES ('$name', '$gender', '$dob', '$email', '$password', '$height', '$weight')";
    if ($conn->query($query) === TRUE) {
        echo "Account created successfully!";
        header('Location: login.php');
    } else {
        echo "Error: " . $query . "<br>" . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Sign Up</h1>
        <form method="POST">
            <input type="text" name="name" placeholder="Full Name" required>
            <select name="gender">
                <option value="Male">Male</option>
                <option value="Female">Female</option>
                <option value="Other">Other</option>
            </select>
            <input type="date" name="dob" placeholder="Date of Birth" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="number" name="height" placeholder="Height (in cm)" required>
            <input type="number" name="weight" placeholder="Weight (in kg)" required>
            <button type="submit">Sign Up</button>
        </form>
    </div>
</body>
</html>
