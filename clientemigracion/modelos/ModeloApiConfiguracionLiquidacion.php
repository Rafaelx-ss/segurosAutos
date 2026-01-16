<?php 
class ConfiguracionLiquidacion{ 
 
private $Conexion; 
private $Database; 
private $NombreTabla = 'ConfiguracionLiquidacion'; 
 
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
	
    function ObtenerDatos($id=0, $tipoconsulta="",$establecimientoID){
		try {

				// $id = intval($id);
	    	// query to check if email exists
	    	
		    
		    $strWhere="";
		    $strTop="";
		    if ($id !=0){
		        $strWhere=" configuracionLiquidacionID=:configuracionLiquidacionID and";
		    }
		    
		    
		    // query to check if email exists
		    //$query = "select * from " . $this->NombreTabla . " " . ($id > 0 ? " where configuracionReclasificacionID = ?" : "");
		    $query = "select * from " . $this->NombreTabla . " where ". $strWhere ."  establecimientoID=:establecimientoID";
	    	
	 
	    	// prepare the query
	    	$stmt = $this->Conexion->prepare( $query );
	 		$this->Mensaje2=$query;
	    	// sanitize
	 		$id=htmlspecialchars(strip_tags($id));
	 		$establecimientoID=htmlspecialchars(strip_tags($establecimientoID));
	 		
	 		if($id > 0){
	 		    
	 		    $stmt->bindParam(':configuracionLiquidacionID', $id);
	 		}
	 		$stmt->bindParam(':establecimientoID', $establecimientoID);
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
			}catch (Exception $e){
			$this->Mensaje = $e->getMessage();
			return false;//.'<br /> <br />Consulta: <br />'.$consulta;
		}
   		
	}
 


