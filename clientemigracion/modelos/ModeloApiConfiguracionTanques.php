<?php 
class ConfiguracionTanques{ 
 
private $Conexion; 
private $Database; 
private $NombreTabla = 'ConfiguracionLecturasTanques'; 
 
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
	
	function ObtenerDatos($ID=0, $establecimientoID=0){
    	// query to check if email exists
    	try{
    		$query = "select * from " . $this->NombreTabla .  " where  configuracionLecturasTanquesID=:configuracionLecturasTanquesID and establecimientoID=:establecimientoID ";
 
    	// prepare the query
    	$stmt = $this->Conexion->prepare( $query );
 		$this->Mensaje2=$query;
    	 // sanitize
		
		$ID=htmlspecialchars(strip_tags($ID));
		$establecimientoID=htmlspecialchars(strip_tags($establecimientoID));
 
    	// bind given id value

    	 // bind the values
		$stmt->bindParam(':configuracionLecturasTanquesID', $ID);
		$stmt->bindParam(':establecimientoID', $establecimientoID);
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
			" (configuracionLecturasTanquesID,puertoConexion,bitParada,velocidadBaudiosID,paridadID,longitudBits,intervaloConsultas,tipoComunicacion,guardaDescargas,cantidadDescargasPantalla,guardaLogs,rutaDBTEAM,
			usuarioDBTEAM,PassDBTEAM,nombreBDTEAM,passINCON,rutaINCON,intervaloEnvioFlotillas,horasPermitidas,
			establecimientoID,versionRegistro,regEstado,regFechaUltimaModificacion,regUsuarioUltimaModificacion,regFormularioUltimaModificacion,regVersionUltimaModificacion) VALUES ";
			
		$coma = false;
		$comaText = "";
		foreach($registros as $item)
		 {
			//echo $item['textoID']."lolol";
			 if($this->ObtenerDatos($item['configuracionLecturasTanquesID'], $item['establecimientoID'])){
				$this->Cambia($item['configuracionLecturasTanquesID'],$item['puertoConexion'],$item['bitParada'],$item['velocidadBaudiosID'],$item['paridadID'],$item['longitudBits'],$item['intervaloConsultas'],$item['tipoComunicacion'],$item['guardaDescargas'],$item['cantidadDescargasPantalla'],$item['guardaLogs'],
				$item['rutaDBTEAM'],$item['usuarioDBTEAM'],$item['PassDBTEAM'],$item['nombreBDTEAM'],$item['passINCON'],$item['rutaINCON'],$item['intervaloEnvioFlotillas'],$item['horasPermitidas'],
				$item['establecimientoID'],$item['versionRegistro'],$item['regEstado'],$item['regFechaUltimaModificacion'],$item['regUsuarioUltimaModificacion'],$item['regFormularioUltimaModificacion'],$item['regVersionUltimaModificacion']);
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
				
				$consulta= $consulta . $comaText . "(" . $item['configuracionLecturasTanquesID'] . ",'" . $item['puertoConexion'] . "'," . $item['bitParada'] . "," . $item['velocidadBaudiosID'] . "," . $item['paridadID'] . "," . $item['longitudBits'] . "," . $item['intervaloConsultas'] . ",'" . $item['tipoComunicacion'] . "'," . $item['guardaDescargas'] . "," . $item['cantidadDescargasPantalla'] . "," . $item['guardaLogs'] . ",'" .
				$item['rutaDBTEAM'] . "','" . $item['usuarioDBTEAM'] . "','" . $item['PassDBTEAM'] . "','" . $item['nombreBDTEAM'] . "','" . $item['passINCON'] . "','" . $item['rutaINCON'] . "'," . $item['intervaloEnvioFlotillas'] . ",'" . $item['horasPermitidas'] . "'," . 
				$item['establecimientoID'] . "," . $item['versionRegistro'] . "," . $item['regEstado'] . ",now()," . $item['regUsuarioUltimaModificacion'] . "," . $item['regFormularioUltimaModificacion'] . "," . $item['regVersionUltimaModificacion'] .")";
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
	$configuracionLecturasTanquesID=htmlspecialchars(strip_tags($item['configuracionLecturasTanquesID']));
	$puertoConexion=htmlspecialchars(strip_tags($item['puertoConexion']));
	$bitParada=htmlspecialchars(strip_tags($item['bitParada']));
	$velocidadBaudiosID=htmlspecialchars(strip_tags($item['velocidadBaudiosID']));
	$paridadID=htmlspecialchars(strip_tags($item['paridadID']));
	$longitudBits=htmlspecialchars(strip_tags($item['longitudBits']));
	$intervaloConsultas=htmlspecialchars(strip_tags($item['intervaloConsultas']));
	$tipoComunicacion=htmlspecialchars(strip_tags($item['tipoComunicacion']));
	$guardaDescargas=htmlspecialchars(strip_tags($item['guardaDescargas']));
	$cantidadDescargasPantalla=htmlspecialchars(strip_tags($item['cantidadDescargasPantalla']));
	$guardaLogs=htmlspecialchars(strip_tags($item['guardaLogs']));
	$rutaDBTEAM=htmlspecialchars(strip_tags($item['rutaDBTEAM']));
	$usuarioDBTEAM=htmlspecialchars(strip_tags($item['usuarioDBTEAM']));
	$PassDBTEAM=htmlspecialchars(strip_tags($item['PassDBTEAM']));
	$nombreBDTEAM=htmlspecialchars(strip_tags($item['nombreBDTEAM']));
	$passINCON=htmlspecialchars(strip_tags($item['passINCON']));
	$rutaINCON=htmlspecialchars(strip_tags($item['rutaINCON']));
	$intervaloEnvioFlotillas=htmlspecialchars(strip_tags($item['intervaloEnvioFlotillas']));
	$horasPermitidas=htmlspecialchars(strip_tags($item['horasPermitidas']));
	$establecimientoID=htmlspecialchars(strip_tags($item['establecimientoID']));
	$versionRegistro=htmlspecialchars(strip_tags($item['versionRegistro']));
	$regEstado=htmlspecialchars(strip_tags($item['regEstado']));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($item['regFechaUltimaModificacion']));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($item['regUsuarioUltimaModificacion']));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($item['regFormularioUltimaModificacion']));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($item['regVersionUltimaModificacion']));
   
    // bind the values
	$stmt->bindParam(':configuracionLecturasTanquesID', $configuracionLecturasTanquesID);
	$stmt->bindParam(':puertoConexion', $puertoConexion);
	$stmt->bindParam(':bitParada', $bitParada);
	$stmt->bindParam(':velocidadBaudiosID', $velocidadBaudiosID);
	$stmt->bindParam(':paridadID', $paridadID);
	$stmt->bindParam(':longitudBits', $longitudBits);
	$stmt->bindParam(':intervaloConsultas', $intervaloConsultas);
	$stmt->bindParam(':tipoComunicacion', $tipoComunicacion);
	$stmt->bindParam(':guardaDescargas', $guardaDescargas);
	$stmt->bindParam(':cantidadDescargasPantalla', $cantidadDescargasPantalla);
	$stmt->bindParam(':guardaLogs', $guardaLogs);
	$stmt->bindParam(':rutaDBTEAM', $rutaDBTEAM);
	$stmt->bindParam(':usuarioDBTEAM', $usuarioDBTEAM);
	$stmt->bindParam(':PassDBTEAM', $PassDBTEAM);
	$stmt->bindParam(':nombreBDTEAM', $nombreBDTEAM);
	$stmt->bindParam(':passINCON', $passINCON);
	$stmt->bindParam(':rutaINCON', $rutaINCON);
	$stmt->bindParam(':intervaloEnvioFlotillas', $intervaloEnvioFlotillas);
	$stmt->bindParam(':horasPermitidas', $horasPermitidas);
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
        $this->Mensaje = $e->getMessage();//.'<br /> <br />Consulta: <br />'.$consulta;
    }

    return false;
	}catch (Exception $e){
		$this->Mensaje = $e->getMessage();
		return false;//.'<br /> <br />Consulta: <br />'.$consulta;
	}
}

