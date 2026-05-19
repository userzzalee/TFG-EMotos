<?php
    include_once "includes/authCheck.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio</title>
    <link rel="stylesheet" href="styles/headerIndex.css">
    <link rel="stylesheet" href="styles/footer.css">
    <link rel="stylesheet" href="styles/index.css">
    <link rel="stylesheet" href="styles/fondo.css">

</head>
<body>
    <?php
    include_once "includes/header.php"
    ?>
    <video autoplay muted loop playsinline id="bg-video">
        <source src="assets/videos/fondo.mp4" type="video/mp4">
    </video>
    <main>
        <h2>RIDE ANYWHERE, <br>
            ANYTIME
        </h2>
        <br>
        <button type="button"><b><a href="">Supermotard</a></b></button>
        <button type="button"><b><a href="">Motrocross</a></b></button>
        <button type="button"><b><a href="">Enduro</a></b></button>
    </main>
    <?php
        include_once "includes/footer.php"
    ?>
</body>
</html>

