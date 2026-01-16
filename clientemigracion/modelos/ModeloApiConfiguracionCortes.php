<?php 
class ConfiguracionCortes{ 
 
private $Conexion; 
private $Database; 
private $NombreTabla = 'ConfiguracionCortes'; 
 
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
		        $strWhere=" configuracionCorteID=:configuracionCorteID and";
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
	 		    
	 		    $stmt->bindParam(':configuracionCorteID', $id);
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
			" (configuracionCorteID,bombasSinBentas,tipoCorte,impresionCorteTermicaDirecto,establecimientoID,estadoReplica,versionRegistro,regEstado,regFechaUltimaModificacion,regUsuarioUltimaModificacion,regFormularioUltimaModificacion,regVersionUltimaModificacion) VALUES ";
			
		$coma = false;
		$comaText = "";
		
		
		foreach($registros as $item)
		{
		    $Registo=$item['configuracionCorteID'];
		    if($this->ObtenerDatos($item['configuracionCorteID'],"",$item['establecimientoID'])){
		        $this->Cambia($item['configuracionCorteID'],$item['bombasSinBentas'],$item['tipoCorte'],$item['impresionCorteTermicaDirecto'],$item['establecimientoID'],$item['estadoReplica'],$item['versionRegistro'],$item['regEstado'],$item['regFechaUltimaModificacion'],$item['regUsuarioUltimaModificacion'],$item['regFormularioUltimaModificacion'],$item['regVersionUltimaModificacion']);
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
		        
		        $consulta= $consulta . $comaText . "(" . $item["configuracionCorteID"] . "," . $item["bombasSinBentas"] . ",'" . $item["tipoCorte"] . "'," . $item["impresionCorteTermicaDirecto"] . ",".$item["establecimientoID"].",".$item['estadoReplica']."," . $item["versionRegistro"] . "," . $item["regEstado"] . ",Now()," . $item["regUsuarioUltimaModificacion"] . "," . $item["regFormularioUltimaModificacion"] . "," . $item["regVersionUltimaModificacion"] . ")";
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
	$configuracionCorteID=htmlspecialchars(strip_tags($item['configuracionCorteID']));
	$bombasSinBentas=htmlspecialchars(strip_tags($item['bombasSinBentas']));
	$tipoCorte=htmlspecialchars(strip_tags($item['tipoCorte']));
	$impresionCorteTermicaDirecto=htmlspecialchars(strip_tags($item['impresionCorteTermicaDirecto']));
	$establecimientoID=htmlspecialchars(strip_tags($item['establecimientoID']));
	$estadoReplica=htmlspecialchars(strip_tags($item['estadoReplica']));
	$versionRegistro=htmlspecialchars(strip_tags($item['versionRegistro']));
	$regEstado=htmlspecialchars(strip_tags($item['regEstado']));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($item['regFechaUltimaModificacion']));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($item['regUsuarioUltimaModificacion']));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($item['regFormularioUltimaModificacion']));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($item['regVersionUltimaModificacion']));
   
    // bind the values
	$stmt->bindParam(':configuracionCorteID', $configuracionCorteID);
	$stmt->bindParam(':bombasSinBentas', $bombasSinBentas);
	$stmt->bindParam(':tipoCorte', $tipoCorte);
	$stmt->bindParam(':impresionCorteTermicaDirecto', $impresionCorteTermicaDirecto);
	$stmt->bindParam(':establecimientoID', $establecimientoID);
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
        " (configuracionCorteID,bombasSinBentas,tipoCorte,impresionCorteTermicaDirecto,establecimientoID,estadoReplica,versionRegistro,regEstado,regFechaUltimaModificacion,regUsuarioUltimaModificacion,regFormularioUltimaModificacion,regVersionUltimaModificacion) VALUES ";
        
        $coma = false;
        $comaText = "";
        
        
        foreach($registros as $item)
        {
            $Registo=$item['configuracionCorteID'];
            if($this->ObtenerDatos($item['configuracionCorteID'],"",$item['establecimientoID'])){
                $this->Cambia($item['configuracionCorteID'],$item['bombasSinBentas'],$item['tipoCorte'],$item['impresionCorteTermicaDirecto'],$item['establecimientoID'],$item['estadoReplica'],$item['versionRegistro'],$item['regEstado'],$item['regFechaUltimaModificacion'],$item['regUsuarioUltimaModificacion'],$item['regFormularioUltimaModificacion'],$item['regVersionUltimaModificacion']);
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
                
                $consulta= $consulta . $comaText . "(" . $item["configuracionCorteID"] . "," . $item["bombasSinBentas"] . ",'" . $item["tipoCorte"] . "'," . $item["impresionCorteTermicaDirecto"] . ",".$item["establecimientoID"].",".$item['estadoReplica']."," . $item["versionRegistro"] . "," . $item["regEstado"] . ",Now()," . $item["regUsuarioUltimaModificacion"] . "," . $item["regFormularioUltimaModificacion"] . "," . $item["regVersionUltimaModificacion"] . ")";
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
        $configuracionCorteID=htmlspecialchars(strip_tags($item['configuracionCorteID']));
        $bombasSinBentas=htmlspecialchars(strip_tags($item['bombasSinBentas']));
        $tipoCorte=htmlspecialchars(strip_tags($item['tipoCorte']));
        $impresionCorteTermicaDirecto=htmlspecialchars(strip_tags($item['impresionCorteTermicaDirecto']));
        $establecimientoID=htmlspecialchars(strip_tags($item['establecimientoID']));
        $estadoReplica=htmlspecialchars(strip_tags($item['estadoReplica']));
        $versionRegistro=htmlspecialchars(strip_tags($item['versionRegistro']));
        $regEstado=htmlspecialchars(strip_tags($item['regEstado']));
        $regFechaUltimaModificacion=htmlspecialchars(strip_tags($item['regFechaUltimaModificacion']));
        $regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($item['regUsuarioUltimaModificacion']));
        $regFormularioUltimaModificacion=htmlspecialchars(strip_tags($item['regFormularioUltimaModificacion']));
        $regVersionUltimaModificacion=htmlspecialchars(strip_tags($item['regVersionUltimaModificacion']));
        
        // bind the values
        $stmt->bindParam(':configuracionCorteID', $configuracionCorteID);
        $stmt->bindParam(':bombasSinBentas', $bombasSinBentas);
        $stmt->bindParam(':tipoCorte', $tipoCorte);
        $stmt->bindParam(':impresionCorteTermicaDirecto', $impresionCorteTermicaDirecto);
        $stmt->bindParam(':establecimientoID', $establecimientoID);
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


function Cambia($configuracionCorteID,$bombasSinBentas,$tipoCorte,$impresionCorteTermicaDirecto,$establecimientoID,$estadoReplica,$versionRegistro,$regEstado,$regFechaUltimaModificacion,$regUsuarioUltimaModificacion,$regFormularioUltimaModificacion,$regVersionUltimaModificacion){
	$Registo= $configuracionCorteID;
			try	{
			$query = "UPDATE " . $this->NombreTabla . " SET bombasSinBentas=:bombasSinBentas,
                    tipoCorte=:tipoCorte,
                    impresionCorteTermicaDirecto=:impresionCorteTermicaDirecto,
                    estadoReplica=:estadoReplica,
                    versionRegistro=:versionRegistro,
                    regEstado=:regEstado,
                    regFechaUltimaModificacion=:regFechaUltimaModificacion,
                    regUsuarioUltimaModificacion=:regUsuarioUltimaModificacion,
                    regFormularioUltimaModificacion=:regFormularioUltimaModificacion,
                    regVersionUltimaModificacion=:regVersionUltimaModificacion 
				WHERE configuracionCorteID=:configuracionCorteID and establecimientoID=:establecimientoID ";

		    // prepare the query
		    $stmt = $this->Conexion->prepare($query);
		 
		     // sanitize
			$configuracionCorteID=htmlspecialchars(strip_tags($configuracionCorteID));
			//$nombreAccion=htmlspecialchars(strip_tags($nombreAccion));
			$bombasSinBentas=htmlspecialchars(strip_tags(bombasSinBentas));
			$tipoCorte=htmlspecialchars(strip_tags($tipoCorte));
			$impresionCorteTermicaDirecto=htmlspecialchars(strip_tags($impresionCorteTermicaDirecto));
			$establecimientoID=htmlspecialchars(strip_tags($establecimientoID));
			$estadoReplica=htmlspecialchars(strip_tags($estadoReplica));
			$versionRegistro=htmlspecialchars(strip_tags($versionRegistro));
			$regEstado=htmlspecialchars(strip_tags($regEstado));
			$regFechaUltimaModificacion=htmlspecialchars(strip_tags($regFechaUltimaModificacion));
			$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($regUsuarioUltimaModificacion));
			$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($regFormularioUltimaModificacion));
			$regVersionUltimaModificacion=htmlspecialchars(strip_tags($regVersionUltimaModificacion));
		   
		    // bind the values
			$stmt->bindParam(':configuracionCorteID', $configuracionCorteID);
			$bombasSinBentas = (int)$bombasSinBentas;
			$stmt->bindParam(':bombasSinVentas', $bombasSinBentas,PDO::PARAM_INT);
			$stmt->bindParam(':tipoCorte', $tipoCorte);
			$impresionCorteTermicaDirecto = (int)$impresionCorteTermicaDirecto;
			$stmt->bindParam(':impresionCorteTermicaDirecto', $impresionCorteTermicaDirecto,PDO::PARAM_INT);
			$stmt->bindParam(':establecimientoID', $establecimientoID);
			$stmt->bindParam(':estadoReplica', $estadoReplica);
			$stmt->bindParam(':versionRegistro', $versionRegistro);
			$regEstado = (int)$regEstado;
			$stmt->bindValue(':regEstado', $regEstado, PDO::PARAM_INT);
			$stmt->bindParam(':regFechaUltimaModificacion', $regFechaUltimaModificacion);
			$stmt->bindParam(':regUsuarioUltimaModificacion', $regUsuarioUltimaModificacion);
			$stmt->bindParam(':regFormularioUltimaModificacion', $regFormularioUltimaModificacion);
			$stmt->bindParam(':regVersionUltimaModificacion', $regVersionUltimaModificacion);
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