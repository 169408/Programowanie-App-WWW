<?php

/*
 * Strona formularzów. Na tej stronie dynamicznie się wyświetlają pola
 * którę występują w tej czy innej tablele w bd.
 * Zwracając uwagę na to, jakie dany chcęmy stworzyć , edytować lub usunąć, php za pomocą if else
 * sprawdza o jaką dokładnie tabele chodzi i generuje odpowiedni formularz.*/

use app\CategoryManager;
use app\PageManager;
use app\ProductManager;

require_once "../cfg.php";
require_once "../app/PageManager.php";
require_once "../app/CategoryManager.php";
require_once "../app/ProductManager.php";

session_start();
$error_message = isset($_SESSION['error_message']) ? ($_SESSION['error_message'] . "<br />") : '';

if (isset($_SESSION['action_category'])) {
    $_POST['action_category'] = $_SESSION['action_category'];
}
unset($_SESSION['error_message']);
unset($_SESSION['action_category']);

$pageManager = new PageManager($link);
if (isset($_POST['id']) && isset($_POST['action_page'])) {
    $result = $pageManager->readPageById($_POST["id"]);
    $result = $result->fetch_assoc();
}

$categoryManager = new CategoryManager($link);
if (isset($_POST['id']) && isset($_POST['action_category'])) {
    $result = $categoryManager->readCategoryById($_POST["id"]);
    $result = $result->fetch_assoc();
}

