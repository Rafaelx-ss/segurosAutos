<?php 
class EstablecimientosAnexosEnviosSAT{ 
 
private $Conexion; 
private $Database; 
private $NombreTabla = 'EstablecimientosAnexosEnviosSAT'; 
 
// object properties
public $Campos;
public $Dataset;
public $Mensaje;
 
// constructor
    public function __construct($dbf){
        $this->Database = $dbf;
        $this->Conexion = $dbf->conn;
    }
	
	function ObtenerDatos($id=0,$establecimientoID=0){
    	// query to check if email exists
    	$query = "select * from " . $this->NombreTabla .  " where 1 " . ($id > 0 ? " and establecimientoAnexoID = ?" : "");
 
    	// prepare the query
    	$stmt = $this->Conexion->prepare( $query );
 
    	
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
			" (establecimientoAnexoID,version,caracter,modalidadPermiso,numeroPermiso,claveInstalacion,descripcionInstalacion,geoLocalizacionLatitud,geoLocalizacionLongitud,establecimientoID,versionRegistro,regEstado,regFechaUltimaModificacion,regUsuarioUltimaModificacion,regFormularioUltimaModificacion,regVersionUltimaModificacion) VALUES ";
			
		$coma = false;
		$comaText = "";
		foreach($registros as $item)
		 {
			//echo $item['textoID']."lolol";
			 if($this->ObtenerDatos($item['establecimientoAnexoID'])){
				$this->Cambia($item['establecimientoAnexoID'],($item['version']),($item['caracter']),($item['modalidadPermiso']),($item['numeroPermiso']),($item['claveInstalacion']),($item['descripcionInstalacion']),($item['geoLocalizacionLatitud']),($item['geoLocalizacionLongitud']),($item['establecimientoID']),$item['versionRegistro'],$item['regEstado'],($item['regFechaUltimaModificacion']),$item['regUsuarioUltimaModificacion'],$item['regFormularioUltimaModificacion'],$item['regVersionUltimaModificacion']);
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
				
				$consulta= $consulta . $comaText . "(" . $item["establecimientoAnexoID"] . ",'" . ($item["version"]) . "','" . ($item["caracter"]) . "','" . ($item["modalidadPermiso"]) . "','" . ($item["numeroPermiso"]) . "','" . ($item["claveInstalacion"]) . "','" . ($item["descripcionInstalacion"]) . "','" . ($item["geoLocalizacionLatitud"]) . "','" . ($item["geoLocalizacionLongitud"]) . "','" . ($item["establecimientoID"]) . "'," . $item["versionRegistro"] . "," . $item["regEstado"] . ",'" . ($item["regFechaUltimaModificacion"]) . "'," . $item["regUsuarioUltimaModificacion"] . "," . $item["regFormularioUltimaModificacion"] . "," . $item["regVersionUltimaModificacion"] . ")";
			}
			 
		   
		 }
	if(!$coma){
		$consulta="select 1";
	}
	
	$this->query=$consulta;
	
    // prepare the query
    $stmt = $this->Conexion->prepare($this->query);
	
    // sanitize
	$establecimientoAnexoID=htmlspecialchars(strip_tags($item['establecimientoAnexoID']));
	$version=htmlspecialchars(strip_tags($item['version']));
	$caracter=htmlspecialchars(strip_tags($item['caracter']));
	$modalidadPermiso=htmlspecialchars(strip_tags($item['modalidadPermiso']));
	$numeroPermiso=htmlspecialchars(strip_tags($item['numeroPermiso']));
	$claveInstalacion=htmlspecialchars(strip_tags($item['claveInstalacion']));
	$descripcionInstalacion=htmlspecialchars(strip_tags($item['descripcionInstalacion']));
	$geoLocalizacionLatitud=htmlspecialchars(strip_tags($item['geoLocalizacionLatitud']));
	$geoLocalizacionLongitud=htmlspecialchars(strip_tags($item['geoLocalizacionLongitud']));
	$establecimientoID=htmlspecialchars(strip_tags($item['establecimientoID']));
	$versionRegistro=htmlspecialchars(strip_tags($item['versionRegistro']));
	$regEstado=htmlspecialchars(strip_tags($item['regEstado']));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($item['regFechaUltimaModificacion']));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($item['regUsuarioUltimaModificacion']));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($item['regFormularioUltimaModificacion']));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($item['regVersionUltimaModificacion']));
   
    // bind the values
	$stmt->bindParam(':establecimientoAnexoID', $establecimientoAnexoID);
	$stmt->bindParam(':version', $version);
	$stmt->bindParam(':caracter', $caracter);
	$stmt->bindParam(':modalidadPermiso', $modalidadPermiso);
	$stmt->bindParam(':numeroPermiso', $numeroPermiso);
	$stmt->bindParam(':claveInstalacion', $claveInstalacion);
	$stmt->bindParam(':descripcionInstalacion', $descripcionInstalacion);
	$stmt->bindParam(':geoLocalizacionLatitud', $geoLocalizacionLatitud);
	$stmt->bindParam(':geoLocalizacionLongitud', $geoLocalizacionLongitud);
	$stmt->bindParam(':establecimientoID', $establecimientoID);
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


