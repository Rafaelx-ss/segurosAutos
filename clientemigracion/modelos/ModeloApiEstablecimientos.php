<?php 
class Establecimientos{ 
 
private $Conexion; 
private $Database; 
private $NombreTabla = 'Establecimientos'; 
 
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
    	$query = "select * from " . $this->NombreTabla . " " . ($id > 0 ? " where establecimientoID = ?" : "");
 
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
			" (establecimientoID,aliasEstablecimiento,razonSocialEstablecimiento,rfcEstablecimiento,codigoEstablecimiento,codigo2Establecimiento,representanteLegal,rfcRepresentanteLegal,activoEstablecimiento,fechaAltaEstablecimiento,grupoID,versionRegistro,regEstado,regFechaUltimaModificacion,regUsuarioUltimaModificacion,regFormularioUltimaModificacion,regVersionUltimaModificacion) VALUES ";
			
		$coma = false;
		$comaText = "";
		foreach($registros as $item)
		 {
			//echo $item['textoID']."lolol";
			 if($this->ObtenerDatos($item['establecimientoID'])){
				$this->Cambia($item['establecimientoID'],$item['aliasEstablecimiento'],$item['razonSocialEstablecimiento'],$item['rfcEstablecimiento'],$item['codigoEstablecimiento'],$item['codigo2Establecimiento'],$item['representanteLegal'],$item['rfcRepresentanteLegal'],$item['activoEstablecimiento'],$item['fechaAltaEstablecimiento'],$item['grupoID'],$item['versionRegistro'],$item['regEstado'],$item['regFechaUltimaModificacion'],$item['regUsuarioUltimaModificacion'],$item['regFormularioUltimaModificacion'],$item['regVersionUltimaModificacion']);
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
				
				$CampoNullrfcRepresentanteLegal="'".$item["rfcRepresentanteLegal"]."'";
				if($item["rfcRepresentanteLegal"]==" " or trim($item["rfcRepresentanteLegal"]) == ""){
					$CampoNullrfcRepresentanteLegal='NULL';
				}
				$consulta= $consulta . $comaText . "('" . $item["establecimientoID"] . "','" . $item["aliasEstablecimiento"] . "','" . $item["razonSocialEstablecimiento"] . "','" . $item["rfcEstablecimiento"] . "','" . $item["codigoEstablecimiento"] . "','" . $item["codigo2Establecimiento"] . "','" . $item["representanteLegal"] . "'," . $CampoNullrfcRepresentanteLegal . "," . $item["activoEstablecimiento"] . ",'" . $item["fechaAltaEstablecimiento"] . "'," . $item["grupoID"] . "," . $item["versionRegistro"] . "," . $item["regEstado"] . ",'" . $item["regFechaUltimaModificacion"] . "'," . $item["regUsuarioUltimaModificacion"] . "," . $item["regFormularioUltimaModificacion"] . "," . $item["regVersionUltimaModificacion"] . ")";
			}
			 
		   
		 }
	if(!$coma){
		$consulta="select 1";
	}
	
	$this->query=$consulta;
	
    // prepare the query
    $stmt = $this->Conexion->prepare($this->query);
	
    // sanitize
	$establecimientoID=htmlspecialchars(strip_tags($item['establecimientoID']));
	//$aliasEstablecimiento=htmlspecialchars(strip_tags($item['aliasEstablecimiento']));
	//$razonSocialEstablecimiento=htmlspecialchars(strip_tags($item['razonSocialEstablecimiento']));
	$aliasEstablecimiento=htmlspecialchars($item['aliasEstablecimiento'], ENT_QUOTES,'UTF-8',false);
	$razonSocialEstablecimiento=htmlspecialchars($item['razonSocialEstablecimiento'], ENT_QUOTES,'UTF-8',false);
	$rfcEstablecimiento=htmlspecialchars(strip_tags($item['rfcEstablecimiento']));
	$codigoEstablecimiento=htmlspecialchars(strip_tags($item['codigoEstablecimiento']));
	$codigo2Establecimiento=htmlspecialchars(strip_tags($item['codigo2Establecimiento']));
	//$representanteLegal=htmlspecialchars(strip_tags($item['representanteLegal']));
	$representanteLegal=htmlspecialchars($item['representanteLegal'], ENT_QUOTES,'UTF-8',false);
	
	$activoEstablecimiento=htmlspecialchars(strip_tags($item['activoEstablecimiento']));
	$fechaAltaEstablecimiento=htmlspecialchars(strip_tags($item['fechaAltaEstablecimiento']));
	$grupoID=htmlspecialchars(strip_tags($item['grupoID']));
	$versionRegistro=htmlspecialchars(strip_tags($item['versionRegistro']));
	$regEstado=htmlspecialchars(strip_tags($item['regEstado']));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($item['regFechaUltimaModificacion']));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($item['regUsuarioUltimaModificacion']));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($item['regFormularioUltimaModificacion']));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($item['regVersionUltimaModificacion']));
   
    // bind the values
	$stmt->bindParam(':establecimientoID', $establecimientoID);
	$stmt->bindParam(':aliasEstablecimiento', $aliasEstablecimiento);
	$stmt->bindParam(':razonSocialEstablecimiento', $razonSocialEstablecimiento);
	$stmt->bindParam(':rfcEstablecimiento', $rfcEstablecimiento);
	$stmt->bindParam(':codigoEstablecimiento', $codigoEstablecimiento);
	$stmt->bindParam(':codigo2Establecimiento', $codigo2Establecimiento);
	$stmt->bindParam(':representanteLegal', $representanteLegal);
	$activoEstablecimiento = (int)$activoEstablecimiento;
	$stmt->bindValue(':activoEstablecimiento', $activoEstablecimiento, PDO::PARAM_INT);
	$stmt->bindParam(':fechaAltaEstablecimiento', $fechaAltaEstablecimiento);
	$stmt->bindParam(':grupoID', $grupoID);
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


