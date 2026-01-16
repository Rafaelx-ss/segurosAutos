<?php 
class ConfiguracionBombasGenerales{ 
 
private $Conexion; 
private $Database; 
private $NombreTabla = 'ConfiguracionBombasGenerales'; 
 
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
	
	function ObtenerDatos($bombaNumero, $establecimientoID){
    	// query to check if email exists
    	try{
    		$query = "select * from " . $this->NombreTabla .  " where 1 " . ($bombaNumero > 0 ? " and bombaNumero = :bombaNumero" : "") . " " . ($establecimientoID > 0 ? " and establecimientoID = :establecimientoID" : "");
    	// prepare the query
    	$stmt = $this->Conexion->prepare( $query );
 		$this->Mensaje2=$query;
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
    	}catch (Exception $e){
			$this->Mensaje = $e->getMessage();
			return false;//.'<br /> <br />Consulta: <br />'.$consulta;
		}
    	
	}
 

function Inserta($registros){
	try{
		$consulta="";
	
		$consulta = 'INSERT INTO ' . $this->NombreTabla . 
			" (bombaNumero,digitosPrecio,digitosImporte,digitosVolumen,interfazID,tipoMedidaBomba,establecimientoID,versionRegistro,regEstado,regFechaUltimaModificacion,regUsuarioUltimaModificacion,regFormularioUltimaModificacion,regVersionUltimaModificacion,leeCalibracion) VALUES ";
			
		$coma = false;
		$comaText = "";
		foreach($registros as $item)
		 {
			//echo $item['textoID']."lolol";
			 if($this->ObtenerDatos($item['bombaNumero'], $item['establecimientoID'])){
				$this->Cambia($item['bombaNumero'],$item['digitosPrecio'],$item['digitosImporte'],$item['digitosVolumen'],$item['interfazID'],($item['tipoMedidaBomba']),($item['establecimientoID']),$item['versionRegistro'],$item['regEstado'],($item['regFechaUltimaModificacion']),$item['regUsuarioUltimaModificacion'],$item['regFormularioUltimaModificacion'],$item['regVersionUltimaModificacion'],$item['leeCalibracion']);
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
				
				$consulta= $consulta . $comaText . "(" . $item["bombaNumero"] . "," . $item["digitosPrecio"] . "," . $item["digitosImporte"] . "," . $item["digitosVolumen"] . "," . $item["interfazID"] . "," . ($item["tipoMedidaBomba"]) . ",'" . ($item["establecimientoID"]) . "'," . $item["versionRegistro"] . "," . $item["regEstado"] . ",'" . ($item["regFechaUltimaModificacion"]) . "'," . $item["regUsuarioUltimaModificacion"] . "," . $item["regFormularioUltimaModificacion"] . "," . $item["regVersionUltimaModificacion"] . "," .$item["leeCalibracion"] .")";
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
	$bombaNumero=htmlspecialchars(strip_tags($item['bombaNumero']));
	$digitosPrecio=htmlspecialchars(strip_tags($item['digitosPrecio']));
	$digitosImporte=htmlspecialchars(strip_tags($item['digitosImporte']));
	$digitosVolumen=htmlspecialchars(strip_tags($item['digitosVolumen']));
	$interfazID=htmlspecialchars(strip_tags($item['interfazID']));
	$tipoMedidaBomba=htmlspecialchars(strip_tags($item['tipoMedidaBomba']));
	$establecimientoID=htmlspecialchars(strip_tags($item['establecimientoID']));
	$versionRegistro=htmlspecialchars(strip_tags($item['versionRegistro']));
	$regEstado=htmlspecialchars(strip_tags($item['regEstado']));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($item['regFechaUltimaModificacion']));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($item['regUsuarioUltimaModificacion']));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($item['regFormularioUltimaModificacion']));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($item['regVersionUltimaModificacion']));
	$leeCalibracion=htmlspecialchars(strip_tags($item['leeCalibracion']));
   
