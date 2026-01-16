<?php
require_once '../libs/apitools.php';
ApiTools::init(__FILE__);

ApiTools::asignaMetodo(function() {
    $tabla = ApiTools::getModel('FacturasCentrales');
    $id=ApiTools::getParam('facturaCentralID');
	$regEstado=ApiTools::getParam('regEstado');
  
	
	if($tabla->ObtenerDatos($id,$regEstado)){
		ApiTools::asignaRespuesta(200,'datos entregados' ,true,$tabla->Dataset, null);
	}else{
		ApiTools::asignaRespuesta(201,'Ocurrio un error en la consulta de datos' ,true,null, $tabla->Mensaje);
	}
	
},'GET');

ApiTools::asignaMetodo(function() {
    $tabla = ApiTools::getModel('FacturasCentrales'); // abrir la tabla
	
    $establecimientoID=ApiTools::getParam('establecimientoID'); // obtengo parametro
    $pacTimbrado=ApiTools::getParam('pacTimbrado'); //otro parametro
	$facturaID=ApiTools::getParam('facturaID'); //otro parametro
	$serieFactura=ApiTools::getParam('serieFactura'); //otro parametro
	$folioFactura=ApiTools::getParam('folioFactura'); //otro parametro
	$uuID=ApiTools::getParam('uuID'); //otro parametro
	$fechaFactura=ApiTools::getParam('fechaFactura'); //otro parametro
	$subTotalFactura=ApiTools::getParam('subTotalFactura'); //otro parametro
	$ivaFactura=ApiTools::getParam('ivaFactura'); //otro parametro
	$importeFactura=ApiTools::getParam('importeFactura'); //otro parametro
	$regUsuarioUltimaModificacion=ApiTools::getParam('regUsuarioUltimaModificacion'); //otro parametro
	$regFormularioUltimaModificacion=ApiTools::getParam('regFormularioUltimaModificacion'); //otro parametro
	$regVersionUltimaModificacion=ApiTools::getParam('regVersionUltimaModificacion'); //otro parametro
	
    if( $tabla->Crear($establecimientoID,$pacTimbrado,$facturaID,$serieFactura,$folioFactura,$uuID,$fechaFactura,$subTotalFactura,$ivaFactura,$importeFactura,$regUsuarioUltimaModificacion,$regFormularioUltimaModificacion,$regVersionUltimaModificacion))   //
    {
        ApiTools::asignaRespuesta(200,'datos creados' ,true,$tabla->Dataset,null);
    }
    else{
        ApiTools::asignaRespuesta(201,'No se pudo crear' ,true,null,$tabla->Mensaje);
    }
},'POST');

ApiTools::asignaMetodo(function() {
    $tabla = ApiTools::getModel('FacturasCentrales');
	
	$facturaCentralID=ApiTools::getParam('facturaCentralID');
	$regEstado=ApiTools::getParam('regEstado'); //otro parametro
    
    if( $tabla->Cambia($regEstado,$facturaCentralID))
    {
        ApiTools::asignaRespuesta(200,'cambios procesados' ,true,null,null);
    }
    else{
        ApiTools::asignaRespuesta(201,'No se pudo procesar' ,true,null,$tabla->Mensaje);
    }
},'PUT');



ApiTools::processRequest();


ApiTools::respuestaApi();
?>
