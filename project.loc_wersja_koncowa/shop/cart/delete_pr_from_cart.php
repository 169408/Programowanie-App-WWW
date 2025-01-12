<?php

require_once "../../config/constants.php";
require_once MAINDIR . "/app/CartManager.php";
require_once MAINDIR . "/cfg.php";

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['product_id']) && is_numeric($_POST['product_id'])) {
        $productId = intval($_POST['product_id']);
        $cartManager = new \app\CartManager($link);
        $new_cart = $cartManager->createCart($_SESSION['user_id']);
        $cart_id = $new_cart["id_cart"];
        if ($cartManager->removeProductFromCart($cart_id, $productId)) {
            echo "Product successfully added to cart!";

            header('Location: index.php');
            exit;
        } else {
            echo "Failed to delete product from cart.";
        }
    } else {
        echo "Invalid product ID.";
    }
} else {
    echo "Invalid request method.";
}