<?php

use yii\helpers\Html;
use yii\helpers\Url;
?>

<div class="app-page-title">
	<div class="page-title-wrapper">
    	<div class="page-title-heading">
        	<div class="page-title-icon">
            	<i class="pe-7s-unlock icon-gradient bg-malibu-beach"></i>
            </div>
            <div>
				Perfil - menus				<div class="page-title-subheading">Alta de registro</div>
            </div>
        </div>
        <div class="page-title-actions">
			<?php 				
					echo Html::a(' Perfiles', $url = ['perfiles/index'], $options = ['class'=>'btn-shadow btn '.Yii::$app->globals->btnMenu().' mr-3']);	
					echo Html::a(' Usuarios', $url = ['usuarios/index'], $options = ['class'=>'btn-shadow btn '.Yii::$app->globals->btnMenu().' mr-3']);			
					echo Html::a(' Perfil Compuesto', $url = ['pcompuestos/index'], $options = ['class'=>'btn-shadow btn '.Yii::$app->globals->btnMenu().' mr-3']);			
					echo Html::a(' Permisos', $url = ['paccion/index'], $options = ['class'=>'btn-shadow btn '.Yii::$app->globals->btnMenu().' mr-3']);
			
					echo Html::a(' Menus', $url = ['pmenus/index'], $options = ['class'=>'btn-shadow btn '.Yii::$app->globals->btnMenu().' mr-3 active']);			
					echo Html::a(' Formularios', $url = ['formulariosperfiles/index'], $options = ['class'=>'btn-shadow btn '.Yii::$app->globals->btnMenu().' mr-3']);			
			?>
		</div>
     </div>
</div>
<?php
$iconAcciones = Yii::$app->db->createCommand("SELECT * FROM Acciones where regEstado='1'")->queryAll();
			$iAdd = "fa fa-plus";
			$iSeacrh = "fa fa-search";
			$iDelete = "fa fa-trash";
			$iExcel = "fa fa-file-excel";
			$iPdf = "fa fa-file-pdf";
			
			foreach($iconAcciones as $rAccion){
				if($rAccion['accionID'] == '2'){
					$iSeacrh = $rAccion['imagen'];
				}
			
				if($rAccion['accionID'] == '3'){
					$iAdd = $rAccion['imagen'];
				}
			
				if($rAccion['accionID'] == '4'){
					$iDelete = $rAccion['imagen'];
				}
			
				if($rAccion['accionID'] == '6'){
					$iExcel = $rAccion['imagen'];
				}
			
				if($rAccion['accionID'] == '7'){
					$iPdf = $rAccion['imagen'];
				}
			}
?>
<ul class="body-tabs body-tabs-layout tabs-animated body-tabs-animated nav">
	<li class="nav-item">
		<?php
		echo Html::a('<span><i class="'.$iSeacrh.'"></i> Consulta</span>', $url = ['pmenus/index'], $options = ['class'=>'mb-2 mr-2 btn-pill btn-transition btn '.Yii::$app->globals->btnAcciones()]);
		?>
    </li>
	<li class="nav-item">
		<?php
		echo Html::a('<span><i class="'.$iAdd.'" aria-hidden="true"></i> Alta</span>', $url = ['pmenus/create'], $options = ['class'=>'mb-2 mr-2 btn-pill btn-transition btn '.Yii::$app->globals->btnAcciones().' active']);
		?>
    </li>
</ul>
<div class="main-card mb-3 card">
      <div class="card-body">

					<span style="font-size: 16px;">Ingresa los datos solicitados<br><br></span>	
					
					<?php 						
						echo $this->render('_form', [
							'model' => $model,
						]); 
					?>
	</div>
</div>