function Cambia($configuracionLecturasTanquesID,$puertoConexion,$bitParada,$velocidadBaudiosID,$paridadID,$longitudBits,$intervaloConsultas,$tipoComunicacion,$guardaDescargas,$cantidadDescargasPantalla,$guardaLogs,$rutaDBTEAM,
			$usuarioDBTEAM,$PassDBTEAM,$nombreBDTEAM,$passINCON,$rutaINCON,$intervaloEnvioFlotillas,$horasPermitidas,
			$establecimientoID,$versionRegistro,$regEstado,$regFechaUltimaModificacion,$regUsuarioUltimaModificacion,$regFormularioUltimaModificacion,$regVersionUltimaModificacion){
	try{
		$query = "UPDATE " . $this->NombreTabla . " SET puertoConexion=:puertoConexion, bitParada=:bitParada, velocidadBaudiosID=:velocidadBaudiosID, paridadID=:paridadID, longitudBits=:longitudBits, intervaloConsultas=:intervaloConsultas, tipoComunicacion=:tipoComunicacion,
		guardaDescargas=:guardaDescargas, cantidadDescargasPantalla=:cantidadDescargasPantalla, guardaLogs=:guardaLogs, rutaDBTEAM=:rutaDBTEAM, usuarioDBTEAM=:usuarioDBTEAM, PassDBTEAM=:PassDBTEAM, nombreBDTEAM=:nombreBDTEAM,
		passINCON=:passINCON, rutaINCON=:rutaINCON, intervaloEnvioFlotillas=:intervaloEnvioFlotillas, horasPermitidas=:horasPermitidas,
		versionRegistro=:versionRegistro, regEstado=:regEstado, regFechaUltimaModificacion=:regFechaUltimaModificacion, regUsuarioUltimaModificacion=:regUsuarioUltimaModificacion, regFormularioUltimaModificacion=:regFormularioUltimaModificacion, regVersionUltimaModificacion=:regVersionUltimaModificacion 
		WHERE configuracionLecturasTanquesID=:configuracionLecturasTanquesID and establecimientoID=:establecimientoID ";

    // prepare the query
    $stmt = $this->Conexion->prepare($query);
 	$this->Mensaje2=$query;

     // sanitize
	$configuracionLecturasTanquesID=htmlspecialchars(strip_tags($configuracionLecturasTanquesID));
	$puertoConexion=htmlspecialchars(strip_tags($puertoConexion));
	$bitParada=htmlspecialchars(strip_tags($bitParada));
	$velocidadBaudiosID=htmlspecialchars(strip_tags($velocidadBaudiosID));
	$paridadID=htmlspecialchars(strip_tags($paridadID));
	$longitudBits=htmlspecialchars(strip_tags($longitudBits));
	$intervaloConsultas=htmlspecialchars(strip_tags($intervaloConsultas));
	$tipoComunicacion=htmlspecialchars(strip_tags($tipoComunicacion));
	$guardaDescargas=htmlspecialchars(strip_tags($guardaDescargas));
	$cantidadDescargasPantalla=htmlspecialchars(strip_tags($cantidadDescargasPantalla));
	$guardaLogs=htmlspecialchars(strip_tags($guardaLogs));
	$rutaDBTEAM=htmlspecialchars(strip_tags($rutaDBTEAM));
	$usuarioDBTEAM=htmlspecialchars(strip_tags($usuarioDBTEAM));
	$PassDBTEAM=htmlspecialchars(strip_tags($PassDBTEAM));
	$nombreBDTEAM=htmlspecialchars(strip_tags($nombreBDTEAM));
	$passINCON=htmlspecialchars(strip_tags($passINCON));
	$rutaINCON=htmlspecialchars(strip_tags($rutaINCON));
	$intervaloEnvioFlotillas=htmlspecialchars(strip_tags($intervaloEnvioFlotillas));
	$horasPermitidas=htmlspecialchars(strip_tags($horasPermitidas));
	$establecimientoID=htmlspecialchars(strip_tags($establecimientoID));
	$versionRegistro=htmlspecialchars(strip_tags($versionRegistro));
	$regEstado=htmlspecialchars(strip_tags($regEstado));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($regFechaUltimaModificacion));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($regUsuarioUltimaModificacion));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($regFormularioUltimaModificacion));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($regVersionUltimaModificacion));
   
    // bind the values
	$stmt->bindParam(':configuracionLecturasTanquesID', $configuracionLecturasTanquesID);
	$stmt->bindParam(':puertoConexion', $puertoConexion);
	$stmt->bindParam(':bitParada', $bitParada);
	$stmt->bindParam(':velocidadBaudiosID', $velocidadBaudiosID);
	$stmt->bindParam(':paridadID', $paridadID);
	$stmt->bindParam(':longitudBits', $longitudBits);
	$stmt->bindParam(':intervaloConsultas', $intervaloConsultas);
	$stmt->bindParam(':tipoComunicacion', $tipoComunicacion);
	$stmt->bindParam(':guardaDescargas', $guardaDescargas, PDO::PARAM_INT);
	$stmt->bindParam(':cantidadDescargasPantalla', $cantidadDescargasPantalla);
	$stmt->bindParam(':guardaLogs', $guardaLogs, PDO::PARAM_INT);
	$stmt->bindParam(':rutaDBTEAM', $rutaDBTEAM);
	$stmt->bindParam(':usuarioDBTEAM', $usuarioDBTEAM);
	$stmt->bindParam(':PassDBTEAM', $PassDBTEAM);
	$stmt->bindParam(':nombreBDTEAM', $nombreBDTEAM);
	$stmt->bindParam(':passINCON', $passINCON);
	$stmt->bindParam(':rutaINCON', $rutaINCON);
	$stmt->bindParam(':intervaloEnvioFlotillas', $intervaloEnvioFlotillas);
	$stmt->bindParam(':horasPermitidas', $horasPermitidas);
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