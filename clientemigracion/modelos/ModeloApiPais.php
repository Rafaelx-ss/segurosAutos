<?php
// 'user' object
class Pais{
 
    // database connection and table name
    private $Conexion;
    private $NombreTabla = "Paises";
 
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
function ObtenerUno($id){
 
    // query to check if email exists
    $query = "select prd.ProductoID, prd.codigoProducto, prd.nombreProducto, prp.precioVenta
        , prp.fechaInicio from " . $this->NombreTabla . " as prd
        inner join ProductosPreciosVenta as prp on prp.productoID=prd.productoID
        inner join TiposProductos as tpr on prd.tipoProductoID=tpr.tipoProductoID
        where tpr.nombreTipoProducto=? " . ($id > 0 ? " and prd.ProductoID = ?" : "");
 
    // prepare the query
    $stmt = $this->Conexion->prepare( $query );
 
    // sanitize
    $id=htmlspecialchars(strip_tags($id));
    $tipo=htmlspecialchars(strip_tags($tipo));
 
    // bind given id value

    $stmt->bindParam(1, $tipo);
    if($id > 0){
      $stmt->bindParam(2, $id);
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


function ObtenerDatos( $id=false){
 
    // query to check if email exists
    $query = "select * from " . $this->NombreTabla . " as prd " 
        . ($id > 0 ? " where prd.ProductoID = ?" : "");
 
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


    // insert query
    $this->query = 'INSERT INTO ' . $this->NombreTabla . 
    " (paisID, nombrePais, estadoPais, versionRegistro, regEstado, regFechaUltimaModificacion, regUsuarioUltimaModificacion, regFormularioUltimaModificacion, regVersionUltimaModificacion) VALUES ";
    

    
    
    $coma = false;
    foreach($registros as $item)
     {
         if($coma)
         {
            $this->query= $this->query . ",(" . $item['paisID'] . ",'" . $item['nombrePais'] . "'," . 
            $item['estadoPais'] . "," . $item['versionRegistro']. "," .  $item['regEstado'] . ",'" .
            $item['regFechaUltimaModificacion'] . "'," . $item['regUsuarioUltimaModificacion'] . "," . 
             $item['regFormularioUltimaModificacion'] . "," . $item['regVersionUltimaModificacion'] . ")";
         }
         else
         {
             $coma = true;
             $this->query= $this->query . "(" . $item['paisID'] . ",'" . $item['nombrePais'] . "'," . 
            $item['estadoPais'] . "," . $item['versionRegistro']. "," .  $item['regEstado'] . ",'" .
            $item['regFechaUltimaModificacion'] . "'," . $item['regUsuarioUltimaModificacion'] . "," . 
             $item['regFormularioUltimaModificacion'] . "," . $item['regVersionUltimaModificacion'] . ")";
         }
       
     }
    
 
    // prepare the query
    $stmt = $this->Conexion->prepare($this->query);
   
   try{
        // execute the query, also check if query was successful
        if($stmt->execute()){
            return true;

        }
    }   
    catch (Exception $e){
        $this->Mensaje = $e->getMessage();
    }

  
    // execute the query, also check if query was successful
    if($stmt->execute()){
        return true;

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