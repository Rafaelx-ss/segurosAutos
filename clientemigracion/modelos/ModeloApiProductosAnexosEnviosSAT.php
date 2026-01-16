<?php 
class ProductosAnexosEnviosSAT{ 
 
private $Conexion; 
private $Database; 
private $NombreTabla = 'ProductosAnexosEnviosSAT'; 
 
// object properties
public $Campos;
public $Dataset;
public $Mensaje;
 
// constructor
    public function __construct($dbf){
        $this->Database = $dbf;
        $this->Conexion = $dbf->conn;
    }
	
	function ObtenerDatos($id=0,$establecimientoID=0){
    	// query to check if email exists
    	$query = "select * from " . $this->NombreTabla .  " where 1 " . ($id > 0 ? " and productoAnexoID = ?" : "");
 
    	// prepare the query
    	$stmt = $this->Conexion->prepare( $query );
 
    	
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
			" (productoAnexoID,productoColor,productoEtiqueta,composicionOctanajeDeGasolina,gasolinaConCombustibleNoFosil,composicionDeCombustibleNoFosilEnGasolina,dieselConCombustibleNoFosil,composicionDeCombustibleNoFosilEnDiesel,marcaComercial,marcaje,concentracionSustanciaMarcaje,productoID,claveProductoEnvioSATID,claveSubProductoEnvioSATID,versionRegistro,regEstado,regFechaUltimaModificacion,regUsuarioUltimaModificacion,regFormularioUltimaModificacion,regVersionUltimaModificacion) VALUES ";
			
		$coma = false;
		$comaText = "";
		foreach($registros as $item)
		 {
			//echo $item['textoID']."lolol";
			 if($this->ObtenerDatos($item['productoAnexoID'])){
				$this->Cambia($item['productoAnexoID'],($item['productoColor']),($item['productoEtiqueta']),$item['composicionOctanajeDeGasolina'],($item['gasolinaConCombustibleNoFosil']),($item['composicionDeCombustibleNoFosilEnGasolina']),($item['dieselConCombustibleNoFosil']),($item['composicionDeCombustibleNoFosilEnDiesel']),($item['marcaComercial']),($item['marcaje']),($item['concentracionSustanciaMarcaje']),$item['productoID'],$item['claveProductoEnvioSATID'],$item['claveSubProductoEnvioSATID'],$item['versionRegistro'],$item['regEstado'],($item['regFechaUltimaModificacion']),$item['regUsuarioUltimaModificacion'],$item['regFormularioUltimaModificacion'],$item['regVersionUltimaModificacion']);
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
				
				$consulta= $consulta . $comaText . "(" . $item["productoAnexoID"] . ",'" . ($item["productoColor"]) . "','" . ($item["productoEtiqueta"]) . "'," . $item["composicionOctanajeDeGasolina"] . ",'" . ($item["gasolinaConCombustibleNoFosil"]) . "','" . ($item["composicionDeCombustibleNoFosilEnGasolina"]) . "','" . ($item["dieselConCombustibleNoFosil"]) . "','" . ($item["composicionDeCombustibleNoFosilEnDiesel"]) . "','" . ($item["marcaComercial"]) . "','" . ($item["marcaje"]) . "','" . ($item["concentracionSustanciaMarcaje"]) . "'," . $item["productoID"] . "," . $item["claveProductoEnvioSATID"] . "," . $item["claveSubProductoEnvioSATID"] . "," . $item["versionRegistro"] . "," . $item["regEstado"] . ",'" . ($item["regFechaUltimaModificacion"]) . "'," . $item["regUsuarioUltimaModificacion"] . "," . $item["regFormularioUltimaModificacion"] . "," . $item["regVersionUltimaModificacion"] . ")";
			}
			 
		   
		 }
	if(!$coma){
		$consulta="select 1";
	}
	
	$this->query=$consulta;
	
    // prepare the query
    $stmt = $this->Conexion->prepare($this->query);
	
    // sanitize
	$productoAnexoID=htmlspecialchars(strip_tags($item['productoAnexoID']));
	$productoColor=htmlspecialchars(strip_tags($item['productoColor']));
	$productoEtiqueta=htmlspecialchars(strip_tags($item['productoEtiqueta']));
	$composicionOctanajeDeGasolina=htmlspecialchars(strip_tags($item['composicionOctanajeDeGasolina']));
	$gasolinaConCombustibleNoFosil=htmlspecialchars(strip_tags($item['gasolinaConCombustibleNoFosil']));
	$composicionDeCombustibleNoFosilEnGasolina=htmlspecialchars(strip_tags($item['composicionDeCombustibleNoFosilEnGasolina']));
	$dieselConCombustibleNoFosil=htmlspecialchars(strip_tags($item['dieselConCombustibleNoFosil']));
	$composicionDeCombustibleNoFosilEnDiesel=htmlspecialchars(strip_tags($item['composicionDeCombustibleNoFosilEnDiesel']));
	$marcaComercial=htmlspecialchars(strip_tags($item['marcaComercial']));
	$marcaje=htmlspecialchars(strip_tags($item['marcaje']));
	$concentracionSustanciaMarcaje=htmlspecialchars(strip_tags($item['concentracionSustanciaMarcaje']));
	$productoID=htmlspecialchars(strip_tags($item['productoID']));
	$claveProductoEnvioSATID=htmlspecialchars(strip_tags($item['claveProductoEnvioSATID']));
	$claveSubProductoEnvioSATID=htmlspecialchars(strip_tags($item['claveSubProductoEnvioSATID']));
	$versionRegistro=htmlspecialchars(strip_tags($item['versionRegistro']));
	$regEstado=htmlspecialchars(strip_tags($item['regEstado']));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($item['regFechaUltimaModificacion']));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($item['regUsuarioUltimaModificacion']));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($item['regFormularioUltimaModificacion']));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($item['regVersionUltimaModificacion']));
   
    // bind the values
	$stmt->bindParam(':productoAnexoID', $productoAnexoID);
	$stmt->bindParam(':productoColor', $productoColor);
	$stmt->bindParam(':productoEtiqueta', $productoEtiqueta);
	$stmt->bindParam(':composicionOctanajeDeGasolina', $composicionOctanajeDeGasolina);
	$stmt->bindParam(':gasolinaConCombustibleNoFosil', $gasolinaConCombustibleNoFosil);
	$stmt->bindParam(':composicionDeCombustibleNoFosilEnGasolina', $composicionDeCombustibleNoFosilEnGasolina);
	$stmt->bindParam(':dieselConCombustibleNoFosil', $dieselConCombustibleNoFosil);
	$stmt->bindParam(':composicionDeCombustibleNoFosilEnDiesel', $composicionDeCombustibleNoFosilEnDiesel);
	$stmt->bindParam(':marcaComercial', $marcaComercial);
	$stmt->bindParam(':marcaje', $marcaje);
	$stmt->bindParam(':concentracionSustanciaMarcaje', $concentracionSustanciaMarcaje);
	$stmt->bindParam(':productoID', $productoID);
	$stmt->bindParam(':claveProductoEnvioSATID', $claveProductoEnvioSATID);
	$stmt->bindParam(':claveSubProductoEnvioSATID', $claveSubProductoEnvioSATID);
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


