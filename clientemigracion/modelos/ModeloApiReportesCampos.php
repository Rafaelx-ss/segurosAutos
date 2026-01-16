<?php 
class ReportesCampos{ 
 
private $Conexion; 
private $Database; 
private $NombreTabla = 'ReportesCampos'; 
 
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
		
			$query = "select * from " . $this->NombreTabla .  " where 1 " . ($id > 0 ? " and reporteCampoID = ?" : "");
		
 
    	// prepare the query
    	$stmt = $this->Conexion->prepare( $query );
 
    	
		$id=htmlspecialchars(strip_tags($id));
 
    	// bind given id value

    
    	//$this->LogMigracion("Api".$this->NombreTabla, "consulta", "Registo ".$query,"consulta");
        
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
			" (reporteCampoID,reporteConfiguracionID,nombreCampo,aliasTabla,visible,searchVisible,orden,textoID,tipoControl,controlQuery,queryValor,versionRegistro,regEstado,regFechaUltimaModificacion,regUsuarioUltimaModificacion,regFormularioUltimaModificacion,regVersionUltimaModificacion,sumarCampo) VALUES ";
			
		$coma = false;
		$comaText = "";
		foreach($registros as $item)
		 {
			//echo $item['textoID']."lolol";
			if($this->ObtenerDatos($item['reporteCampoID'],"CONSULTA_LOCAL")){
				$this->Cambia($item['reporteCampoID'],$item['reporteConfiguracionID'],(str_replace("'","\'",$item['nombreCampo'])),str_replace("'","\'",$item['aliasTabla']),$item['visible'],$item['searchVisible'],$item['orden'],$item['textoID'],($item['tipoControl']),(str_replace("'","\'",$item['controlQuery'])),(str_replace("'","\'",$item['queryValor'])),$item['versionRegistro'],$item['regEstado'],($item['regFechaUltimaModificacion']),$item['regUsuarioUltimaModificacion'],$item['regFormularioUltimaModificacion'],$item['regVersionUltimaModificacion'],$item['sumarCampo']);
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
				
				$campoNULL="'".$item["aliasTabla"]."'";
				if($item["aliasTabla"]==" " or trim($item["aliasTabla"]) == ""){
					$campoNULL='NULL';
				}
				$consulta= $consulta . $comaText . "(" . $item["reporteCampoID"] . "," . $item["reporteConfiguracionID"] . ",'" . (str_replace("'","\'",$item['nombreCampo'])) . "',
				".$campoNULL."," . $item["visible"] . "," . $item["searchVisible"] . "," . $item["orden"] . "," . $item["textoID"] . ",'" . (str_replace("'","\'",$item['tipoControl'])) . "',
				'" . (str_replace("'","\'",$item['controlQuery'])) . "','" . (str_replace("'","\'",$item['queryValor'])) . "'," . $item["versionRegistro"] . "," . $item["regEstado"] . ",'" . ($item["regFechaUltimaModificacion"]) . "'," . $item["regUsuarioUltimaModificacion"] . "," . $item["regFormularioUltimaModificacion"] . "," . $item["regVersionUltimaModificacion"]. "," . $item["sumarCampo"] . ")";
			}
			 
		   
		 }
	if(!$coma){
		$consulta="select 1";
	}
	
	$this->query=$consulta;
	
    // prepare the query
    $stmt = $this->Conexion->prepare($this->query);
	
    // sanitize
	$reporteCampoID=htmlspecialchars(strip_tags($item['reporteCampoID']));
	$reporteConfiguracionID=htmlspecialchars(strip_tags($item['reporteConfiguracionID']));
	$nombreCampo=htmlspecialchars(strip_tags($item['nombreCampo']));
	$visible=htmlspecialchars(strip_tags($item['visible']));
	$searchVisible=htmlspecialchars(strip_tags($item['searchVisible']));
	$orden=htmlspecialchars(strip_tags($item['orden']));
	$textoID=htmlspecialchars(strip_tags($item['textoID']));
	$tipoControl=htmlspecialchars(strip_tags($item['tipoControl']));
	$controlQuery=htmlspecialchars(strip_tags($item['controlQuery']));
	$queryValor=htmlspecialchars(strip_tags($item['queryValor']));
	$versionRegistro=htmlspecialchars(strip_tags($item['versionRegistro']));
	$regEstado=htmlspecialchars(strip_tags($item['regEstado']));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($item['regFechaUltimaModificacion']));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($item['regUsuarioUltimaModificacion']));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($item['regFormularioUltimaModificacion']));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($item['regVersionUltimaModificacion']));
        $sumarCampo=htmlspecialchars(strip_tags($item['sumarCampo']));
   
    // bind the values
	$stmt->bindParam(':reporteCampoID', $reporteCampoID);
	$stmt->bindParam(':reporteConfiguracionID', $reporteConfiguracionID);
	$stmt->bindParam(':nombreCampo', $nombreCampo);
	$visible = (int)$visible;
	$stmt->bindValue(':visible', $visible, PDO::PARAM_INT);
	$searchVisible = (int)$searchVisible;
	$stmt->bindValue(':searchVisible', $searchVisible, PDO::PARAM_INT);
	$stmt->bindParam(':orden', $orden);
	$stmt->bindParam(':textoID', $textoID);
	$stmt->bindParam(':tipoControl', $tipoControl);
	$stmt->bindParam(':controlQuery', $controlQuery);
	$stmt->bindParam(':queryValor', $queryValor);
	$stmt->bindParam(':versionRegistro', $versionRegistro);
	$regEstado = (int)$regEstado;
	$stmt->bindValue(':regEstado', $regEstado, PDO::PARAM_INT);
	$stmt->bindParam(':regFechaUltimaModificacion', $regFechaUltimaModificacion);
	$stmt->bindParam(':regUsuarioUltimaModificacion', $regUsuarioUltimaModificacion);
	$stmt->bindParam(':regFormularioUltimaModificacion', $regFormularioUltimaModificacion);
	$stmt->bindParam(':regVersionUltimaModificacion', $regVersionUltimaModificacion);
        $sumarCampo = (int)$sumarCampo;
	$stmt->bindValue(':sumarCampo', $sumarCampo, PDO::PARAM_INT);
        
   
   try{
        // execute the query, also check if query was successful
        if($stmt->execute()){
            return true;

        }
    }   
    catch (Exception $e){
        return $this->Mensaje .= $e->getMessage().$consulta.'<br /> <br />Consulta: <br />'.$consulta;
    }

 
    return false;
}


