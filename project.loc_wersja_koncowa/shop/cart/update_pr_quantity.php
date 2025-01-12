<?php

require_once "../../config/constants.php";
require_once MAINDIR . "/app/CartManager.php";
require_once MAINDIR . "/cfg.php";

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['product_id']) && is_numeric($_POST['product_id'])) {
        $productId = intval($_POST['product_id']);
        $quantity = intval($_POST['quantity']);
        $action = $_POST['action'];

        $cartManager = new \app\CartManager($link);
        $new_cart = $cartManager->createCart($_SESSION['user_id']);
        $cart_id = $new_cart["id_cart"];

        try {
            if ($action === 'increase') {
                $cartManager->updateProductQuantity($cart_id, $productId, $quantity + 1); // Dodać 1
            } elseif ($action === 'decrease') {
                $cartManager->updateProductQuantity($cart_id, $productId, $quantity - 1); // Odjąć 1
            }

            // Повертаємося назад до кошика
            header('Location: index.php');
            exit;
        } catch (Exception $e) {
            // Обробка помилки
            echo 'Error: ' . htmlspecialchars($e->getMessage());
        }
    } else {
        echo "Invalid product ID.";
    }
} else {
    echo "Invalid request method.";
}
