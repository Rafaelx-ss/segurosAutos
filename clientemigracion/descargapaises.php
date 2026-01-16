<?php
  session_start();


        
            require 'lib/database.php';
           
            require 'modelos/ModeloApiListado.php';
            require 'modelos/ModeloApiPais.php';
            $database = new Database();
            $conn = $database->getConnection();
            $listadoApi = new ListadoApi($database);
            $paises = new Pais($database);

            if($listadoApi->ObtenerDatos('ApiPaises'))
            {
                
                $servidor = $listadoApi->Dataset['direccionServidor'];
              
                $RutaApi = $listadoApi->Dataset['rutaApi'];
            


                require_once 'ApiHttpClient.php';
                ApiHttpClient::Init($servidor);
                ApiHttpClient::$Token = $_SESSION["Token"];
                ApiHttpClient::$MacAddress = $_SESSION["MacAddress"];
              
                $parametros = json_encode(Array(
                    "Token" => ApiHttpClient::$Token,
                    "Id" => 0,
                    "MacAddress" => ApiHttpClient::$MacAddress
                )); 
                $datos = ApiHttpClient::ConsumeApi($RutaApi,'GET',$parametros);
            
                $arraydatos = json_decode($datos,true);
               
                if($arraydatos['resultado'])
                {
                    $coleccion = $arraydatos["datos"];
                    $paises->BorrarTodo();
                    if($paises->Inserta($coleccion))
                    {
                        $mensaje = "Se guardaron los datos";

                        echo ' <div class="row">
                                <div class="col-lg-12 margin-tb">
                                    <div class="pull-left">
                                        <h2> PHP  Client Web Api</h2>
                                    </div>
                                    
                                </div>
                            </div>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>paisID</th>
                                        <th>nombrePais</th>
                                    </tr>
                                </thead>
                                <tbody>';
                    foreach($coleccion as $item)
                    {
                        echo        "<tr>
                                <td>{$item['paisID']}</td>
                                <td>{$item['nombrePais']}</td>
                               
                            </tr>";

                    }
                    echo     '
                            </tbody>
                            </table>';

                    }
                    else
                    {
                        $mensaje = "Hubo un error";
                    }
                }
                
            }
            else
            {
                $mensaje= "Error al obtener los datos";
            }
       
?>