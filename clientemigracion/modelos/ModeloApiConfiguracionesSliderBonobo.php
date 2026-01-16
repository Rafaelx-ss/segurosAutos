<?php
// 'user' object
class ConfiguracionesSliderBonobo{
 
    // database connection and table name
    private $Conexion;
    private $Database;
    private $NombreTabla = "ConfiguracionesSlider";
 
    // object properties
    public $Campos;
    public $Dataset;
	public $Mensaje;
    
    
 
    // constructor
    public function __construct($dbf){
        $this->Database = $dbf;
        $this->Conexion = $dbf->conn;
    }
 

 
	// ObtenerEstados() method will be here
	// check if given email exist in the database


	//function ObtenerDatos(){
   		// $id = intval($id);
    	// query to check if email exists
        //$query = "select * from " . $this->NombreTabla ."";
function ObtenerDatos($id=0, $tipoconsulta=""){
   		// $id = intval($id);
		//IF($tipoconsulta=="CONSULTA_LOCAL"){//Se utiliza con la funcion InsertaRegreso en el metodos POST
			$query = "select * from " . $this->NombreTabla . " WHERE 1 " . ($id > 0 ? " and configuracionesSliderID= ?" : "");
		//}
		//ELSE{
		//	$query = "select * from " . $this->NombreTabla . " WHERE catalogoID in(select catalogoID from MigracionCatalogos where aplicacionID=2) " . ($id > 0 ? " and campoGridID= ?" : "");
		//}
 
                //$this->LogMigracion("ApiConfiguracionesSlider", "Consulta Ex1", "Registo ".$Registo.": ".$query, "Consulta");
    	// prepare the query
    	$stmt = $this->Conexion->prepare( $query );
 
    	// sanitize
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
$Registo= "0";
try{
    $consulta="";
	
	$consulta = 'INSERT INTO ' . $this->NombreTabla . 
			" (configuracionesSliderID,tituloSlider,contenidoSlider,imagenSlider,ordenSlider,activoConfiguracionesSlider,versionRegistro,regEstado,
			regFechaUltimaModificacion,regUsuarioUltimaModificacion,regFormularioUltimaModificacion,regVersionUltimaModificacion) VALUES ";
			
		$coma = false;
		$comaText = "";
		
                
                
                
                 foreach($registros as $item)
		 {
			//echo $item['textoID']."lolol";
                $Registo=$item['configuracionesSliderID'];
		
		
		if($this->ObtenerDatos($item['configuracionesSliderID'], "CONSULTA_LOCAL")){
                        $this->LogMigracion("ApiConfiguracionesSlider", "cambia entro", "Registo ".$Registo.": ".$consulta, $consulta);
                        
			$this->Cambia($item['configuracionesSliderID'],($item['tituloSlider']),($item['contenidoSlider']),($item['imagenSlider']),($item['ordenSlider']),$item['activoConfiguracionesSlider'],$item['versionRegistro'],$item['regEstado'],($item['regFechaUltimaModificacion']),$item['regUsuarioUltimaModificacion'],$item['regFormularioUltimaModificacion'],
				$item['regVersionUltimaModificacion']);
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
			$consulta= $consulta . $comaText . "(" . $item["configuracionesSliderID"] . ",'" . ($item["tituloSlider"]) . "','" . ($item["contenidoSlider"]) . "','" . ($item["imagenSlider"]) . "'
			,'" . $item["ordenSlider"] . "'," . $item["activoConfiguracionesSlider"] . "," . $item["versionRegistro"] . "," . $item["regEstado"] . "
			,'" . ($item["regFechaUltimaModificacion"]) . "'," . $item["regUsuarioUltimaModificacion"] . "," . ($item["regFormularioUltimaModificacion"]) . "
			," . ($item["regVersionUltimaModificacion"]) . ")";

                      $this->LogMigracion("ApiConfiguracionesSlider", "insert entro", "Registo ".$Registo.": ".$consulta, $consulta);

		}	 
		   
                 }
	if(!$coma){
		$consulta="select 1";
	}
	
	$this->query=$consulta;
	
    // prepare the query
    $stmt = $this->Conexion->prepare($this->query);
	
