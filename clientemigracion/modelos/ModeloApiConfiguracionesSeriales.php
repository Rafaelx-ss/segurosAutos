<?php 
class ConfiguracionesSeriales{ 
 
private $Conexion; 
private $Database; 
private $NombreTabla = 'ConfiguracionesSeriales'; 
 
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
    	$query = "select * from " . $this->NombreTabla .  " where 1 " . ($id > 0 ? " and serialID = ?" : "");
 
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
			" (serialID,puertoSerial,velocidadBaudios,paridad,bitParada,longitudBits,usuario,passw,intentosTimeOut,tiempoTimeOut,establecimientoID,interfazID,versionRegistro,regEstado,regFechaUltimaModificacion,regUsuarioUltimaModificacion,regFormularioUltimaModificacion,regVersionUltimaModificacion) VALUES ";
			
		$coma = false;
		$comaText = "";
		foreach($registros as $item)
		 {
			//echo $item['textoID']."lolol";
			 if($this->ObtenerDatos($item['serialID'])){
				$this->Cambia($item['serialID'],($item['puertoSerial']),($item['velocidadBaudios']),($item['paridad']),($item['bitParada']),($item['longitudBits']),($item['usuario']),($item['passw']),$item['intentosTimeOut'],$item['tiempoTimeOut'],($item['establecimientoID']),$item['interfazID'],$item['versionRegistro'],($item['regEstado']),($item['regFechaUltimaModificacion']),$item['regUsuarioUltimaModificacion'],$item['regFormularioUltimaModificacion'],$item['regVersionUltimaModificacion']);
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
				
				$consulta= $consulta . $comaText . "(" . $item["serialID"] . ",'" . ($item["puertoSerial"]) . "','" . ($item["velocidadBaudios"]) . "','" . ($item["paridad"]) . "','" . ($item["bitParada"]) . "','" . ($item["longitudBits"]) . "','" . ($item["usuario"]) . "','" . ($item["passw"]) . "'," . $item["intentosTimeOut"] . "," . $item["tiempoTimeOut"] . ",'" . ($item["establecimientoID"]) . "'," . $item["interfazID"] . "," . $item["versionRegistro"] . ",'" . ($item["regEstado"]) . "','" . ($item["regFechaUltimaModificacion"]) . "'," . $item["regUsuarioUltimaModificacion"] . "," . $item["regFormularioUltimaModificacion"] . "," . $item["regVersionUltimaModificacion"] . ")";
			}
			 
		   
		 }
	if(!$coma){
		$consulta="select 1";
	}
	
	$this->query=$consulta;
	
    // prepare the query
    $stmt = $this->Conexion->prepare($this->query);
	
    // sanitize
	$serialID=htmlspecialchars(strip_tags($item['serialID']));
	$puertoSerial=htmlspecialchars(strip_tags($item['puertoSerial']));
	$velocidadBaudios=htmlspecialchars(strip_tags($item['velocidadBaudios']));
	$paridad=htmlspecialchars(strip_tags($item['paridad']));
	$bitParada=htmlspecialchars(strip_tags($item['bitParada']));
	$longitudBits=htmlspecialchars(strip_tags($item['longitudBits']));
	$usuario=htmlspecialchars(strip_tags($item['usuario']));
	$passw=htmlspecialchars(strip_tags($item['passw']));
	$intentosTimeOut=htmlspecialchars(strip_tags($item['intentosTimeOut']));
	$tiempoTimeOut=htmlspecialchars(strip_tags($item['tiempoTimeOut']));
	$establecimientoID=htmlspecialchars(strip_tags($item['establecimientoID']));
	$interfazID=htmlspecialchars(strip_tags($item['interfazID']));
	$versionRegistro=htmlspecialchars(strip_tags($item['versionRegistro']));
	$regEstado=htmlspecialchars(strip_tags($item['regEstado']));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($item['regFechaUltimaModificacion']));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($item['regUsuarioUltimaModificacion']));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($item['regFormularioUltimaModificacion']));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($item['regVersionUltimaModificacion']));
   
    // bind the values
	$stmt->bindParam(':serialID', $serialID);
	$stmt->bindParam(':puertoSerial', $puertoSerial);
	$stmt->bindParam(':velocidadBaudios', $velocidadBaudios);
	$stmt->bindParam(':paridad', $paridad);
	$stmt->bindParam(':bitParada', $bitParada);
	$stmt->bindParam(':longitudBits', $longitudBits);
	$stmt->bindParam(':usuario', $usuario);
	$stmt->bindParam(':passw', $passw);
	$stmt->bindParam(':intentosTimeOut', $intentosTimeOut);
	$stmt->bindParam(':tiempoTimeOut', $tiempoTimeOut);
	$stmt->bindParam(':establecimientoID', $establecimientoID);
	$stmt->bindParam(':interfazID', $interfazID);
	$stmt->bindParam(':versionRegistro', $versionRegistro);
	$stmt->bindParam(':regEstado', $regEstado);
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


