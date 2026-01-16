<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;


use yii\helpers\ArrayHelper;
use kartik\select2\Select2;

use app\assets\AppAsset;
use yii\helpers\Url;

use app\models\Textos;

/* @var $this yii\web\View */
/* @var $model app\models\Acciones */
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
					 <i class="fa fa-check-square-o" aria-hidden="true"></i>  Registro guardado con exito! ('.Html::a("Ver acciones", $url = ['acciones/update&id='.$_GET['id']], $options = ['class'=>'']).')
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
	<?= $form->field($model, 'nombreAccion')->textInput(['maxlength' => true]) ?>
</div>


<div class="col-4 float-left  pleft-0">
	<?= $form->field($model, 'paginaAccion')->textInput(['maxlength' => true]) ?>
</div>

<div class="col-4 float-left  pleft-0">
	<?= $form->field($model, 'imagen')->textInput(['maxlength' => true]) ?>
</div>




<div class="col-sm-4 float-left pleft-0">    
	<?php 
			echo $form->field($model, 'textoID')->widget(Select2::classname(), [                         
			'data' => ArrayHelper::map(Textos::find()->andWhere(['=', 'regEstado', '1'])->andWhere(['or', ['tipoTexto'=>'Botones'], ['tipoTexto'=>'default']])->orderBy('nombreTexto')->all(), 'textoID', 'nombreTexto'),
			'language' => 'es',
			'options' => ['placeholder' => ' --- Selecciona --- '],
			'pluginOptions' => ['allowClear' => true]]); 	
	?>
</div>

<div class="col-4 float-left  pleft-0">
	<?php
	if($model->isNewRecord){
		echo $form->field($model, 'estadoAccion')->checkbox(['checked'=>'checked']);
	}else{
		echo $form->field($model, 'estadoAccion')->checkbox();
	}
	?>
</div>

<div style="clear: both;"></div>    
<div class="col-12  pleft-0">
    <div class="form-group">
		<br>
        <?= Html::submitButton('<i class="pe-7s-diskette"></i> &nbsp;'.Yii::$app->globals->getTraductor(14, Yii::$app->session['idiomaId'], 'Guardar'), ['class' => 'btn '.Yii::$app->globals->btnSave()]) ?>
    </div>
</div>

 <?php ActiveForm::end(); ?>