function InsertaRegreso($item){
$Registo= "0";
$consulta="";
try{
    
	
	$consulta = 'INSERT INTO ' . $this->NombreTabla . 
			" (reporteCampoID,reporteConfiguracionID,nombreCampo,aliasTabla,visible,searchVisible,orden,textoID,tipoControl,controlQuery,queryValor,versionRegistro,regEstado,regFechaUltimaModificacion,regUsuarioUltimaModificacion,regFormularioUltimaModificacion,regVersionUltimaModificacion,sumarCampo) VALUES ";
			
		$coma = false;
		$comaText = "";
		$Registo=$item['reporteCampoID'];
		if($this->ObtenerDatos($item['reporteCampoID'],"CONSULTA_LOCAL")){
			$this->Cambia($item['reporteCampoID'],$item['reporteConfiguracionID'],(str_replace("'","\'",$item['nombreCampo'])),str_replace("'","\'",$item['aliasTabla']),$item['visible'],$item['searchVisible'],$item['orden'],$item['textoID'],($item['tipoControl']),(str_replace("'","\'",$item['controlQuery'])),(str_replace("'","\'",$item['queryValor'])),$item['versionRegistro'],$item['regEstado'],($item['regFechaUltimaModificacion']),$item['regUsuarioUltimaModificacion'],$item['regFormularioUltimaModificacion'],$item['regVersionUltimaModificacion'],$item['sumarCampo']);
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
			
			$campoNULL="'".$item["aliasTabla"]."'";
			if($item["aliasTabla"]==" " or trim($item["aliasTabla"]) == ""){
				$campoNULL='NULL';
			}
			
			$consulta= $consulta . $comaText . "(" . $item["reporteCampoID"] . "," . $item["reporteConfiguracionID"] . ",'" . (str_replace("'","\'",$item['nombreCampo'])) . "',
			".$campoNULL."," . $item["visible"] . "," . $item["searchVisible"] . "," . $item["orden"] . "," . $item["textoID"] . ",'" . (str_replace("'","\'",$item['tipoControl'])) . "',
			'" . (str_replace("'","\'",$item['controlQuery'])) . "','" . (str_replace("'","\'",$item['queryValor'])) . "'," . $item["versionRegistro"] . "," . $item["regEstado"] . ",'" . ($item["regFechaUltimaModificacion"]) . "'," . $item["regUsuarioUltimaModificacion"] . "," . $item["regFormularioUltimaModificacion"] . "," . $item["regVersionUltimaModificacion"]. "," . $item["sumarCampo"] . ")";
		}
	if(!$coma){
		$consulta="select 1";
	}
	
	$this->query=$consulta;
	
    // prepare the query
    $stmt = $this->Conexion->prepare($this->query);
	
    // sanitize
	$reporteCampoID=htmlspecialchars(strip_tags($item['reporteCampoID']));
	$reporteConfiguracionID=htmlspecialchars(strip_tags($item['reporteConfiguracionID']));
	$nombreCampo=htmlspecialchars(strip_tags($item['nombreCampo']));
	$visible=htmlspecialchars(strip_tags($item['visible']));
	$searchVisible=htmlspecialchars(strip_tags($item['searchVisible']));
	$orden=htmlspecialchars(strip_tags($item['orden']));
	$textoID=htmlspecialchars(strip_tags($item['textoID']));
	$tipoControl=htmlspecialchars(strip_tags($item['tipoControl']));
	$controlQuery=htmlspecialchars(strip_tags($item['controlQuery']));
	$queryValor=htmlspecialchars(strip_tags($item['queryValor']));
	$versionRegistro=htmlspecialchars(strip_tags($item['versionRegistro']));
	$regEstado=htmlspecialchars(strip_tags($item['regEstado']));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($item['regFechaUltimaModificacion']));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($item['regUsuarioUltimaModificacion']));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($item['regFormularioUltimaModificacion']));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($item['regVersionUltimaModificacion']));
        $sumarCampo=htmlspecialchars(strip_tags($item['sumarCampo']));
   
    // bind the values
	$stmt->bindParam(':reporteCampoID', $reporteCampoID);
	$stmt->bindParam(':reporteConfiguracionID', $reporteConfiguracionID);
	$stmt->bindParam(':nombreCampo', $nombreCampo);
	$visible = (int)$visible;
	$stmt->bindValue(':visible', $visible, PDO::PARAM_INT);
	$searchVisible = (int)$searchVisible;
	$stmt->bindValue(':searchVisible', $searchVisible, PDO::PARAM_INT);
	$stmt->bindParam(':orden', $orden);
	$stmt->bindParam(':textoID', $textoID);
	$stmt->bindParam(':tipoControl', $tipoControl);
	$stmt->bindParam(':controlQuery', $controlQuery);
	$stmt->bindParam(':queryValor', $queryValor);
	$stmt->bindParam(':versionRegistro', $versionRegistro);
	$regEstado = (int)$regEstado;
	$stmt->bindValue(':regEstado', $regEstado, PDO::PARAM_INT);
	$stmt->bindParam(':regFechaUltimaModificacion', $regFechaUltimaModificacion);
	$stmt->bindParam(':regUsuarioUltimaModificacion', $regUsuarioUltimaModificacion);
	$stmt->bindParam(':regFormularioUltimaModificacion', $regFormularioUltimaModificacion);
	$stmt->bindParam(':regVersionUltimaModificacion', $regVersionUltimaModificacion);
        $sumarCampo = (int)$sumarCampo;
	$stmt->bindValue(':sumarCampo', $sumarCampo, PDO::PARAM_INT);
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
		echo $this->Mensaje .= $e->getMessage().$consulta;
	}
}
catch (Exception $e){
		$this->LogMigracion("Api".$this->NombreTabla, "INSERTAR Ex2", "Registo ".$Registo.": ".$consulta, $e->getMessage());
	echo $this->Mensaje .= $e->getMessage().$consulta;
}
    return false;
}

