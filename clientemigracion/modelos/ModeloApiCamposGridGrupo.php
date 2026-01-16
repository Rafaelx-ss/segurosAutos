<?php 
class CamposGridGrupo{ 
 
private $Conexion; 
private $Database; 
private $NombreTabla = 'CamposGrid'; 
 
// object properties
public $Campos;
public $Dataset;
 public $Mensaje;
// constructor
    public function __construct($dbf){
        $this->Database = $dbf;
        $this->Conexion = $dbf->conn;
    }
	
	function ObtenerDatos( $id=0){
   		// $id = intval($id);
    	// query to check if email exists
    	$query = "select * from " . $this->NombreTabla . " WHERE 1 " . ($id > 0 ? " and campoGridID= ?" : "");
 
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
 
 
        	// return true because email exists in the database
        	return true;
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

    $this->Mensaje="";
    $consulta="";
	
	$consulta = 'INSERT INTO ' . $this->NombreTabla . 
			" (campoGridID,nombreCampo,visible,searchVisible,orden,textoID,tipoControl,catalogoID,catalogoReferenciaID,textField,valueField,versionRegistro,regEstado,regFechaUltimaModificacion,regUsuarioUltimaModificacion,regFormularioUltimaModificacion,regVersionUltimaModificacion,valorDefault,controlQuery,searchQuery,queryValor) VALUES ";
			
		$coma = false;
		$comaText = "";
		foreach($registros as $item)
		 {
			 if($this->ObtenerDatos($item['campoGridID'])){
				$this->Cambia($item['campoGridID'],$item['nombreCampo'],$item['visible'],$item['searchVisible'],$item['orden'],$item['textoID'],$item['tipoControl'],$item['catalogoID'],$item['catalogoReferenciaID'],$item['textField'],$item['valueField'],$item['versionRegistro'],$item['regEstado'],$item['regFechaUltimaModificacion'],$item['regUsuarioUltimaModificacion'],$item['regFormularioUltimaModificacion'],$item['regVersionUltimaModificacion'],$item['valorDefault'],$item['controlQuery'],$item['searchQuery'],$item['queryValor']);
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
				
				$consulta= $consulta . $comaText . "('" . $item["campoGridID"] . "','" . $item["nombreCampo"] . "'," . $item["visible"] . "," . $item["searchVisible"] . ",'" . $item["orden"] . "','" . $item["textoID"] . "','" . $item["tipoControl"] . "','" . $item["catalogoID"] . "','" . $item["catalogoReferenciaID"] . "','" . $item["textField"] . "','" . $item["valueField"] . "','" . $item["versionRegistro"] . "'," . $item["regEstado"] . ",'" . $item["regFechaUltimaModificacion"] . "','" . $item["regUsuarioUltimaModificacion"] . "','" . $item["regFormularioUltimaModificacion"] . "','" . $item["regVersionUltimaModificacion"] . "','" . $item["valorDefault"] . "'
					,'" . str_replace("'","\''", $item["controlQuery"]) . "','".str_replace("'","\''", $item["searchQuery"])."','".str_replace("'","\''", $item["queryValor"])."')'";
			}
			 
		   
		 }
	if(!$coma){
		$consulta="select 1";
	}
	
	$this->query=$consulta;
	
    // prepare the query
    $stmt = $this->Conexion->prepare($this->query);
	
    // sanitize
	$campoGridID=htmlspecialchars(strip_tags($item['campoGridID']));
	//$nombreCampo=htmlspecialchars(strip_tags($item['nombreCampo']));
	$nombreCampo=htmlspecialchars($item['nombreCampo'], ENT_QUOTES,'UTF-8',false);
	$visible=htmlspecialchars(strip_tags($item['visible']));
	$searchVisible=htmlspecialchars(strip_tags($item['searchVisible']));
	$orden=htmlspecialchars(strip_tags($item['orden']));
	$textoID=htmlspecialchars(strip_tags($item['textoID']));
	$tipoControl=htmlspecialchars(strip_tags($item['tipoControl']));
	$catalogoID=htmlspecialchars(strip_tags($item['catalogoID']));
	$catalogoReferenciaID=htmlspecialchars(strip_tags($item['catalogoReferenciaID']));
	$textField=htmlspecialchars(strip_tags($item['textField']));
	$valueField=htmlspecialchars(strip_tags($item['valueField']));
	$versionRegistro=htmlspecialchars(strip_tags($item['versionRegistro']));
	$regEstado=htmlspecialchars(strip_tags($item['regEstado']));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($item['regFechaUltimaModificacion']));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($item['regUsuarioUltimaModificacion']));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($item['regFormularioUltimaModificacion']));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($item['regVersionUltimaModificacion']));
	$valorDefault=htmlspecialchars(strip_tags($item['valorDefault']));
	$controlQuery=htmlspecialchars(strip_tags($item['controlQuery']));
   
		// inicio Cambios Carlos Cauich
	$searchQuery=htmlspecialchars(strip_tags($item['searchQuery']));
	$queryValor=htmlspecialchars(strip_tags($item['queryValor']));
    // fin Cambios Carlos Cauich


    // bind the values
	$stmt->bindParam(':campoGridID', $campoGridID);
	$stmt->bindParam(':nombreCampo', $nombreCampo);
	$visible = (int)$visible;
	$stmt->bindValue(':visible', $visible, PDO::PARAM_INT);
	$searchVisible = (int)$searchVisible;
	$stmt->bindValue(':searchVisible', $searchVisible, PDO::PARAM_INT);
	$stmt->bindParam(':orden', $orden);
	$stmt->bindParam(':textoID', $textoID);
	$stmt->bindParam(':tipoControl', $tipoControl);
	$stmt->bindParam(':catalogoID', $catalogoID);
	$stmt->bindParam(':catalogoReferenciaID', $catalogoReferenciaID);
	$stmt->bindParam(':textField', $textField);
	$stmt->bindParam(':valueField', $valueField);
	$stmt->bindParam(':versionRegistro', $versionRegistro);
	$regEstado = (int)$regEstado;
	$stmt->bindValue(':regEstado', $regEstado, PDO::PARAM_INT);
	$stmt->bindParam(':regFechaUltimaModificacion', $regFechaUltimaModificacion);
	$stmt->bindParam(':regUsuarioUltimaModificacion', $regUsuarioUltimaModificacion);
	$stmt->bindParam(':regFormularioUltimaModificacion', $regFormularioUltimaModificacion);
	$stmt->bindParam(':regVersionUltimaModificacion', $regVersionUltimaModificacion);
	$stmt->bindParam(':valorDefault', $valorDefault);
	$stmt->bindParam(':controlQuery', $controlQuery);

	$stmt->bindParam(':searchQuery', $searchQuery);
	$stmt->bindParam(':queryValor', $queryValor);
   
   try{
        // execute the query, also check if query was successful
        if($stmt->execute()){
            return true;

        }
    }   
    catch (Exception $e){
        echo $this->Mensaje .= $e->getMessage() .'<br /> <br />Consulta: <br />'.$consulta;
    }

 
    return false;
}


