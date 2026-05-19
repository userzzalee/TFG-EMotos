<?php
    $db = mysqli_connect("localhost", "root", "") or die ("No se puede conectar con la DB");
    mysqli_select_db($db,"clubmotos") or die ("No se puede seleccionar la db");
?>