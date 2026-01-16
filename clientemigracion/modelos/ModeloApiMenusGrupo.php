<?php 
class MenusGrupo{ 
 
private $Conexion; 
private $Database; 
private $NombreTabla = 'Menus'; 
 
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
    	$query = "select * from " . $this->NombreTabla . " " . ($id > 0 ? " where menuID = ?" : "");
 
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
			" (menuID,nombreMenu,urlPagina,imagen,menuPadre,orden,textoID,versionRegistro,regEstado,regFechaUltimaModificacion,regUsuarioUltimaModificacion,regFormularioUltimaModificacion,regVersionUltimaModificacion) VALUES ";
			
		$coma = false;
		$comaText = "";
		foreach($registros as $item)
		 {
			//echo $item['textoID']."lolol";
			 if($this->ObtenerDatos($item['menuID'])){
				$this->Cambia($item['menuID'],$item['nombreMenu'],$item['urlPagina'],$item['imagen'],$item['menuPadre'],$item['orden'],$item['textoID'],$item['versionRegistro'],$item['regEstado'],$item['regFechaUltimaModificacion'],$item['regUsuarioUltimaModificacion'],$item['regFormularioUltimaModificacion'],$item['regVersionUltimaModificacion']);
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
				
				$consulta= $consulta . $comaText . "('" . $item["menuID"] . "','" . utf8_decode($item["nombreMenu"]) . "','" . $item["urlPagina"] . "','" . $item["imagen"] . "','" . $item["menuPadre"] . "','" . $item["orden"] . "','" . $item["textoID"] . "','" . $item["versionRegistro"] . "','" . $item["regEstado"] . "','" . $item["regFechaUltimaModificacion"] . "','" . $item["regUsuarioUltimaModificacion"] . "','" . $item["regFormularioUltimaModificacion"] . "','" . $item["regVersionUltimaModificacion"] . "')";
			}
			 
		   
		 }
	if(!$coma){
		$consulta="select 1";
	}
	
	$this->query=$consulta;
	
    // prepare the query
    $stmt = $this->Conexion->prepare($this->query);
	
    // sanitize
	$menuID=htmlspecialchars(strip_tags($item['menuID']));
	//$nombreMenu=htmlspecialchars(strip_tags($item['nombreMenu']));
	$nombreMenu=htmlspecialchars($item['nombreMenu'], ENT_QUOTES,'UTF-8',false);
	$urlPagina=htmlspecialchars(strip_tags($item['urlPagina']));
	$imagen=htmlspecialchars(strip_tags($item['imagen']));
	$menuPadre=htmlspecialchars(strip_tags($item['menuPadre']));
	$orden=htmlspecialchars(strip_tags($item['orden']));
	$textoID=htmlspecialchars(strip_tags($item['textoID']));
	$versionRegistro=htmlspecialchars(strip_tags($item['versionRegistro']));
	$regEstado=htmlspecialchars(strip_tags($item['regEstado']));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($item['regFechaUltimaModificacion']));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($item['regUsuarioUltimaModificacion']));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($item['regFormularioUltimaModificacion']));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($item['regVersionUltimaModificacion']));
   
    // bind the values
	$stmt->bindParam(':menuID', $menuID);
	$stmt->bindParam(':nombreMenu', $nombreMenu);
	$stmt->bindParam(':urlPagina', $urlPagina);
	$stmt->bindParam(':imagen', $imagen);
	$stmt->bindParam(':menuPadre', $menuPadre);
	$stmt->bindParam(':orden', $orden);
	$stmt->bindParam(':textoID', $textoID);
	$stmt->bindParam(':versionRegistro', $versionRegistro);
	$stmt->bindParam(':regEstado', $regEstado);
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
        echo $this->Mensaje = $e->getMessage().$consulta;
    }

  
    // execute the query, also check if query was successful
    if($stmt->execute()){
        return true;

    }
 
    return false;
}


function Cambia($menuID,$nombreMenu,$urlPagina,$imagen,$menuPadre,$orden,$textoID,$versionRegistro,$regEstado,$regFechaUltimaModificacion,$regUsuarioUltimaModificacion,$regFormularioUltimaModificacion,$regVersionUltimaModificacion){
    $query = "UPDATE " . $this->NombreTabla . " SET nombreMenu=:nombreMenu,urlPagina=:urlPagina,imagen=:imagen,menuPadre=:menuPadre,orden=:orden,textoID=:textoID,versionRegistro=:versionRegistro,regEstado=:regEstado,regFechaUltimaModificacion=:regFechaUltimaModificacion,regUsuarioUltimaModificacion=:regUsuarioUltimaModificacion,regFormularioUltimaModificacion=:regFormularioUltimaModificacion,regVersionUltimaModificacion=:regVersionUltimaModificacion 
		WHERE menuID=:menuID ";

    // prepare the query
    $stmt = $this->Conexion->prepare($query);
 
     // sanitize
	$menuID=htmlspecialchars(strip_tags($menuID));
	//$nombreMenu=htmlspecialchars(strip_tags($nombreMenu));
	$nombreMenu=htmlspecialchars($nombreMenu, ENT_QUOTES,'UTF-8',false);
	$urlPagina=htmlspecialchars(strip_tags($urlPagina));
	$imagen=htmlspecialchars(strip_tags($imagen));
	$menuPadre=htmlspecialchars(strip_tags($menuPadre));
	$orden=htmlspecialchars(strip_tags($orden));
	$textoID=htmlspecialchars(strip_tags($textoID));
	$versionRegistro=htmlspecialchars(strip_tags($versionRegistro));
	$regEstado=htmlspecialchars(strip_tags($regEstado));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($regFechaUltimaModificacion));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($regUsuarioUltimaModificacion));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($regFormularioUltimaModificacion));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($regVersionUltimaModificacion));
   
    // bind the values
	$stmt->bindParam(':menuID', $menuID);
	$stmt->bindParam(':nombreMenu', $nombreMenu);
	$stmt->bindParam(':urlPagina', $urlPagina);
	$stmt->bindParam(':imagen', $imagen);
	$stmt->bindParam(':menuPadre', $menuPadre);
	$stmt->bindParam(':orden', $orden);
	$stmt->bindParam(':textoID', $textoID);
	$stmt->bindParam(':versionRegistro', $versionRegistro);
	$stmt->bindParam(':regEstado', $regEstado);
	$stmt->bindParam(':regFechaUltimaModificacion', $regFechaUltimaModificacion);
	$stmt->bindParam(':regUsuarioUltimaModificacion', $regUsuarioUltimaModificacion);
	$stmt->bindParam(':regFormularioUltimaModificacion', $regFormularioUltimaModificacion);
	$stmt->bindParam(':regVersionUltimaModificacion', $regVersionUltimaModificacion);

    // execute the query, also check if query was successful
    if($stmt->execute()){
        return true;

    }
 
    return false;
}
	
		
}
	?>