<?php 
class VentasProductos{ 
 
private $Conexion; 
private $Database; 
private $NombreTabla = 'VentasProductos'; 
 
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
    	$query = "select * from " . $this->NombreTabla .  " where 1 " . ($id > 0 ? " and ventaProductoID = ?" : "");
 
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
			" (ventaProductoID,almacenID,establecimientoID,ventaNumero,ventaFecha,ventaSubTotal,ventaIVA,ventaTotal,SalidaSubTotalC,SalidaIVAC,SalidaTotalC,tipoMovimientoID,uuid,serie,folio,facturado,facturado2,facturado3,tipoFacturado,almacenTraspasoID,aperturaBombaID,bombaNumero,empleadoID,asistenciaID,jornadaID,venta_Status,versionRegistro,regEstado,regFechaUltimaModificacion,regUsuarioUltimaModificacion,regFormularioUltimaModificacion,regVersionUltimaModificacion,estadoReplica) VALUES ";
			
		$coma = false;
		$comaText = "";
		foreach($registros as $item)
		 {
			//echo $item['textoID']."lolol";
			 if($this->ObtenerDatos($item['ventaProductoID'])){
				$this->Cambia($item['ventaProductoID'],$item['almacenID'],($item['establecimientoID']),$item['ventaNumero'],($item['ventaFecha']),($item['ventaSubTotal']),($item['ventaIVA']),($item['ventaTotal']),($item['SalidaSubTotalC']),($item['SalidaIVAC']),($item['SalidaTotalC']),$item['tipoMovimientoID'],($item['uuid']),($item['serie']),($item['folio']),$item['facturado'],$item['facturado2'],$item['facturado3'],($item['tipoFacturado']),$item['almacenTraspasoID'],$item['aperturaBombaID'],$item['bombaNumero'],$item['empleadoID'],$item['asistenciaID'],$item['jornadaID'],($item['venta_Status']),$item['versionRegistro'],($item['regEstado']),($item['regFechaUltimaModificacion']),$item['regUsuarioUltimaModificacion'],$item['regFormularioUltimaModificacion'],$item['regVersionUltimaModificacion'], $item['estadoReplica']);
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
				
				
				$CampoNullSalidaSubTotalC=$item["SalidaSubTotalC"];
				if($item["SalidaSubTotalC"]==" " or trim($item["SalidaSubTotalC"]) == ""){
					$CampoNullSalidaSubTotalC='NULL';
				}
				$CampoNullSalidaIVAC=$item["SalidaIVAC"];
				if($item["SalidaIVAC"]==" " or trim($item["SalidaIVAC"]) == ""){
					$CampoNullSalidaIVAC='NULL';
				}
				$CampoNullSalidaTotalC=$item["SalidaTotalC"];
				if($item["SalidaTotalC"]==" " or trim($item["SalidaTotalC"]) == ""){
					$CampoNullSalidaTotalC='NULL';
				}
				$CampoNullFacturado1=$item["facturado"];
				if($item["facturado"]==" " or trim($item["facturado"]) == ""){
					$CampoNullFacturado1='NULL';
				}
				$CampoNullFacturado2=$item["facturado2"];
				if($item["facturado2"]==" " or trim($item["facturado2"]) == ""){
					$CampoNullFacturado2='NULL';
				}
				$CampoNullFacturado3=$item["facturado3"];
				if($item["facturado3"]==" " or trim($item["facturado3"]) == ""){
					$CampoNullFacturado3='NULL';
				}
				$CampoNullFolio=$item["folio"];
				if($item["folio"]==" " or trim($item["folio"]) == ""){
					$CampoNullFolio='NULL';
				}
				$CampoNullalmacenTraspasoID=$item["almacenTraspasoID"];
				if($item["almacenTraspasoID"]==" " or trim($item["almacenTraspasoID"]) == ""){
					$CampoNullalmacenTraspasoID='NULL';
				}
				$consulta= $consulta . $comaText . "(" . $item["ventaProductoID"] . "," . $item["almacenID"] . ",'" . ($item["establecimientoID"]) . "'," . $item["ventaNumero"] . ",'" . ($item["ventaFecha"]) . "','" . ($item["ventaSubTotal"]) . "','" . ($item["ventaIVA"]) . "','" . ($item["ventaTotal"]) . "',
				" . $CampoNullSalidaSubTotalC . "," . $CampoNullSalidaIVAC . "," . $CampoNullSalidaTotalC . "," . $item["tipoMovimientoID"] . ",'" . ($item["uuid"]) . "','" . ($item["serie"]) . "',
				" . $CampoNullFolio . "," . $CampoNullFacturado1 . "," . $CampoNullFacturado2. "," . $CampoNullFacturado3 . ",'" . ($item["tipoFacturado"]) . "',
				" . $CampoNullalmacenTraspasoID . "," . $item["aperturaBombaID"] . "," . $item["bombaNumero"] . "," . $item["empleadoID"] . ",
				" . $item["asistenciaID"] . "," . $item["jornadaID"] . ",'" . ($item["venta_Status"]) . "'," . $item["versionRegistro"] . ",
				'" . ($item["regEstado"]) . "','" . ($item["regFechaUltimaModificacion"]) . "'," . $item["regUsuarioUltimaModificacion"] . ",
				" . $item["regFormularioUltimaModificacion"] . "," . $item["regVersionUltimaModificacion"] . "," . $item["estadoReplica"] . ")";
			}
			 
		   
		 }
	if(!$coma){
		$consulta="select 1";
	}
	
	$this->query=$consulta;
	
    // prepare the query
    $stmt = $this->Conexion->prepare($this->query);
	
    // sanitize
	$ventaProductoID=htmlspecialchars(strip_tags($item['ventaProductoID']));
	$almacenID=htmlspecialchars(strip_tags($item['almacenID']));
	$establecimientoID=htmlspecialchars(strip_tags($item['establecimientoID']));
	$ventaNumero=htmlspecialchars(strip_tags($item['ventaNumero']));
	$ventaFecha=htmlspecialchars(strip_tags($item['ventaFecha']));
	$ventaSubTotal=htmlspecialchars(strip_tags($item['ventaSubTotal']));
	$ventaIVA=htmlspecialchars(strip_tags($item['ventaIVA']));
	$ventaTotal=htmlspecialchars(strip_tags($item['ventaTotal']));
	$SalidaSubTotalC=htmlspecialchars(strip_tags($item['SalidaSubTotalC']));
	$SalidaIVAC=htmlspecialchars(strip_tags($item['SalidaIVAC']));
	$SalidaTotalC=htmlspecialchars(strip_tags($item['SalidaTotalC']));
	$tipoMovimientoID=htmlspecialchars(strip_tags($item['tipoMovimientoID']));
	$uuid=htmlspecialchars(strip_tags($item['uuid']));
	$serie=htmlspecialchars(strip_tags($item['serie']));
	$folio=htmlspecialchars(strip_tags($item['folio']));
	$facturado=htmlspecialchars(strip_tags($item['facturado']));
	$facturado2=htmlspecialchars(strip_tags($item['facturado2']));
	$facturado3=htmlspecialchars(strip_tags($item['facturado3']));
	$tipoFacturado=htmlspecialchars(strip_tags($item['tipoFacturado']));
	$almacenTraspasoID=htmlspecialchars(strip_tags($item['almacenTraspasoID']));
	$aperturaBombaID=htmlspecialchars(strip_tags($item['aperturaBombaID']));
	$bombaNumero=htmlspecialchars(strip_tags($item['bombaNumero']));
	$empleadoID=htmlspecialchars(strip_tags($item['empleadoID']));
	$asistenciaID=htmlspecialchars(strip_tags($item['asistenciaID']));
	$jornadaID=htmlspecialchars(strip_tags($item['jornadaID']));
	$venta_Status=htmlspecialchars(strip_tags($item['venta_Status']));
	$versionRegistro=htmlspecialchars(strip_tags($item['versionRegistro']));
	$regEstado=htmlspecialchars(strip_tags($item['regEstado']));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($item['regFechaUltimaModificacion']));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($item['regUsuarioUltimaModificacion']));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($item['regFormularioUltimaModificacion']));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($item['regVersionUltimaModificacion']));
   
    // bind the values
	$stmt->bindParam(':ventaProductoID', $ventaProductoID);
	$stmt->bindParam(':almacenID', $almacenID);
	$stmt->bindParam(':establecimientoID', $establecimientoID);
	$stmt->bindParam(':ventaNumero', $ventaNumero);
	$stmt->bindParam(':ventaFecha', $ventaFecha);
	$stmt->bindParam(':ventaSubTotal', $ventaSubTotal);
	$stmt->bindParam(':ventaIVA', $ventaIVA);
	$stmt->bindParam(':ventaTotal', $ventaTotal);
	$stmt->bindParam(':SalidaSubTotalC', $SalidaSubTotalC);
	$stmt->bindParam(':SalidaIVAC', $SalidaIVAC);
	$stmt->bindParam(':SalidaTotalC', $SalidaTotalC);
	$stmt->bindParam(':tipoMovimientoID', $tipoMovimientoID);
	$stmt->bindParam(':uuid', $uuid);
	$stmt->bindParam(':serie', $serie);
	$stmt->bindParam(':folio', $folio);
	$facturado = (int)$facturado;
	$stmt->bindValue(':facturado', $facturado, PDO::PARAM_INT);
	$facturado2 = (int)$facturado2;
	$stmt->bindValue(':facturado2', $facturado2, PDO::PARAM_INT);
	$facturado3 = (int)$facturado3;
	$stmt->bindValue(':facturado3', $facturado3, PDO::PARAM_INT);
	$stmt->bindParam(':tipoFacturado', $tipoFacturado);
	$stmt->bindParam(':almacenTraspasoID', $almacenTraspasoID);
	$stmt->bindParam(':aperturaBombaID', $aperturaBombaID);
	$stmt->bindParam(':bombaNumero', $bombaNumero);
	$stmt->bindParam(':empleadoID', $empleadoID);
	$stmt->bindParam(':asistenciaID', $asistenciaID);
	$stmt->bindParam(':jornadaID', $jornadaID);
	$stmt->bindParam(':venta_Status', $venta_Status);
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


