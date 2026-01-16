<?php
// 'user' object
class Usuario{
 
    // database connection and table name
    private $Conexion;
    private $NombreTabla = "UsuariosAPI";
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
function Crear(){
    $fecha = $this->database->fecha;
    // insert query
    $query = "INSERT INTO " . $this->NombreTabla . "
            SET
                usuarioApiID= :usuarioID,
                usuario = :usuario,
                nombreUsuario = :nombreusuario,
                correoUsuario = :correoUsuario,
                passw = :passw,
                codigoRecuperacionPassw = '', 
                fechaGeneracioncodigoRecuperacionPassw = {$fecha}, 
                intentosvalidos = 1, 
                versionRegistro = 1, 
                regEstado = 1, 
                tiempoCaducidadToken = 1000,
                regFechaUltimaModificacion = {$fecha}, 
                regUsuarioUltimaModificacion = 0, 
                regFormularioUltimaModificacion = 0, 
                regVersionUltimaModificacion = 1, 
                activoUsuario = 1,
                usarSeguridadIP=1,
                usarSeguridadMac=1,
                usarLectura = 1,
                usarEscritura=1
                ";
 
    // prepare the query
    $stmt = $this->Conexion->prepare($query,array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
 
    // sanitize
    $this->Campos->usuario=htmlspecialchars(strip_tags($this->Campos->usuario));
    $this->Campos->nombreUsuario=htmlspecialchars(strip_tags($this->Campos->nombreUsuario));
    $this->Campos->correoUsuario=htmlspecialchars(strip_tags($this->Campos->correoUsuario));
    $this->Campos->passw=htmlspecialchars(strip_tags($this->Campos->passw));
 
    // bind the values
    $stmt->bindParam(':usuarioID', $this->Campos->usuarioID);
    $stmt->bindParam(':usuario', $this->Campos->usuario);
    $stmt->bindParam(':nombreusuario', $this->Campos->nombreUsuario);
    $stmt->bindParam(':correoUsuario', $this->Campos->correoUsuario);
    
 
    // hash the password before saving to database
    $password_hash = password_hash($this->Campos->passw, PASSWORD_BCRYPT);
    $stmt->bindParam(':passw', $password_hash);
 
    // execute the query, also check if query was successful
    if($stmt->execute()){
        return true;
    }
 
    return false;
}
 
// emailExists() method will be here
// check if given email exist in the database
function emailExists($email){
 
    // query to check if email exists
    $query = "SELECT usuarioApiID, usuario, nombreUsuario, passw
            FROM " . $this->NombreTabla . "
            WHERE correoUsuario = ?";
 
    // prepare the query
    $stmt = $this->Conexion->prepare( $query,array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL) );
 
    // sanitize
    $email=htmlspecialchars(strip_tags($email));
 
    // bind given email value
    $stmt->bindParam(1, $email);
 
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
 
// update() method will be here

function find($id, $campo='usuarioApiID'){
 
    // query to check if email exists
    $query = "SELECT * FROM " . $this->NombreTabla . " WHERE $campo = ? ";
 
    // prepare the query
    $stmt = $this->Conexion->prepare( $query,array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL) );
 
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


function BuscarUsuario($usuario){
 
    // query to check if email exists
    $query = "SELECT  usuarioApiID, nombreUsuario, correoUsuario, codigoRecuperacionPassw, passw 
            , usarSeguridadIP, usarSeguridadMac, usarLectura, usarEscritura,tiempoCaducidadToken  FROM 
             $this->NombreTabla WHERE activoUsuario=1 and usuario = :usuario ";
 
    // prepare the query
    $stmt = $this->Conexion->prepare( $query,array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL) );
 
    // sanitize
    $usuario=htmlspecialchars(strip_tags($usuario));
 
    // bind given email value
    $stmt->bindParam(':usuario', $usuario);
 
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


function validaMetodoApi($usuarioId,$metodoApi,$nombreApi){
  

   
    // query to check if email exists
    $query = "SELECT t.estadoDetalleApi,m.estadoMetodo, a.estadoApi, u.activoUsuario FROM MetodosUsuariosApis t
    INNER JOIN MetodosApis m ON m.metodoApiID = t.metodoApiID
    INNER JOIN Apis a ON a.apiID = m.apiID 
    INNER JOIN UsuariosAPI u ON u.usuarioApiID = t.usuarioApiID
    WHERE t.usuarioApiID = :usuarioApiID and m.tipoMetodoApi = :metodoApi and a.nombreApi= :nombreApi 
    AND u.regEstado = 1 and a.regEstado = 1 and t.regEstado = 1 and m.regEstado = 1";
 
    // prepare the query
    $stmt = $this->Conexion->prepare( $query,array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL) );
 
    // sanitize
    $usuarioId=htmlspecialchars(strip_tags($usuarioId));
    $metodoApi=htmlspecialchars(strip_tags($metodoApi));
 
    // bind given email value
    $stmt->bindParam(':usuarioApiID', $usuarioId);
    $stmt->bindParam(':metodoApi', $metodoApi);
    $stmt->bindParam(':nombreApi', $nombreApi);
 
    // execute the query
    $stmt->execute();
 
    // get number of rows
    $num = $stmt->rowCount();
 
    // if email exists, assign values to object properties for easy access and use for php sessions
    if($num>0){
 
        // get record details / values
        $this->Dataset = $stmt->fetch(PDO::FETCH_ASSOC);
 
        if($this->Dataset['activoUsuario'] == 1 && $this->Dataset['estadoApi'] && $this->Dataset['estadoMetodo'] && 
            $this->Dataset['estadoDetalleApi']){
            return true;
        }
        else{
            return false;
        }
        
    }

    // return false 
    return false;
}
	
	
}