function Cambia($reporteCampoID,$reporteConfiguracionID,$nombreCampo,$aliasTabla,$visible,$searchVisible,$orden,$textoID,$tipoControl,$controlQuery,$queryValor,$versionRegistro,$regEstado,$regFechaUltimaModificacion,$regUsuarioUltimaModificacion,$regFormularioUltimaModificacion,$regVersionUltimaModificacion,$sumarCampo){
    $Registo= $reporteCampoID;
    try
    {
        
    if($aliasTabla != " " and trim($aliasTabla) != ""){
		$CampoNull=",aliasTabla='".$aliasTabla."'";
	}
	else {
		$CampoNull=",aliasTabla=NULL";
		//$CampoNull=",aliasTabla='".$aliasTabla."'";
	}
	$query = "UPDATE " . $this->NombreTabla . " SET reporteConfiguracionID=:reporteConfiguracionID,nombreCampo='".$nombreCampo."' ".$CampoNull.",visible=:visible,searchVisible=:searchVisible,
	orden=:orden,textoID=:textoID,tipoControl=:tipoControl,
	controlQuery='".$controlQuery."',queryValor='".$queryValor."',versionRegistro=:versionRegistro,regEstado=:regEstado,regFechaUltimaModificacion=:regFechaUltimaModificacion,regUsuarioUltimaModificacion=:regUsuarioUltimaModificacion,regFormularioUltimaModificacion=:regFormularioUltimaModificacion,regVersionUltimaModificacion=:regVersionUltimaModificacion,sumarCampo=:sumarCampo 
		WHERE reporteCampoID=:reporteCampoID ";

    // prepare the query
    $stmt = $this->Conexion->prepare($query);
 
     // sanitize
	$reporteCampoID=htmlspecialchars(strip_tags($reporteCampoID));
	$reporteConfiguracionID=htmlspecialchars(strip_tags($reporteConfiguracionID));
	//$nombreCampo=htmlspecialchars(strip_tags($nombreCampo));
	$visible=htmlspecialchars(strip_tags($visible));
	$searchVisible=htmlspecialchars(strip_tags($searchVisible));
	$orden=htmlspecialchars(strip_tags($orden));
	$textoID=htmlspecialchars(strip_tags($textoID));
	$tipoControl=htmlspecialchars(strip_tags($tipoControl));
	//$controlQuery=htmlspecialchars(strip_tags($controlQuery));
	//$queryValor=htmlspecialchars(strip_tags($queryValor));
	$versionRegistro=htmlspecialchars(strip_tags($versionRegistro));
	$regEstado=htmlspecialchars(strip_tags($regEstado));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($regFechaUltimaModificacion));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($regUsuarioUltimaModificacion));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($regFormularioUltimaModificacion));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($regVersionUltimaModificacion));
        $sumarCampo=htmlspecialchars(strip_tags($sumarCampo));
   
    // bind the values
	$stmt->bindParam(':reporteCampoID', $reporteCampoID);
	$stmt->bindParam(':reporteConfiguracionID', $reporteConfiguracionID);
	//$stmt->bindParam(':nombreCampo', $nombreCampo);
	$visible = (int)$visible;
	$stmt->bindValue(':visible', $visible, PDO::PARAM_INT);
	$searchVisible = (int)$searchVisible;
	$stmt->bindValue(':searchVisible', $searchVisible, PDO::PARAM_INT);
	$stmt->bindParam(':orden', $orden);
	$stmt->bindParam(':textoID', $textoID);
	$stmt->bindParam(':tipoControl', $tipoControl);
	//$stmt->bindParam(':controlQuery', $controlQuery);
	//$stmt->bindParam(':queryValor', $queryValor);
	$stmt->bindParam(':versionRegistro', $versionRegistro);
	$regEstado = (int)$regEstado;
	$stmt->bindValue(':regEstado', $regEstado, PDO::PARAM_INT);
	$stmt->bindParam(':regFechaUltimaModificacion', $regFechaUltimaModificacion);
	$stmt->bindParam(':regUsuarioUltimaModificacion', $regUsuarioUltimaModificacion);
	$stmt->bindParam(':regFormularioUltimaModificacion', $regFormularioUltimaModificacion);
	$stmt->bindParam(':regVersionUltimaModificacion', $regVersionUltimaModificacion);
        $sumarCampo = (int)$sumarCampo;
	$stmt->bindValue(':sumarCampo', $sumarCampo, PDO::PARAM_INT);

         try{
			// execute the query, also check if query was successful
	    if($stmt->execute()){
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