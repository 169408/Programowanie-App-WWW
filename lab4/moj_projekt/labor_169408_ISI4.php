<?php

$nr_indeksu = '169408';
$nrGrupy = 'ISI4';

echo 'Ruslan Zhukotynskyi ' . $nr_indeksu . ' grupa ' . $nrGrupy;

echo 'Zastosowanie metody include() <br />';

echo '<b>a) Metoda include(), require_once()</b> <br />';
echo 'Przed: ' . 'uczeń: ' . $my_name . ' przedmiot: ' . $subject;
echo '<br />';
include "constants.php";

echo 'Po: ' . 'uczeń: ' . $my_name . ' przedmiot: ' . $subject;
echo '<br />';

echo '<b>b) Warunki if, else, elseif, switch </b> <br />';

$a = 5;
$b = 3;

if ($a > $b) {
    echo $a . ' jest większe od ' . $b;
} elseif ($a == $b) {
    echo $a . ' rowna się ' . $b;
} else {
    echo $b. ' jest większe od ' . $a;
}

echo "<br />";

$i = 1;
switch ($i) {
    case 0:
        echo "i equals 0";
        break;
    case 1:
        echo "i equals 1";
        break;
    case 2:
        echo "i equals 2";
        break;
}

echo "<br />";

echo '<b>c) Pętla while() i for() </b> <br />';

while ($i < 10){
    echo ++$i . " ";
}
echo "<br />";


for (; $i > 0; $i--) {
    echo 'zmniejszamy o 1 ' . $i . '<br />';
}

echo '<b>d) Typy zmiennych $_GET, $_POST, $_SESSION </b> <br />';

echo 'Hello ' . $_GET["name"] . '!<br />';

session_start();

$_SESSION["newsession"]= "jakies info";

echo $_SESSION["newsession"];