function InsertaRegreso($item){
$Registo= "0";
try{
    $consulta="";
	
	$consulta = 'INSERT INTO ' . $this->NombreTabla . 
			" (ventaProductoID,almacenID,establecimientoID,ventaNumero,ventaFecha,ventaSubTotal,ventaIVA,ventaTotal,SalidaSubTotalC,SalidaIVAC,SalidaTotalC,tipoMovimientoID,uuid,serie,folio,facturado,facturado2,facturado3,tipoFacturado,almacenTraspasoID,aperturaBombaID,bombaNumero,empleadoID,asistenciaID,jornadaID,venta_Status,versionRegistro,regEstado,regFechaUltimaModificacion,regUsuarioUltimaModificacion,regFormularioUltimaModificacion,regVersionUltimaModificacion,estadoReplica) VALUES ";
			
		$coma = false;
		$comaText = "";
		$Registo=$item['ventaProductoID'];
		if($this->ObtenerDatos($item['ventaProductoID'])){
			$this->Cambia($item['ventaProductoID'],$item['almacenID'],($item['establecimientoID']),$item['ventaNumero'],($item['ventaFecha']),($item['ventaSubTotal']),($item['ventaIVA']),($item['ventaTotal']),($item['SalidaSubTotalC']),($item['SalidaIVAC']),($item['SalidaTotalC']),$item['tipoMovimientoID'],($item['uuid']),($item['serie']),($item['folio']),$item['facturado'],$item['facturado2'],$item['facturado3'],($item['tipoFacturado']),$item['almacenTraspasoID'],$item['aperturaBombaID'],$item['bombaNumero'],$item['empleadoID'],$item['asistenciaID'],$item['jornadaID'],($item['venta_Status']),$item['versionRegistro'],($item['regEstado']),($item['regFechaUltimaModificacion']),$item['regUsuarioUltimaModificacion'],$item['regFormularioUltimaModificacion'],$item['regVersionUltimaModificacion'], $item['estadoReplica']);
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
			
			
			$CampoNullSalidaSubTotalC=$item["SalidaSubTotalC"];
			if($item["SalidaSubTotalC"]==" " or trim($item["SalidaSubTotalC"]) == ""){
				$CampoNullSalidaSubTotalC='NULL';
			}
			$CampoNullSalidaIVAC=$item["SalidaIVAC"];
			if($item["SalidaIVAC"]==" " or trim($item["SalidaIVAC"]) == ""){
				$CampoNullSalidaIVAC='NULL';
			}
			$CampoNullSalidaTotalC=$item["SalidaTotalC"];
			if($item["SalidaTotalC"]==" " or trim($item["SalidaTotalC"]) == ""){
				$CampoNullSalidaTotalC='NULL';
			}
			$CampoNullFacturado1=$item["facturado"];
			if($item["facturado"]==" " or trim($item["facturado"]) == ""){
				$CampoNullFacturado1='NULL';
			}
			$CampoNullFacturado2=$item["facturado2"];
			if($item["facturado2"]==" " or trim($item["facturado2"]) == ""){
				$CampoNullFacturado2='NULL';
			}
			$CampoNullFacturado3=$item["facturado3"];
			if($item["facturado3"]==" " or trim($item["facturado3"]) == ""){
				$CampoNullFacturado3='NULL';
			}
			$CampoNullFolio=$item["folio"];
			if($item["folio"]==" " or trim($item["folio"]) == ""){
				$CampoNullFolio='NULL';
			}
			$CampoNullalmacenTraspasoID=$item["almacenTraspasoID"];
			if($item["almacenTraspasoID"]==" " or trim($item["almacenTraspasoID"]) == ""){
				$CampoNullalmacenTraspasoID='NULL';
			}
			$consulta= $consulta . $comaText . "(" . $item["ventaProductoID"] . "," . $item["almacenID"] . ",'" . ($item["establecimientoID"]) . "'," . $item["ventaNumero"] . ",'" . ($item["ventaFecha"]) . "','" . ($item["ventaSubTotal"]) . "','" . ($item["ventaIVA"]) . "','" . ($item["ventaTotal"]) . "',
			" . $CampoNullSalidaSubTotalC . "," . $CampoNullSalidaIVAC . "," . $CampoNullSalidaTotalC . "," . $item["tipoMovimientoID"] . ",'" . ($item["uuid"]) . "','" . ($item["serie"]) . "',
			" . $CampoNullFolio . "," . $CampoNullFacturado1 . "," . $CampoNullFacturado2. "," . $CampoNullFacturado3 . ",'" . ($item["tipoFacturado"]) . "',
			" . $CampoNullalmacenTraspasoID . "," . $item["aperturaBombaID"] . "," . $item["bombaNumero"] . "," . $item["empleadoID"] . ",
			" . $item["asistenciaID"] . "," . $item["jornadaID"] . ",'" . ($item["venta_Status"]) . "'," . $item["versionRegistro"] . ",
			'" . ($item["regEstado"]) . "','" . ($item["regFechaUltimaModificacion"]) . "'," . $item["regUsuarioUltimaModificacion"] . ",
			" . $item["regFormularioUltimaModificacion"] . "," . $item["regVersionUltimaModificacion"] . "," . $item["estadoReplica"] . ")";
		}
	if(!$coma){
		$consulta="select 1";
	}
	
	$this->query=$consulta;
	
    // prepare the query
    $stmt = $this->Conexion->prepare($this->query);
	
    // sanitize
	$ventaProductoID=htmlspecialchars(strip_tags($item['ventaProductoID']));
	$almacenID=htmlspecialchars(strip_tags($item['almacenID']));
	$establecimientoID=htmlspecialchars(strip_tags($item['establecimientoID']));
	$ventaNumero=htmlspecialchars(strip_tags($item['ventaNumero']));
	$ventaFecha=htmlspecialchars(strip_tags($item['ventaFecha']));
	$ventaSubTotal=htmlspecialchars(strip_tags($item['ventaSubTotal']));
	$ventaIVA=htmlspecialchars(strip_tags($item['ventaIVA']));
	$ventaTotal=htmlspecialchars(strip_tags($item['ventaTotal']));
	$SalidaSubTotalC=htmlspecialchars(strip_tags($item['SalidaSubTotalC']));
	$SalidaIVAC=htmlspecialchars(strip_tags($item['SalidaIVAC']));
	$SalidaTotalC=htmlspecialchars(strip_tags($item['SalidaTotalC']));
	$tipoMovimientoID=htmlspecialchars(strip_tags($item['tipoMovimientoID']));
	$uuid=htmlspecialchars(strip_tags($item['uuid']));
	$serie=htmlspecialchars(strip_tags($item['serie']));
	$folio=htmlspecialchars(strip_tags($item['folio']));
	$facturado=htmlspecialchars(strip_tags($item['facturado']));
	$facturado2=htmlspecialchars(strip_tags($item['facturado2']));
	$facturado3=htmlspecialchars(strip_tags($item['facturado3']));
	$tipoFacturado=htmlspecialchars(strip_tags($item['tipoFacturado']));
	$almacenTraspasoID=htmlspecialchars(strip_tags($item['almacenTraspasoID']));
	$aperturaBombaID=htmlspecialchars(strip_tags($item['aperturaBombaID']));
	$bombaNumero=htmlspecialchars(strip_tags($item['bombaNumero']));
	$empleadoID=htmlspecialchars(strip_tags($item['empleadoID']));
	$asistenciaID=htmlspecialchars(strip_tags($item['asistenciaID']));
	$jornadaID=htmlspecialchars(strip_tags($item['jornadaID']));
	$venta_Status=htmlspecialchars(strip_tags($item['venta_Status']));
	$versionRegistro=htmlspecialchars(strip_tags($item['versionRegistro']));
	$regEstado=htmlspecialchars(strip_tags($item['regEstado']));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($item['regFechaUltimaModificacion']));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($item['regUsuarioUltimaModificacion']));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($item['regFormularioUltimaModificacion']));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($item['regVersionUltimaModificacion']));
   
    // bind the values
	$stmt->bindParam(':ventaProductoID', $ventaProductoID);
	$stmt->bindParam(':almacenID', $almacenID);
	$stmt->bindParam(':establecimientoID', $establecimientoID);
	$stmt->bindParam(':ventaNumero', $ventaNumero);
	$stmt->bindParam(':ventaFecha', $ventaFecha);
	$stmt->bindParam(':ventaSubTotal', $ventaSubTotal);
	$stmt->bindParam(':ventaIVA', $ventaIVA);
	$stmt->bindParam(':ventaTotal', $ventaTotal);
	$stmt->bindParam(':SalidaSubTotalC', $SalidaSubTotalC);
	$stmt->bindParam(':SalidaIVAC', $SalidaIVAC);
	$stmt->bindParam(':SalidaTotalC', $SalidaTotalC);
	$stmt->bindParam(':tipoMovimientoID', $tipoMovimientoID);
	$stmt->bindParam(':uuid', $uuid);
	$stmt->bindParam(':serie', $serie);
	$stmt->bindParam(':folio', $folio);
	$facturado = (int)$facturado;
	$stmt->bindValue(':facturado', $facturado, PDO::PARAM_INT);
	$facturado2 = (int)$facturado2;
	$stmt->bindValue(':facturado2', $facturado2, PDO::PARAM_INT);
	$facturado3 = (int)$facturado3;
	$stmt->bindValue(':facturado3', $facturado3, PDO::PARAM_INT);
	$stmt->bindParam(':tipoFacturado', $tipoFacturado);
	$stmt->bindParam(':almacenTraspasoID', $almacenTraspasoID);
	$stmt->bindParam(':aperturaBombaID', $aperturaBombaID);
	$stmt->bindParam(':bombaNumero', $bombaNumero);
	$stmt->bindParam(':empleadoID', $empleadoID);
	$stmt->bindParam(':asistenciaID', $asistenciaID);
	$stmt->bindParam(':jornadaID', $jornadaID);
	$stmt->bindParam(':venta_Status', $venta_Status);
	$stmt->bindParam(':versionRegistro', $versionRegistro);
	$stmt->bindParam(':regEstado', $regEstado);
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


