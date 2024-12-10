<?php
// ------------------------------------------------------
//              Strona Admin
// ------------------------------------------------------

use app\CategoryManager;

session_start();
require('../cfg.php');
require('../config/constants.php');

// ------------------------------------------------------
//              Formularz logowania admina
// ------------------------------------------------------
/*
 * Wyświetla formularz logowania oraz błędy, jeśli występią
 * */
function LoginForm($error = '')
{
    $wynik = VIEWS . '/forms/login_form.php';
    echo "<p style='color: red'>" . $error . "</p>";
    return $wynik;
}

// ------------------------------------------------------
//              Formularz tworzenia nowego admina
// ------------------------------------------------------
/*
 * Wyświetla formularz tworzenia admina oraz błędy, jeśli występią
 * */
function CreateForm($error = '')
{
    $wynik = VIEWS . '/forms/create_form.php';
    echo "<p style='color: red'>" . $error . "</p>";
    return $wynik;
}

/*
 * Tworzy nowego użytkownika admina w bazie danych, walidując dane wejściowe
 * i szyfrując hasło przed zapisaniem.
 */
function createuser($link)
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_user'])) {
        $login = trim($_POST['login']);
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);
        $repeat_password = trim($_POST['repeat_password']);

        if ($password !== $repeat_password) {
            die('Passwords do not match!');
        }

        // Хешування пароля для безпеки
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Підключення до бази даних

        $query = 'INSERT INTO admins (login, email, password) VALUES (?, ?, ?)';
        $stmt = mysqli_prepare($link, $query);

        // Додавання адміністратора
        $stmt->bind_param('sss', $login, $email, $hashed_password);

        if ($stmt->execute()) {
            echo 'Admin successfully created!';
        } else {
            echo 'Error: ' . $stmt->error;
        }

        $stmt->close();
        //$link->close();
    }
}

// ------------------------------------------------------
//              Formularz do tworzenia lub edytowania podstrony
// ------------------------------------------------------
/*
 * Generuje formularz do tworzenia lub edytowania podstrony.
 * Wyświetla istniejące dane w przypadku edycji.
 */
function FormCU($id, $link, $action = 'create') {
    if ($id) {
        $query = "SELECT * FROM page_list WHERE id = $id LIMIT 1";
        $result = mysqli_query($link, $query);
        $row = mysqli_fetch_assoc($result);
    }

    // Виведення форми
    echo '<form method="post" action="admin/admin.php">';

    // Якщо редагування, вивести ID сторінки
    if ($id) {
        echo '<input type="hidden" name="id" value="' . $row['id'] . '">';
    }

    echo '<input type="text" name="page_title" value="' . ($row['page_title'] ?? '') . '" placeholder="Tytuł podstrony" required><br>';
    echo '<textarea name="page_content" placeholder="Treść podstrony" required>' . ($row['page_content'] ?? '') . '</textarea><br>';
    echo '<input type="checkbox" name="status" ' . (($row['status'] ?? '') ? 1 : 0) . '> Aktywna<br>';
    echo '<input type="text" name="alias" value="' . ($row['alias'] ?? '') . '" placeholder="Alias podstrony" required><br>';
    echo '<button type="submit" name="action" value="' . $action . '">' . $action . '</button>';
    echo '</form>';
}

// ------------------------------------------------------
//              Formularz edycji istniejącej strony na podstawie ID.
// ------------------------------------------------------
function EditPage($id, $link)
{
    $action = "update";
    FormCU($id, $link, $action);
}

// ------------------------------------------------------
//              Formularz do stworzenia nowej strony.
// ------------------------------------------------------
function CreatePage($link) {
    $action = "create";
    FormCU(null, $link, $action);
}

/*
 * Usuwa stronę z bazy danych na podstawie ID.
 */
function DeletePage($id, $link) {
    $query = "DELETE FROM page_list WHERE id = ? LIMIT 1";
    $stmt = mysqli_prepare($link, $query);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id);

        if (mysqli_stmt_execute($stmt)) {
            echo "Rekord usunięty.";
        } else {
            echo "Błąd wykonania żądania: " . mysqli_stmt_error($stmt);
        }

        mysqli_stmt_close($stmt);
    } else {
        echo "Błąd podczas przygotowywania żądania: " . mysqli_error($link);
    }
}


// ------------------------------------------------------
//              Lista podstron.
// ------------------------------------------------------
/*
 * Pobiera listę wszystkich stron z bazy danych i wyświetla je w tabeli
 * wraz z opcjami edycji i usunięcia.
 */
