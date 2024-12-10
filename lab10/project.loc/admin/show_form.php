<?php

use app\CategoryManager;

require_once "../cfg.php";
require_once "../app/CategoryManager.php";

session_start();
$error_message = isset($_SESSION['error_message']) ? ($_SESSION['error_message'] . "<br />") : '';
if (isset($_SESSION['action_category'])) {
    $_POST['action_category'] = $_SESSION['action_category'];
}
unset($_SESSION['error_message']);
unset($_SESSION['action_category']);

$categoryManager = new CategoryManager($dbhost, $dbuser, $dbpass, $dbname);
if (isset($_POST['id'])) {
    $result = $categoryManager->readCategoryById($_POST["id"]);
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
</head>
<body>
    <div id="page">
        <div class="form">
            <div class="wallpaper">
                <div class="container">
                    <form action="admin.php" method="post">
                        <?php
                            echo $error_message;
                            if (isset($_POST['action_category'])) {
                                if ($_POST['action_category'] == 'edit' or $_POST['action_category'] == 'add') {
                                    if ($_POST['action_category'] == 'edit') {
                                        echo '<input type="hidden" name="id" value="' . $_POST['id'] . '">';
                                    }
                                    echo '<label>Matka:</label>';
                                    echo '<input type="number" name="parent_id" value="' . (isset($result['parent_id']) ? $result['parent_id'] : '') .'"/>';
                                    echo '<label>Nazwa kategorii:</label>';
                                    echo '<input type="text" name="name" placeholder="Name" value="' . (isset($result['name']) ? $result['name'] : '') .'" />';
                                    echo '<input type="submit" name="action_category" value="' . $_POST['action_category'] . '" />';
                                }
                                if ($_POST['action_category'] == 'delete') {
                                    echo '<input type="hidden" name="id" value="' . $_POST['id'] . '">';
                                    echo '<label>Nazwa kategorii: ' . (isset($result['name']) ? $result['name'] : '') .' </label>';
                                    echo '<button type="submit" name="action_category" value="delete">' . $_POST['action_category'] . '</button>';
                                }
                            }
                        ?>
                    </form>
                    <a href="admin.php">Wrócić</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
