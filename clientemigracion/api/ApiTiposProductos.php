<?php
require_once '../libs/apitools.php';
ApiTools::init(__FILE__);

ApiTools::asignaMetodo(function() {
    $tabla = ApiTools::getModel('TiposProductos');
    $id=ApiTools::getParam('Id');
     if($tabla->ObtenerDatos($id)){
		ApiTools::asignaRespuesta(200,'datos entregados' ,true,$tabla->Dataset, null);
	}else{
		ApiTools::asignaRespuesta(201,'Ocurrio un error en la consulta de datos' ,true,null, $tabla->Mensaje);
	}
},'GET');


ApiTools::processRequest();


ApiTools::respuestaApi();
?>
