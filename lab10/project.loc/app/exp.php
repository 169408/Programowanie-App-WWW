<?php

use app\CategoryManager;

require_once "../cfg.php";
require_once "../app/CategoryManager.php";

$categoryManager = new CategoryManager($dbhost, $dbuser, $dbpass, $dbname);
$allcategories = $categoryManager->readAllCategories();
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Categories</title>
</head>
<body>
<div id="page">
    <header class="header">
        <ul class="menu">
            <div class="wallpaper">
            </div>
        </ul>
    </header>
    <h1>Categorie:</h1>
    <?php
//        while ($category = $allcategories->fetch_assoc()) {
//            echo "ID: " . $category["id"] . " Maatka: " . $category["matka"] . " Name: " . $category["name"] . " Alias: " . $category["alias"] . "<br>";
//        }

        $categoryManager->displayCategoryTree($allcategories);
    ?>
</div>
</body>
</html>