function Cambia($serialID,$puertoSerial,$velocidadBaudios,$paridad,$bitParada,$longitudBits,$usuario,$passw,$intentosTimeOut,$tiempoTimeOut,$establecimientoID,$interfazID,$versionRegistro,$regEstado,$regFechaUltimaModificacion,$regUsuarioUltimaModificacion,$regFormularioUltimaModificacion,$regVersionUltimaModificacion){
    $query = "UPDATE " . $this->NombreTabla . " SET puertoSerial=:puertoSerial,velocidadBaudios=:velocidadBaudios,paridad=:paridad,bitParada=:bitParada,longitudBits=:longitudBits,usuario=:usuario,passw=:passw,intentosTimeOut=:intentosTimeOut,tiempoTimeOut=:tiempoTimeOut,establecimientoID=:establecimientoID,interfazID=:interfazID,versionRegistro=:versionRegistro,regEstado=:regEstado,regFechaUltimaModificacion=:regFechaUltimaModificacion,regUsuarioUltimaModificacion=:regUsuarioUltimaModificacion,regFormularioUltimaModificacion=:regFormularioUltimaModificacion,regVersionUltimaModificacion=:regVersionUltimaModificacion 
		WHERE serialID=:serialID ";

    // prepare the query
    $stmt = $this->Conexion->prepare($query);
 
     // sanitize
	$serialID=htmlspecialchars(strip_tags($serialID));
	$puertoSerial=htmlspecialchars(strip_tags($puertoSerial));
	$velocidadBaudios=htmlspecialchars(strip_tags($velocidadBaudios));
	$paridad=htmlspecialchars(strip_tags($paridad));
	$bitParada=htmlspecialchars(strip_tags($bitParada));
	$longitudBits=htmlspecialchars(strip_tags($longitudBits));
	$usuario=htmlspecialchars(strip_tags($usuario));
	$passw=htmlspecialchars(strip_tags($passw));
	$intentosTimeOut=htmlspecialchars(strip_tags($intentosTimeOut));
	$tiempoTimeOut=htmlspecialchars(strip_tags($tiempoTimeOut));
	$establecimientoID=htmlspecialchars(strip_tags($establecimientoID));
	$interfazID=htmlspecialchars(strip_tags($interfazID));
	$versionRegistro=htmlspecialchars(strip_tags($versionRegistro));
	$regEstado=htmlspecialchars(strip_tags($regEstado));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($regFechaUltimaModificacion));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($regUsuarioUltimaModificacion));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($regFormularioUltimaModificacion));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($regVersionUltimaModificacion));
   
    // bind the values
	$stmt->bindParam(':serialID', $serialID);
	$stmt->bindParam(':puertoSerial', $puertoSerial);
	$stmt->bindParam(':velocidadBaudios', $velocidadBaudios);
	$stmt->bindParam(':paridad', $paridad);
	$stmt->bindParam(':bitParada', $bitParada);
	$stmt->bindParam(':longitudBits', $longitudBits);
	$stmt->bindParam(':usuario', $usuario);
	$stmt->bindParam(':passw', $passw);
	$stmt->bindParam(':intentosTimeOut', $intentosTimeOut);
	$stmt->bindParam(':tiempoTimeOut', $tiempoTimeOut);
	$stmt->bindParam(':establecimientoID', $establecimientoID);
	$stmt->bindParam(':interfazID', $interfazID);
	$stmt->bindParam(':versionRegistro', $versionRegistro);
	$stmt->bindParam(':regEstado', $regEstado);
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