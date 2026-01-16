<?php 
class Impuestos{ 
 
private $Conexion; 
private $Database; 
private $NombreTabla = 'Impuestos'; 
 
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
	
	function ObtenerDatos($tipoImpuestoID=0, $productoID=0, $establecimientoID=0, $clasificacion=0, $fechaInicial=0){
		try{
			// query to check if email exists
    	$query = "select * from " . $this->NombreTabla .  " where 1 " . ($tipoImpuestoID > 0 ? " and tipoImpuestoID = :tipoImpuestoID" : "") . " " . ($productoID > 0 ? " and productoID = :productoID" : "") . " " . ($establecimientoID > 0 ? " and establecimientoID = :establecimientoID" : "") . " " . ($clasificacion > 0 ? " and clasificacion = :clasificacion" : "") . " " . ($fechaInicial > 0 ? " and fechaInicial = :fechaInicial" : "");
 
    	// prepare the query
    	$stmt = $this->Conexion->prepare( $query );
    	$this->Mensaje2=$query;
			
    	 // sanitize
		if($tipoImpuestoID > 0){
			$tipoImpuestoID=htmlspecialchars(strip_tags($tipoImpuestoID));
		}
		if($productoID > 0){
			$productoID=htmlspecialchars(strip_tags($productoID));
		}
		if($establecimientoID > 0){
			$establecimientoID=htmlspecialchars(strip_tags($establecimientoID));
		}
		if($clasificacion > 0){
			$clasificacion=htmlspecialchars(strip_tags($clasificacion));
		}
		if($fechaInicial > 0){
			$fechaInicial=htmlspecialchars(strip_tags($fechaInicial));
		}
 
    	// bind given id value

    
    	 // bind the values
		if($tipoImpuestoID > 0){
			$stmt->bindParam(':tipoImpuestoID', $tipoImpuestoID);
		}
		if($productoID > 0){
			$stmt->bindParam(':productoID', $productoID);
		}
		if($establecimientoID > 0){
			$stmt->bindParam(':establecimientoID', $establecimientoID);
		}
		if($clasificacion > 0){
			$stmt->bindParam(':clasificacion', $clasificacion);
		}
		if($fechaInicial > 0){
			$stmt->bindParam(':fechaInicial', $fechaInicial);
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
		} catch(Exception $e){
			$this->Mensaje = $e->getMessage();
			return false;//.'<br /> <br />Consulta: <br />'.$consulta;
		}
    	
	}
 

function Inserta($registros){
	try{
		$consulta="";
	
	$consulta = 'INSERT INTO ' . $this->NombreTabla . 
			" (tipoImpuestoID,productoID,establecimientoID,clasificacion,factor,importeImpuesto,fechaInicial,versionRegistro,regEstado,regFechaUltimaModificacion,regUsuarioUltimaModificacion,regFormularioUltimaModificacion,regVersionUltimaModificacion) VALUES ";
			
		$coma = false;
		$comaText = "";
		foreach($registros as $item)
		 {
			//echo $item['textoID']."lolol";
			 if($this->ObtenerDatos($item['tipoImpuestoID'], $item['productoID'], $item['establecimientoID'], $item['clasificacion'], $item['fechaInicial'])){
				$this->Cambia($item['tipoImpuestoID'],$item['productoID'],($item['establecimientoID']),($item['clasificacion']),($item['factor']),($item['importeImpuesto']),($item['fechaInicial']),$item['versionRegistro'],$item['regEstado'],($item['regFechaUltimaModificacion']),$item['regUsuarioUltimaModificacion'],$item['regFormularioUltimaModificacion'],$item['regVersionUltimaModificacion']);
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
				
				$consulta= $consulta . $comaText . "(" . $item["tipoImpuestoID"] . "," . $item["productoID"] . ",'" . ($item["establecimientoID"]) . "','" . ($item["clasificacion"]) . "','" . ($item["factor"]) . "','" . ($item["importeImpuesto"]) . "','" . ($item["fechaInicial"]) . "'," . $item["versionRegistro"] . "," . $item["regEstado"] . ",'" . ($item["regFechaUltimaModificacion"]) . "'," . $item["regUsuarioUltimaModificacion"] . "," . $item["regFormularioUltimaModificacion"] . "," . $item["regVersionUltimaModificacion"] . ")";
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
	$tipoImpuestoID=htmlspecialchars(strip_tags($item['tipoImpuestoID']));
	$productoID=htmlspecialchars(strip_tags($item['productoID']));
	$establecimientoID=htmlspecialchars(strip_tags($item['establecimientoID']));
	$clasificacion=htmlspecialchars(strip_tags($item['clasificacion']));
	$factor=htmlspecialchars(strip_tags($item['factor']));
	$importeImpuesto=htmlspecialchars(strip_tags($item['importeImpuesto']));
	$fechaInicial=htmlspecialchars(strip_tags($item['fechaInicial']));
	$versionRegistro=htmlspecialchars(strip_tags($item['versionRegistro']));
	$regEstado=htmlspecialchars(strip_tags($item['regEstado']));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($item['regFechaUltimaModificacion']));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($item['regUsuarioUltimaModificacion']));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($item['regFormularioUltimaModificacion']));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($item['regVersionUltimaModificacion']));
   
    // bind the values
	$stmt->bindParam(':tipoImpuestoID', $tipoImpuestoID);
	$stmt->bindParam(':productoID', $productoID);
	$stmt->bindParam(':establecimientoID', $establecimientoID);
	$stmt->bindParam(':clasificacion', $clasificacion);
	$stmt->bindParam(':factor', $factor);
	$stmt->bindParam(':importeImpuesto', $importeImpuesto);
	$stmt->bindParam(':fechaInicial', $fechaInicial);
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


function Cambia($tipoImpuestoID,$productoID,$establecimientoID,$clasificacion,$factor,$importeImpuesto,$fechaInicial,$versionRegistro,$regEstado,$regFechaUltimaModificacion,$regUsuarioUltimaModificacion,$regFormularioUltimaModificacion,$regVersionUltimaModificacion){
	try{
		$query = "UPDATE " . $this->NombreTabla . " SET productoID=:productoID,establecimientoID=:establecimientoID,clasificacion=:clasificacion,factor=:factor,importeImpuesto=:importeImpuesto,fechaInicial=:fechaInicial,versionRegistro=:versionRegistro,regEstado=:regEstado,regFechaUltimaModificacion=:regFechaUltimaModificacion,regUsuarioUltimaModificacion=:regUsuarioUltimaModificacion,regFormularioUltimaModificacion=:regFormularioUltimaModificacion,regVersionUltimaModificacion=:regVersionUltimaModificacion 
		WHERE tipoImpuestoID=:tipoImpuestoID and productoID=:productoID and establecimientoID=:establecimientoID and clasificacion=:clasificacion and fechaInicial=:fechaInicial ";

    // prepare the query
    $stmt = $this->Conexion->prepare($query);
 	$this->Mensaje2=$query;
     // sanitize
	$tipoImpuestoID=htmlspecialchars(strip_tags($tipoImpuestoID));
	$productoID=htmlspecialchars(strip_tags($productoID));
	$establecimientoID=htmlspecialchars(strip_tags($establecimientoID));
	$clasificacion=htmlspecialchars(strip_tags($clasificacion));
	$factor=htmlspecialchars(strip_tags($factor));
	$importeImpuesto=htmlspecialchars(strip_tags($importeImpuesto));
	$fechaInicial=htmlspecialchars(strip_tags($fechaInicial));
	$versionRegistro=htmlspecialchars(strip_tags($versionRegistro));
	$regEstado=htmlspecialchars(strip_tags($regEstado));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($regFechaUltimaModificacion));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($regUsuarioUltimaModificacion));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($regFormularioUltimaModificacion));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($regVersionUltimaModificacion));
   
    // bind the values
	$stmt->bindParam(':tipoImpuestoID', $tipoImpuestoID);
	$stmt->bindParam(':productoID', $productoID);
	$stmt->bindParam(':establecimientoID', $establecimientoID);
	$stmt->bindParam(':clasificacion', $clasificacion);
	$stmt->bindParam(':factor', $factor);
	$stmt->bindParam(':importeImpuesto', $importeImpuesto);
	$stmt->bindParam(':fechaInicial', $fechaInicial);
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