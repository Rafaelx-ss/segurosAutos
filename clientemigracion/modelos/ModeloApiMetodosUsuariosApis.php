<?php 
class MetodosUsuariosApis{ 
 
private $Conexion; 
private $Database; 
private $NombreTabla = 'MetodosUsuariosApis'; 
 
// object properties
public $Campos;
public $Dataset;
public $Mensaje;
 
// constructor
    public function __construct($dbf){
        $this->Database = $dbf;
        $this->Conexion = $dbf->conn;
    }
	
	function ObtenerDatos($usuarioApiID=0, $metodoApiID=0){
   		// $id = intval($id);
    	// query to check if email exists
    	$query = "select * from " . $this->NombreTabla . " " . ($usuarioApiID > 0 ? " where usuarioApiID = ?" : "") . " " . ($metodoApiID > 0 ? " and metodoApiID = ?" : "");
 
    	// prepare the query
    	$stmt = $this->Conexion->prepare( $query );
 
    	// sanitize
    	$usuarioApiID=htmlspecialchars(strip_tags($usuarioApiID));
    	$metodoApiID=htmlspecialchars(strip_tags($metodoApiID));
 
    	// bind given id value

    
    	if($usuarioApiID > 0){
      		$stmt->bindParam(1, $usuarioApiID);
			if($metodoApiID > 0){
				$stmt->bindParam(2, $metodoApiID);
			}
    	}elseif($metodoApiID > 0){
			$stmt->bindParam(1, $metodoApiID);
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
			" (usuarioApiID,metodoApiID,estadoDetalleApi,versionRegistro,regEstado,regFechaUltimaModificacion,regUsuarioUltimaModificacion,regFormularioUltimaModificacion,regVersionUltimaModificacion) VALUES ";
			
		$coma = false;
		$comaText = "";
		foreach($registros as $item)
		 {
			//echo $item['textoID']."lolol";
			 if($this->ObtenerDatos($item['usuarioApiID'], $item['metodoApiID'])){
				$this->Cambia($item['usuarioApiID'],$item['metodoApiID'],$item['estadoDetalleApi'],$item['versionRegistro'],utf8_decode($item['regEstado']),utf8_decode($item['regFechaUltimaModificacion']),$item['regUsuarioUltimaModificacion'],$item['regFormularioUltimaModificacion'],$item['regVersionUltimaModificacion']);
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
				
				$consulta= $consulta . $comaText . "(" . $item["usuarioApiID"] . "," . $item["metodoApiID"] . "," . $item["estadoDetalleApi"] . "," . $item["versionRegistro"] . ",'" . utf8_decode($item["regEstado"]) . "','" . utf8_decode($item["regFechaUltimaModificacion"]) . "'," . $item["regUsuarioUltimaModificacion"] . "," . $item["regFormularioUltimaModificacion"] . "," . $item["regVersionUltimaModificacion"] . ")";
			}
			 
		   
		 }
	if(!$coma){
		$consulta="select 1";
	}
	
	$this->query=$consulta;
	
    // prepare the query
    $stmt = $this->Conexion->prepare($this->query);
	
    // sanitize
	$usuarioApiID=htmlspecialchars(strip_tags($item['usuarioApiID']));
	$metodoApiID=htmlspecialchars(strip_tags($item['metodoApiID']));
	$estadoDetalleApi=htmlspecialchars(strip_tags($item['estadoDetalleApi']));
	$versionRegistro=htmlspecialchars(strip_tags($item['versionRegistro']));
	$regEstado=htmlspecialchars(strip_tags($item['regEstado']));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($item['regFechaUltimaModificacion']));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($item['regUsuarioUltimaModificacion']));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($item['regFormularioUltimaModificacion']));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($item['regVersionUltimaModificacion']));
   
    // bind the values
	$stmt->bindParam(':usuarioApiID', $usuarioApiID);
	$stmt->bindParam(':metodoApiID', $metodoApiID);
	$estadoDetalleApi = (int)$estadoDetalleApi;
	$stmt->bindValue(':estadoDetalleApi', $estadoDetalleApi, PDO::PARAM_INT);
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
        echo $this->Mensaje = $e->getMessage().'<br /> <br />Consulta: <br />'.$consulta;
    }

 
    return false;
}


function Cambia($usuarioApiID,$metodoApiID,$estadoDetalleApi,$versionRegistro,$regEstado,$regFechaUltimaModificacion,$regUsuarioUltimaModificacion,$regFormularioUltimaModificacion,$regVersionUltimaModificacion){
    $query = "UPDATE " . $this->NombreTabla . " SET metodoApiID=:metodoApiID,estadoDetalleApi=:estadoDetalleApi,versionRegistro=:versionRegistro,regEstado=:regEstado,regFechaUltimaModificacion=:regFechaUltimaModificacion,regUsuarioUltimaModificacion=:regUsuarioUltimaModificacion,regFormularioUltimaModificacion=:regFormularioUltimaModificacion,regVersionUltimaModificacion=:regVersionUltimaModificacion 
		WHERE usuarioApiID=:usuarioApiID and metodoApiID=:metodoApiID ";

    // prepare the query
    $stmt = $this->Conexion->prepare($query);
 
     // sanitize
	$usuarioApiID=htmlspecialchars(strip_tags($usuarioApiID));
	$metodoApiID=htmlspecialchars(strip_tags($metodoApiID));
	$estadoDetalleApi=htmlspecialchars(strip_tags($estadoDetalleApi));
	$versionRegistro=htmlspecialchars(strip_tags($versionRegistro));
	$regEstado=htmlspecialchars(strip_tags($regEstado));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($regFechaUltimaModificacion));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($regUsuarioUltimaModificacion));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($regFormularioUltimaModificacion));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($regVersionUltimaModificacion));
   
    // bind the values
	$stmt->bindParam(':usuarioApiID', $usuarioApiID);
	$stmt->bindParam(':metodoApiID', $metodoApiID);
	$estadoDetalleApi = (int)$estadoDetalleApi;
	$stmt->bindValue(':estadoDetalleApi', $estadoDetalleApi, PDO::PARAM_INT);
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