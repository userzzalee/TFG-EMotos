<?php
if (isset($_POST['logout'])) {
    $iniciado = FALSE;
    $_SESSION['ini'] = $iniciado;
    header('Location: login.php');
    exit;
}
?>
<form method="post">
    <button class="logout-btn" name="logout">Cerrar sesión</button>
</form>