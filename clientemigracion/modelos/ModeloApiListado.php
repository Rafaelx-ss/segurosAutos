<?php
// 'user' object
class ListadoApi{
 
    // database connection and table name
    private $Conexion;
    private $Database;
    private $NombreTabla = "ListadoApis";
 
    // object properties
    public $Campos;
    public $Dataset;
    public $Mensaje;
    public $query;
    
    
 
    // constructor
    public function __construct($dbf){
        $this->Database = $dbf;
        $this->Conexion = $dbf->conn;
    }
 

 
// ObtenerEstados() method will be here
// check if given email exist in the database


function ObtenerDatos( $nombre){
	try{
	   // $id = intval($id);
		// query to check if email exists
		$query = "select * from " . $this->NombreTabla . " WHERE ActivoEstado = 1 and nombreApi='".$nombre."'";
	 	
		// prepare the query
		$stmt = $this->Conexion->prepare( $query );		
		// execute the query
		$stmt->execute();	 
		// get number of rows
		$num = $stmt->rowCount();
	 
		if($num>0){	 
			// get record details / values
			//print_r($stmt->fetch(PDO::FETCH_ASSOC));
			$this->Dataset = $stmt->fetch(PDO::FETCH_ASSOC);
		  
			// return true because email exists in the database
			return true;
		}
	 
	}catch (Exception $e){
        //return false;
		//print_r($e->getMessage());
		$this->Mensaje = $e->getMessage().'<br /> <br />Consulta '.$this->NombreTabla.': <br />'.$query;
    }
    // return false if email does not exist in the database
    return false;
}
 
function Inserta($registros){


    // insert query
    $consulta = 'INSERT INTO ' . $this->NombreTabla . ' (aplicacionGrupoID, nombreAplicacionGrupo, aplicacionID, nombreAplicacion, direccionServidor, nombreApi, rutaApi, ActivoEstado) VALUES ';
    
	
    $coma = false;
    foreach($registros as $item)
     {
         if($coma)
         {
            $consulta= $consulta . ",(" . $item['aplicacionGrupoID'] . ",'" . $item['nombreAplicacionGrupo'] . "'," . 
            $item['aplicacionID'] . ",'" . $item['nombreAplicacion']. "','" .  $item['direccionServidor'] . "','" .
             $item['nombreApi'] . "','" . $item['rutaApi'] . "',1)";
			 
         }
         else
         {
             $coma = true;
             $consulta= $consulta . "(" . $item['aplicacionGrupoID'] . ",'" . $item['nombreAplicacionGrupo'] . "'," . 
            $item['aplicacionID'] . ",'" . $item['nombreAplicacion']. "','" .  $item['direccionServidor'] . "','" .
             $item['nombreApi'] . "','" . $item['rutaApi'] . "',1)";
						
         }
       
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
        $this->Mensaje = $e->getMessage().'<br /> <br />Consulta '.$this->NombreTabla.': <br />'.$consulta;
		
    }

 
    return false;
}


function BorrarTodo(){


    // insert query
    $query = 'DELETE FROM ' . $this->NombreTabla ;
    
    
 
    // prepare the query
    $stmt = $this->Conexion->prepare($query);
 
   
    // execute the query, also check if query was successful
    if($stmt->execute()){
        return true;

    }
 
    return false;
}

	
}