	function InsertaRegreso($registros){
$Registo= "0";
try{

    $consulta="";
	
	$consulta = 'INSERT INTO ' . $this->NombreTabla . 
			" (configuracionLiquidacionID,activiaTiempoLiquidacion,cantidadJornadasReporte,configuracionReporteLiquidacionID,imprimeLogEventosLiquidacion,tiempoLiquidacion,tolerancialiquidacion,verDescripcionJornada,establecimientoID,estadoReplica,versionRegistro,regEstado,regFechaUltimaModificacion,regUsuarioUltimaModificacion,regFormularioUltimaModificacion,regVersionUltimaModificacion,resumenConImpuestos) VALUES ";
			
		$coma = false;
		$comaText = "";

		
		foreach($registros as $item)
		{
		    if($this->ObtenerDatos($item['configuracionLiquidacionID'],"",$item['establecimientoID'])){
		        $this->Cambia($item['configuracionLiquidacionID'],$item['activiaTiempoLiquidacion'],$item['cantidadJornadasReporte'],$item['configuracionReporteLiquidacionID'],$item['imprimeLogEventosLiquidacion'],$item['tiempoLiquidacion'],$item['tolerancialiquidacion'],$item['verDescripcionJornada'],$item['establecimientoID'],$item['estadoReplica'],$item['versionRegistro'],$item['regEstado'],$item['regFechaUltimaModificacion'],$item['regUsuarioUltimaModificacion'],$item['regFormularioUltimaModificacion'],$item['regVersionUltimaModificacion'],$item['resumenConImpuestos']);
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
		        
		        $consulta= $consulta . $comaText . "(" . $item["configuracionLiquidacionID"] . "," . $item["activiaTiempoLiquidacion"] . "," . $item["cantidadJornadasReporte"] . "," . $item["configuracionReporteLiquidacionID"] . "," . $item["imprimeLogEventosLiquidacion"] . "," . $item["tiempoLiquidacion"] . "," . $item["tolerancialiquidacion"] . "," . $item["verDescripcionJornada"] . ",".$item["establecimientoID"]."," . $item["estadoReplica"] . "," . $item["versionRegistro"] . "," . $item["regEstado"] . ",Now()," . $item["regUsuarioUltimaModificacion"] . "," . $item["regFormularioUltimaModificacion"] . "," . $item["regVersionUltimaModificacion"] . "," . $item["resumenConImpuestos"] . ")";
		    }
		    
		    
		}
		
		
	if(!$coma){
		$consulta="select 1";
	}
	
	$this->query=$consulta;
	
    // prepare the query
    $stmt = $this->Conexion->prepare($this->query);
    $this->Mensaje2=$consulta;
    // sanitize
	$configuracionLiquidacionID=htmlspecialchars(strip_tags($item['configuracionLiquidacionID']));
	$activiaTiempoLiquidacion=htmlspecialchars(strip_tags($item['activiaTiempoLiquidacion']));
	$cantidadJornadasReporte=htmlspecialchars(strip_tags($item['cantidadJornadasReporte']));
	$configuracionReporteLiquidacionID=htmlspecialchars(strip_tags($item['configuracionReporteLiquidacionID']));
	$imprimeLogEventosLiquidacion=htmlspecialchars(strip_tags($item['imprimeLogEventosLiquidacion']));
	$tiempoLiquidacion=htmlspecialchars(strip_tags($item['tiempoLiquidacion']));
	$tolerancialiquidacion=htmlspecialchars(strip_tags($item['tolerancialiquidacion']));
	$verDescripcionJornada=htmlspecialchars(strip_tags($item['verDescripcionJornada']));
	$estadoReplica=htmlspecialchars(strip_tags($item['estadoReplica']));
	$versionRegistro=htmlspecialchars(strip_tags($item['versionRegistro']));
    $establecimientoID=htmlspecialchars(strip_tags($item['establecimientoID']));
	$regEstado=htmlspecialchars(strip_tags($item['regEstado']));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($item['regFechaUltimaModificacion']));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($item['regUsuarioUltimaModificacion']));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($item['regFormularioUltimaModificacion']));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($item['regVersionUltimaModificacion']));
	$resumenConImpuestos=htmlspecialchars(strip_tags($item['resumenConImpuestos']));
   
    // bind the values
	$stmt->bindParam(':configuracionLiquidacionID', $configuracionLiquidacionID);
	$stmt->bindParam(':activiaTiempoLiquidacion', $activiaTiempoLiquidacion);
	$stmt->bindParam(':cantidadJornadasReporte', $cantidadJornadasReporte);
	$stmt->bindParam(':configuracionReporteLiquidacionID', $configuracionReporteLiquidacionID);
	$stmt->bindParam(':imprimeLogEventosLiquidacion', $imprimeLogEventosLiquidacion);
	$stmt->bindParam(':tiempoLiquidacion', $tiempoLiquidacion);
	$stmt->bindParam(':tolerancialiquidacion', $tolerancialiquidacion);
	$stmt->bindParam(':verDescripcionJornada', $verDescripcionJornada);
	$stmt->bindParam(':establecimientoID', $establecimientoID);
	$stmt->bindParam(':estadoReplica', $estadoReplica);
	$stmt->bindParam(':versionRegistro', $versionRegistro);
	$regEstado = (int)$regEstado;
	$stmt->bindValue(':regEstado', $regEstado, PDO::PARAM_INT);
	$stmt->bindParam(':regFechaUltimaModificacion', $regFechaUltimaModificacion);
	$stmt->bindParam(':regUsuarioUltimaModificacion', $regUsuarioUltimaModificacion);
	$stmt->bindParam(':regFormularioUltimaModificacion', $regFormularioUltimaModificacion);
	$stmt->bindParam(':regVersionUltimaModificacion', $regVersionUltimaModificacion);
	$stmt->bindParam(':resumenConImpuestos', $resumenConImpuestos);
   
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
		$this->Mensaje .= $e->getMessage();
	}
}
catch (Exception $e){
		$this->LogMigracion("Api".$this->NombreTabla, "INSERTAR Ex2", "Registo ".$Registo.": ".$consulta, $e->getMessage());
	$this->Mensaje .= $e->getMessage();
}
    return false;
}
function Inserta($registros){
    $Registo= "0";
    try{
        
        $consulta="";
        
        $consulta = 'INSERT INTO ' . $this->NombreTabla .
        " (configuracionLiquidacionID,activiaTiempoLiquidacion,cantidadJornadasReporte,configuracionReporteLiquidacionID,imprimeLogEventosLiquidacion,tiempoLiquidacion,tolerancialiquidacion,verDescripcionJornada,establecimientoID,estadoReplica,versionRegistro,regEstado,regFechaUltimaModificacion,regUsuarioUltimaModificacion,regFormularioUltimaModificacion,regVersionUltimaModificacion,resumenConImpuestos) VALUES ";
        
        $coma = false;
        $comaText = "";
        
        
        foreach($registros as $item)
        {
            if($this->ObtenerDatos($item['configuracionLiquidacionID'],"",$item['establecimientoID'])){
                $this->Cambia($item['configuracionLiquidacionID'],$item['activiaTiempoLiquidacion'],$item['cantidadJornadasReporte'],$item['configuracionReporteLiquidacionID'],$item['imprimeLogEventosLiquidacion'],$item['tiempoLiquidacion'],$item['tolerancialiquidacion'],$item['verDescripcionJornada'],$item['establecimientoID'],$item['estadoReplica'],$item['versionRegistro'],$item['regEstado'],$item['regFechaUltimaModificacion'],$item['regUsuarioUltimaModificacion'],$item['regFormularioUltimaModificacion'],$item['regVersionUltimaModificacion'],$item['resumenConImpuestos']);
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
                
                $consulta= $consulta . $comaText . "(" . $item["configuracionLiquidacionID"] . "," . $item["activiaTiempoLiquidacion"] . "," . $item["cantidadJornadasReporte"] . "," . $item["configuracionReporteLiquidacionID"] . "," . $item["imprimeLogEventosLiquidacion"] . "," . $item["tiempoLiquidacion"] . "," . $item["tolerancialiquidacion"] . "," . $item["verDescripcionJornada"] . ",".$item["establecimientoID"]."," . $item["estadoReplica"] . "," . $item["versionRegistro"] . "," . $item["regEstado"] . ",Now()," . $item["regUsuarioUltimaModificacion"] . "," . $item["regFormularioUltimaModificacion"] . "," . $item["regVersionUltimaModificacion"] . "," . $item["resumenConImpuestos"] . ")";
            }
            
            
        }
        
        
        if(!$coma){
            $consulta="select 1";
        }
        
        $this->query=$consulta;
        
        // prepare the query
        $stmt = $this->Conexion->prepare($this->query);
        $this->Mensaje2=$consulta;
        // sanitize
        $configuracionLiquidacionID=htmlspecialchars(strip_tags($item['configuracionLiquidacionID']));
        $activiaTiempoLiquidacion=htmlspecialchars(strip_tags($item['activiaTiempoLiquidacion']));
        $cantidadJornadasReporte=htmlspecialchars(strip_tags($item['cantidadJornadasReporte']));
        $configuracionReporteLiquidacionID=htmlspecialchars(strip_tags($item['configuracionReporteLiquidacionID']));
        $imprimeLogEventosLiquidacion=htmlspecialchars(strip_tags($item['imprimeLogEventosLiquidacion']));
        $tiempoLiquidacion=htmlspecialchars(strip_tags($item['tiempoLiquidacion']));
        $tolerancialiquidacion=htmlspecialchars(strip_tags($item['tolerancialiquidacion']));
        $verDescripcionJornada=htmlspecialchars(strip_tags($item['verDescripcionJornada']));
        $estadoReplica=htmlspecialchars(strip_tags($item['estadoReplica']));
        $versionRegistro=htmlspecialchars(strip_tags($item['versionRegistro']));
        $establecimientoID=htmlspecialchars(strip_tags($item['establecimientoID']));
        $regEstado=htmlspecialchars(strip_tags($item['regEstado']));
        $regFechaUltimaModificacion=htmlspecialchars(strip_tags($item['regFechaUltimaModificacion']));
        $regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($item['regUsuarioUltimaModificacion']));
        $regFormularioUltimaModificacion=htmlspecialchars(strip_tags($item['regFormularioUltimaModificacion']));
        $regVersionUltimaModificacion=htmlspecialchars(strip_tags($item['regVersionUltimaModificacion']));
        $resumenConImpuestos=htmlspecialchars(strip_tags($item['resumenConImpuestos']));
        
        // bind the values
        $stmt->bindParam(':configuracionLiquidacionID', $configuracionLiquidacionID);
        $stmt->bindParam(':activiaTiempoLiquidacion', $activiaTiempoLiquidacion);
        $stmt->bindParam(':cantidadJornadasReporte', $cantidadJornadasReporte);
        $stmt->bindParam(':configuracionReporteLiquidacionID', $configuracionReporteLiquidacionID);
        $stmt->bindParam(':imprimeLogEventosLiquidacion', $imprimeLogEventosLiquidacion);
        $stmt->bindParam(':tiempoLiquidacion', $tiempoLiquidacion);
        $stmt->bindParam(':tolerancialiquidacion', $tolerancialiquidacion);
        $stmt->bindParam(':verDescripcionJornada', $verDescripcionJornada);
        $stmt->bindParam(':establecimientoID', $establecimientoID);
        $stmt->bindParam(':estadoReplica', $estadoReplica);
        $stmt->bindParam(':versionRegistro', $versionRegistro);
        $regEstado = (int)$regEstado;
        $stmt->bindValue(':regEstado', $regEstado, PDO::PARAM_INT);
        $stmt->bindParam(':regFechaUltimaModificacion', $regFechaUltimaModificacion);
        $stmt->bindParam(':regUsuarioUltimaModificacion', $regUsuarioUltimaModificacion);
        $stmt->bindParam(':regFormularioUltimaModificacion', $regFormularioUltimaModificacion);
        $stmt->bindParam(':regVersionUltimaModificacion', $regVersionUltimaModificacion);
        $stmt->bindParam(':resumenConImpuestos', $resumenConImpuestos);
        
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
            $this->Mensaje .= $e->getMessage();
        }
    }
    catch (Exception $e){
        $this->LogMigracion("Api".$this->NombreTabla, "INSERTAR Ex2", "Registo ".$Registo.": ".$consulta, $e->getMessage());
        $this->Mensaje .= $e->getMessage();
    }
    return false;
}

