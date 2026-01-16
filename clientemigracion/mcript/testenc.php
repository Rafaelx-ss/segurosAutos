<?php
include "mcript.php";
$dato = "";
$dato_encriptado = $strHide($dato);
$dato_desencriptado = $strShow($dato_encriptado);
echo 'Dato encriptado: '. $dato_encriptado . '<br>';
echo 'Dato desencriptado: '. $dato_desencriptado . '<br>';
echo "IV generado: " . $getIV();
?>

    