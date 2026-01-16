<?php 
class ConfiguracionAplicacion{ 
 
private $Conexion; 
private $Database; 
private $NombreTabla = 'ConfiguracionAplicacion'; 
 
// object properties
public $Campos;
public $Dataset;
public $Mensaje;
public $Mensaje2; 
// constructor 
    public function __construct($dbf){
        $this->Database = $dbf;
        $this->Conexion = $dbf->conn;
    }
	
	function ObtenerDatos($ID=0, $establecimientoID=0){
    	// query to check if email exists
    	try{
    	    
    	    
    	    $strWhere="";
    	    if ($ID != 0){
    	        $strWhere=" configuracionAplicacionID=:configuracionAplicacionID and ";
    	    }
    	    
    	    
    	    // query to check if email exists
    	    //$query = "select * from " . $this->NombreTabla . " " . ($id > 0 ? " where configuracionReclasificacionID = ?" : "");
    	    $query = "select * from " . $this->NombreTabla . " where ". $strWhere ."  establecimientoID=:establecimientoID";
    	    
    	
    	// prepare the query
    	$stmt = $this->Conexion->prepare( $query );
 		$this->Mensaje2=$query;
    	 // sanitize
		
		$ID=htmlspecialchars(strip_tags($ID));
		$establecimientoID=htmlspecialchars(strip_tags($establecimientoID));
 
    	// bind given id value

    
    	 // bind the values
		
		if($ID > 0){
		    
		    $stmt->bindParam(':configuracionAplicacionID', $ID);
		}
		$stmt->bindParam(':establecimientoID', $establecimientoID);
    	// execute the query
    	$stmt->execute();
 
    	// get number of rows
    	$num = $stmt->rowCount();
 
    	// if email exists, assign values to object properties for easy access and use for php sessions
    	if($num>0){
        	// get record details / values
        	$this->Dataset = $stmt->fetchAll(PDO::FETCH_ASSOC);
        	// return true because email exists in the database
        	return true;
    	}
 
    	// return false if email does not exist in the database
    	return false;
    	}catch (Exception $e){
			$this->Mensaje = $e->getMessage();
			return false;//.'<br /> <br />Consulta: <br />'.$consulta;
		}
    	
	}
 

function Inserta($registros){
	try{
		$consulta="";
	
		$consulta = 'INSERT INTO ' . $this->NombreTabla . 
			" (configuracionAplicacionID,modoDescarga,muestraBotonExportaVentasEinventarios,muestraEncabezado,passwordCerti,permisoReenvio,porFormaPago,timerOutwsFllet,timerReenvioFlotillas,urlWerbservicesMobileGas,protocoloSeguridadTSLID,urlWebservicesMobileFleet,imprimeIvaTicket,tipoConsolaID,tipoConexion,rutaWebSocket,tipoValidacionIdentificadores,validaTarjetaBomba,imprimirPOS,jornadaActiva,preAuth,enviaTicketCentral,urlNeoFact,siic,vtb,establecimientoID,regEstado,regFechaUltimaModificacion,regUsuarioUltimaModificacion,regFormularioUltimaModificacion,regVersionUltimaModificacion,vtt,clco,timerEstacionServicio,precioEntradas,activaTipoReportetotalesInvVentas,reportePorJornadaYTurno,grupoPolizaID,ControlInventarios,numeroMesesDepuracion,codigoIgnora,valesCreditoLocal,versionRegistro) VALUES ";
			
		$coma = false;
		$comaText = "";
		$valNull = is_null($registros);
		if ($valNull)
			{
				$this->Mensaje = "Migracion Sin Datos";
				return false;
			}
		foreach($registros as $item)
		 {
			
			 if($this->ObtenerDatos($item['configuracionAplicacionID'], $item['establecimientoID'])){
				$this->Cambia($item['configuracionAplicacionID'],$item['modoDescarga'],$item['muestraBotonExportaVentasEinventarios'],$item['muestraEncabezado'],$item['passwordCerti'],$item['permisoReenvio'],$item['porFormaPago'],$item['timerOutwsFllet'],$item['timerReenvioFlotillas'],$item['urlWerbservicesMobileGas'],$item['protocoloSeguridadTSLID'],$item['urlWebservicesMobileFleet'],$item['imprimeIvaTicket'],$item['tipoConsolaID'],$item['tipoConexion'],$item['rutaWebSocket'],$item['tipoValidacionIdentificadores'],$item['validaTarjetaBomba'],$item['imprimirPOS'],$item['jornadaActiva'],$item['preAuth'],$item['enviaTicketCentral'],$item['urlNeoFact'],$item['siic'],$item['vtb'],
				$item['establecimientoID'],$item['regEstado'],$item['regFechaUltimaModificacion'],$item['regUsuarioUltimaModificacion'],$item['regFormularioUltimaModificacion'],$item['regVersionUltimaModificacion'],$item['vtt'],$item['clco'],$item['timerEstacionServicio'],$item['precioEntradas'],$item['activaTipoReportetotalesInvVentas'],$item['reportePorJornadaYTurno'],$item['grupoPolizaID'],$item['ControlInventarios'],$item['numeroMesesDepuracion'],$item['codigoIgnora'],$item['valesCreditoLocal'],$item['versionRegistro']);
			}
			else{
				 if($coma)
				 {
					 $comaText=",";
				 }
				 else
				 {
					 $comaText="";
					 $coma = true;
				 }
				
				$consulta= $consulta . $comaText . "(" . $item['configuracionAplicacionID'] . "," . $item['modoDescarga'] . "," . $item['muestraBotonExportaVentasEinventarios'] . "," . $item['muestraEncabezado'] . "," . $item['passwordCerti'] . "," . $item['permisoReenvio'] . "," . $item['porFormaPago'] . "," . $item['timerOutwsFllet'] . "," . $item['timerReenvioFlotillas'] . ",'" . $item['urlWerbservicesMobileGas'] . "'," . $item['protocoloSeguridadTSLID'] . ",'" . $item['urlWebservicesMobileFleet'] . "'," . $item['imprimeIvaTicket'] . "," . $item['tipoConsolaID'] . ",'" . $item['tipoConexion'] . "','" . $item['rutaWebSocket'] . "','" . $item['tipoValidacionIdentificadores'] . "'," . $item['validaTarjetaBomba'] . "," . $item['imprimirPOS'] . "," . $item['jornadaActiva'] . "," . $item['preAuth'] . "," . $item['enviaTicketCentral'] . ",'" . $item['urlNeoFact'] . "','" . $item['siic'] . "'," . $item['vtb'] . "," . 
			$item['establecimientoID'] . "," . $item['regEstado'] . ",now()," . $item['regUsuarioUltimaModificacion'] . "," . $item['regFormularioUltimaModificacion'] . "," . $item['regVersionUltimaModificacion'] . "," . $item['vtt'] . "," . $item['clco'] . "," . $item['timerEstacionServicio'] . "," . $item['precioEntradas'] . "," . $item['activaTipoReportetotalesInvVentas'] . "," . $item['reportePorJornadaYTurno'] . "," . $item['grupoPolizaID'] . "," . $item['ControlInventarios'] . "," . $item['numeroMesesDepuracion'] . ",'" . $item['codigoIgnora'] . "'," . $item['valesCreditoLocal'] . "," . $item['versionRegistro'] . ")";
			}
			 
		   
		 }
	if(!$coma){
		$consulta="select 1";
	}
	$this->query=$consulta;

    // prepare the query
    $stmt = $this->Conexion->prepare($this->query);
	$this->Mensaje2=$this->query;
    // sanitize
	$configuracionAplicacionID=htmlspecialchars(strip_tags($item['configuracionAplicacionID']));
	$modoDescarga=htmlspecialchars(strip_tags($item['modoDescarga']));
	$muestraBotonExportaVentasEinventarios=htmlspecialchars(strip_tags($item['muestraBotonExportaVentasEinventarios']));
	$muestraEncabezado=htmlspecialchars(strip_tags($item['muestraEncabezado']));
	$passwordCerti=htmlspecialchars(strip_tags($item['passwordCerti']));
	$permisoReenvio=htmlspecialchars(strip_tags($item['permisoReenvio']));
	$porFormaPago=htmlspecialchars(strip_tags($item['porFormaPago']));
	$timerOutwsFllet=htmlspecialchars(strip_tags($item['timerOutwsFllet']));
	$timerReenvioFlotillas=htmlspecialchars(strip_tags($item['timerReenvioFlotillas']));
	$urlWerbservicesMobileGas=htmlspecialchars(strip_tags($item['urlWerbservicesMobileGas']));
	$protocoloSeguridadTSLID=htmlspecialchars(strip_tags($item['protocoloSeguridadTSLID']));
	$urlWebservicesMobileFleet=htmlspecialchars(strip_tags($item['urlWebservicesMobileFleet']));
	$imprimeIvaTicket=htmlspecialchars(strip_tags($item['imprimeIvaTicket']));
	$tipoConsolaID=htmlspecialchars(strip_tags($item['tipoConsolaID']));
	$tipoConexion=htmlspecialchars(strip_tags($item['tipoConexion']));
	$rutaWebSocket=htmlspecialchars(strip_tags($item['rutaWebSocket']));
	$tipoValidacionIdentificadores=htmlspecialchars(strip_tags($item['tipoValidacionIdentificadores']));
	$validaTarjetaBomba=htmlspecialchars(strip_tags($item['validaTarjetaBomba']));
	$imprimirPOS=htmlspecialchars(strip_tags($item['imprimirPOS']));
	$jornadaActiva=htmlspecialchars(strip_tags($item['jornadaActiva']));
	$preAuth=htmlspecialchars(strip_tags($item['preAuth']));
	$enviaTicketCentral=htmlspecialchars(strip_tags($item['enviaTicketCentral']));
	$urlNeoFact=htmlspecialchars(strip_tags($item['urlNeoFact']));
	$siic=htmlspecialchars(strip_tags($item['siic']));
	$vtb=htmlspecialchars(strip_tags($item['vtb']));
	$establecimientoID=htmlspecialchars(strip_tags($item['establecimientoID']));
	$regEstado=htmlspecialchars(strip_tags($item['regEstado']));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($item['regFechaUltimaModificacion']));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($item['regUsuarioUltimaModificacion']));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($item['regFormularioUltimaModificacion']));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($item['regVersionUltimaModificacion']));
	$vtt=htmlspecialchars(strip_tags($item['vtt']));
	$clco=htmlspecialchars(strip_tags($item['clco']));
	$timerEstacionServicio=htmlspecialchars(strip_tags($item['timerEstacionServicio']));
	$precioEntradas=htmlspecialchars(strip_tags($item['precioEntradas']));
	$activaTipoReportetotalesInvVentas=htmlspecialchars(strip_tags($item['activaTipoReportetotalesInvVentas']));
	$reportePorJornadaYTurno=htmlspecialchars(strip_tags($item['reportePorJornadaYTurno']));
	$grupoPolizaID=htmlspecialchars(strip_tags($item['grupoPolizaID']));
	$ControlInventarios=htmlspecialchars(strip_tags($item['ControlInventarios']));
	$numeroMesesDepuracion=htmlspecialchars(strip_tags($item['numeroMesesDepuracion']));
	$codigoIgnora=htmlspecialchars(strip_tags($item['codigoIgnora']));
	$valesCreditoLocal=htmlspecialchars(strip_tags($item['valesCreditoLocal']));
	$versionRegistro=htmlspecialchars(strip_tags($item['versionRegistro']));
	
   
    // bind the values
	$stmt->bindParam(':configuracionAplicacionID', $configuracionAplicacionID);
	$stmt->bindParam(':modoDescarga', $modoDescarga);
	$stmt->bindParam(':muestraBotonExportaVentasEinventarios', $muestraBotonExportaVentasEinventarios);
	$stmt->bindParam(':muestraEncabezado', $muestraEncabezado);
	$stmt->bindParam(':passwordCerti', $passwordCerti);
	$stmt->bindParam(':permisoReenvio', $permisoReenvio);
	$stmt->bindParam(':porFormaPago', $porFormaPago);
	$stmt->bindParam(':timerOutwsFllet', $timerOutwsFllet);
	$stmt->bindParam(':timerReenvioFlotillas', $timerReenvioFlotillas);
	$stmt->bindParam(':urlWerbservicesMobileGas', $urlWerbservicesMobileGas);
	$stmt->bindParam(':protocoloSeguridadTSLID', $protocoloSeguridadTSLID);
	$stmt->bindParam(':urlWebservicesMobileFleet', $urlWebservicesMobileFleet);
	$stmt->bindParam(':imprimeIvaTicket', $imprimeIvaTicket);
	$stmt->bindParam(':tipoConsolaID', $tipoConsolaID);
	$stmt->bindParam(':tipoConexion', $tipoConexion);
	$stmt->bindParam(':rutaWebSocket', $rutaWebSocket);
	$stmt->bindParam(':tipoValidacionIdentificadores', $tipoValidacionIdentificadores);
	$validaTarjetaBomba=(int)$validaTarjetaBomba;
	$stmt->bindParam(':validaTarjetaBomba', $validaTarjetaBomba, PDO::PARAM_INT);
	$stmt->bindParam(':imprimirPOS', $imprimirPOS);
	$stmt->bindParam(':jornadaActiva', $jornadaActiva);
	$stmt->bindParam(':preAuth', $preAuth);
	$stmt->bindParam(':enviaTicketCentral', $enviaTicketCentral);
	$stmt->bindParam(':urlNeoFact', $urlNeoFact);
	$stmt->bindParam(':siic', $siic);
	$stmt->bindParam(':vtb', $vtb);
	$stmt->bindParam(':establecimientoID', $establecimientoID);
	$regEstado = (int)$regEstado;
	$stmt->bindValue(':regEstado', $regEstado, PDO::PARAM_INT);
	$stmt->bindParam(':regFechaUltimaModificacion', $regFechaUltimaModificacion);
	$stmt->bindParam(':regUsuarioUltimaModificacion', $regUsuarioUltimaModificacion);
	$stmt->bindParam(':regFormularioUltimaModificacion', $regFormularioUltimaModificacion);
	$stmt->bindParam(':regVersionUltimaModificacion', $regVersionUltimaModificacion);
	$stmt->bindParam(':vtt', $vtt);
	$stmt->bindParam(':clco', $clco);
	$stmt->bindParam(':timerEstacionServicio', $timerEstacionServicio);
	$stmt->bindParam(':precioEntradas', $precioEntradas);
	$stmt->bindParam(':activaTipoReportetotalesInvVentas', $activaTipoReportetotalesInvVentas);
	$stmt->bindParam(':reportePorJornadaYTurno', $reportePorJornadaYTurno);
	$stmt->bindParam(':grupoPolizaID', $grupoPolizaID);
	$stmt->bindParam(':ControlInventarios', $ControlInventarios);
	$stmt->bindParam(':numeroMesesDepuracion', $numeroMesesDepuracion);
	$stmt->bindParam(':codigoIgnora', $codigoIgnora);
	$stmt->bindParam(':valesCreditoLocal', $valesCreditoLocal);
	$stmt->bindParam(':versionRegistro',$versionRegistro);
   
   try{
        // execute the query, also check if query was successful
        if($stmt->execute()){
            return true;

        }
    }   
    catch (Exception $e){
        $this->Mensaje = $e->getMessage();//.'<br /> <br />Consulta: <br />'.$consulta;
    }

    return false;
	}catch (Exception $e){
		$this->Mensaje = $e->getMessage();
		return false;//.'<br /> <br />Consulta: <br />'.$consulta;
	}
    
}


