<?php
session_start();
require_once "../../config/constants.php";
require_once MAINDIR . "/cfg.php";
require_once MAINDIR . "/app/UserManager.php";

// Перевірка авторизації користувача
if (!isset($_SESSION['user_id'])) {
    header("Location: ../forms/login_form.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$userManager = new \app\UserManager($link);
$user = $userManager->readUserById($user_id);

if (!$user) {
    echo "User not found!";
    exit;
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <base href="<?=ROOT?>/">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Профіль користувача</title>
    <link rel="stylesheet" href="css/main.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="container-data">
        <h2>Your profile</h2>
        <p><strong>Name:</strong> <?= htmlspecialchars($user['name']) ?></p>
        <p><strong>Mail address:</strong> <?= htmlspecialchars($user['email']) ?></p>

        <div class="button-container">
            <a href="shop/user/purchase_history.php">Purchase history</a>
            <a href="shop/user/settings.php">Change data</a>
        </div>
        <div class="cart-footer">
            <a href="shop/main.php" class="back-to-shop-btn">← Back to Shop</a>
        </div>
        <div class="logout">
            <form action="shop/user/logout.php" method="POST" style="display: inline;">
                <button type="submit" class="logout-btn">Log Out</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>
