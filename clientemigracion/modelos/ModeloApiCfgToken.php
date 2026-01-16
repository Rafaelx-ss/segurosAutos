<?php
// 'user' object
class CfgToken{
 
    // database connection and table name
    private $Conexion;
    private $NombreTabla = "ConfiguracionesToken";
    private $database;
    // object properties
    public $Campos;
    public $Dataset;
	public $Mensaje;
   
    
    
 
    // constructor
    public function __construct($dtb){
        $this->Conexion = $dtb->conn;
        $this->database = $dtb;
    }
 
// create() method will be here
	// create new user record


function find($id){
 
    // query to check if email exists
    $query = "SELECT *
            FROM " . $this->NombreTabla . "
            WHERE configuracionTokenID = ?";
 
    // prepare the query
    $stmt = $this->Conexion->prepare( $query ,array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL) );
 
    // sanitize
    $id=htmlspecialchars(strip_tags($id));
 
    // bind given email value
    $stmt->bindParam(1, $id);
 
    // execute the query
    $stmt->execute();
 
    // get number of rows
    $num = $stmt->rowCount();
 
    // if email exists, assign values to object properties for easy access and use for php sessions
    if($num>0){
 
        // get record details / values
        $this->Dataset = $stmt->fetch(PDO::FETCH_ASSOC);
 
        
        // return true because email exists in the database
        return true;
    }
 
    // return false if email does not exist in the database
    return false;
}



	
	
}