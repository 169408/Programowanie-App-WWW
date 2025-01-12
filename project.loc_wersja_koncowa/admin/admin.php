<?php
// ------------------------------------------------------
//              Strona Admin
// ------------------------------------------------------

use app\AdminManager;
use app\CategoryManager;
use app\PageManager;
use app\ProductManager;

session_start();
require_once '../cfg.php';
require_once '../config/constants.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if(!$link) {
    die("Database connection failed: " . mysqli_connect_error());
}

// ------------------------------------------------------
//              Formularz logowania admina
// ------------------------------------------------------
/*
 * Wyświetla formularz logowania oraz błędy, jeśli występią
 * */
function LoginForm($error = '')
{
    $wynik = VIEWS . '/admin/forms/login_form.php';
    echo "<p style='color: red'>" . $error . "</p>";
    return $wynik;
}

require_once "../app/PageManager.php";
require_once "../app/CategoryManager.php";
require_once "../app/ProductManager.php";
require_once "../app/AdminManager.php";

$pageManager = new PageManager($link);

if (isset($_POST['action_page'])) {
    try {
        if ($_POST["action_page"] == "add") {
            $pageManager->createPage($_POST);
            header("location: admin.php?control=page");
        } else if ($_POST["action_page"] == "edit") {
            $pageManager->updatePage($_POST, $_POST["id"]);
            header("location: admin.php?control=page");
        } else if ($_POST["action_page"] == "delete") {
            $pageManager->deletePage($_POST["id"]);
            header("location: admin.php?control=page");
        }
    } catch (Exception $e) {
        $_SESSION['error_message'] = $e->getMessage();
        $_SESSION['action_page'] = $_POST["action_page"];
        header("location: show_form.php");
    }
}

$categoryManager = new CategoryManager($link);

if (isset($_POST['action_category'])) {
    try {
        if ($_POST["action_category"] == "add") {
            $categoryManager->addCategory($_POST);
            header("location: admin.php?control=category");
        } else if ($_POST["action_category"] == "edit") {
            $categoryManager->updateCategory($_POST, $_POST["id"]);
            header("location: admin.php?control=category");
        } else if ($_POST["action_category"] == "delete") {
            $productManager = new ProductManager($link);
            $productManager->deleteProductsByCategory($_POST["id"]);
            $categoryManager->deleteCategory($_POST["id"]);
            header("location: admin.php?control=category");
        }
    } catch (Exception $e) {
        $_SESSION['error_message'] = $e->getMessage();
        $_SESSION['action_category'] = $_POST["action_category"];
        header("location: show_form.php");
    }
}

$queryallcategories = $categoryManager->readAllCategories();
while ($row = $queryallcategories->fetch_assoc()) {
    $allcategories[] = $row; // Додаємо кожен рядок як масив до результатів
}

$productManager = new ProductManager($link);

if (isset($_POST['action_product'])) {
    try {
        if ($_POST["action_product"] == "add") {
            $productManager->addProduct($_POST);
            header("location: admin.php?control=product");
        } else if ($_POST["action_product"] == "edit") {
            $productManager->updateProduct($_POST, $_POST["id"]);
            header("location: admin.php?control=product");
        } else if ($_POST["action_product"] == "delete") {
            $productManager->deleteProduct($_POST["id"]);
            header("location: admin.php?control=product");
        }
    } catch (Exception $e) {
        $_SESSION['error_message'] = $e->getMessage();
        $_SESSION['action_product'] = $_POST["action_product"];
        header("location: show_form.php");
    }
}

$adminManager = new AdminManager($link);

?>

<!doctype html>
<html lang="en">
<head>
    <base href="<?=ROOT?>/">
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/admin.css">
    <script src="scripts/img_windows.js"></script>
    <title>Document</title>
