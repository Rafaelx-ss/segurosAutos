<?php


class ApiHttpClient {


    public static $Token; 
    public static $Resultado;
    public static $Mensaje;
    public static $UserName;
    public static $Password;
    public static $MacAddress;
    public static $EstatusHttp;
    public static $BaseAddress;


    public static function Init($baseAdd)
    {
        static::$BaseAddress = $baseAdd;
    }

    public static function SolicitaToken($direccion)
    {
        $curl = curl_init();

        $data = json_encode(Array(
            "UserName" => static::$UserName,
            "Password" => static::$Password,
            "MacAddress" => static::$MacAddress
        ));

        //header('Content-Type: application/json'); 
        curl_setopt($curl, CURLOPT_URL, static::$BaseAddress . $direccion);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json' ));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);

        $result = curl_exec($curl); // Execute the cURL statement
        curl_close($curl); // Close the cURL connection
        $respuesta = json_decode($result,true); // Return the received data

        if (isset($respuesta["Token"]))
        {
            static::$Resultado = true;
            static::$Token = $respuesta["Token"];
            static::$EstatusHttp = 200;
        }
        else
        {
            static::$Resultado = false;
            static::$Token = "";
            static::$Mensaje = $respuesta["mensaje"];
        }

        

        return static::$Token;
    }


    public static function ConsumeApi($direccion, $metodo, $datos = false)
    {
        $curl = curl_init();

        switch ($metodo)
        {
            case "POST":
                curl_setopt($curl, CURLOPT_POSTFIELDS, $datos);
                curl_setopt($curl, CURLOPT_POST, 1);
                break;
            case "PUT":
                
                curl_setopt($curl, CURLOPT_POSTFIELDS, $datos);
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
                break;
            case "DELETE":
                curl_setopt($curl, CURLOPT_POSTFIELDS, $datos);
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE"); 
                break;
            default:
                if ($datos)
                {
                    $direccion = sprintf("%s?%s", $direccion, http_build_query(json_decode($datos,true)));
                }    
        }
    
        // Optional Authentication:
        $authorization = "Authorization: Bearer " . static::$Token; // Prepare the authorisation token
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization ));
    
        curl_setopt($curl, CURLOPT_URL, static::$BaseAddress . $direccion);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    
        $result = curl_exec($curl);



        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        // Check the HTTP Status code
        switch ($httpCode) {
            case 200:
                static::$Mensaje = "200: Success";
               
                break;
            case 404:
                static::$Mensaje = "404: API Not found";
                break;
            case 500:
                static::$Mensaje = "500: servers replied with an error.";
                break;
            case 502:
                static::$Mensaje = "502: servers may be down or being upgraded. Hopefully they'll be OK soon!";
                break;
            case 503:
                static::$Mensaje = "503: service unavailable. Hopefully they'll be OK soon!";
                break;
            default:
                static::$Mensaje = "Undocumented error: " . $httpCode . " : " . curl_error($curl);
                break;
        }
    
        static::$EstatusHttp = $httpCode;
        curl_close($curl);
    
        return $result ;
    }

}



?>