<?php 
require_once '../libs/apitools.php';
ApiTools::init(__FILE__);

ApiTools::asignaMetodo(function() {
    $tabla = ApiTools::getModel('MensajesBitacora');
    $mensajeBitacoraID=ApiTools::getParam('mensajeBitacoraID');
   	
	if($tabla->ObtenerDatos($mensajeBitacoraID)){
		 ApiTools::asignaRespuesta(200,'datos entregados', true, $tabla->Dataset);
	}else{
		 ApiTools::asignaRespuesta(201,'Datos entregados', false, $tabla->Mensaje);
	}
   
},'GET');

ApiTools::asignaMetodo(function() {
    $tabla = ApiTools::getModel('MensajesBitacora'); // abrir la tabla	
	//ApiTools::asignaRespuesta(200,'datos creados' ,true, "bienvenido");	
	
	if( $tabla->Inserta(ApiTools::$datosCliente))   //
    {
        ApiTools::asignaRespuesta(200,'datos creados' ,true,$tabla->Dataset, null);
    }
    else{
        ApiTools::asignaRespuesta(201,'No se pudo crear' ,true,null, $tabla->Mesanje2);
    }

},'POST');



ApiTools::processRequest();
ApiTools::respuestaApi();
?>