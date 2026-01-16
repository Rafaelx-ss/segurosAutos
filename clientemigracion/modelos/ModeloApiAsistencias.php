<?php 
class Asistencias{ 
 
private $Conexion; 
private $Database; 
private $NombreTabla = 'Asistencias'; 
 
// object properties
public $Campos;
public $Dataset;
 public $Mensaje;
// constructor
    public function __construct($dbf){
        $this->Database = $dbf;
        $this->Conexion = $dbf->conn;
    }
	
	function ObtenerDatos($id=0, $tipoconsulta=""){
    	// query to check if email exists
    	$query = "select * from " . $this->NombreTabla .  " where 1 " . ($id > 0 ? " and asistenciaID = ?" : "");
 
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
 
			if($tipoconsulta=="" or $tipoconsulta=="CONSULTA_LOCAL"){
				return true;
			}
			else{
				return $this->Dataset;
			}
    	}
 
    	// return false if email does not exist in the database
    	return false;
	}
 

function Inserta($registros){

    $consulta="";
	
	$consulta = 'INSERT INTO ' . $this->NombreTabla . 
			" (asistenciaID,asistenciaFechaInicio,asistenciaFechaFin,asistenciaEstado,bombaNumero,jornadaID,empleadoID,estadoReplica,establecimientoID,versionRegistro,regEstado,regFechaUltimaModificacion,regUsuarioUltimaModificacion,regFormularioUltimaModificacion,regVersionUltimaModificacion) VALUES ";
			
		$coma = false;
		$comaText = "";
		foreach($registros as $item)
		 {
			//echo $item['textoID']."lolol";
			 if($this->ObtenerDatos($item['asistenciaID'])){
				$this->Cambia($item['asistenciaID'],($item['asistenciaFechaInicio']),($item['asistenciaFechaFin']),($item['asistenciaEstado']),$item['bombaNumero'],$item['jornadaID'],$item['empleadoID'],$item['estadoReplica'],($item['establecimientoID']),$item['versionRegistro'],$item['regEstado'],($item['regFechaUltimaModificacion']),$item['regUsuarioUltimaModificacion'],$item['regFormularioUltimaModificacion'],$item['regVersionUltimaModificacion']);
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
				
				$consulta= $consulta . $comaText . "(" . $item["asistenciaID"] . ",'" . ($item["asistenciaFechaInicio"]) . "','" . ($item["asistenciaFechaFin"]) . "','" . ($item["asistenciaEstado"]) . "'," . $item["bombaNumero"] . "," . $item["jornadaID"] . "," . $item["empleadoID"] . "," . $item["estadoReplica"] . ",'" . ($item["establecimientoID"]) . "'," . $item["versionRegistro"] . "," . $item["regEstado"] . ",'" . ($item["regFechaUltimaModificacion"]) . "'," . $item["regUsuarioUltimaModificacion"] . "," . $item["regFormularioUltimaModificacion"] . "," . $item["regVersionUltimaModificacion"] . ")";
			}
			 
		   
		 }
	if(!$coma){
		$consulta="select 1";
	}
	
	$this->query=$consulta;
	
    // prepare the query
    $stmt = $this->Conexion->prepare($this->query);
	
    // sanitize
	$asistenciaID=htmlspecialchars(strip_tags($item['asistenciaID']));
	$asistenciaFechaInicio=htmlspecialchars(strip_tags($item['asistenciaFechaInicio']));
	$asistenciaFechaFin=htmlspecialchars(strip_tags($item['asistenciaFechaFin']));
	$asistenciaEstado=htmlspecialchars(strip_tags($item['asistenciaEstado']));
	$bombaNumero=htmlspecialchars(strip_tags($item['bombaNumero']));
	$jornadaID=htmlspecialchars(strip_tags($item['jornadaID']));
	$empleadoID=htmlspecialchars(strip_tags($item['empleadoID']));
	$estadoReplica=htmlspecialchars(strip_tags($item['estadoReplica']));
	$establecimientoID=htmlspecialchars(strip_tags($item['establecimientoID']));
	$versionRegistro=htmlspecialchars(strip_tags($item['versionRegistro']));
	$regEstado=htmlspecialchars(strip_tags($item['regEstado']));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($item['regFechaUltimaModificacion']));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($item['regUsuarioUltimaModificacion']));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($item['regFormularioUltimaModificacion']));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($item['regVersionUltimaModificacion']));
   
    // bind the values
	$stmt->bindParam(':asistenciaID', $asistenciaID);
	$stmt->bindParam(':asistenciaFechaInicio', $asistenciaFechaInicio);
	$stmt->bindParam(':asistenciaFechaFin', $asistenciaFechaFin);
	$stmt->bindParam(':asistenciaEstado', $asistenciaEstado);
	$stmt->bindParam(':bombaNumero', $bombaNumero);
	$stmt->bindParam(':jornadaID', $jornadaID);
	$stmt->bindParam(':empleadoID', $empleadoID);
	$stmt->bindParam(':estadoReplica', $estadoReplica);
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


