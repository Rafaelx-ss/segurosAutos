<?php 
class ConfiguracionesRED{ 
 
private $Conexion; 
private $Database; 
private $NombreTabla = 'ConfiguracionesRED'; 
 
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
	
	function ObtenerDatos($id=0,$establecimientoID=0){
   		try{
   			$consulta="";
		if($establecimientoID>0){
			$consulta= " and establecimientoID = " . $establecimientoID;
		}
    	// query to check if email exists
    	$query = "select * from " . $this->NombreTabla .  " where 1 " . $consulta . ($id > 0 ? " and redID = ?" : "");
 
    	// prepare the query
    	$stmt = $this->Conexion->prepare( $query );
 		$this->Mensaje2=$query;
    	
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
   		} catch (Exception $e){
			$this->Mensaje = $e->getMessage();
			return false;//.'<br /> <br />Consulta: <br />'.$consulta;
		}
		
	}
 

function Inserta($registros){
	try{
		$consulta="";
	
	$consulta = 'INSERT INTO ' . $this->NombreTabla . 
			" (redID,ipLocal,puertoLocal,ipRemota,puertoRemoto,usuario,passw,rutaWEB,establecimientoID,interfazID,numeroTerminal,versionRegistro,regEstado,regFechaUltimaModificacion,regUsuarioUltimaModificacion,regFormularioUltimaModificacion,regVersionUltimaModificacion) VALUES ";
			
		$coma = false;
		$comaText = "";
		foreach($registros as $item)
		 {
			//echo $item['textoID']."lolol";
			 if($this->ObtenerDatos($item['redID'])){
				$this->Cambia($item['redID'],($item['ipLocal']),($item['puertoLocal']),($item['ipRemota']),($item['puertoRemoto']),($item['usuario']),($item['passw']),($item['rutaWEB']),($item['establecimientoID']),$item['interfazID'],$item['numeroTerminal'],$item['versionRegistro'],($item['regEstado']),($item['regFechaUltimaModificacion']),$item['regUsuarioUltimaModificacion'],$item['regFormularioUltimaModificacion'],$item['regVersionUltimaModificacion']);
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
				
				$consulta= $consulta . $comaText . "(" . $item["redID"] . ",'" . ($item["ipLocal"]) . "','" . ($item["puertoLocal"]) . "','" . ($item["ipRemota"]) . "','" . ($item["puertoRemoto"]) . "','" . ($item["usuario"]) . "','" . ($item["passw"]) . "','" . ($item["rutaWEB"]) . "','" . ($item["establecimientoID"]) . "'," . $item["interfazID"] . "," . $item["numeroTerminal"] . "," . $item["versionRegistro"] . ",'" . ($item["regEstado"]) . "','" . ($item["regFechaUltimaModificacion"]) . "'," . $item["regUsuarioUltimaModificacion"] . "," . $item["regFormularioUltimaModificacion"] . "," . $item["regVersionUltimaModificacion"] . ")";
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
	$redID=htmlspecialchars(strip_tags($item['redID']));
	$ipLocal=htmlspecialchars(strip_tags($item['ipLocal']));
	$puertoLocal=htmlspecialchars(strip_tags($item['puertoLocal']));
	$ipRemota=htmlspecialchars(strip_tags($item['ipRemota']));
	$puertoRemoto=htmlspecialchars(strip_tags($item['puertoRemoto']));
	$usuario=htmlspecialchars(strip_tags($item['usuario']));
	$passw=htmlspecialchars(strip_tags($item['passw']));
	$rutaWEB=htmlspecialchars(strip_tags($item['rutaWEB']));
	$establecimientoID=htmlspecialchars(strip_tags($item['establecimientoID']));
	$interfazID=htmlspecialchars(strip_tags($item['interfazID']));
	$numeroTerminal=htmlspecialchars(strip_tags($item['numeroTerminal']));
	$versionRegistro=htmlspecialchars(strip_tags($item['versionRegistro']));
	$regEstado=htmlspecialchars(strip_tags($item['regEstado']));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($item['regFechaUltimaModificacion']));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($item['regUsuarioUltimaModificacion']));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($item['regFormularioUltimaModificacion']));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($item['regVersionUltimaModificacion']));
   
