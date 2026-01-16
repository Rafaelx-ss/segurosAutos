<?php 
class VentasDetallesImpuestos{ 
 
private $Conexion; 
private $Database; 
private $NombreTabla = 'VentasDetallesImpuestos'; 
 
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
    	// query to check if email exists
    	$query = "select * from " . $this->NombreTabla .  " where 1 " . ($id > 0 ? " and ventaDetalleImpuestoID = ?" : "");
 
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
 
			if($tipoconsulta=="" or $tipoconsulta=="CONSULTA_LOCAL"){
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

    $consulta="";
	
	$consulta = 'INSERT INTO ' . $this->NombreTabla . 
			" (ventaDetalleImpuestoID,ventaFormaDePagoID,tipoImpuestoID,clasificacion,factor,valorImpuesto,importeImpuesto,estadoReplica,versionRegistro,regEstado,regFechaUltimaModificacion,regUsuarioUltimaModificacion,regFormularioUltimaModificacion,regVersionUltimaModificacion) VALUES ";
			
		$coma = false;
		$comaText = "";
		foreach($registros as $item)
		 {
			//echo $item['textoID']."lolol";
			 if($this->ObtenerDatos($item['ventaDetalleImpuestoID'])){
				$this->Cambia($item['ventaDetalleImpuestoID'],$item['ventaFormaDePagoID'],$item['tipoImpuestoID'],($item['clasificacion']),($item['factor']),($item['valorImpuesto']),($item['importeImpuesto']),$item['estadoReplica'],$item['versionRegistro'],$item['regEstado'],($item['regFechaUltimaModificacion']),$item['regUsuarioUltimaModificacion'],$item['regFormularioUltimaModificacion'],$item['regVersionUltimaModificacion']);
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
				
				$consulta= $consulta . $comaText . "(" . $item["ventaDetalleImpuestoID"] . "," . $item["ventaFormaDePagoID"] . "," . $item["tipoImpuestoID"] . ",'" . ($item["clasificacion"]) . "','" . ($item["factor"]) . "','" . ($item["valorImpuesto"]) . "','" . ($item["importeImpuesto"]) . "'," . $item["estadoReplica"] . "," . $item["versionRegistro"] . "," . $item["regEstado"] . ",'" . ($item["regFechaUltimaModificacion"]) . "'," . $item["regUsuarioUltimaModificacion"] . "," . $item["regFormularioUltimaModificacion"] . "," . $item["regVersionUltimaModificacion"] . ")";
			}
			 
		   
		 }
	if(!$coma){
		$consulta="select 1";
	}
	
	$this->query=$consulta;
	
    // prepare the query
    $stmt = $this->Conexion->prepare($this->query);
	
    // sanitize
	$ventaDetalleImpuestoID=htmlspecialchars(strip_tags($item['ventaDetalleImpuestoID']));
	$ventaFormaDePagoID=htmlspecialchars(strip_tags($item['ventaFormaDePagoID']));
	$tipoImpuestoID=htmlspecialchars(strip_tags($item['tipoImpuestoID']));
	$clasificacion=htmlspecialchars(strip_tags($item['clasificacion']));
	$factor=htmlspecialchars(strip_tags($item['factor']));
	$valorImpuesto=htmlspecialchars(strip_tags($item['valorImpuesto']));
	$importeImpuesto=htmlspecialchars(strip_tags($item['importeImpuesto']));
	$estadoReplica=htmlspecialchars(strip_tags($item['estadoReplica']));
	$versionRegistro=htmlspecialchars(strip_tags($item['versionRegistro']));
	$regEstado=htmlspecialchars(strip_tags($item['regEstado']));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($item['regFechaUltimaModificacion']));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($item['regUsuarioUltimaModificacion']));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($item['regFormularioUltimaModificacion']));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($item['regVersionUltimaModificacion']));
   
    // bind the values
	$stmt->bindParam(':ventaDetalleImpuestoID', $ventaDetalleImpuestoID);
	$stmt->bindParam(':ventaFormaDePagoID', $ventaFormaDePagoID);
	$stmt->bindParam(':tipoImpuestoID', $tipoImpuestoID);
	$stmt->bindParam(':clasificacion', $clasificacion);
	$stmt->bindParam(':factor', $factor);
	$stmt->bindParam(':valorImpuesto', $valorImpuesto);
	$stmt->bindParam(':importeImpuesto', $importeImpuesto);
	$stmt->bindParam(':estadoReplica', $estadoReplica);
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


