<?php 
class ClavesSubProductosEnviosSAT{ 
 
private $Conexion; 
private $Database; 
private $NombreTabla = 'ClavesSubProductosEnviosSAT'; 
 
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
    	$query = "select * from " . $this->NombreTabla . " " . ($id > 0 ? " where claveSubProductoEnvioSATID = ?" : "");
 
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
			" (claveSubProductoEnvioSATID,claveSubProductoEnvioSAT,descripcionClaveSubProductoEnvioSAT,composicionOctanajeDeGasolina,gasolinaConCombustibleNoFosil,composicionDeCombustibleNoFosilEnGasolina,dieselConCombustibleNoFosil,composicionDeCombustibleNoFosilEnDiesel,marcaComercial,marcaje,concentracionSustanciaMarcaje,versionRegistro,regEstado,regFechaUltimaModificacion,regUsuarioUltimaModificacion,regFormularioUltimaModificacion,regVersionUltimaModificacion) VALUES ";
			
		$coma = false;
		$comaText = "";
		foreach($registros as $item)
		 {
			//echo $item['textoID']."lolol";
			 if($this->ObtenerDatos($item['claveSubProductoEnvioSATID'])){
				$this->Cambia($item['claveSubProductoEnvioSATID'],$item['claveSubProductoEnvioSAT'],$item['descripcionClaveSubProductoEnvioSAT'],$item['composicionOctanajeDeGasolina'],$item['gasolinaConCombustibleNoFosil'],$item['composicionDeCombustibleNoFosilEnGasolina'],$item['dieselConCombustibleNoFosil'],$item['composicionDeCombustibleNoFosilEnDiesel'],$item['marcaComercial'],$item['marcaje'],$item['concentracionSustanciaMarcaje'],$item['versionRegistro'],$item['regEstado'],$item['regFechaUltimaModificacion'],$item['regUsuarioUltimaModificacion'],$item['regFormularioUltimaModificacion'],$item['regVersionUltimaModificacion']);
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
				
				$CampoNull=$item["concentracionSustanciaMarcaje"];
				if($item["concentracionSustanciaMarcaje"]==" " or trim($item["concentracionSustanciaMarcaje"]) == ""){
					$CampoNull='NULL';
				}
				$consulta= $consulta . $comaText . "('" . $item["claveSubProductoEnvioSATID"] . "','" . $item["claveSubProductoEnvioSAT"] . "','" . $item["descripcionClaveSubProductoEnvioSAT"] . "','" . $item["composicionOctanajeDeGasolina"] . "','" . $item["gasolinaConCombustibleNoFosil"] . "','" . $item["composicionDeCombustibleNoFosilEnGasolina"] . "','" . $item["dieselConCombustibleNoFosil"] . "','" . $item["composicionDeCombustibleNoFosilEnDiesel"] . "','" . $item["marcaComercial"] . "','" . $item["marcaje"] . "'," . $CampoNull . ",'" . $item["versionRegistro"] . "'," . $item["regEstado"] . ",'" . $item["regFechaUltimaModificacion"] . "','" . $item["regUsuarioUltimaModificacion"] . "','" . $item["regFormularioUltimaModificacion"] . "','" . $item["regVersionUltimaModificacion"] . "')";
			}
			 
		   
		 }
	if(!$coma){
		$consulta="select 1";
	}
	
	$this->query=$consulta;
	
    // prepare the query
    $stmt = $this->Conexion->prepare($this->query);
	
    // sanitize
	$claveSubProductoEnvioSATID=htmlspecialchars(strip_tags($item['claveSubProductoEnvioSATID']));
	$claveSubProductoEnvioSAT=htmlspecialchars(strip_tags($item['claveSubProductoEnvioSAT']));
	$descripcionClaveSubProductoEnvioSAT=htmlspecialchars(strip_tags($item['descripcionClaveSubProductoEnvioSAT']));
	$versionRegistro=htmlspecialchars(strip_tags($item['versionRegistro']));
	$regEstado=htmlspecialchars(strip_tags($item['regEstado']));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($item['regFechaUltimaModificacion']));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($item['regUsuarioUltimaModificacion']));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($item['regFormularioUltimaModificacion']));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($item['regVersionUltimaModificacion']));
   
    // bind the values
	$stmt->bindParam(':claveSubProductoEnvioSATID', $claveSubProductoEnvioSATID);
	$stmt->bindParam(':claveSubProductoEnvioSAT', $claveSubProductoEnvioSAT);
	$stmt->bindParam(':descripcionClaveSubProductoEnvioSAT', $descripcionClaveSubProductoEnvioSAT);
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
        echo $this->Mensaje = $e->getMessage().$consulta;
    }

  
    // execute the query, also check if query was successful
    if($stmt->execute()){
        return true;

    }
 
    return false;
}


