<?php 
class PasoSQLTanques{ 
 
private $Conexion; 
private $Database; 
private $NombreTabla = 'PasoSQLTanques'; 
 
// object properties
public $Campos;
public $Dataset;
public $Mensaje;
 
// constructor
    public function __construct($dbf){
        $this->Database = $dbf;
        $this->Conexion = $dbf->conn;
    }
	
	function ObtenerDatos($tanqueID=0, $establecimientoID=0, $tanqueNumero=0){
    	// query to check if email exists
    	$query = "select * from " . $this->NombreTabla .  " where 1 " . ($tanqueID > 0 ? " and tanqueID = :tanqueID" : "") . " " . ($establecimientoID > 0 ? " and establecimientoID = :establecimientoID" : "") . " " . ($tanqueNumero > 0 ? " and tanqueNumero = :tanqueNumero" : "");
 
    	// prepare the query
    	$stmt = $this->Conexion->prepare( $query );
 
    	 // sanitize
		if($tanqueID > 0){
			$tanqueID=htmlspecialchars(strip_tags($tanqueID));
		}
		if($establecimientoID > 0){
			$establecimientoID=htmlspecialchars(strip_tags($establecimientoID));
		}
		if($tanqueNumero > 0){
			$tanqueNumero=htmlspecialchars(strip_tags($tanqueNumero));
		}
 
    	// bind given id value

    
    	 // bind the values
		if($tanqueID > 0){
			$stmt->bindParam(':tanqueID', $tanqueID);
		}
		if($establecimientoID > 0){
			$stmt->bindParam(':establecimientoID', $establecimientoID);
		}
		if($tanqueNumero > 0){
			$stmt->bindParam(':tanqueNumero', $tanqueNumero);
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
			" (tanqueID,establecimientoID,tanqueNumero,versionRegistro,regEstado,regFechaUltimaModificacion,regUsuarioUltimaModificacion,regFormularioUltimaModificacion,regVersionUltimaModificacion) VALUES ";
			
		$coma = false;
		$comaText = "";
		foreach($registros as $item)
		 {
			//echo $item['textoID']."lolol";
			 if($this->ObtenerDatos($item['tanqueID'], $item['establecimientoID'], $item['tanqueNumero'])){
				$this->Cambia($item['tanqueID'],($item['establecimientoID']),$item['tanqueNumero'],$item['versionRegistro'],$item['regEstado'],($item['regFechaUltimaModificacion']),$item['regUsuarioUltimaModificacion'],$item['regFormularioUltimaModificacion'],$item['regVersionUltimaModificacion']);
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
				
				$consulta= $consulta . $comaText . "(" . $item["tanqueID"] . ",'" . ($item["establecimientoID"]) . "'," . $item["tanqueNumero"] . "," . $item["versionRegistro"] . "," . $item["regEstado"] . ",'" . ($item["regFechaUltimaModificacion"]) . "'," . $item["regUsuarioUltimaModificacion"] . "," . $item["regFormularioUltimaModificacion"] . "," . $item["regVersionUltimaModificacion"] . ")";
			}
			 
		   
		 }
	if(!$coma){
		$consulta="select 1";
	}
	
	$this->query=$consulta;
	
    // prepare the query
    $stmt = $this->Conexion->prepare($this->query);
	
    // sanitize
	$tanqueID=htmlspecialchars(strip_tags($item['tanqueID']));
	$establecimientoID=htmlspecialchars(strip_tags($item['establecimientoID']));
	$tanqueNumero=htmlspecialchars(strip_tags($item['tanqueNumero']));
	$versionRegistro=htmlspecialchars(strip_tags($item['versionRegistro']));
	$regEstado=htmlspecialchars(strip_tags($item['regEstado']));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($item['regFechaUltimaModificacion']));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($item['regUsuarioUltimaModificacion']));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($item['regFormularioUltimaModificacion']));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($item['regVersionUltimaModificacion']));
   
    // bind the values
	$stmt->bindParam(':tanqueID', $tanqueID);
	$stmt->bindParam(':establecimientoID', $establecimientoID);
	$stmt->bindParam(':tanqueNumero', $tanqueNumero);
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


function Cambia($tanqueID,$establecimientoID,$tanqueNumero,$versionRegistro,$regEstado,$regFechaUltimaModificacion,$regUsuarioUltimaModificacion,$regFormularioUltimaModificacion,$regVersionUltimaModificacion){
    $query = "UPDATE " . $this->NombreTabla . " SET establecimientoID=:establecimientoID,tanqueNumero=:tanqueNumero,versionRegistro=:versionRegistro,regEstado=:regEstado,regFechaUltimaModificacion=:regFechaUltimaModificacion,regUsuarioUltimaModificacion=:regUsuarioUltimaModificacion,regFormularioUltimaModificacion=:regFormularioUltimaModificacion,regVersionUltimaModificacion=:regVersionUltimaModificacion 
		WHERE tanqueID=:tanqueID and establecimientoID=:establecimientoID and tanqueNumero=:tanqueNumero ";

    // prepare the query
    $stmt = $this->Conexion->prepare($query);
 
     // sanitize
	$tanqueID=htmlspecialchars(strip_tags($tanqueID));
	$establecimientoID=htmlspecialchars(strip_tags($establecimientoID));
	$tanqueNumero=htmlspecialchars(strip_tags($tanqueNumero));
	$versionRegistro=htmlspecialchars(strip_tags($versionRegistro));
	$regEstado=htmlspecialchars(strip_tags($regEstado));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($regFechaUltimaModificacion));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($regUsuarioUltimaModificacion));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($regFormularioUltimaModificacion));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($regVersionUltimaModificacion));
   
    // bind the values
	$stmt->bindParam(':tanqueID', $tanqueID);
	$stmt->bindParam(':establecimientoID', $establecimientoID);
	$stmt->bindParam(':tanqueNumero', $tanqueNumero);
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