function InsertaRegreso($item){
$Registo= "0";
try{
    $consulta="";
	
	$consulta = 'INSERT INTO ' . $this->NombreTabla . 
			" (ventaDetalleImpuestoID,ventaFormaDePagoID,tipoImpuestoID,clasificacion,factor,valorImpuesto,importeImpuesto,estadoReplica,versionRegistro,regEstado,regFechaUltimaModificacion,regUsuarioUltimaModificacion,regFormularioUltimaModificacion,regVersionUltimaModificacion) VALUES ";
			
		$coma = false;
		$comaText = "";
		$Registo=$item['ventaDetalleImpuestoID'];
		if($this->ObtenerDatos($item['ventaDetalleImpuestoID'])){
			$this->Cambia($item['ventaDetalleImpuestoID'],$item['ventaFormaDePagoID'],$item['tipoImpuestoID'],($item['clasificacion']),($item['factor']),($item['valorImpuesto']),($item['importeImpuesto']),$item['estadoReplica'],$item['versionRegistro'],$item['regEstado'],($item['regFechaUltimaModificacion']),$item['regUsuarioUltimaModificacion'],$item['regFormularioUltimaModificacion'],$item['regVersionUltimaModificacion']);
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
			
			$consulta= $consulta . $comaText . "(" . $item["ventaDetalleImpuestoID"] . "," . $item["ventaFormaDePagoID"] . "," . $item["tipoImpuestoID"] . ",'" . ($item["clasificacion"]) . "','" . ($item["factor"]) . "','" . ($item["valorImpuesto"]) . "','" . ($item["importeImpuesto"]) . "'," . $item["estadoReplica"] . "," . $item["versionRegistro"] . "," . $item["regEstado"] . ",'" . ($item["regFechaUltimaModificacion"]) . "'," . $item["regUsuarioUltimaModificacion"] . "," . $item["regFormularioUltimaModificacion"] . "," . $item["regVersionUltimaModificacion"] . ")";
		}
	if(!$coma){
		$consulta="select 1";
	}
	
	$this->query=$consulta;
	
    // prepare the query
    $stmt = $this->Conexion->prepare($this->query);
	
    // sanitize
	$ventaDetalleImpuestoID=htmlspecialchars(strip_tags($item['ventaDetalleImpuestoID']));
	$ventaFormaDePagoID=htmlspecialchars(strip_tags($item['ventaFormaDePagoID']));
	$tipoImpuestoID=htmlspecialchars(strip_tags($item['tipoImpuestoID']));
	$clasificacion=htmlspecialchars(strip_tags($item['clasificacion']));
	$factor=htmlspecialchars(strip_tags($item['factor']));
	$valorImpuesto=htmlspecialchars(strip_tags($item['valorImpuesto']));
	$importeImpuesto=htmlspecialchars(strip_tags($item['importeImpuesto']));
	$estadoReplica=htmlspecialchars(strip_tags($item['estadoReplica']));
	$versionRegistro=htmlspecialchars(strip_tags($item['versionRegistro']));
	$regEstado=htmlspecialchars(strip_tags($item['regEstado']));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($item['regFechaUltimaModificacion']));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($item['regUsuarioUltimaModificacion']));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($item['regFormularioUltimaModificacion']));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($item['regVersionUltimaModificacion']));
   
    // bind the values
	$stmt->bindParam(':ventaDetalleImpuestoID', $ventaDetalleImpuestoID);
	$stmt->bindParam(':ventaFormaDePagoID', $ventaFormaDePagoID);
	$stmt->bindParam(':tipoImpuestoID', $tipoImpuestoID);
	$stmt->bindParam(':clasificacion', $clasificacion);
	$stmt->bindParam(':factor', $factor);
	$stmt->bindParam(':valorImpuesto', $valorImpuesto);
	$stmt->bindParam(':importeImpuesto', $importeImpuesto);
	$stmt->bindParam(':estadoReplica', $estadoReplica);
	$stmt->bindParam(':versionRegistro', $versionRegistro);
	$regEstado = (int)$regEstado;
	$stmt->bindValue(':regEstado', $regEstado, PDO::PARAM_INT);
	$stmt->bindParam(':regFechaUltimaModificacion', $regFechaUltimaModificacion);
	$stmt->bindParam(':regUsuarioUltimaModificacion', $regUsuarioUltimaModificacion);
	$stmt->bindParam(':regFormularioUltimaModificacion', $regFormularioUltimaModificacion);
	$stmt->bindParam(':regVersionUltimaModificacion', $regVersionUltimaModificacion);
   
   try{
        // execute the query, also check if query was successful
        if($consulta<>"select 1"){
			// execute the query, also check if query was successful
			if($stmt->execute()){
				//$this->LogMigracion("Api".$this->NombreTabla, "INSERTAR", "Registo ".$Registo.": ".$consulta, "INSERCION EXITOSA");
				return true;

			}
		}
		else{
			return true;
		}
    }   
    catch (Exception $e){
		$this->LogMigracion("Api".$this->NombreTabla, "INSERTAR Ex1", "Registo ".$Registo.": ".$consulta, $e->getMessage());
		echo $this->Mensaje = $e->getMessage().$consulta;
	}
}
catch (Exception $e){
		$this->LogMigracion("Api".$this->NombreTabla, "INSERTAR Ex2", "Registo ".$Registo.": ".$consulta, $e->getMessage());
	echo $this->Mensaje = $e->getMessage().$consulta;
}
    return false;
}