function InsertaRegreso($item){
$Registo= "0";
try{

    $consulta="";
	
	$consulta = 'INSERT INTO ' . $this->NombreTabla . 
			" (campoGridID,nombreCampo,visible,searchVisible,orden,textoID,tipoControl,catalogoID,catalogoReferenciaID,textField,valueField,versionRegistro,regEstado,regFechaUltimaModificacion,regUsuarioUltimaModificacion,regFormularioUltimaModificacion,regVersionUltimaModificacion,valorDefault,controlQuery,searchQuery,queryValor) VALUES ";
			
		$coma = false;
		$comaText = "";
		$Registo=$item['campoGridID'];
			//echo $item['textoID']."lolol";
			 if($this->ObtenerDatos($item['campoGridID'], "CONSULTA_LOCAL")){
				$this->Cambia($item['campoGridID'],$item['nombreCampo'],$item['visible'],$item['searchVisible'],$item['orden'],$item['textoID'],$item['tipoControl'],$item['catalogoID'],$item['catalogoReferenciaID'],$item['textField'],$item['valueField'],$item['versionRegistro'],$item['regEstado'],$item['regFechaUltimaModificacion'],$item['regUsuarioUltimaModificacion'],$item['regFormularioUltimaModificacion'],$item['regVersionUltimaModificacion'],$item['valorDefault'],$item['controlQuery'],$item['searchQuery'],$item['queryValor']);
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
				
                                 
				$consulta= $consulta . $comaText . "('" . $item["campoGridID"] . "','" . $item["nombreCampo"] . "'," . $item["visible"] . "," . $item["searchVisible"] . ",'" . $item["orden"] . "','" . $item["textoID"] . "','" . $item["tipoControl"] . "','" . $item["catalogoID"] . "','" . $item["catalogoReferenciaID"] . "','" . $item["textField"] . "','" . $item["valueField"] . "','" . $item["versionRegistro"] . "'," . $item["regEstado"] . ",'" . $item["regFechaUltimaModificacion"] . "','" . $item["regUsuarioUltimaModificacion"] . "','" . $item["regFormularioUltimaModificacion"] . "','" . $item["regVersionUltimaModificacion"] . "','" . $item["valorDefault"] . "','" . str_replace("'","\'", $item["controlQuery"]) . "',
					'".str_replace("'","\'", $item["searchQuery"])."','".str_replace("'","\'", $item["queryValor"])."')";
			}
			
	if(!$coma){
		$consulta="select 1";
	}
	
	$this->query=$consulta;
	
    // prepare the query
    $stmt = $this->Conexion->prepare($this->query);
	
    // sanitize
	$campoGridID=htmlspecialchars(strip_tags($item['campoGridID']));
	//$nombreCampo=htmlspecialchars(strip_tags($item['nombreCampo']));
	$nombreCampo=htmlspecialchars($item['nombreCampo'], ENT_QUOTES,'UTF-8',false);
	$visible=htmlspecialchars(strip_tags($item['visible']));
	$searchVisible=htmlspecialchars(strip_tags($item['searchVisible']));
	$orden=htmlspecialchars(strip_tags($item['orden']));
	$textoID=htmlspecialchars(strip_tags($item['textoID']));
	$tipoControl=htmlspecialchars(strip_tags($item['tipoControl']));
	$catalogoID=htmlspecialchars(strip_tags($item['catalogoID']));
	$catalogoReferenciaID=htmlspecialchars(strip_tags($item['catalogoReferenciaID']));
	$textField=htmlspecialchars(strip_tags($item['textField']));
	$valueField=htmlspecialchars(strip_tags($item['valueField']));
	$versionRegistro=htmlspecialchars(strip_tags($item['versionRegistro']));
	$regEstado=htmlspecialchars(strip_tags($item['regEstado']));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($item['regFechaUltimaModificacion']));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($item['regUsuarioUltimaModificacion']));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($item['regFormularioUltimaModificacion']));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($item['regVersionUltimaModificacion']));
	$valorDefault=htmlspecialchars(strip_tags($item['valorDefault']));
	$controlQuery=htmlspecialchars(strip_tags($item['controlQuery']));
   

		// inicio Cambios Carlos Cauich
		$searchQuery=htmlspecialchars(strip_tags($item['searchQuery']));
		$queryValor=htmlspecialchars(strip_tags($item['queryValor']));
    	// fin Cambios Carlos Cauich


    // bind the values
	$stmt->bindParam(':campoGridID', $campoGridID);
	$stmt->bindParam(':nombreCampo', $nombreCampo);
	$visible = (int)$visible;
	$stmt->bindValue(':visible', $visible, PDO::PARAM_INT);
	$searchVisible = (int)$searchVisible;
	$stmt->bindValue(':searchVisible', $searchVisible, PDO::PARAM_INT);
	$stmt->bindParam(':orden', $orden);
	$stmt->bindParam(':textoID', $textoID);
	$stmt->bindParam(':tipoControl', $tipoControl);
	$stmt->bindParam(':catalogoID', $catalogoID);
	$stmt->bindParam(':catalogoReferenciaID', $catalogoReferenciaID);
	$stmt->bindParam(':textField', $textField);
	$stmt->bindParam(':valueField', $valueField);
	$stmt->bindParam(':versionRegistro', $versionRegistro);
	$regEstado = (int)$regEstado;
	$stmt->bindValue(':regEstado', $regEstado, PDO::PARAM_INT);
	$stmt->bindParam(':regFechaUltimaModificacion', $regFechaUltimaModificacion);
	$stmt->bindParam(':regUsuarioUltimaModificacion', $regUsuarioUltimaModificacion);
	$stmt->bindParam(':regFormularioUltimaModificacion', $regFormularioUltimaModificacion);
	$stmt->bindParam(':regVersionUltimaModificacion', $regVersionUltimaModificacion);
	$stmt->bindParam(':valorDefault', $valorDefault);
	$stmt->bindParam(':controlQuery', $controlQuery);
   
		$stmt->bindParam(':searchQuery', $searchQuery);
		$stmt->bindParam(':queryValor', $queryValor);



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
		$this->Mensaje = $e->getMessage().$consulta;
		return false;
	}
}
catch (Exception $e){
		$this->LogMigracion("Api".$this->NombreTabla, "INSERTAR Ex2", "Registo ".$Registo.": ".$consulta, $e->getMessage());
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
		$this->mensaje .= $e->getMessage();
	}
}


