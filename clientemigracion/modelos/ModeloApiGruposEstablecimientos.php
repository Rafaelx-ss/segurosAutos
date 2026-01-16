<?php 
class GruposEstablecimientos{ 
 
private $Conexion; 
private $Database; 
private $NombreTabla = 'GruposEstablecimientos'; 
 
// object properties
public $Campos;
public $Dataset;
public $Mensaje;
 
// constructor
    public function __construct($dbf){
        $this->Database = $dbf;
        $this->Conexion = $dbf->conn;
    }
	
	function ObtenerDatos( $id=0){
   		// $id = intval($id);
    	// query to check if email exists
    	$query = "select * from " . $this->NombreTabla . " " . ($id > 0 ? " where grupoID = ?" : "");
 
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
			" (grupoID,nombreGrupo,activoGrupo,distribuidorID,versionRegistro,regEstado,regFechaUltimaModificacion,regUsuarioUltimaModificacion,regFormularioUltimaModificacion,regVersionUltimaModificacion) VALUES ";
			
		$coma = false;
		$comaText = "";
		foreach($registros as $item)
		 {
			//echo $item['textoID']."lolol";
			 if($this->ObtenerDatos($item['grupoID'])){
				$this->Cambia($item['grupoID'],$item['nombreGrupo'],$item['activoGrupo'],$item['distribuidorID'],$item['versionRegistro'],$item['regEstado'],$item['regFechaUltimaModificacion'],$item['regUsuarioUltimaModificacion'],$item['regFormularioUltimaModificacion'],$item['regVersionUltimaModificacion']);
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
				
				$consulta= $consulta . $comaText . "('" . $item["grupoID"] . "','" . $item["nombreGrupo"] . "'," . $item["activoGrupo"] . ",'" . $item["distribuidorID"] . "','" . $item["versionRegistro"] . "'," . $item["regEstado"] . ",'" . $item["regFechaUltimaModificacion"] . "','" . $item["regUsuarioUltimaModificacion"] . "','" . $item["regFormularioUltimaModificacion"] . "','" . $item["regVersionUltimaModificacion"] . "')";
			}
			 
		   
		 }
	if(!$coma){
		$consulta="select 1";
	}
	
	$this->query=$consulta;
	
    // prepare the query
    $stmt = $this->Conexion->prepare($this->query);
	
    // sanitize
	$grupoID=htmlspecialchars(strip_tags($item['grupoID']));
	$nombreGrupo=htmlspecialchars($item['nombreGrupo'], ENT_QUOTES,'UTF-8',false);
	//$nombreGrupo=htmlspecialchars(strip_tags($item['nombreGrupo']));
	$activoGrupo=htmlspecialchars(strip_tags($item['activoGrupo']));
	$distribuidorID=htmlspecialchars(strip_tags($item['distribuidorID']));
	$versionRegistro=htmlspecialchars(strip_tags($item['versionRegistro']));
	$regEstado=htmlspecialchars(strip_tags($item['regEstado']));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($item['regFechaUltimaModificacion']));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($item['regUsuarioUltimaModificacion']));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($item['regFormularioUltimaModificacion']));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($item['regVersionUltimaModificacion']));
   
    // bind the values
	$stmt->bindParam(':grupoID', $grupoID);
	$stmt->bindParam(':nombreGrupo', $nombreGrupo);
	$activoGrupo = (int)$activoGrupo;
	$stmt->bindValue(':activoGrupo', $activoGrupo, PDO::PARAM_INT);
	$stmt->bindParam(':distribuidorID', $distribuidorID);
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
        echo $this->Mensaje = $e->getMessage().$consulta;
    }

  
    return false;
}


function Cambia($grupoID,$nombreGrupo,$activoGrupo,$distribuidorID,$versionRegistro,$regEstado,$regFechaUltimaModificacion,$regUsuarioUltimaModificacion,$regFormularioUltimaModificacion,$regVersionUltimaModificacion){
    $query = "UPDATE " . $this->NombreTabla . " SET nombreGrupo=:nombreGrupo,activoGrupo=:activoGrupo,distribuidorID=:distribuidorID,versionRegistro=:versionRegistro,regEstado=:regEstado,regFechaUltimaModificacion=:regFechaUltimaModificacion,regUsuarioUltimaModificacion=:regUsuarioUltimaModificacion,regFormularioUltimaModificacion=:regFormularioUltimaModificacion,regVersionUltimaModificacion=:regVersionUltimaModificacion 
		WHERE grupoID=:grupoID ";

    // prepare the query
    $stmt = $this->Conexion->prepare($query);
 
     // sanitize
	$grupoID=htmlspecialchars(strip_tags($grupoID));
	//$nombreGrupo=htmlspecialchars(strip_tags($nombreGrupo));
	$nombreGrupo=htmlspecialchars($nombreGrupo, ENT_QUOTES,'UTF-8',false);
	$activoGrupo=htmlspecialchars(strip_tags($activoGrupo));
	$distribuidorID=htmlspecialchars(strip_tags($distribuidorID));
	$versionRegistro=htmlspecialchars(strip_tags($versionRegistro));
	$regEstado=htmlspecialchars(strip_tags($regEstado));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($regFechaUltimaModificacion));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($regUsuarioUltimaModificacion));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($regFormularioUltimaModificacion));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($regVersionUltimaModificacion));
   
    // bind the values
	$stmt->bindParam(':grupoID', $grupoID);
	$stmt->bindParam(':nombreGrupo', $nombreGrupo);
	$activoGrupo = (int)$activoGrupo;
	$stmt->bindValue(':activoGrupo', $activoGrupo, PDO::PARAM_INT);
	$stmt->bindParam(':distribuidorID', $distribuidorID);
	$stmt->bindParam(':versionRegistro', $versionRegistro);
	$regEstado = (int)$regEstado;
	$stmt->bindValue(':regEstado', $regEstado, PDO::PARAM_INT);
	$stmt->bindParam(':regFechaUltimaModificacion', $regFechaUltimaModificacion);
	$stmt->bindParam(':regUsuarioUltimaModificacion', $regUsuarioUltimaModificacion);
	$stmt->bindParam(':regFormularioUltimaModificacion', $regFormularioUltimaModificacion);
	$stmt->bindParam(':regVersionUltimaModificacion', $regVersionUltimaModificacion);

    // execute the query, also check if query was successful
    if($stmt->execute()){
        return true;

    }
 
    return false;
}
	
		
}
	?>