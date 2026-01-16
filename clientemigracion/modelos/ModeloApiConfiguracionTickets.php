<?php
class ConfiguracionTickets{

private $Conexion;
private $Database;
private $NombreTabla = 'ConfiguracionTickets';

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
    	        $strWhere=" configuracionTicketID=:configuracionTicketID and ";
    	    }
			
    		$query = "select * from " . $this->NombreTabla .  " where ". $strWhere ." establecimientoID=:establecimientoID ";
 

    	// prepare the query
    	$stmt = $this->Conexion->prepare( $query );
 		$this->Mensaje2=$query;
    	 // sanitize

		$ID=htmlspecialchars(strip_tags($ID));
		$establecimientoID=htmlspecialchars(strip_tags($establecimientoID));

    	// bind given id value


    	 // bind the values
		if($ID > 0){
		    $stmt->bindParam(':configuracionTicketID', $ID);
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
			" (configuracionTicketID,mensajeEncabezado,imprimirTransaccionFlotillas,imprimirSaldo,imprimirClave,transaccion,imprimirVehiculo,imprimirRazonSocial,imprimirPortador,imprimirOdometro,imprimirCuentaDescripcion,imprimirCuentaNumero,
			imprimirVehiculoMarca,imprimirVehiculoModelo,imprimirCalendarioTarjeta,imprimirVehiculoID,imprimirVehiculoFlota,imprimirVehiculoCalendario,imprimirVehiculoClase,imprimirVehiculoColor,imprimirVehiculoPlaca,imprimirVehiculoMotor,
			imprimirVehiculoSerie,imprimirTarjeta,imprimirRendimiento,imprimirRuta,imprimeCodigoBarras,imprimePagare,imprimeFirma,imprimeRazonEstacion,imprimeSiic,imprimerClientePemex,imprimeRFC,imprimeEstacion,imprimeDireccionfiscal,
			imprimeLugar,imprimeRegimen,imprimeAdicionalesAutoconsumo,imprimeViaje,imprimeCartaPorte,imprimeOdometroAnterior,imprimeFleje1,imprimeFleje2,imprimeFleje3,imprimeFuel,imprimeFuelEconomy,imprimeIdleFuel,imprimeIdleTime,
			imprimeIdleTimePorcentaje,imprimeDriving,imprimeTotalEngineHours,imprimeProductoTransportado,mensajePOS1,mensajePOS2,soloCodigoFacturacion,tipoFlotillas,serieFactura,mesNotasFacturacion,formatoFactura,muestraTransaccion,
			muestraTransaccionVales,muestraClaveSatComb,
			establecimientoID,versionRegistro,regEstado,regFechaUltimaModificacion,regUsuarioUltimaModificacion,regFormularioUltimaModificacion,regVersionUltimaModificacion) VALUES ";

		$coma = false;
		$comaText = "";
		foreach($registros as $item)
		 {
			//echo $item['textoID']."lolol";
			 if($this->ObtenerDatos($item['configuracionTicketID'], $item['establecimientoID'])){
				$this->Cambia($item['configuracionTicketID'],$item['mensajeEncabezado'],$item['imprimirTransaccionFlotillas'],$item['imprimirSaldo'],$item['imprimirClave'],$item['transaccion'],$item['imprimirVehiculo'],$item['imprimirRazonSocial'],$item['imprimirPortador'],$item['imprimirOdometro'],$item['imprimirCuentaDescripcion'],$item['imprimirCuentaNumero'],
				$item['imprimirVehiculoMarca'],$item['imprimirVehiculoModelo'],$item['imprimirCalendarioTarjeta'],$item['imprimirVehiculoID'],$item['imprimirVehiculoFlota'],$item['imprimirVehiculoCalendario'],$item['imprimirVehiculoClase'],$item['imprimirVehiculoColor'],$item['imprimirVehiculoPlaca'],$item['imprimirVehiculoMotor'],
				$item['imprimirVehiculoSerie'],$item['imprimirTarjeta'],$item['imprimirRendimiento'],$item['imprimirRuta'],$item['imprimeCodigoBarras'],$item['imprimePagare'],$item['imprimeFirma'],$item['imprimeRazonEstacion'],$item['imprimeSiic'],$item['imprimerClientePemex'],$item['imprimeRFC'],$item['imprimeEstacion'],$item['imprimeDireccionfiscal'],
				$item['imprimeLugar'],$item['imprimeRegimen'],$item['imprimeAdicionalesAutoconsumo'],$item['imprimeViaje'],$item['imprimeCartaPorte'],$item['imprimeOdometroAnterior'],$item['imprimeFleje1'],$item['imprimeFleje2'],$item['imprimeFleje3'],$item['imprimeFuel'],$item['imprimeFuelEconomy'],$item['imprimeIdleFuel'],$item['imprimeIdleTime'],
				$item['imprimeIdleTimePorcentaje'],$item['imprimeDriving'],$item['imprimeTotalEngineHours'],$item['imprimeProductoTransportado'],$item['mensajePOS1'],$item['mensajePOS2'],$item['soloCodigoFacturacion'],$item['tipoFlotillas'],$item['serieFactura'],$item['mesNotasFacturacion'],$item['formatoFactura'],$item['muestraTransaccion'],$item['muestraTransaccionVales'],$item['muestraClaveSatComb'],
				$item['establecimientoID'],$item['versionRegistro'],$item['regEstado'],$item['regFechaUltimaModificacion'],$item['regUsuarioUltimaModificacion'],$item['regFormularioUltimaModificacion'],$item['regVersionUltimaModificacion']);
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

				$consulta= $consulta . $comaText . "(" . $item['configuracionTicketID'] . ",'" . $item['mensajeEncabezado'] . "'," . $item['imprimirTransaccionFlotillas'] . "," . $item['imprimirSaldo'] . "," . $item['imprimirClave'] . "," . $item['transaccion'] . "," . $item['imprimirVehiculo'] . "," . $item['imprimirRazonSocial'] . "," . $item['imprimirPortador'] . "," . $item['imprimirOdometro'] . "," . $item['imprimirCuentaDescripcion'] . "," . $item['imprimirCuentaNumero'] . "," .
				$item['imprimirVehiculoMarca'] . "," . $item['imprimirVehiculoModelo'] . "," . $item['imprimirCalendarioTarjeta'] . "," . $item['imprimirVehiculoID'] . "," . $item['imprimirVehiculoFlota'] . "," . $item['imprimirVehiculoCalendario'] . "," . $item['imprimirVehiculoClase'] . "," . $item['imprimirVehiculoColor'] . "," . $item['imprimirVehiculoPlaca'] . "," . $item['imprimirVehiculoMotor'] . "," .
				$item['imprimirVehiculoSerie'] . "," . $item['imprimirTarjeta'] . "," . $item['imprimirRendimiento'] . "," . $item['imprimirRuta'] . "," . $item['imprimeCodigoBarras'] . "," . $item['imprimePagare'] . "," . $item['imprimeFirma'] . "," . $item['imprimeRazonEstacion'] . "," . $item['imprimeSiic'] . "," . $item['imprimerClientePemex'] . "," . $item['imprimeRFC'] . "," . $item['imprimeEstacion'] . "," . $item['imprimeDireccionfiscal'] . "," . 
				$item['imprimeLugar'] . "," . $item['imprimeRegimen'] . "," . $item['imprimeAdicionalesAutoconsumo'] . "," . $item['imprimeViaje'] . "," . $item['imprimeCartaPorte'] . "," . $item['imprimeOdometroAnterior'] . "," . $item['imprimeFleje1'] . "," . $item['imprimeFleje2'] . "," . $item['imprimeFleje3'] . "," . $item['imprimeFuel'] . "," . $item['imprimeFuelEconomy'] . "," . $item['imprimeIdleFuel'] . "," . $item['imprimeIdleTime'] . "," . 
				$item['imprimeIdleTimePorcentaje'] . "," . $item['imprimeDriving'] . "," . $item['imprimeTotalEngineHours'] . "," . $item['imprimeProductoTransportado'] . ",'" . $item['mensajePOS1'] . "','" . $item['mensajePOS2'] . "'," . $item['soloCodigoFacturacion'] . ",'" . $item['tipoFlotillas'] . "','" . $item['serieFactura'] . "'," . $item['mesNotasFacturacion'] . ",'" . $item['formatoFactura'] . "'," . $item['muestraTransaccion'] . "," . $item['muestraTransaccionVales'] . "," . $item['muestraClaveSatComb'] . "," .
				$item['establecimientoID'] . "," . $item['versionRegistro'] . "," . $item['regEstado'] . ",now()," . $item['regUsuarioUltimaModificacion'] . "," . $item['regFormularioUltimaModificacion'] . "," . $item['regVersionUltimaModificacion'] .")";
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
	$configuracionTicketID=htmlspecialchars(strip_tags($item['configuracionTicketID']));
	$mensajeEncabezado=htmlspecialchars(strip_tags($item['mensajeEncabezado']));
	$imprimirTransaccionFlotillas=htmlspecialchars(strip_tags($item['imprimirTransaccionFlotillas']));
	$imprimirSaldo=htmlspecialchars(strip_tags($item['imprimirSaldo']));
	$imprimirClave=htmlspecialchars(strip_tags($item['imprimirClave']));
	$transaccion=htmlspecialchars(strip_tags($item['transaccion']));
	$imprimirVehiculo=htmlspecialchars(strip_tags($item['imprimirVehiculo']));
	$imprimirRazonSocial=htmlspecialchars(strip_tags($item['imprimirRazonSocial']));
	$imprimirPortador=htmlspecialchars(strip_tags($item['imprimirPortador']));
	$imprimirOdometro=htmlspecialchars(strip_tags($item['imprimirOdometro']));
	$imprimirCuentaDescripcion=htmlspecialchars(strip_tags($item['imprimirCuentaDescripcion']));
	$imprimirCuentaNumero=htmlspecialchars(strip_tags($item['imprimirCuentaNumero']));
	$imprimirVehiculoMarca=htmlspecialchars(strip_tags($item['imprimirVehiculoMarca']));
	$imprimirVehiculoModelo=htmlspecialchars(strip_tags($item['imprimirVehiculoModelo']));
	$imprimirCalendarioTarjeta=htmlspecialchars(strip_tags($item['imprimirCalendarioTarjeta']));
	$imprimirVehiculoID=htmlspecialchars(strip_tags($item['imprimirVehiculoID']));
	$imprimirVehiculoFlota=htmlspecialchars(strip_tags($item['imprimirVehiculoFlota']));
	$imprimirVehiculoCalendario=htmlspecialchars(strip_tags($item['imprimirVehiculoCalendario']));
	$imprimirVehiculoClase=htmlspecialchars(strip_tags($item['imprimirVehiculoClase']));
	$imprimirVehiculoColor=htmlspecialchars(strip_tags($item['imprimirVehiculoColor']));
	$imprimirVehiculoPlaca=htmlspecialchars(strip_tags($item['imprimirVehiculoPlaca']));
	$imprimirVehiculoMotor=htmlspecialchars(strip_tags($item['imprimirVehiculoMotor']));
	$imprimirVehiculoSerie=htmlspecialchars(strip_tags($item['imprimirVehiculoSerie']));
	$imprimirTarjeta=htmlspecialchars(strip_tags($item['imprimirTarjeta']));
	$imprimirRendimiento=htmlspecialchars(strip_tags($item['imprimirRendimiento']));
	$imprimirRuta=htmlspecialchars(strip_tags($item['imprimirRuta']));
	$imprimeCodigoBarras=htmlspecialchars(strip_tags($item['imprimeCodigoBarras']));
	$imprimePagare=htmlspecialchars(strip_tags($item['imprimePagare']));
	$imprimeFirma=htmlspecialchars(strip_tags($item['imprimeFirma']));
	$imprimeRazonEstacion=htmlspecialchars(strip_tags($item['imprimeRazonEstacion']));
	$imprimeSiic=htmlspecialchars(strip_tags($item['imprimeSiic']));
	$imprimerClientePemex=htmlspecialchars(strip_tags($item['imprimerClientePemex']));
	$imprimeRFC=htmlspecialchars(strip_tags($item['imprimeRFC']));
	$imprimeEstacion=htmlspecialchars(strip_tags($item['imprimeEstacion']));
	$imprimeDireccionfiscal=htmlspecialchars(strip_tags($item['imprimeDireccionfiscal']));
	$imprimeLugar=htmlspecialchars(strip_tags($item['imprimeLugar']));
	$imprimeRegimen=htmlspecialchars(strip_tags($item['imprimeRegimen']));
	$imprimeAdicionalesAutoconsumo=htmlspecialchars(strip_tags($item['imprimeAdicionalesAutoconsumo']));
	$imprimeViaje=htmlspecialchars(strip_tags($item['imprimeViaje']));
	$imprimeCartaPorte=htmlspecialchars(strip_tags($item['imprimeCartaPorte']));
	$imprimeOdometroAnterior=htmlspecialchars(strip_tags($item['imprimeOdometroAnterior']));
	$imprimeFleje1=htmlspecialchars(strip_tags($item['imprimeFleje1']));
	$imprimeFleje2=htmlspecialchars(strip_tags($item['imprimeFleje2']));
	$imprimeFleje3=htmlspecialchars(strip_tags($item['imprimeFleje3']));
	$imprimeFuel=htmlspecialchars(strip_tags($item['imprimeFuel']));
	$imprimeFuelEconomy=htmlspecialchars(strip_tags($item['imprimeFuelEconomy']));
	$imprimeIdleFuel=htmlspecialchars(strip_tags($item['imprimeIdleFuel']));
	$imprimeIdleTime=htmlspecialchars(strip_tags($item['imprimeIdleTime']));
	$imprimeIdleTimePorcentaje=htmlspecialchars(strip_tags($item['imprimeIdleTimePorcentaje']));
	$imprimeDriving=htmlspecialchars(strip_tags($item['imprimeDriving']));
	$imprimeTotalEngineHours=htmlspecialchars(strip_tags($item['imprimeTotalEngineHours']));
	$imprimeProductoTransportado=htmlspecialchars(strip_tags($item['imprimeProductoTransportado']));
	$mensajePOS1=htmlspecialchars(strip_tags($item['mensajePOS1']));
	$mensajePOS2=htmlspecialchars(strip_tags($item['mensajePOS2']));
	$soloCodigoFacturacion=htmlspecialchars(strip_tags($item['soloCodigoFacturacion']));
	$tipoFlotillas=htmlspecialchars(strip_tags($item['tipoFlotillas']));
	$serieFactura=htmlspecialchars(strip_tags($item['serieFactura']));
	$mesNotasFacturacion=htmlspecialchars(strip_tags($item['mesNotasFacturacion']));
	$formatoFactura=htmlspecialchars(strip_tags($item['formatoFactura']));
	$muestraTransaccion=htmlspecialchars(strip_tags($item['muestraTransaccion']));
	$muestraTransaccionVales=htmlspecialchars(strip_tags($item['muestraTransaccionVales']));
	$muestraClaveSatComb=htmlspecialchars(strip_tags($item['muestraClaveSatComb']));
	$establecimientoID=htmlspecialchars(strip_tags($item['establecimientoID']));
	$versionRegistro=htmlspecialchars(strip_tags($item['versionRegistro']));
	$regEstado=htmlspecialchars(strip_tags($item['regEstado']));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($item['regFechaUltimaModificacion']));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($item['regUsuarioUltimaModificacion']));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($item['regFormularioUltimaModificacion']));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($item['regVersionUltimaModificacion']));

    // bind the values
	$stmt->bindParam(':configuracionTicketID', $configuracionTicketID);
	$stmt->bindParam(':mensajeEncabezado', $mensajeEncabezado);
	$stmt->bindParam(':imprimirTransaccionFlotillas', $imprimirTransaccionFlotillas);
	$stmt->bindParam(':imprimirSaldo', $imprimirSaldo);
	$stmt->bindParam(':imprimirClave', $imprimirClave);
	$stmt->bindParam(':transaccion', $transaccion);
	$stmt->bindParam(':imprimirVehiculo', $imprimirVehiculo);
	$stmt->bindParam(':imprimirRazonSocial', $imprimirRazonSocial);
	$stmt->bindParam(':imprimirPortador', $imprimirPortador);
	$stmt->bindParam(':imprimirOdometro', $imprimirOdometro);
	$stmt->bindParam(':imprimirCuentaDescripcion', $imprimirCuentaDescripcion);
	$stmt->bindParam(':imprimirCuentaNumero', $imprimirCuentaNumero);
	$stmt->bindParam(':imprimirVehiculoMarca', $imprimirVehiculoMarca);
	$stmt->bindParam(':imprimirVehiculoModelo', $imprimirVehiculoModelo);
	$stmt->bindParam(':imprimirCalendarioTarjeta', $imprimirCalendarioTarjeta);
	$stmt->bindParam(':imprimirVehiculoID', $imprimirVehiculoID);
	$stmt->bindParam(':imprimirVehiculoFlota', $imprimirVehiculoFlota);
	$stmt->bindParam(':imprimirVehiculoCalendario', $imprimirVehiculoCalendario);
	$stmt->bindParam(':imprimirVehiculoClase', $imprimirVehiculoClase);
	$stmt->bindParam(':imprimirVehiculoColor', $imprimirVehiculoColor);
	$stmt->bindParam(':imprimirVehiculoPlaca', $imprimirVehiculoPlaca);
	$stmt->bindParam(':imprimirVehiculoMotor', $imprimirVehiculoMotor);
	$stmt->bindParam(':imprimirVehiculoSerie', $imprimirVehiculoSerie);
	$stmt->bindParam(':imprimirTarjeta', $imprimirTarjeta);
	$stmt->bindParam(':imprimirRendimiento', $imprimirRendimiento);
	$stmt->bindParam(':imprimirRuta', $imprimirRuta);
	$stmt->bindParam(':imprimeCodigoBarras', $imprimeCodigoBarras);
	$stmt->bindParam(':imprimePagare', $imprimePagare);
	$stmt->bindParam(':imprimeFirma', $imprimeFirma);
	$stmt->bindParam(':imprimeRazonEstacion', $imprimeRazonEstacion);
	$stmt->bindParam(':imprimeSiic', $imprimeSiic);
	$stmt->bindParam(':imprimerClientePemex', $imprimerClientePemex);
	$stmt->bindParam(':imprimeRFC', $imprimeRFC);
	$stmt->bindParam(':imprimeEstacion', $imprimeEstacion);
	$stmt->bindParam(':imprimeDireccionfiscal', $imprimeDireccionfiscal);
	$stmt->bindParam(':imprimeLugar', $imprimeLugar);
	$stmt->bindParam(':imprimeRegimen', $imprimeRegimen);
	$stmt->bindParam(':imprimeAdicionalesAutoconsumo', $imprimeAdicionalesAutoconsumo);
	$stmt->bindParam(':imprimeViaje', $imprimeViaje);
	$stmt->bindParam(':imprimeCartaPorte', $imprimeCartaPorte);
	$stmt->bindParam(':imprimeOdometroAnterior', $imprimeOdometroAnterior);
	$stmt->bindParam(':imprimeFleje1', $imprimeFleje1);
	$stmt->bindParam(':imprimeFleje2', $imprimeFleje2);
	$stmt->bindParam(':imprimeFleje3', $imprimeFleje3);
	$stmt->bindParam(':imprimeFuel', $imprimeFuel);
	$stmt->bindParam(':imprimeFuelEconomy', $imprimeFuelEconomy);
	$stmt->bindParam(':imprimeIdleFuel', $imprimeIdleFuel);
	$stmt->bindParam(':imprimeIdleTime', $imprimeIdleTime);
	$stmt->bindParam(':imprimeIdleTimePorcentaje', $imprimeIdleTimePorcentaje);
	$stmt->bindParam(':imprimeDriving', $imprimeDriving);
	$stmt->bindParam(':imprimeTotalEngineHours', $imprimeTotalEngineHours);
	$stmt->bindParam(':imprimeProductoTransportado', $imprimeProductoTransportado);
	$stmt->bindParam(':mensajePOS1', $mensajePOS1);
	$stmt->bindParam(':mensajePOS2', $mensajePOS2);
	$stmt->bindParam(':soloCodigoFacturacion', $soloCodigoFacturacion);
	$stmt->bindParam(':tipoFlotillas', $tipoFlotillas);
	$stmt->bindParam(':serieFactura', $serieFactura);
	$stmt->bindParam(':mesNotasFacturacion', $mesNotasFacturacion);
	$stmt->bindParam(':formatoFactura', $formatoFactura);
	$stmt->bindParam(':muestraTransaccion', $muestraTransaccion);
	$stmt->bindParam(':muestraTransaccionVales', $muestraTransaccionVales);
	$stmt->bindParam(':muestraClaveSatComb', $muestraClaveSatComb);
	$stmt->bindParam(':establecimientoID', $establecimientoID);
	$stmt->bindParam(':versionRegistro', $versionRegistro);
	$regEstado = (int)$regEstado;
	$stmt->bindValue(':regEstado', $regEstado, PDO::PARAM_INT);
	$stmt->bindParam(':regFechaUltimaModificacion', $regFechaUltimaModificacion);
	$stmt->bindParam(':regUsuarioUltimaModificacion', $regUsuarioUltimaModificacion);
	$stmt->bindParam(':regFormularioUltimaModificacion', $regFormularioUltimaModificacion);
	$stmt->bindParam(':regVersionUltimaModificacion', $regVersionUltimaModificacion);

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

