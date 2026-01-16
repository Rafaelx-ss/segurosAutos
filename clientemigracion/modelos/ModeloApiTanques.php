<?php 
class Tanques{ 
 
private $Conexion; 
private $Database; 
private $NombreTabla = 'Tanques'; 
 
// object properties
public $Campos;
public $Dataset;
public $Mensaje;
 
// constructor
    public function __construct($dbf){
        $this->Database = $dbf;
        $this->Conexion = $dbf->conn;
    }
	
	function ObtenerDatos($tanqueNumero=0, $establecimientoID=0){
    	// query to check if email exists
    	$query = "select * from " . $this->NombreTabla .  " where 1 " . ($tanqueNumero > 0 ? " and tanqueNumero = :tanqueNumero" : "") . " " . ($establecimientoID > 0 ? " and establecimientoID = :establecimientoID" : "");
 
    	// prepare the query
    	$stmt = $this->Conexion->prepare( $query );
 
    	 // sanitize
		if($tanqueNumero > 0){
			$tanqueNumero=htmlspecialchars(strip_tags($tanqueNumero));
		}
		if($establecimientoID > 0){
			$establecimientoID=htmlspecialchars(strip_tags($establecimientoID));
		}
 
    	// bind given id value

    
    	 // bind the values
		if($tanqueNumero > 0){
			$stmt->bindParam(':tanqueNumero', $tanqueNumero);
		}
		if($establecimientoID > 0){
			$stmt->bindParam(':establecimientoID', $establecimientoID);
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
			" (tanqueNumero,codigoTanqueAutoridad,tanqueCapacidadOperativa,tanqueCapacidadTotal,tanqueDescripcion,tanqueVolumenMinimo,tanqueVolumenUtil,tanqueVolumenFondaje,tanqueVigenciaCalibracion,tanqueSistemaMedicion,tanqueIncertidumbreMedicion,activoTanque,productoID,establecimientoID,versionRegistro,regEstado,regFechaUltimaModificacion,regUsuarioUltimaModificacion,regFormularioUltimaModificacion,regVersionUltimaModificacion) VALUES ";
			
		$coma = false;
		$comaText = "";
		foreach($registros as $item)
		 {
			//echo $item['textoID']."lolol";
			 if($this->ObtenerDatos($item['tanqueNumero'], $item['establecimientoID'])){
				$this->Cambia($item['tanqueNumero'],($item['codigoTanqueAutoridad']),($item['tanqueCapacidadOperativa']),($item['tanqueCapacidadTotal']),($item['tanqueDescripcion']),($item['tanqueVolumenMinimo']),($item['tanqueVolumenUtil']),($item['tanqueVolumenFondaje']),($item['tanqueVigenciaCalibracion']),($item['tanqueSistemaMedicion']),($item['tanqueIncertidumbreMedicion']),$item['activoTanque'],$item['productoID'],($item['establecimientoID']),$item['versionRegistro'],$item['regEstado'],($item['regFechaUltimaModificacion']),$item['regUsuarioUltimaModificacion'],$item['regFormularioUltimaModificacion'],$item['regVersionUltimaModificacion']);
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
				
				$consulta= $consulta . $comaText . "(" . $item["tanqueNumero"] . ",'" . ($item["codigoTanqueAutoridad"]) . "','" . ($item["tanqueCapacidadOperativa"]) . "','" . ($item["tanqueCapacidadTotal"]) . "','" . ($item["tanqueDescripcion"]) . "','" . ($item["tanqueVolumenMinimo"]) . "','" . ($item["tanqueVolumenUtil"]) . "','" . ($item["tanqueVolumenFondaje"]) . "','" . ($item["tanqueVigenciaCalibracion"]) . "','" . ($item["tanqueSistemaMedicion"]) . "','" . ($item["tanqueIncertidumbreMedicion"]) . "'," . $item["activoTanque"] . "," . $item["productoID"] . ",'" . ($item["establecimientoID"]) . "'," . $item["versionRegistro"] . "," . $item["regEstado"] . ",'" . ($item["regFechaUltimaModificacion"]) . "'," . $item["regUsuarioUltimaModificacion"] . "," . $item["regFormularioUltimaModificacion"] . "," . $item["regVersionUltimaModificacion"] . ")";
			}
			 
		   
		 }
	if(!$coma){
		$consulta="select 1";
	}
	
	$this->query=$consulta;
	
    // prepare the query
    $stmt = $this->Conexion->prepare($this->query);
	
    // sanitize
	$tanqueNumero=htmlspecialchars(strip_tags($item['tanqueNumero']));
	$codigoTanqueAutoridad=htmlspecialchars(strip_tags($item['codigoTanqueAutoridad']));
	$tanqueCapacidadOperativa=htmlspecialchars(strip_tags($item['tanqueCapacidadOperativa']));
	$tanqueCapacidadTotal=htmlspecialchars(strip_tags($item['tanqueCapacidadTotal']));
	$tanqueDescripcion=htmlspecialchars(strip_tags($item['tanqueDescripcion']));
	$tanqueVolumenMinimo=htmlspecialchars(strip_tags($item['tanqueVolumenMinimo']));
	$tanqueVolumenUtil=htmlspecialchars(strip_tags($item['tanqueVolumenUtil']));
	$tanqueVolumenFondaje=htmlspecialchars(strip_tags($item['tanqueVolumenFondaje']));
	$tanqueVigenciaCalibracion=htmlspecialchars(strip_tags($item['tanqueVigenciaCalibracion']));
	$tanqueSistemaMedicion=htmlspecialchars(strip_tags($item['tanqueSistemaMedicion']));
	$tanqueIncertidumbreMedicion=htmlspecialchars(strip_tags($item['tanqueIncertidumbreMedicion']));
	$activoTanque=htmlspecialchars(strip_tags($item['activoTanque']));
	$productoID=htmlspecialchars(strip_tags($item['productoID']));
	$establecimientoID=htmlspecialchars(strip_tags($item['establecimientoID']));
	$versionRegistro=htmlspecialchars(strip_tags($item['versionRegistro']));
	$regEstado=htmlspecialchars(strip_tags($item['regEstado']));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($item['regFechaUltimaModificacion']));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($item['regUsuarioUltimaModificacion']));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($item['regFormularioUltimaModificacion']));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($item['regVersionUltimaModificacion']));
   
    // bind the values
	$stmt->bindParam(':tanqueNumero', $tanqueNumero);
	$stmt->bindParam(':codigoTanqueAutoridad', $codigoTanqueAutoridad);
	$stmt->bindParam(':tanqueCapacidadOperativa', $tanqueCapacidadOperativa);
	$stmt->bindParam(':tanqueCapacidadTotal', $tanqueCapacidadTotal);
	$stmt->bindParam(':tanqueDescripcion', $tanqueDescripcion);
	$stmt->bindParam(':tanqueVolumenMinimo', $tanqueVolumenMinimo);
	$stmt->bindParam(':tanqueVolumenUtil', $tanqueVolumenUtil);
	$stmt->bindParam(':tanqueVolumenFondaje', $tanqueVolumenFondaje);
	$stmt->bindParam(':tanqueVigenciaCalibracion', $tanqueVigenciaCalibracion);
	$stmt->bindParam(':tanqueSistemaMedicion', $tanqueSistemaMedicion);
	$stmt->bindParam(':tanqueIncertidumbreMedicion', $tanqueIncertidumbreMedicion);
	$activoTanque = (int)$activoTanque;
	$stmt->bindValue(':activoTanque', $activoTanque, PDO::PARAM_INT);
	$stmt->bindParam(':productoID', $productoID);
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


