<?php



class LogAcceso{
 
    // database connection and table name
    private $Conexion;
    private $NombreTabla = "LogAccesosAPI";
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
function Registrar($usuarioID,$ip,$mac,$nombreApi,$param='',$codigo=0,$mensaje=''){


    $fecha = $this->database->fecha;
    
   

    // insert query
    $query = "INSERT INTO $this->NombreTabla 
            (
                establecimientoID,
                usuarioApiID,
                ipAddress,
                macAddress,
                fechaAcceso,
                versionRegistro, 
                regEstado, 
                regFechaUltimaModificacion, 
                regUsuarioUltimaModificacion, 
                regFormularioUltimaModificacion, 
                regVersionUltimaModificacion,
                nombreApi,
                parametrosApi,
                codigoResultado,
                mensajeResultado
            ) 
                VALUES(
                    1, 
                    :usuarioID,
                    :ip,
                    :mac,
                    {$fecha},
                    1, 
                    1,
                    {$fecha}, 
                    :ultusrID, 
                    1, 
                    1,
                    :nombreApi,
                    :parametros,
                    :codigo,
                    :mensaje
                )
                ";
 
    // prepare the query
    $stmt = $this->Conexion->prepare($query,array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
 
    // sanitize
    //$usuarioID=htmlspecialchars(strip_tags($usuarioID));
    $ip=htmlspecialchars(strip_tags($ip));
    $mac=htmlspecialchars(strip_tags($mac));
    $nombreApi=htmlspecialchars(strip_tags($nombreApi));
    $param=htmlspecialchars(strip_tags($param));
    $mensaje=htmlspecialchars(strip_tags($mensaje));
    
 
    // bind the values
    $stmt->bindParam(':usuarioID', $usuarioID);
    $stmt->bindParam(':ultusrID', $usuarioID);
    $stmt->bindParam(':ip', $ip);
    $stmt->bindParam(':mac', $mac);
    $stmt->bindParam(':nombreApi', $nombreApi);
    $stmt->bindParam(':parametros', $param);
    $stmt->bindParam(':codigo', $codigo);
    $stmt->bindParam(':mensaje', $mensaje);
 
    // execute the query, also check if query was successful
    if($stmt->execute()){
        return true;
    }
    else
        return false;
}
 

	
	
}