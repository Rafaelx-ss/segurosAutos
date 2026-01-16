<?php
  session_start();
  require 'lib/database.php';
  require 'modelos/ModeloApiConexion.php';
  $txtBanderaApi="";

  if (isset($_POST["txtBandera"])){
	  //
	  $txtBanderaApi=$_POST["txtBandera"];
	  include("descargas.php");
	}
	else{
	  //echo "NADA";
	}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Migración</title>
        <!-- Bootstrap 4 CSS and custom CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous" />
        <link rel="stylesheet" type="text/css" href="site.css" />
		
<script type="text/javascript">
	function seleccionaCK(){
		alert(2);
		$('#ck1').attr('checked', this.checked);
		alert(22);
	}
	</script>
</head>
<body>
<form action="<?php echo  $_SERVER["REQUEST_URI"];?>" id="formulario1" name="formulario1" method="POST">
    <div class="container">
        <main role="main" class="pb-3">
            

   
        <?php
        
		$host= $_SERVER["HTTP_HOST"];
		$url= $_SERVER["REQUEST_URI"];
		//echo "http://" . $host ."-". $url;
            
            $database = new Database();
            $conn = $database->getConnection();
            $conexion = new ConexionApi($database);

            if($conexion->ObtenerDatos(1))
            {
                
                $Username = $conexion->Dataset['UsuarioApiLista'];
                $Password = $conexion->Dataset['PassApiLista'];
                $RutaApi = $conexion->Dataset['RutaApiLista'];
				
				//$Username = "masterBrentec";
                //$Password = "Brentec2020";


                require_once 'ApiHttpClient.php';
                ApiHttpClient::Init($RutaApi);

                
                ApiHttpClient::$UserName = $Username;
                ApiHttpClient::$Password = $Password;
                ApiHttpClient::$MacAddress =  '7c:67:a2:12:c2:30';
                
                $token = ApiHttpClient::SolicitaToken('/api/ApiToken.php');

				try{
					if(ApiHttpClient::$Resultado)
					{
						$_SESSION["Token"] = $token;
						$_SESSION["MacAddress"] = ApiHttpClient::$MacAddress;
						 $mensaje = ApiHttpClient::$Mensaje . " Token: " . ApiHttpClient::$Token;
					}
					else
					{
						//echo ApiHttpClient::$Resultado;
						$mensaje = ApiHttpClient::$Mensaje;
					}
				}   
				catch (Exception $e){
					echo $this->mensaje = $e->getMessage();
				}
				
				if($_SESSION["TIPOSOLICITUD"] == "E"){
					$aplicacionID= 0;
					$establecimientoID= $_SESSION["IDENTIFICADOR"];
					$GrupoEstablecimientoID=0;
				}
				else{
					$aplicacionID= 0;
					$establecimientoID= 0;
					$GrupoEstablecimientoID= $_SESSION["IDENTIFICADOR"];
				}
				
                $parametros = json_encode(Array(
                    "Token" => ApiHttpClient::$Token,
                    "aplicacionID" => $aplicacionID,
                    "establecimientoID" => $establecimientoID,
                    "grupogstablecimientoID" => $GrupoEstablecimientoID,
                    "MacAddress" => ApiHttpClient::$MacAddress
                )); 
                $datos = ApiHttpClient::ConsumeApi('/api/ApiListadoApis.php','GET',$parametros);
            
                $arraydatos = json_decode($datos,true);
               
                if($arraydatos['resultado'])
                {
                    $coleccion = $arraydatos["datos"];
                    $cont=1;
                    echo ' <div class="row">
                                <div class="col-lg-10 margin-tb">
                                    <div class="pull-left">
                                        <h2>Importar ' . str_replace('Api','',$txtBanderaApi) . '</h2>
										<a class="btn btn-primary" href="descargaapis.php">Importar seleccionados</a>
                                    </div>
                                    
                                </div>
								<div class="col-lg-2 margin-tb">
                                    <div class="pull-left">
										<a class="btn btn-primary" href="showdata.php">Recargar</a>
										<a class="btn btn-primary" href="index.php">Inicio</a>
                                    </div>
                                </div>
                            </div>
							'.$mensaje2.'
							<input type="hidden" id="txtBandera" name="txtBandera" value="" />
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th align="center"><label><input type="checkbox" onclick="for (var i = 1; i < 9; i++) {$(\'#ck\' + i).attr(\'checked\', this.checked);};" name="cb-autos" value="gusta"> Seleccionar Todos</label></th>
                                        <th>Nombre Registro</th>
										<th>Acción</th>
                                    </tr>
                                </thead>
                                <tbody>';
                    foreach($coleccion as $item)
                    {
						$nombreTabla= str_replace('Api','',$item['nombreApi']);
						$compara=str_replace('Api','',$txtBanderaApi);
						if (trim($compara) == trim($nombreTabla)){
							$style="style='background-color: #0F9F1F; color:#FFF;'";
						}else{
							$style="";
						}
						
						if(trim($item['nombreApi']) != "ApiListadoApis"){
							$boton="<a class='btn btn-primary' onclick='$(\"#txtBandera\").val(\"" . trim($item['nombreApi']) ." \"); document.forms[\"formulario1\"].submit();'  href='javascript:;' >Importar </a>";
						}
						else{
							$boton="<a class='btn btn-primary' href='descargaapis.php' >Importar </a>";
						}
						$onclick="";
                        echo        "<tr " . $style . ">
                                <td>{$cont}</td>
                                <td align='center'><label><input type='checkbox' id='ck" . $cont . "' name='ck" . $cont . "' 
															class='ck" . $cont . "' value='{$item['nombreApi']}'></label></td>
                                <td>{$nombreTabla}
								</td>
                                <td>" . $boton . "</td>
                            </tr>";
						$cont= $cont+1;
							/*$item['aplicacionGrupoID']
                            $item['nombreAplicacionGrupo']
                            $item['aplicacionID']
                            $item['nombreAplicacion']
                            $item['direccionServidor']
                            $item['nombreApi']}*/

                    }
                    echo     '
                            </tbody>
                            </table>';
                }
                
            }
            else
            {
                $mensaje= "Error al obtener los datos";
            }
       
        ?>
        </main>
    </div>
    <!-- jQuery & Bootstrap 4 JavaScript libraries -->
    <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

	
</form>
</body>
</html>
    
    