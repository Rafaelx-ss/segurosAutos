<?php 
class ConfiguracionBombasComunicacion{ 
 
private $Conexion; 
private $Database; 
private $NombreTabla = 'ConfiguracionBombasComunicacion'; 
 
// object properties
public $Campos;
public $Dataset;
public $Mensaje;
 
// constructor
    public function __construct($dbf){
        $this->Database = $dbf;
        $this->Conexion = $dbf->conn;
    }
	
	function ObtenerDatos($bombaNumero=0, $establecimientoID=0){
    	// query to check if email exists
    	$query = "select * from " . $this->NombreTabla .  " where 1 " . ($bombaNumero > 0 ? " and bombaNumero = :bombaNumero" : "") . " " . ($establecimientoID > 0 ? " and establecimientoID = :establecimientoID" : "");
 
    	// prepare the query
    	$stmt = $this->Conexion->prepare( $query );
 
    	 // sanitize
		if($bombaNumero > 0){
			$bombaNumero=htmlspecialchars(strip_tags($bombaNumero));
		}
		if($establecimientoID > 0){
			$establecimientoID=htmlspecialchars(strip_tags($establecimientoID));
		}
 
    	// bind given id value

    
    	 // bind the values
		if($bombaNumero > 0){
			$stmt->bindParam(':bombaNumero', $bombaNumero);
		}
		if($establecimientoID > 0){
			$stmt->bindParam(':establecimientoID', $establecimientoID);
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
			" (bombaNumero,establecimientoID,bombaPosicionInterfaz,bombaCanalComunicacion,interfazID,bombaTipo,validaCHK,versionRegistro,regEstado,regFechaUltimaModificacion,regUsuarioUltimaModificacion,regFormularioUltimaModificacion,regVersionUltimaModificacion) VALUES ";
			
		$coma = false;
		$comaText = "";
		foreach($registros as $item)
		 {
			//echo $item['textoID']."lolol";
			 if($this->ObtenerDatos($item['bombaNumero'], $item['establecimientoID'])){
				$this->Cambia($item['bombaNumero'],($item['establecimientoID']),($item['bombaPosicionInterfaz']),($item['bombaCanalComunicacion']),$item['interfazID'],($item['bombaTipo']),$item['validaCHK'],$item['versionRegistro'],$item['regEstado'],($item['regFechaUltimaModificacion']),$item['regUsuarioUltimaModificacion'],$item['regFormularioUltimaModificacion'],$item['regVersionUltimaModificacion']);
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
				
				$consulta= $consulta . $comaText . "(" . $item["bombaNumero"] . ",'" . ($item["establecimientoID"]) . "','" . ($item["bombaPosicionInterfaz"]) . "','" . ($item["bombaCanalComunicacion"]) . "'," . $item["interfazID"] . ",'" . ($item["bombaTipo"]) . "'," . $item["validaCHK"] . "," . $item["versionRegistro"] . "," . $item["regEstado"] . ",'" . ($item["regFechaUltimaModificacion"]) . "'," . $item["regUsuarioUltimaModificacion"] . "," . $item["regFormularioUltimaModificacion"] . "," . $item["regVersionUltimaModificacion"] . ")";
			}
			 
		   
		 }
	if(!$coma){
		$consulta="select 1";
	}
	
	$this->query=$consulta;
	
    // prepare the query
    $stmt = $this->Conexion->prepare($this->query);
	
    // sanitize
	$bombaNumero=htmlspecialchars(strip_tags($item['bombaNumero']));
	$establecimientoID=htmlspecialchars(strip_tags($item['establecimientoID']));
	$bombaPosicionInterfaz=htmlspecialchars(strip_tags($item['bombaPosicionInterfaz']));
	$bombaCanalComunicacion=htmlspecialchars(strip_tags($item['bombaCanalComunicacion']));
	$interfazID=htmlspecialchars(strip_tags($item['interfazID']));
	$bombaTipo=htmlspecialchars(strip_tags($item['bombaTipo']));
	$validaCHK=htmlspecialchars(strip_tags($item['validaCHK']));
	$versionRegistro=htmlspecialchars(strip_tags($item['versionRegistro']));
	$regEstado=htmlspecialchars(strip_tags($item['regEstado']));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($item['regFechaUltimaModificacion']));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($item['regUsuarioUltimaModificacion']));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($item['regFormularioUltimaModificacion']));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($item['regVersionUltimaModificacion']));
   
    // bind the values
	$stmt->bindParam(':bombaNumero', $bombaNumero);
	$stmt->bindParam(':establecimientoID', $establecimientoID);
	$stmt->bindParam(':bombaPosicionInterfaz', $bombaPosicionInterfaz);
	$stmt->bindParam(':bombaCanalComunicacion', $bombaCanalComunicacion);
	$stmt->bindParam(':interfazID', $interfazID);
	$stmt->bindParam(':bombaTipo', $bombaTipo);
	$validaCHK = (int)$validaCHK;
	$stmt->bindValue(':validaCHK', $validaCHK, PDO::PARAM_INT);
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


