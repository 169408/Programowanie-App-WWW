<!doctype html>
<html lang="en">
<head>
    <meta http-equiv="Content-type" content="text/html; charset=UTF-8" />
    <meta http-equiv="Content-Language" content="pl" />
    <meta name="Author" content="Ruslan Zhukotynskyi" />
    <link rel="stylesheet" href="css/main.css" />
    <title><?=$title?></title>
    <script src="scripts/timedate.js" type="text/javascript"></script>
    <script src="scripts/jquery-3.7.1.min.js"></script>
</head>
<body onload="startclock()">
    <div id="page">
        <header class="header">
            <ul class="menu">
                <div class="wallpaper">
                    <li><a class="active" href="#">Home</a></li>
                    <li><a href="about.html">About</a></li>
                    <li><a href="success.html">History of success</a></li>
                    <li><a href="records.html">Records</a></li>
                    <li><a href="contact.html">Contact</a></li>
                    <li><a href="another.html">Another projects</a></li>
                    <div class="dataczas">
                        <a href="https://www.timeanddate.com/worldclock/poland"><div id="zegarek"></div>
                            <div id="data"></div></a>
                    </div>
                </div>
            </ul>
        </header>
