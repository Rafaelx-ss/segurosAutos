<?php 
class UsuariosAPIGrupo{ 
 
private $Conexion; 
private $Database; 
private $NombreTabla = 'UsuariosAPI'; 
 
// object properties
public $Campos;
public $Dataset;
public $Mensaje;
 
// constructor
    public function __construct($dbf){
        $this->Database = $dbf;
        $this->Conexion = $dbf->conn;
    }
	
	function ObtenerDatos($id=0, $grupoID=0, $establecimientoID=0){
   		// $id = intval($id);
    	// query to check if email exists
		$cadenaEstablecimiento="";
		if($establecimientoID<>0){
			$cadenaEstablecimiento= " and usuario=".$establecimientoID." ";
			
		}
		$cadenaGrupo="";
		if($grupoID <> 0){
			$cadenaGrupo=" and (usuario in( select establecimientoID from Establecimientos where grupoID=" . $grupoID . ") 
				)";
		}
    	$query = "select * from " . $this->NombreTabla . " WHERE 1 " . $cadenaGrupo . $cadenaEstablecimiento. ($id > 0 ? " and usuarioApiID = ?" : "");
 
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
			" (usuarioApiID,nombreUsuario,passw,usuario,activoUsuario,correoUsuario,codigoRecuperacionPassw,fechaGeneracionCodigoRecuperacionPassw,intentosValidos,tiempoCaducidadToken,usarSeguridadIP,usarSeguridadMac,usarLectura,usarEscritura,versionRegistro,regEstado,regFechaUltimaModificacion,regUsuarioUltimaModificacion,regFormularioUltimaModificacion,regVersionUltimaModificacion) VALUES ";
			
		$coma = false;
		$comaText = "";
		foreach($registros as $item)
		 {
			//echo $item['textoID']."lolol";
			 if($this->ObtenerDatos($item['usuarioApiID'])){
				$this->Cambia($item['usuarioApiID'],($item['nombreUsuario']),($item['passw']),($item['usuario']),($item['activoUsuario']),($item['correoUsuario']),($item['codigoRecuperacionPassw']),($item['fechaGeneracionCodigoRecuperacionPassw']),$item['intentosValidos'],($item['tiempoCaducidadToken']),($item['usarSeguridadIP']),($item['usarSeguridadMac']),($item['usarLectura']),($item['usarEscritura']),$item['versionRegistro'],($item['regEstado']),($item['regFechaUltimaModificacion']),$item['regUsuarioUltimaModificacion'],$item['regFormularioUltimaModificacion'],$item['regVersionUltimaModificacion']);
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
				
				$consulta= $consulta . $comaText . "(" . $item["usuarioApiID"] . ",'" . utf8_decode($item["nombreUsuario"]) . "','" . utf8_decode($item["passw"]) . "','" . utf8_decode($item["usuario"]) . "','" . utf8_decode($item["activoUsuario"]) . "','" . utf8_decode($item["correoUsuario"]) . "','" . utf8_decode($item["codigoRecuperacionPassw"]) . "','" . utf8_decode($item["fechaGeneracionCodigoRecuperacionPassw"]) . "'," . $item["intentosValidos"] . ",'" . utf8_decode($item["tiempoCaducidadToken"]) . "','" . utf8_decode($item["usarSeguridadIP"]) . "','" . utf8_decode($item["usarSeguridadMac"]) . "','" . utf8_decode($item["usarLectura"]) . "','" . utf8_decode($item["usarEscritura"]) . "'," . $item["versionRegistro"] . ",'" . utf8_decode($item["regEstado"]) . "','" . utf8_decode($item["regFechaUltimaModificacion"]) . "'," . $item["regUsuarioUltimaModificacion"] . "," . $item["regFormularioUltimaModificacion"] . "," . $item["regVersionUltimaModificacion"] . ")";
			}
			 
		   
		 }
	if(!$coma){
		$consulta="select 1";
	}
	
	$this->query=$consulta;
	
