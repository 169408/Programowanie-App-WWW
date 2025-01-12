<?php

/* Definiuje stałą 'ROOT', która wskazuje nazwę głównego katalogu projektu w formacie URL
 * Definiuje stałą 'MAINDIR', która zawiera pełną ścieżkę głównego katalogu projektu
 * Stała 'VIEWS' wskazuje ścieżkę do katalogu widoków (views)
 * Stała 'UPLOADS' wskazuje ścieżkę do katalogu, gdzie przechowywane są pliki przesyłane przez użytkowników
*/

// Pobiera ścieżkę głównego katalogu projektu
$main_folder = dirname(__DIR__);

// Dzieli ścieżkę na części na podstawie separatora "/"
$link_split = explode("/", $main_folder);


define('ROOT', "/".$link_split[count($link_split) - 1]);
define('MAINDIR', $main_folder);

const VIEWS = MAINDIR . '/views';
const UPLOADS = MAINDIR . '/uploads';