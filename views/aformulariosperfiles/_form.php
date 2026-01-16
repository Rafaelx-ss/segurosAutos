<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;


use yii\helpers\ArrayHelper;
use kartik\select2\Select2;

use app\assets\AppAsset;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Aformulariosperfiles */
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
					 <i class="fa fa-check-square-o" aria-hidden="true"></i>  Registro guardado con exito! ('.Html::a("Ver aformulariosperfiles", $url = ['aformulariosperfiles/update&id='.$_GET['id']], $options = ['class'=>'']).')
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
	<?= $form->field($model, 'permisoFormularioID')->textInput() ?>
</div>

<div class="col-4 float-left  pleft-0">
	<?= $form->field($model, 'accionFormularioID')->textInput() ?>
</div>

<div class="col-4 float-left  pleft-0">
	<?= $form->field($model, 'perfilID')->textInput() ?>
</div>

<div class="col-4 float-left  pleft-0">
	<?= $form->field($model, 'establecimientoID')->textInput() ?>
</div>

<div class="col-4 float-left  pleft-0">
	<?= $form->field($model, 'activoPermiso')->checkbox() ?>
</div>

<div class="col-4 float-left  pleft-0">
	<?= $form->field($model, 'versionRegistro')->textInput() ?>
</div>

<div class="col-4 float-left  pleft-0">
	<?= $form->field($model, 'regEstado')->checkbox() ?>
</div>

<div class="col-4 float-left  pleft-0">
	<?= $form->field($model, 'regFechaUltimaModificacion')->textInput() ?>
</div>

<div class="col-4 float-left  pleft-0">
	<?= $form->field($model, 'regUsuarioUltimaModificacion')->textInput() ?>
</div>

<div class="col-4 float-left  pleft-0">
	<?= $form->field($model, 'regFormularioUltimaModificacion')->textInput() ?>
</div>

<div class="col-4 float-left  pleft-0">
	<?= $form->field($model, 'regVersionUltimaModificacion')->textInput() ?>
</div>

<div style="clear: both;"></div>    
<div class="col-12  pleft-0">
    <div class="form-group">
		<br>
        <?= Html::submitButton('<i class="pe-7s-diskette"></i> &nbsp;'.Yii::$app->globals->getTraductor(14, Yii::$app->session['idiomaId'], 'Guardar'), ['class' => 'btn btn-success']) ?>
    </div>
</div>

 <?php ActiveForm::end(); ?>

