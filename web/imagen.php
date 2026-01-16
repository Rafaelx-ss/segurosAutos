<?php

require_once('utilerias/phpqrcode/qrlib.php');
    $codesDir = "require/images/QRCode/";   
    $codeFile = $_GET['id'].'.png';

	$ultimosDigito= substr($sello, strlen($sello)-8, strlen($sello));
	$contenido= 'https://verificacfdi.facturaelectronica.sat.gob.mx/default.aspx?id='.$folioFiscal.'&re='.$rfcEmisor.'&rr='.$rfcCliente.'&tt='.$importeImagenQR.'&fe='.$ultimosDigito;


    QRcode::png($contenido, $codesDir.$codeFile, "H", 2); 
    return '<img src="'.$codesDir.$codeFile.'" />';
?>