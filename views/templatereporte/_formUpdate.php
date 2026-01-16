<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;


use yii\helpers\ArrayHelper;
use kartik\select2\Select2;

use app\assets\AppAsset;
use yii\helpers\Url;


$asset = AppAsset::register($this);
$baseUrl = $asset->baseUrl; 

/* @var $this yii\web\View */
/* @var $model app\models\Templatereporte */
/* @var $form yii\widgets\ActiveForm */
?>



 <?php $form = ActiveForm::begin([
        'options' => [
			'enctype' => 'multipart/form-data',
            'onsubmit' => 'cargando()'
        ]
    ]); ?>

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
					 <i class="fa fa-check-square-o" aria-hidden="true"></i>  Registro guardado con exito! ('.Html::a("Ver template reporte", $url = ['templatereporte/update&f='.$_GET['f'].'&id='.$_GET['id']], $options = ['class'=>'']).')
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

<div id="cargando" style="display:none;  color: green; font-size:12px; text-align: left;">
	<img src="<?php echo $baseUrl;?>/require/images/loader.gif" alt="cargando" /> (No cierre ni refresque la pagina hasta finalizar el proceso)
</div>
<div style="clear: both;"></div>
<div class="col-8 float-left  pleft-0">
	<div class="col-6 float-left  pleft-0">
		<?= $form->field($model, 'nombreTemplateReporte')->textInput(['maxlength' => true]) ?>
	</div>

	<div class="col-6 float-left  pleft-0">
		<?= $form->field($model, 'encabezadoTemplateReporte')->textInput(['maxlength' => true]) ?>
	</div>

	<div class="col-6 float-left  pleft-0">
		<?= $form->field($model, 'pieTemplateReporteL1')->textInput(['maxlength' => true]) ?>
	</div>

	<div class="col-6 float-left  pleft-0">
		<?= $form->field($model, 'pieTemplateReporteL2')->textInput(['maxlength' => true]) ?>
	</div>

	<div class="col-6 float-left  pleft-0">
		<?= $form->field($model, 'pieTemplateReporteL3')->textInput(['maxlength' => true]) ?>
	</div>

	<div class="col-3 float-left  pleft-0">
		<?= $form->field($model, 'colorLinea')->textInput(['type' => 'color']) ?>
	</div>

	<div class="col-3 float-left  pleft-0">
		<?= $form->field($model, 'colorTituloTabla')->textInput(['type' => 'color']) ?>
	</div>

	<div class="col-3 float-left  pleft-0">
		<?= $form->field($model, 'colorTituloTexto')->textInput(['type' => 'color']) ?>
	</div>

	<div class="col-3 float-left  pleft-0">
		<?= $form->field($model, 'colorTextoFooter')->textInput(['type' => 'color']) ?>
	</div>
</div>
<div class="col-4 float-left  pleft-0">
	
	 <div class="form-group">
         <div class="form-group">
			 <?php
				if($model->logoTemplateReporte == '' or $model->logoTemplateReporte == 'na'){
					echo '<img src="../web/tlogos/logo_na.jpg" alt="logo" height="50px;" />';
				}else{
					echo '<img src="../web/tlogos/'.$model->logoTemplateReporte.'" alt="logo" height="50px;" />';
				}
			?>
        	<label class="control-label">Selecciona un archivo (JPG 450px X 110px)</label>
        	<input type="file" class="form-control" id="img_logo" name="img_logo" />
    	</div>
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
<script>
	 function cargando(){
		document.getElementById('cargando').style.display = 'block';
	 }  
</script>

