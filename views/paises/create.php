<?php

use yii\helpers\Html;
use yii\helpers\Url;

$idForm = '';
if(isset($_GET['f'])){
	$idForm = $_GET['f'];
}

$frmSeguridad = '';
if(isset($_GET['r'])){
	$frmSeguridad = explode("/", $_GET['r']);
}

$menuBotones = Yii::$app->globals->getActionButton($idForm, 'paises', 'Paises', $frmSeguridad, 'create', '0');
echo $menuBotones['botones'];
?>


<div class="main-card mb-3 card">
      <div class="card-body">

					<span style="font-size: 16px;"><?= Yii::$app->globals->getTraductor(6, Yii::$app->session['idiomaId'], 'Ingresa los datos solicitados') ?><br><br></span>	
					
					<?php 						
						echo $this->render('_form', [
							'model' => $model,
						]); 
					?>
	</div>
</div>