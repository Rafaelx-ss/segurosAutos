<?php 
class FEFormaPago{ 
 
private $Conexion; 
private $Database; 
private $NombreTabla = 'FEFormaPago'; 
 
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
    	$query = "select * from " . $this->NombreTabla .  " where 1 " . ($id > 0 ? " and formaPagoID = ?" : "");
 
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
			" (formaPagoID,formaPago,descripcion,bancarizado,numeroOperacion,rfcEmisorCuentaOrdenante,cuentaOrdenante,patronCuentaOrdenante,rfcEmisorCuentaBeneficiario,cuentaBenenficiario,patronCuentaBeneficiaria,tipoCadenaPago,nombreBancoEmisorCuentaOrdenanteEnCasoExtranjero,versionRegistro,regEstado,regFechaUltimaModificacion,regUsuarioUltimaModificacion,regFormularioUltimaModificacion,regVersionUltimaModificacion) VALUES ";
			
		$coma = false;
		$comaText = "";
		foreach($registros as $item)
		 {
			//echo $item['textoID']."lolol";
			 if($this->ObtenerDatos($item['formaPagoID'])){
				$this->Cambia($item['formaPagoID'],($item['formaPago']),($item['descripcion']),($item['bancarizado']),($item['numeroOperacion']),($item['rfcEmisorCuentaOrdenante']),($item['cuentaOrdenante']),($item['patronCuentaOrdenante']),($item['rfcEmisorCuentaBeneficiario']),($item['cuentaBenenficiario']),($item['patronCuentaBeneficiaria']),($item['tipoCadenaPago']),($item['nombreBancoEmisorCuentaOrdenanteEnCasoExtranjero']),$item['versionRegistro'],$item['regEstado'],($item['regFechaUltimaModificacion']),$item['regUsuarioUltimaModificacion'],$item['regFormularioUltimaModificacion'],$item['regVersionUltimaModificacion']);
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
				
				$consulta= $consulta . $comaText . "(" . $item["formaPagoID"] . ",'" . ($item["formaPago"]) . "','" . ($item["descripcion"]) . "','" . ($item["bancarizado"]) . "','" . ($item["numeroOperacion"]) . "','" . ($item["rfcEmisorCuentaOrdenante"]) . "','" . ($item["cuentaOrdenante"]) . "','" . ($item["patronCuentaOrdenante"]) . "','" . ($item["rfcEmisorCuentaBeneficiario"]) . "','" . ($item["cuentaBenenficiario"]) . "','" . ($item["patronCuentaBeneficiaria"]) . "','" . ($item["tipoCadenaPago"]) . "','" . ($item["nombreBancoEmisorCuentaOrdenanteEnCasoExtranjero"]) . "'," . $item["versionRegistro"] . "," . $item["regEstado"] . ",'" . ($item["regFechaUltimaModificacion"]) . "'," . $item["regUsuarioUltimaModificacion"] . "," . $item["regFormularioUltimaModificacion"] . "," . $item["regVersionUltimaModificacion"] . ")";
			}
			 
		   
		 }
	if(!$coma){
		$consulta="select 1";
	}
	
	$this->query=$consulta;
	
    // prepare the query
    $stmt = $this->Conexion->prepare($this->query);
	
    // sanitize
	$formaPagoID=htmlspecialchars(strip_tags($item['formaPagoID']));
	$formaPago=htmlspecialchars(strip_tags($item['formaPago']));
	$descripcion=htmlspecialchars(strip_tags($item['descripcion']));
	$bancarizado=htmlspecialchars(strip_tags($item['bancarizado']));
	$numeroOperacion=htmlspecialchars(strip_tags($item['numeroOperacion']));
	$rfcEmisorCuentaOrdenante=htmlspecialchars(strip_tags($item['rfcEmisorCuentaOrdenante']));
	$cuentaOrdenante=htmlspecialchars(strip_tags($item['cuentaOrdenante']));
	$patronCuentaOrdenante=htmlspecialchars(strip_tags($item['patronCuentaOrdenante']));
	$rfcEmisorCuentaBeneficiario=htmlspecialchars(strip_tags($item['rfcEmisorCuentaBeneficiario']));
	$cuentaBenenficiario=htmlspecialchars(strip_tags($item['cuentaBenenficiario']));
	$patronCuentaBeneficiaria=htmlspecialchars(strip_tags($item['patronCuentaBeneficiaria']));
	$tipoCadenaPago=htmlspecialchars(strip_tags($item['tipoCadenaPago']));
	$nombreBancoEmisorCuentaOrdenanteEnCasoExtranjero=htmlspecialchars(strip_tags($item['nombreBancoEmisorCuentaOrdenanteEnCasoExtranjero']));
	$versionRegistro=htmlspecialchars(strip_tags($item['versionRegistro']));
	$regEstado=htmlspecialchars(strip_tags($item['regEstado']));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($item['regFechaUltimaModificacion']));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($item['regUsuarioUltimaModificacion']));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($item['regFormularioUltimaModificacion']));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($item['regVersionUltimaModificacion']));
   
    // bind the values
	$stmt->bindParam(':formaPagoID', $formaPagoID);
	$stmt->bindParam(':formaPago', $formaPago);
	$stmt->bindParam(':descripcion', $descripcion);
	$stmt->bindParam(':bancarizado', $bancarizado);
	$stmt->bindParam(':numeroOperacion', $numeroOperacion);
	$stmt->bindParam(':rfcEmisorCuentaOrdenante', $rfcEmisorCuentaOrdenante);
	$stmt->bindParam(':cuentaOrdenante', $cuentaOrdenante);
	$stmt->bindParam(':patronCuentaOrdenante', $patronCuentaOrdenante);
	$stmt->bindParam(':rfcEmisorCuentaBeneficiario', $rfcEmisorCuentaBeneficiario);
	$stmt->bindParam(':cuentaBenenficiario', $cuentaBenenficiario);
	$stmt->bindParam(':patronCuentaBeneficiaria', $patronCuentaBeneficiaria);
	$stmt->bindParam(':tipoCadenaPago', $tipoCadenaPago);
	$stmt->bindParam(':nombreBancoEmisorCuentaOrdenanteEnCasoExtranjero', $nombreBancoEmisorCuentaOrdenanteEnCasoExtranjero);
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


