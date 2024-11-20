<?php

$main_folder = dirname(__DIR__);
$link_split = explode("/", $main_folder);

define('ROOT', "/".$link_split[count($link_split) - 1]);
define('MAINDIR', $main_folder);
const VIEWS = MAINDIR . '/views';
const UPLOADS = MAINDIR . '/uploads';