<?php 
class GrupoPoliza{ 
 
private $Conexion; 
private $Database; 
private $NombreTabla = 'GrupoPoliza'; 
 
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
	
	function ObtenerDatos($ID=0){
    	// query to check if email exists
    	try{
			$strWhere="";
    	    if ($ID != 0){
    	        $strWhere = " where  grupoPolizaID=:grupoPolizaID";
    	    }
    		$query = "select * from " . $this->NombreTabla .  $strWhere;
 
    	// prepare the query
    	$stmt = $this->Conexion->prepare( $query );
 		$this->Mensaje2=$query;
    	 // sanitize
		
		$ID=htmlspecialchars(strip_tags($ID));
 
    	// bind given id value

    	 // bind the values
		if($ID > 0){
		    $stmt->bindParam(':grupoPolizaID', $ID);
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
			" (grupoPolizaID,NombreGrupoPoliza,versionRegistro,regEstado,regFechaUltimaModificacion,regUsuarioUltimaModificacion,regFormularioUltimaModificacion,regVersionUltimaModificacion) VALUES ";
			
		$coma = false;
		$comaText = "";
		foreach($registros as $item)
		 {
			//echo $item['textoID']."lolol";
			 if($this->ObtenerDatos($item['grupoPolizaID'])){
				$this->Cambia($item['grupoPolizaID'],$item['NombreGrupoPoliza'],$item['versionRegistro'],$item['regEstado'],$item['regFechaUltimaModificacion'],$item['regUsuarioUltimaModificacion'],$item['regFormularioUltimaModificacion'],$item['regVersionUltimaModificacion']);
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
				
				$consulta= $consulta . $comaText . "(" . $item['grupoPolizaID'] . ",'" . $item['NombreGrupoPoliza'] . "'," . $item['versionRegistro'] . "," . $item['regEstado'] . ",now()," . $item['regUsuarioUltimaModificacion'] . "," . $item['regFormularioUltimaModificacion'] . "," . $item['regVersionUltimaModificacion'] .")";
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
	$grupoPolizaID=htmlspecialchars(strip_tags($item['grupoPolizaID']));
	$NombreGrupoPoliza=htmlspecialchars(strip_tags($item['NombreGrupoPoliza']));
	$versionRegistro=htmlspecialchars(strip_tags($item['versionRegistro']));
	$regEstado=htmlspecialchars(strip_tags($item['regEstado']));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($item['regFechaUltimaModificacion']));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($item['regUsuarioUltimaModificacion']));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($item['regFormularioUltimaModificacion']));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($item['regVersionUltimaModificacion']));
   
    // bind the values
	$stmt->bindParam(':grupoPolizaID', $grupoPolizaID);
	$stmt->bindParam(':NombreGrupoPoliza', $NombreGrupoPoliza);
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

function Cambia($grupoPolizaID,$NombreGrupoPoliza,$versionRegistro,$regEstado,$regFechaUltimaModificacion,$regUsuarioUltimaModificacion,$regFormularioUltimaModificacion,$regVersionUltimaModificacion){
	try{
		$query = "UPDATE " . $this->NombreTabla . " SET NombreGrupoPoliza=:NombreGrupoPoliza, 
		versionRegistro=:versionRegistro, regEstado=:regEstado, regFechaUltimaModificacion=:regFechaUltimaModificacion, regUsuarioUltimaModificacion=:regUsuarioUltimaModificacion, regFormularioUltimaModificacion=:regFormularioUltimaModificacion, regVersionUltimaModificacion=:regVersionUltimaModificacion 
		WHERE grupoPolizaID=:grupoPolizaID ";

    // prepare the query
    $stmt = $this->Conexion->prepare($query);
 	$this->Mensaje2=$query;
     // sanitize
	$grupoPolizaID=htmlspecialchars(strip_tags($grupoPolizaID));
	$NombreGrupoPoliza=htmlspecialchars(strip_tags($NombreGrupoPoliza));
	$versionRegistro=htmlspecialchars(strip_tags($versionRegistro));
	$regEstado=htmlspecialchars(strip_tags($regEstado));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($regFechaUltimaModificacion));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($regUsuarioUltimaModificacion));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($regFormularioUltimaModificacion));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($regVersionUltimaModificacion));
   
    // bind the values
	$stmt->bindParam(':grupoPolizaID', $grupoPolizaID);
	$stmt->bindParam(':NombreGrupoPoliza', $NombreGrupoPoliza);
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