    // prepare the query
    $stmt = $this->Conexion->prepare($this->query);
	
    // sanitize
	$usuarioApiID=htmlspecialchars(strip_tags($item['usuarioApiID']));
	$nombreUsuario=htmlspecialchars(strip_tags($item['nombreUsuario']));
	$passw=htmlspecialchars(strip_tags($item['passw']));
	$usuario=htmlspecialchars(strip_tags($item['usuario']));
	$activoUsuario=htmlspecialchars(strip_tags($item['activoUsuario']));
	$correoUsuario=htmlspecialchars(strip_tags($item['correoUsuario']));
	$codigoRecuperacionPassw=htmlspecialchars(strip_tags($item['codigoRecuperacionPassw']));
	$fechaGeneracionCodigoRecuperacionPassw=htmlspecialchars(strip_tags($item['fechaGeneracionCodigoRecuperacionPassw']));
	$intentosValidos=htmlspecialchars(strip_tags($item['intentosValidos']));
	$tiempoCaducidadToken=htmlspecialchars(strip_tags($item['tiempoCaducidadToken']));
	$usarSeguridadIP=htmlspecialchars(strip_tags($item['usarSeguridadIP']));
	$usarSeguridadMac=htmlspecialchars(strip_tags($item['usarSeguridadMac']));
	$usarLectura=htmlspecialchars(strip_tags($item['usarLectura']));
	$usarEscritura=htmlspecialchars(strip_tags($item['usarEscritura']));
	$versionRegistro=htmlspecialchars(strip_tags($item['versionRegistro']));
	$regEstado=htmlspecialchars(strip_tags($item['regEstado']));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($item['regFechaUltimaModificacion']));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($item['regUsuarioUltimaModificacion']));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($item['regFormularioUltimaModificacion']));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($item['regVersionUltimaModificacion']));
   
    // bind the values
	$stmt->bindParam(':usuarioApiID', $usuarioApiID);
	$stmt->bindParam(':nombreUsuario', $nombreUsuario);
	$stmt->bindParam(':passw', $passw);
	$stmt->bindParam(':usuario', $usuario);
	$stmt->bindParam(':activoUsuario', $activoUsuario);
	$stmt->bindParam(':correoUsuario', $correoUsuario);
	$stmt->bindParam(':codigoRecuperacionPassw', $codigoRecuperacionPassw);
	$stmt->bindParam(':fechaGeneracionCodigoRecuperacionPassw', $fechaGeneracionCodigoRecuperacionPassw);
	$stmt->bindParam(':intentosValidos', $intentosValidos);
	$stmt->bindParam(':tiempoCaducidadToken', $tiempoCaducidadToken);
	$stmt->bindParam(':usarSeguridadIP', $usarSeguridadIP);
	$stmt->bindParam(':usarSeguridadMac', $usarSeguridadMac);
	$stmt->bindParam(':usarLectura', $usarLectura);
	$stmt->bindParam(':usarEscritura', $usarEscritura);
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


