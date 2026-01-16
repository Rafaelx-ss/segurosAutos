<?php 
class VentasCombustibles{ 
 
private $Conexion; 
private $Database; 
private $NombreTabla = 'VentasCombustibles'; 
 
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
    	$query = "select * from " . $this->NombreTabla .  " where 1 " . ($id > 0 ? " and ventaCombustibleID = ?" : "");
 
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
			" (ventaCombustibleID,ventaCombustibleFolioInterfaz,ventaCombustibleFecha,ventaCombustibleVolumen,ventaCombustibleImporte,ventaCombustiblePrecioVenta,ventaCombustibleIVAVenta,ventaCombustibleFactorIVA,ventaCombustibleIEPSVenta,ventaCombustibleFactorIEPS,ventaCombustibleLecturaElectronica,ventaCombustibleEstado,ventaCombustibleRemisionado,tipoDespachoID,facturado,facturado2,facturado3,tipoFacturado,uuid,serie,folio,empleadoID,asistenciaID,jornadaID,mangueraNumero,bombaNumero,establecimientoID,versionRegistro,regEstado,regFechaUltimaModificacion,regUsuarioUltimaModificacion,regFormularioUltimaModificacion,regVersionUltimaModificacion,
			fechahorainicio,fechahorafin,volumenInicial,volumenFinal,temperatura,presionAbsoluta,productoID,tanqueNumero,estadoReplica) VALUES ";
			
		$coma = false;
		$comaText = "";
		foreach($registros as $item)
		 {
			//echo $item['textoID']."lolol";
			 if($this->ObtenerDatos($item['ventaCombustibleID'])){
				$this->Cambia($item['ventaCombustibleID'],($item['ventaCombustibleFolioInterfaz']),($item['ventaCombustibleFecha']),($item['ventaCombustibleVolumen']),($item['ventaCombustibleImporte']),($item['ventaCombustiblePrecioVenta']),($item['ventaCombustibleIVAVenta']),($item['ventaCombustibleFactorIVA']),($item['ventaCombustibleIEPSVenta']),($item['ventaCombustibleFactorIEPS']),($item['ventaCombustibleLecturaElectronica']),($item['ventaCombustibleEstado']),($item['ventaCombustibleRemisionado']),$item['tipoDespachoID'],$item['facturado'],$item['facturado2'],$item['facturado3'],($item['tipoFacturado']),($item['uuid']),($item['serie']),($item['folio']),$item['empleadoID'],$item['asistenciaID'],$item['jornadaID'],$item['mangueraNumero'],$item['bombaNumero'],($item['establecimientoID']),$item['versionRegistro'],$item['regEstado'],($item['regFechaUltimaModificacion']),$item['regUsuarioUltimaModificacion'],$item['regFormularioUltimaModificacion'],$item['regVersionUltimaModificacion']
				,$item['fechahorainicio'],$item['fechahorafin'],$item['volumenInicial'],$item['volumenFinal'],$item['temperatura']
				,$item['presionAbsoluta'],$item['productoID'],$item['tanqueNumero'],$item['estadoReplica']);
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
				
				$CampoNullFolioInterfaz="".$item["ventaCombustibleFolioInterfaz"]."";
				if($item["ventaCombustibleFolioInterfaz"]==" " or trim($item["ventaCombustibleFolioInterfaz"]) == ""){
					$CampoNullFolioInterfaz='NULL';
				}
				$CampoNullLecturaElectronica=$item["ventaCombustibleLecturaElectronica"];
				if($item["ventaCombustibleLecturaElectronica"]==" " or trim($item["ventaCombustibleLecturaElectronica"]) == ""){
					$CampoNullLecturaElectronica='NULL';
				}
				$CampoNullRemisionado=$item["ventaCombustibleRemisionado"];
				if($item["ventaCombustibleRemisionado"]==" " or trim($item["ventaCombustibleRemisionado"]) == ""){
					$CampoNullRemisionado='NULL';
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
				$CampoNullFechaI=$item["fechahorainicio"];
				if($item["fechahorainicio"]==" " or trim($item["fechahorainicio"]) == ""){
					$CampoNullFechaI='NULL';
				}
				$CampoNullFechaF=$item["fechahorafin"];
				if($item["fechahorafin"]==" " or trim($item["fechahorafin"]) == ""){
					$CampoNullFechaF='NULL';
				}
				$CampoNullVolumenI=$item["volumenInicial"];
				if($item["volumenInicial"]==" " or trim($item["volumenInicial"]) == ""){
					$CampoNullVolumenI='NULL';
				}
				$CampoNullVolumenF=$item["volumenFinal"];
				if($item["volumenFinal"]==" " or trim($item["volumenFinal"]) == ""){
					$CampoNullVolumenF='NULL';
				}
				$CampoNullTemperatura=$item["temperatura"];
				if($item["temperatura"]==" " or trim($item["temperatura"]) == ""){
					$CampoNullTemperatura='NULL';
				}
				$CampoNullPresion=$item["presionAbsoluta"];
				if($item["presionAbsoluta"]==" " or trim($item["presionAbsoluta"]) == ""){
					$CampoNullPresion='NULL';
				}
				$CampoNullProductoID=$item["productoID"];
				if($item["productoID"]==" " or trim($item["productoID"]) == ""){
					$CampoNullProductoID='NULL';
				}
				$CampoNullTanqueNumero=$item["tanqueNumero"];
				if($item["tanqueNumero"]==" " or trim($item["tanqueNumero"]) == ""){
					$CampoNullTanqueNumero='NULL';
				}
				$CampoNullEstadoReplica=$item["estadoReplica"];
				if($item["estadoReplica"]==" " or trim($item["estadoReplica"]) == ""){
					$CampoNullEstadoReplica='NULL';
				}
				$consulta= $consulta . $comaText . "(" . $item["ventaCombustibleID"] . "," . $CampoNullFolioInterfaz . ",'" . ($item["ventaCombustibleFecha"]) . "','" . ($item["ventaCombustibleVolumen"]) . "','" . ($item["ventaCombustibleImporte"]) . "','" . ($item["ventaCombustiblePrecioVenta"]) . "','" . ($item["ventaCombustibleIVAVenta"]) . "','" . ($item["ventaCombustibleFactorIVA"]) . "','" . ($item["ventaCombustibleIEPSVenta"]) . "','" . ($item["ventaCombustibleFactorIEPS"]) . "',
				" . $CampoNullLecturaElectronica . ",'" . ($item["ventaCombustibleEstado"]) . "'," . $CampoNullRemisionado . "," . $item["tipoDespachoID"] . ",
				" . $CampoNullFacturado1 . "," . $CampoNullFacturado2 . "," . $CampoNullFacturado3 . ",'" . ($item["tipoFacturado"]) . "','" . ($item["uuid"]) . "','" . ($item["serie"]) . "',
				" . $CampoNullFolio . "," . $item["empleadoID"] . "," . $item["asistenciaID"] . "," . $item["jornadaID"] . "," . $item["mangueraNumero"] . "," . $item["bombaNumero"] . ",'" . ($item["establecimientoID"]) . "'," . $item["versionRegistro"] . "," . $item["regEstado"] . ",'" . ($item["regFechaUltimaModificacion"]) . "'," . $item["regUsuarioUltimaModificacion"] . "," . $item["regFormularioUltimaModificacion"] . "," . $item["regVersionUltimaModificacion"] . ",
				" . $CampoNullFechaI . "," . $CampoNullFechaF . "," . $CampoNullVolumenI . "," . $CampoNullVolumenF . "," . $CampoNullTemperatura . ",
				" . $CampoNullPresion . "," . $CampoNullProductoID . "," . $CampoNullTanqueNumero . "," . $CampoNullEstadoReplica . ")";
			}
			 
		   
		 }
	if(!$coma){
		$consulta="select 1";
	}
	
	$this->query=$consulta;
	
    // prepare the query
    $stmt = $this->Conexion->prepare($this->query);
	
    // sanitize
	$ventaCombustibleID=htmlspecialchars(strip_tags($item['ventaCombustibleID']));
	$ventaCombustibleFolioInterfaz=htmlspecialchars(strip_tags($item['ventaCombustibleFolioInterfaz']));
	$ventaCombustibleFecha=htmlspecialchars(strip_tags($item['ventaCombustibleFecha']));
	$ventaCombustibleVolumen=htmlspecialchars(strip_tags($item['ventaCombustibleVolumen']));
	$ventaCombustibleImporte=htmlspecialchars(strip_tags($item['ventaCombustibleImporte']));
	$ventaCombustiblePrecioVenta=htmlspecialchars(strip_tags($item['ventaCombustiblePrecioVenta']));
	$ventaCombustibleIVAVenta=htmlspecialchars(strip_tags($item['ventaCombustibleIVAVenta']));
	$ventaCombustibleFactorIVA=htmlspecialchars(strip_tags($item['ventaCombustibleFactorIVA']));
	$ventaCombustibleIEPSVenta=htmlspecialchars(strip_tags($item['ventaCombustibleIEPSVenta']));
	$ventaCombustibleFactorIEPS=htmlspecialchars(strip_tags($item['ventaCombustibleFactorIEPS']));
	$ventaCombustibleLecturaElectronica=htmlspecialchars(strip_tags($item['ventaCombustibleLecturaElectronica']));
	$ventaCombustibleEstado=htmlspecialchars(strip_tags($item['ventaCombustibleEstado']));
	$ventaCombustibleRemisionado=htmlspecialchars(strip_tags($item['ventaCombustibleRemisionado']));
	$tipoDespachoID=htmlspecialchars(strip_tags($item['tipoDespachoID']));
	$facturado=htmlspecialchars(strip_tags($item['facturado']));
	$facturado2=htmlspecialchars(strip_tags($item['facturado2']));
	$facturado3=htmlspecialchars(strip_tags($item['facturado3']));
	$tipoFacturado=htmlspecialchars(strip_tags($item['tipoFacturado']));
	$uuid=htmlspecialchars(strip_tags($item['uuid']));
	$serie=htmlspecialchars(strip_tags($item['serie']));
	$folio=htmlspecialchars(strip_tags($item['folio']));
	$empleadoID=htmlspecialchars(strip_tags($item['empleadoID']));
	$asistenciaID=htmlspecialchars(strip_tags($item['asistenciaID']));
	$jornadaID=htmlspecialchars(strip_tags($item['jornadaID']));
	$mangueraNumero=htmlspecialchars(strip_tags($item['mangueraNumero']));
	$bombaNumero=htmlspecialchars(strip_tags($item['bombaNumero']));
	$establecimientoID=htmlspecialchars(strip_tags($item['establecimientoID']));
	$versionRegistro=htmlspecialchars(strip_tags($item['versionRegistro']));
	$regEstado=htmlspecialchars(strip_tags($item['regEstado']));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($item['regFechaUltimaModificacion']));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($item['regUsuarioUltimaModificacion']));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($item['regFormularioUltimaModificacion']));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($item['regVersionUltimaModificacion']));
   
    // bind the values
	$stmt->bindParam(':ventaCombustibleID', $ventaCombustibleID);
	$stmt->bindParam(':ventaCombustibleFolioInterfaz', $ventaCombustibleFolioInterfaz);
	$stmt->bindParam(':ventaCombustibleFecha', $ventaCombustibleFecha);
	$stmt->bindParam(':ventaCombustibleVolumen', $ventaCombustibleVolumen);
	$stmt->bindParam(':ventaCombustibleImporte', $ventaCombustibleImporte);
	$stmt->bindParam(':ventaCombustiblePrecioVenta', $ventaCombustiblePrecioVenta);
	$stmt->bindParam(':ventaCombustibleIVAVenta', $ventaCombustibleIVAVenta);
	$stmt->bindParam(':ventaCombustibleFactorIVA', $ventaCombustibleFactorIVA);
	$stmt->bindParam(':ventaCombustibleIEPSVenta', $ventaCombustibleIEPSVenta);
	$stmt->bindParam(':ventaCombustibleFactorIEPS', $ventaCombustibleFactorIEPS);
	$stmt->bindParam(':ventaCombustibleLecturaElectronica', $ventaCombustibleLecturaElectronica);
	$stmt->bindParam(':ventaCombustibleEstado', $ventaCombustibleEstado);
	$stmt->bindParam(':ventaCombustibleRemisionado', $ventaCombustibleRemisionado);
	$stmt->bindParam(':tipoDespachoID', $tipoDespachoID);
	$facturado = (int)$facturado;
	$stmt->bindValue(':facturado', $facturado, PDO::PARAM_INT);
	$facturado2 = (int)$facturado2;
	$stmt->bindValue(':facturado2', $facturado2, PDO::PARAM_INT);
	$facturado3 = (int)$facturado3;
	$stmt->bindValue(':facturado3', $facturado3, PDO::PARAM_INT);
	$stmt->bindParam(':tipoFacturado', $tipoFacturado);
	$stmt->bindParam(':uuid', $uuid);
	$stmt->bindParam(':serie', $serie);
	$stmt->bindParam(':folio', $folio);
	$stmt->bindParam(':empleadoID', $empleadoID);
	$stmt->bindParam(':asistenciaID', $asistenciaID);
	$stmt->bindParam(':jornadaID', $jornadaID);
	$stmt->bindParam(':mangueraNumero', $mangueraNumero);
	$stmt->bindParam(':bombaNumero', $bombaNumero);
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


