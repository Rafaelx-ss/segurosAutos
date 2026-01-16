<?php
require_once '../libs/apitools.php';
ApiTools::init(__FILE__);

ApiTools::asignaMetodo(function() {
    $tabla = ApiTools::getModel('Aplicaciones');
    $id=ApiTools::getParam('Id');
    if($tabla->ObtenerDatos($id)){
		ApiTools::asignaRespuesta(200,'datos entregados' ,true,$tabla->Dataset,null);
	}else{
		ApiTools::asignaRespuesta(201,'Ocurrio un error: '.$tabla->Mensaje,true,null,$tabla->Mensaje2);
	}
    
},'GET');


ApiTools::processRequest();


ApiTools::respuestaApi();
?>
	