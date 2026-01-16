<?php
  session_start();
  require 'lib/database.php';
  require 'modelos/ModeloApiConexion.php';
  $txtBanderaApi="";

if (isset($_POST["txtTabla"])){
	$txtBanderaApi=$_POST["txtBandera"];
	$txtTabla=$_POST["txtTabla"];
	$DB=$_POST["txtDB"];
	include("controlador/contoladorAcg.php");
}
else{
	if (isset($_POST["txtBandera"])){
	  //
	  $txtBanderaApi=$_POST["txtBandera"];
	  include("descargas.php");
	}
	else{
	  //echo "NADA";
	}
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
                ApiHttpClient::$MacAddress =  '80:30:49:e7:47:ff';
                
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
                                        <h2>Generar</h2>
										<input type="textbox" id="txtTabla" name="txtTabla" value="PermisosFormulariosPerfiles" />
										<input type="textbox" id="txtDB" name="txtDB" value="c1BonoboLugay" />
										<a class="btn btn-primary" onclick="$(\'#txtBandera\').val(\'ApiAcg\'); document.forms[\'formulario1\'].submit();"  href="javascript:;" >Importar </a>
										<input type="hiddens" id="txtBandera" name="txtBandera" value="" />
                                    </div>
                                    
                                </div>
								<div class="col-lg-2 margin-tb">
                                    <div class="pull-left">
										<a class="btn btn-primary" href="acg.php">Recargar</a>
										<a class="btn btn-primary" href="index.php">Inicio</a>
                                    </div>
                                </div>
                            </div>
							'.$mensaje.'
							';
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
    
    