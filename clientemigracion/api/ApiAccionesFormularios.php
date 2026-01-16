<?php
require_once '../libs/apitools.php';
ApiTools::init(__FILE__);

ApiTools::asignaMetodo(function() {
    $tabla = ApiTools::getModel('AccionesFormularios');
    $id=ApiTools::getParam('Id');
    if($tabla->ObtenerDatos($id)){
		ApiTools::asignaRespuesta(200,'datos entregados' ,true,$tabla->Dataset,  null);
	}else{
		ApiTools::asignaRespuesta(201,'Ocurrio un error en la consulta de datos:'.$tabla->Mensaje,true,null, $tabla->Mensaje2);
	}
    
},'GET');


ApiTools::processRequest();


ApiTools::respuestaApi();
?>
