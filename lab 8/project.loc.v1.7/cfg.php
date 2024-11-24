<?php
$dbhost = "localhost";
$dbuser = "root";
$dbpass = "";
$dbname = "moja_strona";
$link = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);

if (!$link) {
    echo "<b>przerwane połączenie </b>";
}

$login = "adminDarius";
$pass = "flashboom11";