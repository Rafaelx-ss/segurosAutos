<?php
require_once dirname(__DIR__).'/../libs/apitools.php';
ApiTools::init(__FILE__);

ApiTools::asignaMetodo(function() {
    $tabla = ApiTools::getModel('DireccionesEstablecimientos');
    $id=ApiTools::getParam('Id');
    $tabla->ObtenerDatos($id);
    ApiTools::asignaRespuesta(200,'datos entregados' ,true,$tabla->Dataset);
},'GET');


ApiTools::processRequest();


ApiTools::respuestaApi();
?>
