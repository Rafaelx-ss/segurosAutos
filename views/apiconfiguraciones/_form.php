<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;


use yii\helpers\ArrayHelper;
use kartik\select2\Select2;

use app\assets\AppAsset;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Apiconfiguraciones */
/* @var $form yii\widgets\ActiveForm */
?>



 <?php $form = ActiveForm::begin(); ?>

<div class="col-sm-12 pleft-0">
<?php $tterror = count($model->getErrors());
if($tterror != 0){
	echo '<div class="alert alert-danger" role="alert">
			<i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
			'.$form->errorSummary($model).'
	</div>';
}else{
	if(isset($_GET['insert'])){
		if($_GET['insert'] == 'true'){
			echo '<div class="alert alert-success" role="alert">
					 <i class="fa fa-check-square-o" aria-hidden="true"></i>  Registro guardado con exito! ('.Html::a("Ver apiconfiguraciones", $url = ['apiconfiguraciones/update&id='.$_GET['id']], $options = ['class'=>'']).')
				 </div>';
		}
	}
	
	if(isset($_GET['update'])){
		if($_GET['update'] == 'true'){
			echo '<div class="alert alert-success" role="alert">
					 <i class="fa fa-check-square-o" aria-hidden="true"></i>  Registro actualizado con exito!
				 </div>';
		}
	}
}
?>
</div>
<div style="clear: both;"></div>

<div class="col-4 float-left  pleft-0">
	<?= $form->field($model, 'usuarioApiLista')->textInput(['maxlength' => true]) ?>
</div>

<div class="col-4 float-left  pleft-0">
	<?= $form->field($model, 'passwordApiLista')->textInput() ?>
</div>

<div class="col-4 float-left  pleft-0">
	<?= $form->field($model, 'rutaApiLista')->textarea(['rows' => 6]) ?>
</div>

<div class="col-4 float-left  pleft-0">
	<?= $form->field($model, 'identificadorApiLista')->textInput(['maxlength' => true]) ?>
</div>

<div class="col-4 float-left  pleft-0">
	<?= $form->field($model, 'tipoSolicitudApiLista')->textInput(['maxlength' => true]) ?>
</div>

<div class="col-4 float-left  pleft-0">
	<?= $form->field($model, 'aplicacionID')->textInput() ?>
</div>

<div class="col-4 float-left  pleft-0">
	<?= $form->field($model, 'versionActual')->checkbox() ?>
</div>


<div style="clear: both;"></div>    
<div class="col-12  pleft-0">
    <div class="form-group">
		<br>
        <?= Html::submitButton('<i class="pe-7s-diskette"></i> &nbsp;'.Yii::$app->globals->getTraductor(14, Yii::$app->session['idiomaId'], 'Guardar'), ['class' => 'btn '.Yii::$app->globals->btnSave()]) ?>
    </div>
</div>

 <?php ActiveForm::end(); ?>

