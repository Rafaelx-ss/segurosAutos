<?php 
class Productos{ 
 
private $Conexion; 
private $Database; 
private $NombreTabla = 'Productos'; 
 
// object properties
public $Campos;
public $Dataset;
public $Mensaje;
 
// constructor
    public function __construct($dbf){
        $this->Database = $dbf;
        $this->Conexion = $dbf->conn;
    }
	
	function ObtenerDatos($id=0){
   		// $id = intval($id);
    	// query to check if email exists
    	$query = "select * from " . $this->NombreTabla . " " . ($id > 0 ? " where productoID = ?" : "");
 
    	// prepare the query
    	$stmt = $this->Conexion->prepare( $query );
 
    	// sanitize
    	
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
			" (productoID,codigoProducto,nombreProducto,productoRGB,productoColorCCS,claveUnidadID,claveProdServID,tipoProductoID,claveProductoEnvioSATID,claveSubProductoEnvioSATID,claveUnidadSATID,versionRegistro,regEstado,regFechaUltimaModificacion,regUsuarioUltimaModificacion,regFormularioUltimaModificacion,regVersionUltimaModificacion) VALUES ";
			
		$coma = false;
		$comaText = "";
		foreach($registros as $item)
		 {
			//echo $item['textoID']."lolol";
			 if($this->ObtenerDatos($item['productoID'])){
				$this->Cambia($item['productoID'],utf8_decode($item['codigoProducto']),utf8_decode($item['nombreProducto']),utf8_decode($item['productoRGB']),utf8_decode($item['productoColorCCS']),$item['claveUnidadID'],$item['claveProdServID'],$item['tipoProductoID'],$item['claveProductoEnvioSATID'],$item['claveSubProductoEnvioSATID'],$item['claveUnidadSATID'],$item['versionRegistro'],$item['regEstado'],utf8_decode($item['regFechaUltimaModificacion']),$item['regUsuarioUltimaModificacion'],$item['regFormularioUltimaModificacion'],$item['regVersionUltimaModificacion']);
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
				
				$consulta= $consulta . $comaText . "(" . $item["productoID"] . ",'" . utf8_decode($item["codigoProducto"]) . "','" . utf8_decode($item["nombreProducto"]) . "','" . utf8_decode($item["productoRGB"]) . "','" . utf8_decode($item["productoColorCCS"]) . "'," . $item["claveUnidadID"] . "," . $item["claveProdServID"] . "," . $item["tipoProductoID"] . "," . $item["claveProductoEnvioSATID"] . "," . $item["claveSubProductoEnvioSATID"] . "," . $item["claveUnidadSATID"] . "," . $item["versionRegistro"] . "," . $item["regEstado"] . ",'" . utf8_decode($item["regFechaUltimaModificacion"]) . "'," . $item["regUsuarioUltimaModificacion"] . "," . $item["regFormularioUltimaModificacion"] . "," . $item["regVersionUltimaModificacion"] . ")";
			}
			 
		   
		 }
	if(!$coma){
		$consulta="select 1";
	}
	
	$this->query=$consulta;
	
    // prepare the query
    $stmt = $this->Conexion->prepare($this->query);
	
    // sanitize
	$productoID=htmlspecialchars(strip_tags($item['productoID']));
	$codigoProducto=htmlspecialchars(strip_tags($item['codigoProducto']));
	$nombreProducto=htmlspecialchars(strip_tags($item['nombreProducto']));
	$productoRGB=htmlspecialchars(strip_tags($item['productoRGB']));
	$productoColorCCS=htmlspecialchars(strip_tags($item['productoColorCCS']));
	$claveUnidadID=htmlspecialchars(strip_tags($item['claveUnidadID']));
	$claveProdServID=htmlspecialchars(strip_tags($item['claveProdServID']));
	$tipoProductoID=htmlspecialchars(strip_tags($item['tipoProductoID']));
	$claveProductoEnvioSATID=htmlspecialchars(strip_tags($item['claveProductoEnvioSATID']));
	$claveSubProductoEnvioSATID=htmlspecialchars(strip_tags($item['claveSubProductoEnvioSATID']));
	$claveUnidadSATID=htmlspecialchars(strip_tags($item['claveUnidadSATID']));
	$versionRegistro=htmlspecialchars(strip_tags($item['versionRegistro']));
	$regEstado=htmlspecialchars(strip_tags($item['regEstado']));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($item['regFechaUltimaModificacion']));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($item['regUsuarioUltimaModificacion']));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($item['regFormularioUltimaModificacion']));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($item['regVersionUltimaModificacion']));
   
    // bind the values
	$stmt->bindParam(':productoID', $productoID);
	$stmt->bindParam(':codigoProducto', $codigoProducto);
	$stmt->bindParam(':nombreProducto', $nombreProducto);
	$stmt->bindParam(':productoRGB', $productoRGB);
	$stmt->bindParam(':productoColorCCS', $productoColorCCS);
	$stmt->bindParam(':claveUnidadID', $claveUnidadID);
	$stmt->bindParam(':claveProdServID', $claveProdServID);
	$stmt->bindParam(':tipoProductoID', $tipoProductoID);
	$stmt->bindParam(':claveProductoEnvioSATID', $claveProductoEnvioSATID);
	$stmt->bindParam(':claveSubProductoEnvioSATID', $claveSubProductoEnvioSATID);
	$stmt->bindParam(':claveUnidadSATID', $claveUnidadSATID);
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


