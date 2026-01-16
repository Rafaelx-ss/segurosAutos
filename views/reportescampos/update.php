<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Reportescampos */



$iAdd = "fa fa-plus";
$iSeacrh = "fa fa-search";
$iDelete = "fa fa-trash";
$iExcel = "fa fa-file-excel";
$iPdf = "fa fa-file-pdf";


?>

<div class="app-page-title">
	<div class="page-title-wrapper">
    	<div class="page-title-heading">
        	<div class="page-title-icon">
            	<i class="pe-7s-lock icon-gradient bg-malibu-beach"></i>
            </div>
            <div>
				Campos 
				<div class="page-title-subheading"><?= Yii::$app->globals->getTraductor(4, Yii::$app->session['idiomaId'], 'Editar Registro') ?></div>
            </div>
        </div>
        <div class="page-title-actions">
			<?php						
			echo Html::a('<i class="'.$iSeacrh.'"></i> '.Yii::$app->globals->getTraductor(18, Yii::$app->session['idiomaId'], 'Consulta Reportes'), $url = ['reportesconfig/index&f='.$_GET['f']], $options = ['class'=>'mb-2 mr-2 btn-pill btn-transition btn '.Yii::$app->globals->btnAcciones()]);
			
			echo Html::a('<i class="'.$iAdd.'" aria-hidden="true"></i> '.Yii::$app->globals->getTraductor(2, Yii::$app->session['idiomaId'], 'Alta Reportes'), $url = ['reportesconfig/create&f='.$_GET['f']], $options = ['class'=>'mb-2 mr-2 btn-pill btn-transition btn '.Yii::$app->globals->btnAcciones()]);
			
			echo Html::a('<i class="fa fa-edit"></i> Editar reporte', $url = ['reportesconfig/update&f='.$_GET['f'].'&id='.$_GET['token']], $options = ['class'=>'mb-2 mr-2 btn-pill btn-transition btn '.Yii::$app->globals->btnAcciones()]);
									
			echo Html::a('<i class="fa fa-tasks"></i> Campos', $url = ['reportescampos/index&f='.$_GET['f'].'&token='.$_GET['token']], $options = ['class'=>'mb-2 mr-2 btn-pill btn-transition btn '.Yii::$app->globals->btnAcciones()]);
			?>
		</div>
     </div>
</div>

<div class="main-card mb-3 card">
      <div class="card-body">

		<span style="font-size: 16px;"><?= Yii::$app->globals->getTraductor(7, Yii::$app->session['idiomaId'], 'Ingresa los datos solicitados para editar la información').' del campo <strong>"'.$model->nombreCampo.'"</strong>'; ?><br><br></span>	
								
		<?php 					
			echo $this->render('_form', [
					'model' => $model,
				]); 
		?>         
    </div>
</div>

