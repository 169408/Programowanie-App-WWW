<?php

error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "../../config/constants.php";

require_once MAINDIR . "/app/CartManager.php";
require_once MAINDIR . "/cfg.php";

session_start();
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header("Location: ../forms/login_form.php");
}

$cartManager = new \app\CartManager($link);
$active_cart = $cartManager->getActiveCart($_SESSION['user_id'])["id_cart"] ?? null;
//echo $active_cart;
if ($active_cart != null) {
    $cartItems = $cartManager->getCartItems($active_cart);
}

if (isset($_GET["message"])) {
    echo "<p class='message'>" . $_GET["message"] . "</p>";
} elseif (isset($_GET["error"])) {
    echo "<p class='message error'>" . $_GET["error"] . "</p>";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <base href="<?=ROOT?>/">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <link rel="stylesheet" href="css/main.css">
</head>
<body>
<div id="page">
    <div class="cart-container">
        <h1>Shopping Cart</h1>
            <div class="cart-items">
                <?php
                if (!empty($cartItems)) {
                    foreach ($cartItems as $item){
                        $productTotal = $item['price'] * $item['quantity']; ?>
                        <div class="cart-item" data-id="<?=$item['id_product']?>">
                            <div class="item-image">
                                <?php
                                $images = [];
                                if ($item["image"] != null) {
                                    $images = json_decode($item["image"], true);
                                }
                                if (isset($images) && empty($images)) {
                                ?>
                                <img src="uploads/products_img/no_img.jpg" alt="None" />
                                <?php
                                } else { foreach ($images as $productImg) {
                                ?>
                                <img src="<?=$productImg?>" alt="<?=$item['title']?>.img" class="clickable-image" />
                                <?php
                                }
                                }?>
                            </div>
                            <div class="item-details">
                                <h3 class="item-name"><?= htmlspecialchars($item['title']); ?></h3>
                                <p class="price"><b><?= number_format($item["price"], 2); ?>$</b></p>
                                <div class="quantity-control">
                                    <form action="shop/cart/update_pr_quantity.php" method="POST" class="update-form">
                                        <input type="hidden" name="product_id" value="<?= $item['id_product']; ?>">
                                        <input type="hidden" name="quantity" value="<?= $item['quantity']; ?>">
                                        <button type="submit" name="action" value="decrease">-</button>
                                        <span><?= $item['quantity']; ?></span>
                                        <button type="submit" name="action" value="increase">+</button>
                                    </form>
                                </div>
                                <p class="item-total">Total: $<?= number_format($productTotal, 2); ?></p>
                                <form action="shop/cart/delete_pr_from_cart.php" method="POST">
                                    <input type="hidden" name="product_id" value="<?= $item['id_product']; ?>">
                                    <button type="submit" class="delete-btn">Remove</button>
                                </form>
                            </div>
                        </div>
                <?php
                        }
                    } else {
                        echo '<tr><td colspan="5" class="empty-cart">Your cart is empty!</td></tr>';
                    } ?>
            </div>
        <div class="cart-footer">
            <h3>Total: $<?= number_format($cartManager->getCartTotal($active_cart), 2) ?></h3>
            <form action="shop/cart/complete_cart.php" method="POST" style="display: inline;">
                <input type="hidden" name="cart_id" value="<?= $active_cart ?>">
                <button type="submit" class="complete-cart-btn">Complete Cart</button>
            </form>
            <form action="shop/cart/abandon_cart.php" method="POST" style="display: inline;">
                <input type="hidden" name="cart_id" value="<?= $active_cart ?>">
                <button type="submit" class="abandon-cart-btn">Abandon Cart</button>
            </form>
            <a href="shop/main.php" class="back-to-shop-btn">← Back to Shop</a>
        </div>
    </div>
    <div id="imageModal" class="modal">
        <img id="modalImage" src="" alt="Modal Image">
    </div>
</div>
<script src="scripts/img_windows.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const cards = document.querySelectorAll('.cart-item');

        cards.forEach(card => {
            card.addEventListener('click', function (event) {

                if (event.target === this) {
                    const productId = this.dataset.id;
                    if (productId) {

                        window.location.href = `shop/product.php?id=${productId}`;
                    }
                }
            });
        });
    });
</script>
</body>
</html>
