<?php 
class VentasFormasDePago{ 
 
private $Conexion; 
private $Database; 
private $NombreTabla = 'VentasFormasDePago'; 
 
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
    	$query = "select * from " . $this->NombreTabla .  " where 1 " . ($id > 0 ? " and ventaFormaDePagoID = ?" : "");
 
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
			" (ventaFormaDePagoID,ventaCombustibleID,ventaProductoID,establecimientoID,formaPagoID,tipoMovimientoID,importeFormaPago,subTotal,cantidadProducto,tipoFacturacion,estadoReplica,versionRegistro,regEstado,regFechaUltimaModificacion,regUsuarioUltimaModificacion,regFormularioUltimaModificacion,regVersionUltimaModificacion) VALUES ";
			
		$coma = false;
		$comaText = "";
		foreach($registros as $item)
		 {
			//echo $item['textoID']."lolol";
			 if($this->ObtenerDatos($item['ventaFormaDePagoID'])){
				$this->Cambia($item['ventaFormaDePagoID'],$item['ventaCombustibleID'],$item['ventaProductoID'],($item['establecimientoID']),$item['formaPagoID'],$item['tipoMovimientoID'],($item['importeFormaPago']),($item['subTotal']),($item['cantidadProducto']),($item['tipoFacturacion']),$item['estadoReplica'],$item['versionRegistro'],$item['regEstado'],($item['regFechaUltimaModificacion']),$item['regUsuarioUltimaModificacion'],$item['regFormularioUltimaModificacion'],$item['regVersionUltimaModificacion']);
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
				
				
				$CampoNullCombustibleID=$item["ventaCombustibleID"];
				if($item["ventaCombustibleID"]==" " or trim($item["ventaCombustibleID"]) == ""){
					$CampoNullCombustibleID='NULL';
				}
				$CampoNullProductoID=$item["ventaProductoID"];
				if($item["ventaProductoID"]==" " or trim($item["ventaProductoID"]) == ""){
					$CampoNullProductoID='NULL';
				}
				$consulta= $consulta . $comaText . "(" . $item["ventaFormaDePagoID"] . "," . $CampoNullCombustibleID . "," . $CampoNullProductoID . ",'" . ($item["establecimientoID"]) . "'," . $item["formaPagoID"] . "," . $item["tipoMovimientoID"] . ",'" . ($item["importeFormaPago"]) . "','" . ($item["subTotal"]) . "','" . ($item["cantidadProducto"]) . "','" . ($item["tipoFacturacion"]) . "'," . $item["estadoReplica"] . "," . $item["versionRegistro"] . "," . $item["regEstado"] . ",'" . ($item["regFechaUltimaModificacion"]) . "'," . $item["regUsuarioUltimaModificacion"] . "," . $item["regFormularioUltimaModificacion"] . "," . $item["regVersionUltimaModificacion"] . ")";
			}
			 
		   
		 }
	if(!$coma){
		$consulta="select 1";
	}
	
	$this->query=$consulta;
	
    // prepare the query
    $stmt = $this->Conexion->prepare($this->query);
	
