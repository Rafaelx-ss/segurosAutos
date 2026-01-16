<?php 
class Proveedores{ 
 
private $Conexion; 
private $Database; 
private $NombreTabla = 'Proveedores'; 
 
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
    	$query = "select * from " . $this->NombreTabla .  " where 1 " . ($id > 0 ? " and proveedorID = ?" : "");
 
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
			" (proveedorID,razonSocial,nombreComercial,codigoProveedor,rfcProveedor,permisoAlmacenamientoYDistribucion,permisoTransporte,tipoProveedor,permisoImportacion,permisoProveedor,tipoProveedorID,versionRegistro,regEstado,regFechaUltimaModificacion,regUsuarioUltimaModificacion,regFormularioUltimaModificacion,regVersionUltimaModificacion) VALUES ";
			
		$coma = false;
		$comaText = "";
		foreach($registros as $item)
		 {
			//echo $item['textoID']."lolol";
			 if($this->ObtenerDatos($item['proveedorID'])){
				$this->Cambia($item['proveedorID'],($item['razonSocial']),($item['nombreComercial']),$item['codigoProveedor'],($item['rfcProveedor']),($item['permisoAlmacenamientoYDistribucion']),($item['permisoTransporte']),($item['tipoProveedor']),($item['permisoImportacion']),($item['permisoProveedor']),$item['tipoProveedorID'],$item['versionRegistro'],$item['regEstado'],($item['regFechaUltimaModificacion']),$item['regUsuarioUltimaModificacion'],$item['regFormularioUltimaModificacion'],$item['regVersionUltimaModificacion']);
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
				
				$consulta= $consulta . $comaText . "(" . $item["proveedorID"] . ",'" . ($item["razonSocial"]) . "','" . ($item["nombreComercial"]) . "'," . $item["codigoProveedor"] . ",'" . ($item["rfcProveedor"]) . "','" . ($item["permisoAlmacenamientoYDistribucion"]) . "','" . ($item["permisoTransporte"]) . "','" . ($item["tipoProveedor"]) . "','" . ($item["permisoImportacion"]) . "','" . ($item["permisoProveedor"]) . "'," . $item["tipoProveedorID"] . "," . $item["versionRegistro"] . "," . $item["regEstado"] . ",'" . ($item["regFechaUltimaModificacion"]) . "'," . $item["regUsuarioUltimaModificacion"] . "," . $item["regFormularioUltimaModificacion"] . "," . $item["regVersionUltimaModificacion"] . ")";
			}
			 
		   
		 }
	if(!$coma){
		$consulta="select 1";
	}
	
	$this->query=$consulta;
	
    // prepare the query
    $stmt = $this->Conexion->prepare($this->query);
	
    // sanitize
	$proveedorID=htmlspecialchars(strip_tags($item['proveedorID']));
	$razonSocial=htmlspecialchars(strip_tags($item['razonSocial']));
	$nombreComercial=htmlspecialchars(strip_tags($item['nombreComercial']));
	$codigoProveedor=htmlspecialchars(strip_tags($item['codigoProveedor']));
	$rfcProveedor=htmlspecialchars(strip_tags($item['rfcProveedor']));
	$permisoAlmacenamientoYDistribucion=htmlspecialchars(strip_tags($item['permisoAlmacenamientoYDistribucion']));
	$permisoTransporte=htmlspecialchars(strip_tags($item['permisoTransporte']));
	$tipoProveedor=htmlspecialchars(strip_tags($item['tipoProveedor']));
	$permisoImportacion=htmlspecialchars(strip_tags($item['permisoImportacion']));
	$permisoProveedor=htmlspecialchars(strip_tags($item['permisoProveedor']));
	$tipoProveedorID=htmlspecialchars(strip_tags($item['tipoProveedorID']));
	$versionRegistro=htmlspecialchars(strip_tags($item['versionRegistro']));
	$regEstado=htmlspecialchars(strip_tags($item['regEstado']));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($item['regFechaUltimaModificacion']));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($item['regUsuarioUltimaModificacion']));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($item['regFormularioUltimaModificacion']));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($item['regVersionUltimaModificacion']));
   
    // bind the values
	$stmt->bindParam(':proveedorID', $proveedorID);
	$stmt->bindParam(':razonSocial', $razonSocial);
	$stmt->bindParam(':nombreComercial', $nombreComercial);
	$stmt->bindParam(':codigoProveedor', $codigoProveedor);
	$stmt->bindParam(':rfcProveedor', $rfcProveedor);
	$stmt->bindParam(':permisoAlmacenamientoYDistribucion', $permisoAlmacenamientoYDistribucion);
	$stmt->bindParam(':permisoTransporte', $permisoTransporte);
	$stmt->bindParam(':tipoProveedor', $tipoProveedor);
	$stmt->bindParam(':permisoImportacion', $permisoImportacion);
	$stmt->bindParam(':permisoProveedor', $permisoProveedor);
	$stmt->bindParam(':tipoProveedorID', $tipoProveedorID);
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


