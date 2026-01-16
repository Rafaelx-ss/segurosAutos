<?php 
class Empleados{ 
 
private $Conexion; 
private $Database; 
private $NombreTabla = 'Empleados'; 
 
// object properties
public $Campos;
public $Dataset;
public $Mensaje;
 
// constructor
    public function __construct($dbf){
        $this->Database = $dbf;
        $this->Conexion = $dbf->conn;
    }
	
	function ObtenerDatos($empleadoCodigo=0, $establecimientoID=0){
    	// query to check if email exists
    	$query = "select * from " . $this->NombreTabla .  " where 1 " . ($empleadoCodigo > 0 ? " and empleadoCodigo = :empleadoCodigo" : "") . " " . ($establecimientoID > 0 ? " and establecimientoID = :establecimientoID" : "");
 
    	// prepare the query
    	$stmt = $this->Conexion->prepare( $query );
 
    	 // sanitize
		if($empleadoCodigo > 0){
			$empleadoCodigo=htmlspecialchars(strip_tags($empleadoCodigo));
		}
		if($establecimientoID > 0){
			$establecimientoID=htmlspecialchars(strip_tags($establecimientoID));
		}
 
    	// bind given id value

    
    	 // bind the values
		if($empleadoCodigo > 0){
			$stmt->bindParam(':empleadoCodigo', $empleadoCodigo);
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
			" (empleadoCodigo,empleadoNumeroCobro,empleadoNombre,empleadoDireccion,empleadoTelefono,empleadoNIP,intentosNIP,activoEmpleado,tipoEmpleadoCodigo,establecimientoID,versionRegistro,regEstado,regFechaUltimaModificacion,regUsuarioUltimaModificacion,regFormularioUltimaModificacion,regVersionUltimaModificacion) VALUES ";
			
		$coma = false;
		$comaText = "";
		foreach($registros as $item)
		 {
			//echo $item['textoID']."lolol";
			 if($this->ObtenerDatos($item['empleadoCodigo'], $item['establecimientoID'])){
				$this->Cambia($item['empleadoCodigo'],($item['empleadoNumeroCobro']),($item['empleadoNombre']),($item['empleadoDireccion']),($item['empleadoTelefono']),($item['empleadoNIP']),$item['intentosNIP'],$item['activoEmpleado'],($item['tipoEmpleadoCodigo']),($item['establecimientoID']),$item['versionRegistro'],$item['regEstado'],($item['regFechaUltimaModificacion']),$item['regUsuarioUltimaModificacion'],$item['regFormularioUltimaModificacion'],$item['regVersionUltimaModificacion']);
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
				
				$CampoNull=$item["intentosNIP"];
				if($item["intentosNIP"]==" " or trim($item["intentosNIP"]) == ""){
					$intentosNIP="NULL";
				}
				ELSE{
					$intentosNIP="'".$item["intentosNIP"]."'";
				}
				$consulta= $consulta . $comaText . "(" . $item["empleadoCodigo"] . ",'" . ($item["empleadoNumeroCobro"]) . "','" . ($item["empleadoNombre"]) . "','" . ($item["empleadoDireccion"]) . "','" . ($item["empleadoTelefono"]) . "','" . ($item["empleadoNIP"]) . "'," . $intentosNIP . "," . $item["activoEmpleado"] . ",'" . ($item["tipoEmpleadoCodigo"]) . "','" . ($item["establecimientoID"]) . "'," . $item["versionRegistro"] . "," . $item["regEstado"] . ",'" . ($item["regFechaUltimaModificacion"]) . "'," . $item["regUsuarioUltimaModificacion"] . "," . $item["regFormularioUltimaModificacion"] . "," . $item["regVersionUltimaModificacion"] . ")";
			}
			 
		   
		 }
	if(!$coma){
		$consulta="select 1";
	}
	
	$this->query=$consulta;
	
    // prepare the query
    $stmt = $this->Conexion->prepare($this->query);
	
    // sanitize
	$empleadoCodigo=htmlspecialchars(strip_tags($item['empleadoCodigo']));
	$empleadoNumeroCobro=htmlspecialchars(strip_tags($item['empleadoNumeroCobro']));
	$empleadoNombre=htmlspecialchars(strip_tags($item['empleadoNombre']));
	$empleadoDireccion=htmlspecialchars(strip_tags($item['empleadoDireccion']));
	$empleadoTelefono=htmlspecialchars(strip_tags($item['empleadoTelefono']));
	$empleadoNIP=htmlspecialchars(strip_tags($item['empleadoNIP']));
	if($item["intentosNIP"]<>" " and trim($item["intentosNIP"]) <> ""){
		$intentosNIP=htmlspecialchars(strip_tags($item['intentosNIP']));
	}
	$activoEmpleado=htmlspecialchars(strip_tags($item['activoEmpleado']));
	$tipoEmpleadoCodigo=htmlspecialchars(strip_tags($item['tipoEmpleadoCodigo']));
	$establecimientoID=htmlspecialchars(strip_tags($item['establecimientoID']));
	$versionRegistro=htmlspecialchars(strip_tags($item['versionRegistro']));
	$regEstado=htmlspecialchars(strip_tags($item['regEstado']));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($item['regFechaUltimaModificacion']));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($item['regUsuarioUltimaModificacion']));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($item['regFormularioUltimaModificacion']));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($item['regVersionUltimaModificacion']));
   
    // bind the values
	$stmt->bindParam(':empleadoCodigo', $empleadoCodigo);
	$stmt->bindParam(':empleadoNumeroCobro', $empleadoNumeroCobro);
	$stmt->bindParam(':empleadoNombre', $empleadoNombre);
	$stmt->bindParam(':empleadoDireccion', $empleadoDireccion);
	$stmt->bindParam(':empleadoTelefono', $empleadoTelefono);
	$stmt->bindParam(':empleadoNIP', $empleadoNIP);
	$stmt->bindParam(':intentosNIP', $intentosNIP);
	$activoEmpleado = (int)$activoEmpleado;
	$stmt->bindValue(':activoEmpleado', $activoEmpleado, PDO::PARAM_INT);
	$stmt->bindParam(':tipoEmpleadoCodigo', $tipoEmpleadoCodigo);
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