function Cambia($usuarioApiID,$nombreUsuario,$passw,$usuario,$activoUsuario,$correoUsuario,$codigoRecuperacionPassw,$fechaGeneracionCodigoRecuperacionPassw,$intentosValidos,$tiempoCaducidadToken,$usarSeguridadIP,$usarSeguridadMac,$usarLectura,$usarEscritura,$versionRegistro,$regEstado,$regFechaUltimaModificacion,$regUsuarioUltimaModificacion,$regFormularioUltimaModificacion,$regVersionUltimaModificacion){
    $query = "UPDATE " . $this->NombreTabla . " SET nombreUsuario=:nombreUsuario,passw=:passw,usuario=:usuario,activoUsuario=:activoUsuario,correoUsuario=:correoUsuario,codigoRecuperacionPassw=:codigoRecuperacionPassw,fechaGeneracionCodigoRecuperacionPassw=:fechaGeneracionCodigoRecuperacionPassw,intentosValidos=:intentosValidos,tiempoCaducidadToken=:tiempoCaducidadToken,usarSeguridadIP=:usarSeguridadIP,usarSeguridadMac=:usarSeguridadMac,usarLectura=:usarLectura,usarEscritura=:usarEscritura,versionRegistro=:versionRegistro,regEstado=:regEstado,regFechaUltimaModificacion=:regFechaUltimaModificacion,regUsuarioUltimaModificacion=:regUsuarioUltimaModificacion,regFormularioUltimaModificacion=:regFormularioUltimaModificacion,regVersionUltimaModificacion=:regVersionUltimaModificacion 
		WHERE usuarioApiID=:usuarioApiID ";

    // prepare the query
    $stmt = $this->Conexion->prepare($query);
 
     // sanitize
	$usuarioApiID=htmlspecialchars(strip_tags($usuarioApiID));
	$nombreUsuario=htmlspecialchars(strip_tags($nombreUsuario));
	$passw=htmlspecialchars(strip_tags($passw));
	$usuario=htmlspecialchars(strip_tags($usuario));
	$activoUsuario=htmlspecialchars(strip_tags($activoUsuario));
	$correoUsuario=htmlspecialchars(strip_tags($correoUsuario));
	$codigoRecuperacionPassw=htmlspecialchars(strip_tags($codigoRecuperacionPassw));
	$fechaGeneracionCodigoRecuperacionPassw=htmlspecialchars(strip_tags($fechaGeneracionCodigoRecuperacionPassw));
	$intentosValidos=htmlspecialchars(strip_tags($intentosValidos));
	$tiempoCaducidadToken=htmlspecialchars(strip_tags($tiempoCaducidadToken));
	$usarSeguridadIP=htmlspecialchars(strip_tags($usarSeguridadIP));
	$usarSeguridadMac=htmlspecialchars(strip_tags($usarSeguridadMac));
	$usarLectura=htmlspecialchars(strip_tags($usarLectura));
	$usarEscritura=htmlspecialchars(strip_tags($usarEscritura));
	$versionRegistro=htmlspecialchars(strip_tags($versionRegistro));
	$regEstado=htmlspecialchars(strip_tags($regEstado));
	$regFechaUltimaModificacion=htmlspecialchars(strip_tags($regFechaUltimaModificacion));
	$regUsuarioUltimaModificacion=htmlspecialchars(strip_tags($regUsuarioUltimaModificacion));
	$regFormularioUltimaModificacion=htmlspecialchars(strip_tags($regFormularioUltimaModificacion));
	$regVersionUltimaModificacion=htmlspecialchars(strip_tags($regVersionUltimaModificacion));
   
    // bind the values
	$stmt->bindParam(':usuarioApiID', $usuarioApiID);
	$stmt->bindParam(':nombreUsuario', $nombreUsuario);
	$stmt->bindParam(':passw', $passw);
	$stmt->bindParam(':usuario', $usuario);
	$stmt->bindParam(':activoUsuario', $activoUsuario);
	$stmt->bindParam(':correoUsuario', $correoUsuario);
	$stmt->bindParam(':codigoRecuperacionPassw', $codigoRecuperacionPassw);
	$stmt->bindParam(':fechaGeneracionCodigoRecuperacionPassw', $fechaGeneracionCodigoRecuperacionPassw);
	$stmt->bindParam(':intentosValidos', $intentosValidos);
	$stmt->bindParam(':tiempoCaducidadToken', $tiempoCaducidadToken);
	$stmt->bindParam(':usarSeguridadIP', $usarSeguridadIP);
	$stmt->bindParam(':usarSeguridadMac', $usarSeguridadMac);
	$stmt->bindParam(':usarLectura', $usarLectura);
	$stmt->bindParam(':usarEscritura', $usarEscritura);
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
        echo $this->Mensaje = $e->getMessage();//.'<br /> <br />Consulta: <br />'.$consulta;
    }
 
    return false;
}
	
		
}
	?>