function Cambia($configuracionAplicacionID,$modoDescarga,$muestraBotonExportaVentasEinventarios,$muestraEncabezado,$passwordCerti,$permisoReenvio,$porFormaPago,$timerOutwsFllet,$timerReenvioFlotillas,$urlWerbservicesMobileGas,$protocoloSeguridadTSLID,$urlWebservicesMobileFleet,$imprimeIvaTicket,
			$tipoConsolaID,$tipoConexion,$rutaWebSocket,$tipoValidacionIdentificadores,$validaTarjetaBomba,$imprimirPOS,$jornadaActiva,$preAuth,$enviaTicketCentral,$urlNeoFact,$siic,$vtb,$establecimientoID,$regEstado,$regFechaUltimaModificacion,$regUsuarioUltimaModificacion,$regFormularioUltimaModificacion,$regVersionUltimaModificacion,$vtt,$clco,$timerEstacionServicio,$precioEntradas,$activaTipoReportetotalesInvVentas,$reportePorJornadaYTurno,$grupoPolizaID,$ControlInventarios,$numeroMesesDepuracion,$codigoIgnora,$valesCreditoLocal,$versionRegistro){
	try{
		$query = "UPDATE " . $this->NombreTabla . " SET modoDescarga=:modoDescarga, muestraBotonExportaVentasEinventarios=:muestraBotonExportaVentasEinventarios, muestraEncabezado=:muestraEncabezado, passwordCerti=:passwordCerti, permisoReenvio=:permisoReenvio,
		porFormaPago=:porFormaPago, timerOutwsFllet=:timerOutwsFllet, timerReenvioFlotillas=:timerReenvioFlotillas, urlWerbservicesMobileGas=:urlWerbservicesMobileGas, protocoloSeguridadTSLID=:protocoloSeguridadTSLID, urlWebservicesMobileFleet=:urlWebservicesMobileFleet,
		imprimeIvaTicket=:imprimeIvaTicket, tipoConsolaID=:tipoConsolaID, tipoConexion=:tipoConexion, rutaWebSocket=:rutaWebSocket,tipoValidacionIdentificadores=:tipoValidacionIdentificadores,validaTarjetaBomba=:validaTarjetaBomba,imprimirPOS=:imprimirPOS, jornadaActiva=:jornadaActiva, preAuth=:preAuth, enviaTicketCentral=:enviaTicketCentral, urlNeoFact=:urlNeoFact, siic=:siic, vtb=:vtb, regEstado=:regEstado, regFechaUltimaModificacion=:regFechaUltimaModificacion, 
		regUsuarioUltimaModificacion=:regUsuarioUltimaModificacion, regFormularioUltimaModificacion=:regFormularioUltimaModificacion, regVersionUltimaModificacion=:regVersionUltimaModificacion, vtt=:vtt, clco=:clco, timerEstacionServicio=:timerEstacionServicio, precioEntradas=:precioEntradas, activaTipoReportetotalesInvVentas=:activaTipoReportetotalesInvVentas, reportePorJornadaYTurno=:reportePorJornadaYTurno, grupoPolizaID=:grupoPolizaID, ControlInventarios=:ControlInventarios, numeroMesesDepuracion=:numeroMesesDepuracion, codigoIgnora=:codigoIgnora, valesCreditoLocal=:valesCreditoLocal, versionRegistro=:versionRegistro 
		WHERE configuracionAplicacionID=:configuracionAplicacionID and establecimientoID=:establecimientoID ";

    // prepare the query
    $stmt = $this->Conexion->prepare($query);
 	$this->Mensaje2=$query;
     // sanitize
	$configuracionAplicacionID=htmlspecialchars(strip_tags($configuracionAplicacionID));
	$modoDescarga=htmlspecialchars(strip_tags($modoDescarga));
	$muestraBotonExportaVentasEinventarios=htmlspecialchars(strip_tags($muestraBotonExportaVentasEinventarios));
	$muestraEncabezado=htmlspecialchars(strip_tags($muestraEncabezado));
	$passwordCerti=htmlspecialchars(strip_tags($passwordCerti));
	$permisoReenvio=htmlspecialchars(strip_tags($permisoReenvio));
	$porFormaPago=htmlspecialchars(strip_tags($porFormaPago));
	$timerOutwsFllet=htmlspecialchars(strip_tags($timerOutwsFllet));
	$timerReenvioFlotillas=htmlspecialchars(strip_tags($timerReenvioFlotillas));
	$urlWerbservicesMobileGas=htmlspecialchars(strip_tags($urlWerbservicesMobileGas));
	$protocoloSeguridadTSLID=htmlspecialchars(strip_tags($protocoloSeguridadTSLID));
	$urlWebservicesMobileFleet=htmlspecialchars(strip_tags($urlWebservicesMobileFleet));
	$imprimeIvaTicket=htmlspecialchars(strip_tags($imprimeIvaTicket));
	$tipoConsolaID=htmlspecialchars(strip_tags($tipoConsolaID));
	$tipoConexion=htmlspecialchars(strip_tags($tipoConexion));
	$rutaWebSocket=htmlspecialchars(strip_tags($rutaWebSocket));
	$tipoValidacionIdentificadores=htmlspecialchars(strip_tags($tipoValidacionIdentificadores));
	$validaTarjetaBomba=htmlspecialchars(strip_tags($validaTarjetaBomba));
	$imprimirPOS=htmlspecialchars(strip_tags($imprimirPOS));
	$jornadaActiva=htmlspecialchars(strip_tags($jornadaActiva));
	$preAuth=htmlspecialchars(strip_tags($preAuth));
	$enviaTicketCentral=htmlspecialchars(strip_tags($enviaTicketCentral));
	$urlNeoFact=htmlspecialchars(strip_tags($urlNeoFact));
	$siic=htmlspecialchars(strip_tags($siic));
	$vtb=htmlspecialchars(strip_tags($vtb));
	$establecimientoID=htmlspecialchars(strip_tags($establecimientoID));
	$regEstado=htmlspecialchars(strip_tags($regEstado));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($regFechaUltimaModificacion));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($regUsuarioUltimaModificacion));
	$numeroMesesDepuracion=htmlspecialchars(strip_tags($numeroMesesDepuracion));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($regVersionUltimaModificacion));
	$vtt=htmlspecialchars(strip_tags($vtt));
	$clco=htmlspecialchars(strip_tags($clco));
	$timerEstacionServicio=htmlspecialchars(strip_tags($timerEstacionServicio));
	$precioEntradas=htmlspecialchars(strip_tags($precioEntradas));
	$activaTipoReportetotalesInvVentas=htmlspecialchars(strip_tags($activaTipoReportetotalesInvVentas));
	$reportePorJornadaYTurno=htmlspecialchars(strip_tags($reportePorJornadaYTurno));
	$grupoPolizaID=htmlspecialchars(strip_tags($grupoPolizaID));
	$ControlInventarios=htmlspecialchars(strip_tags($ControlInventarios));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($regVersionUltimaModificacion));
	$codigoIgnora=htmlspecialchars(strip_tags($codigoIgnora));
	$valesCreditoLocal=htmlspecialchars(strip_tags($valesCreditoLocal));
	$versionRegistro=htmlspecialchars(strip_tags($versionRegistro));
   
    // bind the values
	$stmt->bindParam(':configuracionAplicacionID', $configuracionAplicacionID);
	$modoDescarga = (int)$modoDescarga;
	$stmt->bindValue(':modoDescarga', $modoDescarga, PDO::PARAM_INT);
	$stmt->bindParam(':muestraBotonExportaVentasEinventarios', $muestraBotonExportaVentasEinventarios, PDO::PARAM_INT);
	$stmt->bindParam(':muestraEncabezado', $muestraEncabezado, PDO::PARAM_INT);
	$stmt->bindParam(':passwordCerti', $passwordCerti, PDO::PARAM_INT);
	$stmt->bindParam(':permisoReenvio', $permisoReenvio, PDO::PARAM_INT);
	$stmt->bindParam(':porFormaPago', $porFormaPago, PDO::PARAM_INT);
	$stmt->bindParam(':timerOutwsFllet', $timerOutwsFllet);
	$stmt->bindParam(':timerReenvioFlotillas', $timerReenvioFlotillas);
	$stmt->bindParam(':urlWerbservicesMobileGas', $urlWerbservicesMobileGas);
	$stmt->bindParam(':protocoloSeguridadTSLID', $protocoloSeguridadTSLID);
	$stmt->bindParam(':urlWebservicesMobileFleet', $urlWebservicesMobileFleet);
	$stmt->bindParam(':imprimeIvaTicket', $imprimeIvaTicket, PDO::PARAM_INT);
	$stmt->bindParam(':tipoConsolaID', $tipoConsolaID);
	$stmt->bindParam(':tipoConexion', $tipoConexion);
	$stmt->bindParam(':rutaWebSocket', $rutaWebSocket);
	$stmt->bindParam(':tipoValidacionIdentificadores', $tipoValidacionIdentificadores);
	$stmt->bindParam(':validaTarjetaBomba', $validaTarjetaBomba, PDO::PARAM_INT);
	$stmt->bindParam(':imprimirPOS', $imprimirPOS, PDO::PARAM_INT);
	$stmt->bindParam(':jornadaActiva', $jornadaActiva, PDO::PARAM_INT);
	$stmt->bindParam(':preAuth', $preAuth, PDO::PARAM_INT);
	$stmt->bindParam(':enviaTicketCentral', $enviaTicketCentral, PDO::PARAM_INT);
	$stmt->bindParam(':urlNeoFact', $urlNeoFact);
	$stmt->bindParam(':siic', $siic);
	$stmt->bindParam(':vtb', $vtb, PDO::PARAM_INT);
	$stmt->bindParam(':establecimientoID', $establecimientoID);
	$regEstado = (int)$regEstado;
	$stmt->bindValue(':regEstado', $regEstado, PDO::PARAM_INT);
	$stmt->bindParam(':regFechaUltimaModificacion', $regFechaUltimaModificacion);
	$stmt->bindParam(':regUsuarioUltimaModificacion', $regUsuarioUltimaModificacion);
	$stmt->bindParam(':regFormularioUltimaModificacion', $regFormularioUltimaModificacion);
	$stmt->bindParam(':regVersionUltimaModificacion', $regVersionUltimaModificacion);
	$stmt->bindParam(':vtt', $vtt);
	$stmt->bindParam(':clco', $clco);
	$stmt->bindParam(':timerEstacionServicio', $timerEstacionServicio);
	$stmt->bindParam(':precioEntradas', $precioEntradas, PDO::PARAM_INT);
	$stmt->bindParam(':activaTipoReportetotalesInvVentas', $activaTipoReportetotalesInvVentas, PDO::PARAM_INT);
	$stmt->bindParam(':reportePorJornadaYTurno', $reportePorJornadaYTurno, PDO::PARAM_INT);
	$stmt->bindParam(':grupoPolizaID', $grupoPolizaID);
	$stmt->bindParam(':ControlInventarios', $ControlInventarios, PDO::PARAM_INT);
	$stmt->bindParam(':numeroMesesDepuracion', $numeroMesesDepuracion);
	$stmt->bindParam(':codigoIgnora', $codigoIgnora);
	$stmt->bindParam(':valesCreditoLocal', $valesCreditoLocal, PDO::PARAM_INT);
	$stmt->bindParam(':versionRegistro',$versionRegistro);
   
    try{
        // execute the query, also check if query was successful
        if($stmt->execute()){
            return true;

        }
    }   
    catch (Exception $e){
        $this->Mensaje = $e->getMessage();//.'<br /> <br />Consulta: <br />'.$consulta;
    }
 
    return false;
	}catch (Exception $e){
			$this->Mensaje = $e->getMessage();
			return false;//.'<br /> <br />Consulta: <br />'.$consulta;
	}
}
	
		
}
	?>