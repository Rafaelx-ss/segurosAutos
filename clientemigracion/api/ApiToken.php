<?php
// required headers

require '../libs/apitools.php';


// incluye los modelos necesarios para el api

require '../modelos/ModeloApiEquipo.php';
require '../modelos/ModeloApiCfgToken.php';

use \Firebase\JWT\JWT;

// genera las cabeceras http
ApiTools::generaCabeceras();

// get ip & mac
$ipAddress=ApiTools::dameIpCliente();
$macAddress=ApiTools::dameMacAdress($ipAddress);
// get database connection
$database = new Database('db');

$db = $database->getConnection();

if(!$database->resultado)
{
	http_response_code(401);

	
 
    // tell the user login failed
    echo json_encode(array(
        "resultado" => false,
        "mensaje" => $database->msg,
        "Token" => ""
    ));
	return;
}

// tabla de log acceso
$logAcceso = new LogAcceso($database);

// instantiate user object
$user = new Usuario($database);
$equipo = new Equipo($database);
$cfgToken = new CfgToken($database);
 

// get posted data
$parametros = file_get_contents("php://input");
$data = json_decode($parametros);
 
// set product property values
$usuario = isset($data->UserName) ? $data->UserName : '';
$macAddress = isset($data->MacAddress) ? $data->MacAddress : $macAddress;
$password = isset($data->Password) ? $data->Password : '';
$existeUsuario = $user->BuscarUsuario($usuario);

if(!$cfgToken->find(1)){
    http_response_code(501);
	
	$logAcceso->Registrar(1,$ipAddress,$macAddress,
		'ObtenerToken','',401,'No se encontró cfgToken. Db:' . $database->db_name);
	
    echo json_encode(array("resultado" => false, "mensaje" => 'No se encontró cfgToken . Db:' . $database->db_name));
    return;
}
 

// generate jwt will be here
// check if email exists and if password is correct


if($existeUsuario && password_verify($password, $user->Dataset["passw"])){
    try{
        if($user->Dataset['usarSeguridadIP'])
        {
            if(!$equipo->BuscarEquipoXIp($user->Dataset['usuarioApiID'],$ipAddress))
            {
                http_response_code(403);
            
                $resultado = $logAcceso->Registrar($user->Dataset['usuarioApiID'],$ipAddress,$macAddress,
        'ObtenerToken','',403,'ip no válida');
                echo json_encode(array("resultado" => false, "mensaje " => "ip no válida " . $ipAddress));
                return;
            }
        }
    }
   catch(Exception $e)
   {
        http_response_code(403);
        echo json_encode(array("resultado" => false, "mensaje" => $e->getMessage() . $ipAddress));
                return;
   }

    if($user->Dataset['usarSeguridadMac'])
    {
        try{

            if(!$equipo->BuscarEquipoXMacAddress($user->Dataset['usuarioApiID'],$macAddress))
            {
                http_response_code(403);
                
                $resultado = $logAcceso->Registrar($user->Dataset['usuarioApiID'],$ipAddress,$macAddress,
                'ObtenerToken','',403,'mac no válida');
            
                echo json_encode(array("resultado" => false, "mensaje" => "mac no válida " . $macAddress));
                return;
             
            }
        }
        catch(Exception $e)
        {
            http_response_code(403);
            echo json_encode(array("resultado" => false, "mensaje" => $e->getMessage() . $macAddress));
                return;
        }
        
    }

    $nbf = time();
    $exp = time() + $user->Dataset['tiempoCaducidadToken'];
 
    $token = array(
       "iss" => $cfgToken->Dataset["issuerToken"],
       "aud" => $cfgToken->Dataset['audience'],
       "exp" => $exp,
       "data" => array(
           "usuarioID" => $user->Dataset["usuarioApiID"],
           "usuario" => $usuario,
           "ip" => $ipAddress,
           "macAddress" => $macAddress
       )
    );

    
    
 
    // generate jwt
    try{
        $jwt = JWT::encode($token, $cfgToken->Dataset['keyToken']);
    }
    catch(Exception $e)
    {
        $jwt = $e->getMessage();
    }
    
    //$decoded = JWT::decode($jwt, $key, array('HS256'));
    
    
    $resultado = $logAcceso->Registrar($user->Dataset['usuarioApiID'],$ipAddress,$macAddress,
    'ObtenerToken','',200,'Token generado');
    // set response code
    http_response_code(200);
    echo json_encode(
            array(
				"resultado" => true,
				"mensaje" => 'Token obtenido con exito',
                "Token" => $jwt
            )
        );
     // registra el consumo del api
   
 
}
else{
    // set response code
    http_response_code(401);

     $logAcceso->Registrar(1,$ipAddress,$macAddress,
    'ObtenerToken','',401,'usuario no encontrado');
 
    // tell the user login failed
    echo json_encode(array(
        "resultado" => false,
        "mensaje" => "usuario no encontrado o no esta activo",
        "Token" => ""
    ));
}
?>