function Cambia($configuracionTicketID,$mensajeEncabezado,$imprimirTransaccionFlotillas,$imprimirSaldo,$imprimirClave,$transaccion,$imprimirVehiculo,$imprimirRazonSocial,$imprimirPortador,$imprimirOdometro,$imprimirCuentaDescripcion,$imprimirCuentaNumero,
				$imprimirVehiculoMarca,$imprimirVehiculoModelo,$imprimirCalendarioTarjeta,$imprimirVehiculoID,$imprimirVehiculoFlota,$imprimirVehiculoCalendario,$imprimirVehiculoClase,$imprimirVehiculoColor,$imprimirVehiculoPlaca,$imprimirVehiculoMotor,
				$imprimirVehiculoSerie,$imprimirTarjeta,$imprimirRendimiento,$imprimirRuta,$imprimeCodigoBarras,$imprimePagare,$imprimeFirma,$imprimeRazonEstacion,$imprimeSiic,$imprimerClientePemex,$imprimeRFC,$imprimeEstacion,$imprimeDireccionfiscal,
				$imprimeLugar,$imprimeRegimen,$imprimeAdicionalesAutoconsumo,$imprimeViaje,$imprimeCartaPorte,$imprimeOdometroAnterior,$imprimeFleje1,$imprimeFleje2,$imprimeFleje3,$imprimeFuel,$imprimeFuelEconomy,$imprimeIdleFuel,$imprimeIdleTime,
    $imprimeIdleTimePorcentaje,$imprimeDriving,$imprimeTotalEngineHours,$imprimeProductoTransportado,$mensajePOS1,$mensajePOS2,$soloCodigoFacturacion,$tipoFlotillas,$serieFactura,$mesNotasFacturacion,$formatoFactura,$muestraTransaccion,$muestraTransaccionVales,$muestraClaveSatComb,
				$establecimientoID,$versionRegistro,$regEstado,$regFechaUltimaModificacion,$regUsuarioUltimaModificacion,$regFormularioUltimaModificacion,$regVersionUltimaModificacion){
	try{
		$query = "UPDATE " . $this->NombreTabla . " SET mensajeEncabezado=:mensajeEncabezado, imprimirTransaccionFlotillas=:imprimirTransaccionFlotillas, imprimirSaldo=:imprimirSaldo, imprimirClave=:imprimirClave, transaccion=:transaccion, imprimirVehiculo=:imprimirVehiculo, imprimirRazonSocial=:imprimirRazonSocial, imprimirPortador=:imprimirPortador,
		imprimirOdometro=:imprimirOdometro, imprimirCuentaDescripcion=:imprimirCuentaDescripcion, imprimirCuentaNumero=:imprimirCuentaNumero, imprimirVehiculoMarca=:imprimirVehiculoMarca, imprimirVehiculoModelo=:imprimirVehiculoModelo, imprimirCalendarioTarjeta=:imprimirCalendarioTarjeta, imprimirVehiculoID=:imprimirVehiculoID,
		imprimirVehiculoFlota=:imprimirVehiculoFlota, imprimirVehiculoCalendario=:imprimirVehiculoCalendario, imprimirVehiculoClase=:imprimirVehiculoClase, imprimirVehiculoColor=:imprimirVehiculoColor, imprimirVehiculoPlaca=:imprimirVehiculoPlaca, imprimirVehiculoMotor=:imprimirVehiculoMotor,
		imprimirVehiculoSerie=:imprimirVehiculoSerie, imprimirTarjeta=:imprimirTarjeta, imprimirRendimiento=:imprimirRendimiento, imprimirRuta=:imprimirRuta, imprimeCodigoBarras=:imprimeCodigoBarras, imprimePagare=:imprimePagare,imprimeFirma=:imprimeFirma,imprimeRazonEstacion=:imprimeRazonEstacion,imprimeSiic=:imprimeSiic,
		imprimerClientePemex=:imprimerClientePemex,imprimeRFC=:imprimeRFC,imprimeEstacion=:imprimeEstacion,imprimeDireccionfiscal=:imprimeDireccionfiscal,imprimeLugar=:imprimeLugar,imprimeRegimen=:imprimeRegimen,imprimeAdicionalesAutoconsumo=:imprimeAdicionalesAutoconsumo,imprimeViaje=:imprimeViaje,
		imprimeCartaPorte=:imprimeCartaPorte,imprimeOdometroAnterior=:imprimeOdometroAnterior,imprimeFleje1=:imprimeFleje1,imprimeFleje2=:imprimeFleje2,imprimeFleje3=:imprimeFleje3,imprimeFuel=:imprimeFuel,imprimeFuelEconomy=:imprimeFuelEconomy,imprimeIdleFuel=:imprimeIdleFuel,imprimeIdleTime=:imprimeIdleTime,
		imprimeIdleTimePorcentaje=:imprimeIdleTimePorcentaje,imprimeDriving=:imprimeDriving,imprimeTotalEngineHours=:imprimeTotalEngineHours,imprimeProductoTransportado=:imprimeProductoTransportado,mensajePOS1=:mensajePOS1, mensajePOS2=:mensajePOS2, soloCodigoFacturacion=:soloCodigoFacturacion,tipoFlotillas=:tipoFlotillas,
		serieFactura=:serieFactura,mesNotasFacturacion=:mesNotasFacturacion,formatoFactura=:formatoFactura,muestraTransaccion=:muestraTransaccion,muestraTransaccionVales=:muestraTransaccionVales,muestraClaveSatComb=:muestraClaveSatComb,
		versionRegistro=:versionRegistro, regEstado=:regEstado, regFechaUltimaModificacion=:regFechaUltimaModificacion, regUsuarioUltimaModificacion=:regUsuarioUltimaModificacion, regFormularioUltimaModificacion=:regFormularioUltimaModificacion, regVersionUltimaModificacion=:regVersionUltimaModificacion
		WHERE configuracionTicketID=:configuracionTicketID and establecimientoID=:establecimientoID ";

    // prepare the query
    $stmt = $this->Conexion->prepare($query);
 	$this->Mensaje2=$query;
     // sanitize
	$configuracionTicketID=htmlspecialchars(strip_tags($configuracionTicketID));
	$mensajeEncabezado=htmlspecialchars(strip_tags($mensajeEncabezado));
	$imprimirTransaccionFlotillas=htmlspecialchars(strip_tags($imprimirTransaccionFlotillas));
	$imprimirSaldo=htmlspecialchars(strip_tags($imprimirSaldo));
	$imprimirClave=htmlspecialchars(strip_tags($imprimirClave));
	$transaccion=htmlspecialchars(strip_tags($transaccion));
	$imprimirVehiculo=htmlspecialchars(strip_tags($imprimirVehiculo));
	$imprimirRazonSocial=htmlspecialchars(strip_tags($imprimirRazonSocial));
	$imprimirPortador=htmlspecialchars(strip_tags($imprimirPortador));
	$imprimirOdometro=htmlspecialchars(strip_tags($imprimirOdometro));
	$imprimirCuentaDescripcion=htmlspecialchars(strip_tags($imprimirCuentaDescripcion));
	$imprimirCuentaNumero=htmlspecialchars(strip_tags($imprimirCuentaNumero));
	$imprimirVehiculoMarca=htmlspecialchars(strip_tags($imprimirVehiculoMarca));
	$imprimirVehiculoModelo=htmlspecialchars(strip_tags($imprimirVehiculoModelo));
	$imprimirCalendarioTarjeta=htmlspecialchars(strip_tags($imprimirCalendarioTarjeta));
	$imprimirVehiculoID=htmlspecialchars(strip_tags($imprimirVehiculoID));
	$imprimirVehiculoFlota=htmlspecialchars(strip_tags($imprimirVehiculoFlota));
	$imprimirVehiculoCalendario=htmlspecialchars(strip_tags($imprimirVehiculoCalendario));
	$imprimirVehiculoClase=htmlspecialchars(strip_tags($imprimirVehiculoClase));
	$imprimirVehiculoColor=htmlspecialchars(strip_tags($imprimirVehiculoColor));
	$imprimirVehiculoPlaca=htmlspecialchars(strip_tags($imprimirVehiculoPlaca));
	$imprimirVehiculoMotor=htmlspecialchars(strip_tags($imprimirVehiculoMotor));
	$imprimirVehiculoSerie=htmlspecialchars(strip_tags($imprimirVehiculoSerie));
	$imprimirTarjeta=htmlspecialchars(strip_tags($imprimirTarjeta));
	$imprimirRendimiento=htmlspecialchars(strip_tags($imprimirRendimiento));
	$imprimirRuta=htmlspecialchars(strip_tags($imprimirRuta));
	$imprimeCodigoBarras=htmlspecialchars(strip_tags($imprimeCodigoBarras));
	$imprimePagare=htmlspecialchars(strip_tags($imprimePagare));
	$imprimeFirma=htmlspecialchars(strip_tags($imprimeFirma));
	$imprimeRazonEstacion=htmlspecialchars(strip_tags($imprimeRazonEstacion));
	$imprimeSiic=htmlspecialchars(strip_tags($imprimeSiic));
	$imprimerClientePemex=htmlspecialchars(strip_tags($imprimerClientePemex));
	$imprimeRFC=htmlspecialchars(strip_tags($imprimeRFC));
	$imprimeEstacion=htmlspecialchars(strip_tags($imprimeEstacion));
	$imprimeDireccionfiscal=htmlspecialchars(strip_tags($imprimeDireccionfiscal));
	$imprimeLugar=htmlspecialchars(strip_tags($imprimeLugar));
	$imprimeRegimen=htmlspecialchars(strip_tags($imprimeRegimen));
	$imprimeAdicionalesAutoconsumo=htmlspecialchars(strip_tags($imprimeAdicionalesAutoconsumo));
	$imprimeViaje=htmlspecialchars(strip_tags($imprimeViaje));
	$imprimeCartaPorte=htmlspecialchars(strip_tags($imprimeCartaPorte));
	$imprimeOdometroAnterior=htmlspecialchars(strip_tags($imprimeOdometroAnterior));
	$imprimeFleje1=htmlspecialchars(strip_tags($imprimeFleje1));
	$imprimeFleje2=htmlspecialchars(strip_tags($imprimeFleje2));
	$imprimeFleje3=htmlspecialchars(strip_tags($imprimeFleje3));
	$imprimeFuel=htmlspecialchars(strip_tags($imprimeFuel));
	$imprimeFuelEconomy=htmlspecialchars(strip_tags($imprimeFuelEconomy));
	$imprimeIdleFuel=htmlspecialchars(strip_tags($imprimeIdleFuel));
	$imprimeIdleTime=htmlspecialchars(strip_tags($imprimeIdleTime));
	$imprimeIdleTimePorcentaje=htmlspecialchars(strip_tags($imprimeIdleTimePorcentaje));
	$imprimeDriving=htmlspecialchars(strip_tags($imprimeDriving));
	$imprimeTotalEngineHours=htmlspecialchars(strip_tags($imprimeTotalEngineHours));
	$imprimeProductoTransportado=htmlspecialchars(strip_tags($imprimeProductoTransportado));
	$mensajePOS1=htmlspecialchars(strip_tags($mensajePOS1));
	$mensajePOS2=htmlspecialchars(strip_tags($mensajePOS2));
	$soloCodigoFacturacion=htmlspecialchars(strip_tags($soloCodigoFacturacion));
	$tipoFlotillas=htmlspecialchars(strip_tags($tipoFlotillas));
	$serieFactura=htmlspecialchars(strip_tags($serieFactura));
	$mesNotasFacturacion=htmlspecialchars(strip_tags($mesNotasFacturacion));
	$formatoFactura=htmlspecialchars(strip_tags($formatoFactura));
	$muestraTransaccion=htmlspecialchars(strip_tags($muestraTransaccion));
	$muestraTransaccionVales=htmlspecialchars(strip_tags($muestraTransaccionVales));
	$muestraClaveSatComb=htmlspecialchars(strip_tags($muestraClaveSatComb));
	$establecimientoID=htmlspecialchars(strip_tags($establecimientoID));
	$versionRegistro=htmlspecialchars(strip_tags($versionRegistro));
	$regEstado=htmlspecialchars(strip_tags($regEstado));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($regFechaUltimaModificacion));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($regUsuarioUltimaModificacion));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($regFormularioUltimaModificacion));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($regVersionUltimaModificacion));

    // bind the values
	$stmt->bindParam(':configuracionTicketID', $configuracionTicketID);
	$stmt->bindParam(':mensajeEncabezado', $mensajeEncabezado);
	$stmt->bindParam(':imprimirTransaccionFlotillas', $imprimirTransaccionFlotillas, PDO::PARAM_INT);
	$stmt->bindParam(':imprimirSaldo', $imprimirSaldo, PDO::PARAM_INT);
	$stmt->bindParam(':imprimirClave', $imprimirClave, PDO::PARAM_INT);
	$stmt->bindParam(':transaccion', $transaccion, PDO::PARAM_INT);
	$stmt->bindParam(':imprimirVehiculo', $imprimirVehiculo, PDO::PARAM_INT);
	$stmt->bindParam(':imprimirRazonSocial', $imprimirRazonSocial, PDO::PARAM_INT);
	$stmt->bindParam(':imprimirPortador', $imprimirPortador, PDO::PARAM_INT);
	$stmt->bindParam(':imprimirOdometro', $imprimirOdometro, PDO::PARAM_INT);
	$stmt->bindParam(':imprimirCuentaDescripcion', $imprimirCuentaDescripcion, PDO::PARAM_INT);
	$stmt->bindParam(':imprimirCuentaNumero', $imprimirCuentaNumero, PDO::PARAM_INT);
	$stmt->bindParam(':imprimirVehiculoMarca', $imprimirVehiculoMarca, PDO::PARAM_INT);
	$stmt->bindParam(':imprimirVehiculoModelo', $imprimirVehiculoModelo, PDO::PARAM_INT);
	$stmt->bindParam(':imprimirCalendarioTarjeta', $imprimirCalendarioTarjeta, PDO::PARAM_INT);
	$stmt->bindParam(':imprimirVehiculoID', $imprimirVehiculoID, PDO::PARAM_INT);
	$stmt->bindParam(':imprimirVehiculoFlota', $imprimirVehiculoFlota, PDO::PARAM_INT);
	$stmt->bindParam(':imprimirVehiculoCalendario', $imprimirVehiculoCalendario, PDO::PARAM_INT);
	$stmt->bindParam(':imprimirVehiculoClase', $imprimirVehiculoClase, PDO::PARAM_INT);
	$stmt->bindParam(':imprimirVehiculoColor', $imprimirVehiculoColor, PDO::PARAM_INT);
	$stmt->bindParam(':imprimirVehiculoPlaca', $imprimirVehiculoPlaca, PDO::PARAM_INT);
	$stmt->bindParam(':imprimirVehiculoMotor', $imprimirVehiculoMotor, PDO::PARAM_INT);
	$stmt->bindParam(':imprimirVehiculoSerie', $imprimirVehiculoSerie, PDO::PARAM_INT);
	$stmt->bindParam(':imprimirTarjeta', $imprimirTarjeta, PDO::PARAM_INT);
	$stmt->bindParam(':imprimirRendimiento', $imprimirRendimiento, PDO::PARAM_INT);
	$stmt->bindParam(':imprimirRuta', $imprimirRuta, PDO::PARAM_INT);
	$stmt->bindParam(':imprimeCodigoBarras', $imprimeCodigoBarras, PDO::PARAM_INT);
	$stmt->bindParam(':imprimePagare', $imprimePagare, PDO::PARAM_INT);
	$stmt->bindParam(':imprimeFirma', $imprimeFirma, PDO::PARAM_INT);
	$stmt->bindParam(':imprimeRazonEstacion', $imprimeRazonEstacion, PDO::PARAM_INT);
	$stmt->bindParam(':imprimeSiic', $imprimeSiic, PDO::PARAM_INT);
	$stmt->bindParam(':imprimerClientePemex', $imprimerClientePemex, PDO::PARAM_INT);
	$stmt->bindParam(':imprimeRFC', $imprimeRFC, PDO::PARAM_INT);
	$stmt->bindParam(':imprimeEstacion', $imprimeEstacion, PDO::PARAM_INT);
	$stmt->bindParam(':imprimeDireccionfiscal', $imprimeDireccionfiscal, PDO::PARAM_INT);
	$stmt->bindParam(':imprimeLugar', $imprimeLugar, PDO::PARAM_INT);
	$stmt->bindParam(':imprimeRegimen', $imprimeRegimen, PDO::PARAM_INT);
	$stmt->bindParam(':imprimeAdicionalesAutoconsumo', $imprimeAdicionalesAutoconsumo, PDO::PARAM_INT);
	$stmt->bindParam(':imprimeViaje', $imprimeViaje, PDO::PARAM_INT);
	$stmt->bindParam(':imprimeCartaPorte', $imprimeCartaPorte, PDO::PARAM_INT);
	$stmt->bindParam(':imprimeOdometroAnterior', $imprimeOdometroAnterior, PDO::PARAM_INT);
	$stmt->bindParam(':imprimeFleje1', $imprimeFleje1, PDO::PARAM_INT);
	$stmt->bindParam(':imprimeFleje2', $imprimeFleje2, PDO::PARAM_INT);
	$stmt->bindParam(':imprimeFleje3', $imprimeFleje3, PDO::PARAM_INT);
	$stmt->bindParam(':imprimeFuel', $imprimeFuel, PDO::PARAM_INT);
	$stmt->bindParam(':imprimeFuelEconomy', $imprimeFuelEconomy, PDO::PARAM_INT);
	$stmt->bindParam(':imprimeIdleFuel', $imprimeIdleFuel, PDO::PARAM_INT);
	$stmt->bindParam(':imprimeIdleTime', $imprimeIdleTime, PDO::PARAM_INT);
	$stmt->bindParam(':imprimeIdleTimePorcentaje', $imprimeIdleTimePorcentaje, PDO::PARAM_INT);
	$stmt->bindParam(':imprimeDriving', $imprimeDriving, PDO::PARAM_INT);
	$stmt->bindParam(':imprimeTotalEngineHours', $imprimeTotalEngineHours, PDO::PARAM_INT);
	$stmt->bindParam(':imprimeProductoTransportado', $imprimeProductoTransportado, PDO::PARAM_INT);
	$stmt->bindParam(':mensajePOS1', $mensajePOS1);
	$stmt->bindParam(':mensajePOS2', $mensajePOS2);
	$stmt->bindParam(':soloCodigoFacturacion', $soloCodigoFacturacion, PDO::PARAM_INT);
	$stmt->bindParam(':tipoFlotillas', $tipoFlotillas);
	$stmt->bindParam(':serieFactura', $serieFactura);
	$stmt->bindParam(':mesNotasFacturacion', $mesNotasFacturacion, PDO::PARAM_INT);
	$stmt->bindParam(':formatoFactura', $formatoFactura);
	$stmt->bindParam(':muestraTransaccion', $muestraTransaccion, PDO::PARAM_INT);
	$stmt->bindParam(':muestraTransaccionVales', $muestraTransaccionVales, PDO::PARAM_INT);
	$stmt->bindParam(':muestraClaveSatComb', $muestraClaveSatComb, PDO::PARAM_INT);
	$stmt->bindParam(':establecimientoID', $establecimientoID);
	$stmt->bindParam(':versionRegistro', $versionRegistro);
	$regEstado = (int)$regEstado;
	$stmt->bindValue(':regEstado', $regEstado, PDO::PARAM_INT);
	$stmt->bindParam(':regFechaUltimaModificacion', $regFechaUltimaModificacion);
	$stmt->bindParam(':regUsuarioUltimaModificacion', $regUsuarioUltimaModificacion);
	$stmt->bindParam(':regFormularioUltimaModificacion', $regFormularioUltimaModificacion);
	$stmt->bindParam(':regVersionUltimaModificacion', $regVersionUltimaModificacion);

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
