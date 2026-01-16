<?php
ini_set('max_execution_time', 300); //300 seconds = 5 minutes
require_once '../libs/php-jwt-master/src/BeforeValidException.php';
require_once '../libs/php-jwt-master/src/ExpiredException.php';
require_once '../libs/php-jwt-master/src/SignatureInvalidException.php';
require_once '../libs/php-jwt-master/src/JWT.php';
require_once '../libs/dbtools.php';

require_once '../modelos/ModeloApiLogAcceso.php';
include_once '../modelos/ModeloApiUsuario.php';

use \Firebase\JWT\JWT;



class ApiTools{

    public static $nombreApi;
    public static $database;
    public static $usuario;
    public static $logAcceso;
    public static $conexion;
    public static $ipAddress;
    public static $macAddress;
    public static $parametros;
    public static $datosCliente;
    public static $jwt;
    public static $respuesta;
    public static $keyToken;
    public static $metodoApi;
    public static $usuarioId;
    public static $funciones;
    public static $equipo;
	public static $jsondb;
   
    

    public static function init($nombre = __FILE__, $jsondb='db', $ruta_config_db='../config'){
		static::$jsondb = $jsondb;
        static::$nombreApi = basename($nombre, '.php');

        static::generaCabeceras();

        static::$usuarioId = 1;
        // obenemos la conexion a la base de datos
        static::$database = new Database($jsondb,$ruta_config_db,static::rutaId());
        static::$conexion = static::$database->getConnection();
        static::$usuario = new Usuario(static::$database);
        static::$equipo = static::getModel('Equipo');

        //obtenemos configuracion del token
        static::$keyToken = static::getKeyToken();
 
        // creamos el objeto LogAcceso

        static::$logAcceso = new LogAcceso(static::$database);

        static::$ipAddress=ApiTools::dameIpCliente();
        static::$macAddress=ApiTools::dameMacAdress(static::$ipAddress);

        // se obtienen los datos posteados
        static::$parametros = file_get_contents("php://input");
        // se decodifican de json a objeto
        static::$datosCliente = json_decode(static::$parametros,true);
        //static::$datosCliente['token'] = static::getBearerToken();
        // optiene el token 
        $token = static::getBearerToken();
        if(isset($token)){
            static::$jwt= $token;
        }
        else
        {
            if(isset(static::$datosCliente['Token']))
            {
                static::$jwt=static::$datosCliente['Token'];
            }
            
        }
        


        // optiene el macAddress
        static::$macAddress = isset(static::$macAddress) ? static::$macAddress : static::getParam('MacAddress');


    }

    public static function getKeyToken()
    {
        $cfgToken = static::getModel('CfgToken');

        if($cfgToken->find(1))
        {
            $key = $cfgToken->Dataset['keyToken'];
        }
        return $key;
    }

    public static function generaCabeceras($metodos='POST,GET,PUT,DELETE',$origenes = '*')
    {

        header("Access-Control-Allow-Origin: " . $origenes);
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: " . $metodos);
        header("Access-Control-Max-Age: 3600");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
        
        
    }




    public static  function dameIpCliente(){
        return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
    }


    public static function dameMacAdress($ipAddress)
    {

        $macAddr=false;

        #run the external command, break output into lines
        $arp=`arp -n $ipAddress`;
        preg_match('/..:..:..:..:..:../',$arp , $matches);
        $macAddr = @$matches[0];

        return $macAddr;
    }


    public static function limpiaParam($parametros) 
    {
        $controlchars = array("\"","\n","\t");

        return str_replace($controlchars,"",$parametros);
    }


    public static function requestMethod()
    {

        return $_SERVER["REQUEST_METHOD"];
    }




