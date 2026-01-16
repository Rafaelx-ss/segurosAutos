<?php 
class ConfiguracionesGlobales{ 
 
private $Conexion; 
private $Database; 
private $NombreTabla = 'ConfiguracionesGlobales'; 
 
// object properties
public $Campos;
public $Dataset;
public $Mensaje;
public $Mensaje2; 
// constructor
    public function __construct($dbf){
        $this->Database = $dbf;
        $this->Conexion = $dbf->conn;
    }
	
	function ObtenerDatos($establecimientoID=0){
    	// query to check if email exists
    	try{
    		$query = "select * from " . $this->NombreTabla .  " where 1 " . ($establecimientoID > 0 ? " and establecimientoID = :establecimientoID" : "");
 
    	// prepare the query
    	$stmt = $this->Conexion->prepare( $query );
 		$this->Mensaje2=$query;
    	 // sanitize
		if($establecimientoID > 0){
			$establecimientoID=htmlspecialchars(strip_tags($establecimientoID));
		}
 
    	// bind given id value

    
    	 // bind the values
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
    	}catch (Exception $e){
			$this->Mensaje = $e->getMessage();
			return false;//.'<br /> <br />Consulta: <br />'.$consulta;
		}

    	
	}
 

function Inserta($registros){
	try{
		$consulta="";
	
	$consulta = 'INSERT INTO ' . $this->NombreTabla . 
			" (tiempoHorasFacturacion,rutaWebSocket,impresionIva,impresionPagare,establecimientoID,tiempoReimpresion,versionRegistro,regEstado,regFechaUltimaModificacion,regUsuarioUltimaModificacion,regFormularioUltimaModificacion,regVersionUltimaModificacion) VALUES ";
			
		$coma = false;
		$comaText = "";
		foreach($registros as $item)
		 {
			//echo $item['textoID']."lolol";
			 if($this->ObtenerDatos($item['establecimientoID'])){
				$this->Cambia($item['tiempoHorasFacturacion'],($item['rutaWebSocket']),$item['impresionIva'],$item['impresionPagare'],($item['establecimientoID']),($item['tiempoReimpresion']),$item['versionRegistro'],$item['regEstado'],($item['regFechaUltimaModificacion']),$item['regUsuarioUltimaModificacion'],$item['regFormularioUltimaModificacion'],$item['regVersionUltimaModificacion']);
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
				
				$consulta= $consulta . $comaText . "(" . $item["tiempoHorasFacturacion"] . ",'" . ($item["rutaWebSocket"]) . "'," . $item["impresionIva"] . "," . $item["impresionPagare"] . ",'" . ($item["establecimientoID"]) . "','" . ($item["tiempoReimpresion"]) . "'," . $item["versionRegistro"] . "," . $item["regEstado"] . ",'" . ($item["regFechaUltimaModificacion"]) . "'," . $item["regUsuarioUltimaModificacion"] . "," . $item["regFormularioUltimaModificacion"] . "," . $item["regVersionUltimaModificacion"] . ")";
			}
			 
		   
		 }
	if(!$coma){
		$consulta="select 1";
	}
	
	$this->query=$consulta;
	
    // prepare the query
    $stmt = $this->Conexion->prepare($this->query);
	$this->Mensaje2=$this->query;
    // sanitize
	$tiempoHorasFacturacion=htmlspecialchars(strip_tags($item['tiempoHorasFacturacion']));
	$rutaWebSocket=htmlspecialchars(strip_tags($item['rutaWebSocket']));
	$impresionIva=htmlspecialchars(strip_tags($item['impresionIva']));
	$impresionPagare=htmlspecialchars(strip_tags($item['impresionPagare']));
	$establecimientoID=htmlspecialchars(strip_tags($item['establecimientoID']));
	$tiempoReimpresion=htmlspecialchars(strip_tags($item['tiempoReimpresion']));
	$versionRegistro=htmlspecialchars(strip_tags($item['versionRegistro']));
	$regEstado=htmlspecialchars(strip_tags($item['regEstado']));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($item['regFechaUltimaModificacion']));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($item['regUsuarioUltimaModificacion']));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($item['regFormularioUltimaModificacion']));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($item['regVersionUltimaModificacion']));
   
    // bind the values
	$stmt->bindParam(':tiempoHorasFacturacion', $tiempoHorasFacturacion);
	$stmt->bindParam(':rutaWebSocket', $rutaWebSocket);
	$impresionIva = (int)$impresionIva;
	$stmt->bindValue(':impresionIva', $impresionIva, PDO::PARAM_INT);
	$impresionPagare = (int)$impresionPagare;
	$stmt->bindValue(':impresionPagare', $impresionPagare, PDO::PARAM_INT);
	$stmt->bindParam(':establecimientoID', $establecimientoID);
	$stmt->bindParam(':tiempoReimpresion', $tiempoReimpresion);
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
        $this->Mensaje = $e->getMessage();//.'<br /> <br />Consulta: <br />'.$consulta;
    }catch (Exception $e){
			$this->Mensaje = $e->getMessage();
			return false;//.'<br /> <br />Consulta: <br />'.$consulta;
		}

 
    return false;
	}
	catch (Exception $e){
			$this->Mensaje = $e->getMessage();
			return false;//.'<br /> <br />Consulta: <br />'.$consulta;
		}
    
}


