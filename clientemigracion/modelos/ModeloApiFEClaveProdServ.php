<?php 
class FEClaveProdServ{ 
 
private $Conexion; 
private $Database; 
private $NombreTabla = 'FEClaveProdServ'; 
 
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
    	$query = "select * from " . $this->NombreTabla . " " . ($id > 0 ? " where claveProdServID = ?" : "");
 
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
			" (claveProdServID,claveProdServ,descripcion,fechaDeInicioDeVigencia,fechaDeFinDeVigencia,incluirIVATraslado,incluirIEPSTraslado,complementoQueDebeIncluir,versionRegistro,regEstado,regFechaUltimaModificacion,regUsuarioUltimaModificacion,regFormularioUltimaModificacion,regVersionUltimaModificacion) VALUES ";
			
		$coma = false;
		$comaText = "";
		foreach($registros as $item)
		 {
			//echo $item['textoID']."lolol";
			 if($this->ObtenerDatos($item['claveProdServID'])){
				$this->Cambia($item['claveProdServID'],$item['claveProdServ'],$item['descripcion'],$item['fechaDeInicioDeVigencia'],$item['fechaDeFinDeVigencia'],$item['incluirIVATraslado'],$item['incluirIEPSTraslado'],$item['complementoQueDebeIncluir'],$item['versionRegistro'],$item['regEstado'],$item['regFechaUltimaModificacion'],$item['regUsuarioUltimaModificacion'],$item['regFormularioUltimaModificacion'],$item['regVersionUltimaModificacion']);
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
				
				$consulta= $consulta . $comaText . "('" . $item["claveProdServID"] . "','" . $item["claveProdServ"] . "','" . $item["descripcion"] . "','" . $item["fechaDeInicioDeVigencia"] . "','" . $item["fechaDeFinDeVigencia"] . "','" . $item["incluirIVATraslado"] . "','" . $item["incluirIEPSTraslado"] . "','" . $item["complementoQueDebeIncluir"] . "','" . $item["versionRegistro"] . "'," . $item["regEstado"] . ",'" . $item["regFechaUltimaModificacion"] . "','" . $item["regUsuarioUltimaModificacion"] . "','" . $item["regFormularioUltimaModificacion"] . "','" . $item["regVersionUltimaModificacion"] . "')";
			}
			 
		   
		 }
	if(!$coma){
		$consulta="select 1";
	}
	
	$this->query=$consulta;
	
    // prepare the query
    $stmt = $this->Conexion->prepare($this->query);
	
    // sanitize
	$claveProdServID=htmlspecialchars(strip_tags($item['claveProdServID']));
	$claveProdServ=htmlspecialchars(strip_tags($item['claveProdServ']));
	$descripcion=htmlspecialchars(strip_tags($item['descripcion']));
	$fechaDeInicioDeVigencia=htmlspecialchars(strip_tags($item['fechaDeInicioDeVigencia']));
	$fechaDeFinDeVigencia=htmlspecialchars(strip_tags($item['fechaDeFinDeVigencia']));
	$incluirIVATraslado=htmlspecialchars(strip_tags($item['incluirIVATraslado']));
	$incluirIEPSTraslado=htmlspecialchars(strip_tags($item['incluirIEPSTraslado']));
	$complementoQueDebeIncluir=htmlspecialchars(strip_tags($item['complementoQueDebeIncluir']));
	$versionRegistro=htmlspecialchars(strip_tags($item['versionRegistro']));
	$regEstado=htmlspecialchars(strip_tags($item['regEstado']));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($item['regFechaUltimaModificacion']));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($item['regUsuarioUltimaModificacion']));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($item['regFormularioUltimaModificacion']));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($item['regVersionUltimaModificacion']));
   
    // bind the values
	$stmt->bindParam(':claveProdServID', $claveProdServID);
	$stmt->bindParam(':claveProdServ', $claveProdServ);
	$stmt->bindParam(':descripcion', $descripcion);
	$stmt->bindParam(':fechaDeInicioDeVigencia', $fechaDeInicioDeVigencia);
	$stmt->bindParam(':fechaDeFinDeVigencia', $fechaDeFinDeVigencia);
	$stmt->bindParam(':incluirIVATraslado', $incluirIVATraslado);
	$stmt->bindParam(':incluirIEPSTraslado', $incluirIEPSTraslado);
	$stmt->bindParam(':complementoQueDebeIncluir', $complementoQueDebeIncluir);
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
        echo $this->Mensaje = $e->getMessage().$consulta;
    }

  
    // execute the query, also check if query was successful
    if($stmt->execute()){
        return true;

    }
 
    return false;
}