function Cambia($ventaDetalleImpuestoID,$ventaFormaDePagoID,$tipoImpuestoID,$clasificacion,$factor,$valorImpuesto,$importeImpuesto,$estadoReplica,$versionRegistro,$regEstado,$regFechaUltimaModificacion,$regUsuarioUltimaModificacion,$regFormularioUltimaModificacion,$regVersionUltimaModificacion){
    $query = "UPDATE " . $this->NombreTabla . " SET ventaFormaDePagoID=:ventaFormaDePagoID,tipoImpuestoID=:tipoImpuestoID,clasificacion=:clasificacion,factor=:factor,valorImpuesto=:valorImpuesto,importeImpuesto=:importeImpuesto,estadoReplica=:estadoReplica,versionRegistro=:versionRegistro,regEstado=:regEstado,regFechaUltimaModificacion=:regFechaUltimaModificacion,regUsuarioUltimaModificacion=:regUsuarioUltimaModificacion,regFormularioUltimaModificacion=:regFormularioUltimaModificacion,regVersionUltimaModificacion=:regVersionUltimaModificacion 
		WHERE ventaDetalleImpuestoID=:ventaDetalleImpuestoID ";

    // prepare the query
    $stmt = $this->Conexion->prepare($query);
 
     // sanitize
	$ventaDetalleImpuestoID=htmlspecialchars(strip_tags($ventaDetalleImpuestoID));
	$ventaFormaDePagoID=htmlspecialchars(strip_tags($ventaFormaDePagoID));
	$tipoImpuestoID=htmlspecialchars(strip_tags($tipoImpuestoID));
	$clasificacion=htmlspecialchars(strip_tags($clasificacion));
	$factor=htmlspecialchars(strip_tags($factor));
	$valorImpuesto=htmlspecialchars(strip_tags($valorImpuesto));
	$importeImpuesto=htmlspecialchars(strip_tags($importeImpuesto));
	$estadoReplica=htmlspecialchars(strip_tags($estadoReplica));
	$versionRegistro=htmlspecialchars(strip_tags($versionRegistro));
	$regEstado=htmlspecialchars(strip_tags($regEstado));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($regFechaUltimaModificacion));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($regUsuarioUltimaModificacion));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($regFormularioUltimaModificacion));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($regVersionUltimaModificacion));
   
    // bind the values
	$stmt->bindParam(':ventaDetalleImpuestoID', $ventaDetalleImpuestoID);
	$stmt->bindParam(':ventaFormaDePagoID', $ventaFormaDePagoID);
	$stmt->bindParam(':tipoImpuestoID', $tipoImpuestoID);
	$stmt->bindParam(':clasificacion', $clasificacion);
	$stmt->bindParam(':factor', $factor);
	$stmt->bindParam(':valorImpuesto', $valorImpuesto);
	$stmt->bindParam(':importeImpuesto', $importeImpuesto);
	$stmt->bindParam(':estadoReplica', $estadoReplica);
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
	
	
	function LogMigracion($Api, $metodo, $consulta, $mensaje){
		try{
			$query2 ="INSERT INTO LogMigracion (logMigracionId, api, metodo, consulta, mensaje, regEstado, regFechaUltimaModificacion, regUsuarioUltimaModificacion, regFormularioUltimaModificacion, regVersionUltimaModificacion) 
			VALUES (NULL, '".$Api."', '".$metodo."', '".str_replace("'","",$consulta)."', '".str_replace("'","",$mensaje)."', b'1', now(), '1', '1', '1');";

			// prepare the query
			$stmt = $this->Conexion->prepare($query2);
			if($stmt->execute()){
			}
		}
		catch (Exception $e){
			$this->Mensaje .= $e->getMessage();
		}
	}
		
}
	?>