    public static  function validaAcceso(){
    
        if(static::$jwt){
            
            // if decode succeed, show user details
            try {
                // decode jwt
                $decoded = JWT::decode(static::$jwt, static::$keyToken, array('HS256'));

                static::$usuarioId = intval($decoded->data->usuarioID);
                static::$usuario->find(static::$usuarioId);
        

                

                if(static::$usuario->Dataset['usarSeguridadIP'])
                {
                    
                    if((static::$ipAddress != $decoded->data->ip) || 
                    !static::$equipo->BuscarEquipoXIp(static::$usuarioId,static::$ipAddress))
                    {
                       
                        static::$respuesta = array (
                                "codigo" => 403,
                                "resultado" => true,
                                "mensaje" => "ip no válida " . static::$ipAddress,
                                "datos" => null,
								"desarrollo" => null
            
                        );
                               
                        return false;
                        
        
                    }
                }
                // valida si debe verificar la MAC
                if(static::$usuario->Dataset['usarSeguridadMac'])
                {
                    if((static::$macAddress != $decoded->data->macAddress) ||
                    !static::$equipo->BuscarEquipoXMacAddress(static::$usuarioId,static::$macAddress))
                    {
                        
                        //http_response_code(201);
            
                        static::$respuesta = array (
                               "codigo" => 403,
                                "resultado" => true,
                                "mensaje" => "MAC no válida [" . static::$macAddress . "]",
                                "datos" => null,
								"desarrollo" => null
            
                        );
                        return false;
                        
        
                    }
                }
    
                if(time() > $decoded->exp )
                {
                    static::$respuesta = array (
                            "codigo" => 200,
                            "resultado" => false,
                            "mensaje" => "el token esta caducado " . time() . ":" . $decoded->exp,
                            "datos" => null,
							"desarrollo" => null
        
                    );
                    return false;
                }
    
    
                static::$respuesta = array (
                        "codigo" => 200,
                        "resultado" => true,
                        "mensaje" => "Acceso permitido ",
                        "datos" => null,
						"desarrollo" => null
    
                );
    
                return true;
            }
            catch (Exception $e){
         
                // set response code
                //http_response_code(401);
             
                // tell the user access denied  & show error message
                static::$respuesta = array(
                    "codigo" => 200,
                    "resultado" => false,
                    "mensaje" => $e->getMessage(),
					"datos" => null,
					"desarrollo" => null
                );
            }
        }
        else {
            static::$respuesta = array(
                "codigo" => 200,
                "resultado" => false,
                "mensaje" => 'Token mal formado o en blanco [' . static::$jwt . ']',
                "datos" => null,
				"desarrollo" => null
            );
            return false;
        }
        
    }


   

    public static function getModel($nombreModelo)
    {
		try
		{
			require '../modelos/ModeloApi' . $nombreModelo . '.php';

			$nombreModelo = $nombreModelo;
			return new $nombreModelo(static::$database);
			
		}
		catch (Exception $e){
		 static::$respuesta = array(
				"codigo" => 200,
				"resultado" => false,
				"mensaje" => $e->getMessage(),
				"datos" => null,
			 	"desarrollo" => null
			);
			return null;
		}
        
    }


    public static function nombreApi()
    {
        return static::$nombreApi;
    }

    public static function getParam($nombreDato)
    {
		
        if(isset($_GET[$nombreDato]) || isset($_GET[ucfirst($nombreDato)]) )
        {
            $valor = isset($_GET[$nombreDato]) ? $_GET[$nombreDato] : $_GET[ucfirst($nombreDato)] ;
        }
        else{
            $valor = isset(static::$datosCliente[$nombreDato]) ? static::$datosCliente[$nombreDato] : 
				(isset(static::$datosCliente[ucfirst($nombreDato)]) ? static::$datosCliente[ucfirst($nombreDato)] : false);
        }
       
        return $valor;
    }

    public static function validaMetodoApi($MetodoApi)
    {
        // if(static::validaAcceso())
        // {
            if(static::$usuario->validaMetodoApi(static::$usuarioId,$MetodoApi,static::$nombreApi)){
                return true;
            }
            else{

                if(static::$usuario->Dataset['activoUsuario'] == 0)
                {
                    $mensaje = 'Usuario inactivo:' . static::$usuarioId . '.Metodo:' . $MetodoApi;
                }
                else
                {
                    $mensaje= 'Acceso a metodo no permitido Usuario:' . static::$usuarioId . '.Metodo:' . $MetodoApi;
                }
                static::$respuesta = array(
                    "codigo" => 200,
                    "resultado" => false,
                    "mensaje" => $mensaje,
                    "datos" => null,
					"desarrollo" => null
                );
                return false;
            }
            
        // }
        // else
        // {
            
        //     return false;
        // }

        
    }

