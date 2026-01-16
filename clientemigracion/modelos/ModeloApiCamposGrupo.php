<?php 
class CamposGrupo{ 
 
private $Conexion; 
private $Database; 
private $NombreTabla = 'Campos'; 
 
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
    	$query = "select * from " . $this->NombreTabla . " " . ($id > 0 ? " where campoID = ?" : "");
 
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
 
 
        	// return true because email exists in the database
        	return true;
    	}
 
    	// return false if email does not exist in the database
    	return false;
	}
 

function Inserta($registros){

    $consulta="";
	
	$consulta = 'INSERT INTO ' . $this->NombreTabla . 
			" (campoID,nombreCampo,tipoControl,longitud,campoPK,campoFK,controlQuery,visible,orden,tipoCampo,campoRequerido,textField,valueField,valorDefault,CSS,catalogoID,textoID,catalogoReferenciaID,versionRegistro,regEstado,regFechaUltimaModificacion,regUsuarioUltimaModificacion,regFormularioUltimaModificacion,regVersionUltimaModificacion) VALUES ";
			
		$coma = false;
		$comaText = "";
		foreach($registros as $item)
		 {
			//echo $item['textoID']."lolol";
			 if($this->ObtenerDatos($item['campoID'])){
				$this->Cambia($item['campoID'],$item['nombreCampo'],$item['tipoControl'],$item['longitud'],$item['campoPK'],$item['campoFK'],$item['controlQuery'],$item['visible'],$item['orden'],$item['tipoCampo'],$item['campoRequerido'],$item['textField'],$item['valueField'],$item['valorDefault'],$item['CSS'],$item['catalogoID'],$item['textoID'],$item['catalogoReferenciaID'],$item['versionRegistro'],$item['regEstado'],$item['regFechaUltimaModificacion'],$item['regUsuarioUltimaModificacion'],$item['regFormularioUltimaModificacion'],$item['regVersionUltimaModificacion']);
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
				
				$consulta= $consulta . $comaText . "('" . $item["campoID"] . "','" . $item["nombreCampo"] . "','" . $item["tipoControl"] . "','" . $item["longitud"] . "'," . $item["campoPK"] . "," . $item["campoFK"] . ",'" . $item["controlQuery"] . "'," . $item["visible"] . ",'" . $item["orden"] . "','" . $item["tipoCampo"] . "'," . $item["campoRequerido"] . ",'" . $item["textField"] . "','" . $item["valueField"] . "','" . $item["valorDefault"] . "','" . $item["CSS"] . "','" . $item["catalogoID"] . "','" . $item["textoID"] . "','" . $item["catalogoReferenciaID"] . "','" . $item["versionRegistro"] . "'," . $item["regEstado"] . ",'" . $item["regFechaUltimaModificacion"] . "','" . $item["regUsuarioUltimaModificacion"] . "','" . $item["regFormularioUltimaModificacion"] . "','" . $item["regVersionUltimaModificacion"] . "')";
			}
			 
		   
		 }
	if(!$coma){
		$consulta="select 1";
	}
	
	$this->query=$consulta;
	
    // prepare the query
    $stmt = $this->Conexion->prepare($this->query);
	
    // sanitize
	$campoID=htmlspecialchars(strip_tags($item['campoID']));
	//$nombreCampo=htmlspecialchars(strip_tags($item['nombreCampo']));
	$nombreCampo=htmlspecialchars($item['nombreCampo'], ENT_QUOTES,'UTF-8',false);
	$tipoControl=htmlspecialchars(strip_tags($item['tipoControl']));
	$longitud=htmlspecialchars(strip_tags($item['longitud']));
	$campoPK=htmlspecialchars(strip_tags($item['campoPK']));
	$campoFK=htmlspecialchars(strip_tags($item['campoFK']));
	$controlQuery=htmlspecialchars(strip_tags($item['controlQuery']));
	$visible=htmlspecialchars(strip_tags($item['visible']));
	$orden=htmlspecialchars(strip_tags($item['orden']));
	$tipoCampo=htmlspecialchars(strip_tags($item['tipoCampo']));
	$campoRequerido=htmlspecialchars(strip_tags($item['campoRequerido']));
	$textField=htmlspecialchars(strip_tags($item['textField']));
	$valueField=htmlspecialchars(strip_tags($item['valueField']));
	$valorDefault=htmlspecialchars(strip_tags($item['valorDefault']));
	$CSS=htmlspecialchars(strip_tags($item['CSS']));
	$catalogoID=htmlspecialchars(strip_tags($item['catalogoID']));
	$textoID=htmlspecialchars(strip_tags($item['textoID']));
	$catalogoReferenciaID=htmlspecialchars(strip_tags($item['catalogoReferenciaID']));
	$versionRegistro=htmlspecialchars(strip_tags($item['versionRegistro']));
	$regEstado=htmlspecialchars(strip_tags($item['regEstado']));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($item['regFechaUltimaModificacion']));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($item['regUsuarioUltimaModificacion']));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($item['regFormularioUltimaModificacion']));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($item['regVersionUltimaModificacion']));
   
    // bind the values
	$stmt->bindParam(':campoID', $campoID);
	$stmt->bindParam(':nombreCampo', $nombreCampo);
	$stmt->bindParam(':tipoControl', $tipoControl);
	$stmt->bindParam(':longitud', $longitud);
	$campoPK = (int)$campoPK;
	$stmt->bindValue(':campoPK', $campoPK, PDO::PARAM_INT);
	$campoFK = (int)$campoFK;
	$stmt->bindValue(':campoFK', $campoFK, PDO::PARAM_INT);
	$stmt->bindParam(':controlQuery', $controlQuery);
	$visible = (int)$visible;
	$stmt->bindValue(':visible', $visible, PDO::PARAM_INT);
	$stmt->bindParam(':orden', $orden);
	$stmt->bindParam(':tipoCampo', $tipoCampo);
	$campoRequerido = (int)$campoRequerido;
	$stmt->bindValue(':campoRequerido', $campoRequerido, PDO::PARAM_INT);
	$stmt->bindParam(':textField', $textField);
	$stmt->bindParam(':valueField', $valueField);
	$stmt->bindParam(':valorDefault', $valorDefault);
	$stmt->bindParam(':CSS', $CSS);
	$stmt->bindParam(':catalogoID', $catalogoID);
	$stmt->bindParam(':textoID', $textoID);
	$stmt->bindParam(':catalogoReferenciaID', $catalogoReferenciaID);
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


