<?php
error_reporting(E_ALL ^ E_DEPRECATED);
include('db.php');
echo 111000;
foreach ($config as $key => $value) {
    echo "Clave: $key, Valor: $value<br>";
}
echo 111;
$conexion= mysqli_connect('127.0.0.1', 'c1marcoblanco', 'p4nc0nl3ch3') or die(mysqli_error());
mysqli_select_db($conexion, 'c1congo2024') or die(mysqli_error($conexion));