function Cambia($tiempoHorasFacturacion,$rutaWebSocket,$impresionIva,$impresionPagare,$establecimientoID,$tiempoReimpresion,$versionRegistro,$regEstado,$regFechaUltimaModificacion,$regUsuarioUltimaModificacion,$regFormularioUltimaModificacion,$regVersionUltimaModificacion){
	try{
		$query = "UPDATE " . $this->NombreTabla . " SET rutaWebSocket=:rutaWebSocket,impresionIva=:impresionIva,impresionPagare=:impresionPagare,establecimientoID=:establecimientoID,tiempoReimpresion=:tiempoReimpresion,versionRegistro=:versionRegistro,regEstado=:regEstado,regFechaUltimaModificacion=:regFechaUltimaModificacion,regUsuarioUltimaModificacion=:regUsuarioUltimaModificacion,regFormularioUltimaModificacion=:regFormularioUltimaModificacion,regVersionUltimaModificacion=:regVersionUltimaModificacion 
		WHERE establecimientoID=:establecimientoID ";

    // prepare the query
    $stmt = $this->Conexion->prepare($query);
 $this->Mensaje2=$query;
     // sanitize
	$tiempoHorasFacturacion=htmlspecialchars(strip_tags($tiempoHorasFacturacion));
	$rutaWebSocket=htmlspecialchars(strip_tags($rutaWebSocket));
	$impresionIva=htmlspecialchars(strip_tags($impresionIva));
	$impresionPagare=htmlspecialchars(strip_tags($impresionPagare));
	$establecimientoID=htmlspecialchars(strip_tags($establecimientoID));
	$tiempoReimpresion=htmlspecialchars(strip_tags($tiempoReimpresion));
	$versionRegistro=htmlspecialchars(strip_tags($versionRegistro));
	$regEstado=htmlspecialchars(strip_tags($regEstado));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($regFechaUltimaModificacion));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($regUsuarioUltimaModificacion));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($regFormularioUltimaModificacion));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($regVersionUltimaModificacion));
   
    // bind the values
	$stmt->bindParam(':tiempoHorasFacturacion', $tiempoHorasFacturacion);
	$stmt->bindParam(':rutaWebSocket', $rutaWebSocket);
	$impresionIva = (int)$impresionIva;
	$stmt->bindValue(':impresionIva', $impresionIva, PDO::PARAM_INT);
	$impresionPagare = (int)$impresionPagare;
	$stmt->bindValue(':impresionPagare', $impresionPagare, PDO::PARAM_INT);
	$stmt->bindParam(':establecimientoID', $establecimientoID);
	$stmt->bindParam(':tiempoReimpresion', $tiempoReimpresion);
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
        $this->Mensaje = $e->getMessage();//.'<br /> <br />Consulta: <br />'.$consulta;
    }
 
    return false;
	}catch (Exception $e){
			$this->Mensaje = $e->getMessage();
			return false;//.'<br /> <br />Consulta: <br />'.$consulta;
		}
    
}
	
		
}
	?>