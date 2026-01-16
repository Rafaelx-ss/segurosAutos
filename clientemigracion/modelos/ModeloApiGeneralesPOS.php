<?php 
class GeneralesPos{ 
 
private $Conexion; 
private $Database; 
private $NombreTabla = 'GeneralesPos'; 
 
// object properties
public $Campos;
public $Dataset;
public $Mensaje;
 
// constructor
    public function __construct($dbf){
        $this->Database = $dbf;
        $this->Conexion = $dbf->conn;
    }
	
	function ObtenerDatos($id=0,$establecimientoID=0){
    	// query to check if email exists
    	$query = "select * from " . $this->NombreTabla .  " where 1 " . ($id > 0 ? " and generalPOSID = ?" : "");
 
    	// prepare the query
    	$stmt = $this->Conexion->prepare( $query );
 
    	
	$id=htmlspecialchars(strip_tags($id));
 
    	// bind given id value

    
    	
    	if($id > 0){
      		$stmt->bindParam(1, $id);
    	}
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
	}
 

function Inserta($registros){

    $consulta="";
	
	$consulta = 'INSERT INTO ' . $this->NombreTabla . 
			" (generalPOSID,validarExistencias,validarNipDespachador,usarMonedero,usarNipMonedero,permiteFacturar,permiteReimprimir,usarDatosAutoconsumo,validarViaje,validarDestino,eliminarDigitosInicio,eliminarDigitosFin,Impresion,bombasVisibles,maximoValesAceptados,maximaCantidadCodeBar,cantidadMenus,forzarImprecionAutoconsumos,urlWebPos,mostrarSiempre,maximizado,contrasenia,ipConexionSocket,puertoSocket,tiempoEsperaGasopay,cantidadColumnasBombasMuestra,CantidadMaximaBombasMuestra,mostrarTeclado,tiempoActualizacionAutomatica,permitirSoloLectura,rutaWebSocket,mostrarBotonEnter,posID,establecimientoID,versionRegistro,regEstado,regFechaUltimaModificacion,regUsuarioUltimaModificacion,regFormularioUltimaModificacion,regVersionUltimaModificacion) VALUES ";
			
		$coma = false;
		$comaText = "";
		foreach($registros as $item)
		 {
			//echo $item['textoID']."lolol";
			 if($this->ObtenerDatos($item['generalPOSID'])){
				$this->Cambia($item['generalPOSID'],$item['validarExistencias'],$item['validarNipDespachador'],$item['usarMonedero'],$item['usarNipMonedero'],$item['permiteFacturar'],$item['permiteReimprimir'],$item['usarDatosAutoconsumo'],$item['validarViaje'],$item['validarDestino'],$item['eliminarDigitosInicio'],$item['eliminarDigitosFin'],$item['Impresion'],($item['bombasVisibles']),$item['maximoValesAceptados'],$item['maximaCantidadCodeBar'],$item['cantidadMenus'],$item['forzarImprecionAutoconsumos'],($item['urlWebPos']),$item['mostrarSiempre'],$item['maximizado'],($item['contrasenia']),($item['ipConexionSocket']),($item['puertoSocket']),$item['tiempoEsperaGasopay'],$item['cantidadColumnasBombasMuestra'],$item['CantidadMaximaBombasMuestra'],$item['mostrarTeclado'],$item['tiempoActualizacionAutomatica'],$item['permitirSoloLectura'],($item['rutaWebSocket']),$item['mostrarBotonEnter'],$item['posID'],($item['establecimientoID']),$item['versionRegistro'],$item['regEstado'],($item['regFechaUltimaModificacion']),$item['regUsuarioUltimaModificacion'],$item['regFormularioUltimaModificacion'],$item['regVersionUltimaModificacion']);
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
				
				$consulta= $consulta . $comaText . "(" . $item["generalPOSID"] . "," . $item["validarExistencias"] . "," . $item["validarNipDespachador"] . "," . $item["usarMonedero"] . "," . $item["usarNipMonedero"] . "," . $item["permiteFacturar"] . "," . $item["permiteReimprimir"] . "," . $item["usarDatosAutoconsumo"] . "," . $item["validarViaje"] . "," . $item["validarDestino"] . "," . $item["eliminarDigitosInicio"] . "," . $item["eliminarDigitosFin"] . "," . $item["Impresion"] . ",'" . ($item["bombasVisibles"]) . "'," . $item["maximoValesAceptados"] . "," . $item["maximaCantidadCodeBar"] . "," . $item["cantidadMenus"] . "," . $item["forzarImprecionAutoconsumos"] . ",'" . ($item["urlWebPos"]) . "'," . $item["mostrarSiempre"] . "," . $item["maximizado"] . ",'" . ($item["contrasenia"]) . "','" . ($item["ipConexionSocket"]) . "','" . ($item["puertoSocket"]) . "'," . $item["tiempoEsperaGasopay"] . "," . $item["cantidadColumnasBombasMuestra"] . "," . $item["CantidadMaximaBombasMuestra"] . "," . $item["mostrarTeclado"] . "," . $item["tiempoActualizacionAutomatica"] . "," . $item["permitirSoloLectura"] . ",'" . ($item["rutaWebSocket"]) . "'," . $item["mostrarBotonEnter"] . "," . $item["posID"] . ",'" . ($item["establecimientoID"]) . "'," . $item["versionRegistro"] . "," . $item["regEstado"] . ",'" . ($item["regFechaUltimaModificacion"]) . "'," . $item["regUsuarioUltimaModificacion"] . "," . $item["regFormularioUltimaModificacion"] . "," . $item["regVersionUltimaModificacion"] . ")";
			}
			 
		   
		 }
	if(!$coma){
		$consulta="select 1";
	}
	
	$this->query=$consulta;
	
    // prepare the query
    $stmt = $this->Conexion->prepare($this->query);
	
    // sanitize
	$generalPOSID=htmlspecialchars(strip_tags($item['generalPOSID']));
	$validarExistencias=htmlspecialchars(strip_tags($item['validarExistencias']));
	$validarNipDespachador=htmlspecialchars(strip_tags($item['validarNipDespachador']));
	$usarMonedero=htmlspecialchars(strip_tags($item['usarMonedero']));
	$usarNipMonedero=htmlspecialchars(strip_tags($item['usarNipMonedero']));
	$permiteFacturar=htmlspecialchars(strip_tags($item['permiteFacturar']));
	$permiteReimprimir=htmlspecialchars(strip_tags($item['permiteReimprimir']));
	$usarDatosAutoconsumo=htmlspecialchars(strip_tags($item['usarDatosAutoconsumo']));
	$validarViaje=htmlspecialchars(strip_tags($item['validarViaje']));
	$validarDestino=htmlspecialchars(strip_tags($item['validarDestino']));
	$eliminarDigitosInicio=htmlspecialchars(strip_tags($item['eliminarDigitosInicio']));
	$eliminarDigitosFin=htmlspecialchars(strip_tags($item['eliminarDigitosFin']));
	$Impresion=htmlspecialchars(strip_tags($item['Impresion']));
	$bombasVisibles=htmlspecialchars(strip_tags($item['bombasVisibles']));
	$maximoValesAceptados=htmlspecialchars(strip_tags($item['maximoValesAceptados']));
	$maximaCantidadCodeBar=htmlspecialchars(strip_tags($item['maximaCantidadCodeBar']));
	$cantidadMenus=htmlspecialchars(strip_tags($item['cantidadMenus']));
	$forzarImprecionAutoconsumos=htmlspecialchars(strip_tags($item['forzarImprecionAutoconsumos']));
	$urlWebPos=htmlspecialchars(strip_tags($item['urlWebPos']));
	$mostrarSiempre=htmlspecialchars(strip_tags($item['mostrarSiempre']));
	$maximizado=htmlspecialchars(strip_tags($item['maximizado']));
	$contrasenia=htmlspecialchars(strip_tags($item['contrasenia']));
	$ipConexionSocket=htmlspecialchars(strip_tags($item['ipConexionSocket']));
	$puertoSocket=htmlspecialchars(strip_tags($item['puertoSocket']));
	$tiempoEsperaGasopay=htmlspecialchars(strip_tags($item['tiempoEsperaGasopay']));
	$cantidadColumnasBombasMuestra=htmlspecialchars(strip_tags($item['cantidadColumnasBombasMuestra']));
	$CantidadMaximaBombasMuestra=htmlspecialchars(strip_tags($item['CantidadMaximaBombasMuestra']));
	$mostrarTeclado=htmlspecialchars(strip_tags($item['mostrarTeclado']));
	$tiempoActualizacionAutomatica=htmlspecialchars(strip_tags($item['tiempoActualizacionAutomatica']));
	$permitirSoloLectura=htmlspecialchars(strip_tags($item['permitirSoloLectura']));
	$rutaWebSocket=htmlspecialchars(strip_tags($item['rutaWebSocket']));
	$mostrarBotonEnter=htmlspecialchars(strip_tags($item['mostrarBotonEnter']));
	$posID=htmlspecialchars(strip_tags($item['posID']));
	$establecimientoID=htmlspecialchars(strip_tags($item['establecimientoID']));
	$versionRegistro=htmlspecialchars(strip_tags($item['versionRegistro']));
	$regEstado=htmlspecialchars(strip_tags($item['regEstado']));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($item['regFechaUltimaModificacion']));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($item['regUsuarioUltimaModificacion']));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($item['regFormularioUltimaModificacion']));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($item['regVersionUltimaModificacion']));
   
    // bind the values
	$stmt->bindParam(':generalPOSID', $generalPOSID);
	$validarExistencias = (int)$validarExistencias;
	$stmt->bindValue(':validarExistencias', $validarExistencias, PDO::PARAM_INT);
	$validarNipDespachador = (int)$validarNipDespachador;
	$stmt->bindValue(':validarNipDespachador', $validarNipDespachador, PDO::PARAM_INT);
	$usarMonedero = (int)$usarMonedero;
	$stmt->bindValue(':usarMonedero', $usarMonedero, PDO::PARAM_INT);
	$usarNipMonedero = (int)$usarNipMonedero;
	$stmt->bindValue(':usarNipMonedero', $usarNipMonedero, PDO::PARAM_INT);
	$permiteFacturar = (int)$permiteFacturar;
	$stmt->bindValue(':permiteFacturar', $permiteFacturar, PDO::PARAM_INT);
	$permiteReimprimir = (int)$permiteReimprimir;
	$stmt->bindValue(':permiteReimprimir', $permiteReimprimir, PDO::PARAM_INT);
	$usarDatosAutoconsumo = (int)$usarDatosAutoconsumo;
	$stmt->bindValue(':usarDatosAutoconsumo', $usarDatosAutoconsumo, PDO::PARAM_INT);
	$validarViaje = (int)$validarViaje;
	$stmt->bindValue(':validarViaje', $validarViaje, PDO::PARAM_INT);
	$validarDestino = (int)$validarDestino;
	$stmt->bindValue(':validarDestino', $validarDestino, PDO::PARAM_INT);
	$stmt->bindParam(':eliminarDigitosInicio', $eliminarDigitosInicio);
	$stmt->bindParam(':eliminarDigitosFin', $eliminarDigitosFin);
	$stmt->bindParam(':Impresion', $Impresion);
	$stmt->bindParam(':bombasVisibles', $bombasVisibles);
	$stmt->bindParam(':maximoValesAceptados', $maximoValesAceptados);
	$stmt->bindParam(':maximaCantidadCodeBar', $maximaCantidadCodeBar);
	$stmt->bindParam(':cantidadMenus', $cantidadMenus);
	$forzarImprecionAutoconsumos = (int)$forzarImprecionAutoconsumos;
	$stmt->bindValue(':forzarImprecionAutoconsumos', $forzarImprecionAutoconsumos, PDO::PARAM_INT);
	$stmt->bindParam(':urlWebPos', $urlWebPos);
	$mostrarSiempre = (int)$mostrarSiempre;
	$stmt->bindValue(':mostrarSiempre', $mostrarSiempre, PDO::PARAM_INT);
	$maximizado = (int)$maximizado;
	$stmt->bindValue(':maximizado', $maximizado, PDO::PARAM_INT);
	$stmt->bindParam(':contrasenia', $contrasenia);
	$stmt->bindParam(':ipConexionSocket', $ipConexionSocket);
	$stmt->bindParam(':puertoSocket', $puertoSocket);
	$stmt->bindParam(':tiempoEsperaGasopay', $tiempoEsperaGasopay);
	$stmt->bindParam(':cantidadColumnasBombasMuestra', $cantidadColumnasBombasMuestra);
	$stmt->bindParam(':CantidadMaximaBombasMuestra', $CantidadMaximaBombasMuestra);
	$mostrarTeclado = (int)$mostrarTeclado;
	$stmt->bindValue(':mostrarTeclado', $mostrarTeclado, PDO::PARAM_INT);
	$stmt->bindParam(':tiempoActualizacionAutomatica', $tiempoActualizacionAutomatica);
	$permitirSoloLectura = (int)$permitirSoloLectura;
	$stmt->bindValue(':permitirSoloLectura', $permitirSoloLectura, PDO::PARAM_INT);
	$stmt->bindParam(':rutaWebSocket', $rutaWebSocket);
	$mostrarBotonEnter = (int)$mostrarBotonEnter;
	$stmt->bindValue(':mostrarBotonEnter', $mostrarBotonEnter, PDO::PARAM_INT);
	$stmt->bindParam(':posID', $posID);
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
        echo $this->Mensaje = $e->getMessage().'<br /> <br />Consulta: <br />'.$consulta;
    }

 
    return false;
}


