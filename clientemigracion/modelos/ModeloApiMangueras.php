<?php 
class Mangueras{ 
 
private $Conexion; 
private $Database; 
private $NombreTabla = 'Mangueras'; 
 
// object properties
public $Campos;
public $Dataset;
public $Mensaje;
 
// constructor
    public function __construct($dbf){
        $this->Database = $dbf;
        $this->Conexion = $dbf->conn;
    }
	
	function ObtenerDatos($mangueraNumero=0, $bombaNumero=0, $establecimientoID=0){
    	// query to check if email exists
    	$query = "select * from " . $this->NombreTabla .  " where 1 " . ($mangueraNumero > 0 ? " and mangueraNumero = :mangueraNumero" : "") . " " . ($bombaNumero > 0 ? " and bombaNumero = :bombaNumero" : "") . " " . ($establecimientoID > 0 ? " and establecimientoID = :establecimientoID" : "");
 
    	// prepare the query
    	$stmt = $this->Conexion->prepare( $query );
 
    	 // sanitize
		if($mangueraNumero > 0){
			$mangueraNumero=htmlspecialchars(strip_tags($mangueraNumero));
		}
		if($bombaNumero > 0){
			$bombaNumero=htmlspecialchars(strip_tags($bombaNumero));
		}
		if($establecimientoID > 0){
			$establecimientoID=htmlspecialchars(strip_tags($establecimientoID));
		}
 
    	// bind given id value

    
    	 // bind the values
		if($mangueraNumero > 0){
			$stmt->bindParam(':mangueraNumero', $mangueraNumero);
		}
		if($bombaNumero > 0){
			$stmt->bindParam(':bombaNumero', $bombaNumero);
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
			" (mangueraNumero,codigoMangueraAutoridad,activoManguera,tanqueNumero,bombaNumero,medidorID,establecimientoID,versionRegistro,regEstado,regFechaUltimaModificacion,regUsuarioUltimaModificacion,regFormularioUltimaModificacion,regVersionUltimaModificacion) VALUES ";
			
		$coma = false;
		$comaText = "";
		foreach($registros as $item)
		 {
			//echo $item['textoID']."lolol";
			 if($this->ObtenerDatos($item['mangueraNumero'], $item['bombaNumero'], $item['establecimientoID'])){
				$this->Cambia($item['mangueraNumero'],($item['codigoMangueraAutoridad']),$item['activoManguera'],$item['tanqueNumero'],$item['bombaNumero'],$item['medidorID'],($item['establecimientoID']),$item['versionRegistro'],$item['regEstado'],($item['regFechaUltimaModificacion']),$item['regUsuarioUltimaModificacion'],$item['regFormularioUltimaModificacion'],$item['regVersionUltimaModificacion']);
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
				
				$consulta= $consulta . $comaText . "(" . $item["mangueraNumero"] . ",'" . ($item["codigoMangueraAutoridad"]) . "'," . $item["activoManguera"] . "," . $item["tanqueNumero"] . "," . $item["bombaNumero"] . "," . $item["medidorID"] . ",'" . ($item["establecimientoID"]) . "'," . $item["versionRegistro"] . "," . $item["regEstado"] . ",'" . ($item["regFechaUltimaModificacion"]) . "'," . $item["regUsuarioUltimaModificacion"] . "," . $item["regFormularioUltimaModificacion"] . "," . $item["regVersionUltimaModificacion"] . ")";
			}
			 
		   
		 }
	if(!$coma){
		$consulta="select 1";
	}
	
	$this->query=$consulta;
	
    // prepare the query
    $stmt = $this->Conexion->prepare($this->query);
	
    // sanitize
	$mangueraNumero=htmlspecialchars(strip_tags($item['mangueraNumero']));
	$codigoMangueraAutoridad=htmlspecialchars(strip_tags($item['codigoMangueraAutoridad']));
	$activoManguera=htmlspecialchars(strip_tags($item['activoManguera']));
	$tanqueNumero=htmlspecialchars(strip_tags($item['tanqueNumero']));
	$bombaNumero=htmlspecialchars(strip_tags($item['bombaNumero']));
	$medidorID=htmlspecialchars(strip_tags($item['medidorID']));
	$establecimientoID=htmlspecialchars(strip_tags($item['establecimientoID']));
	$versionRegistro=htmlspecialchars(strip_tags($item['versionRegistro']));
	$regEstado=htmlspecialchars(strip_tags($item['regEstado']));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($item['regFechaUltimaModificacion']));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($item['regUsuarioUltimaModificacion']));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($item['regFormularioUltimaModificacion']));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($item['regVersionUltimaModificacion']));
   
    // bind the values
	$stmt->bindParam(':mangueraNumero', $mangueraNumero);
	$stmt->bindParam(':codigoMangueraAutoridad', $codigoMangueraAutoridad);
	$activoManguera = (int)$activoManguera;
	$stmt->bindValue(':activoManguera', $activoManguera, PDO::PARAM_INT);
	$stmt->bindParam(':tanqueNumero', $tanqueNumero);
	$stmt->bindParam(':bombaNumero', $bombaNumero);
	$stmt->bindParam(':medidorID', $medidorID);
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


