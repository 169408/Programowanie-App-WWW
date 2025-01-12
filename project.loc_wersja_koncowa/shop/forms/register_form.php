<?php

require_once "../../app/UserManager.php"; // Include the UserManager class
require_once "../../cfg.php";
$userManager = new \app\UserManager($link);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if user already exists
    $existingUser = $userManager->getUserByEmail($_POST["email"]);
    if ($_POST["password"] !== $_POST["repeat_password"]) {
        echo "Passwords do not match. Please try again.";
    } else if ($existingUser) {
        echo "User with that email already exists!";
    } else {
        $userManager->createUser($_POST);
        echo "Registration successful!";
        header("location: login_form.php");
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
</head>
<body>
<h2>Register</h2>
<form action="register_form.php" method="post">
    <label for="name">Name:</label>
    <input type="text" id="name" name="name" required>
    <br>
    <label for="email">Email:</label>
    <input type="email" id="email" name="email" required>
    <br>
    <label for="password">Password:</label>
    <input type="password" id="password" name="password" required>
    <br>
    <label for="repeat_password">Repeat password:</label>
    <input type="password" id="repeat_password" name="repeat_password" required>
    <br/>
    <button type="submit">Register</button>
</form>
</body>
</html>