function InsertaRegreso($item){
$Registo= "0";
try{

    $consulta="";
	
	$consulta = 'INSERT INTO ' . $this->NombreTabla . 
			" (asistenciaID,asistenciaFechaInicio,asistenciaFechaFin,asistenciaEstado,bombaNumero,jornadaID,empleadoID,estadoReplica,establecimientoID,versionRegistro,regEstado,regFechaUltimaModificacion,regUsuarioUltimaModificacion,regFormularioUltimaModificacion,regVersionUltimaModificacion) VALUES ";
			
		$coma = false;
		$comaText = "";
		$Registo=$item['asistenciaID'];
		if($this->ObtenerDatos($item['asistenciaID'])){
			$this->Cambia($item['asistenciaID'],($item['asistenciaFechaInicio']),($item['asistenciaFechaFin']),($item['asistenciaEstado']),$item['bombaNumero'],$item['jornadaID'],$item['empleadoID'],$item['estadoReplica'],($item['establecimientoID']),$item['versionRegistro'],$item['regEstado'],($item['regFechaUltimaModificacion']),$item['regUsuarioUltimaModificacion'],$item['regFormularioUltimaModificacion'],$item['regVersionUltimaModificacion']);
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
			
			$consulta= $consulta . $comaText . "(" . $item["asistenciaID"] . ",'" . ($item["asistenciaFechaInicio"]) . "','" . ($item["asistenciaFechaFin"]) . "','" . ($item["asistenciaEstado"]) . "'," . $item["bombaNumero"] . "," . $item["jornadaID"] . "," . $item["empleadoID"] . "," . $item["estadoReplica"] . ",'" . ($item["establecimientoID"]) . "'," . $item["versionRegistro"] . "," . $item["regEstado"] . ",'" . ($item["regFechaUltimaModificacion"]) . "'," . $item["regUsuarioUltimaModificacion"] . "," . $item["regFormularioUltimaModificacion"] . "," . $item["regVersionUltimaModificacion"] . ")";
		}
	if(!$coma){
		$consulta="select 1";
	}
	
	$this->query=$consulta;
	
    // prepare the query
    $stmt = $this->Conexion->prepare($this->query);
	
    // sanitize
	$asistenciaID=htmlspecialchars(strip_tags($item['asistenciaID']));
	$asistenciaFechaInicio=htmlspecialchars(strip_tags($item['asistenciaFechaInicio']));
	$asistenciaFechaFin=htmlspecialchars(strip_tags($item['asistenciaFechaFin']));
	$asistenciaEstado=htmlspecialchars(strip_tags($item['asistenciaEstado']));
	$bombaNumero=htmlspecialchars(strip_tags($item['bombaNumero']));
	$jornadaID=htmlspecialchars(strip_tags($item['jornadaID']));
	$empleadoID=htmlspecialchars(strip_tags($item['empleadoID']));
	$estadoReplica=htmlspecialchars(strip_tags($item['estadoReplica']));
	$establecimientoID=htmlspecialchars(strip_tags($item['establecimientoID']));
	$versionRegistro=htmlspecialchars(strip_tags($item['versionRegistro']));
	$regEstado=htmlspecialchars(strip_tags($item['regEstado']));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($item['regFechaUltimaModificacion']));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($item['regUsuarioUltimaModificacion']));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($item['regFormularioUltimaModificacion']));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($item['regVersionUltimaModificacion']));
   
    // bind the values
	$stmt->bindParam(':asistenciaID', $asistenciaID);
	$stmt->bindParam(':asistenciaFechaInicio', $asistenciaFechaInicio);
	$stmt->bindParam(':asistenciaFechaFin', $asistenciaFechaFin);
	$stmt->bindParam(':asistenciaEstado', $asistenciaEstado);
	$stmt->bindParam(':bombaNumero', $bombaNumero);
	$stmt->bindParam(':jornadaID', $jornadaID);
	$stmt->bindParam(':empleadoID', $empleadoID);
	$stmt->bindParam(':estadoReplica', $estadoReplica);
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
        if($consulta<>"select 1"){
			// execute the query, also check if query was successful
			if($stmt->execute()){
				//$this->LogMigracion("Api".$this->NombreTabla, "INSERTAR", "Registo ".$Registo.": ".$consulta, "INSERCION EXITOSA");
				return true;

			}
		}
		else{
			return true;
		}
    }   
    catch (Exception $e){
		$this->LogMigracion("Api".$this->NombreTabla, "INSERTAR Ex1", "Registo ".$Registo.": ".$consulta, $e->getMessage());
		echo $this->Mensaje = $e->getMessage().$consulta;
	}
}
catch (Exception $e){
		$this->LogMigracion("Api".$this->NombreTabla, "INSERTAR Ex2", "Registo ".$Registo.": ".$consulta, $e->getMessage());
	echo $this->Mensaje = $e->getMessage().$consulta;
}
    return false;
}

