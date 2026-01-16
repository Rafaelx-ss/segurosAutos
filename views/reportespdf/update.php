<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Reportespdf */


$idForm = '';
if(isset($_GET['f'])){
	$idForm = $_GET['f'];
}


$frmSeguridad = '';
if(isset($_GET['r'])){
	$frmSeguridad = explode("/", $_GET['r']);
}

$menuBotones = Yii::$app->globals->getActionButton($idForm, 'reportespdf', 'Reportespdf', $frmSeguridad, 'update', '1', '&id='.$model->reportesPdfID);
echo $menuBotones['botones'];
?>
<ul class="body-tabs body-tabs-layout tabs-animated body-tabs-animated nav">
	<li class="nav-item">
		<?php
		echo Html::a('<span><i class="fa fa-edit"></i> Editar Generales</span>', $url = ['reportespdf/update&f='.$idForm.'&id='.$model->reportesPdfID], $options = ['class'=>'mb-2 mr-2 btn btn-info ']);
		?>
    </li>
	<li class="nav-item">
		<?php
		echo Html::a('<span><i class="lnr-file-empty"></i>  Encabezado PDF</span>', $url = ['reportespdf/header&f='.$idForm.'&id='.$model->reportesPdfID], $options = ['class'=>'mb-2 mr-2 btn-transition btn btn-outline-info']);
		?>
    </li>
	<li class="nav-item">
		<?php
		echo Html::a('<span><i class="lnr-file-empty"></i> Contenido PDF</span>', $url = ['reportespdf/pdf&f='.$idForm.'&id='.$model->reportesPdfID], $options = ['class'=>'mb-2 mr-2 btn-transition btn btn-outline-info']);
		?>
    </li>
	<li class="nav-item">
		<?php
		echo Html::a('<span><i class="lnr-file-empty"></i> Pie PDF</span>', $url = ['reportespdf/footer&f='.$idForm.'&id='.$model->reportesPdfID], $options = ['class'=>'mb-2 mr-2 btn-transition btn btn-outline-info']);
		?>
    </li>
</ul>

<div class="main-card mb-3 card">
      <div class="card-body">

					<span style="font-size: 16px;"><?= Yii::$app->globals->getTraductor(7, Yii::$app->session['idiomaId'], 'Ingresa los datos solicitados para editar la información') ?><br><br></span>	
								
					<?php 					
						echo $this->render('_formUpdate', [
							'model' => $model,
						]); 
					?>         
    </div>
</div>

