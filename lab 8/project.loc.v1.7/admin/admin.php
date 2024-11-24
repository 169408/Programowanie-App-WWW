<?php

session_start();
require('../cfg.php');
require('../config/constants.php');

function LoginForm($error = '')
{
    $wynik = VIEWS . '/forms/login_form.php';
    echo "<p style='color: red'>" . $error . "</p>";
    return $wynik;
}

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

function EditPage($id, $link)
{
    $action = "update";
    FormCU($id, $link, $action);
}

function CreatePage($link) {
    $action = "create";
    FormCU(null, $link, $action);
}

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
    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
        include(LoginForm());
        exit;
    }
    ?>

    <header>Hellow Admin <?=$login?></header>
    <div class="admin_board content">
        <div class="wallpaper">
            <div class="container">
                <?php
                if (isset($_POST["show"])) {
                    if ($_POST["show"] == "create") {
                        CreatePage($link);
                    } else if ($_POST["show"] == "update") {
                        EditPage($_POST['id'], $link);
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
    <footer>
        FOOTER
    </footer>
</div>
</body>
</html>
