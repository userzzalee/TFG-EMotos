<?php
    include_once "config/dbConect.php"; // Esto conecta y selecciona la base de datos

    include_once "includes/functions.php"; //Añade funciones predefinidas en functions.php

    include_once "includes/authCheck.php";

    
    if($iniciado){
        header('Location: index.php'); // te redirige al inicio si ya hay una sesion activa
        exit;
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $errores = [];

        //Coge los valores del formulario, y verifica que sean validos

        $correo = $_POST['email'];
        if(empty($correo)){
            $errores[] = "El campo email esta vacio";
        }elseif(!verificarEmail($correo)){
            $errores[] = "Email invalido";
        }


    $stmt = mysqli_prepare($db, "SELECT email FROM usuario WHERE email = ?");

        if ($stmt) {        // Verifica que el correo no exista en la DB
            mysqli_stmt_bind_param($stmt, "s", $correo);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);
            if (mysqli_stmt_num_rows($stmt) > 0){
                $errores[] = "Email ya registrado";
            }
            mysqli_stmt_close($stmt);
        }else{
            $errores[] = "Fallo en la consulta"; 
        }

        
        $nombre = $_POST['nombre'];
        if(empty($nombre)){
            $errores[] = "El campo nombre esta vacio";
        }elseif(strlen($nombre)>20){
            $errores[] ="El nombre es muy largo (Maximo 20 caracteres)";
        }

        $apellido = $_POST['apellido'];
        if(empty($apellido)){
            $errores[] = "El campo apellido esta vacio";
        }elseif(strlen($apellido)>20){
            $errores[] ="El apellido es muy largo (Maximo 20 caracteres)";
        }

        $usuario = $_POST['usuario'];
        if(empty($usuario)){
            $errores[] = "El Nombre de usuario esta vacio";
        }elseif(strlen($usuario)>20){
            $errores[] ="El nombre de usuario es muy largo (Maximo 20 caracteres)";
        }

            $stmt = mysqli_prepare($db, "SELECT nombre_usuario FROM usuario WHERE nombre_usuario = ?");

        if ($stmt) {        // Verifica que el usuario no exista en la DB
            mysqli_stmt_bind_param($stmt, "s", $usuario);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);
            if (mysqli_stmt_num_rows($stmt) > 0){
                $errores[] = "Nombre de usuario en uso";
            }
            mysqli_stmt_close($stmt);
        }else{
            $errores[] = "Fallo en la consulta"; 
        }
        $contra = $_POST['contra'];
        if(empty($contra)){
            $errores[] = "contraseña invaldia";
        }elseif(strlen($contra)<8){
            $errores[] ="La contraseña debe ser mayot a 8 caracteres";
        }

        $contraV = $_POST['contraV'];
        if(empty($contraV) || $contra != $contraV){
            $errores[] = "las contraseñas no coinciden";
        }

        $tlf = $_POST ['tlf'];
        if (!empty($tlf) && strlen($tlf)!=9){
            $errores[] = "Numero Telefonico invalido";
        }

    if (empty($errores)) {
        // Encriptamos la contraseña
        $contraEncriptda = password_hash($contra, PASSWORD_DEFAULT);

        // Preparamos la consulta
        $stmt = mysqli_prepare($db, "INSERT INTO usuario (email, nombre_usuario, nombre, apellido, contrasena, telefono, fecha_registro) VALUES (?, ?, ?, ?, ?, ?, current_date)");

        if ($stmt) {
            // Asignamos los valores a los placeholders
            // "sissss" significa 5 strings (s = string) y nu integer (i=integer)
            mysqli_stmt_bind_param($stmt, "sssssi", $correo, $usuario, $nombre, $apellido, $contraEncriptda, $tlf);

            // Ejecutamos la consulta
            mysqli_stmt_execute($stmt);

            // Cerramos la sentencia
            mysqli_stmt_close($stmt);

            $_SESSION['id'] = guardarIdUsuario($db, $correo);   //Guarda la referncia del usuario 
            $_SESSION['ini'] = TRUE;
            header('Location: index.php'); // Y te redirige al inicio
            exit;
            
        } else {
            echo "Error al preparar la consulta: " . mysqli_error($db);
        }
    }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrate</title>
    <link rel="stylesheet" href="styles/header.css">
    <link rel="stylesheet" href="styles/register.css">
    <link rel="stylesheet" href="styles/footer.css">

</head>
<body>
    <?php
    include_once "includes/header.php"
    ?>
    <main>
        <div class="recuadro">
            <h2 class="titulo">CREA UNA NUEVA CUENTA</h2>
            <hr>
            <br>
            <form method="post">
                <input type="email" name="email" required placeholder="Correo electronico" value ="<?php echo isset($_POST['email'])? htmlspecialchars($_POST['email']) :''?>" require>
                <br>
                <input type="text"  name="nombre" maxlength="20" required placeholder="Nombre" value ="<?php echo isset($_POST['nombre'])? htmlspecialchars($_POST['nombre']) :''?>" require>
                <br>
                <input type="text"  name="apellido" maxlength="20" required placeholder="Apellido" value ="<?php echo isset($_POST['apellido'])? htmlspecialchars($_POST['apellido']) :''?>" require> 
                <br>
                <input type="text"  name="usuario" maxlength="20" required placeholder="Nombre Usuario" value ="<?php echo isset($_POST['usuario'])? htmlspecialchars($_POST['usuario']) :''?>" require>
                <br>
                <input type="password" name ="contra" required placeholder="Contrraseña" require>
                <br>
                <input type="password" name="contraV" required placeholder="Confirmar contraseña" require>
                <br>
                <input type="number"  name="tlf" placeholder="Numero de telefono" value ="<?php echo isset($_POST['tlf'])? htmlspecialchars($_POST['tlf']) :''?>" require>
                
                <button class="iniciaRegister">Registrate</button>
            </form>

            <p>
                <?php // Recorre errores y los va mostrando dentro del parrafo separandolos en lineas, haciendo una lista
                    if (!empty($errores)) {
                        foreach ($errores as $error) {
                            echo $error . "<br>";
                        }
                    }   
                ?>
            </p>
            <br>
            <div id="iniciar">
                <p><span>¿Ya tienes una cuenta?</span> <a href="login.php" class="regi">Inicia Sesion</a></p>
            </div>
        </div>
        
    </main>
    <br>
    <br>
</body>
</html>
