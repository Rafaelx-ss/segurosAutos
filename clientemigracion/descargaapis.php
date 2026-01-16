<?php
use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use app\assets\AppAsset;

use yii\helpers\Url;
$asset = AppAsset::register($this);
$baseUrl = $asset->baseUrl;
/* @var $this yii\web\View */
//Yii::$app->response->redirect(['siniestros/index']);
//$this->title = 'My Yii Application';
?>

<div class="app-page-title">
	<div class="page-title-wrapper">
    	<div class="page-title-heading">
        	<div class="page-title-icon">
            	<i class="pe-7s-menu icon-gradient bg-malibu-beach"></i>
            </div>
            <div>
				Migración				<div class="page-title-subheading">Importar datos</div>
            </div>
        </div>
       
     </div>
</div>

<div class="main-card mb-3 card">
	<div class="card-body">	
		<?php
		$apiBase = Yii::$app->basePath;
			
		require_once($apiBase."/clientemigracion/lib/database.php");
		require_once($apiBase."/clientemigracion/modelos/ModeloApiConexion.php");
		require_once($apiBase."/clientemigracion/modelos/ModeloApiListado.php");
      echo "1000";
            $database = new Database();
            $conn = $database->getConnection();
            $conexion = new ConexionApi($database);
            $listadoApi = new ListadoApi($database);

            if($conexion->ObtenerDatos(1))
            {
                
                $Username = $conexion->Dataset['UsuarioApiLista'];
                $Password = $conexion->Dataset['PassApiLista'];
                $RutaApi = $conexion->Dataset['RutaApiLista'];
            


                require_once $apiBase.'/clientemigracion/ApiHttpClient.php';
                ApiHttpClient::Init($RutaApi);

                
                ApiHttpClient::$UserName = $Username;
                ApiHttpClient::$Password = $Password;
                ApiHttpClient::$MacAddress =  '7c:67:a2:12:c2:30';
                
                $token = ApiHttpClient::SolicitaToken('/api/ApiToken.php');

                if(ApiHttpClient::$Resultado)
                {
					Yii::$app->session->set('Token', $token);
					Yii::$app->session->set('MacAddress', ApiHttpClient::$MacAddress);
                    
                    $mensaje = ApiHttpClient::$Mensaje . " Token: " . ApiHttpClient::$Token;
                }
                else
                {
                    $mensaje = ApiHttpClient::$Mensaje;
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
                    "grupoestablecimientoID" => $GrupoEstablecimientoID,
                    "MacAddress" => ApiHttpClient::$MacAddress
                )); 
                $datos = ApiHttpClient::ConsumeApi('/api/ApiListadoApis.php','GET',$parametros);
				//echo $datos;
				$RutaApi.'/api/ApiListadoApis.php';
                $arraydatos = json_decode($datos,true);
				
				//$coleccion = $arraydatos["datos"];
                echo $arraydatos['resultado'].$arraydatos['mensaje']."12345".count($coleccion).$parametros."-".$_SESSION["IDENTIFICADOR"]."-";
				$parametros;
                if($arraydatos['resultado'])
                {
                    $coleccion = $arraydatos["datos"];
                    $listadoApi->BorrarTodo();
                    if($listadoApi->Inserta($coleccion))
                    {
                        $mensaje = "Se guardaron los datos";
						$this->redirect(['showdata.php']);
						header("location: showdata.php");
                    }
                    else
                    {
                        echo "3".$mensaje =$listadoApi->query . ',' . $listadoApi->mensaje;
                    }
                }
				else
					echo "4".$arraydatos['mensaje'];
                
            }
            else
            {
                echo "5".$mensaje= "Error al obtener los datos";
            }
       
        ?>
        <p><?php //echo $mensaje;  ?></p>
		<?php
		echo Html::a(' Consultar datos', $url = ['site/showdata&f='.$_GET['f']], $options = ['class'=>'btn-shadow btn btn-primary mr-3 active']);
		echo Html::a(' Importar todos', $url = ['site/descargaapis&f='.$_GET['f']], $options = ['class'=>'btn-shadow btn btn-primary mr-3 active']);
		echo Html::a(' Paises', $url = ['site/descargapaises&f='.$_GET['f']], $options = ['class'=>'btn-shadow btn btn-primary mr-3 active']);
		?>
		
 	</div>
</div>
    
    