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

        $nombre = $_POST['nombre'];
        if(empty($nombre)){
            $errores[] = " Nombre de usuario esta vacio";
        }


        $contra = $_POST['contra'];
        if(empty($contra)){
            $errores[] = "contraseña incorrecta";
        }

        if (empty($errores)){
            $stmt = mysqli_prepare($db, "SELECT contrasena FROM usuario WHERE email = ? or nombre_usuario = ?");
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "ss", $nombre,$nombre);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_bind_result($stmt, $resConsult); //Linkea el resultado a la variable
                mysqli_stmt_fetch($stmt); //guarda el resultado en la variable vinculada
                mysqli_stmt_close($stmt);
            }
            if (password_verify($contra, $resConsult)){ 
                $_SESSION['id'] = guardarIdUsuario($db, $nombre); //para guardar la referencia del usuario
                $_SESSION['ini'] = TRUE;
                header('Location: index.php'); // Y te redirige al inicio
                exit;
            }else{
                $errores[] = "contraseña incorrecta";
            }
        }
    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="styles/login.css">
    <link rel="stylesheet" href="styles/header.css">
</head>

<body>
<?php
include_once "includes/header.php";
?>
<main>
    <div class="recuadro">
        <h2 class="titulo">INICIA SESION EN TU CUENTA</h2>
        <hr><br>
        <form method="post">
            <!-- <button type="button" class="googleBoton"> INICIA SESION A TRAVES DE GOOGLE </button> -->
            <input type="text" name="nombre" placeholder="Correo electronico" value="<?php echo isset($_POST['nombre'])? htmlspecialchars($_POST['nombre']) :''?>" require><br>
            <input type="password" name="contra" placeholder="Contraseña"><br>
            <button class="iniciaSesion">Inicia sesion</button>
        </form>
        
        <br>
        <p><span>¿No tienes una cuenta?</span> <a href="register.php" class="regi">REGISTRATE</a></p>
        <br>
        <a href="" class="olvido">¿Olvidaste tu contraseña?</a>

        <p>
            <?php // Recorre errores y los va mostrando dentro del parrafo separandolos en lineas, haciendo una lista
                if (!empty($errores)) {
                    foreach ($errores as $error) {
                        echo $error . "<br>";
                    }
                }   
            ?>
        </p>
    </div>
</main>  
</body>
</html>
