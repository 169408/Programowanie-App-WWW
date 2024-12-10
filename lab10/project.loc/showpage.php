<?php

/*
 * Pobiera zawartość strony z bazy danych na podstawie aliasu.
 * Jeśli strona nie zostanie znaleziona, zwraca komunikat '[nie_znaleziono_strony]'.
 *
 * Parametry:
 *  - $id: Alias strony, używany do identyfikacji w bazie danych.
 *  - $link: Połączenie z bazą danych MySQL.
 *
 * Zwraca:
 *  - Treść strony, jeśli istnieje w bazie.
 *  - '[nie_znaleziono_strony]', jeśli strona nie zostanie znaleziona.
 */
function show_page($id, $link) {
    $id_clear = htmlspecialchars($id);
    $query = "SELECT * FROM page_list WHERE alias='$id_clear' LIMIT 1;";
    $result = mysqli_query($link, $query);

    $row = mysqli_fetch_array($result);

    if(empty($row['id'])) {
        $web = '[nie_znaleziono_strony]';
    } else {
        $web = $row['page_content'];
    }
    return $web;

}