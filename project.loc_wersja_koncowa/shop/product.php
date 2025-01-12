<?php

use app\ProductManager;

require_once "../config/constants.php";
require_once "../cfg.php";
require_once "../app/CartManager.php";
require_once "../app/ProductManager.php";

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid product ID.");
}

$productId = intval($_GET['id']);
$productManager = new ProductManager($link);
$product = $productManager->readProductById($productId);
$product = $product->fetch_assoc();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <base href="<?=ROOT?>/">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?=$product['title']?> - Product Details</title>
    <link rel="stylesheet" href="css/main.css">
    <script src="scripts/img_windows.js"></script>
</head>
<body>
<div class="product-details">
    <div class="container">
        <div class="product-card">
            <h1><?=$product['title']?></h1>
            <div class="product-info">
                <?php
                $images = json_decode($product['image'], true);
                if (!empty($images)) {
                    foreach ($images as $productImg) {
                        ?>
                        <img src="<?=$productImg?>" alt="<?=$product['title']?>.img" />
                <?php
                    }
                } else {
                    ?>
                    <img src="uploads/products_img/no_img.jpg" alt="No image" />
                <?php
                }
                ?>
                <p><strong>Description:</strong> <?=$product['description']?></p>
                <p><strong>Price:</strong> $<?=$product['price']?></p>
                <p><strong>VAT:</strong> <?=$product['vat']?>%</p>
                <p><strong>Count:</strong> <?=$product['count']?></p>
                <p><strong>Status:</strong> <?=$product['status']?></p>
                <p><strong>Category:</strong> <?=$product['category']?></p>
                <p><strong>Dimension:</strong> <?=$product['dimension']?></p>
                <p><strong>Material:</strong> <?=$product['material']?></p>
                <p><strong>Color:</strong> <?=$product['color']?></p>
                <p><strong>Discount:</strong> <?=$product['discount']?>%</p>
                <p><strong>Expiration Date:</strong> <?=$product['expiration_date']?></p>
                <p><strong>Created At:</strong> <?=$product['created_at']?></p>
                <p><strong>Updated At:</strong> <?=$product['updated_at']?></p>
            </div>
            <div class="add-to-cart">
                <form action="shop/cart/add_to_cart.php" method="POST">
                    <input type="hidden" name="product_id" value="<?=$product['id']?>">
                    <button type="submit" class="add-to-cart-btn">Add to Cart</button>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>