function Cambia($mangueraNumero,$codigoMangueraAutoridad,$activoManguera,$tanqueNumero,$bombaNumero,$medidorID,$establecimientoID,$versionRegistro,$regEstado,$regFechaUltimaModificacion,$regUsuarioUltimaModificacion,$regFormularioUltimaModificacion,$regVersionUltimaModificacion){
    $query = "UPDATE " . $this->NombreTabla . " SET codigoMangueraAutoridad=:codigoMangueraAutoridad,activoManguera=:activoManguera,tanqueNumero=:tanqueNumero,bombaNumero=:bombaNumero,medidorID=:medidorID,establecimientoID=:establecimientoID,versionRegistro=:versionRegistro,regEstado=:regEstado,regFechaUltimaModificacion=:regFechaUltimaModificacion,regUsuarioUltimaModificacion=:regUsuarioUltimaModificacion,regFormularioUltimaModificacion=:regFormularioUltimaModificacion,regVersionUltimaModificacion=:regVersionUltimaModificacion 
		WHERE mangueraNumero=:mangueraNumero and bombaNumero=:bombaNumero and establecimientoID=:establecimientoID ";

    // prepare the query
    $stmt = $this->Conexion->prepare($query);
 
     // sanitize
	$mangueraNumero=htmlspecialchars(strip_tags($mangueraNumero));
	$codigoMangueraAutoridad=htmlspecialchars(strip_tags($codigoMangueraAutoridad));
	$activoManguera=htmlspecialchars(strip_tags($activoManguera));
	$tanqueNumero=htmlspecialchars(strip_tags($tanqueNumero));
	$bombaNumero=htmlspecialchars(strip_tags($bombaNumero));
	$medidorID=htmlspecialchars(strip_tags($medidorID));
	$establecimientoID=htmlspecialchars(strip_tags($establecimientoID));
	$versionRegistro=htmlspecialchars(strip_tags($versionRegistro));
	$regEstado=htmlspecialchars(strip_tags($regEstado));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($regFechaUltimaModificacion));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($regUsuarioUltimaModificacion));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($regFormularioUltimaModificacion));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($regVersionUltimaModificacion));
   
    // bind the values
	$stmt->bindParam(':mangueraNumero', $mangueraNumero);
	$stmt->bindParam(':codigoMangueraAutoridad', $codigoMangueraAutoridad);
	$activoManguera = (int)$activoManguera;
	$stmt->bindValue(':activoManguera', $activoManguera, PDO::PARAM_INT);
	$stmt->bindParam(':tanqueNumero', $tanqueNumero);
	$stmt->bindParam(':bombaNumero', $bombaNumero);
	$stmt->bindParam(':medidorID', $medidorID);
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