<?php 
class Versiones{ 
 
private $Conexion; 
private $Database; 
private $NombreTabla = 'Versiones'; 
 
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
    	$query = "select * from " . $this->NombreTabla . " " . ($id > 0 ? " where versionID = ?" : "");
		
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
				// return true because email exists in the database

				return $this->Dataset;

			}
		}catch (Exception $e){
			echo  $e->getMessage();//.'<br /> <br />Consulta: <br />'.$consulta;
		}
 
    	// return false if email does not exist in the database
    	return false;
	}
 

function Inserta($registros){

    $consulta="";
	
	$consulta = 'INSERT INTO ' . $this->NombreTabla . 
			" (versionID,version,fechaLiberacionVersion,aliasVersion,urlVersion,urlDocumentacionVersion,versionActual,aplicacionID,versionRegistro,regEstado,regFechaUltimaModificacion,regUsuarioUltimaModificacion,regFormularioUltimaModificacion,regVersionUltimaModificacion) VALUES ";
			
		$coma = false;
		$comaText = "";
		foreach($registros as $item)
		 {
			//echo $item['textoID']."lolol";
			 if($this->ObtenerDatos($item['versionID'])){
				$this->Cambia($item['versionID'],$item['version'],$item['fechaLiberacionVersion'],$item['aliasVersion'],$item['urlVersion'],$item['urlDocumentacionVersion'],$item['aplicacionID'],$item['versionRegistro'],$item['regEstado'],$item['regFechaUltimaModificacion'],$item['regUsuarioUltimaModificacion'],$item['regFormularioUltimaModificacion'],$item['regVersionUltimaModificacion']);
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
				
				$consulta= $consulta . $comaText . "(" . $item["versionID"] . ",'" . $item["version"] . "','" . $item["fechaLiberacionVersion"] . "','" . $item["aliasVersion"] . "','" . $item["urlVersion"] . "','" . $item["urlDocumentacionVersion"] . "',
				0," . $item["aplicacionID"] . "," . $item["versionRegistro"] . "," . $item["regEstado"] . ",'" . $item["regFechaUltimaModificacion"] . "'," . $item["regUsuarioUltimaModificacion"] . "," . $item["regFormularioUltimaModificacion"] . "," . $item["regVersionUltimaModificacion"] . ")";
			}
			 
		   
		 }
	if(!$coma){
		$consulta="select 1";
	}
	
	$this->query=$consulta;
	
    // prepare the query
    $stmt = $this->Conexion->prepare($this->query);
	
    // sanitize
	$versionID=htmlspecialchars(strip_tags($item['versionID']));
	$version=htmlspecialchars(strip_tags($item['version']));
	$fechaLiberacionVersion=htmlspecialchars(strip_tags($item['fechaLiberacionVersion']));
	$aliasVersion=htmlspecialchars(strip_tags($item['aliasVersion']));
	$urlVersion=htmlspecialchars(strip_tags($item['urlVersion']));
	$urlDocumentacionVersion=htmlspecialchars(strip_tags($item['urlDocumentacionVersion']));
	$aplicacionID=htmlspecialchars(strip_tags($item['aplicacionID']));
	$versionRegistro=htmlspecialchars(strip_tags($item['versionRegistro']));
	$regEstado=htmlspecialchars(strip_tags($item['regEstado']));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($item['regFechaUltimaModificacion']));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($item['regUsuarioUltimaModificacion']));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($item['regFormularioUltimaModificacion']));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($item['regVersionUltimaModificacion']));
   
    // bind the values
	$stmt->bindParam(':versionID', $versionID);
	$stmt->bindParam(':version', $version);
	$stmt->bindParam(':fechaLiberacionVersion', $fechaLiberacionVersion);
	$stmt->bindParam(':aliasVersion', $aliasVersion);
	$stmt->bindParam(':urlVersion', $urlVersion);
	$stmt->bindParam(':urlDocumentacionVersion', $urlDocumentacionVersion);
	$stmt->bindParam(':aplicacionID', $aplicacionID);
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


function Cambia($versionID,$version,$fechaLiberacionVersion,$aliasVersion,$urlVersion,$urlDocumentacionVersion,$aplicacionID,$versionRegistro,$regEstado,$regFechaUltimaModificacion,$regUsuarioUltimaModificacion,$regFormularioUltimaModificacion,$regVersionUltimaModificacion){
    $query = "UPDATE " . $this->NombreTabla . " SET version=:version,fechaLiberacionVersion=:fechaLiberacionVersion,aliasVersion=:aliasVersion,urlVersion=:urlVersion,urlDocumentacionVersion=:urlDocumentacionVersion,aplicacionID=:aplicacionID,versionRegistro=:versionRegistro,regEstado=:regEstado,regFechaUltimaModificacion=:regFechaUltimaModificacion,regUsuarioUltimaModificacion=:regUsuarioUltimaModificacion,regFormularioUltimaModificacion=:regFormularioUltimaModificacion,regVersionUltimaModificacion=:regVersionUltimaModificacion 
		WHERE versionID=:versionID ";

    // prepare the query
    $stmt = $this->Conexion->prepare($query);
 
     // sanitize
	$versionID=htmlspecialchars(strip_tags($versionID));
	$version=htmlspecialchars(strip_tags($version));
	$fechaLiberacionVersion=htmlspecialchars(strip_tags($fechaLiberacionVersion));
	$aliasVersion=htmlspecialchars(strip_tags($aliasVersion));
	$urlVersion=htmlspecialchars(strip_tags($urlVersion));
	$urlDocumentacionVersion=htmlspecialchars(strip_tags($urlDocumentacionVersion));
	$aplicacionID=htmlspecialchars(strip_tags($aplicacionID));
	$versionRegistro=htmlspecialchars(strip_tags($versionRegistro));
	$regEstado=htmlspecialchars(strip_tags($regEstado));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($regFechaUltimaModificacion));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($regUsuarioUltimaModificacion));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($regFormularioUltimaModificacion));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($regVersionUltimaModificacion));
   
    // bind the values
	$stmt->bindParam(':versionID', $versionID);
	$stmt->bindParam(':version', $version);
	$stmt->bindParam(':fechaLiberacionVersion', $fechaLiberacionVersion);
	$stmt->bindParam(':aliasVersion', $aliasVersion);
	$stmt->bindParam(':urlVersion', $urlVersion);
	$stmt->bindParam(':urlDocumentacionVersion', $urlDocumentacionVersion);
	$stmt->bindParam(':aplicacionID', $aplicacionID);
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