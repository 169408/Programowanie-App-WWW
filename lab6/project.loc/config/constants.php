<?php

$main_folder = dirname(__DIR__);
$link_split = explode("/", $main_folder);

define('ROOT', "/".$link_split[count($link_split) - 1]);
const VIEWS = ROOT . '/views';
const UPLOADS = ROOT . '/uploads';