    public static function mensajeHttp($numero)
    {
        switch($numero)
        {
            case  200:
                $mensaje = 'HTTP/1.1 200 OK';
            break;
            case 201:
                $mensaje = 'HTTP/1.1 201 Created';
            break;
            case 422: 
                $mensaje = 'HTTP/1.1 422 Unprocessable Entity';
            break;
            case 404: 
                $mensaje = 'HTTP/1.1 404 Not Found';
            break;
            case 401: 
                $mensaje = 'HTTP/1.1 401 Unauthorized';
            break;
            case 403: 
                $mensaje = 'HTTP/1.1 403 Forbidden';
            break;
        default: 
                $mensaje = 'HTTP/1.1 200 OK';

        }
    }

    public static function asignaRespuesta($codigo,$mensaje,$resultado,$datos=null, $mensaje2=null)
    {
        static::$respuesta = Array(
            "codigo" => $codigo,
            "mensaje" => $mensaje,
            "resultado" => $resultado,
            "datos" => $datos,
			"desarrollo"=>$mensaje2
        );
    }

    public static function asignaMetodo($funcion,$metodoHttp)
    {
        static::$funciones[$metodoHttp] = $funcion;
        
    }

    public static function processRequest()
    {
        if(static::validaAcceso())
        {
            if(isset(static::$funciones[static::requestMethod()]))
            {
                if(static::validaMetodoApi(static::requestMethod()))
                {
                    try {
                        static::$funciones[static::requestMethod()]();
                    } catch (Exception $e){
        
                        static::$respuesta = array(
                            "codigo" => 200,
                            "resultado" => false,
                            "mensaje" => $e->getMessage(),
                            "datos" => null,
							"desarrollo" => null
                        );
                    }
                     
                }
            }
            else
            {
                static::asignaRespuesta(200,'método ' . static::requestMethod() .' no implementado',true);
            }
            
        }
    }

    public static function respuestaApi(){

        http_response_code(static::$respuesta['codigo']);
        try{
            // registra en la bitacora
            static::$logAcceso->Registrar(static::$usuarioId,ApiTools::$ipAddress,ApiTools::$macAddress,
            static::$nombreApi,'',static::$respuesta['codigo'],static::$respuesta['mensaje']);
        }
        catch(Exception $e)
        {
            static::$respuesta['resultado'] = false;
            static::$respuesta['mensaje'] = $e->getMessage() . ",error en respuestaApi";
			static::$respuesta['desarrollo'] = null;
        }
       


        echo json_encode(array("resultado" => static::$respuesta['resultado'], 
        "mensaje" => static::$respuesta['mensaje'],
        "datos" => static::$respuesta['datos'],
		"desarrollo" => null
        ),JSON_NUMERIC_CHECK| JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }


    public static function getAuthorizationHeader() {
        $headers = null;
     
        if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER["Authorization"]);
        }
        else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
            $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
        } elseif (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
            //print_r($requestHeaders);
            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
        }
        return $headers;
    }
     
    public static function getBearerToken() {
        $headers = static::getAuthorizationHeader();
     
        // HEADER: Get the access token from the header
        if (!empty($headers)) {
            if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
                return $matches[1];
            }
        }
        return null;
    }


	public static function url_exist($url)
	{
		$file_headers = @get_headers($url);
		if(!$file_headers || strpos($file_headers[0],'404'))
		{
			$exists = false;
		}
		else {
			$exists = true;
		}
		return $exists;
	}
	
	
	public static function isUrl($url)
	{
		return filter_var($url, FILTER_VALIDATE_URL) !== false ? true :  false;
	}

    public static function rutaId()
    {
       return basename(dirname($_SERVER['SCRIPT_FILENAME']));
    }

}






?>