function Cambia($bombaNumero,$establecimientoID,$bombaPosicionInterfaz,$bombaCanalComunicacion,$interfazID,$bombaTipo,$validaCHK,$versionRegistro,$regEstado,$regFechaUltimaModificacion,$regUsuarioUltimaModificacion,$regFormularioUltimaModificacion,$regVersionUltimaModificacion){
    $query = "UPDATE " . $this->NombreTabla . " SET establecimientoID=:establecimientoID,bombaPosicionInterfaz=:bombaPosicionInterfaz,bombaCanalComunicacion=:bombaCanalComunicacion,interfazID=:interfazID,bombaTipo=:bombaTipo,validaCHK=:validaCHK,versionRegistro=:versionRegistro,regEstado=:regEstado,regFechaUltimaModificacion=:regFechaUltimaModificacion,regUsuarioUltimaModificacion=:regUsuarioUltimaModificacion,regFormularioUltimaModificacion=:regFormularioUltimaModificacion,regVersionUltimaModificacion=:regVersionUltimaModificacion 
		WHERE bombaNumero=:bombaNumero and establecimientoID=:establecimientoID ";

    // prepare the query
    $stmt = $this->Conexion->prepare($query);
 
     // sanitize
	$bombaNumero=htmlspecialchars(strip_tags($bombaNumero));
	$establecimientoID=htmlspecialchars(strip_tags($establecimientoID));
	$bombaPosicionInterfaz=htmlspecialchars(strip_tags($bombaPosicionInterfaz));
	$bombaCanalComunicacion=htmlspecialchars(strip_tags($bombaCanalComunicacion));
	$interfazID=htmlspecialchars(strip_tags($interfazID));
	$bombaTipo=htmlspecialchars(strip_tags($bombaTipo));
	$validaCHK=htmlspecialchars(strip_tags($validaCHK));
	$versionRegistro=htmlspecialchars(strip_tags($versionRegistro));
	$regEstado=htmlspecialchars(strip_tags($regEstado));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($regFechaUltimaModificacion));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($regUsuarioUltimaModificacion));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($regFormularioUltimaModificacion));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($regVersionUltimaModificacion));
   
    // bind the values
	$stmt->bindParam(':bombaNumero', $bombaNumero);
	$stmt->bindParam(':establecimientoID', $establecimientoID);
	$stmt->bindParam(':bombaPosicionInterfaz', $bombaPosicionInterfaz);
	$stmt->bindParam(':bombaCanalComunicacion', $bombaCanalComunicacion);
	$stmt->bindParam(':interfazID', $interfazID);
	$stmt->bindParam(':bombaTipo', $bombaTipo);
	$validaCHK = (int)$validaCHK;
	$stmt->bindValue(':validaCHK', $validaCHK, PDO::PARAM_INT);
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