function Cambia($productoID,$codigoProducto,$nombreProducto,$productoRGB,$productoColorCCS,$claveUnidadID,$claveProdServID,$tipoProductoID,$claveProductoEnvioSATID,$claveSubProductoEnvioSATID,$claveUnidadSATID,$versionRegistro,$regEstado,$regFechaUltimaModificacion,$regUsuarioUltimaModificacion,$regFormularioUltimaModificacion,$regVersionUltimaModificacion){
    $query = "UPDATE " . $this->NombreTabla . " SET codigoProducto=:codigoProducto,nombreProducto=:nombreProducto,productoRGB=:productoRGB,productoColorCCS=:productoColorCCS,claveUnidadID=:claveUnidadID,claveProdServID=:claveProdServID,tipoProductoID=:tipoProductoID,claveProductoEnvioSATID=:claveProductoEnvioSATID,claveSubProductoEnvioSATID=:claveSubProductoEnvioSATID,claveUnidadSATID=:claveUnidadSATID,versionRegistro=:versionRegistro,regEstado=:regEstado,regFechaUltimaModificacion=:regFechaUltimaModificacion,regUsuarioUltimaModificacion=:regUsuarioUltimaModificacion,regFormularioUltimaModificacion=:regFormularioUltimaModificacion,regVersionUltimaModificacion=:regVersionUltimaModificacion 
		WHERE productoID=:productoID ";

    // prepare the query
    $stmt = $this->Conexion->prepare($query);
 
     // sanitize
	$productoID=htmlspecialchars(strip_tags($productoID));
	$codigoProducto=htmlspecialchars(strip_tags($codigoProducto));
	$nombreProducto=htmlspecialchars(strip_tags($nombreProducto));
	$productoRGB=htmlspecialchars(strip_tags($productoRGB));
	$productoColorCCS=htmlspecialchars(strip_tags($productoColorCCS));
	$claveUnidadID=htmlspecialchars(strip_tags($claveUnidadID));
	$claveProdServID=htmlspecialchars(strip_tags($claveProdServID));
	$tipoProductoID=htmlspecialchars(strip_tags($tipoProductoID));
	$claveProductoEnvioSATID=htmlspecialchars(strip_tags($claveProductoEnvioSATID));
	$claveSubProductoEnvioSATID=htmlspecialchars(strip_tags($claveSubProductoEnvioSATID));
	$claveUnidadSATID=htmlspecialchars(strip_tags($claveUnidadSATID));
	$versionRegistro=htmlspecialchars(strip_tags($versionRegistro));
	$regEstado=htmlspecialchars(strip_tags($regEstado));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($regFechaUltimaModificacion));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($regUsuarioUltimaModificacion));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($regFormularioUltimaModificacion));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($regVersionUltimaModificacion));
   
    // bind the values
	$stmt->bindParam(':productoID', $productoID);
	$stmt->bindParam(':codigoProducto', $codigoProducto);
	$stmt->bindParam(':nombreProducto', $nombreProducto);
	$stmt->bindParam(':productoRGB', $productoRGB);
	$stmt->bindParam(':productoColorCCS', $productoColorCCS);
	$stmt->bindParam(':claveUnidadID', $claveUnidadID);
	$stmt->bindParam(':claveProdServID', $claveProdServID);
	$stmt->bindParam(':tipoProductoID', $tipoProductoID);
	$stmt->bindParam(':claveProductoEnvioSATID', $claveProductoEnvioSATID);
	$stmt->bindParam(':claveSubProductoEnvioSATID', $claveSubProductoEnvioSATID);
	$stmt->bindParam(':claveUnidadSATID', $claveUnidadSATID);
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