function Cambia($proveedorID,$razonSocial,$nombreComercial,$codigoProveedor,$rfcProveedor,$permisoAlmacenamientoYDistribucion,$permisoTransporte,$tipoProveedor,$permisoImportacion,$permisoProveedor,$tipoProveedorID,$versionRegistro,$regEstado,$regFechaUltimaModificacion,$regUsuarioUltimaModificacion,$regFormularioUltimaModificacion,$regVersionUltimaModificacion){
    $query = "UPDATE " . $this->NombreTabla . " SET razonSocial=:razonSocial,nombreComercial=:nombreComercial,codigoProveedor=:codigoProveedor,rfcProveedor=:rfcProveedor,permisoAlmacenamientoYDistribucion=:permisoAlmacenamientoYDistribucion,permisoTransporte=:permisoTransporte,tipoProveedor=:tipoProveedor,permisoImportacion=:permisoImportacion,permisoProveedor=:permisoProveedor,tipoProveedorID=:tipoProveedorID,versionRegistro=:versionRegistro,regEstado=:regEstado,regFechaUltimaModificacion=:regFechaUltimaModificacion,regUsuarioUltimaModificacion=:regUsuarioUltimaModificacion,regFormularioUltimaModificacion=:regFormularioUltimaModificacion,regVersionUltimaModificacion=:regVersionUltimaModificacion 
		WHERE proveedorID=:proveedorID ";

    // prepare the query
    $stmt = $this->Conexion->prepare($query);
 
     // sanitize
	$proveedorID=htmlspecialchars(strip_tags($proveedorID));
	$razonSocial=htmlspecialchars(strip_tags($razonSocial));
	$nombreComercial=htmlspecialchars(strip_tags($nombreComercial));
	$codigoProveedor=htmlspecialchars(strip_tags($codigoProveedor));
	$rfcProveedor=htmlspecialchars(strip_tags($rfcProveedor));
	$permisoAlmacenamientoYDistribucion=htmlspecialchars(strip_tags($permisoAlmacenamientoYDistribucion));
	$permisoTransporte=htmlspecialchars(strip_tags($permisoTransporte));
	$tipoProveedor=htmlspecialchars(strip_tags($tipoProveedor));
	$permisoImportacion=htmlspecialchars(strip_tags($permisoImportacion));
	$permisoProveedor=htmlspecialchars(strip_tags($permisoProveedor));
	$tipoProveedorID=htmlspecialchars(strip_tags($tipoProveedorID));
	$versionRegistro=htmlspecialchars(strip_tags($versionRegistro));
	$regEstado=htmlspecialchars(strip_tags($regEstado));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($regFechaUltimaModificacion));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($regUsuarioUltimaModificacion));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($regFormularioUltimaModificacion));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($regVersionUltimaModificacion));
   
    // bind the values
	$stmt->bindParam(':proveedorID', $proveedorID);
	$stmt->bindParam(':razonSocial', $razonSocial);
	$stmt->bindParam(':nombreComercial', $nombreComercial);
	$stmt->bindParam(':codigoProveedor', $codigoProveedor);
	$stmt->bindParam(':rfcProveedor', $rfcProveedor);
	$stmt->bindParam(':permisoAlmacenamientoYDistribucion', $permisoAlmacenamientoYDistribucion);
	$stmt->bindParam(':permisoTransporte', $permisoTransporte);
	$stmt->bindParam(':tipoProveedor', $tipoProveedor);
	$stmt->bindParam(':permisoImportacion', $permisoImportacion);
	$stmt->bindParam(':permisoProveedor', $permisoProveedor);
	$stmt->bindParam(':tipoProveedorID', $tipoProveedorID);
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