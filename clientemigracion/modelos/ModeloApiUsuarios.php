<?php 
class Usuarios{ 
 
private $Conexion; 
private $Database; 
private $NombreTabla = 'Usuarios'; 
 
// object properties
public $Campos;
public $Dataset;
public $Mensaje;
 
// constructor
    public function __construct($dbf){
        $this->Database = $dbf;
        $this->Conexion = $dbf->conn;
    }
	
	function ObtenerDatos($id=0, $tipoconsulta=""){
   		// $id = intval($id);
    	// query to check if email exists
    	$query = "select * from " . $this->NombreTabla . " " . ($id > 0 ? " where usuarioID = ?" : "");
 
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
 
			if($tipoconsulta==""){
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

    $consulta="";
	
	$consulta = 'INSERT INTO ' . $this->NombreTabla . 
			" (usuarioID,nombreUsuario,passw,usuario,activoUsuario,correoUsuario,codigoRecuperacionPassw,fechaGeneracionCodigoRecuperacionPassw,intentosValidos,AuthKey,versionRegistro,regEstado,regFechaUltimaModificacion,regUsuarioUltimaModificacion,regFormularioUltimaModificacion,regVersionUltimaModificacion) VALUES ";
			
		$coma = false;
		$comaText = "";
		foreach($registros as $item)
		 {
			//echo $item['textoID']."lolol";
			 if($this->ObtenerDatos($item['usuarioID'])){
				$this->Cambia($item['usuarioID'],$item['nombreUsuario'],$item['passw'],$item['usuario'],$item['activoUsuario'],$item['correoUsuario'],$item['codigoRecuperacionPassw'],$item['fechaGeneracionCodigoRecuperacionPassw'],$item['intentosValidos'],$item['AuthKey'],$item['versionRegistro'],$item['regEstado'],$item['regFechaUltimaModificacion'],$item['regUsuarioUltimaModificacion'],$item['regFormularioUltimaModificacion'],$item['regVersionUltimaModificacion']);
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
				
				$CampoNull=$item["fechaGeneracionCodigoRecuperacionPassw"];
				if($item["fechaGeneracionCodigoRecuperacionPassw"]==" " or trim($item["fechaGeneracionCodigoRecuperacionPassw"]) == ""){
					$CampoNull='NULL';
					$CAMPOfECHA="NULL";
				}
				ELSE{
					$CAMPOfECHA="'".$item["fechaGeneracionCodigoRecuperacionPassw"]."'";
				}
				$consulta= $consulta . $comaText . "(" . $item["usuarioID"] . ",'" . $item["nombreUsuario"] . "','" . $item["passw"] . "','" . $item["usuario"] . "','" . $item["activoUsuario"] . "','" . $item["correoUsuario"] . "','" . $item["codigoRecuperacionPassw"] . "'," . 
				$CAMPOfECHA . "," . $item["intentosValidos"] . ",'" . $item["AuthKey"] . "'," . $item["versionRegistro"] . ",'" . $item["regEstado"] . "','" . $item["regFechaUltimaModificacion"] . "'," . $item["regUsuarioUltimaModificacion"] . "," . $item["regFormularioUltimaModificacion"] . "," . $item["regVersionUltimaModificacion"] . ")";
				//echo $CAMPOfECHA;
			}
			 
		   
		 }
	if(!$coma){
		$consulta="select 1";
	}
	
	$this->query=$consulta;
	
    // prepare the query
    $stmt = $this->Conexion->prepare($this->query);
	
    // sanitize
	$usuarioID=htmlspecialchars(strip_tags($item['usuarioID']));
	//$nombreUsuario=htmlspecialchars(strip_tags($item['nombreUsuario']));
	$nombreUsuario=htmlspecialchars($item['nombreUsuario'], ENT_QUOTES,'UTF-8',false);
	$passw=htmlspecialchars(strip_tags($item['passw']));
	$usuario=htmlspecialchars(strip_tags($item['usuario']));
	$activoUsuario=htmlspecialchars(strip_tags($item['activoUsuario']));
	$correoUsuario=htmlspecialchars(strip_tags($item['correoUsuario']));
	$codigoRecuperacionPassw=htmlspecialchars(strip_tags($item['codigoRecuperacionPassw']));
	if($item["fechaGeneracionCodigoRecuperacionPassw"] != " " and trim($item["fechaGeneracionCodigoRecuperacionPassw"]) != ""){
		$fechaGeneracionCodigoRecuperacionPassw=htmlspecialchars(strip_tags($item['fechaGeneracionCodigoRecuperacionPassw']));
	}
	$intentosValidos=htmlspecialchars(strip_tags($item['intentosValidos']));
	$AuthKey=htmlspecialchars(strip_tags($item['AuthKey']));
	$versionRegistro=htmlspecialchars(strip_tags($item['versionRegistro']));
	$regEstado=htmlspecialchars(strip_tags($item['regEstado']));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($item['regFechaUltimaModificacion']));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($item['regUsuarioUltimaModificacion']));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($item['regFormularioUltimaModificacion']));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($item['regVersionUltimaModificacion']));
   
    // bind the values
	$stmt->bindParam(':usuarioID', $usuarioID);
	$stmt->bindParam(':nombreUsuario', $nombreUsuario);
	$stmt->bindParam(':passw', $passw);
	$stmt->bindParam(':usuario', $usuario);
	$stmt->bindParam(':activoUsuario', $activoUsuario);
	$stmt->bindParam(':correoUsuario', $correoUsuario);
	$stmt->bindParam(':codigoRecuperacionPassw', $codigoRecuperacionPassw);
	$stmt->bindParam(':fechaGeneracionCodigoRecuperacionPassw', $fechaGeneracionCodigoRecuperacionPassw);
	$stmt->bindParam(':intentosValidos', $intentosValidos);
	$stmt->bindParam(':AuthKey', $AuthKey);
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
        echo $this->Mensaje = $e->getMessage().'<br /> <br />Consulta: <br />'.$consulta;
    }

 
    return false;
}