function Cambia($productoAnexoID,$productoColor,$productoEtiqueta,$composicionOctanajeDeGasolina,$gasolinaConCombustibleNoFosil,$composicionDeCombustibleNoFosilEnGasolina,$dieselConCombustibleNoFosil,$composicionDeCombustibleNoFosilEnDiesel,$marcaComercial,$marcaje,$concentracionSustanciaMarcaje,$productoID,$claveProductoEnvioSATID,$claveSubProductoEnvioSATID,$versionRegistro,$regEstado,$regFechaUltimaModificacion,$regUsuarioUltimaModificacion,$regFormularioUltimaModificacion,$regVersionUltimaModificacion){
    $query = "UPDATE " . $this->NombreTabla . " SET productoColor=:productoColor,productoEtiqueta=:productoEtiqueta,composicionOctanajeDeGasolina=:composicionOctanajeDeGasolina,gasolinaConCombustibleNoFosil=:gasolinaConCombustibleNoFosil,composicionDeCombustibleNoFosilEnGasolina=:composicionDeCombustibleNoFosilEnGasolina,dieselConCombustibleNoFosil=:dieselConCombustibleNoFosil,composicionDeCombustibleNoFosilEnDiesel=:composicionDeCombustibleNoFosilEnDiesel,marcaComercial=:marcaComercial,marcaje=:marcaje,concentracionSustanciaMarcaje=:concentracionSustanciaMarcaje,productoID=:productoID,claveProductoEnvioSATID=:claveProductoEnvioSATID,claveSubProductoEnvioSATID=:claveSubProductoEnvioSATID,versionRegistro=:versionRegistro,regEstado=:regEstado,regFechaUltimaModificacion=:regFechaUltimaModificacion,regUsuarioUltimaModificacion=:regUsuarioUltimaModificacion,regFormularioUltimaModificacion=:regFormularioUltimaModificacion,regVersionUltimaModificacion=:regVersionUltimaModificacion 
		WHERE productoAnexoID=:productoAnexoID ";

    // prepare the query
    $stmt = $this->Conexion->prepare($query);
 
     // sanitize
	$productoAnexoID=htmlspecialchars(strip_tags($productoAnexoID));
	$productoColor=htmlspecialchars(strip_tags($productoColor));
	$productoEtiqueta=htmlspecialchars(strip_tags($productoEtiqueta));
	$composicionOctanajeDeGasolina=htmlspecialchars(strip_tags($composicionOctanajeDeGasolina));
	$gasolinaConCombustibleNoFosil=htmlspecialchars(strip_tags($gasolinaConCombustibleNoFosil));
	$composicionDeCombustibleNoFosilEnGasolina=htmlspecialchars(strip_tags($composicionDeCombustibleNoFosilEnGasolina));
	$dieselConCombustibleNoFosil=htmlspecialchars(strip_tags($dieselConCombustibleNoFosil));
	$composicionDeCombustibleNoFosilEnDiesel=htmlspecialchars(strip_tags($composicionDeCombustibleNoFosilEnDiesel));
	$marcaComercial=htmlspecialchars(strip_tags($marcaComercial));
	$marcaje=htmlspecialchars(strip_tags($marcaje));
	$concentracionSustanciaMarcaje=htmlspecialchars(strip_tags($concentracionSustanciaMarcaje));
	$productoID=htmlspecialchars(strip_tags($productoID));
	$claveProductoEnvioSATID=htmlspecialchars(strip_tags($claveProductoEnvioSATID));
	$claveSubProductoEnvioSATID=htmlspecialchars(strip_tags($claveSubProductoEnvioSATID));
	$versionRegistro=htmlspecialchars(strip_tags($versionRegistro));
	$regEstado=htmlspecialchars(strip_tags($regEstado));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($regFechaUltimaModificacion));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($regUsuarioUltimaModificacion));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($regFormularioUltimaModificacion));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($regVersionUltimaModificacion));
   
    // bind the values
	$stmt->bindParam(':productoAnexoID', $productoAnexoID);
	$stmt->bindParam(':productoColor', $productoColor);
	$stmt->bindParam(':productoEtiqueta', $productoEtiqueta);
	$stmt->bindParam(':composicionOctanajeDeGasolina', $composicionOctanajeDeGasolina);
	$stmt->bindParam(':gasolinaConCombustibleNoFosil', $gasolinaConCombustibleNoFosil);
	$stmt->bindParam(':composicionDeCombustibleNoFosilEnGasolina', $composicionDeCombustibleNoFosilEnGasolina);
	$stmt->bindParam(':dieselConCombustibleNoFosil', $dieselConCombustibleNoFosil);
	$stmt->bindParam(':composicionDeCombustibleNoFosilEnDiesel', $composicionDeCombustibleNoFosilEnDiesel);
	$stmt->bindParam(':marcaComercial', $marcaComercial);
	$stmt->bindParam(':marcaje', $marcaje);
	$stmt->bindParam(':concentracionSustanciaMarcaje', $concentracionSustanciaMarcaje);
	$stmt->bindParam(':productoID', $productoID);
	$stmt->bindParam(':claveProductoEnvioSATID', $claveProductoEnvioSATID);
	$stmt->bindParam(':claveSubProductoEnvioSATID', $claveSubProductoEnvioSATID);
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