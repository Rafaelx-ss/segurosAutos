<?php 
class ConfiguracionCompras{ 
 
private $Conexion; 
private $Database; 
private $NombreTabla = 'ConfiguracionCompras'; 
 
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
		    if ($id != 0){
		        $strWhere=" configuracionCompraID=:configuracionCompraID and";
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
	 		    
	 		    $stmt->bindParam(':configuracionCompraID', $id);
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
			" (configuracionCompraID,validaFolioAlfaNumerico,calculaPrecioCompra,establecimientoID,estadoReplica,versionRegistro,regEstado,regFechaUltimaModificacion,regUsuarioUltimaModificacion,regFormularioUltimaModificacion,regVersionUltimaModificacion,cargasRemotas) VALUES ";
			
		
	
	//$Registo=$item['configuracionCompraID'];
	
	$coma = false;
	$comaText = "";
	foreach($registros as $item)
	{
	    if($this->ObtenerDatos($item['configuracionCompraID'],"",$item['establecimientoID'])){
	        $this->Cambia($item['configuracionCompraID'],$item['validaFolioAlfaNumerico'],$item['calculaPrecioCompra'],$item['establecimientoID'],$item['estadoReplica'],$item['versionRegistro'],$item['regEstado'],$item['regFechaUltimaModificacion'],$item['regUsuarioUltimaModificacion'],$item['regFormularioUltimaModificacion'],$item['regVersionUltimaModificacion'],$item['cargasRemotas']);
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
	        $consulta= $consulta . $comaText . "(" . $item["configuracionCompraID"] . "," . $item["validaFolioAlfaNumerico"] . "," . $item["calculaPrecioCompra"] . ",".$item['establecimientoID']."," . $item["estadoReplica"] . "," . $item["versionRegistro"] . "," . $item["regEstado"] . ",Now()," . $item["regUsuarioUltimaModificacion"] . "," . $item["regFormularioUltimaModificacion"] . "," . $item["regVersionUltimaModificacion"] . "," . $item["cargasRemotas"] . ")";
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
	$configuracionCompraID=htmlspecialchars(strip_tags($item['configuracionCompraID']));
	$validaFolioAlfaNumerico=htmlspecialchars($item['validaFolioAlfaNumerico'], ENT_QUOTES,'UTF-8',false);
	$calculaPrecioCompra=htmlspecialchars(strip_tags($item['calculaPrecioCompra']));
	$establecimientoID=htmlspecialchars(strip_tags($item['establecimientoID']));
	$estadoReplica=htmlspecialchars(strip_tags($item['estadoReplica']));
	$versionRegistro=htmlspecialchars(strip_tags($item['versionRegistro']));
	$regEstado=htmlspecialchars(strip_tags($item['regEstado']));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($item['regFechaUltimaModificacion']));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($item['regUsuarioUltimaModificacion']));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($item['regFormularioUltimaModificacion']));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($item['regVersionUltimaModificacion']));
	$cargasRemotas=htmlspecialchars(strip_tags($item['cargasRemotas']));
   
    // bind the values
	$stmt->bindParam(':configuracionCompraID', $configuracionCompraID);
	$validaFolioAlfaNumerico = (int)$validaFolioAlfaNumerico;
	$stmt->bindValue(':validaFolioAlfaNumerico', $validaFolioAlfaNumerico, PDO::PARAM_INT);
	$calculaPrecioCompra = (int)$calculaPrecioCompra;
	$stmt->bindParam(':calculaPrecioCompra', $calculaPrecioCompra, PDO::PARAM_INT);
	$stmt->bindParam(':establecimientoID', $establecimientoID);
	$stmt->bindParam(':estadoReplica', $estadoReplica);
	$stmt->bindParam(':versionRegistro', $versionRegistro);
	$regEstado = (int)$regEstado;
	$stmt->bindValue(':regEstado', $regEstado, PDO::PARAM_INT);
	$stmt->bindParam(':regFechaUltimaModificacion', $regFechaUltimaModificacion);
	$stmt->bindParam(':regUsuarioUltimaModificacion', $regUsuarioUltimaModificacion);
	$stmt->bindParam(':regFormularioUltimaModificacion', $regFormularioUltimaModificacion);
	$stmt->bindParam(':regVersionUltimaModificacion', $regVersionUltimaModificacion);
	$cargasRemotas = (int)$cargasRemotas;
	$stmt->bindParam(':cargasRemotas', $cargasRemotas,PDO::PARAM_INT);
    
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
        " (configuracionCompraID,validaFolioAlfaNumerico,calculaPrecioCompra,establecimientoID,estadoReplica,versionRegistro,regEstado,regFechaUltimaModificacion,regUsuarioUltimaModificacion,regFormularioUltimaModificacion,regVersionUltimaModificacion,cargasRemotas) VALUES ";
        
        
        
        //$Registo=$item['configuracionCompraID'];
        
        $coma = false;
        $comaText = "";
        foreach($registros as $item)
        {
            if($this->ObtenerDatos($item['configuracionCompraID'],"",$item['establecimientoID'])){
                $this->Cambia($item['configuracionCompraID'],$item['validaFolioAlfaNumerico'],$item['calculaPrecioCompra'],$item['establecimientoID'],$item['estadoReplica'],$item['versionRegistro'],$item['regEstado'],$item['regFechaUltimaModificacion'],$item['regUsuarioUltimaModificacion'],$item['regFormularioUltimaModificacion'],$item['regVersionUltimaModificacion'],$item['cargasRemotas']);
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
                $consulta= $consulta . $comaText . "(" . $item["configuracionCompraID"] . "," . $item["validaFolioAlfaNumerico"] . "," . $item["calculaPrecioCompra"] . ",".$item['establecimientoID']."," . $item["estadoReplica"] . "," . $item["versionRegistro"] . "," . $item["regEstado"] . ",Now()," . $item["regUsuarioUltimaModificacion"] . "," . $item["regFormularioUltimaModificacion"] . "," . $item["regVersionUltimaModificacion"] . "," . $item["cargasRemotas"] . ")";
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
        $configuracionCompraID=htmlspecialchars(strip_tags($item['configuracionCompraID']));
        $validaFolioAlfaNumerico=htmlspecialchars($item['validaFolioAlfaNumerico'], ENT_QUOTES,'UTF-8',false);
        $calculaPrecioCompra=htmlspecialchars(strip_tags($item['calculaPrecioCompra']));
        $establecimientoID=htmlspecialchars(strip_tags($item['establecimientoID']));
        $estadoReplica=htmlspecialchars(strip_tags($item['estadoReplica']));
        $versionRegistro=htmlspecialchars(strip_tags($item['versionRegistro']));
        $regEstado=htmlspecialchars(strip_tags($item['regEstado']));
        $regFechaUltimaModificacion=htmlspecialchars(strip_tags($item['regFechaUltimaModificacion']));
        $regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($item['regUsuarioUltimaModificacion']));
        $regFormularioUltimaModificacion=htmlspecialchars(strip_tags($item['regFormularioUltimaModificacion']));
        $regVersionUltimaModificacion=htmlspecialchars(strip_tags($item['regVersionUltimaModificacion']));
        $cargasRemotas=htmlspecialchars(strip_tags($item['cargasRemotas']));
        
        // bind the values
        $stmt->bindParam(':configuracionCompraID', $configuracionCompraID);
        $validaFolioAlfaNumerico = (int)$validaFolioAlfaNumerico;
        $stmt->bindValue(':validaFolioAlfaNumerico', $validaFolioAlfaNumerico, PDO::PARAM_INT);
        $calculaPrecioCompra = (int)$calculaPrecioCompra;
        $stmt->bindParam(':calculaPrecioCompra', $calculaPrecioCompra, PDO::PARAM_INT);
        $stmt->bindParam(':establecimientoID', $establecimientoID);
        $stmt->bindParam(':estadoReplica', $estadoReplica);
        $stmt->bindParam(':versionRegistro', $versionRegistro);
        $regEstado = (int)$regEstado;
        $stmt->bindValue(':regEstado', $regEstado, PDO::PARAM_INT);
        $stmt->bindParam(':regFechaUltimaModificacion', $regFechaUltimaModificacion);
        $stmt->bindParam(':regUsuarioUltimaModificacion', $regUsuarioUltimaModificacion);
        $stmt->bindParam(':regFormularioUltimaModificacion', $regFormularioUltimaModificacion);
        $stmt->bindParam(':regVersionUltimaModificacion', $regVersionUltimaModificacion);
        $cargasRemotas = (int)$cargasRemotas;
        $stmt->bindParam(':cargasRemotas', $cargasRemotas,PDO::PARAM_INT);
        
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
		$query2 ="INSERT INTO LogMigracion (logMigracionId, api, metodo, consulta, mensaje, regEstado, regFechaUltimaModificacion, regUsuarioUltimaModificacion, regFormularioUltimaModificacion, regVersionUltimaModificacion,cargasRemotas) 
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


function Cambia($configuracionCompraID,$validaFolioAlfaNumerico,$calculaPrecioCompra,$establecimientoID,$estadoReplica,$versionRegistro,$regEstado,$regFechaUltimaModificacion,$regUsuarioUltimaModificacion,$regFormularioUltimaModificacion,$regVersionUltimaModificacion,$cargasRemotas){
    $Registo= $configuracionCompraID;
			try	{
			$query = "UPDATE " . $this->NombreTabla . " SET validaFolioAlfaNumerico=:validaFolioAlfaNumerico,calculaPrecioCompra=:calculaPrecioCompra,establecimientoID=:establecimientoID,estadoReplica=:estadoReplica,versionRegistro=:versionRegistro,regEstado=:regEstado,regFechaUltimaModificacion=:regFechaUltimaModificacion,regUsuarioUltimaModificacion=:regUsuarioUltimaModificacion,regFormularioUltimaModificacion=:regFormularioUltimaModificacion,regVersionUltimaModificacion=:regVersionUltimaModificacion,cargasRemotas=:cargasRemotas 
				WHERE configuracionCompraID=:configuracionCompraID and establecimientoID=:establecimientoID";

		    // prepare the query
		    $stmt = $this->Conexion->prepare($query);
		 
		     // sanitize
			$configuracionCompraID=htmlspecialchars(strip_tags($configuracionCompraID));
		    $validaFolioAlfaNumerico=htmlspecialchars(strip_tags($validaFolioAlfaNumerico));
			$calculaPrecioCompra=htmlspecialchars(strip_tags($calculaPrecioCompra));
			$establecimientoID=htmlspecialchars(strip_tags($establecimientoID));
			$estadoReplica=htmlspecialchars(strip_tags($estadoReplica));
			$versionRegistro=htmlspecialchars(strip_tags($versionRegistro));
			$regEstado=htmlspecialchars(strip_tags($regEstado));
			$regFechaUltimaModificacion=htmlspecialchars(strip_tags($regFechaUltimaModificacion));
			$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($regUsuarioUltimaModificacion));
			$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($regFormularioUltimaModificacion));
			$regVersionUltimaModificacion=htmlspecialchars(strip_tags($regVersionUltimaModificacion));
			$cargasRemotas=htmlspecialchars(strip_tags($cargasRemotas));
		   
		    // bind the values
			$stmt->bindParam(':configuracionCompraID', $configuracionCompraID);
			$validaFolioAlfaNumerico = (int)$validaFolioAlfaNumerico;
			$stmt->bindParam(':validaFolioAlfaNumerico', $validaFolioAlfaNumerico,PDO::PARAM_INT);
			$calculaPrecioCompra = (int)$calculaPrecioCompra;
			$stmt->bindParam(':calculaPrecioCompra', $calculaPrecioCompra,PDO::PARAM_INT);
			$stmt->bindParam(':establecimientoID', $establecimientoID);
			$stmt->bindParam(':estadoReplica', $estadoReplica);
			$stmt->bindParam(':versionRegistro', $versionRegistro);
			$regEstado = (int)$regEstado;
			$stmt->bindValue(':regEstado', $regEstado, PDO::PARAM_INT);
			$stmt->bindParam(':regFechaUltimaModificacion', $regFechaUltimaModificacion);
			$stmt->bindParam(':regUsuarioUltimaModificacion', $regUsuarioUltimaModificacion);
			$stmt->bindParam(':regFormularioUltimaModificacion', $regFormularioUltimaModificacion);
			$stmt->bindParam(':regVersionUltimaModificacion', $regVersionUltimaModificacion);
			$$cargasRemotas = (int)$cargasRemotas;
			$stmt->bindParam(':cargasRemotas', $cargasRemotas,PDO::PARAM_INT);
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