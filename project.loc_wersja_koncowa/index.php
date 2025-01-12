<?php

//Włączam wyświetlanie wszystkich błędów występujących podczas generowania strony
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
/*
 * Zaawansowane wyświetlenie błędów w razie czego
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
*/

/* po tym komentarzu będzie kod do dynamicznego ładowania stron */

require_once "config/constants.php"; // podłączyć niezbędne constants
include("cfg.php"); // łączenie z bazą danych

include ("showpage.php"); // podłączenie funkcji do wyświetlania strony.

$web = show_page($_GET['idp'], $link); // zapisanie do zmiennej kodu html strony

?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <base href="<?=ROOT?>/">
	<meta http-equiv="Content-type" content="text/html; charset=UTF-8" />
	<meta http-equiv="Content-Language" content="pl" />
	<meta name="Author" content="Ruslan Zhukotynskyi" />
	<link rel="stylesheet" href="css/main.css" />
	<title><?php
        if (isset($_GET["idp"]) && !empty($_GET["idp"])) {
            echo "WORKOUT " . strtoupper($_GET["idp"]);
        } else {
            echo "My hobby is workout!";
        }
        ?></title>
	<script src="scripts/timedate.js" type="text/javascript"></script>
	<script src="scripts/jquery-3.7.1.min.js"></script>
</head>
<body onload="startclock()">
	<div id="page">
		<header class="header">
			<ul class="menu">
				<div class="wallpaper">
<!--                    sekcja generowania menu - nawigacja po stronie-->
<!--                    ładuje wszystkie podstrony z bazy danych i tworzy ścieżki do podciągów-->

                        <?php
                            $query = "SELECT * FROM page_list WHERE status = 1";
                            $result = mysqli_query($link, $query);

                            while ($row = mysqli_fetch_assoc($result)) {
                                if ($row['alias'] == "") {
                                    ?>
                                    <li><a href="index.php"><?=$row['page_title']?></a></li>
                                <?php
                                    continue;
                                }
                                ?>
                                <li><a href="index.php?idp=<?=$row['alias']?>"><?=$row['page_title']?></a></li>
                            <?php
                            }
                        ?>
					<div class="dataczas">
						<a href="https://www.timeanddate.com/worldclock/poland"><div id="zegarek"></div>
						<div id="data"></div></a>
					</div>
				</div>
			</ul>
		</header>
        <?php
            // Wyświetlenie zawartości strony
            echo $web;
        ?>
        <footer class="footer">
			<p>RISINGRAY &copy; 2024</p>
			<p>Project Version 1.12.2</p>
            <?php
            $nr_indeksu = '169408';
            $nrGrupy = 'ISI4';

            echo '<p>Autor: Ruslan Zhukotynskyi ' . $nr_indeksu . ' grupa ' . $nrGrupy . ' </p><br /><br />';
            ?>
		</footer>
	</div>

	<script src="scripts/kolorujtlo.js"></script>
    <script src="scripts/script.js"></script>
</body>
</html>