function InsertaRegreso($item){
	
$Registo= "0";
try{
    $consulta="";
	
	$consulta = 'INSERT INTO ' . $this->NombreTabla . 
			" (ventaCombustibleID,ventaCombustibleFolioInterfaz,ventaCombustibleFecha,ventaCombustibleVolumen,ventaCombustibleImporte,ventaCombustiblePrecioVenta,ventaCombustibleIVAVenta,ventaCombustibleFactorIVA,ventaCombustibleIEPSVenta,ventaCombustibleFactorIEPS,ventaCombustibleLecturaElectronica,ventaCombustibleEstado,ventaCombustibleRemisionado,tipoDespachoID,facturado,facturado2,facturado3,tipoFacturado,uuid,serie,folio,empleadoID,asistenciaID,jornadaID,mangueraNumero,bombaNumero,establecimientoID,versionRegistro,regEstado,regFechaUltimaModificacion,regUsuarioUltimaModificacion,regFormularioUltimaModificacion,regVersionUltimaModificacion,
			fechahorainicio,fechahorafin,volumenInicial,volumenFinal,temperatura,presionAbsoluta,productoID,tanqueNumero,estadoReplica) VALUES ";
			
		$coma = false;
		$comaText = "";
		$Registo=$item['ventaCombustibleID'];
		if($this->ObtenerDatos($item['ventaCombustibleID'])){
			$this->Cambia($item['ventaCombustibleID'],($item['ventaCombustibleFolioInterfaz']),($item['ventaCombustibleFecha']),($item['ventaCombustibleVolumen']),($item['ventaCombustibleImporte']),($item['ventaCombustiblePrecioVenta']),($item['ventaCombustibleIVAVenta']),($item['ventaCombustibleFactorIVA']),($item['ventaCombustibleIEPSVenta']),($item['ventaCombustibleFactorIEPS']),($item['ventaCombustibleLecturaElectronica']),($item['ventaCombustibleEstado']),($item['ventaCombustibleRemisionado']),$item['tipoDespachoID'],$item['facturado'],$item['facturado2'],$item['facturado3'],($item['tipoFacturado']),($item['uuid']),($item['serie']),($item['folio']),$item['empleadoID'],$item['asistenciaID'],$item['jornadaID'],$item['mangueraNumero'],$item['bombaNumero'],($item['establecimientoID']),$item['versionRegistro'],$item['regEstado'],($item['regFechaUltimaModificacion']),$item['regUsuarioUltimaModificacion'],$item['regFormularioUltimaModificacion'],$item['regVersionUltimaModificacion']
			,$item['fechahorainicio'],$item['fechahorafin'],$item['volumenInicial'],$item['volumenFinal'],$item['temperatura']
			,$item['presionAbsoluta'],$item['productoID'],$item['tanqueNumero'],$item['estadoReplica']);
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
			
			$CampoNullFolioInterfaz="".$item["ventaCombustibleFolioInterfaz"]."";
			if($item["ventaCombustibleFolioInterfaz"]==" " or trim($item["ventaCombustibleFolioInterfaz"]) == ""){
				$CampoNullFolioInterfaz='NULL';
			}
			$CampoNullLecturaElectronica=$item["ventaCombustibleLecturaElectronica"];
			if($item["ventaCombustibleLecturaElectronica"]==" " or trim($item["ventaCombustibleLecturaElectronica"]) == ""){
				$CampoNullLecturaElectronica='NULL';
			}
			$CampoNullRemisionado=$item["ventaCombustibleRemisionado"];
			if($item["ventaCombustibleRemisionado"]==" " or trim($item["ventaCombustibleRemisionado"]) == ""){
				$CampoNullRemisionado='NULL';
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
			$CampoNullFechaI=$item["fechahorainicio"];
			if($item["fechahorainicio"]==" " or trim($item["fechahorainicio"]) == ""){
				$CampoNullFechaI='NULL';
			}
			$CampoNullFechaF=$item["fechahorafin"];
			if($item["fechahorafin"]==" " or trim($item["fechahorafin"]) == ""){
				$CampoNullFechaF='NULL';
			}
			$CampoNullVolumenI=$item["volumenInicial"];
			if($item["volumenInicial"]==" " or trim($item["volumenInicial"]) == ""){
				$CampoNullVolumenI='NULL';
			}
			$CampoNullVolumenF=$item["volumenFinal"];
			if($item["volumenFinal"]==" " or trim($item["volumenFinal"]) == ""){
				$CampoNullVolumenF='NULL';
			}
			$CampoNullTemperatura=$item["temperatura"];
			if($item["temperatura"]==" " or trim($item["temperatura"]) == ""){
				$CampoNullTemperatura='NULL';
			}
			$CampoNullPresion=$item["presionAbsoluta"];
			if($item["presionAbsoluta"]==" " or trim($item["presionAbsoluta"]) == ""){
				$CampoNullPresion='NULL';
			}
			$CampoNullProductoID=$item["productoID"];
			if($item["productoID"]==" " or trim($item["productoID"]) == ""){
				$CampoNullProductoID='NULL';
			}
			$CampoNullTanqueNumero=$item["tanqueNumero"];
			if($item["tanqueNumero"]==" " or trim($item["tanqueNumero"]) == ""){
				$CampoNullTanqueNumero='NULL';
			}
			$CampoNullEstadoReplica=$item["estadoReplica"];
			if($item["estadoReplica"]==" " or trim($item["estadoReplica"]) == ""){
				$CampoNullEstadoReplica='NULL';
			}
			$consulta= $consulta . $comaText . "(" . $item["ventaCombustibleID"] . "," . $CampoNullFolioInterfaz . ",'" . ($item["ventaCombustibleFecha"]) . "','" . ($item["ventaCombustibleVolumen"]) . "','" . ($item["ventaCombustibleImporte"]) . "','" . ($item["ventaCombustiblePrecioVenta"]) . "','" . ($item["ventaCombustibleIVAVenta"]) . "','" . ($item["ventaCombustibleFactorIVA"]) . "','" . ($item["ventaCombustibleIEPSVenta"]) . "','" . ($item["ventaCombustibleFactorIEPS"]) . "',
			" . $CampoNullLecturaElectronica . ",'" . ($item["ventaCombustibleEstado"]) . "'," . $CampoNullRemisionado . "," . $item["tipoDespachoID"] . ",
			" . $CampoNullFacturado1 . "," . $CampoNullFacturado2 . "," . $CampoNullFacturado3 . ",'" . ($item["tipoFacturado"]) . "','" . ($item["uuid"]) . "','" . ($item["serie"]) . "',
			" . $CampoNullFolio . "," . $item["empleadoID"] . "," . $item["asistenciaID"] . "," . $item["jornadaID"] . "," . $item["mangueraNumero"] . "," . $item["bombaNumero"] . ",'" . ($item["establecimientoID"]) . "'," . $item["versionRegistro"] . "," . $item["regEstado"] . ",'" . ($item["regFechaUltimaModificacion"]) . "'," . $item["regUsuarioUltimaModificacion"] . "," . $item["regFormularioUltimaModificacion"] . "," . $item["regVersionUltimaModificacion"] . ",
			" . $CampoNullFechaI . "," . $CampoNullFechaF . "," . $CampoNullVolumenI . "," . $CampoNullVolumenF . "," . $CampoNullTemperatura . ",
			" . $CampoNullPresion . "," . $CampoNullProductoID . "," . $CampoNullTanqueNumero . "," . $CampoNullEstadoReplica . ")";
		}
	if(!$coma){
		$consulta="select 1";
	}
	
	$this->query=$consulta;
	
    // prepare the query
    $stmt = $this->Conexion->prepare($this->query);
	
    // sanitize
	$ventaCombustibleID=htmlspecialchars(strip_tags($item['ventaCombustibleID']));
	$ventaCombustibleFolioInterfaz=htmlspecialchars(strip_tags($item['ventaCombustibleFolioInterfaz']));
	$ventaCombustibleFecha=htmlspecialchars(strip_tags($item['ventaCombustibleFecha']));
	$ventaCombustibleVolumen=htmlspecialchars(strip_tags($item['ventaCombustibleVolumen']));
	$ventaCombustibleImporte=htmlspecialchars(strip_tags($item['ventaCombustibleImporte']));
	$ventaCombustiblePrecioVenta=htmlspecialchars(strip_tags($item['ventaCombustiblePrecioVenta']));
	$ventaCombustibleIVAVenta=htmlspecialchars(strip_tags($item['ventaCombustibleIVAVenta']));
	$ventaCombustibleFactorIVA=htmlspecialchars(strip_tags($item['ventaCombustibleFactorIVA']));
	$ventaCombustibleIEPSVenta=htmlspecialchars(strip_tags($item['ventaCombustibleIEPSVenta']));
	$ventaCombustibleFactorIEPS=htmlspecialchars(strip_tags($item['ventaCombustibleFactorIEPS']));
	$ventaCombustibleLecturaElectronica=htmlspecialchars(strip_tags($item['ventaCombustibleLecturaElectronica']));
	$ventaCombustibleEstado=htmlspecialchars(strip_tags($item['ventaCombustibleEstado']));
	$ventaCombustibleRemisionado=htmlspecialchars(strip_tags($item['ventaCombustibleRemisionado']));
	$tipoDespachoID=htmlspecialchars(strip_tags($item['tipoDespachoID']));
	$facturado=htmlspecialchars(strip_tags($item['facturado']));
	$facturado2=htmlspecialchars(strip_tags($item['facturado2']));
	$facturado3=htmlspecialchars(strip_tags($item['facturado3']));
	$tipoFacturado=htmlspecialchars(strip_tags($item['tipoFacturado']));
	$uuid=htmlspecialchars(strip_tags($item['uuid']));
	$serie=htmlspecialchars(strip_tags($item['serie']));
	$folio=htmlspecialchars(strip_tags($item['folio']));
	$empleadoID=htmlspecialchars(strip_tags($item['empleadoID']));
	$asistenciaID=htmlspecialchars(strip_tags($item['asistenciaID']));
	$jornadaID=htmlspecialchars(strip_tags($item['jornadaID']));
	$mangueraNumero=htmlspecialchars(strip_tags($item['mangueraNumero']));
	$bombaNumero=htmlspecialchars(strip_tags($item['bombaNumero']));
	$establecimientoID=htmlspecialchars(strip_tags($item['establecimientoID']));
	$versionRegistro=htmlspecialchars(strip_tags($item['versionRegistro']));
	$regEstado=htmlspecialchars(strip_tags($item['regEstado']));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($item['regFechaUltimaModificacion']));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($item['regUsuarioUltimaModificacion']));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($item['regFormularioUltimaModificacion']));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($item['regVersionUltimaModificacion']));
   
    // bind the values
	$stmt->bindParam(':ventaCombustibleID', $ventaCombustibleID);
	$stmt->bindParam(':ventaCombustibleFolioInterfaz', $ventaCombustibleFolioInterfaz);
	$stmt->bindParam(':ventaCombustibleFecha', $ventaCombustibleFecha);
	$stmt->bindParam(':ventaCombustibleVolumen', $ventaCombustibleVolumen);
	$stmt->bindParam(':ventaCombustibleImporte', $ventaCombustibleImporte);
	$stmt->bindParam(':ventaCombustiblePrecioVenta', $ventaCombustiblePrecioVenta);
	$stmt->bindParam(':ventaCombustibleIVAVenta', $ventaCombustibleIVAVenta);
	$stmt->bindParam(':ventaCombustibleFactorIVA', $ventaCombustibleFactorIVA);
	$stmt->bindParam(':ventaCombustibleIEPSVenta', $ventaCombustibleIEPSVenta);
	$stmt->bindParam(':ventaCombustibleFactorIEPS', $ventaCombustibleFactorIEPS);
	$stmt->bindParam(':ventaCombustibleLecturaElectronica', $ventaCombustibleLecturaElectronica);
	$stmt->bindParam(':ventaCombustibleEstado', $ventaCombustibleEstado);
	$stmt->bindParam(':ventaCombustibleRemisionado', $ventaCombustibleRemisionado);
	$stmt->bindParam(':tipoDespachoID', $tipoDespachoID);
	$facturado = (int)$facturado;
	$stmt->bindValue(':facturado', $facturado, PDO::PARAM_INT);
	$facturado2 = (int)$facturado2;
	$stmt->bindValue(':facturado2', $facturado2, PDO::PARAM_INT);
	$facturado3 = (int)$facturado3;
	$stmt->bindValue(':facturado3', $facturado3, PDO::PARAM_INT);
	$stmt->bindParam(':tipoFacturado', $tipoFacturado);
	$stmt->bindParam(':uuid', $uuid);
	$stmt->bindParam(':serie', $serie);
	$stmt->bindParam(':folio', $folio);
	$stmt->bindParam(':empleadoID', $empleadoID);
	$stmt->bindParam(':asistenciaID', $asistenciaID);
	$stmt->bindParam(':jornadaID', $jornadaID);
	$stmt->bindParam(':mangueraNumero', $mangueraNumero);
	$stmt->bindParam(':bombaNumero', $bombaNumero);
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