function Cambia($claveProdServID,$claveProdServ,$descripcion,$fechaDeInicioDeVigencia,$fechaDeFinDeVigencia,$incluirIVATraslado,$incluirIEPSTraslado,$complementoQueDebeIncluir,$versionRegistro,$regEstado,$regFechaUltimaModificacion,$regUsuarioUltimaModificacion,$regFormularioUltimaModificacion,$regVersionUltimaModificacion){
    $query = "UPDATE " . $this->NombreTabla . " SET claveProdServ=:claveProdServ,descripcion=:descripcion,fechaDeInicioDeVigencia=:fechaDeInicioDeVigencia,fechaDeFinDeVigencia=:fechaDeFinDeVigencia,incluirIVATraslado=:incluirIVATraslado,incluirIEPSTraslado=:incluirIEPSTraslado,complementoQueDebeIncluir=:complementoQueDebeIncluir,versionRegistro=:versionRegistro,regEstado=:regEstado,regFechaUltimaModificacion=:regFechaUltimaModificacion,regUsuarioUltimaModificacion=:regUsuarioUltimaModificacion,regFormularioUltimaModificacion=:regFormularioUltimaModificacion,regVersionUltimaModificacion=:regVersionUltimaModificacion 
		WHERE claveProdServID=:claveProdServID ";

    // prepare the query
    $stmt = $this->Conexion->prepare($query);
 
     // sanitize
	$claveProdServID=htmlspecialchars(strip_tags($claveProdServID));
	$claveProdServ=htmlspecialchars(strip_tags($claveProdServ));
	$descripcion=htmlspecialchars(strip_tags($descripcion));
	$fechaDeInicioDeVigencia=htmlspecialchars(strip_tags($fechaDeInicioDeVigencia));
	$fechaDeFinDeVigencia=htmlspecialchars(strip_tags($fechaDeFinDeVigencia));
	$incluirIVATraslado=htmlspecialchars(strip_tags($incluirIVATraslado));
	$incluirIEPSTraslado=htmlspecialchars(strip_tags($incluirIEPSTraslado));
	$complementoQueDebeIncluir=htmlspecialchars(strip_tags($complementoQueDebeIncluir));
	$versionRegistro=htmlspecialchars(strip_tags($versionRegistro));
	$regEstado=htmlspecialchars(strip_tags($regEstado));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($regFechaUltimaModificacion));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($regUsuarioUltimaModificacion));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($regFormularioUltimaModificacion));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($regVersionUltimaModificacion));
   
    // bind the values
	$stmt->bindParam(':claveProdServID', $claveProdServID);
	$stmt->bindParam(':claveProdServ', $claveProdServ);
	$stmt->bindParam(':descripcion', $descripcion);
	$stmt->bindParam(':fechaDeInicioDeVigencia', $fechaDeInicioDeVigencia);
	$stmt->bindParam(':fechaDeFinDeVigencia', $fechaDeFinDeVigencia);
	$stmt->bindParam(':incluirIVATraslado', $incluirIVATraslado);
	$stmt->bindParam(':incluirIEPSTraslado', $incluirIEPSTraslado);
	$stmt->bindParam(':complementoQueDebeIncluir', $complementoQueDebeIncluir);
	$stmt->bindParam(':versionRegistro', $versionRegistro);
	$regEstado = (int)$regEstado;
	$stmt->bindValue(':regEstado', $regEstado, PDO::PARAM_INT);
	$stmt->bindParam(':regFechaUltimaModificacion', $regFechaUltimaModificacion);
	$stmt->bindParam(':regUsuarioUltimaModificacion', $regUsuarioUltimaModificacion);
	$stmt->bindParam(':regFormularioUltimaModificacion', $regFormularioUltimaModificacion);
	$stmt->bindParam(':regVersionUltimaModificacion', $regVersionUltimaModificacion);

    // execute the query, also check if query was successful
    if($stmt->execute()){
        return true;

    }
 
    return false;
}
	
		
}
	?>