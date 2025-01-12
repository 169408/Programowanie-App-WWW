<?php

use app\CartManager;
use app\ProductManager;
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require_once "../../config/constants.php";
require_once MAINDIR . "/cfg.php";
require_once MAINDIR . "/app/UserManager.php";
require_once MAINDIR . "/app/CartManager.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../forms/login_form.php");
    exit();
}

$cartManager = new CartManager($link);
$completedCarts = $cartManager->getAllCompletedCarts($_SESSION['user_id']);
$cart_ids = [];
foreach ($completedCarts as $cart) {
    array_push($cart_ids, $cart["id_cart"]);
}
$allProductsOwnUser = $cartManager->getAllProductsOwnUser($cart_ids);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <base href="<?=ROOT?>/">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase History</title>
    <link rel="stylesheet" href="css/main.css">
    <style>
        h1 {
            margin: 20px 0;
            padding: 20px;
        }

        .cart-item {
            margin: 10px 0;
            position: relative;
        }

        .cart-item .item-details {
            flex: 0.3;
            width: auto;
        }

        .cart-item .right {
            bottom: 20px;
            color: #666;
            font-size: 12px;
            right: 30px;
            position: absolute;
        }
    </style>
</head>
<body>
<div id="page">
    <div class="container">
        <h1>Purchase History</h1>
        <?php if (count($allProductsOwnUser) > 0): ?>
            <?php
            foreach ($allProductsOwnUser as $item){
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
                        <div class="quantity-control"><span><?= $item['quantity']; ?></span></div>
                        <p class="item-total">Total: $<?= number_format($productTotal, 2); ?></p>
                    </div>
                    <div class="right">
                        <?php
                            $date = $cartManager->getCartById($item["id_cart"])["updated_at"];
                        ?>
                        <p class="date"><?=$date?></p>
                    </div>
                </div>
                <?php
            } ?>
        <?php else: ?>
            <p class="no-purchases">You have not made any purchases yet.</p>
        <?php endif; ?>
        <div class="back-container">
            <a href="shop/user/index.php" class="back-to-shop-btn">← Back to Shop</a>
        </div>
    </div>
</div>
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

