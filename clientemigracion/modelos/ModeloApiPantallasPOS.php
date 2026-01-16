<?php 
class PantallasPOS{ 
 
private $Conexion; 
private $Database; 
private $NombreTabla = 'PantallasPOS'; 
 
// object properties
public $Campos;
public $Dataset;
public $Mensaje;
 
// constructor
    public function __construct($dbf){
        $this->Database = $dbf;
        $this->Conexion = $dbf->conn;
    }
	
	function ObtenerDatos($id=0){
   		// $id = intval($id);
    	// query to check if email exists
    	$query = "select * from " . $this->NombreTabla . " " . ($id > 0 ? " where PantallaPosID = ?" : "");
 
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
			" (PantallaPosID,ordenPantalla,objetoPantalla,activoPantalla,imagenObjetoPantalla,textoID,menuPOSID,versionRegistro,regEstado,regFechaUltimaModificacion,regUsuarioUltimaModificacion,regFormularioUltimaModificacion,regVersionUltimaModificacion) VALUES ";
			
		$coma = false;
		$comaText = "";
		foreach($registros as $item)
		 {
			//echo $item['textoID']."lolol";
			 if($this->ObtenerDatos($item['PantallaPosID'])){
				$this->Cambia($item['PantallaPosID'],$item['ordenPantalla'],utf8_decode($item['objetoPantalla']),$item['activoPantalla'],utf8_decode($item['imagenObjetoPantalla']),$item['textoID'],$item['menuPOSID'],$item['versionRegistro'],$item['regEstado'],utf8_decode($item['regFechaUltimaModificacion']),$item['regUsuarioUltimaModificacion'],$item['regFormularioUltimaModificacion'],$item['regVersionUltimaModificacion']);
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
				
				$consulta= $consulta . $comaText . "(" . $item["PantallaPosID"] . "," . $item["ordenPantalla"] . ",'" . utf8_decode($item["objetoPantalla"]) . "'," . $item["activoPantalla"] . ",'" . utf8_decode($item["imagenObjetoPantalla"]) . "'," . $item["textoID"] . "," . $item["menuPOSID"] . "," . $item["versionRegistro"] . "," . $item["regEstado"] . ",'" . utf8_decode($item["regFechaUltimaModificacion"]) . "'," . $item["regUsuarioUltimaModificacion"] . "," . $item["regFormularioUltimaModificacion"] . "," . $item["regVersionUltimaModificacion"] . ")";
			}
			 
		   
		 }
	if(!$coma){
		$consulta="select 1";
	}
	
	$this->query=$consulta;
	
    // prepare the query
    $stmt = $this->Conexion->prepare($this->query);
	
    // sanitize
	$PantallaPosID=htmlspecialchars(strip_tags($item['PantallaPosID']));
	$ordenPantalla=htmlspecialchars(strip_tags($item['ordenPantalla']));
	$objetoPantalla=htmlspecialchars(strip_tags($item['objetoPantalla']));
	$activoPantalla=htmlspecialchars(strip_tags($item['activoPantalla']));
	$imagenObjetoPantalla=htmlspecialchars(strip_tags($item['imagenObjetoPantalla']));
	$textoID=htmlspecialchars(strip_tags($item['textoID']));
	$menuPOSID=htmlspecialchars(strip_tags($item['menuPOSID']));
	$versionRegistro=htmlspecialchars(strip_tags($item['versionRegistro']));
	$regEstado=htmlspecialchars(strip_tags($item['regEstado']));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($item['regFechaUltimaModificacion']));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($item['regUsuarioUltimaModificacion']));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($item['regFormularioUltimaModificacion']));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($item['regVersionUltimaModificacion']));
   
    // bind the values
	$stmt->bindParam(':PantallaPosID', $PantallaPosID);
	$stmt->bindParam(':ordenPantalla', $ordenPantalla);
	$stmt->bindParam(':objetoPantalla', $objetoPantalla);
	$activoPantalla = (int)$activoPantalla;
	$stmt->bindValue(':activoPantalla', $activoPantalla, PDO::PARAM_INT);
	$stmt->bindParam(':imagenObjetoPantalla', $imagenObjetoPantalla);
	$stmt->bindParam(':textoID', $textoID);
	$stmt->bindParam(':menuPOSID', $menuPOSID);
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
        echo $this->Mensaje = $e->getMessage().'<br /> <br />Consulta: <br />'.$consulta;
    }

 
    return false;
}


function Cambia($PantallaPosID,$ordenPantalla,$objetoPantalla,$activoPantalla,$imagenObjetoPantalla,$textoID,$menuPOSID,$versionRegistro,$regEstado,$regFechaUltimaModificacion,$regUsuarioUltimaModificacion,$regFormularioUltimaModificacion,$regVersionUltimaModificacion){
    $query = "UPDATE " . $this->NombreTabla . " SET ordenPantalla=:ordenPantalla,objetoPantalla=:objetoPantalla,activoPantalla=:activoPantalla,imagenObjetoPantalla=:imagenObjetoPantalla,textoID=:textoID,menuPOSID=:menuPOSID,versionRegistro=:versionRegistro,regEstado=:regEstado,regFechaUltimaModificacion=:regFechaUltimaModificacion,regUsuarioUltimaModificacion=:regUsuarioUltimaModificacion,regFormularioUltimaModificacion=:regFormularioUltimaModificacion,regVersionUltimaModificacion=:regVersionUltimaModificacion 
		WHERE PantallaPosID=:PantallaPosID ";

    // prepare the query
    $stmt = $this->Conexion->prepare($query);
 
     // sanitize
	$PantallaPosID=htmlspecialchars(strip_tags($PantallaPosID));
	$ordenPantalla=htmlspecialchars(strip_tags($ordenPantalla));
	$objetoPantalla=htmlspecialchars(strip_tags($objetoPantalla));
	$activoPantalla=htmlspecialchars(strip_tags($activoPantalla));
	$imagenObjetoPantalla=htmlspecialchars(strip_tags($imagenObjetoPantalla));
	$textoID=htmlspecialchars(strip_tags($textoID));
	$menuPOSID=htmlspecialchars(strip_tags($menuPOSID));
	$versionRegistro=htmlspecialchars(strip_tags($versionRegistro));
	$regEstado=htmlspecialchars(strip_tags($regEstado));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($regFechaUltimaModificacion));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($regUsuarioUltimaModificacion));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($regFormularioUltimaModificacion));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($regVersionUltimaModificacion));
   
    // bind the values
	$stmt->bindParam(':PantallaPosID', $PantallaPosID);
	$stmt->bindParam(':ordenPantalla', $ordenPantalla);
	$stmt->bindParam(':objetoPantalla', $objetoPantalla);
	$activoPantalla = (int)$activoPantalla;
	$stmt->bindValue(':activoPantalla', $activoPantalla, PDO::PARAM_INT);
	$stmt->bindParam(':imagenObjetoPantalla', $imagenObjetoPantalla);
	$stmt->bindParam(':textoID', $textoID);
	$stmt->bindParam(':menuPOSID', $menuPOSID);
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
        echo $this->Mensaje = $e->getMessage();//.'<br /> <br />Consulta: <br />'.$consulta;
    }
 
    return false;
}
	
		
}
	?>