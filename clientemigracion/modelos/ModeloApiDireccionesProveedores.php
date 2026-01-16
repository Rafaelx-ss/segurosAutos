<?php 
class DireccionesProveedores{ 
 
private $Conexion; 
private $Database; 
private $NombreTabla = 'DireccionesProveedores'; 
 
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
	
	function ObtenerDatos($id=0,$establecimientoID=0){
    	// query to check if email exists
    	try{
		$query = "select * from " . $this->NombreTabla .  " where 1 " . ($id > 0 ? " and direccionProveedorID = ?" : "");
 
    	// prepare the query
    	$stmt = $this->Conexion->prepare( $query );
 		$this->Mensaje2=$query;
    	
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
    	}catch (Exception $e){
			$this->Mensaje = $e->getMessage();
			return false;//.'<br /> <br />Consulta: <br />'.$consulta;
		}
    	
	}
 

function Inserta($registros){
	try{
		$consulta="";
	
	$consulta = 'INSERT INTO ' . $this->NombreTabla . 
			" (direccionProveedorID,alias,calle,numeroInterior,numeroExterior,codigoPostal,colonia,localidad,referencia,municipio,esDefault,estadoID,activoDireccion,proveedorID,versionRegistro,regEstado,regFechaUltimaModificacion,regUsuarioUltimaModificacion,regFormularioUltimaModificacion,regVersionUltimaModificacion) VALUES ";
			
		$coma = false;
		$comaText = "";
		foreach($registros as $item)
		 {
			//echo $item['textoID']."lolol";
			 if($this->ObtenerDatos($item['direccionProveedorID'])){
				$this->Cambia($item['direccionProveedorID'],($item['alias']),($item['calle']),($item['numeroInterior']),($item['numeroExterior']),($item['codigoPostal']),($item['colonia']),($item['localidad']),($item['referencia']),($item['municipio']),$item['esDefault'],$item['estadoID'],$item['activoDireccion'],$item['proveedorID'],$item['versionRegistro'],$item['regEstado'],($item['regFechaUltimaModificacion']),$item['regUsuarioUltimaModificacion'],$item['regFormularioUltimaModificacion'],$item['regVersionUltimaModificacion']);
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
				
				$consulta= $consulta . $comaText . "(" . $item["direccionProveedorID"] . ",'" . ($item["alias"]) . "','" . ($item["calle"]) . "','" . ($item["numeroInterior"]) . "','" . ($item["numeroExterior"]) . "','" . ($item["codigoPostal"]) . "','" . ($item["colonia"]) . "','" . ($item["localidad"]) . "','" . ($item["referencia"]) . "','" . ($item["municipio"]) . "'," . $item["esDefault"] . "," . $item["estadoID"] . "," . $item["activoDireccion"] . "," . $item["proveedorID"] . "," . $item["versionRegistro"] . "," . $item["regEstado"] . ",'" . ($item["regFechaUltimaModificacion"]) . "'," . $item["regUsuarioUltimaModificacion"] . "," . $item["regFormularioUltimaModificacion"] . "," . $item["regVersionUltimaModificacion"] . ")";
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
	$direccionProveedorID=htmlspecialchars(strip_tags($item['direccionProveedorID']));
	$alias=htmlspecialchars(strip_tags($item['alias']));
	$calle=htmlspecialchars(strip_tags($item['calle']));
	$numeroInterior=htmlspecialchars(strip_tags($item['numeroInterior']));
	$numeroExterior=htmlspecialchars(strip_tags($item['numeroExterior']));
	$codigoPostal=htmlspecialchars(strip_tags($item['codigoPostal']));
	$colonia=htmlspecialchars(strip_tags($item['colonia']));
	$localidad=htmlspecialchars(strip_tags($item['localidad']));
	$referencia=htmlspecialchars(strip_tags($item['referencia']));
	$municipio=htmlspecialchars(strip_tags($item['municipio']));
	$esDefault=htmlspecialchars(strip_tags($item['esDefault']));
	$estadoID=htmlspecialchars(strip_tags($item['estadoID']));
	$activoDireccion=htmlspecialchars(strip_tags($item['activoDireccion']));
	$proveedorID=htmlspecialchars(strip_tags($item['proveedorID']));
	$versionRegistro=htmlspecialchars(strip_tags($item['versionRegistro']));
	$regEstado=htmlspecialchars(strip_tags($item['regEstado']));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($item['regFechaUltimaModificacion']));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($item['regUsuarioUltimaModificacion']));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($item['regFormularioUltimaModificacion']));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($item['regVersionUltimaModificacion']));
   
