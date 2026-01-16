<?php
require_once '../libs/apitools.php';
ApiTools::init(__FILE__);

ApiTools::asignaMetodo(function() {
    $tabla = ApiTools::getModel('Menus');
    $id=ApiTools::getParam('Id');
    if($tabla->ObtenerDatos($id)){
		ApiTools::asignaRespuesta(200,'datos entregados' ,true,$tabla->Dataset, null);
	}else{
		ApiTools::asignaRespuesta(201,'Ocuerio un error:'.$tabla->Mensaje,true,$tabla->Dataset, $tabla->Mensaje2);
	}
    
},'GET');


ApiTools::processRequest();


ApiTools::respuestaApi();
?>
