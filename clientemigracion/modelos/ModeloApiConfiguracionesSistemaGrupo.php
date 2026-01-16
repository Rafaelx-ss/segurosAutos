<?php
// 'user' object
class ConfiguracionesSistemaGrupo{
 
     // database connection and table name
    private $Conexion;
    private $Database;
    private $NombreTabla = "ConfiguracionesSistema";
 
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
			$query = "select * from " . $this->NombreTabla . " WHERE 1 " . ($id > 0 ? " and configuracionesSistemaID= ?" : "");
		//}
		//ELSE{
		//	$query = "select * from " . $this->NombreTabla . " WHERE catalogoID in(select catalogoID from MigracionCatalogos where aplicacionID=2) " . ($id > 0 ? " and campoGridID= ?" : "");
		//}
                
                //$this->LogMigracion("ApiConfiguracionesSistema", "Consulta", "Registo ".$Registo.": ".$query,"Consulta");
                
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

 function Inserta2($registros){
$Registo= "0";
try{
    $consulta="";
	
	$consulta = 'INSERT INTO ' . $this->NombreTabla . 
			" (configuracionesSistemaID,logoLogin,logoBanner,iconoMenu,temaBanner,temaMenu,temaContenido,activoConfiguracionesSistema,versionRegistro,regEstado,regFechaUltimaModificacion,regUsuarioUltimaModificacion,regFormularioUltimaModificacion,regVersionUltimaModificacion,logoFooter,favIcon,titlePagina,footerPagina,btnAccion,btnSave,btnMenu) VALUES ";
			
		$coma = false;
		$comaText = "";
		
		
		
                
                foreach($registros as $item)
		 {
			//echo $item['textoID']."lolol";
                    $Registo=$item['configuracionesSistemaID'];
			 
		if($this->ObtenerDatos($item['configuracionesSistemaID'], "CONSULTA_LOCAL")){
			$this->Cambia($item['configuracionesSistemaID'],($item['logoLogin']),($item['logoBanner']),($item['iconoMenu']),$item['temaBanner'],$item['temaMenu'],$item['temaContenido'],$item['activoConfiguracionesSistema'],$item['versionRegistro'],$item['regEstado'],($item['regFechaUltimaModificacion']),$item['regUsuarioUltimaModificacion'],$item['regFormularioUltimaModificacion'],
				$item['regVersionUltimaModificacion'],$item["logoFooter"],$item["favIcon"],$item["titlePagina"],$item["footerPagina"],$item["btnAccion"],$item["btnSave"],$item["btnMenu"]);
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
			$consulta= $consulta . $comaText . "(" . $item["configuracionesSistemaID"] . ",'" . ($item["logoLogin"]) . "','" . ($item["logoBanner"]) . "','" . ($item["iconoMenu"]) . "'
			,'" . $item["temaBanner"] . "','" . $item["temaMenu"]  . "','" . $item["temaContenido"] . "',".$item["activoConfiguracionesSistema"]."," . $item["versionRegistro"] . "," . $item["regEstado"] . "
			,'" . ($item["regFechaUltimaModificacion"]) . "'," . $item["regUsuarioUltimaModificacion"] . "," . ($item["regFormularioUltimaModificacion"]) . "
			," . ($item["regVersionUltimaModificacion"]) . ",'" . ($item["logoFooter"]) . "','" . ($item["favIcon"]) . "','" . $item["titlePagina"] . "','" .$item["footerPagina"]. "'
			,'" . $item["btnAccion"] . "','" . $item["btnSave"] . "','" . $item["btnMenu"] . "')";



		}
			 
		   
                 }
	if(!$coma){
		$consulta="select 1";
	}
	
	$this->query=$consulta;
	
    // prepare the query
    $stmt = $this->Conexion->prepare($this->query);
	
    // sanitize
	$configuracionesSistemaID=htmlspecialchars(strip_tags($item['configuracionesSistemaID']));
	$logoLogin=htmlspecialchars(strip_tags($item['logoLogin']));
	$logoBanner=htmlspecialchars(strip_tags($item['logoBanner']));
	$iconoMenu=htmlspecialchars(strip_tags($item['iconoMenu']));
	$temaBanner=htmlspecialchars(strip_tags($item['temaBanner']));
        $temaMenu=htmlspecialchars(strip_tags($item['temaMenu']));
	$temaContenido=htmlspecialchars(strip_tags($item['temaContenido']));
	$activoConfiguracionesSistema=htmlspecialchars(strip_tags($item['activoConfiguracionesSistema']));
	$versionRegistro=htmlspecialchars(strip_tags($item['versionRegistro']));
	$regEstado=htmlspecialchars(strip_tags($item['regEstado']));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($item['regVersionUltimaModificacion']));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($item['regUsuarioUltimaModificacion']));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($item['regFormularioUltimaModificacion']));
        $logoFooter=htmlspecialchars(strip_tags($item['logoFooter']));
        $favIcon=htmlspecialchars(strip_tags($item['favIcon']));
        $titlePagina=htmlspecialchars(strip_tags($item['titlePagina']));
	$footerPagina=htmlspecialchars(strip_tags($item['footerPagina']));
        $btnAccion=htmlspecialchars(strip_tags($item['btnAccion']));
	$btnSave=htmlspecialchars(strip_tags($item['btnSave']));
	$btnMenu=htmlspecialchars(strip_tags($item['btnMenu']));
	
    // bind the values
        
        //$campoPK = (int)$campoPK;
	//$stmt->bindValue(':campoPK', $campoPK, PDO::PARAM_INT);
        
	$stmt->bindParam(':configuracionesSistemaID', $configuracionesSistemaID);
	$stmt->bindParam(':logoLogin', $logoLogin);
	$stmt->bindParam(':logoBanner', $logoBanner);
	$stmt->bindParam(':iconoMenu', $iconoMenu);
	$stmt->bindParam(':temaBanner', $temaBanner);
        $stmt->bindParam(':temaMenu', $temaMenu);
        $stmt->bindParam(':temaContenido', $temaContenido);
         $activoConfiguracionesSistema = (int)$activoConfiguracionesSistema;
	$stmt->bindValue(':activoConfiguracionesSistema', $activoConfiguracionesSistema, PDO::PARAM_INT);
        //$stmt->bindParam(':activoConfiguracionesSistema', $activoConfiguracionesSistema);
        $stmt->bindParam(':versionRegistro', $versionRegistro);
        $regEstado = (int)$regEstado;
	$stmt->bindValue(':regEstado', $regEstado, PDO::PARAM_INT);
        $stmt->bindParam(':regVersionUltimaModificacion', $regVersionUltimaModificacion);
        $stmt->bindParam(':regUsuarioUltimaModificacion', $regUsuarioUltimaModificacion);
        $stmt->bindParam(':regFormularioUltimaModificacion', $regFormularioUltimaModificacion);
        $stmt->bindParam(':logoFooter', $logoFooter);
        $stmt->bindParam(':favIcon', $favIcon);
        $stmt->bindParam(':titlePagina', $titlePagina);
        $stmt->bindParam(':footerPagina', $footerPagina);
        $stmt->bindParam(':btnAccion', $btnAccion);
        $stmt->bindParam(':btnSave', $btnSave);
        $stmt->bindParam(':btnMenu', $btnMenu);
        
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
		$this->LogMigracion("ApiConfiguracionesSistema", "INSERTAR Ex1", "Registo ".$Registo.": ".$consulta, $e->getMessage());
		$this->Mensaje = $e->getMessage().$consulta;
		return false;
	}
}
catch (Exception $e){
		$this->LogMigracion("ApiConfiguracionesSistema", "INSERTAR Ex2", "Registo ".$Registo.": ".$consulta, $e->getMessage());
		$this->Mensaje = $e->getMessage().$consulta;
	    return false; 
}

 
    return false;
}

