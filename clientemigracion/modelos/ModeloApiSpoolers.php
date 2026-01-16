<?php 
class Spoolers{ 
 
private $Conexion; 
private $Database; 
private $NombreTabla = 'Spoolers'; 
 
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
   		
		$consulta="";
		if($establecimientoID>0){
			$consulta= " and establecimientoID = " . $establecimientoID;
		}
    	// query to check if email exists
    	$query = "select * from " . $this->NombreTabla .  " where 1 " . $consulta . ($id > 0 ? " and spoolerID = ?" : "");
 
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
			" (spoolerID,nombreSpooler,listaImpresoras,intervaloConsulta,establecimientoID,versionRegistro,regEstado,regFechaUltimaModificacion,regUsuarioUltimaModificacion,regFormularioUltimaModificacion,regVersionUltimaModificacion) VALUES ";
			
		$coma = false;
		$comaText = "";
		foreach($registros as $item)
		 {
			//echo $item['textoID']."lolol";
			 if($this->ObtenerDatos($item['spoolerID'])){
				$this->Cambia($item['spoolerID'],($item['nombreSpooler']),($item['listaImpresoras']),($item['intervaloConsulta']),($item['establecimientoID']),$item['versionRegistro'],($item['regEstado']),($item['regFechaUltimaModificacion']),$item['regUsuarioUltimaModificacion'],$item['regFormularioUltimaModificacion'],$item['regVersionUltimaModificacion']);
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
				
				$CampoNull=$item["intervaloConsulta"];
				if($item["intervaloConsulta"]==" " or trim($item["intervaloConsulta"]) == ""){
					$CampoNull='NULL';
				}
				ELSE{
					$CampoNull="'".$item["intervaloConsulta"]."'";
				}
				$consulta= $consulta . $comaText . "(" . $item["spoolerID"] . ",'" . ($item["nombreSpooler"]) . "','" . ($item["listaImpresoras"]) . "',
				" . $CampoNull . ",'" . ($item["establecimientoID"]) . "'," . $item["versionRegistro"] . ",'" . ($item["regEstado"]) . "','" . ($item["regFechaUltimaModificacion"]) . "'," . $item["regUsuarioUltimaModificacion"] . "," . $item["regFormularioUltimaModificacion"] . "," . $item["regVersionUltimaModificacion"] . ")";
			}
			 
		   
		 }
	if(!$coma){
		$consulta="select 1";
	}
	
	$this->query=$consulta;
	
    // prepare the query
    $stmt = $this->Conexion->prepare($this->query);
	
    // sanitize
	$spoolerID=htmlspecialchars(strip_tags($item['spoolerID']));
	$nombreSpooler=htmlspecialchars(strip_tags($item['nombreSpooler']));
	$listaImpresoras=htmlspecialchars(strip_tags($item['listaImpresoras']));
	if($item["intervaloConsulta"] != " " and trim($item["intervaloConsulta"]) != ""){
		$intervaloConsulta=htmlspecialchars(strip_tags($item['intervaloConsulta']));
	}
	$establecimientoID=htmlspecialchars(strip_tags($item['establecimientoID']));
	$versionRegistro=htmlspecialchars(strip_tags($item['versionRegistro']));
	$regEstado=htmlspecialchars(strip_tags($item['regEstado']));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($item['regFechaUltimaModificacion']));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($item['regUsuarioUltimaModificacion']));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($item['regFormularioUltimaModificacion']));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($item['regVersionUltimaModificacion']));
   
    // bind the values
	$stmt->bindParam(':spoolerID', $spoolerID);
	$stmt->bindParam(':nombreSpooler', $nombreSpooler);
	$stmt->bindParam(':listaImpresoras', $listaImpresoras);
	if($item["intervaloConsulta"] != " " and trim($item["intervaloConsulta"]) != ""){
		$stmt->bindParam(':intervaloConsulta', $intervaloConsulta);
	}
	$stmt->bindParam(':establecimientoID', $establecimientoID);
	$stmt->bindParam(':versionRegistro', $versionRegistro);
	$stmt->bindParam(':regEstado', $regEstado);
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


function Cambia($spoolerID,$nombreSpooler,$listaImpresoras,$intervaloConsulta,$establecimientoID,$versionRegistro,$regEstado,$regFechaUltimaModificacion,$regUsuarioUltimaModificacion,$regFormularioUltimaModificacion,$regVersionUltimaModificacion){
    if($intervaloConsulta != " " and trim($intervaloConsulta) != ""){
		$CampoNull=",intervaloConsulta=:intervaloConsulta";
	}
	else {
		$CampoNull="";
	}
	$query = "UPDATE " . $this->NombreTabla . " SET nombreSpooler=:nombreSpooler,listaImpresoras=:listaImpresoras".$CampoNull.",establecimientoID=:establecimientoID,versionRegistro=:versionRegistro,regEstado=:regEstado,regFechaUltimaModificacion=:regFechaUltimaModificacion,regUsuarioUltimaModificacion=:regUsuarioUltimaModificacion,regFormularioUltimaModificacion=:regFormularioUltimaModificacion,regVersionUltimaModificacion=:regVersionUltimaModificacion 
		WHERE spoolerID=:spoolerID ";

    // prepare the query
    $stmt = $this->Conexion->prepare($query);
 
     // sanitize
	$spoolerID=htmlspecialchars(strip_tags($spoolerID));
	$nombreSpooler=htmlspecialchars(strip_tags($nombreSpooler));
	$listaImpresoras=htmlspecialchars(strip_tags($listaImpresoras));
	if($intervaloConsulta != " " and trim($intervaloConsulta) != ""){
		$intervaloConsulta=htmlspecialchars(strip_tags($intervaloConsulta));
	}
	$establecimientoID=htmlspecialchars(strip_tags($establecimientoID));
	$versionRegistro=htmlspecialchars(strip_tags($versionRegistro));
	$regEstado=htmlspecialchars(strip_tags($regEstado));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($regFechaUltimaModificacion));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($regUsuarioUltimaModificacion));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($regFormularioUltimaModificacion));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($regVersionUltimaModificacion));
   
    // bind the values
	$stmt->bindParam(':spoolerID', $spoolerID);
	$stmt->bindParam(':nombreSpooler', $nombreSpooler);
	$stmt->bindParam(':listaImpresoras', $listaImpresoras);
	if($intervaloConsulta != " " and trim($intervaloConsulta) != ""){
		$stmt->bindParam(':intervaloConsulta', $intervaloConsulta);
	}
	$stmt->bindParam(':establecimientoID', $establecimientoID);
	$stmt->bindParam(':versionRegistro', $versionRegistro);
	$stmt->bindParam(':regEstado', $regEstado);
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