function LogMigracion($Api, $metodo, $consulta, $mensaje){
	try{
		$query2 ="INSERT INTO LogMigracion (logMigracionId, api, metodo, consulta, mensaje, regEstado, regFechaUltimaModificacion, regUsuarioUltimaModificacion, regFormularioUltimaModificacion, regVersionUltimaModificacion, resumenConImpuestos) 
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


function Cambia($configuracionLiquidacionID,$activiaTiempoLiquidacion,$cantidadJornadasReporte,$configuracionReporteLiquidacionID,$imprimeLogEventosLiquidacion,$tiempoLiquidacion,$tolerancialiquidacion,$verDescripcionJornada,$establecimientoID,$estadoReplica,$versionRegistro,$regEstado,$regFechaUltimaModificacion,$regUsuarioUltimaModificacion,$regFormularioUltimaModificacion,$regVersionUltimaModificacion,$resumenConImpuestos){
    $Registo= $configuracionLiquidacionID;
			try	{
			$query = "UPDATE " . $this->NombreTabla . " SET activiaTiempoLiquidacion=:activiaTiempoLiquidacion,
                cantidadJornadasReporte=:cantidadJornadasReporte,
                configuracionReporteLiquidacionID=:configuracionReporteLiquidacionID,
                imprimeLogEventosLiquidacion=:imprimeLogEventosLiquidacion,
                tiempoLiquidacion=:tiempoLiquidacion,
                tolerancialiquidacion=:tolerancialiquidacion,
                verDescripcionJornada=:verDescripcionJornada,
                establecimientoID=:establecimientoID,
                estadoReplica=:estadoReplica,
                versionRegistro=:versionRegistro,
                regEstado=:regEstado,
                regFechaUltimaModificacion=:regFechaUltimaModificacion,
                regUsuarioUltimaModificacion=:regUsuarioUltimaModificacion,
                regFormularioUltimaModificacion=:regFormularioUltimaModificacion,
                regVersionUltimaModificacion=:regVersionUltimaModificacion,
				resumenConImpuestos=:resumenConImpuestos 
				WHERE configuracionLiquidacionID=:configuracionLiquidacionID and establecimientoID=:establecimientoID ";

		    // prepare the query
		    $stmt = $this->Conexion->prepare($query);
		 
		     // sanitize
			$configuracionLiquidacionID=htmlspecialchars(strip_tags($configuracionLiquidacionID));
			$activiaTiempoLiquidacion=htmlspecialchars(strip_tags($activiaTiempoLiquidacion));
			$cantidadJornadasReporte=htmlspecialchars(strip_tags($cantidadJornadasReporte));
			$configuracionReporteLiquidacionID=htmlspecialchars(strip_tags($configuracionReporteLiquidacionID));
			$imprimeLogEventosLiquidacion=htmlspecialchars(strip_tags($imprimeLogEventosLiquidacion));
			$tiempoLiquidacion=htmlspecialchars(strip_tags($tiempoLiquidacion));
			$tolerancialiquidacion=htmlspecialchars(strip_tags($tolerancialiquidacion));
			$verDescripcionJornada=htmlspecialchars(strip_tags($verDescripcionJornada));
            $establecimientoID=htmlspecialchars(strip_tags($establecimientoID));
            $estadoReplica=htmlspecialchars(strip_tags($estadoReplica));
			$versionRegistro=htmlspecialchars(strip_tags($versionRegistro));
			$regEstado=htmlspecialchars(strip_tags($regEstado));
			$regFechaUltimaModificacion=htmlspecialchars(strip_tags($regFechaUltimaModificacion));
			$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($regUsuarioUltimaModificacion));
			$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($regFormularioUltimaModificacion));
			$regVersionUltimaModificacion=htmlspecialchars(strip_tags($regVersionUltimaModificacion));
			$resumenConImpuestos=htmlspecialchars(strip_tags($resumenConImpuestos));
		   
		    // bind the values
			$stmt->bindParam(':configuracionLiquidacionID', $configuracionLiquidacionID);
			$activiaTiempoLiquidacion = (int)$activiaTiempoLiquidacion;
			$stmt->bindParam(':activiaTiempoLiquidacion', $activiaTiempoLiquidacion,PDO::PARAM_INT);
			$stmt->bindParam(':cantidadJornadasReporte', $cantidadJornadasReporte);
			$stmt->bindParam(':configuracionReporteLiquidacionID', $configuracionReporteLiquidacionID);
			$imprimeLogEventosLiquidacion = (int)$imprimeLogEventosLiquidacion;
			$stmt->bindParam(':imprimeLogEventosLiquidacion', $imprimeLogEventosLiquidacion,PDO::PARAM_INT);
			$stmt->bindParam(':tiempoLiquidacion', $tiempoLiquidacion);
			$stmt->bindParam(':tolerancialiquidacion', $tolerancialiquidacion);
			$verDescripcionJornada = (int)$verDescripcionJornada;
			$stmt->bindParam(':verDescripcionJornada', $verDescripcionJornada,PDO::PARAM_INT);
            $stmt->bindParam(':establecimientoID', $establecimientoID);
            $stmt->bindParam(':estadoReplica', $estadoReplica);
			$stmt->bindParam(':versionRegistro', $versionRegistro);
			$regEstado = (int)$regEstado;
			$stmt->bindValue(':regEstado', $regEstado, PDO::PARAM_INT);
			$stmt->bindParam(':regFechaUltimaModificacion', $regFechaUltimaModificacion);
			$stmt->bindParam(':regUsuarioUltimaModificacion', $regUsuarioUltimaModificacion);
			$stmt->bindParam(':regFormularioUltimaModificacion', $regFormularioUltimaModificacion);
			$stmt->bindParam(':regVersionUltimaModificacion', $regVersionUltimaModificacion);
			$stmt->bindParam(':resumenConImpuestos', $resumenConImpuestos);
			$this->Mensaje2=$query;
			try{
				// execute the query, also check if query was successful
				if($stmt->execute()){
					//$this->LogMigracion("Api".$this->NombreTabla, "UPDATE", "Registo ".$Registo.": ".$query, "ACTUALIZACION EXITOSA");
					return true;

				}
			}   
			catch (Exception $e){
				
				$this->LogMigracion("Api".$this->NombreTabla, "UPDATE Ex1", "Registo ".$Registo.": ".$e->getMessage().$query, $e->getMessage());
				$this->Mensaje = $e->getMessage();//.'<br /> <br />Consulta: <br />'.$consulta;
			}
		 
		}
		catch (Exception $e){
			$this->LogMigracion("Api".$this->NombreTabla, "UPDATE Ex2", "Registo ".$Registo.": ".$e->getMessage().$query, $e->getMessage());
			$this->Mensaje = $e->getMessage();//.'<br /> <br />Consulta: <br />'.$consulta;
		}
		    return false;
	}
	
		
}
	?>