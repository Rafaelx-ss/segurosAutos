<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Versiones */


$idForm = '';
if(isset($_GET['f'])){
	$idForm = $_GET['f'];
}


$frmSeguridad = '';
if(isset($_GET['r'])){
	$frmSeguridad = explode("/", $_GET['r']);
}

$menuBotones = Yii::$app->globals->getActionButton($idForm, 'versiones', 'Versiones', $frmSeguridad, 'update', '1', '&id='.$model->versionID);
echo $menuBotones['botones'];
?>


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