function Cambia($generalPOSID,$validarExistencias,$validarNipDespachador,$usarMonedero,$usarNipMonedero,$permiteFacturar,$permiteReimprimir,$usarDatosAutoconsumo,$validarViaje,$validarDestino,$eliminarDigitosInicio,$eliminarDigitosFin,$Impresion,$bombasVisibles,$maximoValesAceptados,$maximaCantidadCodeBar,$cantidadMenus,$forzarImprecionAutoconsumos,$urlWebPos,$mostrarSiempre,$maximizado,$contrasenia,$ipConexionSocket,$puertoSocket,$tiempoEsperaGasopay,$cantidadColumnasBombasMuestra,$CantidadMaximaBombasMuestra,$mostrarTeclado,$tiempoActualizacionAutomatica,$permitirSoloLectura,$rutaWebSocket,$mostrarBotonEnter,$posID,$establecimientoID,$versionRegistro,$regEstado,$regFechaUltimaModificacion,$regUsuarioUltimaModificacion,$regFormularioUltimaModificacion,$regVersionUltimaModificacion){
    $query = "UPDATE " . $this->NombreTabla . " SET validarExistencias=:validarExistencias,validarNipDespachador=:validarNipDespachador,usarMonedero=:usarMonedero,usarNipMonedero=:usarNipMonedero,permiteFacturar=:permiteFacturar,permiteReimprimir=:permiteReimprimir,usarDatosAutoconsumo=:usarDatosAutoconsumo,validarViaje=:validarViaje,validarDestino=:validarDestino,eliminarDigitosInicio=:eliminarDigitosInicio,eliminarDigitosFin=:eliminarDigitosFin,Impresion=:Impresion,bombasVisibles=:bombasVisibles,maximoValesAceptados=:maximoValesAceptados,maximaCantidadCodeBar=:maximaCantidadCodeBar,cantidadMenus=:cantidadMenus,forzarImprecionAutoconsumos=:forzarImprecionAutoconsumos,urlWebPos=:urlWebPos,mostrarSiempre=:mostrarSiempre,maximizado=:maximizado,contrasenia=:contrasenia,ipConexionSocket=:ipConexionSocket,puertoSocket=:puertoSocket,tiempoEsperaGasopay=:tiempoEsperaGasopay,cantidadColumnasBombasMuestra=:cantidadColumnasBombasMuestra,CantidadMaximaBombasMuestra=:CantidadMaximaBombasMuestra,mostrarTeclado=:mostrarTeclado,tiempoActualizacionAutomatica=:tiempoActualizacionAutomatica,permitirSoloLectura=:permitirSoloLectura,rutaWebSocket=:rutaWebSocket,mostrarBotonEnter=:mostrarBotonEnter,posID=:posID,establecimientoID=:establecimientoID,versionRegistro=:versionRegistro,regEstado=:regEstado,regFechaUltimaModificacion=:regFechaUltimaModificacion,regUsuarioUltimaModificacion=:regUsuarioUltimaModificacion,regFormularioUltimaModificacion=:regFormularioUltimaModificacion,regVersionUltimaModificacion=:regVersionUltimaModificacion 
		WHERE generalPOSID=:generalPOSID ";

    // prepare the query
    $stmt = $this->Conexion->prepare($query);
 
     // sanitize
	$generalPOSID=htmlspecialchars(strip_tags($generalPOSID));
	$validarExistencias=htmlspecialchars(strip_tags($validarExistencias));
	$validarNipDespachador=htmlspecialchars(strip_tags($validarNipDespachador));
	$usarMonedero=htmlspecialchars(strip_tags($usarMonedero));
	$usarNipMonedero=htmlspecialchars(strip_tags($usarNipMonedero));
	$permiteFacturar=htmlspecialchars(strip_tags($permiteFacturar));
	$permiteReimprimir=htmlspecialchars(strip_tags($permiteReimprimir));
	$usarDatosAutoconsumo=htmlspecialchars(strip_tags($usarDatosAutoconsumo));
	$validarViaje=htmlspecialchars(strip_tags($validarViaje));
	$validarDestino=htmlspecialchars(strip_tags($validarDestino));
	$eliminarDigitosInicio=htmlspecialchars(strip_tags($eliminarDigitosInicio));
	$eliminarDigitosFin=htmlspecialchars(strip_tags($eliminarDigitosFin));
	$Impresion=htmlspecialchars(strip_tags($Impresion));
	$bombasVisibles=htmlspecialchars(strip_tags($bombasVisibles));
	$maximoValesAceptados=htmlspecialchars(strip_tags($maximoValesAceptados));
	$maximaCantidadCodeBar=htmlspecialchars(strip_tags($maximaCantidadCodeBar));
	$cantidadMenus=htmlspecialchars(strip_tags($cantidadMenus));
	$forzarImprecionAutoconsumos=htmlspecialchars(strip_tags($forzarImprecionAutoconsumos));
	$urlWebPos=htmlspecialchars(strip_tags($urlWebPos));
	$mostrarSiempre=htmlspecialchars(strip_tags($mostrarSiempre));
	$maximizado=htmlspecialchars(strip_tags($maximizado));
	$contrasenia=htmlspecialchars(strip_tags($contrasenia));
	$ipConexionSocket=htmlspecialchars(strip_tags($ipConexionSocket));
	$puertoSocket=htmlspecialchars(strip_tags($puertoSocket));
	$tiempoEsperaGasopay=htmlspecialchars(strip_tags($tiempoEsperaGasopay));
	$cantidadColumnasBombasMuestra=htmlspecialchars(strip_tags($cantidadColumnasBombasMuestra));
	$CantidadMaximaBombasMuestra=htmlspecialchars(strip_tags($CantidadMaximaBombasMuestra));
	$mostrarTeclado=htmlspecialchars(strip_tags($mostrarTeclado));
	$tiempoActualizacionAutomatica=htmlspecialchars(strip_tags($tiempoActualizacionAutomatica));
	$permitirSoloLectura=htmlspecialchars(strip_tags($permitirSoloLectura));
	$rutaWebSocket=htmlspecialchars(strip_tags($rutaWebSocket));
	$mostrarBotonEnter=htmlspecialchars(strip_tags($mostrarBotonEnter));
	$posID=htmlspecialchars(strip_tags($posID));
	$establecimientoID=htmlspecialchars(strip_tags($establecimientoID));
	$versionRegistro=htmlspecialchars(strip_tags($versionRegistro));
	$regEstado=htmlspecialchars(strip_tags($regEstado));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($regFechaUltimaModificacion));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($regUsuarioUltimaModificacion));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($regFormularioUltimaModificacion));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($regVersionUltimaModificacion));
   
    // bind the values
	$stmt->bindParam(':generalPOSID', $generalPOSID);
	$validarExistencias = (int)$validarExistencias;
	$stmt->bindValue(':validarExistencias', $validarExistencias, PDO::PARAM_INT);
	$validarNipDespachador = (int)$validarNipDespachador;
	$stmt->bindValue(':validarNipDespachador', $validarNipDespachador, PDO::PARAM_INT);
	$usarMonedero = (int)$usarMonedero;
	$stmt->bindValue(':usarMonedero', $usarMonedero, PDO::PARAM_INT);
	$usarNipMonedero = (int)$usarNipMonedero;
	$stmt->bindValue(':usarNipMonedero', $usarNipMonedero, PDO::PARAM_INT);
	$permiteFacturar = (int)$permiteFacturar;
	$stmt->bindValue(':permiteFacturar', $permiteFacturar, PDO::PARAM_INT);
	$permiteReimprimir = (int)$permiteReimprimir;
	$stmt->bindValue(':permiteReimprimir', $permiteReimprimir, PDO::PARAM_INT);
	$usarDatosAutoconsumo = (int)$usarDatosAutoconsumo;
	$stmt->bindValue(':usarDatosAutoconsumo', $usarDatosAutoconsumo, PDO::PARAM_INT);
	$validarViaje = (int)$validarViaje;
	$stmt->bindValue(':validarViaje', $validarViaje, PDO::PARAM_INT);
	$validarDestino = (int)$validarDestino;
	$stmt->bindValue(':validarDestino', $validarDestino, PDO::PARAM_INT);
	$stmt->bindParam(':eliminarDigitosInicio', $eliminarDigitosInicio);
	$stmt->bindParam(':eliminarDigitosFin', $eliminarDigitosFin);
	$stmt->bindParam(':Impresion', $Impresion);
	$stmt->bindParam(':bombasVisibles', $bombasVisibles);
	$stmt->bindParam(':maximoValesAceptados', $maximoValesAceptados);
	$stmt->bindParam(':maximaCantidadCodeBar', $maximaCantidadCodeBar);
	$stmt->bindParam(':cantidadMenus', $cantidadMenus);
	$forzarImprecionAutoconsumos = (int)$forzarImprecionAutoconsumos;
	$stmt->bindValue(':forzarImprecionAutoconsumos', $forzarImprecionAutoconsumos, PDO::PARAM_INT);
	$stmt->bindParam(':urlWebPos', $urlWebPos);
	$mostrarSiempre = (int)$mostrarSiempre;
	$stmt->bindValue(':mostrarSiempre', $mostrarSiempre, PDO::PARAM_INT);
	$maximizado = (int)$maximizado;
	$stmt->bindValue(':maximizado', $maximizado, PDO::PARAM_INT);
	$stmt->bindParam(':contrasenia', $contrasenia);
	$stmt->bindParam(':ipConexionSocket', $ipConexionSocket);
	$stmt->bindParam(':puertoSocket', $puertoSocket);
	$stmt->bindParam(':tiempoEsperaGasopay', $tiempoEsperaGasopay);
	$stmt->bindParam(':cantidadColumnasBombasMuestra', $cantidadColumnasBombasMuestra);
	$stmt->bindParam(':CantidadMaximaBombasMuestra', $CantidadMaximaBombasMuestra);
	$mostrarTeclado = (int)$mostrarTeclado;
	$stmt->bindValue(':mostrarTeclado', $mostrarTeclado, PDO::PARAM_INT);
	$stmt->bindParam(':tiempoActualizacionAutomatica', $tiempoActualizacionAutomatica);
	$permitirSoloLectura = (int)$permitirSoloLectura;
	$stmt->bindValue(':permitirSoloLectura', $permitirSoloLectura, PDO::PARAM_INT);
	$stmt->bindParam(':rutaWebSocket', $rutaWebSocket);
	$mostrarBotonEnter = (int)$mostrarBotonEnter;
	$stmt->bindValue(':mostrarBotonEnter', $mostrarBotonEnter, PDO::PARAM_INT);
	$stmt->bindParam(':posID', $posID);
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
        echo $this->Mensaje = $e->getMessage();//.'<br /> <br />Consulta: <br />'.$consulta;
    }
 
    return false;
}
	
		
}
	?>