<?php
// 'user' object
class Usuarios{
 
    // database connection and table name
    private $Conexion;
    private $Database;
    private $NombreTabla = "Usuarios";
 
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


	function ObtenerDatos( $id=0){
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
 
 
        	// return true because email exists in the database
        	return true;
    	}
 
    	// return false if email does not exist in the database
    	return false;
	}
 
	
	
function Inserta($tabla,$registros){

	$tabla=str_replace('Api','', $tabla);
    $consulta="";
	
	$consulta = 'INSERT INTO ' . $tabla . 
			" (grupoID, nombreGrupo, activoGrupo, distribuidorID, versionRegistro, 
			regEstado, regFechaUltimaModificacion, regUsuarioUltimaModificacion, regFormularioUltimaModificacion, regVersionUltimaModificacion) VALUES ";
			
		$coma = false;
		$comaText = "";
		foreach($registros as $item)
		 {
			 if($coma)
			 {
				 $comaText=",";
			 }
			 else
			 {
				 $comaText="";
				 $coma = true;
			 }
			$consulta= $consulta . $comaText . "(" . $item['grupoID'] . ",'" . utf8_decode($item['nombreGrupo']) . "'," . 
			$item['activoGrupo'] . "," . $item['distribuidorID']. "," . $item['versionRegistro']. "," .  $item['regEstado'] . ",
			'" . $item['regFechaUltimaModificacion'] . "'," . $item['regUsuarioUltimaModificacion'] . "," . 
			$item['regFormularioUltimaModificacion'] . "," . $item['regVersionUltimaModificacion'] . ")";
		   
		 }
	
	$this->query=$consulta;
	
    // prepare the query
    $stmt = $this->Conexion->prepare($this->query);
   
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

	
function BorrarTodo($tabla,$error){

	$tabla=str_replace('Api','', $tabla);
    $query = 'DELETE FROM ' . $tabla ;
    
    
    // prepare the query
    $stmt = $this->Conexion->prepare($query);
 
	try{
        // execute the query, also check if query was successful
        if($stmt->execute()){
            return true;

        }
    }   
    catch (Exception $e){
        $this->Mensaje = $e->getMessage();
		echo $error= $this->Mensaje;
    }
   
    /*// execute the query, also check if query was successful
    if($stmt->execute()){
        return true;

    }*/
 
    return false;
}
	
}