    // sanitize
	$ventaFormaDePagoID=htmlspecialchars(strip_tags($item['ventaFormaDePagoID']));
	$ventaCombustibleID=htmlspecialchars(strip_tags($item['ventaCombustibleID']));
	$ventaProductoID=htmlspecialchars(strip_tags($item['ventaProductoID']));
	$establecimientoID=htmlspecialchars(strip_tags($item['establecimientoID']));
	$formaPagoID=htmlspecialchars(strip_tags($item['formaPagoID']));
	$tipoMovimientoID=htmlspecialchars(strip_tags($item['tipoMovimientoID']));
	$importeFormaPago=htmlspecialchars(strip_tags($item['importeFormaPago']));
	$subTotal=htmlspecialchars(strip_tags($item['subTotal']));
	$cantidadProducto=htmlspecialchars(strip_tags($item['cantidadProducto']));
	$tipoFacturacion=htmlspecialchars(strip_tags($item['tipoFacturacion']));
	$estadoReplica=htmlspecialchars(strip_tags($item['estadoReplica']));
	$versionRegistro=htmlspecialchars(strip_tags($item['versionRegistro']));
	$regEstado=htmlspecialchars(strip_tags($item['regEstado']));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($item['regFechaUltimaModificacion']));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($item['regUsuarioUltimaModificacion']));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($item['regFormularioUltimaModificacion']));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($item['regVersionUltimaModificacion']));
   
    // bind the values
	$stmt->bindParam(':ventaFormaDePagoID', $ventaFormaDePagoID);
	$stmt->bindParam(':ventaCombustibleID', $ventaCombustibleID);
	$stmt->bindParam(':ventaProductoID', $ventaProductoID);
	$stmt->bindParam(':establecimientoID', $establecimientoID);
	$stmt->bindParam(':formaPagoID', $formaPagoID);
	$stmt->bindParam(':tipoMovimientoID', $tipoMovimientoID);
	$stmt->bindParam(':importeFormaPago', $importeFormaPago);
	$stmt->bindParam(':subTotal', $subTotal);
	$stmt->bindParam(':cantidadProducto', $cantidadProducto);
	$stmt->bindParam(':tipoFacturacion', $tipoFacturacion);
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
			" (ventaFormaDePagoID,ventaCombustibleID,ventaProductoID,establecimientoID,formaPagoID,tipoMovimientoID,importeFormaPago,subTotal,cantidadProducto,tipoFacturacion,estadoReplica,versionRegistro,regEstado,regFechaUltimaModificacion,regUsuarioUltimaModificacion,regFormularioUltimaModificacion,regVersionUltimaModificacion) VALUES ";
			
		$coma = false;
		$comaText = "";
		$Registo=$item['ventaFormaDePagoID'];
		if($this->ObtenerDatos($item['ventaFormaDePagoID'])){
			$this->Cambia($item['ventaFormaDePagoID'],$item['ventaCombustibleID'],$item['ventaProductoID'],($item['establecimientoID']),$item['formaPagoID'],$item['tipoMovimientoID'],($item['importeFormaPago']),($item['subTotal']),($item['cantidadProducto']),($item['tipoFacturacion']),$item['estadoReplica'],$item['versionRegistro'],$item['regEstado'],($item['regFechaUltimaModificacion']),$item['regUsuarioUltimaModificacion'],$item['regFormularioUltimaModificacion'],$item['regVersionUltimaModificacion']);
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
			
			
			$CampoNullCombustibleID=$item["ventaCombustibleID"];
			if($item["ventaCombustibleID"]==" " or trim($item["ventaCombustibleID"]) == ""){
				$CampoNullCombustibleID='NULL';
			}
			$CampoNullProductoID=$item["ventaProductoID"];
			if($item["ventaProductoID"]==" " or trim($item["ventaProductoID"]) == ""){
				$CampoNullProductoID='NULL';
			}
			$consulta= $consulta . $comaText . "(" . $item["ventaFormaDePagoID"] . "," . $CampoNullCombustibleID . "," . $CampoNullProductoID . ",'" . ($item["establecimientoID"]) . "'," . $item["formaPagoID"] . "," . $item["tipoMovimientoID"] . ",'" . ($item["importeFormaPago"]) . "','" . ($item["subTotal"]) . "','" . ($item["cantidadProducto"]) . "','" . ($item["tipoFacturacion"]) . "'," . $item["estadoReplica"] . "," . $item["versionRegistro"] . "," . $item["regEstado"] . ",'" . ($item["regFechaUltimaModificacion"]) . "'," . $item["regUsuarioUltimaModificacion"] . "," . $item["regFormularioUltimaModificacion"] . "," . $item["regVersionUltimaModificacion"] . ")";
		}
	if(!$coma){
		$consulta="select 1";
	}
	
	$this->query=$consulta;
	
    // prepare the query
    $stmt = $this->Conexion->prepare($this->query);
	
    // sanitize
	$ventaFormaDePagoID=htmlspecialchars(strip_tags($item['ventaFormaDePagoID']));
	$ventaCombustibleID=htmlspecialchars(strip_tags($item['ventaCombustibleID']));
	$ventaProductoID=htmlspecialchars(strip_tags($item['ventaProductoID']));
	$establecimientoID=htmlspecialchars(strip_tags($item['establecimientoID']));
	$formaPagoID=htmlspecialchars(strip_tags($item['formaPagoID']));
	$tipoMovimientoID=htmlspecialchars(strip_tags($item['tipoMovimientoID']));
	$importeFormaPago=htmlspecialchars(strip_tags($item['importeFormaPago']));
	$subTotal=htmlspecialchars(strip_tags($item['subTotal']));
	$cantidadProducto=htmlspecialchars(strip_tags($item['cantidadProducto']));
	$tipoFacturacion=htmlspecialchars(strip_tags($item['tipoFacturacion']));
	$estadoReplica=htmlspecialchars(strip_tags($item['estadoReplica']));
	$versionRegistro=htmlspecialchars(strip_tags($item['versionRegistro']));
	$regEstado=htmlspecialchars(strip_tags($item['regEstado']));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($item['regFechaUltimaModificacion']));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($item['regUsuarioUltimaModificacion']));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($item['regFormularioUltimaModificacion']));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($item['regVersionUltimaModificacion']));
   
    // bind the values
	$stmt->bindParam(':ventaFormaDePagoID', $ventaFormaDePagoID);
	$stmt->bindParam(':ventaCombustibleID', $ventaCombustibleID);
	$stmt->bindParam(':ventaProductoID', $ventaProductoID);
	$stmt->bindParam(':establecimientoID', $establecimientoID);
	$stmt->bindParam(':formaPagoID', $formaPagoID);
	$stmt->bindParam(':tipoMovimientoID', $tipoMovimientoID);
	$stmt->bindParam(':importeFormaPago', $importeFormaPago);
	$stmt->bindParam(':subTotal', $subTotal);
	$stmt->bindParam(':cantidadProducto', $cantidadProducto);
	$stmt->bindParam(':tipoFacturacion', $tipoFacturacion);
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