function Cambia($claveSubProductoEnvioSATID,$claveSubProductoEnvioSAT,$descripcionClaveSubProductoEnvioSAT,$composicionOctanajeDeGasolina,$gasolinaConCombustibleNoFosil,$composicionDeCombustibleNoFosilEnGasolina,$dieselConCombustibleNoFosil,$composicionDeCombustibleNoFosilEnDiesel,$marcaComercial,$marcaje,$concentracionSustanciaMarcaje,$versionRegistro,$regEstado,$regFechaUltimaModificacion,$regUsuarioUltimaModificacion,$regFormularioUltimaModificacion,$regVersionUltimaModificacion){
    if($concentracionSustanciaMarcaje != " " and trim($concentracionSustanciaMarcaje) != ""){
		$CampoNull=",concentracionSustanciaMarcaje=".$concentracionSustanciaMarcaje;
	}
	else {
		$CampoNull=",concentracionSustanciaMarcaje=NULL";
	}
	$query = "UPDATE " . $this->NombreTabla . " SET claveSubProductoEnvioSAT=:claveSubProductoEnvioSAT,descripcionClaveSubProductoEnvioSAT=:descripcionClaveSubProductoEnvioSAT,composicionOctanajeDeGasolina=:composicionOctanajeDeGasolina,gasolinaConCombustibleNoFosil=:gasolinaConCombustibleNoFosil,composicionDeCombustibleNoFosilEnGasolina=:composicionDeCombustibleNoFosilEnGasolina,dieselConCombustibleNoFosil=:dieselConCombustibleNoFosil,composicionDeCombustibleNoFosilEnDiesel=:composicionDeCombustibleNoFosilEnDiesel,marcaComercial=:marcaComercial,marcaje=:marcaje
	".$CampoNull.",versionRegistro=:versionRegistro,regEstado=:regEstado,regFechaUltimaModificacion=:regFechaUltimaModificacion,regUsuarioUltimaModificacion=:regUsuarioUltimaModificacion,regFormularioUltimaModificacion=:regFormularioUltimaModificacion,regVersionUltimaModificacion=:regVersionUltimaModificacion 
		WHERE claveSubProductoEnvioSATID=:claveSubProductoEnvioSATID ";

    // prepare the query
    $stmt = $this->Conexion->prepare($query);
 
     // sanitize
	$claveSubProductoEnvioSATID=htmlspecialchars(strip_tags($claveSubProductoEnvioSATID));
	$claveSubProductoEnvioSAT=htmlspecialchars(strip_tags($claveSubProductoEnvioSAT));
	$descripcionClaveSubProductoEnvioSAT=htmlspecialchars(strip_tags($descripcionClaveSubProductoEnvioSAT));
	$composicionOctanajeDeGasolina=htmlspecialchars(strip_tags($composicionOctanajeDeGasolina));
	$gasolinaConCombustibleNoFosil=htmlspecialchars(strip_tags($gasolinaConCombustibleNoFosil));
	$composicionDeCombustibleNoFosilEnGasolina=htmlspecialchars(strip_tags($composicionDeCombustibleNoFosilEnGasolina));
	$dieselConCombustibleNoFosil=htmlspecialchars(strip_tags($dieselConCombustibleNoFosil));
	$composicionDeCombustibleNoFosilEnDiesel=htmlspecialchars(strip_tags($composicionDeCombustibleNoFosilEnDiesel));
	$marcaComercial=htmlspecialchars(strip_tags($marcaComercial));
	$marcaje=htmlspecialchars(strip_tags($marcaje));
	$versionRegistro=htmlspecialchars(strip_tags($versionRegistro));
	$regEstado=htmlspecialchars(strip_tags($regEstado));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($regFechaUltimaModificacion));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($regUsuarioUltimaModificacion));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($regFormularioUltimaModificacion));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($regVersionUltimaModificacion));
   
    // bind the values
	$stmt->bindParam(':claveSubProductoEnvioSATID', $claveSubProductoEnvioSATID);
	$stmt->bindParam(':claveSubProductoEnvioSAT', $claveSubProductoEnvioSAT);
	$stmt->bindParam(':descripcionClaveSubProductoEnvioSAT', $descripcionClaveSubProductoEnvioSAT);
	$stmt->bindParam(':composicionOctanajeDeGasolina', $composicionOctanajeDeGasolina);
	$stmt->bindParam(':gasolinaConCombustibleNoFosil', $gasolinaConCombustibleNoFosil);
	$stmt->bindParam(':composicionDeCombustibleNoFosilEnGasolina', $composicionDeCombustibleNoFosilEnGasolina);
	$stmt->bindParam(':dieselConCombustibleNoFosil', $dieselConCombustibleNoFosil);
	$stmt->bindParam(':composicionDeCombustibleNoFosilEnDiesel', $composicionDeCombustibleNoFosilEnDiesel);
	$stmt->bindParam(':marcaComercial', $marcaComercial);
	$stmt->bindParam(':marcaje', $marcaje);
	$stmt->bindParam(':versionRegistro', $versionRegistro);
	$regEstado = (int)$regEstado;
	$stmt->bindValue(':regEstado', $regEstado, PDO::PARAM_INT);
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