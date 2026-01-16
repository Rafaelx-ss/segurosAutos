<?php 
require_once '../libs/apitools.php';
ApiTools::init(__FILE__);

ApiTools::asignaMetodo(function() {
    $tabla = ApiTools::getModel('ReglasPassw');
    $id=ApiTools::getParam('Id');
    if( $tabla->ObtenerDatos($id)){
		ApiTools::asignaRespuesta(200,'datos entregados' . $tabla->Mensaje,true,$tabla->Dataset,$tabla->Mensaje2);
	} else {
		ApiTools::asignaRespuesta(201,'No se pudo Obtener Datos: ' . $tabla->Mensaje ,true,null,$tabla->Mensaje2);
	}
},'GET');

ApiTools::asignaMetodo(function() {
    $tabla = ApiTools::getModel('ReglasPassw'); // abrir la tabla
	
    if( $tabla->Inserta(ApiTools::$datosCliente['datosCliente']))   //
    {
        ApiTools::asignaRespuesta(200,'datos creados' . $tabla->Mensaje,true,$tabla->Dataset,$tabla->Mensaje2);
    }
    else{
        ApiTools::asignaRespuesta(201,'No se pudo crear' . $tabla->Mensaje,true,null,$tabla->Mensaje2);
    }
},'POST');

ApiTools::processRequest();

ApiTools::respuestaApi();?>