    // sanitize
	$configuracionesSliderID=htmlspecialchars(strip_tags($item['configuracionesSliderID']));
	$tituloSlider=htmlspecialchars(strip_tags($item['tituloSlider']));
        $contenidoSlider=htmlspecialchars(strip_tags($item['contenidoSlider']));
	$imagenSlider=htmlspecialchars(strip_tags($item['imagenSlider']));
	$ordenSlider=htmlspecialchars(strip_tags($item['ordenSlider']));
	$activoConfiguracionesSlider=htmlspecialchars(strip_tags($item['activoConfiguracionesSlider']));
	$versionRegistro=htmlspecialchars(strip_tags($item['versionRegistro']));
	$regEstado=htmlspecialchars(strip_tags($item['regEstado']));
        $regFechaUltimaModificacion=htmlspecialchars(strip_tags($item['regFechaUltimaModificacion']));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($item['regVersionUltimaModificacion']));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($item['regUsuarioUltimaModificacion']));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($item['regFormularioUltimaModificacion']));
	
	
    // bind the values
	$stmt->bindParam(':configuracionesSliderID', $configuracionesSliderID);
	$stmt->bindParam(':tituloSlider', $tituloSlider);
        $stmt->bindParam(':contenidoSlider', $contenidoSlider);
        $stmt->bindParam(':imagenSlider', $imagenSlider);
	$stmt->bindParam(':ordenSlider', $ordenSlider);
        //$stmt->bindParam(':activoConfiguracionesSlider', activoConfiguracionesSlider);
        $activoConfiguracionesSlider = (int)$activoConfiguracionesSlider;
        $stmt->bindValue(':activoConfiguracionesSlider', $activoConfiguracionesSlider, PDO::PARAM_INT);
        
	$stmt->bindParam(':versionRegistro', $versionRegistro);
	$regEstado = (int)$regEstado;
	$stmt->bindValue(':regEstado', $regEstado, PDO::PARAM_INT);
	$stmt->bindParam(':regFechaUltimaModificacion', $regFechaUltimaModificacion);
	$stmt->bindParam(':regUsuarioUltimaModificacion', $regUsuarioUltimaModificacion);
	$stmt->bindParam(':regFormularioUltimaModificacion', $regFormularioUltimaModificacion);
	$stmt->bindParam(':regVersionUltimaModificacion', $regVersionUltimaModificacion);
   
   try{
		if($consulta<>"select 1"){
			// execute the query, also check if query was successful
			if($stmt->execute()){
				//$this->LogMigracion("ApiCampos", "INSERTAR", "Registo ".$Registo.": ".$consulta, "INSERCION EXITOSA");

				return true;

			}
		}
		else{
			return true;
		}
    }   
    catch (Exception $e){
		$this->LogMigracion("ApiConfiguracionesSlider", "INSERTAR Ex1", "Registo ".$Registo.": ".$consulta, $e->getMessage());
		$this->Mensaje = $e->getMessage().$consulta;
		return false;
	}
}
catch (Exception $e){
		$this->LogMigracion("ApiConfiguracionesSlider", "INSERTAR Ex2", "Registo ".$Registo.": ".$consulta, $e->getMessage());
		$this->Mensaje = $e->getMessage().$consulta;
	    return false; 
}

 
    return false;
}
function InsertaRegreso($item){
$Registo= "0";
$consulta="";
try{
    
	
	$consulta = 'INSERT INTO ' . $this->NombreTabla . 
			" (configuracionesSliderID,tituloSlider,contenidoSlider,imagenSlider,ordenSlider,activoConfiguracionesSlider,versionRegistro,regEstado,
			regFechaUltimaModificacion,regUsuarioUltimaModificacion,regFormularioUltimaModificacion,regVersionUltimaModificacion) VALUES ";
			
		$coma = false;
		$comaText = "";
		$Registo=$item['configuracionesSliderID'];
		
		
		if($this->ObtenerDatos($item['configuracionesSliderID'], "CONSULTA_LOCAL")){
                        //$this->LogMigracion("ApiConfiguracionesSlider", "cambia entro", "Registo ".$Registo.": ".$query, $consulta);
                        
			$this->Cambia($item['configuracionesSliderID'],($item['tituloSlider']),($item['contenidoSlider']),($item['imagenSlider']),($item['ordenSlider']),$item['activoConfiguracionesSlider'],$item['versionRegistro'],$item['regEstado'],($item['regFechaUltimaModificacion']),$item['regUsuarioUltimaModificacion'],$item['regFormularioUltimaModificacion'],
				$item['regVersionUltimaModificacion']);
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
			$consulta= $consulta . $comaText . "(" . $item["configuracionesSliderID"] . ",'" . ($item["tituloSlider"]) . "','" . ($item["contenidoSlider"]) . "','" . ($item["imagenSlider"]) . "'
			,'" . $item["ordenSlider"] . "'," . $item["activoConfiguracionesSlider"] . "," . $item["versionRegistro"] . "," . $item["regEstado"] . "
			,'" . ($item["regFechaUltimaModificacion"]) . "'," . $item["regUsuarioUltimaModificacion"] . "," . ($item["regFormularioUltimaModificacion"]) . "
			," . ($item["regVersionUltimaModificacion"]) . ")";

                     // $this->LogMigracion("ApiConfiguracionesSlider", "insert entro", "Registo ".$Registo.": ".$query, $consulta);

		}
	if(!$coma){
		$consulta="select 1";
	}
	
	$this->query=$consulta;
	
    // prepare the query
    $stmt = $this->Conexion->prepare($this->query);
	
    // sanitize
	$configuracionesSliderID=htmlspecialchars(strip_tags($item['configuracionesSliderID']));
	$tituloSlider=htmlspecialchars(strip_tags($item['tituloSlider']));
        $contenidoSlider=htmlspecialchars(strip_tags($item['contenidoSlider']));
	$imagenSlider=htmlspecialchars(strip_tags($item['imagenSlider']));
	$ordenSlider=htmlspecialchars(strip_tags($item['ordenSlider']));
	$activoConfiguracionesSlider=htmlspecialchars(strip_tags($item['activoConfiguracionesSlider']));
	$versionRegistro=htmlspecialchars(strip_tags($item['versionRegistro']));
	$regEstado=htmlspecialchars(strip_tags($item['regEstado']));
        $regFechaUltimaModificacion=htmlspecialchars(strip_tags($item['regFechaUltimaModificacion']));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($item['regVersionUltimaModificacion']));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($item['regUsuarioUltimaModificacion']));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($item['regFormularioUltimaModificacion']));
	
	
    // bind the values
	$stmt->bindParam(':configuracionesSliderID', $configuracionesSliderID);
	$stmt->bindParam(':tituloSlider', $tituloSlider);
        $stmt->bindParam(':contenidoSlider', $contenidoSlider);
        $stmt->bindParam(':imagenSlider', $imagenSlider);
	$stmt->bindParam(':ordenSlider', $ordenSlider);
        //$stmt->bindParam(':activoConfiguracionesSlider', activoConfiguracionesSlider);
        $activoConfiguracionesSlider = (int)$activoConfiguracionesSlider;
        $stmt->bindValue(':activoConfiguracionesSlider', $activoConfiguracionesSlider, PDO::PARAM_INT);
        
	$stmt->bindParam(':versionRegistro', $versionRegistro);
	$regEstado = (int)$regEstado;
	$stmt->bindValue(':regEstado', $regEstado, PDO::PARAM_INT);
	$stmt->bindParam(':regFechaUltimaModificacion', $regFechaUltimaModificacion);
	$stmt->bindParam(':regUsuarioUltimaModificacion', $regUsuarioUltimaModificacion);
	$stmt->bindParam(':regFormularioUltimaModificacion', $regFormularioUltimaModificacion);
	$stmt->bindParam(':regVersionUltimaModificacion', $regVersionUltimaModificacion);
   
   try{
		if($consulta<>"select 1"){
			// execute the query, also check if query was successful
			if($stmt->execute()){
				//$this->LogMigracion("ApiCampos", "INSERTAR", "Registo ".$Registo.": ".$consulta, "INSERCION EXITOSA");

				return true;

			}
		}
		else{
			return true;
		}
    }   
    catch (Exception $e){
		$this->LogMigracion("ApiConfiguracionesSlider", "INSERTAR Ex1", "Registo ".$Registo.": ".$consulta, $e->getMessage());
		$this->Mensaje = $e->getMessage().$consulta;
		return false;
	}
}
catch (Exception $e){
		$this->LogMigracion("ApiConfiguracionesSlider", "INSERTAR Ex2", "Registo ", $e->getMessage());
		$this->Mensaje = $e->getMessage().$consulta;
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
function Cambia($configuracionesSliderID,$tituloSlider,$contenidoSlider,$imagenSlider,$ordenSlider,$activoConfiguracionesSlider,$versionRegistro,$regEstado,$regFechaUltimaModificacion,$regUsuarioUltimaModificacion,$regFormularioUltimaModificacion,$regVersionUltimaModificacion){
	$Registo= $configuracionesSliderID;
       /*	$strCampoNull1="textoID=".$textoID.",";
	if(trim($textoID)=="NULL" or trim($textoID)=="null"){
		$strCampoNull1="";
	}
	$strCampoNull2="catalogoReferenciaID=".$catalogoReferenciaID.",";
	if(trim($catalogoReferenciaID)=="NULL" or trim($catalogoReferenciaID)=="null"){
		$catalogoReferenciaID="";
	}*/
        $query ="";
	TRY
	{
		$query = "UPDATE " . $this->NombreTabla . " SET tituloSlider=:tituloSlider,contenidoSlider=:contenidoSlider,imagenSlider=:imagenSlider,ordenSlider=:ordenSlider,activoConfiguracionesSlider=:activoConfiguracionesSlider, versionRegistro=:versionRegistro,regEstado=:regEstado,regFechaUltimaModificacion=:regFechaUltimaModificacion,regUsuarioUltimaModificacion=:regUsuarioUltimaModificacion,regFormularioUltimaModificacion=:regFormularioUltimaModificacion,
		regVersionUltimaModificacion=:regVersionUltimaModificacion
			WHERE configuracionesSliderID=:configuracionesSliderID ";

                  //$this->LogMigracion("ApiConfiguracionesSlider", "Cambia Ex1", "Registo ".$Registo.": ".$query, "Consulta");
		// prepare the query
		$stmt = $this->Conexion->prepare($query);
	 
		 // sanitize
        $configuracionesSliderID=htmlspecialchars(strip_tags($configuracionesSliderID));
	$tituloSlider=htmlspecialchars(strip_tags($tituloSlider));
        $contenidoSlider=htmlspecialchars(strip_tags($contenidoSlider));
	$imagenSlider=htmlspecialchars(strip_tags($imagenSlider));
	$ordenSlider=htmlspecialchars(strip_tags($ordenSlider));
	$activoConfiguracionesSlider=htmlspecialchars(strip_tags($activoConfiguracionesSlider));
	$versionRegistro=htmlspecialchars(strip_tags($versionRegistro));
	$regEstado=htmlspecialchars(strip_tags($regEstado));
        $regFechaUltimaModificacion=htmlspecialchars(strip_tags($regFechaUltimaModificacion));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($regVersionUltimaModificacion));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($regUsuarioUltimaModificacion));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($regFormularioUltimaModificacion));
	
	
    // bind the values
	$stmt->bindParam(':configuracionesSliderID', $configuracionesSliderID);
	$stmt->bindParam(':tituloSlider', $tituloSlider);
        $stmt->bindParam(':contenidoSlider', $contenidoSlider);
        $stmt->bindParam(':imagenSlider', $imagenSlider);
	$stmt->bindParam(':ordenSlider', $ordenSlider);
        //$stmt->bindParam(':activoConfiguracionesSlider', activoConfiguracionesSlider);
        $activoConfiguracionesSlider = (int)$activoConfiguracionesSlider;
        $stmt->bindValue(':activoConfiguracionesSlider', $activoConfiguracionesSlider, PDO::PARAM_INT);
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
				//$this->LogMigracion("Api".$this->NombreTabla, "UPDATE", "Registo ".$Registo.": ".$query, "ACTUALIZACION EXITOSA");
				return true;

			}
		}   
		catch (Exception $e){
			//.'<br /> <br />Consulta: <br />'.$consulta;
			$this->LogMigracion("Api".$this->NombreTabla, "UPDATE Ex1", "Registo ".$Registo.": ".$e->getMessage().$query, $e->getMessage());
			$this->Mensaje = $e->getMessage().$query;
			return false;
		}
	 
	}
	catch (Exception $e){
		$this->LogMigracion("Api".$this->NombreTabla, "UPDATE Ex2", "Registo ".$Registo.": ".$e->getMessage().$query, $e->getMessage());
		$this->Mensaje = $e->getMessage().$query;
		return false;
	}
	 
        return false;
}
	
	
}