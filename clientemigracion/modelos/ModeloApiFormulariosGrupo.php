<?php 
class FormulariosGrupo{ 
 
private $Conexion; 
private $Database; 
private $NombreTabla = 'Formularios'; 
 
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
    	$query = "select * from " . $this->NombreTabla . " " . ($id > 0 ? " where formularioID = ?" : "");
		//echo $id;
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

    $consulta="";
	
	$consulta = 'INSERT INTO ' . $this->NombreTabla . 
			" (formularioID,tipoMenu,formID,nombreFormulario,urlArchivo,estadoFormulario,orden,icono,menuID,aplicacionID,catalogoID,textoID,tipoFormularioID,versionRegistro,regEstado,regFechaUltimaModificacion,regUsuarioUltimaModificacion,regFormularioUltimaModificacion,regVersionUltimaModificacion) VALUES ";
			
		$coma = false;
		$comaText = "";
		foreach($registros as $item)
		 {
			//echo "<br /> ".$item['formularioID']." TipoMenu: -".$item['tipoMenu']."- formID: ".$item['formID']."-";
			 if($this->ObtenerDatos($item['formularioID'])){
				$this->Cambia($item['formularioID'],$item['tipoMenu'],$item['formID'],$item['nombreFormulario'],$item['urlArchivo'],$item['estadoFormulario'],$item['orden'],$item['icono'],$item['menuID'],$item['aplicacionID'],$item['catalogoID'],$item['textoID'],$item['tipoFormularioID'],$item['versionRegistro'],$item['regEstado'],$item['regFechaUltimaModificacion'],$item['regUsuarioUltimaModificacion'],$item['regFormularioUltimaModificacion'],$item['regVersionUltimaModificacion']);
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
				
				$CampoNull=$item["catalogoID"];
				if($item["catalogoID"]=" " or trim($item["catalogoID"]) != ""){
					$CampoNull="NULL";
				}
				ELSE{
					$CampoNull="'".$item["catalogoID"]."'";
				}
				
				$CampoNullFormID=$item["formID"];
				if($item["formID"]==" " or trim($item["formID"]) == ""){
					$CampoNullFormID="NULL";
				}
				ELSE{
					$CampoNullFormID="'".$item["formID"]."'";
				}
				
				$CampoNulltipoMenu=$item["tipoMenu"];
				if($CampoNulltipoMenu!=" " and trim($CampoNulltipoMenu) != ""){
					$CampoNulltipoMenu="'".$CampoNulltipoMenu."'";
				}
				ELSE{
					$CampoNulltipoMenu="NULL";
				}
					
				$consulta= $consulta . $comaText . "('" . $item["formularioID"] . "'," . $CampoNulltipoMenu . "," . $CampoNullFormID . ",'" . $item["nombreFormulario"] . "','" . $item["urlArchivo"] . "'," . $item["estadoFormulario"] . ",'" . $item["orden"] . "','" . $item["icono"] . "','" . $item["menuID"] . "','" . $item["aplicacionID"] . "',
				" . $CampoNull . ",'" . $item["textoID"] . "','" . $item["tipoFormularioID"] . "','" . $item["versionRegistro"] . "'," . $item["regEstado"] . ",'" . $item["regFechaUltimaModificacion"] . "','" . $item["regUsuarioUltimaModificacion"] . "','" . $item["regFormularioUltimaModificacion"] . "','" . $item["regVersionUltimaModificacion"] . "')";
				
				//echo $item["formularioID"] ."...".$item["tipoMenu"]."-".$CampoNulltipoMenu;
			
			}
			 
		 }
	if(!$coma){
		$consulta="select 1";
	}
	
	$this->query=$consulta;
	
    // prepare the query
    $stmt = $this->Conexion->prepare($this->query);
	
    // sanitize
	$formularioID=htmlspecialchars(strip_tags($item['formularioID']));
	if($item['tipoMenu'] != " " and trim($item['tipoMenu']) != ""){
		$tipoMenu=htmlspecialchars(strip_tags($item['tipoMenu']));
	}
	if($item['formID'] != " " and trim($item['formID']) != ""){
		$formID=htmlspecialchars(strip_tags($item['formID']));
	}
	//$nombreFormulario=htmlspecialchars(strip_tags($item['nombreFormulario']));
	$nombreFormulario=htmlspecialchars($item['nombreFormulario'], ENT_QUOTES,'UTF-8',false);
	$urlArchivo=htmlspecialchars(strip_tags($item['urlArchivo']));
	$estadoFormulario=htmlspecialchars(strip_tags($item['estadoFormulario']));
	$orden=htmlspecialchars(strip_tags($item['orden']));
	$icono=htmlspecialchars(strip_tags($item['icono']));
	$menuID=htmlspecialchars(strip_tags($item['menuID']));
	$aplicacionID=htmlspecialchars(strip_tags($item['aplicacionID']));
	$catalogoID=htmlspecialchars(strip_tags($item['catalogoID']));
	$textoID=htmlspecialchars(strip_tags($item['textoID']));
	$tipoFormularioID=htmlspecialchars(strip_tags($item['tipoFormularioID']));
	$versionRegistro=htmlspecialchars(strip_tags($item['versionRegistro']));
	$regEstado=htmlspecialchars(strip_tags($item['regEstado']));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($item['regFechaUltimaModificacion']));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($item['regUsuarioUltimaModificacion']));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($item['regFormularioUltimaModificacion']));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($item['regVersionUltimaModificacion']));
   
    // bind the values
	$stmt->bindParam(':formularioID', $formularioID);
	if($item['tipoMenu'] != " " and trim($item['tipoMenu']) != ""){
		$stmt->bindParam(':tipoMenu', $tipoMenu);
	}
	if($item['formID'] != " " and trim($item['formID']) != ""){
		$stmt->bindParam(':formID', $formID);
	}
	$stmt->bindParam(':nombreFormulario', $nombreFormulario);
	$stmt->bindParam(':urlArchivo', $urlArchivo);
	$estadoFormulario = (int)$estadoFormulario;
	$stmt->bindValue(':estadoFormulario', $estadoFormulario, PDO::PARAM_INT);
	$stmt->bindParam(':orden', $orden);
	$stmt->bindParam(':icono', $icono);
	$stmt->bindParam(':menuID', $menuID);
	$stmt->bindParam(':aplicacionID', $aplicacionID);
	$stmt->bindParam(':catalogoID', $catalogoID);
	$stmt->bindParam(':textoID', $textoID);
	$stmt->bindParam(':tipoFormularioID', $tipoFormularioID);
	$stmt->bindParam(':versionRegistro', $versionRegistro);
	$regEstado = (int)$regEstado;
	$stmt->bindValue(':regEstado', $regEstado, PDO::PARAM_INT);
	$stmt->bindParam(':regFechaUltimaModificacion', $regFechaUltimaModificacion);
	$stmt->bindParam(':regUsuarioUltimaModificacion', $regUsuarioUltimaModificacion);
	$stmt->bindParam(':regFormularioUltimaModificacion', $regFormularioUltimaModificacion);
	$stmt->bindParam(':regVersionUltimaModificacion', $regVersionUltimaModificacion);
   //echo $consulta;
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