function Cambia($campoID,$nombreCampo,$tipoControl,$longitud,$campoPK,$campoFK,$controlQuery,$visible,$orden,$tipoCampo,$campoRequerido,$textField,$valueField,$valorDefault,$CSS,$catalogoID,$textoID,$catalogoReferenciaID,$versionRegistro,$regEstado,$regFechaUltimaModificacion,$regUsuarioUltimaModificacion,$regFormularioUltimaModificacion,$regVersionUltimaModificacion){
    $query = "UPDATE " . $this->NombreTabla . " SET nombreCampo=:nombreCampo,tipoControl=:tipoControl,longitud=:longitud,campoPK=:campoPK,campoFK=:campoFK,controlQuery=:controlQuery,visible=:visible,orden=:orden,tipoCampo=:tipoCampo,campoRequerido=:campoRequerido,textField=:textField,valueField=:valueField,valorDefault=:valorDefault,CSS=:CSS,catalogoID=:catalogoID,textoID=:textoID,catalogoReferenciaID=:catalogoReferenciaID,versionRegistro=:versionRegistro,regEstado=:regEstado,regFechaUltimaModificacion=:regFechaUltimaModificacion,regUsuarioUltimaModificacion=:regUsuarioUltimaModificacion,regFormularioUltimaModificacion=:regFormularioUltimaModificacion,regVersionUltimaModificacion=:regVersionUltimaModificacion 
		WHERE campoID=:campoID ";

    // prepare the query
    $stmt = $this->Conexion->prepare($query);
 
     // sanitize
	$campoID=htmlspecialchars(strip_tags($campoID));
	$nombreCampo=htmlspecialchars($nombreCampo, ENT_QUOTES,'UTF-8',false);
	//$nombreCampo=htmlspecialchars(strip_tags($nombreCampo));
	$tipoControl=htmlspecialchars(strip_tags($tipoControl));
	$longitud=htmlspecialchars(strip_tags($longitud));
	$campoPK=htmlspecialchars(strip_tags($campoPK));
	$campoFK=htmlspecialchars(strip_tags($campoFK));
	$controlQuery=htmlspecialchars(strip_tags($controlQuery));
	$visible=htmlspecialchars(strip_tags($visible));
	$orden=htmlspecialchars(strip_tags($orden));
	$tipoCampo=htmlspecialchars(strip_tags($tipoCampo));
	$campoRequerido=htmlspecialchars(strip_tags($campoRequerido));
	$textField=htmlspecialchars(strip_tags($textField));
	$valueField=htmlspecialchars(strip_tags($valueField));
	$valorDefault=htmlspecialchars(strip_tags($valorDefault));
	$CSS=htmlspecialchars(strip_tags($CSS));
	$catalogoID=htmlspecialchars(strip_tags($catalogoID));
	$textoID=htmlspecialchars(strip_tags($textoID));
	$catalogoReferenciaID=htmlspecialchars(strip_tags($catalogoReferenciaID));
	$versionRegistro=htmlspecialchars(strip_tags($versionRegistro));
	$regEstado=htmlspecialchars(strip_tags($regEstado));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($regFechaUltimaModificacion));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($regUsuarioUltimaModificacion));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($regFormularioUltimaModificacion));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($regVersionUltimaModificacion));
   
    // bind the values
	$stmt->bindParam(':campoID', $campoID);
	$stmt->bindParam(':nombreCampo', $nombreCampo);
	$stmt->bindParam(':tipoControl', $tipoControl);
	$stmt->bindParam(':longitud', $longitud);
	$campoPK = (int)$campoPK;
	$stmt->bindValue(':campoPK', $campoPK, PDO::PARAM_INT);
	$campoFK = (int)$campoFK;
	$stmt->bindValue(':campoFK', $campoFK, PDO::PARAM_INT);
	$stmt->bindParam(':controlQuery', $controlQuery);
	$visible = (int)$visible;
	$stmt->bindValue(':visible', $visible, PDO::PARAM_INT);
	$stmt->bindParam(':orden', $orden);
	$stmt->bindParam(':tipoCampo', $tipoCampo);
	$campoRequerido = (int)$campoRequerido;
	$stmt->bindValue(':campoRequerido', $campoRequerido, PDO::PARAM_INT);
	$stmt->bindParam(':textField', $textField);
	$stmt->bindParam(':valueField', $valueField);
	$stmt->bindParam(':valorDefault', $valorDefault);
	$stmt->bindParam(':CSS', $CSS);
	$stmt->bindParam(':catalogoID', $catalogoID);
	$stmt->bindParam(':textoID', $textoID);
	$stmt->bindParam(':catalogoReferenciaID', $catalogoReferenciaID);
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