<?php
use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use app\assets\AppAsset;

$asset = AppAsset::register($this);
$baseUrl = $asset->baseUrl;



$apiClient = Yii::$app->basePath.'/clienteapiphp/ApiHttpClient.php';
require_once($apiClient);
ApiHttpClient::Init('http://localhost:8000');
            
ApiHttpClient::$UserName = 'leavendano';
ApiHttpClient::$Password = '12345678';
ApiHttpClient::$MacAddress = 'd4:61:9d:01:85:18';
$token = ApiHttpClient::SolicitaToken('/kairos/api/ApiToken.php');

if(ApiHttpClient::$Resultado)
{
	Yii::$app->session->set('Token', $token);
	Yii::$app->session->set('MacAddress', ApiHttpClient::$MacAddress);
   
   $mensaje = ApiHttpClient::$Mensaje . " Token: " . ApiHttpClient::$Token;
	
	print_r($mensaje);
}
else
{
  $mensaje = ApiHttpClient::$Mensaje;
	print_r($mensaje);
}
?>


<div class="app-page-title">
	<div class="page-title-wrapper">
    	<div class="page-title-heading">
        	<div class="page-title-icon">
            	<i class="pe-7s-add-user icon-gradient bg-malibu-beach"></i>
            </div>
            <div>
				Usuarios				<div class="page-title-subheading">Consulta</div>
            </div>
        </div>
        <div class="page-title-actions">
			<?php 				
					echo Html::a(' Perfiles', $url = ['site/pageone'], $options = ['class'=>'btn-shadow btn btn-info mr-3']);		
					echo Html::a(' Usuarios', $url = ['site/pagetwo'], $options = ['class'=>'btn-shadow btn btn-info mr-3 active']);
					echo Html::a('<span><i class="fa fa-search"></i> Consulta</span>', $url = ['usuarios/index'], $options = ['class'=>'mb-2 mr-2 btn-pill btn-transition btn btn-outline-info active']);
							
			?>			
		</div>
     </div>
</div>
<ul class="body-tabs body-tabs-layout tabs-animated body-tabs-animated nav">
	<li class="nav-item">
		<?php
		echo Html::a('<span><i class="fa fa-search"></i> Consulta</span>', $url = ['usuarios/index'], $options = ['class'=>'mb-2 mr-2 btn-pill btn-transition btn btn-outline-info active']);
		?>
    </li>
	<li class="nav-item">
		<?php
		echo Html::a('<span><i class="fa fa-plus" aria-hidden="true"></i> Alta</span>', $url = ['usuarios/create'], $options = ['class'=>'mb-2 mr-2 btn-pill btn-transition btn btn-outline-info']);
		?>
    </li>
    <li class="nav-item">
		<?php
		echo  Html::a('<span><i class="fa fa-trash" aria-hidden="true"></i> Eliminar</span>', $url = ['#'], $options = ['class' => 'mb-2 mr-2 btn-pill btn-transition btn btn-outline-info', 'onclick' => 'getRows()']);
		?>
    </li>
</ul>
<div class="main-card mb-3 card">
      <div class="card-body">				
					<!-- inicia el grid -->	
            							Aqui va tu contenido
 	</div>
</div>
