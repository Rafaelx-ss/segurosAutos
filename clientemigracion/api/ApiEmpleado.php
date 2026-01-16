<?php
require_once '../libs/apitools.php';
ApiTools::init(__FILE__);

ApiTools::asignaMetodo(function() {
    $tabla = ApiTools::getModel('Empleado');
    $id=ApiTools::getParam('EmpleadoCodigo');
   
	
	if($tabla->ObtenerDatos($id)){
		ApiTools::asignaRespuesta(200,'datos entregados' ,true,$tabla->Dataset, null);
	}else{
		ApiTools::asignaRespuesta(201,'Ocurrio un error en la consulta de datos' ,true,null, $tabla->Mensaje);
	}
	
},'GET');

ApiTools::asignaMetodo(function() {
    $tabla = ApiTools::getModel('Empleado'); // abrir la tabla
    $nombre=ApiTools::getParam('EmpleadoNombre'); // obtengo parametro
    $telefono=ApiTools::getParam('EmpleadoTelefono'); //otro parametro
    if( $tabla->Crear($nombre,$telefono))   //
    {
        ApiTools::asignaRespuesta(200,'datos creados' ,true,$tabla->Dataset,null);
    }
    else{
        ApiTools::asignaRespuesta(201,'No se pudo crear' ,true,null, $tabla->Mensaje);
    }
},'POST');

ApiTools::asignaMetodo(function() {
    $tabla = ApiTools::getModel('Empleado');
    $codigo=ApiTools::getParam('EmpleadoCodigo');
    
    if( $tabla->Baja($codigo))
    {
        ApiTools::asignaRespuesta(200,'baja procesada' ,true,null, $tabla->Mensaje);
    }
    else{
        ApiTools::asignaRespuesta(201,'No se pudo procesar' ,true,null, $tabla->Mensaje);
    }
},'DELETE');

ApiTools::asignaMetodo(function() {
    $tabla = ApiTools::getModel('Empleado');
    $codigo=ApiTools::getParam('EmpleadoCodigo');
    $nombre=ApiTools::getParam('EmpleadoNombre');
    
    if( $tabla->Cambia($codigo,$nombre))
    {
        ApiTools::asignaRespuesta(200,'cambios procesados' ,true,null, $tabla->Mensaje);
    }
    else{
        ApiTools::asignaRespuesta(201,'No se pudo procesar' ,true,null, $tabla->Mensaje);
    }
},'PUT');



ApiTools::processRequest();


ApiTools::respuestaApi();
?>
