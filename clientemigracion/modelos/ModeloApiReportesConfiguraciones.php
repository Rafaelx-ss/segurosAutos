<?php 
class ReportesConfiguraciones{ 
 
private $Conexion; 
private $Database; 
private $NombreTabla = 'ReportesConfiguraciones'; 
 
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
		
	$query = "select * from " . $this->NombreTabla .  " where 1 " . ($id > 0 ? " and reporteConfiguracionID = ?" : "");
		
                
                //$this->LogMigracion("Api".$this->NombreTabla, "Consulta",$query, "Consulta");
		//$query = "select * from " . $this->NombreTabla .  " where 1 " . ($id > 0 ? " and reporteConfiguracionID = ?" : "");
    	//echo $query;
		//echo "<br />";
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
    
    //$this->LogMigracion("ApiConfiguracionesSlider", "Inserta 2", "Registo ", "inserta 2");
    foreach($registros as $item){
        $this->InsertaRegreso($item);
    }
    
    return true;
}
function Inserta2($registros){

    $consulta="";
	
	$consulta = 'INSERT INTO ' . $this->NombreTabla . 
			" (reporteConfiguracionID,templateReporteID,nombreReporte,queryReporte,columnasReporte,imprimirLogoPdf,imprimirEncabezado,imprimirFechaHora,imprimirNombreUsuario,imprimirLogoExcel,imprimirPie,imprimirEncabezadoExcel,imprimirFechaHoraExcel,imprimirNombreUsuarioExcel,versionRegistro,regEstado,regFechaUltimaModificacion,regUsuarioUltimaModificacion,regFormularioUltimaModificacion,regVersionUltimaModificacion,orientacionPagina) VALUES ";
			
		$coma = false;
		$comaText = "";
		foreach($registros as $item)
		 {
			//echo $item['textoID']."lolol";
			 if($this->ObtenerDatos($item['reporteConfiguracionID'],"CONSULTA_LOCAL")){
				$this->Cambia($item['reporteConfiguracionID'],$item['templateReporteID'],($item['nombreReporte']),($item['queryReporte']),($item['columnasReporte']),$item['imprimirLogoPdf'],$item['imprimirEncabezado'],$item['imprimirFechaHora'],$item['imprimirNombreUsuario'],$item['imprimirLogoExcel'],$item['imprimirPie'],$item['imprimirEncabezadoExcel'],$item['imprimirFechaHoraExcel'],$item['imprimirNombreUsuarioExcel'],$item['versionRegistro'],$item['regEstado'],($item['regFechaUltimaModificacion']),$item['regUsuarioUltimaModificacion'],$item['regFormularioUltimaModificacion'],$item['regVersionUltimaModificacion'],$item['orientacionPagina']);
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
				
				$consulta= $consulta . $comaText . "(" . $item["reporteConfiguracionID"] . "," . $item["templateReporteID"] . ",'" . ($item["nombreReporte"]) . "','" . ($item["queryReporte"]) . "','" . ($item["columnasReporte"]) . "'," . $item["imprimirLogoPdf"] . "," . $item["imprimirEncabezado"] . "," . $item["imprimirFechaHora"] . "," . $item["imprimirNombreUsuario"] . "," . $item["imprimirLogoExcel"] . "," . $item["imprimirPie"] . "," . $item["imprimirEncabezadoExcel"] . "," . $item["imprimirFechaHoraExcel"] . "," . $item["imprimirNombreUsuarioExcel"] . "," . $item["versionRegistro"] . "," . $item["regEstado"] . ",'" . ($item["regFechaUltimaModificacion"]) . "'," . $item["regUsuarioUltimaModificacion"] . "," . $item["regFormularioUltimaModificacion"] . "," . $item["regVersionUltimaModificacion"] . ",'" . $item["orientacionPagina"] . "')";
			}
			 
		   
		 }
	if(!$coma){
		$consulta="select 1";
	}
	
	$this->query=$consulta;
	
    // prepare the query
    $stmt = $this->Conexion->prepare($this->query);
	
    // sanitize
	$reporteConfiguracionID=htmlspecialchars(strip_tags($item['reporteConfiguracionID']));
	$templateReporteID=htmlspecialchars(strip_tags($item['templateReporteID']));
	$nombreReporte=htmlspecialchars(strip_tags($item['nombreReporte']));
	$queryReporte=htmlspecialchars(strip_tags($item['queryReporte']));
	$columnasReporte=htmlspecialchars(strip_tags($item['columnasReporte']));
	$imprimirLogoPdf=htmlspecialchars(strip_tags($item['imprimirLogoPdf']));
	$imprimirEncabezado=htmlspecialchars(strip_tags($item['imprimirEncabezado']));
	$imprimirFechaHora=htmlspecialchars(strip_tags($item['imprimirFechaHora']));
	$imprimirNombreUsuario=htmlspecialchars(strip_tags($item['imprimirNombreUsuario']));
	$imprimirLogoExcel=htmlspecialchars(strip_tags($item['imprimirLogoExcel']));
	$imprimirPie=htmlspecialchars(strip_tags($item['imprimirPie']));
	$imprimirEncabezadoExcel=htmlspecialchars(strip_tags($item['imprimirEncabezadoExcel']));
	$imprimirFechaHoraExcel=htmlspecialchars(strip_tags($item['imprimirFechaHoraExcel']));
	$imprimirNombreUsuarioExcel=htmlspecialchars(strip_tags($item['imprimirNombreUsuarioExcel']));
	$versionRegistro=htmlspecialchars(strip_tags($item['versionRegistro']));
	$regEstado=htmlspecialchars(strip_tags($item['regEstado']));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($item['regFechaUltimaModificacion']));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($item['regUsuarioUltimaModificacion']));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($item['regFormularioUltimaModificacion']));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($item['regVersionUltimaModificacion']));
        $orientacionPagina=htmlspecialchars(strip_tags($item['orientacionPagina']));
   
    // bind the values
	$stmt->bindParam(':reporteConfiguracionID', $reporteConfiguracionID);
	$stmt->bindParam(':templateReporteID', $templateReporteID);
	$stmt->bindParam(':nombreReporte', $nombreReporte);
	$stmt->bindParam(':queryReporte', $queryReporte);
	$stmt->bindParam(':columnasReporte', $columnasReporte);
	$imprimirLogoPdf = (int)$imprimirLogoPdf;
	$stmt->bindValue(':imprimirLogoPdf', $imprimirLogoPdf, PDO::PARAM_INT);
	$imprimirEncabezado = (int)$imprimirEncabezado;
	$stmt->bindValue(':imprimirEncabezado', $imprimirEncabezado, PDO::PARAM_INT);
	$imprimirFechaHora = (int)$imprimirFechaHora;
	$stmt->bindValue(':imprimirFechaHora', $imprimirFechaHora, PDO::PARAM_INT);
	$imprimirNombreUsuario = (int)$imprimirNombreUsuario;
	$stmt->bindValue(':imprimirNombreUsuario', $imprimirNombreUsuario, PDO::PARAM_INT);
	$imprimirLogoExcel = (int)$imprimirLogoExcel;
	$stmt->bindValue(':imprimirLogoExcel', $imprimirLogoExcel, PDO::PARAM_INT);
	$imprimirPie = (int)$imprimirPie;
	$stmt->bindValue(':imprimirPie', $imprimirPie, PDO::PARAM_INT);
	$imprimirEncabezadoExcel = (int)$imprimirEncabezadoExcel;
	$stmt->bindValue(':imprimirEncabezadoExcel', $imprimirEncabezadoExcel, PDO::PARAM_INT);
	$imprimirFechaHoraExcel = (int)$imprimirFechaHoraExcel;
	$stmt->bindValue(':imprimirFechaHoraExcel', $imprimirFechaHoraExcel, PDO::PARAM_INT);
	$imprimirNombreUsuarioExcel = (int)$imprimirNombreUsuarioExcel;
	$stmt->bindValue(':imprimirNombreUsuarioExcel', $imprimirNombreUsuarioExcel, PDO::PARAM_INT);
	$stmt->bindParam(':versionRegistro', $versionRegistro);
	$regEstado = (int)$regEstado;
	$stmt->bindValue(':regEstado', $regEstado, PDO::PARAM_INT);
	$stmt->bindParam(':regFechaUltimaModificacion', $regFechaUltimaModificacion);
	$stmt->bindParam(':regUsuarioUltimaModificacion', $regUsuarioUltimaModificacion);
	$stmt->bindParam(':regFormularioUltimaModificacion', $regFormularioUltimaModificacion);
	$stmt->bindParam(':regVersionUltimaModificacion', $regVersionUltimaModificacion);
        $stmt->bindParam(':orientacionPagina', $orientacionPagina);
   
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



