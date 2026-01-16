<?php 
class CatalogosBonobo{ 
 
private $Conexion; 
private $Database; 
private $NombreTabla = 'Catalogos'; 
 
// object properties
public $Campos;
public $Dataset;
 public $Mensaje;
// constructor
    public function __construct($dbf){
        $this->Database = $dbf;
        $this->Conexion = $dbf->conn;
    }
	
	function ObtenerDatos($id=0, $tipoconsulta=""){
   		// $id = intval($id);
    	// query to check if email exists
    	$query = "select * from " . $this->NombreTabla . " " . ($id > 0 ? " where catalogoID = ?" : "");
 
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
 
			if($tipoconsulta==""){
				return true;
			}
			else{
				return $this->Dataset;
			}
    	}
 
    	// return false if email does not exist in the database
    	return false;
	}
 

function Inserta($registros){

    $consulta="";
	
	$consulta = 'INSERT INTO ' . $this->NombreTabla . 
			" (catalogoID,nombreCatalogo,nombreModelo,activoCatalogo,sqlQuery,versionRegistro,regEstado,regFechaUltimaModificacion,regUsuarioUltimaModificacion,regFormularioUltimaModificacion,regVersionUltimaModificacion) VALUES ";
			
		$coma = false;
		$comaText = "";
		foreach($registros as $item)
		 {
			//echo $item['textoID']."lolol";
			 if($this->ObtenerDatos($item['catalogoID'])){
				$this->Cambia($item['catalogoID'],$item['nombreCatalogo'],$item['nombreModelo'],$item['activoCatalogo'],$item['sqlQuery'],$item['versionRegistro'],$item['regEstado'],$item['regFechaUltimaModificacion'],$item['regUsuarioUltimaModificacion'],$item['regFormularioUltimaModificacion'],$item['regVersionUltimaModificacion']);
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
				
				$consulta= $consulta . $comaText . "('" . $item["catalogoID"] . "','" . $item["nombreCatalogo"] . "','" . $item["nombreModelo"] . "','" . $item["activoCatalogo"] . "','" . $item["sqlQuery"] . "','" . $item["versionRegistro"] . "'," . $item["regEstado"] . ",'" . $item["regFechaUltimaModificacion"] . "','" . $item["regUsuarioUltimaModificacion"] . "','" . $item["regFormularioUltimaModificacion"] . "','" . $item["regVersionUltimaModificacion"] . "')";
			}
			 
		   
		 }
	if(!$coma){
		$consulta="select 1";
	}
	
	$this->query=$consulta;
	
    // prepare the query
    $stmt = $this->Conexion->prepare($this->query);
	
    // sanitize
	$catalogoID=htmlspecialchars(strip_tags($item['catalogoID']));
	$nombreCatalogo=htmlspecialchars($item['nombreCatalogo'], ENT_QUOTES,'UTF-8',false);	
	$nombreModelo=htmlspecialchars($item['nombreModelo'], ENT_QUOTES,'UTF-8',false);
	//$nombreCatalogo=htmlspecialchars(strip_tags($item['nombreCatalogo']));
	//$nombreModelo=htmlspecialchars(strip_tags($item['nombreModelo']));
	$activoCatalogo=htmlspecialchars(strip_tags($item['activoCatalogo']));
	$sqlQuery=htmlspecialchars(strip_tags($item['sqlQuery']));
	$versionRegistro=htmlspecialchars(strip_tags($item['versionRegistro']));
	$regEstado=htmlspecialchars(strip_tags($item['regEstado']));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($item['regFechaUltimaModificacion']));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($item['regUsuarioUltimaModificacion']));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($item['regFormularioUltimaModificacion']));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($item['regVersionUltimaModificacion']));
   
    // bind the values
	$stmt->bindParam(':catalogoID', $catalogoID);
	$stmt->bindParam(':nombreCatalogo', $nombreCatalogo);
	$stmt->bindParam(':nombreModelo', $nombreModelo);
	$stmt->bindParam(':activoCatalogo', $activoCatalogo);
	$stmt->bindParam(':sqlQuery', $sqlQuery);
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

  
    // execute the query, also check if query was successful
    if($stmt->execute()){
        return true;

    }
 
    return false;
}


function Cambia($catalogoID,$nombreCatalogo,$nombreModelo,$activoCatalogo,$sqlQuery,$versionRegistro,$regEstado,$regFechaUltimaModificacion,$regUsuarioUltimaModificacion,$regFormularioUltimaModificacion,$regVersionUltimaModificacion){
    $query = "UPDATE " . $this->NombreTabla . " SET nombreCatalogo=:nombreCatalogo,nombreModelo=:nombreModelo,activoCatalogo=:activoCatalogo,sqlQuery=:sqlQuery,versionRegistro=:versionRegistro,regEstado=:regEstado,regFechaUltimaModificacion=:regFechaUltimaModificacion,regUsuarioUltimaModificacion=:regUsuarioUltimaModificacion,regFormularioUltimaModificacion=:regFormularioUltimaModificacion,regVersionUltimaModificacion=:regVersionUltimaModificacion 
		WHERE catalogoID=:catalogoID ";

    // prepare the query
    $stmt = $this->Conexion->prepare($query);
 
     // sanitize
	$catalogoID=htmlspecialchars(strip_tags($catalogoID));
	//$nombreCatalogo=htmlspecialchars(strip_tags($nombreCatalogo));
	//$nombreModelo=htmlspecialchars(strip_tags($nombreModelo));
	$nombreModelo=htmlspecialchars($nombreModelo, ENT_QUOTES,'UTF-8',false);
	$nombreCatalogo=htmlspecialchars($nombreCatalogo, ENT_QUOTES,'UTF-8',false);	
	$activoCatalogo=htmlspecialchars(strip_tags($activoCatalogo));
	$sqlQuery=htmlspecialchars(strip_tags($sqlQuery));
	$versionRegistro=htmlspecialchars(strip_tags($versionRegistro));
	$regEstado=htmlspecialchars(strip_tags($regEstado));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($regFechaUltimaModificacion));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($regUsuarioUltimaModificacion));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($regFormularioUltimaModificacion));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($regVersionUltimaModificacion));
   
    // bind the values
	$stmt->bindParam(':catalogoID', $catalogoID);
	$stmt->bindParam(':nombreCatalogo', $nombreCatalogo);
	$stmt->bindParam(':nombreModelo', $nombreModelo);
	$stmt->bindParam(':activoCatalogo', $activoCatalogo);
	$stmt->bindParam(':sqlQuery', $sqlQuery);
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