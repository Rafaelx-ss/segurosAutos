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
		
		//echo "AKI: ".$_POST["cmbSeleccionaTipoMetodo"];
		try{
			Yii::$app->session->set('TIPOMETODO', $_POST["cmbSeleccionaTipoMetodo"]);
		}
		catch (Exception $e){
			Yii::$app->session->set('TIPOMETODO', "GET");
			//echo $e->getMessage();
		}
		require_once($apiBase."/clientemigracion/lib/database.php");
		require_once($apiBase."/clientemigracion/modelos/ModeloApiConexion.php");
		require_once($apiBase."/clientemigracion/modelos/ModeloApiListado.php");
    try{
            $database = new Database();
            $conn = $database->getConnection();
            $conexion = new ConexionApi($database);
            $listadoApi = new ListadoApi($database);

            if($conexion->ObtenerDatos(1))
            {
                
              	$Username = $conexion->Dataset['UsuarioApiLista'];
                $Password = $conexion->Dataset['PassApiLista'];
                $RutaApi = $conexion->Dataset['RutaApiLista'];
				//$RutaApi = "http://10.128.5.230/ApiPapion/";
				
				require_once $apiBase.'/clientemigracion/ApiHttpClient.php';
                
				ApiHttpClient::Init($RutaApi);
				ApiHttpClient::$UserName = $Username;
                ApiHttpClient::$Password = $Password;
                
                $token = ApiHttpClient::SolicitaToken('api/ApiToken.php');
				
				 if(ApiHttpClient::$Resultado){
					Yii::$app->session->set('Token', $token);
					Yii::$app->session->set('MacAddress', ApiHttpClient::$MacAddress);
                    
                    $mensaje = ApiHttpClient::$Mensaje . " Token: " . ApiHttpClient::$Token;
					//echo "aaaa";
                }else{
                     $mensaje = "".ApiHttpClient::$Mensaje."<br />";
                }
				//echo $RutaApi."MARCOOO=".$token;
				$tipoLista="A";
				$aplicacionID=0;
				if(Yii::$app->session['TIPOSOLICITUD'] == "E"){
					$establecimientoID= Yii::$app->session['IDENTIFICADOR'];
					$GrupoEstablecimientoID=0;
					$tipoLista="";
				}
				else{
					$establecimientoID= 0;
					$GrupoEstablecimientoID= Yii::$app->session['IDENTIFICADOR'];
				}
				
				///marco
				$tempo= Yii::$app->session['TIPOMETODO']; 
				$tipoMetodo=Yii::$app->session['TIPOMETODO'];
				
				$permisoMaster="9";
				$permisoGrupo="9";
				$permisoEstablecimiento="9";
				
				
				if(Yii::$app->session['IDENTIFICADOR'] == "0" and $conexion->Dataset['UsuarioApiLista'] == "masterBrentec"  ){
					
					if(Yii::$app->session['TIPOMETODO']=="POST"){
						$aplicacionID=Yii::$app->session['APLICACION_ID'];
						echo "<br /><b>Aplicacion:</b> ".$aplicacionID;
					}
					elseif(Yii::$app->session['TIPOSOLICITUD']=="M"){
						$aplicacionID=1;
						echo "<br /><b>Aplicacion:</b> ".$aplicacionID;
					}
				}elseif(Yii::$app->session['IDENTIFICADOR'] == "0"){
					$aplicacionID=99999;
					echo "SIN DATOS PARA EL IDENTIFICADOR SELECCIONADO <BR />";
					echo  Html::a(' Regresar', $url = ['site/migracion&f='.$_GET['f']], $options = ['class'=>'btn-shadow btn btn-primary mr-3 active']);;
				}
				
				
				$tipoTemp= Yii::$app->session['TIPOSOLICITUD'];
				//echo Yii::$app->session['TIPOSOLICITUD']."MAARRCCOOO";
				
				if($tipoTemp=="M"){
					$permisoMaster="1";
					echo " (Master)";
				}
				if($tipoTemp=="G"){
					$permisoGrupo="1";
					echo " (Grupo)";
				}
				if($tipoTemp=="E"){
					$permisoEstablecimiento="1";
					echo " (Establecimiento)";
				}
				
				$parametros = json_encode(Array(
					"Token" => ApiHttpClient::$Token,
					"aplicacionID" => $aplicacionID,
					"establecimientoID" => $establecimientoID,
					"grupoestablecimientoID" => $GrupoEstablecimientoID,
					"tipoLista" => $tipoLista,
					"tipoMetodo" => $tipoMetodo,
					"permisoMaster" => $permisoMaster,
					"permisoGrupo" => $permisoGrupo,
					"permisoEstablecimiento" => $permisoEstablecimiento,
					"MacAddress" => ApiHttpClient::$MacAddress
				)); 
				echo "<pre>";
				print_r($parametros);
                $datos = ApiHttpClient::ConsumeApi('/api/ApiListadoApis.php','GET',$parametros);
				echo $datos;
				echo $RutaApi.'api/ApiListadoApis.php <br />';
                $arraydatos = json_decode($datos,true);
				
				//print_r($arraydatos);
				
				try { 
					if($arraydatos['resultado'])
					{
					   $coleccion = $arraydatos["datos"];
						$listadoApi->BorrarTodo();
						if(count($coleccion) > 0){
							if($listadoApi->Inserta($coleccion))
							{
								$mensaje = "Se guardaron los datos";
								//echo Yii::$app->session['TIPOMETODO'];
								Yii::$app->response->redirect(['site/showdata&f='.$_GET['f']]);
								//header("location: showdata.php");
							}
							else
							{
								echo $mensaje = $listadoApi->mensaje;
							}
						}
						else
						{
							echo $mensaje = $listadoApi->mensaje;
						}
					}else{
						echo $arraydatos['mensaje'];
					}
						
				}catch (Exception $e){
					echo " | No hay datos para el listado de apis | error: ";
					echo  $e->getMessage();
				}
				
				
            }
            else
            {
                echo $mensaje= "Error al obtener los datos";
            }
	}
    catch (Exception $e){
        echo $e->getMessage();
    }
        ?>
        <p><?php //echo $mensaje;  ?></p>
		<?php
		Html::a(' Consultar datos', $url = ['site/showdata&f='.$_GET['f']], $options = ['class'=>'btn-shadow btn btn-primary mr-3 active']);
		Html::a(' Importar todos', $url = ['site/descargaapis&f='.$_GET['f']], $options = ['class'=>'btn-shadow btn btn-primary mr-3 active']);
		echo Html::a(' Regresar', $url = ['site/migracion&f='.$_GET['f']], $options = ['class'=>'btn-shadow btn btn-primary mr-3 active']);
		?>
		
 	</div>
</div>
    
    