function Cambia($formularioID,$tipoMenu,$formID,$nombreFormulario,$urlArchivo,$estadoFormulario,$orden,$icono,$menuID,$aplicacionID,$catalogoID,$textoID,$tipoFormularioID,$versionRegistro,$regEstado,$regFechaUltimaModificacion,$regUsuarioUltimaModificacion,$regFormularioUltimaModificacion,$regVersionUltimaModificacion){
    $valor="--".$catalogoID."--";
	if($catalogoID != " " and trim($catalogoID) != ""){
		$CampoNull=",catalogoID=:catalogoID";
	}
	else {
		$CampoNull="";
	}
	if($tipoMenu != " " and trim($tipoMenu) != ""){
		$CampoNullTipoMenu="tipoMenu=:tipoMenu,";
	}
	else {
		$CampoNullTipoMenu="";
	}
	if($formID != " " and trim($formID) != ""){
		$CampoNullID="formID=:formID,";
	}
	else {
		$CampoNullID="";
	}
	$query = "UPDATE " . $this->NombreTabla . " SET ".$CampoNullTipoMenu.$CampoNullID."nombreFormulario=:nombreFormulario,urlArchivo='".$urlArchivo."',estadoFormulario=:estadoFormulario,orden=:orden,icono=:icono,menuID=:menuID,aplicacionID=:aplicacionID".$CampoNull.",textoID=:textoID,tipoFormularioID=:tipoFormularioID,versionRegistro=:versionRegistro,regEstado=:regEstado,regFechaUltimaModificacion=:regFechaUltimaModificacion,regUsuarioUltimaModificacion=:regUsuarioUltimaModificacion,regFormularioUltimaModificacion=:regFormularioUltimaModificacion,regVersionUltimaModificacion=:regVersionUltimaModificacion 
		WHERE formularioID=:formularioID ";

    // prepare the query
    $stmt = $this->Conexion->prepare($query);
 
     // sanitize
	$formularioID=htmlspecialchars(strip_tags($formularioID));
	//$tipoMenu=htmlspecialchars(strip_tags($tipoMenu));
	if($tipoMenu != " " and trim($tipoMenu) != ""){
		$tipoMenu=htmlspecialchars(strip_tags($tipoMenu));
	}
	if($formID != " " and trim($formID) != ""){
		$formID=htmlspecialchars(strip_tags($formID));
	}
	//$nombreFormulario=htmlspecialchars(strip_tags($nombreFormulario));
	$nombreFormulario=htmlspecialchars($nombreFormulario, ENT_QUOTES,'UTF-8',false);
	//$urlArchivo=htmlspecialchars(strip_tags($urlArchivo));
	$estadoFormulario=htmlspecialchars(strip_tags($estadoFormulario));
	$orden=htmlspecialchars(strip_tags($orden));
	$icono=htmlspecialchars(strip_tags($icono));
	$menuID=htmlspecialchars(strip_tags($menuID));
	$aplicacionID=htmlspecialchars(strip_tags($aplicacionID));
	if($catalogoID != " " and trim($catalogoID) != ""){
		$catalogoID=htmlspecialchars(strip_tags($catalogoID));
	}
	$textoID=htmlspecialchars(strip_tags($textoID));
	$tipoFormularioID=htmlspecialchars(strip_tags($tipoFormularioID));
	$versionRegistro=htmlspecialchars(strip_tags($versionRegistro));
	$regEstado=htmlspecialchars(strip_tags($regEstado));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($regFechaUltimaModificacion));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($regUsuarioUltimaModificacion));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($regFormularioUltimaModificacion));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($regVersionUltimaModificacion));
   
    // bind the values
	$stmt->bindParam(':formularioID', $formularioID);
	//$stmt->bindParam(':tipoMenu', $tipoMenu);
	if($tipoMenu != " " and trim($tipoMenu) != ""){
		$stmt->bindParam(':tipoMenu', $tipoMenu);
	}
	if($formID != " " and trim($formID) != ""){
		$stmt->bindParam(':formID', $formID);
	}
	$stmt->bindParam(':nombreFormulario', $nombreFormulario);
	//$stmt->bindParam(':urlArchivo', $urlArchivo);
	$estadoFormulario = (int)$estadoFormulario;
	$stmt->bindValue(':estadoFormulario', $estadoFormulario, PDO::PARAM_INT);
	$stmt->bindParam(':orden', $orden);
	$stmt->bindParam(':icono', $icono);
	$stmt->bindParam(':menuID', $menuID);
	$stmt->bindParam(':aplicacionID', $aplicacionID);
	if($catalogoID != " " and trim($catalogoID) != ""){
		$stmt->bindParam(':catalogoID', $catalogoID);
	}
	$stmt->bindParam(':textoID', $textoID);
	$stmt->bindParam(':tipoFormularioID', $tipoFormularioID);
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
        echo $this->Mensaje = $e->getMessage().'<br /> <br />Consulta: <br />'.$query;
    }
 
    return true;
}
	
		
}
	?>