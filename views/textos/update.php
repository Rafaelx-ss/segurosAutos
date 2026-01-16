<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Textos */
?>

<div class="app-page-title">
	<div class="page-title-wrapper">
    	<div class="page-title-heading">
        	<div class="page-title-icon">
            	<i class="pe-7s-chat icon-gradient bg-malibu-beach"></i>
            </div>
            <div>
				Textos 
				<div class="page-title-subheading">Editar registro</div>
            </div>
        </div>
        <div class="page-title-actions">
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
					echo Html::a('<i class="'.$iSeacrh.'"></i> Consulta texto', $url = ['textos/index'], $options = ['class'=>'mb-2 mr-2 btn-pill btn-transition btn '.Yii::$app->globals->btnAcciones()]);
			
					echo Html::a('<i class="'.$iAdd.'" aria-hidden="true"></i> Alta texto', $url = ['textos/create'], $options = ['class'=>'mb-2 mr-2 btn-pill btn-transition btn '.Yii::$app->globals->btnAcciones()]);
				
					echo Html::a('<i class="fa fa-edit" aria-hidden="true"></i> Editar texto', $url = ['textos/update&id='.$model->textoID], $options = ['class'=>'mb-2 mr-2 btn-pill btn-transition btn '.Yii::$app->globals->btnAcciones()]);
			
				    echo Html::a('<i class="fa fa-comments" aria-hidden="true"></i> Traducciones', $url = ['traducciones/index&token='.$model->textoID], $options = ['class'=>'mb-2 mr-2 btn-pill btn-transition btn '.Yii::$app->globals->btnAcciones()]);
			?>
		</div>
     </div>
</div>

<div class="main-card mb-3 card">
      <div class="card-body">

					<span style="font-size: 16px;">Ingresa los datos solicitados para editar la información<br><br></span>	
								
					<?php 					
						echo $this->render('_form', [
							'model' => $model,
						]); 
					?>         
    </div>
</div>

