<?php 
require_once '../libs/apitools.php';
ApiTools::init(__FILE__);

ApiTools::asignaMetodo(function() {
    $tabla = ApiTools::getModel('TiposUsuarios');
    $id=ApiTools::getParam('Id');
     if($tabla->ObtenerDatos($id)){
		ApiTools::asignaRespuesta(200,'datos entregados' ,true,$tabla->Dataset, null);
	}else{
		ApiTools::asignaRespuesta(201,'Ocurrio un error en la consulta de datos' ,true,null, $tabla->Mensaje);
	}
},'GET');

ApiTools::asignaMetodo(function() {
    $tabla = ApiTools::getModel('TiposUsuarios'); // abrir la tabla
	
    if( $tabla->Crear2(ApiTools::$datosCliente))   //
    {
        ApiTools::asignaRespuesta(200,'datos creados' ,true,$tabla->Dataset,null);
    }
    else{
        ApiTools::asignaRespuesta(201,'No se pudo crear' ,true,null,$tabla->Mensaje);
    }
},'POST');

ApiTools::processRequest();

ApiTools::respuestaApi();?>