function Cambia($usuarioID,$nombreUsuario,$passw,$usuario,$activoUsuario,$correoUsuario,$codigoRecuperacionPassw,$fechaGeneracionCodigoRecuperacionPassw,$intentosValidos,$AuthKey,$versionRegistro,$regEstado,$regFechaUltimaModificacion,$regUsuarioUltimaModificacion,$regFormularioUltimaModificacion,$regVersionUltimaModificacion){
    $valor="--".$fechaGeneracionCodigoRecuperacionPassw."--";
	if($fechaGeneracionCodigoRecuperacionPassw != " " and trim($fechaGeneracionCodigoRecuperacionPassw) != ""){
		$CampoNull=",fechaGeneracionCodigoRecuperacionPassw=:fechaGeneracionCodigoRecuperacionPassw";
	}
	else {
		$CampoNull="";
	}
	$query = "UPDATE " . $this->NombreTabla . " SET nombreUsuario=:nombreUsuario,passw=:passw,usuario=:usuario,activoUsuario=:activoUsuario,correoUsuario=:correoUsuario,codigoRecuperacionPassw=:codigoRecuperacionPassw".$CampoNull.",intentosValidos=:intentosValidos,AuthKey=:AuthKey,versionRegistro=:versionRegistro,regEstado=:regEstado,regFechaUltimaModificacion=:regFechaUltimaModificacion,regUsuarioUltimaModificacion=:regUsuarioUltimaModificacion,regFormularioUltimaModificacion=:regFormularioUltimaModificacion,regVersionUltimaModificacion=:regVersionUltimaModificacion 
		WHERE usuarioID=:usuarioID ";

    // prepare the query
    $stmt = $this->Conexion->prepare($query);
 
     // sanitize
	$usuarioID=htmlspecialchars(strip_tags($usuarioID));
	//$nombreUsuario=htmlspecialchars(strip_tags($nombreUsuario));
	$nombreUsuario=htmlspecialchars($nombreUsuario, ENT_QUOTES,'UTF-8',false);
	$passw=htmlspecialchars(strip_tags($passw));
	$usuario=htmlspecialchars(strip_tags($usuario));
	$activoUsuario=htmlspecialchars(strip_tags($activoUsuario));
	$correoUsuario=htmlspecialchars(strip_tags($correoUsuario));
	$codigoRecuperacionPassw=htmlspecialchars(strip_tags($codigoRecuperacionPassw));
	if($fechaGeneracionCodigoRecuperacionPassw != " " and trim($fechaGeneracionCodigoRecuperacionPassw) != ""){
		$fechaGeneracionCodigoRecuperacionPassw=htmlspecialchars(strip_tags($fechaGeneracionCodigoRecuperacionPassw));
	}
	$intentosValidos=htmlspecialchars(strip_tags($intentosValidos));
	$AuthKey=htmlspecialchars(strip_tags($AuthKey));
	$versionRegistro=htmlspecialchars(strip_tags($versionRegistro));
	$regEstado=htmlspecialchars(strip_tags($regEstado));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($regFechaUltimaModificacion));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($regUsuarioUltimaModificacion));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($regFormularioUltimaModificacion));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($regVersionUltimaModificacion));
   
   
	// bind the values
	$stmt->bindParam(':usuarioID', $usuarioID);
	$stmt->bindParam(':nombreUsuario', $nombreUsuario);
	$stmt->bindParam(':passw', $passw);
	$stmt->bindParam(':usuario', $usuario);
	$stmt->bindParam(':activoUsuario', $activoUsuario);
	$stmt->bindParam(':correoUsuario', $correoUsuario);
	$stmt->bindParam(':codigoRecuperacionPassw', $codigoRecuperacionPassw);
	if($fechaGeneracionCodigoRecuperacionPassw != " " and trim($fechaGeneracionCodigoRecuperacionPassw) != ""){
		$stmt->bindParam(':fechaGeneracionCodigoRecuperacionPassw', $fechaGeneracionCodigoRecuperacionPassw);
	}
	$stmt->bindParam(':intentosValidos', $intentosValidos);
	$stmt->bindParam(':AuthKey', $AuthKey);
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
        echo $this->Mensaje = $e->getMessage().$valor.$query;//.'<br /> <br />Consulta: <br />'.$consulta;
    }
 
    return false;
}
	
		
}
	?>