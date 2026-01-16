<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;


use yii\helpers\ArrayHelper;
use kartik\select2\Select2;

use app\assets\AppAsset;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Reportesconfig */
/* @var $form yii\widgets\ActiveForm */
use app\models\Templatereporte;
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
					 <i class="fa fa-check-square-o" aria-hidden="true"></i>  Registro guardado con exito! ('.Html::a("Ver Administrador de Reportes", $url = ['reportesconfig/update&f='.$_GET['f'].'&id='.$_GET['id']], $options = ['class'=>'']).')
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
<div class="col-8 float-left  pleft-0">
	<div class="col-sm-6 float-left pleft-0">    
		<?php 
			echo $form->field($model, 'templateReporteID')->widget(Select2::classname(), [                         
			'data' => ArrayHelper::map(Templatereporte::find()->andWhere(['=', 'regEstado', '1'])->orderBy('nombreTemplateReporte')->all(), 'templateReporteID', 'nombreTemplateReporte'),
			'language' => 'es',
			'options' => ['placeholder' => ' --- Selecciona --- '],
			'pluginOptions' => ['allowClear' => true]]); 	
		?>
	</div>

	<div class="col-6 float-left  pleft-0">
		<?= $form->field($model, 'nombreReporte')->textInput(['maxlength' => true]) ?>
	</div>
	
	<div class="col-6 float-left  pleft-0">
		<?= $form->field($model, 'orientacionPagina')->dropDownList(['Vertical'=>'Vertical', 'Horizontal'=>'Horizontal'], ['required'=> 'required']) ?>
	</div>

	<div class="col-6 float-left  pleft-0">
		<?= $form->field($model, 'imprimirLogoPdf')->checkbox() ?>
	</div>

	<div class="col-6 float-left  pleft-0">
		<?= $form->field($model, 'imprimirEncabezado')->checkbox() ?>
	</div>

	<div class="col-6 float-left  pleft-0">
		<?= $form->field($model, 'imprimirFechaHora')->checkbox() ?>
	</div>

	<div class="col-6 float-left  pleft-0">
		<?= $form->field($model, 'imprimirNombreUsuario')->checkbox() ?>
	</div>

	<div class="col-6 float-left  pleft-0">
		<?= $form->field($model, 'imprimirPie')->checkbox() ?>
	</div>

	
	<div class="col-6 float-left  pleft-0">
		<?= $form->field($model, 'imprimirLogoExcel')->checkbox() ?>
	</div>

	
	<div class="col-6 float-left  pleft-0">
		<?= $form->field($model, 'imprimirEncabezadoExcel')->checkbox() ?>
	</div>

	<div class="col-6 float-left  pleft-0">
		<?= $form->field($model, 'imprimirFechaHoraExcel')->checkbox() ?>
	</div>

	<div class="col-6 float-left  pleft-0">
		<?= $form->field($model, 'imprimirNombreUsuarioExcel')->checkbox() ?>
	</div>

</div>

<div class="col-4 float-left  pleft-0">
	<div class="col-12 float-left  pleft-0">
		<?= $form->field($model, 'queryReporte')->textarea(['rows' => 6]) ?>
	</div>
</div>

<div style="clear: both;"></div>    
<div class="col-12  pleft-0">
    <div class="form-group">
		<br>
        <?= Html::submitButton('<i class="pe-7s-diskette"></i> &nbsp;'.Yii::$app->globals->getTraductor(14, Yii::$app->session['idiomaId'], 'Guardar'), ['class' => 'btn '.Yii::$app->globals->btnSave()]) ?>
    </div>
</div>

 <?php ActiveForm::end(); ?>