function Inserta($registros){
    
    $this->LogMigracion("ApiConfiguracionesSistema", "Inserta 3", "Registo ", "inserta 3");
      
    foreach($registros as $item){
        $this->InsertaRegreso($item);
    }
    
    return true;
}
 function Inserta3($registros){
$Registo= "0";
try{
    $consulta="";
	
	$consulta = 'INSERT INTO ' . $this->NombreTabla . 
			" (configuracionesSistemaID,logoLogin,logoBanner,iconoMenu,temaBanner,temaMenu,temaContenido,activoConfiguracionesSistema,versionRegistro,regEstado,regFechaUltimaModificacion,regUsuarioUltimaModificacion,regFormularioUltimaModificacion,regVersionUltimaModificacion,logoFooter,favIcon,titlePagina,footerPagina,btnAccion,btnSave,btnMenu) VALUES ";
			
		$coma = false;
		$comaText = "";
		
		
		
                
                foreach($registros as $item)
		 {
			//echo $item['textoID']."lolol";
                    $Registo=$item['configuracionesSistemaID'];
			 
		if($this->ObtenerDatos($item['configuracionesSistemaID'], "CONSULTA_LOCAL")){
                    
                    
			$this->Cambia($item['configuracionesSistemaID'],($item['logoLogin']),($item['logoBanner']),($item['iconoMenu']),$item['temaBanner'],$item['temaMenu'],$item['temaContenido'],$item['activoConfiguracionesSistema'],$item['versionRegistro'],$item['regEstado'],($item['regFechaUltimaModificacion']),$item['regUsuarioUltimaModificacion'],$item['regFormularioUltimaModificacion'],
				$item['regVersionUltimaModificacion'],$item["logoFooter"],$item["favIcon"],$item["titlePagina"],$item["footerPagina"],$item["btnAccion"],$item["btnSave"],$item["btnMenu"]);
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
			$consulta= $consulta . $comaText . "(" . $item["configuracionesSistemaID"] . ",'" . ($item["logoLogin"]) . "','" . ($item["logoBanner"]) . "','" . ($item["iconoMenu"]) . "'
			,'" . $item["temaBanner"] . "','" . $item["temaMenu"]  . "','" . $item["temaContenido"] . "',".$item["activoConfiguracionesSistema"]."," . $item["versionRegistro"] . "," . $item["regEstado"] . "
			,'" . ($item["regFechaUltimaModificacion"]) . "'," . $item["regUsuarioUltimaModificacion"] . "," . ($item["regFormularioUltimaModificacion"]) . "
			," . ($item["regVersionUltimaModificacion"]) . ",'" . ($item["logoFooter"]) . "','" . ($item["favIcon"]) . "','" . $item["titlePagina"] . "','" .$item["footerPagina"]. "'
			,'" . $item["btnAccion"] . "','" . $item["btnSave"] . "','" . $item["btnMenu"] . "')";



		}
			 
		   
                 }
	if(!$coma){
		$consulta="select 1";
	}
	
	$this->query=$consulta;
	
    // prepare the query
    $stmt = $this->Conexion->prepare($this->query);
	
    // sanitize
	$configuracionesSistemaID=htmlspecialchars(strip_tags($item['configuracionesSistemaID']));
	$logoLogin=htmlspecialchars(strip_tags($item['logoLogin']));
	$logoBanner=htmlspecialchars(strip_tags($item['logoBanner']));
	$iconoMenu=htmlspecialchars(strip_tags($item['iconoMenu']));
	$temaBanner=htmlspecialchars(strip_tags($item['temaBanner']));
        $temaMenu=htmlspecialchars(strip_tags($item['temaMenu']));
	$temaContenido=htmlspecialchars(strip_tags($item['temaContenido']));
	$activoConfiguracionesSistema=htmlspecialchars(strip_tags($item['activoConfiguracionesSistema']));
	$versionRegistro=htmlspecialchars(strip_tags($item['versionRegistro']));
	$regEstado=htmlspecialchars(strip_tags($item['regEstado']));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($item['regVersionUltimaModificacion']));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($item['regUsuarioUltimaModificacion']));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($item['regFormularioUltimaModificacion']));
        $logoFooter=htmlspecialchars(strip_tags($item['logoFooter']));
        $favIcon=htmlspecialchars(strip_tags($item['favIcon']));
        $titlePagina=htmlspecialchars(strip_tags($item['titlePagina']));
	$footerPagina=htmlspecialchars(strip_tags($item['footerPagina']));
        $btnAccion=htmlspecialchars(strip_tags($item['btnAccion']));
	$btnSave=htmlspecialchars(strip_tags($item['btnSave']));
	$btnMenu=htmlspecialchars(strip_tags($item['btnMenu']));
	
    // bind the values
        
        //$campoPK = (int)$campoPK;
	//$stmt->bindValue(':campoPK', $campoPK, PDO::PARAM_INT);
        
	$stmt->bindParam(':configuracionesSistemaID', $configuracionesSistemaID);
	$stmt->bindParam(':logoLogin', $logoLogin);
	$stmt->bindParam(':logoBanner', $logoBanner);
	$stmt->bindParam(':iconoMenu', $iconoMenu);
	$stmt->bindParam(':temaBanner', $temaBanner);
        $stmt->bindParam(':temaMenu', $temaMenu);
        $stmt->bindParam(':temaContenido', $temaContenido);
         $activoConfiguracionesSistema = (int)$activoConfiguracionesSistema;
	$stmt->bindValue(':activoConfiguracionesSistema', $activoConfiguracionesSistema, PDO::PARAM_INT);
        //$stmt->bindParam(':activoConfiguracionesSistema', $activoConfiguracionesSistema);
        $stmt->bindParam(':versionRegistro', $versionRegistro);
        $regEstado = (int)$regEstado;
	$stmt->bindValue(':regEstado', $regEstado, PDO::PARAM_INT);
        $stmt->bindParam(':regVersionUltimaModificacion', $regVersionUltimaModificacion);
        $stmt->bindParam(':regUsuarioUltimaModificacion', $regUsuarioUltimaModificacion);
        $stmt->bindParam(':regFormularioUltimaModificacion', $regFormularioUltimaModificacion);
        $stmt->bindParam(':logoFooter', $logoFooter);
        $stmt->bindParam(':favIcon', $favIcon);
        $stmt->bindParam(':titlePagina', $titlePagina);
        $stmt->bindParam(':footerPagina', $footerPagina);
        $stmt->bindParam(':btnAccion', $btnAccion);
        $stmt->bindParam(':btnSave', $btnSave);
        $stmt->bindParam(':btnMenu', $btnMenu);
        
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
		$this->LogMigracion("ApiConfiguracionesSistema", "INSERTAR Ex1", "Registo ".$Registo.": ".$consulta, $e->getMessage());
		$this->Mensaje = $e->getMessage().$consulta;
		return false;
	}
}
catch (Exception $e){
		$this->LogMigracion("ApiConfiguracionesSistema", "INSERTAR Ex2", "Registo ".$Registo.": ".$consulta, $e->getMessage());
		$this->Mensaje = $e->getMessage().$consulta;
	    return false; 
}

 
    return false;
}