function Cambia($ventaProductoID,$almacenID,$establecimientoID,$ventaNumero,$ventaFecha,$ventaSubTotal,$ventaIVA,$ventaTotal,$SalidaSubTotalC,$SalidaIVAC,$SalidaTotalC,$tipoMovimientoID,$uuid,$serie,$folio,$facturado,$facturado2,$facturado3,$tipoFacturado,$almacenTraspasoID,$aperturaBombaID,$bombaNumero,$empleadoID,$asistenciaID,$jornadaID,$venta_Status,$versionRegistro,$regEstado,$regFechaUltimaModificacion,$regUsuarioUltimaModificacion,$regFormularioUltimaModificacion,$regVersionUltimaModificacion,$estadoReplica){
    
	$CampoNullSalidaSubTotalC=$facturado;
	if($facturado==" " or trim($facturado) == ""){
		$CampoNullSalidaSubTotalC='NULL';
	}
	$CampoNullSalidaIVAC=$facturado2;
	if($facturado2==" " or trim($facturado2) == ""){
		$CampoNullSalidaIVAC='NULL';
	}
	$CampoNullSalidaTotalC=$facturado3;
	if($facturado3==" " or trim($facturado3) == ""){
		$CampoNullSalidaTotalC='NULL';
	}
	$CampoNullFacturado1=$facturado;
	if($facturado==" " or trim($facturado) == ""){
		$CampoNullFacturado1='NULL';
	}
	$CampoNullFacturado2=$facturado2;
	if($facturado2==" " or trim($facturado2) == ""){
		$CampoNullFacturado2='NULL';
	}
	$CampoNullFacturado3=$facturado3;
	if($facturado3==" " or trim($facturado3) == ""){
		$CampoNullFacturado3='NULL';
	}
	$CampoNullFolio=$folio;
	if($folio==" " or trim($folio) == ""){
		$CampoNullFolio='NULL';
	}
	$CampoNullalmacenTraspasoID=$almacenTraspasoID;
	if($almacenTraspasoID==" " or trim($almacenTraspasoID) == ""){
		$CampoNullalmacenTraspasoID='NULL';
	}
	
	$query = "UPDATE " . $this->NombreTabla . " SET almacenID=:almacenID,establecimientoID=:establecimientoID,ventaNumero=:ventaNumero,ventaFecha=:ventaFecha,
	ventaSubTotal=:ventaSubTotal,ventaIVA=:ventaIVA,ventaTotal=:ventaTotal,
	SalidaSubTotalC=".$CampoNullSalidaSubTotalC.",SalidaIVAC=".$CampoNullSalidaIVAC.",SalidaTotalC=".$CampoNullSalidaTotalC.",tipoMovimientoID=:tipoMovimientoID,uuid=:uuid,
	serie=:serie,folio=".$CampoNullFolio.",facturado=".$CampoNullFacturado1.",facturado2=".$CampoNullFacturado2.",facturado3=".$CampoNullFacturado3.",tipoFacturado=:tipoFacturado,
	almacenTraspasoID=".$CampoNullalmacenTraspasoID.",aperturaBombaID=:aperturaBombaID,bombaNumero=:bombaNumero,empleadoID=:empleadoID,asistenciaID=:asistenciaID,jornadaID=:jornadaID,venta_Status=:venta_Status,versionRegistro=:versionRegistro,regEstado=:regEstado,regFechaUltimaModificacion=:regFechaUltimaModificacion,regUsuarioUltimaModificacion=:regUsuarioUltimaModificacion,regFormularioUltimaModificacion=:regFormularioUltimaModificacion,regVersionUltimaModificacion=:regVersionUltimaModificacion
	,estadoReplica=".$estadoReplica." 
		WHERE ventaProductoID=:ventaProductoID ";

    // prepare the query
    $stmt = $this->Conexion->prepare($query);
 
     // sanitize
	$ventaProductoID=htmlspecialchars(strip_tags($ventaProductoID));
	$almacenID=htmlspecialchars(strip_tags($almacenID));
	$establecimientoID=htmlspecialchars(strip_tags($establecimientoID));
	$ventaNumero=htmlspecialchars(strip_tags($ventaNumero));
	$ventaFecha=htmlspecialchars(strip_tags($ventaFecha));
	$ventaSubTotal=htmlspecialchars(strip_tags($ventaSubTotal));
	$ventaIVA=htmlspecialchars(strip_tags($ventaIVA));
	$ventaTotal=htmlspecialchars(strip_tags($ventaTotal));
	$tipoMovimientoID=htmlspecialchars(strip_tags($tipoMovimientoID));
	$uuid=htmlspecialchars(strip_tags($uuid));
	$serie=htmlspecialchars(strip_tags($serie));
	$tipoFacturado=htmlspecialchars(strip_tags($tipoFacturado));
	$aperturaBombaID=htmlspecialchars(strip_tags($aperturaBombaID));
	$bombaNumero=htmlspecialchars(strip_tags($bombaNumero));
	$empleadoID=htmlspecialchars(strip_tags($empleadoID));
	$asistenciaID=htmlspecialchars(strip_tags($asistenciaID));
	$jornadaID=htmlspecialchars(strip_tags($jornadaID));
	$venta_Status=htmlspecialchars(strip_tags($venta_Status));
	$versionRegistro=htmlspecialchars(strip_tags($versionRegistro));
	$regEstado=htmlspecialchars(strip_tags($regEstado));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($regFechaUltimaModificacion));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($regUsuarioUltimaModificacion));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($regFormularioUltimaModificacion));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($regVersionUltimaModificacion));
   
    // bind the values
	$stmt->bindParam(':ventaProductoID', $ventaProductoID);
	$stmt->bindParam(':almacenID', $almacenID);
	$stmt->bindParam(':establecimientoID', $establecimientoID);
	$stmt->bindParam(':ventaNumero', $ventaNumero);
	$stmt->bindParam(':ventaFecha', $ventaFecha);
	$stmt->bindParam(':ventaSubTotal', $ventaSubTotal);
	$stmt->bindParam(':ventaIVA', $ventaIVA);
	$stmt->bindParam(':ventaTotal', $ventaTotal);
	$stmt->bindParam(':tipoMovimientoID', $tipoMovimientoID);
	$stmt->bindParam(':uuid', $uuid);
	$stmt->bindParam(':serie', $serie);
	$stmt->bindParam(':tipoFacturado', $tipoFacturado);
	$stmt->bindParam(':aperturaBombaID', $aperturaBombaID);
	$stmt->bindParam(':bombaNumero', $bombaNumero);
	$stmt->bindParam(':empleadoID', $empleadoID);
	$stmt->bindParam(':asistenciaID', $asistenciaID);
	$stmt->bindParam(':jornadaID', $jornadaID);
	$stmt->bindParam(':venta_Status', $venta_Status);
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