<?php
 
class CombosAnidadosBonobo{ 
 
private $Conexion; 
private $Database; 
private $NombreTabla = 'CombosAnidados'; 
 
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
    	$query = "select * from " . $this->NombreTabla . " " . ($id > 0 ? " where comboAnidadoID = ?" : "");
 
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
	//print_r($registros);
	//return true;

    $consulta="";
	
	$consulta = 'INSERT INTO ' . $this->NombreTabla . 
			" (comboAnidadoID,catalogoID,campoIDPadre,campoIDdependiente,controlQuery,queryValue,queryText,parametrosQuery,versionRegistro,activoCombo,regEstado, regFechaUltimaModificacion,regUsuarioUltimaModificacion,regFormularioUltimaModificacion,regVersionUltimaModificacion) VALUES ";
			
		$coma = false;
		$comaText = "";
		foreach($registros as $item)
		 {
			//echo $item['textoID']."lolol";
			 if($this->ObtenerDatos($item['comboAnidadoID'])){
				$this->Cambia($item['comboAnidadoID'],$item['catalogoID'],$item['campoIDPadre'],$item['campoIDdependiente'],utf8_decode($item['controlQuery']),utf8_decode($item['queryValue']),utf8_decode($item['queryText']),utf8_decode($item['parametrosQuery']),$item['versionRegistro'],utf8_decode($item['activoCombo']),$item['regEstado'],utf8_decode($item['regFechaUltimaModificacion']),$item['regUsuarioUltimaModificacion'],$item['regFormularioUltimaModificacion'],$item['regVersionUltimaModificacion']);
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
				
				$consulta= $consulta . $comaText . "(" . $item["comboAnidadoID"] . "," . $item["catalogoID"] . "," . $item["campoIDPadre"] . "," . $item["campoIDdependiente"] . ", '" . utf8_decode(addslashes($item["controlQuery"])) . "', '" . utf8_decode(addslashes($item["queryValue"])) . "','" . utf8_decode(addslashes($item["queryText"])) . "','" . utf8_decode(addslashes($item["parametrosQuery"])) . "'," . $item["versionRegistro"] . ",'" . utf8_decode($item["activoCombo"]) . "'," . $item["regEstado"] . ",'" . utf8_decode($item["regFechaUltimaModificacion"]) . "'," . $item["regUsuarioUltimaModificacion"] . "," . $item["regFormularioUltimaModificacion"] . "," . $item["regVersionUltimaModificacion"] . ")";
			}
			 
		   
		 }
	if(!$coma){
		$consulta="select 1";
	}
	//print_r($consulta);
	//return true;
	$this->query=$consulta;
	
    // prepare the query
    $stmt = $this->Conexion->prepare($this->query);
	
    // sanitize
	$comboAnidadoID=htmlspecialchars(strip_tags($item['comboAnidadoID']));
	$catalogoID=htmlspecialchars(strip_tags($item['catalogoID']));
	$campoIDPadre=htmlspecialchars(strip_tags($item['campoIDPadre']));
	$campoIDdependiente=htmlspecialchars(strip_tags($item['campoIDdependiente']));
	$controlQuery=htmlspecialchars(strip_tags($item['controlQuery']));
	$queryValue=htmlspecialchars(strip_tags($item['queryValue']));
	$queryText=htmlspecialchars(strip_tags($item['queryText']));
	$parametrosQuery=htmlspecialchars(strip_tags($item['parametrosQuery']));
	$versionRegistro=htmlspecialchars(strip_tags($item['versionRegistro']));
	$activoCombo=htmlspecialchars(strip_tags($item['activoCombo']));
	$regEstado=htmlspecialchars(strip_tags($item['regEstado']));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($item['regFechaUltimaModificacion']));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($item['regUsuarioUltimaModificacion']));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($item['regFormularioUltimaModificacion']));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($item['regVersionUltimaModificacion']));
   
    // bind the values
	$stmt->bindParam(':comboAnidadoID', $comboAnidadoID);
	$stmt->bindParam(':catalogoID', $catalogoID);
	$stmt->bindParam(':campoIDPadre', $campoIDPadre);
	$stmt->bindParam(':campoIDdependiente', $campoIDdependiente);
	$stmt->bindParam(':controlQuery', $controlQuery);
	$stmt->bindParam(':queryValue', $queryValue);
	$stmt->bindParam(':queryText', $queryText);
	$stmt->bindParam(':parametrosQuery', $parametrosQuery);
	$stmt->bindParam(':versionRegistro', $versionRegistro);
	$stmt->bindParam(':activoCombo', $activoCombo);
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


function Cambia($comboAnidadoID,$catalogoID,$campoIDPadre,$campoIDdependiente,$controlQuery,$queryValue,$queryText,$parametrosQuery,$versionRegistro,$activoCombo,$regEstado,$regFechaUltimaModificacion,$regUsuarioUltimaModificacion,$regFormularioUltimaModificacion,$regVersionUltimaModificacion){
    $query = "UPDATE " . $this->NombreTabla . " SET catalogoID=:catalogoID,campoIDPadre=:campoIDPadre,campoIDdependiente=:campoIDdependiente,controlQuery=:controlQuery,queryValue=:queryValue,queryText=:queryText,parametrosQuery=:parametrosQuery,versionRegistro=:versionRegistro,activoCombo=:activoCombo,regEstado=:regEstado,regFechaUltimaModificacion=:regFechaUltimaModificacion,regUsuarioUltimaModificacion=:regUsuarioUltimaModificacion,regFormularioUltimaModificacion=:regFormularioUltimaModificacion,regVersionUltimaModificacion=:regVersionUltimaModificacion 
		WHERE comboAnidadoID=:comboAnidadoID ";

    // prepare the query
    $stmt = $this->Conexion->prepare($query);
 
     // sanitize
	$comboAnidadoID=htmlspecialchars(strip_tags($comboAnidadoID));
	$catalogoID=htmlspecialchars(strip_tags($catalogoID));
	$campoIDPadre=htmlspecialchars(strip_tags($campoIDPadre));
	$campoIDdependiente=htmlspecialchars(strip_tags($campoIDdependiente));
	$controlQuery=htmlspecialchars(strip_tags($controlQuery));
	$queryValue=htmlspecialchars(strip_tags($queryValue));
	$queryText=htmlspecialchars(strip_tags($queryText));
	$parametrosQuery=htmlspecialchars(strip_tags($parametrosQuery));
	$versionRegistro=htmlspecialchars(strip_tags($versionRegistro));
	$activoCombo=htmlspecialchars(strip_tags($activoCombo));
	$regEstado=htmlspecialchars(strip_tags($regEstado));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($regFechaUltimaModificacion));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($regUsuarioUltimaModificacion));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($regFormularioUltimaModificacion));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($regVersionUltimaModificacion));
   
    // bind the values
	$stmt->bindParam(':comboAnidadoID', $comboAnidadoID);
	$stmt->bindParam(':catalogoID', $catalogoID);
	$stmt->bindParam(':campoIDPadre', $campoIDPadre);
	$stmt->bindParam(':campoIDdependiente', $campoIDdependiente);
	$stmt->bindParam(':controlQuery', $controlQuery);
	$stmt->bindParam(':queryValue', $queryValue);
	$stmt->bindParam(':queryText', $queryText);
	$stmt->bindParam(':parametrosQuery', $parametrosQuery);
	$stmt->bindParam(':versionRegistro', $versionRegistro);
	$stmt->bindParam(':activoCombo', $activoCombo);
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
