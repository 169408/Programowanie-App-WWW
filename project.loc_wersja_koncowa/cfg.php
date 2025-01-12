<?php

// połączenie z bazą danych

$dbhost = "localhost";
$dbuser = "root";
$dbpass = "";
$dbname = "moja_strona";
$link = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);

if (!$link) {
    echo "<b>przerwane połączenie </b>";
}

// Przykładowy użytkownik, już nie potrzebny ale niech będzie
//$login = "adminDarius";
//$pass = "flashboom11";