function Cambia($campoGridID,$nombreCampo,$visible,$searchVisible,$orden,$textoID,$tipoControl,$catalogoID,$catalogoReferenciaID,$textField,$valueField,$versionRegistro,$regEstado,$regFechaUltimaModificacion,$regUsuarioUltimaModificacion,$regFormularioUltimaModificacion,$regVersionUltimaModificacion,$valorDefault,$controlQuery,$searchQuery,$queryValor){
    $Registo= $campoGridID;
	try
	{      $this->Mensaje="";
		//$query = "UPDATE " . $this->NombreTabla . " SET nombreCampo=:nombreCampo,visible=:visible,searchVisible=:searchVisible,orden=:orden,textoID=:textoID,tipoControl=:tipoControl,catalogoID=:catalogoID,catalogoReferenciaID=:catalogoReferenciaID,textField=:textField,valueField=:valueField,versionRegistro=:versionRegistro,regEstado=:regEstado,regFechaUltimaModificacion=:regFechaUltimaModificacion,regUsuarioUltimaModificacion=:regUsuarioUltimaModificacion,regFormularioUltimaModificacion=:regFormularioUltimaModificacion,
		//regVersionUltimaModificacion=:regVersionUltimaModificacion,valorDefault=:valorDefault,controlQuery=:controlQuery,searchQuery=:searchQuery,queryValor=:queryValor 
		//	WHERE campoGridID=:campoGridID ";
                
                $query = "UPDATE " . $this->NombreTabla . " SET nombreCampo=:nombreCampo,visible=:visible,searchVisible=:searchVisible,orden=:orden,textoID=:textoID,tipoControl=:tipoControl,catalogoID=:catalogoID,catalogoReferenciaID=:catalogoReferenciaID,textField=:textField,valueField=:valueField,versionRegistro=:versionRegistro,regEstado=:regEstado,regFechaUltimaModificacion=:regFechaUltimaModificacion,regUsuarioUltimaModificacion=:regUsuarioUltimaModificacion,regFormularioUltimaModificacion=:regFormularioUltimaModificacion,
		regVersionUltimaModificacion=:regVersionUltimaModificacion,valorDefault=:valorDefault,controlQuery=:controlQuery,searchQuery=:searchQuery,queryValor=:queryValor
                	WHERE campoGridID=:campoGridID ";
                
		//ECHO $campoGridID.'-'.$nombreCampo.'-'.$visible.'-'.$searchVisible.'-'.$orden.'-'.$textoID.'-'.$tipoControl.'-'.$catalogoID.'-'.$catalogoReferenciaID.'-'.$textField.'-'.$valueField.'-'.$versionRegistro.'-'.$regEstado.'-'.$regFechaUltimaModificacion.'-'.$regUsuarioUltimaModificacion.'-'.$regFormularioUltimaModificacion.'-'.$regVersionUltimaModificacion.'-'.$valorDefault.'-'.$controlQuery;
		// prepare the query
		$stmt = $this->Conexion->prepare($query);
	 
		 // sanitize
		$campoGridID=htmlspecialchars(strip_tags($campoGridID));
		//$nombreCampo=htmlspecialchars(strip_tags($nombreCampo));
		$nombreCampo=htmlspecialchars($nombreCampo, ENT_QUOTES,'UTF-8',false);
		$visible=htmlspecialchars(strip_tags($visible));
		$searchVisible=htmlspecialchars(strip_tags($searchVisible));
		$orden=htmlspecialchars(strip_tags($orden));
		$textoID=htmlspecialchars(strip_tags($textoID));
		$tipoControl=htmlspecialchars(strip_tags($tipoControl));
		$catalogoID=htmlspecialchars(strip_tags($catalogoID));
		$catalogoReferenciaID=htmlspecialchars(strip_tags($catalogoReferenciaID));
		$textField=htmlspecialchars(strip_tags($textField));
		$valueField=htmlspecialchars(strip_tags($valueField));
		$versionRegistro=htmlspecialchars(strip_tags($versionRegistro));
		$regEstado=htmlspecialchars(strip_tags($regEstado));
		$regFechaUltimaModificacion=htmlspecialchars(strip_tags($regFechaUltimaModificacion));
		$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($regUsuarioUltimaModificacion));
		$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($regFormularioUltimaModificacion));
		$regVersionUltimaModificacion=htmlspecialchars(strip_tags($regVersionUltimaModificacion));
		$valorDefault=htmlspecialchars(strip_tags($valorDefault));
		$controlQuery=htmlspecialchars(strip_tags($controlQuery));
		// inicio Cambios Carlos Cauich
		$searchQuery=htmlspecialchars(strip_tags($searchQuery));
		$queryValor=htmlspecialchars(strip_tags($queryValor));
    	// fin Cambios Carlos Cauich



		// bind the values
		$stmt->bindParam(':campoGridID', $campoGridID);
		$stmt->bindParam(':nombreCampo', $nombreCampo);
		$visible = (int)$visible;
		$stmt->bindValue(':visible', $visible, PDO::PARAM_INT);
		$searchVisible = (int)$searchVisible;
		$stmt->bindValue(':searchVisible', $searchVisible, PDO::PARAM_INT);
		$stmt->bindParam(':orden', $orden);
		$stmt->bindParam(':textoID', $textoID);
		$stmt->bindParam(':tipoControl', $tipoControl);
		$stmt->bindParam(':catalogoID', $catalogoID);
		$stmt->bindParam(':catalogoReferenciaID', $catalogoReferenciaID);
		$stmt->bindParam(':textField', $textField);
		$stmt->bindParam(':valueField', $valueField);
		$stmt->bindParam(':versionRegistro', $versionRegistro);
		$regEstado = (int)$regEstado;
		$stmt->bindValue(':regEstado', $regEstado, PDO::PARAM_INT);
		$stmt->bindParam(':regFechaUltimaModificacion', $regFechaUltimaModificacion);
		$stmt->bindParam(':regUsuarioUltimaModificacion', $regUsuarioUltimaModificacion);
		$stmt->bindParam(':regFormularioUltimaModificacion', $regFormularioUltimaModificacion);
		$stmt->bindParam(':regVersionUltimaModificacion', $regVersionUltimaModificacion);
		$stmt->bindParam(':valorDefault', $valorDefault);
		$stmt->bindParam(':controlQuery', $controlQuery);

		$stmt->bindParam(':searchQuery', $searchQuery);
		$stmt->bindParam(':queryValor', $queryValor);



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