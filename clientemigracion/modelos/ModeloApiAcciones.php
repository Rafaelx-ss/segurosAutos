<?php 
class Acciones{ 
 
private $Conexion; 
private $Database; 
private $NombreTabla = 'Acciones'; 
 
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
	
	function ObtenerDatos($id=0, $tipoconsulta=""){
   		// $id = intval($id);
    	// query to check if email exists
    	$query = "select * from " . $this->NombreTabla . " " . ($id > 0 ? " where accionID = ?" : "");
 
    	// prepare the query
    	$stmt = $this->Conexion->prepare( $query );
 
    	// sanitize
    	$id=htmlspecialchars(strip_tags($id));
 
    	// bind given id value

    
    	if($id > 0){
      		$stmt->bindParam(1, $id);
    	}
    	// execute the query
		
	 try{
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
	 }catch (Exception $e){
        echo $this->Mensaje2 = $e->getMessage();
     }
 
    	// return false if email does not exist in the database
    	return false;
	}
 

function Inserta($registros){

    $consulta="";
	
	$consulta = 'INSERT INTO ' . $this->NombreTabla . 
			" (accionID,nombreAccion,imagen,estadoAccion,textoID,versionRegistro,regEstado,regFechaUltimaModificacion,regUsuarioUltimaModificacion,regFormularioUltimaModificacion,regVersionUltimaModificacion) VALUES ";
			
		$coma = false;
		$comaText = "";
		foreach($registros as $item)
		 {
			//echo $item['textoID']."lolol";
			 if($this->ObtenerDatos($item['accionID'])){
				$this->Cambia($item['accionID'],$item['nombreAccion'],$item['imagen'],$item['estadoAccion'],$item['textoID'],$item['versionRegistro'],$item['regEstado'],$item['regFechaUltimaModificacion'],$item['regUsuarioUltimaModificacion'],$item['regFormularioUltimaModificacion'],$item['regVersionUltimaModificacion']);
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
				
				$consulta= $consulta . $comaText . "('" . $item["accionID"] . "','" . $item["nombreAccion"] . "','" . $item["imagen"] . "'," . $item["estadoAccion"] . ",'" . $item["textoID"] . "','" . $item["versionRegistro"] . "'," . $item["regEstado"] . ",'" . $item["regFechaUltimaModificacion"] . "','" . $item["regUsuarioUltimaModificacion"] . "','" . $item["regFormularioUltimaModificacion"] . "','" . $item["regVersionUltimaModificacion"] . "')";
			}
			 
		   
		 }
	if(!$coma){
		$consulta="select 1";
	}
	
	$this->query=$consulta;
	
    // prepare the query
    $stmt = $this->Conexion->prepare($this->query);
	
    // sanitize
	$accionID=htmlspecialchars(strip_tags($item['accionID']));
	$nombreAccion=htmlspecialchars($item['nombreAccion'], ENT_QUOTES,'UTF-8',false);
	//$nombreAccion=htmlspecialchars(strip_tags($item['nombreAccion']));
	$imagen=htmlspecialchars(strip_tags($item['imagen']));
	$estadoAccion=htmlspecialchars(strip_tags($item['estadoAccion']));
	$textoID=htmlspecialchars(strip_tags($item['textoID']));
	$versionRegistro=htmlspecialchars(strip_tags($item['versionRegistro']));
	$regEstado=htmlspecialchars(strip_tags($item['regEstado']));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($item['regFechaUltimaModificacion']));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($item['regUsuarioUltimaModificacion']));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($item['regFormularioUltimaModificacion']));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($item['regVersionUltimaModificacion']));
   
    // bind the values
	$stmt->bindParam(':accionID', $accionID);
	$stmt->bindParam(':nombreAccion', $nombreAccion);
	$stmt->bindParam(':imagen', $imagen);
	$estadoAccion = (int)$estadoAccion;
	$stmt->bindValue(':estadoAccion', $estadoAccion, PDO::PARAM_INT);
	$stmt->bindParam(':textoID', $textoID);
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
        echo $this->Mensaje2 = $e->getMessage().$consulta;
    }

  
    // execute the query, also check if query was successful
    if($stmt->execute()){
        return true;

    }
 
    return false;
}


function Cambia($accionID,$nombreAccion,$imagen,$estadoAccion,$textoID,$versionRegistro,$regEstado,$regFechaUltimaModificacion,$regUsuarioUltimaModificacion,$regFormularioUltimaModificacion,$regVersionUltimaModificacion){
    $query = "UPDATE " . $this->NombreTabla . " SET nombreAccion=:nombreAccion,imagen=:imagen,estadoAccion=:estadoAccion,textoID=:textoID,versionRegistro=:versionRegistro,regEstado=:regEstado,regFechaUltimaModificacion=:regFechaUltimaModificacion,regUsuarioUltimaModificacion=:regUsuarioUltimaModificacion,regFormularioUltimaModificacion=:regFormularioUltimaModificacion,regVersionUltimaModificacion=:regVersionUltimaModificacion 
		WHERE accionID=:accionID ";

    // prepare the query
    $stmt = $this->Conexion->prepare($query);
 
     // sanitize
	$accionID=htmlspecialchars(strip_tags($accionID));
	//$nombreAccion=htmlspecialchars(strip_tags($nombreAccion));
	$nombreAccion=htmlspecialchars($nombreAccion, ENT_QUOTES,'UTF-8',false);
	$imagen=htmlspecialchars(strip_tags($imagen));
	$estadoAccion=htmlspecialchars(strip_tags($estadoAccion));
	$textoID=htmlspecialchars(strip_tags($textoID));
	$versionRegistro=htmlspecialchars(strip_tags($versionRegistro));
	$regEstado=htmlspecialchars(strip_tags($regEstado));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($regFechaUltimaModificacion));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($regUsuarioUltimaModificacion));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($regFormularioUltimaModificacion));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($regVersionUltimaModificacion));
   
    // bind the values
	$stmt->bindParam(':accionID', $accionID);
	$stmt->bindParam(':nombreAccion', $nombreAccion);
	$stmt->bindParam(':imagen', $imagen);
	$estadoAccion = (int)$estadoAccion;
	$stmt->bindValue(':estadoAccion', $estadoAccion, PDO::PARAM_INT);
	$stmt->bindParam(':textoID', $textoID);
	$stmt->bindParam(':versionRegistro', $versionRegistro);
	$regEstado = (int)$regEstado;
	$stmt->bindValue(':regEstado', $regEstado, PDO::PARAM_INT);
	$stmt->bindParam(':regFechaUltimaModificacion', $regFechaUltimaModificacion);
	$stmt->bindParam(':regUsuarioUltimaModificacion', $regUsuarioUltimaModificacion);
	$stmt->bindParam(':regFormularioUltimaModificacion', $regFormularioUltimaModificacion);
	$stmt->bindParam(':regVersionUltimaModificacion', $regVersionUltimaModificacion);

    // execute the query, also check if query was successful
    
	try{
        // execute the query, also check if query was successful
        if($stmt->execute()){
            return true;

        }
    }catch (Exception $e){
        echo $this->Mensaje2 = $e->getMessage().$query;
    }
 
    return false;
}
	
		
}
	?>