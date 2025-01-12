<?php

use app\ProductManager;

session_start();
require_once "../../cfg.php";
require_once "../../app/CartManager.php";
require_once "../../app/ProductManager.php";

if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header("Location: ../forms/login_form.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['product_id']) && is_numeric($_POST['product_id'])) {
        $productId = intval($_POST['product_id']);
        $productManager = new ProductManager($link);
        $product = $productManager->readProductById($productId)->fetch_assoc();
        $cartManager = new \app\CartManager($link);
        $new_cart = $cartManager->createCart($_SESSION['user_id']);
        if ($cartManager->addProductToCart(["id_cart" => $new_cart["id_cart"],"id_product" => $productId, "quantity" => 1, "price" => $product["price"]])) {
            echo "Product successfully added to cart!";
        } else {
            echo "Failed to add product to cart.";
        }
    } else {
        echo "Invalid product ID.";
    }
} else {
    echo "Invalid request method.";
}

