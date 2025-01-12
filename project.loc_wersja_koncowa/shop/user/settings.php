<?php

session_start();
require_once "../../config/constants.php";
require_once MAINDIR . "/cfg.php";
require_once MAINDIR . "/app/UserManager.php";

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

// Обробка змін
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $user['name'];
    $email = $user['email'];
    $password = $user['password'];

    if (isset($_POST['update_name'])) {
        $userManager->updateUserNameById($user_id, $_POST['name']);

        echo "Name successfully changed!";
    } elseif (isset($_POST['update_email'])) {
        $userManager->updateUserEmailById($user_id, $_POST['email']);

        echo "Email successfully changed!";
    } elseif (isset($_POST['update_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];

        if (password_verify($current_password, $user['password'])) {
            $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);
            $userManager->updateUserPasswordById($user_id, $hashed_new_password);

            echo "Password successfully changed!";
        } else {
            echo "Current password is wrong!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <base href="<?=ROOT?>/">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Налаштування користувача</title>
    <link rel="stylesheet" href="css/main.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 15px 0;
        }
    </style>
</head>
<body>
<div id="page">
    <div class="wallpaper">
        <div class="container">
            <h2 style="margin: 10px; text-align: center">Change data</h2>
            <?php
            if(empty($_GET)) {
                ?>
                <div class="sett-cards">
                    <div class="card">
                        <h4>Change Name</h4>
                        <p>Change your profile name.</p>
                        <a href="shop/user/settings.php?change=name" class="btn">Choose</a>
                    </div>
                    <div class="card">
                        <h4>Change email address</h4>
                        <p>Change your email address.</p>
                        <a href="shop/user/settings.php?change=mail" class="btn">Choose</a>
                    </div>
                    <div class="card">
                        <h4>Change password</h4>
                        <p>Update your login password.</p>
                        <a href="shop/user/settings.php?change=password" class="btn">Choose</a>
                    </div>
                </div>
                <br />
                <div class="div_actions">
                    <a href="shop/user/index.php">Back</a>
                </div>
            <?php
            }
            if (isset($_GET["change"])) {
                ?>
            <div class="forms">
                <?php if($_GET["change"] == "name") {
                    ?>
                    <div class="form-container">
                        <h2>Change Name</h2>
                        <form action="shop/user/settings.php" method="POST">
                            <label for="name">Name</label>
                            <input type="text" id="name" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
                            <button type="submit" name="update_name">Update name</button>
                        </form>
                    </div>
                <?php
                } elseif($_GET["change"] == "mail") {
                    ?>
                    <div class="form-container">
                        <h2>Change email address</h2>
                        <form action="shop/user/settings.php" method="POST">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                            <button type="submit" name="update_email">Update your email</button>
                        </form>
                    </div>
                    <?php
                } elseif($_GET["change"] == "password") {
                    ?>
                    <div class="form-container">
                        <h2>Change password</h2>
                        <form action="shop/user/settings.php" method="POST">
                            <label for="current_password">Current password</label>
                            <input type="password" id="current_password" name="current_password" required>

                            <label for="new_password">New password</label>
                            <input type="password" id="new_password" name="new_password" required>

                            <button type="submit" name="update_password">Update password</button>
                        </form>
                    </div>
                <?php
                } ?>
            </div>
                <a href="shop/user/settings.php">← Back</a>
            <?php
            }
            ?>
        </div>
    </div>
</div>
</body>
</html>
