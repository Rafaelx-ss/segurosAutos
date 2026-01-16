<?php 
class Bombas{ 
 
private $Conexion; 
private $Database; 
private $NombreTabla = 'Bombas'; 
 
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
   		// $id = intval($id);
    	// query to check if email exists
    	$query = "select * from " . $this->NombreTabla . " WHERE 1 " . ($bombaNumero > 0 ? " AND bombaNumero = :bombaNumero" : "") . " " . ($establecimientoID > 0 ? " and establecimientoID = :establecimientoID" : "");
 
    	// prepare the query
    	$stmt = $this->Conexion->prepare( $query );
 
    	// sanitize
		if($bombaNumero > 0){
			$bombaNumero=htmlspecialchars(strip_tags($bombaNumero));
		}
		if($establecimientoID > 0){
			$establecimientoID=htmlspecialchars(strip_tags($establecimientoID));
		}
		
 
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
			" (bombaNumero,bombaDescripcion,bombaIsla,almacenID,dispensarioID,establecimientoID,activoBomba,versionRegistro,regEstado,regFechaUltimaModificacion,regUsuarioUltimaModificacion,regFormularioUltimaModificacion,regVersionUltimaModificacion) VALUES ";
			
		$coma = false;
		$comaText = "";
		foreach($registros as $item)
		 {
			//echo $item['textoID']."lolol";
			 if($this->ObtenerDatos($item['bombaNumero'], $item['establecimientoID'])){
				$this->Cambia($item['bombaNumero'],utf8_decode($item['bombaDescripcion']),$item['bombaIsla'],$item['almacenID'],$item['dispensarioID'],utf8_decode($item['establecimientoID']),$item['activoBomba'],$item['versionRegistro'],$item['regEstado'],utf8_decode($item['regFechaUltimaModificacion']),$item['regUsuarioUltimaModificacion'],$item['regFormularioUltimaModificacion'],$item['regVersionUltimaModificacion']);
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
				
				$consulta= $consulta . $comaText . "(" . $item["bombaNumero"] . ",'" . utf8_decode($item["bombaDescripcion"]) . "'," . $item["bombaIsla"] . "," . $item["almacenID"] . "," . $item["dispensarioID"] . ",'" . utf8_decode($item["establecimientoID"]) . "'," . $item["activoBomba"] . "," . $item["versionRegistro"] . "," . $item["regEstado"] . ",'" . utf8_decode($item["regFechaUltimaModificacion"]) . "'," . $item["regUsuarioUltimaModificacion"] . "," . $item["regFormularioUltimaModificacion"] . "," . $item["regVersionUltimaModificacion"] . ")";
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
	$bombaDescripcion=htmlspecialchars(strip_tags($item['bombaDescripcion']));
	$bombaIsla=htmlspecialchars(strip_tags($item['bombaIsla']));
	$almacenID=htmlspecialchars(strip_tags($item['almacenID']));
	$dispensarioID=htmlspecialchars(strip_tags($item['dispensarioID']));
	$establecimientoID=htmlspecialchars(strip_tags($item['establecimientoID']));
	$activoBomba=htmlspecialchars(strip_tags($item['activoBomba']));
	$versionRegistro=htmlspecialchars(strip_tags($item['versionRegistro']));
	$regEstado=htmlspecialchars(strip_tags($item['regEstado']));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($item['regFechaUltimaModificacion']));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($item['regUsuarioUltimaModificacion']));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($item['regFormularioUltimaModificacion']));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($item['regVersionUltimaModificacion']));
   
    // bind the values
	$stmt->bindParam(':bombaNumero', $bombaNumero);
	$stmt->bindParam(':bombaDescripcion', $bombaDescripcion);
	$stmt->bindParam(':bombaIsla', $bombaIsla);
	$stmt->bindParam(':almacenID', $almacenID);
	$stmt->bindParam(':dispensarioID', $dispensarioID);
	$stmt->bindParam(':establecimientoID', $establecimientoID);
	$activoBomba = (int)$activoBomba;
	$stmt->bindValue(':activoBomba', $activoBomba, PDO::PARAM_INT);
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


function Cambia($bombaNumero,$bombaDescripcion,$bombaIsla,$almacenID,$dispensarioID,$establecimientoID,$activoBomba,$versionRegistro,$regEstado,$regFechaUltimaModificacion,$regUsuarioUltimaModificacion,$regFormularioUltimaModificacion,$regVersionUltimaModificacion){
    $query = "UPDATE " . $this->NombreTabla . " SET bombaDescripcion=:bombaDescripcion,bombaIsla=:bombaIsla,almacenID=:almacenID,dispensarioID=:dispensarioID,establecimientoID=:establecimientoID,activoBomba=:activoBomba,versionRegistro=:versionRegistro,regEstado=:regEstado,regFechaUltimaModificacion=:regFechaUltimaModificacion,regUsuarioUltimaModificacion=:regUsuarioUltimaModificacion,regFormularioUltimaModificacion=:regFormularioUltimaModificacion,regVersionUltimaModificacion=:regVersionUltimaModificacion 
		WHERE bombaNumero=:bombaNumero and establecimientoID=:establecimientoID ";

    // prepare the query
    $stmt = $this->Conexion->prepare($query);
 
     // sanitize
	$bombaNumero=htmlspecialchars(strip_tags($bombaNumero));
	$bombaDescripcion=htmlspecialchars(strip_tags($bombaDescripcion));
	$bombaIsla=htmlspecialchars(strip_tags($bombaIsla));
	$almacenID=htmlspecialchars(strip_tags($almacenID));
	$dispensarioID=htmlspecialchars(strip_tags($dispensarioID));
	$establecimientoID=htmlspecialchars(strip_tags($establecimientoID));
	$activoBomba=htmlspecialchars(strip_tags($activoBomba));
	$versionRegistro=htmlspecialchars(strip_tags($versionRegistro));
	$regEstado=htmlspecialchars(strip_tags($regEstado));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($regFechaUltimaModificacion));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($regUsuarioUltimaModificacion));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($regFormularioUltimaModificacion));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($regVersionUltimaModificacion));
   
    // bind the values
	$stmt->bindParam(':bombaNumero', $bombaNumero);
	$stmt->bindParam(':bombaDescripcion', $bombaDescripcion);
	$stmt->bindParam(':bombaIsla', $bombaIsla);
	$stmt->bindParam(':almacenID', $almacenID);
	$stmt->bindParam(':dispensarioID', $dispensarioID);
	$stmt->bindParam(':establecimientoID', $establecimientoID);
	$activoBomba = (int)$activoBomba;
	$stmt->bindValue(':activoBomba', $activoBomba, PDO::PARAM_INT);
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