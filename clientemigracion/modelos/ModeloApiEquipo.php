<?php
// 'user' object
class Equipo{
 
    // database connection and table name
    private $conexion;
    private $nombreTabla = "ListadoEquiposSeguros";
    private $database;
    // object properties
    public $campos;
    public $dataset;
    public $Mensaje;
    
 
    // constructor
    public function __construct($dtb){
        $this->conexion = $dtb->conn;
        $this->database = $dtb;
    }
 
// create() method will be here
	// create new user record

 
// emailExists() method will be here
// check if given email exist in the database
function buscarEquipoXIp($usuarioID,$ipAddress){
 
    // query to check if email exists
    $query = "SELECT *
            FROM " . $this->nombreTabla . "
            WHERE usuarioApiID = ?
            and regEstado = 1  and ipAddress = ? ";
 
    // prepare the query
    $stmt = $this->conexion->prepare( $query ,array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
 
    // sanitize
    $usuarioID=htmlspecialchars(strip_tags($usuarioID));
    $ipAddress=htmlspecialchars(strip_tags($ipAddress));
 
    // bind parameters
    $stmt->bindParam(1, $usuarioID);
    $stmt->bindParam(2, $ipAddress);
 
    // execute the query
    $stmt->execute();
 
    // get number of rows
    $num = $stmt->rowCount();
 
    // if email exists, assign values to object properties for easy access and use for php sessions
    if($num>0){
 
        // get record details / values
        $this->dataset = $stmt->fetch(PDO::FETCH_ASSOC);
 
 
        // return true because email exists in the database
        return true;
    }
 
    // return false if email does not exist in the database
    return false;
}


function buscarEquipoXMacAddress($usuarioID,$macAddress){
 
    // query to check if email exists
    $query = "SELECT *
            FROM " . $this->nombreTabla . "
            WHERE usuarioApiID = ? and regEstado = 1
            AND macAddress = ? ";
 
    // prepare the query
    $stmt = $this->conexion->prepare( $query,array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL) );
 
    // sanitize
    $usuarioID=htmlspecialchars(strip_tags($usuarioID));
    $macAddress=htmlspecialchars(strip_tags($macAddress));
 
    // bind parameters
    $stmt->bindParam(1, $usuarioID);
    $stmt->bindParam(2, $macAddress);
 
    // execute the query
    $stmt->execute();
 
    // get number of rows
    $num = $stmt->rowCount();
 
    // if email exists, assign values to object properties for easy access and use for php sessions
    if($num>0){
 
        // get record details / values
        $this->dataset = $stmt->fetch(PDO::FETCH_ASSOC);
 
 
        // return true because email exists in the database
        return true;
    }
 
    // return false if email does not exist in the database
    return false;
}
 

	
	
}