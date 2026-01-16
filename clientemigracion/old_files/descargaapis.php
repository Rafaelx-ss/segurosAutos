<?php
ob_start();
  session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Home Page - WebApiClient</title>
        <!-- Bootstrap 4 CSS and custom CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous" />
        <link rel="stylesheet" type="text/css" href="site.css" />
</head>
<body>
    <div class="container">
        <main role="main" class="pb-3">
            

   
        <?php
        
            require 'lib/database.php';
            require 'modelos/ModeloApiConexion.php';
            require 'modelos/ModeloApiListado.php';
            $database = new Database();
            $conn = $database->getConnection();
            $conexion = new ConexionApi($database);
            $listadoApi = new ListadoApi($database);

			
            if($conexion->ObtenerDatos(1))
            {
             
                $Username = $conexion->Dataset['UsuarioApiLista'];
                $Password = $conexion->Dataset['PassApiLista'];
                $RutaApi = $conexion->Dataset['RutaApiLista'];
            


                require_once 'ApiHttpClient.php';
                ApiHttpClient::Init($RutaApi);

                
                ApiHttpClient::$UserName = $Username;
                ApiHttpClient::$Password = $Password;
                ApiHttpClient::$MacAddress =  '0C:96:E6:E1:48:E9';
                
                $token = ApiHttpClient::SolicitaToken('/api/ApiToken.php');

                if(ApiHttpClient::$Resultado)
                {
                    $_SESSION["Token"] = $token;
                    $_SESSION["MacAddress"] = ApiHttpClient::$MacAddress;
                    $mensaje = ApiHttpClient::$Mensaje . " Token: " . ApiHttpClient::$Token;
                }
                else
                {
                    $mensaje = ApiHttpClient::$Mensaje;
					
                }
                
            	            

                $parametros = json_encode(Array(
                    "Token" => ApiHttpClient::$Token,
                    "aplicacionID" => 0,
                    "establecimientoID" => 116297,
                    "MacAddress" => ApiHttpClient::$MacAddress
                )); 
                $datos = ApiHttpClient::ConsumeApi('/api/ApiListadoApis.php','GET',$parametros);
            
                $arraydatos = json_decode($datos,true);
               	
                if($arraydatos['resultado'])
                {
					
                    $coleccion = $arraydatos["datos"];
                    $listadoApi->BorrarTodo();
					
                    if($listadoApi->Inserta($coleccion))
                    {						
                        $mensaje = "Se guardaron los datos";
						header("location: showdata.php");
                    }
                    else
                    {
                        $mensaje =$listadoApi->query . ',' . $listadoApi->mensaje;
                    }
					
					
                }
                
            }
            else
            {
                $mensaje= "Error al obtener los datos";
            }
			
       
        ?>
        <p><?php //echo $mensaje;  ?></p>
				<h2>Migración</h2>
				
		<a class="btn btn-primary" href="showdata.php">Consultar datos</a>
		<a class="btn btn-primary" href="descargaapis.php">Importar todos</a>
				
        <a class='btn btn-primary' href='descargapaises.php' >Paises</a>
    


        </main>
    </div>
</body>
</html>
    
    