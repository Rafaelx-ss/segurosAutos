<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;


use yii\helpers\ArrayHelper;
use kartik\select2\Select2;

use app\assets\AppAsset;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Mformularios */
/* @var $form yii\widgets\ActiveForm */
use app\models\Menus;
use app\models\Formularios;
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
					 <i class="fa fa-check-square-o" aria-hidden="true"></i>  Registro guardado con exito! ('.Html::a("Ver mformularios", $url = ['mformularios/update&id='.$_GET['id']], $options = ['class'=>'']).')
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
	<?php 
		echo $form->field($model, 'menuID')->widget(Select2::classname(), [                         
			'data' => ArrayHelper::map(Menus::find()->andWhere(['=', 'regEstado', '1'])->orderBy('nombreMenu')->all(), 'menuID', 'nombreMenu'),
			'language' => 'es',
			'options' => ['placeholder' => ' --- Selecciona --- '],
			'pluginOptions' => ['allowClear' => true]]); 	
	?>
</div>


<div class="col-4 float-left  pleft-0">
	<?php 
		echo $form->field($model, 'formularioID')->widget(Select2::classname(), [                         
			'data' => ArrayHelper::map(Formularios::find()->andWhere(['=', 'regEstado', '1'])->orderBy('nombreFormulario')->all(), 'formularioID', 'nombreFormulario'),
			'language' => 'es',
			'options' => ['placeholder' => ' --- Selecciona --- '],
			'pluginOptions' => ['allowClear' => true]]); 	
	?>
</div>

<div class="col-4 float-left  pleft-0">
	<?= $form->field($model, 'ordenMenuFormulario')->textInput() ?>
</div>

<div class="col-4 float-left  pleft-0">
	<?php
	if($model->isNewRecord){
		echo $form->field($model, 'activoMenusForumlarios')->checkbox(['checked'=>'checked']);
	}else{
		echo $form->field($model, 'activoMenusForumlarios')->checkbox();
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

