<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
/* po tym komentarzu będzie kod do dynamicznego ładowania stron */

require_once "config/constants.php";
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <base href="<?=ROOT?>/">
	<meta http-equiv="Content-type" content="text/html; charset=UTF-8" />
	<meta http-equiv="Content-Language" content="pl" />
	<meta name="Author" content="Ruslan Zhukotynskyi" />
	<link rel="stylesheet" href="css/main.css" />
	<title>Moje hobby to Workout</title>
	<script src="scripts/timedate.js" type="text/javascript"></script>
	<script src="scripts/jquery-3.7.1.min.js"></script>
</head>
<body onload="startclock()">
	<div id="page">
		<header class="header">
			<ul class="menu">
				<div class="wallpaper">
					<li><a class="active" href="index.php">Home</a></li>
					<li><a href="index.php?idp=about">About</a></li>
					<li><a href="index.php?idp=success">History of success</a></li>
					<li><a href="index.php?idp=records">Records</a></li>
					<li><a href="index.php?idp=contact">Contact</a></li>
					<li><a href="index.php?idp=another">Another projects</a></li>
					<li><a href="index.php?idp=movies">Movies</a></li>
					<div class="dataczas">
						<a href="https://www.timeanddate.com/worldclock/poland"><div id="zegarek"></div>
						<div id="data"></div></a>
					</div>
				</div>
			</ul>
		</header>
        <?php
            if($_GET["idp"] == "") $page_link = "views/main.html";
            if($_GET["idp"] == "about") $page_link = "views/about.html";
            if($_GET["idp"] == "success") $page_link = "views/success.html";
            if($_GET["idp"] == "records") $page_link = "views/records.html";
            if($_GET["idp"] == "contact") $page_link = "views/contact.html";
            if($_GET["idp"] == "another") $page_link = "views/another.html";
            if($_GET["idp"] == "movies") $page_link = "views/movies.html";

            if(file_exists($page_link)) {
                include($page_link);
            } else {
                include("views/errors/404_tpl.html");
            }
        ?>
        <footer class="footer">
			<p>RISINGRAY &copy; 2024</p>
            <?php
            $nr_indeksu = '169408';
            $nrGrupy = 'ISI4';

            echo '<p>Autor: Ruslan Zhukotynskyi ' . $nr_indeksu . ' grupa ' . $nrGrupy . ' </p><br /><br />';
            ?>
		</footer>
	</div>
	<script src="scripts/script.js"></script>
	<script src="scripts/kolorujtlo.js"></script>
</body>
</html>