function Cambia($asistenciaID,$asistenciaFechaInicio,$asistenciaFechaFin,$asistenciaEstado,$bombaNumero,$jornadaID,$empleadoID,$estadoReplica,$establecimientoID,$versionRegistro,$regEstado,$regFechaUltimaModificacion,$regUsuarioUltimaModificacion,$regFormularioUltimaModificacion,$regVersionUltimaModificacion){
    $query = "UPDATE " . $this->NombreTabla . " SET asistenciaFechaInicio=:asistenciaFechaInicio,asistenciaFechaFin=:asistenciaFechaFin,asistenciaEstado=:asistenciaEstado,bombaNumero=:bombaNumero,jornadaID=:jornadaID,empleadoID=:empleadoID,estadoReplica=:estadoReplica,establecimientoID=:establecimientoID,versionRegistro=:versionRegistro,regEstado=:regEstado,regFechaUltimaModificacion=:regFechaUltimaModificacion,regUsuarioUltimaModificacion=:regUsuarioUltimaModificacion,regFormularioUltimaModificacion=:regFormularioUltimaModificacion,regVersionUltimaModificacion=:regVersionUltimaModificacion 
		WHERE asistenciaID=:asistenciaID ";

    // prepare the query
    $stmt = $this->Conexion->prepare($query);
 
     // sanitize
	$asistenciaID=htmlspecialchars(strip_tags($asistenciaID));
	$asistenciaFechaInicio=htmlspecialchars(strip_tags($asistenciaFechaInicio));
	$asistenciaFechaFin=htmlspecialchars(strip_tags($asistenciaFechaFin));
	$asistenciaEstado=htmlspecialchars(strip_tags($asistenciaEstado));
	$bombaNumero=htmlspecialchars(strip_tags($bombaNumero));
	$jornadaID=htmlspecialchars(strip_tags($jornadaID));
	$empleadoID=htmlspecialchars(strip_tags($empleadoID));
	$estadoReplica=htmlspecialchars(strip_tags($estadoReplica));
	$establecimientoID=htmlspecialchars(strip_tags($establecimientoID));
	$versionRegistro=htmlspecialchars(strip_tags($versionRegistro));
	$regEstado=htmlspecialchars(strip_tags($regEstado));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($regFechaUltimaModificacion));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($regUsuarioUltimaModificacion));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($regFormularioUltimaModificacion));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($regVersionUltimaModificacion));
   
    // bind the values
	$stmt->bindParam(':asistenciaID', $asistenciaID);
	$stmt->bindParam(':asistenciaFechaInicio', $asistenciaFechaInicio);
	$stmt->bindParam(':asistenciaFechaFin', $asistenciaFechaFin);
	$stmt->bindParam(':asistenciaEstado', $asistenciaEstado);
	$stmt->bindParam(':bombaNumero', $bombaNumero);
	$stmt->bindParam(':jornadaID', $jornadaID);
	$stmt->bindParam(':empleadoID', $empleadoID);
	$stmt->bindParam(':estadoReplica', $estadoReplica);
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
	
	function LogMigracion($Api, $metodo, $consulta, $mensaje){
		try{
			$query2 ="INSERT INTO LogMigracion (logMigracionId, api, metodo, consulta, mensaje, regEstado, regFechaUltimaModificacion, regUsuarioUltimaModificacion, regFormularioUltimaModificacion, regVersionUltimaModificacion) 
			VALUES (NULL, '".$Api."', '".$metodo."', '".str_replace("'","",$consulta)."', '".str_replace("'","",$mensaje)."', b'1', now(), '1', '1', '1');";

			// prepare the query
			$stmt = $this->Conexion->prepare($query2);
			if($stmt->execute()){
			}
		}
		catch (Exception $e){
			$this->Mensaje .= $e->getMessage();
		}
	}
}
	?>