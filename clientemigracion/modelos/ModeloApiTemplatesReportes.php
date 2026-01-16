<?php 
class TemplatesReportes{ 
 
private $Conexion; 
private $Database; 
private $NombreTabla = 'TemplatesReportes'; 
 
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
		$query = "select * from " . $this->NombreTabla .  " where 1 " . ($id > 0 ? " and templateReporteID = ?" : "");
    	//echo $query."<br />";
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
			" (templateReporteID,nombreTemplateReporte,logoTemplateReporte,encabezadoTemplateReporte,pieTemplateReporteL1,pieTemplateReporteL2,pieTemplateReporteL3,colorLinea,colorTituloTabla,colorTituloTexto,colorTextoFooter,versionRegistro,regEstado,regFechaUltimaModificacion,regUsuarioUltimaModificacion,regFormularioUltimaModificacion,regVersionUltimaModificacion) VALUES ";
			
		$coma = false;
		$comaText = "";
		foreach($registros as $item)
		 {
			//echo $item['textoID']."lolol";
			 if($this->ObtenerDatos($item['templateReporteID'],"CONSULTA_LOCAL")){
				$this->Cambia($item['templateReporteID'],($item['nombreTemplateReporte']),($item['logoTemplateReporte']),($item['encabezadoTemplateReporte']),($item['pieTemplateReporteL1']),($item['pieTemplateReporteL2']),($item['pieTemplateReporteL3']),($item['colorLinea']),($item['colorTituloTabla']),($item['colorTituloTexto']),($item['colorTextoFooter']),$item['versionRegistro'],$item['regEstado'],($item['regFechaUltimaModificacion']),$item['regUsuarioUltimaModificacion'],$item['regFormularioUltimaModificacion'],$item['regVersionUltimaModificacion']);
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
				
				$consulta= $consulta . $comaText . "(" . $item["templateReporteID"] . ",'" . ($item["nombreTemplateReporte"]) . "','" . ($item["logoTemplateReporte"]) . "','" . ($item["encabezadoTemplateReporte"]) . "','" . ($item["pieTemplateReporteL1"]) . "','" . ($item["pieTemplateReporteL2"]) . "','" . ($item["pieTemplateReporteL3"]) . "','" . ($item["colorLinea"]) . "','" . ($item["colorTituloTabla"]) . "','" . ($item["colorTituloTexto"]) . "','" . ($item["colorTextoFooter"]) . "'," . $item["versionRegistro"] . "," . $item["regEstado"] . ",'" . ($item["regFechaUltimaModificacion"]) . "'," . $item["regUsuarioUltimaModificacion"] . "," . $item["regFormularioUltimaModificacion"] . "," . $item["regVersionUltimaModificacion"] . ")";
			}
			 
		   
		 }
	if(!$coma){
		$consulta="select 1";
	}
	
	$this->query=$consulta;
	
    // prepare the query
    $stmt = $this->Conexion->prepare($this->query);
	
    // sanitize
	$templateReporteID=htmlspecialchars(strip_tags($item['templateReporteID']));
	$nombreTemplateReporte=htmlspecialchars(strip_tags($item['nombreTemplateReporte']));
	$logoTemplateReporte=htmlspecialchars(strip_tags($item['logoTemplateReporte']));
	$encabezadoTemplateReporte=htmlspecialchars(strip_tags($item['encabezadoTemplateReporte']));
	$pieTemplateReporteL1=htmlspecialchars(strip_tags($item['pieTemplateReporteL1']));
	$pieTemplateReporteL2=htmlspecialchars(strip_tags($item['pieTemplateReporteL2']));
	$pieTemplateReporteL3=htmlspecialchars(strip_tags($item['pieTemplateReporteL3']));
	$colorLinea=htmlspecialchars(strip_tags($item['colorLinea']));
	$colorTituloTabla=htmlspecialchars(strip_tags($item['colorTituloTabla']));
	$colorTituloTexto=htmlspecialchars(strip_tags($item['colorTituloTexto']));
	$colorTextoFooter=htmlspecialchars(strip_tags($item['colorTextoFooter']));
	$versionRegistro=htmlspecialchars(strip_tags($item['versionRegistro']));
	$regEstado=htmlspecialchars(strip_tags($item['regEstado']));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($item['regFechaUltimaModificacion']));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($item['regUsuarioUltimaModificacion']));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($item['regFormularioUltimaModificacion']));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($item['regVersionUltimaModificacion']));
   
    // bind the values
	$stmt->bindParam(':templateReporteID', $templateReporteID);
	$stmt->bindParam(':nombreTemplateReporte', $nombreTemplateReporte);
	$stmt->bindParam(':logoTemplateReporte', $logoTemplateReporte);
	$stmt->bindParam(':encabezadoTemplateReporte', $encabezadoTemplateReporte);
	$stmt->bindParam(':pieTemplateReporteL1', $pieTemplateReporteL1);
	$stmt->bindParam(':pieTemplateReporteL2', $pieTemplateReporteL2);
	$stmt->bindParam(':pieTemplateReporteL3', $pieTemplateReporteL3);
	$stmt->bindParam(':colorLinea', $colorLinea);
	$stmt->bindParam(':colorTituloTabla', $colorTituloTabla);
	$stmt->bindParam(':colorTituloTexto', $colorTituloTexto);
	$stmt->bindParam(':colorTextoFooter', $colorTextoFooter);
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