function Cambia($empleadoCodigo,$empleadoNumeroCobro,$empleadoNombre,$empleadoDireccion,$empleadoTelefono,$empleadoNIP,$intentosNIP,$activoEmpleado,$tipoEmpleadoCodigo,$establecimientoID,$versionRegistro,$regEstado,$regFechaUltimaModificacion,$regUsuarioUltimaModificacion,$regFormularioUltimaModificacion,$regVersionUltimaModificacion){
	
	$CampoNull=$intentosNIP;
	if($intentosNIP==" " or trim($intentosNIP) == ""){
		$CampoNull="";
	}
	else {
		$CampoNull=",intentosNIP=:intentosNIP";
	}
	$query = "UPDATE " . $this->NombreTabla . " SET empleadoNumeroCobro=:empleadoNumeroCobro,empleadoNombre=:empleadoNombre,empleadoDireccion=:empleadoDireccion,
	empleadoTelefono=:empleadoTelefono,empleadoNIP=:empleadoNIP".$CampoNull.",activoEmpleado=:activoEmpleado,tipoEmpleadoCodigo=:tipoEmpleadoCodigo,
	versionRegistro=:versionRegistro,regEstado=:regEstado,regFechaUltimaModificacion=:regFechaUltimaModificacion,
	regUsuarioUltimaModificacion=:regUsuarioUltimaModificacion,regFormularioUltimaModificacion=:regFormularioUltimaModificacion,regVersionUltimaModificacion=:regVersionUltimaModificacion 
		WHERE empleadoCodigo=:empleadoCodigo and establecimientoID=:establecimientoID ";

    // prepare the query
    $stmt = $this->Conexion->prepare($query);
 
     // sanitize
	$empleadoCodigo=htmlspecialchars(strip_tags($empleadoCodigo));
	$empleadoNumeroCobro=htmlspecialchars(strip_tags($empleadoNumeroCobro));
	$empleadoNombre=htmlspecialchars(strip_tags($empleadoNombre));
	$empleadoDireccion=htmlspecialchars(strip_tags($empleadoDireccion));
	$empleadoTelefono=htmlspecialchars(strip_tags($empleadoTelefono));
	$empleadoNIP=htmlspecialchars(strip_tags($empleadoNIP));
	if($intentosNIP != " " and trim($intentosNIP) != ""){
		$intentosNIP=htmlspecialchars(strip_tags($intentosNIP));
	}
	$activoEmpleado=htmlspecialchars(strip_tags($activoEmpleado));
	$tipoEmpleadoCodigo=htmlspecialchars(strip_tags($tipoEmpleadoCodigo));
	$establecimientoID=htmlspecialchars(strip_tags($establecimientoID));
	$versionRegistro=htmlspecialchars(strip_tags($versionRegistro));
	$regEstado=htmlspecialchars(strip_tags($regEstado));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($regFechaUltimaModificacion));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($regUsuarioUltimaModificacion));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($regFormularioUltimaModificacion));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($regVersionUltimaModificacion));
   
    // bind the values
	$stmt->bindParam(':empleadoCodigo', $empleadoCodigo);
	$stmt->bindParam(':empleadoNumeroCobro', $empleadoNumeroCobro);
	$stmt->bindParam(':empleadoNombre', $empleadoNombre);
	$stmt->bindParam(':empleadoDireccion', $empleadoDireccion);
	$stmt->bindParam(':empleadoTelefono', $empleadoTelefono);
	$stmt->bindParam(':empleadoNIP', $empleadoNIP);
	if($intentosNIP != " " and trim($intentosNIP) != ""){
		$stmt->bindParam(':intentosNIP', $intentosNIP);
	}
	$activoEmpleado = (int)$activoEmpleado;
	$stmt->bindValue(':activoEmpleado', $activoEmpleado, PDO::PARAM_INT);
	$stmt->bindParam(':tipoEmpleadoCodigo', $tipoEmpleadoCodigo);
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