function PageList($link)
{
    //$id_clear = htmlspecialchars($id);
    $query = "SELECT * FROM page_list ORDER BY id";
    $result = mysqli_query($link, $query);

    echo '<table>';
    echo '<tr><th>ID</th><th>Tytuł</th></tr>';
    while ($row = mysqli_fetch_array($result)) {
        echo '<tr>';
        echo '<td>' . $row['id'] . '</td>';
        echo '<td>' . $row['page_title'] . '</td>';
        echo '<td>
                <form method="post" style="display:inline;" action="admin/admin.php">
                    <input type="hidden" name="id" value="' . $row['id'] . '">
                    <button type="submit" name="show" value="update">edit</button>
                </form>
                </td>
                <td>
                <form method="post" style="display:inline;">
                    <input type="hidden" name="id" value="' . $row['id'] . '">
                    <button type="submit" name="action" value="delete">delete</button>
                </form>
              </td>';
        echo '</tr>';
    }
    echo '<tr><td><form method="post" style="display:inline;" action="admin/admin.php">
                    <button type="submit" name="show" value="create">create</button>
                </form></td></tr>';
    echo '</table>';
}

require_once "../app/CategoryManager.php";

$categoryManager = new CategoryManager($dbhost, $dbuser, $dbpass, $dbname);

if (isset($_POST['action_category'])) {
    try {
        if ($_POST["action_category"] == "add") {
            $categoryManager->addCategory($_POST);
            header("location: admin.php");
        } else if ($_POST["action_category"] == "edit") {
            $categoryManager->updateCategory($_POST, $_POST["id"]);
            header("location: admin.php");
        } else if ($_POST["action_category"] == "delete") {
            $categoryManager->deleteCategory($_POST["id"]);
            header("location: admin.php");
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
    <title>Document</title>
</head>
<body>
<div id="page">
    <?php
    if (isset($_POST['submit'])) {
        if ($_POST['login'] == $login && $_POST['password'] == $pass) {
            $_SESSION['logged_in'] = true;
        } else {
            include(LoginForm('Nieprawidłowe dane logowania.'));
            exit;
        }
    }
    if (isset($_POST['create_user'])) {
        include(createuser($link));
    }
    if (isset($_GET['create'])) {
        include(CreateForm());
        exit;
    }
    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
        include(LoginForm());
        exit;
    }
    ?>

    <header style="background: #4af3f2">Hellow Admin <?=$login?></header>
    <div class="admin_board content">
        <div class="pages_controller">
            <div class="wallpaper">
                <div class="container">
                    <?php
                    if (isset($_POST["show"])) {
                        if ($_POST["show"] == "create") {
                            CreatePage($link);
                            die;
                        } else if ($_POST["show"] == "update") {
                            EditPage($_POST['id'], $link);
                            die;
                        } else {
                            echo "Błąd!";
                        }
                    } else if (isset($_POST["action"])) {
                        if ($_POST["action"] == "create") {
                            $query = "INSERT INTO page_list (page_title, page_content, status, alias) VALUES (?, ?, ?, ?)";
                            $stmt = mysqli_prepare($link, $query);
                            if ($stmt) {
                                $active = isset($_POST['status']) ? 1 : 0;
                                mysqli_stmt_bind_param($stmt, "ssis", $_POST['page_title'], $_POST['page_content'], $active, $_POST['alias']);
                                if (mysqli_stmt_execute($stmt)) {
                                    echo "Nowy rekord został dodany.";
                                } else {
                                    echo "Błąd wykonania żądania: " . mysqli_stmt_error($stmt);
                                }

                                mysqli_stmt_close($stmt);
                            } else {
                                echo "Błąd podczas przygotowywania żądania: " . mysqli_error($link);
                            }
                        } else if ($_POST["action"] == "update") {
                            $query = "UPDATE page_list SET page_title = ?, page_content = ?, status = ?, alias = ? WHERE id = ?";
                            $stmt = mysqli_prepare($link, $query);
                            print_r($stmt);
                            if ($stmt) {
                                $active = isset($_POST['active']) ? 1 : 0;
                                mysqli_stmt_bind_param($stmt, "ssisi", $_POST['page_title'], $_POST['page_content'], $active, $_POST["alias"], $_POST['id']);
                                if (mysqli_stmt_execute($stmt)) {
                                    echo "Rekord został zedytowany.";
                                } else {
                                    echo "Błąd wykonania żądania: " . mysqli_stmt_error($stmt);
                                }

                                mysqli_stmt_close($stmt);
                            } else {
                                echo "Błąd podczas przygotowywania żądania: " . mysqli_error($link);
                            }
                        } else if ($_POST["action"] == "delete") {
                            DeletePage($_POST['id'], $link);
                        }

                        header("Location: admin.php");
                        exit;
                    } else {
                        PageList($link);
                    }


                    ?>
                </div>
            </div>
        </div>
        <div class="category_controller">
            <div class="wallpaper">
                <div class="container">
                    <h2>Categorie:</h2>
                    <?php

                        $categoryManager->displayCategoryTree($allcategories);
                    ?>
                    <hr style="margin: 9px">

                    <form action="admin/show_form.php" style="display: inline" method="post">
                        <button type="submit" name="action_category" value="add">add category</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <footer>
        FOOTER
    </footer>
</div>
</body>
</html>