function Cambia($ventaFormaDePagoID,$ventaCombustibleID,$ventaProductoID,$establecimientoID,$formaPagoID,$tipoMovimientoID,$importeFormaPago,$subTotal,$cantidadProducto,$tipoFacturacion,$estadoReplica,$versionRegistro,$regEstado,$regFechaUltimaModificacion,$regUsuarioUltimaModificacion,$regFormularioUltimaModificacion,$regVersionUltimaModificacion){
    
	$CampoNullCombustibleID=$ventaCombustibleID;
	if($ventaCombustibleID==" " or trim($ventaCombustibleID) == ""){
		$CampoNullCombustibleID='NULL';
	}
	$CampoNullProductoID=$ventaProductoID;
	if($ventaProductoID==" " or trim($ventaProductoID) == ""){
		$CampoNullProductoID='NULL';
	}
	$query = "UPDATE " . $this->NombreTabla . " SET ventaCombustibleID=".$CampoNullCombustibleID.",ventaProductoID=".$CampoNullProductoID.",establecimientoID=:establecimientoID,formaPagoID=:formaPagoID,tipoMovimientoID=:tipoMovimientoID,importeFormaPago=:importeFormaPago,subTotal=:subTotal,cantidadProducto=:cantidadProducto,tipoFacturacion=:tipoFacturacion,estadoReplica=:estadoReplica,versionRegistro=:versionRegistro,regEstado=:regEstado,regFechaUltimaModificacion=:regFechaUltimaModificacion,regUsuarioUltimaModificacion=:regUsuarioUltimaModificacion,regFormularioUltimaModificacion=:regFormularioUltimaModificacion,regVersionUltimaModificacion=:regVersionUltimaModificacion 
		WHERE ventaFormaDePagoID=:ventaFormaDePagoID ";

    // prepare the query
    $stmt = $this->Conexion->prepare($query);
 
     // sanitize
	$ventaFormaDePagoID=htmlspecialchars(strip_tags($ventaFormaDePagoID));
	$establecimientoID=htmlspecialchars(strip_tags($establecimientoID));
	$formaPagoID=htmlspecialchars(strip_tags($formaPagoID));
	$tipoMovimientoID=htmlspecialchars(strip_tags($tipoMovimientoID));
	$importeFormaPago=htmlspecialchars(strip_tags($importeFormaPago));
	$subTotal=htmlspecialchars(strip_tags($subTotal));
	$cantidadProducto=htmlspecialchars(strip_tags($cantidadProducto));
	$tipoFacturacion=htmlspecialchars(strip_tags($tipoFacturacion));
	$estadoReplica=htmlspecialchars(strip_tags($estadoReplica));
	$versionRegistro=htmlspecialchars(strip_tags($versionRegistro));
	$regEstado=htmlspecialchars(strip_tags($regEstado));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($regFechaUltimaModificacion));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($regUsuarioUltimaModificacion));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($regFormularioUltimaModificacion));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($regVersionUltimaModificacion));
   
    // bind the values
	$stmt->bindParam(':ventaFormaDePagoID', $ventaFormaDePagoID);
	$stmt->bindParam(':establecimientoID', $establecimientoID);
	$stmt->bindParam(':formaPagoID', $formaPagoID);
	$stmt->bindParam(':tipoMovimientoID', $tipoMovimientoID);
	$stmt->bindParam(':importeFormaPago', $importeFormaPago);
	$stmt->bindParam(':subTotal', $subTotal);
	$stmt->bindParam(':cantidadProducto', $cantidadProducto);
	$stmt->bindParam(':tipoFacturacion', $tipoFacturacion);
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