function InsertaRegreso($item){
$Registo= "0";
try{
    $consulta="";
	
	$consulta = 'INSERT INTO ' . $this->NombreTabla . 
			" (configuracionesSistemaID,logoLogin,logoBanner,iconoMenu,temaBanner,temaMenu,temaContenido,activoConfiguracionesSistema,versionRegistro,regEstado,regFechaUltimaModificacion,regUsuarioUltimaModificacion,regFormularioUltimaModificacion,regVersionUltimaModificacion,logoFooter,favIcon,titlePagina,footerPagina,btnAccion,btnSave,btnMenu) VALUES ";
			
		$coma = false;
		$comaText = "";
		$Registo=$item['configuracionesSistemaID'];
		
		
		if($this->ObtenerDatos($item['configuracionesSistemaID'], "CONSULTA_LOCAL")){
			$this->Cambia($item['configuracionesSistemaID'],($item['logoLogin']),($item['logoBanner']),($item['iconoMenu']),$item['temaBanner'],$item['temaMenu'],$item['temaContenido'],$item['activoConfiguracionesSistema'],$item['versionRegistro'],$item['regEstado'],($item['regFechaUltimaModificacion']),$item['regUsuarioUltimaModificacion'],$item['regFormularioUltimaModificacion'],
				$item['regVersionUltimaModificacion'],$item["logoFooter"],$item["favIcon"],$item["titlePagina"],$item["footerPagina"],$item["btnAccion"],$item["btnSave"],$item["btnMenu"]);
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
			$consulta= $consulta . $comaText . "(" . $item["configuracionesSistemaID"] . ",'" . ($item["logoLogin"]) . "','" . ($item["logoBanner"]) . "','" . ($item["iconoMenu"]) . "'
			,'" . $item["temaBanner"] . "','" . $item["temaMenu"]  . "','" . $item["temaContenido"] . "',".$item["activoConfiguracionesSistema"]."," . $item["versionRegistro"] . "," . $item["regEstado"] . "
			,'" . ($item["regFechaUltimaModificacion"]) . "'," . $item["regUsuarioUltimaModificacion"] . "," . ($item["regFormularioUltimaModificacion"]) . "
			," . ($item["regVersionUltimaModificacion"]) . ",'" . ($item["logoFooter"]) . "','" . ($item["favIcon"]) . "','" . $item["titlePagina"] . "','" .$item["footerPagina"]. "'
			,'" . $item["btnAccion"] . "','" . $item["btnSave"] . "','" . $item["btnMenu"] . "')";



		}
	if(!$coma){
		$consulta="select 1";
	}
	
	$this->query=$consulta;
	
    // prepare the query
    $stmt = $this->Conexion->prepare($this->query);
	
    // sanitize
	$configuracionesSistemaID=htmlspecialchars(strip_tags($item['configuracionesSistemaID']));
	$logoLogin=htmlspecialchars(strip_tags($item['logoLogin']));
	$logoBanner=htmlspecialchars(strip_tags($item['logoBanner']));
	$iconoMenu=htmlspecialchars(strip_tags($item['iconoMenu']));
	$temaBanner=htmlspecialchars(strip_tags($item['temaBanner']));
        $temaMenu=htmlspecialchars(strip_tags($item['temaMenu']));
	$temaContenido=htmlspecialchars(strip_tags($item['temaContenido']));
	$activoConfiguracionesSistema=htmlspecialchars(strip_tags($item['activoConfiguracionesSistema']));
	$versionRegistro=htmlspecialchars(strip_tags($item['versionRegistro']));
	$regEstado=htmlspecialchars(strip_tags($item['regEstado']));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($item['regVersionUltimaModificacion']));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($item['regUsuarioUltimaModificacion']));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($item['regFormularioUltimaModificacion']));
        $logoFooter=htmlspecialchars(strip_tags($item['logoFooter']));
        $favIcon=htmlspecialchars(strip_tags($item['favIcon']));
        $titlePagina=htmlspecialchars(strip_tags($item['titlePagina']));
	$footerPagina=htmlspecialchars(strip_tags($item['footerPagina']));
        $btnAccion=htmlspecialchars(strip_tags($item['btnAccion']));
	$btnSave=htmlspecialchars(strip_tags($item['btnSave']));
	$btnMenu=htmlspecialchars(strip_tags($item['btnMenu']));
	
    // bind the values
        
        //$campoPK = (int)$campoPK;
	//$stmt->bindValue(':campoPK', $campoPK, PDO::PARAM_INT);
        
	$stmt->bindParam(':configuracionesSistemaID', $configuracionesSistemaID);
	$stmt->bindParam(':logoLogin', $logoLogin);
	$stmt->bindParam(':logoBanner', $logoBanner);
	$stmt->bindParam(':iconoMenu', $iconoMenu);
	$stmt->bindParam(':temaBanner', $temaBanner);
        $stmt->bindParam(':temaMenu', $temaMenu);
        $stmt->bindParam(':temaContenido', $temaContenido);
         $activoConfiguracionesSistema = (int)$activoConfiguracionesSistema;
	$stmt->bindValue(':activoConfiguracionesSistema', $activoConfiguracionesSistema, PDO::PARAM_INT);
        //$stmt->bindParam(':activoConfiguracionesSistema', $activoConfiguracionesSistema);
        $stmt->bindParam(':versionRegistro', $versionRegistro);
        $regEstado = (int)$regEstado;
	$stmt->bindValue(':regEstado', $regEstado, PDO::PARAM_INT);
        $stmt->bindParam(':regVersionUltimaModificacion', $regVersionUltimaModificacion);
        $stmt->bindParam(':regUsuarioUltimaModificacion', $regUsuarioUltimaModificacion);
        $stmt->bindParam(':regFormularioUltimaModificacion', $regFormularioUltimaModificacion);
        $stmt->bindParam(':logoFooter', $logoFooter);
        $stmt->bindParam(':favIcon', $favIcon);
        $stmt->bindParam(':titlePagina', $titlePagina);
        $stmt->bindParam(':footerPagina', $footerPagina);
        $stmt->bindParam(':btnAccion', $btnAccion);
        $stmt->bindParam(':btnSave', $btnSave);
        $stmt->bindParam(':btnMenu', $btnMenu);
        
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
		$this->LogMigracion("ApiConfiguracionesSistema", "INSERTAR Ex1", "Registo ".$Registo.": ".$consulta, $e->getMessage());
		$this->Mensaje = $e->getMessage().$consulta;
		return false;
	}
}
catch (Exception $e){
		$this->LogMigracion("ApiConfiguracionesSistema", "INSERTAR Ex2", "Registo ".$Registo.": ".$consulta, $e->getMessage());
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


function Cambia($configuracionesSistemaID,$logoLogin,$logoBanner,$iconoMenu,$temaBanner,$temaMenu,$temaContenido,$activoConfiguracionesSistema,$versionRegistro,$regEstado,$regFechaUltimaModificacion,$regUsuarioUltimaModificacion,$regFormularioUltimaModificacion,$regVersionUltimaModificacion,$logoFooter,$favIcon,$titlePagina,$footerPagina,$btnAccion,$btnSave,$btnMenu){
	$Registo= $configuracionesSistemaID;
	
	
	TRY
	{
		$query = "UPDATE " . $this->NombreTabla . " SET logoLogin=:logoLogin,logoBanner=:logoBanner,iconoMenu=:iconoMenu,temaBanner=:temaBanner,temaMenu=:temaMenu,temaContenido=:temaContenido,activoConfiguracionesSistema=:activoConfiguracionesSistema,
		versionRegistro=:versionRegistro,regEstado=:regEstado,regFechaUltimaModificacion=:regFechaUltimaModificacion,regUsuarioUltimaModificacion=:regUsuarioUltimaModificacion,
		regFormularioUltimaModificacion=:regFormularioUltimaModificacion,regVersionUltimaModificacion=:regVersionUltimaModificacion,logoFooter=:logoFooter,favIcon=:favIcon,titlePagina=:titlePagina,footerPagina=:footerPagina,btnAccion=:btnAccion,btnSave=:btnSave,
		btnMenu=:btnMenu 
			WHERE configuracionesSistemaID=:configuracionesSistemaID ";

		// prepare the query
		$stmt = $this->Conexion->prepare($query);
	  
		 // sanitize
                $configuracionesSistemaID=htmlspecialchars(strip_tags($configuracionesSistemaID));
	        $logoLogin=htmlspecialchars(strip_tags($logoLogin));
	        $logoBanner=htmlspecialchars(strip_tags($logoBanner));
	        $iconoMenu=htmlspecialchars(strip_tags($iconoMenu));
	        $temaBanner=htmlspecialchars(strip_tags($temaBanner));
                $temaMenu=htmlspecialchars(strip_tags($temaMenu));
	        $temaContenido=htmlspecialchars(strip_tags($temaContenido));
	        $activoConfiguracionesSistema=htmlspecialchars(strip_tags($activoConfiguracionesSistema));
	        $versionRegistro=htmlspecialchars(strip_tags($versionRegistro));
	        $regEstado=htmlspecialchars(strip_tags($regEstado));
                $regFechaUltimaModificacion=htmlspecialchars(strip_tags($regFechaUltimaModificacion));
	        $regVersionUltimaModificacion=htmlspecialchars(strip_tags($regVersionUltimaModificacion));
	        $regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($regUsuarioUltimaModificacion));
	        $regFormularioUltimaModificacion=htmlspecialchars(strip_tags($regFormularioUltimaModificacion));
                $logoFooter=htmlspecialchars(strip_tags($logoFooter));
                $favIcon=htmlspecialchars(strip_tags($favIcon));
                $titlePagina=htmlspecialchars(strip_tags($titlePagina));
	        $footerPagina=htmlspecialchars(strip_tags($footerPagina));
                $btnAccion=htmlspecialchars(strip_tags($btnAccion));
	        $btnSave=htmlspecialchars(strip_tags($btnSave));
	        $btnMenu=htmlspecialchars(strip_tags($btnMenu));
	
        

                $stmt->bindParam(':configuracionesSistemaID', $configuracionesSistemaID);
	        $stmt->bindParam(':logoLogin', $logoLogin);
	        $stmt->bindParam(':logoBanner', $logoBanner);
	        $stmt->bindParam(':iconoMenu', $iconoMenu);
	        $stmt->bindParam(':temaBanner', $temaBanner);
                $stmt->bindParam(':temaContenido', $temaContenido);
                $stmt->bindParam(':temaMenu', $temaMenu);
                $activoConfiguracionesSistema = (int)$activoConfiguracionesSistema;
	        $stmt->bindValue(':activoConfiguracionesSistema', $activoConfiguracionesSistema, PDO::PARAM_INT);
                //$stmt->bindParam(':activoConfiguracionesSistema', $activoConfiguracionesSistema);
                $stmt->bindParam(':versionRegistro', $versionRegistro);
                $regEstado = (int)$regEstado;
	        $stmt->bindValue(':regEstado', $regEstado, PDO::PARAM_INT);
                //$stmt->bindParam(':regEstado', $regEstado);
                $stmt->bindParam(':regFechaUltimaModificacion', $regFechaUltimaModificacion);
                $stmt->bindParam(':regVersionUltimaModificacion', $regVersionUltimaModificacion);
                $stmt->bindParam(':regUsuarioUltimaModificacion', $regUsuarioUltimaModificacion);
                $stmt->bindParam(':regFormularioUltimaModificacion', $regFormularioUltimaModificacion);
                $stmt->bindParam(':logoFooter', $logoFooter);
                $stmt->bindParam(':favIcon', $favIcon);
                $stmt->bindParam(':titlePagina', $titlePagina);
                $stmt->bindParam(':footerPagina', $footerPagina);
                $stmt->bindParam(':btnAccion', $btnAccion);
                $stmt->bindParam(':btnSave', $btnSave);
                $stmt->bindParam(':btnMenu', $btnMenu);
        
	   
		
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
			$this->Mensaje = $e->getMessage().$consulta;
			return false;
		}
	 
	}
	catch (Exception $e){
		$this->LogMigracion("Api".$this->NombreTabla, "UPDATE Ex2", "Registo ".$Registo.": ".$e->getMessage().$query, $e->getMessage());
		$this->Mensaje = $e->getMessage().$consulta;
		return false;
	}
	 
        return false;
}
	
	
}