function InsertaRegreso($item){
$Registo= "0";
try{
    $consulta="";
	
	$consulta = 'INSERT INTO ' . $this->NombreTabla . 
			" (templateReporteID,nombreTemplateReporte,logoTemplateReporte,encabezadoTemplateReporte,pieTemplateReporteL1,pieTemplateReporteL2,pieTemplateReporteL3,colorLinea,colorTituloTabla,colorTituloTexto,colorTextoFooter,versionRegistro,regEstado,regFechaUltimaModificacion,regUsuarioUltimaModificacion,regFormularioUltimaModificacion,regVersionUltimaModificacion) VALUES ";
			
		$coma = false;
		$comaText = "";
		$Registo=$item['templateReporteID'];
		if($this->ObtenerDatos($item['templateReporteID'],"CONSULTA_LOCAL")){
			$this->Cambia($item['templateReporteID'],($item['nombreTemplateReporte']),($item['logoTemplateReporte']),($item['encabezadoTemplateReporte']),($item['pieTemplateReporteL1']),($item['pieTemplateReporteL2']),($item['pieTemplateReporteL3']),($item['colorLinea']),($item['colorTituloTabla']),($item['colorTituloTexto']),($item['colorTextoFooter']),$item['versionRegistro'],$item['regEstado'],($item['regFechaUltimaModificacion']),$item['regUsuarioUltimaModificacion'],$item['regFormularioUltimaModificacion'],$item['regVersionUltimaModificacion']);
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
			
			$consulta= $consulta . $comaText . "(" . $item["templateReporteID"] . ",'" . ($item["nombreTemplateReporte"]) . "','" . ($item["logoTemplateReporte"]) . "','" . ($item["encabezadoTemplateReporte"]) . "','" . ($item["pieTemplateReporteL1"]) . "','" . ($item["pieTemplateReporteL2"]) . "','" . ($item["pieTemplateReporteL3"]) . "','" . ($item["colorLinea"]) . "','" . ($item["colorTituloTabla"]) . "','" . ($item["colorTituloTexto"]) . "','" . ($item["colorTextoFooter"]) . "'," . $item["versionRegistro"] . "," . $item["regEstado"] . ",'" . ($item["regFechaUltimaModificacion"]) . "'," . $item["regUsuarioUltimaModificacion"] . "," . $item["regFormularioUltimaModificacion"] . "," . $item["regVersionUltimaModificacion"] . ")";
		}
	if(!$coma){
		$consulta="select 1";
	}
	
	$this->query=$consulta;
	
    // prepare the query
    $stmt = $this->Conexion->prepare($this->query);
	
    // sanitize
	$templateReporteID=htmlspecialchars(strip_tags($item['templateReporteID']));
	$nombreTemplateReporte=htmlspecialchars(strip_tags($item['nombreTemplateReporte']));
	$logoTemplateReporte=htmlspecialchars(strip_tags($item['logoTemplateReporte']));
	$encabezadoTemplateReporte=htmlspecialchars(strip_tags($item['encabezadoTemplateReporte']));
	$pieTemplateReporteL1=htmlspecialchars(strip_tags($item['pieTemplateReporteL1']));
	$pieTemplateReporteL2=htmlspecialchars(strip_tags($item['pieTemplateReporteL2']));
	$pieTemplateReporteL3=htmlspecialchars(strip_tags($item['pieTemplateReporteL3']));
	$colorLinea=htmlspecialchars(strip_tags($item['colorLinea']));
	$colorTituloTabla=htmlspecialchars(strip_tags($item['colorTituloTabla']));
	$colorTituloTexto=htmlspecialchars(strip_tags($item['colorTituloTexto']));
	$colorTextoFooter=htmlspecialchars(strip_tags($item['colorTextoFooter']));
	$versionRegistro=htmlspecialchars(strip_tags($item['versionRegistro']));
	$regEstado=htmlspecialchars(strip_tags($item['regEstado']));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($item['regFechaUltimaModificacion']));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($item['regUsuarioUltimaModificacion']));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($item['regFormularioUltimaModificacion']));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($item['regVersionUltimaModificacion']));
   
    // bind the values
	$stmt->bindParam(':templateReporteID', $templateReporteID);
	$stmt->bindParam(':nombreTemplateReporte', $nombreTemplateReporte);
	$stmt->bindParam(':logoTemplateReporte', $logoTemplateReporte);
	$stmt->bindParam(':encabezadoTemplateReporte', $encabezadoTemplateReporte);
	$stmt->bindParam(':pieTemplateReporteL1', $pieTemplateReporteL1);
	$stmt->bindParam(':pieTemplateReporteL2', $pieTemplateReporteL2);
	$stmt->bindParam(':pieTemplateReporteL3', $pieTemplateReporteL3);
	$stmt->bindParam(':colorLinea', $colorLinea);
	$stmt->bindParam(':colorTituloTabla', $colorTituloTabla);
	$stmt->bindParam(':colorTituloTexto', $colorTituloTexto);
	$stmt->bindParam(':colorTextoFooter', $colorTextoFooter);
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