function Cambia($establecimientoAnexoID,$version,$caracter,$modalidadPermiso,$numeroPermiso,$claveInstalacion,$descripcionInstalacion,$geoLocalizacionLatitud,$geoLocalizacionLongitud,$establecimientoID,$versionRegistro,$regEstado,$regFechaUltimaModificacion,$regUsuarioUltimaModificacion,$regFormularioUltimaModificacion,$regVersionUltimaModificacion){
    $query = "UPDATE " . $this->NombreTabla . " SET version=:version,caracter=:caracter,modalidadPermiso=:modalidadPermiso,numeroPermiso=:numeroPermiso,claveInstalacion=:claveInstalacion,descripcionInstalacion=:descripcionInstalacion,geoLocalizacionLatitud=:geoLocalizacionLatitud,geoLocalizacionLongitud=:geoLocalizacionLongitud,establecimientoID=:establecimientoID,versionRegistro=:versionRegistro,regEstado=:regEstado,regFechaUltimaModificacion=:regFechaUltimaModificacion,regUsuarioUltimaModificacion=:regUsuarioUltimaModificacion,regFormularioUltimaModificacion=:regFormularioUltimaModificacion,regVersionUltimaModificacion=:regVersionUltimaModificacion 
		WHERE establecimientoAnexoID=:establecimientoAnexoID ";

    // prepare the query
    $stmt = $this->Conexion->prepare($query);
 
     // sanitize
	$establecimientoAnexoID=htmlspecialchars(strip_tags($establecimientoAnexoID));
	$version=htmlspecialchars(strip_tags($version));
	$caracter=htmlspecialchars(strip_tags($caracter));
	$modalidadPermiso=htmlspecialchars(strip_tags($modalidadPermiso));
	$numeroPermiso=htmlspecialchars(strip_tags($numeroPermiso));
	$claveInstalacion=htmlspecialchars(strip_tags($claveInstalacion));
	$descripcionInstalacion=htmlspecialchars(strip_tags($descripcionInstalacion));
	$geoLocalizacionLatitud=htmlspecialchars(strip_tags($geoLocalizacionLatitud));
	$geoLocalizacionLongitud=htmlspecialchars(strip_tags($geoLocalizacionLongitud));
	$establecimientoID=htmlspecialchars(strip_tags($establecimientoID));
	$versionRegistro=htmlspecialchars(strip_tags($versionRegistro));
	$regEstado=htmlspecialchars(strip_tags($regEstado));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($regFechaUltimaModificacion));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($regUsuarioUltimaModificacion));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($regFormularioUltimaModificacion));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($regVersionUltimaModificacion));
   
    // bind the values
	$stmt->bindParam(':establecimientoAnexoID', $establecimientoAnexoID);
	$stmt->bindParam(':version', $version);
	$stmt->bindParam(':caracter', $caracter);
	$stmt->bindParam(':modalidadPermiso', $modalidadPermiso);
	$stmt->bindParam(':numeroPermiso', $numeroPermiso);
	$stmt->bindParam(':claveInstalacion', $claveInstalacion);
	$stmt->bindParam(':descripcionInstalacion', $descripcionInstalacion);
	$stmt->bindParam(':geoLocalizacionLatitud', $geoLocalizacionLatitud);
	$stmt->bindParam(':geoLocalizacionLongitud', $geoLocalizacionLongitud);
	$stmt->bindParam(':establecimientoID', $establecimientoID);
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