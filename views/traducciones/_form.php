<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Traducciones */
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
					 <i class="fa fa-check-square-o" aria-hidden="true"></i>  Registro guardado con exito! ('.Html::a("Ver traducciones", $url = ['traducciones/update&id='.$_GET['id']], $options = ['class'=>'']).')
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
	<?= $form->field($model, 'texto')->textInput(['maxlength' => true]) ?>
</div>

<div class="col-4 float-left  pleft-0">
	<?= $form->field($model, 'textoID')->textInput() ?>
</div>

<div class="col-4 float-left  pleft-0">
	<?= $form->field($model, 'idiomaID')->textInput() ?>
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
        <?= Html::submitButton('<i class="pe-7s-diskette"></i> &nbsp;Guardar', ['class' => 'btn '.Yii::$app->globals->btnSave()]) ?>
    </div>
</div>

 <?php ActiveForm::end(); ?>

