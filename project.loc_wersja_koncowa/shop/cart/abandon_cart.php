<?php
require_once "../../config/constants.php";
require_once MAINDIR . "/app/CartManager.php";
require_once MAINDIR . "/cfg.php";

session_start();

if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header("Location: forms/login_form.php");
    exit();
}

$cartManager = new \app\CartManager($link);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cart_id']) && !empty($_POST['cart_id'])) {
    $cart_id = intval($_POST['cart_id']);
    $cartManager->abandonCart($cart_id);
    header("Location: ../main.php?message=Cart abandoned successfully");
    exit();
} else {
    header("Location: index.php?error=Invalid request");
    exit();
}
