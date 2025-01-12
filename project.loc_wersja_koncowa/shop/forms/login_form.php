<?php

require_once "../../config/constants.php";
require_once "../../app/UserManager.php"; // Include the UserManager class
require_once "../../cfg.php";

$userManager = new \app\UserManager($link);

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $user = $userManager->getUserByEmail($email);
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        echo "Login successful!";
        header("location: ../main.php");
    } else {
        echo "Invalid email or password.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <base href="<?=ROOT?>/">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="css/main.css" />
</head>
<body>
<div class="wallpaper">
    <div class="container">
        <div class="user_login_form">
            <h2>Login</h2>
            <form action="shop/forms/login_form.php" method="post">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
                <br>
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
                <br>
                <button type="submit">Login</button>
                <a href="shop/forms/register_form.php">Register</a>
            </form>
        </div>
    </div>
</div>
</body>
</html>
