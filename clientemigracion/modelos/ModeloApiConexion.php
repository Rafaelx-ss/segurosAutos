<?php
// 'user' object

class ConexionApi{
 
    // database connection and table name
    private $Conexion;
    private $Database;
    private $NombreTabla = "ConexionApisInicio";
 
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


function ObtenerDatos( $id=1){
    require dirname(__DIR__).'/config/llaves.php';
   // $id = intval($id);
    // query to check if email exists
    $query = "select * from " . $this->NombreTabla . " WHERE regEstado = 1 and id = ?";
 
    // prepare the query
    $stmt = $this->Conexion->prepare( $query );
 
    // sanitize
    $id=htmlspecialchars(strip_tags($id));
 
    // bind given id value

    
    if($id > 0){
      $stmt->bindParam(1, $id);
    }
    // execute the query
	
	try{
		$stmt->execute();

		// get number of rows
		$num = $stmt->rowCount();

		// if email exists, assign values to object properties for easy access and use for php sessions
		if($num>0){

			// get record details / values
			$this->Dataset = $stmt->fetch(PDO::FETCH_ASSOC);
			$crypttext = base64_decode( $this->Dataset['PassApiLista']);

			//$llaveprivada = file_get_contents("config/llaveprivada.pem");


			openssl_private_decrypt($crypttext, $decrypted, $llaveprivada);
			$this->Dataset['PassApiLista'] = $decrypted;

			// return true because email exists in the database
			return true;
		}
		
	}catch (Exception $e){
        //return false;
		$this->Mensaje = $e->getMessage();
		
		print_r($e->getMessage());
    }
 
    // return false if email does not exist in the database
    return false;
}
 
function Update($campos){

    require dirname(__DIR__).'/config/llaves.php';
	
	//$existeId = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='".$this->NombreTabla."' AND column_name='identificadorEstacion'";
	$existeId = "SHOW COLUMNS FROM ".$this->NombreTabla." LIKE 'identificadorEstacion'";
	$pExisteId = $this->Conexion->prepare($existeId);
	$pExisteId->execute();
	$ejecutaExisteId = $pExisteId->fetch(PDO::FETCH_ASSOC);
	
	//print_r($ejecutaExisteId);
	$numExisteId = 0;
	if(isset($ejecutaExisteId['Field'])){
		$numExisteId = 1;
	}
	//echo $existeId."aquie esta existe".$numExisteId;
	
	if($numExisteId != 0){
		$query = "UPDATE " . $this->NombreTabla .
		 "  SET UsuarioApiLista = :UsuarioApiLista,
		  PassApiLista = :PassApiLista,
		  RutaApiLista = :RutaApiLista, 
		  TipoSolicitud = :TipoSolicitud, 
		  identificadorEstacion = :identificadorEstacion, 
		  RegEstado = 1 WHERE id = 1";
	}else{
		$query = "UPDATE " . $this->NombreTabla .
		 "  SET UsuarioApiLista = :UsuarioApiLista,
		  PassApiLista = :PassApiLista,
		  RutaApiLista = :RutaApiLista, 
		  TipoSolicitud = :TipoSolicitud, 
		  RegEstado = 1 WHERE id = 1";
	}
	
    // insert query
	try{
		

		// prepare the query
		$stmt = $this->Conexion->prepare($query);

		// sanitize

		$UsuarioApiLista=htmlspecialchars(strip_tags($campos['UsuarioApiLista']));
		$PassApiLista=htmlspecialchars(strip_tags($campos['PassApiLista']));
		$RutaApiLista=htmlspecialchars(strip_tags($campos['RutaApiLista']));
		$TipoSolicitud=htmlspecialchars(strip_tags($campos['TipoSolicitud']));
		
		if($numExisteId != 0){
			$identificadorEstacion=htmlspecialchars(strip_tags($campos['identificadorEstacion']));
		}
		

		//$llavepublica = file_get_contents("config/llavepublica.pem");

		openssl_public_encrypt($PassApiLista, $crypttext, $llavepublica);
		$PassApiLista = base64_encode($crypttext);

		// $llaveprivada = file_get_contents("config/llaveprivada.pem");

		//  openssl_private_decrypt($crypttext, $decrypted, $llaveprivada);
		//  $RutaApiLista= $decrypted;

		// bind the values
		$stmt->bindParam(':UsuarioApiLista', $UsuarioApiLista);
		$stmt->bindParam(':PassApiLista', $PassApiLista);
		$stmt->bindParam(':RutaApiLista', $RutaApiLista);
		$stmt->bindParam(':TipoSolicitud', $TipoSolicitud);
		if($numExisteId != 0){
			$stmt->bindParam(':identificadorEstacion', $identificadorEstacion);
		}
		// execute the query, also check if query was successful
		if($stmt->execute()){
			return true;

		}
	}catch (Exception $e){
        //return false;
		$this->Mensaje = $e->getMessage();
		echo $this->NombreTabla."<br>";
		print_r($e->getMessage());
    }
 
   // return false;
}



function Cambia($codigo,$nombre){

    // insert query
    $query = "UPDATE " . $this->NombreTabla .
     " SET empleadoNombre= :empleadoNombre WHERE empleadoCodigo= :empleadoCodigo";
 
    // prepare the query
    $stmt = $this->Conexion->prepare($query);
 
    // sanitize
    
    
    $nombre=htmlspecialchars(strip_tags($nombre));
 
    // bind the values
    $stmt->bindParam(':empleadoCodigo', $codigo);
    $stmt->bindParam(':empleadoNombre', $nombre);

    // execute the query, also check if query was successful
    if($stmt->execute()){
        return true;

    }
 
    return false;
}

	
	
}