</head>
<body>
<div id="page">
    <?php
    /*
     * Sprawdzenie czy admin się zalogował*/

    if (isset($_POST['submit'])) {
        $admin = $adminManager->getAdminByLogin($_POST["login"]);
        if ($admin && password_verify($_POST["password"], $admin['password'])) {
            $_SESSION['admin_id'] = $admin['user_id'];
            $_SESSION['logged_in'] = true;
            echo "Login successful!";
            header("location: admin.php");
        } else {
            include(LoginForm('Nieprawidłowe dane logowania.'));
            exit;
        }
    }
    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
        include(LoginForm());
        exit;
    }
    ?>

    <header style="background: #4af3f2">Hellow Admin <?=$adminManager->readAdminById($_SESSION["admin_id"])["login"]?></header>
    <div id="admin_board" class="admin_board content">
        <?php
        /*
         * Jeżeli jeszcze nie wybrałeś opcji w swoim panelu CMS
         * to możesz to zrobić , lub wyłoguj się
         * */

        if (empty($_GET)) {
            ?>
            <div class="center_content">
                <a href="admin/admin.php?control=page" style="display: block;">Page Controller</a>
                <a href="admin/admin.php?control=category" style="display: block;">Category Controller</a>
                <a href="admin/admin.php?control=product" style="display: block;">Product Controller</a>
            </div>
            <div class="logout-admin">
                <form action="admin/logout.php" method="post">
                    <button class="logout-button" type="submit">Log Out</button>
                </form>
            </div>
        <?php
        } else {
            ?>
            <a href="admin/admin.php" style="display: block;">Wrócić na główną</a>
            <?php
        }
        ?>
        <br/>
        <p>----------------------------------------------------------------</p>
        <br/>
        <?php
        /*
         * Jeżeli jest wybrana jedna z opcij, sprawdza się która
         * */

            if(isset($_GET["control"]) && $_GET["control"] == 'page'){
                ?>
                <div class="pages_controller">
                    <div class="wallpaper">
                        <div class="container">
                            <h2>Strony:</h2>
                            <?php

                            $pageManager->pageList();
                            ?>
                            <hr style="margin: 9px">

                            <form action="admin/show_form.php" style="display: inline" method="post">
                                <button type="submit" name="action_page" value="add">add new page</button>
                            </form>
                        </div>
                    </div>
                </div>
        <?php
            } else if(isset($_GET["control"]) && $_GET["control"] == 'category') {
                ?>
                <div class="category_controller">
                    <div class="wallpaper">
                        <div class="container">
                            <h2>Categorie:</h2>
                            <?php
                            if (!empty($allcategories)) {
                                $categoryManager->displayCategoryTree($allcategories);
                            }
                            ?>
                            <hr style="margin: 9px">

                            <form action="admin/show_form.php" style="display: inline" method="post">
                                <button type="submit" name="action_category" value="add">add category</button>
                            </form>
                        </div>
                    </div>
                </div>
        <?php
            } else if(isset($_GET["control"]) && $_GET["control"] == 'product') {
                ?>
                <div class="product_controller">
                    <div class="wallpaper">
                        <div class="container container-full">
                            <h2>All products:
                            <div class="table-container">
                                <table border="1">
                                    <tr class="head_lines">
                                        <?php
                                            $allproducts = $productManager->readAllProducts();
                                            $product = $allproducts->fetch_assoc();
                                            if (!empty($product)) {
                                                foreach ($product as $productKey => $productValue) {
                                                    ?>
                                                    <td><?=$productKey?></td>
                                                        <?php
                                                }
                                        ?>
                                        <td>Actions</td>
                                    </tr>
                                    <tr>
                                        <?php
                                        foreach ($product as $productKey => $productValue) {
                                            if ($productKey == "image") {
                                                if(empty($productValue) || is_null($productValue)) {
                                                    ?>
                                                    <td></td>
                                                    <?php
                                                } else {
                                                    $images = json_decode($productValue, true); ?>
                                                    <td>
                                                        <?php
                                                        foreach ($images as $productImg) {
                                                            ?>
                                                            <img src="<?=$productImg?>" alt="1" class="clickable-image" style="height: 60px; width: 70px" />
                                                            <?php
                                                        } ?>
                                                    </td>
                                                    <?php
                                                }
                                            } else if($productKey == "category") {
                                                $productCategory = $categoryManager->readCategoryById($productValue);
                                                $productCategory = $productCategory->fetch_assoc();
                                                ?>
                                                <td><?=$productCategory["name"]?></td>
                                                <?php
                                            } else {
                                                ?>
                                                <td><?=$productValue?></td>
                                                <?php
                                            }
                                        }
                                        ?>
                                        <td>
                                            <form action='admin/show_form.php' style='display:inline;' method='post'>
                                                <input type='hidden' name='id' value="<?=$product['id']?>">
                                                <button type='submit' name='action_product' value='edit'>edit</button>
                                            </form>
                                            <form action='admin/show_form.php' style='display:inline;' method='post'>
                                                <input type='hidden' name='id' value="<?=$product['id']?>">
                                                <button type='submit' name='action_product' value='delete'>delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php
                                        while ($product = $allproducts->fetch_assoc()) {
                                            ?>
                                            <tr>
                                                <?php
                                                foreach ($product as $productKey => $productValue) {
                                                    if ($productKey == "image") {
                                                        if(empty($productValue) || is_null($productValue)) {
                                                            ?>
                                                            <td></td>
                                                                <?php
                                                        } else {
                                                        $images = json_decode($productValue, true); ?>
                                                        <td>
                                                        <?php
                                                        foreach ($images as $productImg) {
                                                            ?>
                                                            <img src="<?=$productImg?>" alt="1" class="clickable-image" style="height: 60px; width: 70px" />
                                                            <?php
                                                        } ?>
                                                        </td>
                                                        <?php
                                                        }
                                                    } else if($productKey == "category") {
                                                        $productCategory = $categoryManager->readCategoryById($productValue);
                                                        $productCategory = $productCategory->fetch_assoc();
                                                        ?>
                                                        <td><?=$productCategory["name"]?></td>
                                                        <?php
                                                    } else {
                                                        ?>
                                                        <td><?=$productValue?></td>
                                                        <?php
                                                    }
                                                }
                                                ?>
                                                <td>
                                                    <form action='admin/show_form.php' style='display:inline;' method='post'>
                                                        <input type='hidden' name='id' value="<?=$product['id']?>">
                                                        <button type='submit' name='action_product' value='edit'>edit</button>
                                                    </form>
                                                    <form action='admin/show_form.php' style='display:inline;' method='post'>
                                                        <input type='hidden' name='id' value="<?=$product['id']?>">
                                                        <button type='submit' name='action_product' value='delete'>delete</button>
                                                    </form>
                                                </td>
                                            </tr>
                                                <?php
                                            }
                                        }
                                    ?>
                                </table>
                            </div>
                            <hr style="margin: 9px">

                            <form action="admin/show_form.php" style="display: inline" method="post">
                                <button type="submit" name="action_product" value="add">add product</button>
                            </form>
                            <div id="imageModal" class="modal">
                                <img id="modalImage" src="" alt="Modal Image">
                            </div>
                        </div>
                    </div>
                </div>
        <?php
            }
        ?>
    </div>
    <footer>
        <p>FOOTER</p>
    </footer>
</div>
<script src="scripts/admin.js"></script>
</body>
</html>