function Cambia($establecimientoID,$aliasEstablecimiento,$razonSocialEstablecimiento,$rfcEstablecimiento,$codigoEstablecimiento,$codigo2Establecimiento,$representanteLegal,$rfcRepresentanteLegal,$activoEstablecimiento,$fechaAltaEstablecimiento,$grupoID,$versionRegistro,$regEstado,$regFechaUltimaModificacion,$regUsuarioUltimaModificacion,$regFormularioUltimaModificacion,$regVersionUltimaModificacion){
    $cadenaRfcRepresentanteLegal=",rfcRepresentanteLegal='".$rfcRepresentanteLegal."'";
	if($rfcRepresentanteLegal == " " or trim($rfcRepresentanteLegal) == ""){
		$cadenaRfcRepresentanteLegal=",rfcRepresentanteLegal=NULL";
	}
	
	$query = "UPDATE " . $this->NombreTabla . " SET aliasEstablecimiento=:aliasEstablecimiento,razonSocialEstablecimiento=:razonSocialEstablecimiento,
	rfcEstablecimiento=:rfcEstablecimiento,codigoEstablecimiento=:codigoEstablecimiento,codigo2Establecimiento=:codigo2Establecimiento,
	representanteLegal=:representanteLegal".$cadenaRfcRepresentanteLegal.",activoEstablecimiento=:activoEstablecimiento,fechaAltaEstablecimiento=:fechaAltaEstablecimiento,grupoID=:grupoID,versionRegistro=:versionRegistro,regEstado=:regEstado,regFechaUltimaModificacion=:regFechaUltimaModificacion,regUsuarioUltimaModificacion=:regUsuarioUltimaModificacion,regFormularioUltimaModificacion=:regFormularioUltimaModificacion,regVersionUltimaModificacion=:regVersionUltimaModificacion 
		WHERE establecimientoID=:establecimientoID ";

    // prepare the query
    $stmt = $this->Conexion->prepare($query);
 
     // sanitize
	$establecimientoID=htmlspecialchars(strip_tags($establecimientoID));
	//$aliasEstablecimiento=htmlspecialchars(strip_tags($aliasEstablecimiento));
	//$razonSocialEstablecimiento=htmlspecialchars(strip_tags($razonSocialEstablecimiento));
	$aliasEstablecimiento=htmlspecialchars($aliasEstablecimiento, ENT_QUOTES,'UTF-8',false);
	$razonSocialEstablecimiento=htmlspecialchars($razonSocialEstablecimiento, ENT_QUOTES,'UTF-8',false);
	$rfcEstablecimiento=htmlspecialchars(strip_tags($rfcEstablecimiento));
	$codigoEstablecimiento=htmlspecialchars(strip_tags($codigoEstablecimiento));
	$codigo2Establecimiento=htmlspecialchars(strip_tags($codigo2Establecimiento));
	//$representanteLegal=htmlspecialchars(strip_tags($representanteLegal));
	$representanteLegal=htmlspecialchars($representanteLegal, ENT_QUOTES,'UTF-8',false);
	$activoEstablecimiento=htmlspecialchars(strip_tags($activoEstablecimiento));
	$fechaAltaEstablecimiento=htmlspecialchars(strip_tags($fechaAltaEstablecimiento));
	$grupoID=htmlspecialchars(strip_tags($grupoID));
	$versionRegistro=htmlspecialchars(strip_tags($versionRegistro));
	$regEstado=htmlspecialchars(strip_tags($regEstado));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($regFechaUltimaModificacion));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($regUsuarioUltimaModificacion));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($regFormularioUltimaModificacion));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($regVersionUltimaModificacion));
   
    // bind the values
	$stmt->bindParam(':establecimientoID', $establecimientoID);
	$stmt->bindParam(':aliasEstablecimiento', $aliasEstablecimiento);
	$stmt->bindParam(':razonSocialEstablecimiento', $razonSocialEstablecimiento);
	$stmt->bindParam(':rfcEstablecimiento', $rfcEstablecimiento);
	$stmt->bindParam(':codigoEstablecimiento', $codigoEstablecimiento);
	$stmt->bindParam(':codigo2Establecimiento', $codigo2Establecimiento);
	$stmt->bindParam(':representanteLegal', $representanteLegal);
	$activoEstablecimiento = (int)$activoEstablecimiento;
	$stmt->bindValue(':activoEstablecimiento', $activoEstablecimiento, PDO::PARAM_INT);
	$stmt->bindParam(':fechaAltaEstablecimiento', $fechaAltaEstablecimiento);
	$stmt->bindParam(':grupoID', $grupoID);
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