function InsertaRegreso($item){
$Registo= "0";
$consulta="";
try{
    
	
	$consulta = 'INSERT INTO ' . $this->NombreTabla . 
			" (reporteConfiguracionID,templateReporteID,nombreReporte,queryReporte,columnasReporte,imprimirLogoPdf,imprimirEncabezado,imprimirFechaHora,imprimirNombreUsuario,imprimirLogoExcel,imprimirPie,imprimirEncabezadoExcel,imprimirFechaHoraExcel,imprimirNombreUsuarioExcel,versionRegistro,regEstado,regFechaUltimaModificacion,regUsuarioUltimaModificacion,regFormularioUltimaModificacion,regVersionUltimaModificacion,orientacionPagina) VALUES ";
			
		$coma = false;
		$comaText = "";
		$Registo=$item['reporteConfiguracionID'];
		if($this->ObtenerDatos($item['reporteConfiguracionID'],"CONSULTA_LOCAL")){
			//$this->Cambia($item['reporteConfiguracionID'],$item['templateReporteID'],($item['nombreReporte']),(str_replace("'","\'",$item['queryReporte'])),($item['columnasReporte']),$item['imprimirLogoPdf'],$item['imprimirEncabezado'],$item['imprimirFechaHora'],$item['imprimirNombreUsuario'],$item['imprimirLogoExcel'],$item['imprimirPie'],$item['imprimirEncabezadoExcel'],$item['imprimirFechaHoraExcel'],$item['imprimirNombreUsuarioExcel'],$item['versionRegistro'],$item['regEstado'],($item['regFechaUltimaModificacion']),$item['regUsuarioUltimaModificacion'],$item['regFormularioUltimaModificacion'],$item['regVersionUltimaModificacion'],$item['orientacionPagina']);
                       $this->Cambia($item['reporteConfiguracionID'],$item['templateReporteID'],($item['nombreReporte']),($item['queryReporte']),($item['columnasReporte']),$item['imprimirLogoPdf'],$item['imprimirEncabezado'],$item['imprimirFechaHora'],$item['imprimirNombreUsuario'],$item['imprimirLogoExcel'],$item['imprimirPie'],$item['imprimirEncabezadoExcel'],$item['imprimirFechaHoraExcel'],$item['imprimirNombreUsuarioExcel'],$item['versionRegistro'],$item['regEstado'],($item['regFechaUltimaModificacion']),$item['regUsuarioUltimaModificacion'],$item['regFormularioUltimaModificacion'],$item['regVersionUltimaModificacion'],$item['orientacionPagina']);
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
			
			//$consulta= $consulta . $comaText . "(" . $item["reporteConfiguracionID"] . "," . $item["templateReporteID"] . ",'" . ($item["nombreReporte"]) . "','" . (str_replace("'","\'",$item['queryReporte'])) . "','" . ($item["columnasReporte"]) . "'," . $item["imprimirLogoPdf"] . "," . $item["imprimirEncabezado"] . "," . $item["imprimirFechaHora"] . "," . $item["imprimirNombreUsuario"] . "," . $item["imprimirLogoExcel"] . "," . $item["imprimirPie"] . "," . $item["imprimirEncabezadoExcel"] . "," . $item["imprimirFechaHoraExcel"] . "," . $item["imprimirNombreUsuarioExcel"] . "," . $item["versionRegistro"] . "," . $item["regEstado"] . ",'" . ($item["regFechaUltimaModificacion"]) . "'," . $item["regUsuarioUltimaModificacion"] . "," . $item["regFormularioUltimaModificacion"] . "," . $item["regVersionUltimaModificacion"]. ",'" . $item["orientacionPagina"] . "')";
                         $consulta= $consulta . $comaText . "(:reporteConfiguracionID,:templateReporteID,:nombreReporte,:queryReporte,:columnasReporte,:imprimirLogoPdf,:imprimirEncabezado,:imprimirFechaHora,:imprimirNombreUsuario,:imprimirLogoExcel,:imprimirPie,:imprimirEncabezadoExcel,:imprimirFechaHoraExcel,:imprimirNombreUsuarioExcel,:versionRegistro,:regEstado,:regFechaUltimaModificacion,:regUsuarioUltimaModificacion,:regFormularioUltimaModificacion,:regVersionUltimaModificacion,:orientacionPagina)";
		}
	if(!$coma){
		$consulta="select 1";
	}
	
	$this->query=$consulta;
	
    // prepare the query
    $stmt = $this->Conexion->prepare($this->query);
	
    // sanitize
	$reporteConfiguracionID=htmlspecialchars(strip_tags($item['reporteConfiguracionID']));
	$templateReporteID=htmlspecialchars(strip_tags($item['templateReporteID']));
	$nombreReporte=htmlspecialchars(strip_tags($item['nombreReporte']));
	$queryReporte=htmlspecialchars(strip_tags($item['queryReporte']));
	$columnasReporte=htmlspecialchars(strip_tags($item['columnasReporte']));
	$imprimirLogoPdf=htmlspecialchars(strip_tags($item['imprimirLogoPdf']));
	$imprimirEncabezado=htmlspecialchars(strip_tags($item['imprimirEncabezado']));
	$imprimirFechaHora=htmlspecialchars(strip_tags($item['imprimirFechaHora']));
	$imprimirNombreUsuario=htmlspecialchars(strip_tags($item['imprimirNombreUsuario']));
	$imprimirLogoExcel=htmlspecialchars(strip_tags($item['imprimirLogoExcel']));
	$imprimirPie=htmlspecialchars(strip_tags($item['imprimirPie']));
	$imprimirEncabezadoExcel=htmlspecialchars(strip_tags($item['imprimirEncabezadoExcel']));
	$imprimirFechaHoraExcel=htmlspecialchars(strip_tags($item['imprimirFechaHoraExcel']));
	$imprimirNombreUsuarioExcel=htmlspecialchars(strip_tags($item['imprimirNombreUsuarioExcel']));
	$versionRegistro=htmlspecialchars(strip_tags($item['versionRegistro']));
	$regEstado=htmlspecialchars(strip_tags($item['regEstado']));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($item['regFechaUltimaModificacion']));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($item['regUsuarioUltimaModificacion']));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($item['regFormularioUltimaModificacion']));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($item['regVersionUltimaModificacion']));
        $orientacionPagina=htmlspecialchars(strip_tags($item['orientacionPagina']));
   
    // bind the values
	$stmt->bindParam(':reporteConfiguracionID', $reporteConfiguracionID);
	$stmt->bindParam(':templateReporteID', $templateReporteID);
	$stmt->bindParam(':nombreReporte', $nombreReporte);
	$stmt->bindParam(':queryReporte', $queryReporte);
	$stmt->bindParam(':columnasReporte', $columnasReporte);
	$imprimirLogoPdf = (int)$imprimirLogoPdf;
	$stmt->bindValue(':imprimirLogoPdf', $imprimirLogoPdf, PDO::PARAM_INT);
	$imprimirEncabezado = (int)$imprimirEncabezado;
	$stmt->bindValue(':imprimirEncabezado', $imprimirEncabezado, PDO::PARAM_INT);
	$imprimirFechaHora = (int)$imprimirFechaHora;
	$stmt->bindValue(':imprimirFechaHora', $imprimirFechaHora, PDO::PARAM_INT);
	$imprimirNombreUsuario = (int)$imprimirNombreUsuario;
	$stmt->bindValue(':imprimirNombreUsuario', $imprimirNombreUsuario, PDO::PARAM_INT);
	$imprimirLogoExcel = (int)$imprimirLogoExcel;
	$stmt->bindValue(':imprimirLogoExcel', $imprimirLogoExcel, PDO::PARAM_INT);
	$imprimirPie = (int)$imprimirPie;
	$stmt->bindValue(':imprimirPie', $imprimirPie, PDO::PARAM_INT);
	$imprimirEncabezadoExcel = (int)$imprimirEncabezadoExcel;
	$stmt->bindValue(':imprimirEncabezadoExcel', $imprimirEncabezadoExcel, PDO::PARAM_INT);
	$imprimirFechaHoraExcel = (int)$imprimirFechaHoraExcel;
	$stmt->bindValue(':imprimirFechaHoraExcel', $imprimirFechaHoraExcel, PDO::PARAM_INT);
	$imprimirNombreUsuarioExcel = (int)$imprimirNombreUsuarioExcel;
	$stmt->bindValue(':imprimirNombreUsuarioExcel', $imprimirNombreUsuarioExcel, PDO::PARAM_INT);
	$stmt->bindParam(':versionRegistro', $versionRegistro);
	$regEstado = (int)$regEstado;
	$stmt->bindValue(':regEstado', $regEstado, PDO::PARAM_INT);
	$stmt->bindParam(':regFechaUltimaModificacion', $regFechaUltimaModificacion);
	$stmt->bindParam(':regUsuarioUltimaModificacion', $regUsuarioUltimaModificacion);
	$stmt->bindParam(':regFormularioUltimaModificacion', $regFormularioUltimaModificacion);
	$stmt->bindParam(':regVersionUltimaModificacion', $regVersionUltimaModificacion);
        $stmt->bindParam(':orientacionPagina', $orientacionPagina);
   
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
		return $this->Mensaje = $e->getMessage().$consulta;
	}
}
catch (Exception $e){
		$this->LogMigracion("Api".$this->NombreTabla, "INSERTAR Ex2", "Registo ".$Registo.": ".$consulta, $e->getMessage());
	return $this->Mensaje = $e->getMessage().$consulta;
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

function Cambia($reporteConfiguracionID,$templateReporteID,$nombreReporte,$queryReporte,$columnasReporte,$imprimirLogoPdf,$imprimirEncabezado,$imprimirFechaHora,$imprimirNombreUsuario,$imprimirLogoExcel,$imprimirPie,$imprimirEncabezadoExcel,$imprimirFechaHoraExcel,$imprimirNombreUsuarioExcel,$versionRegistro,$regEstado,$regFechaUltimaModificacion,$regUsuarioUltimaModificacion,$regFormularioUltimaModificacion,$regVersionUltimaModificacion,$orientacionPagina){
    $Registo=$reporteConfiguracionID;
    $query="";
    try
    {
        //$query = "UPDATE " . $this->NombreTabla . " SET templateReporteID=:templateReporteID,nombreReporte=:nombreReporte,queryReporte='".(str_replace("'","\'",$queryReporte))."',columnasReporte=:columnasReporte,imprimirLogoPdf=:imprimirLogoPdf,imprimirEncabezado=:imprimirEncabezado,imprimirFechaHora=:imprimirFechaHora,imprimirNombreUsuario=:imprimirNombreUsuario,imprimirLogoExcel=:imprimirLogoExcel,imprimirPie=:imprimirPie,imprimirEncabezadoExcel=:imprimirEncabezadoExcel,imprimirFechaHoraExcel=:imprimirFechaHoraExcel,imprimirNombreUsuarioExcel=:imprimirNombreUsuarioExcel,versionRegistro=:versionRegistro,regEstado=:regEstado,regFechaUltimaModificacion=:regFechaUltimaModificacion,regUsuarioUltimaModificacion=:regUsuarioUltimaModificacion,regFormularioUltimaModificacion=:regFormularioUltimaModificacion,regVersionUltimaModificacion=:regVersionUltimaModificacion,orientacionPagina=:orientacionPagina
	//	WHERE reporteConfiguracionID=:reporteConfiguracionID ";

    $query = "UPDATE " . $this->NombreTabla . " SET templateReporteID=:templateReporteID,nombreReporte=:nombreReporte,queryReporte=:queryReporte,columnasReporte=:columnasReporte,imprimirLogoPdf=:imprimirLogoPdf,imprimirEncabezado=:imprimirEncabezado,imprimirFechaHora=:imprimirFechaHora,imprimirNombreUsuario=:imprimirNombreUsuario,imprimirLogoExcel=:imprimirLogoExcel,imprimirPie=:imprimirPie,imprimirEncabezadoExcel=:imprimirEncabezadoExcel,imprimirFechaHoraExcel=:imprimirFechaHoraExcel,imprimirNombreUsuarioExcel=:imprimirNombreUsuarioExcel,versionRegistro=:versionRegistro,regEstado=:regEstado,regFechaUltimaModificacion=:regFechaUltimaModificacion,regUsuarioUltimaModificacion=:regUsuarioUltimaModificacion,regFormularioUltimaModificacion=:regFormularioUltimaModificacion,regVersionUltimaModificacion=:regVersionUltimaModificacion,orientacionPagina=:orientacionPagina
		WHERE reporteConfiguracionID=:reporteConfiguracionID ";

    // prepare the query
    $stmt = $this->Conexion->prepare($query);
 
     // sanitize
	$reporteConfiguracionID=htmlspecialchars(strip_tags($reporteConfiguracionID));
	$templateReporteID=htmlspecialchars(strip_tags($templateReporteID));
	$nombreReporte=htmlspecialchars(strip_tags($nombreReporte));
	$queryReporte=htmlspecialchars(strip_tags($queryReporte));
	$columnasReporte=htmlspecialchars(strip_tags($columnasReporte));
	$imprimirLogoPdf=htmlspecialchars(strip_tags($imprimirLogoPdf));
	$imprimirEncabezado=htmlspecialchars(strip_tags($imprimirEncabezado));
	$imprimirFechaHora=htmlspecialchars(strip_tags($imprimirFechaHora));
	$imprimirNombreUsuario=htmlspecialchars(strip_tags($imprimirNombreUsuario));
	$imprimirLogoExcel=htmlspecialchars(strip_tags($imprimirLogoExcel));
	$imprimirPie=htmlspecialchars(strip_tags($imprimirPie));
	$imprimirEncabezadoExcel=htmlspecialchars(strip_tags($imprimirEncabezadoExcel));
	$imprimirFechaHoraExcel=htmlspecialchars(strip_tags($imprimirFechaHoraExcel));
	$imprimirNombreUsuarioExcel=htmlspecialchars(strip_tags($imprimirNombreUsuarioExcel));
	$versionRegistro=htmlspecialchars(strip_tags($versionRegistro));
	$regEstado=htmlspecialchars(strip_tags($regEstado));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($regFechaUltimaModificacion));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($regUsuarioUltimaModificacion));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($regFormularioUltimaModificacion));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($regVersionUltimaModificacion));
        $orientacionPagina=htmlspecialchars(strip_tags($orientacionPagina));
   
    // bind the values
	$stmt->bindParam(':reporteConfiguracionID', $reporteConfiguracionID);
	$stmt->bindParam(':templateReporteID', $templateReporteID);
	$stmt->bindParam(':nombreReporte', $nombreReporte);
	$stmt->bindParam(':queryReporte', $queryReporte);
	$stmt->bindParam(':columnasReporte', $columnasReporte);
	$imprimirLogoPdf = (int)$imprimirLogoPdf;
	$stmt->bindValue(':imprimirLogoPdf', $imprimirLogoPdf, PDO::PARAM_INT);
	$imprimirEncabezado = (int)$imprimirEncabezado;
	$stmt->bindValue(':imprimirEncabezado', $imprimirEncabezado, PDO::PARAM_INT);
	$imprimirFechaHora = (int)$imprimirFechaHora;
	$stmt->bindValue(':imprimirFechaHora', $imprimirFechaHora, PDO::PARAM_INT);
	$imprimirNombreUsuario = (int)$imprimirNombreUsuario;
	$stmt->bindValue(':imprimirNombreUsuario', $imprimirNombreUsuario, PDO::PARAM_INT);
	$imprimirLogoExcel = (int)$imprimirLogoExcel;
	$stmt->bindValue(':imprimirLogoExcel', $imprimirLogoExcel, PDO::PARAM_INT);
	$imprimirPie = (int)$imprimirPie;
	$stmt->bindValue(':imprimirPie', $imprimirPie, PDO::PARAM_INT);
	$imprimirEncabezadoExcel = (int)$imprimirEncabezadoExcel;
	$stmt->bindValue(':imprimirEncabezadoExcel', $imprimirEncabezadoExcel, PDO::PARAM_INT);
	$imprimirFechaHoraExcel = (int)$imprimirFechaHoraExcel;
	$stmt->bindValue(':imprimirFechaHoraExcel', $imprimirFechaHoraExcel, PDO::PARAM_INT);
	$imprimirNombreUsuarioExcel = (int)$imprimirNombreUsuarioExcel;
	$stmt->bindValue(':imprimirNombreUsuarioExcel', $imprimirNombreUsuarioExcel, PDO::PARAM_INT);
	$stmt->bindParam(':versionRegistro', $versionRegistro);
	$regEstado = (int)$regEstado;
	$stmt->bindValue(':regEstado', $regEstado, PDO::PARAM_INT);
	$stmt->bindParam(':regFechaUltimaModificacion', $regFechaUltimaModificacion);
	$stmt->bindParam(':regUsuarioUltimaModificacion', $regUsuarioUltimaModificacion);
	$stmt->bindParam(':regFormularioUltimaModificacion', $regFormularioUltimaModificacion);
	$stmt->bindParam(':regVersionUltimaModificacion', $regVersionUltimaModificacion);
        $stmt->bindParam(':orientacionPagina', $orientacionPagina);

   try{
			// execute the query, also check if query was successful
			if($stmt->execute()){
				//$this->LogMigracion("Api".$this->NombreTabla, "UPDATE", "Registo ".$Registo.": ".$query, "ACTUALIZACION EXITOSA");
				return true;

			}
		}   
		catch (Exception $e){
			//.'<br /> <br />Consulta: <br />'.$consulta;
			$this->LogMigracion("Api".$this->NombreTabla, "UPDATE Ex1", "Registo ".$Registo.": ".$e->getMessage().$query, $e->getMessage());
			//$this->Mensaje .= $e->getMessage().$consulta;
                        $this->Mensaje .= $e->getMessage();
			return false;
		}
	 
	}
	catch (Exception $e){
		$this->LogMigracion("Api".$this->NombreTabla, "UPDATE Ex2", "Registo ".$Registo.": ".$e->getMessage().$query, $e->getMessage());
		//$this->Mensaje .= $e->getMessage().$consulta;
                $this->Mensaje .= $e->getMessage();
		return false;
	}
 
    return false;
}

	
		
}
	?>