function Cambia($ventaCombustibleID,$ventaCombustibleFolioInterfaz,$ventaCombustibleFecha,$ventaCombustibleVolumen,$ventaCombustibleImporte,$ventaCombustiblePrecioVenta,$ventaCombustibleIVAVenta,$ventaCombustibleFactorIVA,$ventaCombustibleIEPSVenta,$ventaCombustibleFactorIEPS,$ventaCombustibleLecturaElectronica,$ventaCombustibleEstado,$ventaCombustibleRemisionado,$tipoDespachoID,$facturado,$facturado2,$facturado3,$tipoFacturado,$uuid,$serie,$folio,$empleadoID,$asistenciaID,$jornadaID,$mangueraNumero,$bombaNumero,$establecimientoID,$versionRegistro,$regEstado,$regFechaUltimaModificacion,$regUsuarioUltimaModificacion,$regFormularioUltimaModificacion,$regVersionUltimaModificacion,
$fechahorainicio,$fechahorafin,$volumenInicial,$volumenFinal,$temperatura,$presionAbsoluta,$productoID,$tanqueNumero,$estadoReplica){
    
	$ventaCombustibleID."-".$ventaCombustibleFolioInterfaz."-".$ventaCombustibleFecha."-".$ventaCombustibleVolumen."-".$ventaCombustibleImporte."-".$ventaCombustiblePrecioVenta."-".$ventaCombustibleIVAVenta."-".$ventaCombustibleFactorIVA."-".
	$ventaCombustibleIEPSVenta."-".$ventaCombustibleFactorIEPS."-".$ventaCombustibleLecturaElectronica."-".$ventaCombustibleEstado."-".$ventaCombustibleRemisionado."-".$tipoDespachoID."-".
	$facturado."-".$facturado2."-".$facturado3."-".$tipoFacturado."-".$uuid."-".$serie."-".$folio."-".$empleadoID."-".$asistenciaID."-".$jornadaID."-".$mangueraNumero."-".$bombaNumero."-".
	$establecimientoID."-".$versionRegistro."-".$regEstado."-".$regFechaUltimaModificacion."-".$regUsuarioUltimaModificacion."-".$regFormularioUltimaModificacion."-".
	$regVersionUltimaModificacion."-".$fechahorainicio."-".$fechahorafin."-".$volumenInicial."-".$volumenFinal."-".$temperatura."-".$presionAbsoluta."-".$productoID."-".
	$tanqueNumero."-".$estadoReplica;

	$CampoNullFolioInterfaz="".$ventaCombustibleFolioInterfaz."";
	if($ventaCombustibleFolioInterfaz==" " or trim($ventaCombustibleFolioInterfaz) == ""){
		$CampoNullFolioInterfaz='NULL';
	}
	$CampoNullLecturaElectronica=$ventaCombustibleLecturaElectronica;
	if($ventaCombustibleLecturaElectronica==" " or trim($ventaCombustibleLecturaElectronica) == ""){
		$CampoNullLecturaElectronica='NULL';
	}
	$CampoNullRemisionado=$ventaCombustibleRemisionado;
	if($ventaCombustibleRemisionado ==" " or trim($ventaCombustibleRemisionado) == ""){
		$CampoNullRemisionado='NULL';
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
	$CampoNullFechaI="'".$fechahorainicio."'";
	if($fechahorainicio==" " or trim($fechahorainicio) == ""){
		$CampoNullFechaI='NULL';
	}
	$CampoNullFechaF="'".$fechahorafin."'";
	if($fechahorafin==" " or trim($fechahorafin) == ""){
		$CampoNullFechaF='NULL';
	}
	$CampoNullVolumenI=$volumenInicial;
	if($volumenInicial==" " or trim($volumenInicial) == ""){
		$CampoNullVolumenI='NULL';
	}
	$CampoNullVolumenF=$volumenFinal;
	if($volumenFinal==" " or trim($volumenFinal) == ""){
		$CampoNullVolumenF='NULL';
	}
	$CampoNullTemperatura=$temperatura;
	if($temperatura==" " or trim($temperatura) == ""){
		$CampoNullTemperatura='NULL';
	}
	$CampoNullPresion=$presionAbsoluta;
	if($presionAbsoluta==" " or trim($presionAbsoluta) == ""){
		$CampoNullPresion='NULL';
	}
	$CampoNullProductoID=$productoID;
	if($productoID==" " or trim($productoID) == ""){
		$CampoNullProductoID='NULL';
	}
	$CampoNullTanqueNumero=$tanqueNumero;
	if($tanqueNumero==" " or trim($tanqueNumero) == ""){
		$CampoNullTanqueNumero='NULL';
	}
	$CampoNullEstadoReplica=$estadoReplica;
	if($estadoReplica==" " or trim($estadoReplica) == ""){
		$CampoNullEstadoReplica='NULL';
	}
	$query = "UPDATE " . $this->NombreTabla . " SET ventaCombustibleFolioInterfaz=:ventaCombustibleFolioInterfaz,ventaCombustibleFecha=:ventaCombustibleFecha,ventaCombustibleVolumen=:ventaCombustibleVolumen,ventaCombustibleImporte=:ventaCombustibleImporte,ventaCombustiblePrecioVenta=:ventaCombustiblePrecioVenta,ventaCombustibleIVAVenta=:ventaCombustibleIVAVenta,ventaCombustibleFactorIVA=:ventaCombustibleFactorIVA,ventaCombustibleIEPSVenta=:ventaCombustibleIEPSVenta,ventaCombustibleFactorIEPS=:ventaCombustibleFactorIEPS,
	ventaCombustibleLecturaElectronica=".$CampoNullLecturaElectronica.",ventaCombustibleEstado=:ventaCombustibleEstado,ventaCombustibleRemisionado=".$CampoNullRemisionado.",tipoDespachoID=:tipoDespachoID,facturado=:facturado,facturado2=:facturado2,facturado3=:facturado3,tipoFacturado=:tipoFacturado,uuid=:uuid,serie=:serie,
	folio=".$CampoNullFolio.",empleadoID=:empleadoID,asistenciaID=:asistenciaID,jornadaID=:jornadaID,mangueraNumero=:mangueraNumero,bombaNumero=:bombaNumero,establecimientoID=:establecimientoID,versionRegistro=:versionRegistro,regEstado=:regEstado,regFechaUltimaModificacion=:regFechaUltimaModificacion,regUsuarioUltimaModificacion=:regUsuarioUltimaModificacion,regFormularioUltimaModificacion=:regFormularioUltimaModificacion,regVersionUltimaModificacion=:regVersionUltimaModificacion,
	fechahorainicio=".$CampoNullFechaI.",fechahorafin=".$CampoNullFechaF.",volumenInicial=".$CampoNullVolumenI.",volumenFinal=".$CampoNullVolumenF.",temperatura=". $CampoNullTemperatura.",
	presionAbsoluta=".$CampoNullPresion.",productoID=".$CampoNullProductoID.",tanqueNumero=".$CampoNullTanqueNumero.",estadoReplica=".$CampoNullEstadoReplica."	
		WHERE ventaCombustibleID=:ventaCombustibleID ";

    // prepare the query
    $stmt = $this->Conexion->prepare($query);
 
     // sanitize
	$ventaCombustibleID=htmlspecialchars(strip_tags($ventaCombustibleID));
	$ventaCombustibleFolioInterfaz=htmlspecialchars(strip_tags($CampoNullFolioInterfaz));
	$ventaCombustibleFecha=htmlspecialchars(strip_tags($ventaCombustibleFecha));
	$ventaCombustibleVolumen=htmlspecialchars(strip_tags($ventaCombustibleVolumen));
	$ventaCombustibleImporte=htmlspecialchars(strip_tags($ventaCombustibleImporte));
	$ventaCombustiblePrecioVenta=htmlspecialchars(strip_tags($ventaCombustiblePrecioVenta));
	$ventaCombustibleIVAVenta=htmlspecialchars(strip_tags($ventaCombustibleIVAVenta));
	$ventaCombustibleFactorIVA=htmlspecialchars(strip_tags($ventaCombustibleFactorIVA));
	$ventaCombustibleIEPSVenta=htmlspecialchars(strip_tags($ventaCombustibleIEPSVenta));
	$ventaCombustibleFactorIEPS=htmlspecialchars(strip_tags($ventaCombustibleFactorIEPS));
	$ventaCombustibleEstado=htmlspecialchars(strip_tags($ventaCombustibleEstado));
	$tipoDespachoID=htmlspecialchars(strip_tags($tipoDespachoID));
	$facturado=htmlspecialchars(strip_tags($CampoNullFacturado1));
	$facturado2=htmlspecialchars(strip_tags($CampoNullFacturado2));
	$facturado3=htmlspecialchars(strip_tags($CampoNullFacturado3));
	$tipoFacturado=htmlspecialchars(strip_tags($tipoFacturado));
	$uuid=htmlspecialchars(strip_tags($uuid));
	$serie=htmlspecialchars(strip_tags($serie));
	$empleadoID=htmlspecialchars(strip_tags($empleadoID));
	$asistenciaID=htmlspecialchars(strip_tags($asistenciaID));
	$jornadaID=htmlspecialchars(strip_tags($jornadaID));
	$mangueraNumero=htmlspecialchars(strip_tags($mangueraNumero));
	$bombaNumero=htmlspecialchars(strip_tags($bombaNumero));
	$establecimientoID=htmlspecialchars(strip_tags($establecimientoID));
	$versionRegistro=htmlspecialchars(strip_tags($versionRegistro));
	$regEstado=htmlspecialchars(strip_tags($regEstado));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($regFechaUltimaModificacion));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($regUsuarioUltimaModificacion));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($regFormularioUltimaModificacion));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($regVersionUltimaModificacion));
   
    // bind the values
	$stmt->bindParam(':ventaCombustibleID', $ventaCombustibleID);
	$stmt->bindParam(':ventaCombustibleFolioInterfaz', $ventaCombustibleFolioInterfaz);
	$stmt->bindParam(':ventaCombustibleFecha', $ventaCombustibleFecha);
	$stmt->bindParam(':ventaCombustibleVolumen', $ventaCombustibleVolumen);
	$stmt->bindParam(':ventaCombustibleImporte', $ventaCombustibleImporte);
	$stmt->bindParam(':ventaCombustiblePrecioVenta', $ventaCombustiblePrecioVenta);
	$stmt->bindParam(':ventaCombustibleIVAVenta', $ventaCombustibleIVAVenta);
	$stmt->bindParam(':ventaCombustibleFactorIVA', $ventaCombustibleFactorIVA);
	$stmt->bindParam(':ventaCombustibleIEPSVenta', $ventaCombustibleIEPSVenta);
	$stmt->bindParam(':ventaCombustibleFactorIEPS', $ventaCombustibleFactorIEPS);
	$stmt->bindParam(':ventaCombustibleEstado', $ventaCombustibleEstado);
	$stmt->bindParam(':tipoDespachoID', $tipoDespachoID);
	$facturado = (int)$facturado;
	$stmt->bindValue(':facturado', $facturado, PDO::PARAM_INT);
	$facturado2 = (int)$facturado2;
	$stmt->bindValue(':facturado2', $facturado2, PDO::PARAM_INT);
	$facturado3 = (int)$facturado3;
	$stmt->bindValue(':facturado3', $facturado3, PDO::PARAM_INT);
	$stmt->bindParam(':tipoFacturado', $tipoFacturado);
	$stmt->bindParam(':uuid', $uuid);
	$stmt->bindParam(':serie', $serie);
	$stmt->bindParam(':empleadoID', $empleadoID);
	$stmt->bindParam(':asistenciaID', $asistenciaID);
	$stmt->bindParam(':jornadaID', $jornadaID);
	$stmt->bindParam(':mangueraNumero', $mangueraNumero);
	$stmt->bindParam(':bombaNumero', $bombaNumero);
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
	

