<?php
    include_once "config/dbConect.php"; // Esto conecta y selecciona la base de datos
    include_once "includes/functions.php"; //Añade funciones predefinidas en functions.php
    include_once "includes/authCheck.php";

    if(!$iniciado){
        header('Location: register.php'); // te redirige al register si no hay sesion
        exit;
    }

    $id = $_SESSION['id'];


    if(!empty($id)){ // verifica que el id no se ha guardado, cosa imposible en este punto ya que sin el no se podria iniciar sesion  
    $selectUser = "SELECT nombre_usuario FROM usuario WHERE id_usuario = '$id'"; 
    $resultado = mysqli_query($db, $selectUser);
    $fila = mysqli_fetch_assoc($resultado); //Lo combierte a un array
    $nombreUsuario = $fila['nombre_usuario'];
    }
    if(!empty($id)){
    $selectFoto = "SELECT foto FROM usuario WHERE id_usuario = '$id'";
    $resultado = mysqli_query($db, $selectFoto);
    $fila = mysqli_fetch_assoc($resultado);
    $fotoPerfil = $fila['foto'];        //Coge el contenido de foto que es la ruta de la misma dentro de la uploads
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="styles/perfil.css">
    <link rel="stylesheet" href="styles/header.css">
</head>
<body>
    <?php
    include_once "includes/header.php"
    ?>

    <main>
        <div class="recuadro">
            <img src="<?php echo 'uploads/fotosPerfil/'.$fotoPerfil ?>" alt="<?php echo $nombreUsuario; ?>">
            <hr>
            <h3><?php echo $nombreUsuario; ?></h3>
            <br>
            <?php
            include_once "logout.php"
            ?>

        </div>
    </main>
</body>
</html>