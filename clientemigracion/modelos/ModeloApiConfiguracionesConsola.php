<?php 
class ConfiguracionesConsola{ 
 
private $Conexion; 
private $Database; 
private $NombreTabla = 'ConfiguracionesConsola'; 
 
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
    		$query = "select * from " . $this->NombreTabla .  " where  configuracionConsolaID=:configuracionConsolaID and establecimientoID=:establecimientoID ";
 
    	// prepare the query
    	$stmt = $this->Conexion->prepare( $query );
 		$this->Mensaje2=$query;
    	 // sanitize
		
		$ID=htmlspecialchars(strip_tags($ID));
		$establecimientoID=htmlspecialchars(strip_tags($establecimientoID));
 
    	// bind given id value

    	 // bind the values
		$stmt->bindParam(':configuracionConsolaID', $ID);
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
			" (configuracionConsolaID,rutaRecursosBombas,rutaRecursosTanques,preAutorizacion,actualizaPreset,cancelaPresetDespacho,maximaDiferencia,retrasoInicio,intentosAutoriza,cantidadDespachosPantalla,forzarPrecio,
			establecimientoID,versionRegistro,regEstado,regFechaUltimaModificacion,regUsuarioUltimaModificacion,regFormularioUltimaModificacion,regVersionUltimaModificacion) VALUES ";
			
		$coma = false;
		$comaText = "";
		foreach($registros as $item)
		 {
			//echo $item['textoID']."lolol";
			 if($this->ObtenerDatos($item['configuracionConsolaID'], $item['establecimientoID'])){
				$this->Cambia($item['configuracionConsolaID'],$item['rutaRecursosBombas'],$item['rutaRecursosTanques'],$item['preAutorizacion'],$item['actualizaPreset'],$item['cancelaPresetDespacho'],$item['maximaDiferencia'],$item['retrasoInicio'],$item['intentosAutoriza'],$item['cantidadDespachosPantalla'],$item['forzarPrecio'],
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
				
				$consulta= $consulta . $comaText . "(" . $item['configuracionConsolaID'] . ",'" . $item['rutaRecursosBombas'] . "','" . $item['rutaRecursosTanques'] . "'," . $item['preAutorizacion'] . "," . $item['actualizaPreset'] . "," . $item['cancelaPresetDespacho'] . "," . $item['maximaDiferencia'] . "," . $item['retrasoInicio'] . "," . $item['intentosAutoriza'] . "," . $item['cantidadDespachosPantalla'] . "," . $item['forzarPrecio'] . "," .
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
	$configuracionConsolaID=htmlspecialchars(strip_tags($item['configuracionConsolaID']));
	$rutaRecursosBombas=htmlspecialchars(strip_tags($item['rutaRecursosBombas']));
	$rutaRecursosTanques=htmlspecialchars(strip_tags($item['rutaRecursosTanques']));
	$preAutorizacion=htmlspecialchars(strip_tags($item['preAutorizacion']));
	$actualizaPreset=htmlspecialchars(strip_tags($item['actualizaPreset']));
	$cancelaPresetDespacho=htmlspecialchars(strip_tags($item['cancelaPresetDespacho']));
	$maximaDiferencia=htmlspecialchars(strip_tags($item['maximaDiferencia']));
	$retrasoInicio=htmlspecialchars(strip_tags($item['retrasoInicio']));
	$intentosAutoriza=htmlspecialchars(strip_tags($item['intentosAutoriza']));
	$cantidadDespachosPantalla=htmlspecialchars(strip_tags($item['cantidadDespachosPantalla']));
	$forzarPrecio=htmlspecialchars(strip_tags($item['forzarPrecio']));
	$establecimientoID=htmlspecialchars(strip_tags($item['establecimientoID']));
	$versionRegistro=htmlspecialchars(strip_tags($item['versionRegistro']));
	$regEstado=htmlspecialchars(strip_tags($item['regEstado']));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($item['regFechaUltimaModificacion']));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($item['regUsuarioUltimaModificacion']));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($item['regFormularioUltimaModificacion']));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($item['regVersionUltimaModificacion']));
   
    // bind the values
	$stmt->bindParam(':configuracionConsolaID', $configuracionConsolaID);
	$stmt->bindParam(':rutaRecursosBombas', $rutaRecursosBombas);
	$stmt->bindParam(':rutaRecursosTanques', $rutaRecursosTanques);
	$stmt->bindParam(':preAutorizacion', $preAutorizacion);
	$stmt->bindParam(':actualizaPreset', $actualizaPreset);
	$stmt->bindParam(':cancelaPresetDespacho', $cancelaPresetDespacho);
	$stmt->bindParam(':maximaDiferencia', $maximaDiferencia);
	$stmt->bindParam(':retrasoInicio', $retrasoInicio);
	$stmt->bindParam(':intentosAutoriza', $intentosAutoriza);
	$stmt->bindParam(':cantidadDespachosPantalla', $cantidadDespachosPantalla);
	$stmt->bindParam(':forzarPrecio', $forzarPrecio);
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

