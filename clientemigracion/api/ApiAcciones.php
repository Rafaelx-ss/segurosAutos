<?php
require_once '../libs/apitools.php';
ApiTools::init(__FILE__);

ApiTools::asignaMetodo(function() {
    $tabla = ApiTools::getModel('Acciones');
    $id=ApiTools::getParam('Id');
    if( $tabla->ObtenerDatos($id)){
		ApiTools::asignaRespuesta(200,'datos entregados' ,true,$tabla->Dataset, null);
	}else{
		ApiTools::asignaRespuesta(201,'No se pudo crear: '.$tabla->Mensaje,true,null,$tabla->Mensaje2);
	}
    
},'GET');

ApiTools::asignaMetodo(function() {
    $tabla = ApiTools::getModel('Acciones'); // abrir la tabla
	
    if( $tabla->Crear2(ApiTools::$datosCliente))   //
    {
        ApiTools::asignaRespuesta(200,'datos creados' ,true,$tabla->Dataset, null);
    }
    else{
        ApiTools::asignaRespuesta(201,'No se pudo crear: '.$tabla->Mensaje ,true,null,$tabla->Mensaje2);
    }
},'POST');




ApiTools::processRequest();

ApiTools::respuestaApi();
?>