    // bind the values
	$stmt->bindParam(':direccionProveedorID', $direccionProveedorID);
	$stmt->bindParam(':alias', $alias);
	$stmt->bindParam(':calle', $calle);
	$stmt->bindParam(':numeroInterior', $numeroInterior);
	$stmt->bindParam(':numeroExterior', $numeroExterior);
	$stmt->bindParam(':codigoPostal', $codigoPostal);
	$stmt->bindParam(':colonia', $colonia);
	$stmt->bindParam(':localidad', $localidad);
	$stmt->bindParam(':referencia', $referencia);
	$stmt->bindParam(':municipio', $municipio);
	$esDefault = (int)$esDefault;
	$stmt->bindValue(':esDefault', $esDefault, PDO::PARAM_INT);
	$stmt->bindParam(':estadoID', $estadoID);
	$activoDireccion = (int)$activoDireccion;
	$stmt->bindValue(':activoDireccion', $activoDireccion, PDO::PARAM_INT);
	$stmt->bindParam(':proveedorID', $proveedorID);
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


function Cambia($direccionProveedorID,$alias,$calle,$numeroInterior,$numeroExterior,$codigoPostal,$colonia,$localidad,$referencia,$municipio,$esDefault,$estadoID,$activoDireccion,$proveedorID,$versionRegistro,$regEstado,$regFechaUltimaModificacion,$regUsuarioUltimaModificacion,$regFormularioUltimaModificacion,$regVersionUltimaModificacion){
	try{
$query = "UPDATE " . $this->NombreTabla . " SET alias=:alias,calle=:calle,numeroInterior=:numeroInterior,numeroExterior=:numeroExterior,codigoPostal=:codigoPostal,colonia=:colonia,localidad=:localidad,referencia=:referencia,municipio=:municipio,esDefault=:esDefault,estadoID=:estadoID,activoDireccion=:activoDireccion,proveedorID=:proveedorID,versionRegistro=:versionRegistro,regEstado=:regEstado,regFechaUltimaModificacion=:regFechaUltimaModificacion,regUsuarioUltimaModificacion=:regUsuarioUltimaModificacion,regFormularioUltimaModificacion=:regFormularioUltimaModificacion,regVersionUltimaModificacion=:regVersionUltimaModificacion 
		WHERE direccionProveedorID=:direccionProveedorID ";

    // prepare the query
    $stmt = $this->Conexion->prepare($query);
 	$this->Mensaje2=$query;
     // sanitize
	$direccionProveedorID=htmlspecialchars(strip_tags($direccionProveedorID));
	$alias=htmlspecialchars(strip_tags($alias));
	$calle=htmlspecialchars(strip_tags($calle));
	$numeroInterior=htmlspecialchars(strip_tags($numeroInterior));
	$numeroExterior=htmlspecialchars(strip_tags($numeroExterior));
	$codigoPostal=htmlspecialchars(strip_tags($codigoPostal));
	$colonia=htmlspecialchars(strip_tags($colonia));
	$localidad=htmlspecialchars(strip_tags($localidad));
	$referencia=htmlspecialchars(strip_tags($referencia));
	$municipio=htmlspecialchars(strip_tags($municipio));
	$esDefault=htmlspecialchars(strip_tags($esDefault));
	$estadoID=htmlspecialchars(strip_tags($estadoID));
	$activoDireccion=htmlspecialchars(strip_tags($activoDireccion));
	$proveedorID=htmlspecialchars(strip_tags($proveedorID));
	$versionRegistro=htmlspecialchars(strip_tags($versionRegistro));
	$regEstado=htmlspecialchars(strip_tags($regEstado));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($regFechaUltimaModificacion));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($regUsuarioUltimaModificacion));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($regFormularioUltimaModificacion));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($regVersionUltimaModificacion));
   
    // bind the values
	$stmt->bindParam(':direccionProveedorID', $direccionProveedorID);
	$stmt->bindParam(':alias', $alias);
	$stmt->bindParam(':calle', $calle);
	$stmt->bindParam(':numeroInterior', $numeroInterior);
	$stmt->bindParam(':numeroExterior', $numeroExterior);
	$stmt->bindParam(':codigoPostal', $codigoPostal);
	$stmt->bindParam(':colonia', $colonia);
	$stmt->bindParam(':localidad', $localidad);
	$stmt->bindParam(':referencia', $referencia);
	$stmt->bindParam(':municipio', $municipio);
	$esDefault = (int)$esDefault;
	$stmt->bindValue(':esDefault', $esDefault, PDO::PARAM_INT);
	$stmt->bindParam(':estadoID', $estadoID);
	$activoDireccion = (int)$activoDireccion;
	$stmt->bindValue(':activoDireccion', $activoDireccion, PDO::PARAM_INT);
	$stmt->bindParam(':proveedorID', $proveedorID);
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