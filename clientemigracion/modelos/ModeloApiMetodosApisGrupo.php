<?php 
class MetodosApisGrupo{ 
 
private $Conexion; 
private $Database; 
private $NombreTabla = 'MetodosApis'; 
 
// object properties
public $Campos;
public $Dataset;
public $Mensaje;
 
// constructor
    public function __construct($dbf){
        $this->Database = $dbf;
        $this->Conexion = $dbf->conn;
    }
	
	function ObtenerDatos($id=0, $aplicacionID=0, $establecimientoID=0){
   		// $id = intval($id);
    	// query to check if email exists
		$cadenaEstablecimiento="";
		if($establecimientoID<>0){
			$cadenaEstablecimiento= " and permisoEstablecimiento=1";
			
		}
    	$query = "select * from " . $this->NombreTabla . " where regEstado=1 " . $cadenaEstablecimiento . ($aplicacionID > 0 ? " and apiID in(select apiID from Apis where aplicacionID in(2,3))" : "") . " " . ($id > 0 ? " and metodoApiID = ?" : "");
 
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
			" (metodoApiID,apiID,estadoMetodo,versionRegistro,regEstado,regFechaUltimaModificacion,regUsuarioUltimaModificacion,regFormularioUltimaModificacion,regVersionUltimaModificacion,tipoMetodoApi,permisoMaster,permisoGrupoEstablecimiento,permisoEstablecimiento) VALUES ";
			
		$coma = false;
		$comaText = "";
		foreach($registros as $item)
		 {
			//echo $item['textoID']."lolol";
			 if($this->ObtenerDatos($item['metodoApiID'])){
				$this->Cambia($item['metodoApiID'],$item['apiID'],$item['estadoMetodo'],$item['versionRegistro'],utf8_decode($item['regEstado']),utf8_decode($item['regFechaUltimaModificacion']),$item['regUsuarioUltimaModificacion'],$item['regFormularioUltimaModificacion'],$item['regVersionUltimaModificacion'],$item['tipoMetodoApi'],$item['permisoMaster'],$item['permisoGrupoEstablecimiento'],$item['permisoEstablecimiento']);
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
				
				$consulta= $consulta . $comaText . "(" . $item["metodoApiID"] . "," . $item["apiID"] . "," . $item["estadoMetodo"] . "," . $item["versionRegistro"] . ",'" . utf8_decode($item["regEstado"]) . "','" . utf8_decode($item["regFechaUltimaModificacion"]) . "'," . $item["regUsuarioUltimaModificacion"] . "," . $item["regFormularioUltimaModificacion"] . "," . $item["regVersionUltimaModificacion"] . ",'" . $item["tipoMetodoApi"] . "'
				," . $item["permisoMaster"] . "," . $item["permisoGrupoEstablecimiento"] . "," . $item["permisoEstablecimiento"] . ")";
			}
			 
		   
		 }
	if(!$coma){
		$consulta="select 1";
	}
	
	$this->query=$consulta;
	
    // prepare the query
    $stmt = $this->Conexion->prepare($this->query);
	
    // sanitize
	$metodoApiID=htmlspecialchars(strip_tags($item['metodoApiID']));
	$apiID=htmlspecialchars(strip_tags($item['apiID']));
	$estadoMetodo=htmlspecialchars(strip_tags($item['estadoMetodo']));
	$versionRegistro=htmlspecialchars(strip_tags($item['versionRegistro']));
	$regEstado=htmlspecialchars(strip_tags($item['regEstado']));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($item['regFechaUltimaModificacion']));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($item['regUsuarioUltimaModificacion']));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($item['regFormularioUltimaModificacion']));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($item['regVersionUltimaModificacion']));
	$tipoMetodoApi=htmlspecialchars(strip_tags($item['tipoMetodoApi']));
   
    // bind the values
	$stmt->bindParam(':metodoApiID', $metodoApiID);
	$stmt->bindParam(':apiID', $apiID);
	$estadoMetodo = (int)$estadoMetodo;
	$stmt->bindValue(':estadoMetodo', $estadoMetodo, PDO::PARAM_INT);
	$stmt->bindParam(':versionRegistro', $versionRegistro);
	$stmt->bindParam(':regEstado', $regEstado);
	$stmt->bindParam(':regFechaUltimaModificacion', $regFechaUltimaModificacion);
	$stmt->bindParam(':regUsuarioUltimaModificacion', $regUsuarioUltimaModificacion);
	$stmt->bindParam(':regFormularioUltimaModificacion', $regFormularioUltimaModificacion);
	$stmt->bindParam(':regVersionUltimaModificacion', $regVersionUltimaModificacion);
	$stmt->bindParam(':tipoMetodoApi', $tipoMetodoApi);
   
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


function Cambia($metodoApiID,$apiID,$estadoMetodo,$versionRegistro,$regEstado,$regFechaUltimaModificacion,$regUsuarioUltimaModificacion,$regFormularioUltimaModificacion,$regVersionUltimaModificacion,$tipoMetodoApi,$permisoMaster,$permisoGrupoEstablecimiento,$permisoEstablecimiento){
    $query = "UPDATE " . $this->NombreTabla . " SET apiID=:apiID,estadoMetodo=:estadoMetodo,versionRegistro=:versionRegistro,regEstado=:regEstado,regFechaUltimaModificacion=:regFechaUltimaModificacion,regUsuarioUltimaModificacion=:regUsuarioUltimaModificacion,regFormularioUltimaModificacion=:regFormularioUltimaModificacion,regVersionUltimaModificacion=:regVersionUltimaModificacion,tipoMetodoApi=:tipoMetodoApi,
		permisoMaster=".$permisoMaster.",permisoGrupoEstablecimiento=".$permisoGrupoEstablecimiento.",permisoEstablecimiento=".$permisoEstablecimiento."
		WHERE metodoApiID=:metodoApiID ";

    // prepare the query
    $stmt = $this->Conexion->prepare($query);
 
     // sanitize
	$metodoApiID=htmlspecialchars(strip_tags($metodoApiID));
	$apiID=htmlspecialchars(strip_tags($apiID));
	$estadoMetodo=htmlspecialchars(strip_tags($estadoMetodo));
	$versionRegistro=htmlspecialchars(strip_tags($versionRegistro));
	$regEstado=htmlspecialchars(strip_tags($regEstado));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($regFechaUltimaModificacion));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($regUsuarioUltimaModificacion));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($regFormularioUltimaModificacion));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($regVersionUltimaModificacion));
	$tipoMetodoApi=htmlspecialchars(strip_tags($tipoMetodoApi));
   
    // bind the values
	$stmt->bindParam(':metodoApiID', $metodoApiID);
	$stmt->bindParam(':apiID', $apiID);
	$estadoMetodo = (int)$estadoMetodo;
	$stmt->bindValue(':estadoMetodo', $estadoMetodo, PDO::PARAM_INT);
	$stmt->bindParam(':versionRegistro', $versionRegistro);
	$stmt->bindParam(':regEstado', $regEstado);
	$stmt->bindParam(':regFechaUltimaModificacion', $regFechaUltimaModificacion);
	$stmt->bindParam(':regUsuarioUltimaModificacion', $regUsuarioUltimaModificacion);
	$stmt->bindParam(':regFormularioUltimaModificacion', $regFormularioUltimaModificacion);
	$stmt->bindParam(':regVersionUltimaModificacion', $regVersionUltimaModificacion);
	$stmt->bindParam(':tipoMetodoApi', $tipoMetodoApi);

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