<?php
    if($iniciado){
?>

<header class="header">
    <nav class="nav-left">
        <a href="#">Tienda</a>
        <a href="#">Foro</a>
        <a href="#">Taller</a>
    </nav>

    <div class="nav-center">
        <a href="/TFG-clubMotos/proyect/index.php" class="home-btn"><b>Home</b></a>
    </div>

    <nav class="nav-right">
        <a href="/TFG-clubMotos/proyect/perfil.php" class="profile-btn">Perfil</a>
        <a href="#" class="profile-btn">Cesta</a>
    </nav>
</header>

<?php
    }else{
?>

<header class="header">
    <nav class="nav-left">
        <a href="#">Tienda</a>
        <a href="#">Foro</a>
        <a href="#">Taller</a>
    </nav>

    <div class="nav-center">
        <a href="/TFG-clubMotos/proyect/index.php" class="home-btn"><b>Home</b></a>
    </div>

    <nav class="nav-right">
        <a href="/TFG-clubMotos/proyect/login.php" class="profile-btn">Login</a>
        <a href="/TFG-clubMotos/proyect/register.php" class="profile-btn">Register</a>
    </nav>
</header>

<?php
    }
?>