$productManager = new ProductManager($link);
if (isset($_POST['id']) && isset($_POST['action_product'])) {
    $result = $productManager->readProductById($_POST["id"]);
    $result = $result->fetch_assoc();
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
    <div id="page">
        <div class="form-action" id="main_forms">
            <div class="wallpaper">
                <div class="container">
                    <form action="admin.php" method="post" enctype="multipart/form-data">
                        <?php
                            echo $error_message;
                            if (isset($_POST['action_page'])) {
                                echo '<h2>' . strtoupper($_POST["action_page"] . " page") .'</h2>';
                                if ($_POST['action_page'] == 'edit' or $_POST['action_page'] == 'add') {
                                    if ($_POST['action_page'] == 'edit') {
                                        echo '<input type="hidden" name="id" value="' . $_POST['id'] . '">';
                                    }
                                    echo '<label>Tutuł:</label>';
                                    echo '<input type="text" name="page_title" value="' . ($result['page_title'] ?? '') . '" placeholder="Tytuł podstrony" required><br>';
                                    echo '<label>Kontent:</label>';
                                    echo '<textarea name="page_content" placeholder="Treść podstrony" required>' . ($result['page_content'] ?? '') . '</textarea><br>';
                                    echo '<label>Czy strona jest aktywna:</label>';
                                    echo '<input type="checkbox" name="status" ' . (($result['status'] ?? '') ? 1 : 0) . '> Aktywna<br>';
                                    echo '<label>Alias strony:</label>';
                                    echo '<input type="text" name="alias" value="' . ($result['alias'] ?? '') . '" placeholder="Alias podstrony" required><br>';
                                    echo '<button type="submit" name="action_page" value="' . $_POST['action_page'] . '">' . $_POST['action_page'] . '</button>';
                                }
                                if ($_POST['action_page'] == 'delete') {
                                    echo '<input type="hidden" name="id" value="' . $_POST['id'] . '">';
                                    echo '<label>Nazwa podstrony: ' . (isset($result['page_title']) ? $result['page_title'] : '') .' </label>';
                                    echo '<button type="submit" name="action_page" value="delete">' . $_POST['action_page'] . '</button>';
                                }
                            } else if (isset($_POST['action_category'])) {
                                echo '<h2>' . strtoupper($_POST["action_category"] . " category") .'</h2>';
                                if ($_POST['action_category'] == 'edit' or $_POST['action_category'] == 'add') {
                                    if ($_POST['action_category'] == 'edit') {
                                        echo '<input type="hidden" name="id" value="' . $_POST['id'] . '">';
                                    }
                                    echo '<label>Matka:</label>';
                                    echo '<input type="number" name="parent_id" value="' . (isset($result['parent_id']) ? $result['parent_id'] : '') .'"/>';
                                    echo '<label>Nazwa kategorii:</label>';
                                    echo '<input type="text" name="name" placeholder="Name" value="' . (isset($result['name']) ? $result['name'] : '') .'" />';
                                    echo '<button type="submit" name="action_category" value="' . $_POST['action_category'] . '">' . $_POST['action_category'] . '</button>';
                                }
                                if ($_POST['action_category'] == 'delete') {
                                    echo '<input type="hidden" name="id" value="' . $_POST['id'] . '">';
                                    echo '<label>Nazwa kategorii: ' . (isset($result['name']) ? $result['name'] : '') .' </label>';
                                    echo '<button type="submit" name="action_category" value="delete">' . $_POST['action_category'] . '</button>';
                                }
                            } else if (isset($_POST['action_product'])) {
                                echo '<h2>' . strtoupper($_POST["action_product"] . " product") .'</h2>';
                                echo "
                                    <script>
                                        document.addEventListener('DOMContentLoaded', function() {
                                            const div = document.getElementById('main_forms');
                                            if (div) {
                                                div.classList.add('form');
                                                div.classList.remove('form-action');
                                            }
                                        });
                                    </script>
                                    ";
                                if($_POST['action_product'] == 'edit' or $_POST['action_product'] == 'add') {
                                    if ($_POST['action_product'] == 'edit') {
                                        echo '<input type="hidden" name="id" value="' . $_POST['id'] . '">';
                                    }
                                    echo '<label>Title:</label>';
                                    echo '<input type="text" name="title" placeholder="Title" value="' . (isset($result['title']) ? $result['title'] : '') .'" required/>';
                                    echo '<label>Description:</label>';
                                    echo '<input type="text" name="description" placeholder="Description" value="' . (isset($result['description']) ? $result['description'] : '') .'" required/>';
                                    echo '<label>Price:</label>';
                                    echo '<input type="number" name="price" placeholder="Price" value="' . (isset($result['price']) ? $result['price'] : '') .'" step="0.01" required/>';
                                    echo '<label>Vat:</label>';
                                    echo '<input type="number" name="vat" placeholder="Vat" value="' . (isset($result['vat']) ? $result['vat'] : '') .'" step="0.01" required/>';
                                    echo '<label>Count:</label>';
                                    echo '<input type="number" name="count" placeholder="Count" value="' . (isset($result['count']) ? $result['count'] : '') .'" required/>';
                                    echo '<label>Status:</label>';
                                    echo '<select name="status" required>';
                                    echo '<option value="available">Available</option>';
                                    echo '<option value="not available">Not Available</option>';
                                    echo '<option value="to order">To Order</option>';
                                    echo '</select>';
                                    echo '<label>Category:</label>';
                                    echo '<input type="number" name="category" placeholder="Category" value="' . (isset($result['category']) ? $result['category'] : '') .'" required/>';
                                    echo '<label>Dimension:</label>';
                                    echo '<input type="text" name="dimension" placeholder="Dimension" value="' . (isset($result['dimension']) ? $result['dimension'] : '') .'" />';
                                    echo '<label>Image:</label>';
                                    echo '<input type="file" name="image[]" accept="image/*" multiple />';
                                    echo '<label>Material:</label>';
                                    echo '<input type="text" name="material" placeholder="Material" value="' . (isset($result['material']) ? $result['material'] : '') .'" />';
                                    echo '<label>Color:</label>';
                                    echo '<input type="text" name="color" placeholder="Color" value="' . (isset($result['color']) ? $result['color'] : '') .'" />';
                                    echo '<label>Discount:</label>';
                                    echo '<input type="number" name="discount" placeholder="Discount" value="' . (isset($result['discount']) ? $result['discount'] : '') .'" step="0.01" />';
                                    echo '<label>Expiration date:</label>';
                                    echo '<input type="date" name="expiration_date" value="' . (isset($result['expiration_date']) ? htmlspecialchars($result['expiration_date']) : '') . '" />';
                                    echo '<button type="submit" name="action_product" value="' . $_POST['action_product'] . '">' . $_POST['action_product'] . '</button>';
                                }
                                if ($_POST["action_product"] == "delete") {
                                    echo '<input type="hidden" name="id" value="' . $_POST['id'] . '">';
                                    echo '<label>Nazwa produktu: ' . (isset($result['title']) ? $result['title'] : '') .' </label>';
                                    echo '<button type="submit" name="action_product" value="delete">' . $_POST['action_product'] . '</button>';
                                }
                            }
                        ?>
                    </form>
                    <a class="back" href="admin.php">Wrócić</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