    // bind the values
	$stmt->bindParam(':bombaNumero', $bombaNumero);
	$stmt->bindParam(':digitosPrecio', $digitosPrecio);
	$stmt->bindParam(':digitosImporte', $digitosImporte);
	$stmt->bindParam(':digitosVolumen', $digitosVolumen);
	$stmt->bindParam(':interfazID', $interfazID);
	$stmt->bindParam(':tipoMedidaBomba', $tipoMedidaBomba);
	$stmt->bindParam(':establecimientoID', $establecimientoID);
	$stmt->bindParam(':versionRegistro', $versionRegistro);
	$regEstado = (int)$regEstado;
	$stmt->bindValue(':regEstado', $regEstado, PDO::PARAM_INT);
	$stmt->bindParam(':regFechaUltimaModificacion', $regFechaUltimaModificacion);
	$stmt->bindParam(':regUsuarioUltimaModificacion', $regUsuarioUltimaModificacion);
	$stmt->bindParam(':regFormularioUltimaModificacion', $regFormularioUltimaModificacion);
	$stmt->bindParam(':regVersionUltimaModificacion', $regVersionUltimaModificacion);
	$stmt->bindParam(':leeCalibracion', $leeCalibracion);
   
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


function Cambia($bombaNumero,$digitosPrecio,$digitosImporte,$digitosVolumen,$interfazID,$tipoMedidaBomba,$establecimientoID,$versionRegistro,$regEstado,$regFechaUltimaModificacion,$regUsuarioUltimaModificacion,$regFormularioUltimaModificacion,$regVersionUltimaModificacion,$leeCalibracion){
	try{
		$query = "UPDATE " . $this->NombreTabla . " SET digitosPrecio=:digitosPrecio,digitosImporte=:digitosImporte,digitosVolumen=:digitosVolumen,interfazID=:interfazID,tipoMedidaBomba=:tipoMedidaBomba,establecimientoID=:establecimientoID,versionRegistro=:versionRegistro,regEstado=:regEstado,regFechaUltimaModificacion=:regFechaUltimaModificacion,regUsuarioUltimaModificacion=:regUsuarioUltimaModificacion,regFormularioUltimaModificacion=:regFormularioUltimaModificacion,regVersionUltimaModificacion=:regVersionUltimaModificacion,leeCalibracion=:leeCalibracion 
		WHERE bombaNumero=:bombaNumero and establecimientoID=:establecimientoID ";

    // prepare the query
    $stmt = $this->Conexion->prepare($query);
 	$this->Mensaje2=$query;
     // sanitize
	$bombaNumero=htmlspecialchars(strip_tags($bombaNumero));
	$digitosPrecio=htmlspecialchars(strip_tags($digitosPrecio));
	$digitosImporte=htmlspecialchars(strip_tags($digitosImporte));
	$digitosVolumen=htmlspecialchars(strip_tags($digitosVolumen));
	$interfazID=htmlspecialchars(strip_tags($interfazID));
	$tipoMedidaBomba=htmlspecialchars(strip_tags($tipoMedidaBomba));
	$establecimientoID=htmlspecialchars(strip_tags($establecimientoID));
	$versionRegistro=htmlspecialchars(strip_tags($versionRegistro));
	$regEstado=htmlspecialchars(strip_tags($regEstado));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($regFechaUltimaModificacion));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($regUsuarioUltimaModificacion));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($regFormularioUltimaModificacion));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($regVersionUltimaModificacion));
	$leeCalibracion=htmlspecialchars(strip_tags($leeCalibracion));
   
    // bind the values
	$stmt->bindParam(':bombaNumero', $bombaNumero);
	$stmt->bindParam(':digitosPrecio', $digitosPrecio);
	$stmt->bindParam(':digitosImporte', $digitosImporte);
	$stmt->bindParam(':digitosVolumen', $digitosVolumen);
	$stmt->bindParam(':interfazID', $interfazID);
	$stmt->bindParam(':tipoMedidaBomba', $tipoMedidaBomba);
	$stmt->bindParam(':establecimientoID', $establecimientoID);
	$stmt->bindParam(':versionRegistro', $versionRegistro);
	$regEstado = (int)$regEstado;
	$stmt->bindValue(':regEstado', $regEstado, PDO::PARAM_INT);
	$stmt->bindParam(':regFechaUltimaModificacion', $regFechaUltimaModificacion);
	$stmt->bindParam(':regUsuarioUltimaModificacion', $regUsuarioUltimaModificacion);
	$stmt->bindParam(':regFormularioUltimaModificacion', $regFormularioUltimaModificacion);
	$stmt->bindParam(':regVersionUltimaModificacion', $regVersionUltimaModificacion);
	$stmt->bindParam(':leeCalibracion', $leeCalibracion, PDO::PARAM_INT);

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