    // bind the values
	$stmt->bindParam(':redID', $redID);
	$stmt->bindParam(':ipLocal', $ipLocal);
	$stmt->bindParam(':puertoLocal', $puertoLocal);
	$stmt->bindParam(':ipRemota', $ipRemota);
	$stmt->bindParam(':puertoRemoto', $puertoRemoto);
	$stmt->bindParam(':usuario', $usuario);
	$stmt->bindParam(':passw', $passw);
	$stmt->bindParam(':rutaWEB', $rutaWEB);
	$stmt->bindParam(':establecimientoID', $establecimientoID);
	$stmt->bindParam(':interfazID', $interfazID);
	$stmt->bindParam(':numeroTerminal', $numeroTerminal);
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
        $this->Mensaje = $e->getMessage();//.'<br /> <br />Consulta: <br />'.$consulta;
    }

 
    return false;
	}
    catch (Exception $e){
        $this->Mensaje = $e->getMessage();//.'<br /> <br />Consulta: <br />'.$consulta;
    }
}


function Cambia($redID,$ipLocal,$puertoLocal,$ipRemota,$puertoRemoto,$usuario,$passw,$rutaWEB,$establecimientoID,$interfazID,$numeroTerminal,$versionRegistro,$regEstado,$regFechaUltimaModificacion,$regUsuarioUltimaModificacion,$regFormularioUltimaModificacion,$regVersionUltimaModificacion){
	try{
		$query = "UPDATE " . $this->NombreTabla . " SET ipLocal=:ipLocal,puertoLocal=:puertoLocal,ipRemota=:ipRemota,puertoRemoto=:puertoRemoto,usuario=:usuario,passw=:passw,rutaWEB=:rutaWEB,establecimientoID=:establecimientoID,interfazID=:interfazID,numeroTerminal=:numeroTerminal,versionRegistro=:versionRegistro,regEstado=:regEstado,regFechaUltimaModificacion=:regFechaUltimaModificacion,regUsuarioUltimaModificacion=:regUsuarioUltimaModificacion,regFormularioUltimaModificacion=:regFormularioUltimaModificacion,regVersionUltimaModificacion=:regVersionUltimaModificacion 
		WHERE redID=:redID ";

    // prepare the query
    $stmt = $this->Conexion->prepare($query);
 	$this->Mensaje2=$query;
     // sanitize
	$redID=htmlspecialchars(strip_tags($redID));
	$ipLocal=htmlspecialchars(strip_tags($ipLocal));
	$puertoLocal=htmlspecialchars(strip_tags($puertoLocal));
	$ipRemota=htmlspecialchars(strip_tags($ipRemota));
	$puertoRemoto=htmlspecialchars(strip_tags($puertoRemoto));
	$usuario=htmlspecialchars(strip_tags($usuario));
	$passw=htmlspecialchars(strip_tags($passw));
	$rutaWEB=htmlspecialchars(strip_tags($rutaWEB));
	$establecimientoID=htmlspecialchars(strip_tags($establecimientoID));
	$interfazID=htmlspecialchars(strip_tags($interfazID));
	$numeroTerminal=htmlspecialchars(strip_tags($numeroTerminal));
	$versionRegistro=htmlspecialchars(strip_tags($versionRegistro));
	$regEstado=htmlspecialchars(strip_tags($regEstado));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($regFechaUltimaModificacion));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($regUsuarioUltimaModificacion));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($regFormularioUltimaModificacion));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($regVersionUltimaModificacion));
   
    // bind the values
	$stmt->bindParam(':redID', $redID);
	$stmt->bindParam(':ipLocal', $ipLocal);
	$stmt->bindParam(':puertoLocal', $puertoLocal);
	$stmt->bindParam(':ipRemota', $ipRemota);
	$stmt->bindParam(':puertoRemoto', $puertoRemoto);
	$stmt->bindParam(':usuario', $usuario);
	$stmt->bindParam(':passw', $passw);
	$stmt->bindParam(':rutaWEB', $rutaWEB);
	$stmt->bindParam(':establecimientoID', $establecimientoID);
	$stmt->bindParam(':interfazID', $interfazID);
	$stmt->bindParam(':numeroTerminal', $numeroTerminal);
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
        $this->Mensaje = $e->getMessage();//.'<br /> <br />Consulta: <br />'.$consulta;
    }
 
    return false;
	}
    catch (Exception $e){
        $this->Mensaje = $e->getMessage();//.'<br /> <br />Consulta: <br />'.$consulta;
    }
}
	
		
}
	?>