function Cambia($templateReporteID,$nombreTemplateReporte,$logoTemplateReporte,$encabezadoTemplateReporte,$pieTemplateReporteL1,$pieTemplateReporteL2,$pieTemplateReporteL3,$colorLinea,$colorTituloTabla,$colorTituloTexto,$colorTextoFooter,$versionRegistro,$regEstado,$regFechaUltimaModificacion,$regUsuarioUltimaModificacion,$regFormularioUltimaModificacion,$regVersionUltimaModificacion){
    $query = "UPDATE " . $this->NombreTabla . " SET nombreTemplateReporte=:nombreTemplateReporte,logoTemplateReporte=:logoTemplateReporte,encabezadoTemplateReporte=:encabezadoTemplateReporte,pieTemplateReporteL1=:pieTemplateReporteL1,pieTemplateReporteL2=:pieTemplateReporteL2,pieTemplateReporteL3=:pieTemplateReporteL3,colorLinea=:colorLinea,colorTituloTabla=:colorTituloTabla,colorTituloTexto=:colorTituloTexto,colorTextoFooter=:colorTextoFooter,versionRegistro=:versionRegistro,regEstado=:regEstado,regFechaUltimaModificacion=:regFechaUltimaModificacion,regUsuarioUltimaModificacion=:regUsuarioUltimaModificacion,regFormularioUltimaModificacion=:regFormularioUltimaModificacion,regVersionUltimaModificacion=:regVersionUltimaModificacion 
		WHERE templateReporteID=:templateReporteID ";

    // prepare the query
    $stmt = $this->Conexion->prepare($query);
 
     // sanitize
	$templateReporteID=htmlspecialchars(strip_tags($templateReporteID));
	$nombreTemplateReporte=htmlspecialchars(strip_tags($nombreTemplateReporte));
	$logoTemplateReporte=htmlspecialchars(strip_tags($logoTemplateReporte));
	$encabezadoTemplateReporte=htmlspecialchars(strip_tags($encabezadoTemplateReporte));
	$pieTemplateReporteL1=htmlspecialchars(strip_tags($pieTemplateReporteL1));
	$pieTemplateReporteL2=htmlspecialchars(strip_tags($pieTemplateReporteL2));
	$pieTemplateReporteL3=htmlspecialchars(strip_tags($pieTemplateReporteL3));
	$colorLinea=htmlspecialchars(strip_tags($colorLinea));
	$colorTituloTabla=htmlspecialchars(strip_tags($colorTituloTabla));
	$colorTituloTexto=htmlspecialchars(strip_tags($colorTituloTexto));
	$colorTextoFooter=htmlspecialchars(strip_tags($colorTextoFooter));
	$versionRegistro=htmlspecialchars(strip_tags($versionRegistro));
	$regEstado=htmlspecialchars(strip_tags($regEstado));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($regFechaUltimaModificacion));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($regUsuarioUltimaModificacion));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($regFormularioUltimaModificacion));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($regVersionUltimaModificacion));
   
    // bind the values
	$stmt->bindParam(':templateReporteID', $templateReporteID);
	$stmt->bindParam(':nombreTemplateReporte', $nombreTemplateReporte);
	$stmt->bindParam(':logoTemplateReporte', $logoTemplateReporte);
	$stmt->bindParam(':encabezadoTemplateReporte', $encabezadoTemplateReporte);
	$stmt->bindParam(':pieTemplateReporteL1', $pieTemplateReporteL1);
	$stmt->bindParam(':pieTemplateReporteL2', $pieTemplateReporteL2);
	$stmt->bindParam(':pieTemplateReporteL3', $pieTemplateReporteL3);
	$stmt->bindParam(':colorLinea', $colorLinea);
	$stmt->bindParam(':colorTituloTabla', $colorTituloTabla);
	$stmt->bindParam(':colorTituloTexto', $colorTituloTexto);
	$stmt->bindParam(':colorTextoFooter', $colorTextoFooter);
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