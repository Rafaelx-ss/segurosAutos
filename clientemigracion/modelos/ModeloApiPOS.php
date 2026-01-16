<?php 
class POS{ 
 
private $Conexion; 
private $Database; 
private $NombreTabla = 'POS'; 
 
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
    	$query = "select * from " . $this->NombreTabla .  " where 1 " . $consulta . ($id > 0 ? " and posID = ?" : "");
 
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
			" (posID,nombrePOS,seriePOS,ipPOS,macAdress,activoPOS,tipoPosID,establecimientoID,versionRegistro,regEstado,regFechaUltimaModificacion,regUsuarioUltimaModificacion,regFormularioUltimaModificacion,regVersionUltimaModificacion) VALUES ";
			
		$coma = false;
		$comaText = "";
		foreach($registros as $item)
		 {
			//echo $item['textoID']."lolol";
			 if($this->ObtenerDatos($item['posID'])){
				$this->Cambia($item['posID'],($item['nombrePOS']),($item['seriePOS']),($item['ipPOS']),($item['macAdress']),$item['activoPOS'],$item['tipoPosID'],($item['establecimientoID']),$item['versionRegistro'],$item['regEstado'],($item['regFechaUltimaModificacion']),$item['regUsuarioUltimaModificacion'],$item['regFormularioUltimaModificacion'],$item['regVersionUltimaModificacion']);
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
				
				$consulta= $consulta . $comaText . "(" . $item["posID"] . ",'" . ($item["nombrePOS"]) . "','" . ($item["seriePOS"]) . "','" . ($item["ipPOS"]) . "','" . ($item["macAdress"]) . "'," . $item["activoPOS"] . "," . $item["tipoPosID"] . ",'" . ($item["establecimientoID"]) . "'," . $item["versionRegistro"] . "," . $item["regEstado"] . ",'" . ($item["regFechaUltimaModificacion"]) . "'," . $item["regUsuarioUltimaModificacion"] . "," . $item["regFormularioUltimaModificacion"] . "," . $item["regVersionUltimaModificacion"] . ")";
			}
			 
		   
		 }
	if(!$coma){
		$consulta="select 1";
	}
	
	$this->query=$consulta;
	
    // prepare the query
    $stmt = $this->Conexion->prepare($this->query);
	
    // sanitize
	$posID=htmlspecialchars(strip_tags($item['posID']));
	$nombrePOS=htmlspecialchars(strip_tags($item['nombrePOS']));
	$seriePOS=htmlspecialchars(strip_tags($item['seriePOS']));
	$ipPOS=htmlspecialchars(strip_tags($item['ipPOS']));
	$macAdress=htmlspecialchars(strip_tags($item['macAdress']));
	$activoPOS=htmlspecialchars(strip_tags($item['activoPOS']));
	$tipoPosID=htmlspecialchars(strip_tags($item['tipoPosID']));
	$establecimientoID=htmlspecialchars(strip_tags($item['establecimientoID']));
	$versionRegistro=htmlspecialchars(strip_tags($item['versionRegistro']));
	$regEstado=htmlspecialchars(strip_tags($item['regEstado']));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($item['regFechaUltimaModificacion']));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($item['regUsuarioUltimaModificacion']));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($item['regFormularioUltimaModificacion']));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($item['regVersionUltimaModificacion']));
   
    // bind the values
	$stmt->bindParam(':posID', $posID);
	$stmt->bindParam(':nombrePOS', $nombrePOS);
	$stmt->bindParam(':seriePOS', $seriePOS);
	$stmt->bindParam(':ipPOS', $ipPOS);
	$stmt->bindParam(':macAdress', $macAdress);
	$activoPOS = (int)$activoPOS;
	$stmt->bindValue(':activoPOS', $activoPOS, PDO::PARAM_INT);
	$stmt->bindParam(':tipoPosID', $tipoPosID);
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


function Cambia($posID,$nombrePOS,$seriePOS,$ipPOS,$macAdress,$activoPOS,$tipoPosID,$establecimientoID,$versionRegistro,$regEstado,$regFechaUltimaModificacion,$regUsuarioUltimaModificacion,$regFormularioUltimaModificacion,$regVersionUltimaModificacion){
    $query = "UPDATE " . $this->NombreTabla . " SET nombrePOS=:nombrePOS,seriePOS=:seriePOS,ipPOS=:ipPOS,macAdress=:macAdress,activoPOS=:activoPOS,tipoPosID=:tipoPosID,establecimientoID=:establecimientoID,versionRegistro=:versionRegistro,regEstado=:regEstado,regFechaUltimaModificacion=:regFechaUltimaModificacion,regUsuarioUltimaModificacion=:regUsuarioUltimaModificacion,regFormularioUltimaModificacion=:regFormularioUltimaModificacion,regVersionUltimaModificacion=:regVersionUltimaModificacion 
		WHERE posID=:posID ";

    // prepare the query
    $stmt = $this->Conexion->prepare($query);
 
     // sanitize
	$posID=htmlspecialchars(strip_tags($posID));
	$nombrePOS=htmlspecialchars(strip_tags($nombrePOS));
	$seriePOS=htmlspecialchars(strip_tags($seriePOS));
	$ipPOS=htmlspecialchars(strip_tags($ipPOS));
	$macAdress=htmlspecialchars(strip_tags($macAdress));
	$activoPOS=htmlspecialchars(strip_tags($activoPOS));
	$tipoPosID=htmlspecialchars(strip_tags($tipoPosID));
	$establecimientoID=htmlspecialchars(strip_tags($establecimientoID));
	$versionRegistro=htmlspecialchars(strip_tags($versionRegistro));
	$regEstado=htmlspecialchars(strip_tags($regEstado));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($regFechaUltimaModificacion));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($regUsuarioUltimaModificacion));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($regFormularioUltimaModificacion));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($regVersionUltimaModificacion));
   
    // bind the values
	$stmt->bindParam(':posID', $posID);
	$stmt->bindParam(':nombrePOS', $nombrePOS);
	$stmt->bindParam(':seriePOS', $seriePOS);
	$stmt->bindParam(':ipPOS', $ipPOS);
	$stmt->bindParam(':macAdress', $macAdress);
	$activoPOS = (int)$activoPOS;
	$stmt->bindValue(':activoPOS', $activoPOS, PDO::PARAM_INT);
	$stmt->bindParam(':tipoPosID', $tipoPosID);
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