function Cambia($configuracionConsolaID,$rutaRecursosBombas,$rutaRecursosTanques,$preAutorizacion,$actualizaPreset,$cancelaPresetDespacho,$maximaDiferencia,$retrasoInicio,$intentosAutoriza,$cantidadDespachosPantalla,$forzarPrecio,
			$establecimientoID,$versionRegistro,$regEstado,$regFechaUltimaModificacion,$regUsuarioUltimaModificacion,$regFormularioUltimaModificacion,$regVersionUltimaModificacion){
	try{
		$query = "UPDATE " . $this->NombreTabla . " SET rutaRecursosBombas=:rutaRecursosBombas, rutaRecursosTanques=:rutaRecursosTanques, preAutorizacion=:preAutorizacion, actualizaPreset=:actualizaPreset, cancelaPresetDespacho=:cancelaPresetDespacho, maximaDiferencia=:maximaDiferencia, retrasoInicio=:retrasoInicio,
		intentosAutoriza=:intentosAutoriza, cantidadDespachosPantalla=:cantidadDespachosPantalla, forzarPrecio=:forzarPrecio, 
		versionRegistro=:versionRegistro, regEstado=:regEstado, regFechaUltimaModificacion=:regFechaUltimaModificacion, regUsuarioUltimaModificacion=:regUsuarioUltimaModificacion, regFormularioUltimaModificacion=:regFormularioUltimaModificacion, regVersionUltimaModificacion=:regVersionUltimaModificacion 
		WHERE configuracionConsolaID=:configuracionConsolaID and establecimientoID=:establecimientoID ";

    // prepare the query
    $stmt = $this->Conexion->prepare($query);
 	$this->Mensaje2=$query;
     // sanitize
	$configuracionConsolaID=htmlspecialchars(strip_tags($configuracionConsolaID));
	$rutaRecursosBombas=htmlspecialchars(strip_tags($rutaRecursosBombas));
	$rutaRecursosTanques=htmlspecialchars(strip_tags($rutaRecursosTanques));
	$preAutorizacion=htmlspecialchars(strip_tags($preAutorizacion));
	$actualizaPreset=htmlspecialchars(strip_tags($actualizaPreset));
	$cancelaPresetDespacho=htmlspecialchars(strip_tags($cancelaPresetDespacho));
	$maximaDiferencia=htmlspecialchars(strip_tags($maximaDiferencia));
	$retrasoInicio=htmlspecialchars(strip_tags($retrasoInicio));
	$intentosAutoriza=htmlspecialchars(strip_tags($intentosAutoriza));
	$cantidadDespachosPantalla=htmlspecialchars(strip_tags($cantidadDespachosPantalla));
	$forzarPrecio=htmlspecialchars(strip_tags($forzarPrecio));
	$establecimientoID=htmlspecialchars(strip_tags($establecimientoID));
	$versionRegistro=htmlspecialchars(strip_tags($versionRegistro));
	$regEstado=htmlspecialchars(strip_tags($regEstado));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($regFechaUltimaModificacion));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($regUsuarioUltimaModificacion));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($regFormularioUltimaModificacion));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($regVersionUltimaModificacion));
   
    // bind the values
	$stmt->bindParam(':configuracionConsolaID', $configuracionConsolaID);
	$stmt->bindParam(':rutaRecursosBombas', $rutaRecursosBombas);
	$stmt->bindParam(':rutaRecursosTanques', $rutaRecursosTanques);
	$stmt->bindParam(':preAutorizacion', $preAutorizacion, PDO::PARAM_INT);
	$stmt->bindParam(':actualizaPreset', $actualizaPreset, PDO::PARAM_INT);
	$stmt->bindParam(':cancelaPresetDespacho', $cancelaPresetDespacho, PDO::PARAM_INT);
	$stmt->bindParam(':maximaDiferencia', $maximaDiferencia);
	$stmt->bindParam(':retrasoInicio', $retrasoInicio);
	$stmt->bindParam(':intentosAutoriza', $intentosAutoriza);
	$stmt->bindParam(':cantidadDespachosPantalla', $cantidadDespachosPantalla);
	$stmt->bindParam(':forzarPrecio', $forzarPrecio, PDO::PARAM_INT);
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