function Cambia($formaPagoID,$formaPago,$descripcion,$bancarizado,$numeroOperacion,$rfcEmisorCuentaOrdenante,$cuentaOrdenante,$patronCuentaOrdenante,$rfcEmisorCuentaBeneficiario,$cuentaBenenficiario,$patronCuentaBeneficiaria,$tipoCadenaPago,$nombreBancoEmisorCuentaOrdenanteEnCasoExtranjero,$versionRegistro,$regEstado,$regFechaUltimaModificacion,$regUsuarioUltimaModificacion,$regFormularioUltimaModificacion,$regVersionUltimaModificacion){
    $query = "UPDATE " . $this->NombreTabla . " SET formaPago=:formaPago,descripcion=:descripcion,bancarizado=:bancarizado,numeroOperacion=:numeroOperacion,rfcEmisorCuentaOrdenante=:rfcEmisorCuentaOrdenante,cuentaOrdenante=:cuentaOrdenante,patronCuentaOrdenante=:patronCuentaOrdenante,rfcEmisorCuentaBeneficiario=:rfcEmisorCuentaBeneficiario,cuentaBenenficiario=:cuentaBenenficiario,patronCuentaBeneficiaria=:patronCuentaBeneficiaria,tipoCadenaPago=:tipoCadenaPago,nombreBancoEmisorCuentaOrdenanteEnCasoExtranjero=:nombreBancoEmisorCuentaOrdenanteEnCasoExtranjero,versionRegistro=:versionRegistro,regEstado=:regEstado,regFechaUltimaModificacion=:regFechaUltimaModificacion,regUsuarioUltimaModificacion=:regUsuarioUltimaModificacion,regFormularioUltimaModificacion=:regFormularioUltimaModificacion,regVersionUltimaModificacion=:regVersionUltimaModificacion 
		WHERE formaPagoID=:formaPagoID ";

    // prepare the query
    $stmt = $this->Conexion->prepare($query);
 
     // sanitize
	$formaPagoID=htmlspecialchars(strip_tags($formaPagoID));
	$formaPago=htmlspecialchars(strip_tags($formaPago));
	$descripcion=htmlspecialchars(strip_tags($descripcion));
	$bancarizado=htmlspecialchars(strip_tags($bancarizado));
	$numeroOperacion=htmlspecialchars(strip_tags($numeroOperacion));
	$rfcEmisorCuentaOrdenante=htmlspecialchars(strip_tags($rfcEmisorCuentaOrdenante));
	$cuentaOrdenante=htmlspecialchars(strip_tags($cuentaOrdenante));
	$patronCuentaOrdenante=htmlspecialchars(strip_tags($patronCuentaOrdenante));
	$rfcEmisorCuentaBeneficiario=htmlspecialchars(strip_tags($rfcEmisorCuentaBeneficiario));
	$cuentaBenenficiario=htmlspecialchars(strip_tags($cuentaBenenficiario));
	$patronCuentaBeneficiaria=htmlspecialchars(strip_tags($patronCuentaBeneficiaria));
	$tipoCadenaPago=htmlspecialchars(strip_tags($tipoCadenaPago));
	$nombreBancoEmisorCuentaOrdenanteEnCasoExtranjero=htmlspecialchars(strip_tags($nombreBancoEmisorCuentaOrdenanteEnCasoExtranjero));
	$versionRegistro=htmlspecialchars(strip_tags($versionRegistro));
	$regEstado=htmlspecialchars(strip_tags($regEstado));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($regFechaUltimaModificacion));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($regUsuarioUltimaModificacion));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($regFormularioUltimaModificacion));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($regVersionUltimaModificacion));
   
    // bind the values
	$stmt->bindParam(':formaPagoID', $formaPagoID);
	$stmt->bindParam(':formaPago', $formaPago);
	$stmt->bindParam(':descripcion', $descripcion);
	$stmt->bindParam(':bancarizado', $bancarizado);
	$stmt->bindParam(':numeroOperacion', $numeroOperacion);
	$stmt->bindParam(':rfcEmisorCuentaOrdenante', $rfcEmisorCuentaOrdenante);
	$stmt->bindParam(':cuentaOrdenante', $cuentaOrdenante);
	$stmt->bindParam(':patronCuentaOrdenante', $patronCuentaOrdenante);
	$stmt->bindParam(':rfcEmisorCuentaBeneficiario', $rfcEmisorCuentaBeneficiario);
	$stmt->bindParam(':cuentaBenenficiario', $cuentaBenenficiario);
	$stmt->bindParam(':patronCuentaBeneficiaria', $patronCuentaBeneficiaria);
	$stmt->bindParam(':tipoCadenaPago', $tipoCadenaPago);
	$stmt->bindParam(':nombreBancoEmisorCuentaOrdenanteEnCasoExtranjero', $nombreBancoEmisorCuentaOrdenanteEnCasoExtranjero);
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