function Cambia($tanqueNumero,$codigoTanqueAutoridad,$tanqueCapacidadOperativa,$tanqueCapacidadTotal,$tanqueDescripcion,$tanqueVolumenMinimo,$tanqueVolumenUtil,$tanqueVolumenFondaje,$tanqueVigenciaCalibracion,$tanqueSistemaMedicion,$tanqueIncertidumbreMedicion,$activoTanque,$productoID,$establecimientoID,$versionRegistro,$regEstado,$regFechaUltimaModificacion,$regUsuarioUltimaModificacion,$regFormularioUltimaModificacion,$regVersionUltimaModificacion){
    $query = "UPDATE " . $this->NombreTabla . " SET codigoTanqueAutoridad=:codigoTanqueAutoridad,tanqueCapacidadOperativa=:tanqueCapacidadOperativa,tanqueCapacidadTotal=:tanqueCapacidadTotal,tanqueDescripcion=:tanqueDescripcion,tanqueVolumenMinimo=:tanqueVolumenMinimo,tanqueVolumenUtil=:tanqueVolumenUtil,tanqueVolumenFondaje=:tanqueVolumenFondaje,tanqueVigenciaCalibracion=:tanqueVigenciaCalibracion,tanqueSistemaMedicion=:tanqueSistemaMedicion,tanqueIncertidumbreMedicion=:tanqueIncertidumbreMedicion,activoTanque=:activoTanque,productoID=:productoID,establecimientoID=:establecimientoID,versionRegistro=:versionRegistro,regEstado=:regEstado,regFechaUltimaModificacion=:regFechaUltimaModificacion,regUsuarioUltimaModificacion=:regUsuarioUltimaModificacion,regFormularioUltimaModificacion=:regFormularioUltimaModificacion,regVersionUltimaModificacion=:regVersionUltimaModificacion 
		WHERE tanqueNumero=:tanqueNumero and establecimientoID=:establecimientoID ";

    // prepare the query
    $stmt = $this->Conexion->prepare($query);
 
     // sanitize
	$tanqueNumero=htmlspecialchars(strip_tags($tanqueNumero));
	$codigoTanqueAutoridad=htmlspecialchars(strip_tags($codigoTanqueAutoridad));
	$tanqueCapacidadOperativa=htmlspecialchars(strip_tags($tanqueCapacidadOperativa));
	$tanqueCapacidadTotal=htmlspecialchars(strip_tags($tanqueCapacidadTotal));
	$tanqueDescripcion=htmlspecialchars(strip_tags($tanqueDescripcion));
	$tanqueVolumenMinimo=htmlspecialchars(strip_tags($tanqueVolumenMinimo));
	$tanqueVolumenUtil=htmlspecialchars(strip_tags($tanqueVolumenUtil));
	$tanqueVolumenFondaje=htmlspecialchars(strip_tags($tanqueVolumenFondaje));
	$tanqueVigenciaCalibracion=htmlspecialchars(strip_tags($tanqueVigenciaCalibracion));
	$tanqueSistemaMedicion=htmlspecialchars(strip_tags($tanqueSistemaMedicion));
	$tanqueIncertidumbreMedicion=htmlspecialchars(strip_tags($tanqueIncertidumbreMedicion));
	$activoTanque=htmlspecialchars(strip_tags($activoTanque));
	$productoID=htmlspecialchars(strip_tags($productoID));
	$establecimientoID=htmlspecialchars(strip_tags($establecimientoID));
	$versionRegistro=htmlspecialchars(strip_tags($versionRegistro));
	$regEstado=htmlspecialchars(strip_tags($regEstado));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($regFechaUltimaModificacion));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($regUsuarioUltimaModificacion));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($regFormularioUltimaModificacion));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($regVersionUltimaModificacion));
   
    // bind the values
	$stmt->bindParam(':tanqueNumero', $tanqueNumero);
	$stmt->bindParam(':codigoTanqueAutoridad', $codigoTanqueAutoridad);
	$stmt->bindParam(':tanqueCapacidadOperativa', $tanqueCapacidadOperativa);
	$stmt->bindParam(':tanqueCapacidadTotal', $tanqueCapacidadTotal);
	$stmt->bindParam(':tanqueDescripcion', $tanqueDescripcion);
	$stmt->bindParam(':tanqueVolumenMinimo', $tanqueVolumenMinimo);
	$stmt->bindParam(':tanqueVolumenUtil', $tanqueVolumenUtil);
	$stmt->bindParam(':tanqueVolumenFondaje', $tanqueVolumenFondaje);
	$stmt->bindParam(':tanqueVigenciaCalibracion', $tanqueVigenciaCalibracion);
	$stmt->bindParam(':tanqueSistemaMedicion', $tanqueSistemaMedicion);
	$stmt->bindParam(':tanqueIncertidumbreMedicion', $tanqueIncertidumbreMedicion);
	$activoTanque = (int)$activoTanque;
	$stmt->bindValue(':activoTanque', $activoTanque, PDO::PARAM_INT);
	$stmt->bindParam(':productoID', $productoID);
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