function ODR($tabla="", $FechaInicial="", $FechaFinal="", $ValidaRegistrosEnviados=true){
	try{
		$query= "SELECT *from jornadas where estadoReplica in(0,1)";
		$stmt = $this->Conexion->prepare( $query );
		
		$stmt->execute();
		
		$num = $stmt->rowCount();

		if($num>0){
			$this->Dataset = $stmt->fetchAll(PDO::FETCH_ASSOC);
		}
	}
	catch (Exception $e){
		echo $this->Mensaje = "ERROR: ".$e->getMessage();//.'<br /> <br />Consulta: <br />'.$consulta;
	}
	
	return false;
}

function ObtenerDatosReplica($tabla="", $FechaInicial="", $FechaFinal="", $ValidaRegistrosEnviados=true){
	try{
		$cadenaFecha="";
		$cadenaValidacion="";
		
		if($FechaInicial != ""){
			if($tabla == "VentasCombustibles"){
				$cadenaFecha=" and ventaCombustibleFecha between '".$FechaInicial."' and '".$FechaFinal."'";
			}
		}
		if($ValidaRegistrosEnviados){
			$cadenaValidacion= " and estadoReplica in(0,1)";
		}
		
		$query= "SELECT *from " . $tabla . " where 1 ".$cadenaFecha. $cadenaValidacion ."";
		$stmt = $this->Conexion->prepare( $query );
		
		$stmt->execute();
		
		$num = $stmt->rowCount();

		if($num>0){
			$this->Dataset = $stmt->fetchAll(PDO::FETCH_ASSOC);
			return $this->Dataset;
		}
		
	}
	catch (Exception $e){
		echo $this->Mensaje = "ERROR: ".$e->getMessage();//.'<br /> <br />Consulta: <br />'.$consulta;
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