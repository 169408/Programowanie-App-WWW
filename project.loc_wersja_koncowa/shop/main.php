<?php

/*Strona do wyświetlenia skłepu i wszystkich produktów
 Z krótkim opisem produktu*/

//error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

use app\CartManager;
use app\CategoryManager;
use app\ProductManager;

require_once "../config/constants.php";
require_once "../cfg.php";
require_once "../app/CartManager.php";
require_once "../app/ProductManager.php";
require_once "../app/CategoryManager.php";

session_start();
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header("Location: forms/login_form.php");
}

$cartManager = new CartManager($link);
$productManager = new ProductManager($link);
$categoryManager = new CategoryManager($link);

if(!empty($_GET) && isset($_GET['category'])) {
    $category = $categoryManager->readCategoryByName($_GET['category'])->fetch_assoc();
    $category_id = $category["id"];
    $categories = [];
    array_push($categories, $category_id);
    $child_categories = $categoryManager->getChildrenIds($category_id);
    foreach ($child_categories as $child) {
        array_push($categories, $child);
    }
    $result = $productManager->readProductsByCategories($categories);
} elseif (!empty($_GET) && isset($_GET['query'])) {
    $result = $productManager->readProductByName($_GET['query']);
} else {
    $result = $productManager->readAllProducts();
}

if (isset($_GET["message"])) {
    echo "<p class='message'>" . $_GET["message"] . "</p>";
} elseif (isset($_GET["error"])) {
    echo "<p class='message error'>" . $_GET["error"] . "</p>";
}
?>

<!doctype html>
<html lang="en">
<head>
    <base href="<?=ROOT?>/">
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Shop</title>
    <link rel="stylesheet" href="css/main.css">
    <script src="scripts/img_windows.js"></script>
</head>
<body>
<div id="page">
    <header class="header_product">
        <div class="container">
            <a href="shop/main.php" class="logo">
                <img src="uploads/shop_logo.png" alt="Logo" />
            </a>
            <div class="menu-icon" id="menu-icon">
                <!-- Іконка для бургера -->
                ☰
            </div>
            <div class="search-bar">
                <form action="shop/main.php" method="GET">
                    <input type="text" name="query" placeholder="Search product by name..." />
                    <button type="submit">Search</button>
                </form>
            </div>
            <div class="menu">
                <ul>
                    <li class="dropdown">
                        <a href="shop/main.php">Kategorie</a>
                        <ul class="dropdown-menu">
                            <?php
                            $categoryManager = new CategoryManager($link);
                            $categories = $categoryManager->readAllCategories();
                            while ($row = $categories->fetch_assoc()) {
                                ?>
                                <a href="shop/main.php?category=<?=$row['name']?>"><?=$row['name']?></a>
                            <?php
                            }
                            ?>
                        </ul>
                    </li>
                    <li>
                        <a href="shop/user/index.php" class="settings-btn">
                            <img src="uploads/settings_icon.png" alt="Settings" class="settings-icon">
                        </a>
                    </li>
                </ul>
            </div>
            <div class="mobile-menu" id="mobile-menu">
                <ul>
                    <li>
                        <a href="shop/main.php" id="dropdown-toggle">Kategorie</a>
                        <ul class="mobile-dropdown-menu" id="mobile-dropdown-menu">
                            <?php
                            $categories = $categoryManager->readAllCategories();
                            while ($row = $categories->fetch_assoc()) {
                                ?>
                                <a href="shop/main.php?category=<?=$row['name']?>"><?=$row['name']?></a>
                                <?php
                            }
                            ?>
                        </ul>
                    </li>
                    <li>
                        <a href="shop/user/index.php">Settings</a>
                    </li>
                </ul>
            </div>
        </div>
    </header>
    <div class="products">
        <div class="container">
            <div class="wallpaper">
                <?php
                while($row = $result->fetch_assoc()) {
                    $images = json_decode($row["image"], true);
                    ?>
                    <div class="card" data-id="<?=$row['id']?>">
                        <p><?=$row["title"]?></p>
                        <?php
                        if (empty($images)) {
                            ?>
                            <img src="uploads/products_img/no_img.jpg" alt="None" />
                            <?php
                        }
                        foreach ($images as $productImg) {
                            ?>
                            <img src="<?=$productImg?>" alt="<?=$row['title']?>.img" class="clickable-image" />
                            <?php
                        }
                        $shortDescription = mb_substr($row["description"], 0, 100, "UTF-8");
                        ?>
                        <p><?= $shortDescription ?><?= strlen($row["description"]) > 100 ? "..." : "" ?></p>
                        <p class="price"><b><?=$row["price"]?>$</b></p>
                        <form action="shop/cart/add_to_cart.php" method="POST">
                            <input type="hidden" name="product_id" value="<?=$row['id']?>">
                            <button type="submit">Add to Cart</button>
                        </form>
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>
    </div>
    <div id="cart-icon">
        <a href="shop/cart/index.php">
            <img src="uploads/1413908.png" alt="Cart Icon">
            <span id="cart-count">
                <?php
                // Отримуємо кількість товарів у кошику
                $cartItemCount = $cartManager->getCartItemCount($_SESSION['user_id']);
                echo $cartItemCount;
                ?>
            </span>
        </a>
    </div>
    <div id="imageModal" class="modal">
        <img id="modalImage" src="" alt="Modal Image">
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const cards = document.querySelectorAll('.card');

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

    document.addEventListener('DOMContentLoaded', function () {
        const menuIcon = document.getElementById('menu-icon');
        const mobileMenu = document.getElementById('mobile-menu');

        menuIcon.addEventListener('click', function () {
            mobileMenu.classList.toggle('open');
        });

        // Закриття меню при кліку поза ним
        document.addEventListener('click', function (event) {
            if (!mobileMenu.contains(event.target) && event.target !== menuIcon) {
                mobileMenu.classList.remove('open');
            }
        });
    });

    document.addEventListener('DOMContentLoaded', function () {
        const dropdownToggle = document.getElementById('dropdown-toggle');
        const dropdownMenu = document.getElementById('mobile-dropdown-menu');

        dropdownToggle.addEventListener('click', function (event) {
            event.preventDefault();
            if (dropdownMenu.style.display === 'block') {
                dropdownMenu.style.display = 'none';
            } else {
                dropdownMenu.style.display = 'block';
            }
        });
    });
</script>
</body>
</html>

